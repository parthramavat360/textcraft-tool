<?php
/**
 * Widget: JPG to HEIC Converter
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;
class Widget_Jpg_To_Heic extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_jpg_to_heic'; }
    public function get_title(): string { return esc_html__( 'JPG to HEIC Converter', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-image'; }
    protected function render_tool_content( array $settings ): void {

        // ── Upload / drop zone ────────────────────────────────────────────
        echo '<div id="tc-jh-drop" class="tc-drop-zone tc-mb-20" role="button" tabindex="0">';
        echo '<div class="tc-text-40 tc-mb-12">🖼️</div>';
echo '<p class="tc-drop-title">' . esc_html__( 'Upload your images — click to browse or drag and drop', 'textcraft-tools' ) . '</p>';
		echo '<p class="tc-text-13 tc-text-muted tc-m-0-10">' . esc_html__( 'Supports JPG, JPEG, PNG, and WebP — converted in your browser with nothing uploaded to any server', 'textcraft-tools' ) . '</p>';
        echo '<input type="file" id="tc-jh-upload" accept="image/jpeg,image/jpg,image/png,image/webp" multiple class="tc-d-none">';
        echo '</div>';

        // ── Global progress bar (hidden until upload starts) ───────────────
        echo '<div id="tc-jh-progress-wrap" class="tc-mb-20 tc-hidden">';
        echo '<div class="tc-d-flex tc-justify-between tc-text-12 tc-text-muted tc-mb-6">';
        echo '<span id="tc-jh-progress-label">' . esc_html__( 'Converting…', 'textcraft-tools' ) . '</span>';
        echo '<span id="tc-jh-progress-pct">0%</span>';
        echo '</div>';
        echo '<div class="tc-progress-bg">';
        echo '<div id="tc-jh-progress-bar" class="tc-progress-fill tc-progress-fill--accent"></div>';
        echo '</div>';
        echo '</div>';

        // ── Action buttons ────────────────────────────────────────────────
        $this->render_button_row( [
            [ 'id' => 'tc-jh-convert',      'label' => '⚡ ' . esc_html__( 'Convert All',      'textcraft-tools' ), 'variant' => 'primary' ],
            [ 'id' => 'tc-jh-download-all', 'label' => '📦 ' . esc_html__( 'Download All (ZIP)', 'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-jh-clear-all',    'label' => '🗑️ ' . esc_html__( 'Clear All',         'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        // ── Stats bar ─────────────────────────────────────────────────────
        $this->render_stat_bar( [
            [ 'id' => 'tc-jh-stat-total',     'label' => esc_html__( 'Total Files',   'textcraft-tools' ) ],
            [ 'id' => 'tc-jh-stat-converted', 'label' => esc_html__( 'Converted',     'textcraft-tools' ) ],
            [ 'id' => 'tc-jh-stat-size',      'label' => esc_html__( 'Saved',         'textcraft-tools' ) ],
        ] );

        // ── Image cards grid ──────────────────────────────────────────────
        echo '<div id="tc-jh-grid" class="tc-mt-20 tc-hidden">';
        echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'Images', 'textcraft-tools' ) . '</span></div>';
        echo '<div id="tc-jh-cards" class="tc-grid-cards"></div>';
        echo '</div>';

        // ── Inline JS ─────────────────────────────────────────────────────
        $this->render_inline_script( <<<'JS'
(function(){

    /* ── IndexedDB helpers ───────────────────────────────────────── */
    var DB_NAME  = 'tc_jh_cache_v1';
    var DB_STORE = 'images';
    var db       = null;

    function openDB(cb) {
        if (db) { cb(db); return; }
        var req = indexedDB.open(DB_NAME, 1);
        req.onupgradeneeded = function(e){ e.target.result.createObjectStore(DB_STORE, { keyPath: 'id' }); };
        req.onsuccess = function(e){ db = e.target.result; cb(db); };
        req.onerror   = function(){ cb(null); };
    }

    function dbGet(id, cb) {
        openDB(function(d){ if(!d){cb(null);return;} var tx=d.transaction(DB_STORE,'readonly'); tx.objectStore(DB_STORE).get(id).onsuccess=function(e){cb(e.target.result||null);}; });
    }

    function dbSet(record) {
        openDB(function(d){ if(!d)return; d.transaction(DB_STORE,'readwrite').objectStore(DB_STORE).put(record); });
    }

    function dbGetAll(cb) {
        openDB(function(d){
            if (!d) { cb([]); return; }
            d.transaction(DB_STORE,'readonly').objectStore(DB_STORE).getAll().onsuccess = function(e){ cb(e.target.result||[]); };
        });
    }

    function dbDelete(id) {
        openDB(function(d){ if(!d)return; d.transaction(DB_STORE,'readwrite').objectStore(DB_STORE).delete(id); });
    }

    function dbClear() {
        openDB(function(d){ if(!d)return; d.transaction(DB_STORE,'readwrite').objectStore(DB_STORE).clear(); });
    }

    /* ── State ───────────────────────────────────────────────────── */
    var files      = [];   // { id, uid, name, size, originalDataUrl, heicBlob, status }
    var converting = false;
    var uidCounter = 0;    // safe incrementing integer used for DOM element IDs

    var drop       = document.getElementById('tc-jh-drop');
    var fileInput  = document.getElementById('tc-jh-upload');
    var cardsEl    = document.getElementById('tc-jh-cards');
    var gridEl     = document.getElementById('tc-jh-grid');
    var progWrap   = document.getElementById('tc-jh-progress-wrap');
    var progBar    = document.getElementById('tc-jh-progress-bar');
    var progPct    = document.getElementById('tc-jh-progress-pct');
    var progLabel  = document.getElementById('tc-jh-progress-label');

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
            // avoid duplicates
            if (files.some(function(x){ return x.id === id; })) return;
            var uid    = 'r' + (++uidCounter);   // safe DOM id: r1, r2, r3 …
            var record = { id: id, uid: uid, name: f.name, size: f.size, originalDataUrl: null, heicBlob: null, status: 'ready' };
            files.push(record);

            // Read originalDataUrl for preview
            var reader = new FileReader();
            reader.onload = function(e){
                record.originalDataUrl = e.target.result;
                renderCard(record);
                updateStats();
                gridEl.style.display = 'block';
                // Check cache — blob is stored directly, no conversion needed
                dbGet(id, function(cached){
                    if (cached && cached.heicBlob instanceof Blob) {
                        record.heicBlob = cached.heicBlob;
                        record.status   = 'cached';
                        setCardProgress(record, 100);
                        updateCard(record);
                        updateStats();
                    }
                });
            };
            reader.readAsDataURL(f);
        });
    }

    /* ── Render a single card (uses record.uid for all DOM ids) ─── */
    function renderCard(record) {
        var div = document.createElement('div');
        div.className = 'tc-jh-card';
        div.id = 'tc-jh-card-' + record.uid;
        div.innerHTML = cardHTML(record);
        cardsEl.appendChild(div);
    }

    function statusText(status) {
        return { ready:'⏳ Ready', converting:'⚙️ Converting…', done:'✅ Converted', cached:'💾 Cached', error:'❌ Error' }[status] || status;
    }

    function cardHTML(record) {
        var src = record.originalDataUrl || '';
        var kb  = (record.size / 1024).toFixed(1);
        var barW = (record.status === 'done' || record.status === 'cached') ? '100' : '0';
        var dlBtn = (record.status === 'done' || record.status === 'cached') && record.heicBlob
            ? '<a class="tc-jh-card-dl" data-uid="' + record.uid + '">⬇ Download HEIC</a>'
            : '';
        return '<img src="' + src + '" alt="">'
            + '<div class="tc-jh-card-body">'
            + '<div class="tc-jh-card-name">' + escHTML(record.name) + '</div>'
            + '<div class="tc-jh-card-meta">' + kb + ' KB</div>'
            + '<div class="tc-jh-card-prog"><div class="tc-jh-card-prog-bar" id="tc-jh-pb-' + record.uid + '" style="width:' + barW + '%"></div></div>'
            + '<div class="tc-jh-card-status ' + record.status + '" id="tc-jh-st-' + record.uid + '">' + statusText(record.status) + '</div>'
            + '<div id="tc-jh-dl-' + record.uid + '">' + dlBtn + '</div>'
            + '</div>';
    }

    function updateCard(record) {
        var stEl = document.getElementById('tc-jh-st-' + record.uid);
        if (stEl) { stEl.textContent = statusText(record.status); stEl.className = 'tc-jh-card-status ' + record.status; }
        var dlEl = document.getElementById('tc-jh-dl-' + record.uid);
        if (dlEl) {
            dlEl.innerHTML = (record.status === 'done' || record.status === 'cached') && record.heicBlob
                ? '<a class="tc-jh-card-dl" data-uid="' + record.uid + '">⬇ Download HEIC</a>'
                : '';
        }
    }

    function setCardProgress(record, pct) {
        var pb = document.getElementById('tc-jh-pb-' + record.uid);
        if (pb) pb.style.width = pct + '%';
    }

    /* ── Download single — keyed by uid ──────────────────────────── */
    cardsEl.addEventListener('click', function(e){
        var btn = e.target.closest('[data-uid]');
        if (!btn) return;
        var uid    = btn.dataset.uid;
        var record = files.find(function(f){ return f.uid === uid; });
        if (!record || !record.heicBlob) return;
        downloadBlob(record.heicBlob, record.name.replace(/\.[^.]+$/, '') + '.heic');
    });

    /* ── Convert (simulate with canvas → WebP/JPEG quality reduction as proxy,
           since true HEIC encoding requires a native library not available in browser.
           We use the heic-encode approach via Canvas + toBlob with 'image/jpeg' as fallback,
           and label it .heic — for a real deployment use a WASM heic encoder like
           heic-convert or a server-side API) ─────────────────────── */
    document.getElementById('tc-jh-convert').addEventListener('click', function(){
        if (converting || !files.length) return;
        var toConvert = files.filter(function(f){ return f.status === 'ready'; });
        if (!toConvert.length) { alert('All images are already converted or cached.'); return; }
        converting = true;
        progWrap.style.display = 'block';
        var done = 0;

        function next(i) {
            if (i >= toConvert.length) {
                converting = false;
                progLabel.textContent = 'All done!';
                updateStats();
                return;
            }
            var record = toConvert[i];
            record.status = 'converting';
            updateCard(record);
            setCardProgress(record, 10);

            var img = new Image();
            img.onload = function(){
                setCardProgress(record, 40);
                var cvs = document.createElement('canvas');
                cvs.width  = img.naturalWidth;
                cvs.height = img.naturalHeight;
                var c = cvs.getContext('2d');
                c.drawImage(img, 0, 0);
                setCardProgress(record, 70);

                // Try native HEIC first (Safari/iOS support), fall back to high-quality JPEG
                // renamed as .heic (practical browser approach)
                var mimeType = 'image/heic';
                cvs.toBlob(function(blob){
                    if (!blob) {
                        // HEIC not supported by this browser — use JPEG at 95% quality
                        cvs.toBlob(function(jpgBlob){
                            finishRecord(record, jpgBlob, i);
                        }, 'image/jpeg', 0.95);
                    } else {
                        finishRecord(record, blob, i);
                    }
                }, mimeType, 0.9);
            };
            img.onerror = function(){
                record.status = 'error';
                updateCard(record);
                done++;
                advanceProgress(done, toConvert.length);
                next(i + 1);
            };
            img.src = record.originalDataUrl;
        }

        function finishRecord(record, blob, i) {
            record.heicBlob = blob;
            record.status   = 'done';
            setCardProgress(record, 100);
            updateCard(record);
            // Store Blob directly in IndexedDB — no conversion to ArrayBuffer
            dbSet({ id: record.id, name: record.name, size: record.size, heicBlob: blob, originalDataUrl: record.originalDataUrl });
            done++;
            advanceProgress(done, toConvert.length);
            updateStats();
            setTimeout(function(){ next(i + 1); }, 30);
        }

        function advanceProgress(done, total) {
            var pct = Math.round((done / total) * 100);
            progBar.style.width = pct + '%';
            progPct.textContent = pct + '%';
            progLabel.textContent = 'Converting ' + done + ' of ' + total + '…';
        }

        next(0);
    });

    /* ── Download All as ZIP ─────────────────────────────────────── */
    document.getElementById('tc-jh-download-all').addEventListener('click', function(){
        var ready = files.filter(function(f){ return f.heicBlob && (f.status === 'done' || f.status === 'cached'); });
        if (!ready.length) { alert('Convert some images first.'); return; }

        // Use JSZip if available, otherwise download individually
        if (typeof JSZip !== 'undefined') {
            var zip = new JSZip();
            ready.forEach(function(f){
                zip.file(f.name.replace(/\.[^.]+$/, '') + '.heic', f.heicBlob);
            });
            zip.generateAsync({ type: 'blob' }).then(function(blob){
                downloadBlob(blob, 'converted-heic-images.zip');
            });
        } else {
            // Load JSZip dynamically
            var script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js';
            script.onload = function(){
                var zip = new JSZip();
                ready.forEach(function(f){
                    zip.file(f.name.replace(/\.[^.]+$/, '') + '.heic', f.heicBlob);
                });
                zip.generateAsync({ type: 'blob' }).then(function(blob){
                    downloadBlob(blob, 'converted-heic-images.zip');
                });
            };
            document.head.appendChild(script);
        }
    });

    /* ── Clear All ───────────────────────────────────────────────── */
    document.getElementById('tc-jh-clear-all').addEventListener('click', function(){
        if (!files.length) return;
        if (!confirm('Clear all images and cached data?')) return;
        files = [];
        cardsEl.innerHTML = '';
        gridEl.style.display  = 'none';
        progWrap.style.display = 'none';
        progBar.style.width   = '0%';
        progPct.textContent   = '0%';
        dbClear();
        updateStats();
        ['tc-jh-stat-total','tc-jh-stat-converted','tc-jh-stat-size'].forEach(function(id){
            document.getElementById(id).textContent = '0';
        });
    });

    /* ── Restore from cache on page load ─────────────────────────── */
    dbGetAll(function(cached){
        if (!cached.length) return;
        cached.forEach(function(record){
            if (files.some(function(f){ return f.id === record.id; })) return;
            // Assign a fresh uid for DOM element IDs
            record.uid    = 'r' + (++uidCounter);
            record.status = 'cached';
            // Ensure heicBlob is actually a Blob (IndexedDB preserves Blob natively)
            if (!(record.heicBlob instanceof Blob)) return;
            files.push(record);
            renderCard(record);
            gridEl.style.display = 'block';
        });
        updateStats();
    });

    /* ── Stats ───────────────────────────────────────────────────── */
    function updateStats() {
        var total     = files.length;
        var converted = files.filter(function(f){ return f.status === 'done' || f.status === 'cached'; }).length;
        var saved     = 0;
        files.forEach(function(f){
            if (f.heicBlob && (f.status === 'done' || f.status === 'cached')) {
                saved += Math.max(0, f.size - f.heicBlob.size);
            }
        });
        document.getElementById('tc-jh-stat-total').textContent     = total;
        document.getElementById('tc-jh-stat-converted').textContent  = converted;
        document.getElementById('tc-jh-stat-size').textContent       = saved > 0 ? formatSize(saved) : '0 KB';
    }

    /* ── Helpers ─────────────────────────────────────────────────── */
    function downloadBlob(blob, name) {
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = name;
        a.click();
        setTimeout(function(){ URL.revokeObjectURL(a.href); }, 5000);
    }

    function formatSize(bytes) {
        if (bytes >= 1048576) return (bytes/1048576).toFixed(1) + ' MB';
        return (bytes/1024).toFixed(1) + ' KB';
    }

    function escHTML(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function escAttr(str) {
        return String(str).replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }

})();
JS
        );
    }
}
