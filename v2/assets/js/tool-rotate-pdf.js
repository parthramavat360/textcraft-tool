/**
 * Rotate PDF — Tool JS
 *
 * Drop zone, rotation mode buttons (90 CW, 90 CCW, 180),
 * apply button, download. Stats: pages rotated.
 * Requires pdf-lib loaded dynamically.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var file = null;
    var rotation = 90;
    var lastBlob = null;

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

    document.querySelectorAll('.tc-modes[data-group="rp-rotation"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            rotation = parseInt(btn.getAttribute('data-val')) || 90;
        });
    });

    TCTP.initDropZone('tc-rp-drop', 'tc-rp-drop-input', function (f) {
        if (f.type !== 'application/pdf' && !/\.pdf$/i.test(f.name)) {
            TCTP.toast('Please select a PDF file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        TCTP.showFileRow('tc-rp-file', f);
        var dlBtn = document.getElementById('tc-rp-download');
        if (dlBtn) dlBtn.style.display = 'none';
        setStat('tc-rp-stat-total', '-');
        setStat('tc-rp-stat-rotated', '0');
        setStat('tc-rp-stat-status', 'Ready');
    }, '.pdf,application/pdf');

    function setStat(id, val) {
        var el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    var removeBtn = document.querySelector('#tc-rp-file .tc-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        file = null;
        TCTP.hideFileRow('tc-rp-file');
        setStat('tc-rp-stat-total', '-');
        setStat('tc-rp-stat-rotated', '0');
        setStat('tc-rp-stat-status', 'Ready');
    });

    var applyBtn = document.getElementById('tc-rp-rotate');
    if (applyBtn) applyBtn.addEventListener('click', async function () {
        if (!file) { TCTP.toast('Please select a PDF file first.', '\u26A0\uFE0F'); return; }

        TCTP.showProgress('tc-rp-progress');
        TCTP.setProgress('tc-rp-progress', 10, 'Loading pdf-lib...');

        try {
            if (!window.PDFLib) {
                await loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js');
            }
            TCTP.setProgress('tc-rp-progress', 30, 'Reading PDF...');

            var ab = await file.arrayBuffer();
            var pdfBytes = new Uint8Array(ab);
            var pdfDoc = await window.PDFLib.PDFDocument.load(pdfBytes);
            var pages = pdfDoc.getPages();

            TCTP.setProgress('tc-rp-progress', 50, 'Rotating pages...');

            var rotatedCount = 0;
            pages.forEach(function (page) {
                var currentRotation = page.getRotation().angle;
                var newAngle = (currentRotation + rotation) % 360;
                if (newAngle < 0) newAngle += 360;
                page.setRotation(window.PDFLib.degrees(newAngle));
                rotatedCount++;
            });

            TCTP.setProgress('tc-rp-progress', 75, 'Saving...');
            var newBytes = await pdfDoc.save();
            var blob = new Blob([newBytes], { type: 'application/pdf' });

            var countEl = document.getElementById('tc-rp-stat-total');
            var rotEl = document.getElementById('tc-rp-stat-rotated');
            if (countEl) countEl.textContent = pages.length;
            if (rotEl) rotEl.textContent = rotatedCount;
            setStat('tc-rp-stat-status', 'Done');

            TCTP.setProgress('tc-rp-progress', 100, 'Done!');
            TCTP.toast('Rotated ' + rotatedCount + ' pages by ' + rotation + '\u00B0!');

            lastBlob = blob;
            var downloadBtn = document.getElementById('tc-rp-download');
            if (downloadBtn) downloadBtn.style.display = '';
        } catch (err) {
            TCTP.toast('Rotation failed: ' + err.message, '\u274C');
            TCTP.hideProgress('tc-rp-progress');
            setStat('tc-rp-stat-status', 'Error');
        }
    });

    var downloadBtn = document.getElementById('tc-rp-download');
    if (downloadBtn) downloadBtn.addEventListener('click', function () {
        if (!lastBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
        var name = (file ? file.name.replace(/\.pdf$/i, '') : 'document') + '-rotated.pdf';
        TCTP.downloadBlob(lastBlob, name);
    });

})();
