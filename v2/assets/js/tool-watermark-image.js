/**
 * Watermark Image — Tool JS
 * 100% client-side image watermark using canvas API.
 * Text and image watermarks with position, opacity, tiling.
 *
 * Widget IDs (widget-watermark-image.php):
 *  - tc-wm-drop, tc-wm-file
 *  - tc-wm-canvas, tc-wm-preview-wrap, tc-wm-preview-section
 *  - tc-wm-text-opts, tc-wm-image-opts
 *  - tc-wm-text, tc-wm-font, tc-wm-size, tc-wm-color, tc-wm-stroke-color, tc-wm-stroke-width
 *  - tc-wm-logo-drop, tc-wm-logo-input, tc-wm-logo-preview, tc-wm-logo-img, tc-wm-logo-remove
 *  - tc-wm-logo-scale, tc-wm-logo-size-wrap
 *  - tc-wm-opacity, tc-wm-opacity-val
 *  - tc-wm-repeat-modes, tc-wm-tile-opts, tc-wm-spacing, tc-wm-spacing-val
 *  - tc-wm-position-grid, tc-wm-pos
 *  - tc-wm-quality-wrap, tc-wm-quality, tc-wm-quality-val
 *  - tc-wm-apply, tc-wm-download
 *  - tc-wm-stat-orig, tc-wm-stat-out, tc-wm-stat-saved
 *  - tc-wm-preview-orig, tc-wm-result
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var dropEl = document.getElementById('tc-wm-drop');
    if (!dropEl) return;

    var file = null;
    var imgEl = null;
    var naturalW = 0, naturalH = 0;
    var displayW = 0, displayH = 0;
    var scaleX = 1, scaleY = 1;

    var watermarkType = 'text';
    var watermarkPosition = 'center';
    var watermarkRepeat = 'once';
    var outputFormat = 'original';
    var watermarkBlob = null;

    var logoImage = null;
    var logoLoaded = false;

    var canvas = document.getElementById('tc-wm-canvas');
    var previewWrap = document.getElementById('tc-wm-preview-wrap');
    var previewSection = document.getElementById('tc-wm-preview-section');
    var textOpts = document.getElementById('tc-wm-text-opts');
    var imageOpts = document.getElementById('tc-wm-image-opts');
    var typeCards = document.querySelectorAll('.tc-wm-type-modes .tc-rsz-mode-card');
    var repeatCards = document.querySelectorAll('.tc-wm-repeat-modes .tc-rsz-mode-card');
    var posCards = document.querySelectorAll('.tc-wm-pos');
    var fmtCards = document.querySelectorAll('.tc-rsz-format-row .tc-rsz-fmt');
    var applyBtn = document.getElementById('tc-wm-apply');
    var dlBtn = document.getElementById('tc-wm-download');

    // ── Drop zone ──────────────────────────────────────────────

    TCTP.initDropZone('tc-wm-drop', 'tc-wm-drop-input', function (f) {
        if (!f.type.match(/^image\/(jpeg|png|webp|gif)$/)) {
            TCTP.toast('Please select a JPG, PNG, WebP, or GIF image.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        watermarkBlob = null;
        if (dlBtn) dlBtn.style.display = 'none';
        TCTP.showFileRow('tc-wm-file', f);
        loadImage(f);
    }, 'image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif');

    var removeBtn = document.querySelector('#tc-wm-file .tc-x');
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            file = null;
            imgEl = null;
            watermarkBlob = null;
            if (dlBtn) dlBtn.style.display = 'none';
            TCTP.hideFileRow('tc-wm-file');
            if (previewSection) previewSection.style.display = 'none';
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

                if (previewSection) previewSection.style.display = '';
                if (!canvas) return;

                var wrapW = previewWrap ? previewWrap.clientWidth : 600;
                var maxH = 400;
                displayW = Math.min(wrapW, naturalW);
                displayH = Math.round(displayW * (naturalH / naturalW));
                if (displayH > maxH) {
                    displayH = maxH;
                    displayW = Math.round(displayH * (naturalW / naturalH));
                }

                canvas.width = displayW;
                canvas.height = displayH;

                scaleX = naturalW / displayW;
                scaleY = naturalH / displayH;

                renderPreview();

                var origStat = document.getElementById('tc-wm-stat-orig');
                if (origStat) origStat.textContent = naturalW + '\u00D7' + naturalH;

                TCTP.updateResultPanel(
                    naturalW + '\u00D7' + naturalH,
                    '\u2014',
                    '\u2014',
                    'Ready'
                );

                var prevOrig = document.getElementById('tc-wm-preview-orig');
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

                TCTP.toast('Image loaded! Configure your watermark and click Add Watermark.', '\u2705');
            };
            imgEl.src = e.target.result;
        };
        reader.readAsDataURL(f);
    }

    // ── Render preview with watermark ──────────────────────────

    function renderPreview() {
        if (!canvas || !imgEl) return;
        var ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, displayW, displayH);
        ctx.drawImage(imgEl, 0, 0, displayW, displayH);
        drawWatermark(ctx, displayW, displayH, 1);
    }

    function drawWatermark(ctx, w, h, scale) {
        var opacity = parseInt(document.getElementById('tc-wm-opacity').value, 10) / 100;
        var spacing = parseInt(document.getElementById('tc-wm-spacing').value, 10) * scale;

        ctx.save();
        ctx.globalAlpha = opacity;

        if (watermarkType === 'image' && logoImage && logoLoaded) {
            var logoScalePct = parseInt(document.getElementById('tc-wm-logo-scale').value, 10) / 100;
            var logoW = w * logoScalePct;
            var logoH = logoW * (logoImage.naturalHeight / logoImage.naturalWidth);

            if (watermarkRepeat === 'tile') {
                drawTiled(ctx, w, h, logoW, logoH, spacing, function (ctx, x, y) {
                    ctx.drawImage(logoImage, x, y, logoW, logoH);
                });
            } else {
                var pos = getWatermarkPos(w, h, logoW, logoH);
                ctx.drawImage(logoImage, pos.x, pos.y, logoW, logoH);
            }
        } else if (watermarkType === 'text') {
            var text = document.getElementById('tc-wm-text').value || 'WATERMARK';
            var fontSize = parseInt(document.getElementById('tc-wm-size').value, 10) * scale;
            var font = document.getElementById('tc-wm-font').value;
            var color = document.getElementById('tc-wm-color').value;
            var strokeColor = document.getElementById('tc-wm-stroke-color').value;
            var strokeWidth = parseInt(document.getElementById('tc-wm-stroke-width').value, 10) * scale;

            ctx.font = 'bold ' + fontSize + 'px ' + font;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            if (watermarkRepeat === 'tile') {
                var metrics = ctx.measureText(text);
                var tw = metrics.width;
                var th = fontSize * 1.2;
                drawTiled(ctx, w, h, tw, th, spacing, function (ctx, x, y) {
                    ctx.fillStyle = color;
                    ctx.strokeStyle = strokeColor;
                    ctx.lineWidth = strokeWidth;
                    if (strokeWidth > 0) ctx.strokeText(text, x + tw / 2, y + th / 2);
                    ctx.fillText(text, x + tw / 2, y + th / 2);
                });
            } else {
                var tw = ctx.measureText(text).width;
                var th = fontSize * 1.2;
                var pos = getWatermarkPos(w, h, tw, th);
                ctx.fillStyle = color;
                ctx.strokeStyle = strokeColor;
                ctx.lineWidth = strokeWidth;
                if (strokeWidth > 0) ctx.strokeText(text, pos.x + tw / 2, pos.y + th / 2);
                ctx.fillText(text, pos.x + tw / 2, pos.y + th / 2);
            }
        }

        ctx.restore();
    }

    function drawTiled(ctx, w, h, itemW, itemH, spacing, drawFn) {
        var cols = Math.ceil(w / (itemW + spacing)) + 1;
        var rows = Math.ceil(h / (itemH + spacing)) + 1;
        var offsetX = (w - cols * (itemW + spacing) + spacing) / 2;
        var offsetY = (h - rows * (itemH + spacing) + spacing) / 2;

        for (var r = 0; r < rows; r++) {
            for (var c = 0; c < cols; c++) {
                var x = offsetX + c * (itemW + spacing);
                var y = offsetY + r * (itemH + spacing);
                drawFn(ctx, x, y);
            }
        }
    }

    function getWatermarkPos(canvasW, canvasH, itemW, itemH) {
        var margin = 20 * (canvasW / naturalW);
        var positions = {
            'top-left':      { x: margin, y: margin },
            'top-center':    { x: (canvasW - itemW) / 2, y: margin },
            'top-right':     { x: canvasW - itemW - margin, y: margin },
            'center-left':   { x: margin, y: (canvasH - itemH) / 2 },
            'center':        { x: (canvasW - itemW) / 2, y: (canvasH - itemH) / 2 },
            'center-right':  { x: canvasW - itemW - margin, y: (canvasH - itemH) / 2 },
            'bottom-left':   { x: margin, y: canvasH - itemH - margin },
            'bottom-center': { x: (canvasW - itemW) / 2, y: canvasH - itemH - margin },
            'bottom-right':  { x: canvasW - itemW - margin, y: canvasH - itemH - margin }
        };
        return positions[watermarkPosition] || positions['center'];
    }

    // ── Watermark type toggle ──────────────────────────────────

    typeCards.forEach(function (card) {
        card.addEventListener('click', function () {
            typeCards.forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            watermarkType = card.getAttribute('data-val') || 'text';
            if (textOpts) textOpts.style.display = watermarkType === 'text' ? '' : 'none';
            if (imageOpts) imageOpts.style.display = watermarkType === 'image' ? '' : 'none';
            renderPreview();
        });
    });

    // ── Position buttons ───────────────────────────────────────

    posCards.forEach(function (card) {
        card.addEventListener('click', function () {
            posCards.forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            watermarkPosition = card.getAttribute('data-val') || 'center';
            renderPreview();
        });
    });

    // ── Repeat toggle ──────────────────────────────────────────

    repeatCards.forEach(function (card) {
        card.addEventListener('click', function () {
            repeatCards.forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            watermarkRepeat = card.getAttribute('data-val') || 'once';
            var tileOpts = document.getElementById('tc-wm-tile-opts');
            if (tileOpts) tileOpts.style.display = watermarkRepeat === 'tile' ? '' : 'none';
            renderPreview();
        });
    });

    // ── Output format ──────────────────────────────────────────

    fmtCards.forEach(function (btn) {
        btn.addEventListener('click', function () {
            fmtCards.forEach(function (b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            outputFormat = btn.getAttribute('data-val') || 'original';
            var qw = document.getElementById('tc-wm-quality-wrap');
            if (qw) qw.style.display = (outputFormat === 'image/jpeg' || outputFormat === 'image/webp') ? '' : 'none';
        });
    });

    // ── Live update sliders & inputs ───────────────────────────

    var debounceTimer = null;
    function debouncedRender() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(renderPreview, 100);
    }

    var opacitySlider = document.getElementById('tc-wm-opacity');
    var opacityVal = document.getElementById('tc-wm-opacity-val');
    if (opacitySlider) {
        opacitySlider.addEventListener('input', function () {
            if (opacityVal) opacityVal.textContent = opacitySlider.value + '%';
            debouncedRender();
        });
    }

    var spacingSlider = document.getElementById('tc-wm-spacing');
    var spacingVal = document.getElementById('tc-wm-spacing-val');
    if (spacingSlider) {
        spacingSlider.addEventListener('input', function () {
            if (spacingVal) spacingVal.textContent = spacingSlider.value + 'px';
            debouncedRender();
        });
    }

    var qualitySlider = document.getElementById('tc-wm-quality');
    var qualityVal = document.getElementById('tc-wm-quality-val');
    if (qualitySlider) {
        qualitySlider.addEventListener('input', function () {
            if (qualityVal) qualityVal.textContent = qualitySlider.value + '%';
        });
    }

    var sizeInput = document.getElementById('tc-wm-size');
    if (sizeInput) sizeInput.addEventListener('input', debouncedRender);

    var textInput = document.getElementById('tc-wm-text');
    if (textInput) textInput.addEventListener('input', debouncedRender);

    var fontSelect = document.getElementById('tc-wm-font');
    if (fontSelect) fontSelect.addEventListener('change', debouncedRender);

    var colorInput = document.getElementById('tc-wm-color');
    var colorHex = document.getElementById('tc-wm-color-hex');
    if (colorInput) {
        colorInput.addEventListener('input', function () {
            if (colorHex) colorHex.textContent = colorInput.value;
            debouncedRender();
        });
    }

    var strokeInput = document.getElementById('tc-wm-stroke-color');
    var strokeHex = document.getElementById('tc-wm-stroke-hex');
    if (strokeInput) {
        strokeInput.addEventListener('input', function () {
            if (strokeHex) strokeHex.textContent = strokeInput.value;
            debouncedRender();
        });
    }

    var strokeWidth = document.getElementById('tc-wm-stroke-width');
    if (strokeWidth) strokeWidth.addEventListener('input', debouncedRender);

    var logoScale = document.getElementById('tc-wm-logo-scale');
    if (logoScale) logoScale.addEventListener('input', debouncedRender);

    // ── Logo upload (for image watermark) ──────────────────────

    var logoDrop = document.getElementById('tc-wm-logo-drop');
    var logoInput = document.getElementById('tc-wm-logo-input');
    var logoPreview = document.getElementById('tc-wm-logo-preview');
    var logoImg = document.getElementById('tc-wm-logo-img');
    var logoRemoveBtn = document.getElementById('tc-wm-logo-remove');

    if (logoDrop) {
        logoDrop.addEventListener('click', function () {
            if (logoInput) logoInput.click();
        });

        logoDrop.addEventListener('dragover', function (e) {
            e.preventDefault();
            logoDrop.classList.add('tc-wm-logo-drop-hover');
        });
        logoDrop.addEventListener('dragleave', function () {
            logoDrop.classList.remove('tc-wm-logo-drop-hover');
        });
        logoDrop.addEventListener('drop', function (e) {
            e.preventDefault();
            logoDrop.classList.remove('tc-wm-logo-drop-hover');
            if (e.dataTransfer.files.length) handleLogoFile(e.dataTransfer.files[0]);
        });
    }

    if (logoInput) {
        logoInput.addEventListener('change', function () {
            if (logoInput.files.length) handleLogoFile(logoInput.files[0]);
        });
    }

    function handleLogoFile(f) {
        if (!f.type.match(/^image\//)) {
            TCTP.toast('Please select an image file for the watermark.', '\u26A0\uFE0F');
            return;
        }
        var reader = new FileReader();
        reader.onload = function (e) {
            logoImage = new Image();
            logoImage.onload = function () {
                logoLoaded = true;
                if (logoDrop) logoDrop.style.display = 'none';
                if (logoPreview) logoPreview.style.display = '';
                if (logoImg) logoImg.src = e.target.result;
                renderPreview();
                TCTP.toast('Logo loaded!', '\u2705');
            };
            logoImage.src = e.target.result;
        };
        reader.readAsDataURL(f);
    }

    if (logoRemoveBtn) {
        logoRemoveBtn.addEventListener('click', function () {
            logoImage = null;
            logoLoaded = false;
            if (logoDrop) logoDrop.style.display = '';
            if (logoPreview) logoPreview.style.display = 'none';
            if (logoInput) logoInput.value = '';
            renderPreview();
        });
    }

    // ── Apply watermark ────────────────────────────────────────

    if (applyBtn) {
        applyBtn.addEventListener('click', function () {
            if (!imgEl) {
                TCTP.toast('Please upload an image first.', '\u26A0\uFE0F');
                return;
            }
            if (watermarkType === 'image' && !logoLoaded) {
                TCTP.toast('Please upload a watermark logo first.', '\u26A0\uFE0F');
                return;
            }
            if (watermarkType === 'text' && !document.getElementById('tc-wm-text').value.trim()) {
                TCTP.toast('Please enter watermark text.', '\u26A0\uFE0F');
                return;
            }

            TCTP.showProgress('tc-wm-progress', 50, 'Adding watermark...');

            var outCanvas = document.createElement('canvas');
            outCanvas.width = naturalW;
            outCanvas.height = naturalH;
            var ctx = outCanvas.getContext('2d');
            ctx.drawImage(imgEl, 0, 0, naturalW, naturalH);
            drawWatermark(ctx, naturalW, naturalH, scaleX);

            var mime = outputFormat === 'original' ? (file ? file.type : 'image/png') : outputFormat;
            var quality = 0.92;
            if (mime === 'image/jpeg' || mime === 'image/webp') {
                var qs = document.getElementById('tc-wm-quality');
                if (qs) quality = parseInt(qs.value, 10) / 100;
            }

            outCanvas.toBlob(function (blob) {
                watermarkBlob = blob;
                TCTP.setProgress('tc-wm-progress', 100, 'Done!');
                setTimeout(function () { TCTP.hideProgress('tc-wm-progress'); }, 600);

                var outStat = document.getElementById('tc-wm-stat-out');
                if (outStat) outStat.textContent = naturalW + '\u00D7' + naturalH;

                var saved = file ? (((1 - blob.size / file.size) * 100).toFixed(1) + '%') : '\u2014';
                var savedStat = document.getElementById('tc-wm-stat-saved');
                if (savedStat) savedStat.textContent = saved;

                TCTP.updateResultPanel(
                    naturalW + '\u00D7' + naturalH,
                    naturalW + '\u00D7' + naturalH,
                    saved,
                    'Done'
                );
                TCTP.switchToResultTab();

                var resultEl = document.getElementById('tc-wm-result');
                if (resultEl) {
                    resultEl.innerHTML = '';
                    var url = URL.createObjectURL(blob);
                    var img = new Image();
                    img.src = url;
                    img.style.maxWidth = '100%';
                    img.style.borderRadius = '8px';
                    img.style.objectFit = 'contain';
                    img.alt = 'Watermarked image';
                    resultEl.appendChild(img);
                }

                if (dlBtn) {
                    dlBtn.style.display = '';
                    dlBtn.onclick = function () {
                        var a = document.createElement('a');
                        a.href = URL.createObjectURL(blob);
                        a.download = 'watermarked-image.' + (mime === 'image/jpeg' ? 'jpg' : mime === 'image/png' ? 'png' : mime === 'image/webp' ? 'webp' : 'png');
                        a.click();
                        URL.revokeObjectURL(a.href);
                    };
                }

                TCTP.toast('Watermark added successfully!', '\u2705');
            }, mime, quality);
        });
    }
})();
