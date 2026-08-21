/**
 * Video Converter — Tool JS
 * Uses FFmpeg.wasm for client-side video conversion.
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var drop = document.getElementById('tc-vid-drop');
    if (!drop) return;

    var ffmpegInstance = null;
    var ffmpegLoading = false;
    var ffmpegLoaded = false;
    var ffmpegFailed = false;
    var inputFile = null;
    var convertedBlob = null;

    var FORMAT_MAP = {
        mp4:  { ext: 'mp4',  mime: 'video/mp4' },
        webm: { ext: 'webm', mime: 'video/webm' },
        avi:  { ext: 'avi',  mime: 'video/x-msvideo' },
        gif:  { ext: 'gif',  mime: 'image/gif' }
    };

    function setStatus(text) {
        var el = document.getElementById('tc-vid-status');
        if (el) el.textContent = text;
    }

    function setProgress(pct, label) {
        TCTP.showProgress('tc-vid-progress');
        TCTP.setProgress('tc-vid-progress', pct, label || 'Processing...');
    }

    function hideProgress() {
        TCTP.hideProgress('tc-vid-progress');
    }

    function setDownloadEnabled(enabled) {
        var btn = document.getElementById('tc-vid-download');
        if (btn) btn.disabled = !enabled;
    }

    TCTP.initDropZone('tc-vid-drop', 'tc-vid-drop-input', function (f) {
        if (!f.type.match(/^video\//) && !/\.(mp4|webm|avi|mov|mkv|gif)$/i.test(f.name)) {
            TCTP.toast('Please select a video file.', '\u26A0\uFE0F');
            return;
        }
        inputFile = f;
        convertedBlob = null;
        setDownloadEnabled(false);
        TCTP.showFileRow('tc-vid-file', f);
        setStatus('File selected: ' + f.name + ' (' + TCTP.formatSize(f.size) + ')');
        hideProgress();
    }, 'video/*,.mp4,.webm,.avi,.mov,.mkv');

    var removeBtn = document.querySelector('#tc-vid-file .tctp-x');
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            inputFile = null;
            convertedBlob = null;
            setDownloadEnabled(false);
            TCTP.hideFileRow('tc-vid-file');
            setStatus('');
            hideProgress();
        });
    }

    async function loadFFmpeg() {
        if (ffmpegLoaded) return ffmpegInstance;
        if (ffmpegFailed) return null;
        if (ffmpegLoading) return null;

        ffmpegLoading = true;
        setStatus('Loading FFmpeg engine...');

        try {
            if (typeof FFmpeg === 'undefined' && typeof FFmpegWASM === 'undefined') {
                var script1 = document.createElement('script');
                script1.src = 'https://unpkg.com/@ffmpeg/ffmpeg@0.12.10/dist/umd/ffmpeg.js';
                document.head.appendChild(script1);
                await new Promise(function (resolve, reject) {
                    script1.onload = resolve;
                    script1.onerror = reject;
                });

                var script2 = document.createElement('script');
                script2.src = 'https://unpkg.com/@ffmpeg/util@0.12.1/dist/umd/util.js';
                document.head.appendChild(script2);
                await new Promise(function (resolve, reject) {
                    script2.onload = resolve;
                    script2.onerror = reject;
                });
            }

            var FFmpegLib = (typeof FFmpeg !== 'undefined') ? FFmpeg : FFmpegWASM;
            var createFFmpeg = FFmpegLib.createFFmpeg || FFmpegLib.createFFmpeg;
            var fetchFile = FFmpegLib.fetchFile;

            if (!createFFmpeg) {
                throw new Error('FFmpeg createFFmpeg not found');
            }

            ffmpegInstance = createFFmpeg({
                log: true,
                progress: function (p) {
                    if (p.ratio !== undefined) {
                        var pct = Math.round(p.ratio * 100);
                        setProgress(Math.min(pct, 99), 'Converting: ' + pct + '%');
                    }
                }
            });

            await ffmpegInstance.load();
            ffmpegLoaded = true;
            ffmpegLoading = false;
            setStatus('FFmpeg loaded and ready.');
            return ffmpegInstance;
        } catch (e) {
            ffmpegFailed = true;
            ffmpegLoading = false;
            setStatus('FFmpeg.wasm failed to load: ' + e.message);
            TCTP.toast('Could not load video engine. Check your connection.', '\u274C');
            return null;
        }
    }

    var convertBtn = document.getElementById('tc-vid-convert');
    if (convertBtn) {
        convertBtn.addEventListener('click', async function () {
            if (!inputFile) {
                TCTP.toast('Please select a video file first.', '\u26A0\uFE0F');
                return;
            }

            var formatSel = document.getElementById('tc-vid-format');
            var targetFormat = formatSel ? formatSel.value : 'mp4';
            var fmt = FORMAT_MAP[targetFormat] || FORMAT_MAP.mp4;

            var ffmpeg = await loadFFmpeg();
            if (!ffmpeg) {
                TCTP.toast('FFmpeg not available. Cannot convert.', '\u274C');
                return;
            }

            setProgress(10, 'Reading file...');
            setStatus('Converting to ' + targetFormat.toUpperCase() + '...');

            try {
                var fetchFile = (typeof FFmpeg !== 'undefined' ? FFmpeg : FFmpegWASM).fetchFile;
                var fileData = await fetchFile(inputFile);

                var inputName = 'input.' + (inputFile.name.split('.').pop() || 'mp4');
                var outputName = 'output.' + fmt.ext;

                ffmpeg.FS('writeFile', inputName, fileData);

                var args;
                if (targetFormat === 'gif') {
                    args = ['-i', inputName, '-vf', 'fps=10,scale=480:-1:flags=lanczos', '-loop', '0', outputName];
                } else if (targetFormat === 'webm') {
                    args = ['-i', inputName, '-c:v', 'libvpx', '-crf', '10', '-b:v', '1M', '-c:a', 'libvorbis', outputName];
                } else if (targetFormat === 'avi') {
                    args = ['-i', inputName, '-c:v', 'mpeg4', '-q:v', '5', '-c:a', 'mp3', outputName];
                } else {
                    args = ['-i', inputName, '-c:v', 'libx264', '-crf', '23', '-preset', 'fast', '-c:a', 'aac', outputName];
                }

                await ffmpeg.run.apply(ffmpeg, args);

                var outputData;
                try {
                    outputData = ffmpeg.FS('readFile', outputName);
                } catch (e) {
                    throw new Error('FFmpeg did not produce output. The format may not be supported for this input.');
                }

                convertedBlob = new Blob([outputData.buffer], { type: fmt.mime });

                try {
                    ffmpeg.FS('unlink', inputName);
                    ffmpeg.FS('unlink', outputName);
                } catch (e) { /* ignore cleanup errors */ }

                setProgress(100, 'Done!');
                setStatus(
                    'Converted: ' + TCTP.formatSize(inputFile.size) + ' -> ' +
                    TCTP.formatSize(convertedBlob.size) + ' (' + fmt.ext.toUpperCase() + ')'
                );
                setDownloadEnabled(true);
                TCTP.updateResultPanel(TCTP.formatSize(inputFile.size), TCTP.formatSize(convertedBlob.size), (inputFile.size > convertedBlob.size ? ((1 - convertedBlob.size / inputFile.size) * 100).toFixed(1) + '%' : '0%'), 'Done');
                TCTP.toast('Video converted to ' + fmt.ext.toUpperCase() + '!');
            } catch (e) {
                setStatus('Conversion failed: ' + e.message);
                hideProgress();
                TCTP.toast('Conversion failed. ' + e.message, '\u274C');
            }
        });
    }

    var downloadBtn = document.getElementById('tc-vid-download');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function () {
            if (!convertedBlob) {
                TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F');
                return;
            }
            var formatSel = document.getElementById('tc-vid-format');
            var targetFormat = formatSel ? formatSel.value : 'mp4';
            var fmt = FORMAT_MAP[targetFormat] || FORMAT_MAP.mp4;
            var baseName = inputFile ? inputFile.name.replace(/\.[^.]+$/, '') : 'video';
            TCTP.downloadBlob(convertedBlob, baseName + '.' + fmt.ext);
        });
    }

})();
