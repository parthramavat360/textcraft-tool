/**
 * Flip Image — Tool JS
 * 100% client-side image flip using canvas API.
 * Horizontal, vertical, or both flips with live preview.
 *
 * Widget IDs (widget-flip-image.php):
 *  - tc-flip-drop, tc-flip-file
 *  - tc-flip-canvas, tc-flip-preview-wrap, tc-flip-preview-section
 *  - tc-flip-modes, tc-flip-quality-wrap, tc-flip-quality, tc-flip-quality-val
 *  - tc-flip-apply, tc-flip-download
 *  - tc-flip-stat-orig, tc-flip-stat-out, tc-flip-stat-dir
 *  - tc-flip-preview-orig, tc-flip-result
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var dropEl = document.getElementById('tc-flip-drop');
    if (!dropEl) return;

    var file = null;
    var imgEl = null;
    var naturalW = 0, naturalH = 0;
    var displayW = 0, displayH = 0;

    var flipDirection = 'horizontal';
    var outputFormat = 'original';
    var flippedBlob = null;

    var canvas = document.getElementById('tc-flip-canvas');
    var previewWrap = document.getElementById('tc-flip-preview-wrap');
    var previewSection = document.getElementById('tc-flip-preview-section');
    var modeCards = document.querySelectorAll('.tc-flip-modes .tc-rsz-mode-card');
    var fmtCards = document.querySelectorAll('.tc-rsz-format-row .tc-rsz-fmt');
    var applyBtn = document.getElementById('tc-flip-apply');
    var dlBtn = document.getElementById('tc-flip-download');

    // ── Drop zone ──────────────────────────────────────────────

    TCTP.initDropZone('tc-flip-drop', 'tc-flip-drop-input', function (f) {
        if (!f.type.match(/^image\/(jpeg|png|webp|gif)$/)) {
            TCTP.toast('Please select a JPG, PNG, WebP, or GIF image.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        flippedBlob = null;
        if (dlBtn) dlBtn.style.display = 'none';
        TCTP.showFileRow('tc-flip-file', f);
        loadImage(f);
    }, 'image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif');

    var removeBtn = document.querySelector('#tc-flip-file .tc-x');
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            file = null;
            imgEl = null;
            flippedBlob = null;
            if (dlBtn) dlBtn.style.display = 'none';
            TCTP.hideFileRow('tc-flip-file');
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

                renderPreview();

                var origStat = document.getElementById('tc-flip-stat-orig');
                if (origStat) origStat.textContent = naturalW + '\u00D7' + naturalH;

                TCTP.updateResultPanel(
                    naturalW + '\u00D7' + naturalH,
                    '\u2014',
                    '\u2014',
                    'Ready'
                );

                var prevOrig = document.getElementById('tc-flip-preview-orig');
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

                TCTP.toast('Image loaded! Choose a flip direction and click Flip.', '\u2705');
            };
            imgEl.src = e.target.result;
        };
        reader.readAsDataURL(f);
    }

    // ── Render preview with flip ───────────────────────────────

    function renderPreview() {
        if (!canvas || !imgEl) return;
        var ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, displayW, displayH);
        ctx.save();

        if (flipDirection === 'horizontal') {
            ctx.translate(displayW, 0);
            ctx.scale(-1, 1);
        } else if (flipDirection === 'vertical') {
            ctx.translate(0, displayH);
            ctx.scale(1, -1);
        } else {
            ctx.translate(displayW, displayH);
            ctx.scale(-1, -1);
        }

        ctx.drawImage(imgEl, 0, 0, displayW, displayH);
        ctx.restore();
    }

    // ── Direction mode cards ───────────────────────────────────

    modeCards.forEach(function (card) {
        card.addEventListener('click', function () {
            modeCards.forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            flipDirection = card.getAttribute('data-val') || 'horizontal';

            var dirStat = document.getElementById('tc-flip-stat-dir');
            if (dirStat) {
                var labels = { horizontal: 'Horizontal', vertical: 'Vertical', both: 'Both' };
                dirStat.textContent = labels[flipDirection] || flipDirection;
            }

            renderPreview();
        });
    });

    // ── Output format ──────────────────────────────────────────

    fmtCards.forEach(function (btn) {
        btn.addEventListener('click', function () {
            fmtCards.forEach(function (b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            outputFormat = btn.getAttribute('data-val') || 'original';
            var qw = document.getElementById('tc-flip-quality-wrap');
            if (qw) qw.style.display = (outputFormat === 'image/jpeg' || outputFormat === 'image/webp') ? '' : 'none';
        });
    });

    // ── Quality slider ─────────────────────────────────────────

    var qualitySlider = document.getElementById('tc-flip-quality');
    var qualityVal = document.getElementById('tc-flip-quality-val');
    if (qualitySlider) {
        qualitySlider.addEventListener('input', function () {
            if (qualityVal) qualityVal.textContent = qualitySlider.value + '%';
        });
    }

    // ── Apply flip ─────────────────────────────────────────────

    if (applyBtn) {
        applyBtn.addEventListener('click', function () {
            if (!imgEl) {
                TCTP.toast('Please upload an image first.', '\u26A0\uFE0F');
                return;
            }

            TCTP.showProgress('tc-flip-progress', 50, 'Flipping...');

            var outCanvas = document.createElement('canvas');
            outCanvas.width = naturalW;
            outCanvas.height = naturalH;
            var ctx = outCanvas.getContext('2d');
            ctx.save();

            if (flipDirection === 'horizontal') {
                ctx.translate(naturalW, 0);
                ctx.scale(-1, 1);
            } else if (flipDirection === 'vertical') {
                ctx.translate(0, naturalH);
                ctx.scale(1, -1);
            } else {
                ctx.translate(naturalW, naturalH);
                ctx.scale(-1, -1);
            }

            ctx.drawImage(imgEl, 0, 0, naturalW, naturalH);
            ctx.restore();

            var mime = outputFormat === 'original' ? (file ? file.type : 'image/png') : outputFormat;
            var quality = 0.92;
            if (mime === 'image/jpeg' || mime === 'image/webp') {
                var qs = document.getElementById('tc-flip-quality');
                if (qs) quality = parseInt(qs.value, 10) / 100;
            }

            outCanvas.toBlob(function (blob) {
                flippedBlob = blob;
                TCTP.setProgress('tc-flip-progress', 100, 'Done!');
                setTimeout(function () { TCTP.hideProgress('tc-flip-progress'); }, 600);

                var outStat = document.getElementById('tc-flip-stat-out');
                if (outStat) outStat.textContent = naturalW + '\u00D7' + naturalH;

                var saved = file ? (((1 - blob.size / file.size) * 100).toFixed(1) + '%') : '\u2014';

                TCTP.updateResultPanel(
                    naturalW + '\u00D7' + naturalH,
                    naturalW + '\u00D7' + naturalH,
                    saved,
                    'Done'
                );
                TCTP.switchToResultTab();

                var resultEl = document.getElementById('tc-flip-result');
                if (resultEl) {
                    resultEl.innerHTML = '';
                    var url = URL.createObjectURL(blob);
                    var img = new Image();
                    img.src = url;
                    img.style.maxWidth = '100%';
                    img.style.borderRadius = '8px';
                    img.style.objectFit = 'contain';
                    img.alt = 'Flipped image';
                    resultEl.appendChild(img);
                }

                if (dlBtn) {
                    dlBtn.style.display = '';
                    dlBtn.onclick = function () {
                        var a = document.createElement('a');
                        a.href = URL.createObjectURL(blob);
                        a.download = 'flipped-image.' + (mime === 'image/jpeg' ? 'jpg' : mime === 'image/png' ? 'png' : mime === 'image/webp' ? 'webp' : 'png');
                        a.click();
                        URL.revokeObjectURL(a.href);
                    };
                }

                TCTP.toast('Image flipped successfully!', '\u2705');
            }, mime, quality);
        });
    }
})();
