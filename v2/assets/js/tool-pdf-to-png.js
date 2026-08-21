/**
 * PDF to PNG — Tool JS
 *
 * Drop zone, DPI select, render pages to canvas, export as PNG,
 * download ZIP. Requires pdf.js, JSZip loaded dynamically.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var file = null;
    var lastZip = null;
    var lastName = '';

    var drop = document.getElementById('tc-p2p-drop');
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

    function setStat(id, val) {
        var el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    TCTP.initDropZone('tc-p2p-drop', 'tc-p2p-drop-input', function (f) {
        if (f.type !== 'application/pdf' && !/\.pdf$/i.test(f.name)) {
            TCTP.toast('Please select a PDF file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        lastZip = null;
        TCTP.showFileRow('tc-p2p-file', f);
        var dlBtn = document.getElementById('tc-p2p-download');
        if (dlBtn) dlBtn.style.display = 'none';
        setStat('tc-p2p-stat-pages', '-');
        setStat('tc-p2p-stat-done', '0');
        setStat('tc-p2p-stat-status', 'Ready');
    }, '.pdf,application/pdf');

    var removeBtn = document.querySelector('#tc-p2p-file .tc-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        file = null;
        lastZip = null;
        TCTP.hideFileRow('tc-p2p-file');
        setStat('tc-p2p-stat-pages', '-');
        setStat('tc-p2p-stat-done', '0');
        setStat('tc-p2p-stat-status', 'Ready');
    });

    var convertBtn = document.getElementById('tc-p2p-convert');
    if (convertBtn) convertBtn.addEventListener('click', async function () {
        if (!file) { TCTP.toast('Please select a PDF file first.', '\u26A0\uFE0F'); return; }

        var dpiSel = document.getElementById('tc-p2p-dpi');
        var dpi = dpiSel ? (parseInt(dpiSel.value, 10) || 150) : 150;

        TCTP.showProgress('tc-p2p-progress');
        TCTP.setProgress('tc-p2p-progress', 5, 'Loading libraries...');
        setStat('tc-p2p-stat-status', 'Converting...');

        try {
            await ensureLibs();
            TCTP.setProgress('tc-p2p-progress', 15, 'Reading PDF...');

            var ab = await file.arrayBuffer();
            var pdf = await window.pdfjsLib.getDocument({ data: new Uint8Array(ab) }).promise;
            var numPages = pdf.numPages;
            var scale = dpi / 72;
            setStat('tc-p2p-stat-pages', numPages);

            var zip = new JSZip();
            for (var i = 1; i <= numPages; i++) {
                TCTP.setProgress('tc-p2p-progress', 15 + Math.round((i / numPages) * 75), 'Rendering page ' + i + '/' + numPages + '...');
                setStat('tc-p2p-stat-done', String(i - 1));

                var page = await pdf.getPage(i);
                var vp = page.getViewport({ scale: scale });
                var canvas = document.createElement('canvas');
                canvas.width = vp.width;
                canvas.height = vp.height;
                var ctx = canvas.getContext('2d');
                await page.render({ canvasContext: ctx, viewport: vp }).promise;

                var imgData = canvas.toDataURL('image/png');
                var imgBytes = Uint8Array.from(atob(imgData.split(',')[1]), function (c) { return c.charCodeAt(0); });
                var paddedNum = String(i).padStart(String(numPages).length, '0');
                zip.file('page-' + paddedNum + '.png', imgBytes);
            }

            TCTP.setProgress('tc-p2p-progress', 95, 'Creating ZIP...');
            var zipBlob = await zip.generateAsync({ type: 'blob' });

            TCTP.setProgress('tc-p2p-progress', 100, 'Done!');
            lastName = (file ? file.name.replace(/\.pdf$/i, '') : 'document') + '-pages.zip';
            lastZip = zipBlob;
            setStat('tc-p2p-stat-done', String(numPages));
            setStat('tc-p2p-stat-status', 'Done');
            var dlBtn = document.getElementById('tc-p2p-download');
            if (dlBtn) dlBtn.style.display = '';
            TCTP.downloadBlob(zipBlob, lastName);
            TCTP.toast('Exported ' + numPages + ' pages as PNG!');
            var saved = file.size > zipBlob.size ? ((1 - zipBlob.size / file.size) * 100).toFixed(1) : '0';
            TCTP.updateResultPanel(TCTP.formatSize(file.size), TCTP.formatSize(zipBlob.size), saved + '%', 'Done');
        } catch (err) {
            TCTP.toast('Conversion failed: ' + err.message, '\u274C');
            TCTP.hideProgress('tc-p2p-progress');
            setStat('tc-p2p-stat-status', 'Error');
        }
    });

    var downloadBtn = document.getElementById('tc-p2p-download');
    if (downloadBtn) downloadBtn.addEventListener('click', function () {
        if (!lastZip) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
        TCTP.downloadBlob(lastZip, lastName || 'pages.zip');
    });

})();
