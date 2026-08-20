/**
 * PNG Compressor — Tool JS
 *
 * Client-side PNG compression using canvas + UPNG.js for lossy encoding.
 * Smart color quantization with optional resize.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var file = null;
    var compressedBlob = null;
    var level = 2;

    function loadScript(src) {
        return new Promise(function (resolve, reject) {
            if (document.querySelector('script[src="' + src + '"]')) { resolve(); return; }
            var s = document.createElement('script');
            s.src = src;
            s.onload = resolve;
            s.onerror = reject;
            document.head.appendChild(s);
        });
    }

    // ── Drop zone ────────────────────────────────────────────

    TCTP.initDropZone('tc-png-drop', 'tc-png-drop-input', function (f) {
        if (f.type !== 'image/png') {
            TCTP.toast('Please select a PNG file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        TCTP.showFileRow('tc-png-file', f);
    }, 'image/png,.png');

    var removeBtn = document.querySelector('#tc-png-file .tctp-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        file = null;
        compressedBlob = null;
        TCTP.hideFileRow('tc-png-file');
    });

    // Level buttons
    document.querySelectorAll('.tctp-modes[data-group="png-level"] .tctp-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            level = parseInt(btn.getAttribute('data-val')) || 2;
        });
    });

    // ── Compress ─────────────────────────────────────────────

    document.getElementById('tc-png-compress').addEventListener('click', async function () {
        if (!file) { TCTP.toast('Please select a PNG file first.', '\u26A0\uFE0F'); return; }

        TCTP.showProgress('tc-png-progress');
        TCTP.setProgress('tc-png-progress', 20, 'Reading image...');

        try {
            await loadScript('https://cdn.jsdelivr.net/npm/upng-js@2.1.0/UPNG.js');
        } catch (e) {
            // Fallback: skip UPNG, use canvas only
        }

        var reader = new FileReader();
        reader.onload = function (e) {
            TCTP.setProgress('tc-png-progress', 50, 'Processing...');
            var img = new Image();
            img.onload = function () {
                var canvas = document.createElement('canvas');
                var w = img.naturalWidth;
                var h = img.naturalHeight;
                var doResize = document.getElementById('tc-png-resize').checked;
                var maxDim = 1200;

                if (doResize && (w > maxDim || h > maxDim)) {
                    var ratio = Math.min(maxDim / w, maxDim / h);
                    w = Math.round(w * ratio);
                    h = Math.round(h * ratio);
                }

                canvas.width = w;
                canvas.height = h;
                var ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, w, h);

                // Quantize colors (step by level)
                var steps = { 1: 64, 2: 32, 3: 16 };
                var step = steps[level] || 32;
                var imgData = ctx.getImageData(0, 0, w, h);
                var data = imgData.data;
                for (var i = 0; i < data.length; i += 4) {
                    data[i] = Math.round(data[i] / step) * step;
                    data[i + 1] = Math.round(data[i + 1] / step) * step;
                    data[i + 2] = Math.round(data[i + 2] / step) * step;
                }
                ctx.putImageData(imgData, 0, 0);

                // Try UPNG first
                var useUpng = window.UPNG;
                if (useUpng) {
                    var rgba = ctx.getImageData(0, 0, w, h).data.buffer;
                    try {
                        var upngBuf = UPNG.encode([rgba], w, h, 64);
                        compressedBlob = new Blob([upngBuf], { type: 'image/png' });
                    } catch (e) {
                        useUpng = false;
                    }
                }

                if (!compressedBlob) {
                    canvas.toBlob(function (blob) {
                        compressedBlob = blob;
                        showResult(file.size, blob.size);
                    }, 'image/png');
                } else {
                    showResult(file.size, compressedBlob.size);
                }
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });

    function showResult(origSize, compSize) {
        var saved = origSize > compSize ? ((1 - compSize / origSize) * 100).toFixed(1) : '0';
        document.getElementById('tc-png-stat-orig').textContent = TCTP.formatSize(origSize);
        document.getElementById('tc-png-stat-comp').textContent = TCTP.formatSize(compSize);
        document.getElementById('tc-png-stat-saved').textContent = saved + '%';
        TCTP.setProgress('tc-png-progress', 100, 'Done!');
        TCTP.toast('Compressed! Saved ' + saved + '%');
    }

    // Download
    document.getElementById('tc-png-download').addEventListener('click', function () {
        if (!compressedBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
        var name = (file ? file.name.replace(/\.png$/i, '') : 'image') + '-compressed.png';
        TCTP.downloadBlob(compressedBlob, name);
    });

})();