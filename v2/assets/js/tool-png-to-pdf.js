/**
 * PNG to PDF - Tool JS
 *
 * Multi-file PNG drop to single PDF. Card-based page size selection,
 * margin slider, fit/landscape toggles. Original + result preview
 * rendered via pdf.js canvas to data URL.
 * Requires pdf.js + pdf-lib loaded dynamically.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var files = [];
    var resultBlob = null;

    var drop = document.getElementById('tc-p2pdf-drop');
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

    var PAGE_SIZES = {
        'a4':     { w: 595.28, h: 841.89 },
        'letter': { w: 612,    h: 792    },
        'legal':  { w: 612,    h: 1008   },
        'auto':   null
    };

    var inputEl = document.getElementById('tc-p2pdf-drop-input');
    if (!inputEl) return;
    inputEl.setAttribute('multiple', 'multiple');

    function handleFiles(newFiles) {
        var valid = [];
        for (var i = 0; i < newFiles.length; i++) {
            var f = newFiles[i];
            if (f.type === 'image/png' || /\.png$/i.test(f.name)) {
                valid.push(f);
            }
        }
        if (!valid.length) {
            TCTP.toast('Please select PNG images.', '\u26A0\uFE0F');
            return;
        }
        files = files.concat(valid);
        resultBlob = null;
        var dlBtn = document.getElementById('tc-p2pdf-download');
        if (dlBtn) dlBtn.style.display = 'none';
        showFileList();
        showOriginalPreview();
    }

    drop.addEventListener('click', function () { inputEl.click(); });
    drop.addEventListener('dragover', function (e) { e.preventDefault(); drop.classList.add('hot'); });
    drop.addEventListener('dragleave', function () { drop.classList.remove('hot'); });
    drop.addEventListener('drop', function (e) {
        e.preventDefault();
        drop.classList.remove('hot');
        if (e.dataTransfer.files.length) handleFiles(e.dataTransfer.files);
    });
    inputEl.addEventListener('change', function () {
        if (inputEl.files.length) handleFiles(inputEl.files);
        inputEl.value = '';
    });

    function showFileList() {
        var row = document.getElementById('tc-p2pdf-file');
        if (!row) return;
        if (!files.length) { row.style.display = 'none'; return; }
        var nameEl = row.querySelector('.tc-file-name');
        var sizeEl = row.querySelector('.tc-file-size');
        if (nameEl) nameEl.textContent = files.length === 1 ? files[0].name : files.length + ' images selected';
        var totalSize = 0;
        files.forEach(function (f) { totalSize += f.size; });
        if (sizeEl) sizeEl.textContent = TCTP.formatSize(totalSize);
        row.style.display = '';
        row.classList.add('visible');
    }

    function removeFile() {
        files = [];
        resultBlob = null;
        var row = document.getElementById('tc-p2pdf-file');
        if (row) { row.style.display = 'none'; row.classList.remove('visible'); }
        var dlBtn = document.getElementById('tc-p2pdf-download');
        if (dlBtn) dlBtn.style.display = 'none';
        var origEl = document.getElementById('tc-preview-orig');
        if (origEl) origEl.innerHTML = '';
        TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Ready');
    }

    var removeBtn = document.querySelector('#tc-p2pdf-file .tc-x');
    if (removeBtn) removeBtn.addEventListener('click', removeFile);

    function showOriginalPreview() {
        if (!files.length) return;
        var reader = new FileReader();
        reader.onload = function (e) {
            TCTP.showOriginalPreview(e.target.result);
            TCTP.switchToOriginalTab();
        };
        reader.readAsDataURL(files[0]);
    }

    var sizeCards = document.querySelectorAll('.tc-p2pdf-sizes .tc-rsz-mode-card');
    sizeCards.forEach(function (card) {
        card.addEventListener('click', function () {
            sizeCards.forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
        });
    });

    function getSelectedPageSize() {
        var sel = document.querySelector('.tc-p2pdf-sizes .tc-rsz-mode-card.sel');
        return sel ? sel.getAttribute('data-val') : 'a4';
    }

    var marginRange = document.getElementById('tc-p2pdf-margins');
    var marginVal = document.getElementById('tc-p2pdf-margins-val');
    if (marginRange && marginVal) {
        marginRange.addEventListener('input', function () {
            marginVal.textContent = marginRange.value + ' px';
        });
    }

    var convertBtn = document.getElementById('tc-p2pdf-convert');
    if (convertBtn) convertBtn.addEventListener('click', async function () {
        if (!files.length) {
            TCTP.toast('Please drop PNG images first.', '\u26A0\uFE0F');
            return;
        }

        TCTP.showProgress('tc-p2pdf-progress');
        TCTP.setProgress('tc-p2pdf-progress', 5, 'Loading libraries...');

        try {
            await ensurePdfJs();
            await ensurePdfLib();
            TCTP.setProgress('tc-p2pdf-progress', 15, 'Creating PDF...');

            var sizeKey = getSelectedPageSize();
            var margins = marginRange ? parseInt(marginRange.value, 10) : 20;
            var fitToPage = document.getElementById('tc-p2pdf-fit');
            var landscape = document.getElementById('tc-p2pdf-landscape');
            var doFit = fitToPage ? fitToPage.checked : true;
            var isLandscape = landscape ? landscape.checked : false;

            var pdfDoc = await window.PDFLib.PDFDocument.create();
            var total = files.length;

            for (var i = 0; i < total; i++) {
                var f = files[i];
                var pct = 15 + Math.round(((i + 0.5) / total) * 75);
                TCTP.setProgress('tc-p2pdf-progress', pct, 'Adding image ' + (i + 1) + ' of ' + total + '...');

                var ab = await f.arrayBuffer();
                var imgBytes = new Uint8Array(ab);

                var image;
                try {
                    image = await pdfDoc.embedPng(imgBytes);
                } catch (_) {
                    TCTP.toast('Skipping unsupported image: ' + f.name, '\u26A0\uFE0F');
                    continue;
                }

                var pageW, pageH;

                if (sizeKey === 'auto') {
                    var imgPxW = image.width;
                    var imgPxH = image.height;
                    var marginPx = margins * 0.75;
                    var usableW = imgPxW + marginPx * 2;
                    var usableH = imgPxH + marginPx * 2;
                    pageW = usableW * 72 / 96;
                    pageH = usableH * 72 / 96;
                    if (isLandscape && pageW < pageH) {
                        var tmp = pageW;
                        pageW = pageH;
                        pageH = tmp;
                    }
                } else {
                    var ps = PAGE_SIZES[sizeKey] || PAGE_SIZES['a4'];
                    pageW = ps.w;
                    pageH = ps.h;
                    if (isLandscape) {
                        var tmp2 = pageW;
                        pageW = pageH;
                        pageH = tmp2;
                    }
                }

                var page = pdfDoc.addPage([pageW, pageH]);

                var availW = pageW - (margins * 2 * 72 / 96);
                var availH = pageH - (margins * 2 * 72 / 96);
                var scaleW = availW / image.width;
                var scaleH = availH / image.height;
                var scale = doFit ? Math.min(scaleW, scaleH, 1) : Math.min(scaleW, scaleH);

                var w = image.width * scale;
                var h = image.height * scale;
                var x = (pageW - w) / 2;
                var y = (pageH - h) / 2;

                page.drawImage(image, { x: x, y: y, width: w, height: h });
            }

            TCTP.setProgress('tc-p2pdf-progress', 92, 'Saving PDF...');
            var newBytes = await pdfDoc.save({ useObjectStreams: false, updateMetadata: false });
            resultBlob = new Blob([newBytes], { type: 'application/pdf' });

            TCTP.setProgress('tc-p2pdf-progress', 100, 'Done!');

            var totalIn = 0;
            files.forEach(function (f) { totalIn += f.size; });
            var saved = totalIn > resultBlob.size ? ((1 - resultBlob.size / totalIn) * 100).toFixed(1) : '0';

            var origEl = document.getElementById('tc-p2pdf-stat-orig');
            var compEl = document.getElementById('tc-p2pdf-stat-comp');
            var pagesEl = document.getElementById('tc-p2pdf-stat-pages');
            if (origEl) origEl.textContent = TCTP.formatSize(totalIn);
            if (compEl) compEl.textContent = TCTP.formatSize(resultBlob.size);
            if (pagesEl) pagesEl.textContent = total + (total === 1 ? ' page' : ' pages');

            TCTP.updateResultPanel(TCTP.formatSize(totalIn), TCTP.formatSize(resultBlob.size), saved + '%', 'Done');
            TCTP.toast('PDF created with ' + total + ' page(s)!');

            var dlBtn = document.getElementById('tc-p2pdf-download');
            if (dlBtn) dlBtn.style.display = '';

            try {
                var resultAb = await resultBlob.arrayBuffer();
                var pdf = await window.pdfjsLib.getDocument({ data: new Uint8Array(resultAb) }).promise;
                var page1 = await pdf.getPage(1);
                var vp = page1.getViewport({ scale: 1.5 });
                var canvas = document.createElement('canvas');
                canvas.width = vp.width;
                canvas.height = vp.height;
                var ctx = canvas.getContext('2d');
                await page1.render({ canvasContext: ctx, viewport: vp }).promise;
                var dataUrl = canvas.toDataURL('image/png');
                canvas.width = 0;
                canvas.height = 0;
                TCTP.showResultPreview(dataUrl);
            } catch (_) {}

            TCTP.switchToResultTab();

        } catch (err) {
            TCTP.toast('Conversion failed: ' + err.message, '\u274C');
            TCTP.hideProgress('tc-p2pdf-progress');
        }
    });

    var downloadBtn = document.getElementById('tc-p2pdf-download');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function () {
            if (!resultBlob) {
                TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F');
                return;
            }
            var name = files.length === 1
                ? files[0].name.replace(/\.[^.]+$/, '') + '.pdf'
                : 'converted.pdf';
            TCTP.downloadBlob(resultBlob, name);
        });
    }

})();
