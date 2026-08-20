<?php
/**
 * Widget: HEIC to JPG Converter
 *
 * @package TextCraft_Tools\Widgets
 */

declare( strict_types = 1 );

namespace TextCraft_Tools\Widgets;

defined( 'ABSPATH' ) || exit;

class Widget_Heic_To_Jpg extends TextCraft_Base_Widget {

	public function get_name(): string {
		return 'textcraft_heic_to_jpg';
	}

	public function get_title(): string {
		return esc_html__( 'HEIC to JPG Converter', 'textcraft-tools' );
	}

	public function get_icon(): string {
		return 'eicon-image';
	}

	protected function render_tool_content( array $settings ): void {
		echo '<div class="tc-h2j" data-heic-to-jpg>';

		echo '<div class="tc-h2j-drop" role="button" tabindex="0" aria-label="' . esc_attr__( 'Click or drag HEIC images to convert to JPG', 'textcraft-tools' ) . '">';
		echo '<div class="tc-h2j-drop__icon">JPG</div>';
		echo '<p class="tc-h2j-drop__title">' . esc_html__( 'Click to upload or drag & drop HEIC files', 'textcraft-tools' ) . '</p>';
		echo '<p class="tc-h2j-drop__hint">' . esc_html__( 'Convert HEIC and HEIF images to JPG format online for free. HEIC to JPG conversion works with standard JPEG compression — all processed locally in your browser.', 'textcraft-tools' ) . '</p>';
		echo '<input type="file" class="tc-h2j-upload" accept=".heic,.heif,image/heic,image/heif" multiple>';
		echo '</div>';

		echo '<div class="tc-h2j-options">';
		echo '<div class="tc-h2j-option">';
		echo '<label class="tc-label" for="tc-h2j-quality">' . esc_html__( 'JPG Quality', 'textcraft-tools' ) . '</label>';
		echo '<div class="tc-h2j-range">';
		echo '<input type="range" id="tc-h2j-quality" class="tc-h2j-quality" min="50" max="100" value="92">';
		echo '<span class="tc-h2j-quality-value">92%</span>';
		echo '</div>';
		echo '</div>';

		echo '<div class="tc-h2j-note">';
		echo esc_html__( 'HEIC support varies by browser. If native decoding is unavailable, this tool loads a client-side converter library before processing.', 'textcraft-tools' );
		echo '</div>';
		echo '</div>';

		echo '<div class="tc-h2j-progress" hidden>';
		echo '<div class="tc-h2j-progress__row">';
		echo '<span class="tc-h2j-progress-label">' . esc_html__( 'Converting...', 'textcraft-tools' ) . '</span>';
		echo '<span class="tc-h2j-progress-pct">0%</span>';
		echo '</div>';
		echo '<div class="tc-h2j-progress__track"><div class="tc-h2j-progress__bar"></div></div>';
		echo '</div>';

		$this->render_button_row(
			[
				[
					'id'       => 'tc-h2j-convert',
					'label'    => esc_html__( 'Convert to JPG', 'textcraft-tools' ),
					'variant'  => 'primary',
					'disabled' => true,
				],
				[
					'id'      => 'tc-h2j-download-all',
					'label'   => esc_html__( 'Download All (ZIP)', 'textcraft-tools' ),
					'variant' => 'ghost',
				],
				[
					'id'      => 'tc-h2j-clear',
					'label'   => esc_html__( 'Clear All', 'textcraft-tools' ),
					'variant' => 'danger',
				],
			]
		);

		$this->render_stat_bar(
			[
				[ 'id' => 'tc-h2j-stat-loaded',    'label' => esc_html__( 'Files Loaded', 'textcraft-tools' ) ],
				[ 'id' => 'tc-h2j-stat-converted', 'label' => esc_html__( 'Converted', 'textcraft-tools' ) ],
				[ 'id' => 'tc-h2j-stat-size',      'label' => esc_html__( 'Total JPG Size', 'textcraft-tools' ) ],
			]
		);

		echo '<div class="tc-h2j-results" hidden>';
		echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'Converted JPG Files', 'textcraft-tools' ) . '</span></div>';
		echo '<div class="tc-h2j-grid"></div>';
		echo '</div>';

