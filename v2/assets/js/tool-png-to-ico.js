/**
 * PNG to ICO (Favicon Generator) — Tool JS
 * 100% client-side ICO generation using canvas API.
 * Creates multi-size ICO files from any image.
 *
 * Widget IDs (widget-png-to-ico.php):
 *  - tc-ico-drop, tc-ico-file
 *  - tc-ico-canvas, tc-ico-preview-wrap, tc-ico-preview-section
 *  - tc-ico-size-cb, tc-ico-result-sizes
 *  - tc-ico-apply, tc-ico-download
 *  - tc-ico-stat-orig, tc-ico-stat-out, tc-ico-stat-sizes
 *  - tc-ico-preview-orig, tc-ico-result
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var dropEl = document.getElementById('tc-ico-drop');
    if (!dropEl) return;

    var file = null;
    var imgEl = null;
    var naturalW = 0, naturalH = 0;
    var icoBlob = null;

    var canvas = document.getElementById('tc-ico-canvas');
    var previewWrap = document.getElementById('tc-ico-preview-wrap');
    var previewSection = document.getElementById('tc-ico-preview-section');
    var sizeCbs = document.querySelectorAll('.tc-ico-size-cb');
    var resultSizes = document.getElementById('tc-ico-result-sizes');
    var applyBtn = document.getElementById('tc-ico-apply');
    var dlBtn = document.getElementById('tc-ico-download');

    // ── Drop zone ──────────────────────────────────────────────

    TCTP.initDropZone('tc-ico-drop', 'tc-ico-drop-input', function (f) {
        if (!f.type.match(/^image\/(jpeg|png|webp|gif)$/)) {
            TCTP.toast('Please select a JPG, PNG, WebP, or GIF image.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        icoBlob = null;
        if (dlBtn) dlBtn.style.display = 'none';
        TCTP.showFileRow('tc-ico-file', f);
        loadImage(f);
    }, 'image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif');

    var removeBtn = document.querySelector('#tc-ico-file .tc-x');
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            file = null;
            imgEl = null;
            icoBlob = null;
            if (dlBtn) dlBtn.style.display = 'none';
            TCTP.hideFileRow('tc-ico-file');
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

                var size = Math.min(256, Math.max(naturalW, naturalH));
                canvas.width = size;
                canvas.height = size;
                var ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, size, size);
                var s = Math.min(size / naturalW, size / naturalH);
                var w = naturalW * s, h = naturalH * s;
                ctx.drawImage(imgEl, (size - w) / 2, (size - h) / 2, w, h);

                var origStat = document.getElementById('tc-ico-stat-orig');
                if (origStat) origStat.textContent = naturalW + '\u00D7' + naturalH;

                TCTP.updateResultPanel(
                    naturalW + '\u00D7' + naturalH,
                    '\u2014',
                    '\u2014',
                    'Ready'
                );

                var prevOrig = document.getElementById('tc-ico-preview-orig');
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

                TCTP.toast('Image loaded! Select sizes and click Generate.', '\u2705');
            };
            imgEl.src = e.target.result;
        };
        reader.readAsDataURL(f);
    }

    // ── Preset buttons ─────────────────────────────────────────

    document.querySelectorAll('.tc-ico-preset-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var action = btn.getAttribute('data-action');
            var webSizes = ['16', '32', '48', '64'];
            sizeCbs.forEach(function (cb) {
                if (action === 'all') {
                    cb.checked = true;
                } else if (action === 'none') {
                    cb.checked = false;
                } else if (action === 'web') {
                    cb.checked = webSizes.indexOf(cb.value) !== -1;
                }
            });
        });
    });

    // ── Build ICO binary ───────────────────────────────────────

    function buildICO(pngBlobs, sizes) {
        var count = pngBlobs.length;
        var headerSize = 6 + count * 16;
        var totalDataSize = 0;
        for (var i = 0; i < count; i++) {
            totalDataSize += pngBlobs[i].data.length;
        }

        var buffer = new ArrayBuffer(headerSize + totalDataSize);
        var view = new DataView(buffer);
        var offset = 0;

        // ICO header
        view.setUint16(offset, 0, true); offset += 2;     // reserved
        view.setUint16(offset, 1, true); offset += 2;     // type: 1 = ICO
        view.setUint16(offset, count, true); offset += 2; // count

        var dataOffset = headerSize;
        for (var i = 0; i < count; i++) {
            var sz = sizes[i];
            var pngData = new Uint8Array(pngBlobs[i].data);
            var width = sz >= 256 ? 0 : sz;
            var height = sz >= 256 ? 0 : sz;

            view.setUint8(offset++, width);               // width
            view.setUint8(offset++, height);              // height
            view.setUint8(offset++, 0);                   // colors (0 = PNG)
            view.setUint8(offset++, 0);                   // reserved
            view.setUint16(offset, 1, true); offset += 2; // planes
            view.setUint16(offset, 32, true); offset += 2; // bpp
            view.setUint32(offset, pngData.length, true); offset += 4; // data size
            view.setUint32(offset, dataOffset, true); offset += 4; // data offset

            var uint8 = new Uint8Array(buffer);
            uint8.set(pngData, dataOffset);
            dataOffset += pngData.length;
        }

        return new Blob([buffer], { type: 'image/x-icon' });
    }

    function canvasToPNGBlob(canvas, size) {
        return new Promise(function (resolve) {
            var tempCanvas = document.createElement('canvas');
            tempCanvas.width = size;
            tempCanvas.height = size;
            var ctx = tempCanvas.getContext('2d');
            ctx.drawImage(imgEl, 0, 0, size, size);
            tempCanvas.toBlob(function (blob) {
                resolve({ data: blob, size: size });
            }, 'image/png');
        });
    }

    // ── Generate ICO ───────────────────────────────────────────

    if (applyBtn) {
        applyBtn.addEventListener('click', function () {
            if (!imgEl) {
                TCTP.toast('Please upload an image first.', '\u26A0\uFE0F');
                return;
            }

            var selectedSizes = [];
            sizeCbs.forEach(function (cb) {
                if (cb.checked) selectedSizes.push(parseInt(cb.value, 10));
            });

            if (selectedSizes.length === 0) {
                TCTP.toast('Please select at least one size.', '\u26A0\uFE0F');
                return;
            }

            selectedSizes.sort(function (a, b) { return a - b; });

            TCTP.showProgress('tc-ico-progress', 20, 'Generating PNGs...');

            var promises = selectedSizes.map(function (sz) {
                return canvasToPNGBlob(canvas, sz);
            });

            Promise.all(promises).then(function (pngBlobs) {
                TCTP.setProgress('tc-ico-progress', 70, 'Building ICO...');

                icoBlob = buildICO(pngBlobs, selectedSizes);

                TCTP.setProgress('tc-ico-progress', 100, 'Done!');
                setTimeout(function () { TCTP.hideProgress('tc-ico-progress'); }, 600);

                var outStat = document.getElementById('tc-ico-stat-out');
                if (outStat) outStat.textContent = TCTP.formatSize(icoBlob.size);

                var sizesStat = document.getElementById('tc-ico-stat-sizes');
                if (sizesStat) sizesStat.textContent = selectedSizes.length + ' sizes';

                var saved = file ? (((icoBlob.size / file.size) * 100).toFixed(0) + '% of source') : '\u2014';
                TCTP.updateResultPanel(
                    naturalW + '\u00D7' + naturalH,
                    TCTP.formatSize(icoBlob.size),
                    saved,
                    'Done'
                );
                TCTP.switchToResultTab();

                // Show size previews
                if (resultSizes) {
                    resultSizes.innerHTML = '';
                    selectedSizes.forEach(function (sz) {
                        var previewCanvas = document.createElement('canvas');
                        previewCanvas.width = sz;
                        previewCanvas.height = sz;
                        var pCtx = previewCanvas.getContext('2d');
                        pCtx.drawImage(imgEl, 0, 0, sz, sz);

                        var item = document.createElement('div');
                        item.className = 'tc-ico-size-item';
                        item.appendChild(previewCanvas);
                        var label = document.createElement('span');
                        label.textContent = sz + '×' + sz;
                        item.appendChild(label);
                        resultSizes.appendChild(item);
                    });
                }

                // Update result panel
                var resultEl = document.getElementById('tc-ico-result');
                if (resultEl) {
                    resultEl.innerHTML = '';
                    var info = document.createElement('div');
                    info.style.cssText = 'text-align:center;padding:16px';
                    info.innerHTML = '<p style="color:var(--ink);font-weight:600;margin-bottom:8px">ICO file ready!</p>' +
                        '<p style="color:var(--muted);font-size:13px">' + selectedSizes.length + ' sizes included: ' +
                        selectedSizes.map(function(s){return s+'×'+s}).join(', ') + '</p>';
                    resultEl.appendChild(info);
                }

                if (dlBtn) {
                    dlBtn.style.display = '';
                    dlBtn.onclick = function () {
                        var a = document.createElement('a');
                        a.href = URL.createObjectURL(icoBlob);
                        a.download = 'favicon.ico';
                        a.click();
                        URL.revokeObjectURL(a.href);
                    };
                }

                TCTP.toast('ICO file generated with ' + selectedSizes.length + ' sizes!', '\u2705');
            });
        });
    }
})();
