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

    var drop = document.getElementById('tc-pmerg-drop');
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

    function renderFileList() {
        var list = document.getElementById('tc-pmerg-list');
        if (!list) return;
        list.innerHTML = '';

        var totalSize = 0;
        files.forEach(function (f, idx) {
            totalSize += f.size;
            var li = document.createElement('li');
            li.className = 'tc-pmerg-item';

            var nameSpan = document.createElement('span');
            nameSpan.className = 'tc-pmerg-name';
            nameSpan.textContent = f.name;

            var sizeSpan = document.createElement('span');
            sizeSpan.className = 'tc-pmerg-size';
            sizeSpan.textContent = TCTP.formatSize(f.size);

            var upBtn = document.createElement('button');
            upBtn.className = 'tc-pmerg-up';
            upBtn.textContent = '\u25B2';
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
            downBtn.className = 'tc-pmerg-down';
            downBtn.textContent = '\u25BC';
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
            removeBtn.className = 'tc-pmerg-remove';
            removeBtn.textContent = '\u2715';
            removeBtn.title = 'Remove';
            removeBtn.addEventListener('click', function () {
                files.splice(idx, 1);
                renderFileList();
            });

            var btnGroup = document.createElement('span');
            btnGroup.className = 'tc-pmerg-btns';
            btnGroup.appendChild(upBtn);
            btnGroup.appendChild(downBtn);
            btnGroup.appendChild(removeBtn);

            li.appendChild(nameSpan);
            li.appendChild(sizeSpan);
            li.appendChild(btnGroup);
            list.appendChild(li);
        });

        var countEl = document.getElementById('tc-pmerg-count');
        var sizeEl = document.getElementById('tc-pmerg-total-size');
        if (countEl) countEl.textContent = files.length + ' files';
        if (sizeEl) sizeEl.textContent = TCTP.formatSize(totalSize);

        var statsEl = document.getElementById('tc-pmerg-stats');
        if (statsEl) statsEl.style.display = files.length ? '' : 'none';
    }

    TCTP.initDropZone('tc-pmerg-drop', 'tc-pmerg-drop-input', function (f) {
        if (f.type !== 'application/pdf' && !/\.pdf$/i.test(f.name)) {
            TCTP.toast('Please select PDF files only.', '\u26A0\uFE0F');
            return;
        }
        files.push(f);
        renderFileList();
    }, '.pdf,application/pdf');

    var mergeBtn = document.getElementById('tc-pmerg-merge');
    if (mergeBtn) mergeBtn.addEventListener('click', async function () {
        if (files.length < 2) {
            TCTP.toast('Please add at least 2 PDF files.', '\u26A0\uFE0F');
            return;
        }

        TCTP.showProgress('tc-pmerg-progress');
        TCTP.setProgress('tc-pmerg-progress', 10, 'Loading pdf-lib...');

        try {
            if (!window.PDFLib) {
                await loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js');
            }
            TCTP.setProgress('tc-pmerg-progress', 30, 'Merging PDFs...');

            var mergedPdf = await window.PDFLib.PDFDocument.create();
            for (var i = 0; i < files.length; i++) {
                TCTP.setProgress('tc-pmerg-progress', 30 + Math.round((i / files.length) * 50), 'Merging ' + (i + 1) + '/' + files.length + '...');
                var ab = await files[i].arrayBuffer();
                var srcPdf = await window.PDFLib.PDFDocument.load(ab);
                var copiedPages = await mergedPdf.copyPages(srcPdf, srcPdf.getPageIndices());
                copiedPages.forEach(function (page) { mergedPdf.addPage(page); });
            }

            TCTP.setProgress('tc-pmerg-progress', 85, 'Saving...');
            var bytes = await mergedPdf.save();
            mergedBlob = new Blob([bytes], { type: 'application/pdf' });

            TCTP.setProgress('tc-pmerg-progress', 100, 'Done!');
            TCTP.toast('Merged ' + files.length + ' PDFs!');
        } catch (err) {
            TCTP.toast('Merge failed: ' + err.message, '\u274C');
            TCTP.hideProgress('tc-pmerg-progress');
        }
    });

    var downloadBtn = document.getElementById('tc-pmerg-download');
    if (downloadBtn) downloadBtn.addEventListener('click', function () {
        if (!mergedBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
        TCTP.downloadBlob(mergedBlob, 'merged.pdf');
    });

})();
