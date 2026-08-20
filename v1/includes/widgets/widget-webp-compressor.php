<?php
/**
 * Widget: WebP Compressor
 *
 * @package TextCraft_Tools\Widgets
 */

declare( strict_types = 1 );

namespace TextCraft_Tools\Widgets;

defined( 'ABSPATH' ) || exit;

class Widget_Webp_Compressor extends TextCraft_Base_Widget {

	public function get_name(): string {
		return 'textcraft_webp_compressor';
	}

	public function get_title(): string {
		return esc_html__( 'WebP Compressor', 'textcraft-tools' );
	}

	public function get_icon(): string {
		return 'eicon-image';
	}

	protected function render_tool_content( array $settings ): void {
		echo '<div class="tc-wc" data-webp-compressor>';
		echo '<div class="tc-wc-drop" role="button" tabindex="0" aria-label="' . esc_attr__( 'Click or drag WebP images to compress', 'textcraft-tools' ) . '">';
		echo '<div class="tc-wc-drop__icon">WebP</div>';
		echo '<p class="tc-wc-drop__title">' . esc_html__( 'Click to upload or drag & drop WebP images', 'textcraft-tools' ) . '</p>';
		echo '<p class="tc-wc-drop__hint">' . esc_html__( 'Compress WebP images online to reduce file size while maintaining visual quality. Optimise WebP images for faster website loading — all processed privately in your browser.', 'textcraft-tools' ) . '</p>';
		echo '<input type="file" class="tc-wc-upload" accept="image/webp,.webp" multiple>';
		echo '</div>';

		echo '<div class="tc-wc-upload-line" hidden><span class="tc-wc-upload-text">' . esc_html__( 'Uploading images: 0%', 'textcraft-tools' ) . '</span></div>';

		echo '<div class="tc-wc-options">';
		echo '<div class="tc-wc-option">';
		echo '<label class="tc-label" for="tc-wc-quality">' . esc_html__( 'WebP Quality', 'textcraft-tools' ) . '</label>';
		echo '<div class="tc-wc-range"><input type="range" id="tc-wc-quality" class="tc-wc-quality" min="10" max="95" value="62"><span class="tc-wc-quality-value">62%</span></div>';
		echo '</div>';
		echo '<div class="tc-wc-option">';
		echo '<label class="tc-label" for="tc-wc-max-side">' . esc_html__( 'Max Image Side', 'textcraft-tools' ) . '</label>';
		echo '<select id="tc-wc-max-side" class="tc-wc-select">';
		echo '<option value="0">' . esc_html__( 'Keep original size', 'textcraft-tools' ) . '</option>';
		echo '<option value="1920">1920px</option>';
		echo '<option value="1600">1600px</option>';
		echo '<option value="1200" selected>1200px</option>';
		echo '<option value="900">900px</option>';
		echo '<option value="600">600px</option>';
		echo '</select>';
		echo '</div>';
		echo '<div class="tc-wc-note">' . esc_html__( 'Best compression comes from WebP quality reduction plus optional resizing. Images stay after reload in this tab and clear when the tab closes.', 'textcraft-tools' ) . '</div>';
		echo '</div>';

		echo '<div class="tc-wc-progress" hidden>';
		echo '<div class="tc-wc-progress__row"><span class="tc-wc-progress-label">' . esc_html__( 'Compressing...', 'textcraft-tools' ) . '</span><span class="tc-wc-progress-pct">0%</span></div>';
		echo '<div class="tc-wc-progress__track"><div class="tc-wc-progress__bar"></div></div>';
		echo '</div>';

		$this->render_button_row(
			[
				[ 'id' => 'tc-wc-compress',     'label' => esc_html__( 'Compress WebP', 'textcraft-tools' ),      'variant' => 'primary', 'disabled' => true ],
				[ 'id' => 'tc-wc-download-all', 'label' => esc_html__( 'Download All (ZIP)', 'textcraft-tools' ), 'variant' => 'ghost' ],
				[ 'id' => 'tc-wc-clear',        'label' => esc_html__( 'Clear All', 'textcraft-tools' ),          'variant' => 'danger' ],
			]
		);

		$this->render_stat_bar(
			[
				[ 'id' => 'tc-wc-stat-loaded',     'label' => esc_html__( 'Files Loaded', 'textcraft-tools' ) ],
				[ 'id' => 'tc-wc-stat-compressed', 'label' => esc_html__( 'Compressed', 'textcraft-tools' ) ],
				[ 'id' => 'tc-wc-stat-saved',      'label' => esc_html__( 'Space Saved', 'textcraft-tools' ) ],
			]
		);

		echo '<div class="tc-wc-results" hidden>';
		echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'Image Preview', 'textcraft-tools' ) . '</span></div>';
		echo '<div class="tc-wc-grid"></div>';
		echo '</div></div>';

