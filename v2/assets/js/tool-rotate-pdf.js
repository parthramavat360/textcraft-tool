/**
 * Rotate PDF — Tool JS
 *
 * Drop zone, rotate-by cards (90 CW / 90 CCW / 180), apply-to scope
 * (all pages / specific page numbers), optimize toggle, output file name,
 * clear all. Original + result previews via pdf.js canvas.
 * Requires pdf.js + pdf-lib loaded dynamically.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var file = null;
    var totalPages = 0;
    var rotation = 90;
    var scope = 'all';           // all | pages
    var optimize = false;
    var lastBlob = null;
    var lastName = '';

    var drop = document.getElementById('tc-rp-drop');
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

    function ensurePdfJs() {
        if (window.pdfjsLib) return Promise.resolve();
        return loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js').then(function () {
            window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        });
    }

    function ensurePdfLib() {
        if (window.PDFLib) return Promise.resolve();
        return loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js');
    }

    function setStat(id, val) {
        var el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    function rotatePage(page) {
        var currentRotation = page.getRotation().angle;
        var newAngle = (currentRotation + rotation) % 360;
        if (newAngle < 0) newAngle += 360;
        page.setRotation(window.PDFLib.degrees(newAngle));
    }

    /* ------------------------------------------------------------------ */
    /*  Hints                                                              */
    /* ------------------------------------------------------------------ */

    var ROTATION_HINTS = {
        90: '90\u00B0 CW \u2014 rotates every page a quarter turn clockwise.',
        270: '90\u00B0 CCW \u2014 rotates every page a quarter turn counter-clockwise.',
        180: '180\u00B0 \u2014 flips every page upside down.'
    };
    var SCOPE_HINTS = {
        all: 'All pages \u2014 every page in the PDF gets rotated.',
        pages: 'Page numbers \u2014 only the pages you enter are rotated.'
    };

    function updateHints() {
        var rh = document.getElementById('tc-rp-rotation-hint');
        if (rh) rh.textContent = ROTATION_HINTS[rotation] || ROTATION_HINTS[90];
        var sh = document.getElementById('tc-rp-scope-hint');
        if (sh) sh.textContent = SCOPE_HINTS[scope] || SCOPE_HINTS.all;
    }

    function updateScopeOpts() {
        var pagesOpts = document.getElementById('tc-rp-pages-opts');
        if (pagesOpts) pagesOpts.style.display = scope === 'pages' ? '' : 'none';
        updateHints();
    }

    /* ------------------------------------------------------------------ */
    /*  Page number parsing                                                */
    /* ------------------------------------------------------------------ */

    function parseRange(str, max) {
        var pages = [];
        var parts = str.split(',');
        parts.forEach(function (part) {
            part = part.trim();
            if (!part) return;
            if (part.indexOf('-') !== -1) {
                var range = part.split('-');
                var start = parseInt(range[0], 10);
                var end = parseInt(range[1], 10);
                if (isNaN(start)) return;
                if (isNaN(end)) end = start;
                for (var i = start; i <= Math.min(end, max); i++) {
                    if (i >= 1) pages.push(i - 1); // 0-based for pdf-lib getPage
                }
            } else {
                var n = parseInt(part, 10);
                if (!isNaN(n) && n >= 1 && n <= max) pages.push(n - 1);
            }
        });
        return pages;
    }

    /* ------------------------------------------------------------------ */
    /*  Option wiring                                                      */
    /* ------------------------------------------------------------------ */

    document.querySelectorAll('.tc-modes[data-group="rp-rotation"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            rotation = parseInt(btn.getAttribute('data-val')) || 90;
            updateHints();
        });
    });

    document.querySelectorAll('.tc-modes[data-group="rp-scope"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            scope = btn.getAttribute('data-val') || 'all';
            updateScopeOpts();
        });
    });

    var optimizeInput = document.getElementById('tc-rp-optimize');
    if (optimizeInput) optimizeInput.addEventListener('change', function () {
        optimize = optimizeInput.checked;
    });

    TCTP.initDropZone('tc-rp-drop', 'tc-rp-drop-input', function (f) {
        if (f.type !== 'application/pdf' && !/\.pdf$/i.test(f.name)) {
            TCTP.toast('Please select a PDF file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        totalPages = 0;
        lastBlob = null;
        TCTP.showFileRow('tc-rp-file', f);
        var dlBtn = document.getElementById('tc-rp-download');
        if (dlBtn) dlBtn.style.display = 'none';
        setStat('tc-rp-stat-total', '-');
        setStat('tc-rp-stat-rotated', '0');
        setStat('tc-rp-stat-status', 'Ready');

        ensurePdfJs().then(function () {
            return file.arrayBuffer();
        }).then(function (ab) {
            var bytes = new Uint8Array(ab.slice(0));
            return window.pdfjsLib.getDocument({ data: bytes }).promise.then(function (pdf) {
                totalPages = pdf.numPages;
                setStat('tc-rp-stat-total', totalPages + ' pages');
                return renderPageToImage(ab.slice(0), 1);
            });
        }).then(function (dataUrl) {
            TCTP.showOriginalPreview(dataUrl);
            TCTP.switchToOriginalTab();
        }).catch(function (err) {
            TCTP.toast('Could not render preview: ' + err.message, '\u274C');
        });
    }, '.pdf,application/pdf');

    var removeBtn = document.querySelector('#tc-rp-file .tc-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        clearAll();
    });

    /* ------------------------------------------------------------------ */
    /*  Clear all                                                          */
    /* ------------------------------------------------------------------ */

    function clearAll() {
        file = null;
        totalPages = 0;
        lastBlob = null;
        TCTP.hideFileRow('tc-rp-file');
        setStat('tc-rp-stat-total', '-');
        setStat('tc-rp-stat-rotated', '0');
        setStat('tc-rp-stat-status', 'Ready');
        var dlBtn = document.getElementById('tc-rp-download');
        if (dlBtn) dlBtn.style.display = 'none';
        var pagesInput = document.getElementById('tc-rp-pages');
        if (pagesInput) pagesInput.value = '';
        var nameInput = document.getElementById('tc-rp-name');
        if (nameInput) nameInput.value = '';
        var orig = document.getElementById('tc-preview-orig');
        if (orig) orig.innerHTML = '<span style="color:var(--muted);font-size:13px">Original preview will appear here</span>';
        var res = document.getElementById('tc-preview-result');
        if (res) res.innerHTML = '<span style="color:var(--muted);font-size:13px">Result preview will appear here</span>';
        TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Idle');
        TCTP.switchToOriginalTab();
    }

    var clearBtn = document.getElementById('tc-rp-clear');
    if (clearBtn) clearBtn.addEventListener('click', function () {
        clearAll();
        TCTP.toast('Cleared.', '\uD83E\uDDF9');
    });

    /* ------------------------------------------------------------------ */
    /*  Apply rotation                                                     */
    /* ------------------------------------------------------------------ */

    var applyBtn = document.getElementById('tc-rp-rotate');
    if (applyBtn) applyBtn.addEventListener('click', async function () {
        if (!file) { TCTP.toast('Please select a PDF file first.', '\u26A0\uFE0F'); return; }

        var targetIndices = [];
        if (scope === 'pages') {
            var input = document.getElementById('tc-rp-pages');
            var val = input ? input.value.trim() : '';
            if (!val) { TCTP.toast('Please enter page numbers to rotate.', '\u26A0\uFE0F'); return; }
            targetIndices = parseRange(val, totalPages || 99999);
            if (!targetIndices.length) { TCTP.toast('No valid pages in range.', '\u26A0\uFE0F'); return; }
        }

        TCTP.showProgress('tc-rp-progress');
        TCTP.setProgress('tc-rp-progress', 10, 'Loading libraries...');

        try {
            await ensurePdfJs();
            await ensurePdfLib();
            TCTP.setProgress('tc-rp-progress', 30, 'Reading PDF...');

            var ab = await file.arrayBuffer();
            var pdfDoc = await window.PDFLib.PDFDocument.load(new Uint8Array(ab));
            var pages = pdfDoc.getPages();

            TCTP.setProgress('tc-rp-progress', 50, 'Rotating pages...');

            var rotatedCount = 0;
            if (scope === 'all') {
                pages.forEach(function (page) {
                    rotatePage(page);
                    rotatedCount++;
                });
            } else {
                targetIndices.forEach(function (idx) {
                    if (idx >= 0 && idx < pages.length) {
                        rotatePage(pages[idx]);
                        rotatedCount++;
                    }
                });
            }

            TCTP.setProgress('tc-rp-progress', 75, 'Saving...');
            var saveOpts = optimize
                ? { useObjectStreams: true, updateMetadata: false }
                : { useObjectStreams: false, updateMetadata: true };
            var newBytes = await pdfDoc.save(saveOpts);
            var blob = new Blob([newBytes], { type: 'application/pdf' });

            setStat('tc-rp-stat-total', pages.length + ' pages');
            setStat('tc-rp-stat-rotated', rotatedCount);
            setStat('tc-rp-stat-status', 'Done');

            var nameInputEl = document.getElementById('tc-rp-name');
            var custom = nameInputEl ? nameInputEl.value.trim() : '';
            lastName = (custom || (file ? file.name.replace(/\.pdf$/i, '') : 'document')) + '.pdf';
            lastBlob = blob;

            var saved = file.size > blob.size ? ((1 - blob.size / file.size) * 100).toFixed(1) : '0';
            TCTP.updateResultPanel(TCTP.formatSize(file.size), TCTP.formatSize(blob.size), saved + '%', 'Done');

            TCTP.setProgress('tc-rp-progress', 90, 'Rendering result preview...');
            var resultAb = await blob.arrayBuffer();
            var resultUrl = await renderPageToImage(resultAb, 1);
            TCTP.showResultPreview(resultUrl);
            TCTP.switchToResultTab();

            TCTP.setProgress('tc-rp-progress', 100, 'Done!');
            TCTP.toast('Rotated ' + rotatedCount + ' pages by ' + rotation + '\u00B0!');

            var downloadBtn = document.getElementById('tc-rp-download');
            if (downloadBtn) downloadBtn.style.display = '';
            TCTP.downloadBlob(blob, lastName);
            TCTP.hideProgress('tc-rp-progress');
        } catch (err) {
            TCTP.toast('Rotation failed: ' + err.message, '\u274C');
            TCTP.hideProgress('tc-rp-progress');
            setStat('tc-rp-stat-status', 'Error');
        }
    });

    var downloadBtn = document.getElementById('tc-rp-download');
    if (downloadBtn) downloadBtn.addEventListener('click', function () {
        if (!lastBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
        TCTP.downloadBlob(lastBlob, lastName);
    });

    updateScopeOpts();

})();
