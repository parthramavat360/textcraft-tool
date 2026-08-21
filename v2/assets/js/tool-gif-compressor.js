/**
 * GIF Compressor â€” Tool JS
 *
 * Client-side GIF compression with gif.js. Animated GIFs are decoded
 * frame-by-frame (gifuct-js) so all frames, delays and disposal methods
 * are preserved. Falls back to first-frame-only output with a clear
 * warning when frame decoding is unavailable.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var GIFJS_URL = 'https://cdn.jsdelivr.net/npm/gif.js@0.2.0/dist/gif.js';
    var GIFJS_WORKER_URL = 'https://cdn.jsdelivr.net/npm/gif.js@0.2.0/dist/gif.worker.js';
    var DECODER_URLS = [
        'https://cdn.jsdelivr.net/npm/gifuct-js@2.1.2/dist/gifuct-js.min.js',
        'https://cdn.jsdelivr.net/npm/gifuct-js@2.1.2/dist/gifuct-js.js'
    ];

    var file = null;
    var compressedBlob = null;
    var qualityPct = 70;

    var drop = document.getElementById('tc-gif-drop');
    if (!drop) return;

    function loadScript(src) {
        return new Promise(function (resolve, reject) {
            var s = document.createElement('script');
            s.src = src;
            s.onload = resolve;
            s.onerror = reject;
            document.head.appendChild(s);
        });
    }

    function ensureScript(src) {
        return document.querySelector('script[src="' + src + '"]')
            ? Promise.resolve()
            : loadScript(src);
    }

    function loadFirst(urls) {
        return urls.reduce(function (chain, src) {
            return chain.catch(function () { return ensureScript(src); });
        }, Promise.reject());
    }

    function readFileBuffer(blob) {
        return new Promise(function (resolve, reject) {
            if (blob.arrayBuffer) { blob.arrayBuffer().then(resolve, reject); return; }
            var r = new FileReader();
            r.onload = function () { resolve(r.result); };
            r.onerror = reject;
            r.readAsArrayBuffer(blob);
        });
    }

    // â”€â”€ Quality slider â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // gif.js "quality" is a color-sampling threshold where LOWER
    // values produce BETTER output, so the UI % is inverted here.

    var qualitySlider = document.getElementById('tc-gif-quality');
    var qualityVal = document.getElementById('tc-gif-quality-val');
    if (qualitySlider) {
        qualitySlider.addEventListener('input', function () {
            qualityPct = parseInt(qualitySlider.value, 10);
            if (qualityVal) qualityVal.textContent = qualityPct + '%';
        });
    }

    function gifQuality() {
        return Math.max(1, Math.round(20 - (qualityPct / 100) * 19));
    }

    // â”€â”€ Drop zone â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    TCTP.initDropZone('tc-gif-drop', 'tc-gif-drop-input', function (f) {
        if (!f.type.match(/image\/gif/) && !/\.gif$/i.test(f.name)) {
            TCTP.toast('Please select a GIF file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        compressedBlob = null;
        TCTP.showFileRow('tc-gif-file', f);
    }, 'image/gif,.gif');

    var removeBtn = document.querySelector('#tc-gif-file .tctp-x, #tc-gif-file .tc-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        file = null;
        compressedBlob = null;
        TCTP.hideFileRow('tc-gif-file');
    });

    // â”€â”€ GIF parsing â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    function looksAnimated(buffer) {
        var bytes = new Uint8Array(buffer);
        var count = 0;
        for (var i = 0; i < bytes.length - 1; i++) {
            if (bytes[i] === 0x21 && bytes[i + 1] === 0xF9) {
                count++;
                if (count > 1) return true;
            }
        }
        return false;
    }

    function decodeGif(buffer) {
        var lib = window.gifuct || window.gifuctJs || window.GIFuct;
        if (!lib || typeof lib.parseGIF !== 'function' || typeof lib.decompressFrames !== 'function') return null;
        try {
            var parsed = lib.parseGIF(buffer);
            var frames = lib.decompressFrames(parsed, true);
            return frames.length ? { lsd: parsed.lsd, frames: frames } : null;
        } catch (e) {
            return null;
        }
    }

    // â”€â”€ Encoding â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    function makeEncoder(width, height) {
        return new GIF({
            workers: 2,
            quality: gifQuality(),
            width: width,
            height: height,
            workerScript: GIFJS_WORKER_URL
        });
    }

    function render(gif) {
        gif.on('progress', function (p) {
            TCTP.setProgress('tc-gif-progress', Math.round(40 + p * 55), 'Encoding...');
        });
        gif.on('finished', function (blob) {
            compressedBlob = blob;
            showResult(file.size, blob.size);
        });
        gif.render();
    }

    function addDecodedFrames(gif, decoded) {
        var width = decoded.lsd.width;
        var height = decoded.lsd.height;
        var canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        var ctx = canvas.getContext('2d');
        var prevImage = null;

        decoded.frames.forEach(function (frame) {
            if (frame.disposalType === 3) {
                prevImage = ctx.getImageData(0, 0, width, height);
            }

            var patch = document.createElement('canvas');
            patch.width = frame.dims.width;
            patch.height = frame.dims.height;
            var pctx = patch.getContext('2d');
            var patchData = pctx.createImageData(frame.dims.width, frame.dims.height);
            patchData.data.set(frame.patch);
            pctx.putImageData(patchData, 0, 0);

            ctx.drawImage(patch, frame.dims.left, frame.dims.top);
            gif.addFrame(canvas, { copy: true, delay: frame.delay || 100 });

            if (frame.disposalType === 2) {
                ctx.clearRect(frame.dims.left, frame.dims.top, frame.dims.width, frame.dims.height);
            } else if (frame.disposalType === 3 && prevImage) {
                ctx.putImageData(prevImage, 0, 0);
                prevImage = null;
            }
        });
    }

    function showResult(origSize, compSize) {
        var saved = origSize > compSize ? ((1 - compSize / origSize) * 100).toFixed(1) : '0';
        var origEl = document.getElementById('tc-gif-stat-orig');
        var compEl = document.getElementById('tc-gif-stat-comp');
        var savedEl = document.getElementById('tc-gif-stat-saved');
        if (origEl) origEl.textContent = TCTP.formatSize(origSize);
        if (compEl) compEl.textContent = TCTP.formatSize(compSize);
        if (savedEl) savedEl.textContent = saved + '%';
        TCTP.updateResultPanel(TCTP.formatSize(origSize), TCTP.formatSize(compSize), saved + '%', 'Done');
                            TCTP.showResultPreview(URL.createObjectURL(compressedBlob));
        TCTP.switchToResultTab();
        TCTP.setProgress('tc-gif-progress', 100, 'Done!');
        TCTP.toast('Compressed! Saved ' + saved + '%');
    }

    // â”€â”€ Compress â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    var compressBtn = document.getElementById('tc-gif-compress');
    if (compressBtn) compressBtn.addEventListener('click', async function () {
        if (!file) { TCTP.toast('Please select a GIF file first.', '\u26A0\uFE0F'); return; }

        TCTP.showProgress('tc-gif-progress');
        TCTP.setProgress('tc-gif-progress', 5, 'Loading libraries...');

        try {
            await ensureScript(GIFJS_URL);
        } catch (e) {
            TCTP.toast('Failed to load gif.js library.', '\u274C');
            TCTP.hideProgress('tc-gif-progress');
            return;
        }

        var decoderLoaded = true;
        try {
            await loadFirst(DECODER_URLS);
        } catch (e) {
            decoderLoaded = false;
        }

        TCTP.setProgress('tc-gif-progress', 15, 'Reading GIF...');

        var buffer = await readFileBuffer(file);
        var decoded = decoderLoaded ? decodeGif(buffer) : null;
        var animated = decoded ? decoded.frames.length > 1 : looksAnimated(buffer);

        var framesCheckbox = document.getElementById('tc-gif-frames');
        var wantFrames = animated && (!framesCheckbox || framesCheckbox.checked);

        if (wantFrames && decoded) {
            TCTP.setProgress('tc-gif-progress', 30, 'Decoding ' + decoded.frames.length + ' frames...');
            var agif = makeEncoder(decoded.lsd.width, decoded.lsd.height);
            addDecodedFrames(agif, decoded);
            render(agif);
            return;
        }

        if (animated && !decoded) {
            TCTP.toast('Could not decode animation frames â€” only the first frame will be kept.', '\u26A0\uFE0F', 5000);
        }

        var url = URL.createObjectURL(file);
        var img = new Image();
        img.onload = function () {
            var w = img.naturalWidth;
            var h = img.naturalHeight;
            var canvas = document.createElement('canvas');
            canvas.width = w;
            canvas.height = h;
            canvas.getContext('2d').drawImage(img, 0, 0);
            URL.revokeObjectURL(url);

            TCTP.setProgress('tc-gif-progress', 40, 'Encoding...');
            var gif = makeEncoder(w, h);
            gif.addFrame(canvas, { copy: true, delay: 100 });
            render(gif);
        };
        img.onerror = function () {
            URL.revokeObjectURL(url);
            TCTP.hideProgress('tc-gif-progress');
            TCTP.toast('Failed to decode GIF image.', '\u274C');
        };
        img.src = url;
    });

    // â”€â”€ Download â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    var downloadBtn = document.getElementById('tc-gif-download');
    if (downloadBtn) downloadBtn.addEventListener('click', function () {
        if (!compressedBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
        var name = (file ? file.name.replace(/\.gif$/i, '') : 'animation') + '-compressed.gif';
        TCTP.downloadBlob(compressedBlob, name);
    });

})();
