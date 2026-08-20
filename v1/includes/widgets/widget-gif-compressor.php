<?php
/**
 * Widget: GIF Compressor
 *
 * @package TextCraft_Tools\Widgets
 */

declare( strict_types = 1 );

namespace TextCraft_Tools\Widgets;

defined( 'ABSPATH' ) || exit;

class Widget_Gif_Compressor extends TextCraft_Base_Widget {

	public function get_name(): string {
		return 'textcraft_gif_compressor';
	}

	public function get_title(): string {
		return esc_html__( 'GIF Compressor', 'textcraft-tools' );
	}

	public function get_icon(): string {
		return 'eicon-image';
	}

	protected function render_tool_content( array $settings ): void {
		echo '<div class="tc-gc" data-gif-compressor>';
		echo '<div class="tc-gc-drop" role="button" tabindex="0" aria-label="' . esc_attr__( 'Click or drag GIF images to compress', 'textcraft-tools' ) . '">';
		echo '<div class="tc-gc-drop__icon">GIF</div>';
		echo '<p class="tc-gc-drop__title">' . esc_html__( 'Click to upload or drag & drop GIF images', 'textcraft-tools' ) . '</p>';
		echo '<p class="tc-gc-drop__hint">' . esc_html__( 'Compress and resize animated GIF images online for faster loading. Adjust encoder quality and max dimensions to reduce file size — all processed privately in your browser with no uploads.', 'textcraft-tools' ) . '</p>';
		echo '<input type="file" class="tc-gc-upload" accept="image/gif,.gif" multiple>';
		echo '</div>';

		echo '<div class="tc-gc-upload-line" hidden><span class="tc-gc-upload-text">' . esc_html__( 'Uploading images: 0%', 'textcraft-tools' ) . '</span></div>';

		echo '<div class="tc-gc-options">';
		echo '<div class="tc-gc-option">';
		echo '<label class="tc-label" for="tc-gc-quality">' . esc_html__( 'Encoder Compression', 'textcraft-tools' ) . '</label>';
		echo '<div class="tc-gc-range"><input type="range" id="tc-gc-quality" class="tc-gc-quality" min="1" max="30" value="12"><span class="tc-gc-quality-value">12</span></div>';
		echo '</div>';
		echo '<div class="tc-gc-option">';
		echo '<label class="tc-label" for="tc-gc-max-side">' . esc_html__( 'Max Image Side', 'textcraft-tools' ) . '</label>';
		echo '<select id="tc-gc-max-side" class="tc-gc-select">';
		echo '<option value="0">' . esc_html__( 'Keep original size', 'textcraft-tools' ) . '</option>';
		echo '<option value="900">900px</option>';
		echo '<option value="720" selected>720px</option>';
		echo '<option value="540">540px</option>';
		echo '<option value="360">360px</option>';
		echo '<option value="240">240px</option>';
		echo '</select>';
		echo '</div>';
		echo '<div class="tc-gc-note">' . esc_html__( 'GIFs are cached in this browser tab after upload and compression, so they remain after reload and clear when the tab closes.', 'textcraft-tools' ) . '</div>';
		echo '</div>';

		echo '<div class="tc-gc-progress" hidden>';
		echo '<div class="tc-gc-progress__row"><span class="tc-gc-progress-label">' . esc_html__( 'Compressing...', 'textcraft-tools' ) . '</span><span class="tc-gc-progress-pct">0%</span></div>';
		echo '<div class="tc-gc-progress__track"><div class="tc-gc-progress__bar"></div></div>';
		echo '</div>';

		$this->render_button_row(
			[
				[ 'id' => 'tc-gc-compress',     'label' => esc_html__( 'Compress GIF', 'textcraft-tools' ),       'variant' => 'primary', 'disabled' => true ],
				[ 'id' => 'tc-gc-download-all', 'label' => esc_html__( 'Download All (ZIP)', 'textcraft-tools' ), 'variant' => 'ghost' ],
				[ 'id' => 'tc-gc-clear',        'label' => esc_html__( 'Clear All', 'textcraft-tools' ),          'variant' => 'danger' ],
			]
		);

		$this->render_stat_bar(
			[
				[ 'id' => 'tc-gc-stat-loaded',     'label' => esc_html__( 'Files Loaded', 'textcraft-tools' ) ],
				[ 'id' => 'tc-gc-stat-compressed', 'label' => esc_html__( 'Compressed', 'textcraft-tools' ) ],
				[ 'id' => 'tc-gc-stat-saved',      'label' => esc_html__( 'Space Saved', 'textcraft-tools' ) ],
			]
		);

		echo '<div class="tc-gc-results" hidden>';
		echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'Image Preview', 'textcraft-tools' ) . '</span></div>';
		echo '<div class="tc-gc-grid"></div>';
		echo '</div></div>';

