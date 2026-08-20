<?php
/**
 * Widget: SVG Compressor
 *
 * @package TextCraft_Tools\Widgets
 */

declare( strict_types = 1 );

namespace TextCraft_Tools\Widgets;

defined( 'ABSPATH' ) || exit;

class Widget_Svg_Compressor extends TextCraft_Base_Widget {

	public function get_name(): string {
		return 'textcraft_svg_compressor';
	}

	public function get_title(): string {
		return esc_html__( 'SVG Compressor', 'textcraft-tools' );
	}

	public function get_icon(): string {
		return 'eicon-image';
	}

	protected function render_tool_content( array $settings ): void {
		echo '<div class="tc-svgc" data-svg-compressor>';
		echo '<div class="tc-jc-drop" role="button" tabindex="0" aria-label="' . esc_attr__( 'Click or drag SVG images to compress', 'textcraft-tools' ) . '">';
		echo '<div class="tc-jc-drop__icon">SVG</div>';
		echo '<p class="tc-jc-drop__title">' . esc_html__( 'Click to upload or drag & drop SVG images', 'textcraft-tools' ) . '</p>';
		echo '<p class="tc-jc-drop__hint">' . esc_html__( 'Compress SVG files online by optimizing vector paths and removing unnecessary metadata. Reduce SVG file size while preserving quality — all processed privately in your browser.', 'textcraft-tools' ) . '</p>';
		echo '<input type="file" class="tc-jc-upload" accept="image/svg+xml,.svg" multiple>';
		echo '</div>';

		echo '<div class="tc-jc-upload-line" hidden><span class="tc-jc-upload-text">' . esc_html__( 'Uploading images: 0%', 'textcraft-tools' ) . '</span></div>';

		echo '<div class="tc-jc-options">';
		echo '<div class="tc-jc-option">';
		echo '<label class="tc-label" for="tc-svgc-quality">' . esc_html__( 'Optimization Level', 'textcraft-tools' ) . '</label>';
		echo '<div class="tc-jc-range">';
		echo '<input type="range" id="tc-svgc-quality" class="tc-jc-quality" min="1" max="3" value="2">';
		echo '<span class="tc-jc-quality-value">' . esc_html__( 'Balanced', 'textcraft-tools' ) . '</span>';
		echo '</div></div>';
		echo '<div class="tc-jc-note">' . esc_html__( 'SVGs are saved in this browser tab so they remain after reload. Closing the tab clears the compressor and starts fresh.', 'textcraft-tools' ) . '</div>';
		echo '</div>';

		echo '<div class="tc-jc-progress" hidden>';
		echo '<div class="tc-jc-progress__row"><span class="tc-jc-progress-label">' . esc_html__( 'Compressing...', 'textcraft-tools' ) . '</span><span class="tc-jc-progress-pct">0%</span></div>';
		echo '<div class="tc-jc-progress__track"><div class="tc-jc-progress__bar"></div></div>';
		echo '</div>';

		$this->render_button_row(
			[
				[ 'id' => 'tc-svgc-compress',     'label' => esc_html__( 'Compress SVG', 'textcraft-tools' ),       'variant' => 'primary', 'disabled' => true ],
				[ 'id' => 'tc-svgc-download-all', 'label' => esc_html__( 'Download All (ZIP)', 'textcraft-tools' ), 'variant' => 'ghost' ],
				[ 'id' => 'tc-svgc-clear',        'label' => esc_html__( 'Clear All', 'textcraft-tools' ),          'variant' => 'danger' ],
			]
		);

		$this->render_stat_bar(
			[
				[ 'id' => 'tc-svgc-stat-loaded',     'label' => esc_html__( 'Files Loaded', 'textcraft-tools' ) ],
				[ 'id' => 'tc-svgc-stat-compressed', 'label' => esc_html__( 'Compressed', 'textcraft-tools' ) ],
				[ 'id' => 'tc-svgc-stat-saved',      'label' => esc_html__( 'Space Saved', 'textcraft-tools' ) ],
			]
		);

		echo '<div class="tc-jc-results" hidden>';
		echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'Image Preview', 'textcraft-tools' ) . '</span></div>';
		echo '<div class="tc-jc-grid"></div>';
		echo '</div></div>';

