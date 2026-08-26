/**
 * JPG to AVIF Converter — Tool JS (Premium)
 * Uses @jsquash/avif (WASM) via esm.sh for real AVIF encoding.
 * WASM binary hosted locally to avoid CDN WASM 404.
 *
 * @package TextCraft_Tools_Pro
 */
(function () {
    'use strict';

    var prefix = 'tc-j2a-';
    var dropEl = document.getElementById(prefix + 'drop');
    if (!dropEl) return;

    var scriptSrc = document.currentScript && document.currentScript.src ? document.currentScript.src : '';
    var WASM_URL = scriptSrc ? scriptSrc.replace(/\/assets\/js\/[^\/]+$/, '/assets/wasm/avif_enc.wasm') : '/wp-content/plugins/textcrafttoolspro/assets/wasm/avif_enc.wasm';

    var convertBtn    = document.getElementById(prefix + 'convert');
    var downloadBtn   = document.getElementById(prefix + 'download');
    var qualitySlider = document.getElementById(prefix + 'quality');
    var qualityBadge  = document.getElementById(prefix + 'quality-badge');
    var iosToggle     = document.getElementById(prefix + 'ios');
    var PROGRESS_ID   = prefix + 'progress';

    var file = null;
    var convertedBlob = null;
    var convertedUrl = null;
    var quality = 80;
    var avifEncode = null;
    var avifInit = null;
    var encoderReady = false;

    // ── Quality Slider + Badge ────────────────────────────────

    if (qualitySlider && qualityBadge) {
        qualitySlider.addEventListener('input', function () {
            quality = parseInt(qualitySlider.value, 10);
            qualityBadge.textContent = quality;
        });
    }

    // ── Quality Presets ───────────────────────────────────────

    document.querySelectorAll('[data-group="j2a-quality"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var group = btn.closest('[data-group="j2a-quality"]');
            if (group) group.querySelectorAll('.sel').forEach(function (b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            quality = parseInt(btn.getAttribute('data-val'), 10) || 80;
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

    // ── Load WASM Encoder ─────────────────────────────────────

    function loadEncoder(cb) {
        if (encoderReady) { cb(); return; }

        TCTP.showProgress(PROGRESS_ID);
        TCTP.setProgress(PROGRESS_ID, 5, 'Loading AVIF encoder...');

        import('https://esm.sh/@jsquash/avif@2.1.1/encode')
            .then(function (mod) {
                TCTP.setProgress(PROGRESS_ID, 15, 'Downloading AVIF WASM...');

                avifEncode = mod.default;
                avifInit = mod.init;

                return fetch(WASM_URL);
            })
            .then(function (resp) {
                if (!resp.ok) throw new Error('WASM download failed: ' + resp.status);
                return resp.arrayBuffer();
            })
            .then(function (buf) {
                TCTP.setProgress(PROGRESS_ID, 35, 'Compiling WASM...');
                return WebAssembly.compile(buf);
            })
            .then(function (wasmModule) {
                TCTP.setProgress(PROGRESS_ID, 50, 'Initializing encoder...');
                return avifInit(wasmModule);
            })
            .then(function () {
                encoderReady = true;
                cb();
            })
            .catch(function (err) {
                TCTP.hideProgress(PROGRESS_ID);
                TCTP.toast('Failed to load AVIF encoder: ' + err.message, '\u274C');
            });
    }

    // ── Convert ───────────────────────────────────────────────

    if (convertBtn) {
        convertBtn.addEventListener('click', function () {
            if (!file) { TCTP.toast('Please select a JPG file first.', '\u26A0\uFE0F'); return; }
            loadEncoder(function () { doConvert(); });
        });
    }

    function doConvert() {
        var doDownscale = iosToggle ? iosToggle.checked : true;

        TCTP.setProgress(PROGRESS_ID, 55, 'Reading image...');

        var img = new Image();
        img.onload = function () {
            TCTP.setProgress(PROGRESS_ID, 65, 'Processing...');

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

            var imageData = ctx.getImageData(0, 0, w, h);
            TCTP.setProgress(PROGRESS_ID, 75, 'Encoding AVIF...');

            var opts = {
                quality: quality,
                speed: 6,
                subsample: 1
            };

            avifEncode(imageData, opts).then(function (buffer) {
                convertedBlob = new Blob([buffer], { type: 'image/avif' });
                var origSize = file.size;
                var compSize = convertedBlob.size;
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
                convertedUrl = URL.createObjectURL(convertedBlob);
                TCTP.showResultPreview(convertedUrl);
                TCTP.switchToResultTab();

                TCTP.setProgress(PROGRESS_ID, 100, 'Done!');
                setTimeout(function () { TCTP.hideProgress(PROGRESS_ID); }, 600);
                TCTP.toast('Converted to AVIF! Saved ' + savedPct + '%');
                URL.revokeObjectURL(img.src);
            }).catch(function (err) {
                TCTP.hideProgress(PROGRESS_ID);
                TCTP.toast('AVIF encoding failed: ' + err.message, '\u274C');
            });
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
            var name = (file ? file.name.replace(/\.jpe?g$/i, '') : 'image') + '.avif';
            TCTP.downloadBlob(convertedBlob, name);
        });
    }

})();
