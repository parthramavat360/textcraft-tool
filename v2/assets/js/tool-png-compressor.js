/**
 * PNG Compressor — Tool JS
 *
 * Quality slider controls UPNG quantization (cnum = max colors).
 * 100% = lossless (cnum=0), lower = fewer colors = smaller file.
 * Downscale toggle with slider. Original + result preview.
 *
 * @package TextCraft_Tools_Pro
 */
(function () {
    'use strict';

    var file = null;
    var compressedBlob = null;
    var quality = 92;
    var maxDim = 1200;

    var drop = document.getElementById('tc-png-drop');
    if (!drop) return;

    function loadScript(src) {
        return new Promise(function (resolve, reject) {
            if (document.querySelector('script[src="' + src + '"]')) { resolve(); return; }
            var s = document.createElement('script');
            s.src = src;
            s.onload = resolve;
            s.onerror = reject;
            document.head.appendChild(s);
        });
    }

    function setStat(id, val) {
        var el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    function qualityToColors(q) {
        if (q >= 100) return 0;
        if (q >= 90) return 256;
        if (q >= 75) return 192;
        if (q >= 60) return 128;
        if (q >= 45) return 96;
        if (q >= 30) return 64;
        if (q >= 20) return 48;
        return 32;
    }

    // ── Drop zone ──

    TCTP.initDropZone('tc-png-drop', 'tc-png-drop-input', function (f) {
        if (f.type !== 'image/png' && !/\.png$/i.test(f.name)) {
            TCTP.toast('Please select a PNG file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        compressedBlob = null;
        TCTP.showFileRow('tc-png-file', f);
        var dlBtn = document.getElementById('tc-png-download');
        if (dlBtn) dlBtn.style.display = 'none';
        setStat('tc-png-stat-orig', '-');
        setStat('tc-png-stat-comp', '-');
        setStat('tc-png-stat-saved', '-');

        var reader = new FileReader();
        reader.onload = function (ev) {
            TCTP.showOriginalPreview(ev.target.result);
            TCTP.switchToOriginalTab();
        };
        reader.readAsDataURL(f);
    }, 'image/png,.png');

    var removeBtn = document.querySelector('#tc-png-file .tc-x');
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            file = null;
            compressedBlob = null;
            TCTP.hideFileRow('tc-png-file');
            setStat('tc-png-stat-orig', '-');
            setStat('tc-png-stat-comp', '-');
            setStat('tc-png-stat-saved', '-');
        });
    }

    // ── Quality slider ──

    var qualitySlider = document.getElementById('tc-png-quality');
    var qualityVal = document.getElementById('tc-png-quality-val');
    if (qualitySlider && qualityVal) {
        qualitySlider.addEventListener('input', function () {
            quality = parseInt(qualitySlider.value) || 92;
            qualityVal.textContent = quality + '%';
        });
    }

    // ── Downscale toggle ──

    var resizeToggle = document.getElementById('tc-png-resize');
    var resizeVal = document.getElementById('tc-png-resize-val');
    var sliderSection = document.getElementById('tc-png-slider-section');
    var maxDimSlider = document.getElementById('tc-png-maxdim');
    var maxDimVal = document.getElementById('tc-png-dim-val');

    if (resizeToggle && resizeVal) {
        resizeToggle.addEventListener('change', function () {
            if (resizeToggle.checked) {
                resizeVal.textContent = 'On';
                if (sliderSection) sliderSection.style.display = '';
            } else {
                resizeVal.textContent = 'Off';
                if (sliderSection) sliderSection.style.display = 'none';
            }
        });
    }

    if (maxDimSlider && maxDimVal) {
        maxDimSlider.addEventListener('input', function () {
            maxDim = parseInt(maxDimSlider.value) || 1200;
            maxDimVal.textContent = maxDim + 'px';
            var dimUserVal = document.getElementById('tc-png-maxdim-val');
            if (dimUserVal) dimUserVal.textContent = maxDim;
        });
    }

    // ── Compress ──

    var compressBtn = document.getElementById('tc-png-compress');
    if (compressBtn) {
        compressBtn.addEventListener('click', async function () {
            if (!file) { TCTP.toast('Please select a PNG file first.', '\u26A0\uFE0F'); return; }

            TCTP.showProgress('tc-png-progress');
            TCTP.setProgress('tc-png-progress', 5, 'Loading libraries...');

            try {
                if (!window.pako) {
                    await loadScript('https://cdn.jsdelivr.net/npm/pako@2.1.0/dist/pako.min.js');
                }
                if (!window.UPNG) {
                    await loadScript('https://cdn.jsdelivr.net/npm/upng-js@2.1.0/UPNG.js');
                }
            } catch (e) {
                TCTP.hideProgress('tc-png-progress');
                TCTP.toast('Failed to load compression library.', '\u274C');
                return;
            }

            TCTP.setProgress('tc-png-progress', 15, 'Reading PNG...');

            var ab = await file.arrayBuffer();
            var bytes = new Uint8Array(ab);
            var cnum = qualityToColors(quality);
            var needsDownscale = resizeToggle && resizeToggle.checked;

            try {
                var decoded = UPNG.decode(bytes.buffer);
                var w = decoded.width;
                var h = decoded.height;
                var frames = UPNG.toRGBA8(decoded);

                if (needsDownscale) {
                    TCTP.setProgress('tc-png-progress', 35, 'Downscaling...');
                    var scaled = await downscaleFrames(frames, w, h, maxDim);
                    frames = scaled.frames;
                    w = scaled.w;
                    h = scaled.h;
                }

                TCTP.setProgress('tc-png-progress', 60, 'Compressing...');
                var buf = UPNG.encode(frames, w, h, cnum);
                compressedBlob = new Blob([buf], { type: 'image/png' });
            } catch (e) {
                TCTP.hideProgress('tc-png-progress');
                TCTP.toast('Failed to process PNG: ' + e.message, '\u274C');
                return;
            }

            var origSize = file.size;
            var compSize = compressedBlob.size;

            if (compSize >= origSize && cnum === 0) {
                compressedBlob = new Blob([bytes], { type: 'image/png' });
                compSize = origSize;
            }

            var saved = origSize > compSize ? ((1 - compSize / origSize) * 100).toFixed(1) : '0';

            setStat('tc-png-stat-orig', TCTP.formatSize(origSize));
            setStat('tc-png-stat-comp', TCTP.formatSize(compSize));
            setStat('tc-png-stat-saved', saved + '%');

            TCTP.updateResultPanel(TCTP.formatSize(origSize), TCTP.formatSize(compSize), saved + '%', 'Done');

            TCTP.showResultPreview(URL.createObjectURL(compressedBlob));
            TCTP.switchToResultTab();
            TCTP.setProgress('tc-png-progress', 100, 'Done!');

            if (saved !== '0') {
                TCTP.toast('Compressed! Saved ' + saved + '%');
            } else {
                TCTP.toast('Image is already optimally compressed.');
            }

            var dlBtn = document.getElementById('tc-png-download');
            if (dlBtn) dlBtn.style.display = '';
        });
    }

    function downscaleFrames(frames, origW, origH, maxDimension) {
        return new Promise(function (resolve) {
            if (origW <= maxDimension && origH <= maxDimension) {
                resolve({ frames: frames, w: origW, h: origH });
                return;
            }
            var ratio = Math.min(maxDimension / origW, maxDimension / origH);
            var w = Math.round(origW * ratio);
            var h = Math.round(origH * ratio);

            var canvas = document.createElement('canvas');
            canvas.width = w;
            canvas.height = h;
            var ctx = canvas.getContext('2d');

            var firstFrame = frames[0];
            var imgBlob = new Blob([firstFrame], { type: 'image/png' });
            var url = URL.createObjectURL(imgBlob);
            var img = new Image();
            img.onload = function () {
                ctx.drawImage(img, 0, 0, w, h);
                var imgData = ctx.getImageData(0, 0, w, h);
                URL.revokeObjectURL(url);
                resolve({ frames: [imgData.data.buffer], w: w, h: h });
            };
            img.onerror = function () {
                URL.revokeObjectURL(url);
                resolve({ frames: frames, w: origW, h: origH });
            };
            img.src = url;
        });
    }

    // ── Download ──

    var downloadBtn = document.getElementById('tc-png-download');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function () {
            if (!compressedBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
            var name = (file ? file.name.replace(/\.png$/i, '') : 'image') + '-compressed.png';
            TCTP.downloadBlob(compressedBlob, name);
        });
    }

})();
