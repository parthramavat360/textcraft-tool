<?php
/**
 * Widget: JPG to WebP Converter
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;

class Widget_Jpg_To_Webp extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_jpg_to_webp'; }
    public function get_title(): string { return esc_html__( 'JPG to WebP Converter', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-image-rollover'; }
    protected function render_tool_content( array $settings ): void {

        // Drop zone
        echo '<div id="tc-j2w-drop" role="button" tabindex="0" aria-label="' . esc_attr__( 'Upload JPG images — click to browse or drag and drop to convert to WebP online', 'textcraft-tools' ) . '" '
           . 'class="tc-drop-zone">';
        echo '<div class="tc-drop-icon">⚡</div>';
        echo '<p class="tc-drop-title">' . esc_html__( 'Upload your JPG images — click to browse or drag and drop', 'textcraft-tools' ) . '</p>';
        echo '<p class="tc-text-13 tc-text-muted tc-m-0">' . esc_html__( 'Convert JPG images to WebP online for free. Up to 20 JPEG files convert to modern WebP format — all processed in your browser with no uploads.', 'textcraft-tools' ) . '</p>';
        echo '<input type="file" id="tc-j2w-upload" accept="image/jpeg,image/jpg" multiple class="tc-d-none">';
        echo '</div>';

        // Upload progress bar
        echo '<div id="tc-j2w-upload-progress" class="tc-progress-wrap">';
        echo '<div class="tc-d-flex tc-justify-between tc-mb-6">';
        echo '<span id="tc-j2w-upload-label" class="tc-progress-label">' . esc_html__( 'Loading images…', 'textcraft-tools' ) . '</span>';
        echo '<span id="tc-j2w-upload-pct" class="tc-progress-pct">0%</span>';
        echo '</div>';
        echo '<div class="tc-progress-bg">';
        echo '<div id="tc-j2w-upload-bar" class="tc-progress-fill tc-progress-fill--pink"></div>';
        echo '</div>';
        echo '</div>';

        // Conversion progress bar
        echo '<div id="tc-j2w-conv-progress" class="tc-progress-wrap">';
        echo '<div class="tc-d-flex tc-justify-between tc-mb-6">';
        echo '<span id="tc-j2w-conv-label" class="tc-progress-label">' . esc_html__( 'Converting…', 'textcraft-tools' ) . '</span>';
        echo '<span id="tc-j2w-conv-pct" class="tc-progress-pct">0%</span>';
        echo '</div>';
        echo '<div class="tc-progress-bg">';
        echo '<div id="tc-j2w-conv-bar" class="tc-progress-fill tc-progress-fill--pink"></div>';
        echo '</div>';
        echo '</div>';

        // Inline previews of loaded images
        echo '<div id="tc-j2w-previews" class="tc-preview-wrap">';
        echo '<div class="tc-label-row tc-mb-8"><span class="tc-label">' . esc_html__( 'Loaded Images', 'textcraft-tools' ) . '</span></div>';
        echo '<div id="tc-j2w-preview-grid" class="tc-grid-nato"></div>';
        echo '</div>';

        // Quality slider + presets
        echo '<div class="tc-settings-grid">';

        echo '<div>';
        echo '<label class="tc-label tc-d-block tc-mb-8">' . esc_html__( 'Output WebP Quality', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-items-center tc-gap-10">';
        echo '<input type="range" id="tc-j2w-quality" min="1" max="100" value="82" class="tc-slider">';
        echo '<span id="tc-j2w-qval" class="tc-text-14 tc-accent-value tc-min-w-36">82%</span>';
        echo '</div>';
        echo '<p class="tc-info-text">' . esc_html__( '80–90% is the sweet spot for web use — great balance of quality and file size reduction', 'textcraft-tools' ) . '</p>';
        echo '</div>';

        echo '<div>';
        echo '<label class="tc-label tc-d-block tc-mb-8">' . esc_html__( 'Quick Quality Presets — One-Click Selection', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap" id="tc-j2w-preset-group">';
        $presets = [ '60' => '60% Small', '75' => '75% Good', '82' => '82% Best', '95' => '95% HQ', '100' => '100% Lossless' ];
        foreach ( $presets as $q => $label ) {
            $active  = $q === '82' ? ' tc-btn-active' : '';
            $variant = $q === '82' ? 'primary' : 'secondary';
            echo '<button class="tc-btn tc-btn--' . esc_attr( $variant ) . ' tc-j2w-preset' . $active . '" data-q="' . esc_attr( $q ) . '">' . esc_html( $label ) . '</button>';
        }
        echo '</div>';
        echo '</div>';

        echo '</div>'; // end quality grid

        // Options
        echo '<div class="tc-option-bar tc-p-14-16">';
        $this->render_options_row( [
            [ 'id' => 'tc-j2w-opt-prefix',    'label' => esc_html__( 'Add "swiftwebp_" prefix to filenames', 'textcraft-tools' ), 'checked' => false ],
            [ 'id' => 'tc-j2w-opt-show-save', 'label' => esc_html__( 'Show file size savings',               'textcraft-tools' ), 'checked' => true  ],
        ] );
        echo '</div>';

        // Action buttons
        $this->render_button_row( [
            [ 'id' => 'tc-j2w-convert',      'label' => '⚡ ' . esc_html__( 'Convert to WebP',    'textcraft-tools' ), 'variant' => 'primary', 'disabled' => true ],
            [ 'id' => 'tc-j2w-download-all', 'label' => '📦 ' . esc_html__( 'Download All (ZIP)', 'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-j2w-clear',        'label' => '🗑️ ' . esc_html__( 'Clear All',          'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        // Stats bar
        $this->render_stat_bar( [
            [ 'id' => 'tc-j2w-stat-loaded',    'label' => esc_html__( 'Files Loaded',  'textcraft-tools' ) ],
            [ 'id' => 'tc-j2w-stat-converted', 'label' => esc_html__( 'Converted',     'textcraft-tools' ) ],
            [ 'id' => 'tc-j2w-stat-saved',     'label' => esc_html__( 'Space Saved',   'textcraft-tools' ) ],
            [ 'id' => 'tc-j2w-stat-pct',       'label' => esc_html__( 'Avg Reduction', 'textcraft-tools' ) ],
        ] );

        // Results grid
        echo '<div id="tc-j2w-cards" class="tc-results-wrap">';
        echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'Converted Files', 'textcraft-tools' ) . '</span></div>';
        echo '<div id="tc-j2w-grid" class="tc-grid-cards"></div>';
        echo '</div>';

        echo '<canvas id="tc-j2w-canvas" class="tc-d-none"></canvas>';

        // JSZip for Download All
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" defer crossorigin="anonymous"></script>';

        // Inline JS
        $this->render_inline_script( <<<'JS'
(function () {
    var drop        = document.getElementById('tc-j2w-drop');
    var fileInp     = document.getElementById('tc-j2w-upload');
    var canvas      = document.getElementById('tc-j2w-canvas');
    var ctx         = canvas.getContext('2d');
    var grid        = document.getElementById('tc-j2w-grid');
    var previewGrid = document.getElementById('tc-j2w-preview-grid');
    var btnConv     = document.getElementById('tc-j2w-convert');
    var btnDlAll    = document.getElementById('tc-j2w-download-all');

    var files   = [];
    var results = [];
    var quality = 0.82;

    var DB_NAME  = 'tc_j2w_cache';
    var DB_STORE = 'images';
    var db       = null;

    // Initially hide download-all
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

    // ── Restore from cache on load ───────────────────────────────────
    cacheGet('session', function (cached) {
        if (!cached || !cached.results || !cached.results.length) return;
        results = cached.results;

        results.forEach(function (r) { addPreviewThumb(r.previewSrc, r.origName); });
        document.getElementById('tc-j2w-previews').style.display = 'block';
        renderCards(results);

        var totalOrig = results.reduce(function (s, r) { return s + r.origSize; }, 0);
        var totalNew  = results.reduce(function (s, r) { return s + r.newSize;  }, 0);
        var saved     = totalOrig - totalNew;
        var pct       = totalOrig > 0 ? ((saved / totalOrig) * 100).toFixed(1) : '0.0';

        document.getElementById('tc-j2w-stat-loaded').textContent    = results.length;
        document.getElementById('tc-j2w-stat-converted').textContent = results.length;
        document.getElementById('tc-j2w-stat-saved').textContent     = (saved / 1024).toFixed(1) + ' KB';
        document.getElementById('tc-j2w-stat-pct').textContent       = pct + '%';

        btnConv.disabled = false;
        if (btnDlAll) btnDlAll.style.display = 'inline-flex';
    });

    // ── Quality slider ───────────────────────────────────────────────
    document.getElementById('tc-j2w-quality').addEventListener('input', function () {
        quality = this.value / 100;
        document.getElementById('tc-j2w-qval').textContent = this.value + '%';
    });

    // ── Quality presets ──────────────────────────────────────────────
    document.querySelectorAll('.tc-j2w-preset').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.tc-j2w-preset').forEach(function (b) {
                b.classList.remove('tc-btn-active', 'tc-btn--primary');
                b.classList.add('tc-btn--secondary');
            });
            btn.classList.add('tc-btn-active', 'tc-btn--primary');
            btn.classList.remove('tc-btn--secondary');
            var q = parseInt(btn.getAttribute('data-q'));
            quality = q / 100;
            document.getElementById('tc-j2w-quality').value = q;
            document.getElementById('tc-j2w-qval').textContent = q + '%';
        });
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

        var progressWrap = document.getElementById('tc-j2w-upload-progress');
        var bar          = document.getElementById('tc-j2w-upload-bar');
        var pctEl        = document.getElementById('tc-j2w-upload-pct');
        var labelEl      = document.getElementById('tc-j2w-upload-label');

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
        document.getElementById('tc-j2w-stat-loaded').textContent = files.length;
        document.getElementById('tc-j2w-previews').style.display  = 'block';
        btnConv.disabled = false;
    }

    function addPreviewThumb(src, name) {
        var thumb = document.createElement('div');
        thumb.className = 'tc-text-center';
        thumb.innerHTML = '<img src="' + src + '" alt="' + name + '" class="tc-conv-thumb">'
                        + '<p class="tc-text-10 tc-text-muted tc-mt-4 tc-text-ellipsis">' + name + '</p>';
        previewGrid.appendChild(thumb);
    }

    // ── Convert ──────────────────────────────────────────────────────
    function convertFile(file) {
        return new Promise(function (resolve) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = new Image();
                img.onload = function () {
                    canvas.width  = img.width;
                    canvas.height = img.height;
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    ctx.drawImage(img, 0, 0);
                    canvas.toBlob(function (blob) {
                        var fr = new FileReader();
                        fr.onload = function (ev) {
                            resolve({
                                origName:   file.name,
                                name:       file.name.replace(/\.jpe?g$/i, '') + '.webp',
                                base64:     ev.target.result,
                                blobUrl:    URL.createObjectURL(blob),
                                origSize:   file.size,
                                newSize:    blob.size,
                                width:      img.width,
                                height:     img.height,
                                previewSrc: e.target.result,
                            });
                        };
                        fr.readAsDataURL(blob);
                    }, 'image/webp', quality);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    btnConv.addEventListener('click', async function () {
        if (!files.length) return;

        var prefix    = document.getElementById('tc-j2w-opt-prefix').checked ? 'swiftwebp_' : '';
        var convWrap  = document.getElementById('tc-j2w-conv-progress');
        var convBar   = document.getElementById('tc-j2w-conv-bar');
        var convPct   = document.getElementById('tc-j2w-conv-pct');
        var convLbl   = document.getElementById('tc-j2w-conv-label');

        btnConv.disabled    = true;
        btnConv.textContent = '⏳ Converting…';
        convWrap.style.display = 'block';
        results = [];
        grid.innerHTML = '';

        var totalOrig = 0, totalNew = 0;

        for (var i = 0; i < files.length; i++) {
            var r = await convertFile(files[i]);
            r.name = prefix + r.name;
            results.push(r);
            totalOrig += r.origSize;
            totalNew  += r.newSize;

            var pct = Math.round(((i + 1) / files.length) * 100);
            convBar.style.width    = pct + '%';
            convPct.textContent    = pct + '%';
            convLbl.textContent    = 'Converting ' + (i + 1) + ' of ' + files.length + '…';
            document.getElementById('tc-j2w-stat-converted').textContent = i + 1;
        }

        setTimeout(function () { convWrap.style.display = 'none'; convBar.style.width = '0%'; }, 600);

        var saved    = totalOrig - totalNew;
        var savedPct = totalOrig > 0 ? ((saved / totalOrig) * 100).toFixed(1) : '0.0';
        document.getElementById('tc-j2w-stat-saved').textContent = (saved / 1024).toFixed(1) + ' KB';
        document.getElementById('tc-j2w-stat-pct').textContent   = savedPct + '%';

        renderCards(results);

        // Cache to IndexedDB
        cacheSet('session', {
            results: results.map(function (r) {
                return { origName: r.origName, name: r.name, base64: r.base64, origSize: r.origSize, newSize: r.newSize, width: r.width, height: r.height, previewSrc: r.previewSrc };
            })
        });

        if (btnDlAll) btnDlAll.style.display = 'inline-flex';
        btnConv.disabled    = false;
        btnConv.textContent = '⚡ Convert to WebP';
    });

    function renderCards(res) {
        var showSave = document.getElementById('tc-j2w-opt-show-save')
            ? document.getElementById('tc-j2w-opt-show-save').checked
            : true;
        grid.innerHTML = '';
        res.forEach(function (r) {
            var src     = r.blobUrl || r.base64;
            var saving  = r.origSize > 0 ? ((r.origSize - r.newSize) / r.origSize * 100).toFixed(1) : '0.0';
            var saveEl  = showSave
                ? '<p class="tc-text-10 tc-m-0 tc-mb-6 tc-text-green">▼ ' + saving + '% smaller</p>'
                : '';
            var card = document.createElement('div');
            card.className = 'tc-result-card';
            card.innerHTML = '<img src="' + r.previewSrc + '" alt="' + r.name + '" class="tc-card-img-full tc-bg-white">'
                           + '<p class="tc-text-11 tc-font-semibold tc-text-primary tc-text-ellipsis tc-m-0 tc-mb-4" title="' + r.name + '">' + r.name + '</p>'
                           + '<p class="tc-text-10 tc-text-secondary tc-m-0 tc-mb-2">' + r.width + '×' + r.height + '</p>'
                           + '<p class="tc-text-10 tc-text-muted tc-m-0 tc-mb-6">' + (r.origSize / 1024).toFixed(1) + ' KB → ' + (r.newSize / 1024).toFixed(1) + ' KB</p>'
                           + saveEl
                           + '<a href="' + src + '" download="' + r.name + '" class="tc-btn tc-btn--primary tc-card-dl-btn">⬇️ Download</a>';
            grid.appendChild(card);
        });
        document.getElementById('tc-j2w-cards').style.display = 'block';
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
                var b64 = (r.base64 || '').replace(/^data:image\/webp;base64,/, '');
                zip.file(r.name, b64, { base64: true });
            });

            var blob = await zip.generateAsync({ type: 'blob' });
            var a = document.createElement('a');
            a.href     = URL.createObjectURL(blob);
            a.download = 'swiftwebp-images.zip';
            a.click();

            btnDlAll.disabled    = false;
            btnDlAll.textContent = '📦 Download All (ZIP)';
        });
    }

    // ── Clear ────────────────────────────────────────────────────────
    document.getElementById('tc-j2w-clear').addEventListener('click', function () {
        files   = [];
        results = [];
        fileInp.value = '';
        grid.innerHTML        = '';
        previewGrid.innerHTML = '';
        document.getElementById('tc-j2w-cards').style.display    = 'none';
        document.getElementById('tc-j2w-previews').style.display = 'none';
        document.getElementById('tc-j2w-upload-progress').style.display = 'none';
        document.getElementById('tc-j2w-conv-progress').style.display   = 'none';
        document.getElementById('tc-j2w-stat-loaded').textContent    = '0';
        document.getElementById('tc-j2w-stat-converted').textContent = '0';
        document.getElementById('tc-j2w-stat-saved').textContent     = '—';
        document.getElementById('tc-j2w-stat-pct').textContent       = '—';
        btnConv.disabled = true;
        if (btnDlAll) btnDlAll.style.display = 'none';
        cacheClear();
    });
})();
JS
        );
    }
}