		$this->render_inline_script( <<<'JS'
(function () {
    var root = document.querySelector('[data-svg-compressor]');
    if (!root) return;

    var STORE_KEY = 'tc_svg_compressor_session';
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
    var btnCompress = document.getElementById('tc-svgc-compress');
    var btnDownloadAll = document.getElementById('tc-svgc-download-all');
    var btnClear = document.getElementById('tc-svgc-clear');
    var statLoaded = document.getElementById('tc-svgc-stat-loaded');
    var statCompressed = document.getElementById('tc-svgc-stat-compressed');
    var statSaved = document.getElementById('tc-svgc-stat-saved');
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

    function byteSize(text) {
        return new Blob([text], { type: 'image/svg+xml' }).size;
    }

    function escapeHtml(value) {
        return String(value).replace(/[&<>"']/g, function (char) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[char];
        });
    }

    function svgToDataUrl(svg) {
        return 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svg);
    }

    function textToSvgBlob(text) {
        return new Blob([text], { type: 'image/svg+xml;charset=utf-8' });
    }

    function fileToText(file) {
        return new Promise(function (resolve, reject) {
            var reader = new FileReader();
            reader.onload = function () { resolve(reader.result); };
            reader.onerror = reject;
            reader.readAsText(file);
        });
    }

    function outputName(name) {
        return name.replace(/\.svg$/i, '') + '-compressed.svg';
    }

    function qualityLabel(value) {
        if (String(value) === '1') return 'Light';
        if (String(value) === '3') return 'Maximum';
        return 'Balanced';
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
            console.warn('SVG compressor session cache skipped:', error);
        }
    }

    function restore() {
        try {
            var saved = JSON.parse(sessionStorage.getItem(STORE_KEY) || 'null');
            if (!saved || !Array.isArray(saved.items)) return;
            if (saved.quality) {
                quality.value = saved.quality;
                qualityValue.textContent = qualityLabel(saved.quality);
            }
            items = saved.items;
            renderResults();
            updateStats();
        } catch (error) {
            sessionStorage.removeItem(STORE_KEY);
        }
    }

    function updateStats() {
        var compressed = items.filter(function (item) { return item.compressedText; });
        var originalTotal = items.reduce(function (sum, item) { return sum + item.originalSize; }, 0);
        var compressedTotal = compressed.reduce(function (sum, item) { return sum + item.compressedSize; }, 0);
        statLoaded.textContent = String(items.length);
        statCompressed.textContent = String(compressed.length);
        statSaved.textContent = compressed.length ? formatSize(Math.max(0, originalTotal - compressedTotal)) : '-';
        btnCompress.disabled = !items.length;
        btnDownloadAll.style.display = compressed.length > 1 ? 'inline-flex' : 'none';
    }

    function isSvg(file) {
        return file.type === 'image/svg+xml' || /\.svg$/i.test(file.name);
    }

    function removeNode(node) {
        if (node && node.parentNode) node.parentNode.removeChild(node);
    }

    function compressSvgMarkup(svgText) {
        var parser = new DOMParser();
        var doc = parser.parseFromString(svgText, 'image/svg+xml');
        var parseError = doc.querySelector('parsererror');

        if (parseError || !doc.documentElement || doc.documentElement.nodeName.toLowerCase() !== 'svg') {
            return svgText
                .replace(/<!--[\s\S]*?-->/g, '')
                .replace(/>\s+</g, '><')
                .replace(/\s{2,}/g, ' ')
                .trim();
        }

        var walker = doc.createTreeWalker(doc, NodeFilter.SHOW_COMMENT | NodeFilter.SHOW_TEXT);
        var removeList = [];
        var node;

        while ((node = walker.nextNode())) {
            if (node.nodeType === 8) {
                removeList.push(node);
            } else if (node.nodeType === 3 && !node.nodeValue.trim()) {
                removeList.push(node);
            }
        }

        removeList.forEach(removeNode);

        if (parseInt(quality.value, 10) >= 2) {
            Array.from(doc.getElementsByTagName('metadata')).forEach(removeNode);
            Array.from(doc.querySelectorAll('[id^="adobe_"], [data-name="Layer 1"]')).forEach(function (nodeToClean) {
                nodeToClean.removeAttribute('data-name');
            });
        }

        var output = new XMLSerializer()
            .serializeToString(doc.documentElement)
            .replace(/>\s+</g, '><')
            .replace(/\s+=/g, '=')
            .replace(/=\s+/g, '=')
            .trim();

        if (parseInt(quality.value, 10) >= 3) {
            output = output
                .replace(/\s(id|class|data-name)=""/g, '')
                .replace(/\s{2,}/g, ' ')
                .replace(/;"/g, '"');
        }

        return output;
    }

    async function addFiles(fileList) {
        var incoming = Array.from(fileList || []).filter(isSvg);
        if (!incoming.length) return;

        for (var i = 0; i < incoming.length; i++) {
            var file = incoming[i];
            if (!items.some(function (item) { return item.name === file.name && item.originalSize === file.size && item.lastModified === file.lastModified; })) {
                var text = await fileToText(file);
                items.push({
                    name: file.name,
                    outputName: outputName(file.name),
                    originalSize: file.size,
                    lastModified: file.lastModified,
                    originalText: text,
                    compressedText: '',
                    compressedSize: 0,
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
        var compressed = compressSvgMarkup(item.originalText);
        var compressedSize = byteSize(compressed);

        if (compressedSize < item.originalSize) {
            item.compressedText = compressed;
            item.compressedSize = compressedSize;
            item.note = 'Optimized SVG markup';
        } else {
            item.compressedText = item.originalText;
            item.compressedSize = item.originalSize;
            item.note = 'Original kept - already smaller';
        }

        return item;
    }

    function renderResults() {
        grid.innerHTML = '';
        items.forEach(function (item) {
            var hasCompressed = !!item.compressedText;
            var outputText = item.compressedText || item.originalText;
            var savedPct = hasCompressed && item.originalSize > 0 ? Math.max(0, Math.round((1 - item.compressedSize / item.originalSize) * 100)) : 0;
            var card = document.createElement('div');
            card.className = 'tc-jc-card';
            card.innerHTML =
                '<div class="tc-jc-card__preview"><div><span>Original</span><img src="' + svgToDataUrl(item.originalText) + '" alt=""></div><div><span>Compressed</span><img src="' + svgToDataUrl(outputText) + '" alt=""></div></div>' +
                '<div class="tc-jc-card__body"><div class="tc-jc-card__name" title="' + escapeHtml(item.name) + '">' + escapeHtml(item.name) + '</div>' +
                '<div class="tc-jc-card__meta">' + formatSize(item.originalSize) + (hasCompressed ? ' -> ' + formatSize(item.compressedSize) + ' (' + savedPct + '% smaller) - ' + escapeHtml(item.note) : ' - ready') + '</div>' +
                (hasCompressed ? '<a class="tc-jc-card__download" href="' + svgToDataUrl(outputText) + '" download="' + escapeHtml(item.outputName) + '">Download SVG</a>' : '') + '</div>';
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
        qualityValue.textContent = qualityLabel(quality.value);
        persist();
    });

    btnCompress.addEventListener('click', function () {
        if (!items.length) return;
        btnCompress.disabled = true;
        btnCompress.textContent = 'Compressing...';
        items.forEach(function (item, index) {
            setProgress(index, items.length, 'Compressing ' + (index + 1) + ' of ' + items.length + '...');
            compressItem(item);
            setProgress(index + 1, items.length);
        });
        renderResults();
        updateStats();
        persist();
        setProgress(items.length, items.length, 'All done!');
        btnCompress.disabled = false;
        btnCompress.textContent = 'Compress SVG';
    });

    btnDownloadAll.addEventListener('click', async function () {
        var compressed = items.filter(function (item) { return item.compressedText; });
        if (!compressed.length) return;
        await ensureZipLib();
        var zip = new JSZip();
        compressed.forEach(function (item) { zip.file(item.outputName, textToSvgBlob(item.compressedText)); });
        btnDownloadAll.disabled = true;
        btnDownloadAll.textContent = 'Zipping...';
        var zipBlob = await zip.generateAsync({ type: 'blob' });
        var zipUrl = URL.createObjectURL(zipBlob);
        var link = document.createElement('a');
        link.href = zipUrl;
        link.download = 'compressed-svg-images.zip';
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
