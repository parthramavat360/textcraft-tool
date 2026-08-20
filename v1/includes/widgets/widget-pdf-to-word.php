<?php
/**
 * Widget: PDF to Word Converter
 *
 * Server-side conversion using LibreOffice (soffice) — no external API required.
 * Preserves headings, fonts, tables, images, columns, and page layout.
 *
 * Requirements (server):
 *   - LibreOffice >= 6.0  (apt install libreoffice  OR  yum install libreoffice)
 *   - PHP exec() or shell_exec() enabled
 *   - Writable /tmp directory
 *
 * @package TextCraft_Tools\Widgets
 */

declare( strict_types = 1 );

namespace TextCraft_Tools\Widgets;

defined( 'ABSPATH' ) || exit;

/* =========================================================================
 * REST ENDPOINT REGISTRATION
 * Register once when the class file is loaded.
 * ========================================================================= */
add_action( 'rest_api_init', function () {
    register_rest_route( 'textcraft-tools/v1', '/pdf-to-word', [
        'methods'             => 'POST',
        'callback'            => [ Widget_Pdf_To_Word::class, 'rest_convert' ],
        'permission_callback' => function () {
            // Requires a logged-in user by default. Remove check if you want guests.
            return current_user_can( 'read' );
        },
    ] );
} );


/* =========================================================================
 * WIDGET CLASS
 * ========================================================================= */
class Widget_Pdf_To_Word extends TextCraft_Base_Widget {

    /* -----------------------------------------------------------------------
     * Elementor identity
     * --------------------------------------------------------------------- */

    public function get_name(): string {
        return 'textcraft_pdf_to_word';
    }

    public function get_title(): string {
        return esc_html__( 'PDF to Word Converter', 'textcraft-tools' );
    }

    public function get_keywords(): array {
        return [ 'pdf to word', 'convert pdf to docx', 'pdf to word converter', 'pdf to docx online', 'free online pdf converter' ];
    }

    public function get_icon(): string {
        return 'eicon-document-file';
    }

    /* -----------------------------------------------------------------------
     * Front-end markup
     * --------------------------------------------------------------------- */

