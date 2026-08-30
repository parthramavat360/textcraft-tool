/**
 * JPG Compressor — Tool JS
 *
 * Premium redesign. Quality slider, optional downscale, output file name,
 * Clear all (also clears previews). Client-side canvas.toBlob compression.
 *
 * @package TextCraft_Tools_Pro
 */
(function () {
    'use strict';

    var file = null;
    var compressedBlob = null;
    var quality = 92;
    var maxDim = 1200;
    var resizeToggle = document.getElementById('tc-jpg-resize');
    var sliderSection = document.getElementById('tc-jpg-slider-section');
    var maxDimSlider = document.getElementById('tc-jpg-maxdim');
    var maxDimVal = document.getElementById('tc-jpg-dim-val');

    function setStat(id, val) {
        var el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    function resetStats() {
        setStat('tc-jpg-stat-orig', '-');
        setStat('tc-jpg-stat-comp', '-');
        setStat('tc-jpg-stat-saved', '-');
    }

    /* ── Drop zone ── */

    TCTP.initDropZone('tc-jpg-drop', 'tc-jpg-drop-input', function (f) {
        if (!f.type.match(/image\/jpe?g/)) {
            TCTP.toast('Please select a JPG/JPEG file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        compressedBlob = null;
        TCTP.showFileRow('tc-jpg-file', f);
        var dlBtn = document.getElementById('tc-jpg-download');
        if (dlBtn) dlBtn.style.display = 'none';
        resetStats();

        var reader = new FileReader();
        reader.onload = function (ev) {
            TCTP.showOriginalPreview(ev.target.result);
            TCTP.switchToOriginalTab();
        };
        reader.readAsDataURL(f);
    }, 'image/jpeg,.jpg,.jpeg');

    var removeBtn = document.querySelector('#tc-jpg-file .tc-x');
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            file = null;
            compressedBlob = null;
            TCTP.hideFileRow('tc-jpg-file');
            resetStats();
            var dlBtn = document.getElementById('tc-jpg-download');
            if (dlBtn) dlBtn.style.display = 'none';
        });
    }

    /* ── Quality slider ── */

    var qualitySlider = document.getElementById('tc-jpg-quality');
    var qualityVal = document.getElementById('tc-jpg-quality-val');
    if (qualitySlider && qualityVal) {
        qualitySlider.addEventListener('input', function () {
            quality = parseInt(qualitySlider.value) || 92;
            qualityVal.textContent = quality + '%';
        });
    }

    /* ── Downscale toggle ── */

    if (resizeToggle && sliderSection) {
        resizeToggle.addEventListener('change', function () {
            sliderSection.style.display = resizeToggle.checked ? '' : 'none';
        });
    }

    if (maxDimSlider && maxDimVal) {
        maxDimSlider.addEventListener('input', function () {
            maxDim = parseInt(maxDimSlider.value) || 1200;
            maxDimVal.textContent = maxDim + 'px';
        });
    }

    /* ── Compress ── */

    document.getElementById('tc-jpg-compress').addEventListener('click', function () {
        if (!file) { TCTP.toast('Please select a JPG file first.', '\u26A0\uFE0F'); return; }

        TCTP.showProgress('tc-jpg-progress');
        TCTP.setProgress('tc-jpg-progress', 30, 'Reading image...');

        var reader = new FileReader();
        reader.onload = function (e) {
            TCTP.setProgress('tc-jpg-progress', 50, 'Compressing...');
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

                var canvas = document.createElement('canvas');
                canvas.width = w;
                canvas.height = h;
                var ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, w, h);

                canvas.toBlob(function (blob) {
                    compressedBlob = blob;
                    var origSize = file.size;
                    var compSize = blob.size;
                    var saved = ((1 - compSize / origSize) * 100).toFixed(1);

                    setStat('tc-jpg-stat-orig', TCTP.formatSize(origSize));
                    setStat('tc-jpg-stat-comp', TCTP.formatSize(compSize));
                    setStat('tc-jpg-stat-saved', saved + '%');

                    TCTP.updateResultPanel(TCTP.formatSize(origSize), TCTP.formatSize(compSize), saved + '%', 'Done');
                    TCTP.showResultPreview(URL.createObjectURL(compressedBlob));
                    TCTP.switchToResultTab();
                    TCTP.setProgress('tc-jpg-progress', 100, 'Done!');
                    TCTP.toast('Compressed! Saved ' + saved + '%');

                    var dlBtn = document.getElementById('tc-jpg-download');
                    if (dlBtn) dlBtn.style.display = '';
                }, 'image/jpeg', quality / 100);
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });

    /* ── Download ── */

    document.getElementById('tc-jpg-download').addEventListener('click', function () {
        if (!compressedBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
        var nameInput = document.getElementById('tc-jpg-name');
        var base = (nameInput && nameInput.value.trim()) ? nameInput.value.trim().replace(/\.jpe?g$/i, '') : (file ? file.name.replace(/\.jpe?g$/i, '') : 'image');
        TCTP.downloadBlob(compressedBlob, base + '.jpg');
    });

    /* ── Clear all ── */

    var clearBtn = document.getElementById('tc-jpg-clear');
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            file = null;
            compressedBlob = null;
            TCTP.hideFileRow('tc-jpg-file');
            resetStats();
            var dlBtn = document.getElementById('tc-jpg-download');
            if (dlBtn) dlBtn.style.display = 'none';
            var origP = document.getElementById('tc-preview-orig');
            if (origP) origP.innerHTML = '<span style="color:var(--muted);font-size:13px">Original preview will appear here</span>';
            var resP = document.getElementById('tc-preview-result');
            if (resP) resP.innerHTML = '<span style="color:var(--muted);font-size:13px">Result preview will appear here</span>';
            TCTP.switchToOriginalTab();
            var nameInput = document.getElementById('tc-jpg-name');
            if (nameInput) nameInput.value = '';
        });
    }

})();
