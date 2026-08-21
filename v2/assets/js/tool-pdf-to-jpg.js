/**
 * PDF to JPG â€” Tool JS
 *
 * Drop zone, DPI select, render each page to canvas, export as JPG,
 * download ZIP. Requires pdf.js, JSZip loaded dynamically.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var file = null;
    var dpi = 150;

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

    async function ensureLibs() {
        if (!window.pdfjsLib) {
            await loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js');
            window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        }
        if (!window.JSZip) {
            await loadScript('https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js');
        }
    }

    document.querySelectorAll('.tc-modes[data-group="p2j-dpi"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            dpi = parseInt(btn.getAttribute('data-val')) || 150;
        });
    });

    TCTP.initDropZone('tc-p2j-drop', 'tc-p2j-drop-input', function (f) {
        if (f.type !== 'application/pdf' && !/\.pdf$/i.test(f.name)) {
            TCTP.toast('Please select a PDF file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        TCTP.showFileRow('tc-p2j-file', f);
    }, '.pdf,application/pdf');

    var removeBtn = document.querySelector('#tc-p2j-file .tctp-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        file = null;
        TCTP.hideFileRow('tc-p2j-file');
    });

    var convertBtn = document.getElementById('tc-p2j-convert');
    if (convertBtn) convertBtn.addEventListener('click', async function () {
        if (!file) { TCTP.toast('Please select a PDF file first.', '\u26A0\uFE0F'); return; }

        TCTP.showProgress('tc-p2j-progress');
        TCTP.setProgress('tc-p2j-progress', 5, 'Loading libraries...');

        try {
            await ensureLibs();
            TCTP.setProgress('tc-p2j-progress', 15, 'Reading PDF...');

            var ab = await file.arrayBuffer();
            var pdf = await window.pdfjsLib.getDocument({ data: new Uint8Array(ab) }).promise;
            var numPages = pdf.numPages;
            var scale = dpi / 72;

            var zip = new JSZip();
            for (var i = 1; i <= numPages; i++) {
                TCTP.setProgress('tc-p2j-progress', 15 + Math.round((i / numPages) * 75), 'Rendering page ' + i + '/' + numPages + '...');

                var page = await pdf.getPage(i);
                var vp = page.getViewport({ scale: scale });
                var canvas = document.createElement('canvas');
                canvas.width = vp.width;
                canvas.height = vp.height;
                var ctx = canvas.getContext('2d');
                await page.render({ canvasContext: ctx, viewport: vp }).promise;

                var imgData = canvas.toDataURL('image/jpeg', 0.92);
                var imgBytes = Uint8Array.from(atob(imgData.split(',')[1]), function (c) { return c.charCodeAt(0); });
                var paddedNum = String(i).padStart(String(numPages).length, '0');
                zip.file('page-' + paddedNum + '.jpg', imgBytes);
            }

            TCTP.setProgress('tc-p2j-progress', 95, 'Creating ZIP...');
            var zipBlob = await zip.generateAsync({ type: 'blob' });

            TCTP.setProgress('tc-p2j-progress', 100, 'Done!');
            var name = (file ? file.name.replace(/\.pdf$/i, '') : 'document') + '-pages.zip';
            TCTP.downloadBlob(zipBlob, name);
            TCTP.toast('Exported ' + numPages + ' pages as JPG!');
            var saved = file.size > zipBlob.size ? ((1 - zipBlob.size / file.size) * 100).toFixed(1) : '0';
            TCTP.updateResultPanel(TCTP.formatSize(file.size), TCTP.formatSize(zipBlob.size), saved + '%', 'Done');
                                TCTP.showResultPreview(URL.createObjectURL(zipBlob));
            TCTP.switchToResultTab();
        } catch (err) {
            TCTP.toast('Conversion failed: ' + err.message, '\u274C');
            TCTP.hideProgress('tc-p2j-progress');
        }
    });

})();
