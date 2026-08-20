<?php
/**
 * Widget: PNG to SVG Converter
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );

namespace TextCraft_Tools\Widgets;

defined( 'ABSPATH' ) || exit;

class Widget_Png_To_Svg extends TextCraft_Base_Widget {
	public function get_name(): string  { return 'textcraft_png_to_svg'; }
	public function get_title(): string { return esc_html__( 'PNG to SVG Converter', 'textcraft-tools' ); }
	public function get_icon(): string  { return 'eicon-image-box'; }

	protected function render_tool_content( array $settings ): void {

		// Drop zone.
		echo '<div id="tc-p2s-drop" role="button" tabindex="0" aria-label="' . esc_attr__( 'Upload PNG images — click to browse or drag and drop to convert to SVG online', 'textcraft-tools' ) . '" '
			. 'class="tc-drop-zone">';
		echo '<div class="tc-drop-icon">SVG</div>';
		echo '<p class="tc-drop-title">' . esc_html__( 'Upload your PNG images — click to browse or drag and drop', 'textcraft-tools' ) . '</p>';
		echo '<p class="tc-drop-desc">' . esc_html__( 'Convert PNG to SVG online for free. Up to 20 PNG images become base64-embedded scalable graphics — all processed in your browser.', 'textcraft-tools' ) . '</p>';
		echo '<p class="tc-drop-desc-sm">' . esc_html__( 'SVG output embeds your PNG as a base64 image, preserving dimensions and transparency. Your files stay private — no uploads to any server.', 'textcraft-tools' ) . '</p>';
		echo '<input type="file" id="tc-p2s-upload" accept="image/png" multiple class="tc-d-none">';
		echo '</div>';

		// One-line upload progress.
		echo '<div id="tc-p2s-upload-line" class="tc-upload-line">';
		echo '<span id="tc-p2s-upload-text">' . esc_html__( 'Loading images: 0%', 'textcraft-tools' ) . '</span>';
		echo '</div>';

		// Conversion progress bar.
		echo '<div id="tc-p2s-conv-progress" class="tc-progress-wrap">';
		echo '<div class="tc-d-flex tc-justify-between tc-mb-6">';
		echo '<span id="tc-p2s-conv-label" class="tc-progress-label">' . esc_html__( 'Converting...', 'textcraft-tools' ) . '</span>';
		echo '<span id="tc-p2s-conv-pct" class="tc-progress-pct">0%</span>';
		echo '</div>';
		echo '<div class="tc-progress-bg">';
		echo '<div id="tc-p2s-conv-bar" class="tc-progress-fill tc-progress-fill--pink"></div>';
		echo '</div>';
		echo '</div>';

		// Previews.
		echo '<div id="tc-p2s-previews" class="tc-preview-wrap">';
		echo '<div class="tc-label-row tc-mb-8"><span class="tc-label">' . esc_html__( 'Loaded PNG Images', 'textcraft-tools' ) . '</span></div>';
		echo '<div id="tc-p2s-preview-grid" class="tc-grid-preview-sm"></div>';
		echo '</div>';

		// Options.
		echo '<div class="tc-settings-grid">';

		echo '<div>';
		echo '<label class="tc-label tc-d-block tc-mb-8" for="tc-p2s-responsive">' . esc_html__( 'SVG Output', 'textcraft-tools' ) . '</label>';
		echo '<div class="tc-card-surface tc-d-flex tc-flex-col tc-gap-8 tc-p-12">';
		echo '<label class="tc-text-13 tc-d-flex tc-items-center tc-gap-8 tc-text-primary"><input type="checkbox" id="tc-p2s-responsive" checked> ' . esc_html__( 'Create responsive SVG output', 'textcraft-tools' ) . '</label>';
		echo '<p class="tc-info-text tc-m-0">' . esc_html__( 'Responsive SVG uses a viewBox so it scales cleanly at any size without fixed pixel dimensions — ideal for responsive web design', 'textcraft-tools' ) . '</p>';
		echo '</div>';
		echo '</div>';

		echo '<div>';
		echo '<label class="tc-label tc-d-block tc-mb-8" for="tc-p2s-title-prefix">' . esc_html__( 'SVG Accessibility Label', 'textcraft-tools' ) . '</label>';
		echo '<input type="text" id="tc-p2s-title-prefix" value="' . esc_attr__( 'Converted PNG', 'textcraft-tools' ) . '" class="tc-color-hex tc-w-full">';
		echo '<p class="tc-info-text">' . esc_html__( 'This text appears in the SVG title tag, making exported files easier to identify and more accessible for screen readers', 'textcraft-tools' ) . '</p>';
		echo '</div>';

		echo '<div class="tc-flex-col-end">';
		$this->render_options_row(
			[
				[
					'id'      => 'tc-p2s-opt-prefix',
					'label'   => esc_html__( 'Add "converted_" prefix to filenames', 'textcraft-tools' ),
					'checked' => false,
				],
				[
					'id'      => 'tc-p2s-opt-desc',
					'label'   => esc_html__( 'Include basic SVG description metadata', 'textcraft-tools' ),
					'checked' => true,
				],
			]
		);
		echo '</div>';

		echo '<div class="tc-flex-end">';
		echo '<div class="tc-info-card">';
		echo esc_html__( 'Your files stay private — everything is processed locally in your browser with no data uploaded to any server. Uploads, previews, and converted SVGs are cached so they persist after page reload.', 'textcraft-tools' );
		echo '</div>';
		echo '</div>';

		echo '</div>';

		// Actions.
		$this->render_button_row(
			[
				[
					'id'       => 'tc-p2s-convert',
					'label'    => esc_html__( 'Convert to SVG', 'textcraft-tools' ),
					'variant'  => 'primary',
					'disabled' => true,
				],
				[
					'id'      => 'tc-p2s-download-all',
					'label'   => esc_html__( 'Download All (ZIP)', 'textcraft-tools' ),
					'variant' => 'ghost',
				],
				[
					'id'      => 'tc-p2s-clear',
					'label'   => esc_html__( 'Clear All', 'textcraft-tools' ),
					'variant' => 'danger',
				],
			]
		);

		// Stats.
		$this->render_stat_bar(
			[
				[ 'id' => 'tc-p2s-stat-loaded',    'label' => esc_html__( 'Files Loaded', 'textcraft-tools' ) ],
				[ 'id' => 'tc-p2s-stat-converted', 'label' => esc_html__( 'Converted', 'textcraft-tools' ) ],
				[ 'id' => 'tc-p2s-stat-size',      'label' => esc_html__( 'Total SVG Size', 'textcraft-tools' ) ],
				[ 'id' => 'tc-p2s-stat-mode',      'label' => esc_html__( 'Output Mode', 'textcraft-tools' ) ],
			]
		);

		// Results.
		echo '<div id="tc-p2s-cards" class="tc-results-wrap">';
		echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'Converted SVG Files', 'textcraft-tools' ) . '</span></div>';
		echo '<div id="tc-p2s-grid" class="tc-grid-cards-lg"></div>';
		echo '</div>';

		// JSZip.
		echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" defer crossorigin="anonymous"></script>';

		$this->render_inline_script( <<<'JS'
(function () {
    var drop          = document.getElementById('tc-p2s-drop');
    var fileInp       = document.getElementById('tc-p2s-upload');
    var previewWrap   = document.getElementById('tc-p2s-previews');
    var previewGrid   = document.getElementById('tc-p2s-preview-grid');
    var uploadLine    = document.getElementById('tc-p2s-upload-line');
    var uploadText    = document.getElementById('tc-p2s-upload-text');
    var convWrap      = document.getElementById('tc-p2s-conv-progress');
    var convBar       = document.getElementById('tc-p2s-conv-bar');
    var convPct       = document.getElementById('tc-p2s-conv-pct');
    var convLabel     = document.getElementById('tc-p2s-conv-label');
    var grid          = document.getElementById('tc-p2s-grid');
    var cardsWrap     = document.getElementById('tc-p2s-cards');
    var btnConv       = document.getElementById('tc-p2s-convert');
    var btnDlAll      = document.getElementById('tc-p2s-download-all');
    var btnClear      = document.getElementById('tc-p2s-clear');
    var responsiveInp = document.getElementById('tc-p2s-responsive');
    var titleInp      = document.getElementById('tc-p2s-title-prefix');
    var descInp       = document.getElementById('tc-p2s-opt-desc');

    var files = [];
    var results = [];

    var DB_NAME  = 'tc_p2s_cache';
    var DB_STORE = 'sessions';
    var DB_KEY   = 'png_to_svg_session';
    var db = null;

    if (btnDlAll) {
        btnDlAll.style.display = 'none';
    }

    function openDB(cb) {
        if (db) {
            cb(db);
            return;
        }
        var req = indexedDB.open(DB_NAME, 1);
        req.onupgradeneeded = function (e) {
            var database = e.target.result;
            if (!database.objectStoreNames.contains(DB_STORE)) {
                database.createObjectStore(DB_STORE, { keyPath: 'id' });
            }
        };
        req.onsuccess = function (e) {
            db = e.target.result;
            cb(db);
        };
        req.onerror = function () {
            cb(null);
        };
    }

    function cacheSet(data) {
        openDB(function (database) {
            if (!database) {
                return;
            }
            database.transaction(DB_STORE, 'readwrite').objectStore(DB_STORE).put({
                id: DB_KEY,
                data: data
            });
        });
    }

    function cacheGet(cb) {
        openDB(function (database) {
            if (!database) {
                cb(null);
                return;
            }
            var req = database.transaction(DB_STORE).objectStore(DB_STORE).get(DB_KEY);
            req.onsuccess = function () {
                cb(req.result ? req.result.data : null);
            };
            req.onerror = function () {
                cb(null);
            };
        });
    }

    function cacheClear() {
        openDB(function (database) {
            if (!database) {
                return;
            }
            database.transaction(DB_STORE, 'readwrite').objectStore(DB_STORE).delete(DB_KEY);
        });
    }

    function setUploadLine(text, show) {
        uploadText.textContent = text;
        uploadLine.style.display = show ? 'block' : 'none';
    }

    function formatSize(bytes) {
        return (bytes / 1024).toFixed(1) + ' KB';
    }

    function resetStats() {
        document.getElementById('tc-p2s-stat-loaded').textContent = '0';
        document.getElementById('tc-p2s-stat-converted').textContent = '0';
        document.getElementById('tc-p2s-stat-size').textContent = '-';
        document.getElementById('tc-p2s-stat-mode').textContent = responsiveInp.checked ? 'Responsive' : 'Fixed';
    }

    function xmlEscape(text) {
        return String(text || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&apos;');
    }

    function addPreviewThumb(item) {
        var thumb = document.createElement('div');
        thumb.className = 'tc-thumb-card';
        thumb.innerHTML =
            '<img src="' + item.previewSrc + '" alt="' + item.origName + '" class="tc-conv-thumb-lg tc-bg-checkerboard">' +
            '<p class="tc-text-10 tc-text-muted tc-mt-6 tc-text-ellipsis" title="' + item.origName + '">' + item.origName + '</p>';
        previewGrid.appendChild(thumb);
    }

    function refreshPreviews(items) {
        previewGrid.innerHTML = '';
        if (!items.length) {
            previewWrap.style.display = 'none';
            return;
        }
        items.forEach(function (item) {
            addPreviewThumb(item);
        });
        previewWrap.style.display = 'block';
    }

    function createBlobUrl(svgStr) {
        return URL.createObjectURL(new Blob([svgStr], { type: 'image/svg+xml' }));
    }

    function generateSVG(dataUrl, width, height, options) {
        var titleText = xmlEscape((options.titlePrefix || 'Converted PNG') + ' - ' + options.fileName);
        var descText = xmlEscape('Converted from PNG in the browser. Original dimensions: ' + width + 'x' + height + '.');
        var widthAttr = options.responsive ? '100%' : String(width);
        var heightAttr = options.responsive ? '100%' : String(height);
        var ratioStyle = options.responsive ? ' style="max-width:100%;height:auto;"' : '';
        var descMarkup = options.includeDesc ? '\n  <desc>' + descText + '</desc>' : '';

        return '<?xml version="1.0" encoding="UTF-8"?>\n'
            + '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"'
            + ' width="' + widthAttr + '" height="' + heightAttr + '" viewBox="0 0 ' + width + ' ' + height + '"' + ratioStyle + '>\n'
            + '  <title>' + titleText + '</title>' + descMarkup + '\n'
            + '  <image width="' + width + '" height="' + height + '" xlink:href="' + dataUrl + '"/>\n'
            + '</svg>';
    }

    function restoreFromCache() {
        cacheGet(function (cached) {
            if (!cached || !cached.results || !cached.results.length) {
                resetStats();
                return;
            }

            results = cached.results;
            files = [];

            if (typeof cached.responsive !== 'undefined') {
                responsiveInp.checked = !!cached.responsive;
            }
            if (typeof cached.includeDesc !== 'undefined' && descInp) {
                descInp.checked = !!cached.includeDesc;
            }
            if (cached.titlePrefix) {
                titleInp.value = cached.titlePrefix;
            }

            refreshPreviews(results.map(function (item) {
                return {
                    origName: item.origName,
                    previewSrc: item.previewSrc
                };
            }));
            renderCards(results);

            document.getElementById('tc-p2s-stat-loaded').textContent = results.length;
            document.getElementById('tc-p2s-stat-converted').textContent = results.length;
            document.getElementById('tc-p2s-stat-size').textContent = formatSize(results.reduce(function (sum, item) {
                return sum + item.svgSize;
            }, 0));
            document.getElementById('tc-p2s-stat-mode').textContent = cached.responsive ? 'Responsive' : 'Fixed';

            btnConv.disabled = false;
            if (btnDlAll) {
                btnDlAll.style.display = 'inline-flex';
            }
        });
    }

    responsiveInp.addEventListener('change', function () {
        document.getElementById('tc-p2s-stat-mode').textContent = responsiveInp.checked ? 'Responsive' : 'Fixed';
    });

    drop.addEventListener('click', function () { fileInp.click(); });
    drop.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            fileInp.click();
        }
    });
    drop.addEventListener('dragover', function (e) {
        e.preventDefault();
        drop.style.borderColor = 'var(--tc-accent)';
    });
    drop.addEventListener('dragleave', function () {
        drop.style.borderColor = 'var(--tc-border)';
    });
    drop.addEventListener('drop', function (e) {
        e.preventDefault();
        drop.style.borderColor = 'var(--tc-border)';
        addFiles(e.dataTransfer.files);
    });
    fileInp.addEventListener('change', function () {
        addFiles(fileInp.files);
    });

    function addFiles(fileList) {
        var incoming = Array.from(fileList || []).filter(function (file) {
            return file.type === 'image/png';
        }).slice(0, Math.max(0, 20 - files.length));

        if (!incoming.length) {
            return;
        }

        var loaded = 0;
        setUploadLine('Uploading images: 0%', true);

        incoming.forEach(function (file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                loaded += 1;
                files.push(file);
                addPreviewThumb({
                    origName: file.name,
                    previewSrc: e.target.result
                });
                previewWrap.style.display = 'block';
                document.getElementById('tc-p2s-stat-loaded').textContent = files.length;

                var pct = Math.round((loaded / incoming.length) * 100);
                setUploadLine('Uploading images: ' + pct + '% (' + loaded + ' of ' + incoming.length + ')', true);

                if (loaded === incoming.length) {
                    btnConv.disabled = false;
                }
            };
            reader.readAsDataURL(file);
        });
    }

    function convertFile(file, options) {
        return new Promise(function (resolve) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = new Image();
                img.onload = function () {
                    var svgStr = generateSVG(e.target.result, img.width, img.height, {
                        responsive: options.responsive,
                        includeDesc: options.includeDesc,
                        titlePrefix: options.titlePrefix,
                        fileName: file.name
                    });

                    resolve({
                        origName: file.name,
                        name: file.name.replace(/\.png$/i, '.svg'),
                        previewSrc: e.target.result,
                        svgStr: svgStr,
                        svgSize: new Blob([svgStr], { type: 'image/svg+xml' }).size,
                        origSize: file.size,
                        width: img.width,
                        height: img.height,
                        mode: options.responsive ? 'Responsive' : 'Fixed'
                    });
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    btnConv.addEventListener('click', async function () {
        if (!files.length) {
            return;
        }

        var prefix = document.getElementById('tc-p2s-opt-prefix').checked ? 'converted_' : '';
        var options = {
            responsive: responsiveInp.checked,
            includeDesc: !!(descInp && descInp.checked),
            titlePrefix: titleInp.value.trim() || 'Converted PNG'
        };

        btnConv.disabled = true;
        btnConv.textContent = 'Converting...';
        convWrap.style.display = 'block';
        grid.innerHTML = '';
        results = [];

        var totalSize = 0;

        for (var i = 0; i < files.length; i++) {
            var item = await convertFile(files[i], options);
            item.name = prefix + item.name;
            results.push(item);
            totalSize += item.svgSize;

            var pct = Math.round(((i + 1) / files.length) * 100);
            convBar.style.width = pct + '%';
            convPct.textContent = pct + '%';
            convLabel.textContent = 'Converting ' + (i + 1) + ' of ' + files.length + '...';
            document.getElementById('tc-p2s-stat-converted').textContent = String(i + 1);
        }

        document.getElementById('tc-p2s-stat-size').textContent = formatSize(totalSize);
        document.getElementById('tc-p2s-stat-mode').textContent = options.responsive ? 'Responsive' : 'Fixed';
        renderCards(results);

        cacheSet({
            responsive: options.responsive,
            includeDesc: options.includeDesc,
            titlePrefix: options.titlePrefix,
            results: results
        });

        if (btnDlAll) {
            btnDlAll.style.display = 'inline-flex';
        }

        setTimeout(function () {
            convWrap.style.display = 'none';
            convBar.style.width = '0%';
        }, 500);

        btnConv.disabled = false;
        btnConv.textContent = 'Convert to SVG';
    });

    function renderCards(items) {
        grid.innerHTML = '';

        items.forEach(function (item) {
            var blobUrl = createBlobUrl(item.svgStr);
            var savings = item.origSize > 0 ? ((1 - item.svgSize / item.origSize) * 100).toFixed(1) : '0.0';
            var smaller = parseFloat(savings) >= 0;
            var savingsText = smaller ? 'Down ' + savings + '% in size' : 'Up ' + Math.abs(parseFloat(savings)).toFixed(1) + '% in size';
            var savingsColor = smaller ? '#22c55e' : '#b45309';

            var card = document.createElement('div');
            card.className = 'tc-result-card';
            card.innerHTML =
                '<div class="tc-grid-preview-2col">' +
                    '<div>' +
                        '<div class="tc-card-label-sm">Original PNG</div>' +
                        '<img src="' + item.previewSrc + '" alt="' + item.origName + '" class="tc-card-preview-img tc-bg-checkerboard">' +
                    '</div>' +
                    '<div>' +
                        '<div class="tc-card-label-sm">Converted SVG</div>' +
                        '<img src="' + blobUrl + '" alt="' + item.name + '" class="tc-card-preview-img tc-bg-checkerboard">' +
                    '</div>' +
                '</div>' +
                '<p class="tc-text-11 tc-font-semibold tc-text-primary tc-text-ellipsis tc-m-0 tc-mb-4" title="' + item.name + '">' + item.name + '</p>' +
                '<p class="tc-text-10 tc-text-secondary tc-m-0 tc-mb-2">' + item.width + 'x' + item.height + '</p>' +
                '<p class="tc-text-10 tc-text-secondary tc-m-0 tc-mb-4">' + formatSize(item.origSize) + ' -> ' + formatSize(item.svgSize) + '</p>' +
                '<p class="tc-text-10 tc-m-0 tc-mb-4" style="color:' + savingsColor + '">' + savingsText + '</p>' +
                '<p class="tc-text-10 tc-text-secondary tc-m-0 tc-mb-8">' + item.mode + ' SVG</p>' +
                '<a href="' + blobUrl + '" download="' + item.name + '" class="tc-btn tc-btn--primary tc-card-dl-btn">Download</a>';

            grid.appendChild(card);
        });

        cardsWrap.style.display = 'block';
    }

    if (btnDlAll) {
        btnDlAll.addEventListener('click', async function () {
            if (!results.length) {
                return;
            }
            if (typeof JSZip === 'undefined') {
                alert('JSZip is still loading. Please try again in a moment.');
                return;
            }

            btnDlAll.disabled = true;
            btnDlAll.textContent = 'Zipping...';

            var zip = new JSZip();
            results.forEach(function (item) {
                zip.file(item.name, item.svgStr);
            });

            var blob = await zip.generateAsync({ type: 'blob' });
            var a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'converted-svg-images.zip';
            a.click();

            btnDlAll.disabled = false;
            btnDlAll.textContent = 'Download All (ZIP)';
        });
    }

    btnClear.addEventListener('click', function () {
        files = [];
        results = [];
        fileInp.value = '';
        previewGrid.innerHTML = '';
        grid.innerHTML = '';
        previewWrap.style.display = 'none';
        cardsWrap.style.display = 'none';
        convWrap.style.display = 'none';
        convBar.style.width = '0%';
        setUploadLine('Uploading images: 0%', false);
        resetStats();
        btnConv.disabled = true;
        btnConv.textContent = 'Convert to SVG';
        if (btnDlAll) {
            btnDlAll.style.display = 'none';
        }
        cacheClear();
    });

    resetStats();
    restoreFromCache();
})();
JS
		);
	}
}
