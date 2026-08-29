/**
 * PDF Compressor - Tool JS
 *
 * Client-side PDF compression using pdf.js + pdf-lib.
 * Visual re-raster at reduced quality/resolution + lossless metadata strip.
 * No file-size limit — adaptive parameters scale with input size.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var file = null;
    var compressedBlob = null;
    var originalUrl = null;
    var level = 2;

    /* ------------------------------------------------------------------ */
    /*  Library loader                                                     */
    /* ------------------------------------------------------------------ */

    function loadScript(src) {
        return new Promise(function (resolve, reject) {
            if (document.querySelector('script[src="' + src + '"]')) { resolve(); return; }
            var s = document.createElement('script');
            s.src = src;
            s.onload = resolve;
            s.onerror = reject;
            document.head.appendChild(s);
        });
    }

    async function ensureLibs() {
        if (!window.pdfjsLib) {
            await loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js');
            window.pdfjsLib.GlobalWorkerOptions.workerSrc =
                'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        }
        if (!window.PDFLib) {
            await loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js');
        }
    }

    /* ------------------------------------------------------------------ */
    /*  Helpers                                                            */
    /* ------------------------------------------------------------------ */

    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    function clamp(v, lo, hi) { return Math.max(lo, Math.min(hi, v)); }

    /**
     * Compute JPEG quality based on file size and compression level.
     * Scale is ALWAYS 1.0 — text is vector in PDFs and reducing scale makes it blurry.
     * Compression savings come from: eliminating embedded fonts (1-5 MB each)
     * and JPEG-encoding images (which are often stored uncompressed in PDFs).
     *
     * Level 1 = Less (near-lossless), 2 = Recommended, 3 = Strong.
     */
    function getParams(fileSize, lvl) {
        var baseQual = { 1: 0.95, 2: 0.88, 3: 0.78 };
        var q = baseQual[lvl] || 0.88;

        if (fileSize > 50 * 1048576)      { q *= 0.92; }
        else if (fileSize > 20 * 1048576) { q *= 0.95; }

        return { scale: 1.0, quality: clamp(q, 0.55, 0.95) };
    }

    /* ------------------------------------------------------------------ */
    /*  Lossless pass — strip metadata + object streams                    */
    /* ------------------------------------------------------------------ */

    async function compressLossless(arrayBuffer) {
        var pdfDoc = await window.PDFLib.PDFDocument.load(arrayBuffer, { ignoreEncryption: true });
        pdfDoc.setTitle('');
        pdfDoc.setAuthor('');
        pdfDoc.setSubject('');
        pdfDoc.setKeywords([]);
        pdfDoc.setProducer('TextCraft PDF Compressor');
        pdfDoc.setCreator('TextCraft PDF Compressor');
        return await pdfDoc.save({ useObjectStreams: true, addDefaultPage: false });
    }

    /* ------------------------------------------------------------------ */
    /*  Visual pass — re-raster every page as JPEG, rebuild PDF            */
    /* ------------------------------------------------------------------ */

    async function compressVisual(arrayBuffer, lvl, onProgress) {
        var fileSize = arrayBuffer.byteLength;
        var params = getParams(fileSize, lvl);

        var pdf = await window.pdfjsLib.getDocument({ data: arrayBuffer, disableAutoFetch: true }).promise;
        var numPages = pdf.numPages;
        var newPdf = await window.PDFLib.PDFDocument.create();

        for (var i = 1; i <= numPages; i++) {
            if (onProgress) onProgress(i, numPages);

            var page = await pdf.getPage(i);
            var unscaled = page.getViewport({ scale: 1 });

            var renderScale = 2.0;
            var vp = page.getViewport({ scale: renderScale });

            var canvas = document.createElement('canvas');
            canvas.width = vp.width;
            canvas.height = vp.height;
            var ctx = canvas.getContext('2d', { willReadFrequently: false });
            await page.render({ canvasContext: ctx, viewport: vp }).promise;

            var imgBytes = await canvasToJpegBytes(canvas, params.quality);
            canvas.width = 0;
            canvas.height = 0;
            canvas = null;
            ctx = null;

            var img = await newPdf.embedJpg(imgBytes);
            var newPage = newPdf.addPage([unscaled.width, unscaled.height]);
            newPage.drawImage(img, { x: 0, y: 0, width: newPage.getWidth(), height: newPage.getHeight() });

            page.cleanup && page.cleanup();
        }

        return await newPdf.save({ useObjectStreams: true });
    }

    function canvasToJpegBytes(canvas, quality) {
        return new Promise(function (resolve, reject) {
            canvas.toBlob(function (blob) {
                if (!blob) { reject(new Error('Canvas toBlob failed')); return; }
                blob.arrayBuffer().then(resolve, reject);
            }, 'image/jpeg', quality);
        });
    }

    /* ------------------------------------------------------------------ */
    /*  Pick the smallest result (never returns null)                      */
    /* ------------------------------------------------------------------ */

    function pickBest(originalBytes, candidates) {
        var best = null;
        var bestLen = originalBytes.byteLength;

        for (var i = 0; i < candidates.length; i++) {
            var c = candidates[i];
            if (c && c.byteLength < bestLen) {
                best = c;
                bestLen = c.byteLength;
            }
        }

        if (!best) {
            best = originalBytes;
        }
        return best;
    }

    /* ------------------------------------------------------------------ */
    /*  Preview renderer (single page to data-URL)                         */
    /* ------------------------------------------------------------------ */

    function renderPageToImage(arrayBuffer, pageNum) {
        pageNum = pageNum || 1;
        return window.pdfjsLib.getDocument({ data: arrayBuffer }).promise.then(function (pdf) {
            if (pageNum > pdf.numPages) pageNum = 1;
            return pdf.getPage(pageNum);
        }).then(function (page) {
            var vp = page.getViewport({ scale: 1.2 });
            var canvas = document.createElement('canvas');
            canvas.width = vp.width;
            canvas.height = vp.height;
            var ctx = canvas.getContext('2d');
            return page.render({ canvasContext: ctx, viewport: vp }).promise.then(function () {
                var dataUrl = canvas.toDataURL('image/png');
                canvas.width = 0;
                canvas.height = 0;
                return dataUrl;
            });
        });
    }

    /* ------------------------------------------------------------------ */
    /*  Drop zone                                                          */
    /* ------------------------------------------------------------------ */

    function clearPreviews() {
        var o = document.getElementById('tc-preview-orig');
        var r = document.getElementById('tc-preview-result');
        if (o) o.innerHTML = '<span style="color:var(--muted);font-size:13px">Original preview will appear here</span>';
        if (r) r.innerHTML = '<span style="color:var(--muted);font-size:13px">Compressed preview will appear here</span>';
    }

    var LEVEL_HINTS = {
        1: 'Less \u2014 near-lossless compression that keeps text and images crisp.',
        2: 'Recommended \u2014 the best balance between file size and visual quality.',
        3: 'Strong \u2014 maximum file-size reduction with slightly softer images.'
    };

    function updateLevelHint(lvl) {
        var hint = document.getElementById('tc-pdf-level-hint');
        if (hint && LEVEL_HINTS[lvl]) hint.textContent = LEVEL_HINTS[lvl];
    }

    function resetTool() {
        file = null;
        compressedBlob = null;
        if (originalUrl) { URL.revokeObjectURL(originalUrl); originalUrl = null; }
        TCTP.hideFileRow('tc-pdf-file');
        clearPreviews();
        TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Idle');
        TCTP.switchToOriginalTab();
    }

    TCTP.initDropZone('tc-pdf-drop', 'tc-pdf-drop-input', function (f) {
        if (f.type !== 'application/pdf') {
            TCTP.toast('Please select a PDF file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        compressedBlob = null;
        TCTP.showFileRow('tc-pdf-file', f);

        if (originalUrl) { URL.revokeObjectURL(originalUrl); originalUrl = null; }
        clearPreviews();
        TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Idle');

        ensureLibs().then(function () {
            return file.arrayBuffer();
        }).then(function (ab) {
            originalUrl = URL.createObjectURL(new Blob([ab], { type: 'application/pdf' }));
            return renderPageToImage(new Uint8Array(ab).buffer, 1);
        }).then(function (dataUrl) {
            TCTP.showOriginalPreview(dataUrl);
            TCTP.switchToOriginalTab();
        }).catch(function () {});
    }, '.pdf,application/pdf');

    var removeBtn = document.querySelector('#tc-pdf-file .tc-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        resetTool();
    });

    var clearBtn = document.getElementById('tc-pdf-clear');
    if (clearBtn) clearBtn.addEventListener('click', function () {
        resetTool();
        TCTP.toast('Cleared.', '\uD83E\uDDF9');
    });

    /* ------------------------------------------------------------------ */
    /*  Level buttons                                                      */
    /* ------------------------------------------------------------------ */

    document.querySelectorAll('.tc-modes[data-group="pdf-level"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            level = parseInt(btn.getAttribute('data-val')) || 2;
            updateLevelHint(level);
        });
    });
    updateLevelHint(level);

    /* ------------------------------------------------------------------ */
    /*  Compress button                                                    */
    /* ------------------------------------------------------------------ */

    document.getElementById('tc-pdf-compress')?.addEventListener('click', async function () {
        if (!file) { TCTP.toast('Please select a PDF file first.', '\u26A0\uFE0F'); return; }

        TCTP.showProgress('tc-pdf-progress');
        TCTP.setProgress('tc-pdf-progress', 5, 'Loading libraries...');

        try {
            await ensureLibs();
            TCTP.setProgress('tc-pdf-progress', 10, 'Reading PDF...');
            var ab = await file.arrayBuffer();
            var abLen = ab.byteLength;
            var abU8 = new Uint8Array(ab);

            TCTP.setProgress('tc-pdf-progress', 15, 'Compressing lossless...');
            var lossless = null;
            try {
                lossless = await compressLossless(ab.slice(0));
            } catch (_) { /* ignore */ }

            TCTP.setProgress('tc-pdf-progress', 25, 'Compressing visual...');
            var visual = null;
            try {
                visual = await compressVisual(ab.slice(0), level, function (pageIdx, total) {
                    var frac = pageIdx / total;
                    var pct = Math.round(25 + frac * 60);
                    TCTP.setProgress('tc-pdf-progress', pct,
                        'Rasterizing page ' + pageIdx + '/' + total + '...');
                });
            } catch (err) {
                TCTP.toast('Visual compression error: ' + err.message, '\u26A0\uFE0F');
            }

            TCTP.setProgress('tc-pdf-progress', 90, 'Comparing results...');
            var best = pickBest(abU8.buffer, [lossless, visual]);
            compressedBlob = new Blob([best], { type: 'application/pdf' });
            var saved = Math.max(0, ((1 - best.byteLength / abLen) * 100)).toFixed(1);

            TCTP.updateResultPanel(formatSize(abLen), formatSize(best.byteLength), saved + '%', 'Done');

            TCTP.setProgress('tc-pdf-progress', 94, 'Rendering preview...');
            try {
                var compDataUrl = await renderPageToImage(
                    await compressedBlob.arrayBuffer(), 1
                );
                TCTP.showResultPreview(compDataUrl);
                TCTP.switchToResultTab();
            } catch (_) { /* preview optional */ }

            TCTP.setProgress('tc-pdf-progress', 100, 'Done!');
            TCTP.hideProgress('tc-pdf-progress');
            TCTP.toast('Compressed! Saved ' + saved + '%');

        } catch (e) {
            TCTP.toast('Compression failed: ' + e.message, '\u274C');
            TCTP.hideProgress('tc-pdf-progress');
        }
    });

    /* ------------------------------------------------------------------ */
    /*  Download button                                                    */
    /* ------------------------------------------------------------------ */

    document.getElementById('tc-pdf-download')?.addEventListener('click', function () {
        if (!compressedBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
        var name = (file ? file.name.replace(/\.pdf$/i, '') : 'document') + '-compressed.pdf';
        TCTP.downloadBlob(compressedBlob, name);
    });

})();
