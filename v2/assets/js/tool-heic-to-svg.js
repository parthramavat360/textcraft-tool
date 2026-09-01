/**
 * HEIC to SVG Converter — Tool JS (Premium)
 * Features: embed mode (pixel-perfect), trace modes, detail cards, color mode cards, paths slider.
 * Strategy: try native canvas first (Safari 11+), fallback to heic2any, then trace/embed.
 *
 * @package TextCraft_Tools_Pro
 */
(function () {
    'use strict';

    var prefix = 'tc-h2s-';
    var dropEl = document.getElementById(prefix + 'drop');
    if (!dropEl) return;

    var convertBtn   = document.getElementById(prefix + 'convert');
    var downloadBtn  = document.getElementById(prefix + 'download');
    var pathsSlider  = document.getElementById(prefix + 'paths');
    var pathsBadge   = document.getElementById(prefix + 'paths-val');
    var PROGRESS_ID  = prefix + 'progress';

    var file = null;
    var resultSVG = null;
    var convertedUrl = null;
    var heicLoaded = false;
    var tracerLoaded = false;
    var converting = false;

    var MAX_TRACE_DIM = 1200;

    var DETAIL_PRESETS = {
        high:   { scale: 3, colorsampling: 2, numberofcolors: 128, mincolorratio: 0, colorquantcycles: 5, pathomit: 0, ltres: 0.5, qtres: 0.5, blurradius: 0, strokewidth: 0 },
        medium: { scale: 2, colorsampling: 2, numberofcolors: 64,  mincolorratio: 0, colorquantcycles: 4, pathomit: 1, ltres: 1,   qtres: 1,   blurradius: 0, strokewidth: 0 },
        low:    { scale: 1, colorsampling: 2, numberofcolors: 32,  mincolorratio: 0, colorquantcycles: 3, pathomit: 4, ltres: 2,   qtres: 2,   blurradius: 0, strokewidth: 0 }
    };

    document.querySelectorAll('[data-group="h2s-detail"] .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            var group = card.closest('[data-group="h2s-detail"]');
            if (group) group.querySelectorAll('.sel').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
        });
    });

    document.querySelectorAll('[data-group="h2s-color"] .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            var group = card.closest('[data-group="h2s-color"]');
            if (group) group.querySelectorAll('.sel').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
        });
    });

    if (pathsSlider && pathsBadge) {
        pathsSlider.addEventListener('input', function () {
            pathsBadge.textContent = pathsSlider.value;
        });
    }

    function loadHeic(cb) {
        if (heicLoaded) { cb(); return; }
        var s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/heic2any@0.0.4/dist/heic2any.min.js';
        s.onload = function () { heicLoaded = true; cb(); };
        s.onerror = function () { TCTP.toast('Failed to load HEIC decoder.', '\u274C'); };
        document.head.appendChild(s);
    }

    function loadTracer(cb) {
        if (tracerLoaded) { cb(); return; }
        var s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/imagetracerjs@1.2.6/imagetracer_v1.2.6.js';
        s.onload = function () { tracerLoaded = true; cb(); };
        s.onerror = function () { TCTP.toast('Failed to load imagetracer library.', '\u274C'); };
        document.head.appendChild(s);
    }

    TCTP.initDropZone(prefix + 'drop', prefix + 'drop-input', function (f) {
        if (!/\.heic$/i.test(f.name) && !/\.heif$/i.test(f.name) && f.type !== 'image/heic') {
            TCTP.toast('Please select a HEIC/HEIF file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        resultSVG = null;
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
        resultSVG = null;
        if (convertedUrl) { URL.revokeObjectURL(convertedUrl); convertedUrl = null; }
        TCTP.hideFileRow(prefix + 'file');
    });

    function getSelectedVal(group) {
        var sel = document.querySelector('[data-group="' + group + '"] .tc-rsz-mode-card.sel');
        return sel ? sel.getAttribute('data-val') : '';
    }

    function setStat(id, val) {
        var el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    if (convertBtn) {
        convertBtn.addEventListener('click', function () {
            if (converting) return;
            if (!file) { TCTP.toast('Please select a HEIC file first.', '\u26A0\uFE0F'); return; }
            tryNativeFirst();
        });
    }

    function tryNativeFirst() {
        converting = true;
        TCTP.showProgress(PROGRESS_ID);
        TCTP.setProgress(PROGRESS_ID, 5, 'Reading HEIC...');

        var img = new Image();
        img.onload = function () {
            if (img.naturalWidth > 0 && img.naturalHeight > 0) {
                TCTP.setProgress(PROGRESS_ID, 20, 'Decoded natively...');
                doConvertFromImage(img);
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
            if (converting && !resultSVG) {
                try { URL.revokeObjectURL(blobUrl); } catch(e) {}
                fallbackHeic2Any();
            }
        }, 5000);
    }

    function fallbackHeic2Any() {
        TCTP.setProgress(PROGRESS_ID, 15, 'Loading HEIC library...');
        loadHeic(function () {
            TCTP.setProgress(PROGRESS_ID, 25, 'Decoding HEIC...');
            heic2any({ blob: file, toType: 'image/png' }).then(function (pngBlob) {
                var url = URL.createObjectURL(pngBlob);
                var img = new Image();
                img.onload = function () {
                    TCTP.setProgress(PROGRESS_ID, 40, 'HEIC decoded...');
                    doConvertFromImage(img);
                    URL.revokeObjectURL(url);
                };
                img.onerror = function () {
                    TCTP.hideProgress(PROGRESS_ID);
                    TCTP.toast('Failed to decode HEIC image.', '\u274C');
                    converting = false;
                    URL.revokeObjectURL(url);
                };
                img.src = url;
            }).catch(function (err) {
                TCTP.hideProgress(PROGRESS_ID);
                var msg = err && err.message ? err.message : 'Unknown error';
                if (msg.indexOf('format not supported') !== -1 || msg.indexOf('ERR_LIBHEIF') !== -1) {
                    TCTP.toast('This HEIC variant is not supported. Try converting on your device first.', '\u26A0\uFE0F');
                } else {
                    TCTP.toast('Conversion failed: ' + msg, '\u274C');
                }
                converting = false;
            });
        });
    }

    function doConvertFromImage(img) {
        var colorMode = getSelectedVal('h2s-color') || 'embed';
        if (colorMode === 'embed') {
            doEmbedFromImage(img);
        } else {
            loadTracer(function () { doTraceFromImage(img); });
        }
    }

    function doEmbedFromImage(img) {
        TCTP.setProgress(PROGRESS_ID, 50, 'Building SVG...');

        var reader = new FileReader();
        reader.onload = function (ev) {
            var dataUrl = ev.target.result;
            var w = img.naturalWidth;
            var h = img.naturalHeight;

            var svgStr = '<?xml version="1.0" encoding="UTF-8"?>\n'
                + '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" '
                + 'width="' + w + '" height="' + h + '" viewBox="0 0 ' + w + ' ' + h + '">\n'
                + '  <image width="' + w + '" height="' + h + '" href="' + dataUrl + '"/>\n'
                + '</svg>';

            resultSVG = svgStr;

            var origSize = file.size;
            var svgKB = (svgStr.length / 1024).toFixed(1);

            setStat(prefix + 'stat-orig', TCTP.formatSize(origSize));
            setStat(prefix + 'stat-comp', svgKB + ' KB');
            setStat(prefix + 'stat-fmt', 'Embedded');

            TCTP.updateResultPanel(TCTP.formatSize(origSize), svgKB + ' KB', 'Embedded', 'Done');

            TCTP.setProgress(PROGRESS_ID, 80, 'Rendering preview...');
            var svgBlob = new Blob([svgStr], { type: 'image/svg+xml;charset=utf-8' });
            if (convertedUrl) URL.revokeObjectURL(convertedUrl);
            convertedUrl = URL.createObjectURL(svgBlob);
            TCTP.showResultPreview(convertedUrl);
            TCTP.switchToResultTab();

            TCTP.setProgress(PROGRESS_ID, 100, 'Done!');
            setTimeout(function () { TCTP.hideProgress(PROGRESS_ID); }, 600);
            TCTP.toast('Pixel-perfect SVG created!');
            converting = false;
        };
        reader.readAsDataURL(file);
    }

    function doTraceFromImage(img) {
        var detailKey = getSelectedVal('h2s-detail') || 'medium';
        var colorMode = getSelectedVal('h2s-color') || 'color';
        var maxPaths = pathsSlider ? parseInt(pathsSlider.value, 10) : 500;

        var opts = DETAIL_PRESETS[detailKey] || DETAIL_PRESETS['medium'];
        opts = JSON.parse(JSON.stringify(opts));
        opts.pathomit = Math.max(0, Math.round((1 - maxPaths / 2000) * 20));

        if (colorMode === 'bw') {
            opts.numberofcolors = 2;
        } else if (colorMode === 'grayscale') {
            opts.numberofcolors = 4;
        }

        var origW = img.naturalWidth;
        var origH = img.naturalHeight;
        var w = origW;
        var h = origH;

        if (w > MAX_TRACE_DIM || h > MAX_TRACE_DIM) {
            var sc = MAX_TRACE_DIM / Math.max(w, h);
            w = Math.round(w * sc);
            h = Math.round(h * sc);
        }

        var scaled = (w !== origW || h !== origH);
        var sizeInfo = scaled ? ' (scaled ' + w + '\u00D7' + h + ')' : ' (' + w + '\u00D7' + h + ')';

        TCTP.setProgress(PROGRESS_ID, 50, 'Preparing canvas' + sizeInfo + '...');

        var c = document.createElement('canvas');
        c.width = w;
        c.height = h;
        var ctx = c.getContext('2d');
        ctx.drawImage(img, 0, 0, w, h);

        TCTP.setProgress(PROGRESS_ID, 60, 'Tracing' + sizeInfo + '...');

        setTimeout(function () {
            try {
                var imgData = ctx.getImageData(0, 0, w, h);
                TCTP.setProgress(PROGRESS_ID, 70, 'Generating SVG paths...');
                var svgStr = window.ImageTracer.imagedataToSVG(imgData, opts);

                resultSVG = svgStr;

                var origSize = file.size;
                var svgKB = (svgStr.length / 1024).toFixed(1);
                var fmt = colorMode === 'bw' ? 'B&W' : colorMode === 'grayscale' ? 'Gray' : 'Color';

                setStat(prefix + 'stat-orig', TCTP.formatSize(origSize));
                setStat(prefix + 'stat-comp', svgKB + ' KB');
                setStat(prefix + 'stat-fmt', fmt);

                TCTP.updateResultPanel(TCTP.formatSize(origSize), svgKB + ' KB', fmt, 'Done');

                TCTP.setProgress(PROGRESS_ID, 85, 'Rendering preview...');
                var svgBlob = new Blob([svgStr], { type: 'image/svg+xml;charset=utf-8' });
                if (convertedUrl) URL.revokeObjectURL(convertedUrl);
                convertedUrl = URL.createObjectURL(svgBlob);
                TCTP.showResultPreview(convertedUrl);
                TCTP.switchToResultTab();

                TCTP.setProgress(PROGRESS_ID, 100, 'Done!');
                setTimeout(function () { TCTP.hideProgress(PROGRESS_ID); }, 600);
                TCTP.toast('Converted to SVG!' + (scaled ? ' (downscaled for speed)' : ''));
            } catch (err) {
                TCTP.hideProgress(PROGRESS_ID);
                TCTP.toast('Conversion failed: ' + err.message, '\u274C');
            } finally {
                converting = false;
            }
        }, 50);
    }

    if (downloadBtn) {
        downloadBtn.addEventListener('click', function () {
            if (!resultSVG) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
            var blob = new Blob([resultSVG], { type: 'image/svg+xml' });
            var name = (file ? file.name.replace(/\.heic$/i, '').replace(/\.heif$/i, '') : 'image') + '.svg';
            TCTP.downloadBlob(blob, name);
        });
    }

    // ── Clear all ─────────────────────────────────────────────

    var clearBtn = document.getElementById(prefix + 'clear');
    if (clearBtn) clearBtn.addEventListener('click', function () {
        file = null;
        resultSVG = null;
        if (convertedUrl) { URL.revokeObjectURL(convertedUrl); convertedUrl = null; }
        converting = false;
        TCTP.hideFileRow(prefix + 'file');

        function resetGroup(group, val) {
            var g = document.querySelector('[data-group="' + group + '"]');
            if (!g) return;
            g.querySelectorAll('.tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            var def = g.querySelector('.tc-rsz-mode-card[data-val="' + val + '"]');
            if (def) def.classList.add('sel');
        }
        resetGroup('h2s-detail', 'high');
        resetGroup('h2s-color', 'embed');
        if (pathsSlider) pathsSlider.value = 500;
        if (pathsBadge) pathsBadge.textContent = 500;

        ['stat-orig', 'stat-comp', 'stat-fmt'].forEach(function (k) {
            var el = document.getElementById(prefix + k);
            if (el) el.textContent = '-';
        });
        TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Idle');

        var origPv = document.getElementById('tc-preview-orig');
        if (origPv) origPv.innerHTML = '<div class="tc-preview-placeholder">Original HEIC will appear here</div>';
        var resPv = document.getElementById('tc-preview-result');
        if (resPv) resPv.innerHTML = '<div class="tc-preview-placeholder">Converted SVG will appear here</div>';
        TCTP.switchToOriginalTab();
        TCTP.toast('Cleared.', '\uD83E\uDDF9');
    });

})();
