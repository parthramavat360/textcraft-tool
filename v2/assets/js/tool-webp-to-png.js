/**
 * WebP to PNG Converter — Tool JS (Premium)
 * Features: iOS downscale, preview tabs.
 *
 * @package TextCraft_Tools_Pro
 */
(function () {
    'use strict';

    var prefix = 'tc-w2p-';
    var dropEl = document.getElementById(prefix + 'drop');
    if (!dropEl) return;

    var convertBtn  = document.getElementById(prefix + 'convert');
    var downloadBtn = document.getElementById(prefix + 'download');
    var iosToggle   = document.getElementById(prefix + 'ios');
    var PROGRESS_ID = prefix + 'progress';

    var file = null;
    var convertedBlob = null;
    var convertedUrl = null;

    // ── Drop Zone ─────────────────────────────────────────────

    TCTP.initDropZone(prefix + 'drop', prefix + 'drop-input', function (f) {
        if (f.type !== 'image/webp' && !/\.webp$/i.test(f.name)) {
            TCTP.toast('Please select a WebP file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        convertedBlob = null;
        if (convertedUrl) { URL.revokeObjectURL(convertedUrl); convertedUrl = null; }
        TCTP.showFileRow(prefix + 'file', f);

        var reader = new FileReader();
        reader.onload = function (ev) {
            TCTP.showOriginalPreview(ev.target.result);
            TCTP.switchToOriginalTab();
        };
        reader.readAsDataURL(f);
    }, 'image/webp,.webp');

    var removeBtn = document.querySelector('#' + prefix + 'file .tc-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        file = null;
        convertedBlob = null;
        if (convertedUrl) { URL.revokeObjectURL(convertedUrl); convertedUrl = null; }
        TCTP.hideFileRow(prefix + 'file');
    });

    // ── Convert ───────────────────────────────────────────────

    if (convertBtn) {
        convertBtn.addEventListener('click', function () {
            if (!file) { TCTP.toast('Please select a WebP file first.', '\u26A0\uFE0F'); return; }
            doConvert();
        });
    }

    function doConvert() {
        var doDownscale = iosToggle ? iosToggle.checked : true;

        TCTP.showProgress(PROGRESS_ID);
        TCTP.setProgress(PROGRESS_ID, 10, 'Reading image...');

        var reader = new FileReader();
        reader.onload = function (e) {
            TCTP.setProgress(PROGRESS_ID, 30, 'Decoding WebP...');

            var img = new Image();
            img.onload = function () {
                TCTP.setProgress(PROGRESS_ID, 50, 'Processing...');

                var w = img.naturalWidth;
                var h = img.naturalHeight;

                if (doDownscale) {
                    var maxDim = 4096;
                    if (w > maxDim || h > maxDim) {
                        var scale = maxDim / Math.max(w, h);
                        w = Math.round(w * scale);
                        h = Math.round(h * scale);
                    }
                }

                var canvas = document.createElement('canvas');
                canvas.width = w;
                canvas.height = h;
                var ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, w, h);

                TCTP.setProgress(PROGRESS_ID, 70, 'Encoding PNG...');

                canvas.toBlob(function (blob) {
                    if (!blob) {
                        TCTP.hideProgress(PROGRESS_ID);
                        TCTP.toast('PNG encoding failed.', '\u274C');
                        return;
                    }

                    convertedBlob = blob;
                    var origSize = file.size;
                    var compSize = blob.size;
                    var pctVal = origSize > 0 ? ((compSize - origSize) / origSize * 100) : 0;
                    var savedLabel = (pctVal >= 0 ? '+' : '') + pctVal.toFixed(1) + '%';

                    // Input panel stats
                    var statOrig = document.getElementById(prefix + 'stat-orig');
                    var statComp = document.getElementById(prefix + 'stat-comp');
                    var statSaved = document.getElementById(prefix + 'stat-saved');
                    if (statOrig) statOrig.textContent = TCTP.formatSize(origSize);
                    if (statComp) statComp.textContent = TCTP.formatSize(compSize);
                    if (statSaved) statSaved.textContent = savedLabel;

                    // Result panel stats
                    TCTP.updateResultPanel(
                        TCTP.formatSize(origSize),
                        TCTP.formatSize(compSize),
                        savedLabel,
                        'Done'
                    );

                    // Preview tabs
                    if (convertedUrl) URL.revokeObjectURL(convertedUrl);
                    convertedUrl = URL.createObjectURL(blob);
                    TCTP.showResultPreview(convertedUrl);
                    TCTP.switchToResultTab();

                    TCTP.setProgress(PROGRESS_ID, 100, 'Done!');
                    setTimeout(function () { TCTP.hideProgress(PROGRESS_ID); }, 600);
                    TCTP.toast('Converted to PNG!');
                    URL.revokeObjectURL(img.src);
                }, 'image/png');
            };

            img.onerror = function () {
                TCTP.hideProgress(PROGRESS_ID);
                TCTP.toast('Failed to decode WebP image.', '\u274C');
            };

            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    // ── Download ──────────────────────────────────────────────

    if (downloadBtn) {
        downloadBtn.addEventListener('click', function () {
            if (!convertedBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
            var name = (file ? file.name.replace(/\.webp$/i, '') : 'image') + '.png';
            TCTP.downloadBlob(convertedBlob, name);
        });
    }

})();
