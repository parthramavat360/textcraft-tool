/**
 * WebP Compressor — Tool JS
 *
 * Quality slider, downscale toggle. Original + result preview.
 * Canvas.toBlob('image/webp') for compression.
 *
 * @package TextCraft_Tools_Pro
 */
(function () {
    'use strict';

    var file = null;
    var compressedBlob = null;
    var quality = 90;
    var maxDim = 1200;

    var drop = document.getElementById('tc-wp-drop');
    if (!drop) return;

    function setStat(id, val) {
        var el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    // ── Drop zone ──

    TCTP.initDropZone('tc-wp-drop', 'tc-wp-drop-input', function (f) {
        if (!f.type.match(/image\/webp/) && !/\.webp$/i.test(f.name)) {
            TCTP.toast('Please select a WebP file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        compressedBlob = null;
        TCTP.showFileRow('tc-wp-file', f);
        var dlBtn = document.getElementById('tc-wp-download');
        if (dlBtn) dlBtn.style.display = 'none';
        setStat('tc-wp-stat-orig', '-');
        setStat('tc-wp-stat-comp', '-');
        setStat('tc-wp-stat-saved', '-');

        var reader = new FileReader();
        reader.onload = function (ev) {
            TCTP.showOriginalPreview(ev.target.result);
            TCTP.switchToOriginalTab();
        };
        reader.readAsDataURL(f);
    }, 'image/webp,.webp');

    var removeBtn = document.querySelector('#tc-wp-file .tc-x');
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            file = null;
            compressedBlob = null;
            TCTP.hideFileRow('tc-wp-file');
            setStat('tc-wp-stat-orig', '-');
            setStat('tc-wp-stat-comp', '-');
            setStat('tc-wp-stat-saved', '-');
        });
    }

    // ── Quality slider ──

    var qualitySlider = document.getElementById('tc-wp-quality');
    var qualityVal = document.getElementById('tc-wp-quality-val');
    if (qualitySlider && qualityVal) {
        qualitySlider.addEventListener('input', function () {
            quality = parseInt(qualitySlider.value) || 90;
            qualityVal.textContent = quality + '%';
        });
    }

    // ── Downscale toggle ──

    var resizeToggle = document.getElementById('tc-wp-resize');
    var resizeVal = document.getElementById('tc-wp-resize-val');
    var sliderSection = document.getElementById('tc-wp-slider-section');
    var maxDimSlider = document.getElementById('tc-wp-maxdim');
    var maxDimVal = document.getElementById('tc-wp-dim-val');

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
            var dimUserVal = document.getElementById('tc-wp-maxdim-val');
            if (dimUserVal) dimUserVal.textContent = maxDim;
        });
    }

    // ── Compress ──

    var compressBtn = document.getElementById('tc-wp-compress');
    if (compressBtn) {
        compressBtn.addEventListener('click', function () {
            if (!file) { TCTP.toast('Please select a WebP file first.', '\u26A0\uFE0F'); return; }

            TCTP.showProgress('tc-wp-progress');
            TCTP.setProgress('tc-wp-progress', 10, 'Reading image...');

            var reader = new FileReader();
            reader.onload = function (ev) {
                TCTP.setProgress('tc-wp-progress', 30, 'Decoding...');
                var img = new Image();
                img.onload = function () {
                    var w = img.naturalWidth;
                    var h = img.naturalHeight;

                    if (resizeToggle && resizeToggle.checked) {
                        if (w > maxDim || h > maxDim) {
                            var ratio = Math.min(maxDim / w, maxDim / h);
                            w = Math.round(w * ratio);
                            h = Math.round(h * ratio);
                        }
                    }

                    TCTP.setProgress('tc-wp-progress', 50, 'Compressing...');

                    var canvas = document.createElement('canvas');
                    canvas.width = w;
                    canvas.height = h;
                    var ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, w, h);

                    canvas.toBlob(function (blob) {
                        if (!blob) {
                            TCTP.toast('WebP compression not supported in your browser.', '\u274C');
                            TCTP.hideProgress('tc-wp-progress');
                            return;
                        }

                        compressedBlob = blob;
                        var origSize = file.size;
                        var compSize = blob.size;
                        var saved = origSize > compSize ? ((1 - compSize / origSize) * 100).toFixed(1) : '0';

                        setStat('tc-wp-stat-orig', TCTP.formatSize(origSize));
                        setStat('tc-wp-stat-comp', TCTP.formatSize(compSize));
                        setStat('tc-wp-stat-saved', saved + '%');

                        TCTP.updateResultPanel(TCTP.formatSize(origSize), TCTP.formatSize(compSize), saved + '%', 'Done');
                        TCTP.showResultPreview(URL.createObjectURL(blob));
                        TCTP.switchToResultTab();
                        TCTP.setProgress('tc-wp-progress', 100, 'Done!');
                        TCTP.toast('Compressed! Saved ' + saved + '%');

                        var dlBtn = document.getElementById('tc-wp-download');
                        if (dlBtn) dlBtn.style.display = '';
                    }, 'image/webp', quality / 100);
                };
                img.onerror = function () {
                    TCTP.hideProgress('tc-wp-progress');
                    TCTP.toast('Failed to decode WebP image.', '\u274C');
                };
                img.src = ev.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    // ── Download ──

    var downloadBtn = document.getElementById('tc-wp-download');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function () {
            if (!compressedBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
            var name = (file ? file.name.replace(/\.webp$/i, '') : 'image') + '-compressed.webp';
            TCTP.downloadBlob(compressedBlob, name);
        });
    }

})();
