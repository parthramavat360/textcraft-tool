/**
 * JPG to SVG Converter — Tool JS
 *
 * Premium client-side JPG→SVG conversion using imagetracerjs.
 * Features: detail mode cards, color mode cards, paths slider, preview tabs.
 * Auto-downscales large images before tracing to prevent browser freeze.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var prefix = 'tc-j2svg-';
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
    var libLoaded = false;
    var converting = false;

    var MAX_TRACE_DIM = 1200;

    // ── Detail Level Cards ────────────────────────────────────

    var detailCards = document.querySelectorAll('[data-group="j2svg-detail"] .tc-rsz-mode-card');
    detailCards.forEach(function (card) {
        card.addEventListener('click', function () {
            var group = card.closest('[data-group="j2svg-detail"]');
            if (group) group.querySelectorAll('.sel').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
        });
    });

    // ── Color Mode Cards ──────────────────────────────────────

    var colorCards = document.querySelectorAll('[data-group="j2svg-color"] .tc-rsz-mode-card');
    colorCards.forEach(function (card) {
        card.addEventListener('click', function () {
            var group = card.closest('[data-group="j2svg-color"]');
            if (group) group.querySelectorAll('.sel').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
        });
    });

    // ── Paths Slider Badge ────────────────────────────────────

    if (pathsSlider && pathsBadge) {
        pathsSlider.addEventListener('input', function () {
            pathsBadge.textContent = pathsSlider.value;
        });
    }

    // ── Load imagetracerjs ────────────────────────────────────

    function loadLib(cb) {
        if (libLoaded) { cb(); return; }
        var s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/imagetracerjs@1.2.6/imagetracer_v1.2.6.js';
        s.onload = function () { libLoaded = true; cb(); };
        s.onerror = function () { TCTP.toast('Failed to load imagetracer library.', '\u274C'); };
        document.head.appendChild(s);
    }

    // ── Drop Zone ─────────────────────────────────────────────

    TCTP.initDropZone(prefix + 'drop', prefix + 'drop-input', function (f) {
        if (!f.type.match(/image\/jpe?g/) && !/\.jpe?g$/i.test(f.name)) {
            TCTP.toast('Please select a JPG/JPEG file.', '\u26A0\uFE0F');
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
    }, 'image/jpeg,.jpg,.jpeg');

    var removeBtn = document.querySelector('#' + prefix + 'file .tc-x');
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            file = null;
            resultSVG = null;
            if (convertedUrl) { URL.revokeObjectURL(convertedUrl); convertedUrl = null; }
            TCTP.hideFileRow(prefix + 'file');
        });
    }

    // ── Detail presets ────────────────────────────────────────

    var DETAIL_PRESETS = {
        high:   { scale: 3, colorsampling: 2, numberofcolors: 128, mincolorratio: 0, colorquantcycles: 5, pathomit: 0, ltres: 0.5, qtres: 0.5, blurradius: 0, strokewidth: 0 },
        medium: { scale: 2, colorsampling: 2, numberofcolors: 64,  mincolorratio: 0, colorquantcycles: 4, pathomit: 1, ltres: 1,   qtres: 1,   blurradius: 0, strokewidth: 0 },
        low:    { scale: 1, colorsampling: 2, numberofcolors: 32,  mincolorratio: 0, colorquantcycles: 3, pathomit: 4, ltres: 2,   qtres: 2,   blurradius: 0, strokewidth: 0 }
    };

    // ── Convert ───────────────────────────────────────────────

    if (convertBtn) {
        convertBtn.addEventListener('click', function () {
            if (converting) return;
            if (!file) { TCTP.toast('Please select a JPG file first.', '\u26A0\uFE0F'); return; }

            var colorMode = getSelectedVal('j2svg-color') || 'embed';
            if (colorMode === 'embed') {
                doEmbed();
            } else {
                loadLib(doTrace);
            }
        });
    }

    function getSelectedVal(group) {
        var sel = document.querySelector('[data-group="' + group + '"] .tc-rsz-mode-card.sel');
        return sel ? sel.getAttribute('data-val') : '';
    }

    // ── Embed mode: pixel-perfect SVG ────────────────────────

    function doEmbed() {
        converting = true;
        TCTP.showProgress(PROGRESS_ID);
        TCTP.setProgress(PROGRESS_ID, 10, 'Reading image...');

        var reader = new FileReader();
        reader.onload = function (ev) {
            try {
                var dataUrl = ev.target.result;

                TCTP.setProgress(PROGRESS_ID, 40, 'Building SVG...');

                var img = new Image();
                img.onload = function () {
                    var w = img.naturalWidth;
                    var h = img.naturalHeight;

                    TCTP.setProgress(PROGRESS_ID, 60, 'Embedding image...');

                    var svgStr = '<?xml version="1.0" encoding="UTF-8"?>\n'
                        + '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" '
                        + 'width="' + w + '" height="' + h + '" viewBox="0 0 ' + w + ' ' + h + '">\n'
                        + '  <image width="' + w + '" height="' + h + '" href="' + dataUrl + '"/>\n'
                        + '</svg>';

                    resultSVG = svgStr;

                    var origSize = file.size;
                    var svgSize  = svgStr.length;
                    var svgKB    = (svgSize / 1024).toFixed(1);

                    setStat(prefix + 'stat-orig', TCTP.formatSize(origSize));
                    setStat(prefix + 'stat-comp', svgKB + ' KB');
                    setStat(prefix + 'stat-fmt', 'Embedded');

                    TCTP.updateResultPanel(
                        TCTP.formatSize(origSize),
                        svgKB + ' KB',
                        'Embedded',
                        'Done'
                    );

                    TCTP.setProgress(PROGRESS_ID, 80, 'Rendering preview...');
                    var svgBlob = new Blob([svgStr], { type: 'image/svg+xml;charset=utf-8' });
                    if (convertedUrl) URL.revokeObjectURL(convertedUrl);
                    convertedUrl = URL.createObjectURL(svgBlob);
                    TCTP.showResultPreview(convertedUrl);
                    TCTP.switchToResultTab();

                    TCTP.setProgress(PROGRESS_ID, 100, 'Done!');
                    setTimeout(function () { TCTP.hideProgress(PROGRESS_ID); }, 600);
                    TCTP.toast('Pixel-perfect SVG created!');
                    URL.revokeObjectURL(img.src);
                    converting = false;
                };
                img.onerror = function () {
                    TCTP.hideProgress(PROGRESS_ID);
                    TCTP.toast('Failed to load image.', '\u274C');
                    converting = false;
                };
                img.src = dataUrl;
            } catch (err) {
                TCTP.hideProgress(PROGRESS_ID);
                TCTP.toast('Embed failed: ' + err.message, '\u274C');
                converting = false;
            }
        };
        reader.readAsDataURL(file);
    }

    // ── Trace mode: imagetracerjs vectorization ───────────────

    function doTrace() {
        if (converting) return;
        converting = true;

        var detailKey = getSelectedVal('j2svg-detail') || 'medium';
        var colorMode = getSelectedVal('j2svg-color') || 'color';
        var maxPaths  = pathsSlider ? parseInt(pathsSlider.value, 10) : 500;

        var opts = DETAIL_PRESETS[detailKey] || DETAIL_PRESETS['medium'];
        opts = JSON.parse(JSON.stringify(opts));

        opts.pathomit = Math.max(0, Math.round((1 - maxPaths / 2000) * 20));

        if (colorMode === 'bw') {
            opts.numberofcolors = 2;
        } else if (colorMode === 'grayscale') {
            opts.numberofcolors = 4;
        }

        TCTP.showProgress(PROGRESS_ID);
        TCTP.setProgress(PROGRESS_ID, 10, 'Reading image...');

        var img = new Image();
        img.onload = function () {
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

            TCTP.setProgress(PROGRESS_ID, 25, 'Preparing canvas' + sizeInfo + '...');

            var c = document.createElement('canvas');
            c.width = w;
            c.height = h;
            var ctx = c.getContext('2d');
            ctx.drawImage(img, 0, 0, w, h);

            TCTP.setProgress(PROGRESS_ID, 40, 'Tracing' + sizeInfo + ' — this may take a moment...');

            setTimeout(function () {
                try {
                    var imgData = ctx.getImageData(0, 0, w, h);

                    TCTP.setProgress(PROGRESS_ID, 60, 'Generating SVG paths...');

                    var svgStr = window.ImageTracer.imagedataToSVG(imgData, opts);

                    resultSVG = svgStr;

                    var origSize = file.size;
                    var svgSize  = svgStr.length;
                    var svgKB    = (svgSize / 1024).toFixed(1);
                    var fmt      = colorMode === 'bw' ? 'B&W' : colorMode === 'grayscale' ? 'Gray' : 'Color';

                    setStat(prefix + 'stat-orig', TCTP.formatSize(origSize));
                    setStat(prefix + 'stat-comp', svgKB + ' KB');
                    setStat(prefix + 'stat-fmt', fmt);

                    TCTP.updateResultPanel(
                        TCTP.formatSize(origSize),
                        svgKB + ' KB',
                        fmt,
                        'Done'
                    );

                    TCTP.setProgress(PROGRESS_ID, 85, 'Rendering preview...');
                    var svgBlob = new Blob([svgStr], { type: 'image/svg+xml;charset=utf-8' });
                    if (convertedUrl) URL.revokeObjectURL(convertedUrl);
                    convertedUrl = URL.createObjectURL(svgBlob);
                    TCTP.showResultPreview(convertedUrl);
                    TCTP.switchToResultTab();

                    TCTP.setProgress(PROGRESS_ID, 100, 'Done!');
                    setTimeout(function () { TCTP.hideProgress(PROGRESS_ID); }, 600);
                    TCTP.toast('Converted to SVG!' + (scaled ? ' (downscaled for speed)' : ''));
                    URL.revokeObjectURL(img.src);
                } catch (err) {
                    TCTP.hideProgress(PROGRESS_ID);
                    TCTP.toast('Conversion failed: ' + err.message, '\u274C');
                } finally {
                    converting = false;
                }
            }, 50);
        };

        img.onerror = function () {
            TCTP.hideProgress(PROGRESS_ID);
            TCTP.toast('Failed to load image.', '\u274C');
            converting = false;
        };

        img.src = URL.createObjectURL(file);
    }

    function setStat(id, val) {
        var el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    // ── Download ──────────────────────────────────────────────

    if (downloadBtn) {
        downloadBtn.addEventListener('click', function () {
            if (!resultSVG) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
            var blob = new Blob([resultSVG], { type: 'image/svg+xml' });
            var name = (file ? file.name.replace(/\.jpe?g$/i, '') : 'image') + '.svg';
            TCTP.downloadBlob(blob, name);
        });
    }

})();
