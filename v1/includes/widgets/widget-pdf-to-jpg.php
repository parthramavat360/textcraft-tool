<?php
/**
 * Widget: PDF to JPG
 *
 * @package TextCraft_Tools\Widgets
 */

declare( strict_types = 1 );

namespace TextCraft_Tools\Widgets;

defined( 'ABSPATH' ) || exit;

class Widget_Pdf_To_Jpg extends TextCraft_Base_Widget {

    public function get_name(): string {
        return 'textcraft_pdf_to_jpg';
    }

    public function get_title(): string {
        return esc_html__( 'PDF to JPG Converter', 'textcraft-tools' );
    }

    public function get_keywords(): array {
        return [ 'pdf to jpg', 'convert pdf to image', 'pdf to jpeg converter', 'pdf page to jpg', 'free online pdf converter' ];
    }

    public function get_icon(): string {
        return 'eicon-image';
    }

    protected function render_tool_content( array $settings ): void {
        echo '<div class="tc-pdftojpg" data-pdf-to-jpg>';

        // ── Drop zone (identical structure to PDF compressor) ──────────────
        echo '<div class="tc-jc-drop" role="button" tabindex="0" aria-label="' . esc_attr__( 'Click or drag a PDF file to convert its pages to JPG images', 'textcraft-tools' ) . '">';
        echo '<div class="tc-jc-drop__icon">PDF</div>';
        echo '<p class="tc-jc-drop__title">' . esc_html__( 'Click to upload or drag & drop a PDF file', 'textcraft-tools' ) . '</p>';
        echo '<p class="tc-jc-drop__hint">' . esc_html__( 'Turn every PDF page into a high-quality JPG image — processed privately in your browser', 'textcraft-tools' ) . '</p>';
        echo '<input type="file" class="tc-jc-upload" accept="application/pdf,.pdf">';
        echo '</div>';

        // ── Upload progress line ───────────────────────────────────────────
        echo '<div class="tc-jc-upload-line" hidden><span class="tc-jc-upload-text">' . esc_html__( 'Uploading PDF: 0%', 'textcraft-tools' ) . '</span></div>';

        // ── Options (quality range) ────────────────────────────────────────
        echo '<div class="tc-jc-options">';
        echo '<div class="tc-jc-option">';
        echo '<label class="tc-label" for="tc-pdftojpg-quality">' . esc_html__( 'Image Quality', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-jc-range">';
        echo '<input type="range" id="tc-pdftojpg-quality" class="tc-jc-quality" min="1" max="3" value="2">';
        echo '<span class="tc-jc-quality-value">' . esc_html__( 'Balanced', 'textcraft-tools' ) . '</span>';
        echo '</div></div>';
        echo '<div class="tc-jc-note">' . esc_html__( 'Higher quality produces sharper JPG images, but results in larger file sizes. Choose what works best for your needs.', 'textcraft-tools' ) . '</div>';
        echo '</div>';

        // ── Conversion progress bar ────────────────────────────────────────
        echo '<div class="tc-jc-progress" hidden>';
        echo '<div class="tc-jc-progress__row"><span class="tc-jc-progress-label">' . esc_html__( 'Preparing...', 'textcraft-tools' ) . '</span><span class="tc-jc-progress-pct">0%</span></div>';
        echo '<div class="tc-jc-progress__track"><div class="tc-jc-progress__bar"></div></div>';
        echo '</div>';

        // ── Buttons ────────────────────────────────────────────────────────
        $this->render_button_row(
            [
                [ 'id' => 'tc-pdftojpg-convert',     'label' => esc_html__( 'Convert to JPG', 'textcraft-tools' ),  'variant' => 'primary', 'disabled' => true ],
                [ 'id' => 'tc-pdftojpg-download-all', 'label' => esc_html__( 'Download All (ZIP)', 'textcraft-tools' ), 'variant' => 'ghost' ],
                [ 'id' => 'tc-pdftojpg-clear',        'label' => esc_html__( 'Clear All', 'textcraft-tools' ),        'variant' => 'danger' ],
            ]
        );

        // ── Stat bar ───────────────────────────────────────────────────────
        $this->render_stat_bar(
            [
                [ 'id' => 'tc-pdftojpg-stat-pages',  'label' => esc_html__( 'Total Pages', 'textcraft-tools' ) ],
                [ 'id' => 'tc-pdftojpg-stat-done',   'label' => esc_html__( 'Converted', 'textcraft-tools' ) ],
                [ 'id' => 'tc-pdftojpg-stat-size',   'label' => esc_html__( 'Total Size', 'textcraft-tools' ) ],
            ]
        );

        // ── Results grid — mirrors tc-jc-results / tc-jc-grid / tc-jc-card exactly ──
        echo '<div class="tc-jc-results" hidden>';
        echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'Converted Pages', 'textcraft-tools' ) . '</span></div>';
        echo '<div class="tc-jc-grid tc-pdftojpg-grid"></div>';
        echo '</div>';

        echo '</div>'; // .tc-pdftojpg

        $this->render_inline_script( <<<'JS'
(function () {
    var root = document.querySelector('[data-pdf-to-jpg]');
    if (!root) return;

    var PDFJS_URL        = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
    var PDFJS_WORKER_URL = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    var JSZIP_URL        = 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js';

    // ── DOM refs ────────────────────────────────────────────────────────────
    var drop         = root.querySelector('.tc-jc-drop');
    var upload       = root.querySelector('.tc-jc-upload');
    var uploadLine   = root.querySelector('.tc-jc-upload-line');
    var uploadText   = root.querySelector('.tc-jc-upload-text');
    var qualitySlider= root.querySelector('.tc-jc-quality');
    var qualityLabel = root.querySelector('.tc-jc-quality-value');
    var progress     = root.querySelector('.tc-jc-progress');
    var progressBar  = root.querySelector('.tc-jc-progress__bar');
    var progressLbl  = root.querySelector('.tc-jc-progress-label');
    var progressPct  = root.querySelector('.tc-jc-progress-pct');
    var resultsWrap  = root.querySelector('.tc-jc-results');
    var grid         = root.querySelector('.tc-pdftojpg-grid');
    var btnConvert   = document.getElementById('tc-pdftojpg-convert');
    var btnDownAll   = document.getElementById('tc-pdftojpg-download-all');
    var btnClear     = document.getElementById('tc-pdftojpg-clear');
    var statPages    = document.getElementById('tc-pdftojpg-stat-pages');
    var statDone     = document.getElementById('tc-pdftojpg-stat-done');
    var statSize     = document.getElementById('tc-pdftojpg-stat-size');

    // ── State ───────────────────────────────────────────────────────────────
    var selectedFile  = null;
    var originalBytes = null;
    var pdfDoc        = null;
    var convertedPages= []; // [{dataUrl, blob, name}]
    var pdfJsPromise  = null;
    var jsZipPromise  = null;

    // ── Session-storage cache key prefix ────────────────────────────────────
    // Images are stored as base64 in sessionStorage (cleared on tab close).
    var CACHE_PREFIX = 'tc_pdftojpg_';
    var CACHE_META   = CACHE_PREFIX + 'meta';

    // ── Init UI ─────────────────────────────────────────────────────────────
    btnDownAll.style.display = 'none';
    statPages.textContent = '-';
    statDone.textContent  = '-';
    statSize.textContent  = '-';

    // ── Script loader ────────────────────────────────────────────────────────
    function loadScript(src) {
        return new Promise(function (resolve, reject) {
            var existing = document.querySelector('script[src="' + src + '"]');
            if (existing) {
                if (existing.dataset.loaded === 'true') { resolve(); return; }
                existing.addEventListener('load',  resolve, { once: true });
                existing.addEventListener('error', reject,  { once: true });
                return;
            }
            var s = document.createElement('script');
            s.src = src; s.async = true;
            s.onload  = function () { s.dataset.loaded = 'true'; resolve(); };
            s.onerror = reject;
            document.head.appendChild(s);
        });
    }

    function ensurePdfJs() {
        if (window.pdfjsLib) return Promise.resolve(window.pdfjsLib);
        if (!pdfJsPromise) {
            pdfJsPromise = loadScript(PDFJS_URL).then(function () {
                window.pdfjsLib.GlobalWorkerOptions.workerSrc = PDFJS_WORKER_URL;
                return window.pdfjsLib;
            });
        }
        return pdfJsPromise;
    }

    function ensureJsZip() {
        if (window.JSZip) return Promise.resolve(window.JSZip);
        if (!jsZipPromise) jsZipPromise = loadScript(JSZIP_URL).then(function () { return window.JSZip; });
        return jsZipPromise;
    }

    // ── Helpers ──────────────────────────────────────────────────────────────
    function formatSize(bytes) {
        return bytes >= 1048576
            ? (bytes / 1048576).toFixed(1) + ' MB'
            : (bytes / 1024).toFixed(1) + ' KB';
    }

    function qualitySettings(value) {
        // Returns {scale, jpegQuality} per slider level
        if (String(value) === '1') return { scale: 1.0,  jpeg: 0.70, label: 'Low' };
        if (String(value) === '3') return { scale: 2.0,  jpeg: 0.95, label: 'High' };
        return                            { scale: 1.5,  jpeg: 0.85, label: 'Balanced' };
    }

    function setUploadLine(done, total) {
        var pct = total ? Math.round((done / total) * 100) : 0;
        uploadLine.hidden = false;
        uploadText.textContent = 'Uploading PDF: ' + pct + '%';
        if (done === total) setTimeout(function () { uploadLine.hidden = true; }, 900);
    }

    function setProgress(done, total, label) {
        var pct = total ? Math.round((done / total) * 100) : 0;
        progress.hidden        = false;
        progressBar.style.width = pct + '%';
        progressPct.textContent = pct + '%';
        progressLbl.textContent = label || ('Converting page ' + done + ' of ' + total + '...');
    }

    function readArrayBuffer(file) {
        return new Promise(function (resolve, reject) {
            var reader = new FileReader();
            reader.onload    = function () { resolve(reader.result); };
            reader.onerror   = reject;
            reader.onprogress = function (e) {
                if (e.lengthComputable) setUploadLine(e.loaded, e.total);
            };
            reader.readAsArrayBuffer(file);
        });
    }

    // ── Session-storage cache ────────────────────────────────────────────────
    // We store each page as a separate sessionStorage key so we don't hit the
    // 5 MB limit in one shot. On reload the images are restored from cache.

    function cacheKey(index) { return CACHE_PREFIX + 'page_' + index; }

    function saveToCache(pages, fileName) {
        try {
            sessionStorage.removeItem(CACHE_META);
            // Clear old pages
            var keys = Object.keys(sessionStorage).filter(function (k) { return k.indexOf(CACHE_PREFIX) === 0 && k !== CACHE_META; });
            keys.forEach(function (k) { sessionStorage.removeItem(k); });

            var meta = { fileName: fileName, count: pages.length, sizes: [] };
            pages.forEach(function (p, i) {
                try {
                    sessionStorage.setItem(cacheKey(i), p.dataUrl);
                    meta.sizes.push(p.blob.size);
                } catch (e) { /* storage full – skip */ }
            });
            sessionStorage.setItem(CACHE_META, JSON.stringify(meta));
        } catch (e) { /* sessionStorage not available */ }
    }

    function loadFromCache() {
        try {
            var raw = sessionStorage.getItem(CACHE_META);
            if (!raw) return null;
            var meta = JSON.parse(raw);
            var pages = [];
            for (var i = 0; i < meta.count; i++) {
                var dataUrl = sessionStorage.getItem(cacheKey(i));
                if (!dataUrl) return null; // incomplete cache
                // Reconstruct blob from dataUrl
                var arr    = dataUrl.split(',');
                var mime   = arr[0].match(/:(.*?);/)[1];
                var bstr   = atob(arr[1]);
                var u8     = new Uint8Array(bstr.length);
                for (var j = 0; j < bstr.length; j++) u8[j] = bstr.charCodeAt(j);
                var blob = new Blob([u8], { type: mime });
                pages.push({ dataUrl: dataUrl, blob: blob, name: 'page-' + (i + 1) + '.jpg', size: meta.sizes[i] || blob.size });
            }
            return { pages: pages, fileName: meta.fileName };
        } catch (e) { return null; }
    }

    function clearCache() {
        try {
            var keys = Object.keys(sessionStorage).filter(function (k) { return k.indexOf(CACHE_PREFIX) === 0; });
            keys.forEach(function (k) { sessionStorage.removeItem(k); });
        } catch (e) {}
    }

    // ── Build preview grid — exact same card DOM as PDF Compressor ───────────
    function buildGrid(pages) {
        grid.innerHTML = '';
        var totalBytes = 0;

        pages.forEach(function (p, i) {
            totalBytes += p.blob.size;

            // <div class="tc-jc-card">
            var card = document.createElement('div');
            card.className = 'tc-jc-card';

            // <div class="tc-jc-card__preview">
            //   <div><span>Page N</span><img ...></div>
            // </div>
            var preview = document.createElement('div');
            preview.className = 'tc-jc-card__preview';
            var imgWrap = document.createElement('div');
            var pageLabel = document.createElement('span');
            pageLabel.textContent = 'Page ' + (i + 1);
            var img = document.createElement('img');
            img.src = p.dataUrl;
            img.alt = 'Page ' + (i + 1);
            imgWrap.appendChild(pageLabel);
            imgWrap.appendChild(img);
            preview.appendChild(imgWrap);
            card.appendChild(preview);

            // <div class="tc-jc-card__body">
            //   <div class="tc-jc-card__name">page-N.jpg</div>
            //   <div class="tc-jc-card__meta">123 KB</div>
            // </div>
            var body = document.createElement('div');
            body.className = 'tc-jc-card__body';

            var nameEl = document.createElement('div');
            nameEl.className = 'tc-jc-card__name';
            nameEl.textContent = p.name;
            nameEl.title = p.name;

            var metaEl = document.createElement('div');
            metaEl.className = 'tc-jc-card__meta';
            metaEl.textContent = formatSize(p.blob.size);

            // Per-card download — same ghost anchor pattern used across the widget suite
            var dlLink = document.createElement('a');
            dlLink.href = '#';
            dlLink.className = 'tc-jc-card__download';
            dlLink.textContent = '↓ Download';
            // These properties come from class tc-jc-card__download + tc-dl-link
            dlLink.className = 'tc-jc-card__download tc-dl-link';
            dlLink.addEventListener('click', function (e) { e.preventDefault(); downloadSingle(p); });

            body.appendChild(nameEl);
            body.appendChild(metaEl);
            body.appendChild(dlLink);
            card.appendChild(body);
            grid.appendChild(card);
        });

        statPages.textContent    = pages.length;
        statDone.textContent     = pages.length;
        statSize.textContent     = formatSize(totalBytes);
        resultsWrap.hidden       = false;
        btnDownAll.style.display = pages.length > 0 ? 'inline-flex' : 'none';
    }

    // ── Download helpers ──────────────────────────────────────────────────────
    function downloadSingle(p) {
        var url  = URL.createObjectURL(p.blob);
        var link = document.createElement('a');
        link.href = url; link.download = p.name; link.click();
        setTimeout(function () { URL.revokeObjectURL(url); }, 5000);
    }

    async function downloadAllAsZip() {
        if (!convertedPages.length) return;
        btnDownAll.disabled = true;
        btnDownAll.textContent = 'Zipping...';
        try {
            var JSZip = await ensureJsZip();
            var zip   = new JSZip();
            var folder = zip.folder(selectedFile ? selectedFile.name.replace(/\.pdf$/i, '') : 'pages');
            convertedPages.forEach(function (p) {
                folder.file(p.name, p.blob);
            });
            var content = await zip.generateAsync({ type: 'blob', compression: 'DEFLATE', compressionOptions: { level: 6 } });
            var zipName = (selectedFile ? selectedFile.name.replace(/\.pdf$/i, '') : 'pages') + '.zip';
            var url = URL.createObjectURL(content);
            var link = document.createElement('a');
            link.href = url; link.download = zipName; link.click();
            setTimeout(function () { URL.revokeObjectURL(url); }, 8000);
        } catch (e) {
            console.error('ZIP failed', e);
        } finally {
            btnDownAll.disabled = false;
            btnDownAll.textContent = 'Download All (ZIP)';
        }
    }

    // ── Load file ─────────────────────────────────────────────────────────────
    async function loadFile(file) {
        if (!file || !(file.type === 'application/pdf' || /\.pdf$/i.test(file.name))) return;
        selectedFile  = file;
        originalBytes = await readArrayBuffer(file);
        convertedPages = [];
        grid.innerHTML = '';
        btnDownAll.style.display = 'none';
        resultsWrap.hidden = true;
        statPages.textContent = '-';
        statDone.textContent  = '-';
        statSize.textContent  = '-';
        setProgress(8, 100, 'Loading PDF...');

        try {
            var pdfjs = await ensurePdfJs();
            pdfDoc = await pdfjs.getDocument({ data: originalBytes.slice(0) }).promise;
            statPages.textContent = pdfDoc.numPages;
            setProgress(100, 100, 'PDF loaded — ' + pdfDoc.numPages + ' page(s) ready.');
            btnConvert.disabled = false;
        } catch (err) {
            console.error(err);
            setProgress(0, 100, 'Could not open this PDF.');
        }
    }

    // ── Convert ───────────────────────────────────────────────────────────────
    btnConvert.addEventListener('click', async function () {
        if (!pdfDoc) return;
        btnConvert.disabled = true;
        btnConvert.textContent = 'Converting...';
        btnDownAll.style.display = 'none';
        convertedPages = [];
        grid.innerHTML = '';
        resultsWrap.hidden = true;

        var cfg = qualitySettings(qualitySlider.value);

        try {
            for (var i = 1; i <= pdfDoc.numPages; i++) {
                setProgress(i - 1, pdfDoc.numPages, 'Converting page ' + i + ' of ' + pdfDoc.numPages + '...');

                var page     = await pdfDoc.getPage(i);
                var viewport = page.getViewport({ scale: cfg.scale });
                var canvas   = document.createElement('canvas');
                var ctx      = canvas.getContext('2d');
                canvas.width  = viewport.width;
                canvas.height = viewport.height;
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                await page.render({ canvasContext: ctx, viewport: viewport }).promise;

                var dataUrl = canvas.toDataURL('image/jpeg', cfg.jpeg);
                // Convert dataUrl → Blob
                var arr  = dataUrl.split(',');
                var bstr = atob(arr[1]);
                var u8   = new Uint8Array(bstr.length);
                for (var j = 0; j < bstr.length; j++) u8[j] = bstr.charCodeAt(j);
                var blob = new Blob([u8], { type: 'image/jpeg' });

                convertedPages.push({
                    dataUrl : dataUrl,
                    blob    : blob,
                    name    : 'page-' + i + '.jpg',
                    size    : blob.size
                });

                setProgress(i, pdfDoc.numPages);
            }

            buildGrid(convertedPages);
            saveToCache(convertedPages, selectedFile ? selectedFile.name : 'document.pdf');
            setProgress(100, 100, 'Conversion complete — ' + convertedPages.length + ' image(s) ready.');

        } catch (err) {
            console.error(err);
            setProgress(0, 100, 'Conversion failed.');
        } finally {
            btnConvert.disabled = false;
            btnConvert.textContent = 'Convert to JPG';
        }
    });

    btnDownAll.addEventListener('click', downloadAllAsZip);

    btnClear.addEventListener('click', function () {
        selectedFile  = null;
        originalBytes = null;
        pdfDoc        = null;
        convertedPages = [];
        grid.innerHTML = '';
        clearCache();
        resultsWrap.hidden       = true;
        progress.hidden          = true;
        uploadLine.hidden        = true;
        progressBar.style.width  = '0%';
        progressPct.textContent  = '0%';
        btnConvert.disabled      = true;
        btnConvert.textContent   = 'Convert to JPG';
        btnDownAll.style.display = 'none';
        statPages.textContent    = '-';
        statDone.textContent     = '-';
        statSize.textContent     = '-';
    });

    // ── Drop zone events ──────────────────────────────────────────────────────
    drop.addEventListener('click',   function () { upload.click(); });
    drop.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); upload.click(); }
    });
    drop.addEventListener('dragover',  function (e) { e.preventDefault(); drop.classList.add('is-dragging'); });
    drop.addEventListener('dragleave', function ()  { drop.classList.remove('is-dragging'); });
    drop.addEventListener('drop', function (e) {
        e.preventDefault();
        drop.classList.remove('is-dragging');
        if (e.dataTransfer.files[0]) loadFile(e.dataTransfer.files[0]);
    });
    upload.addEventListener('change', function () {
        if (upload.files[0]) loadFile(upload.files[0]);
        upload.value = '';
    });
    qualitySlider.addEventListener('input', function () {
        qualityLabel.textContent = qualitySettings(qualitySlider.value).label;
    });

    // ── Restore from session cache on page load ───────────────────────────────
    (function restoreCache() {
        var cached = loadFromCache();
        if (!cached || !cached.pages.length) return;
        convertedPages = cached.pages;
        buildGrid(convertedPages);
        statPages.textContent = cached.pages.length;
        setProgress(100, 100, 'Restored ' + cached.pages.length + ' page(s) from cache.');
    })();

})();
JS
        );
    }
}