		$this->render_inline_script( <<<'JS'
(function () {
    var root = document.querySelector('[data-gif-compressor]');
    if (!root) return;

    var STORE_KEY = 'tc_gif_compressor_session';
    var drop = root.querySelector('.tc-gc-drop');
    var upload = root.querySelector('.tc-gc-upload');
    var uploadLine = root.querySelector('.tc-gc-upload-line');
    var uploadText = root.querySelector('.tc-gc-upload-text');
    var quality = root.querySelector('.tc-gc-quality');
    var qualityValue = root.querySelector('.tc-gc-quality-value');
    var maxSide = root.querySelector('.tc-gc-select');
    var progress = root.querySelector('.tc-gc-progress');
    var progressBar = root.querySelector('.tc-gc-progress__bar');
    var progressLabel = root.querySelector('.tc-gc-progress-label');
    var progressPct = root.querySelector('.tc-gc-progress-pct');
    var resultsWrap = root.querySelector('.tc-gc-results');
    var grid = root.querySelector('.tc-gc-grid');
    var btnCompress = document.getElementById('tc-gc-compress');
    var btnDownloadAll = document.getElementById('tc-gc-download-all');
    var btnClear = document.getElementById('tc-gc-clear');
    var statLoaded = document.getElementById('tc-gc-stat-loaded');
    var statCompressed = document.getElementById('tc-gc-stat-compressed');
    var statSaved = document.getElementById('tc-gc-stat-saved');
    var items = [];
    var zipLibPromise = null;
    var gifLibPromise = null;
    var gifEncoderPromise = null;
    var gifWorkerScript = 'https://cdnjs.cloudflare.com/ajax/libs/gif.js/0.2.0/gif.worker.js';

    btnDownloadAll.style.display = 'none';

    function loadScript(src) {
        return new Promise(function (resolve, reject) {
            var existing = document.querySelector('script[src="' + src + '"]');
            if (existing) {
                existing.addEventListener('load', resolve, { once: true });
                existing.addEventListener('error', reject, { once: true });
                if (existing.dataset.loaded === 'true') resolve();
                return;
            }
            var script = document.createElement('script');
            script.src = src;
            script.async = true;
            script.onload = function () { script.dataset.loaded = 'true'; resolve(); };
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    function ensureZipLib() {
        if (window.JSZip) return Promise.resolve();
        if (!zipLibPromise) zipLibPromise = loadScript('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js');
        return zipLibPromise;
    }

    function ensureGifEncoder() {
        if (window.GIF) return Promise.resolve();
        if (!gifEncoderPromise) gifEncoderPromise = loadScript('https://cdnjs.cloudflare.com/ajax/libs/gif.js/0.2.0/gif.js');
        return gifEncoderPromise;
    }

    function ensureGifLibs() {
        if (window.GIF && getGifParser()) return Promise.resolve();
        if (!gifLibPromise) {
            gifLibPromise = Promise.all([
                ensureGifEncoder(),
                getGifParser() ? Promise.resolve() : loadScript('https://cdn.jsdelivr.net/npm/gifuct-js@2.1.2/dist/gifuct.min.js')
            ]);
        }
        return gifLibPromise;
    }

    function getGifParser() {
        if (window.gifuct && window.gifuct.parseGIF && window.gifuct.decompressFrames) return window.gifuct;
        if (window.parseGIF && window.decompressFrames) {
            return { parseGIF: window.parseGIF, decompressFrames: window.decompressFrames };
        }
        return null;
    }

    function formatSize(bytes) {
        return bytes >= 1048576 ? (bytes / 1048576).toFixed(1) + ' MB' : (bytes / 1024).toFixed(1) + ' KB';
    }

    function fileToDataUrl(file) {
        return new Promise(function (resolve, reject) {
            var reader = new FileReader();
            reader.onload = function () { resolve(reader.result); };
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    function dataUrlToBlob(dataUrl) {
        var parts = dataUrl.split(',');
        var binary = atob(parts[1]);
        var bytes = new Uint8Array(binary.length);
        for (var i = 0; i < binary.length; i++) bytes[i] = binary.charCodeAt(i);
        return new Blob([bytes], { type: 'image/gif' });
    }

    function dataUrlToArrayBuffer(dataUrl) {
        var binary = atob(dataUrl.split(',')[1]);
        var bytes = new Uint8Array(binary.length);
        for (var i = 0; i < binary.length; i++) bytes[i] = binary.charCodeAt(i);
        return bytes.buffer;
    }

    function blobToDataUrl(blob) {
        return new Promise(function (resolve, reject) {
            var reader = new FileReader();
            reader.onload = function () { resolve(reader.result); };
            reader.onerror = reject;
            reader.readAsDataURL(blob);
        });
    }

    function outputName(name) {
        return name.replace(/\.gif$/i, '') + '-compressed.gif';
    }

    function fitSize(width, height) {
        var max = parseInt(maxSide.value, 10) || 0;
        var scale = max > 0 ? Math.min(1, max / Math.max(width, height)) : 1;
        return {
            width: Math.max(1, Math.round(width * scale)),
            height: Math.max(1, Math.round(height * scale))
        };
    }

    function setUploadLine(done, total) {
        var pct = total ? Math.round((done / total) * 100) : 0;
        uploadLine.hidden = false;
        uploadText.textContent = 'Uploading images: ' + pct + '% (' + done + ' of ' + total + ')';
        if (done === total) setTimeout(function () { uploadLine.hidden = true; }, 900);
    }

    function setProgress(done, total, label) {
        var pct = total ? Math.round((done / total) * 100) : 0;
        progress.hidden = false;
        progressBar.style.width = pct + '%';
        progressPct.textContent = pct + '%';
        progressLabel.textContent = label || 'Compressing ' + done + ' of ' + total + '...';
    }

    function persist() {
        try {
            sessionStorage.setItem(STORE_KEY, JSON.stringify({ quality: quality.value, maxSide: maxSide.value, items: items }));
        } catch (error) {
            console.warn('GIF compressor session cache skipped:', error);
        }
    }

    function restore() {
        try {
            var saved = JSON.parse(sessionStorage.getItem(STORE_KEY) || 'null');
            if (!saved || !Array.isArray(saved.items)) return;
            if (saved.quality) {
                quality.value = saved.quality;
                qualityValue.textContent = saved.quality;
            }
            if (saved.maxSide) maxSide.value = saved.maxSide;
            items = saved.items;
            renderResults();
            updateStats();
        } catch (error) {
            sessionStorage.removeItem(STORE_KEY);
        }
    }

    function updateStats() {
        var compressed = items.filter(function (item) { return item.compressedDataUrl; });
        var originalTotal = items.reduce(function (sum, item) { return sum + item.originalSize; }, 0);
        var compressedTotal = compressed.reduce(function (sum, item) { return sum + item.compressedSize; }, 0);
        statLoaded.textContent = String(items.length);
        statCompressed.textContent = String(compressed.length);
        statSaved.textContent = compressed.length ? formatSize(Math.max(0, originalTotal - compressedTotal)) : '-';
        btnCompress.disabled = !items.length;
        btnDownloadAll.style.display = compressed.length > 1 ? 'inline-flex' : 'none';
    }

    function isGif(file) {
        return file.type === 'image/gif' || /\.gif$/i.test(file.name);
    }

    async function addFiles(fileList) {
        var incoming = Array.from(fileList || []).filter(isGif);
        if (!incoming.length) return;

        for (var i = 0; i < incoming.length; i++) {
            var file = incoming[i];
            if (!items.some(function (item) { return item.name === file.name && item.originalSize === file.size && item.lastModified === file.lastModified; })) {
                items.push({
                    name: file.name,
                    outputName: outputName(file.name),
                    originalSize: file.size,
                    lastModified: file.lastModified,
                    originalDataUrl: await fileToDataUrl(file),
                    compressedDataUrl: '',
                    compressedSize: 0,
                    width: 0,
                    height: 0,
                    note: ''
                });
            }
            setUploadLine(i + 1, incoming.length);
            renderResults();
            updateStats();
            persist();
        }
    }

    function compressStillGif(item) {
        return new Promise(function (resolve, reject) {
            var img = new Image();
            img.onload = function () {
                var originalWidth = img.naturalWidth || img.width;
                var originalHeight = img.naturalHeight || img.height;
                var size = fitSize(originalWidth, originalHeight);
                var canvas = document.createElement('canvas');
                canvas.width = size.width;
                canvas.height = size.height;
                canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);

                ensureGifEncoder().then(function () {
                    var gif = new GIF({
                        workers: 2,
                        quality: parseInt(quality.value, 10) || 12,
                        workerScript: gifWorkerScript
                    });
                    gif.addFrame(canvas, { delay: 120, copy: true });
                    gif.on('finished', function (blob) {
                        blobToDataUrl(blob).then(function (dataUrl) {
                            item.compressedDataUrl = blob.size < item.originalSize ? dataUrl : item.originalDataUrl;
                            item.compressedSize = Math.min(blob.size, item.originalSize);
                            item.width = canvas.width;
                            item.height = canvas.height;
                            item.note = blob.size < item.originalSize ? 'Optimized GIF' : 'Original kept - already smaller';
                            resolve(item);
                        });
                    });
                    gif.render();
                }).catch(reject);
            };
            img.onerror = reject;
            img.src = item.originalDataUrl;
        });
    }

    function compressAnimatedGif(item) {
        return ensureGifLibs().then(function () {
            var parser = getGifParser();
            if (!parser || !window.GIF) throw new Error('GIF parser unavailable.');

            var parsed = parser.parseGIF(dataUrlToArrayBuffer(item.originalDataUrl));
            var frames = parser.decompressFrames(parsed, true);
            if (!frames || !frames.length) throw new Error('No GIF frames found.');

            var sourceWidth = (parsed.lsd && parsed.lsd.width) || frames[0].dims.width;
            var sourceHeight = (parsed.lsd && parsed.lsd.height) || frames[0].dims.height;
            var size = fitSize(sourceWidth, sourceHeight);
            var screen = document.createElement('canvas');
            var frameCanvas = document.createElement('canvas');
            var patchCanvas = document.createElement('canvas');
            var screenCtx = screen.getContext('2d');
            var frameCtx = frameCanvas.getContext('2d');
            var patchCtx = patchCanvas.getContext('2d');
            var gif = new GIF({
                workers: 2,
                quality: parseInt(quality.value, 10) || 12,
                workerScript: gifWorkerScript
            });
            var previousFrame = null;
            var restoreImage = null;

            screen.width = sourceWidth;
            screen.height = sourceHeight;
            frameCanvas.width = size.width;
            frameCanvas.height = size.height;

            frames.forEach(function (frame) {
                var dims = frame.dims;

                if (previousFrame && previousFrame.disposalType === 2) {
                    screenCtx.clearRect(previousFrame.dims.left, previousFrame.dims.top, previousFrame.dims.width, previousFrame.dims.height);
                } else if (previousFrame && previousFrame.disposalType === 3 && restoreImage) {
                    screenCtx.putImageData(restoreImage, 0, 0);
                    restoreImage = null;
                }

                if (frame.disposalType === 3) {
                    restoreImage = screenCtx.getImageData(0, 0, sourceWidth, sourceHeight);
                }

                patchCanvas.width = dims.width;
                patchCanvas.height = dims.height;
                patchCtx.putImageData(new ImageData(new Uint8ClampedArray(frame.patch), dims.width, dims.height), 0, 0);
                screenCtx.drawImage(patchCanvas, dims.left, dims.top);
                frameCtx.clearRect(0, 0, size.width, size.height);
                frameCtx.drawImage(screen, 0, 0, size.width, size.height);
                gif.addFrame(frameCanvas, { delay: Math.max(20, frame.delay || 100), copy: true });
                previousFrame = frame;
            });

            return new Promise(function (resolve) {
                gif.on('finished', function (blob) {
                    blobToDataUrl(blob).then(function (dataUrl) {
                        item.compressedDataUrl = blob.size < item.originalSize ? dataUrl : item.originalDataUrl;
                        item.compressedSize = Math.min(blob.size, item.originalSize);
                        item.width = size.width;
                        item.height = size.height;
                        item.note = blob.size < item.originalSize
                            ? (frames.length > 1 ? 'Animated GIF optimized' : 'Optimized GIF')
                            : 'Original kept - already smaller';
                        resolve(item);
                    });
                });
                gif.render();
            });
        });
    }

    function compressItem(item) {
        return compressAnimatedGif(item).catch(function () {
            return compressStillGif(item).catch(function () {
                item.compressedDataUrl = item.originalDataUrl;
                item.compressedSize = item.originalSize;
                item.note = 'Original kept because GIF encoding failed';
                return item;
            });
        });
    }

    function renderResults() {
        grid.innerHTML = '';
        items.forEach(function (item) {
            var hasCompressed = !!item.compressedDataUrl;
            var savedPct = hasCompressed && item.originalSize > 0 ? Math.max(0, Math.round((1 - item.compressedSize / item.originalSize) * 100)) : 0;
            var note = item.note || (hasCompressed ? 'Optimized GIF' : 'ready');
            var card = document.createElement('div');
            card.className = 'tc-gc-card';
            card.innerHTML =
                '<div class="tc-gc-card__preview"><div><span>Original</span><img src="' + item.originalDataUrl + '" alt=""></div><div><span>Compressed</span><img src="' + (item.compressedDataUrl || item.originalDataUrl) + '" alt=""></div></div>' +
                '<div class="tc-gc-card__body"><div class="tc-gc-card__name" title="' + item.name.replace(/"/g, '&quot;') + '">' + item.name + '</div>' +
                '<div class="tc-gc-card__meta">' + formatSize(item.originalSize) + (hasCompressed ? ' -> ' + formatSize(item.compressedSize) + ' (' + savedPct + '% smaller) - ' + note : ' - ready') + '</div>' +
                (hasCompressed ? '<a class="tc-gc-card__download" href="' + item.compressedDataUrl + '" download="' + item.outputName.replace(/"/g, '&quot;') + '">Download GIF</a>' : '') + '</div>';
            grid.appendChild(card);
        });
        resultsWrap.hidden = !items.length;
    }

    drop.addEventListener('click', function () { upload.click(); });
    drop.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            upload.click();
        }
    });
    drop.addEventListener('dragover', function (event) {
        event.preventDefault();
        drop.classList.add('is-dragging');
    });
    drop.addEventListener('dragleave', function () { drop.classList.remove('is-dragging'); });
    drop.addEventListener('drop', function (event) {
        event.preventDefault();
        drop.classList.remove('is-dragging');
        addFiles(event.dataTransfer.files);
    });
    upload.addEventListener('change', function () {
        addFiles(upload.files);
        upload.value = '';
    });
    quality.addEventListener('input', function () {
        qualityValue.textContent = quality.value;
        persist();
    });
    maxSide.addEventListener('change', persist);

    btnCompress.addEventListener('click', async function () {
        if (!items.length) return;
        btnCompress.disabled = true;
        btnCompress.textContent = 'Compressing...';
        for (var i = 0; i < items.length; i++) {
            setProgress(i, items.length, 'Compressing ' + (i + 1) + ' of ' + items.length + '...');
            await compressItem(items[i]);
            setProgress(i + 1, items.length);
            renderResults();
            updateStats();
            persist();
        }
        setProgress(items.length, items.length, 'All done!');
        btnCompress.disabled = false;
        btnCompress.textContent = 'Compress GIF';
    });

    btnDownloadAll.addEventListener('click', async function () {
        var compressed = items.filter(function (item) { return item.compressedDataUrl; });
        if (!compressed.length) return;
        await ensureZipLib();
        var zip = new JSZip();
        compressed.forEach(function (item) { zip.file(item.outputName, dataUrlToBlob(item.compressedDataUrl)); });
        btnDownloadAll.disabled = true;
        btnDownloadAll.textContent = 'Zipping...';
        var zipBlob = await zip.generateAsync({ type: 'blob' });
        var zipUrl = URL.createObjectURL(zipBlob);
        var link = document.createElement('a');
        link.href = zipUrl;
        link.download = 'compressed-gif-images.zip';
        link.click();
        setTimeout(function () { URL.revokeObjectURL(zipUrl); }, 5000);
        btnDownloadAll.disabled = false;
        btnDownloadAll.textContent = 'Download All (ZIP)';
    });

    btnClear.addEventListener('click', function () {
        items = [];
        sessionStorage.removeItem(STORE_KEY);
        progress.hidden = true;
        progressBar.style.width = '0%';
        progressPct.textContent = '0%';
        uploadLine.hidden = true;
        renderResults();
        updateStats();
    });

    restore();
    updateStats();
})();
JS
		);
	}
}