    protected function render_tool_content( array $settings ): void {
        $endpoint = esc_url_raw( rest_url( 'textcraft-tools/v1/pdf-to-word' ) );
        $nonce    = wp_create_nonce( 'wp_rest' );
        ?>
        <div class="tc-pdfw" data-pdf-to-word>

            <!-- Drop zone -->
            <div class="tc-jc-drop tc-pdfw__drop" role="button" tabindex="0"
                aria-label="<?php esc_attr_e( 'Click or drag a PDF file to convert to Word', 'textcraft-tools' ); ?>">
                <div class="tc-jc-drop__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="56" height="56" fill="none">
                        <rect width="64" height="64" rx="12" fill="#E8F0FE"/>
                        <path d="M20 14h16l12 12v28a2 2 0 0 1-2 2H20a2 2 0 0 1-2-2V16a2 2 0 0 1 2-2z" fill="#4285F4" opacity=".15"/>
                        <path d="M36 14l12 12H38a2 2 0 0 1-2-2V14z" fill="#4285F4" opacity=".35"/>
                        <text x="22" y="45" font-size="11" font-weight="700" fill="#4285F4" font-family="Arial,sans-serif">PDF</text>
                        <path d="M44 50l-6-6m6 0l-6 6" stroke="#DB4437" stroke-width="2" stroke-linecap="round"/>
                        <path d="M50 44h4m-4 6h4" stroke="#0F9D58" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <p class="tc-jc-drop__title"><?php esc_html_e( 'Click to upload or drag & drop a PDF', 'textcraft-tools' ); ?></p>
                <p class="tc-jc-drop__hint"><?php esc_html_e( 'Upload a PDF and convert it to an editable Word DOCX document — headings, tables, images and layout are preserved', 'textcraft-tools' ); ?></p>
                <input type="file" class="tc-jc-upload" accept="application/pdf,.pdf" aria-hidden="true">
            </div>

            <!-- Upload progress line -->
            <div class="tc-jc-upload-line" hidden>
                <span class="tc-jc-upload-text"><?php esc_html_e( 'Uploading PDF: 0%', 'textcraft-tools' ); ?></span>
                <div class="tc-jc-upload-track"><div class="tc-jc-upload-bar"></div></div>
            </div>

            <!-- Conversion progress bar -->
            <div class="tc-jc-progress" hidden>
                <div class="tc-jc-progress__row">
                    <span class="tc-jc-progress-label"><?php esc_html_e( 'Preparing…', 'textcraft-tools' ); ?></span>
                    <span class="tc-jc-progress-pct">0%</span>
                </div>
                <div class="tc-jc-progress__track"><div class="tc-jc-progress__bar"></div></div>
            </div>

            <!-- Action buttons -->
            <?php $this->render_button_row( [
                [ 'id' => 'tc-pdfw-convert',  'label' => esc_html__( 'Convert to Word', 'textcraft-tools' ), 'variant' => 'primary', 'disabled' => true ],
                [ 'id' => 'tc-pdfw-download', 'label' => esc_html__( 'Download DOCX',   'textcraft-tools' ), 'variant' => 'ghost'   ],
                [ 'id' => 'tc-pdfw-clear',    'label' => esc_html__( 'Clear All',        'textcraft-tools' ), 'variant' => 'danger'  ],
            ] ); ?>

            <!-- Stat bar -->
            <?php $this->render_stat_bar( [
                [ 'id' => 'tc-pdfw-stat-pages',  'label' => esc_html__( 'Pages',       'textcraft-tools' ) ],
                [ 'id' => 'tc-pdfw-stat-size',   'label' => esc_html__( 'Output Size', 'textcraft-tools' ) ],
                [ 'id' => 'tc-pdfw-stat-status', 'label' => esc_html__( 'Status',      'textcraft-tools' ) ],
            ] ); ?>

            <!-- Results card -->
            <div class="tc-jc-results" hidden>
                <div class="tc-label-row"><span class="tc-label"><?php esc_html_e( 'Conversion Summary', 'textcraft-tools' ); ?></span></div>
                <div class="tc-jc-grid">
                    <div class="tc-jc-card">
                        <div class="tc-jc-card__preview">
                            <span class="tc-pdfw-preview-label"><?php esc_html_e( 'PDF Preview', 'textcraft-tools' ); ?></span>
                            <canvas class="tc-pdfw-canvas" aria-label="<?php esc_attr_e( 'First page preview', 'textcraft-tools' ); ?>"></canvas>
                        </div>
                        <div class="tc-jc-card__body">
                            <div class="tc-jc-card__name tc-pdfw-file-name"></div>
                            <div class="tc-jc-card__meta tc-pdfw-file-meta"><?php esc_html_e( 'Ready', 'textcraft-tools' ); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info note -->
            <p class="tc-jc-note">
                <?php esc_html_e( 'Conversion runs entirely on your own server using LibreOffice — no data is sent to any third party, keeping your documents secure and private.', 'textcraft-tools' ); ?>
            </p>

        </div><!-- .tc-pdfw -->

        <?php
        // Pass PHP config to JS
        $this->render_inline_script(
            'window.tcPdfToWordConfig = ' . wp_json_encode( [
                'endpoint' => $endpoint,
                'nonce'    => $nonce,
                'i18n'     => [
                    'uploading'       => __( 'Uploading PDF: %d%%', 'textcraft-tools' ),
                    'loadingEngine'   => __( 'Loading PDF engine…', 'textcraft-tools' ),
                    'openingPdf'      => __( 'Opening PDF…', 'textcraft-tools' ),
                    'pdfLoaded'       => __( 'PDF loaded and ready to convert to Word', 'textcraft-tools' ),
                    'uploading2'      => __( 'Uploading to converter…', 'textcraft-tools' ),
                    'converting'      => __( 'Converting PDF to DOCX — this may take a moment', 'textcraft-tools' ),
                    'done'            => __( 'Word document ready for download!', 'textcraft-tools' ),
                    'failed'          => __( 'Conversion failed — please try again', 'textcraft-tools' ),
                    'unsupported'     => __( 'Please upload a valid PDF file', 'textcraft-tools' ),
                    'noExec'          => __( 'Server cannot run LibreOffice. Please contact your hosting provider.', 'textcraft-tools' ),
                    'convertBtn'      => __( 'Convert to Word', 'textcraft-tools' ),
                    'convertingBtn'   => __( 'Converting to DOCX…', 'textcraft-tools' ),
                ],
            ] ) . ';' . "\n" . $this->get_inline_js()
        );
    }

