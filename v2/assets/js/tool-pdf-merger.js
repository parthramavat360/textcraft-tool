/**
 * PDF Merger — Tool JS
 *
 * Multi-file drop zone, file list with reorder (up/down buttons),
 * merge button, download. Stats: count, total size.
 * Requires pdf-lib loaded dynamically.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var files = [];
    var mergedBlob = null;

    var drop = document.getElementById('tc-pm-drop');
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

    async function ensurePdfLib() {
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

    function renderFileList() {
        var list = document.getElementById('tc-pm-list');
        if (!list) return;
        list.innerHTML = '';

        var totalSize = 0;
        files.forEach(function (f, idx) {
            totalSize += f.size;
            var li = document.createElement('li');
            li.className = 'tc-pm-item';

            var numSpan = document.createElement('span');
            numSpan.className = 'tc-pm-num';
            numSpan.textContent = (idx + 1);

            var icon = document.createElement('span');
            icon.className = 'tc-pm-icon';
            icon.textContent = '\uD83D\uDCC4';

            var nameSpan = document.createElement('span');
            nameSpan.className = 'tc-pm-name';
            nameSpan.textContent = f.name;

            var sizeSpan = document.createElement('span');
            sizeSpan.className = 'tc-pm-size';
            sizeSpan.textContent = TCTP.formatSize(f.size);

            var upBtn = document.createElement('button');
            upBtn.className = 'tc-pm-up';
            upBtn.innerHTML = '\u25B2';
            upBtn.title = 'Move up';
            upBtn.disabled = idx === 0;
            upBtn.addEventListener('click', function () {
                if (idx > 0) {
                    var temp = files[idx];
                    files[idx] = files[idx - 1];
                    files[idx - 1] = temp;
                    renderFileList();
                }
            });

            var downBtn = document.createElement('button');
            downBtn.className = 'tc-pm-down';
            downBtn.innerHTML = '\u25BC';
            downBtn.title = 'Move down';
            downBtn.disabled = idx === files.length - 1;
            downBtn.addEventListener('click', function () {
                if (idx < files.length - 1) {
                    var temp = files[idx];
                    files[idx] = files[idx + 1];
                    files[idx + 1] = temp;
                    renderFileList();
                }
            });

            var removeBtn = document.createElement('button');
            removeBtn.className = 'tc-pm-remove tc-pm-btns-del';
            removeBtn.innerHTML = '\u2715';
            removeBtn.title = 'Remove';
            removeBtn.addEventListener('click', function () {
                files.splice(idx, 1);
                renderFileList();
            });

            var btnGroup = document.createElement('span');
            btnGroup.className = 'tc-pm-btns';
            btnGroup.appendChild(upBtn);
            btnGroup.appendChild(downBtn);
            btnGroup.appendChild(removeBtn);

            li.appendChild(numSpan);
            li.appendChild(icon);
            li.appendChild(nameSpan);
            li.appendChild(sizeSpan);
            li.appendChild(btnGroup);
            list.appendChild(li);
        });

        var countEl = document.getElementById('tc-pm-stat-count');
        var sizeEl = document.getElementById('tc-pm-stat-size');
        if (countEl) countEl.textContent = files.length;
        if (sizeEl) sizeEl.textContent = files.length ? TCTP.formatSize(totalSize) : '-';

        if (!files.length) {
            var listEl = document.getElementById('tc-pm-list');
            if (listEl) listEl.style.display = 'none';
        } else {
            var listEl2 = document.getElementById('tc-pm-list');
            if (listEl2) listEl2.style.display = '';
        }
    }

    TCTP.initDropZone('tc-pm-drop', 'tc-pm-drop-input', function (f) {
        if (f.type !== 'application/pdf' && !/\.pdf$/i.test(f.name)) {
            TCTP.toast('Please select PDF files only.', '\u26A0\uFE0F');
            return;
        }
        files.push(f);
        var dl = document.getElementById('tc-pm-download');
        if (dl) dl.style.display = 'none';
        renderFileList();

        if (files.length === 1) {
            Promise.all([
                loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js'),
                loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js')
            ]).then(function () {
                window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
                return f.arrayBuffer();
            }).then(function (ab) {
                return renderPageToImage(ab, 1);
            }).then(function (dataUrl) {
                TCTP.showOriginalPreview(dataUrl);
                TCTP.switchToOriginalTab();
            }).catch(function () {});
        }
    }, '.pdf,application/pdf');

    var mergeBtn = document.getElementById('tc-pm-merge');
    if (mergeBtn) mergeBtn.addEventListener('click', async function () {
        if (files.length < 2) {
            TCTP.toast('Please add at least 2 PDF files.', '\u26A0\uFE0F');
            return;
        }

        TCTP.showProgress('tc-pm-progress');
        TCTP.setProgress('tc-pm-progress', 10, 'Loading pdf-lib...');

        try {
            await ensurePdfLib();
            if (!window.pdfjsLib) {
                await loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js');
                window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
            }
            TCTP.setProgress('tc-pm-progress', 30, 'Merging PDFs...');

            var mergedPdf = await window.PDFLib.PDFDocument.create();
            for (var i = 0; i < files.length; i++) {
                TCTP.setProgress('tc-pm-progress', 30 + Math.round((i / files.length) * 50), 'Merging ' + (i + 1) + '/' + files.length + '...');
                var ab = await files[i].arrayBuffer();
                var srcPdf = await window.PDFLib.PDFDocument.load(ab);
                var copiedPages = await mergedPdf.copyPages(srcPdf, srcPdf.getPageIndices());
                copiedPages.forEach(function (page) { mergedPdf.addPage(page); });
            }

            TCTP.setProgress('tc-pm-progress', 85, 'Saving...');
            var bytes = await mergedPdf.save({ useObjectStreams: false, updateMetadata: false });
            mergedBlob = new Blob([bytes], { type: 'application/pdf' });

            TCTP.setProgress('tc-pm-progress', 100, 'Done!');
            var mergedEl = document.getElementById('tc-pm-stat-merged');
            if (mergedEl) mergedEl.textContent = TCTP.formatSize(mergedBlob.size);
            if (downloadBtn) downloadBtn.style.display = '';
            TCTP.toast('Merged ' + files.length + ' PDFs!');
            var totalIn = 0;
            files.forEach(function (f) { totalIn += f.size; });
            var saved = totalIn > mergedBlob.size ? ((1 - mergedBlob.size / totalIn) * 100).toFixed(1) : '0';
            TCTP.updateResultPanel(TCTP.formatSize(totalIn), TCTP.formatSize(mergedBlob.size), saved + '%', 'Done');
            TCTP.switchToResultTab();
            try {
                var mergedAb = await mergedBlob.arrayBuffer();
                var compDataUrl = await renderPageToImage(mergedAb, 1);
                TCTP.showResultPreview(compDataUrl);
            } catch (_) {}
        } catch (err) {
            TCTP.toast('Merge failed: ' + err.message, '\u274C');
            TCTP.hideProgress('tc-pm-progress');
        }
    });

    var downloadBtn = document.getElementById('tc-pm-download');
    if (downloadBtn) downloadBtn.addEventListener('click', function () {
        if (!mergedBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
        TCTP.downloadBlob(mergedBlob, 'merged.pdf');
    });

})();
