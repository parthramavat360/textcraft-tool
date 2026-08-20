<?php
/**
 * Widget: JPG Compressor
 *
 * @package TextCraft_Tools\Widgets
 */

declare( strict_types = 1 );

namespace TextCraft_Tools\Widgets;

defined( 'ABSPATH' ) || exit;

class Widget_Jpg_Compressor extends TextCraft_Base_Widget {

	public function get_name(): string {
		return 'textcraft_jpg_compressor';
	}

	public function get_title(): string {
		return esc_html__( 'JPG Compressor', 'textcraft-tools' );
	}

	public function get_icon(): string {
		return 'eicon-image';
	}

	protected function render_tool_content( array $settings ): void {
		echo '<div class="tc-jc" data-jpg-compressor>';
		echo '<div class="tc-jc-drop" role="button" tabindex="0" aria-label="' . esc_attr__( 'Click or drag JPG images to compress', 'textcraft-tools' ) . '">';
		echo '<div class="tc-jc-drop__icon">JPG</div>';
		echo '<p class="tc-jc-drop__title">' . esc_html__( 'Click to upload or drag & drop JPG images', 'textcraft-tools' ) . '</p>';
		echo '<p class="tc-jc-drop__hint">' . esc_html__( 'Compress JPG and JPEG images online to reduce file size without losing quality. This free image compressor works in your browser — no uploads needed.', 'textcraft-tools' ) . '</p>';
		echo '<input type="file" class="tc-jc-upload" accept="image/jpeg,.jpg,.jpeg" multiple>';
		echo '</div>';

		echo '<div class="tc-jc-upload-line" hidden><span class="tc-jc-upload-text">' . esc_html__( 'Uploading images: 0%', 'textcraft-tools' ) . '</span></div>';

		echo '<div class="tc-jc-options">';
		echo '<div class="tc-jc-option">';
		echo '<label class="tc-label" for="tc-jc-quality">' . esc_html__( 'Compression Quality', 'textcraft-tools' ) . '</label>';
		echo '<div class="tc-jc-range">';
		echo '<input type="range" id="tc-jc-quality" class="tc-jc-quality" min="20" max="95" value="72">';
		echo '<span class="tc-jc-quality-value">72%</span>';
		echo '</div></div>';
		echo '<div class="tc-jc-note">' . esc_html__( 'Images are saved in this browser tab so they remain after reload. Closing the tab clears the compressor and starts fresh.', 'textcraft-tools' ) . '</div>';
		echo '</div>';

		echo '<div class="tc-jc-progress" hidden>';
		echo '<div class="tc-jc-progress__row"><span class="tc-jc-progress-label">' . esc_html__( 'Compressing...', 'textcraft-tools' ) . '</span><span class="tc-jc-progress-pct">0%</span></div>';
		echo '<div class="tc-jc-progress__track"><div class="tc-jc-progress__bar"></div></div>';
		echo '</div>';

		$this->render_button_row(
			[
				[ 'id' => 'tc-jc-compress',     'label' => esc_html__( 'Compress JPG', 'textcraft-tools' ),       'variant' => 'primary', 'disabled' => true ],
				[ 'id' => 'tc-jc-download-all', 'label' => esc_html__( 'Download All (ZIP)', 'textcraft-tools' ), 'variant' => 'ghost' ],
				[ 'id' => 'tc-jc-clear',        'label' => esc_html__( 'Clear All', 'textcraft-tools' ),          'variant' => 'danger' ],
			]
		);

		$this->render_stat_bar(
			[
				[ 'id' => 'tc-jc-stat-loaded',     'label' => esc_html__( 'Files Loaded', 'textcraft-tools' ) ],
				[ 'id' => 'tc-jc-stat-compressed', 'label' => esc_html__( 'Compressed', 'textcraft-tools' ) ],
				[ 'id' => 'tc-jc-stat-saved',      'label' => esc_html__( 'Space Saved', 'textcraft-tools' ) ],
			]
		);

		echo '<div class="tc-jc-results" hidden>';
		echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'Image Preview', 'textcraft-tools' ) . '</span></div>';
		echo '<div class="tc-jc-grid"></div>';
		echo '</div></div>';

