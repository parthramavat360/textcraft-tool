<?php
/**
 * Widget: Image to Text (OCR)
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;

class Widget_Image_To_Text extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_image_to_text'; }
    public function get_title(): string { return esc_html__( 'Image to Text (OCR)', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-search-bold'; }
    protected function render_tool_content( array $settings ): void {

        // Drop zone
        echo '<div id="tc-ocr-drop" role="button" tabindex="0" aria-label="' . esc_attr__( 'Click or drag an image to upload for OCR', 'textcraft-tools' ) . '" '
           . 'class="tc-p-48-24 tc-ocr-drop-zone">';
        echo '<div class="tc-text-40 tc-mb-12">🔍</div>';
        echo '<p class="tc-text-15 tc-font-semibold tc-text-primary tc-m-0-6">' . esc_html__( 'Click to upload or drag & drop', 'textcraft-tools' ) . '</p>';
        echo '<p class="tc-text-13 tc-text-muted tc-m-0-4">' . esc_html__( 'Extract text from images using OCR (optical character recognition) powered by Tesseract.js. Supports PNG, JPG, GIF, WebP, and more — all processed locally in your browser with no server uploads.', 'textcraft-tools' ) . '</p>';
        echo '<p class="tc-text-12 tc-text-muted tc-m-0">' . esc_html__( 'Best results: clear photos, high-contrast scans, printed text', 'textcraft-tools' ) . '</p>';
        echo '<input type="file" id="tc-ocr-upload" accept="image/*" class="tc-d-none">';
        echo '</div>';

        // Image preview
        echo '<div id="tc-ocr-preview" class="tc-ocr-preview tc-hidden">';
        echo '<img id="tc-ocr-img" class="tc-ocr-img" alt="' . esc_attr__( 'Uploaded image for OCR', 'textcraft-tools' ) . '">';
        echo '<p id="tc-ocr-img-info" class="tc-text-12 tc-text-muted tc-mt-8"></p>';
        echo '</div>';

        // Language + Post-processing options
        echo '<div class="tc-grid-2col-16 tc-mb-20">';

        echo '<div>';
        echo '<label class="tc-label tc-d-block tc-mb-8">' . esc_html__( 'OCR Language', 'textcraft-tools' ) . '</label>';
        echo '<select id="tc-ocr-lang" class="tc-text-input">';
        $langs = [
            'eng'     => 'English',
            'fra'     => 'French',
            'deu'     => 'German',
            'spa'     => 'Spanish',
            'ita'     => 'Italian',
            'por'     => 'Portuguese',
            'chi_sim' => 'Chinese (Simplified)',
            'jpn'     => 'Japanese',
            'ara'     => 'Arabic',
            'hin'     => 'Hindi',
        ];
        foreach ( $langs as $val => $label ) {
            echo '<option value="' . esc_attr( $val ) . '">' . esc_html( $label ) . '</option>';
        }
        echo '</select>';
        echo '</div>';

        echo '<div>';
        echo '<label class="tc-label tc-d-block tc-mb-8">' . esc_html__( 'Post-Processing', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-flex-col tc-gap-8">';
        $this->render_options_row( [
            [ 'id' => 'tc-ocr-opt-trim',        'label' => esc_html__( 'Trim extra whitespace',       'textcraft-tools' ), 'checked' => true  ],
            [ 'id' => 'tc-ocr-opt-single-line',  'label' => esc_html__( 'Merge into single paragraph', 'textcraft-tools' ), 'checked' => false ],
        ] );
        echo '</div>';
        echo '</div>';

        echo '</div>'; // end grid

        // Progress bar
        echo '<div id="tc-ocr-progress" class="tc-mb-16 tc-hidden">';
        echo '<div class="tc-d-flex tc-flex-between tc-mb-6">';
echo '<span id="tc-ocr-status" class="tc-text-13 tc-text-muted">' . esc_html__( 'Initialising OCR engine…', 'textcraft-tools' ) . '</span>';
echo '<span id="tc-ocr-pct" class="tc-text-13 tc-accent-value">0%</span>';
        echo '</div>';
        echo '<div class="tc-ocr-progress-track">';
        echo '<div id="tc-ocr-bar" class="tc-ocr-progress-fill"></div>';
        echo '</div>';
        echo '</div>';

        // Action buttons
        $this->render_button_row( [
            [ 'id' => 'tc-ocr-extract',  'label' => '🔍 ' . esc_html__( 'Extract Text',   'textcraft-tools' ), 'variant' => 'primary', 'disabled' => true ],
            [ 'id' => 'tc-ocr-copy',     'label' => '📋 ' . esc_html__( 'Copy Text',      'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-ocr-download', 'label' => '💾 ' . esc_html__( 'Download .txt',  'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-ocr-clear',    'label' => '🗑️ ' . esc_html__( 'Clear',          'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        // Stats bar
        $this->render_stat_bar( [
            [ 'id' => 'tc-ocr-stat-words', 'label' => esc_html__( 'Words',      'textcraft-tools' ) ],
            [ 'id' => 'tc-ocr-stat-chars', 'label' => esc_html__( 'Characters', 'textcraft-tools' ) ],
            [ 'id' => 'tc-ocr-stat-conf',  'label' => esc_html__( 'Confidence', 'textcraft-tools' ) ],
        ] );

        // Output textarea
        echo '<div class="tc-label-row tc-mt-20"><span class="tc-label">' . esc_html__( 'Extracted Text', 'textcraft-tools' ) . '</span></div>';
        $this->render_textarea( 'tc-ocr-output', '', esc_html__( 'Your extracted text from the uploaded image will appear here after OCR completes.', 'textcraft-tools' ), 12, false );

        // Load Tesseract.js from CDN (defer so it doesn't block render)
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/tesseract.js/5.0.3/tesseract.min.js" defer crossorigin="anonymous"></script>';

        // Inline JS
        $this->render_inline_script( <<<'JS'
(function () {
    var drop    = document.getElementById('tc-ocr-drop');
    var fileInp = document.getElementById('tc-ocr-upload');
    var out     = document.getElementById('tc-ocr-output');
    var btnEx   = document.getElementById('tc-ocr-extract');
    var btnCopy = document.getElementById('tc-ocr-copy');
    var btnDl   = document.getElementById('tc-ocr-download');
    var imgEl   = document.getElementById('tc-ocr-img');
    var imgInfo = document.getElementById('tc-ocr-img-info');
    var preview = document.getElementById('tc-ocr-preview');
    var progress= document.getElementById('tc-ocr-progress');
    var bar     = document.getElementById('tc-ocr-bar');
    var pctEl   = document.getElementById('tc-ocr-pct');
    var statusEl= document.getElementById('tc-ocr-status');

    var uploadedFile = null;
    var worker       = null;

    // Initially hide copy/download
    if (btnCopy) btnCopy.style.display = 'none';
    if (btnDl)   btnDl.style.display   = 'none';

    // Drop zone interactions
    drop.addEventListener('click', function () { fileInp.click(); });
    drop.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') fileInp.click(); });
    drop.addEventListener('dragover',  function (e) { e.preventDefault(); drop.style.borderColor = 'var(--tc-accent)'; });
    drop.addEventListener('dragleave', function ()  { drop.style.borderColor = 'var(--tc-border)'; });
    drop.addEventListener('drop', function (e) {
        e.preventDefault();
        drop.style.borderColor = 'var(--tc-border)';
        if (e.dataTransfer.files[0]) loadFile(e.dataTransfer.files[0]);
    });
    fileInp.addEventListener('change', function () {
        if (fileInp.files[0]) loadFile(fileInp.files[0]);
    });

    function loadFile(file) {
        if (!file || !file.type.startsWith('image/')) return;
        uploadedFile = file;
        var reader = new FileReader();
        reader.onload = function (e) {
            imgEl.src = e.target.result;
            if (imgInfo) imgInfo.textContent = file.name + ' — ' + (file.size / 1024).toFixed(1) + ' KB';
            preview.style.display = 'block';
            btnEx.disabled = false;
        };
        reader.readAsDataURL(file);
    }

    // ── SPEED OPTIMISATIONS ──
    // 1. Reuse a persistent worker across extractions (avoid re-init cost).
    // 2. Use OEM 1 (LSTM only) — faster than combined mode (OEM 3).
    // 3. PSM 6 (uniform block of text) — fastest for most scanned docs/screenshots.
    // 4. Disable unnecessary Tesseract features via parameters.
    var WORKER_LANG = null;

    async function ensureWorker(lang) {
        if (worker && WORKER_LANG === lang) return; // reuse existing
        if (worker) { await worker.terminate(); worker = null; }

        worker = await Tesseract.createWorker(lang, 1 /* OEM_LSTM_ONLY */, {
            logger: function (m) {
                if (m.status === 'recognizing text') {
                    var p = Math.round((m.progress || 0) * 100);
                    bar.style.width    = p + '%';
                    pctEl.textContent  = p + '%';
                    statusEl.textContent = 'Recognising text…';
                } else {
                    statusEl.textContent = m.status || 'Loading…';
                }
            },
        });

        // PSM 6 = assume a single uniform block — significantly faster for most images
        await worker.setParameters({ tessedit_pageseg_mode: '6' });
        WORKER_LANG = lang;
    }

    btnEx.addEventListener('click', async function () {
        if (!uploadedFile) return;

        var lang = document.getElementById('tc-ocr-lang').value;

        // Wait for Tesseract to be available (it loads deferred)
        if (typeof Tesseract === 'undefined') {
            out.value = '⚠️ Tesseract.js is still loading — please wait a moment and try again.';
            return;
        }

        btnEx.disabled       = true;
        btnEx.textContent    = '⏳ Processing…';
        progress.style.display = 'block';
        bar.style.width      = '0%';
        pctEl.textContent    = '0%';
        out.value            = '';
        if (btnCopy) btnCopy.style.display = 'none';
        if (btnDl)   btnDl.style.display   = 'none';

        try {
            await ensureWorker(lang);
            var result = await worker.recognize(uploadedFile);
            var text   = result.data.text;

            // Post-processing
            if (document.getElementById('tc-ocr-opt-trim').checked)
                text = text.replace(/[ \t]+/g, ' ').replace(/\n{3,}/g, '\n\n').trim();
            if (document.getElementById('tc-ocr-opt-single-line').checked)
                text = text.replace(/\n+/g, ' ').trim();

            out.value = text;

            // Stats
            var wordCount = text.trim() ? text.trim().split(/\s+/).filter(Boolean).length : 0;
            var conf      = Math.round(result.data.confidence);
            var statWords = document.getElementById('tc-ocr-stat-words');
            var statChars = document.getElementById('tc-ocr-stat-chars');
            var statConf  = document.getElementById('tc-ocr-stat-conf');
            if (statWords) statWords.textContent = wordCount;
            if (statChars) statChars.textContent = text.length;
            if (statConf)  statConf.textContent  = conf + '%';

            if (btnCopy) btnCopy.style.display = 'inline-flex';
            if (btnDl)   btnDl.style.display   = 'inline-flex';

        } catch (err) {
            out.value = '⚠️ OCR failed: ' + err.message + '\n\nTip: Try a clearer, higher-contrast image.';
            // Reset worker so next attempt re-initialises cleanly
            if (worker) { try { await worker.terminate(); } catch(e){} worker = null; WORKER_LANG = null; }
        } finally {
            btnEx.disabled      = false;
            btnEx.textContent   = '🔍 Extract Text';
            progress.style.display = 'none';
            bar.style.width     = '0%';
        }
    });

    // Copy
    if (btnCopy) {
        btnCopy.addEventListener('click', function () {
            if (!out.value) return;
            navigator.clipboard.writeText(out.value).then(function () {
                btnCopy.textContent = '✅ Copied!';
                setTimeout(function () { btnCopy.textContent = '📋 Copy Text'; }, 2000);
            });
        });
    }

    // Download
    if (btnDl) {
        btnDl.addEventListener('click', function () {
            if (!out.value) return;
            var blob = new Blob([out.value], { type: 'text/plain' });
            var a = document.createElement('a');
            a.href     = URL.createObjectURL(blob);
            a.download = 'textlens-extracted.txt';
            a.click();
        });
    }

    // Clear
    document.getElementById('tc-ocr-clear').addEventListener('click', async function () {
        if (worker) { try { await worker.terminate(); } catch(e){} worker = null; WORKER_LANG = null; }
        fileInp.value          = '';
        uploadedFile           = null;
        preview.style.display  = 'none';
        progress.style.display = 'none';
        bar.style.width        = '0%';
        out.value              = '';
        btnEx.disabled         = true;
        if (btnCopy) btnCopy.style.display = 'none';
        if (btnDl)   btnDl.style.display   = 'none';
        var ids = ['tc-ocr-stat-words', 'tc-ocr-stat-chars', 'tc-ocr-stat-conf'];
        ids.forEach(function (id) {
            var el = document.getElementById(id);
            if (el) el.textContent = '—';
        });
    });
})();
JS
        );
    }
}