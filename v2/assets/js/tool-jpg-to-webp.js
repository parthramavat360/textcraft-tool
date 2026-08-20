/**
 * JPG to WebP Converter — Tool JS
 *
 * Client-side JPG-to-WebP conversion using canvas.toBlob('image/webp', quality).
 * Quality adjustable with presets.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var file = null;
    var convertedBlob = null;
    var quality = 82;

    var qualitySlider = document.getElementById('tc-j2w-quality');
    var qualityVal = document.getElementById('tc-j2w-quality-val');
    if (!qualitySlider) return;

    qualitySlider.addEventListener('input', function () {
        quality = parseInt(qualitySlider.value);
        qualityVal.textContent = quality;
    });

    // Quality presets
    document.querySelectorAll('.tctp-modes[data-group="j2w-quality"] .tctp-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            quality = parseInt(btn.getAttribute('data-val')) || 82;
            qualitySlider.value = quality;
            qualityVal.textContent = quality;
        });
    });

    // ── Drop zone ────────────────────────────────────────────

    TCTP.initDropZone('tc-j2w-drop', 'tc-j2w-drop-input', function (f) {
        if (!f.type.match(/image\/jpe?g/)) {
            TCTP.toast('Please select a JPG/JPEG file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        TCTP.showFileRow('tc-j2w-file', f);
    }, 'image/jpeg,.jpg,.jpeg');

    var removeBtn = document.querySelector('#tc-j2w-file .tctp-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        file = null;
        convertedBlob = null;
        TCTP.hideFileRow('tc-j2w-file');
    });

    // ── Convert ──────────────────────────────────────────────

    document.getElementById('tc-j2w-convert').addEventListener('click', function () {
        if (!file) { TCTP.toast('Please select a JPG file first.', '\u26A0\uFE0F'); return; }

        TCTP.showProgress('tc-j2w-progress');
        TCTP.setProgress('tc-j2w-progress', 30, 'Reading image...');

        var reader = new FileReader();
        reader.onload = function (e) {
            TCTP.setProgress('tc-j2w-progress', 60, 'Converting...');
            var img = new Image();
            img.onload = function () {
                var canvas = document.createElement('canvas');
                canvas.width = img.naturalWidth;
                canvas.height = img.naturalHeight;
                var ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0);

                canvas.toBlob(function (blob) {
                    if (!blob) {
                        TCTP.toast('WebP is not supported in your browser.', '\u274C');
                        TCTP.hideProgress('tc-j2w-progress');
                        return;
                    }
                    convertedBlob = blob;
                    var origSize = file.size;
                    var compSize = blob.size;
                    var saved = origSize > compSize ? ((1 - compSize / origSize) * 100).toFixed(1) : '0';

                    document.getElementById('tc-j2w-stat-orig').textContent = TCTP.formatSize(origSize);
                    document.getElementById('tc-j2w-stat-comp').textContent = TCTP.formatSize(compSize);
                    document.getElementById('tc-j2w-stat-saved').textContent = saved + '%';
                    TCTP.setProgress('tc-j2w-progress', 100, 'Done!');
                    TCTP.toast('Converted to WebP! Saved ' + saved + '%');
                }, 'image/webp', quality / 100);
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });

    // Download
    document.getElementById('tc-j2w-download').addEventListener('click', function () {
        if (!convertedBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
        var name = (file ? file.name.replace(/\.jpe?g$/i, '') : 'image') + '.webp';
        TCTP.downloadBlob(convertedBlob, name);
    });

})();