/**
 * JPG to WebP Converter — Tool JS
 *
 * Premium client-side JPG→WebP conversion using canvas.
 * Features: quality slider with presets, iOS auto-downscale, preview tabs.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var prefix = 'tc-j2w-';
    var dropEl = document.getElementById(prefix + 'drop');
    if (!dropEl) return;

    var convertBtn    = document.getElementById(prefix + 'convert');
    var downloadBtn   = document.getElementById(prefix + 'download');
    var qualitySlider = document.getElementById(prefix + 'quality');
    var qualityBadge  = document.getElementById(prefix + 'quality-badge');
    var iosToggle     = document.getElementById(prefix + 'ios');
    var progressWrap  = document.getElementById(prefix + 'progress');

    var file = null;
    var convertedBlob = null;
    var convertedUrl = null;
    var quality = 92;

    // ── Quality Slider + Badge ────────────────────────────────

    if (qualitySlider && qualityBadge) {
        qualitySlider.addEventListener('input', function () {
            quality = parseInt(qualitySlider.value, 10);
            qualityBadge.textContent = quality;
        });
    }

    // ── Quality Presets ───────────────────────────────────────

    document.querySelectorAll('[data-group="j2w-quality"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var group = btn.closest('[data-group="j2w-quality"]');
            if (group) group.querySelectorAll('.sel').forEach(function (b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            quality = parseInt(btn.getAttribute('data-val'), 10) || 92;
            if (qualitySlider) qualitySlider.value = quality;
            if (qualityBadge) qualityBadge.textContent = quality;
        });
    });

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

    // ── Convert ───────────────────────────────────────────────

    if (convertBtn) {
        convertBtn.addEventListener('click', function () {
            if (!file) { TCTP.toast('Please select a JPG file first.', '\u26A0\uFE0F'); return; }
            doConvert();
        });
    }

    var PROGRESS_ID = prefix + 'progress';

    function doConvert() {
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

            TCTP.setProgress(PROGRESS_ID, 50, 'Converting to WebP...');

            var canvas = document.createElement('canvas');
            canvas.width = w;
            canvas.height = h;
            var ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, w, h);

            TCTP.setProgress(PROGRESS_ID, 75, 'Encoding WebP...');

            canvas.toBlob(function (blob) {
                if (!blob) {
                    TCTP.hideProgress(PROGRESS_ID);
                    TCTP.toast('WebP is not supported in your browser.', '\u274C');
                    return;
                }

                convertedBlob = blob;
                var origSize = file.size;
                var compSize = blob.size;
                var savedPct = origSize > 0 ? ((1 - compSize / origSize) * 100).toFixed(1) : '0';

                // Input panel stats
                var statOrig = document.getElementById(prefix + 'stat-orig');
                var statComp = document.getElementById(prefix + 'stat-comp');
                var statSaved = document.getElementById(prefix + 'stat-saved');
                if (statOrig) statOrig.textContent = TCTP.formatSize(origSize);
                if (statComp) statComp.textContent = TCTP.formatSize(compSize);
                if (statSaved) statSaved.textContent = savedPct + '%';

                // Result panel stats
                TCTP.updateResultPanel(
                    TCTP.formatSize(origSize),
                    TCTP.formatSize(compSize),
                    savedPct + '%',
                    'Done'
                );

                // Preview tabs
                if (convertedUrl) URL.revokeObjectURL(convertedUrl);
                convertedUrl = URL.createObjectURL(blob);
                TCTP.showResultPreview(convertedUrl);
                TCTP.switchToResultTab();

                TCTP.setProgress(PROGRESS_ID, 100, 'Done!');
                setTimeout(function () { TCTP.hideProgress(PROGRESS_ID); }, 600);
                TCTP.toast('Converted to WebP! Saved ' + savedPct + '%');
                URL.revokeObjectURL(img.src);
            }, 'image/webp', quality / 100);
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
            var name = (file ? file.name.replace(/\.jpe?g$/i, '') : 'image') + '.webp';
            TCTP.downloadBlob(convertedBlob, name);
        });
    }

})();
