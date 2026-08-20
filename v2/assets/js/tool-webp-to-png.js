/**
 * WebP to PNG Converter — Tool JS
 *
 * Canvas-based WebP to PNG conversion with stats.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var file = null;
    var convertedBlob = null;

    var drop = document.getElementById('tc-w2p-drop');
    if (!drop) return;

    TCTP.initDropZone('tc-w2p-drop', 'tc-w2p-drop-input', function (f) {
        if (!f.type.match(/image\/webp/) && !/\.webp$/i.test(f.name)) {
            TCTP.toast('Please select a WebP file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        convertedBlob = null;
        TCTP.showFileRow('tc-w2p-file', f);
        var statsEl = document.getElementById('tc-w2p-stats');
        if (statsEl) statsEl.style.display = 'none';
    }, 'image/webp,.webp');

    var removeBtn = document.querySelector('#tc-w2p-file .tctp-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        file = null;
        convertedBlob = null;
        TCTP.hideFileRow('tc-w2p-file');
        var statsEl = document.getElementById('tc-w2p-stats');
        if (statsEl) statsEl.style.display = 'none';
    });

    var convertBtn = document.getElementById('tc-w2p-convert');
    if (convertBtn) convertBtn.addEventListener('click', function () {
        if (!file) { TCTP.toast('Please select a WebP file first.', '\u26A0\uFE0F'); return; }

        TCTP.showProgress('tc-w2p-progress');
        TCTP.setProgress('tc-w2p-progress', 20, 'Reading image...');

        var reader = new FileReader();
        reader.onload = function (e) {
            TCTP.setProgress('tc-w2p-progress', 50, 'Converting...');
            var img = new Image();
            img.onload = function () {
                var canvas = document.createElement('canvas');
                canvas.width = img.naturalWidth;
                canvas.height = img.naturalHeight;
                var ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0);

                canvas.toBlob(function (blob) {
                    if (!blob) {
                        TCTP.toast('Conversion failed. WebP may not be supported.', '\u274C');
                        TCTP.hideProgress('tc-w2p-progress');
                        return;
                    }
                    convertedBlob = blob;
                    var origSize = file.size;
                    var compSize = blob.size;
                    var diff = origSize - compSize;
                    var sign = diff >= 0 ? '-' : '+';

                    document.getElementById('tc-w2p-stat-orig').textContent = TCTP.formatSize(origSize);
                    document.getElementById('tc-w2p-stat-conv').textContent = TCTP.formatSize(compSize);
                    document.getElementById('tc-w2p-stat-diff').textContent = sign + TCTP.formatSize(Math.abs(diff));

                    var statsEl = document.getElementById('tc-w2p-stats');
                    if (statsEl) statsEl.style.display = '';

                    TCTP.setProgress('tc-w2p-progress', 100, 'Done!');
                    TCTP.toast('Converted to PNG!');
                }, 'image/png');
            };
            img.onerror = function () {
                TCTP.toast('Failed to decode WebP image.', '\u274C');
                TCTP.hideProgress('tc-w2p-progress');
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });

    var downloadBtn = document.getElementById('tc-w2p-download');
    if (downloadBtn) downloadBtn.addEventListener('click', function () {
        if (!convertedBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
        var name = (file ? file.name.replace(/\.webp$/i, '') : 'image') + '.png';
        TCTP.downloadBlob(convertedBlob, name);
    });

})();
