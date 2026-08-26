/**
 * Crop Image — Tool JS
 * 100% client-side image crop using canvas API.
 * Interactive drag-to-crop with aspect ratio presets.
 *
 * Widget IDs (widget-crop-image.php):
 *  - tc-crop-drop, tc-crop-file
 *  - tc-crop-canvas, tc-crop-overlay, tc-crop-box
 *  - tc-crop-custom-opts, tc-crop-custom-w, tc-crop-custom-h, tc-crop-lock
 *  - tc-crop-quality, tc-crop-quality-val, tc-crop-quality-wrap
 *  - tc-crop-apply, tc-crop-download
 *  - tc-crop-stat-orig, tc-crop-stat-crop, tc-crop-stat-dims
 *  - tc-crop-preview-orig, tc-crop-result
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var dropEl = document.getElementById('tc-crop-drop');
    if (!dropEl) return;

    var file = null;
    var imgEl = null;
    var naturalW = 0, naturalH = 0;
    var displayW = 0, displayH = 0;
    var scaleX = 1, scaleY = 1;

    var cropBox = { x: 0, y: 0, w: 0, h: 0 };
    var dragging = false;
    var dragMode = 'move';
    var dragStartX = 0, dragStartY = 0;
    var dragStartBox = null;

    var currentRatio = 'free';
    var outputFormat = 'original';
    var lockRatio = true;
    var croppedBlob = null;

    var canvas = document.getElementById('tc-crop-canvas');
    var overlay = document.getElementById('tc-crop-overlay');
    var boxEl = document.getElementById('tc-crop-box');
    var wrapEl = document.getElementById('tc-crop-canvas-wrap');
    var previewWrap = document.getElementById('tc-crop-preview-wrap');
    var customOpts = document.getElementById('tc-crop-custom-opts');
    var modeCards = document.querySelectorAll('.tc-crop-modes .tc-rsz-mode-card');
    var fmtCards = document.querySelectorAll('.tc-rsz-format-row .tc-rsz-fmt');
    var applyBtn = document.getElementById('tc-crop-apply');
    var dlBtn = document.getElementById('tc-crop-download');

    // ── Drop zone ──────────────────────────────────────────────

    TCTP.initDropZone('tc-crop-drop', 'tc-crop-drop-input', function (f) {
        if (!f.type.match(/^image\/(jpeg|png|webp|gif)$/)) {
            TCTP.toast('Please select a JPG, PNG, WebP, or GIF image.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        croppedBlob = null;
        if (dlBtn) dlBtn.style.display = 'none';
        TCTP.showFileRow('tc-crop-file', f);
        loadImage(f);
    }, 'image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif');

    var removeBtn = document.querySelector('#tc-crop-file .tc-x');
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            file = null;
            imgEl = null;
            croppedBlob = null;
            if (dlBtn) dlBtn.style.display = 'none';
            TCTP.hideFileRow('tc-crop-file');
            if (previewWrap) previewWrap.style.display = 'none';
        });
    }

    // ── Load image onto canvas ─────────────────────────────────

    function loadImage(f) {
        var reader = new FileReader();
        reader.onload = function (e) {
            imgEl = new Image();
            imgEl.onload = function () {
                naturalW = imgEl.naturalWidth;
                naturalH = imgEl.naturalHeight;

                if (previewWrap) previewWrap.style.display = '';
                if (!canvas || !overlay) return;

                var wrapW = wrapEl ? wrapEl.clientWidth : 600;
                var maxH = 400;
                displayW = Math.min(wrapW, naturalW);
                displayH = Math.round(displayW * (naturalH / naturalW));
                if (displayH > maxH) {
                    displayH = maxH;
                    displayW = Math.round(displayH * (naturalW / naturalH));
                }

                canvas.width = displayW;
                canvas.height = displayH;
                overlay.style.width = displayW + 'px';
                overlay.style.height = displayH + 'px';

                scaleX = naturalW / displayW;
                scaleY = naturalH / displayH;

                var ctx = canvas.getContext('2d');
                ctx.drawImage(imgEl, 0, 0, displayW, displayH);

                cropBox = { x: 0, y: 0, w: displayW, h: displayH };
                updateBox();

                var origStat = document.getElementById('tc-crop-stat-orig');
                if (origStat) origStat.textContent = naturalW + '\u00D7' + naturalH;
                var dimsStat = document.getElementById('tc-crop-stat-dims');
                if (dimsStat) dimsStat.textContent = naturalW + '\u00D7' + naturalH;

                TCTP.updateResultPanel(
                    naturalW + '\u00D7' + naturalH,
                    '\u2014',
                    '\u2014',
                    'Ready'
                );

                // Update original preview
                var prevOrig = document.getElementById('tc-crop-preview-orig');
                if (prevOrig) {
                    prevOrig.innerHTML = '';
                    var prevImg = new Image();
                    prevImg.src = e.target.result;
                    prevImg.style.maxWidth = '100%';
                    prevImg.style.maxHeight = '300px';
                    prevImg.style.borderRadius = '8px';
                    prevImg.style.objectFit = 'contain';
                    prevImg.alt = 'Original image';
                    prevOrig.appendChild(prevImg);
                }

                TCTP.toast('Image loaded! Drag the crop handles or choose a preset.', '\u2705');
            };
            imgEl.src = e.target.result;
        };
        reader.readAsDataURL(f);
    }

    // ── Aspect ratio mode cards ────────────────────────────────

    modeCards.forEach(function (card) {
        card.addEventListener('click', function () {
            modeCards.forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            currentRatio = card.getAttribute('data-val') || 'free';

            if (customOpts) customOpts.style.display = currentRatio === 'custom' ? '' : 'none';

            if (currentRatio !== 'free' && currentRatio !== 'custom' && displayW && displayH) {
                var parts = currentRatio.split(':');
                var rW = parseInt(parts[0], 10);
                var rH = parseInt(parts[1], 10);
                if (rW && rH) {
                    var ratio = rW / rH;
                    var newW = displayW;
                    var newH = Math.round(newW / ratio);
                    if (newH > displayH) {
                        newH = displayH;
                        newW = Math.round(newH * ratio);
                    }
                    cropBox = {
                        x: Math.round((displayW - newW) / 2),
                        y: Math.round((displayH - newH) / 2),
                        w: newW,
                        h: newH
                    };
                    updateBox();
                }
            }
        });
    });

    // ── Output format ──────────────────────────────────────────

    fmtCards.forEach(function (btn) {
        btn.addEventListener('click', function () {
            fmtCards.forEach(function (b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            outputFormat = btn.getAttribute('data-val') || 'original';

            var qw = document.getElementById('tc-crop-quality-wrap');
            if (qw) {
                qw.style.display = (outputFormat === 'image/jpeg' || outputFormat === 'image/webp') ? '' : 'none';
            }
        });
    });

    // ── Quality slider ─────────────────────────────────────────

    var qualitySlider = document.getElementById('tc-crop-quality');
    var qualityVal = document.getElementById('tc-crop-quality-val');
    if (qualitySlider) {
        qualitySlider.addEventListener('input', function () {
            if (qualityVal) qualityVal.textContent = qualitySlider.value + '%';
        });
    }

    // ── Custom dimensions lock ─────────────────────────────────

    var lockBtn = document.getElementById('tc-crop-lock');
    var customW = document.getElementById('tc-crop-custom-w');
    var customH = document.getElementById('tc-crop-custom-h');

    if (lockBtn) {
        lockBtn.addEventListener('click', function () {
            lockRatio = !lockRatio;
            lockBtn.className = lockRatio ? 'tc-rsz-lock tc-rsz-lock--on' : 'tc-rsz-lock tc-rsz-lock--off';
        });
    }

    if (customW) {
        customW.addEventListener('input', function () {
            if (lockRatio && customH) {
                var w = parseInt(customW.value, 10);
                if (w > 0 && naturalW > 0) {
                    customH.value = Math.round(w * (naturalH / naturalW));
                }
            }
        });
    }

    // ── Crop box dragging ──────────────────────────────────────

    function updateBox() {
        if (!boxEl || !overlay) return;
        boxEl.style.left = cropBox.x + 'px';
        boxEl.style.top = cropBox.y + 'px';
        boxEl.style.width = cropBox.w + 'px';
        boxEl.style.height = cropBox.h + 'px';
    }

    function getOverlayXY(e) {
        var rect = overlay.getBoundingClientRect();
        var clientX = e.touches ? e.touches[0].clientX : e.clientX;
        var clientY = e.touches ? e.touches[0].clientY : e.clientY;
        return {
            x: Math.max(0, Math.min(displayW, clientX - rect.left)),
            y: Math.max(0, Math.min(displayH, clientY - rect.top))
        };
    }

    function onPointerDown(e) {
        if (!overlay) return;
        e.preventDefault();

        var handle = e.target.getAttribute('data-handle');
        if (handle) {
            dragMode = handle;
        } else if (e.target === boxEl || boxEl.contains(e.target)) {
            dragMode = 'move';
        } else {
            dragMode = 'nw';
            var pos = getOverlayXY(e);
            cropBox.x = pos.x;
            cropBox.y = pos.y;
            cropBox.w = 0;
            cropBox.h = 0;
        }

        dragStartX = getOverlayXY(e).x;
        dragStartY = getOverlayXY(e).y;
        dragStartBox = { x: cropBox.x, y: cropBox.y, w: cropBox.w, h: cropBox.h };
        dragging = true;

        document.addEventListener('mousemove', onPointerMove, { passive: false });
        document.addEventListener('mouseup', onPointerUp);
        document.addEventListener('touchmove', onPointerMove, { passive: false });
        document.addEventListener('touchend', onPointerUp);
    }

    function onPointerMove(e) {
        if (!dragging) return;
        e.preventDefault();

        var pos = getOverlayXY(e);
        var dx = pos.x - dragStartX;
        var dy = pos.y - dragStartY;
        var src = dragStartBox;
        var ratio = (currentRatio !== 'free' && currentRatio !== 'custom' && currentRatio.indexOf(':') !== -1)
            ? (function () { var p = currentRatio.split(':'); return parseInt(p[0], 10) / parseInt(p[1], 10); })()
            : 0;

        if (dragMode === 'move') {
            cropBox.x = Math.max(0, Math.min(displayW - src.w, src.x + dx));
            cropBox.y = Math.max(0, Math.min(displayH - src.h, src.y + dy));
            cropBox.w = src.w;
            cropBox.h = src.h;
        } else {
            var newX = src.x, newY = src.y, newW = src.w, newH = src.h;

            if (dragMode === 'se' || dragMode === 'e' || dragMode === 's') {
                newW = Math.max(20, src.w + dx);
                newH = ratio ? Math.round(newW / ratio) : Math.max(20, src.h + dy);
                if (ratio) {
                    newW = Math.round(newH * ratio);
                }
                if (newX + newW > displayW) { newW = displayW - newX; if (ratio) newH = Math.round(newW / ratio); }
                if (newY + newH > displayH) { newH = displayH - newY; if (ratio) newW = Math.round(newH * ratio); }
            } else if (dragMode === 'nw' || dragMode === 'n' || dragMode === 'w') {
                newW = Math.max(20, src.w - dx);
                newH = ratio ? Math.round(newW / ratio) : Math.max(20, src.h - dy);
                if (ratio) { newW = Math.round(newH * ratio); }
                newX = src.x + src.w - newW;
                newY = src.y + src.h - newH;
                if (newX < 0) { newW += newX; newX = 0; if (ratio) newH = Math.round(newW / ratio); newY = src.y + src.h - newH; }
                if (newY < 0) { newH += newY; newY = 0; if (ratio) { newW = Math.round(newH * ratio); newX = src.x + src.w - newW; } }
            } else if (dragMode === 'ne') {
                newW = Math.max(20, src.w + dx);
                newH = ratio ? Math.round(newW / ratio) : Math.max(20, src.h - dy);
                if (ratio) { newW = Math.round(newH * ratio); }
                newY = src.y + src.h - newH;
                if (newX + newW > displayW) { newW = displayW - newX; if (ratio) { newH = Math.round(newW / ratio); newY = src.y + src.h - newH; } }
                if (newY < 0) { newH += newY; newY = 0; if (ratio) { newW = Math.round(newH * ratio); } }
            } else if (dragMode === 'sw') {
                newW = Math.max(20, src.w - dx);
                newH = ratio ? Math.round(newW / ratio) : Math.max(20, src.h + dy);
                if (ratio) { newW = Math.round(newH * ratio); }
                newX = src.x + src.w - newW;
                if (newX < 0) { newW += newX; newX = 0; if (ratio) newH = Math.round(newW / ratio); }
                if (newY + newH > displayH) { newH = displayH - newY; if (ratio) { newW = Math.round(newH * ratio); newX = src.x + src.w - newW; } }
            } else if (dragMode === 'n') {
                newH = Math.max(20, src.h - dy);
                newY = src.y + src.h - newH;
                if (newY < 0) { newH += newY; newY = 0; }
                if (ratio) { newW = Math.round(newH * ratio); newX = src.x + (src.w - newW) / 2; }
            } else if (dragMode === 's') {
                newH = Math.max(20, src.h + dy);
                if (newY + newH > displayH) newH = displayH - newY;
                if (ratio) { newW = Math.round(newH * ratio); newX = src.x + (src.w - newW) / 2; }
            } else if (dragMode === 'e') {
                newW = Math.max(20, src.w + dx);
                if (newX + newW > displayW) newW = displayW - newX;
                if (ratio) { newH = Math.round(newW / ratio); newY = src.y + (src.h - newH) / 2; }
            } else if (dragMode === 'w') {
                newW = Math.max(20, src.w - dx);
                newX = src.x + src.w - newW;
                if (newX < 0) { newW += newX; newX = 0; }
                if (ratio) { newH = Math.round(newW / ratio); newY = src.y + (src.h - newH) / 2; }
            }

            cropBox = { x: Math.round(newX), y: Math.round(newY), w: Math.round(newW), h: Math.round(newH) };
        }

        updateBox();
    }

    function onPointerUp() {
        dragging = false;
        document.removeEventListener('mousemove', onPointerMove);
        document.removeEventListener('mouseup', onPointerUp);
        document.removeEventListener('touchmove', onPointerMove);
        document.removeEventListener('touchend', onPointerUp);
    }

    if (overlay) {
        overlay.addEventListener('mousedown', onPointerDown);
        overlay.addEventListener('touchstart', onPointerDown, { passive: false });
    }

    // ── Crop button ────────────────────────────────────────────

    if (applyBtn) {
        applyBtn.addEventListener('click', function () {
            if (!imgEl) {
                TCTP.toast('Please upload an image first.', '\u26A0\uFE0F');
                return;
            }

            if (cropBox.w < 10 || cropBox.h < 10) {
                TCTP.toast('Please select a crop area.', '\u26A0\uFE0F');
                return;
            }

            TCTP.showProgress('tc-crop-progress', 50, 'Cropping...');

            var rx = Math.round(cropBox.x * scaleX);
            var ry = Math.round(cropBox.y * scaleY);
            var rw = Math.round(cropBox.w * scaleX);
            var rh = Math.round(cropBox.h * scaleY);

            if (rx + rw > naturalW) rw = naturalW - rx;
            if (ry + rh > naturalH) rh = naturalH - ry;

            var outCanvas = document.createElement('canvas');
            outCanvas.width = rw;
            outCanvas.height = rh;
            var ctx = outCanvas.getContext('2d');
            ctx.drawImage(imgEl, rx, ry, rw, rh, 0, 0, rw, rh);

            var mime = outputFormat === 'original' ? (file ? file.type : 'image/png') : outputFormat;
            var quality = 0.92;
            if (mime === 'image/jpeg' || mime === 'image/webp') {
                var qs = document.getElementById('tc-crop-quality');
                if (qs) quality = parseInt(qs.value, 10) / 100;
            }

            outCanvas.toBlob(function (blob) {
                croppedBlob = blob;
                TCTP.setProgress('tc-crop-progress', 100, 'Done!');
                setTimeout(function () { TCTP.hideProgress('tc-crop-progress'); }, 600);

                var cropStat = document.getElementById('tc-crop-stat-crop');
                if (cropStat) cropStat.textContent = rw + '\u00D7' + rh;
                var dimsStat = document.getElementById('tc-crop-stat-dims');
                if (dimsStat) dimsStat.textContent = rw + '\u00D7' + rh;

                var saved = file ? (((1 - blob.size / file.size) * 100).toFixed(1) + '%') : '\u2014';
                TCTP.updateResultPanel(
                    naturalW + '\u00D7' + naturalH,
                    rw + '\u00D7' + rh,
                    saved,
                    'Done'
                );
                TCTP.switchToResultTab();

                // Show result preview
                var resultEl = document.getElementById('tc-crop-result');
                if (resultEl) {
                    resultEl.innerHTML = '';
                    var url = URL.createObjectURL(blob);
                    var img = new Image();
                    img.src = url;
                    img.style.maxWidth = '100%';
                    img.style.borderRadius = '8px';
                    img.style.objectFit = 'contain';
                    img.alt = 'Cropped image';
                    resultEl.appendChild(img);
                }

                if (dlBtn) {
                    dlBtn.style.display = '';
                    dlBtn.onclick = function () {
                        var a = document.createElement('a');
                        a.href = URL.createObjectURL(blob);
                        a.download = 'cropped-image.' + (mime === 'image/jpeg' ? 'jpg' : mime === 'image/png' ? 'png' : mime === 'image/webp' ? 'webp' : 'png');
                        a.click();
                        URL.revokeObjectURL(a.href);
                    };
                }

                TCTP.toast('Image cropped successfully!', '\u2705');
            }, mime, quality);
        });
    }
})();
