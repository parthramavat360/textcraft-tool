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

            renderWithHtml2canvas(code, outW, outH, scale, quality, function (blob) {
                if (!blob) {
                    TCTP.hideProgress('tc-html-progress');
                    TCTP.toast('Failed to render HTML. Check your code.', '\u274C');
                    return;
                }
                imageBlob = blob;

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
                    resultEl.appendChild(showImg(imgUrl));
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

                TCTP.setProgress('tc-html-progress', 100, 'Done!');
                setTimeout(function () { TCTP.hideProgress('tc-html-progress'); }, 600);
                TCTP.toast('Image generated!', '\u2705');
            });
        });
    }

    // ── html2canvas-based renderer ──────────────────────────
    // Chromium taints canvases that draw foreignObject SVG images,
    // which made the old toBlob()-based export fail silently. html2canvas
    // renders the DOM directly to canvas (no SVG) so export is safe.

    var h2cLoaded = false;

    function showImg(url) {
        var resultImg = new Image();
        resultImg.src = url;
        resultImg.style.maxWidth = '100%';
        resultImg.style.borderRadius = '8px';
        resultImg.style.objectFit = 'contain';
        resultImg.alt = 'Generated image';
        return resultImg;
    }

    function loadHtml2canvas(cb) {
        if (window.html2canvas) { cb(); return; }
        var s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js';
        s.onload = function () { h2cLoaded = true; cb(); };
        s.onerror = function () { TCTP.toast('Failed to load the HTML renderer. Check your connection.', '\u274C'); cb(); };
        document.head.appendChild(s);
    }

    function renderWithHtml2canvas(code, outW, outH, scale, quality, cb) {
        loadHtml2canvas(function () {
            if (!window.html2canvas) { cb(null); return; }

            TCTP.setProgress('tc-html-progress', 40, 'Building preview...');

            var host = document.createElement('div');
            host.id = 'tc-html-render-host';
            host.style.cssText = 'position:absolute;left:-10000px;top:0;width:' + outW + 'px;height:' + outH + 'px;overflow:hidden;background:#ffffff;';
            host.innerHTML = '<style>html,body{margin:0;padding:0;}#root{margin:0;padding:0;}*{box-sizing:border-box;}</style>' + code;
            document.body.appendChild(host);

            setTimeout(function () {
                TCTP.setProgress('tc-html-progress', 60, 'Rendering...');
                html2canvas(host, {
                    width: outW,
                    height: outH,
                    scale: scale,
                    backgroundColor: outputFormat === 'image/jpeg' ? '#ffffff' : null,
                    useCORS: true,
                    logging: false
                }).then(function (canvas) {
                    var hostEl = document.getElementById('tc-html-render-host');
                    if (hostEl) hostEl.remove();
                    TCTP.setProgress('tc-html-progress', 80, 'Encoding...');
                    canvas.toBlob(cb, outputFormat, quality);
                }).catch(function () {
                    var hostEl = document.getElementById('tc-html-render-host');
                    if (hostEl) hostEl.remove();
                    cb(null);
                });
            }, 150);
        });
    }
})();
