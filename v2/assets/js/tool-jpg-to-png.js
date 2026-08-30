/**
 * JPG to PNG Converter — Tool JS
 *
 * Premium client-side JPG→PNG conversion using canvas.
 * Features: background color fill, iOS auto-downscale, preview tabs.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var prefix = 'tc-j2p-';
    var dropEl = document.getElementById(prefix + 'drop');
    if (!dropEl) return;

    var convertBtn    = document.getElementById(prefix + 'convert');
    var downloadBtn   = document.getElementById(prefix + 'download');
    var bgColorInput  = document.getElementById(prefix + 'bgcolor');
    var bgColorHex    = document.getElementById(prefix + 'bgcolor-hex');
    var iosToggle     = document.getElementById(prefix + 'ios');
    var fileRow       = document.getElementById(prefix + 'file');
    var progressWrap  = document.getElementById(prefix + 'progress');

    var file = null;
    var convertedBlob = null;
    var convertedUrl = null;

    // ── Drop Zone ─────────────────────────────────────────────

    TCTP.initDropZone(prefix + 'drop', prefix + 'drop-input', function (f) {
        if (!f.type.match(/image\/jpe?g/) && !/\.jpe?g$/i.test(f.name)) {
            TCTP.toast('Please select a JPG/JPEG file.', '\u26A0\uFE0F');
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
    }, 'image/jpeg,.jpg,.jpeg');

    var removeBtn = document.querySelector('#' + prefix + 'file .tc-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        file = null;
        convertedBlob = null;
        if (convertedUrl) { URL.revokeObjectURL(convertedUrl); convertedUrl = null; }
        TCTP.hideFileRow(prefix + 'file');
    });

    // ── Background Color Sync ─────────────────────────────────

    if (bgColorInput && bgColorHex) {
        bgColorInput.addEventListener('input', function () {
            bgColorHex.textContent = bgColorInput.value;
        });
    }

    // ── Convert ───────────────────────────────────────────────

    if (convertBtn) {
        convertBtn.addEventListener('click', function () {
            if (!file) { TCTP.toast('Please select a JPG file first.', '\u26A0\uFE0F'); return; }
            doConvert();
        });
    }

    var PROGRESS_ID = prefix + 'progress';

    function doConvert() {
        var bgColor = bgColorInput ? bgColorInput.value : '#ffffff';
        var doDownscale = iosToggle ? iosToggle.checked : true;

        TCTP.showProgress(PROGRESS_ID);
        TCTP.setProgress(PROGRESS_ID, 10, 'Reading image...');

        var img = new Image();
        img.onload = function () {
            TCTP.setProgress(PROGRESS_ID, 30, 'Processing...');

            var w = img.naturalWidth;
            var h = img.naturalHeight;

            // iOS downscale: cap long edge at 4096
            if (doDownscale) {
                var maxDim = 4096;
                if (w > maxDim || h > maxDim) {
                    var scale = maxDim / Math.max(w, h);
                    w = Math.round(w * scale);
                    h = Math.round(h * scale);
                }
            }

            TCTP.setProgress(PROGRESS_ID, 50, 'Converting...');

            var canvas = document.createElement('canvas');
            canvas.width = w;
            canvas.height = h;
            var ctx = canvas.getContext('2d');

            // Fill background
            ctx.fillStyle = bgColor;
            ctx.fillRect(0, 0, w, h);

            // Draw image
            ctx.drawImage(img, 0, 0, w, h);

            TCTP.setProgress(PROGRESS_ID, 75, 'Encoding PNG...');

            canvas.toBlob(function (blob) {
                if (!blob) {
                    TCTP.hideProgress(PROGRESS_ID);
                    TCTP.toast('Conversion failed. Try a different image.', '\u274C');
                    return;
                }

                convertedBlob = blob;
                var origSize = file.size;
                var compSize = blob.size;
                var diffPct = origSize > 0 ? ((compSize - origSize) / origSize * 100).toFixed(1) : '0';
                var diffStr = (diffPct > 0 ? '+' : '') + diffPct + '%';

                // Input panel stats
                var statOrig = document.getElementById(prefix + 'stat-orig');
                var statComp = document.getElementById(prefix + 'stat-comp');
                var statDiff = document.getElementById(prefix + 'stat-diff');
                if (statOrig) statOrig.textContent = TCTP.formatSize(origSize);
                if (statComp) statComp.textContent = TCTP.formatSize(compSize);
                if (statDiff) statDiff.textContent = diffStr;

                // Result panel stats
                TCTP.updateResultPanel(
                    TCTP.formatSize(origSize),
                    TCTP.formatSize(compSize),
                    diffStr,
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
            TCTP.toast('Failed to load image.', '\u274C');
        };

        img.src = URL.createObjectURL(file);
    }

    // ── Download ──────────────────────────────────────────────

    if (downloadBtn) {
        downloadBtn.addEventListener('click', function () {
            if (!convertedBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
            var nameInput = document.getElementById(prefix + 'name');
            var base = (nameInput && nameInput.value.trim()) ? nameInput.value.trim().replace(/\.png$/i, '') : (file ? file.name.replace(/\.jpe?g$/i, '') : 'image');
            TCTP.downloadBlob(convertedBlob, base + '.png');
        });
    }

    // ── Clear all ─────────────────────────────────────────────

    var clearBtn = document.getElementById(prefix + 'clear');
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            file = null;
            convertedBlob = null;
            if (convertedUrl) { URL.revokeObjectURL(convertedUrl); convertedUrl = null; }
            var row = document.getElementById(prefix + 'file');
            if (row) { row.style.display = 'none'; row.classList.remove('visible'); }
            var dl = document.getElementById(prefix + 'download');
            if (dl) dl.style.display = 'none';
            var statOrig = document.getElementById(prefix + 'stat-orig');
            var statComp = document.getElementById(prefix + 'stat-comp');
            var statDiff = document.getElementById(prefix + 'stat-diff');
            if (statOrig) statOrig.textContent = '-';
            if (statComp) statComp.textContent = '-';
            if (statDiff) statDiff.textContent = '-';
            var nameInput = document.getElementById(prefix + 'name');
            if (nameInput) nameInput.value = '';
            var origP = document.getElementById('tc-preview-orig');
            if (origP) origP.innerHTML = '<span style="color:var(--muted);font-size:13px">Original preview will appear here</span>';
            var resP = document.getElementById('tc-preview-result');
            if (resP) resP.innerHTML = '<span style="color:var(--muted);font-size:13px">Result preview will appear here</span>';
            TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Ready');
            TCTP.switchToOriginalTab();
        });
    }

})();
