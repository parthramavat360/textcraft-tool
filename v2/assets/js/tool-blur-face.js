/**
 * Blur Face / Objects — Tool JS
 * 100% client-side blur using canvas API.
 * Interactive rectangle drawing to select areas to blur.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var dropEl = document.getElementById('tc-blur-drop');
    if (!dropEl) return;

    var file = null;
    var imgEl = null;
    var naturalW = 0, naturalH = 0;
    var displayW = 0, displayH = 0;
    var scaleX = 1, scaleY = 1;

    var blurRegions = [];
    var isDrawing = false;
    var startX = 0, startY = 0;
    var currentRect = null;
    var blurMode = 'gaussian';
    var outputFormat = 'original';
    var blurredBlob = null;

    var canvas = document.getElementById('tc-blur-canvas');
    var overlay = document.getElementById('tc-blur-overlay');
    var wrapEl = document.getElementById('tc-blur-canvas-wrap');
    var workspace = document.getElementById('tc-blur-workspace');
    var strengthSection = document.getElementById('tc-blur-strength-section');
    var modeSection = document.getElementById('tc-blur-mode-section');
    var formatSection = document.getElementById('tc-blur-format-section');
    var modeCards = document.querySelectorAll('.tc-blur-modes .tc-rsz-mode-card');
    var fmtCards = document.querySelectorAll('.tc-rsz-format-row .tc-rsz-fmt');
    var applyBtn = document.getElementById('tc-blur-apply');
    var dlBtn = document.getElementById('tc-blur-download');

    // ── Drop zone ──────────────────────────────────────────────

    TCTP.initDropZone('tc-blur-drop', 'tc-blur-drop-input', function (f) {
        if (!f.type.match(/^image\/(jpeg|png|webp|gif)$/)) {
            TCTP.toast('Please select a JPG, PNG, WebP, or GIF image.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        blurredBlob = null;
        blurRegions = [];
        if (dlBtn) dlBtn.style.display = 'none';
        TCTP.showFileRow('tc-blur-file', f);
        loadImage(f);
    }, 'image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif');

    var removeBtn = document.querySelector('#tc-blur-file .tc-x');
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            file = null;
            imgEl = null;
            blurRegions = [];
            blurredBlob = null;
            if (dlBtn) dlBtn.style.display = 'none';
            TCTP.hideFileRow('tc-blur-file');
            if (workspace) workspace.style.display = 'none';
            if (strengthSection) strengthSection.style.display = 'none';
            if (modeSection) modeSection.style.display = 'none';
            if (formatSection) formatSection.style.display = 'none';
        });
    }

    // ── Load image ─────────────────────────────────────────────

    function loadImage(f) {
        var reader = new FileReader();
        reader.onload = function (e) {
            imgEl = new Image();
            imgEl.onload = function () {
                naturalW = imgEl.naturalWidth;
                naturalH = imgEl.naturalHeight;

                if (workspace) workspace.style.display = '';
                if (strengthSection) strengthSection.style.display = '';
                if (modeSection) modeSection.style.display = '';
                if (formatSection) formatSection.style.display = '';
                if (!canvas || !overlay) return;

                var wrapW = wrapEl ? wrapEl.clientWidth : 600;
                var maxH = 500;
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

                var origStat = document.getElementById('tc-blur-stat-orig');
                if (origStat) origStat.textContent = naturalW + '\u00D7' + naturalH;

                TCTP.updateResultPanel(
                    naturalW + '\u00D7' + naturalH,
                    '\u2014',
                    '\u2014',
                    'Ready'
                );

                var prevOrig = document.getElementById('tc-blur-preview-orig');
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

                blurRegions = [];
                updateRegionCount();
                TCTP.toast('Image loaded! Draw rectangles over areas to blur.', '\u2705');
            };
            imgEl.src = e.target.result;
        };
        reader.readAsDataURL(f);
    }

    // ── Region drawing ─────────────────────────────────────────

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
        if (!overlay || !imgEl) return;
        e.preventDefault();
        var pos = getOverlayXY(e);
        startX = pos.x;
        startY = pos.y;
        isDrawing = true;
        currentRect = document.createElement('div');
        currentRect.className = 'tc-blur-rect';
        currentRect.style.left = startX + 'px';
        currentRect.style.top = startY + 'px';
        currentRect.style.width = '0px';
        currentRect.style.height = '0px';
        overlay.appendChild(currentRect);

        document.addEventListener('mousemove', onPointerMove, { passive: false });
        document.addEventListener('mouseup', onPointerUp);
        document.addEventListener('touchmove', onPointerMove, { passive: false });
        document.addEventListener('touchend', onPointerUp);
    }

    function onPointerMove(e) {
        if (!isDrawing || !currentRect) return;
        e.preventDefault();
        var pos = getOverlayXY(e);
        var x = Math.min(startX, pos.x);
        var y = Math.min(startY, pos.y);
        var w = Math.abs(pos.x - startX);
        var h = Math.abs(pos.y - startY);

        currentRect.style.left = x + 'px';
        currentRect.style.top = y + 'px';
        currentRect.style.width = w + 'px';
        currentRect.style.height = h + 'px';
    }

    function onPointerUp(e) {
        if (!isDrawing) return;
        isDrawing = false;

        document.removeEventListener('mousemove', onPointerMove);
        document.removeEventListener('mouseup', onPointerUp);
        document.removeEventListener('touchmove', onPointerMove);
        document.removeEventListener('touchend', onPointerUp);

        if (currentRect) {
            var w = parseFloat(currentRect.style.width);
            var h = parseFloat(currentRect.style.height);
            if (w > 5 && h > 5) {
                blurRegions.push({
                    x: parseFloat(currentRect.style.left),
                    y: parseFloat(currentRect.style.top),
                    w: w,
                    h: h
                });
                currentRect.setAttribute('data-index', blurRegions.length - 1);
            } else {
                currentRect.remove();
            }
            currentRect = null;
        }

        updateRegionCount();
    }

    if (overlay) {
        overlay.addEventListener('mousedown', onPointerDown);
        overlay.addEventListener('touchstart', onPointerDown, { passive: false });
    }

    function updateRegionCount() {
        var count = blurRegions.length;
        var countEl = document.getElementById('tc-blur-count');
        if (countEl) countEl.textContent = count + ' area' + (count !== 1 ? 's' : '') + ' selected';
        var statEl = document.getElementById('tc-blur-stat-areas');
        if (statEl) statEl.textContent = count;
    }

    // ── Clear / Undo ───────────────────────────────────────────

    var clearBtn = document.getElementById('tc-blur-clear');
    var undoBtn = document.getElementById('tc-blur-undo');

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            blurRegions = [];
            var rects = overlay.querySelectorAll('.tc-blur-rect');
            rects.forEach(function (r) { r.remove(); });
            updateRegionCount();
        });
    }

    if (undoBtn) {
        undoBtn.addEventListener('click', function () {
            if (blurRegions.length === 0) return;
            blurRegions.pop();
            var rects = overlay.querySelectorAll('.tc-blur-rect');
            if (rects.length > 0) rects[rects.length - 1].remove();
            updateRegionCount();
        });
    }

    // ── Mode / Format ──────────────────────────────────────────

    modeCards.forEach(function (card) {
        card.addEventListener('click', function () {
            modeCards.forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            blurMode = card.getAttribute('data-val') || 'gaussian';
        });
    });

    fmtCards.forEach(function (btn) {
        btn.addEventListener('click', function () {
            fmtCards.forEach(function (b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            outputFormat = btn.getAttribute('data-val') || 'original';
            var qw = document.getElementById('tc-blur-quality-wrap');
            if (qw) qw.style.display = (outputFormat === 'image/jpeg' || outputFormat === 'image/webp') ? '' : 'none';
        });
    });

    var qualitySlider = document.getElementById('tc-blur-quality');
    var qualityVal = document.getElementById('tc-blur-quality-val');
    if (qualitySlider) {
        qualitySlider.addEventListener('input', function () {
            if (qualityVal) qualityVal.textContent = qualitySlider.value + '%';
        });
    }

    var strengthSlider = document.getElementById('tc-blur-strength');
    var strengthVal = document.getElementById('tc-blur-strength-val');
    if (strengthSlider) {
        strengthSlider.addEventListener('input', function () {
            if (strengthVal) strengthVal.textContent = strengthSlider.value + 'px';
        });
    }

    // ── Apply blur ─────────────────────────────────────────────

    function applyGaussianBlur(ctx, x, y, w, h, strength) {
        // Multi-pass box blur approximates Gaussian
        var passes = 3;
        for (var p = 0; p < passes; p++) {
            var radius = Math.max(1, Math.round(strength / 3));
            // Use canvas filter if available
            try {
                ctx.filter = 'blur(' + radius + 'px)';
                var tempCanvas = document.createElement('canvas');
                tempCanvas.width = naturalW;
                tempCanvas.height = naturalH;
                var tCtx = tempCanvas.getContext('2d');
                tCtx.drawImage(ctx.canvas, 0, 0);
                ctx.clearRect(x, y, w, h);
                ctx.drawImage(tempCanvas, x, y, w, h, x, y, w, h);
                ctx.filter = 'none';
                return;
            } catch (e) {
                // Fallback: stack blur approximation
            }
        }
    }

    function applyPixelate(ctx, x, y, w, h, strength) {
        var pixelSize = Math.max(4, strength);
        var tempCanvas = document.createElement('canvas');
        tempCanvas.width = Math.ceil(w / pixelSize);
        tempCanvas.height = Math.ceil(h / pixelSize);
        var tCtx = tempCanvas.getContext('2d');
        tCtx.drawImage(ctx.canvas, x, y, w, h, 0, 0, tempCanvas.width, tempCanvas.height);
        ctx.imageSmoothingEnabled = false;
        ctx.drawImage(tempCanvas, 0, 0, tempCanvas.width, tempCanvas.height, x, y, w, h);
        ctx.imageSmoothingEnabled = true;
    }

    if (applyBtn) {
        applyBtn.addEventListener('click', function () {
            if (!imgEl) {
                TCTP.toast('Please upload an image first.', '\u26A0\uFE0F');
                return;
            }
            if (blurRegions.length === 0) {
                TCTP.toast('Please draw at least one blur area.', '\u26A0\uFE0F');
                return;
            }

            TCTP.showProgress('tc-blur-progress', 30, 'Blurring...');

            var strength = parseInt(strengthSlider ? strengthSlider.value : 10, 10);
            var outCanvas = document.createElement('canvas');
            outCanvas.width = naturalW;
            outCanvas.height = naturalH;
            var ctx = outCanvas.getContext('2d');
            ctx.drawImage(imgEl, 0, 0, naturalW, naturalH);

            blurRegions.forEach(function (region) {
                var rx = Math.round(region.x * scaleX);
                var ry = Math.round(region.y * scaleY);
                var rw = Math.round(region.w * scaleX);
                var rh = Math.round(region.h * scaleY);

                if (rx + rw > naturalW) rw = naturalW - rx;
                if (ry + rh > naturalH) rh = naturalH - ry;

                if (blurMode === 'pixelate') {
                    applyPixelate(ctx, rx, ry, rw, rh, strength);
                } else {
                    // Gaussian: use canvas filter
                    try {
                        var tempCanvas = document.createElement('canvas');
                        tempCanvas.width = naturalW;
                        tempCanvas.height = naturalH;
                        var tCtx = tempCanvas.getContext('2d');
                        tCtx.drawImage(outCanvas, 0, 0);
                        ctx.clearRect(0, 0, naturalW, naturalH);
                        ctx.drawImage(outCanvas, 0, 0);
                        ctx.save();
                        ctx.beginPath();
                        ctx.rect(rx, ry, rw, rh);
                        ctx.clip();
                        ctx.filter = 'blur(' + strength + 'px)';
                        ctx.drawImage(tempCanvas, 0, 0);
                        ctx.filter = 'none';
                        ctx.restore();
                    } catch (e) {
                        // Fallback: pixelate
                        applyPixelate(ctx, rx, ry, rw, rh, strength);
                    }
                }
            });

            TCTP.setProgress('tc-blur-progress', 70, 'Encoding...');

            var mime = outputFormat === 'original' ? (file ? file.type : 'image/png') : outputFormat;
            var quality = 0.92;
            if (mime === 'image/jpeg' || mime === 'image/webp') {
                var qs = document.getElementById('tc-blur-quality');
                if (qs) quality = parseInt(qs.value, 10) / 100;
            }

            outCanvas.toBlob(function (blob) {
                blurredBlob = blob;
                TCTP.setProgress('tc-blur-progress', 100, 'Done!');
                setTimeout(function () { TCTP.hideProgress('tc-blur-progress'); }, 600);

                var outStat = document.getElementById('tc-blur-stat-out');
                if (outStat) outStat.textContent = TCTP.formatSize(blob.size);

                var saved = file ? (((1 - blob.size / file.size) * 100).toFixed(1) + '%') : '\u2014';
                TCTP.updateResultPanel(
                    naturalW + '\u00D7' + naturalH,
                    TCTP.formatSize(blob.size),
                    saved,
                    'Done'
                );
                TCTP.switchToResultTab();

                var resultEl = document.getElementById('tc-blur-result');
                if (resultEl) {
                    resultEl.innerHTML = '';
                    var url = URL.createObjectURL(blob);
                    var img = new Image();
                    img.src = url;
                    img.style.maxWidth = '100%';
                    img.style.borderRadius = '8px';
                    img.style.objectFit = 'contain';
                    img.alt = 'Blurred image';
                    resultEl.appendChild(img);
                }

                if (dlBtn) {
                    dlBtn.style.display = '';
                    dlBtn.onclick = function () {
                        var a = document.createElement('a');
                        a.href = URL.createObjectURL(blob);
                        a.download = 'blurred-image.' + (mime === 'image/jpeg' ? 'jpg' : mime === 'image/png' ? 'png' : mime === 'image/webp' ? 'webp' : 'png');
                        a.click();
                        URL.revokeObjectURL(a.href);
                    };
                }

                TCTP.toast('Blur applied to ' + blurRegions.length + ' area' + (blurRegions.length !== 1 ? 's' : '') + '!', '\u2705');
            }, mime, quality);
        });
    }
})();