		$this->render_inline_script( <<<'JS'
(function () {
    var root = document.querySelector('[data-webp-compressor]');
    if (!root) return;

    var STORE_KEY = 'tc_webp_compressor_session';
    var drop = root.querySelector('.tc-wc-drop');
    var upload = root.querySelector('.tc-wc-upload');
    var uploadLine = root.querySelector('.tc-wc-upload-line');
    var uploadText = root.querySelector('.tc-wc-upload-text');
    var quality = root.querySelector('.tc-wc-quality');
    var qualityValue = root.querySelector('.tc-wc-quality-value');
    var maxSide = root.querySelector('.tc-wc-select');
    var progress = root.querySelector('.tc-wc-progress');
    var progressBar = root.querySelector('.tc-wc-progress__bar');
    var progressLabel = root.querySelector('.tc-wc-progress-label');
    var progressPct = root.querySelector('.tc-wc-progress-pct');
    var resultsWrap = root.querySelector('.tc-wc-results');
    var grid = root.querySelector('.tc-wc-grid');
    var btnCompress = document.getElementById('tc-wc-compress');
    var btnDownloadAll = document.getElementById('tc-wc-download-all');
    var btnClear = document.getElementById('tc-wc-clear');
    var statLoaded = document.getElementById('tc-wc-stat-loaded');
    var statCompressed = document.getElementById('tc-wc-stat-compressed');
    var statSaved = document.getElementById('tc-wc-stat-saved');
    var items = [];
    var zipLibPromise = null;

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

    function formatSize(bytes) {
        return bytes >= 1048576 ? (bytes / 1048576).toFixed(1) + ' MB' : (bytes / 1024).toFixed(1) + ' KB';
    }

    function dataUrlSize(dataUrl) {
        return Math.round((dataUrl.length - dataUrl.indexOf(',') - 1) * 3 / 4);
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
        return new Blob([bytes], { type: 'image/webp' });
    }

    function outputName(name) {
        return name.replace(/\.webp$/i, '') + '-compressed.webp';
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
            console.warn('WebP compressor session cache skipped:', error);
        }
    }

    function restore() {
        try {
            var saved = JSON.parse(sessionStorage.getItem(STORE_KEY) || 'null');
            if (!saved || !Array.isArray(saved.items)) return;
            if (saved.quality) {
                quality.value = saved.quality;
                qualityValue.textContent = saved.quality + '%';
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

    function isWebp(file) {
        return file.type === 'image/webp' || /\.webp$/i.test(file.name);
    }

    async function addFiles(fileList) {
        var incoming = Array.from(fileList || []).filter(isWebp);
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

    function compressItem(item) {
        return new Promise(function (resolve, reject) {
            var img = new Image();
            img.onload = function () {
                var originalWidth = img.naturalWidth || img.width;
                var originalHeight = img.naturalHeight || img.height;
                var max = parseInt(maxSide.value, 10) || 0;
                var scale = max > 0 ? Math.min(1, max / Math.max(originalWidth, originalHeight)) : 1;
                var targetWidth = Math.max(1, Math.round(originalWidth * scale));
                var targetHeight = Math.max(1, Math.round(originalHeight * scale));
                var canvas = document.createElement('canvas');
                canvas.width = targetWidth;
                canvas.height = targetHeight;
                var ctx = canvas.getContext('2d');
                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';
                ctx.drawImage(img, 0, 0, targetWidth, targetHeight);
                var output = canvas.toDataURL('image/webp', Math.max(0.1, Math.min(0.95, parseInt(quality.value, 10) / 100)));
                var outputSize = dataUrlSize(output);
                item.compressedDataUrl = output;
                item.compressedSize = outputSize;
                item.width = targetWidth;
                item.height = targetHeight;
                item.note = targetWidth < originalWidth || targetHeight < originalHeight ? 'Resized + compressed WebP' : 'Compressed WebP';
                resolve(item);
            };
            img.onerror = reject;
            img.src = item.originalDataUrl;
        });
    }

    function renderResults() {
        grid.innerHTML = '';
        items.forEach(function (item) {
            var hasCompressed = !!item.compressedDataUrl;
            var savedPct = hasCompressed && item.originalSize > 0 ? Math.round((1 - item.compressedSize / item.originalSize) * 100) : 0;
            var card = document.createElement('div');
            card.className = 'tc-wc-card';
            card.innerHTML =
                '<div class="tc-wc-card__preview"><div><span>Original</span><img src="' + item.originalDataUrl + '" alt=""></div><div><span>Compressed</span><img src="' + (item.compressedDataUrl || item.originalDataUrl) + '" alt=""></div></div>' +
                '<div class="tc-wc-card__body"><div class="tc-wc-card__name" title="' + item.name.replace(/"/g, '&quot;') + '">' + item.name + '</div>' +
                '<div class="tc-wc-card__meta">' + formatSize(item.originalSize) + (hasCompressed ? ' -> ' + formatSize(item.compressedSize) + ' (' + savedPct + '% smaller) - ' + item.note : ' - ready') + '</div>' +
                (hasCompressed ? '<a class="tc-wc-card__download" href="' + item.compressedDataUrl + '" download="' + item.outputName.replace(/"/g, '&quot;') + '">Download WebP</a>' : '') + '</div>';
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
    quality.addEventListener('input', function () { qualityValue.textContent = quality.value + '%'; });
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
        btnCompress.textContent = 'Compress WebP';
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
        link.download = 'compressed-webp-images.zip';
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
