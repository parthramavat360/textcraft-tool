/**
 * PDF Compressor — Tool JS
 *
 * Client-side PDF compression using pdf.js + pdf-lib.
 * Two-pass: lossless metadata strip + visual re-raster at reduced quality.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var file = null;
    var compressedBlob = null;
    var level = 2;

    // ── Load external libs ───────────────────────────────────

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
            window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        }
        if (!window.PDFLib) {
            await loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js');
        }
    }

    // ── Compression logic ────────────────────────────────────

    async function compressStructureOnly(arrayBuffer) {
        var pdfDoc = await window.PDFLib.PDFDocument.load(arrayBuffer);
        pdfDoc.setTitle('');
        pdfDoc.setAuthor('TextCraft PDF Compressor');
        pdfDoc.setSubject('');
        pdfDoc.setKeywords([]);
        pdfDoc.setProducer('TextCraft PDF Compressor');
        pdfDoc.setCreator('TextCraft PDF Compressor');
        return await pdfDoc.save({ useObjectStreams: true });
    }

    async function compressVisualPdf(arrayBuffer, level) {
        var pdf = await window.pdfjsLib.getDocument({ data: arrayBuffer }).promise;
        var newPdf = await window.PDFLib.PDFDocument.create();
        var scales = { 1: 1.2, 2: 1.05, 3: 0.9 };
        var qualities = { 1: 0.70, 2: 0.58, 3: 0.42 };
        var scale = scales[level] || 1.05;
        var quality = qualities[level] || 0.58;

        for (var i = 1; i <= pdf.numPages; i++) {
            var page = await pdf.getPage(i);
            var vp = page.getViewport({ scale: scale });
            var canvas = document.createElement('canvas');
            canvas.width = vp.width;
            canvas.height = vp.height;
            var ctx = canvas.getContext('2d');
            await page.render({ canvasContext: ctx, viewport: vp }).promise;

            var imgData = canvas.toDataURL('image/jpeg', quality);
            var imgBytes = Uint8Array.from(atob(imgData.split(',')[1]), function (c) { return c.charCodeAt(0); });
            var img = await newPdf.embedJpg(imgBytes);
            var newPage = newPdf.addPage([page.getViewport({ scale: 1 }).width, page.getViewport({ scale: 1 }).height]);
            newPage.drawImage(img, { x: 0, y: 0, width: newPage.getWidth(), height: newPage.getHeight() });
        }
        return await newPdf.save();
    }

    function pickSmaller(original, candidates) {
        var best = null;
        var bestSize = original.byteLength;
        candidates.forEach(function (c) {
            if (c && c.byteLength < bestSize) {
                best = c;
                bestSize = c.byteLength;
            }
        });
        return best || null;
    }

    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    // ── Init ─────────────────────────────────────────────────

    TCTP.initDropZone('tc-pdf-drop', 'tc-pdf-drop-input', function (f) {
        if (f.type !== 'application/pdf') {
            TCTP.toast('Please select a PDF file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        TCTP.showFileRow('tc-pdf-file', f);
    }, '.pdf,application/pdf');

    // Remove file
    var removeBtn = document.querySelector('#tc-pdf-file .tctp-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        file = null;
        compressedBlob = null;
        TCTP.hideFileRow('tc-pdf-file');
    });

    // Level buttons
    document.querySelectorAll('.tc-modes[data-group="pdf-level"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            level = parseInt(btn.getAttribute('data-val')) || 2;
        });
    });

    // Compress
    document.getElementById('tc-pdf-compress').addEventListener('click', async function () {
        if (!file) { TCTP.toast('Please select a PDF file first.', '\u26A0\uFE0F'); return; }

        var progressEl = document.getElementById('tc-pdf-progress');
        TCTP.showProgress('tc-pdf-progress');
        TCTP.setProgress('tc-pdf-progress', 10, 'Loading libraries...');

        try {
            await ensureLibs();
            TCTP.setProgress('tc-pdf-progress', 30, 'Reading PDF...');

            var ab = await file.arrayBuffer();
            TCTP.setProgress('tc-pdf-progress', 50, 'Compressing...');

            var lossless = await compressStructureOnly(new Uint8Array(ab).buffer);
            var visual = await compressVisualPdf(new Uint8Array(ab).buffer, level);
            TCTP.setProgress('tc-pdf-progress', 90, 'Comparing results...');

            var best = pickSmaller(ab, [lossless, visual]);
            if (best) {
                compressedBlob = new Blob([best], { type: 'application/pdf' });
                var saved = ((1 - best.byteLength / ab.byteLength) * 100).toFixed(1);
                document.getElementById('tc-pdf-stat-orig').textContent = formatSize(ab.byteLength);
                document.getElementById('tc-pdf-stat-comp').textContent = formatSize(best.byteLength);
                document.getElementById('tc-pdf-stat-saved').textContent = saved + '%';
                TCTP.setProgress('tc-pdf-progress', 100, 'Done!');
                TCTP.toast('Compressed! Saved ' + saved + '%');
            } else {
                TCTP.toast('Could not compress further. Original kept.', '\u2139\uFE0F');
                TCTP.hideProgress('tc-pdf-progress');
            }
        } catch (e) {
            TCTP.toast('Compression failed: ' + e.message, '\u274C');
            TCTP.hideProgress('tc-pdf-progress');
        }
    });

    // Download
    document.getElementById('tc-pdf-download').addEventListener('click', function () {
        if (!compressedBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
        var name = (file ? file.name.replace(/\.pdf$/i, '') : 'document') + '-compressed.pdf';
        TCTP.downloadBlob(compressedBlob, name);
    });

    // Preview page 1
    document.getElementById('tc-pdf-file').addEventListener('click', function (e) {
        if (e.target.closest('.tctp-x') || !file) return;
        // Show preview if file is set
    });

})();