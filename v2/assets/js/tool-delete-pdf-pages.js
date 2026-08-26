/**
 * Delete PDF Pages — Tool JS
 *
 * Drop zone, page grid thumbnails, click to select pages for deletion,
 * delete button, download remaining pages. Progress bar.
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
        if (wrap) wrap.style.display = '';

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

    var removeBtn = document.querySelector('#tc-dp-file .tc-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        file = null;
        pdfDoc = null;
        selectedPages = {};
        totalPages = 0;
        TCTP.hideFileRow('tc-dp-file');
        var wrap = document.getElementById('tc-dp-grid-wrap');
        if (wrap) wrap.style.display = 'none';
        var grid = document.getElementById('tc-dp-grid');
        if (grid) grid.innerHTML = '';
    });

    var deleteBtn = document.getElementById('tc-dp-delete');
    if (deleteBtn) deleteBtn.addEventListener('click', async function () {
        var toDelete = Object.keys(selectedPages).map(Number);
        if (toDelete.length === 0) {
            TCTP.toast('Please click on page thumbnails to select pages for deletion.', '\u26A0\uFE0F');
            return;
        }
        if (toDelete.length >= totalPages) {
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
                if (!selectedPages[i + 1]) keepPages.push(i);
            }

            TCTP.setProgress('tc-dp-progress', 50, 'Copying pages...');
            var copiedPages = await newPdf.copyPages(srcPdf, keepPages);
            copiedPages.forEach(function (page) { newPdf.addPage(page); });

            TCTP.setProgress('tc-dp-progress', 75, 'Saving...');
            var newBytes = await newPdf.save({ useObjectStreams: false, updateMetadata: false });
            var blob = new Blob([newBytes], { type: 'application/pdf' });

            TCTP.setProgress('tc-dp-progress', 100, 'Done!');
            var name = (file ? file.name.replace(/\.pdf$/i, '') : 'document') + '-removed-pages.pdf';
            lastBlob = blob;
            lastName = name;
            var remainingEl = document.getElementById('tc-dp-stat-remaining');
            if (remainingEl) remainingEl.textContent = keepPages.length + ' pages';
            var downloadBtn = document.getElementById('tc-dp-download');
            if (downloadBtn) downloadBtn.style.display = '';
            TCTP.downloadBlob(blob, name);
            TCTP.toast(keepPages.length + ' pages remaining. Downloaded!');
            var saved = file.size > blob.size ? ((1 - blob.size / file.size) * 100).toFixed(1) : '0';
            TCTP.updateResultPanel(TCTP.formatSize(file.size), TCTP.formatSize(blob.size), saved + '%', 'Done');
            TCTP.switchToResultTab();
            try {
                var resultAb = await blob.arrayBuffer();
                var compDataUrl = await renderPageToImage(resultAb, 1);
                TCTP.showResultPreview(compDataUrl);
            } catch (_) {}
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

})();
