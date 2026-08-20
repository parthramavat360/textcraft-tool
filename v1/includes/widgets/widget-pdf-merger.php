<?php
/**
 * Widget: PDF Merger
 *
 * @package TextCraft_Tools\Widgets
 */

declare( strict_types = 1 );

namespace TextCraft_Tools\Widgets;

defined( 'ABSPATH' ) || exit;

class Widget_Pdf_Merger extends TextCraft_Base_Widget {

    public function get_name(): string {
        return 'textcraft_pdf_merger';
    }

    public function get_title(): string {
        return esc_html__( 'PDF Merger', 'textcraft-tools' );
    }

    public function get_keywords(): array {
        return [ 'pdf merger', 'combine pdf', 'merge pdf files online', 'join pdf', 'free online pdf tool' ];
    }

    public function get_icon(): string {
        return 'eicon-file-download';
    }

    protected function render_tool_content( array $settings ): void {
        echo '<div class="tc-pdfmerger" data-pdf-merger>';

        echo '<div class="tc-jc-drop" role="button" tabindex="0" aria-label="' . esc_attr__( 'Click or drag PDF files to merge them online securely', 'textcraft-tools' ) . '">';
        echo '<div class="tc-jc-drop__icon">PDF</div>';
        echo '<p class="tc-jc-drop__title">' . esc_html__( 'Click to upload or drag & drop PDF files', 'textcraft-tools' ) . '</p>';
        echo '<p class="tc-jc-drop__hint">' . esc_html__( 'Merge multiple PDF files into one document online for free. Combine PDF pages in any order — all processed securely in your browser with no file uploads.', 'textcraft-tools' ) . '</p>';
        echo '<input type="file" class="tc-jc-upload" accept="application/pdf,.pdf" multiple>';
        echo '</div>';

        echo '<div class="tc-jc-upload-line" hidden><span class="tc-jc-upload-text">' . esc_html__( 'Uploading PDFs: 0%', 'textcraft-tools' ) . '</span></div>';

        echo '<div class="tc-jc-progress" hidden>';
        echo '<div class="tc-jc-progress__row"><span class="tc-jc-progress-label">' . esc_html__( 'Preparing...', 'textcraft-tools' ) . '</span><span class="tc-jc-progress-pct">0%</span></div>';
        echo '<div class="tc-jc-progress__track"><div class="tc-jc-progress__bar"></div></div>';
        echo '</div>';

        echo '<div class="tc-jc-results" data-pdfmerger-previews hidden>';
        echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'Loaded PDFs', 'textcraft-tools' ) . '</span></div>';
        echo '<div class="tc-jc-grid tc-pdfmerger-preview-grid"></div>';
        echo '</div>';

        $this->render_button_row(
            [
                [ 'id' => 'tc-pdfmerger-merge',         'label' => esc_html__( 'Merge Files', 'textcraft-tools' ),            'variant' => 'primary', 'disabled' => true ],
                [ 'id' => 'tc-pdfmerger-download-pdf',  'label' => esc_html__( 'Download Merged PDF', 'textcraft-tools' ),   'variant' => 'ghost', 'disabled' => true ],
                [ 'id' => 'tc-pdfmerger-download-all',  'label' => esc_html__( 'Download All as ZIP', 'textcraft-tools' ),    'variant' => 'ghost', 'disabled' => true ],
                [ 'id' => 'tc-pdfmerger-clear',         'label' => esc_html__( 'Clear All', 'textcraft-tools' ),             'variant' => 'danger' ],
            ]
        );

        $this->render_stat_bar(
            [
                [ 'id' => 'tc-pdfmerger-stat-loaded', 'label' => esc_html__( 'Files Loaded', 'textcraft-tools' ) ],
                [ 'id' => 'tc-pdfmerger-stat-pages',  'label' => esc_html__( 'Total Pages', 'textcraft-tools' ) ],
                [ 'id' => 'tc-pdfmerger-stat-size',   'label' => esc_html__( 'Output Size', 'textcraft-tools' ) ],
            ]
        );

