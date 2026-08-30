/**
 * PDF Compressor - Tool JS
 *
 * Client-side PDF compression using pdf.js + pdf-lib.
 * Premium options: Compression Mode (Auto / Lossless / Maximum),
 * Image Quality fine-tune slider, Remove Metadata toggle.
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
    var mode = 'auto';              // auto | lossless | maximum
    var qualityOverride = null;     // set when the user moves the quality slider (0-100)
    var stripMeta = true;

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
     * Compute JPEG quality based on file size, compression level and any
     * manual quality override.
     *
     * - A manual override from the Image Quality slider always wins.
     * - Otherwise the Compression Level preset is used, softened for very
     *   large files.
     *
     * Level 1 = Less (near-lossless), 2 = Recommended, 3 = Strong.
     */
    function getParams(fileSize, lvl, qOverride) {
        var q;
        if (qOverride != null) {
            q = qOverride / 100;
        } else {
            var baseQual = { 1: 0.95, 2: 0.88, 3: 0.78 };
            q = baseQual[lvl] || 0.88;
            if (fileSize > 50 * 1048576)      { q *= 0.92; }
            else if (fileSize > 20 * 1048576) { q *= 0.95; }
        }
        return { scale: 1.0, quality: clamp(q, 0.5, 1.0) };
    }

    /* ------------------------------------------------------------------ */
    /*  Lossless pass — optionally strip metadata + re-optimize streams    */
    /* ------------------------------------------------------------------ */

    async function compressLossless(arrayBuffer) {
        var pdfDoc = await window.PDFLib.PDFDocument.load(arrayBuffer, { ignoreEncryption: true });
        if (stripMeta) {
            pdfDoc.setTitle('');
            pdfDoc.setAuthor('');
            pdfDoc.setSubject('');
            pdfDoc.setKeywords([]);
            pdfDoc.setProducer('TextCraft PDF Compressor');
            pdfDoc.setCreator('TextCraft PDF Compressor');
        } else {
            pdfDoc.setProducer('TextCraft PDF Compressor');
        }
        return await pdfDoc.save({ useObjectStreams: true, addDefaultPage: false });
    }

    /* ------------------------------------------------------------------ */
    /*  Visual pass — re-raster every page as JPEG, rebuild PDF            */
    /* ------------------------------------------------------------------ */

    async function compressVisual(arrayBuffer, lvl, qOverride, onProgress) {
        var fileSize = arrayBuffer.byteLength;
        var params = getParams(fileSize, lvl, qOverride);

        var pdf = await window.pdfjsLib.getDocument({ data: arrayBuffer, disableAutoFetch: true }).promise;
        var numPages = pdf.numPages;
        var newPdf = await window.PDFLib.PDFDocument.create();

        for (var i = 1; i <= numPages; i++) {
            if (onProgress) onProgress(i, numPages);

            var page = await pdf.getPage(i);
            var unscaled = page.getViewport({ scale: 1 });

            // Render at 2x then JPEG-encode at the chosen quality. Higher
            // quality keeps images sharper; the 2x supersample prevents aliasing.
            var renderScale = params.quality < 0.6 ? 1.5 : 2.0;
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

    var MODE_HINTS = {
        auto: 'Auto runs both methods and keeps the smallest result.',
        lossless: 'Lossless strips metadata and re-optimizes streams without any visual change \u2014 text stays crisp.',
        maximum: 'Maximum re-encodes every page as an image for the smallest possible file \u2014 softer text and images.'
    };

    function updateHints() {
        var lh = document.getElementById('tc-pdf-level-hint');
        if (lh && LEVEL_HINTS[level]) lh.textContent = LEVEL_HINTS[level];
        var mh = document.getElementById('tc-pdf-mode-hint');
        if (mh && MODE_HINTS[mode]) mh.textContent = MODE_HINTS[mode];
    }

    function syncQualityVisibility() {
        var wrap = document.getElementById('tc-pdf-quality-wrap');
        if (wrap) wrap.style.display = (mode === 'lossless') ? 'none' : '';
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
            updateHints();
        });
    });

    /* ------------------------------------------------------------------ */
    /*  Mode buttons                                                       */
    /* ------------------------------------------------------------------ */

    document.querySelectorAll('.tc-modes[data-group="pdf-mode"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            mode = btn.getAttribute('data-val') || 'auto';
            syncQualityVisibility();
            updateHints();
        });
    });

    /* ------------------------------------------------------------------ */
    /*  Quality slider                                                     */
    /* ------------------------------------------------------------------ */

    var qualityInput = document.getElementById('tc-pdf-quality');
    if (qualityInput) {
        qualityInput.addEventListener('input', function () {
            qualityOverride = parseFloat(qualityInput.value) || 88;
            var val = document.getElementById('tc-pdf-quality-val');
            if (val) val.textContent = Math.round(qualityOverride) + '%';
        });
    }

    /* ------------------------------------------------------------------ */
    /*  Remove metadata toggle                                             */
    /* ------------------------------------------------------------------ */

    var metaInput = document.getElementById('tc-pdf-strip-meta');
    if (metaInput) {
        metaInput.addEventListener('change', function () {
            stripMeta = metaInput.checked;
        });
    }

    updateHints();

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

            var candidates = [];

            // Auto & Lossless both run the lossless/optimization pass.
            if (mode !== 'maximum') {
                TCTP.setProgress('tc-pdf-progress', 15, 'Optimizing streams\u2026');
                try {
                    candidates.push(await compressLossless(ab.slice(0)));
                } catch (_) { /* ignore */ }
            }

            // Auto & Maximum both run the visual re-raster pass.
            if (mode !== 'lossless') {
                TCTP.setProgress('tc-pdf-progress', 25, 'Compressing visual\u2026');
                try {
                    candidates.push(await compressVisual(ab.slice(0), level, qualityOverride, function (pageIdx, total) {
                        var frac = pageIdx / total;
                        var pct = Math.round(25 + frac * 60);
                        TCTP.setProgress('tc-pdf-progress', pct,
                            'Rasterizing page ' + pageIdx + '/' + total + '...');
                    }));
                } catch (err) {
                    TCTP.toast('Visual compression error: ' + err.message, '\u26A0\uFE0F');
                }
            }

            TCTP.setProgress('tc-pdf-progress', 90, 'Finalizing result\u2026');
            var best = pickBest(abU8.buffer, candidates);
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
