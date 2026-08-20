<?php
/**
 * Widget: PNG to WebP Converter
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );

namespace TextCraft_Tools\Widgets;

defined( 'ABSPATH' ) || exit;

class Widget_Png_To_Webp extends TextCraft_Base_Widget {
	public function get_name(): string  { return 'textcraft_png_to_webp'; }
	public function get_title(): string { return esc_html__( 'PNG to WebP Converter', 'textcraft-tools' ); }
	public function get_icon(): string  { return 'eicon-image-rollover'; }

	protected function render_tool_content( array $settings ): void {

		// Drop zone.
		echo '<div id="tc-p2w-drop" role="button" tabindex="0" aria-label="' . esc_attr__( 'Upload PNG images — click to browse or drag and drop to convert to WebP online', 'textcraft-tools' ) . '" '
			. 'class="tc-drop-zone">';
		echo '<div class="tc-drop-icon">PNG</div>';
		echo '<p class="tc-drop-title">' . esc_html__( 'Upload your PNG images — click to browse or drag and drop', 'textcraft-tools' ) . '</p>';
		echo '<p class="tc-drop-desc">' . esc_html__( 'Convert PNG to WebP online for free. Up to 20 PNG images convert preserving transparency — all processed in your browser with no uploads.', 'textcraft-tools' ) . '</p>';
		echo '<p class="tc-drop-desc-sm">' . esc_html__( 'Supports transparent PNG images and exports optimized WebP copies locally. Your files stay private with no server uploads.', 'textcraft-tools' ) . '</p>';
		echo '<input type="file" id="tc-p2w-upload" accept="image/png" multiple class="tc-d-none">';
		echo '</div>';

		// One-line upload progress.
		echo '<div id="tc-p2w-upload-line" class="tc-upload-line">';
		echo '<span id="tc-p2w-upload-text">' . esc_html__( 'Loading images: 0%', 'textcraft-tools' ) . '</span>';
		echo '</div>';

		// Conversion progress.
		echo '<div id="tc-p2w-conv-progress" class="tc-progress-wrap">';
		echo '<div class="tc-d-flex tc-justify-between tc-mb-6">';
		echo '<span id="tc-p2w-conv-label" class="tc-progress-label">' . esc_html__( 'Converting...', 'textcraft-tools' ) . '</span>';
		echo '<span id="tc-p2w-conv-pct" class="tc-progress-pct">0%</span>';
		echo '</div>';
		echo '<div class="tc-progress-bg">';
		echo '<div id="tc-p2w-conv-bar" class="tc-progress-fill tc-progress-fill--green"></div>';
		echo '</div>';
		echo '</div>';

		// Loaded previews.
		echo '<div id="tc-p2w-previews" class="tc-preview-wrap">';
		echo '<div class="tc-label-row tc-mb-8"><span class="tc-label">' . esc_html__( 'Loaded PNG Images', 'textcraft-tools' ) . '</span></div>';
		echo '<div id="tc-p2w-preview-grid" class="tc-grid-preview-sm"></div>';
		echo '</div>';

		// Options.
		echo '<div class="tc-settings-grid">';

		echo '<div>';
		echo '<label class="tc-label tc-d-block tc-mb-8" for="tc-p2w-quality">' . esc_html__( 'Output WebP Quality', 'textcraft-tools' ) . '</label>';
		echo '<div class="tc-d-flex tc-items-center tc-gap-10">';
		echo '<input type="range" id="tc-p2w-quality" min="50" max="100" value="90" class="tc-slider">';
		echo '<span id="tc-p2w-quality-val" class="tc-text-14 tc-accent-value tc-min-w-40">90%</span>';
		echo '</div>';
		echo '<p class="tc-info-text">' . esc_html__( 'Higher quality preserves more detail but produces larger files. Lower quality creates smaller WebP files — adjust the slider to find your ideal balance.', 'textcraft-tools' ) . '</p>';
		echo '</div>';

		echo '<div class="tc-flex-end">';
		echo '<div class="tc-info-card">';
		echo esc_html__( 'Transparent PNG areas are preserved when your browser supports WebP alpha channels. Your privacy is protected — all conversions happen locally with no data uploaded to any server. Files and settings are cached in your browser for convenience after reload.', 'textcraft-tools' );
		echo '</div>';
		echo '</div>';

		echo '<div class="tc-flex-col-end">';
		$this->render_options_row(
			[
				[
					'id'      => 'tc-p2w-opt-prefix',
					'label'   => esc_html__( 'Add "converted_" prefix to filenames', 'textcraft-tools' ),
					'checked' => false,
				],
				[
					'id'      => 'tc-p2w-opt-lossless',
					'label'   => esc_html__( 'Try higher-fidelity WebP export when browser supports it', 'textcraft-tools' ),
					'checked' => false,
				],
			]
		);
		echo '</div>';

		echo '<div class="tc-flex-end">';
		echo '<div class="tc-info-card">';
		echo esc_html__( 'Each result card shows both the original PNG and the converted WebP preview side by side so you can compare quality before downloading your images.', 'textcraft-tools' );
		echo '</div>';
		echo '</div>';

		echo '</div>';

		// Actions.
		$this->render_button_row(
			[
				[
					'id'       => 'tc-p2w-convert',
					'label'    => 'Convert to WebP',
					'variant'  => 'primary',
					'disabled' => true,
				],
				[
					'id'      => 'tc-p2w-download-all',
					'label'   => 'Download All (ZIP)',
					'variant' => 'ghost',
				],
				[
					'id'      => 'tc-p2w-clear',
					'label'   => 'Clear All',
					'variant' => 'danger',
				],
			]
		);

		// Stats.
		$this->render_stat_bar(
			[
				[ 'id' => 'tc-p2w-stat-loaded',    'label' => esc_html__( 'Files Loaded', 'textcraft-tools' ) ],
				[ 'id' => 'tc-p2w-stat-converted', 'label' => esc_html__( 'Converted', 'textcraft-tools' ) ],
				[ 'id' => 'tc-p2w-stat-size',      'label' => esc_html__( 'Total WebP Size', 'textcraft-tools' ) ],
				[ 'id' => 'tc-p2w-stat-quality',   'label' => esc_html__( 'Quality', 'textcraft-tools' ) ],
			]
		);

		// Results.
		echo '<div id="tc-p2w-cards" class="tc-results-wrap">';
		echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'Converted WebP Files', 'textcraft-tools' ) . '</span></div>';
		echo '<div id="tc-p2w-grid" class="tc-grid-cards-lg"></div>';
		echo '</div>';

		echo '<canvas id="tc-p2w-canvas" class="tc-d-none"></canvas>';

		// JSZip for Download All.
		echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" defer crossorigin="anonymous"></script>';

		$this->render_inline_script( <<<'JS'
(function () {
    var drop        = document.getElementById('tc-p2w-drop');
    var fileInp     = document.getElementById('tc-p2w-upload');
    var previewWrap = document.getElementById('tc-p2w-previews');
    var previewGrid = document.getElementById('tc-p2w-preview-grid');
    var uploadLine  = document.getElementById('tc-p2w-upload-line');
    var uploadText  = document.getElementById('tc-p2w-upload-text');
    var convWrap    = document.getElementById('tc-p2w-conv-progress');
    var convBar     = document.getElementById('tc-p2w-conv-bar');
    var convPct     = document.getElementById('tc-p2w-conv-pct');
    var convLabel   = document.getElementById('tc-p2w-conv-label');
    var grid        = document.getElementById('tc-p2w-grid');
    var cardsWrap   = document.getElementById('tc-p2w-cards');
    var btnConv     = document.getElementById('tc-p2w-convert');
    var btnDlAll    = document.getElementById('tc-p2w-download-all');
    var btnClear    = document.getElementById('tc-p2w-clear');
    var qualityInp  = document.getElementById('tc-p2w-quality');
    var qualityVal  = document.getElementById('tc-p2w-quality-val');
    var losslessInp = document.getElementById('tc-p2w-opt-lossless');
    var canvas      = document.getElementById('tc-p2w-canvas');
    var ctx         = canvas.getContext('2d');

    var files   = [];
    var results = [];

    var DB_NAME  = 'tc_p2w_cache';
    var DB_STORE = 'sessions';
    var DB_KEY   = 'png_to_webp_session';
    var db       = null;

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

    function updateQualityLabel() {
        qualityVal.textContent = qualityInp.value + '%';
        document.getElementById('tc-p2w-stat-quality').textContent = qualityInp.value + '%';
    }

    function formatSize(bytes) {
        return (bytes / 1024).toFixed(1) + ' KB';
    }

    function resetStats() {
        document.getElementById('tc-p2w-stat-loaded').textContent = '0';
        document.getElementById('tc-p2w-stat-converted').textContent = '0';
        document.getElementById('tc-p2w-stat-size').textContent = '-';
        document.getElementById('tc-p2w-stat-quality').textContent = qualityInp.value + '%';
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

    function createDownloadUrl(dataUrl) {
        var parts = dataUrl.split(',');
        var mimeMatch = parts[0].match(/:(.*?);/);
        var mime = mimeMatch ? mimeMatch[1] : 'image/webp';
        var binary = atob(parts[1]);
        var len = binary.length;
        var arr = new Uint8Array(len);
        for (var i = 0; i < len; i++) {
            arr[i] = binary.charCodeAt(i);
        }
        return URL.createObjectURL(new Blob([arr], { type: mime }));
    }

    function restoreFromCache() {
        cacheGet(function (cached) {
            if (!cached || !cached.results || !cached.results.length) {
                updateQualityLabel();
                return;
            }

            results = cached.results;
            files = [];

            if (cached.quality) {
                qualityInp.value = String(cached.quality);
            }
            if (typeof cached.lossless !== 'undefined' && losslessInp) {
                losslessInp.checked = !!cached.lossless;
            }

            updateQualityLabel();
            refreshPreviews(results.map(function (item) {
                return {
                    origName: item.origName,
                    previewSrc: item.previewSrc
                };
            }));
            renderCards(results);

            document.getElementById('tc-p2w-stat-loaded').textContent = results.length;
            document.getElementById('tc-p2w-stat-converted').textContent = results.length;
            document.getElementById('tc-p2w-stat-size').textContent = formatSize(results.reduce(function (sum, item) {
                return sum + item.webpSize;
            }, 0));

            btnConv.disabled = false;
            if (btnDlAll) {
                btnDlAll.style.display = 'inline-flex';
            }
        });
    }

    qualityInp.addEventListener('input', updateQualityLabel);

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
                document.getElementById('tc-p2w-stat-loaded').textContent = files.length;

                var pct = Math.round((loaded / incoming.length) * 100);
                setUploadLine('Uploading images: ' + pct + '% (' + loaded + ' of ' + incoming.length + ')', true);

                if (loaded === incoming.length) {
                    btnConv.disabled = false;
                }
            };
            reader.readAsDataURL(file);
        });
    }

    function convertFile(file, quality) {
        return new Promise(function (resolve) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = new Image();
                img.onload = function () {
                    canvas.width = img.width;
                    canvas.height = img.height;
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    ctx.drawImage(img, 0, 0);

                    var requestedQuality = losslessInp && losslessInp.checked ? 1 : quality;
                    var webpDataUrl = canvas.toDataURL('image/webp', requestedQuality);
                    var fallbackUsed = webpDataUrl.indexOf('data:image/png') === 0;

                    resolve({
                        origName: file.name,
                        name: file.name.replace(/\.png$/i, '.webp'),
                        previewSrc: e.target.result,
                        webpDataUrl: webpDataUrl,
                        origSize: file.size,
                        webpSize: Math.round((webpDataUrl.length - webpDataUrl.split(',')[0].length - 1) * 3 / 4),
                        width: img.width,
                        height: img.height,
                        quality: Math.round(requestedQuality * 100),
                        fallbackUsed: fallbackUsed
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

        var prefix = document.getElementById('tc-p2w-opt-prefix').checked ? 'converted_' : '';
        var quality = Math.max(0.5, Math.min(1, parseInt(qualityInp.value, 10) / 100));

        btnConv.disabled = true;
        btnConv.textContent = 'Converting...';
        convWrap.style.display = 'block';
        grid.innerHTML = '';
        results = [];

        var totalSize = 0;

        for (var i = 0; i < files.length; i++) {
            var item = await convertFile(files[i], quality);
            item.name = prefix + item.name;
            results.push(item);
            totalSize += item.webpSize;

            var pct = Math.round(((i + 1) / files.length) * 100);
            convBar.style.width = pct + '%';
            convPct.textContent = pct + '%';
            convLabel.textContent = 'Converting ' + (i + 1) + ' of ' + files.length + '...';
            document.getElementById('tc-p2w-stat-converted').textContent = String(i + 1);
        }

        document.getElementById('tc-p2w-stat-size').textContent = formatSize(totalSize);
        renderCards(results);

        cacheSet({
            quality: parseInt(qualityInp.value, 10),
            lossless: !!(losslessInp && losslessInp.checked),
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
        btnConv.textContent = 'Convert to WebP';
    });

    function renderCards(items) {
        grid.innerHTML = '';

        items.forEach(function (item) {
            var downloadUrl = createDownloadUrl(item.webpDataUrl);
            var savings = item.origSize > 0 ? ((1 - item.webpSize / item.origSize) * 100).toFixed(1) : '0.0';
            var smaller = parseFloat(savings) >= 0;
            var savingsText = smaller ? 'Down ' + savings + '% in size' : 'Up ' + Math.abs(parseFloat(savings)).toFixed(1) + '% in size';
            var savingsColor = smaller ? '#22c55e' : '#b45309';
            var exportLabel = item.fallbackUsed ? 'Fallback PNG returned by browser' : 'Converted WebP';

            var card = document.createElement('div');
            card.className = 'tc-result-card';
            card.innerHTML =
                '<div class="tc-grid-preview-2col">' +
                    '<div>' +
                        '<div class="tc-card-label-sm">Original PNG</div>' +
                        '<img src="' + item.previewSrc + '" alt="' + item.origName + '" class="tc-card-preview-img tc-bg-checkerboard">' +
                    '</div>' +
                    '<div>' +
                        '<div class="tc-card-label-sm">' + exportLabel + '</div>' +
                        '<img src="' + item.webpDataUrl + '" alt="' + item.name + '" class="tc-card-preview-img tc-bg-checkerboard">' +
                    '</div>' +
                '</div>' +
                '<p class="tc-text-11 tc-font-semibold tc-text-primary tc-text-ellipsis tc-m-0 tc-mb-4" title="' + item.name + '">' + item.name + '</p>' +
                '<p class="tc-text-10 tc-text-secondary tc-m-0 tc-mb-2">' + item.width + 'x' + item.height + '</p>' +
                '<p class="tc-text-10 tc-text-secondary tc-m-0 tc-mb-4">' + formatSize(item.origSize) + ' -> ' + formatSize(item.webpSize) + '</p>' +
                '<p class="tc-text-10 tc-m-0 tc-mb-4" style="color:' + savingsColor + '">' + savingsText + '</p>' +
                '<p class="tc-text-10 tc-text-secondary tc-m-0 tc-mb-8">Quality ' + item.quality + '%</p>' +
                '<a href="' + downloadUrl + '" download="' + item.name + '" class="tc-btn tc-btn--primary tc-card-dl-btn">Download</a>';

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
                zip.file(item.name, item.webpDataUrl.split(',')[1], { base64: true });
            });

            var blob = await zip.generateAsync({ type: 'blob' });
            var a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'converted-webp-images.zip';
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
        btnConv.textContent = 'Convert to WebP';
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
