/**
 * JPG Compressor â€” Tool JS
 *
 * Client-side JPG compression using canvas.toDataURL with quality control.
 * Supports batch processing and ZIP download via JSZip.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var file = null;
    var compressedBlob = null;
    var quality = 92;

    var qualitySlider = document.getElementById('tc-jpg-quality');
    var qualityVal = document.getElementById('tc-jpg-quality-val');
    if (!qualitySlider) return;

    qualitySlider.addEventListener('input', function () {
        quality = parseInt(qualitySlider.value);
        qualityVal.textContent = quality;
    });

    // â”€â”€ Drop zone â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    TCTP.initDropZone('tc-jpg-drop', 'tc-jpg-drop-input', function (f) {
        if (!f.type.match(/image\/jpe?g/)) {
            TCTP.toast('Please select a JPG/JPEG file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        TCTP.showFileRow('tc-jpg-file', f);
    }, 'image/jpeg,.jpg,.jpeg');

    var removeBtn = document.querySelector('#tc-jpg-file .tctp-x, #tc-jpg-file .tc-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        file = null;
        compressedBlob = null;
        TCTP.hideFileRow('tc-jpg-file');
    });

    // â”€â”€ Compress â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    document.getElementById('tc-jpg-compress').addEventListener('click', function () {
        if (!file) { TCTP.toast('Please select a JPG file first.', '\u26A0\uFE0F'); return; }

        TCTP.showProgress('tc-jpg-progress');
        TCTP.setProgress('tc-jpg-progress', 30, 'Reading image...');

        var reader = new FileReader();
        reader.onload = function (e) {
            TCTP.setProgress('tc-jpg-progress', 50, 'Compressing...');
            var img = new Image();
            img.onload = function () {
                var canvas = document.createElement('canvas');
                canvas.width = img.naturalWidth;
                canvas.height = img.naturalHeight;
                var ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0);

                canvas.toBlob(function (blob) {
                    compressedBlob = blob;
                    var origSize = file.size;
                    var compSize = blob.size;
                    var saved = ((1 - compSize / origSize) * 100).toFixed(1);

                    document.getElementById('tc-jpg-stat-orig').textContent = TCTP.formatSize(origSize);
                    document.getElementById('tc-jpg-stat-comp').textContent = TCTP.formatSize(compSize);
                    document.getElementById('tc-jpg-stat-saved').textContent = saved + '%';

                    TCTP.updateResultPanel(TCTP.formatSize(origSize), TCTP.formatSize(compSize), saved + '%', 'Done');
                                        TCTP.showResultPreview(URL.createObjectURL(compressedBlob));
                    TCTP.switchToResultTab();
                    TCTP.setProgress('tc-jpg-progress', 100, 'Done!');
                    TCTP.toast('Compressed! Saved ' + saved + '%');
                }, 'image/jpeg', quality / 100);
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });

    // â”€â”€ Download â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    document.getElementById('tc-jpg-download').addEventListener('click', function () {
        if (!compressedBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
        var name = (file ? file.name.replace(/\.jpe?g$/i, '') : 'image') + '-compressed.jpg';
        TCTP.downloadBlob(compressedBlob, name);
    });

})();