/**
 * PDF Splitter — Tool JS
 *
 * Drop zone, split mode select (every N pages, extract range, individual pages),
 * output file naming, optimize-size toggle, range input, split button, download ZIP.
 * Requires pdf.js, pdf-lib, JSZip loaded dynamically.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var file = null;
    var splitMode = 'every';
    var nameStyle = 'pages';
    var optimize = false;
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

    function renderPageToImage(arrayBuffer, pageNum) {
        pageNum = pageNum || 1;
        return window.pdfjsLib.getDocument({ data: new Uint8Array(arrayBuffer) }).promise.then(function (pdf) {
            if (pageNum > pdf.numPages) pageNum = 1;
            return pdf.getPage(pageNum);
        }).then(function (page) {
            var vp = page.getViewport({ scale: 1.5 });
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
    /*  Hints                                                              */
    /* ------------------------------------------------------------------ */

    var MODE_HINTS = {
        every: 'Every N Pages \u2014 group pages in fixed-size chunks.',
        range: 'Page Range \u2014 extract only the pages you choose.',
        individual: 'Individual Pages \u2014 one PDF per page.'
    };

    var NAME_HINTS = {
        pages: 'Pages \u2014 names reflect the page ranges (e.g. pages-1-5.pdf).',
        sequential: 'Sequential \u2014 files are named part-1.pdf, part-2.pdf, and so on.',
        original: 'Original name \u2014 files keep your source filename as a prefix.'
    };

    function updateHints() {
        var mh = document.getElementById('tc-ps-mode-hint');
        if (mh && MODE_HINTS[splitMode]) mh.textContent = MODE_HINTS[splitMode];
        var nh = document.getElementById('tc-ps-name-hint');
        if (nh && NAME_HINTS[nameStyle]) nh.textContent = NAME_HINTS[nameStyle];
    }

    function updateModeOpts() {
        var everyOpts = document.getElementById('tc-ps-every-opts');
        var rangeOpts = document.getElementById('tc-ps-range-opts');
        if (everyOpts) everyOpts.style.display = splitMode === 'every' ? '' : 'none';
        if (rangeOpts) rangeOpts.style.display = splitMode === 'range' ? '' : 'none';
        updateHints();
    }

    document.querySelectorAll('.tc-modes[data-group="ps-mode"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            splitMode = btn.getAttribute('data-val') || 'every';
            updateModeOpts();
        });
    });

    document.querySelectorAll('.tc-modes[data-group="ps-name"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            nameStyle = btn.getAttribute('data-val') || 'pages';
            updateHints();
        });
    });

    var optInput = document.getElementById('tc-ps-optimize');
    if (optInput) optInput.addEventListener('change', function () {
        optimize = optInput.checked;
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
            var previewBytes = ab.slice(0);
            return window.pdfjsLib.getDocument({ data: new Uint8Array(ab) }).promise.then(function (pdf) {
                totalPages = pdf.numPages;
                setStat('tc-ps-stat-total', totalPages + ' pages');
                return renderPageToImage(previewBytes, 1);
            });
        }).then(function (dataUrl) {
            TCTP.showOriginalPreview(dataUrl);
            TCTP.switchToOriginalTab();
        }).catch(function (err) {
            TCTP.toast('Failed to read PDF: ' + err.message, '\u274C');
        });
    }, '.pdf,application/pdf');

    var removeBtn = document.querySelector('#tc-ps-file .tc-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        resetTool();
    });

    var clearBtn = document.getElementById('tc-ps-clear');
    if (clearBtn) clearBtn.addEventListener('click', function () {
        resetTool();
        TCTP.toast('Cleared.', '\uD83E\uDDF9');
    });

    function resetTool() {
        if (removeBtn) removeBtn.blur();
        file = null;
        totalPages = 0;
        lastZip = null;
        TCTP.hideFileRow('tc-ps-file');
        setStat('tc-ps-stat-total', '-');
        setStat('tc-ps-stat-files', '-');
        setStat('tc-ps-stat-status', 'Ready');
        var dlBtn = document.getElementById('tc-ps-download');
        if (dlBtn) dlBtn.style.display = 'none';
        TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Idle');
        var orig = document.getElementById('tc-preview-orig');
        var result = document.getElementById('tc-preview-result');
        if (orig) orig.innerHTML = '<span style="color:var(--muted);font-size:13px">Original preview will appear here</span>';
        if (result) result.innerHTML = '<span style="color:var(--muted);font-size:13px">Result preview will appear here</span>';
    }

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

    /* ------------------------------------------------------------------ */
    /*  Output file naming                                                  */
    /* ------------------------------------------------------------------ */

    var fileCounter = 0;

    function makeChunkName(base, style, pagesDefault, singlePage) {
        fileCounter++;
        if (style === 'sequential') return 'part-' + fileCounter + '.pdf';
        if (style === 'original') return (base || 'pdf') + '-' + fileCounter + '.pdf';
        return pagesDefault;
    }

    function baseName(name) {
        return (name || 'pdf').replace(/\.pdf$/i, '');
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
            var srcPdf = await window.PDFLib.PDFDocument.load(new Uint8Array(ab.slice(0)));
            var pdf = await window.pdfjsLib.getDocument({ data: new Uint8Array(ab.slice(0)) }).promise;
            totalPages = pdf.numPages;
            setStat('tc-ps-stat-total', totalPages + ' pages');

            var base = baseName(file.name);
            var chunks = [];
            fileCounter = 0;

            if (splitMode === 'every') {
                var perChunk = everyRange ? (parseInt(everyRange.value) || 1) : 1;
                for (var i = 1; i <= totalPages; i += perChunk) {
                    var end = Math.min(i + perChunk - 1, totalPages);
                    var indices = [];
                    for (var j = i; j <= end; j++) indices.push(j - 1);
                    chunks.push({ name: makeChunkName(base, nameStyle, 'pages-' + i + '-' + end + '.pdf'), indices: indices });
                }
            } else if (splitMode === 'range') {
                if (!rangeVal) { TCTP.toast('Please enter a page range.', '\u26A0\uFE0F'); TCTP.hideProgress('tc-ps-progress'); setStat('tc-ps-stat-status', 'Ready'); return; }
                var pageNumbers = parseRange(rangeVal, totalPages);
                if (!pageNumbers.length) { TCTP.toast('No valid pages in range.', '\u26A0\uFE0F'); TCTP.hideProgress('tc-ps-progress'); setStat('tc-ps-stat-status', 'Ready'); return; }
                var idxs = pageNumbers.map(function (p) { return p - 1; });
                chunks.push({ name: makeChunkName(base, nameStyle, 'extracted-pages.pdf'), indices: idxs });
            } else {
                for (var k = 1; k <= totalPages; k++) {
                    chunks.push({ name: makeChunkName(base, nameStyle, 'page-' + k + '.pdf', k), indices: [k - 1] });
                }
            }

            var zip = new JSZip();
            var firstChunkBytes = null;
            var saveOpts = optimize
                ? { useObjectStreams: true, updateMetadata: false }
                : { useObjectStreams: false, updateMetadata: true };

            for (var c = 0; c < chunks.length; c++) {
                TCTP.setProgress('tc-ps-progress', 25 + Math.round((c / chunks.length) * 60), 'Splitting file ' + (c + 1) + '/' + chunks.length + '...');
                var newPdf = await window.PDFLib.PDFDocument.create();
                var copiedPages = await newPdf.copyPages(srcPdf, chunks[c].indices);
                copiedPages.forEach(function (page) { newPdf.addPage(page); });
                var bytes = await newPdf.save(saveOpts);
                zip.file(chunks[c].name, bytes);
                if (c === 0) firstChunkBytes = bytes;
            }

            TCTP.setProgress('tc-ps-progress', 90, 'Creating ZIP...');
            var zipBlob = await zip.generateAsync({ type: 'blob' });

            TCTP.setProgress('tc-ps-progress', 100, 'Done!');
            TCTP.hideProgress('tc-ps-progress');
            lastName = (file ? file.name.replace(/\.pdf$/i, '') : 'document') + '-split.zip';
            lastZip = zipBlob;
            setStat('tc-ps-stat-files', chunks.length);
            setStat('tc-ps-stat-status', 'Done');
            var dlBtn = document.getElementById('tc-ps-download');
            if (dlBtn) dlBtn.style.display = '';
            TCTP.downloadBlob(zipBlob, lastName);
            TCTP.toast('Split into ' + chunks.length + ' files!');
            var saved = file.size > zipBlob.size ? ((1 - zipBlob.size / file.size) * 100).toFixed(1) : '0';
            TCTP.updateResultPanel(TCTP.formatSize(file.size), TCTP.formatSize(zipBlob.size), saved + '%', 'Done');
            TCTP.switchToResultTab();
            try {
                var compDataUrl = await renderPageToImage(firstChunkBytes.buffer, 1);
                TCTP.showResultPreview(compDataUrl);
            } catch (_) {}
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

    updateModeOpts();

})();
