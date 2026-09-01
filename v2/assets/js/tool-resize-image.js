/**
 * Image Resizer — Tool JS
 * 100% client-side image resize using canvas API.
 * Supports JPG, PNG, WebP, GIF. Batch resize with ZIP download.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var file = null;
    var originalDims = { w: 0, h: 0 };
    var resizedBlob = null;
    var resizedDims = { w: 0, h: 0 };
    var aspectRatio = 1;
    var lockRatio = true;
    var noEnlarge = true;
    var resizeMode = 'pixels';
    var outputFormat = 'original';

    var dropEl = document.getElementById('tc-rsz-drop');
    if (!dropEl) return;

    // ── Drop zone ──────────────────────────────────────────────

    TCTP.initDropZone('tc-rsz-drop', 'tc-rsz-drop-input', function (f) {
        if (!f.type.match(/^image\/(jpeg|png|webp|gif)$/)) {
            TCTP.toast('Please select a JPG, PNG, WebP, or GIF image.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        resizedBlob = null;
        TCTP.showFileRow('tc-rsz-file', f);
        var dlBtn = document.getElementById('tc-rsz-download');
        if (dlBtn) dlBtn.style.display = 'none';

        var img = new Image();
        img.onload = function () {
            originalDims.w = img.naturalWidth;
            originalDims.h = img.naturalHeight;
            aspectRatio = originalDims.w / originalDims.h;

            var wInput = document.getElementById('tc-rsz-width');
            var hInput = document.getElementById('tc-rsz-height');
            if (wInput) wInput.value = originalDims.w;
            if (hInput) hInput.value = originalDims.h;

            TCTP.updateResultPanel(
                originalDims.w + '\u00D7' + originalDims.h,
                '\u2014',
                '\u2014',
                'Ready'
            );
        };
        img.src = URL.createObjectURL(f);
    }, 'image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif');

    var removeBtn = document.querySelector('#tc-rsz-file .tc-x');
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            file = null;
            resizedBlob = null;
            TCTP.hideFileRow('tc-rsz-file');
            if (document.getElementById('tc-preview-orig')) document.getElementById('tc-preview-orig').innerHTML = '';
            if (document.getElementById('tc-preview-result')) document.getElementById('tc-preview-result').innerHTML = '';
            var dlBtn = document.getElementById('tc-rsz-download');
            if (dlBtn) dlBtn.style.display = 'none';
        });
    }

    // ── Mode toggle ────────────────────────────────────────────

    var modeCards = document.querySelectorAll('.tc-rsz-mode-card');
    modeCards.forEach(function (card) {
        card.addEventListener('click', function () {
            modeCards.forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            resizeMode = card.getAttribute('data-val');

            var pixelsOpts = document.getElementById('tc-rsz-pixels-opts');
            var percentOpts = document.getElementById('tc-rsz-percent-opts');
            if (resizeMode === 'pixels') {
                if (pixelsOpts) pixelsOpts.classList.remove('tc-hide');
                if (percentOpts) percentOpts.classList.add('tc-hide');
            } else {
                if (pixelsOpts) pixelsOpts.classList.add('tc-hide');
                if (percentOpts) percentOpts.classList.remove('tc-hide');
            }
        });
    });

    // ── Format toggle ──────────────────────────────────────────

    var fmtBtns = document.querySelectorAll('.tc-rsz-fmt');
    fmtBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            fmtBtns.forEach(function (b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            outputFormat = btn.getAttribute('data-val');
            var qWrap = document.getElementById('tc-rsz-quality-wrap');
            if (qWrap) {
                if (outputFormat === 'image/png') {
                    qWrap.classList.add('tc-hide');
                } else {
                    qWrap.classList.remove('tc-hide');
                }
            }
        });
    });

    // ── Aspect ratio lock toggle ───────────────────────────────

    var lockBtn = document.getElementById('tc-rsz-lock');
    var lockCheckbox = document.getElementById('tc-rsz-lock-ratio');

    function updateLockUI() {
        if (lockBtn) {
            if (lockRatio) {
                lockBtn.classList.add('tc-rsz-lock--on');
                lockBtn.title = 'Aspect ratio locked';
            } else {
                lockBtn.classList.remove('tc-rsz-lock--on');
                lockBtn.title = 'Aspect ratio unlocked';
            }
        }
    }

    if (lockBtn) {
        lockBtn.addEventListener('click', function () {
            lockRatio = !lockRatio;
            if (lockCheckbox) lockCheckbox.checked = lockRatio;
            updateLockUI();
        });
    }
    if (lockCheckbox) {
        lockCheckbox.addEventListener('change', function () {
            lockRatio = lockCheckbox.checked;
            updateLockUI();
        });
    }

    // ── Width/Height linked inputs ─────────────────────────────

    var wInput = document.getElementById('tc-rsz-width');
    var hInput = document.getElementById('tc-rsz-height');

    if (wInput) {
        wInput.addEventListener('input', function () {
            if (!lockRatio || !hInput) return;
            var w = parseInt(wInput.value) || 0;
            hInput.value = Math.round(w / aspectRatio);
        });
    }
    if (hInput) {
        hInput.addEventListener('input', function () {
            if (!lockRatio || !wInput) return;
            var h = parseInt(hInput.value) || 0;
            wInput.value = Math.round(h * aspectRatio);
        });
    }

    // ── No-enlarge checkbox ────────────────────────────────────

    var noEnlargeChk = document.getElementById('tc-rsz-no-enlarge');
    if (noEnlargeChk) {
        noEnlargeChk.addEventListener('change', function () {
            noEnlarge = noEnlargeChk.checked;
        });
    }

    // ── Quality slider ─────────────────────────────────────────

    var qualityRange = document.getElementById('tc-rsz-quality');
    var qualityVal = document.getElementById('tc-rsz-quality-val');
    if (qualityRange && qualityVal) {
        qualityRange.addEventListener('input', function () {
            qualityVal.textContent = qualityRange.value + '%';
        });
    }

    // ── Preset buttons ─────────────────────────────────────────

    var presetBtns = document.querySelectorAll('#tc-rsz-presets .tc-rsz-preset');
    presetBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            presetBtns.forEach(function (b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            var val = parseInt(btn.getAttribute('data-val'));
            var pctInput = document.getElementById('tc-rsz-percent');
            if (pctInput) pctInput.value = val;
        });
    });

    // Custom percent input syncs back to presets
    var pctInput = document.getElementById('tc-rsz-percent');
    if (pctInput) {
        pctInput.addEventListener('input', function () {
            var val = parseInt(pctInput.value) || 0;
            presetBtns.forEach(function (b) {
                if (parseInt(b.getAttribute('data-val')) === val) {
                    b.classList.add('sel');
                } else {
                    b.classList.remove('sel');
                }
            });
        });
    }

    // ── Resize ─────────────────────────────────────────────────

    var resizeBtn = document.getElementById('tc-rsz-resize');
    if (resizeBtn) {
        resizeBtn.addEventListener('click', function () {
            if (!file) {
                TCTP.toast('Please select an image first.', '\u26A0\uFE0F');
                return;
            }

            var targetW, targetH;

            if (resizeMode === 'pixels') {
                targetW = parseInt(document.getElementById('tc-rsz-width').value) || 0;
                targetH = parseInt(document.getElementById('tc-rsz-height').value) || 0;
            } else {
                var pct = parseInt(document.getElementById('tc-rsz-percent').value) || 100;
                targetW = Math.round(originalDims.w * pct / 100);
                targetH = Math.round(originalDims.h * pct / 100);
            }

            if (targetW < 1 || targetH < 1) {
                TCTP.toast('Please enter valid dimensions.', '\u26A0\uFE0F');
                return;
            }

            if (noEnlarge) {
                if (targetW > originalDims.w && targetH > originalDims.h) {
                    TCTP.toast('Image is already smaller \u2014 not enlarging.', '\u2139\uFE0F');
                    return;
                }
            }

            TCTP.showProgress('tc-rsz-progress');
            TCTP.setProgress('tc-rsz-progress', 20, 'Loading image...');

            var reader = new FileReader();
            reader.onload = function (e) {
                TCTP.setProgress('tc-rsz-progress', 50, 'Resizing...');
                var img = new Image();
                img.onload = function () {
                    var canvas = document.createElement('canvas');
                    canvas.width = targetW;
                    canvas.height = targetH;
                    var ctx = canvas.getContext('2d');

                    ctx.imageSmoothingEnabled = true;
                    ctx.imageSmoothingQuality = 'high';
                    ctx.drawImage(img, 0, 0, targetW, targetH);

                    var mime = outputFormat === 'original' ? file.type : outputFormat;
                    var quality = parseInt(document.getElementById('tc-rsz-quality').value) || 92;
                    if (mime === 'image/png') quality = undefined;

                    TCTP.setProgress('tc-rsz-progress', 80, 'Encoding...');

                    canvas.toBlob(function (blob) {
                        resizedBlob = blob;
                        resizedDims.w = targetW;
                        resizedDims.h = targetH;

                        var saved = file.size > 0 ? ((1 - blob.size / file.size) * 100).toFixed(1) : 0;

                        TCTP.setProgress('tc-rsz-progress', 100, 'Done!');
                        TCTP.hideProgress('tc-rsz-progress');

                        TCTP.updateResultPanel(
                            TCTP.formatSize(file.size),
                            TCTP.formatSize(blob.size),
                            (saved > 0 ? saved + '% saved' : '0%'),
                            'Done'
                        );

                        var dimsEl = document.getElementById('tc-rsz-stat-dims');
                        if (dimsEl) dimsEl.textContent = targetW + '\u00D7' + targetH + ' px';

                        TCTP.showResultPreview(canvas.toDataURL(mime, quality / 100));
                        TCTP.switchToResultTab();

                        var dlBtn = document.getElementById('tc-rsz-download');
                        if (dlBtn) dlBtn.style.display = '';
                        TCTP.toast('Image resized to ' + targetW + '\u00D7' + targetH + '!');
                    }, mime, quality);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    // ── Download ───────────────────────────────────────────────

    var downloadBtn = document.getElementById('tc-rsz-download');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function () {
            if (!resizedBlob || !file) {
                TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F');
                return;
            }

            var ext = '.jpg';
            if (outputFormat === 'image/png') ext = '.png';
            else if (outputFormat === 'image/webp') ext = '.webp';
            else if (file.name) {
                var match = file.name.match(/\.(jpe?g|png|webp|gif)$/i);
                if (match) ext = '.' + match[1].toLowerCase();
            }

            var name = file.name ? file.name.replace(/\.[^.]+$/, '') : 'image';
            TCTP.downloadBlob(resizedBlob, name + '-resized' + ext);
        });
    }

})();
