<?php
/**
 * Widget: PNG to HEIC Converter
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );

namespace TextCraft_Tools\Widgets;

defined( 'ABSPATH' ) || exit;

class Widget_Png_To_Heic extends TextCraft_Base_Widget {
	public function get_name(): string  { return 'textcraft_png_to_heic'; }
	public function get_title(): string { return esc_html__( 'PNG to HEIC Converter', 'textcraft-tools' ); }
	public function get_icon(): string  { return 'eicon-image-rollover'; }

	protected function render_tool_content( array $settings ): void {

		// Drop zone.
		echo '<div id="tc-p2h-drop" role="button" tabindex="0" aria-label="' . esc_attr__( 'Upload PNG images — click to browse or drag and drop to convert to HEIC online', 'textcraft-tools' ) . '" '
			. 'class="tc-drop-zone">';
		echo '<div class="tc-drop-icon">HEIC</div>';
		echo '<p class="tc-drop-title">' . esc_html__( 'Upload your PNG images — click to browse or drag and drop', 'textcraft-tools' ) . '</p>';
		echo '<p class="tc-drop-desc">' . esc_html__( 'Convert PNG to HEIC online for free. Up to 20 PNG images convert with transparency support — all processed locally in your browser.', 'textcraft-tools' ) . '</p>';
		echo '<p class="tc-drop-desc-sm">' . esc_html__( 'HEIC export depends on your browser support. Your privacy is protected — everything runs locally with no files uploaded to any server.', 'textcraft-tools' ) . '</p>';
		echo '<input type="file" id="tc-p2h-upload" accept="image/png" multiple class="tc-d-none">';
		echo '</div>';

		// One-line upload progress.
		echo '<div id="tc-p2h-upload-line" class="tc-upload-line">';
		echo '<span id="tc-p2h-upload-text">' . esc_html__( 'Loading images: 0%', 'textcraft-tools' ) . '</span>';
		echo '</div>';

		// Status notice.
		echo '<div id="tc-p2h-status" class="tc-text-13 tc-text-muted tc-mb-16 tc-card-surface tc-status-notice">';
		echo '<strong class="tc-text-primary">' . esc_html__( 'Browser support check:', 'textcraft-tools' ) . '</strong> ';
		echo '<span id="tc-p2h-status-text">' . esc_html__( 'Checking HEIC export support...', 'textcraft-tools' ) . '</span>';
		echo '</div>';

		// Conversion progress.
		echo '<div id="tc-p2h-conv-progress" class="tc-progress-wrap">';
		echo '<div class="tc-d-flex tc-justify-between tc-mb-6">';
		echo '<span id="tc-p2h-conv-label" class="tc-progress-label">' . esc_html__( 'Converting...', 'textcraft-tools' ) . '</span>';
		echo '<span id="tc-p2h-conv-pct" class="tc-progress-pct">0%</span>';
		echo '</div>';
		echo '<div class="tc-progress-bg">';
		echo '<div id="tc-p2h-conv-bar" class="tc-progress-fill tc-progress-fill--orange"></div>';
		echo '</div>';
		echo '</div>';

		// Previews.
		echo '<div id="tc-p2h-previews" class="tc-preview-wrap">';
		echo '<div class="tc-label-row tc-mb-8"><span class="tc-label">' . esc_html__( 'Loaded PNG Images', 'textcraft-tools' ) . '</span></div>';
		echo '<div id="tc-p2h-preview-grid" class="tc-grid-preview-sm"></div>';
		echo '</div>';

		// Options.
		echo '<div class="tc-settings-grid">';

		echo '<div>';
		echo '<label class="tc-label tc-d-block tc-mb-8" for="tc-p2h-quality">' . esc_html__( 'Output HEIC Quality', 'textcraft-tools' ) . '</label>';
		echo '<div class="tc-d-flex tc-items-center tc-gap-10">';
		echo '<input type="range" id="tc-p2h-quality" min="50" max="100" value="90" class="tc-slider">';
		echo '<span id="tc-p2h-quality-val" class="tc-text-14 tc-accent-value tc-min-w-40">90%</span>';
		echo '</div>';
		echo '<p class="tc-info-text">' . esc_html__( 'Higher quality preserves more detail, but HEIC encoding support varies by browser. Adjust the slider to balance quality with file size.', 'textcraft-tools' ) . '</p>';
		echo '</div>';

		echo '<div>';
		echo '<label class="tc-label tc-d-block tc-mb-8" for="tc-p2h-bg">' . esc_html__( 'Background Color', 'textcraft-tools' ) . '</label>';
		echo '<div class="tc-d-flex tc-items-center tc-gap-10">';
		echo '<input type="color" id="tc-p2h-bg" value="#ffffff" class="tc-color-picker">';
		echo '<input type="text" id="tc-p2h-bg-text" value="#ffffff" aria-label="' . esc_attr__( 'Background color hex value', 'textcraft-tools' ) . '" class="tc-color-hex">';
		echo '</div>';
		echo '<p class="tc-info-text">' . esc_html__( 'HEIC does not support transparency in this browser-based workflow, so transparent PNG areas will be filled with your chosen background color', 'textcraft-tools' ) . '</p>';
		echo '</div>';

		echo '<div class="tc-flex-col-end">';
		$this->render_options_row(
			[
				[
					'id'      => 'tc-p2h-opt-prefix',
					'label'   => esc_html__( 'Add "converted_" prefix to filenames', 'textcraft-tools' ),
					'checked' => false,
				],
				[
					'id'      => 'tc-p2h-opt-cache',
					'label'   => esc_html__( 'Keep converted HEIC files in browser cache', 'textcraft-tools' ),
					'checked' => true,
				],
			]
		);
		echo '</div>';

		echo '<div class="tc-flex-end">';
		echo '<div class="tc-info-card">';
		echo esc_html__( 'Your files stay private — all conversions happen locally in your browser with no data uploaded to any server. Previews and converted files are cached so they persist after page reload.', 'textcraft-tools' );
		echo '</div>';
		echo '</div>';

		echo '</div>';

		// Actions.
		$this->render_button_row(
			[
				[
					'id'       => 'tc-p2h-convert',
					'label'    => esc_html__( 'Convert to HEIC', 'textcraft-tools' ),
					'variant'  => 'primary',
					'disabled' => true,
				],
				[
					'id'      => 'tc-p2h-download-all',
					'label'   => esc_html__( 'Download All (ZIP)', 'textcraft-tools' ),
					'variant' => 'ghost',
				],
				[
					'id'      => 'tc-p2h-clear',
					'label'   => esc_html__( 'Clear All', 'textcraft-tools' ),
					'variant' => 'danger',
				],
			]
		);

		// Stats.
		$this->render_stat_bar(
			[
				[ 'id' => 'tc-p2h-stat-loaded',    'label' => esc_html__( 'Files Loaded', 'textcraft-tools' ) ],
				[ 'id' => 'tc-p2h-stat-converted', 'label' => esc_html__( 'Converted', 'textcraft-tools' ) ],
				[ 'id' => 'tc-p2h-stat-size',      'label' => esc_html__( 'Total HEIC Size', 'textcraft-tools' ) ],
				[ 'id' => 'tc-p2h-stat-quality',   'label' => esc_html__( 'Quality', 'textcraft-tools' ) ],
			]
		);

		// Results.
		echo '<div id="tc-p2h-cards" class="tc-results-wrap">';
		echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'Converted HEIC Files', 'textcraft-tools' ) . '</span></div>';
		echo '<div id="tc-p2h-grid" class="tc-grid-cards-lg"></div>';
		echo '</div>';

		echo '<canvas id="tc-p2h-canvas" class="tc-d-none"></canvas>';

		// JSZip for Download All.
		echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" defer crossorigin="anonymous"></script>';

		$this->render_inline_script( <<<'JS'
(function () {
    var drop        = document.getElementById('tc-p2h-drop');
    var fileInp     = document.getElementById('tc-p2h-upload');
    var previewWrap = document.getElementById('tc-p2h-previews');
    var previewGrid = document.getElementById('tc-p2h-preview-grid');
    var uploadLine  = document.getElementById('tc-p2h-upload-line');
    var uploadText  = document.getElementById('tc-p2h-upload-text');
    var statusText  = document.getElementById('tc-p2h-status-text');
    var convWrap    = document.getElementById('tc-p2h-conv-progress');
    var convBar     = document.getElementById('tc-p2h-conv-bar');
    var convPct     = document.getElementById('tc-p2h-conv-pct');
    var convLabel   = document.getElementById('tc-p2h-conv-label');
    var grid        = document.getElementById('tc-p2h-grid');
    var cardsWrap   = document.getElementById('tc-p2h-cards');
    var btnConv     = document.getElementById('tc-p2h-convert');
    var btnDlAll    = document.getElementById('tc-p2h-download-all');
    var btnClear    = document.getElementById('tc-p2h-clear');
    var qualityInp  = document.getElementById('tc-p2h-quality');
    var qualityVal  = document.getElementById('tc-p2h-quality-val');
    var bgInp       = document.getElementById('tc-p2h-bg');
    var bgTextInp   = document.getElementById('tc-p2h-bg-text');
    var cacheInp    = document.getElementById('tc-p2h-opt-cache');
    var canvas      = document.getElementById('tc-p2h-canvas');
    var ctx         = canvas.getContext('2d');

    var files = [];
    var results = [];
    var heicSupported = false;

    var DB_NAME  = 'tc_p2h_cache';
    var DB_STORE = 'sessions';
    var DB_KEY   = 'png_to_heic_session';
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

    function updateQualityLabel() {
        qualityVal.textContent = qualityInp.value + '%';
        document.getElementById('tc-p2h-stat-quality').textContent = qualityInp.value + '%';
    }

    function syncColorInputs(fromPicker) {
        if (fromPicker) {
            bgTextInp.value = bgInp.value;
            return;
        }
        var val = bgTextInp.value.trim();
        if (/^#[0-9a-fA-F]{6}$/.test(val)) {
            bgInp.value = val;
        }
    }

    function formatSize(bytes) {
        return (bytes / 1024).toFixed(1) + ' KB';
    }

    function resetStats() {
        document.getElementById('tc-p2h-stat-loaded').textContent = '0';
        document.getElementById('tc-p2h-stat-converted').textContent = '0';
        document.getElementById('tc-p2h-stat-size').textContent = '-';
        document.getElementById('tc-p2h-stat-quality').textContent = qualityInp.value + '%';
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
        var mime = mimeMatch ? mimeMatch[1] : 'image/heic';
        var binary = atob(parts[1]);
        var len = binary.length;
        var arr = new Uint8Array(len);
        for (var i = 0; i < len; i++) {
            arr[i] = binary.charCodeAt(i);
        }
        return URL.createObjectURL(new Blob([arr], { type: mime }));
    }

    function updateSupportNotice() {
        if (heicSupported) {
            statusText.textContent = 'This browser appears to support canvas HEIC export. You can convert and cache files locally.';
        } else {
            statusText.textContent = 'This browser does not appear to support direct HEIC export from canvas. The widget is ready, but conversion will stay disabled until HEIC export is available.';
        }
    }

    function detectHeicSupport() {
        try {
            if (!canvas || !canvas.toDataURL) {
                heicSupported = false;
                updateSupportNotice();
                return;
            }
            canvas.width = 1;
            canvas.height = 1;
            ctx.clearRect(0, 0, 1, 1);
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, 1, 1);
            var probe = canvas.toDataURL('image/heic', 0.9);
            heicSupported = probe.indexOf('data:image/heic') === 0 || probe.indexOf('data:image/heif') === 0;
        } catch (err) {
            heicSupported = false;
        }
        updateSupportNotice();
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
            if (cached.background && /^#[0-9a-fA-F]{6}$/.test(cached.background)) {
                bgInp.value = cached.background;
                bgTextInp.value = cached.background;
            }
            if (typeof cached.keepCache !== 'undefined' && cacheInp) {
                cacheInp.checked = !!cached.keepCache;
            }

            updateQualityLabel();
            refreshPreviews(results.map(function (item) {
                return {
                    origName: item.origName,
                    previewSrc: item.previewSrc
                };
            }));
            renderCards(results);

            document.getElementById('tc-p2h-stat-loaded').textContent = results.length;
            document.getElementById('tc-p2h-stat-converted').textContent = results.length;
            document.getElementById('tc-p2h-stat-size').textContent = formatSize(results.reduce(function (sum, item) {
                return sum + item.heicSize;
            }, 0));

            btnConv.disabled = !heicSupported;
            if (btnDlAll) {
                btnDlAll.style.display = 'inline-flex';
            }
        });
    }

    qualityInp.addEventListener('input', updateQualityLabel);
    bgInp.addEventListener('input', function () { syncColorInputs(true); });
    bgTextInp.addEventListener('input', function () { syncColorInputs(false); });

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
                document.getElementById('tc-p2h-stat-loaded').textContent = files.length;

                var pct = Math.round((loaded / incoming.length) * 100);
                setUploadLine('Uploading images: ' + pct + '% (' + loaded + ' of ' + incoming.length + ')', true);

                if (loaded === incoming.length && heicSupported) {
                    btnConv.disabled = false;
                }
            };
            reader.readAsDataURL(file);
        });
    }

    function exportCanvasAsHeic(canvasEl, quality) {
        return new Promise(function (resolve, reject) {
            if (!canvasEl.toBlob) {
                reject(new Error('Canvas blob export is not available.'));
                return;
            }

            canvasEl.toBlob(function (blob) {
                if (!blob || (!/^image\/hei[cf]/.test(blob.type) && blob.type !== 'image/heic' && blob.type !== 'image/heif')) {
                    reject(new Error('This browser did not return a HEIC blob.'));
                    return;
                }

                var reader = new FileReader();
                reader.onloadend = function () {
                    resolve({
                        blob: blob,
                        dataUrl: reader.result
                    });
                };
                reader.onerror = function () {
                    reject(new Error('Unable to read generated HEIC blob.'));
                };
                reader.readAsDataURL(blob);
            }, 'image/heic', quality);
        });
    }

    function convertFile(file, quality, backgroundHex) {
        return new Promise(function (resolve, reject) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = new Image();
                img.onload = async function () {
                    try {
                        canvas.width = img.width;
                        canvas.height = img.height;
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        ctx.fillStyle = backgroundHex;
                        ctx.fillRect(0, 0, canvas.width, canvas.height);
                        ctx.drawImage(img, 0, 0);

                        var exported = await exportCanvasAsHeic(canvas, quality);

                        resolve({
                            origName: file.name,
                            name: file.name.replace(/\.png$/i, '.heic'),
                            previewSrc: e.target.result,
                            heicDataUrl: exported.dataUrl,
                            origSize: file.size,
                            heicSize: exported.blob.size,
                            width: img.width,
                            height: img.height,
                            quality: Math.round(quality * 100),
                            background: backgroundHex
                        });
                    } catch (err) {
                        reject(err);
                    }
                };
                img.onerror = function () {
                    reject(new Error('Unable to read the uploaded PNG image.'));
                };
                img.src = e.target.result;
            };
            reader.onerror = function () {
                reject(new Error('Unable to load the selected PNG file.'));
            };
            reader.readAsDataURL(file);
        });
    }

    btnConv.addEventListener('click', async function () {
        if (!files.length || !heicSupported) {
            return;
        }

        var prefix = document.getElementById('tc-p2h-opt-prefix').checked ? 'converted_' : '';
        var quality = Math.max(0.5, Math.min(1, parseInt(qualityInp.value, 10) / 100));
        var backgroundHex = /^#[0-9a-fA-F]{6}$/.test(bgTextInp.value.trim()) ? bgTextInp.value.trim() : '#ffffff';

        btnConv.disabled = true;
        btnConv.textContent = 'Converting...';
        convWrap.style.display = 'block';
        grid.innerHTML = '';
        results = [];

        var totalSize = 0;

        try {
            for (var i = 0; i < files.length; i++) {
                var item = await convertFile(files[i], quality, backgroundHex);
                item.name = prefix + item.name;
                results.push(item);
                totalSize += item.heicSize;

                var pct = Math.round(((i + 1) / files.length) * 100);
                convBar.style.width = pct + '%';
                convPct.textContent = pct + '%';
                convLabel.textContent = 'Converting ' + (i + 1) + ' of ' + files.length + '...';
                document.getElementById('tc-p2h-stat-converted').textContent = String(i + 1);
            }

            document.getElementById('tc-p2h-stat-size').textContent = formatSize(totalSize);
            renderCards(results);

            if (cacheInp && cacheInp.checked) {
                cacheSet({
                    quality: parseInt(qualityInp.value, 10),
                    background: backgroundHex,
                    keepCache: true,
                    results: results
                });
            } else {
                cacheClear();
            }

            if (btnDlAll) {
                btnDlAll.style.display = 'inline-flex';
            }
        } catch (err) {
            alert(err && err.message ? err.message : 'HEIC conversion failed in this browser.');
        }

        setTimeout(function () {
            convWrap.style.display = 'none';
            convBar.style.width = '0%';
        }, 500);

        btnConv.disabled = false;
        btnConv.textContent = 'Convert to HEIC';
    });

    function renderCards(items) {
        grid.innerHTML = '';

        items.forEach(function (item) {
            var downloadUrl = createDownloadUrl(item.heicDataUrl);
            var savings = item.origSize > 0 ? ((1 - item.heicSize / item.origSize) * 100).toFixed(1) : '0.0';
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
                        '<div class="tc-card-label-sm">HEIC Preview</div>' +
                        '<img src="' + item.heicDataUrl + '" alt="' + item.name + '" class="tc-card-preview-img tc-bg-white">' +
                    '</div>' +
                '</div>' +
                '<p class="tc-text-11 tc-font-semibold tc-text-primary tc-text-ellipsis tc-m-0 tc-mb-4" title="' + item.name + '">' + item.name + '</p>' +
                '<p class="tc-text-10 tc-text-secondary tc-m-0 tc-mb-2">' + item.width + 'x' + item.height + '</p>' +
                '<p class="tc-text-10 tc-text-secondary tc-m-0 tc-mb-4">' + formatSize(item.origSize) + ' -> ' + formatSize(item.heicSize) + '</p>' +
                '<p class="tc-text-10 tc-m-0 tc-mb-4" style="color:' + savingsColor + '">' + savingsText + '</p>' +
                '<p class="tc-text-10 tc-text-secondary tc-m-0 tc-mb-8">Quality ' + item.quality + '% • BG ' + item.background + '</p>' +
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
                zip.file(item.name, item.heicDataUrl.split(',')[1], { base64: true });
            });

            var blob = await zip.generateAsync({ type: 'blob' });
            var a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'converted-heic-images.zip';
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
        btnConv.disabled = !heicSupported;
        btnConv.textContent = 'Convert to HEIC';
        if (btnDlAll) {
            btnDlAll.style.display = 'none';
        }
        cacheClear();
    });

    updateQualityLabel();
    detectHeicSupport();
    resetStats();
    restoreFromCache();
})();
JS
		);
	}
}
