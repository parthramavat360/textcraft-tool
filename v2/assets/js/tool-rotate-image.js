/**
 * Rotate Image — Tool JS
 * 100% client-side image rotation using canvas API.
 * Supports 90°/180°/270° and custom angles, plus horizontal/vertical flip.
 *
 * Widget IDs (widget-rotate-image.php):
 *  - tc-rot-drop, tc-rot-file
 *  - tc-rot-canvas, tc-rot-preview-section
 *  - tc-rot-custom-wrap, tc-rot-angle, tc-rot-angle-val
 *  - tc-rot-apply, tc-rot-download
 *  - tc-rot-stat-orig, tc-rot-stat-out, tc-rot-stat-angle
 *  - tc-rot-preview-orig, tc-rot-result
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var dropEl = document.getElementById('tc-rot-drop');
    if (!dropEl) return;

    var file = null;
    var imgEl = null;
    var naturalW = 0, naturalH = 0;
    var rotateMode = '90';
    var flipMode = 'none';
    var outputFormat = 'original';
    var rotatedBlob = null;

    var previewSection = document.getElementById('tc-rot-preview-section');
    var canvas = document.getElementById('tc-rot-canvas');
    var previewWrap = document.getElementById('tc-rot-preview-wrap');
    var customWrap = document.getElementById('tc-rot-custom-wrap');
    var angleSlider = document.getElementById('tc-rot-angle');
    var angleVal = document.getElementById('tc-rot-angle-val');
    var modeCards = document.querySelectorAll('.tc-rot-modes .tc-rsz-mode-card');
    var flipCards = document.querySelectorAll('.tc-rot-flip-modes .tc-rsz-mode-card');
    var fmtCards = document.querySelectorAll('.tc-rot-format-row .tc-rsz-fmt, [data-widget-type="rotate_image"] .tc-rsz-format-row .tc-rsz-fmt');
    var applyBtn = document.getElementById('tc-rot-apply');
    var dlBtn = document.getElementById('tc-rot-download');

    // Select format buttons within this widget only
    var widgetEl = document.querySelector('[data-widget-type="rotate_image"]');
    if (widgetEl) {
        fmtCards = widgetEl.querySelectorAll('.tc-rsz-format-row .tc-rsz-fmt');
    }

    // ── Drop zone ──────────────────────────────────────────────

    TCTP.initDropZone('tc-rot-drop', 'tc-rot-drop-input', function (f) {
        if (!f.type.match(/^image\/(jpeg|png|webp|gif)$/)) {
            TCTP.toast('Please select a JPG, PNG, WebP, or GIF image.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        rotatedBlob = null;
        if (dlBtn) dlBtn.style.display = 'none';
        TCTP.showFileRow('tc-rot-file', f);
        loadImage(f);
    }, 'image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif');

    var removeBtn = document.querySelector('#tc-rot-file .tc-x');
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            file = null;
            imgEl = null;
            rotatedBlob = null;
            if (dlBtn) dlBtn.style.display = 'none';
            TCTP.hideFileRow('tc-rot-file');
            if (previewSection) previewSection.style.display = 'none';
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

                if (previewSection) previewSection.style.display = '';
                drawPreview();

                var origStat = document.getElementById('tc-rot-stat-orig');
                if (origStat) origStat.textContent = naturalW + '\u00D7' + naturalH;
                var outStat = document.getElementById('tc-rot-stat-out');
                if (outStat) outStat.textContent = naturalW + '\u00D7' + naturalH;

                TCTP.updateResultPanel(
                    naturalW + '\u00D7' + naturalH,
                    '\u2014',
                    '\u2014',
                    'Ready'
                );

                // Original preview
                var prevOrig = document.getElementById('tc-rot-preview-orig');
                if (prevOrig) {
                    prevOrig.innerHTML = '';
                    var img = new Image();
                    img.src = e.target.result;
                    img.style.maxWidth = '100%';
                    img.style.maxHeight = '300px';
                    img.style.borderRadius = '8px';
                    img.style.objectFit = 'contain';
                    img.alt = 'Original image';
                    prevOrig.appendChild(img);
                }

                TCTP.toast('Image loaded! Choose a rotation angle.', '\u2705');
            };
            imgEl.src = e.target.result;
        };
        reader.readAsDataURL(f);
    }

    // ── Draw preview with current rotation/flip ────────────────

    function drawPreview() {
        if (!imgEl || !canvas || !previewWrap) return;

        var deg = getRotationDeg();
        var isQuadrant = (deg % 90 === 0);
        var srcW = naturalW, srcH = naturalH;

        if (isQuadrant) {
            if (deg === 90 || deg === -270 || deg === 270 || deg === -90) {
                canvas.width = srcH;
                canvas.height = srcW;
            } else {
                canvas.width = srcW;
                canvas.height = srcH;
            }
        } else {
            var rad = Math.abs(deg) * Math.PI / 180;
            var cos = Math.cos(rad);
            var sin = Math.sin(rad);
            canvas.width = Math.ceil(Math.abs(srcW * cos) + Math.abs(srcH * sin));
            canvas.height = Math.ceil(Math.abs(srcW * sin) + Math.abs(srcH * cos));
        }

        var ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.save();
        ctx.translate(canvas.width / 2, canvas.height / 2);
        ctx.rotate(deg * Math.PI / 180);

        if (flipMode === 'horizontal') ctx.scale(-1, 1);
        else if (flipMode === 'vertical') ctx.scale(1, -1);

        ctx.drawImage(imgEl, -srcW / 2, -srcH / 2, srcW, srcH);
        ctx.restore();

        // Scale preview to fit container
        var maxW = previewWrap.clientWidth || 600;
        var maxH = 400;
        var scale = Math.min(1, maxW / canvas.width, maxH / canvas.height);
        canvas.style.width = Math.round(canvas.width * scale) + 'px';
        canvas.style.height = Math.round(canvas.height * scale) + 'px';

        var outStat = document.getElementById('tc-rot-stat-out');
        if (outStat) outStat.textContent = canvas.width + '\u00D7' + canvas.height;
    }

    function getRotationDeg() {
        if (rotateMode === 'custom') {
            return parseInt(angleSlider ? angleSlider.value : '0', 10) || 0;
        }
        return parseInt(rotateMode, 10) || 0;
    }

    // ── Mode cards ─────────────────────────────────────────────

    modeCards.forEach(function (card) {
        card.addEventListener('click', function () {
            modeCards.forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            rotateMode = card.getAttribute('data-val') || '90';

            if (customWrap) customWrap.style.display = rotateMode === 'custom' ? '' : 'none';
            drawPreview();
        });
    });

    // ── Flip cards ─────────────────────────────────────────────

    flipCards.forEach(function (card) {
        card.addEventListener('click', function () {
            flipCards.forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            flipMode = card.getAttribute('data-val') || 'none';
            drawPreview();
        });
    });

    // ── Angle slider ───────────────────────────────────────────

    if (angleSlider) {
        angleSlider.addEventListener('input', function () {
            if (angleVal) angleVal.textContent = angleSlider.value + '\u00B0';
            drawPreview();
        });
    }

    // ── Output format ──────────────────────────────────────────

    // Use event delegation on the widget container for format buttons
    var widgetContainer = dropEl ? dropEl.closest('.tc-rsz-options') || dropEl.closest('.elementor-widget-container') : null;
    var allFmtBtns = widgetContainer ? widgetContainer.querySelectorAll('.tc-rsz-format-row .tc-rsz-fmt') : fmtCards;

    allFmtBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            allFmtBtns.forEach(function (b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            outputFormat = btn.getAttribute('data-val') || 'original';
            var qw = document.getElementById('tc-rot-quality-wrap');
            if (qw) {
                qw.style.display = (outputFormat === 'image/jpeg' || outputFormat === 'image/webp') ? '' : 'none';
            }
        });
    });

    // ── Quality slider ─────────────────────────────────────────

    var qualitySlider = document.getElementById('tc-rot-quality');
    var qualityValEl = document.getElementById('tc-rot-quality-val');
    if (qualitySlider) {
        qualitySlider.addEventListener('input', function () {
            if (qualityValEl) qualityValEl.textContent = qualitySlider.value + '%';
        });
    }

    // ── Apply / Rotate ─────────────────────────────────────────

    if (applyBtn) {
        applyBtn.addEventListener('click', function () {
            if (!imgEl) {
                TCTP.toast('Please upload an image first.', '\u26A0\uFE0F');
                return;
            }

            TCTP.showProgress('tc-rot-progress', 50, 'Rotating...');

            var deg = getRotationDeg();
            var srcW = naturalW, srcH = naturalH;
            var isQuadrant = (deg % 90 === 0);
            var outW, outH;

            if (isQuadrant) {
                if (deg === 90 || deg === -270 || deg === 270 || deg === -90) {
                    outW = srcH; outH = srcW;
                } else {
                    outW = srcW; outH = srcH;
                }
            } else {
                var rad = Math.abs(deg) * Math.PI / 180;
                var cos = Math.cos(rad);
                var sin = Math.sin(rad);
                outW = Math.ceil(Math.abs(srcW * cos) + Math.abs(srcH * sin));
                outH = Math.ceil(Math.abs(srcW * sin) + Math.abs(srcH * cos));
            }

            var outCanvas = document.createElement('canvas');
            outCanvas.width = outW;
            outCanvas.height = outH;
            var ctx = outCanvas.getContext('2d');
            ctx.translate(outW / 2, outH / 2);
            ctx.rotate(deg * Math.PI / 180);

            if (flipMode === 'horizontal') ctx.scale(-1, 1);
            else if (flipMode === 'vertical') ctx.scale(1, -1);

            ctx.drawImage(imgEl, -srcW / 2, -srcH / 2, srcW, srcH);

            var mime = outputFormat === 'original' ? (file ? file.type : 'image/png') : outputFormat;
            var quality = 0.92;
            if (mime === 'image/jpeg' || mime === 'image/webp') {
                var qs = document.getElementById('tc-rot-quality');
                if (qs) quality = parseInt(qs.value, 10) / 100;
            }

            outCanvas.toBlob(function (blob) {
                rotatedBlob = blob;
                TCTP.setProgress('tc-rot-progress', 100, 'Done!');
                setTimeout(function () { TCTP.hideProgress('tc-rot-progress'); }, 600);

                var angleStat = document.getElementById('tc-rot-stat-angle');
                if (angleStat) angleStat.textContent = (deg >= 0 ? '+' : '') + deg + '\u00B0' + (flipMode !== 'none' ? ' + ' + flipMode : '');
                var outStat = document.getElementById('tc-rot-stat-out');
                if (outStat) outStat.textContent = outW + '\u00D7' + outH;

                var saved = file ? (((1 - blob.size / file.size) * 100).toFixed(1) + '%') : '\u2014';
                TCTP.updateResultPanel(
                    srcW + '\u00D7' + srcH,
                    outW + '\u00D7' + outH,
                    saved,
                    'Done'
                );
                TCTP.switchToResultTab();

                // Show result preview
                var resultEl = document.getElementById('tc-rot-result');
                if (resultEl) {
                    resultEl.innerHTML = '';
                    var url = URL.createObjectURL(blob);
                    var img = new Image();
                    img.src = url;
                    img.style.maxWidth = '100%';
                    img.style.maxHeight = '400px';
                    img.style.borderRadius = '8px';
                    img.style.objectFit = 'contain';
                    img.alt = 'Rotated image';
                    resultEl.appendChild(img);
                }

                if (dlBtn) {
                    dlBtn.style.display = '';
                    dlBtn.onclick = function () {
                        var a = document.createElement('a');
                        a.href = URL.createObjectURL(blob);
                        var ext = mime === 'image/jpeg' ? 'jpg' : mime === 'image/png' ? 'png' : mime === 'image/webp' ? 'webp' : 'png';
                        a.download = 'rotated-image.' + ext;
                        a.click();
                        URL.revokeObjectURL(a.href);
                    };
                }

                TCTP.toast('Image rotated successfully!', '\u2705');
            }, mime, quality);
        });
    }
})();
