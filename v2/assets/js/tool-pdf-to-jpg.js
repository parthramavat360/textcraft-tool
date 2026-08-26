/**
 * PDF to JPG — Tool JS
 *
 * Card-based DPI, JPG quality slider, page range input.
 * Original + result preview via pdf.js canvas.
 * Download all as ZIP. Requires pdf.js + JSZip loaded dynamically.
 *
 * @package TextCraft_Tools_Pro
 */
(function () {
    'use strict';

    var file = null;
    var dpi = 150;
    var quality = 0.92;
    var zipBlob = null;

    var drop = document.getElementById('tc-p2j-drop');
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

    function ensureLibs() {
        var chain = Promise.resolve();
        if (!window.pdfjsLib) {
            chain = chain.then(function () {
                return loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js');
            }).then(function () {
                window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
            });
        }
        if (!window.JSZip) {
            chain = chain.then(function () {
                return loadScript('https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js');
            });
        }
        return chain;
    }

    function renderPageToImage(arrayBuffer, pageNum, scale) {
        pageNum = pageNum || 1;
        scale = scale || 1.5;
        return window.pdfjsLib.getDocument({ data: new Uint8Array(arrayBuffer) }).promise.then(function (pdf) {
            if (pageNum > pdf.numPages) pageNum = 1;
            return pdf.getPage(pageNum);
        }).then(function (page) {
            var vp = page.getViewport({ scale: scale });
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

    function setStat(id, val) {
        var el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    function parsePageRange(input, max) {
        if (!input || !input.trim()) {
            var all = [];
            for (var i = 1; i <= max; i++) all.push(i);
            return all;
        }
        var pages = {};
        var parts = input.split(',');
        for (var p = 0; p < parts.length; p++) {
            var part = parts[p].trim();
            if (!part) continue;
            var range = part.split('-');
            if (range.length === 2) {
                var start = parseInt(range[0]) || 1;
                var end = parseInt(range[1]) || max;
                if (start < 1) start = 1;
                if (end > max) end = max;
                for (var j = start; j <= end; j++) pages[j] = true;
            } else {
                var num = parseInt(part) || 0;
                if (num >= 1 && num <= max) pages[num] = true;
            }
        }
        var result = [];
        Object.keys(pages).forEach(function (k) { result.push(parseInt(k)); });
        result.sort(function (a, b) { return a - b; });
        if (!result.length) {
            for (var m = 1; m <= max; m++) result.push(m);
        }
        return result;
    }

    function ensureArrayBuffer(f) {
        return f.arrayBuffer().then(function (ab) {
            return ab.slice(0);
        });
    }

    TCTP.initDropZone('tc-p2j-drop', 'tc-p2j-drop-input', function (f) {
        if (f.type !== 'application/pdf' && !/\.pdf$/i.test(f.name)) {
            TCTP.toast('Please select a PDF file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        zipBlob = null;
        TCTP.showFileRow('tc-p2j-file', f);
        var dlBtn = document.getElementById('tc-p2j-download');
        if (dlBtn) dlBtn.style.display = 'none';
        setStat('tc-p2j-stat-pages', '-');
        setStat('tc-p2j-stat-done', '-');
        setStat('tc-p2j-stat-size', '-');

        ensureLibs().then(function () {
            return ensureArrayBuffer(file);
        }).then(function (ab) {
            return renderPageToImage(ab.slice(0), 1, 1.5);
        }).then(function (dataUrl) {
            TCTP.showOriginalPreview(dataUrl);
            TCTP.switchToOriginalTab();
            return ensureArrayBuffer(file);
        }).then(function (ab) {
            return window.pdfjsLib.getDocument({ data: new Uint8Array(ab) }).promise;
        }).then(function (pdf) {
            setStat('tc-p2j-stat-pages', pdf.numPages + ' pages');
        }).catch(function () {});
    }, '.pdf,application/pdf');

    var removeBtn = document.querySelector('#tc-p2j-file .tc-x');
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            file = null;
            zipBlob = null;
            TCTP.hideFileRow('tc-p2j-file');
            setStat('tc-p2j-stat-pages', '-');
            setStat('tc-p2j-stat-done', '-');
            setStat('tc-p2j-stat-size', '-');
        });
    }

    var dpiCards = document.querySelectorAll('.tc-p2j-dpi-cards .tc-rsz-mode-card');
    dpiCards.forEach(function (card) {
        card.addEventListener('click', function () {
            dpiCards.forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            dpi = parseInt(card.getAttribute('data-val')) || 150;
        });
    });

    var qualityRange = document.getElementById('tc-p2j-quality');
    var qualityVal = document.getElementById('tc-p2j-quality-val');
    if (qualityRange && qualityVal) {
        qualityRange.addEventListener('input', function () {
            qualityVal.textContent = qualityRange.value + '%';
            quality = parseInt(qualityRange.value) / 100;
        });
    }

    var convertBtn = document.getElementById('tc-p2j-convert');
    if (convertBtn) {
        convertBtn.addEventListener('click', async function () {
            if (!file) { TCTP.toast('Please select a PDF file first.', '\u26A0\uFE0F'); return; }

            TCTP.showProgress('tc-p2j-progress');
            TCTP.setProgress('tc-p2j-progress', 5, 'Loading libraries...');

            try {
                await ensureLibs();
                TCTP.setProgress('tc-p2j-progress', 10, 'Reading PDF...');

                var ab = await ensureArrayBuffer(file);
                var pdf = await window.pdfjsLib.getDocument({ data: new Uint8Array(ab) }).promise;
                var numPages = pdf.numPages;
                var scale = dpi / 72;
                var rangeInput = document.getElementById('tc-p2j-range');
                var pagesToConvert = parsePageRange(rangeInput ? rangeInput.value : '', numPages);

                setStat('tc-p2j-stat-pages', numPages + ' pages');
                setStat('tc-p2j-stat-done', '0');

                var zip = new JSZip();
                var converted = 0;
                var totalPages = pagesToConvert.length;
                var firstPageDataUrl = null;

                for (var idx = 0; idx < totalPages; idx++) {
                    var pageNum = pagesToConvert[idx];
                    var pct = 10 + Math.round(((idx + 0.5) / totalPages) * 80);
                    TCTP.setProgress('tc-p2j-progress', pct, 'Rendering page ' + pageNum + ' (' + (idx + 1) + '/' + totalPages + ')...');

                    var page = await pdf.getPage(pageNum);
                    var vp = page.getViewport({ scale: scale });
                    var canvas = document.createElement('canvas');
                    canvas.width = vp.width;
                    canvas.height = vp.height;
                    var ctx = canvas.getContext('2d');
                    await page.render({ canvasContext: ctx, viewport: vp }).promise;

                    var imgData = canvas.toDataURL('image/jpeg', quality);
                    var imgBytes = Uint8Array.from(atob(imgData.split(',')[1]), function (c) { return c.charCodeAt(0); });
                    var paddedNum = String(pageNum).padStart(String(numPages).length, '0');
                    zip.file('page-' + paddedNum + '.jpg', imgBytes);

                    if (idx === 0) firstPageDataUrl = imgData;

                    converted++;
                    setStat('tc-p2j-stat-done', converted + ' / ' + totalPages);
                    canvas.width = 0;
                    canvas.height = 0;
                }

                TCTP.setProgress('tc-p2j-progress', 95, 'Creating ZIP...');
                zipBlob = await zip.generateAsync({ type: 'blob' });

                TCTP.setProgress('tc-p2j-progress', 100, 'Done!');

                var inputSize = file.size;
                var outputSize = zipBlob.size;
                var saved = inputSize > outputSize ? ((1 - outputSize / inputSize) * 100).toFixed(1) : '0';
                setStat('tc-p2j-stat-size', TCTP.formatSize(outputSize));

                TCTP.updateResultPanel(TCTP.formatSize(inputSize), TCTP.formatSize(outputSize), saved + '%', 'Done');
                TCTP.toast('Exported ' + converted + ' pages as JPG!');

                if (firstPageDataUrl) {
                    TCTP.showResultPreview(firstPageDataUrl);
                }
                TCTP.switchToResultTab();

                var dlBtn = document.getElementById('tc-p2j-download');
                if (dlBtn) dlBtn.style.display = '';
            } catch (err) {
                TCTP.toast('Conversion failed: ' + err.message, '\u274C');
                TCTP.hideProgress('tc-p2j-progress');
            }
        });
    }

    var downloadBtn = document.getElementById('tc-p2j-download');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function () {
            if (!zipBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
            var name = (file ? file.name.replace(/\.pdf$/i, '') : 'document') + '-pages.zip';
            TCTP.downloadBlob(zipBlob, name);
        });
    }

})();
