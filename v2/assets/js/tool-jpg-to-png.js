/**
 * JPG to PNG Converter — Tool JS
 *
 * Client-side JPG-to-PNG conversion using canvas.toBlob('image/png').
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var file = null;
    var convertedBlob = null;

    // ── Drop zone ────────────────────────────────────────────

    TCTP.initDropZone('tc-j2p-drop', 'tc-j2p-drop-input', function (f) {
        if (!f.type.match(/image\/jpe?g/)) {
            TCTP.toast('Please select a JPG/JPEG file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        TCTP.showFileRow('tc-j2p-file', f);
    }, 'image/jpeg,.jpg,.jpeg');

    var removeBtn = document.querySelector('#tc-j2p-file .tctp-x, #tc-j2p-file .tc-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        file = null;
        convertedBlob = null;
        TCTP.hideFileRow('tc-j2p-file');
    });

    // ── Convert ──────────────────────────────────────────────

    document.getElementById('tc-j2p-convert').addEventListener('click', function () {
        if (!file) { TCTP.toast('Please select a JPG file first.', '\u26A0\uFE0F'); return; }

        TCTP.showProgress('tc-j2p-progress');
        TCTP.setProgress('tc-j2p-progress', 30, 'Reading image...');

        var reader = new FileReader();
        reader.onload = function (e) {
            TCTP.setProgress('tc-j2p-progress', 60, 'Converting...');
            var img = new Image();
            img.onload = function () {
                var canvas = document.createElement('canvas');
                canvas.width = img.naturalWidth;
                canvas.height = img.naturalHeight;
                var ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0);

                canvas.toBlob(function (blob) {
                    convertedBlob = blob;
                    var origSize = file.size;
                    var compSize = blob.size;
                    var diff = ((compSize - origSize) / origSize * 100).toFixed(1);

                    document.getElementById('tc-j2p-stat-orig').textContent = TCTP.formatSize(origSize);
                    document.getElementById('tc-j2p-stat-comp').textContent = TCTP.formatSize(compSize);
                    document.getElementById('tc-j2p-stat-diff').textContent = (diff > 0 ? '+' : '') + diff + '%';
                    TCTP.setProgress('tc-j2p-progress', 100, 'Done!');
                    TCTP.toast('Converted to PNG!');
                }, 'image/png');
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });

    // Download
    document.getElementById('tc-j2p-download').addEventListener('click', function () {
        if (!convertedBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
        var name = (file ? file.name.replace(/\.jpe?g$/i, '') : 'image') + '.png';
        TCTP.downloadBlob(convertedBlob, name);
    });

})();