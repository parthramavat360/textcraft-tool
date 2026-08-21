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

    function renderThumbnails(ab) {
        var grid = document.getElementById('tc-dp-grid');
        if (!grid) return;
        grid.innerHTML = '';
        selectedPages = {};
        totalPages = 0;

        window.pdfjsLib.getDocument({ data: new Uint8Array(ab) }).promise.then(function (pdf) {
            pdfDoc = pdf;
            totalPages = pdf.numPages;
            var pageSelectEl = document.getElementById('tc-dp-stat-total');
            if (pageSelectEl) pageSelectEl.textContent = totalPages + ' pages';

            var pending = pdf.numPages;
            for (var i = 1; i <= pdf.numPages; i++) {
                (function (pageNum) {
                    pdf.getPage(pageNum).then(function (page) {
                        var vp = page.getViewport({ scale: 0.3 });
                        var canvas = document.createElement('canvas');
                        canvas.width = vp.width;
                        canvas.height = vp.height;
                        canvas.setAttribute('data-page', pageNum);
                        canvas.className = 'tc-dp-thumb';
                        canvas.title = 'Page ' + pageNum;

                        var ctx = canvas.getContext('2d');
                        page.render({ canvasContext: ctx, viewport: vp }).promise.then(function () {
                            pending--;
                            if (pending === 0) {
                                var gridEl = document.getElementById('tc-dp-grid');
                                if (gridEl) gridEl.style.display = '';
                            }
                        });

                        canvas.addEventListener('click', function () {
                            if (selectedPages[pageNum]) {
                                delete selectedPages[pageNum];
                                canvas.classList.remove('selected');
                            } else {
                                selectedPages[pageNum] = true;
                                canvas.classList.add('selected');
                            }
                            var selCount = Object.keys(selectedPages).length;
                            var selEl = document.getElementById('tc-dp-stat-selected');
                            if (selEl) selEl.textContent = selCount + ' selected';
                        });

                        grid.appendChild(canvas);
                    });
                })(i);
            }
        }).catch(function (err) {
            TCTP.toast('Failed to read PDF: ' + err.message, '\u274C');
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
        var grid = document.getElementById('tc-dp-grid');
        if (grid) grid.style.display = 'none';
        var dlBtn = document.getElementById('tc-dp-download');
        if (dlBtn) dlBtn.style.display = 'none';
        var remainingEl = document.getElementById('tc-dp-stat-remaining');
        if (remainingEl) remainingEl.textContent = '-';

        TCTP.showProgress('tc-dp-progress');
        TCTP.setProgress('tc-dp-progress', 10, 'Loading libraries...');

        ensureLibs().then(function () {
            TCTP.setProgress('tc-dp-progress', 30, 'Rendering thumbnails...');
            return file.arrayBuffer();
        }).then(function (ab) {
            TCTP.setProgress('tc-dp-progress', 50, 'Loading pages...');
            renderThumbnails(ab);
            TCTP.setProgress('tc-dp-progress', 100, 'Done!');
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
        var grid = document.getElementById('tc-dp-grid');
        if (grid) { grid.innerHTML = ''; grid.style.display = 'none'; }
    });

    var deleteBtn = document.getElementById('tc-dp-delete');
    if (deleteBtn) deleteBtn.addEventListener('click', async function () {
        var toDelete = Object.keys(selectedPages).map(Number);
        if (toDelete.length === 0) {
            TCTP.toast('No pages selected for deletion.', '\u26A0\uFE0F');
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
            var pdfBytes = new Uint8Array(ab);
            var newPdf = await window.PDFLib.PDFDocument.create();
            var srcPdf = await window.PDFLib.PDFDocument.load(pdfBytes);

            var keepPages = [];
            for (var i = 0; i < totalPages; i++) {
                if (!selectedPages[i + 1]) keepPages.push(i);
            }

            var copiedPages = await newPdf.copyPages(srcPdf, keepPages);
            copiedPages.forEach(function (page) { newPdf.addPage(page); });

            TCTP.setProgress('tc-dp-progress', 70, 'Saving...');
            var newBytes = await newPdf.save();
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
