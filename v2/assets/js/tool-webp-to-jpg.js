/**
 * WebP to JPG Converter — Tool JS (Premium)
 * Features: quality slider with presets, bg color, iOS downscale, preview tabs.
 *
 * @package TextCraft_Tools_Pro
 */
(function () {
    'use strict';

    var prefix = 'tc-w2j-';
    var dropEl = document.getElementById(prefix + 'drop');
    if (!dropEl) return;

    var convertBtn    = document.getElementById(prefix + 'convert');
    var downloadBtn   = document.getElementById(prefix + 'download');
    var qualitySlider = document.getElementById(prefix + 'quality');
    var qualityBadge  = document.getElementById(prefix + 'quality-val');
    var bgcolorInput  = document.getElementById(prefix + 'bgcolor');
    var bgcolorHex    = document.getElementById(prefix + 'bgcolor-hex');
    var iosToggle     = document.getElementById(prefix + 'ios');
    var PROGRESS_ID   = prefix + 'progress';

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

    document.querySelectorAll('[data-group="w2j-quality"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var group = btn.closest('[data-group="w2j-quality"]');
            if (group) group.querySelectorAll('.sel').forEach(function (b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            quality = parseInt(btn.getAttribute('data-val'), 10) || 92;
            if (qualitySlider) qualitySlider.value = quality;
            if (qualityBadge) qualityBadge.textContent = quality;
        });
    });

    // ── Background Color ──────────────────────────────────────

    if (bgcolorInput && bgcolorHex) {
        bgcolorInput.addEventListener('input', function () {
            bgcolorHex.textContent = bgcolorInput.value;
        });
    }

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
        var q = quality / 100;
        var bg = bgcolorInput ? bgcolorInput.value : '#ffffff';
        var doDownscale = iosToggle ? iosToggle.checked : true;

        TCTP.showProgress(PROGRESS_ID);
        TCTP.setProgress(PROGRESS_ID, 10, 'Reading image...');

        var img = new Image();
        img.onload = function () {
            TCTP.setProgress(PROGRESS_ID, 30, 'Processing...');

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

            ctx.fillStyle = bg;
            ctx.fillRect(0, 0, w, h);
            ctx.drawImage(img, 0, 0, w, h);

            TCTP.setProgress(PROGRESS_ID, 60, 'Encoding JPG...');

            canvas.toBlob(function (blob) {
                if (!blob) {
                    TCTP.hideProgress(PROGRESS_ID);
                    TCTP.toast('JPG encoding failed.', '\u274C');
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
                TCTP.toast('Converted to JPG! Saved ' + savedPct + '%');
                URL.revokeObjectURL(img.src);
            }, 'image/jpeg', q);
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
            var name = (file ? file.name.replace(/\.webp$/i, '') : 'image') + '.jpg';
            TCTP.downloadBlob(convertedBlob, name);
        });
    }

    // ── Clear all ─────────────────────────────────────────────

    var clearBtn = document.getElementById(prefix + 'clear');
    if (clearBtn) clearBtn.addEventListener('click', function () {
        file = null;
        convertedBlob = null;
        if (convertedUrl) { URL.revokeObjectURL(convertedUrl); convertedUrl = null; }
        TCTP.hideFileRow(prefix + 'file');

        quality = 92;
        if (qualitySlider) qualitySlider.value = quality;
        if (qualityBadge) qualityBadge.textContent = quality;
        var qGroup = document.querySelector('[data-group="w2j-quality"]');
        if (qGroup) {
            qGroup.querySelectorAll('.tc-btn').forEach(function (b) { b.classList.remove('sel'); });
            var def = qGroup.querySelector('.tc-btn[data-val="92"]');
            if (def) def.classList.add('sel');
        }
        if (bgcolorInput) bgcolorInput.value = '#ffffff';
        if (bgcolorHex) bgcolorHex.textContent = '#ffffff';
        if (window.TCTP && TCTP.syncPremiumColorPickers) TCTP.syncPremiumColorPickers(prefix + 'bgcolor');
        if (iosToggle) iosToggle.checked = true;

        ['stat-orig', 'stat-comp', 'stat-saved'].forEach(function (k) {
            var el = document.getElementById(prefix + k);
            if (el) el.textContent = '-';
        });
        TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Idle');

        var origPv = document.getElementById('tc-preview-orig');
        if (origPv) origPv.innerHTML = '<div class="tc-preview-placeholder">Original WebP will appear here</div>';
        var resPv = document.getElementById('tc-preview-result');
        if (resPv) resPv.innerHTML = '<div class="tc-preview-placeholder">Converted JPG will appear here</div>';
        TCTP.switchToOriginalTab();
        TCTP.toast('Cleared.', '\uD83E\uDDF9');
    });

})();
