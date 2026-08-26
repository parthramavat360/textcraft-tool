/**
 * HTML to Image — Tool JS
 * 100% client-side HTML to image using foreignObject rendering.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var dropEl = document.getElementById('tc-html-code');
    if (!dropEl) return;

    var outputFormat = 'image/png';
    var imageBlob = null;

    var fmtCards = document.querySelectorAll('.tc-rsz-format-row .tc-rsz-fmt');
    var applyBtn = document.getElementById('tc-html-apply');
    var dlBtn = document.getElementById('tc-html-download');
    var wInput = document.getElementById('tc-html-w');
    var hInput = document.getElementById('tc-html-h');
    var lockBtn = document.getElementById('tc-html-lock');
    var lockRatio = false;

    // ── Format ─────────────────────────────────────────────────

    fmtCards.forEach(function (btn) {
        btn.addEventListener('click', function () {
            fmtCards.forEach(function (b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            outputFormat = btn.getAttribute('data-val') || 'image/png';
            var qw = document.getElementById('tc-html-quality-section');
            if (qw) qw.style.display = outputFormat === 'image/jpeg' ? '' : 'none';
        });
    });

    // ── Quality ────────────────────────────────────────────────

    var qualitySlider = document.getElementById('tc-html-quality');
    var qualityVal = document.getElementById('tc-html-quality-val');
    if (qualitySlider) {
        qualitySlider.addEventListener('input', function () {
            if (qualityVal) qualityVal.textContent = qualitySlider.value + '%';
        });
    }

    // ── Scale ──────────────────────────────────────────────────

    var scaleSlider = document.getElementById('tc-html-scale');
    var scaleVal = document.getElementById('tc-html-scale-val');
    if (scaleSlider) {
        scaleSlider.addEventListener('input', function () {
            if (scaleVal) scaleVal.textContent = scaleSlider.value + 'x';
        });
    }

    // ── Preset buttons ─────────────────────────────────────────

    document.querySelectorAll('.tc-svg-preset-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var w = parseInt(btn.getAttribute('data-w'), 10);
            var h = parseInt(btn.getAttribute('data-h'), 10);
            if (wInput) wInput.value = w;
            if (hInput) hInput.value = h;
        });
    });

    // ── Lock ───────────────────────────────────────────────────

    if (lockBtn) {
        lockBtn.addEventListener('click', function () {
            lockRatio = !lockRatio;
            lockBtn.className = lockRatio ? 'tc-rsz-lock tc-rsz-lock--on' : 'tc-rsz-lock tc-rsz-lock--off';
        });
    }

    // ── Generate image from HTML ───────────────────────────────

    if (applyBtn) {
        applyBtn.addEventListener('click', function () {
            var code = document.getElementById('tc-html-code').value.trim();
            if (!code) {
                TCTP.toast('Please enter some HTML code.', '\u26A0\uFE0F');
                return;
            }

            var outW = parseInt(wInput ? wInput.value : 800, 10);
            var outH = parseInt(hInput ? hInput.value : 400, 10);
            var scale = parseInt(scaleSlider ? scaleSlider.value : 1, 10);
            var quality = qualitySlider ? parseInt(qualitySlider.value, 10) / 100 : 0.92;

            if (outW < 50 || outH < 50 || outW > 4096 || outH > 4096) {
                TCTP.toast('Dimensions must be between 50 and 4096.', '\u26A0\uFE0F');
                return;
            }

            TCTP.showProgress('tc-html-progress', 20, 'Rendering HTML...');

            // Create SVG with foreignObject
            var svgNS = 'http://www.w3.org/2000/svg';
            var xhtmlNS = 'http://www.w3.org/1999/xhtml';

            var svgStr = '<svg xmlns="' + svgNS + '" width="' + outW + '" height="' + outH + '">' +
                '<foreignObject width="100%" height="100%">' +
                '<div xmlns="' + xhtmlNS + '" style="width:' + outW + 'px;height:' + outH + 'px;overflow:hidden;">' +
                '<style>body{margin:0;padding:0;}</style>' +
                code +
                '</div></foreignObject></svg>';

            var svgBlob = new Blob([svgStr], { type: 'image/svg+xml;charset=utf-8' });
            var url = URL.createObjectURL(svgBlob);

            var img = new Image();
            img.onload = function () {
                TCTP.setProgress('tc-html-progress', 60, 'Drawing to canvas...');

                var canvas = document.createElement('canvas');
                canvas.width = outW * scale;
                canvas.height = outH * scale;
                var ctx = canvas.getContext('2d');
                ctx.scale(scale, scale);

                // White background for JPEG
                if (outputFormat === 'image/jpeg') {
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, outW, outH);
                }

                ctx.drawImage(img, 0, 0, outW, outH);
                URL.revokeObjectURL(url);

                TCTP.setProgress('tc-html-progress', 80, 'Encoding...');

                canvas.toBlob(function (blob) {
                    imageBlob = blob;
                    TCTP.setProgress('tc-html-progress', 100, 'Done!');
                    setTimeout(function () { TCTP.hideProgress('tc-html-progress'); }, 600);

                    var origStat = document.getElementById('tc-html-stat-orig');
                    if (origStat) origStat.textContent = code.length + ' chars';

                    var outStat = document.getElementById('tc-html-stat-out');
                    if (outStat) outStat.textContent = TCTP.formatSize(blob.size);

                    var dimsStat = document.getElementById('tc-html-stat-dims');
                    if (dimsStat) dimsStat.textContent = (outW * scale) + '\u00D7' + (outH * scale);

                    TCTP.updateResultPanel(
                        code.length + ' chars',
                        TCTP.formatSize(blob.size),
                        (outW * scale) + '\u00D7' + (outH * scale),
                        'Done'
                    );
                    TCTP.switchToResultTab();

                    var resultEl = document.getElementById('tc-html-result');
                    if (resultEl) {
                        resultEl.innerHTML = '';
                        var imgUrl = URL.createObjectURL(blob);
                        var resultImg = new Image();
                        resultImg.src = imgUrl;
                        resultImg.style.maxWidth = '100%';
                        resultImg.style.borderRadius = '8px';
                        resultImg.style.objectFit = 'contain';
                        resultImg.alt = 'Generated image';
                        resultEl.appendChild(resultImg);
                    }

                    if (dlBtn) {
                        dlBtn.style.display = '';
                        dlBtn.onclick = function () {
                            var a = document.createElement('a');
                            a.href = URL.createObjectURL(blob);
                            a.download = 'html-image.' + (outputFormat === 'image/jpeg' ? 'jpg' : 'png');
                            a.click();
                            URL.revokeObjectURL(a.href);
                        };
                    }

                    TCTP.toast('Image generated!', '\u2705');
                }, outputFormat, quality);
            };

            img.onerror = function () {
                TCTP.hideProgress('tc-html-progress');
                URL.revokeObjectURL(url);
                TCTP.toast('Failed to render HTML. Check your code.', '\u274C');
            };

            img.src = url;
        });
    }
})();