        echo '</div>';

        $this->render_inline_script( <<<'JS'
(function () {
    var root = document.querySelector('[data-pdf-merger]');
    if (!root) return;

    var PDFJS_URL        = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
    var PDFJS_WORKER_URL = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    var PDFLIB_URL       = 'https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js';
    var JSZIP_URL        = 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js';
    var CACHE_KEY        = 'tc_pdfmerger_session';

    var drop          = root.querySelector('.tc-jc-drop');
    var upload        = root.querySelector('.tc-jc-upload');
    var uploadLine    = root.querySelector('.tc-jc-upload-line');
    var uploadText    = root.querySelector('.tc-jc-upload-text');
    var progress      = root.querySelector('.tc-jc-progress');
    var progressBar   = root.querySelector('.tc-jc-progress__bar');
    var progressLbl   = root.querySelector('.tc-jc-progress-label');
    var progressPct   = root.querySelector('.tc-jc-progress-pct');
    var previewsWrap  = root.querySelector('[data-pdfmerger-previews]');
    var previewGrid   = root.querySelector('.tc-pdfmerger-preview-grid');
    var btnMerge      = document.getElementById('tc-pdfmerger-merge');
    var btnDownload   = document.getElementById('tc-pdfmerger-download-pdf');
    var btnDownAll    = document.getElementById('tc-pdfmerger-download-all');
    var btnClear      = document.getElementById('tc-pdfmerger-clear');
    var statLoaded    = document.getElementById('tc-pdfmerger-stat-loaded');
    var statPages     = document.getElementById('tc-pdfmerger-stat-pages');
    var statSize      = document.getElementById('tc-pdfmerger-stat-size');

    var files      = []; // [{name, dataUrl, arrayBuffer, pageCount, previewUrl}]
    var mergedBlob = null;
    var mergedName = 'merged-document.pdf';
    var scriptPromises = {};
    var totalPages = 0;

    statLoaded.textContent = '0';
    statPages.textContent  = '0';
    statSize.textContent   = '-';

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

    function setUploadLine(done, total) {
        var pct = total ? Math.round((done / total) * 100) : 0;
        uploadLine.hidden = false;
        uploadText.textContent = 'Uploading PDFs: ' + pct + '%';
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

    function resetMergeResult() {
        mergedBlob = null;
        btnDownload.disabled = true;
        statSize.textContent = '-';
    }

    function saveCache() {
        try {
            var payload = {
                files: files.map(function (item) {
                    return { name: item.name, dataUrl: item.dataUrl, pageCount: item.pageCount, previewUrl: item.previewUrl };
                }),
                mergedName: mergedName,
            };
            if (mergedBlob) {
                var fr = new FileReader();
                fr.onload = function () {
                    payload.merged = fr.result;
                    sessionStorage.setItem(CACHE_KEY, JSON.stringify(payload));
                };
                fr.readAsDataURL(mergedBlob);
            } else {
                sessionStorage.setItem(CACHE_KEY, JSON.stringify(payload));
            }
        } catch (e) {}
    }

    function clearCache() {
        try { sessionStorage.removeItem(CACHE_KEY); } catch (e) {}
    }

    function renderPreviews() {
        previewGrid.innerHTML = '';
        if (!files.length) {
            previewsWrap.hidden = true;
            return;
        }
        files.forEach(function (item, idx) {
            var card = document.createElement('div');
            card.className = 'tc-jc-card';

            var preview = document.createElement('div');
            preview.className = 'tc-jc-card__preview';
            var wrap = document.createElement('div');
            var label = document.createElement('span');
            label.textContent = 'PDF ' + (idx + 1);
            var img = document.createElement('img');
            img.src = item.previewUrl;
            img.alt = item.name;
            wrap.appendChild(label);
            wrap.appendChild(img);
            preview.appendChild(wrap);

            var body = document.createElement('div');
            body.className = 'tc-jc-card__body';
            var name = document.createElement('div');
            name.className = 'tc-jc-card__name';
            name.textContent = item.name;
            name.title = item.name;
            body.appendChild(name);
            var meta = document.createElement('div');
            meta.className = 'tc-jc-card__meta';
            meta.textContent = item.pageCount + ' page' + (item.pageCount > 1 ? 's' : '');
            body.appendChild(meta);

            card.appendChild(preview);
            card.appendChild(body);
            previewGrid.appendChild(card);
        });
        previewsWrap.hidden = false;
    }

    function updateUiState() {
        var hasFiles    = files.length > 0;
        var canMerge    = files.length >= 2;
        btnMerge.disabled   = !canMerge;
        btnDownAll.disabled  = !hasFiles;
        btnDownload.disabled = !mergedBlob;
        statLoaded.textContent = String(files.length);
        statPages.textContent  = String(totalPages);
    }

    function arrayBufferToBase64(buffer) {
        var binary = '';
        var bytes = new Uint8Array(buffer);
        var len = bytes.byteLength;
        for (var i = 0; i < len; i++) binary += String.fromCharCode(bytes[i]);
        return btoa(binary);
    }

    function dataUrlToArrayBuffer(dataUrl) {
        var parts = dataUrl.split(',');
        var bstr = atob(parts[1]);
        var len = bstr.length;
        var arr = new Uint8Array(len);
        for (var i = 0; i < len; i++) arr[i] = bstr.charCodeAt(i);
        return arr.buffer;
    }

    function dataUrlToBlob(dataUrl, mime) {
        var parts = dataUrl.split(',');
        var bstr = atob(parts[1]);
        var len = bstr.length;
        var arr = new Uint8Array(len);
        for (var i = 0; i < len; i++) arr[i] = bstr.charCodeAt(i);
        return new Blob([arr], { type: mime || 'application/pdf' });
    }

    async function renderPdfPreview(arrayBuffer) {
        var pdfjs = await ensurePdfJs();
        var pdfDoc = await pdfjs.getDocument({ data: arrayBuffer.slice(0) }).promise;
        var page = await pdfDoc.getPage(1);
        var viewport = page.getViewport({ scale: 0.5 });
        var canvas = document.createElement('canvas');
        canvas.width = viewport.width;
        canvas.height = viewport.height;
        var ctx = canvas.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        await page.render({ canvasContext: ctx, viewport: viewport }).promise;
        return { previewUrl: canvas.toDataURL('image/jpeg', 0.6), pageCount: pdfDoc.numPages };
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

    async function addFiles(fileList) {
        var incoming = Array.from(fileList || []).filter(function (file) {
            return /application\/pdf/i.test(file.type) || /\.pdf$/i.test(file.name);
        });
        if (!incoming.length) return;

        await ensurePdfJs();

        var total = incoming.length;
        var done = 0;

        for (var i = 0; i < incoming.length; i++) {
            var file = incoming[i];
            try {
                var fr = new FileReader();
                var arrayBuffer = await new Promise(function (resolve, reject) {
                    fr.onload = function () { resolve(fr.result); };
                    fr.onerror = reject;
                    fr.readAsArrayBuffer(file);
                });

                var result = await renderPdfPreview(arrayBuffer);
                var base64 = arrayBufferToBase64(arrayBuffer);
                var dataUrl = 'data:application/pdf;base64,' + base64;

                files.push({
                    name: file.name,
                    dataUrl: dataUrl,
                    arrayBuffer: arrayBuffer,
                    pageCount: result.pageCount,
                    previewUrl: result.previewUrl
                });
                totalPages += result.pageCount;
            } catch (e) {}

            done += 1;
            setUploadLine(done, total);
        }

        renderPreviews();
        resetMergeResult();
        updateUiState();
        saveCache();
    }

    async function mergePdfs() {
        if (files.length < 2) return;

        await Promise.all([ensurePdfLib(), ensurePdfJs()]);
        if (!window.PDFLib) return;

        btnMerge.disabled = true;
        btnMerge.textContent = 'Merging...';

        try {
            var mergedPdf = await window.PDFLib.PDFDocument.create();

            for (var i = 0; i < files.length; i++) {
                setProgress(i + 1, files.length, 'Merging ' + (i + 1) + ' of ' + files.length + '...');

                var srcPdf = await window.PDFLib.PDFDocument.load(files[i].arrayBuffer, { ignoreEncryption: true });
                var indices = srcPdf.getPageIndices();
                var copiedPages = await mergedPdf.copyPages(srcPdf, indices);
                copiedPages.forEach(function (page) {
                    mergedPdf.addPage(page);
                });
            }

            var pdfBytes = await mergedPdf.save();
            mergedBlob = new Blob([pdfBytes], { type: 'application/pdf' });
            statSize.textContent = formatSize(mergedBlob.size);
            btnDownload.disabled = false;
            saveCache();
            setProgress(files.length, files.length, 'Merge complete!');
        } catch (err) {
            setProgress(0, 100, 'Merge failed.');
        } finally {
            btnMerge.disabled = false;
            btnMerge.textContent = 'Merge PDFs';
        }
    }

    function restoreFromCache() {
        try {
            var raw = sessionStorage.getItem(CACHE_KEY);
            if (!raw) return;
            var cached = JSON.parse(raw);
            if (!Array.isArray(cached.files) || !cached.files.length) return;
            if (cached.mergedName) mergedName = cached.mergedName;

            files = [];
            totalPages = 0;
            cached.files.forEach(function (item) {
                var ab = dataUrlToArrayBuffer(item.dataUrl);
                files.push({
                    name: item.name,
                    dataUrl: item.dataUrl,
                    arrayBuffer: ab,
                    pageCount: item.pageCount,
                    previewUrl: item.previewUrl
                });
                totalPages += item.pageCount;
            });

            if (cached.merged) {
                mergedBlob = dataUrlToBlob(cached.merged, 'application/pdf');
                statSize.textContent = formatSize(mergedBlob.size);
            }

            renderPreviews();
            updateUiState();
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
        addFiles(e.dataTransfer.files);
    });

    upload.addEventListener('change', function () {
        addFiles(upload.files);
        upload.value = '';
    });

    btnMerge.addEventListener('click', function () {
        mergePdfs();
    });

    btnDownload.addEventListener('click', function () {
        if (!mergedBlob) return;
        var link = document.createElement('a');
        link.href = URL.createObjectURL(mergedBlob);
        link.download = mergedName;
        document.body.appendChild(link);
        link.click();
        link.remove();
        setTimeout(function () { URL.revokeObjectURL(link.href); }, 800);
    });

    btnDownAll.addEventListener('click', async function () {
        if (!files.length) return;
        await ensureJsZip();
        if (!window.JSZip) return;
        var zip = new window.JSZip();
        files.forEach(function (item, idx) {
            var blob = dataUrlToBlob(item.dataUrl, 'application/pdf');
            zip.file(item.name || ('pdf-' + (idx + 1) + '.pdf'), blob);
        });
        var content = await zip.generateAsync({ type: 'blob' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(content);
        link.download = 'pdf-files.zip';
        document.body.appendChild(link);
        link.click();
        link.remove();
        setTimeout(function () { URL.revokeObjectURL(link.href); }, 800);
    });

    btnClear.addEventListener('click', function () {
        files = [];
        mergedBlob = null;
        totalPages = 0;
        renderPreviews();
        updateUiState();
        statSize.textContent = '-';
        progress.hidden = true;
        uploadLine.hidden = true;
        clearCache();
    });

    restoreFromCache();
    updateUiState();
})();
JS
        );
    }
}
