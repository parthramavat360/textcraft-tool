/**
 * SVG to PNG — Tool JS
 * 100% client-side SVG to PNG conversion using canvas API.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var dropEl = document.getElementById('tc-svg-drop');
    if (!dropEl) return;

    var file = null;
    var svgText = null;
    var svgW = 0, svgH = 0;
    var lockRatio = true;
    var bgColor = 'transparent';
    var pngBlob = null;

    var display = document.getElementById('tc-svg-display');
    var previewSection = document.getElementById('tc-svg-preview-section');
    var dimsSection = document.getElementById('tc-svg-dims-section');
    var modeCards = document.querySelectorAll('.tc-svg-bg-modes .tc-rsz-mode-card');
    var applyBtn = document.getElementById('tc-svg-apply');
    var dlBtn = document.getElementById('tc-svg-download');

    // ── Drop zone ──────────────────────────────────────────────

    TCTP.initDropZone('tc-svg-drop', 'tc-svg-drop-input', function (f) {
        if (f.type !== 'image/svg+xml' && !f.name.match(/\.svg$/i)) {
            TCTP.toast('Please select an SVG file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        pngBlob = null;
        if (dlBtn) dlBtn.style.display = 'none';
        TCTP.showFileRow('tc-svg-file', f);
        loadSVG(f);
    }, 'image/svg+xml,.svg');

    var removeBtn = document.querySelector('#tc-svg-file .tc-x');
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            file = null;
            svgText = null;
            pngBlob = null;
            if (dlBtn) dlBtn.style.display = 'none';
            TCTP.hideFileRow('tc-svg-file');
            var po = document.getElementById('tc-svg-preview-orig');
            if (po) po.innerHTML = '';
            var pr = document.getElementById('tc-svg-result');
            if (pr) pr.innerHTML = '';
            if (previewSection) previewSection.style.display = 'none';
            if (dimsSection) dimsSection.style.display = 'none';
        });
    }

    // ── Load SVG ───────────────────────────────────────────────

    function loadSVG(f) {
        var reader = new FileReader();
        reader.onload = function (e) {
            svgText = e.target.result;

            // Parse SVG dimensions
            var parser = new DOMParser();
            var doc = parser.parseFromString(svgText, 'image/svg+xml');
            var svgEl = doc.querySelector('svg');
            if (!svgEl) {
                TCTP.toast('Invalid SVG file.', '\u26A0\uFE0F');
                return;
            }

            var w = svgEl.getAttribute('width');
            var h = svgEl.getAttribute('height');
            var viewBox = svgEl.getAttribute('viewBox');

            if (w && h) {
                svgW = parseFloat(w) || 512;
                svgH = parseFloat(h) || 512;
            } else if (viewBox) {
                var parts = viewBox.split(/[\s,]+/);
                svgW = parseFloat(parts[2]) || 512;
                svgH = parseFloat(parts[3]) || 512;
            } else {
                svgW = 512;
                svgH = 512;
            }

            // Display SVG preview
            if (display) {
                display.innerHTML = '';
                var wrapper = document.createElement('div');
                wrapper.style.cssText = 'display:flex;align-items:center;justify-content:center;background:#0d1321;border-radius:8px;padding:12px;min-height:120px';
                var img = document.createElement('img');
                img.src = e.target.result;
                img.style.cssText = 'max-width:100%;max-height:300px;border-radius:4px';
                img.alt = 'SVG preview';
                wrapper.appendChild(img);
                display.appendChild(wrapper);
            }

            if (previewSection) previewSection.style.display = '';

            // Set output dimensions
            var wInput = document.getElementById('tc-svg-out-w');
            var hInput = document.getElementById('tc-svg-out-h');
            if (wInput) wInput.value = Math.round(svgW);
            if (hInput) hInput.value = Math.round(svgH);

            if (dimsSection) dimsSection.style.display = '';

            var origStat = document.getElementById('tc-svg-stat-orig');
            if (origStat) origStat.textContent = Math.round(svgW) + '\u00D7' + Math.round(svgH);

            TCTP.updateResultPanel(
                Math.round(svgW) + '\u00D7' + Math.round(svgH),
                '\u2014',
                '\u2014',
                'Ready'
            );

            var prevOrig = document.getElementById('tc-svg-preview-orig');
            if (prevOrig) {
                prevOrig.innerHTML = '';
                var prevImg = new Image();
                prevImg.src = e.target.result;
                prevImg.style.maxWidth = '100%';
                prevImg.style.maxHeight = '300px';
                prevImg.style.borderRadius = '8px';
                prevImg.style.objectFit = 'contain';
                prevImg.alt = 'SVG preview';
                prevOrig.appendChild(prevImg);
            }

            TCTP.toast('SVG loaded! Set dimensions and click Convert.', '\u2705');
        };
        reader.readAsDataURL(f);
    }

    // ── Dimension lock ─────────────────────────────────────────

    var lockBtn = document.getElementById('tc-svg-lock');
    var wInput = document.getElementById('tc-svg-out-w');
    var hInput = document.getElementById('tc-svg-out-h');

    if (lockBtn) {
        lockBtn.addEventListener('click', function () {
            lockRatio = !lockRatio;
            lockBtn.className = lockRatio ? 'tc-rsz-lock tc-rsz-lock--on' : 'tc-rsz-lock tc-rsz-lock--off';
        });
    }

    if (wInput) {
        wInput.addEventListener('input', function () {
            if (lockRatio && hInput) {
                var w = parseInt(wInput.value, 10);
                if (w > 0 && svgW > 0) {
                    hInput.value = Math.round(w * (svgH / svgW));
                }
            }
        });
    }

    // ── Preset buttons ─────────────────────────────────────────

    document.querySelectorAll('.tc-svg-preset-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var w = parseInt(btn.getAttribute('data-w'), 10);
            var h = parseInt(btn.getAttribute('data-h'), 10);
            if (wInput) wInput.value = w;
            if (hInput) hInput.value = h;
            lockRatio = false;
            if (lockBtn) lockBtn.className = 'tc-rsz-lock tc-rsz-lock--off';
        });
    });

    // ── Background mode ────────────────────────────────────────

    modeCards.forEach(function (card) {
        card.addEventListener('click', function () {
            modeCards.forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            bgColor = card.getAttribute('data-val') || 'transparent';
            var customSection = document.getElementById('tc-svg-custom-color-section');
            if (customSection) customSection.style.display = bgColor === 'custom' ? '' : 'none';
        });
    });

    var bgColorInput = document.getElementById('tc-svg-bg-color');
    var bgHex = document.getElementById('tc-svg-bg-hex');
    if (bgColorInput) {
        bgColorInput.addEventListener('input', function () {
            if (bgHex) bgHex.textContent = bgColorInput.value;
        });
    }

    // ── Convert ────────────────────────────────────────────────

    if (applyBtn) {
        applyBtn.addEventListener('click', function () {
            if (!svgText) {
                TCTP.toast('Please upload an SVG file first.', '\u26A0\uFE0F');
                return;
            }

            var outW = parseInt(wInput ? wInput.value : 512, 10);
            var outH = parseInt(hInput ? hInput.value : 512, 10);
            if (outW < 16 || outH < 16 || outW > 8192 || outH > 8192) {
                TCTP.toast('Dimensions must be between 16 and 8192.', '\u26A0\uFE0F');
                return;
            }

            TCTP.showProgress('tc-svg-progress', 30, 'Rendering SVG...');

            var canvas = document.createElement('canvas');
            canvas.width = outW;
            canvas.height = outH;
            var ctx = canvas.getContext('2d');

            // Draw background
            if (bgColor === 'custom') {
                ctx.fillStyle = bgColorInput ? bgColorInput.value : '#ffffff';
                ctx.fillRect(0, 0, outW, outH);
            } else if (bgColor === 'white') {
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, outW, outH);
            } else if (bgColor === 'black') {
                ctx.fillStyle = '#000000';
                ctx.fillRect(0, 0, outW, outH);
            }

            var img = new Image();
            img.onload = function () {
                TCTP.setProgress('tc-svg-progress', 70, 'Encoding PNG...');

                ctx.drawImage(img, 0, 0, outW, outH);

                canvas.toBlob(function (blob) {
                    pngBlob = blob;
                    TCTP.setProgress('tc-svg-progress', 100, 'Done!');
                    setTimeout(function () { TCTP.hideProgress('tc-svg-progress'); }, 600);

                    var outStat = document.getElementById('tc-svg-stat-out');
                    if (outStat) outStat.textContent = TCTP.formatSize(blob.size);

                    var dimsStat = document.getElementById('tc-svg-stat-dims');
                    if (dimsStat) dimsStat.textContent = outW + '\u00D7' + outH;

                    var saved = file ? (((blob.size / file.size) * 100).toFixed(0) + '% of source') : '\u2014';
                    TCTP.updateResultPanel(
                        Math.round(svgW) + '\u00D7' + Math.round(svgH),
                        outW + '\u00D7' + outH,
                        saved,
                        'Done'
                    );
                    TCTP.switchToResultTab();

                    var resultEl = document.getElementById('tc-svg-result');
                    if (resultEl) {
                        resultEl.innerHTML = '';
                        var url = URL.createObjectURL(blob);
                        var resultImg = new Image();
                        resultImg.src = url;
                        resultImg.style.maxWidth = '100%';
                        resultImg.style.borderRadius = '8px';
                        resultImg.style.objectFit = 'contain';
                        resultImg.alt = 'Converted PNG';
                        resultEl.appendChild(resultImg);
                    }

                    if (dlBtn) {
                        dlBtn.style.display = '';
                        dlBtn.onclick = function () {
                            var a = document.createElement('a');
                            a.href = URL.createObjectURL(blob);
                            a.download = 'converted.png';
                            a.click();
                            URL.revokeObjectURL(a.href);
                        };
                    }

                    TCTP.toast('SVG converted to PNG successfully!', '\u2705');
                }, 'image/png');
            };

            img.onerror = function () {
                TCTP.hideProgress('tc-svg-progress');
                TCTP.toast('Failed to render SVG. It may contain external resources.', '\u274C');
            };

            var svgBlob = new Blob([svgText], { type: 'image/svg+xml;charset=utf-8' });
            img.src = URL.createObjectURL(svgBlob);
        });
    }
})();
