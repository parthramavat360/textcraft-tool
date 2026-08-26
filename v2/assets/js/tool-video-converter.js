/**
 * Video Converter - Tool JS
 *
 * FFmpeg.wasm 0.12.x powered client-side video conversion.
 * Supports MP4, WebM, AVI, MOV, GIF, MP3, WAV output.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var PREFIX = 'tc-vid-';
    var ffmpegReady = false;
    var ffmpegInstance = null;
    var selectedFile = null;
    var resultBlob = null;

    var drop = document.getElementById(PREFIX + 'drop');
    if (!drop) return;

    function initModeCards(sel) {
        document.querySelectorAll(sel).forEach(function (card) {
            card.addEventListener('click', function () {
                document.querySelectorAll(sel).forEach(function (c) { c.classList.remove('sel'); });
                card.classList.add('sel');
                toggleAudioOptions();
            });
        });
    }
    initModeCards('.tc-vid-formats .tc-rsz-mode-card');
    initModeCards('.tc-vid-resolution .tc-rsz-mode-card');

    function getSelectedFormat() {
        var s = document.querySelector('.tc-vid-formats .tc-rsz-mode-card.sel');
        return s ? s.getAttribute('data-val') : 'mp4';
    }
    function getSelectedResolution() {
        var s = document.querySelector('.tc-vid-resolution .tc-rsz-mode-card.sel');
        return s ? s.getAttribute('data-val') : 'original';
    }

    function toggleAudioOptions() {
        var isAudio = getSelectedFormat() === 'mp3' || getSelectedFormat() === 'wav';
        var r = document.getElementById('tc-vid-resolution-section');
        if (r) r.style.display = isAudio ? 'none' : '';
    }
    toggleAudioOptions();

    var qs = document.getElementById('tc-vid-quality');
    var qv = document.getElementById('tc-vid-quality-val');
    if (qs && qv) qs.addEventListener('input', function () { qv.textContent = qs.value + ' CRF'; });

    var fs = document.getElementById('tc-vid-fps');
    var fv = document.getElementById('tc-vid-fps-val');
    if (fs && fv) fs.addEventListener('input', function () { fv.textContent = fs.value; });

    var inputEl = document.getElementById(PREFIX + 'drop-input');
    if (!inputEl) return;

    function handleFile(file) {
        if (!file || !file.type.startsWith('video/')) {
            TCTP.toast('Please select a video file.', 'Warning');
            return;
        }
        selectedFile = file;
        resultBlob = null;
        var dlBtn = document.getElementById(PREFIX + 'download');
        if (dlBtn) dlBtn.style.display = 'none';

        var row = document.getElementById(PREFIX + 'file');
        if (row) {
            var ne = row.querySelector('.tc-file-name');
            var se = row.querySelector('.tc-file-size');
            if (ne) ne.textContent = file.name;
            if (se) se.textContent = TCTP.formatSize(file.size);
            row.style.display = '';
            row.classList.add('visible');
        }

        var rb = document.querySelector('#' + PREFIX + 'file .tc-x');
        if (rb) rb.onclick = function () {
            selectedFile = null;
            row.style.display = 'none';
            row.classList.remove('visible');
            var o = document.getElementById('tc-preview-orig');
            if (o) o.innerHTML = '<p style="color:var(--muted);font-size:14px;">Original video will appear here.</p>';
            TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Ready');
        };

        showOriginalPreview(file);
    }

    drop.addEventListener('click', function () { inputEl.click(); });
    drop.addEventListener('dragover', function (e) { e.preventDefault(); drop.classList.add('hot'); });
    drop.addEventListener('dragleave', function () { drop.classList.remove('hot'); });
    drop.addEventListener('drop', function (e) {
        e.preventDefault(); drop.classList.remove('hot');
        if (e.dataTransfer.files.length) handleFile(e.dataTransfer.files[0]);
    });
    inputEl.addEventListener('change', function () {
        if (inputEl.files.length) handleFile(inputEl.files[0]);
        inputEl.value = '';
    });

    function showOriginalPreview(file) {
        var o = document.getElementById('tc-preview-orig');
        if (!o) return;
        var u = URL.createObjectURL(file);
        o.innerHTML = '<video controls preload="metadata" style="width:100%;border-radius:8px;max-height:400px;"><source src="' + u + '" type="' + file.type + '"></video>';
        TCTP.switchToOriginalTab();
    }

    function loadScript(src) {
        return new Promise(function (resolve, reject) {
            var existing = document.querySelector('script[src="' + src + '"]');
            if (existing) { resolve(); return; }
            var s = document.createElement('script');
            s.src = src;
            s.onload = resolve;
            s.onerror = function () { reject(new Error('Failed to load: ' + src)); };
            document.head.appendChild(s);
        });
    }

    var WASM_BASE = (typeof tctpWasm !== 'undefined' && tctpWasm.url) ? tctpWasm.url : (window.location.origin + '/wp-content/plugins/textcrafttoolspro/assets/wasm');

    function withTimeout(promise, ms, label) {
        return Promise.race([
            promise,
            new Promise(function (_, reject) {
                setTimeout(function () { reject(new Error((label || 'Operation') + ' timed out after ' + (ms / 1000) + 's')); }, ms);
            })
        ]);
    }

    async function ensureFFmpeg() {
        if (ffmpegReady && ffmpegInstance) return ffmpegInstance;

        console.log('[vid] Starting ensureFFmpeg');
        TCTP.setResultStatus('Loading FFmpeg engine...');

        await loadScript(WASM_BASE + '/ffmpeg.js');
        console.log('[vid] ffmpeg.js loaded, FFmpegWASM =', !!window.FFmpegWASM);

        var FFmpegWASM = window.FFmpegWASM;
        if (!FFmpegWASM) throw new Error('FFmpeg library failed to load.');

        var FFmpegClass = FFmpegWASM.FFmpeg || FFmpegWASM;
        var ff = new FFmpegClass();
        console.log('[vid] FFmpeg instance created');

        ff.on('log', function (e) {
            console.log('[ffmpeg]', e.message);
        });

        ff.on('progress', function (e) {
            if (typeof e.progress === 'number' && e.progress >= 0) {
                var pct = Math.round(e.progress * 100);
                TCTP.setProgress(PREFIX + 'progress', Math.min(pct, 99), 'Converting... ' + pct + '%');
            }
        });

        TCTP.setProgress(PREFIX + 'progress', 5, 'Loading WASM core (30MB, one-time)...');

        var OrigWorker = window.Worker;
        window.Worker = function (url, opts) {
            var workerPath = WASM_BASE + '/814.ffmpeg.js';
            console.log('[vid] Worker intercepted, redirecting to', workerPath);
            var fixedUrl = new URL(workerPath, window.location.href);
            var w = new OrigWorker(fixedUrl, opts);
            w.onerror = function (e) {
                console.error('[vid] Worker ERROR:', e.message || e.type, e.filename || '');
            };
            return w;
        };
        window.Worker.prototype = OrigWorker.prototype;

        try {
            console.log('[vid] Calling ff.load...');
            await withTimeout(ff.load({
                coreURL: WASM_BASE + '/ffmpeg-core.js',
                wasmURL: WASM_BASE + '/ffmpeg-core.wasm'
            }), 60000, 'WASM load');
            console.log('[vid] ff.load completed');
        } finally {
            window.Worker = OrigWorker;
        }

        ffmpegReady = true;
        ffmpegInstance = ff;
        return ff;
    }

    function buildArgs(inputName, fmt, resolution, crf, fps, mute, trimStart, trimEnd, memSave) {
        var args = [];

        if (trimStart > 0) args.push('-ss', String(trimStart));
        args.push('-i', inputName);
        if (trimEnd > 0) args.push('-to', String(trimEnd));

        if (mute || fmt === 'mp3' || fmt === 'wav') {
            args.push('-an');
        }

        if (fmt === 'gif') {
            var gFps = Math.min(fps || 10, 15);
            var gScale = resolution === 'original' ? '480' : Math.min(parseInt(resolution) || 480, 480);
            args.push('-vf', 'fps=' + gFps + ',scale=' + gScale + ':-2:flags=lanczos,split[s0][s1];[s0]palettegen[p];[s1][p]paletteuse');
        } else if (fmt !== 'mp3' && fmt !== 'wav') {
            var vfilters = [];
            if (resolution !== 'original') {
                vfilters.push('scale=' + resolution + ':-2');
            }
            if (fps > 0) {
                vfilters.push('fps=' + fps);
            }
            if (vfilters.length > 0) {
                args.push('-vf', vfilters.join(','));
            }
        }

        switch (fmt) {
            case 'mp4':
                args.push('-c:v', 'libx264', '-crf', String(crf), '-preset', 'ultrafast', '-pix_fmt', 'yuv420p', '-movflags', '+faststart');
                if (memSave >= 2) args.push('-refs', '1', '-g', '30');
                else if (memSave >= 1) args.push('-refs', '2');
                args.push('-c:a', 'aac', '-b:a', '128k');
                break;
            case 'webm':
                args.push('-c:v', 'libvpx-vp9', '-crf', String(Math.min(crf, 31)), '-b:v', '0', '-deadline', 'realtime', '-cpu-used', '4');
                if (memSave >= 1) args.push('-tile-columns', '0', '-frame-threads', '1');
                args.push('-c:a', 'libopus', '-b:a', '128k');
                break;
            case 'avi':
                args.push('-c:v', 'mpeg4', '-q:v', String(Math.max(1, Math.round(crf / 4))));
                break;
            case 'mov':
                args.push('-c:v', 'libx264', '-crf', String(crf), '-preset', 'ultrafast', '-pix_fmt', 'yuv420p');
                if (memSave >= 2) args.push('-refs', '1');
                break;
            case 'mp3':
                args.push('-codec:a', 'libmp3lame', '-q:a', '2');
                break;
            case 'wav':
                args.push('-codec:a', 'pcm_s16le');
                break;
        }

        var ext = fmt === 'gif' ? 'gif' : fmt;
        args.push('output.' + ext);
        return args;
    }

    var convertBtn = document.getElementById(PREFIX + 'convert');
    if (convertBtn) convertBtn.addEventListener('click', async function () {
        if (!selectedFile) {
            TCTP.toast('Please drop a video file first.', 'Warning');
            return;
        }

        TCTP.showProgress(PREFIX + 'progress');
        TCTP.setProgress(PREFIX + 'progress', 3, 'Loading FFmpeg engine...');
        convertBtn.disabled = true;
        convertBtn.textContent = 'Converting...';

        try {
            var ff = await ensureFFmpeg();
            TCTP.setProgress(PREFIX + 'progress', 50, 'Reading file...');

            var inputExt = selectedFile.name.split('.').pop() || 'mp4';
            var inputName = 'input.' + inputExt;
            var inputData = new Uint8Array(await selectedFile.arrayBuffer());
            console.log('[vid] Writing file:', inputName, 'size:', inputData.length);
            await ff.writeFile(inputName, inputData);
            console.log('[vid] File written OK');

            var fmt = getSelectedFormat();
            var resolution = getSelectedResolution();
            var crf = qs ? parseInt(qs.value, 10) : 23;
            var fpsVal = fs ? parseInt(fs.value, 10) : 30;
            var mute = document.getElementById('tc-vid-mute') ? document.getElementById('tc-vid-mute').checked : false;
            var trimStart = parseFloat(document.getElementById('tc-vid-trim-start').value) || 0;
            var trimEnd = parseFloat(document.getElementById('tc-vid-trim-end').value) || 0;
            var memSave = 0;
            var fileSize = selectedFile.size;

            if (fileSize > 5 * 1024 * 1024 && resolution === 'original') {
                resolution = '720';
            }
            if (fileSize > 15 * 1024 * 1024) {
                if (resolution === 'original' || parseInt(resolution) > 480) resolution = '480';
                if (fpsVal > 24) fpsVal = 24;
                if (crf < 26) crf = 26;
                memSave = 1;
            }
            if (fileSize > 30 * 1024 * 1024) {
                if (parseInt(resolution) > 360) resolution = '360';
                if (fpsVal > 24) fpsVal = 24;
                if (crf < 28) crf = 28;
                memSave = 2;
            }
            if (fileSize > 60 * 1024 * 1024) {
                if (parseInt(resolution) > 240) resolution = '240';
                if (fpsVal > 15) fpsVal = 15;
                if (crf < 30) crf = 30;
                memSave = 2;
            }

            var args = buildArgs(inputName, fmt, resolution, crf, fpsVal, mute, trimStart, trimEnd, memSave);
            console.log('[vid] ffmpeg args:', JSON.stringify(args));

            TCTP.setProgress(PREFIX + 'progress', 55, 'Converting...');
            console.log('[vid] Calling ff.exec...');
            await withTimeout(ff.exec(args), 120000, 'Conversion');
            console.log('[vid] ff.exec completed');

            var outputName = 'output.' + (fmt === 'gif' ? 'gif' : fmt);
            var outputData = await ff.readFile(outputName);

            var mimeMap = {
                'mp4': 'video/mp4', 'webm': 'video/webm', 'avi': 'video/x-msvideo',
                'mov': 'video/quicktime', 'gif': 'image/gif', 'mp3': 'audio/mpeg', 'wav': 'audio/wav'
            };
            resultBlob = new Blob([outputData], { type: mimeMap[fmt] || 'application/octet-stream' });

            try { await ff.deleteFile(inputName); } catch (e) {}
            try { await ff.deleteFile(outputName); } catch (e) {}

            TCTP.setProgress(PREFIX + 'progress', 100, 'Done!');

            var origSize = selectedFile.size;
            var convSize = resultBlob.size;
            var saved = origSize > convSize ? ((1 - convSize / origSize) * 100).toFixed(1) : '0';

            var oe = document.getElementById(PREFIX + 'stat-orig');
            var ce = document.getElementById(PREFIX + 'stat-conv');
            var se = document.getElementById(PREFIX + 'stat-saved');
            if (oe) oe.textContent = TCTP.formatSize(origSize);
            if (ce) ce.textContent = TCTP.formatSize(convSize);
            if (se) se.textContent = (origSize > convSize ? '+' : '-') + saved + '%';

            TCTP.updateResultPanel(TCTP.formatSize(origSize), TCTP.formatSize(convSize), saved + '%', 'Done');
            TCTP.toast('Video converted successfully!');

            var dlBtn = document.getElementById(PREFIX + 'download');
            if (dlBtn) dlBtn.style.display = '';

            var resultUrl = URL.createObjectURL(resultBlob);
            var resultEl = document.getElementById('tc-preview-result');
            if (resultEl) {
                if (fmt === 'gif') {
                    resultEl.innerHTML = '<img src="' + resultUrl + '" alt="Converted GIF" style="width:100%;border-radius:8px;max-height:400px;object-fit:contain;">';
                } else {
                    resultEl.innerHTML = '<video controls preload="metadata" style="width:100%;border-radius:8px;max-height:400px;"><source src="' + resultUrl + '" type="' + (mimeMap[fmt] || '') + '"></video>';
                }
            }
            TCTP.switchToResultTab();

        } catch (err) {
            console.error('[Video Converter]', err);
            var msg = err.message || err.toString() || 'Unknown error';
            if (msg.indexOf('memory') !== -1 || msg.indexOf('out of bounds') !== -1) {
                msg = 'Ran out of browser memory. Reload the page and try a smaller file, or try converting a shorter segment using trim.';
            } else if (msg.indexOf('timed out') !== -1) {
                msg = 'Conversion took too long. Try a smaller file.';
            }
            TCTP.toast('Conversion failed: ' + msg, 'Error');
            TCTP.hideProgress(PREFIX + 'progress');
            try { if (typeof inputName !== 'undefined') await ff.deleteFile(inputName); } catch (e) {}
            try { if (typeof fmt !== 'undefined') await ff.deleteFile('output.' + fmt); } catch (e) {}
            ffmpegReady = false;
            ffmpegInstance = null;
        }

        convertBtn.disabled = false;
        convertBtn.textContent = 'Convert Video';
    });

    var downloadBtn = document.getElementById(PREFIX + 'download');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function () {
            if (!resultBlob || !selectedFile) {
                TCTP.toast('Nothing to download yet.', 'Warning');
                return;
            }
            var fmt = getSelectedFormat();
            var name = selectedFile.name.replace(/\.[^.]+$/, '') + '.' + (fmt === 'gif' ? 'gif' : fmt);
            TCTP.downloadBlob(resultBlob, name);
        });
    }

})();