    /* -----------------------------------------------------------------------
     * Inline JavaScript (runs inside IIFE)
     * --------------------------------------------------------------------- */

    private function get_inline_js(): string {
        return <<<'JS'
(function () {
    'use strict';

    var cfg  = window.tcPdfToWordConfig || {};
    var i18n = cfg.i18n || {};
    var root = document.querySelector('[data-pdf-to-word]');
    if (!root) return;

    /* ---- PDF.js CDN ---- */
    var PDFJS_URL        = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
    var PDFJS_WORKER_URL = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    /* ---- DOM refs ---- */
    var drop        = root.querySelector('.tc-jc-drop');
    var uploadInput = root.querySelector('.tc-jc-upload');
    var uploadLine  = root.querySelector('.tc-jc-upload-line');
    var uploadText  = root.querySelector('.tc-jc-upload-text');
    var uploadBar   = root.querySelector('.tc-jc-upload-bar');
    var progressWrap  = root.querySelector('.tc-jc-progress');
    var progressBar   = root.querySelector('.tc-jc-progress__bar');
    var progressLabel = root.querySelector('.tc-jc-progress-label');
    var progressPct   = root.querySelector('.tc-jc-progress-pct');
    var resultsWrap = root.querySelector('.tc-jc-results');
    var canvas      = root.querySelector('.tc-pdfw-canvas');
    var fileNameEl  = root.querySelector('.tc-pdfw-file-name');
    var fileMetaEl  = root.querySelector('.tc-pdfw-file-meta');
    var btnConvert  = document.getElementById('tc-pdfw-convert');
    var btnDownload = document.getElementById('tc-pdfw-download');
    var btnClear    = document.getElementById('tc-pdfw-clear');
    var statPages   = document.getElementById('tc-pdfw-stat-pages');
    var statSize    = document.getElementById('tc-pdfw-stat-size');
    var statStatus  = document.getElementById('tc-pdfw-stat-status');

    /* ---- State ---- */
    var pdfLibLoaded  = null;   // Promise
    var pdfDoc        = null;
    var selectedFile  = null;
    var docxBlob      = null;
    var outputFileName = '';

    /* ---- Init ---- */
    btnDownload.style.display = 'none';
    resetStats();

    /* ================================================================
     * Helpers
     * ============================================================== */

    function fmt(bytes) {
        if (!bytes) return '-';
        return bytes >= 1048576
            ? (bytes / 1048576).toFixed(1) + ' MB'
            : (bytes / 1024).toFixed(1) + ' KB';
    }

    function docxName(name) {
        return name.replace(/\.pdf$/i, '') + '.docx';
    }

    function resetStats() {
        statPages.textContent  = '0';
        statSize.textContent   = '-';
        statStatus.textContent = 'Ready';
    }

    function setProgress(pct, label) {
        progressWrap.hidden = false;
        progressBar.style.width  = pct + '%';
        progressPct.textContent  = pct + '%';
        progressLabel.textContent = label || '';
    }

    function hideProgress() {
        progressWrap.hidden = true;
        progressBar.style.width = '0%';
        progressPct.textContent = '0%';
    }

    function setUploadProgress(pct) {
        uploadLine.hidden = false;
        uploadText.textContent = (i18n.uploading || 'Uploading PDF: %d%%').replace('%d', pct);
        if (uploadBar) uploadBar.style.width = pct + '%';
        if (pct >= 100) setTimeout(function () { uploadLine.hidden = true; }, 800);
    }

    /* ================================================================
     * PDF.js loading & preview
     * ============================================================== */

    function ensurePdfJs() {
        if (window.pdfjsLib) return Promise.resolve(window.pdfjsLib);
        if (!pdfLibLoaded) {
            pdfLibLoaded = new Promise(function (resolve, reject) {
                var s = document.createElement('script');
                s.src = PDFJS_URL;
                s.async = true;
                s.onload = function () {
                    window.pdfjsLib.GlobalWorkerOptions.workerSrc = PDFJS_WORKER_URL;
                    resolve(window.pdfjsLib);
                };
                s.onerror = reject;
                document.head.appendChild(s);
            });
        }
        return pdfLibLoaded;
    }

    async function renderPreview() {
        if (!pdfDoc || !canvas) return;
        try {
            var page     = await pdfDoc.getPage(1);
            var scale    = Math.min(1.5, 280 / page.getViewport({ scale: 1 }).width);
            var viewport = page.getViewport({ scale: scale });
            canvas.width  = viewport.width;
            canvas.height = viewport.height;
            await page.render({ canvasContext: canvas.getContext('2d'), viewport: viewport }).promise;
        } catch (e) { /* preview is best-effort */ }
    }

    /* ================================================================
     * File loading
     * ============================================================== */

    function isPdf(file) {
        return file && (file.type === 'application/pdf' || /\.pdf$/i.test(file.name));
    }

    function readArrayBuffer(file) {
        return new Promise(function (resolve, reject) {
            var fr = new FileReader();
            fr.onload  = function () { resolve(fr.result); };
            fr.onerror = reject;
            fr.onprogress = function (e) {
                if (e.lengthComputable) setUploadProgress(Math.round(e.loaded / e.total * 100));
            };
            fr.readAsArrayBuffer(file);
        });
    }

    async function loadFile(file) {
        if (!isPdf(file)) {
            statStatus.textContent = i18n.unsupported || 'Invalid file';
            return;
        }

        /* Reset */
        selectedFile  = file;
        pdfDoc        = null;
        docxBlob      = null;
        outputFileName = '';
        btnDownload.style.display = 'none';
        btnConvert.disabled = true;
        resultsWrap.hidden  = false;
        fileNameEl.textContent = file.name;
        fileMetaEl.textContent = fmt(file.size) + ' — loading…';
        statStatus.textContent = 'Loading';
        setProgress(5, i18n.loadingEngine || 'Loading PDF engine…');

        try {
            var lib    = await ensurePdfJs();
            var buffer = await readArrayBuffer(file);
            setUploadProgress(100);
            setProgress(30, i18n.openingPdf || 'Opening PDF…');

            pdfDoc = await lib.getDocument({ data: buffer }).promise;
            statPages.textContent = String(pdfDoc.numPages);
            statStatus.textContent = 'Loaded';
            fileMetaEl.textContent = fmt(file.size) + ' — ' + (i18n.pdfLoaded || 'ready to convert');
            setProgress(100, i18n.pdfLoaded || 'PDF loaded');
            await renderPreview();
            btnConvert.disabled = false;
        } catch (err) {
            console.error('[PDF→Word] Load error:', err);
            statStatus.textContent = 'Error';
            fileMetaEl.textContent = 'Could not open PDF';
            setProgress(0, 'Load failed');
        }
    }

    /* ================================================================
     * Server conversion via REST
     * ============================================================== */

    async function convertOnServer(file) {
        if (!cfg.endpoint || !cfg.nonce) {
            throw new Error('REST endpoint not configured.');
        }

        /* Upload with XHR so we can track progress */
        return new Promise(function (resolve, reject) {
            var fd  = new FormData();
            fd.append('pdf', file, file.name);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', cfg.endpoint, true);
            xhr.setRequestHeader('X-WP-Nonce', cfg.nonce);

            xhr.upload.onprogress = function (e) {
                if (e.lengthComputable) {
                    var pct = Math.round(e.loaded / e.total * 60); // 0-60 %
                    setProgress(pct, i18n.uploading2 || 'Uploading…');
                }
            };

            xhr.onload = function () {
                var payload = {};
                try { payload = JSON.parse(xhr.responseText); } catch (e) {}

                if (xhr.status >= 200 && xhr.status < 300) {
                    resolve(payload);
                } else {
                    reject(new Error(payload.message || 'HTTP ' + xhr.status));
                }
            };

            xhr.onerror = function () { reject(new Error('Network error')); };
            xhr.send(fd);
        }).then(function (payload) {
            setProgress(95, i18n.converting || 'Finalising…');

            /* Decode base64 → Blob */
            var raw  = atob(payload.content || '');
            var buf  = new Uint8Array(raw.length);
            for (var i = 0; i < raw.length; i++) buf[i] = raw.charCodeAt(i);
            var blob = new Blob([buf], { type: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' });

            return {
                blob:     blob,
                filename: payload.filename || docxName(file.name),
                bytes:    payload.bytes    || blob.size,
            };
        });
    }

    /* ================================================================
     * Button handlers
     * ============================================================== */

    btnConvert.addEventListener('click', async function () {
        if (!selectedFile) return;

        btnConvert.disabled = true;
        btnConvert.textContent = i18n.convertingBtn || 'Converting…';
        btnDownload.style.display = 'none';
        statStatus.textContent = 'Converting';
        setProgress(10, i18n.uploading2 || 'Uploading PDF…');

        try {
            var result = await convertOnServer(selectedFile);
            docxBlob       = result.blob;
            outputFileName = result.filename;

            statSize.textContent   = fmt(result.bytes || docxBlob.size);
            statStatus.textContent = 'Done';
            fileMetaEl.textContent = fmt(selectedFile.size) + ' → ' + fmt(docxBlob.size) + ' DOCX';
            btnDownload.style.display = 'inline-flex';
            setProgress(100, i18n.done || 'Word document ready!');

        } catch (err) {
            console.error('[PDF→Word] Convert error:', err);
            statStatus.textContent = 'Failed';
            fileMetaEl.textContent = err.message || (i18n.failed || 'Conversion failed');
            setProgress(0, i18n.failed || 'Conversion failed');
        } finally {
            btnConvert.disabled = false;
            btnConvert.textContent = i18n.convertBtn || 'Convert to Word';
        }
    });

    btnDownload.addEventListener('click', function () {
        if (!docxBlob) return;
        var url  = URL.createObjectURL(docxBlob);
        var link = document.createElement('a');
        link.href     = url;
        link.download = outputFileName || docxName(selectedFile.name);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        setTimeout(function () { URL.revokeObjectURL(url); }, 5000);
    });

    btnClear.addEventListener('click', function () {
        pdfDoc        = null;
        selectedFile  = null;
        docxBlob      = null;
        outputFileName = '';

        if (canvas) {
            var ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }
        fileNameEl.textContent = '';
        fileMetaEl.textContent = 'Ready';
        resultsWrap.hidden = true;
        uploadLine.hidden  = true;
        hideProgress();

        btnConvert.disabled = true;
        btnConvert.textContent = i18n.convertBtn || 'Convert to Word';
        btnDownload.style.display = 'none';
        resetStats();
    });

    /* ================================================================
     * Drop zone interactions
     * ============================================================== */

    drop.addEventListener('click', function () { uploadInput.click(); });
    drop.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); uploadInput.click(); }
    });
    drop.addEventListener('dragover', function (e) {
        e.preventDefault();
        drop.classList.add('is-dragging');
    });
    drop.addEventListener('dragleave', function () { drop.classList.remove('is-dragging'); });
    drop.addEventListener('drop', function (e) {
        e.preventDefault();
        drop.classList.remove('is-dragging');
        var f = e.dataTransfer.files[0];
        if (f) loadFile(f);
    });
    uploadInput.addEventListener('change', function () {
        if (uploadInput.files[0]) loadFile(uploadInput.files[0]);
        uploadInput.value = '';
    });

})();
JS;
    }

    /* -----------------------------------------------------------------------
     * REST endpoint callback — server-side conversion
     * --------------------------------------------------------------------- */

    /**
     * Converts an uploaded PDF to DOCX using LibreOffice on the server.
     *
     * LibreOffice preserves:
     *  - Headings (H1–H6 styles mapped to Word styles)
     *  - Paragraph formatting (alignment, indents, spacing)
     *  - Tables with cell formatting
     *  - Inline images and figures
     *  - Fonts and text decoration (bold, italic, underline)
     *  - Multi-column layouts (approximated)
     *  - Page size and margins
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response|\WP_Error
     */
    public static function rest_convert( \WP_REST_Request $request ) {
        /* ---- Pre-flight: can we run LibreOffice? ---- */
        if ( ! self::exec_enabled() ) {
            return new \WP_Error(
                'no_exec',
                __( 'PHP exec() is disabled on this server. Please enable it or contact your hosting provider.', 'textcraft-tools' ),
                [ 'status' => 500 ]
            );
        }

        $soffice = self::find_soffice();
        if ( ! $soffice ) {
            return new \WP_Error(
                'no_libreoffice',
                __( 'LibreOffice (soffice) is not installed. Install it via: sudo apt install libreoffice', 'textcraft-tools' ),
                [ 'status' => 500 ]
            );
        }

        /* ---- Validate uploaded file ---- */
        $files = $request->get_file_params();
        if ( empty( $files['pdf'] ) || $files['pdf']['error'] !== UPLOAD_ERR_OK ) {
            return new \WP_Error( 'no_file', __( 'No PDF file received.', 'textcraft-tools' ), [ 'status' => 400 ] );
        }

        $upload = $files['pdf'];

        /* Size limit: 50 MB */
        $max_bytes = (int) apply_filters( 'textcraft_pdf_to_word_max_size', 50 * 1024 * 1024 );
        if ( $upload['size'] > $max_bytes ) {
            return new \WP_Error( 'too_large', __( 'PDF exceeds the 50 MB limit.', 'textcraft-tools' ), [ 'status' => 413 ] );
        }

        /* MIME check */
        $finfo    = new \finfo( FILEINFO_MIME_TYPE );
        $detected = $finfo->file( $upload['tmp_name'] );
        if ( $detected !== 'application/pdf' ) {
            return new \WP_Error( 'invalid_type', __( 'Uploaded file is not a PDF.', 'textcraft-tools' ), [ 'status' => 415 ] );
        }

        /* ---- Set up temp workspace ---- */
        $tmp_dir  = rtrim( sys_get_temp_dir(), DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . 'tc_pdfw_' . wp_generate_password( 12, false );
        if ( ! wp_mkdir_p( $tmp_dir ) ) {
            return new \WP_Error( 'tmp_fail', __( 'Cannot create temp directory.', 'textcraft-tools' ), [ 'status' => 500 ] );
        }

        /* Sanitise original filename for use on disk */
        $original_name = sanitize_file_name( $upload['name'] );
        $pdf_path      = $tmp_dir . DIRECTORY_SEPARATOR . $original_name;
        $docx_name     = preg_replace( '/\.pdf$/i', '', $original_name ) . '.docx';
        $docx_path     = $tmp_dir . DIRECTORY_SEPARATOR . $docx_name;

        /* Move uploaded PDF into temp dir */
        if ( ! move_uploaded_file( $upload['tmp_name'], $pdf_path ) ) {
            self::cleanup( $tmp_dir );
            return new \WP_Error( 'move_fail', __( 'Failed to move uploaded file.', 'textcraft-tools' ), [ 'status' => 500 ] );
        }

        /* ---- Run LibreOffice conversion ---- */
        /*
         * LibreOffice flags used:
         *   --headless            Run without GUI
         *   --infilter="writer_pdf_import"
         *                         Use the Writer PDF import filter (editable text,
         *                         not image-based) — the key to format fidelity
         *   --convert-to docx     Output format
         *   --outdir              Output directory
         *
         * The Writer PDF import filter re-flows text, maps font sizes to Word
         * heading styles, and embeds images — matching ilovepdf-style output.
         */
        $cmd = sprintf(
            'HOME=%s %s --headless --norestore --nofirststartwizard --infilter=%s --convert-to docx --outdir %s %s 2>&1',
            escapeshellarg( $tmp_dir ),
            escapeshellarg( $soffice ),
            escapeshellarg( 'writer_pdf_import' ),
            escapeshellarg( $tmp_dir ),
            escapeshellarg( $pdf_path )
        );

        $output   = [];
        $exit_code = 0;
        exec( $cmd, $output, $exit_code );

        /* LibreOffice may name output slightly differently; find it */
        if ( ! file_exists( $docx_path ) ) {
            /* Fallback: find any .docx in tmp_dir */
            $found = glob( $tmp_dir . DIRECTORY_SEPARATOR . '*.docx' );
            if ( $found ) {
                $docx_path = $found[0];
                $docx_name = basename( $docx_path );
            } else {
                $log = implode( "\n", $output );
                self::cleanup( $tmp_dir );
                return new \WP_Error(
                    'convert_fail',
                    sprintf( __( 'LibreOffice conversion failed (exit %d). Log: %s', 'textcraft-tools' ), $exit_code, $log ),
                    [ 'status' => 500 ]
                );
            }
        }

        /* ---- Read DOCX and stream back as base64 ---- */
        $docx_content = file_get_contents( $docx_path );
        if ( $docx_content === false ) {
            self::cleanup( $tmp_dir );
            return new \WP_Error( 'read_fail', __( 'Could not read converted DOCX.', 'textcraft-tools' ), [ 'status' => 500 ] );
        }

        $response_data = [
            'filename' => $docx_name,
            'bytes'    => strlen( $docx_content ),
            'mime'     => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'content'  => base64_encode( $docx_content ),
        ];

        self::cleanup( $tmp_dir );

        return new \WP_REST_Response( $response_data, 200 );
    }

    /* -----------------------------------------------------------------------
     * Server utility helpers
     * --------------------------------------------------------------------- */

    /** Check if exec() is callable */
    private static function exec_enabled(): bool {
        if ( ! function_exists( 'exec' ) ) return false;
        $disabled = array_map( 'trim', explode( ',', (string) ini_get( 'disable_functions' ) ) );
        return ! in_array( 'exec', $disabled, true );
    }

    /**
     * Locate the LibreOffice binary.
     * Checks common install paths on Linux, macOS, and Windows.
     *
     * @return string|null  Full path to soffice, or null if not found.
     */
    private static function find_soffice(): ?string {
        /* Allow override via wp-config.php or filter */
        $override = apply_filters( 'textcraft_soffice_path', defined( 'TEXTCRAFT_SOFFICE_PATH' ) ? TEXTCRAFT_SOFFICE_PATH : null );
        if ( $override && is_executable( $override ) ) return $override;

        $candidates = [
            /* Linux (apt/snap/flatpak) */
            '/usr/bin/soffice',
            '/usr/local/bin/soffice',
            '/usr/lib/libreoffice/program/soffice',
            '/opt/libreoffice/program/soffice',
            /* macOS */
            '/Applications/LibreOffice.app/Contents/MacOS/soffice',
            /* Windows (WSL / Plesk / cPanel edge cases) */
            'C:\\Program Files\\LibreOffice\\program\\soffice.exe',
            'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe',
        ];

        foreach ( $candidates as $path ) {
            if ( is_executable( $path ) ) return $path;
        }

        /* Last resort: check PATH */
        $which = trim( (string) shell_exec( 'which soffice 2>/dev/null' ) );
        return ( $which && is_executable( $which ) ) ? $which : null;
    }

    /** Recursively delete a temp directory */
    private static function cleanup( string $dir ): void {
        if ( ! is_dir( $dir ) ) return;
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator( $dir, \RecursiveDirectoryIterator::SKIP_DOTS ),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ( $items as $item ) {
            $item->isDir() ? rmdir( $item->getRealPath() ) : unlink( $item->getRealPath() );
        }
        rmdir( $dir );
    }
}