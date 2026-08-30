/**
 * Delete PDF Pages — Tool JS
 *
 * Drop zone, page thumbnails (click to select), page-number input mode,
 * delete/keep action, optimize toggle, output file name, clear all.
 * Requires pdf.js + pdf-lib loaded dynamically.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var file = null;
    var pdfDoc = null;
    var selectedPages = {};
    var totalPages = 0;
    var lastBlob = null;
    var lastName = '';
    var method = 'click';        // click | numbers
    var action = 'delete';       // delete | keep
    var optimize = false;

    var drop = document.getElementById('tc-dp-drop');
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

    var METHOD_HINTS = {
        click: 'Click thumbnails \u2014 tap each page preview to toggle its selection.',
        numbers: 'Enter numbers \u2014 type pages/ranges like 1-3, 5, 8 to act on them.'
    };
    var ACTION_HINTS = {
        delete: 'Delete selected \u2014 removes the chosen pages and keeps the rest.',
        keep: 'Keep only selected \u2014 removes everything except the chosen pages.'
    };

    function updateHints() {
        var mh = document.getElementById('tc-dp-method-hint');
        if (mh) mh.textContent = METHOD_HINTS[method] || METHOD_HINTS.click;
        var ah = document.getElementById('tc-dp-action-hint');
        if (ah) ah.textContent = ACTION_HINTS[action] || ACTION_HINTS.delete;
    }

    function updateMethodOpts() {
        var numOpts = document.getElementById('tc-dp-numbers-opts');
        if (numOpts) numOpts.style.display = method === 'numbers' ? '' : 'none';
        var gridWrap = document.getElementById('tc-dp-grid-wrap');
        if (gridWrap && method === 'numbers') gridWrap.style.display = 'none';
        updateHints();
    }

    function updateStats() {
        var selCount = Object.keys(selectedPages).length;
        var selEl = document.getElementById('tc-dp-stat-selected');
        if (selEl) selEl.textContent = selCount + ' selected';
        var remEl = document.getElementById('tc-dp-stat-remaining');
        if (remEl) remEl.textContent = totalPages ? (totalPages - selCount) + ' pages' : '-';
    }

    function renderThumbnails(ab) {
        var wrap = document.getElementById('tc-dp-grid-wrap');
        var grid = document.getElementById('tc-dp-grid');
        if (!grid) return;
        grid.innerHTML = '';
        selectedPages = {};
        totalPages = 0;
        if (wrap) wrap.style.display = (method === 'numbers') ? 'none' : '';

        window.pdfjsLib.getDocument({ data: new Uint8Array(ab) }).promise.then(function (pdf) {
            pdfDoc = pdf;
            totalPages = pdf.numPages;
            updateStats();

            var totalEl = document.getElementById('tc-dp-stat-total');
            if (totalEl) totalEl.textContent = totalPages + ' pages';

            var scale = totalPages > 30 ? 0.25 : 0.4;

            var i = 1;
            function renderNext() {
                if (i > totalPages) {
                    TCTP.hideProgress('tc-dp-progress');
                    return;
                }
                var pct = Math.round((i / totalPages) * 100);
                TCTP.setProgress('tc-dp-progress', pct, 'Loading page ' + i + ' of ' + totalPages + '...');

                var pageNum = i;
                i++;

                pdf.getPage(pageNum).then(function (page) {
                    var vp = page.getViewport({ scale: scale });

                    var item = document.createElement('div');
                    item.className = 'tc-dp-item';
                    item.setAttribute('data-page', pageNum);

                    var canvas = document.createElement('canvas');
                    canvas.width = vp.width;
                    canvas.height = vp.height;
                    canvas.className = 'tc-dp-canvas';

                    var ctx = canvas.getContext('2d');
                    return page.render({ canvasContext: ctx, viewport: vp }).promise.then(function () {
                        var overlay = document.createElement('div');
                        overlay.className = 'tc-dp-overlay';

                        var check = document.createElement('div');
                        check.className = 'tc-dp-check';
                        check.textContent = '\u2715';

                        var label = document.createElement('div');
                        label.className = 'tc-dp-label';
                        label.textContent = 'Page ' + pageNum;

                        item.appendChild(canvas);
                        item.appendChild(overlay);
                        item.appendChild(check);
                        item.appendChild(label);
                        grid.appendChild(item);

                        item.addEventListener('click', function () {
                            if (selectedPages[pageNum]) {
                                delete selectedPages[pageNum];
                                item.classList.remove('selected');
                            } else {
                                selectedPages[pageNum] = true;
                                item.classList.add('selected');
                            }
                            updateStats();
                        });

                        renderNext();
                    });
                }).catch(function () {
                    renderNext();
                });
            }

            renderNext();
        }).catch(function (err) {
            TCTP.toast('Failed to read PDF: ' + err.message, '\u274C');
            TCTP.hideProgress('tc-dp-progress');
        });
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
                    if (i >= 1) pages.push(i);
                }
            } else {
                var n = parseInt(part, 10);
                if (!isNaN(n) && n >= 1 && n <= max) pages.push(n);
            }
        });
        return pages;
    }

    function resolveSelected() {
        var result = {};
        if (method === 'numbers') {
            var input = document.getElementById('tc-dp-pages');
            var val = input ? input.value.trim() : '';
            if (!val) return result;
            parseRange(val, totalPages).forEach(function (p) { result[p] = true; });
        } else {
            return selectedPages;
        }
        return result;
    }

    /* ------------------------------------------------------------------ */
    /*  Drop zone                                                          */
    /* ------------------------------------------------------------------ */

    TCTP.initDropZone('tc-dp-drop', 'tc-dp-drop-input', function (f) {
        if (f.type !== 'application/pdf' && !/\.pdf$/i.test(f.name)) {
            TCTP.toast('Please select a PDF file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        selectedPages = {};
        lastBlob = null;
        TCTP.showFileRow('tc-dp-file', f);
        var wrap = document.getElementById('tc-dp-grid-wrap');
        if (wrap) wrap.style.display = 'none';
        var grid = document.getElementById('tc-dp-grid');
        if (grid) grid.innerHTML = '';
        var dlBtn = document.getElementById('tc-dp-download');
        if (dlBtn) dlBtn.style.display = 'none';
        var remainingEl = document.getElementById('tc-dp-stat-remaining');
        if (remainingEl) remainingEl.textContent = '-';
        var pagesInput = document.getElementById('tc-dp-pages');
        if (pagesInput) pagesInput.value = '';

        TCTP.showProgress('tc-dp-progress');
        TCTP.setProgress('tc-dp-progress', 5, 'Loading libraries...');

        ensureLibs().then(function () {
            TCTP.setProgress('tc-dp-progress', 15, 'Reading PDF...');
            return file.arrayBuffer();
        }).then(function (ab) {
            TCTP.showProgress('tc-dp-progress');
            TCTP.setProgress('tc-dp-progress', 20, 'Rendering page 1 of...');
            renderThumbnails(ab.slice(0));
            return renderPageToImage(ab.slice(0), 1);
        }).then(function (dataUrl) {
            TCTP.showOriginalPreview(dataUrl);
            TCTP.switchToOriginalTab();
        }).catch(function (err) {
            TCTP.toast('Failed: ' + err.message, '\u274C');
            TCTP.hideProgress('tc-dp-progress');
        });
    }, '.pdf,application/pdf');

    /* ------------------------------------------------------------------ */
    /*  Option wiring                                                      */
    /* ------------------------------------------------------------------ */

    document.querySelectorAll('.tc-modes[data-group="dp-method"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            method = btn.getAttribute('data-val') || 'click';
            selectedPages = {};
            if (file) {
                var grid = document.getElementById('tc-dp-grid');
                if (grid) grid.querySelectorAll('.tc-dp-item').forEach(function (it) { it.classList.remove('selected'); });
            }
            updateMethodOpts();
            updateStats();
        });
    });

    document.querySelectorAll('.tc-modes[data-group="dp-action"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            action = btn.getAttribute('data-val') || 'delete';
            updateHints();
        });
    });

    var optimizeInput = document.getElementById('tc-dp-optimize');
    if (optimizeInput) optimizeInput.addEventListener('change', function () {
        optimize = optimizeInput.checked;
    });

    var removeBtn = document.querySelector('#tc-dp-file .tc-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        clearAll();
    });

    function clearAll() {
        file = null;
        pdfDoc = null;
        selectedPages = {};
        totalPages = 0;
        lastBlob = null;
        TCTP.hideFileRow('tc-dp-file');
        var wrap = document.getElementById('tc-dp-grid-wrap');
        if (wrap) wrap.style.display = 'none';
        var grid = document.getElementById('tc-dp-grid');
        if (grid) grid.innerHTML = '';
        var dlBtn = document.getElementById('tc-dp-download');
        if (dlBtn) dlBtn.style.display = 'none';
        var pagesInput = document.getElementById('tc-dp-pages');
        if (pagesInput) pagesInput.value = '';
        var nameInput = document.getElementById('tc-dp-name');
        if (nameInput) nameInput.value = '';
        var t = document.getElementById('tc-dp-stat-total');
        if (t) t.textContent = '-';
        var s = document.getElementById('tc-dp-stat-selected');
        if (s) s.textContent = '0';
        var r = document.getElementById('tc-dp-stat-remaining');
        if (r) r.textContent = '-';
        var orig = document.getElementById('tc-preview-orig');
        if (orig) orig.innerHTML = '<span style="color:var(--muted);font-size:13px">Original preview will appear here</span>';
        var res = document.getElementById('tc-preview-result');
        if (res) res.innerHTML = '<span style="color:var(--muted);font-size:13px">Result preview will appear here</span>';
        TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Idle');
        TCTP.switchToOriginalTab();
    }

    var clearBtn = document.getElementById('tc-dp-clear');
    if (clearBtn) clearBtn.addEventListener('click', function () {
        clearAll();
        TCTP.toast('Cleared.', '\uD83E\uDDF9');
    });

    /* ------------------------------------------------------------------ */
    /*  Delete / process                                                   */
    /* ------------------------------------------------------------------ */

    var deleteBtn = document.getElementById('tc-dp-delete');
    if (deleteBtn) deleteBtn.addEventListener('click', async function () {
        if (!file) { TCTP.toast('Please add a PDF file first.', '\u26A0\uFE0F'); return; }

        var sel = resolveSelected();
        var toDelete = Object.keys(sel).map(Number);
        if (toDelete.length === 0) {
            TCTP.toast(method === 'numbers'
                ? 'Please enter page numbers to act on.' : 'Please click on page thumbnails to select pages.', '\u26A0\uFE0F');
            return;
        }
        if (toDelete.length >= totalPages && action === 'delete') {
            TCTP.toast('Cannot delete all pages.', '\u26A0\uFE0F');
            return;
        }

        TCTP.showProgress('tc-dp-progress');
        TCTP.setProgress('tc-dp-progress', 10, 'Loading libraries...');

        try {
            await ensureLibs();
            TCTP.setProgress('tc-dp-progress', 30, 'Processing PDF...');

            var ab = await file.arrayBuffer();
            var newPdf = await window.PDFLib.PDFDocument.create();
            var srcPdf = await window.PDFLib.PDFDocument.load(new Uint8Array(ab));

            var keepPages = [];
            for (var i = 0; i < totalPages; i++) {
                var pageNo = i + 1;
                var isSelected = !!sel[pageNo];
                if (action === 'delete') {
                    if (!isSelected) keepPages.push(i);
                } else {
                    if (isSelected) keepPages.push(i);
                }
            }
            if (keepPages.length === 0 && action === 'delete') {
                TCTP.toast('Cannot delete all pages.', '\u26A0\uFE0F');
                TCTP.hideProgress('tc-dp-progress');
                return;
            }
            if (keepPages.length === 0 && action === 'keep') {
                TCTP.toast('Nothing to keep.', '\u26A0\uFE0F');
                TCTP.hideProgress('tc-dp-progress');
                return;
            }

            TCTP.setProgress('tc-dp-progress', 50, 'Copying pages...');
            var copiedPages = await newPdf.copyPages(srcPdf, keepPages);
            copiedPages.forEach(function (page) { newPdf.addPage(page); });

            TCTP.setProgress('tc-dp-progress', 75, 'Saving...');
            var saveOpts = optimize
                ? { useObjectStreams: true, updateMetadata: false }
                : { useObjectStreams: false, updateMetadata: true };
            var newBytes = await newPdf.save(saveOpts);
            var blob = new Blob([newBytes], { type: 'application/pdf' });

            var nameInputEl = document.getElementById('tc-dp-name');
            var custom = nameInputEl ? nameInputEl.value.trim() : '';
            var name = (custom || (file ? file.name.replace(/\.pdf$/i, '') : 'document')) + '.pdf';
            lastBlob = blob;
            lastName = name;
            var remainingEl = document.getElementById('tc-dp-stat-remaining');
            if (remainingEl) remainingEl.textContent = keepPages.length + ' pages';
            var downloadBtn = document.getElementById('tc-dp-download');
            if (downloadBtn) downloadBtn.style.display = '';

            TCTP.setProgress('tc-dp-progress', 100, 'Done!');
            var totalIn = file.size;
            var saved = totalIn > blob.size ? ((1 - blob.size / totalIn) * 100).toFixed(1) : '0';
            TCTP.updateResultPanel(TCTP.formatSize(totalIn), TCTP.formatSize(blob.size), saved + '%', 'Done');
            TCTP.toast(keepPages.length + ' pages remaining.');
            TCTP.switchToResultTab();
            try {
                var resultAb = await blob.arrayBuffer();
                var compDataUrl = await renderPageToImage(resultAb, 1);
                TCTP.showResultPreview(compDataUrl);
            } catch (_) {}
            TCTP.downloadBlob(blob, name);
            TCTP.hideProgress('tc-dp-progress');
        } catch (err) {
            TCTP.toast('Failed: ' + err.message, '\u274C');
            TCTP.hideProgress('tc-dp-progress');
        }
    });

    var downloadBtn = document.getElementById('tc-dp-download');
    if (downloadBtn) downloadBtn.addEventListener('click', function () {
        if (!lastBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
        TCTP.downloadBlob(lastBlob, lastName);
    });

    updateMethodOpts();
    updateStats();

})();
