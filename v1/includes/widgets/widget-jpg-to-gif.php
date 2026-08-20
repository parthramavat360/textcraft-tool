<?php
/**
 * Widget: JPG to GIF Converter
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;

class Widget_Jpg_To_Gif extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_jpg_to_gif'; }
    public function get_title(): string { return esc_html__( 'JPG to GIF Converter', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-image'; }
    protected function render_tool_content( array $settings ): void {

        // Drop zone
        echo '<div id="tc-j2g-drop" role="button" tabindex="0" aria-label="' . esc_attr__( 'Upload JPG images — click to browse or drag and drop to convert to GIF online', 'textcraft-tools' ) . '" '
           . 'class="tc-drop-zone">';
        echo '<div class="tc-drop-icon">🎞️</div>';
        echo '<p class="tc-drop-title">' . esc_html__( 'Upload your JPG images — click to browse or drag and drop', 'textcraft-tools' ) . '</p>';
        echo '<p class="tc-drop-desc">' . esc_html__( 'Convert JPG images to GIF online for free. Turn JPEG photos into static GIFs or animated sequences — all processed in your browser.', 'textcraft-tools' ) . '</p>';
        echo '<p class="tc-drop-desc-sm">' . esc_html__( 'Single images become static GIFs — multiple images create an animated GIF. All processing stays on your device.', 'textcraft-tools' ) . '</p>';
        echo '<input type="file" id="tc-j2g-upload" accept="image/jpeg,image/jpg" multiple class="tc-d-none">';
        echo '</div>';

        // Upload progress bar
        echo '<div id="tc-j2g-upload-progress" class="tc-progress-wrap">';
        echo '<div class="tc-d-flex tc-justify-between tc-mb-6">';
        echo '<span id="tc-j2g-upload-label" class="tc-progress-label">' . esc_html__( 'Loading images…', 'textcraft-tools' ) . '</span>';
        echo '<span id="tc-j2g-upload-pct" class="tc-progress-pct">0%</span>';
        echo '</div>';
        echo '<div class="tc-progress-bg">';
        echo '<div id="tc-j2g-upload-bar" class="tc-progress-fill tc-progress-fill--pink"></div>';
        echo '</div>';
        echo '</div>';

        // Conversion progress bar
        echo '<div id="tc-j2g-conv-progress" class="tc-progress-wrap">';
        echo '<div class="tc-d-flex tc-justify-between tc-mb-6">';
        echo '<span id="tc-j2g-conv-label" class="tc-progress-label">' . esc_html__( 'Converting…', 'textcraft-tools' ) . '</span>';
        echo '<span id="tc-j2g-conv-pct" class="tc-progress-pct">0%</span>';
        echo '</div>';
        echo '<div class="tc-progress-bg">';
        echo '<div id="tc-j2g-conv-bar" class="tc-progress-fill tc-progress-fill--pink"></div>';
        echo '</div>';
        echo '</div>';

        // Inline previews of loaded images
        echo '<div id="tc-j2g-previews" class="tc-preview-wrap">';
        echo '<div class="tc-label-row tc-mb-8"><span class="tc-label">' . esc_html__( 'Loaded Images', 'textcraft-tools' ) . '</span></div>';
        echo '<div id="tc-j2g-preview-grid" class="tc-grid-nato"></div>';
        echo '</div>';

        // Options grid
        echo '<div class="tc-settings-grid">';

        // Output mode
        echo '<div>';
        echo '<label class="tc-label tc-d-block tc-mb-8">' . esc_html__( 'Output Mode', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap" id="tc-j2g-mode-group">';
        $modes = [
            'individual' => [ 'label' => __( 'Individual GIFs', 'textcraft-tools' ), 'sub' => __( 'One GIF per image', 'textcraft-tools' ) ],
            'animated'   => [ 'label' => __( 'Animated GIF',    'textcraft-tools' ), 'sub' => __( 'All frames in one', 'textcraft-tools' ) ],
        ];
        $first = true;
        foreach ( $modes as $val => $info ) {
            $active  = $first ? ' tc-btn-active' : '';
            $variant = $first ? 'primary' : 'secondary';
            echo '<button class="tc-btn tc-btn--' . esc_attr( $variant ) . ' tc-j2g-mode-btn' . $active . '" data-mode="' . esc_attr( $val ) . '">';
            echo esc_html( $info['label'] ) . '<br><small>' . esc_html( $info['sub'] ) . '</small>';
            echo '</button>';
            $first = false;
        }
        echo '</div>';
        echo '</div>';

        // Colour depth
        echo '<div>';
        echo '<label class="tc-label tc-d-block tc-mb-8">' . esc_html__( 'Colour Depth (GIF palette)', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap" id="tc-j2g-depth-group">';
        $depths = [ '32' => '32', '64' => '64', '128' => '128', '256' => '256 Max' ];
        $first  = true;
        foreach ( $depths as $val => $label ) {
            $active  = $val === '256' ? ' tc-btn-active' : '';
            $variant = $val === '256' ? 'primary' : 'secondary';
            echo '<button class="tc-btn tc-btn--' . esc_attr( $variant ) . ' tc-j2g-depth-btn' . $active . '" data-depth="' . esc_attr( $val ) . '">' . esc_html( $label ) . '</button>';
            $first = false;
        }
        echo '</div>';
        echo '<p class="tc-info-text">' . esc_html__( 'GIF supports a maximum of 256 colors — fewer colors produce smaller file sizes. Choose the lowest acceptable quality for best compression.', 'textcraft-tools' ) . '</p>';
        echo '</div>';

        echo '</div>'; // end top options grid

        // Animated GIF options (shown only in animated mode)
        echo '<div id="tc-j2g-anim-opts" class="tc-card-surface tc-p-16 tc-mb-20 tc-hidden">';
        echo '<p class="tc-label tc-mt-0 tc-mb-12">' . esc_html__( 'Animated GIF Settings', 'textcraft-tools' ) . '</p>';
        echo '<div class="tc-grid-settings tc-mb-0">';

        echo '<div>';
        echo '<label class="tc-label tc-d-block tc-mb-6">' . esc_html__( 'Frame Delay in Milliseconds', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-items-center tc-gap-10">';
        echo '<input type="range" id="tc-j2g-delay" min="50" max="2000" step="50" value="500" class="tc-slider">';
        echo '<span id="tc-j2g-delay-val" class="tc-text-13 tc-accent-value tc-min-w-48">500ms</span>';
        echo '</div>';
        echo '</div>';

        echo '<div>';
        echo '<label class="tc-label tc-d-block tc-mb-6">' . esc_html__( 'Loop Count', 'textcraft-tools' ) . '</label>';
        echo '<select id="tc-j2g-loop" class="tc-text-input">';
        echo '<option value="0">' . esc_html__( 'Loop forever',   'textcraft-tools' ) . '</option>';
        echo '<option value="1">' . esc_html__( 'Play once',      'textcraft-tools' ) . '</option>';
        echo '<option value="3">' . esc_html__( 'Play 3 times',   'textcraft-tools' ) . '</option>';
        echo '<option value="5">' . esc_html__( 'Play 5 times',   'textcraft-tools' ) . '</option>';
        echo '<option value="10">' . esc_html__( 'Play 10 times', 'textcraft-tools' ) . '</option>';
        echo '</select>';
        echo '</div>';

        echo '</div>'; // end anim grid
        echo '</div>'; // end anim opts panel

        // Extra options row
        echo '<div class="tc-p-14-16 tc-card-surface tc-mb-20">';
        $this->render_options_row( [
            [ 'id' => 'tc-j2g-opt-prefix',  'label' => esc_html__( 'Add "converted_" prefix to filenames', 'textcraft-tools' ), 'checked' => false ],
            [ 'id' => 'tc-j2g-opt-dither',  'label' => esc_html__( 'Enable dithering (better gradients)',   'textcraft-tools' ), 'checked' => true  ],
        ] );
        echo '</div>';

        // Action buttons
        $this->render_button_row( [
            [ 'id' => 'tc-j2g-convert',      'label' => '🎞️ ' . esc_html__( 'Convert to GIF',    'textcraft-tools' ), 'variant' => 'primary', 'disabled' => true ],
            [ 'id' => 'tc-j2g-download-all', 'label' => '📦 ' . esc_html__( 'Download All (ZIP)', 'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-j2g-clear',        'label' => '🗑️ ' . esc_html__( 'Clear All',          'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        // Stats bar
        $this->render_stat_bar( [
            [ 'id' => 'tc-j2g-stat-loaded',    'label' => esc_html__( 'Files Loaded', 'textcraft-tools' ) ],
            [ 'id' => 'tc-j2g-stat-converted', 'label' => esc_html__( 'Converted',   'textcraft-tools' ) ],
            [ 'id' => 'tc-j2g-stat-size',      'label' => esc_html__( 'Total Size',  'textcraft-tools' ) ],
            [ 'id' => 'tc-j2g-stat-mode',      'label' => esc_html__( 'Mode',        'textcraft-tools' ) ],
        ] );

        // Results grid
        echo '<div id="tc-j2g-cards" class="tc-results-wrap">';
        echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'Converted Files', 'textcraft-tools' ) . '</span></div>';
        echo '<div id="tc-j2g-grid" class="tc-grid-cards"></div>';
        echo '</div>';

        echo '<canvas id="tc-j2g-canvas" class="tc-d-none"></canvas>';

        // JSZip for Download All
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" defer crossorigin="anonymous"></script>';

        // Inline JS
        $this->render_inline_script( <<<'JS'
(function () {
    'use strict';

    var drop        = document.getElementById('tc-j2g-drop');
    var fileInp     = document.getElementById('tc-j2g-upload');
    var canvas      = document.getElementById('tc-j2g-canvas');
    var ctx         = canvas.getContext('2d');
    var grid        = document.getElementById('tc-j2g-grid');
    var previewGrid = document.getElementById('tc-j2g-preview-grid');
    var btnConv     = document.getElementById('tc-j2g-convert');
    var btnDlAll    = document.getElementById('tc-j2g-download-all');

    var files   = [];
    var results = [];
    var gifMode = 'individual';
    var palette = 256;

    var DB_NAME  = 'tc_j2g_cache';
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
        document.getElementById('tc-j2g-previews').style.display = 'block';
        renderCards(results);

        var totalSize = results.reduce(function (s, r) { return s + (r.gifSize || 0); }, 0);
        document.getElementById('tc-j2g-stat-loaded').textContent    = results.length;
        document.getElementById('tc-j2g-stat-converted').textContent = results.length;
        document.getElementById('tc-j2g-stat-size').textContent      = (totalSize / 1024).toFixed(1) + ' KB';
        document.getElementById('tc-j2g-stat-mode').textContent      = cached.mode || 'individual';

        btnConv.disabled = false;
        if (btnDlAll) btnDlAll.style.display = 'inline-flex';
    });

    // ── Mode buttons ─────────────────────────────────────────────────
    document.querySelectorAll('.tc-j2g-mode-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.tc-j2g-mode-btn').forEach(function (b) {
                b.classList.remove('tc-btn-active', 'tc-btn--primary');
                b.classList.add('tc-btn--secondary');
            });
            btn.classList.add('tc-btn-active', 'tc-btn--primary');
            btn.classList.remove('tc-btn--secondary');
            gifMode = btn.getAttribute('data-mode');
            document.getElementById('tc-j2g-anim-opts').style.display = gifMode === 'animated' ? 'block' : 'none';
        });
    });

    // ── Colour depth buttons ─────────────────────────────────────────
    document.querySelectorAll('.tc-j2g-depth-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.tc-j2g-depth-btn').forEach(function (b) {
                b.classList.remove('tc-btn-active', 'tc-btn--primary');
                b.classList.add('tc-btn--secondary');
            });
            btn.classList.add('tc-btn-active', 'tc-btn--primary');
            btn.classList.remove('tc-btn--secondary');
            palette = parseInt(btn.getAttribute('data-depth'));
        });
    });

    // Frame delay slider
    document.getElementById('tc-j2g-delay').addEventListener('input', function () {
        document.getElementById('tc-j2g-delay-val').textContent = this.value + 'ms';
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

        var progressWrap = document.getElementById('tc-j2g-upload-progress');
        var bar          = document.getElementById('tc-j2g-upload-bar');
        var pctEl        = document.getElementById('tc-j2g-upload-pct');
        var labelEl      = document.getElementById('tc-j2g-upload-label');

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
        document.getElementById('tc-j2g-stat-loaded').textContent = files.length;
        document.getElementById('tc-j2g-previews').style.display  = 'block';
        btnConv.disabled = false;
    }

    function addPreviewThumb(src, name) {
        var thumb = document.createElement('div');
        thumb.className = 'tc-text-center';
        thumb.innerHTML = '<img src="' + src + '" alt="' + name + '" class="tc-conv-thumb">'
            + '<p class="tc-text-10 tc-text-muted tc-mt-4 tc-text-ellipsis">' + name + '</p>';
        previewGrid.appendChild(thumb);
    }

    // ── GIF encoder (pure JS, no library) ───────────────────────────
    // Minimal GIF89a encoder — supports static and animated GIFs
    // Uses median-cut palette quantisation per frame

    function clamp(v, lo, hi) { return v < lo ? lo : v > hi ? hi : v; }

    // Median-cut colour quantisation
    function quantise(pixels, maxColours) {
        // Build initial bucket with all pixels
        var buckets = [{ pixels: pixels }];

        while (buckets.length < maxColours) {
            // Find largest bucket by range
            var largest = 0, largestRange = -1;
            for (var b = 0; b < buckets.length; b++) {
                var bkt = buckets[b];
                var rMin = 255, rMax = 0, gMin = 255, gMax = 0, bMin = 255, bMax = 0;
                for (var p = 0; p < bkt.pixels.length; p++) {
                    var px = bkt.pixels[p];
                    var r = px >> 16 & 0xff, g = px >> 8 & 0xff, bv = px & 0xff;
                    if (r < rMin) rMin = r; if (r > rMax) rMax = r;
                    if (g < gMin) gMin = g; if (g > gMax) gMax = g;
                    if (bv < bMin) bMin = bv; if (bv > bMax) bMax = bv;
                }
                var range = Math.max(rMax - rMin, gMax - gMin, bMax - bMin);
                if (range > largestRange) { largestRange = range; largest = b; }
            }

            var bkt = buckets[largest];
            if (bkt.pixels.length < 2) break;

            // Find dominant channel
            var rMin = 255, rMax = 0, gMin = 255, gMax = 0, bMin = 255, bMax = 0;
            for (var p = 0; p < bkt.pixels.length; p++) {
                var px = bkt.pixels[p];
                var r = px >> 16 & 0xff, g = px >> 8 & 0xff, bv = px & 0xff;
                if (r < rMin) rMin = r; if (r > rMax) rMax = r;
                if (g < gMin) gMin = g; if (g > gMax) gMax = g;
                if (bv < bMin) bMin = bv; if (bv > bMax) bMax = bv;
            }
            var rR = rMax - rMin, gR = gMax - gMin, bR = bMax - bMin;
            var ch = rR >= gR && rR >= bR ? 0 : gR >= bR ? 1 : 2;

            bkt.pixels.sort(function (a, z) {
                var av = ch === 0 ? (a >> 16 & 0xff) : ch === 1 ? (a >> 8 & 0xff) : (a & 0xff);
                var zv = ch === 0 ? (z >> 16 & 0xff) : ch === 1 ? (z >> 8 & 0xff) : (z & 0xff);
                return av - zv;
            });

            var mid = Math.floor(bkt.pixels.length / 2);
            var a   = { pixels: bkt.pixels.slice(0, mid) };
            var bb  = { pixels: bkt.pixels.slice(mid) };
            buckets.splice(largest, 1, a, bb);
        }

        // Average each bucket → palette entry
        return buckets.map(function (bkt) {
            var rS = 0, gS = 0, bS = 0, n = bkt.pixels.length;
            for (var p = 0; p < n; p++) {
                rS += bkt.pixels[p] >> 16 & 0xff;
                gS += bkt.pixels[p] >> 8  & 0xff;
                bS += bkt.pixels[p]       & 0xff;
            }
            return [Math.round(rS / n), Math.round(gS / n), Math.round(bS / n)];
        });
    }

    // Map each pixel to nearest palette index (Euclidean distance)
    function mapPixels(imageData, pal, dither) {
        var w = imageData.width, h = imageData.height;
        var data   = imageData.data;
        var errors = new Float32Array(w * h * 3); // for Floyd-Steinberg
        var indices = new Uint8Array(w * h);

        for (var y = 0; y < h; y++) {
            for (var x = 0; x < w; x++) {
                var idx = (y * w + x) * 4;
                var ei  = (y * w + x) * 3;
                var r = clamp(data[idx]     + Math.round(errors[ei]),     0, 255);
                var g = clamp(data[idx + 1] + Math.round(errors[ei + 1]), 0, 255);
                var b = clamp(data[idx + 2] + Math.round(errors[ei + 2]), 0, 255);

                // Find nearest
                var best = 0, bestDist = Infinity;
                for (var c = 0; c < pal.length; c++) {
                    var dr = r - pal[c][0], dg = g - pal[c][1], db = b - pal[c][2];
                    var dist = dr * dr + dg * dg + db * db;
                    if (dist < bestDist) { bestDist = dist; best = c; }
                }
                indices[y * w + x] = best;

                if (dither) {
                    var er = r - pal[best][0], eg = g - pal[best][1], eb = b - pal[best][2];
                    // Floyd-Steinberg diffusion
                    function spread(ox, oy, factor) {
                        var nx = x + ox, ny = y + oy;
                        if (nx < 0 || nx >= w || ny < 0 || ny >= h) return;
                        var ni = (ny * w + nx) * 3;
                        errors[ni]     += er * factor;
                        errors[ni + 1] += eg * factor;
                        errors[ni + 2] += eb * factor;
                    }
                    spread(1,  0, 7/16);
                    spread(-1, 1, 3/16);
                    spread(0,  1, 5/16);
                    spread(1,  1, 1/16);
                }
            }
        }
        return indices;
    }

    // LZW encoder
    function lzwEncode(indices, minCodeSize) {
        var clearCode = 1 << minCodeSize;
        var eofCode   = clearCode + 1;
        var codeSize  = minCodeSize + 1;
        var maxCode   = 1 << codeSize;
        var table     = {};
        var output    = [];
        var buf       = 0, bufBits = 0;

        function emit(code) {
            buf |= code << bufBits;
            bufBits += codeSize;
            while (bufBits >= 8) { output.push(buf & 0xff); buf >>= 8; bufBits -= 8; }
        }

        // Init table
        function initTable() {
            table = {};
            for (var i = 0; i < clearCode; i++) table[String(i)] = i;
            var next = eofCode + 1;
            codeSize = minCodeSize + 1;
            maxCode  = 1 << codeSize;
            return next;
        }

        emit(clearCode);
        var nextCode = initTable();
        var prefix   = String(indices[0]);

        for (var i = 1; i < indices.length; i++) {
            var ch  = String(indices[i]);
            var key = prefix + ',' + ch;
            if (table[key] !== undefined) {
                prefix = key;
            } else {
                emit(table[prefix]);
                table[key] = nextCode++;
                if (nextCode > maxCode) {
                    if (codeSize < 12) { codeSize++; maxCode <<= 1; }
                    else { emit(clearCode); nextCode = initTable(); }
                }
                prefix = ch;
            }
        }
        emit(table[prefix]);
        emit(eofCode);
        if (bufBits > 0) output.push(buf & 0xff);
        return output;
    }

    // Pack LZW data into sub-blocks (max 255 bytes each)
    function packBlocks(lzwData) {
        var out = [];
        var i = 0;
        while (i < lzwData.length) {
            var chunk = lzwData.slice(i, i + 255);
            out.push(chunk.length);
            for (var j = 0; j < chunk.length; j++) out.push(chunk[j]);
            i += 255;
        }
        out.push(0); // block terminator
        return out;
    }

    // Build complete GIF binary from one or more frames
    function buildGIF(frames, loopCount) {
        // frames: [{ pal, indices, w, h, delay }]
        var isAnim   = frames.length > 1;
        var palSize  = frames[0].pal.length;
        // Find smallest power-of-2 palette size ≥ palSize
        var palBits  = 1;
        while ((1 << palBits) < palSize) palBits++;
        palBits = Math.max(palBits, 2);

        var bytes = [];

        // Header
        'GIF89a'.split('').forEach(function (c) { bytes.push(c.charCodeAt(0)); });

        // Logical Screen Descriptor (use first frame dimensions)
        var W = frames[0].w, H = frames[0].h;
        bytes.push(W & 0xff, W >> 8, H & 0xff, H >> 8);
        bytes.push(0x80 | (palBits - 1)); // Global CT flag, palBits-1
        bytes.push(0); // background colour index
        bytes.push(0); // pixel aspect ratio

        // Global Colour Table (use first frame palette, padded to power of 2)
        var pal = frames[0].pal;
        for (var i = 0; i < (1 << palBits); i++) {
            var c = i < pal.length ? pal[i] : [0, 0, 0];
            bytes.push(c[0], c[1], c[2]);
        }

        // Netscape Application Extension (for looping animated GIFs)
        if (isAnim) {
            bytes.push(0x21, 0xff, 0x0b);
            'NETSCAPE2.0'.split('').forEach(function (c) { bytes.push(c.charCodeAt(0)); });
            bytes.push(3, 1, loopCount & 0xff, loopCount >> 8, 0);
        }

        // Frames
        frames.forEach(function (fr) {
            // Graphic Control Extension (for animation delay)
            if (isAnim) {
                var delayCentisecs = Math.round(fr.delay / 10);
                bytes.push(0x21, 0xf9, 0x04);
                bytes.push(0x00); // disposal method
                bytes.push(delayCentisecs & 0xff, delayCentisecs >> 8);
                bytes.push(0x00); // transparent colour index
                bytes.push(0x00); // block terminator
            }

            // Image Descriptor
            bytes.push(0x2c);
            bytes.push(0, 0, 0, 0); // left, top = 0
            bytes.push(fr.w & 0xff, fr.w >> 8, fr.h & 0xff, fr.h >> 8);
            bytes.push(0x00); // no local colour table

            // Minimum LZW code size
            var minCodeSize = Math.max(palBits, 2);
            bytes.push(minCodeSize);

            // LZW-compressed image data
            var lzwData = lzwEncode(fr.indices, minCodeSize);
            var packed  = packBlocks(lzwData);
            packed.forEach(function (b) { bytes.push(b); });
        });

        // Trailer
        bytes.push(0x3b);

        return new Uint8Array(bytes);
    }

    // Convert a single File to a GIF frame descriptor
    function fileToFrame(file, targetW, targetH) {
        return new Promise(function (resolve) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = new Image();
                img.onload = function () {
                    var w = targetW || img.width;
                    var h = targetH || img.height;
                    canvas.width  = w;
                    canvas.height = h;
                    ctx.clearRect(0, 0, w, h);
                    ctx.drawImage(img, 0, 0, w, h);
                    var imageData = ctx.getImageData(0, 0, w, h);

                    // Collect unique pixels for quantisation (sample up to 50k)
                    var data    = imageData.data;
                    var pixMap  = {};
                    var step    = Math.max(1, Math.floor(data.length / (50000 * 4)));
                    for (var i = 0; i < data.length; i += 4 * step) {
                        var packed = (data[i] << 16) | (data[i+1] << 8) | data[i+2];
                        pixMap[packed] = true;
                    }
                    var allPixels = Object.keys(pixMap).map(Number);

                    var pal     = quantise(allPixels, palette);
                    var dither  = document.getElementById('tc-j2g-opt-dither').checked;
                    var indices = mapPixels(imageData, pal, dither);

                    resolve({
                        pal:        pal,
                        indices:    indices,
                        w:          w,
                        h:          h,
                        previewSrc: e.target.result,
                        origName:   file.name,
                        origSize:   file.size,
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

        var prefix   = document.getElementById('tc-j2g-opt-prefix').checked ? 'converted_' : '';
        var delay    = parseInt(document.getElementById('tc-j2g-delay').value) || 500;
        var loopVal  = parseInt(document.getElementById('tc-j2g-loop').value);
        var convWrap = document.getElementById('tc-j2g-conv-progress');
        var convBar  = document.getElementById('tc-j2g-conv-bar');
        var convPct  = document.getElementById('tc-j2g-conv-pct');
        var convLbl  = document.getElementById('tc-j2g-conv-label');

        btnConv.disabled    = true;
        btnConv.textContent = '⏳ Converting…';
        convWrap.style.display = 'block';
        results = [];
        grid.innerHTML = '';

        var totalSize = 0;

        if (gifMode === 'animated') {
            // All files → one animated GIF
            convLbl.textContent = 'Building frames…';
            var frames = [];
            var firstW = null, firstH = null;

            for (var i = 0; i < files.length; i++) {
                var fr = await fileToFrame(files[i], firstW, firstH);
                if (!firstW) { firstW = fr.w; firstH = fr.h; }
                fr.delay = delay;
                frames.push(fr);

                var pct = Math.round(((i + 1) / files.length) * 100);
                convBar.style.width    = pct + '%';
                convPct.textContent    = pct + '%';
                convLbl.textContent    = 'Building frame ' + (i + 1) + ' of ' + files.length + '…';
            }

            convLbl.textContent = 'Encoding GIF…';
            var gifBytes = buildGIF(frames, loopVal);
            var blob     = new Blob([gifBytes], { type: 'image/gif' });
            var blobUrl  = URL.createObjectURL(blob);
            var b64      = await blobToBase64(blob);
            var name     = prefix + 'animated.gif';

            results.push({
                origName:   'animated.gif',
                name:       name,
                base64:     b64,
                blobUrl:    blobUrl,
                gifSize:    blob.size,
                origSize:   files.reduce(function (s, f) { return s + f.size; }, 0),
                width:      firstW,
                height:     firstH,
                previewSrc: frames[0].previewSrc,
                frameCount: frames.length,
                mode:       'animated',
            });

            totalSize = blob.size;
            document.getElementById('tc-j2g-stat-converted').textContent = 1;

        } else {
            // Individual GIFs — one per file
            for (var i = 0; i < files.length; i++) {
                var fr  = await fileToFrame(files[i], null, null);
                fr.delay = 100;
                var gifBytes = buildGIF([fr], 1);
                var blob     = new Blob([gifBytes], { type: 'image/gif' });
                var blobUrl  = URL.createObjectURL(blob);
                var b64      = await blobToBase64(blob);
                var name     = prefix + files[i].name.replace(/\.jpe?g$/i, '') + '.gif';

                results.push({
                    origName:   files[i].name,
                    name:       name,
                    base64:     b64,
                    blobUrl:    blobUrl,
                    gifSize:    blob.size,
                    origSize:   files[i].size,
                    width:      fr.w,
                    height:     fr.h,
                    previewSrc: fr.previewSrc,
                    frameCount: 1,
                    mode:       'individual',
                });

                totalSize += blob.size;
                var pct = Math.round(((i + 1) / files.length) * 100);
                convBar.style.width    = pct + '%';
                convPct.textContent    = pct + '%';
                convLbl.textContent    = 'Converting ' + (i + 1) + ' of ' + files.length + '…';
                document.getElementById('tc-j2g-stat-converted').textContent = i + 1;
            }
        }

        setTimeout(function () { convWrap.style.display = 'none'; convBar.style.width = '0%'; }, 600);

        document.getElementById('tc-j2g-stat-size').textContent = (totalSize / 1024).toFixed(1) + ' KB';
        document.getElementById('tc-j2g-stat-mode').textContent = gifMode;
        renderCards(results);

        // Cache
        cacheSet('session', {
            mode: gifMode,
            results: results.map(function (r) {
                return {
                    origName: r.origName, name: r.name, base64: r.base64,
                    gifSize: r.gifSize, origSize: r.origSize,
                    width: r.width, height: r.height,
                    previewSrc: r.previewSrc, frameCount: r.frameCount, mode: r.mode,
                };
            })
        });

        if (btnDlAll) btnDlAll.style.display = 'inline-flex';
        btnConv.disabled    = false;
        btnConv.textContent = '🎞️ Convert to GIF';
    });

    function blobToBase64(blob) {
        return new Promise(function (resolve) {
            var fr = new FileReader();
            fr.onload = function (e) { resolve(e.target.result); };
            fr.readAsDataURL(blob);
        });
    }

    function renderCards(res) {
        grid.innerHTML = '';
        res.forEach(function (r) {
            var src = r.blobUrl;
            if (!src) {
                var b64data = (r.base64 || '').replace(/^data:image\/gif;base64,/, '');
                var bytes   = atob(b64data);
                var arr     = new Uint8Array(bytes.length);
                for (var i = 0; i < bytes.length; i++) arr[i] = bytes.charCodeAt(i);
                var bl = new Blob([arr], { type: 'image/gif' });
                src = URL.createObjectURL(bl);
                r.blobUrl = src;
            }

            var saving = r.origSize > 0
                ? ((1 - r.gifSize / r.origSize) * 100).toFixed(1)
                : '0';
            var savingColour = parseFloat(saving) > 0 ? '#22c55e' : '#b45309';
            var savingLabel  = parseFloat(saving) > 0
                ? '▼ ' + saving + '% smaller'
                : '▲ ' + Math.abs(parseFloat(saving)).toFixed(1) + '% larger';

            var frameInfo = r.frameCount > 1
                ? '<p class="tc-text-10 tc-m-0 tc-mb-4 tc-text-accent">🎞️ ' + r.frameCount + ' frames</p>'
                : '';

            var card = document.createElement('div');
            card.className = 'tc-result-card';
            card.innerHTML =
                '<img src="' + src + '" alt="' + r.name + '" '
                + 'class="tc-card-img-full tc-bg-white">'
                + '<p class="tc-text-11 tc-font-semibold tc-text-primary tc-text-ellipsis tc-m-0 tc-mb-4" title="' + r.name + '">' + r.name + '</p>'
                + '<p class="tc-text-10 tc-text-secondary tc-m-0 tc-mb-2">' + r.width + '×' + r.height + '</p>'
                + '<p class="tc-text-10 tc-text-secondary tc-m-0 tc-mb-4">'
                +   (r.origSize / 1024).toFixed(1) + ' KB → ' + (r.gifSize / 1024).toFixed(1) + ' KB'
                + '</p>'
                + '<p class="tc-text-10 tc-m-0 tc-mb-4" style="color:' + savingColour + '">' + savingLabel + '</p>'
                + frameInfo
                + '<a href="' + src + '" download="' + r.name + '" class="tc-btn tc-btn--primary tc-card-dl-btn">⬇️ Download</a>';
            grid.appendChild(card);
        });
        document.getElementById('tc-j2g-cards').style.display = 'block';
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
                var b64 = (r.base64 || '').replace(/^data:image\/gif;base64,/, '');
                zip.file(r.name, b64, { base64: true });
            });

            var blob = await zip.generateAsync({ type: 'blob' });
            var a    = document.createElement('a');
            a.href     = URL.createObjectURL(blob);
            a.download = 'converted-gif-images.zip';
            a.click();

            btnDlAll.disabled    = false;
            btnDlAll.textContent = '📦 Download All (ZIP)';
        });
    }

    // ── Clear ────────────────────────────────────────────────────────
    document.getElementById('tc-j2g-clear').addEventListener('click', function () {
        files   = [];
        results = [];
        fileInp.value         = '';
        grid.innerHTML        = '';
        previewGrid.innerHTML = '';
        document.getElementById('tc-j2g-cards').style.display    = 'none';
        document.getElementById('tc-j2g-previews').style.display = 'none';
        document.getElementById('tc-j2g-upload-progress').style.display = 'none';
        document.getElementById('tc-j2g-conv-progress').style.display   = 'none';
        document.getElementById('tc-j2g-stat-loaded').textContent    = '0';
        document.getElementById('tc-j2g-stat-converted').textContent = '0';
        document.getElementById('tc-j2g-stat-size').textContent      = '—';
        document.getElementById('tc-j2g-stat-mode').textContent      = '—';
        btnConv.disabled = true;
        if (btnDlAll) btnDlAll.style.display = 'none';
        cacheClear();
    });
})();
JS
        );
    }
}