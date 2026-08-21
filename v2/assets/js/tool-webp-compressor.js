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
    var quality = 90;

    var drop = document.getElementById('tc-wp-drop');
    if (!drop) return;

    var qualitySlider = document.getElementById('tc-wp-quality');
    var qualityVal = document.getElementById('tc-wp-quality-val');
    if (qualitySlider) {
        qualitySlider.addEventListener('input', function () {
            quality = parseInt(qualitySlider.value);
            if (qualityVal) qualityVal.textContent = quality;
        });
    }

    TCTP.initDropZone('tc-wp-drop', 'tc-wp-drop-input', function (f) {
        if (!f.type.match(/image\/webp/) && !/\.webp$/i.test(f.name)) {
            TCTP.toast('Please select a WebP file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        compressedBlob = null;
        TCTP.showFileRow('tc-wp-file', f);
        var statsEl = document.getElementById('tc-wp-stats');
        if (statsEl) statsEl.style.display = 'none';
    }, 'image/webp,.webp');

    var removeBtn = document.querySelector('#tc-wp-file .tctp-x, #tc-wp-file .tc-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        file = null;
        compressedBlob = null;
        TCTP.hideFileRow('tc-wp-file');
        var statsEl = document.getElementById('tc-wp-stats');
        if (statsEl) statsEl.style.display = 'none';
    });

    var compressBtn = document.getElementById('tc-wp-compress');
    if (compressBtn) compressBtn.addEventListener('click', function () {
        if (!file) { TCTP.toast('Please select a WebP file first.', '\u26A0\uFE0F'); return; }

        TCTP.showProgress('tc-wp-progress');
        TCTP.setProgress('tc-wp-progress', 20, 'Reading image...');

        var reader = new FileReader();
        reader.onload = function (e) {
            TCTP.setProgress('tc-wp-progress', 50, 'Compressing...');
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
                        TCTP.hideProgress('tc-wp-progress');
                        return;
                    }
                    compressedBlob = blob;
                    var origSize = file.size;
                    var compSize = blob.size;
                    var saved = origSize > compSize ? ((1 - compSize / origSize) * 100).toFixed(1) : '0';

                    var origEl = document.getElementById('tc-wp-stat-orig');
                    var compEl = document.getElementById('tc-wp-stat-comp');
                    var savedEl = document.getElementById('tc-wp-stat-saved');
                    if (origEl) origEl.textContent = TCTP.formatSize(origSize);
                    if (compEl) compEl.textContent = TCTP.formatSize(compSize);
                    if (savedEl) savedEl.textContent = saved + '%';

                    var statsEl = document.getElementById('tc-wp-stats');
                    if (statsEl) statsEl.style.display = '';

                    TCTP.updateResultPanel(TCTP.formatSize(origSize), TCTP.formatSize(compSize), saved + '%', 'Done');
                                        TCTP.showResultPreview(URL.createObjectURL(compressedBlob));
                    TCTP.switchToResultTab();
                    TCTP.setProgress('tc-wp-progress', 100, 'Done!');
                    TCTP.toast('Compressed! Saved ' + saved + '%');
                }, 'image/webp', quality / 100);
            };
            img.onerror = function () {
                TCTP.toast('Failed to decode WebP image.', '\u274C');
                TCTP.hideProgress('tc-wp-progress');
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });

    var downloadBtn = document.getElementById('tc-wp-download');
    if (downloadBtn) downloadBtn.addEventListener('click', function () {
        if (!compressedBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
        var name = (file ? file.name.replace(/\.webp$/i, '') : 'image') + '-compressed.webp';
        TCTP.downloadBlob(compressedBlob, name);
    });

})();
