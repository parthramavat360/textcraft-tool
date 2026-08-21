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
    var lastZip = null;
    var lastName = '';

    var drop = document.getElementById('tc-ps-drop');
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

    function setStat(id, val) {
        var el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    function updateModeOpts() {
        var everyOpts = document.getElementById('tc-ps-every-opts');
        var rangeOpts = document.getElementById('tc-ps-range-opts');
        if (everyOpts) everyOpts.style.display = splitMode === 'every' ? '' : 'none';
        if (rangeOpts) rangeOpts.style.display = splitMode === 'range' ? '' : 'none';
    }

    document.querySelectorAll('.tc-modes[data-group="ps-mode"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            splitMode = btn.getAttribute('data-val') || 'every';
            updateModeOpts();
        });
    });

    var everyRange = document.getElementById('tc-ps-every');
    var everyVal = document.getElementById('tc-ps-every-val');
    if (everyRange && everyVal) {
        everyVal.textContent = everyRange.value + ' pages';
        everyRange.addEventListener('input', function () {
            everyVal.textContent = everyRange.value + ' pages';
        });
    }

    TCTP.initDropZone('tc-ps-drop', 'tc-ps-drop-input', function (f) {
        if (f.type !== 'application/pdf' && !/\.pdf$/i.test(f.name)) {
            TCTP.toast('Please select a PDF file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        totalPages = 0;
        lastZip = null;
        TCTP.showFileRow('tc-ps-file', f);
        var dlBtn = document.getElementById('tc-ps-download');
        if (dlBtn) dlBtn.style.display = 'none';
        setStat('tc-ps-stat-files', '-');
        setStat('tc-ps-stat-status', 'Ready');

        ensureLibs().then(function () {
            return file.arrayBuffer();
        }).then(function (ab) {
            return window.pdfjsLib.getDocument({ data: new Uint8Array(ab) }).promise;
        }).then(function (pdf) {
            totalPages = pdf.numPages;
            setStat('tc-ps-stat-total', totalPages + ' pages');
        }).catch(function (err) {
            TCTP.toast('Failed to read PDF: ' + err.message, '\u274C');
        });
    }, '.pdf,application/pdf');

    var removeBtn = document.querySelector('#tc-ps-file .tc-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        file = null;
        totalPages = 0;
        lastZip = null;
        TCTP.hideFileRow('tc-ps-file');
        setStat('tc-ps-stat-total', '-');
        setStat('tc-ps-stat-files', '-');
        setStat('tc-ps-stat-status', 'Ready');
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

    var splitBtn = document.getElementById('tc-ps-split');
    if (splitBtn) splitBtn.addEventListener('click', async function () {
        if (!file) { TCTP.toast('Please select a PDF file first.', '\u26A0\uFE0F'); return; }

        var rangeInput = document.getElementById('tc-ps-range');
        var rangeVal = rangeInput ? rangeInput.value.trim() : '';

        TCTP.showProgress('tc-ps-progress');
        TCTP.setProgress('tc-ps-progress', 10, 'Loading libraries...');
        setStat('tc-ps-stat-status', 'Splitting...');

        try {
            await ensureLibs();
            TCTP.setProgress('tc-ps-progress', 25, 'Reading PDF...');

            var ab = await file.arrayBuffer();
            var srcPdf = await window.PDFLib.PDFDocument.load(new Uint8Array(ab));
            var pdf = await window.pdfjsLib.getDocument({ data: new Uint8Array(ab) }).promise;
            totalPages = pdf.numPages;
            setStat('tc-ps-stat-total', totalPages + ' pages');

            var chunks = [];

            if (splitMode === 'every') {
                var perChunk = everyRange ? (parseInt(everyRange.value) || 1) : 1;
                for (var i = 1; i <= totalPages; i += perChunk) {
                    var end = Math.min(i + perChunk - 1, totalPages);
                    var indices = [];
                    for (var j = i; j <= end; j++) indices.push(j - 1);
                    chunks.push({ name: 'pages-' + i + '-' + end + '.pdf', indices: indices });
                }
            } else if (splitMode === 'range') {
                if (!rangeVal) { TCTP.toast('Please enter a page range.', '\u26A0\uFE0F'); TCTP.hideProgress('tc-ps-progress'); setStat('tc-ps-stat-status', 'Ready'); return; }
                var pageNumbers = parseRange(rangeVal, totalPages);
                if (!pageNumbers.length) { TCTP.toast('No valid pages in range.', '\u26A0\uFE0F'); TCTP.hideProgress('tc-ps-progress'); setStat('tc-ps-stat-status', 'Ready'); return; }
                var idxs = pageNumbers.map(function (p) { return p - 1; });
                chunks.push({ name: 'extracted-pages.pdf', indices: idxs });
            } else {
                for (var k = 1; k <= totalPages; k++) {
                    chunks.push({ name: 'page-' + k + '.pdf', indices: [k - 1] });
                }
            }

            var zip = new JSZip();
            for (var c = 0; c < chunks.length; c++) {
                TCTP.setProgress('tc-ps-progress', 25 + Math.round((c / chunks.length) * 60), 'Splitting chunk ' + (c + 1) + '/' + chunks.length + '...');
                var newPdf = await window.PDFLib.PDFDocument.create();
                var copiedPages = await newPdf.copyPages(srcPdf, chunks[c].indices);
                copiedPages.forEach(function (page) { newPdf.addPage(page); });
                var bytes = await newPdf.save();
                zip.file(chunks[c].name, bytes);
            }

            TCTP.setProgress('tc-ps-progress', 90, 'Creating ZIP...');
            var zipBlob = await zip.generateAsync({ type: 'blob' });

            TCTP.setProgress('tc-ps-progress', 100, 'Done!');
            lastName = (file ? file.name.replace(/\.pdf$/i, '') : 'document') + '-split.zip';
            lastZip = zipBlob;
            setStat('tc-ps-stat-files', chunks.length);
            setStat('tc-ps-stat-status', 'Done');
            var dlBtn = document.getElementById('tc-ps-download');
            if (dlBtn) dlBtn.style.display = '';
            TCTP.downloadBlob(zipBlob, lastName);
            TCTP.toast('Split into ' + chunks.length + ' files!');
        } catch (err) {
            TCTP.toast('Split failed: ' + err.message, '\u274C');
            TCTP.hideProgress('tc-ps-progress');
            setStat('tc-ps-stat-status', 'Error');
        }
    });

    var downloadBtn = document.getElementById('tc-ps-download');
    if (downloadBtn) downloadBtn.addEventListener('click', function () {
        if (!lastZip) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
        TCTP.downloadBlob(lastZip, lastName || 'split.zip');
    });

})();
