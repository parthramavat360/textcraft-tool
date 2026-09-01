/**
 * HEIC to PNG Converter — Tool JS (Premium)
 * Strategy: try native canvas decode first (Safari 11+), fallback to heic2any.
 *
 * @package TextCraft_Tools_Pro
 */
(function () {
    'use strict';

    var prefix = 'tc-h2p-';
    var dropEl = document.getElementById(prefix + 'drop');
    if (!dropEl) return;

    var convertBtn    = document.getElementById(prefix + 'convert');
    var downloadBtn   = document.getElementById(prefix + 'download');
    var iosToggle     = document.getElementById(prefix + 'ios');
    var PROGRESS_ID   = prefix + 'progress';

    var file = null;
    var convertedBlob = null;
    var convertedUrl = null;
    var libLoaded = false;

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

    function loadLib(cb) {
        if (libLoaded) { cb(); return; }
        var s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/heic2any@0.0.4/dist/heic2any.min.js';
        s.onload = function () { libLoaded = true; cb(); };
        s.onerror = function () { TCTP.toast('Failed to load HEIC decoder.', '\u274C'); };
        document.head.appendChild(s);
    }

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

        img.onload = function () {
            if (img.naturalWidth > 0 && img.naturalHeight > 0) {
                TCTP.setProgress(PROGRESS_ID, 40, 'Decoded natively, encoding PNG...');
                encodeToPng(img);
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
            if (!convertedBlob) {
                try { URL.revokeObjectURL(blobUrl); } catch(e) {}
                fallbackHeic2Any();
            }
        }, 5000);
    }

    function fallbackHeic2Any() {
        TCTP.setProgress(PROGRESS_ID, 20, 'Loading HEIC library...');
        loadLib(function () {
            TCTP.setProgress(PROGRESS_ID, 40, 'Decoding with libheif...');

            heic2any({
                blob: file,
                toType: 'image/png'
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
                    TCTP.toast('This HEIC variant is not supported by the decoder. Try converting on your device first.', '\u26A0\uFE0F');
                } else {
                    TCTP.toast('Conversion failed: ' + msg, '\u274C');
                }
            });
        });
    }

    function encodeToPng(img) {
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

        TCTP.setProgress(PROGRESS_ID, 70, 'Encoding PNG...');

        canvas.toBlob(function (blob) {
            if (!blob) {
                TCTP.hideProgress(PROGRESS_ID);
                TCTP.toast('PNG encoding failed.', '\u274C');
                return;
            }
            finishConvert(blob);
        }, 'image/png');
    }

    function finishConvert(blob) {
        convertedBlob = blob;
        var origSize = file.size;
        var compSize = blob.size;
        var pctVal = origSize > 0 ? ((compSize - origSize) / origSize * 100) : 0;
        var savedLabel = (pctVal >= 0 ? '+' : '') + pctVal.toFixed(1) + '%';

        var statOrig = document.getElementById(prefix + 'stat-orig');
        var statComp = document.getElementById(prefix + 'stat-comp');
        var statSaved = document.getElementById(prefix + 'stat-saved');
        if (statOrig) statOrig.textContent = TCTP.formatSize(origSize);
        if (statComp) statComp.textContent = TCTP.formatSize(compSize);
        if (statSaved) statSaved.textContent = savedLabel;

        TCTP.updateResultPanel(
            TCTP.formatSize(origSize),
            TCTP.formatSize(compSize),
            savedLabel,
            'Done'
        );

        if (convertedUrl) URL.revokeObjectURL(convertedUrl);
        convertedUrl = URL.createObjectURL(blob);
        TCTP.showResultPreview(convertedUrl);
        TCTP.switchToResultTab();

        TCTP.setProgress(PROGRESS_ID, 100, 'Done!');
        setTimeout(function () { TCTP.hideProgress(PROGRESS_ID); }, 600);
        TCTP.toast('Converted to PNG!');
    }

    if (downloadBtn) {
        downloadBtn.addEventListener('click', function () {
            if (!convertedBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
            var name = (file ? file.name.replace(/\.heic$/i, '').replace(/\.heif$/i, '') : 'image') + '.png';
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

        if (iosToggle) iosToggle.checked = true;

        ['stat-orig', 'stat-comp', 'stat-saved'].forEach(function (k) {
            var el = document.getElementById(prefix + k);
            if (el) el.textContent = '-';
        });
        TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Idle');

        var origPv = document.getElementById('tc-preview-orig');
        if (origPv) origPv.innerHTML = '<div class="tc-preview-placeholder">Original HEIC will appear here</div>';
        var resPv = document.getElementById('tc-preview-result');
        if (resPv) resPv.innerHTML = '<div class="tc-preview-placeholder">Converted PNG will appear here</div>';
        TCTP.switchToOriginalTab();
        TCTP.toast('Cleared.', '\uD83E\uDDF9');
    });

})();