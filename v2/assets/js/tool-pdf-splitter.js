/**
 * PDF Splitter — Tool JS
 *
 * Drop zone, split mode select (every N pages, extract range, individual pages),
 * range input, split button, download ZIP.
 * Requires pdf.js, pdf-lib, JSZip loaded dynamically.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var file = null;
    var splitMode = 'every';
    var totalPages = 0;

    var drop = document.getElementById('tc-psplit-drop');
    if (!drop) return;

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
        if (!window.JSZip) {
            await loadScript('https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js');
        }
    }

    document.querySelectorAll('.tctp-modes[data-group="psplit-mode"] .tctp-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            splitMode = btn.getAttribute('data-val') || 'every';
            var rangeInput = document.getElementById('tc-psplit-range');
            if (rangeInput) rangeInput.placeholder = splitMode === 'range' ? 'e.g. 1-5, 8, 10-12' : splitMode === 'every' ? 'Pages per chunk (e.g. 5)' : 'Page number to extract';
        });
    });

    TCTP.initDropZone('tc-psplit-drop', 'tc-psplit-drop-input', function (f) {
        if (f.type !== 'application/pdf' && !/\.pdf$/i.test(f.name)) {
            TCTP.toast('Please select a PDF file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        totalPages = 0;
        TCTP.showFileRow('tc-psplit-file', f);

        ensureLibs().then(function () {
            return file.arrayBuffer();
        }).then(function (ab) {
            return window.pdfjsLib.getDocument({ data: new Uint8Array(ab) }).promise;
        }).then(function (pdf) {
            totalPages = pdf.numPages;
            var infoEl = document.getElementById('tc-psplit-page-info');
            if (infoEl) infoEl.textContent = totalPages + ' pages total';
        }).catch(function (err) {
            TCTP.toast('Failed to read PDF: ' + err.message, '\u274C');
        });
    }, '.pdf,application/pdf');

    var removeBtn = document.querySelector('#tc-psplit-file .tctp-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        file = null;
        totalPages = 0;
        TCTP.hideFileRow('tc-psplit-file');
    });

    function parseRange(rangeStr, max) {
        var pages = [];
        var parts = rangeStr.split(',');
        parts.forEach(function (part) {
            part = part.trim();
            if (part.indexOf('-') !== -1) {
                var range = part.split('-');
                var start = parseInt(range[0]);
                var end = parseInt(range[1]);
                for (var i = start; i <= Math.min(end, max); i++) {
                    if (i >= 1) pages.push(i);
                }
            } else {
                var n = parseInt(part);
                if (n >= 1 && n <= max) pages.push(n);
            }
        });
        return pages;
    }

    var splitBtn = document.getElementById('tc-psplit-split');
    if (splitBtn) splitBtn.addEventListener('click', async function () {
        if (!file) { TCTP.toast('Please select a PDF file first.', '\u26A0\uFE0F'); return; }

        var rangeInput = document.getElementById('tc-psplit-range');
        var rangeVal = rangeInput ? rangeInput.value.trim() : '';

        TCTP.showProgress('tc-psplit-progress');
        TCTP.setProgress('tc-psplit-progress', 10, 'Loading libraries...');

        try {
            await ensureLibs();
            TCTP.setProgress('tc-psplit-progress', 25, 'Reading PDF...');

            var ab = await file.arrayBuffer();
            var srcPdf = await window.PDFLib.PDFDocument.load(new Uint8Array(ab));
            var pdf = await window.pdfjsLib.getDocument({ data: new Uint8Array(ab) }).promise;
            totalPages = pdf.numPages;

            var chunks = [];
            var chunkIndex = 0;

            if (splitMode === 'every') {
                var perChunk = parseInt(rangeVal) || 5;
                for (var i = 1; i <= totalPages; i += perChunk) {
                    chunkIndex++;
                    var end = Math.min(i + perChunk - 1, totalPages);
                    var indices = [];
                    for (var j = i; j <= end; j++) indices.push(j - 1);
                    chunks.push({ name: 'pages-' + i + '-' + end + '.pdf', indices: indices });
                }
            } else if (splitMode === 'range') {
                if (!rangeVal) { TCTP.toast('Please enter a page range.', '\u26A0\uFE0F'); TCTP.hideProgress('tc-psplit-progress'); return; }
                var pageNumbers = parseRange(rangeVal, totalPages);
                var indices = pageNumbers.map(function (p) { return p - 1; });
                chunks.push({ name: 'extracted-pages.pdf', indices: indices });
            } else {
                for (var i = 1; i <= totalPages; i++) {
                    chunks.push({ name: 'page-' + i + '.pdf', indices: [i - 1] });
                }
            }

            var zip = new JSZip();
            for (var c = 0; c < chunks.length; c++) {
                TCTP.setProgress('tc-psplit-progress', 25 + Math.round((c / chunks.length) * 60), 'Splitting chunk ' + (c + 1) + '/' + chunks.length + '...');
                var newPdf = await window.PDFLib.PDFDocument.create();
                var copiedPages = await newPdf.copyPages(srcPdf, chunks[c].indices);
                copiedPages.forEach(function (page) { newPdf.addPage(page); });
                var bytes = await newPdf.save();
                zip.file(chunks[c].name, bytes);
            }

            TCTP.setProgress('tc-psplit-progress', 90, 'Creating ZIP...');
            var zipBlob = await zip.generateAsync({ type: 'blob' });

            TCTP.setProgress('tc-psplit-progress', 100, 'Done!');
            var name = (file ? file.name.replace(/\.pdf$/i, '') : 'document') + '-split.zip';
            TCTP.downloadBlob(zipBlob, name);
            TCTP.toast('Split into ' + chunks.length + ' files!');
        } catch (err) {
            TCTP.toast('Split failed: ' + err.message, '\u274C');
            TCTP.hideProgress('tc-psplit-progress');
        }
    });

})();
