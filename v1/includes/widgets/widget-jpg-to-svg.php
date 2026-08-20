<?php
/**
 * Widget: JPG to SVG Converter
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;

class Widget_Jpg_To_Svg extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_jpg_to_svg'; }
    public function get_title(): string { return esc_html__( 'JPG to SVG Converter', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-image-box'; }
    protected function render_tool_content( array $settings ): void {

        // Drop zone
        echo '<div id="tc-j2s-drop" role="button" tabindex="0" aria-label="' . esc_attr__( 'Upload JPG images — click to browse or drag and drop to convert to SVG online', 'textcraft-tools' ) . '" '
           . 'class="tc-drop-zone">';
        echo '<div class="tc-drop-icon">🖼️</div>';
        echo '<p class="tc-drop-title">' . esc_html__( 'Upload your JPG images — click to browse or drag and drop', 'textcraft-tools' ) . '</p>';
        echo '<p class="tc-drop-desc">' . esc_html__( 'Convert JPG images to SVG online for free. Transform JPEG photos into scalable vector graphics — all processed in your browser with no uploads.', 'textcraft-tools' ) . '</p>';
        echo '<p class="tc-drop-desc-sm">' . esc_html__( 'SVG embeds your image as a base64 data URI — ideal for scalable web graphics. Your files stay private with no server uploads.', 'textcraft-tools' ) . '</p>';
        echo '<input type="file" id="tc-j2s-upload" accept="image/jpeg,image/jpg" multiple class="tc-d-none">';
        echo '</div>';

        // Upload progress bar
        echo '<div id="tc-j2s-upload-progress" class="tc-progress-wrap">';
        echo '<div class="tc-d-flex tc-justify-between tc-mb-6">';
        echo '<span id="tc-j2s-upload-label" class="tc-progress-label">' . esc_html__( 'Loading images…', 'textcraft-tools' ) . '</span>';
        echo '<span id="tc-j2s-upload-pct" class="tc-progress-pct">0%</span>';
        echo '</div>';
        echo '<div class="tc-progress-bg">';
        echo '<div id="tc-j2s-upload-bar" class="tc-progress-fill tc-progress-fill--pink"></div>';
        echo '</div>';
        echo '</div>';

        // Conversion progress bar
        echo '<div id="tc-j2s-conv-progress" class="tc-progress-wrap">';
        echo '<div class="tc-d-flex tc-justify-between tc-mb-6">';
        echo '<span id="tc-j2s-conv-label" class="tc-progress-label">' . esc_html__( 'Converting…', 'textcraft-tools' ) . '</span>';
        echo '<span id="tc-j2s-conv-pct" class="tc-progress-pct">0%</span>';
        echo '</div>';
        echo '<div class="tc-progress-bg">';
        echo '<div id="tc-j2s-conv-bar" class="tc-progress-fill tc-progress-fill--pink"></div>';
        echo '</div>';
        echo '</div>';

        // Inline previews of loaded images
        echo '<div id="tc-j2s-previews" class="tc-preview-wrap">';
        echo '<div class="tc-label-row tc-mb-8"><span class="tc-label">' . esc_html__( 'Loaded Images', 'textcraft-tools' ) . '</span></div>';
        echo '<div id="tc-j2s-preview-grid" class="tc-grid-nato"></div>';
        echo '</div>';

        // Options
        echo '<div class="tc-settings-grid">';

        // SVG mode
        echo '<div>';
        echo '<label class="tc-label tc-d-block tc-mb-8">' . esc_html__( 'SVG Output Mode', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap" id="tc-j2s-mode-group">';
        $modes = [
            'embed'   => [ 'label' => __( 'Embedded',   'textcraft-tools' ), 'sub' => __( 'Base64 image tag', 'textcraft-tools' ) ],
            'traced'  => [ 'label' => __( 'Posterised', 'textcraft-tools' ), 'sub' => __( 'Colour blocks',    'textcraft-tools' ) ],
        ];
        $first = true;
        foreach ( $modes as $val => $info ) {
            $active  = $first ? ' tc-btn-active' : '';
            $variant = $first ? 'primary' : 'secondary';
            echo '<button class="tc-btn tc-btn--' . esc_attr( $variant ) . ' tc-j2s-mode-btn' . $active . '" data-mode="' . esc_attr( $val ) . '">';
            echo esc_html( $info['label'] );
            echo '<br><small>' . esc_html( $info['sub'] ) . '</small>';
            echo '</button>';
            $first = false;
        }
        echo '</div>';
        echo '</div>';

        // Posterise colours (shown for traced mode)
        echo '<div id="tc-j2s-traced-opts" class="tc-hidden">';
        echo '<label class="tc-label tc-d-block tc-mb-8">' . esc_html__( 'Colour Levels (posterise)', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-items-center tc-gap-10">';
        echo '<input type="range" id="tc-j2s-levels" min="2" max="16" value="6" class="tc-slider">';
        echo '<span id="tc-j2s-levels-val" class="tc-text-14 tc-accent-value tc-min-w-24">6</span>';
        echo '</div>';
        echo '<p class="tc-info-text">' . esc_html__( 'Lower values create fewer colors and a simpler SVG — good for flat graphics and icons', 'textcraft-tools' ) . '</p>';
        echo '</div>';

        echo '<div class="tc-flex-col-end">';
        $this->render_options_row( [
            [ 'id' => 'tc-j2s-opt-prefix',     'label' => esc_html__( 'Add "converted_" prefix to filenames', 'textcraft-tools' ), 'checked' => false ],
            [ 'id' => 'tc-j2s-opt-responsive',  'label' => esc_html__( 'Make SVG responsive (no fixed size)',  'textcraft-tools' ), 'checked' => true  ],
        ] );
        echo '</div>';

        echo '</div>'; // end options grid

        // Action buttons
        $this->render_button_row( [
            [ 'id' => 'tc-j2s-convert',      'label' => '🖼️ ' . esc_html__( 'Convert to SVG',    'textcraft-tools' ), 'variant' => 'primary', 'disabled' => true ],
            [ 'id' => 'tc-j2s-download-all', 'label' => '📦 ' . esc_html__( 'Download All (ZIP)', 'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-j2s-clear',        'label' => '🗑️ ' . esc_html__( 'Clear All',          'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        // Stats bar
        $this->render_stat_bar( [
            [ 'id' => 'tc-j2s-stat-loaded',    'label' => esc_html__( 'Files Loaded', 'textcraft-tools' ) ],
            [ 'id' => 'tc-j2s-stat-converted', 'label' => esc_html__( 'Converted',   'textcraft-tools' ) ],
            [ 'id' => 'tc-j2s-stat-size',      'label' => esc_html__( 'Total Size',  'textcraft-tools' ) ],
            [ 'id' => 'tc-j2s-stat-mode',      'label' => esc_html__( 'Mode',        'textcraft-tools' ) ],
        ] );

        // Results grid
        echo '<div id="tc-j2s-cards" class="tc-results-wrap">';
        echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'Converted Files', 'textcraft-tools' ) . '</span></div>';
        echo '<div id="tc-j2s-grid" class="tc-grid-cards"></div>';
        echo '</div>';

        echo '<canvas id="tc-j2s-canvas" class="tc-d-none"></canvas>';

        // JSZip for Download All
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" defer crossorigin="anonymous"></script>';

        // Inline JS
        $this->render_inline_script( <<<'JS'
(function () {
    var drop        = document.getElementById('tc-j2s-drop');
    var fileInp     = document.getElementById('tc-j2s-upload');
    var canvas      = document.getElementById('tc-j2s-canvas');
    var ctx         = canvas.getContext('2d');
    var grid        = document.getElementById('tc-j2s-grid');
    var previewGrid = document.getElementById('tc-j2s-preview-grid');
    var btnConv     = document.getElementById('tc-j2s-convert');
    var btnDlAll    = document.getElementById('tc-j2s-download-all');

    var files   = [];
    var results = [];
    var svgMode = 'embed';

    var DB_NAME  = 'tc_j2s_cache';
    var DB_STORE = 'images';
    var db       = null;

    if (btnDlAll) btnDlAll.style.display = 'none';

    // ── IndexedDB cache ──────────────────────────────────────────────
    function openDB(cb) {
        if (db) { cb(db); return; }
        var req = indexedDB.open(DB_NAME, 1);
        req.onupgradeneeded = function (e) {
            e.target.result.createObjectStore(DB_STORE, { keyPath: 'id' });
        };
        req.onsuccess = function (e) { db = e.target.result; cb(db); };
        req.onerror   = function ()  { cb(null); };
    }
    function cacheSet(key, data) {
        openDB(function (d) {
            if (!d) return;
            d.transaction(DB_STORE, 'readwrite').objectStore(DB_STORE).put({ id: key, data: data });
        });
    }
    function cacheGet(key, cb) {
        openDB(function (d) {
            if (!d) { cb(null); return; }
            var req = d.transaction(DB_STORE).objectStore(DB_STORE).get(key);
            req.onsuccess = function () { cb(req.result ? req.result.data : null); };
            req.onerror   = function () { cb(null); };
        });
    }
    function cacheClear() {
        openDB(function (d) {
            if (!d) return;
            d.transaction(DB_STORE, 'readwrite').objectStore(DB_STORE).clear();
        });
    }

    // ── Restore from cache ───────────────────────────────────────────
    cacheGet('session', function (cached) {
        if (!cached || !cached.results || !cached.results.length) return;
        results = cached.results;
        results.forEach(function (r) { addPreviewThumb(r.previewSrc, r.origName); });
        document.getElementById('tc-j2s-previews').style.display = 'block';
        renderCards(results);

        var totalSize = results.reduce(function (s, r) { return s + r.svgSize; }, 0);
        document.getElementById('tc-j2s-stat-loaded').textContent    = results.length;
        document.getElementById('tc-j2s-stat-converted').textContent = results.length;
        document.getElementById('tc-j2s-stat-size').textContent      = (totalSize / 1024).toFixed(1) + ' KB';
        document.getElementById('tc-j2s-stat-mode').textContent      = cached.mode || 'embed';

        btnConv.disabled = false;
        if (btnDlAll) btnDlAll.style.display = 'inline-flex';
    });

    // ── Mode buttons ─────────────────────────────────────────────────
    document.querySelectorAll('.tc-j2s-mode-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.tc-j2s-mode-btn').forEach(function (b) {
                b.classList.remove('tc-btn-active', 'tc-btn--primary');
                b.classList.add('tc-btn--secondary');
            });
            btn.classList.add('tc-btn-active', 'tc-btn--primary');
            btn.classList.remove('tc-btn--secondary');
            svgMode = btn.getAttribute('data-mode');
            document.getElementById('tc-j2s-traced-opts').style.display = svgMode === 'traced' ? 'block' : 'none';
        });
    });

    // Colour levels slider
    document.getElementById('tc-j2s-levels').addEventListener('input', function () {
        document.getElementById('tc-j2s-levels-val').textContent = this.value;
    });

    // ── Drop zone ────────────────────────────────────────────────────
    drop.addEventListener('click',    function () { fileInp.click(); });
    drop.addEventListener('keydown',  function (e) { if (e.key === 'Enter' || e.key === ' ') fileInp.click(); });
    drop.addEventListener('dragover', function (e) { e.preventDefault(); drop.style.borderColor = 'var(--tc-accent)'; });
    drop.addEventListener('dragleave',function ()  { drop.style.borderColor = 'var(--tc-border)'; });
    drop.addEventListener('drop', function (e) {
        e.preventDefault(); drop.style.borderColor = 'var(--tc-border)';
        addFiles(e.dataTransfer.files);
    });
    fileInp.addEventListener('change', function () { addFiles(fileInp.files); });

    function addFiles(list) {
        var valid = Array.from(list).filter(function (f) {
            return f.type === 'image/jpeg';
        }).slice(0, 20 - files.length);
        if (!valid.length) return;

        var progressWrap = document.getElementById('tc-j2s-upload-progress');
        var bar          = document.getElementById('tc-j2s-upload-bar');
        var pctEl        = document.getElementById('tc-j2s-upload-pct');
        var labelEl      = document.getElementById('tc-j2s-upload-label');

        progressWrap.style.display = 'block';
        var loaded = 0;

        valid.forEach(function (file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                addPreviewThumb(e.target.result, file.name);
                loaded++;
                var pct = Math.round((loaded / valid.length) * 100);
                bar.style.width     = pct + '%';
                pctEl.textContent   = pct + '%';
                labelEl.textContent = 'Loading image ' + loaded + ' of ' + valid.length + '…';
                if (loaded === valid.length) {
                    setTimeout(function () { progressWrap.style.display = 'none'; bar.style.width = '0%'; }, 600);
                }
            };
            reader.readAsDataURL(file);
            files.push(file);
        });

        files = files.slice(0, 20);
        document.getElementById('tc-j2s-stat-loaded').textContent = files.length;
        document.getElementById('tc-j2s-previews').style.display  = 'block';
        btnConv.disabled = false;
    }

    function addPreviewThumb(src, name) {
        var thumb = document.createElement('div');
        thumb.className = 'tc-text-center';
        thumb.innerHTML = '<img src="' + src + '" alt="' + name + '" class="tc-conv-thumb">'
                        + '<p class="tc-text-10 tc-text-muted tc-mt-4 tc-text-ellipsis">' + name + '</p>';
        previewGrid.appendChild(thumb);
    }

    // ── SVG generation ───────────────────────────────────────────────

    // Mode 1: Embed — wraps base64 JPEG inside a proper SVG <image> tag
    function generateEmbedSVG(dataUrl, w, h, responsive) {
        var wAttr = responsive ? '100%' : w;
        var hAttr = responsive ? 'auto' : h;
        var vb    = '0 0 ' + w + ' ' + h;
        return '<?xml version="1.0" encoding="UTF-8"?>\n'
             + '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"'
             + ' width="' + wAttr + '" height="' + hAttr + '" viewBox="' + vb + '">\n'
             + '  <image width="' + w + '" height="' + h + '" xlink:href="' + dataUrl + '"/>\n'
             + '</svg>';
    }

    // Mode 2: Posterised — reduces colours then builds SVG rects for each unique colour block
    // Uses a downscaled canvas for performance (max 200px wide for the colour map)
    function generatePosterisedSVG(origDataUrl, origW, origH, levels, responsive) {
        // Downscale to max 200px for colour analysis
        var SCALE = Math.min(1, 200 / Math.max(origW, origH));
        var sw = Math.max(1, Math.round(origW * SCALE));
        var sh = Math.max(1, Math.round(origH * SCALE));

        var offCanvas = document.createElement('canvas');
        offCanvas.width  = sw;
        offCanvas.height = sh;
        var offCtx = offCanvas.getContext('2d');

        // Draw downscaled version
        var img = new Image();
        img.src = origDataUrl;
        offCtx.drawImage(img, 0, 0, sw, sh);

        var imageData = offCtx.getImageData(0, 0, sw, sh);
        var data      = imageData.data;

        // Posterise: snap each channel to `levels` steps
        var step = 255 / (levels - 1);
        for (var i = 0; i < data.length; i += 4) {
            data[i]     = Math.round(Math.round(data[i]     / step) * step);
            data[i + 1] = Math.round(Math.round(data[i + 1] / step) * step);
            data[i + 2] = Math.round(Math.round(data[i + 2] / step) * step);
        }

        // Build runs of identical pixels per row (run-length encoding → fewer rects)
        var cellW = origW / sw;
        var cellH = origH / sh;
        var rects = [];

        for (var y = 0; y < sh; y++) {
            var runStart = 0;
            var runR = data[(y * sw) * 4];
            var runG = data[(y * sw) * 4 + 1];
            var runB = data[(y * sw) * 4 + 2];

            for (var x = 1; x <= sw; x++) {
                var idx = (y * sw + x) * 4;
                var r = data[idx], g = data[idx + 1], b = data[idx + 2];
                var newColour = (x === sw) || (r !== runR || g !== runG || b !== runB);
                if (newColour) {
                    var fill = 'rgb(' + runR + ',' + runG + ',' + runB + ')';
                    rects.push('<rect x="' + (runStart * cellW).toFixed(2)
                             + '" y="'     + (y * cellH).toFixed(2)
                             + '" width="' + ((x - runStart) * cellW).toFixed(2)
                             + '" height="'+ cellH.toFixed(2)
                             + '" fill="'  + fill + '"/>');
                    runStart = x; runR = r; runG = g; runB = b;
                }
            }
        }

        var wAttr = responsive ? '100%' : origW;
        var hAttr = responsive ? 'auto' : origH;
        var vb    = '0 0 ' + origW + ' ' + origH;

        return '<?xml version="1.0" encoding="UTF-8"?>\n'
             + '<svg xmlns="http://www.w3.org/2000/svg"'
             + ' width="' + wAttr + '" height="' + hAttr + '" viewBox="' + vb + '">\n'
             + rects.join('\n') + '\n'
             + '</svg>';
    }

    function convertFile(file) {
        return new Promise(function (resolve) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = new Image();
                img.onload = function () {
                    var w   = img.width;
                    var h   = img.height;
                    var responsive = document.getElementById('tc-j2s-opt-responsive').checked;

                    var svgStr;
                    if (svgMode === 'traced') {
                        var levels = parseInt(document.getElementById('tc-j2s-levels').value) || 6;
                        // Draw on shared canvas for the img reference
                        canvas.width  = w;
                        canvas.height = h;
                        ctx.drawImage(img, 0, 0);
                        svgStr = generatePosterisedSVG(e.target.result, w, h, levels, responsive);
                    } else {
                        svgStr = generateEmbedSVG(e.target.result, w, h, responsive);
                    }

                    var blob    = new Blob([svgStr], { type: 'image/svg+xml' });
                    var blobUrl = URL.createObjectURL(blob);

                    resolve({
                        origName:   file.name,
                        name:       file.name.replace(/\.jpe?g$/i, '') + '.svg',
                        svgStr:     svgStr,
                        blobUrl:    blobUrl,
                        origSize:   file.size,
                        svgSize:    blob.size,
                        width:      w,
                        height:     h,
                        previewSrc: e.target.result,
                        mode:       svgMode,
                    });
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    // ── Convert button ───────────────────────────────────────────────
    btnConv.addEventListener('click', async function () {
        if (!files.length) return;

        var prefix   = document.getElementById('tc-j2s-opt-prefix').checked ? 'converted_' : '';
        var convWrap = document.getElementById('tc-j2s-conv-progress');
        var convBar  = document.getElementById('tc-j2s-conv-bar');
        var convPct  = document.getElementById('tc-j2s-conv-pct');
        var convLbl  = document.getElementById('tc-j2s-conv-label');

        btnConv.disabled    = true;
        btnConv.textContent = '⏳ Converting…';
        convWrap.style.display = 'block';
        results = [];
        grid.innerHTML = '';

        var totalSize = 0;

        for (var i = 0; i < files.length; i++) {
            var r = await convertFile(files[i]);
            r.name = prefix + r.name;
            results.push(r);
            totalSize += r.svgSize;

            var pct = Math.round(((i + 1) / files.length) * 100);
            convBar.style.width    = pct + '%';
            convPct.textContent    = pct + '%';
            convLbl.textContent    = 'Converting ' + (i + 1) + ' of ' + files.length + '…';
            document.getElementById('tc-j2s-stat-converted').textContent = i + 1;
        }

        setTimeout(function () { convWrap.style.display = 'none'; convBar.style.width = '0%'; }, 600);

        document.getElementById('tc-j2s-stat-size').textContent = (totalSize / 1024).toFixed(1) + ' KB';
        document.getElementById('tc-j2s-stat-mode').textContent = svgMode;
        renderCards(results);

        // Save to IndexedDB (svgStr is the text, safe to store)
        cacheSet('session', {
            mode: svgMode,
            results: results.map(function (r) {
                return {
                    origName: r.origName, name: r.name, svgStr: r.svgStr,
                    origSize: r.origSize, svgSize: r.svgSize,
                    width: r.width, height: r.height,
                    previewSrc: r.previewSrc, mode: r.mode,
                };
            })
        });

        if (btnDlAll) btnDlAll.style.display = 'inline-flex';
        btnConv.disabled    = false;
        btnConv.textContent = '🖼️ Convert to SVG';
    });

    function renderCards(res) {
        grid.innerHTML = '';
        res.forEach(function (r) {
            // Recreate blob URL from svgStr if needed (cache restore)
            var dlUrl = r.blobUrl;
            if (!dlUrl || dlUrl.startsWith('blob:') === false) {
                var b = new Blob([r.svgStr], { type: 'image/svg+xml' });
                dlUrl = URL.createObjectURL(b);
                r.blobUrl = dlUrl;
            }
            var savings = r.origSize > 0
                ? ((1 - r.svgSize / r.origSize) * 100).toFixed(1)
                : '—';
            var savingsColour = parseFloat(savings) > 0 ? '#22c55e' : '#b45309';
            var savingsLabel  = parseFloat(savings) > 0
                ? '▼ ' + savings + '% smaller'
                : '▲ ' + Math.abs(parseFloat(savings)) + '% larger (embed mode)';

            var modeLabel = r.mode === 'traced' ? '🎨 Posterised' : '📎 Embedded';

            var card = document.createElement('div');
            card.className = 'tc-result-card';
            card.innerHTML =
                '<img src="' + r.previewSrc + '" alt="' + r.name + '" '
                + 'class="tc-card-img-full tc-bg-white">'
                + '<p class="tc-text-11 tc-font-semibold tc-text-primary tc-text-ellipsis tc-m-0 tc-mb-4" title="' + r.name + '">' + r.name + '</p>'
                + '<p class="tc-text-10 tc-text-secondary tc-m-0 tc-mb-2">' + r.width + '×' + r.height + '</p>'
                + '<p class="tc-text-10 tc-text-secondary tc-m-0 tc-mb-4">'
                +   (r.origSize / 1024).toFixed(1) + ' KB → ' + (r.svgSize / 1024).toFixed(1) + ' KB'
                + '</p>'
                + '<p class="tc-text-10 tc-m-0 tc-mb-4" style="color:' + savingsColour + '">' + savingsLabel + '</p>'
                + '<p class="tc-text-10 tc-text-secondary tc-m-0 tc-mb-8">' + modeLabel + '</p>'
                + '<a href="' + dlUrl + '" download="' + r.name + '" class="tc-btn tc-btn--primary tc-card-dl-btn">⬇️ Download</a>';
            grid.appendChild(card);
        });
        document.getElementById('tc-j2s-cards').style.display = 'block';
    }

    // ── Download All as ZIP ──────────────────────────────────────────
    if (btnDlAll) {
        btnDlAll.addEventListener('click', async function () {
            if (!results.length) return;
            if (typeof JSZip === 'undefined') {
                alert('JSZip is still loading — please try again in a moment.');
                return;
            }
            btnDlAll.disabled    = true;
            btnDlAll.textContent = '⏳ Zipping…';

            var zip = new JSZip();
            results.forEach(function (r) {
                zip.file(r.name, r.svgStr);
            });

            var blob = await zip.generateAsync({ type: 'blob' });
            var a    = document.createElement('a');
            a.href     = URL.createObjectURL(blob);
            a.download = 'converted-svg-images.zip';
            a.click();

            btnDlAll.disabled    = false;
            btnDlAll.textContent = '📦 Download All (ZIP)';
        });
    }

    // ── Clear ────────────────────────────────────────────────────────
    document.getElementById('tc-j2s-clear').addEventListener('click', function () {
        files   = [];
        results = [];
        fileInp.value         = '';
        grid.innerHTML        = '';
        previewGrid.innerHTML = '';
        document.getElementById('tc-j2s-cards').style.display    = 'none';
        document.getElementById('tc-j2s-previews').style.display = 'none';
        document.getElementById('tc-j2s-upload-progress').style.display = 'none';
        document.getElementById('tc-j2s-conv-progress').style.display   = 'none';
        document.getElementById('tc-j2s-stat-loaded').textContent    = '0';
        document.getElementById('tc-j2s-stat-converted').textContent = '0';
        document.getElementById('tc-j2s-stat-size').textContent      = '—';
        document.getElementById('tc-j2s-stat-mode').textContent      = '—';
        btnConv.disabled = true;
        if (btnDlAll) btnDlAll.style.display = 'none';
        cacheClear();
    });
})();
JS
        );
    }
}