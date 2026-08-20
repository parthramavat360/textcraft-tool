<?php
/**
 * Widget: PDF Compressor
 *
 * @package TextCraft_Tools\Widgets
 */

declare( strict_types = 1 );

namespace TextCraft_Tools\Widgets;

defined( 'ABSPATH' ) || exit;

class Widget_Pdf_Compressor extends TextCraft_Base_Widget {

    public function get_name(): string {
        return 'textcraft_pdf_compressor';
    }

    public function get_title(): string {
        return esc_html__( 'PDF Compressor', 'textcraft-tools' );
    }

    public function get_keywords(): array {
        return [ 'pdf compressor', 'compress pdf online', 'reduce pdf file size', 'shrink pdf', 'free online pdf tool' ];
    }

    public function get_icon(): string {
        return 'eicon-document-file';
    }

    protected function render_tool_content( array $settings ): void {
        echo '<div class="tc-pdfc" data-pdf-compressor>';
        echo '<div class="tc-jc-drop" role="button" tabindex="0" aria-label="' . esc_attr__( 'Click or drag a PDF file to reduce its size', 'textcraft-tools' ) . '">';
        echo '<div class="tc-jc-drop__icon">PDF</div>';
        echo '<p class="tc-jc-drop__title">' . esc_html__( 'Click to upload or drag & drop a PDF file', 'textcraft-tools' ) . '</p>';
        echo '<p class="tc-jc-drop__hint">' . esc_html__( 'Compress PDF files online to reduce file size while preserving quality. Perfect for shrinking large PDFs before emailing or uploading — all processed securely in your browser.', 'textcraft-tools' ) . '</p>';
        echo '<input type="file" class="tc-jc-upload" accept="application/pdf,.pdf">';
        echo '</div>';

        echo '<div class="tc-jc-upload-line" hidden><span class="tc-jc-upload-text">' . esc_html__( 'Uploading PDF: 0%', 'textcraft-tools' ) . '</span></div>';

        echo '<div class="tc-jc-options">';
        echo '<div class="tc-jc-option">';
        echo '<label class="tc-label" for="tc-pdfc-level">' . esc_html__( 'Compression Level', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-jc-range">';
        echo '<input type="range" id="tc-pdfc-level" class="tc-jc-quality" min="1" max="3" value="2">';
        echo '<span class="tc-jc-quality-value">' . esc_html__( 'Balanced', 'textcraft-tools' ) . '</span>';
        echo '</div></div>';
        echo '<div class="tc-jc-note">' . esc_html__( 'Light mode retains selectable text when possible. Stronger compression rebuilds pages visually for smaller file sizes.', 'textcraft-tools' ) . '</div>';
        echo '</div>';

        echo '<div class="tc-jc-progress" hidden>';
        echo '<div class="tc-jc-progress__row"><span class="tc-jc-progress-label">' . esc_html__( 'Preparing...', 'textcraft-tools' ) . '</span><span class="tc-jc-progress-pct">0%</span></div>';
        echo '<div class="tc-jc-progress__track"><div class="tc-jc-progress__bar"></div></div>';
        echo '</div>';

        $this->render_button_row(
            [
                [ 'id' => 'tc-pdfc-compress', 'label' => esc_html__( 'Compress PDF', 'textcraft-tools' ), 'variant' => 'primary', 'disabled' => true ],
                [ 'id' => 'tc-pdfc-download', 'label' => esc_html__( 'Download PDF', 'textcraft-tools' ),   'variant' => 'ghost' ],
                [ 'id' => 'tc-pdfc-clear',    'label' => esc_html__( 'Clear All', 'textcraft-tools' ),      'variant' => 'danger' ],
            ]
        );

        $this->render_stat_bar(
            [
                [ 'id' => 'tc-pdfc-stat-original',   'label' => esc_html__( 'Original Size', 'textcraft-tools' ) ],
                [ 'id' => 'tc-pdfc-stat-compressed', 'label' => esc_html__( 'Compressed Size', 'textcraft-tools' ) ],
                [ 'id' => 'tc-pdfc-stat-saved',      'label' => esc_html__( 'Space Saved', 'textcraft-tools' ) ],
            ]
        );

        echo '<div class="tc-jc-results" hidden>';
        echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'PDF Preview', 'textcraft-tools' ) . '</span></div>';
        echo '<div class="tc-jc-grid">';
        echo '<div class="tc-jc-card">';
        echo '<div class="tc-jc-card__preview">';
        echo '<div><span>' . esc_html__( 'Original', 'textcraft-tools' ) . '</span><img class="tc-pdfc-original-preview" alt=""></div>';
        echo '<div><span>' . esc_html__( 'Compressed', 'textcraft-tools' ) . '</span><img class="tc-pdfc-compressed-preview" alt=""></div>';
        echo '</div>';
        echo '<div class="tc-jc-card__body">';
        echo '<div class="tc-jc-card__name tc-pdfc-file-name"></div>';
        echo '<div class="tc-jc-card__meta tc-pdfc-file-meta">' . esc_html__( 'Ready to compress', 'textcraft-tools' ) . '</div>';
        echo '</div></div></div>';
        echo '</div></div>';

        $this->render_inline_script( <<<'JS'
(function () {
    var root = document.querySelector('[data-pdf-compressor]');
    if (!root) return;

    var PDFJS_URL = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
    var PDFJS_WORKER_URL = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    var PDFLIB_URL = 'https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js';
    var drop = root.querySelector('.tc-jc-drop');
    var upload = root.querySelector('.tc-jc-upload');
    var uploadLine = root.querySelector('.tc-jc-upload-line');
    var uploadText = root.querySelector('.tc-jc-upload-text');
    var level = root.querySelector('.tc-jc-quality');
    var levelValue = root.querySelector('.tc-jc-quality-value');
    var progress = root.querySelector('.tc-jc-progress');
    var progressBar = root.querySelector('.tc-jc-progress__bar');
    var progressLabel = root.querySelector('.tc-jc-progress-label');
    var progressPct = root.querySelector('.tc-jc-progress-pct');
    var resultsWrap = root.querySelector('.tc-jc-results');
    var originalPreview = root.querySelector('.tc-pdfc-original-preview');
    var compressedPreview = root.querySelector('.tc-pdfc-compressed-preview');
    var fileName = root.querySelector('.tc-pdfc-file-name');
    var fileMeta = root.querySelector('.tc-pdfc-file-meta');
    var btnCompress = document.getElementById('tc-pdfc-compress');
    var btnDownload = document.getElementById('tc-pdfc-download');
    var btnClear = document.getElementById('tc-pdfc-clear');
    var statOriginal = document.getElementById('tc-pdfc-stat-original');
    var statCompressed = document.getElementById('tc-pdfc-stat-compressed');
    var statSaved = document.getElementById('tc-pdfc-stat-saved');
    var pdfLibPromise = null;
    var pdfJsPromise = null;
    var selectedFile = null;
    var originalBytes = null;
    var pdfDoc = null;
    var compressedBlob = null;

    btnDownload.style.display = 'none';
    statOriginal.textContent = '-';
    statCompressed.textContent = '-';
    statSaved.textContent = '-';

    function loadScript(src) {
        return new Promise(function (resolve, reject) {
            var existing = document.querySelector('script[src="' + src + '"]');
            if (existing) {
                existing.addEventListener('load', resolve, { once: true });
                existing.addEventListener('error', reject, { once: true });
                if (existing.dataset.loaded === 'true') resolve();
                return;
            }
            var script = document.createElement('script');
            script.src = src;
            script.async = true;
            script.onload = function () { script.dataset.loaded = 'true'; resolve(); };
            script.onerror = reject;
            document.head.appendChild(script);
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

    function ensurePdfLib() {
        if (window.PDFLib) return Promise.resolve(window.PDFLib);
        if (!pdfLibPromise) pdfLibPromise = loadScript(PDFLIB_URL).then(function () { return window.PDFLib; });
        return pdfLibPromise;
    }

    function formatSize(bytes) {
        return bytes >= 1048576 ? (bytes / 1048576).toFixed(1) + ' MB' : (bytes / 1024).toFixed(1) + ' KB';
    }

    function outputName(name) {
        return name.replace(/\.pdf$/i, '') + '-compressed.pdf';
    }

    function levelLabel(value) {
        if (String(value) === '1') return 'Light';
        if (String(value) === '3') return 'Strong';
        return 'Balanced';
    }

    function isPdf(file) {
        return file && (file.type === 'application/pdf' || /\.pdf$/i.test(file.name));
    }

    function setUploadLine(done, total) {
        var pct = total ? Math.round((done / total) * 100) : 0;
        uploadLine.hidden = false;
        uploadText.textContent = 'Uploading PDF: ' + pct + '%';
        if (done === total) setTimeout(function () { uploadLine.hidden = true; }, 900);
    }

    function setProgress(done, total, label) {
        var pct = total ? Math.round((done / total) * 100) : 0;
        progress.hidden = false;
        progressBar.style.width = pct + '%';
        progressPct.textContent = pct + '%';
        progressLabel.textContent = label || 'Compressing ' + done + ' of ' + total + '...';
    }

    function readArrayBuffer(file) {
        return new Promise(function (resolve, reject) {
            var reader = new FileReader();
            reader.onload = function () { resolve(reader.result); };
            reader.onerror = reject;
            reader.onprogress = function (event) {
                if (event.lengthComputable) setUploadLine(event.loaded, event.total);
            };
            reader.readAsArrayBuffer(file);
        });
    }

    function canvasToJpegBytes(canvas, quality) {
        return new Promise(function (resolve, reject) {
            canvas.toBlob(function (blob) {
                if (!blob) {
                    reject(new Error('Could not export PDF page image.'));
                    return;
                }
                blob.arrayBuffer().then(function (buffer) {
                    resolve(new Uint8Array(buffer));
                }).catch(reject);
            }, 'image/jpeg', quality);
        });
    }

    async function renderPageToCanvas(pageNumber, scale) {
        var page = await pdfDoc.getPage(pageNumber);
        var viewport = page.getViewport({ scale: scale });
        var canvas = document.createElement('canvas');
        var ctx = canvas.getContext('2d');
        canvas.width = viewport.width;
        canvas.height = viewport.height;
        ctx.fillStyle = '#fff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        await page.render({ canvasContext: ctx, viewport: viewport }).promise;
        return {
            canvas: canvas,
            width: page.getViewport({ scale: 1 }).width,
            height: page.getViewport({ scale: 1 }).height
        };
    }

    async function renderPreview() {
        var rendered = await renderPageToCanvas(1, 1.35);
        var previewUrl = rendered.canvas.toDataURL('image/jpeg', 0.82);
        originalPreview.src = previewUrl;
        if (!compressedPreview.src) compressedPreview.src = previewUrl;
    }

    async function loadFile(file) {
        if (!isPdf(file)) return;
        selectedFile = file;
        originalBytes = await readArrayBuffer(file);
        compressedBlob = null;
        btnDownload.style.display = 'none';
        statOriginal.textContent = formatSize(file.size);
        statCompressed.textContent = '-';
        statSaved.textContent = '-';
        fileName.textContent = file.name;
        fileName.title = file.name;
        fileMeta.textContent = formatSize(file.size) + ' - ready';
        resultsWrap.hidden = false;
        setProgress(8, 100, 'Loading PDF preview...');

        try {
            var pdfjs = await ensurePdfJs();
            pdfDoc = await pdfjs.getDocument({ data: originalBytes.slice(0) }).promise;
            await renderPreview();
            setProgress(100, 100, 'PDF loaded.');
            btnCompress.disabled = false;
        } catch (error) {
            console.error(error);
            fileMeta.textContent = 'Could not open this PDF';
            setProgress(0, 100, 'PDF preview failed.');
        }
    }

    // ── Lossless: strip metadata and use object streams ──────────────────────
    async function compressStructureOnly() {
        var PDFLib = await ensurePdfLib();
        var loaded = await PDFLib.PDFDocument.load(originalBytes.slice(0), { ignoreEncryption: true });
        loaded.setTitle('');
        loaded.setAuthor('');
        loaded.setSubject('');
        loaded.setKeywords([]);
        loaded.setProducer('TextCraft PDF Compressor');
        loaded.setCreator('TextCraft PDF Compressor');
        return loaded.save({ useObjectStreams: true, addDefaultPage: false, objectsPerTick: 50 });
    }

    // ── Lossy: re-render each page as JPEG ───────────────────────────────────
    async function compressVisualPdf(compression) {
        var PDFLib = await ensurePdfLib();
        var output = await PDFLib.PDFDocument.create();
        // Lower scale & quality = smaller output; tuned to actually beat small originals
        var scale   = compression === 3 ? 0.9  : (compression === 2 ? 1.05 : 1.2);
        var quality = compression === 3 ? 0.42 : (compression === 2 ? 0.58 : 0.70);

        for (var i = 1; i <= pdfDoc.numPages; i++) {
            setProgress(i - 1, pdfDoc.numPages, 'Optimizing page ' + i + ' of ' + pdfDoc.numPages + '...');
            var rendered = await renderPageToCanvas(i, scale);
            var jpgBytes = await canvasToJpegBytes(rendered.canvas, quality);
            var jpg = await output.embedJpg(jpgBytes);
            var page = output.addPage([rendered.width, rendered.height]);
            page.drawImage(jpg, { x: 0, y: 0, width: rendered.width, height: rendered.height });
            setProgress(i, pdfDoc.numPages);
        }

        return output.save({ useObjectStreams: true, addDefaultPage: false, objectsPerTick: 50 });
    }

    // ── Pick the candidate that is genuinely smaller than the original ────────
    function pickSmaller(candidates) {
        var best = null;
        var bestSize = originalBytes.byteLength; // must beat the original to qualify
        for (var i = 0; i < candidates.length; i++) {
            if (candidates[i] && candidates[i].byteLength < bestSize) {
                bestSize = candidates[i].byteLength;
                best = candidates[i];
            }
        }
        return best; // null means nothing beat the original
    }

    btnCompress.addEventListener('click', async function () {
        if (!selectedFile || !originalBytes || !pdfDoc) return;
        btnCompress.disabled = true;
        btnCompress.textContent = 'Compressing...';
        btnDownload.style.display = 'none';

        try {
            var compression = parseInt(level.value, 10);
            setProgress(0, 100, 'Compressing PDF...');

            var structBytes = null;
            var visualBytes = null;

            // Pass 1: always try lossless metadata strip (fast)
            try {
                setProgress(10, 100, 'Stripping metadata...');
                structBytes = await compressStructureOnly();
            } catch (e) { console.warn('Structure pass failed', e); }

            // Pass 2: visual re-render for Balanced and Strong only
            if (compression >= 2) {
                try {
                    visualBytes = await compressVisualPdf(compression);
                } catch (e) { console.warn('Visual pass failed', e); }
            }

            // Only use a result if it is actually smaller than the original
            var bestBytes = pickSmaller([structBytes, visualBytes]);

            if (!bestBytes) {
                // Nothing beat the original — hand back the original untouched
                compressedBlob = new Blob([originalBytes], { type: 'application/pdf' });
                statCompressed.textContent = formatSize(selectedFile.size);
                statSaved.textContent = '0 KB (0%)';
                fileMeta.textContent = 'Already fully optimised — no reduction possible';
                setProgress(100, 100, 'Already optimised.');
            } else {
                compressedBlob = new Blob([bestBytes], { type: 'application/pdf' });
                var saved = selectedFile.size - compressedBlob.size;
                var savedPct = Math.round((saved / selectedFile.size) * 100);
                statCompressed.textContent = formatSize(compressedBlob.size);
                statSaved.textContent = formatSize(saved) + ' (' + savedPct + '%)';
                fileMeta.textContent = formatSize(selectedFile.size) + ' → ' + formatSize(compressedBlob.size);
                setProgress(100, 100, 'PDF compressed!');
            }

            compressedPreview.src = originalPreview.src;
            btnDownload.style.display = 'inline-flex';

        } catch (error) {
            console.error(error);
            fileMeta.textContent = 'Compression failed';
            setProgress(0, 100, 'Compression failed.');
        } finally {
            btnCompress.disabled = false;
            btnCompress.textContent = 'Compress PDF';
        }
    });

    btnDownload.addEventListener('click', function () {
        if (!compressedBlob || !selectedFile) return;
        var url = URL.createObjectURL(compressedBlob);
        var link = document.createElement('a');
        link.href = url;
        link.download = outputName(selectedFile.name);
        link.click();
        setTimeout(function () { URL.revokeObjectURL(url); }, 5000);
    });

    btnClear.addEventListener('click', function () {
        selectedFile = null;
        originalBytes = null;
        pdfDoc = null;
        compressedBlob = null;
        originalPreview.removeAttribute('src');
        compressedPreview.removeAttribute('src');
        fileName.textContent = '';
        fileName.removeAttribute('title');
        fileMeta.textContent = 'Ready';
        resultsWrap.hidden = true;
        progress.hidden = true;
        uploadLine.hidden = true;
        progressBar.style.width = '0%';
        progressPct.textContent = '0%';
        btnCompress.disabled = true;
        btnCompress.textContent = 'Compress PDF';
        btnDownload.style.display = 'none';
        statOriginal.textContent = '-';
        statCompressed.textContent = '-';
        statSaved.textContent = '-';
    });

    drop.addEventListener('click', function () { upload.click(); });
    drop.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            upload.click();
        }
    });
    drop.addEventListener('dragover', function (event) {
        event.preventDefault();
        drop.classList.add('is-dragging');
    });
    drop.addEventListener('dragleave', function () { drop.classList.remove('is-dragging'); });
    drop.addEventListener('drop', function (event) {
        event.preventDefault();
        drop.classList.remove('is-dragging');
        if (event.dataTransfer.files[0]) loadFile(event.dataTransfer.files[0]);
    });
    upload.addEventListener('change', function () {
        if (upload.files[0]) loadFile(upload.files[0]);
        upload.value = '';
    });
    level.addEventListener('input', function () {
        levelValue.textContent = levelLabel(level.value);
    });
})();
JS
        );
    }
}