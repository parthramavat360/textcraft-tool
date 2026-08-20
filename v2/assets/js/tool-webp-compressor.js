/**
 * WebP Compressor — Tool JS
 *
 * Client-side WebP compression via canvas re-encode with quality control.
 * Drop zone, quality slider, compress, download, stats.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var file = null;
    var compressedBlob = null;
    var quality = 80;

    var drop = document.getElementById('tc-wpc-drop');
    if (!drop) return;

    var qualitySlider = document.getElementById('tc-wpc-quality');
    var qualityVal = document.getElementById('tc-wpc-quality-val');
    if (qualitySlider) {
        qualitySlider.addEventListener('input', function () {
            quality = parseInt(qualitySlider.value);
            if (qualityVal) qualityVal.textContent = quality;
        });
    }

    document.querySelectorAll('.tctp-modes[data-group="wpc-quality"] .tctp-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            quality = parseInt(btn.getAttribute('data-val')) || 80;
            if (qualitySlider) qualitySlider.value = quality;
            if (qualityVal) qualityVal.textContent = quality;
        });
    });

    TCTP.initDropZone('tc-wpc-drop', 'tc-wpc-drop-input', function (f) {
        if (!f.type.match(/image\/webp/) && !/\.webp$/i.test(f.name)) {
            TCTP.toast('Please select a WebP file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        compressedBlob = null;
        TCTP.showFileRow('tc-wpc-file', f);
        var statsEl = document.getElementById('tc-wpc-stats');
        if (statsEl) statsEl.style.display = 'none';
    }, 'image/webp,.webp');

    var removeBtn = document.querySelector('#tc-wpc-file .tctp-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        file = null;
        compressedBlob = null;
        TCTP.hideFileRow('tc-wpc-file');
        var statsEl = document.getElementById('tc-wpc-stats');
        if (statsEl) statsEl.style.display = 'none';
    });

    var compressBtn = document.getElementById('tc-wpc-compress');
    if (compressBtn) compressBtn.addEventListener('click', function () {
        if (!file) { TCTP.toast('Please select a WebP file first.', '\u26A0\uFE0F'); return; }

        TCTP.showProgress('tc-wpc-progress');
        TCTP.setProgress('tc-wpc-progress', 20, 'Reading image...');

        var reader = new FileReader();
        reader.onload = function (e) {
            TCTP.setProgress('tc-wpc-progress', 50, 'Compressing...');
            var img = new Image();
            img.onload = function () {
                var canvas = document.createElement('canvas');
                canvas.width = img.naturalWidth;
                canvas.height = img.naturalHeight;
                var ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0);

                canvas.toBlob(function (blob) {
                    if (!blob) {
                        TCTP.toast('WebP compression not supported in your browser.', '\u274C');
                        TCTP.hideProgress('tc-wpc-progress');
                        return;
                    }
                    compressedBlob = blob;
                    var origSize = file.size;
                    var compSize = blob.size;
                    var saved = origSize > compSize ? ((1 - compSize / origSize) * 100).toFixed(1) : '0';

                    var origEl = document.getElementById('tc-wpc-stat-orig');
                    var compEl = document.getElementById('tc-wpc-stat-comp');
                    var savedEl = document.getElementById('tc-wpc-stat-saved');
                    if (origEl) origEl.textContent = TCTP.formatSize(origSize);
                    if (compEl) compEl.textContent = TCTP.formatSize(compSize);
                    if (savedEl) savedEl.textContent = saved + '%';

                    var statsEl = document.getElementById('tc-wpc-stats');
                    if (statsEl) statsEl.style.display = '';

                    TCTP.setProgress('tc-wpc-progress', 100, 'Done!');
                    TCTP.toast('Compressed! Saved ' + saved + '%');
                }, 'image/webp', quality / 100);
            };
            img.onerror = function () {
                TCTP.toast('Failed to decode WebP image.', '\u274C');
                TCTP.hideProgress('tc-wpc-progress');
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });

    var downloadBtn = document.getElementById('tc-wpc-download');
    if (downloadBtn) downloadBtn.addEventListener('click', function () {
        if (!compressedBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
        var name = (file ? file.name.replace(/\.webp$/i, '') : 'image') + '-compressed.webp';
        TCTP.downloadBlob(compressedBlob, name);
    });

})();
