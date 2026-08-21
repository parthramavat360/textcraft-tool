/**
 * PNG Compressor â€” Tool JS
 *
 * Client-side LOSSLESS PNG compression: canvas re-encode at full original
 * resolution (downscale only if the user explicitly opts in), optionally
 * further optimized via UPNG.js with cnum=0 (lossless). No palette
 * quantization, no color reduction â€” output is pixel-identical.
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

    // â”€â”€ Drop zone â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    TCTP.initDropZone('tc-png-drop', 'tc-png-drop-input', function (f) {
        if (f.type !== 'image/png') {
            TCTP.toast('Please select a PNG file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        TCTP.showFileRow('tc-png-file', f);
    }, 'image/png,.png');

    var removeBtn = document.querySelector('#tc-png-file .tctp-x, #tc-png-file .tc-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        file = null;
        compressedBlob = null;
        TCTP.hideFileRow('tc-png-file');
    });

    // Level buttons: Light = fast browser encode,
    // Balanced/Strong = additional UPNG lossless optimization.
    document.querySelectorAll('[data-group="png-level"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var group = btn.closest('[data-group="png-level"]');
            if (group) group.querySelectorAll('.sel').forEach(function (b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            level = parseInt(btn.getAttribute('data-val'), 10) || 2;
        });
    });

    // â”€â”€ Compress â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    document.getElementById('tc-png-compress').addEventListener('click', async function () {
        if (!file) { TCTP.toast('Please select a PNG file first.', '\u26A0\uFE0F'); return; }

        TCTP.showProgress('tc-png-progress');
        TCTP.setProgress('tc-png-progress', 20, 'Reading image...');

        if (level > 1) {
            try {
                await loadScript('https://cdn.jsdelivr.net/npm/upng-js@2.1.0/UPNG.js');
            } catch (e) {
                // UPNG unavailable â€” plain canvas encoding is used below.
            }
        }

        var reader = new FileReader();
        reader.onload = function (e) {
            TCTP.setProgress('tc-png-progress', 50, 'Processing...');
            var img = new Image();
            img.onload = function () {
                var w = img.naturalWidth;
                var h = img.naturalHeight;

                // Downscale ONLY when the user explicitly opted in.
                var resizeBox = document.getElementById('tc-png-resize');
                if (resizeBox && resizeBox.checked) {
                    var maxDim = 1200;
                    if (w > maxDim || h > maxDim) {
                        var ratio = Math.min(maxDim / w, maxDim / h);
                        w = Math.round(w * ratio);
                        h = Math.round(h * ratio);
                    }
                }

                var canvas = document.createElement('canvas');
                canvas.width = w;
                canvas.height = h;
                var ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, w, h);

                compressedBlob = null;

                // Lossless UPNG optimization (cnum=0 = no palette quantization).
                if (level > 1 && window.UPNG) {
                    try {
                        var rgba = ctx.getImageData(0, 0, w, h).data.buffer;
                        var upngBuf = UPNG.encode([rgba], w, h, 0);
                        if (upngBuf) compressedBlob = new Blob([upngBuf], { type: 'image/png' });
                    } catch (e) {
                        compressedBlob = null;
                    }
                }

                if (compressedBlob) {
                    showResult(file.size, compressedBlob.size);
                } else {
                    canvas.toBlob(function (blob) {
                        compressedBlob = blob;
                        showResult(file.size, blob.size);
                    }, 'image/png');
                }
            };
            img.onerror = function () {
                TCTP.hideProgress('tc-png-progress');
                TCTP.toast('Failed to decode PNG image.', '\u274C');
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
        TCTP.updateResultPanel(TCTP.formatSize(origSize), TCTP.formatSize(compSize), saved + '%', 'Done');
                            TCTP.showResultPreview(URL.createObjectURL(compressedBlob));
        TCTP.switchToResultTab();
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