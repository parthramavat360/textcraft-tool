/**
 * Meme Generator — Tool JS
 * 100% client-side meme generation using canvas API.
 * Classic top/bottom text with white fill + black outline.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var dropEl = document.getElementById('tc-meme-drop');
    if (!dropEl) return;

    var file = null;
    var imgEl = null;
    var naturalW = 0, naturalH = 0;
    var displayW = 0, displayH = 0;
    var outputFormat = 'original';
    var memeBlob = null;

    var canvas = document.getElementById('tc-meme-canvas');
    var previewWrap = document.getElementById('tc-meme-preview-wrap');
    var previewSection = document.getElementById('tc-meme-preview-section');
    var textSection = document.getElementById('tc-meme-text-section');
    var styleSection = document.getElementById('tc-meme-style-section');
    var formatSection = document.getElementById('tc-meme-format-section');
    var fmtCards = document.querySelectorAll('.tc-rsz-format-row .tc-rsz-fmt');
    var applyBtn = document.getElementById('tc-meme-apply');
    var dlBtn = document.getElementById('tc-meme-download');

    // ── Drop zone ──────────────────────────────────────────────

    TCTP.initDropZone('tc-meme-drop', 'tc-meme-drop-input', function (f) {
        if (!f.type.match(/^image\/(jpeg|png|webp|gif)$/)) {
            TCTP.toast('Please select a JPG, PNG, WebP, or GIF image.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        memeBlob = null;
        if (dlBtn) dlBtn.style.display = 'none';
        TCTP.showFileRow('tc-meme-file', f);
        loadImage(f);
    }, 'image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif');

    var removeBtn = document.querySelector('#tc-meme-file .tc-x');
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            file = null;
            imgEl = null;
            memeBlob = null;
            if (dlBtn) dlBtn.style.display = 'none';
            TCTP.hideFileRow('tc-meme-file');
            if (previewSection) previewSection.style.display = 'none';
            if (textSection) textSection.style.display = 'none';
            if (styleSection) styleSection.style.display = 'none';
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

                if (previewSection) previewSection.style.display = '';
                if (textSection) textSection.style.display = '';
                if (styleSection) styleSection.style.display = '';
                if (formatSection) formatSection.style.display = '';
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

                var origStat = document.getElementById('tc-meme-stat-orig');
                if (origStat) origStat.textContent = naturalW + '\u00D7' + naturalH;

                TCTP.updateResultPanel(naturalW + '\u00D7' + naturalH, '\u2014', '\u2014', 'Ready');

                var prevOrig = document.getElementById('tc-meme-preview-orig');
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

                TCTP.toast('Image loaded! Add your meme text.', '\u2705');
            };
            imgEl.src = e.target.result;
        };
        reader.readAsDataURL(f);
    }

    // ── Draw meme text ─────────────────────────────────────────

    function drawMemeText(ctx, text, y, w, h) {
        if (!text) return;

        var fontSize = parseInt(document.getElementById('tc-meme-fontsize').value, 10) * (h / naturalH);
        var color = document.getElementById('tc-meme-color').value;
        var strokeColor = document.getElementById('tc-meme-stroke').value;
        var strokeWidth = parseInt(document.getElementById('tc-meme-strokewidth').value, 10) * (h / naturalH);

        ctx.save();
        ctx.font = 'bold ' + fontSize + 'px Impact, Arial Black, sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'top';

        // Word wrap
        var words = text.toUpperCase().split(' ');
        var lines = [];
        var currentLine = words[0] || '';
        for (var i = 1; i < words.length; i++) {
            var testLine = currentLine + ' ' + words[i];
            if (ctx.measureText(testLine).width > w * 0.9) {
                lines.push(currentLine);
                currentLine = words[i];
            } else {
                currentLine = testLine;
            }
        }
        lines.push(currentLine);

        var lineHeight = fontSize * 1.1;
        var totalHeight = lines.length * lineHeight;
        var startY = y;

        for (var j = 0; j < lines.length; j++) {
            var lineY = startY + j * lineHeight;

            // Outline
            ctx.strokeStyle = strokeColor;
            ctx.lineWidth = strokeWidth;
            ctx.lineJoin = 'round';
            ctx.miterLimit = 2;
            ctx.strokeText(lines[j], w / 2, lineY);

            // Fill
            ctx.fillStyle = color;
            ctx.fillText(lines[j], w / 2, lineY);
        }

        ctx.restore();
    }

    // ── Render preview ─────────────────────────────────────────

    function renderPreview() {
        if (!canvas || !imgEl) return;
        var ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, displayW, displayH);
        ctx.drawImage(imgEl, 0, 0, displayW, displayH);

        var topText = document.getElementById('tc-meme-top').value;
        var bottomText = document.getElementById('tc-meme-bottom').value;

        if (topText) drawMemeText(ctx, topText, 10, displayW, displayH);
        if (bottomText) {
            var fontSize = parseInt(document.getElementById('tc-meme-fontsize').value, 10) * (displayH / naturalH);
            var words = bottomText.toUpperCase().split(' ');
            var lines = [];
            var currentLine = words[0] || '';
            for (var i = 1; i < words.length; i++) {
                ctx.font = 'bold ' + fontSize + 'px Impact, Arial Black, sans-serif';
                var testLine = currentLine + ' ' + words[i];
                if (ctx.measureText(testLine).width > displayW * 0.9) {
                    lines.push(currentLine);
                    currentLine = words[i];
                } else {
                    currentLine = testLine;
                }
            }
            lines.push(currentLine);
            var lineHeight = fontSize * 1.1;
            var totalHeight = lines.length * lineHeight;
            drawMemeText(ctx, bottomText, displayH - totalHeight - 10, displayW, displayH);
        }

        // Update char count
        var totalChars = (topText || '').length + (bottomText || '').length;
        var textStat = document.getElementById('tc-meme-stat-text');
        if (textStat) textStat.textContent = totalChars + ' chars';
    }

    // ── Live update listeners ──────────────────────────────────

    var debounceTimer = null;
    function debouncedRender() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(renderPreview, 80);
    }

    var topInput = document.getElementById('tc-meme-top');
    var bottomInput = document.getElementById('tc-meme-bottom');
    if (topInput) topInput.addEventListener('input', debouncedRender);
    if (bottomInput) bottomInput.addEventListener('input', debouncedRender);

    var fontSlider = document.getElementById('tc-meme-fontsize');
    var fontVal = document.getElementById('tc-meme-size-val');
    if (fontSlider) {
        fontSlider.addEventListener('input', function () {
            if (fontVal) fontVal.textContent = fontSlider.value;
            debouncedRender();
        });
    }

    var strokeSlider = document.getElementById('tc-meme-strokewidth');
    var strokeVal = document.getElementById('tc-meme-stroke-val');
    if (strokeSlider) {
        strokeSlider.addEventListener('input', function () {
            if (strokeVal) strokeVal.textContent = strokeSlider.value;
            debouncedRender();
        });
    }

    var colorInput = document.getElementById('tc-meme-color');
    var strokeInput = document.getElementById('tc-meme-stroke');
    if (colorInput) colorInput.addEventListener('input', debouncedRender);
    if (strokeInput) strokeInput.addEventListener('input', debouncedRender);

    // ── Format ─────────────────────────────────────────────────

    fmtCards.forEach(function (btn) {
        btn.addEventListener('click', function () {
            fmtCards.forEach(function (b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            outputFormat = btn.getAttribute('data-val') || 'original';
        });
    });

    // ── Generate meme ──────────────────────────────────────────

    if (applyBtn) {
        applyBtn.addEventListener('click', function () {
            if (!imgEl) {
                TCTP.toast('Please upload an image first.', '\u26A0\uFE0F');
                return;
            }

            var topText = document.getElementById('tc-meme-top').value;
            var bottomText = document.getElementById('tc-meme-bottom').value;
            if (!topText && !bottomText) {
                TCTP.toast('Please add at least some text.', '\u26A0\uFE0F');
                return;
            }

            TCTP.showProgress('tc-meme-progress', 50, 'Creating meme...');

            var outCanvas = document.createElement('canvas');
            outCanvas.width = naturalW;
            outCanvas.height = naturalH;
            var ctx = outCanvas.getContext('2d');
            ctx.drawImage(imgEl, 0, 0, naturalW, naturalH);

            if (topText) drawMemeText(ctx, topText, 10, naturalW, naturalH);
            if (bottomText) {
                var fontSize = parseInt(document.getElementById('tc-meme-fontsize').value, 10);
                var words = bottomText.toUpperCase().split(' ');
                var lines = [];
                var currentLine = words[0] || '';
                ctx.font = 'bold ' + fontSize + 'px Impact, Arial Black, sans-serif';
                for (var i = 1; i < words.length; i++) {
                    var testLine = currentLine + ' ' + words[i];
                    if (ctx.measureText(testLine).width > naturalW * 0.9) {
                        lines.push(currentLine);
                        currentLine = words[i];
                    } else {
                        currentLine = testLine;
                    }
                }
                lines.push(currentLine);
                var lineHeight = fontSize * 1.1;
                var totalHeight = lines.length * lineHeight;
                drawMemeText(ctx, bottomText, naturalH - totalHeight - 10, naturalW, naturalH);
            }

            var mime = outputFormat === 'original' ? (file ? file.type : 'image/png') : outputFormat;
            var quality = 0.92;

            outCanvas.toBlob(function (blob) {
                memeBlob = blob;
                TCTP.setProgress('tc-meme-progress', 100, 'Done!');
                setTimeout(function () { TCTP.hideProgress('tc-meme-progress'); }, 600);

                var outStat = document.getElementById('tc-meme-stat-out');
                if (outStat) outStat.textContent = TCTP.formatSize(blob.size);

                var saved = file ? (((1 - blob.size / file.size) * 100).toFixed(1) + '%') : '\u2014';
                TCTP.updateResultPanel(naturalW + '\u00D7' + naturalH, TCTP.formatSize(blob.size), saved, 'Done');
                TCTP.switchToResultTab();

                var resultEl = document.getElementById('tc-meme-result');
                if (resultEl) {
                    resultEl.innerHTML = '';
                    var url = URL.createObjectURL(blob);
                    var img = new Image();
                    img.src = url;
                    img.style.maxWidth = '100%';
                    img.style.borderRadius = '8px';
                    img.style.objectFit = 'contain';
                    img.alt = 'Generated meme';
                    resultEl.appendChild(img);
                }

                if (dlBtn) {
                    dlBtn.style.display = '';
                    dlBtn.onclick = function () {
                        var a = document.createElement('a');
                        a.href = URL.createObjectURL(blob);
                        a.download = 'meme.' + (mime === 'image/jpeg' ? 'jpg' : 'png');
                        a.click();
                        URL.revokeObjectURL(a.href);
                    };
                }

                TCTP.toast('Meme created!', '\u2705');
            }, mime, quality);
        });
    }
})();
