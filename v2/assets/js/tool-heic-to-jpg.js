/**
 * HEIC to JPG Converter — Tool JS (Premium)
 * Features: quality slider with presets, iOS downscale, preview tabs.
 * Strategy: try native canvas decode first (Safari 11+), fallback to heic2any.
 *
 * @package TextCraft_Tools_Pro
 */
(function () {
    'use strict';

    var prefix = 'tc-h2j-';
    var dropEl = document.getElementById(prefix + 'drop');
    if (!dropEl) return;

    var convertBtn    = document.getElementById(prefix + 'convert');
    var downloadBtn   = document.getElementById(prefix + 'download');
    var qualitySlider = document.getElementById(prefix + 'quality');
    var qualityBadge  = document.getElementById(prefix + 'quality-val');
    var iosToggle     = document.getElementById(prefix + 'ios');
    var PROGRESS_ID   = prefix + 'progress';

    var file = null;
    var convertedBlob = null;
    var convertedUrl = null;
    var quality = 92;
    var libLoaded = false;

    if (qualitySlider && qualityBadge) {
        qualitySlider.addEventListener('input', function () {
            quality = parseInt(qualitySlider.value, 10);
            qualityBadge.textContent = quality;
        });
    }

    document.querySelectorAll('[data-group="h2j-quality"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var group = btn.closest('[data-group="h2j-quality"]');
            if (group) group.querySelectorAll('.sel').forEach(function (b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            quality = parseInt(btn.getAttribute('data-val'), 10) || 92;
            if (qualitySlider) qualitySlider.value = quality;
            if (qualityBadge) qualityBadge.textContent = quality;
        });
    });

    function loadLib(cb) {
        if (libLoaded) { cb(); return; }
        var s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/heic2any@0.0.4/dist/heic2any.min.js';
        s.onload = function () { libLoaded = true; cb(); };
        s.onerror = function () { TCTP.toast('Failed to load HEIC decoder.', '\u274C'); };
        document.head.appendChild(s);
    }

    TCTP.initDropZone(prefix + 'drop', prefix + 'drop-input', function (f) {
        if (!/\.heic$/i.test(f.name) && !/\.heif$/i.test(f.name) && f.type !== 'image/heic') {
            TCTP.toast('Please select a HEIC/HEIF file.', '\u26A0\uFE0F');
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
    }, 'image/heic,.heic,.HEIF,.heif');

    var removeBtn = document.querySelector('#' + prefix + 'file .tc-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        file = null;
        convertedBlob = null;
        if (convertedUrl) { URL.revokeObjectURL(convertedUrl); convertedUrl = null; }
        TCTP.hideFileRow(prefix + 'file');
    });

    if (convertBtn) {
        convertBtn.addEventListener('click', function () {
            if (!file) { TCTP.toast('Please select a HEIC file first.', '\u26A0\uFE0F'); return; }
            tryNativeFirst();
        });
    }

    function tryNativeFirst() {
        TCTP.showProgress(PROGRESS_ID);
        TCTP.setProgress(PROGRESS_ID, 10, 'Reading HEIC...');

        var img = new Image();
        var nativeFailed = false;

        img.onload = function () {
            if (img.naturalWidth > 0 && img.naturalHeight > 0) {
                TCTP.setProgress(PROGRESS_ID, 40, 'Decoded natively, encoding JPG...');
                encodeToJpg(img);
            } else {
                fallbackHeic2Any();
            }
            URL.revokeObjectURL(img.src);
        };

        img.onerror = function () {
            URL.revokeObjectURL(img.src);
            fallbackHeic2Any();
        };

        var blobUrl = URL.createObjectURL(file);
        img.src = blobUrl;

        setTimeout(function () {
            if (!nativeFailed && !convertedBlob) {
                try { URL.revokeObjectURL(blobUrl); } catch(e) {}
                fallbackHeic2Any();
            }
        }, 5000);
    }

    function fallbackHeic2Any() {
        TCTP.setProgress(PROGRESS_ID, 20, 'Loading HEIC library...');
        loadLib(function () {
            TCTP.setProgress(PROGRESS_ID, 40, 'Decoding with libheif...');
            var q = quality / 100;

            heic2any({
                blob: file,
                toType: 'image/jpeg',
                quality: q
            }).then(function (blob) {
                if (!blob) {
                    TCTP.hideProgress(PROGRESS_ID);
                    TCTP.toast('HEIC decoding failed. This HEIC variant may not be supported.', '\u274C');
                    return;
                }
                finishConvert(blob);
            }).catch(function (err) {
                TCTP.hideProgress(PROGRESS_ID);
                var msg = err && err.message ? err.message : 'Unknown error';
                if (msg.indexOf('format not supported') !== -1 || msg.indexOf('ERR_LIBHEIF') !== -1) {
                    TCTP.toast('This HEIC variant is not supported by the decoder. Try converting to JPG on your device first, or use a different HEIC file.', '\u26A0\uFE0F');
                } else {
                    TCTP.toast('Conversion failed: ' + msg, '\u274C');
                }
            });
        });
    }

    function encodeToJpg(img) {
        var q = quality / 100;
        var w = img.naturalWidth;
        var h = img.naturalHeight;
        var doDownscale = iosToggle ? iosToggle.checked : true;

        if (doDownscale) {
            var maxDim = 4096;
            if (Math.max(w, h) > maxDim) {
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

        TCTP.setProgress(PROGRESS_ID, 70, 'Encoding JPG...');

        canvas.toBlob(function (blob) {
            if (!blob) {
                TCTP.hideProgress(PROGRESS_ID);
                TCTP.toast('JPG encoding failed.', '\u274C');
                return;
            }
            finishConvert(blob);
        }, 'image/jpeg', q);
    }

    function finishConvert(blob) {
        convertedBlob = blob;
        var origSize = file.size;
        var compSize = blob.size;
        var savedPct = origSize > 0 ? ((1 - compSize / origSize) * 100).toFixed(1) : '0';

        var statOrig = document.getElementById(prefix + 'stat-orig');
        var statComp = document.getElementById(prefix + 'stat-comp');
        var statSaved = document.getElementById(prefix + 'stat-saved');
        if (statOrig) statOrig.textContent = TCTP.formatSize(origSize);
        if (statComp) statComp.textContent = TCTP.formatSize(compSize);
        if (statSaved) statSaved.textContent = savedPct + '%';

        TCTP.updateResultPanel(
            TCTP.formatSize(origSize),
            TCTP.formatSize(compSize),
            savedPct + '%',
            'Done'
        );

        if (convertedUrl) URL.revokeObjectURL(convertedUrl);
        convertedUrl = URL.createObjectURL(blob);
        TCTP.showResultPreview(convertedUrl);
        TCTP.switchToResultTab();

        TCTP.setProgress(PROGRESS_ID, 100, 'Done!');
        setTimeout(function () { TCTP.hideProgress(PROGRESS_ID); }, 600);
        TCTP.toast('Converted to JPG! Saved ' + savedPct + '%');
    }

    if (downloadBtn) {
        downloadBtn.addEventListener('click', function () {
            if (!convertedBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
            var name = (file ? file.name.replace(/\.heic$/i, '').replace(/\.heif$/i, '') : 'image') + '.jpg';
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
        var qGroup = document.querySelector('[data-group="h2j-quality"]');
        if (qGroup) {
            qGroup.querySelectorAll('.tc-btn').forEach(function (b) { b.classList.remove('sel'); });
            var def = qGroup.querySelector('.tc-btn[data-val="92"]');
            if (def) def.classList.add('sel');
        }
        if (iosToggle) iosToggle.checked = true;

        ['stat-orig', 'stat-comp', 'stat-saved'].forEach(function (k) {
            var el = document.getElementById(prefix + k);
            if (el) el.textContent = '-';
        });
        TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Idle');

        var origPv = document.getElementById('tc-preview-orig');
        if (origPv) origPv.innerHTML = '<div class="tc-preview-placeholder">Original HEIC will appear here</div>';
        var resPv = document.getElementById('tc-preview-result');
        if (resPv) resPv.innerHTML = '<div class="tc-preview-placeholder">Converted JPG will appear here</div>';
        TCTP.switchToOriginalTab();
        TCTP.toast('Cleared.', '\uD83E\uDDF9');
    });

})();