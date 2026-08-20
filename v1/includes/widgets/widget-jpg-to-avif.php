<?php
/**
 * Widget: JPG to AVIF Converter
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;
class Widget_Jpg_To_Avif extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_jpg_to_avif'; }
    public function get_title(): string { return esc_html__( 'JPG to AVIF Converter', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-image'; }
    protected function render_tool_content( array $settings ): void {

        // ── Upload / drop zone ────────────────────────────────────────────
        echo '<div id="tc-ja-drop" class="tc-drop-zone tc-mb-20" role="button" tabindex="0">';
        echo '<div class="tc-text-40 tc-mb-12">🖼️</div>';
echo '<p class="tc-drop-title">' . esc_html__( 'Upload your images — click to browse or drag and drop', 'textcraft-tools' ) . '</p>';
		echo '<p class="tc-text-13 tc-text-muted tc-m-0-10">' . esc_html__( 'Supports JPG, JPEG, PNG, and WebP — converted to AVIF in your browser with nothing uploaded to any server', 'textcraft-tools' ) . '</p>';
        echo '<input type="file" id="tc-ja-upload" accept="image/jpeg,image/jpg,image/png,image/webp" multiple class="tc-d-none">';
        echo '</div>';

        // ── Quality control ───────────────────────────────────────────────
        echo '<div class="tc-grid-settings">';

        echo '<div>';
        echo '<label class="tc-label-upper tc-mb-8">' . esc_html__( 'AVIF Quality', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-flex-row">';
        echo '<input type="range" id="tc-ja-quality" min="10" max="100" value="80" class="tc-slider">';
        echo '<span id="tc-ja-qval" class="tc-text-14 tc-accent-value tc-min-w-36">80%</span>';
        echo '</div>';
        echo '<p class="tc-text-info tc-mt-4">' . esc_html__( 'Higher quality settings preserve more detail but produce larger files. Adjust the slider to find your ideal balance.', 'textcraft-tools' ) . '</p>';
        echo '</div>';

        echo '<div>';
        echo '<label class="tc-label-upper tc-mb-8">' . esc_html__( 'Resize (optional)', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap">';
        $scales = [
            [ 'val' => '100', 'label' => '100%', 'active' => true  ],
            [ 'val' => '75',  'label' => '75%',  'active' => false ],
            [ 'val' => '50',  'label' => '50%',  'active' => false ],
            [ 'val' => '25',  'label' => '25%',  'active' => false ],
        ];
        foreach ( $scales as $s ) {
            $cls = $s['active'] ? 'tc-btn tc-btn--primary tc-ja-scale active' : 'tc-btn tc-btn--ghost tc-ja-scale';
            echo '<button class="' . esc_attr( $cls ) . '" data-scale="' . esc_attr( $s['val'] ) . '">' . esc_html( $s['label'] ) . '</button>';
        }
        echo '</div>';
        echo '</div>';

        echo '</div>'; // end controls grid

        // ── Global progress bar ───────────────────────────────────────────
        echo '<div id="tc-ja-progress-wrap" class="tc-mb-20 tc-hidden">';
        echo '<div class="tc-d-flex tc-justify-between tc-text-12 tc-text-muted tc-mb-6">';
        echo '<span id="tc-ja-progress-label">' . esc_html__( 'Converting…', 'textcraft-tools' ) . '</span>';
        echo '<span id="tc-ja-progress-pct">0%</span>';
        echo '</div>';
        echo '<div class="tc-progress-bg">';
        echo '<div id="tc-ja-progress-bar" class="tc-progress-fill tc-progress-fill--accent"></div>';
        echo '</div>';
        echo '</div>';

        // ── Action buttons ────────────────────────────────────────────────
        $this->render_button_row( [
            [ 'id' => 'tc-ja-convert',      'label' => '⚡ ' . esc_html__( 'Convert All',        'textcraft-tools' ), 'variant' => 'primary' ],
            [ 'id' => 'tc-ja-download-all', 'label' => '📦 ' . esc_html__( 'Download All (ZIP)', 'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-ja-clear-all',    'label' => '🗑️ ' . esc_html__( 'Clear All',          'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        // ── Stats bar ─────────────────────────────────────────────────────
        $this->render_stat_bar( [
            [ 'id' => 'tc-ja-stat-total',     'label' => esc_html__( 'Total Files', 'textcraft-tools' ) ],
            [ 'id' => 'tc-ja-stat-converted', 'label' => esc_html__( 'Converted',   'textcraft-tools' ) ],
            [ 'id' => 'tc-ja-stat-saved',     'label' => esc_html__( 'Size Saved',  'textcraft-tools' ) ],
        ] );

        // ── Image cards grid ──────────────────────────────────────────────
        echo '<div id="tc-ja-grid" class="tc-mt-20 tc-hidden">';
        echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'Images', 'textcraft-tools' ) . '</span></div>';
        echo '<div id="tc-ja-cards" class="tc-grid-cards"></div>';
        echo '</div>';

        // ── Inline JS ─────────────────────────────────────────────────────
        $this->render_inline_script( <<<'JS'
(function(){

    /* ── IndexedDB ───────────────────────────────────────────────── */
    var DB_NAME  = 'tc_ja_cache_v1';
    var DB_STORE = 'avif_images';
    var db       = null;

    function openDB(cb) {
        if (db) { cb(db); return; }
        var req = indexedDB.open(DB_NAME, 1);
        req.onupgradeneeded = function(e){ e.target.result.createObjectStore(DB_STORE, { keyPath: 'id' }); };
        req.onsuccess       = function(e){ db = e.target.result; cb(db); };
        req.onerror         = function(){ cb(null); };
    }
    function dbGet(id, cb) {
        openDB(function(d){ if(!d){cb(null);return;}
            d.transaction(DB_STORE,'readonly').objectStore(DB_STORE).get(id).onsuccess = function(e){ cb(e.target.result||null); };
        });
    }
    function dbSet(rec) {
        openDB(function(d){ if(!d)return; d.transaction(DB_STORE,'readwrite').objectStore(DB_STORE).put(rec); });
    }
    function dbGetAll(cb) {
        openDB(function(d){ if(!d){cb([]);return;}
            d.transaction(DB_STORE,'readonly').objectStore(DB_STORE).getAll().onsuccess = function(e){ cb(e.target.result||[]); };
        });
    }
    function dbClear() {
        openDB(function(d){ if(!d)return; d.transaction(DB_STORE,'readwrite').objectStore(DB_STORE).clear(); });
    }

    /* ── State ───────────────────────────────────────────────────── */
    var files      = [];
    var converting = false;
    var uidCounter = 0;
    var scale      = 100;   // resize percentage

    var drop      = document.getElementById('tc-ja-drop');
    var fileInput = document.getElementById('tc-ja-upload');
    var cardsEl   = document.getElementById('tc-ja-cards');
    var gridEl    = document.getElementById('tc-ja-grid');
    var progWrap  = document.getElementById('tc-ja-progress-wrap');
    var progBar   = document.getElementById('tc-ja-progress-bar');
    var progPct   = document.getElementById('tc-ja-progress-pct');
    var progLabel = document.getElementById('tc-ja-progress-label');

    /* ── Quality slider ──────────────────────────────────────────── */
    document.getElementById('tc-ja-quality').addEventListener('input', function(){
        document.getElementById('tc-ja-qval').textContent = this.value + '%';
    });

    /* ── Scale buttons ───────────────────────────────────────────── */
    document.querySelectorAll('.tc-ja-scale').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('.tc-ja-scale').forEach(function(b){
                b.classList.remove('active','tc-btn--primary'); b.classList.add('tc-btn--ghost');
            });
            btn.classList.add('active','tc-btn--primary'); btn.classList.remove('tc-btn--ghost');
            scale = parseInt(btn.dataset.scale);
        });
    });

    /* ── Drop zone ───────────────────────────────────────────────── */
    drop.addEventListener('click', function(){ fileInput.click(); });
    drop.addEventListener('dragover', function(e){ e.preventDefault(); drop.style.borderColor='var(--tc-accent)'; });
    drop.addEventListener('dragleave', function(){ drop.style.borderColor='var(--tc-border)'; });
    drop.addEventListener('drop', function(e){
        e.preventDefault(); drop.style.borderColor='var(--tc-border)';
        handleFiles(e.dataTransfer.files);
    });
    fileInput.addEventListener('change', function(){ handleFiles(fileInput.files); fileInput.value=''; });

    /* ── Handle new files ────────────────────────────────────────── */
    function handleFiles(fileList) {
        Array.from(fileList).forEach(function(f){
            if (!f.type.match(/^image\/(jpeg|jpg|png|webp)$/)) return;
            var id = f.name + '_' + f.size + '_' + f.lastModified;
            if (files.some(function(x){ return x.id === id; })) return;

            var uid    = 'ja' + (++uidCounter);
            var record = { id: id, uid: uid, name: f.name, size: f.size, avifSize: 0,
                           originalDataUrl: null, avifBlob: null, status: 'ready' };
            files.push(record);

            // Read file for preview
            var reader = new FileReader();
            reader.onload = function(e){
                record.originalDataUrl = e.target.result;
                renderCard(record);
                gridEl.style.display = 'block';
                updateStats();

                // Check IndexedDB cache
                dbGet(id, function(cached){
                    if (cached && cached.avifBlob instanceof Blob) {
                        record.avifBlob  = cached.avifBlob;
                        record.avifSize  = cached.avifBlob.size;
                        record.status    = 'cached';
                        setCardProgress(record, 100);
                        updateCard(record);
                        updateStats();
                    }
                });
            };
            reader.readAsDataURL(f);
        });
    }

    /* ── Card rendering ──────────────────────────────────────────── */
    function statusText(s) {
        return { ready:'⏳ Ready', converting:'⚙️ Converting…', done:'✅ Converted', cached:'💾 Cached', error:'❌ Error' }[s] || s;
    }

    function cardHTML(rec) {
        var kb     = (rec.size / 1024).toFixed(1);
        var barW   = (rec.status === 'done' || rec.status === 'cached') ? '100' : '0';
        var pctTxt = (rec.status === 'done' || rec.status === 'cached') ? '100%' : '0%';
        var dlBtn  = (rec.status === 'done' || rec.status === 'cached') && rec.avifBlob
            ? '<button class="tc-ja-dl-btn" data-uid="' + rec.uid + '">⬇ Download AVIF</button>'
            : '';
        return '<img class="tc-ja-card-thumb" src="' + (rec.originalDataUrl||'') + '" alt="">'
            + '<div class="tc-ja-card-body">'
            + '<div class="tc-ja-card-name">' + escHTML(rec.name) + '</div>'
            + '<div class="tc-ja-card-meta">' + kb + ' KB original</div>'
            + '<div class="tc-ja-card-prog-wrap"><div class="tc-ja-card-prog-bar" id="tc-ja-pb-' + rec.uid + '" style="width:' + barW + '%"></div></div>'
            + '<div class="tc-ja-card-pct" id="tc-ja-pp-' + rec.uid + '">' + pctTxt + '</div>'
            + '<div class="tc-ja-card-status ' + rec.status + '" id="tc-ja-st-' + rec.uid + '">' + statusText(rec.status) + '</div>'
            + '<div id="tc-ja-dl-' + rec.uid + '">' + dlBtn + '</div>'
            + '</div>';
    }

    function renderCard(rec) {
        var div = document.createElement('div');
        div.className = 'tc-ja-card';
        div.id = 'tc-ja-card-' + rec.uid;
        div.innerHTML = cardHTML(rec);
        cardsEl.appendChild(div);
    }

    function updateCard(rec) {
        var stEl = document.getElementById('tc-ja-st-' + rec.uid);
        if (stEl) { stEl.textContent = statusText(rec.status); stEl.className = 'tc-ja-card-status ' + rec.status; }
        var dlEl = document.getElementById('tc-ja-dl-' + rec.uid);
        if (dlEl) {
            dlEl.innerHTML = (rec.status === 'done' || rec.status === 'cached') && rec.avifBlob
                ? '<button class="tc-ja-dl-btn" data-uid="' + rec.uid + '">⬇ Download AVIF</button>'
                : '';
        }
        // Update meta with saved size
        if ((rec.status === 'done' || rec.status === 'cached') && rec.avifSize) {
            var metaEl = document.querySelector('#tc-ja-card-' + rec.uid + ' .tc-ja-card-meta');
            if (metaEl) {
                var origKb = (rec.size / 1024).toFixed(1);
                var avifKb = (rec.avifSize / 1024).toFixed(1);
                var saved  = rec.size > 0 ? Math.round((1 - rec.avifSize/rec.size)*100) : 0;
                metaEl.textContent = origKb + ' KB → ' + avifKb + ' KB (' + (saved > 0 ? '-' + saved : '+' + Math.abs(saved)) + '%)';
            }
        }
    }

    function setCardProgress(rec, pct) {
        var pb = document.getElementById('tc-ja-pb-' + rec.uid);
        if (pb) pb.style.width = pct + '%';
        var pp = document.getElementById('tc-ja-pp-' + rec.uid);
        if (pp) pp.textContent = pct + '%';
    }

    /* ── Download single ─────────────────────────────────────────── */
    cardsEl.addEventListener('click', function(e){
        var btn = e.target.closest('[data-uid]');
        if (!btn) return;
        var rec = files.find(function(f){ return f.uid === btn.dataset.uid; });
        if (!rec || !rec.avifBlob) return;
        triggerDownload(rec.avifBlob, rec.name.replace(/\.[^.]+$/, '') + '.avif');
    });

    /* ── Convert All ─────────────────────────────────────────────── */
    document.getElementById('tc-ja-convert').addEventListener('click', function(){
        if (converting || !files.length) return;
        var toConvert = files.filter(function(f){ return f.status === 'ready'; });
        if (!toConvert.length) { alert('All images are already converted or cached.'); return; }

        converting = true;
        progWrap.style.display = 'block';
        var done = 0;
        var quality = parseInt(document.getElementById('tc-ja-quality').value) / 100;

        function advanceGlobal() {
            var pct = Math.round((done / toConvert.length) * 100);
            progBar.style.width = pct + '%';
            progPct.textContent = pct + '%';
            progLabel.textContent = 'Converting ' + done + ' of ' + toConvert.length + '…';
        }

        function next(i) {
            if (i >= toConvert.length) {
                converting = false;
                progLabel.textContent = '✅ All done!';
                updateStats();
                return;
            }
            var rec = toConvert[i];
            rec.status = 'converting';
            updateCard(rec);
            setCardProgress(rec, 10);

            var img = new Image();
            img.onload = function(){
                setCardProgress(rec, 35);

                var cvs = document.createElement('canvas');
                var w   = Math.round(img.naturalWidth  * scale / 100);
                var h   = Math.round(img.naturalHeight * scale / 100);
                cvs.width  = w;
                cvs.height = h;
                var ctx = cvs.getContext('2d');
                ctx.drawImage(img, 0, 0, w, h);
                setCardProgress(rec, 65);

                // Try AVIF first (supported in Chrome 85+, Firefox 93+), fall back to WebP, then JPEG
                function tryBlob(mimes, idx) {
                    if (idx >= mimes.length) {
                        rec.status = 'error';
                        updateCard(rec);
                        done++; advanceGlobal();
                        setTimeout(function(){ next(i+1); }, 30);
                        return;
                    }
                    cvs.toBlob(function(blob){
                        if (!blob) { tryBlob(mimes, idx+1); return; }
                        setCardProgress(rec, 90);
                        rec.avifBlob = blob;
                        rec.avifSize = blob.size;
                        rec.status   = 'done';
                        setCardProgress(rec, 100);
                        updateCard(rec);
                        // Store Blob directly in IndexedDB
                        dbSet({ id: rec.id, name: rec.name, size: rec.size,
                                avifBlob: blob, originalDataUrl: rec.originalDataUrl });
                        done++; advanceGlobal();
                        updateStats();
                        setTimeout(function(){ next(i+1); }, 30);
                    }, mimes[idx], quality);
                }

                tryBlob(['image/avif', 'image/webp', 'image/jpeg'], 0);
            };
            img.onerror = function(){
                rec.status = 'error';
                updateCard(rec);
                done++; advanceGlobal();
                setTimeout(function(){ next(i+1); }, 30);
            };
            img.src = rec.originalDataUrl;
        }

        advanceGlobal();
        next(0);
    });

    /* ── Download All as ZIP ─────────────────────────────────────── */
    document.getElementById('tc-ja-download-all').addEventListener('click', function(){
        var ready = files.filter(function(f){ return f.avifBlob && (f.status==='done'||f.status==='cached'); });
        if (!ready.length) { alert('Convert some images first.'); return; }

        function buildZip() {
            var zip = new JSZip();
            ready.forEach(function(f){
                zip.file(f.name.replace(/\.[^.]+$/, '') + '.avif', f.avifBlob);
            });
            zip.generateAsync({ type:'blob' }).then(function(blob){
                triggerDownload(blob, 'converted-avif-images.zip');
            });
        }

        if (typeof JSZip !== 'undefined') {
            buildZip();
        } else {
            var s = document.createElement('script');
            s.src = 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js';
            s.onload = buildZip;
            document.head.appendChild(s);
        }
    });

    /* ── Clear All ───────────────────────────────────────────────── */
    document.getElementById('tc-ja-clear-all').addEventListener('click', function(){
        if (!files.length) return;
        if (!confirm('Clear all images and cached data?')) return;
        files = [];
        cardsEl.innerHTML = '';
        gridEl.style.display   = 'none';
        progWrap.style.display = 'none';
        progBar.style.width    = '0%';
        progPct.textContent    = '0%';
        dbClear();
        ['tc-ja-stat-total','tc-ja-stat-converted','tc-ja-stat-saved'].forEach(function(id){
            document.getElementById(id).textContent = '0';
        });
    });

    /* ── Restore from cache on page load ─────────────────────────── */
    dbGetAll(function(cached){
        if (!cached.length) return;
        cached.forEach(function(rec){
            if (files.some(function(f){ return f.id === rec.id; })) return;
            if (!(rec.avifBlob instanceof Blob)) return;
            rec.uid      = 'ja' + (++uidCounter);
            rec.status   = 'cached';
            rec.avifSize = rec.avifBlob.size;
            files.push(rec);
            renderCard(rec);
            // update meta after card is in DOM
            updateCard(rec);
            gridEl.style.display = 'block';
        });
        updateStats();
    });

    /* ── Stats ───────────────────────────────────────────────────── */
    function updateStats() {
        var total     = files.length;
        var converted = files.filter(function(f){ return f.status==='done'||f.status==='cached'; }).length;
        var savedBytes = 0;
        files.forEach(function(f){
            if (f.avifBlob && (f.status==='done'||f.status==='cached')) {
                savedBytes += Math.max(0, f.size - f.avifSize);
            }
        });
        document.getElementById('tc-ja-stat-total').textContent     = total;
        document.getElementById('tc-ja-stat-converted').textContent  = converted;
        document.getElementById('tc-ja-stat-saved').textContent      = savedBytes > 0 ? fmtSize(savedBytes) : '0 KB';
    }

    /* ── Helpers ─────────────────────────────────────────────────── */
    function triggerDownload(blob, name) {
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = name;
        a.click();
        setTimeout(function(){ URL.revokeObjectURL(a.href); }, 5000);
    }
    function fmtSize(bytes) {
        if (bytes >= 1048576) return (bytes/1048576).toFixed(1) + ' MB';
        return (bytes/1024).toFixed(1) + ' KB';
    }
    function escHTML(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

})();
JS
        );
    }
}