		echo '</div>';

		$this->render_inline_script( <<<'JS'
(function () {
    var root = document.querySelector('[data-heic-to-jpg]');
    if (!root) {
        return;
    }

    var drop = root.querySelector('.tc-h2j-drop');
    var upload = root.querySelector('.tc-h2j-upload');
    var quality = root.querySelector('.tc-h2j-quality');
    var qualityValue = root.querySelector('.tc-h2j-quality-value');
    var progress = root.querySelector('.tc-h2j-progress');
    var progressBar = root.querySelector('.tc-h2j-progress__bar');
    var progressLabel = root.querySelector('.tc-h2j-progress-label');
    var progressPct = root.querySelector('.tc-h2j-progress-pct');
    var resultsWrap = root.querySelector('.tc-h2j-results');
    var grid = root.querySelector('.tc-h2j-grid');
    var btnConvert = document.getElementById('tc-h2j-convert');
    var btnDownloadAll = document.getElementById('tc-h2j-download-all');
    var btnClear = document.getElementById('tc-h2j-clear');
    var statLoaded = document.getElementById('tc-h2j-stat-loaded');
    var statConverted = document.getElementById('tc-h2j-stat-converted');
    var statSize = document.getElementById('tc-h2j-stat-size');

    var files = [];
    var results = [];
    var heicLibPromise = null;
    var zipLibPromise = null;

    btnDownloadAll.style.display = 'none';

    function loadScript(src) {
        return new Promise(function (resolve, reject) {
            var existing = document.querySelector('script[src="' + src + '"]');
            if (existing) {
                existing.addEventListener('load', resolve, { once: true });
                existing.addEventListener('error', reject, { once: true });
                if (existing.dataset.loaded === 'true') {
                    resolve();
                }
                return;
            }

            var script = document.createElement('script');
            script.src = src;
            script.async = true;
            script.onload = function () {
                script.dataset.loaded = 'true';
                resolve();
            };
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    function ensureHeicLib() {
        if (window.heic2any) {
            return Promise.resolve();
        }
        if (!heicLibPromise) {
            heicLibPromise = loadScript('https://cdn.jsdelivr.net/npm/heic2any@0.0.4/dist/heic2any.min.js');
        }
        return heicLibPromise;
    }

    function ensureZipLib() {
        if (window.JSZip) {
            return Promise.resolve();
        }
        if (!zipLibPromise) {
            zipLibPromise = loadScript('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js');
        }
        return zipLibPromise;
    }

    function isHeic(file) {
        return /\.(heic|heif)$/i.test(file.name) || /image\/hei[cf]/i.test(file.type);
    }

    function formatSize(bytes) {
        if (bytes >= 1048576) {
            return (bytes / 1048576).toFixed(1) + ' MB';
        }
        return (bytes / 1024).toFixed(1) + ' KB';
    }

    function setProgress(done, total, label) {
        var pct = total ? Math.round((done / total) * 100) : 0;
        progress.hidden = false;
        progressBar.style.width = pct + '%';
        progressPct.textContent = pct + '%';
        progressLabel.textContent = label || 'Converting ' + done + ' of ' + total + '...';
    }

    function updateStats() {
        var totalSize = results.reduce(function (sum, item) {
            return sum + item.blob.size;
        }, 0);

        statLoaded.textContent = String(files.length);
        statConverted.textContent = String(results.length);
        statSize.textContent = results.length ? formatSize(totalSize) : '-';
        btnConvert.disabled = !files.length;
        btnDownloadAll.style.display = results.length > 1 ? 'inline-flex' : 'none';
    }

    function addFiles(fileList) {
        Array.from(fileList || []).forEach(function (file) {
            if (!isHeic(file)) {
                return;
            }
            if (files.some(function (item) {
                return item.name === file.name && item.size === file.size && item.lastModified === file.lastModified;
            })) {
                return;
            }
            files.push(file);
        });

        updateStats();
    }

    function outputName(file) {
        return file.name.replace(/\.(heic|heif)$/i, '') + '.jpg';
    }

    function convertFile(file) {
        return ensureHeicLib().then(function () {
            return window.heic2any({
                blob: file,
                toType: 'image/jpeg',
                quality: Math.max(0.5, Math.min(1, parseInt(quality.value, 10) / 100))
            });
        }).then(function (converted) {
            var blob = Array.isArray(converted) ? converted[0] : converted;
            return {
                original: file,
                name: outputName(file),
                blob: blob,
                url: URL.createObjectURL(blob)
            };
        });
    }

    function renderCard(item) {
        var card = document.createElement('div');
        card.className = 'tc-h2j-card';
        card.innerHTML =
            '<img class="tc-h2j-card__image" src="' + item.url + '" alt="">' +
            '<div class="tc-h2j-card__body">' +
                '<div class="tc-h2j-card__name" title="' + item.name.replace(/"/g, '&quot;') + '">' + item.name + '</div>' +
                '<div class="tc-h2j-card__meta">' + formatSize(item.original.size) + ' -> ' + formatSize(item.blob.size) + '</div>' +
                '<a class="tc-h2j-card__download" href="' + item.url + '" download="' + item.name.replace(/"/g, '&quot;') + '">Download JPG</a>' +
            '</div>';
        grid.appendChild(card);
    }

    function renderResults() {
        grid.innerHTML = '';
        results.forEach(renderCard);
        resultsWrap.hidden = !results.length;
    }

    drop.addEventListener('click', function () {
        upload.click();
    });

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

    drop.addEventListener('dragleave', function () {
        drop.classList.remove('is-dragging');
    });

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
        qualityValue.textContent = quality.value + '%';
    });

    btnConvert.addEventListener('click', async function () {
        if (!files.length) {
            return;
        }

        btnConvert.disabled = true;
        btnConvert.textContent = 'Converting...';
        results.forEach(function (item) {
            URL.revokeObjectURL(item.url);
        });
        results = [];
        renderResults();

        for (var i = 0; i < files.length; i++) {
            setProgress(i, files.length, 'Converting ' + (i + 1) + ' of ' + files.length + '...');
            try {
                results.push(await convertFile(files[i]));
            } catch (error) {
                console.error('HEIC conversion failed:', error);
            }
            setProgress(i + 1, files.length);
            updateStats();
        }

        renderResults();
        setProgress(files.length, files.length, 'All done!');
        btnConvert.disabled = false;
        btnConvert.textContent = 'Convert to JPG';
    });

    btnDownloadAll.addEventListener('click', async function () {
        if (!results.length) {
            return;
        }

        await ensureZipLib();
        var zip = new JSZip();
        results.forEach(function (item) {
            zip.file(item.name, item.blob);
        });

        btnDownloadAll.disabled = true;
        btnDownloadAll.textContent = 'Zipping...';
        var zipBlob = await zip.generateAsync({ type: 'blob' });
        var zipUrl = URL.createObjectURL(zipBlob);
        var link = document.createElement('a');
        link.href = zipUrl;
        link.download = 'heic-to-jpg-images.zip';
        link.click();
        setTimeout(function () {
            URL.revokeObjectURL(zipUrl);
        }, 5000);
        btnDownloadAll.disabled = false;
        btnDownloadAll.textContent = 'Download All (ZIP)';
    });

    btnClear.addEventListener('click', function () {
        results.forEach(function (item) {
            URL.revokeObjectURL(item.url);
        });
        files = [];
        results = [];
        progress.hidden = true;
        progressBar.style.width = '0%';
        progressPct.textContent = '0%';
        renderResults();
        updateStats();
    });

    updateStats();
})();
JS
		);
	}
}