		$this->render_inline_script( <<<'JS'
(function () {
    var root = document.querySelector('[data-jpg-compressor]');
    if (!root) return;

    var STORE_KEY = 'tc_jpg_compressor_session';
    var drop = root.querySelector('.tc-jc-drop');
    var upload = root.querySelector('.tc-jc-upload');
    var uploadLine = root.querySelector('.tc-jc-upload-line');
    var uploadText = root.querySelector('.tc-jc-upload-text');
    var quality = root.querySelector('.tc-jc-quality');
    var qualityValue = root.querySelector('.tc-jc-quality-value');
    var progress = root.querySelector('.tc-jc-progress');
    var progressBar = root.querySelector('.tc-jc-progress__bar');
    var progressLabel = root.querySelector('.tc-jc-progress-label');
    var progressPct = root.querySelector('.tc-jc-progress-pct');
    var resultsWrap = root.querySelector('.tc-jc-results');
    var grid = root.querySelector('.tc-jc-grid');
    var btnCompress = document.getElementById('tc-jc-compress');
    var btnDownloadAll = document.getElementById('tc-jc-download-all');
    var btnClear = document.getElementById('tc-jc-clear');
    var statLoaded = document.getElementById('tc-jc-stat-loaded');
    var statCompressed = document.getElementById('tc-jc-stat-compressed');
    var statSaved = document.getElementById('tc-jc-stat-saved');
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
        var mime = (parts[0].match(/:(.*?);/) || [])[1] || 'image/jpeg';
        var binary = atob(parts[1]);
        var bytes = new Uint8Array(binary.length);
        for (var i = 0; i < binary.length; i++) bytes[i] = binary.charCodeAt(i);
        return new Blob([bytes], { type: mime });
    }

    function outputName(name) {
        return name.replace(/\.(jpe?g)$/i, '') + '-compressed.jpg';
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
            sessionStorage.setItem(STORE_KEY, JSON.stringify({ quality: quality.value, items: items }));
        } catch (error) {
            console.warn('JPG compressor session cache skipped:', error);
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

    function isJpg(file) {
        return file.type === 'image/jpeg' || /\.(jpe?g)$/i.test(file.name);
    }

    async function addFiles(fileList) {
        var incoming = Array.from(fileList || []).filter(isJpg);
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
                    height: 0
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
                var canvas = document.createElement('canvas');
                canvas.width = img.naturalWidth || img.width;
                canvas.height = img.naturalHeight || img.height;
                canvas.getContext('2d').drawImage(img, 0, 0);
                var dataUrl = canvas.toDataURL('image/jpeg', Math.max(0.2, Math.min(0.95, parseInt(quality.value, 10) / 100)));
                item.compressedDataUrl = dataUrl;
                item.compressedSize = dataUrlSize(dataUrl);
                item.width = canvas.width;
                item.height = canvas.height;
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
            var savedPct = hasCompressed && item.originalSize > 0 ? Math.max(0, Math.round((1 - item.compressedSize / item.originalSize) * 100)) : 0;
            var card = document.createElement('div');
            card.className = 'tc-jc-card';
            card.innerHTML =
                '<div class="tc-jc-card__preview"><div><span>Original</span><img src="' + item.originalDataUrl + '" alt=""></div><div><span>Compressed</span><img src="' + (item.compressedDataUrl || item.originalDataUrl) + '" alt=""></div></div>' +
                '<div class="tc-jc-card__body"><div class="tc-jc-card__name" title="' + item.name.replace(/"/g, '&quot;') + '">' + item.name + '</div>' +
                '<div class="tc-jc-card__meta">' + formatSize(item.originalSize) + (hasCompressed ? ' -> ' + formatSize(item.compressedSize) + ' (' + savedPct + '% smaller)' : ' - ready') + '</div>' +
                (hasCompressed ? '<a class="tc-jc-card__download" href="' + item.compressedDataUrl + '" download="' + item.outputName.replace(/"/g, '&quot;') + '">Download JPG</a>' : '') + '</div>';
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
        btnCompress.textContent = 'Compress JPG';
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
        link.download = 'compressed-jpg-images.zip';
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
