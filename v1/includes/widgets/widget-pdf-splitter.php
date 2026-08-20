<?php
/**
 * Widget: PDF Splitter
 *
 * @package TextCraft_Tools\Widgets
 */

declare( strict_types = 1 );

namespace TextCraft_Tools\Widgets;

defined( 'ABSPATH' ) || exit;

class Widget_Pdf_Splitter extends TextCraft_Base_Widget {

    public function get_name(): string {
        return 'textcraft_pdf_splitter';
    }

    public function get_title(): string {
        return esc_html__( 'PDF Splitter', 'textcraft-tools' );
    }

    public function get_keywords(): array {
        return [ 'pdf splitter', 'split pdf online', 'separate pdf pages', 'extract pdf pages', 'free online pdf tool' ];
    }

    public function get_icon(): string {
        return 'eicon-file-download';
    }

    protected function render_tool_content( array $settings ): void {
        echo '<div class="tc-pdfsplitter" data-pdf-splitter>';

        echo '<div class="tc-jc-drop" role="button" tabindex="0" aria-label="' . esc_attr__( 'Click or drag a PDF file to split it online', 'textcraft-tools' ) . '">';
        echo '<div class="tc-jc-drop__icon">PDF</div>';
        echo '<p class="tc-jc-drop__title">' . esc_html__( 'Click to upload or drag & drop a PDF file', 'textcraft-tools' ) . '</p>';
        echo '<p class="tc-jc-drop__hint">' . esc_html__( 'Split PDF files by page ranges or extract individual pages online. A fast way to separate PDF pages into multiple documents — all done privately in your browser.', 'textcraft-tools' ) . '</p>';
        echo '<input type="file" class="tc-jc-upload" accept="application/pdf,.pdf">';
        echo '</div>';

        echo '<div class="tc-jc-upload-line" hidden><span class="tc-jc-upload-text">' . esc_html__( 'Uploading PDF: 0%', 'textcraft-tools' ) . '</span></div>';

        echo '<div class="tc-jc-progress" hidden>';
        echo '<div class="tc-jc-progress__row"><span class="tc-jc-progress-label">' . esc_html__( 'Preparing...', 'textcraft-tools' ) . '</span><span class="tc-jc-progress-pct">0%</span></div>';
        echo '<div class="tc-jc-progress__track"><div class="tc-jc-progress__bar"></div></div>';
        echo '</div>';

        // Split mode selector
        echo '<div class="tc-options-row tc-mb-0" id="tc-pdfsplitter-mode-row">';
        echo '<label class="tc-option tc-option-flex">';
        echo '<input type="radio" name="tc-pdfsplitter-mode" value="range" checked>';
        echo '<span class="tc-font-semibold">' . esc_html__( 'Range', 'textcraft-tools' ) . '</span>';
        echo '</label>';
        echo '<label class="tc-option tc-option-flex">';
        echo '<input type="radio" name="tc-pdfsplitter-mode" value="pages">';
        echo '<span class="tc-font-semibold">' . esc_html__( 'Pages', 'textcraft-tools' ) . '</span>';
        echo '</label>';
        echo '<label class="tc-option tc-option-flex">';
        echo '<input type="radio" name="tc-pdfsplitter-mode" value="size">';
        echo '<span class="tc-font-semibold">' . esc_html__( 'Size', 'textcraft-tools' ) . '</span>';
        echo '</label>';
        echo '</div>';

        // Mode-specific inputs
        echo '<div class="tc-jc-options tc-mt-16" id="tc-pdfsplitter-options">';

        // Range mode
        echo '<div id="tc-pdfsplitter-range-opt" class="tc-grid-full">';
        echo '<label class="tc-label" for="tc-pdfsplitter-range">' . esc_html__( 'Page Ranges', 'textcraft-tools' ) . '</label>';
        echo '<input type="text" id="tc-pdfsplitter-range" class="tc-text-input tc-w-full" value="1-3, 5, 7-9" placeholder="' . esc_attr__( 'e.g. 1-3, 5, 7-9', 'textcraft-tools' ) . '">';
        echo '<p class="tc-text-11 tc-text-muted tc-mt-6">' . esc_html__( 'Enter comma-separated page numbers or ranges. Each range becomes its own PDF file.', 'textcraft-tools' ) . '</p>';
        echo '</div>';

        // Pages mode (hidden by default)
        echo '<div id="tc-pdfsplitter-pages-opt" class="tc-grid-full tc-hidden">';
        echo '<label class="tc-label" for="tc-pdfsplitter-pages">' . esc_html__( 'Pages per Split', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-jc-range">';
        echo '<input type="range" id="tc-pdfsplitter-pages" class="tc-jc-quality" min="1" max="50" value="2">';
        echo '<span class="tc-jc-quality-value" id="tc-pdfsplitter-pages-val">2 pages</span>';
        echo '</div>';
        echo '<p class="tc-text-11 tc-text-muted tc-mt-6">' . esc_html__( 'Each output file will contain this many pages. The last file may have fewer.', 'textcraft-tools' ) . '</p>';
        echo '</div>';

        // Size mode (hidden by default)
        echo '<div id="tc-pdfsplitter-size-opt" class="tc-grid-full tc-hidden">';
        echo '<label class="tc-label" for="tc-pdfsplitter-size">' . esc_html__( 'Target Size per File', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-jc-range">';
        echo '<input type="range" id="tc-pdfsplitter-size" class="tc-jc-quality" min="50" max="5000" value="500" step="50">';
        echo '<span class="tc-jc-quality-value" id="tc-pdfsplitter-size-val">500 KB</span>';
        echo '</div>';
        echo '<p class="tc-text-11 tc-text-muted tc-mt-6">' . esc_html__( 'Each split file targets roughly this size. Actual sizes may vary slightly.', 'textcraft-tools' ) . '</p>';
        echo '</div>';

        echo '</div>';

        // Page previews
        echo '<div class="tc-jc-results" data-pdfsplitter-previews hidden>';
        echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'Pages', 'textcraft-tools' ) . '</span></div>';
        echo '<div class="tc-jc-grid tc-pdfsplitter-preview-grid"></div>';
        echo '</div>';

        // Split results
        echo '<div class="tc-jc-results" data-pdfsplitter-results hidden>';
        echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'Split Files', 'textcraft-tools' ) . '</span></div>';
        echo '<div class="tc-jc-grid tc-pdfsplitter-results-grid"></div>';
        echo '</div>';

        $this->render_button_row(
            [
                [ 'id' => 'tc-pdfsplitter-split',        'label' => esc_html__( 'Split PDF', 'textcraft-tools' ),              'variant' => 'primary', 'disabled' => true ],
                [ 'id' => 'tc-pdfsplitter-download-all', 'label' => esc_html__( 'Download All as ZIP', 'textcraft-tools' ),      'variant' => 'ghost', 'disabled' => true ],
                [ 'id' => 'tc-pdfsplitter-clear',        'label' => esc_html__( 'Clear All', 'textcraft-tools' ),               'variant' => 'danger' ],
            ]
        );

        $this->render_stat_bar(
            [
                [ 'id' => 'tc-pdfsplitter-stat-pages',    'label' => esc_html__( 'Total Pages', 'textcraft-tools' ) ],
                [ 'id' => 'tc-pdfsplitter-stat-splits',   'label' => esc_html__( 'Split Files', 'textcraft-tools' ) ],
                [ 'id' => 'tc-pdfsplitter-stat-size',     'label' => esc_html__( 'Total Size', 'textcraft-tools' ) ],
            ]
        );

        echo '</div>';

        $this->render_inline_script( <<<'JS'
(function () {
    var root = document.querySelector('[data-pdf-splitter]');
    if (!root) return;

    var PDFJS_URL        = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
    var PDFJS_WORKER_URL = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    var PDFLIB_URL       = 'https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js';
    var JSZIP_URL        = 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js';
    var CACHE_KEY        = 'tc_pdfsplitter_session';

    var drop           = root.querySelector('.tc-jc-drop');
    var upload         = root.querySelector('.tc-jc-upload');
    var uploadLine     = root.querySelector('.tc-jc-upload-line');
    var uploadText     = root.querySelector('.tc-jc-upload-text');
    var progress       = root.querySelector('.tc-jc-progress');
    var progressBar    = root.querySelector('.tc-jc-progress__bar');
    var progressLbl    = root.querySelector('.tc-jc-progress-label');
    var progressPct    = root.querySelector('.tc-jc-progress-pct');
    var pagePreviews   = root.querySelector('[data-pdfsplitter-previews]');
    var pageGrid       = root.querySelector('.tc-pdfsplitter-preview-grid');
    var resultsWrap    = root.querySelector('[data-pdfsplitter-results]');
    var resultsGrid    = root.querySelector('.tc-pdfsplitter-results-grid');
    var btnSplit       = document.getElementById('tc-pdfsplitter-split');
    var btnDownAll     = document.getElementById('tc-pdfsplitter-download-all');
    var btnClear       = document.getElementById('tc-pdfsplitter-clear');
    var statPages      = document.getElementById('tc-pdfsplitter-stat-pages');
    var statSplits     = document.getElementById('tc-pdfsplitter-stat-splits');
    var statSize       = document.getElementById('tc-pdfsplitter-stat-size');

    var modeRadios     = document.querySelectorAll('input[name="tc-pdfsplitter-mode"]');
    var rangeOpt       = document.getElementById('tc-pdfsplitter-range-opt');
    var pagesOpt       = document.getElementById('tc-pdfsplitter-pages-opt');
    var sizeOpt        = document.getElementById('tc-pdfsplitter-size-opt');
    var rangeInput     = document.getElementById('tc-pdfsplitter-range');
    var pagesSlider    = document.getElementById('tc-pdfsplitter-pages');
    var pagesVal       = document.getElementById('tc-pdfsplitter-pages-val');
    var sizeSlider     = document.getElementById('tc-pdfsplitter-size');
    var sizeVal        = document.getElementById('tc-pdfsplitter-size-val');

    var selectedFile  = null;
    var arrayBuffer   = null;
    var pdfDoc        = null;
    var pageImages    = []; // [{dataUrl, index}]
    var splitResults  = []; // [{blob, name, pages}]
    var scriptPromises = {};
    var totalSize     = 0;

    statPages.textContent = '0';
    statSplits.textContent = '0';
    statSize.textContent  = '-';
    btnDownAll.style.display = 'none';

    function loadScript(src) {
        if (scriptPromises[src]) return scriptPromises[src];
        scriptPromises[src] = new Promise(function (resolve, reject) {
            var existing = document.querySelector('script[src="' + src + '"]');
            if (existing) {
                if (existing.dataset.loaded === 'true') { resolve(); return; }
                existing.addEventListener('load', resolve, { once: true });
                existing.addEventListener('error', reject, { once: true });
                return;
            }
            var s = document.createElement('script');
            s.src = src;
            s.async = true;
            s.onload = function () { s.dataset.loaded = 'true'; resolve(); };
            s.onerror = reject;
            document.head.appendChild(s);
        });
        return scriptPromises[src];
    }

    function formatSize(bytes) {
        if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
        return (bytes / 1024).toFixed(1) + ' KB';
    }

    function ensurePdfJs() {
        if (window.pdfjsLib) return Promise.resolve(window.pdfjsLib);
        if (!scriptPromises[PDFJS_URL]) {
            scriptPromises[PDFJS_URL] = loadScript(PDFJS_URL).then(function () {
                window.pdfjsLib.GlobalWorkerOptions.workerSrc = PDFJS_WORKER_URL;
                return window.pdfjsLib;
            });
        }
        return scriptPromises[PDFJS_URL];
    }

    function ensurePdfLib() {
        if (window.PDFLib) return Promise.resolve(window.PDFLib);
        return loadScript(PDFLIB_URL).then(function () { return window.PDFLib; });
    }

    function ensureJsZip() {
        if (window.JSZip) return Promise.resolve(window.JSZip);
        if (!scriptPromises[JSZIP_URL]) {
            scriptPromises[JSZIP_URL] = loadScript(JSZIP_URL).then(function () { return window.JSZip; });
        }
        return scriptPromises[JSZIP_URL];
    }

    function setUploadLine(done, total) {
        var pct = total ? Math.round((done / total) * 100) : 0;
        uploadLine.hidden = false;
        uploadText.textContent = 'Uploading PDF: ' + pct + '%';
        if (done === total) setTimeout(function () { uploadLine.hidden = true; }, 800);
    }

    function setProgress(done, total, label) {
        var pct = total ? Math.round((done / total) * 100) : 0;
        progress.hidden = false;
        progressBar.style.width = pct + '%';
        progressPct.textContent = pct + '%';
        progressLbl.textContent = label || ('Processing ' + done + ' of ' + total + '...');
        if (done === total) {
            setTimeout(function () {
                progress.hidden = true;
                progressBar.style.width = '0%';
            }, 700);
        }
    }

    function saveCache() {
        try {
            var payload = {
                fileName: selectedFile ? selectedFile.name : null,
                dataUrl: null,
                pageImages: pageImages,
                splitResults: null,
            };
            if (arrayBuffer) {
                var binary = '';
                var bytes = new Uint8Array(arrayBuffer);
                for (var i = 0; i < bytes.byteLength; i++) binary += String.fromCharCode(bytes[i]);
                payload.dataUrl = 'data:application/pdf;base64,' + btoa(binary);
            }
            if (splitResults.length) {
                payload.splitResults = [];
                var pending = splitResults.length;
                splitResults.forEach(function (item, idx) {
                    var fr = new FileReader();
                    fr.onload = function () {
                        payload.splitResults[idx] = { name: item.name, dataUrl: fr.result, pages: item.pages, size: item.size };
                        pending -= 1;
                        if (pending === 0) sessionStorage.setItem(CACHE_KEY, JSON.stringify(payload));
                    };
                    fr.readAsDataURL(item.blob);
                });
            } else {
                sessionStorage.setItem(CACHE_KEY, JSON.stringify(payload));
            }
        } catch (e) {}
    }

    function clearCache() {
        try { sessionStorage.removeItem(CACHE_KEY); } catch (e) {}
    }

    function dataUrlToBlob(dataUrl, mime) {
        var parts = dataUrl.split(',');
        var bstr = atob(parts[1]);
        var len = bstr.length;
        var arr = new Uint8Array(len);
        for (var i = 0; i < len; i++) arr[i] = bstr.charCodeAt(i);
        return new Blob([arr], { type: mime || 'application/pdf' });
    }

    function renderPagePreviews() {
        pageGrid.innerHTML = '';
        if (!pageImages.length) {
            pagePreviews.hidden = true;
            return;
        }
        pageImages.forEach(function (item, idx) {
            var card = document.createElement('div');
            card.className = 'tc-jc-card';

            var preview = document.createElement('div');
            preview.className = 'tc-jc-card__preview';
            preview.style.gridTemplateColumns = '1fr';
            var wrap = document.createElement('div');
            var label = document.createElement('span');
            label.textContent = 'Page ' + (idx + 1);
            var img = document.createElement('img');
            img.src = item.dataUrl;
            img.alt = 'Page ' + (idx + 1);
            wrap.appendChild(label);
            wrap.appendChild(img);
            preview.appendChild(wrap);

            var body = document.createElement('div');
            body.className = 'tc-jc-card__body';
            var name = document.createElement('div');
            name.className = 'tc-jc-card__name';
            name.textContent = 'Page ' + (idx + 1);
            body.appendChild(name);

            card.appendChild(preview);
            card.appendChild(body);
            pageGrid.appendChild(card);
        });
        pagePreviews.hidden = false;
    }

    function renderSplitResults() {
        resultsGrid.innerHTML = '';
        if (!splitResults.length) {
            resultsWrap.hidden = true;
            return;
        }
        totalSize = 0;
        splitResults.forEach(function (item, idx) {
            totalSize += item.size;
            var card = document.createElement('div');
            card.className = 'tc-jc-card';

            var preview = document.createElement('div');
            preview.className = 'tc-jc-card__preview';
            preview.style.gridTemplateColumns = '1fr';
            var wrap = document.createElement('div');
            var label = document.createElement('span');
            label.textContent = 'Split ' + (idx + 1);
            var img = document.createElement('img');
            var firstPage = pageImages[item.pages[0]];
            img.src = firstPage ? firstPage.dataUrl : '';
            img.alt = item.name;
            wrap.appendChild(label);
            wrap.appendChild(img);
            preview.appendChild(wrap);
            card.appendChild(preview);

            var body = document.createElement('div');
            body.className = 'tc-jc-card__body';
            var name = document.createElement('div');
            name.className = 'tc-jc-card__name';
            name.textContent = item.name;
            name.title = item.name;
            body.appendChild(name);
            var meta = document.createElement('div');
            meta.className = 'tc-jc-card__meta';
            meta.textContent = item.pages.length + ' page' + (item.pages.length > 1 ? 's' : '') + ' — ' + formatSize(item.size);
            body.appendChild(meta);

            var dlLink = document.createElement('a');
            dlLink.href = '#';
            dlLink.className = 'tc-jc-card__download';
            dlLink.textContent = 'Download';
            dlLink.addEventListener('click', function (e) {
                e.preventDefault();
                var url = URL.createObjectURL(item.blob);
                var link = document.createElement('a');
                link.href = url;
                link.download = item.name;
                link.click();
                setTimeout(function () { URL.revokeObjectURL(url); }, 800);
            });
            body.appendChild(dlLink);
            card.appendChild(body);
            resultsGrid.appendChild(card);
        });

        resultsWrap.hidden = false;
        statSplits.textContent = String(splitResults.length);
        statSize.textContent = formatSize(totalSize);
    }

    function updateUiState() {
        var loaded = pageImages.length > 0;
        btnSplit.disabled = !loaded;
        btnDownAll.disabled = splitResults.length === 0;
        if (splitResults.length) btnDownAll.style.display = 'inline-flex';
        statPages.textContent = String(pageImages.length);
    }

    function switchMode(mode) {
        rangeOpt.style.display = mode === 'range' ? 'block' : 'none';
        pagesOpt.style.display = mode === 'pages' ? 'block' : 'none';
        sizeOpt.style.display  = mode === 'size'  ? 'block' : 'none';
    }

    modeRadios.forEach(function (radio) {
        radio.addEventListener('change', function () {
            if (radio.checked) switchMode(radio.value);
        });
    });

    pagesSlider.addEventListener('input', function () {
        pagesVal.textContent = pagesSlider.value + ' page' + (pagesSlider.value > 1 ? 's' : '');
    });

    sizeSlider.addEventListener('input', function () {
        sizeVal.textContent = sizeSlider.value + ' KB';
    });

    async function loadFile(file) {
        if (!file || !(/application\/pdf/i.test(file.type) || /\.pdf$/i.test(file.name))) return;
        selectedFile  = file;
        pageImages    = [];
        splitResults  = [];
        pageGrid.innerHTML = '';
        resultsGrid.innerHTML = '';
        resultsWrap.hidden = true;
        pagePreviews.hidden = true;
        btnDownAll.style.display = 'none';

        await ensurePdfJs();

        var fr = new FileReader();
        fr.onprogress = function (e) {
            if (e.lengthComputable) setUploadLine(e.loaded, e.total);
        };
        arrayBuffer = await new Promise(function (resolve, reject) {
            fr.onload = function () { resolve(fr.result); };
            fr.onerror = reject;
            fr.readAsArrayBuffer(file);
        });

        setProgress(10, 100, 'Loading PDF...');

        try {
            var pdfjs = window.pdfjsLib;
            pdfDoc = await pdfjs.getDocument({ data: arrayBuffer.slice(0) }).promise;

            var numPages = pdfDoc.numPages;
            setProgress(30, 100, 'Rendering ' + numPages + ' page(s)...');

            for (var i = 1; i <= numPages; i++) {
                var page = await pdfDoc.getPage(i);
                var viewport = page.getViewport({ scale: 0.5 });
                var canvas = document.createElement('canvas');
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                var ctx = canvas.getContext('2d');
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                await page.render({ canvasContext: ctx, viewport: viewport }).promise;

                pageImages.push({
                    dataUrl: canvas.toDataURL('image/jpeg', 0.5),
                    index: i - 1,
                });

                setProgress(30 + Math.round((i / numPages) * 60), 100, 'Rendering page ' + i + ' of ' + numPages + '...');
            }

            renderPagePreviews();
            updateUiState();
            setProgress(100, 100, 'Loaded ' + numPages + ' page(s).');
            saveCache();
        } catch (err) {
            setProgress(0, 100, 'Could not open this PDF.');
        }
    }

    function parseRanges(str, maxPage) {
        var parts = str.split(',').map(function (s) { return s.trim(); });
        var ranges = [];
        for (var i = 0; i < parts.length; i++) {
            var part = parts[i];
            if (/^\d+$/.test(part)) {
                var p = parseInt(part, 10);
                if (p >= 1 && p <= maxPage) ranges.push([p - 1, p - 1]);
            } else if (/^(\d+)\s*-\s*(\d+)$/.test(part)) {
                var m = part.match(/^(\d+)\s*-\s*(\d+)$/);
                var start = parseInt(m[1], 10);
                var end = parseInt(m[2], 10);
                if (start >= 1 && end <= maxPage && start <= end) ranges.push([start - 1, end - 1]);
            }
        }
        return ranges;
    }

    async function splitPdf() {
        if (!pdfDoc || !arrayBuffer) return;

        var mode = 'range';
        modeRadios.forEach(function (r) { if (r.checked) mode = r.value; });

        var pageRanges = [];
        var numPages = pdfDoc.numPages;

        if (mode === 'range') {
            var raw = rangeInput.value.trim();
            if (!raw) return;
            var parsed = parseRanges(raw, numPages);
            if (!parsed.length) return;
            pageRanges = parsed;
        } else if (mode === 'pages') {
            var perSplit = parseInt(pagesSlider.value, 10) || 2;
            for (var s = 0; s < numPages; s += perSplit) {
                var end = Math.min(s + perSplit - 1, numPages - 1);
                pageRanges.push([s, end]);
            }
        } else if (mode === 'size') {
            var targetBytes = (parseInt(sizeSlider.value, 10) || 500) * 1024;
            var bytesPerPage = (selectedFile ? selectedFile.size : 0) / numPages;
            var pagesPerSplit = Math.max(1, Math.round(targetBytes / bytesPerPage));
            for (var t = 0; t < numPages; t += pagesPerSplit) {
                var e = Math.min(t + pagesPerSplit - 1, numPages - 1);
                pageRanges.push([t, e]);
            }
        }

        await ensurePdfLib();
        if (!window.PDFLib) return;

        btnSplit.disabled = true;
        btnSplit.textContent = 'Splitting...';
        splitResults = [];

        try {
            for (var r = 0; r < pageRanges.length; r++) {
                setProgress(r + 1, pageRanges.length, 'Creating split ' + (r + 1) + ' of ' + pageRanges.length + '...');

                var srcPdf = await window.PDFLib.PDFDocument.load(arrayBuffer.slice(0), { ignoreEncryption: true });
                var newPdf = await window.PDFLib.PDFDocument.create();
                var startPage = pageRanges[r][0];
                var endPage = pageRanges[r][1];
                var indices = [];
                for (var p = startPage; p <= endPage; p++) indices.push(p);
                var copied = await newPdf.copyPages(srcPdf, indices);
                copied.forEach(function (cp) { newPdf.addPage(cp); });
                var pdfBytes = await newPdf.save();
                var blob = new Blob([pdfBytes], { type: 'application/pdf' });
                var baseName = (selectedFile ? selectedFile.name.replace(/\.pdf$/i, '') : 'document');
                splitResults.push({
                    blob: blob,
                    name: baseName + '-split-' + (r + 1) + '.pdf',
                    pages: indices,
                    size: blob.size
                });
            }

            renderSplitResults();
            updateUiState();
            saveCache();
            setProgress(pageRanges.length, pageRanges.length, 'Split complete! ' + splitResults.length + ' file(s).');
        } catch (err) {
            setProgress(0, 100, 'Split failed.');
        } finally {
            btnSplit.disabled = false;
            btnSplit.textContent = 'Split PDF';
        }
    }

    function restoreFromCache() {
        try {
            var raw = sessionStorage.getItem(CACHE_KEY);
            if (!raw) return;
            var cached = JSON.parse(raw);
            if (cached.dataUrl) {
                var ab = (function () {
                    var parts = cached.dataUrl.split(',');
                    var bstr = atob(parts[1]);
                    var len = bstr.length;
                    var arr = new Uint8Array(len);
                    for (var i = 0; i < len; i++) arr[i] = bstr.charCodeAt(i);
                    return arr.buffer;
                })();
                arrayBuffer = ab;
                selectedFile = cached.fileName ? { name: cached.fileName } : null;
                pageImages = cached.pageImages || [];

                ensurePdfJs().then(function () {
                    var pdfjs = window.pdfjsLib;
                    pdfjs.getDocument({ data: ab.slice(0) }).promise.then(function (doc) {
                        pdfDoc = doc;
                        renderPagePreviews();
                        updateUiState();
                    });
                });
            }
            if (cached.splitResults && cached.splitResults.length) {
                splitResults = cached.splitResults.map(function (item) {
                    var blob = dataUrlToBlob(item.dataUrl, 'application/pdf');
                    return { blob: blob, name: item.name, pages: item.pages, size: item.size };
                });
                renderSplitResults();
                updateUiState();
            }
        } catch (e) {}
    }

    drop.addEventListener('click', function () { upload.click(); });
    drop.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') upload.click();
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
        if (e.dataTransfer.files[0]) loadFile(e.dataTransfer.files[0]);
    });

    upload.addEventListener('change', function () {
        if (upload.files[0]) loadFile(upload.files[0]);
        upload.value = '';
    });

    btnSplit.addEventListener('click', function () {
        splitPdf();
    });

    btnDownAll.addEventListener('click', async function () {
        if (!splitResults.length) return;
        await ensureJsZip();
        if (!window.JSZip) return;
        var zip = new window.JSZip();
        splitResults.forEach(function (item) {
            zip.file(item.name, item.blob);
        });
        var content = await zip.generateAsync({ type: 'blob' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(content);
        link.download = (selectedFile ? selectedFile.name.replace(/\.pdf$/i, '') : 'document') + '-splits.zip';
        document.body.appendChild(link);
        link.click();
        link.remove();
        setTimeout(function () { URL.revokeObjectURL(link.href); }, 800);
    });

    btnClear.addEventListener('click', function () {
        selectedFile = null;
        arrayBuffer = null;
        pdfDoc = null;
        pageImages = [];
        splitResults = [];
        totalSize = 0;
        pageGrid.innerHTML = '';
        resultsGrid.innerHTML = '';
        pagePreviews.hidden = true;
        resultsWrap.hidden = true;
        btnDownAll.style.display = 'none';
        progress.hidden = true;
        uploadLine.hidden = true;
        progressBar.style.width = '0%';
        progressPct.textContent = '0%';
        btnSplit.disabled = true;
        statPages.textContent = '0';
        statSplits.textContent = '0';
        statSize.textContent = '-';
        clearCache();
    });

    restoreFromCache();
    updateUiState();
})();
JS
        );
    }
}
