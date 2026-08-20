/**
 * GIF Compressor — Tool JS
 *
 * Client-side GIF compression using gif.js loaded dynamically.
 * Drop zone, quality slider, compress, download.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var file = null;
    var compressedBlob = null;
    var quality = 10;

    var drop = document.getElementById('tc-gifc-drop');
    if (!drop) return;

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

    var qualitySlider = document.getElementById('tc-gifc-quality');
    var qualityVal = document.getElementById('tc-gifc-quality-val');
    if (qualitySlider) {
        qualitySlider.addEventListener('input', function () {
            quality = parseInt(qualitySlider.value);
            if (qualityVal) qualityVal.textContent = quality;
        });
    }

    TCTP.initDropZone('tc-gifc-drop', 'tc-gifc-drop-input', function (f) {
        if (!f.type.match(/image\/gif/) && !/\.gif$/i.test(f.name)) {
            TCTP.toast('Please select a GIF file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        compressedBlob = null;
        TCTP.showFileRow('tc-gifc-file', f);
    }, 'image/gif,.gif');

    var removeBtn = document.querySelector('#tc-gifc-file .tctp-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        file = null;
        compressedBlob = null;
        TCTP.hideFileRow('tc-gifc-file');
    });

    var compressBtn = document.getElementById('tc-gifc-compress');
    if (compressBtn) compressBtn.addEventListener('click', async function () {
        if (!file) { TCTP.toast('Please select a GIF file first.', '\u26A0\uFE0F'); return; }

        TCTP.showProgress('tc-gifc-progress');
        TCTP.setProgress('tc-gifc-progress', 10, 'Loading gif.js...');

        try {
            await loadScript('https://cdn.jsdelivr.net/npm/gif.js@0.2.0/dist/gif.js');
        } catch (e) {
            TCTP.toast('Failed to load gif.js library.', '\u274C');
            TCTP.hideProgress('tc-gifc-progress');
            return;
        }

        TCTP.setProgress('tc-gifc-progress', 30, 'Reading frames...');

        var img = new Image();
        img.onload = function () {
            TCTP.setProgress('tc-gifc-progress', 50, 'Compressing...');

            var canvas = document.createElement('canvas');
            canvas.width = img.naturalWidth;
            canvas.height = img.naturalHeight;
            var ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0);

            if (window.GIF) {
                var gif = new GIF({
                    workers: 2,
                    quality: quality,
                    width: img.naturalWidth,
                    height: img.naturalHeight
                });

                gif.addFrame(canvas, { copy: true, delay: 200 });

                gif.on('progress', function (p) {
                    TCTP.setProgress('tc-w2p-progress', Math.round(50 + p * 40), 'Encoding...');
                });

                gif.on('finished', function (blob) {
                    compressedBlob = blob;
                    var origSize = file.size;
                    var compSize = blob.size;
                    var saved = origSize > compSize ? ((1 - compSize / origSize) * 100).toFixed(1) : '0';

                    var origEl = document.getElementById('tc-gifc-stat-orig');
                    var compEl = document.getElementById('tc-gifc-stat-comp');
                    var savedEl = document.getElementById('tc-gifc-stat-saved');
                    if (origEl) origEl.textContent = TCTP.formatSize(origSize);
                    if (compEl) compEl.textContent = TCTP.formatSize(compSize);
                    if (savedEl) savedEl.textContent = saved + '%';

                    TCTP.setProgress('tc-gifc-progress', 100, 'Done!');
                    TCTP.toast('Compressed! Saved ' + saved + '%');
                });

                gif.render();
            } else {
                canvas.toBlob(function (blob) {
                    compressedBlob = blob;
                    var origSize = file.size;
                    var compSize = blob.size;
                    var saved = origSize > compSize ? ((1 - compSize / origSize) * 100).toFixed(1) : '0';

                    var origEl = document.getElementById('tc-gifc-stat-orig');
                    var compEl = document.getElementById('tc-gifc-stat-comp');
                    var savedEl = document.getElementById('tc-gifc-stat-saved');
                    if (origEl) origEl.textContent = TCTP.formatSize(origSize);
                    if (compEl) compEl.textContent = TCTP.formatSize(compSize);
                    if (savedEl) savedEl.textContent = saved + '%';

                    TCTP.setProgress('tc-gifc-progress', 100, 'Done!');
                    TCTP.toast('Compressed! Saved ' + saved + '%');
                }, 'image/gif', quality / 100);
            }
        };
        img.onerror = function () {
            TCTP.toast('Failed to decode GIF image.', '\u274C');
            TCTP.hideProgress('tc-gifc-progress');
        };

        var reader = new FileReader();
        reader.onload = function (e) { img.src = e.target.result; };
        reader.readAsDataURL(file);
    });

    var downloadBtn = document.getElementById('tc-gifc-download');
    if (downloadBtn) downloadBtn.addEventListener('click', function () {
        if (!compressedBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
        var name = (file ? file.name.replace(/\.gif$/i, '') : 'animation') + '-compressed.gif';
        TCTP.downloadBlob(compressedBlob, name);
    });

})();
