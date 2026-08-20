<?php
/**
 * Widget: JPG to PNG Converter
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;

class Widget_Jpg_To_Png extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_jpg_to_png'; }
    public function get_title(): string { return esc_html__( 'JPG to PNG Converter', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-image-box'; }
    protected function render_tool_content( array $settings ): void {

        // Drop zone
        echo '<div id="tc-j2p-drop" role="button" tabindex="0" aria-label="' . esc_attr__( 'Upload JPG images — click to browse or drag and drop to convert to PNG online', 'textcraft-tools' ) . '" '
           . 'class="tc-drop-zone">';
        echo '<div class="tc-drop-icon">📸</div>';
        echo '<p class="tc-drop-title">' . esc_html__( 'Upload your JPG images — click to browse or drag and drop', 'textcraft-tools' ) . '</p>';
        echo '<p class="tc-drop-desc">' . esc_html__( 'Convert JPG images to PNG online for free. Up to 20 JPEG photos preserve transparency with browser-based conversion — no uploads needed.', 'textcraft-tools' ) . '</p>';
        echo '<input type="file" id="tc-j2p-upload" accept="image/jpeg,image/jpg" multiple class="tc-d-none">';
        echo '</div>';

        // Upload progress bar (shown while reading files)
        echo '<div id="tc-j2p-upload-progress" class="tc-progress-wrap">';
        echo '<div class="tc-d-flex tc-justify-between tc-mb-6">';
        echo '<span id="tc-j2p-upload-label" class="tc-progress-label">' . esc_html__( 'Loading images…', 'textcraft-tools' ) . '</span>';
        echo '<span id="tc-j2p-upload-pct" class="tc-progress-pct">0%</span>';
        echo '</div>';
        echo '<div class="tc-progress-bg">';
        echo '<div id="tc-j2p-upload-bar" class="tc-progress-fill tc-progress-fill--pink"></div>';
        echo '</div>';
        echo '</div>';

        // Conversion progress bar
        echo '<div id="tc-j2p-conv-progress" class="tc-progress-wrap">';
        echo '<div class="tc-d-flex tc-justify-between tc-mb-6">';
        echo '<span id="tc-j2p-conv-label" class="tc-progress-label">' . esc_html__( 'Converting…', 'textcraft-tools' ) . '</span>';
        echo '<span id="tc-j2p-conv-pct" class="tc-progress-pct">0%</span>';
        echo '</div>';
        echo '<div class="tc-progress-bg">';
        echo '<div id="tc-j2p-conv-bar" class="tc-progress-fill tc-progress-fill--pink"></div>';
        echo '</div>';
        echo '</div>';

        // Inline previews of loaded images (before conversion)
        echo '<div id="tc-j2p-previews" class="tc-preview-wrap">';
        echo '<div class="tc-label-row tc-mb-8"><span class="tc-label">' . esc_html__( 'Loaded Images', 'textcraft-tools' ) . '</span></div>';
        echo '<div id="tc-j2p-preview-grid" class="tc-grid-nato"></div>';
        echo '</div>';

        // Options
        echo '<div class="tc-settings-grid">';

        echo '<div>';
        echo '<label class="tc-label tc-d-block tc-mb-8">' . esc_html__( 'Output Compression Level (0 = largest file, 9 = smallest file)', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-items-center tc-gap-10">';
        echo '<input type="range" id="tc-j2p-compression" min="0" max="9" value="6" class="tc-slider">';
        echo '<span id="tc-j2p-comp-val" class="tc-text-14 tc-accent-value tc-min-w-20">6</span>';
        echo '</div>';
        echo '</div>';

        echo '<div>';
        echo '<label class="tc-label tc-d-block tc-mb-8">' . esc_html__( 'Options', 'textcraft-tools' ) . '</label>';
        $this->render_options_row( [
            [ 'id' => 'tc-j2p-opt-max-res', 'label' => esc_html__( 'Keep original resolution',            'textcraft-tools' ), 'checked' => true  ],
            [ 'id' => 'tc-j2p-prefix',      'label' => esc_html__( 'Add "snapconvert_" prefix to filenames','textcraft-tools' ), 'checked' => false ],
        ] );
        echo '</div>';

        echo '</div>'; // end options grid

        // Action buttons
        $this->render_button_row( [
            [ 'id' => 'tc-j2p-convert',      'label' => '🔄 ' . esc_html__( 'Convert All to PNG', 'textcraft-tools' ), 'variant' => 'primary', 'disabled' => true ],
            [ 'id' => 'tc-j2p-download-all', 'label' => '📦 ' . esc_html__( 'Download All (ZIP)', 'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-j2p-clear',        'label' => '🗑️ ' . esc_html__( 'Clear All',          'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        // Stats bar
        $this->render_stat_bar( [
            [ 'id' => 'tc-j2p-stat-loaded',    'label' => esc_html__( 'Files Loaded', 'textcraft-tools' ) ],
            [ 'id' => 'tc-j2p-stat-converted', 'label' => esc_html__( 'Converted',   'textcraft-tools' ) ],
            [ 'id' => 'tc-j2p-stat-size',      'label' => esc_html__( 'Total Size',  'textcraft-tools' ) ],
        ] );

        // Results grid
        echo '<div id="tc-j2p-cards" class="tc-results-wrap">';
        echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'Converted Files', 'textcraft-tools' ) . '</span></div>';
        echo '<div id="tc-j2p-grid" class="tc-grid-cards"></div>';
        echo '</div>';

        echo '<canvas id="tc-j2p-canvas" class="tc-d-none"></canvas>';

        // JSZip for Download All
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" defer crossorigin="anonymous"></script>';

        // Inline JS
        $this->render_inline_script( <<<'JS'
(function () {
    var drop        = document.getElementById('tc-j2p-drop');
    var fileInp     = document.getElementById('tc-j2p-upload');
    var canvas      = document.getElementById('tc-j2p-canvas');
    var ctx         = canvas.getContext('2d');
    var grid        = document.getElementById('tc-j2p-grid');
    var previewGrid = document.getElementById('tc-j2p-preview-grid');
    var btnConv     = document.getElementById('tc-j2p-convert');
    var btnDlAll    = document.getElementById('tc-j2p-download-all');

    var files       = [];       // File objects
    var results     = [];       // { name, blob, newSize, width, height, previewSrc }
    var DB_NAME     = 'tc_j2p_cache';
    var DB_STORE    = 'images';
    var db          = null;

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
            var tx = d.transaction(DB_STORE, 'readwrite');
            tx.objectStore(DB_STORE).put({ id: key, data: data });
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

        // Rebuild preview grid
        results.forEach(function (r) {
            addPreviewThumb(r.previewSrc, r.origName);
        });
        document.getElementById('tc-j2p-previews').style.display = 'block';

        // Rebuild result cards
        renderCards(results);

        // Update stats
        var totalSize = results.reduce(function (s, r) { return s + r.newSize; }, 0);
        document.getElementById('tc-j2p-stat-loaded').textContent    = results.length;
        document.getElementById('tc-j2p-stat-converted').textContent = results.length;
        document.getElementById('tc-j2p-stat-size').textContent      = (totalSize / 1024).toFixed(1) + ' KB';
        btnConv.disabled = false;
        if (btnDlAll) btnDlAll.style.display = 'inline-flex';
    });

    // ── Drop zone ────────────────────────────────────────────────────
    document.getElementById('tc-j2p-compression').addEventListener('input', function () {
        document.getElementById('tc-j2p-comp-val').textContent = this.value;
    });

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

        var progressWrap = document.getElementById('tc-j2p-upload-progress');
        var bar          = document.getElementById('tc-j2p-upload-bar');
        var pctEl        = document.getElementById('tc-j2p-upload-pct');
        var labelEl      = document.getElementById('tc-j2p-upload-label');

        progressWrap.style.display = 'block';
        var loaded = 0;

        valid.forEach(function (file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                // Show inline preview thumb
                addPreviewThumb(e.target.result, file.name);
                loaded++;
                var pct = Math.round((loaded / valid.length) * 100);
                bar.style.width    = pct + '%';
                pctEl.textContent  = pct + '%';
                labelEl.textContent = 'Loading image ' + loaded + ' of ' + valid.length + '…';

                if (loaded === valid.length) {
                    setTimeout(function () { progressWrap.style.display = 'none'; bar.style.width = '0%'; }, 600);
                }
            };
            reader.readAsDataURL(file);
            files.push(file);
        });

        files = files.slice(0, 20);
        document.getElementById('tc-j2p-stat-loaded').textContent = files.length;
        document.getElementById('tc-j2p-previews').style.display  = 'block';
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
                        // Convert blob to base64 for cache storage
                        var fr = new FileReader();
                        fr.onload = function (ev) {
                            resolve({
                                origName:   file.name,
                                name:       file.name.replace(/\.jpe?g$/i, '') + '.png',
                                blobUrl:    URL.createObjectURL(blob),
                                base64:     ev.target.result,   // stored in cache
                                newSize:    blob.size,
                                width:      img.width,
                                height:     img.height,
                                previewSrc: e.target.result,
                            });
                        };
                        fr.readAsDataURL(blob);
                    }, 'image/png');
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    btnConv.addEventListener('click', async function () {
        if (!files.length) return;

        var prefix   = document.getElementById('tc-j2p-prefix').checked ? 'snapconvert_' : '';
        var convWrap = document.getElementById('tc-j2p-conv-progress');
        var convBar  = document.getElementById('tc-j2p-conv-bar');
        var convPct  = document.getElementById('tc-j2p-conv-pct');
        var convLbl  = document.getElementById('tc-j2p-conv-label');

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
            totalSize += r.newSize;

            var pct = Math.round(((i + 1) / files.length) * 100);
            convBar.style.width    = pct + '%';
            convPct.textContent    = pct + '%';
            convLbl.textContent    = 'Converting ' + (i + 1) + ' of ' + files.length + '…';
            document.getElementById('tc-j2p-stat-converted').textContent = i + 1;
        }

        setTimeout(function () { convWrap.style.display = 'none'; convBar.style.width = '0%'; }, 600);

        document.getElementById('tc-j2p-stat-size').textContent = (totalSize / 1024).toFixed(1) + ' KB';
        renderCards(results);

        // Save to IndexedDB cache (store base64, not blob URLs which expire)
        cacheSet('session', {
            results: results.map(function (r) {
                return { origName: r.origName, name: r.name, base64: r.base64, newSize: r.newSize, width: r.width, height: r.height, previewSrc: r.previewSrc };
            })
        });

        if (btnDlAll) btnDlAll.style.display = 'inline-flex';
        btnConv.disabled    = false;
        btnConv.textContent = '🔄 Convert All to PNG';
    });

    function renderCards(res) {
        grid.innerHTML = '';
        res.forEach(function (r) {
            var src  = r.blobUrl || r.base64;
            var card = document.createElement('div');
            card.className = 'tc-result-card';
            card.innerHTML = '<img src="' + r.previewSrc + '" alt="' + r.name + '" class="tc-card-img-full tc-bg-white">'
                           + '<p class="tc-text-11 tc-font-semibold tc-text-primary tc-text-ellipsis tc-m-0 tc-mb-4" title="' + r.name + '">' + r.name + '</p>'
                           + '<p class="tc-text-10 tc-text-secondary tc-m-0 tc-mb-8">' + r.width + '×' + r.height + ' · ' + (r.newSize / 1024).toFixed(1) + ' KB</p>'
                           + '<a href="' + src + '" download="' + r.name + '" class="tc-btn tc-btn--primary tc-card-dl-btn">⬇️ Download</a>';
            grid.appendChild(card);
        });
        document.getElementById('tc-j2p-cards').style.display = 'block';
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
                // base64 data URL → strip prefix
                var b64 = (r.base64 || '').replace(/^data:image\/png;base64,/, '');
                zip.file(r.name, b64, { base64: true });
            });

            var blob = await zip.generateAsync({ type: 'blob' });
            var a = document.createElement('a');
            a.href     = URL.createObjectURL(blob);
            a.download = 'snapconvert-images.zip';
            a.click();

            btnDlAll.disabled    = false;
            btnDlAll.textContent = '📦 Download All (ZIP)';
        });
    }

    // ── Clear ────────────────────────────────────────────────────────
    document.getElementById('tc-j2p-clear').addEventListener('click', function () {
        files   = [];
        results = [];
        fileInp.value            = '';
        grid.innerHTML           = '';
        previewGrid.innerHTML    = '';
        document.getElementById('tc-j2p-cards').style.display    = 'none';
        document.getElementById('tc-j2p-previews').style.display = 'none';
        document.getElementById('tc-j2p-upload-progress').style.display = 'none';
        document.getElementById('tc-j2p-conv-progress').style.display   = 'none';
        document.getElementById('tc-j2p-stat-loaded').textContent    = '0';
        document.getElementById('tc-j2p-stat-converted').textContent = '0';
        document.getElementById('tc-j2p-stat-size').textContent      = '—';
        btnConv.disabled = true;
        if (btnDlAll) btnDlAll.style.display = 'none';
        cacheClear();
    });
})();
JS
        );
    }
}