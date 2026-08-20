<?php
/**
 * Widget: PNG to PDF
 *
 * @package TextCraft_Tools\Widgets
 */

declare( strict_types = 1 );

namespace TextCraft_Tools\Widgets;

defined( 'ABSPATH' ) || exit;

class Widget_Png_To_Pdf extends TextCraft_Base_Widget {

    public function get_name(): string {
        return 'textcraft_png_to_pdf';
    }

    public function get_title(): string {
        return esc_html__( 'PNG to PDF Converter', 'textcraft-tools' );
    }

    public function get_keywords(): array {
        return [ 'png to pdf', 'convert png to pdf', 'image to pdf converter', 'picture to pdf', 'free online pdf converter' ];
    }

    public function get_icon(): string {
        return 'eicon-file-download';
    }

    protected function render_tool_content( array $settings ): void {
        echo '<div class="tc-pngtopdf" data-png-to-pdf>';

        echo '<div class="tc-jc-drop" role="button" tabindex="0" aria-label="' . esc_attr__( 'Click or drag PNG images to convert them to PDF', 'textcraft-tools' ) . '">';
        echo '<div class="tc-jc-drop__icon">PNG</div>';
        echo '<p class="tc-jc-drop__title">' . esc_html__( 'Click to upload or drag & drop PNG files', 'textcraft-tools' ) . '</p>';
        echo '<p class="tc-jc-drop__hint">' . esc_html__( 'Combine multiple PNG images into a single PDF document — entirely processed in your browser for privacy', 'textcraft-tools' ) . '</p>';
        echo '<input type="file" class="tc-jc-upload" accept="image/png" multiple>';
        echo '</div>';

        echo '<div class="tc-jc-upload-line" hidden><span class="tc-jc-upload-text">' . esc_html__( 'Uploading images: 0%', 'textcraft-tools' ) . '</span></div>';

        echo '<div class="tc-jc-options">';
        echo '<div class="tc-jc-option">';
        echo '<label class="tc-label" for="tc-pngtopdf-quality">' . esc_html__( 'Image Quality', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-jc-range">';
        echo '<input type="range" id="tc-pngtopdf-quality" class="tc-jc-quality" min="50" max="100" value="90">';
        echo '<span class="tc-jc-quality-value">90%</span>';
        echo '</div></div>';
        echo '<div class="tc-jc-note">' . esc_html__( 'Higher quality preserves sharper image detail but creates a larger PDF. Use the slider to find your ideal balance.', 'textcraft-tools' ) . '</div>';
        echo '</div>';

        echo '<div class="tc-jc-progress" hidden>';
        echo '<div class="tc-jc-progress__row"><span class="tc-jc-progress-label">' . esc_html__( 'Preparing...', 'textcraft-tools' ) . '</span><span class="tc-jc-progress-pct">0%</span></div>';
        echo '<div class="tc-jc-progress__track"><div class="tc-jc-progress__bar"></div></div>';
        echo '</div>';

        echo '<div class="tc-jc-results" data-pngtopdf-previews hidden>';
        echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'Loaded Images', 'textcraft-tools' ) . '</span></div>';
        echo '<div class="tc-jc-grid tc-pngtopdf-preview-grid"></div>';
        echo '</div>';

        $this->render_button_row(
            [
                [ 'id' => 'tc-pngtopdf-convert',      'label' => esc_html__( 'Convert to PDF', 'textcraft-tools' ),      'variant' => 'primary', 'disabled' => true ],
                [ 'id' => 'tc-pngtopdf-download-pdf', 'label' => esc_html__( 'Download PDF', 'textcraft-tools' ),         'variant' => 'ghost', 'disabled' => true ],
                [ 'id' => 'tc-pngtopdf-download-all', 'label' => esc_html__( 'Download All Images (ZIP)', 'textcraft-tools' ), 'variant' => 'ghost', 'disabled' => true ],
                [ 'id' => 'tc-pngtopdf-clear',        'label' => esc_html__( 'Clear All', 'textcraft-tools' ),            'variant' => 'danger' ],
            ]
        );

        $this->render_stat_bar(
            [
                [ 'id' => 'tc-pngtopdf-stat-loaded', 'label' => esc_html__( 'Images Loaded', 'textcraft-tools' ) ],
                [ 'id' => 'tc-pngtopdf-stat-pages',  'label' => esc_html__( 'PDF Pages', 'textcraft-tools' ) ],
                [ 'id' => 'tc-pngtopdf-stat-size',   'label' => esc_html__( 'Output PDF Size', 'textcraft-tools' ) ],
            ]
        );

        echo '</div>';

        $this->render_inline_script( <<<'JS'
(function () {
    var root = document.querySelector('[data-png-to-pdf]');
    if (!root) return;

    var JSPDF_URL = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
    var JSZIP_URL = 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js';
    var CACHE_KEY = 'tc_pngtopdf_session';

    var drop          = root.querySelector('.tc-jc-drop');
    var upload        = root.querySelector('.tc-jc-upload');
    var uploadLine    = root.querySelector('.tc-jc-upload-line');
    var uploadText    = root.querySelector('.tc-jc-upload-text');
    var qualitySlider = root.querySelector('.tc-jc-quality');
    var qualityLabel  = root.querySelector('.tc-jc-quality-value');
    var progress      = root.querySelector('.tc-jc-progress');
    var progressBar   = root.querySelector('.tc-jc-progress__bar');
    var progressLbl   = root.querySelector('.tc-jc-progress-label');
    var progressPct   = root.querySelector('.tc-jc-progress-pct');
    var previewsWrap  = root.querySelector('[data-pngtopdf-previews]');
    var previewGrid   = root.querySelector('.tc-pngtopdf-preview-grid');
    var btnConvert    = document.getElementById('tc-pngtopdf-convert');
    var btnDownload   = document.getElementById('tc-pngtopdf-download-pdf');
    var btnDownAll    = document.getElementById('tc-pngtopdf-download-all');
    var btnClear      = document.getElementById('tc-pngtopdf-clear');
    var statLoaded    = document.getElementById('tc-pngtopdf-stat-loaded');
    var statPages     = document.getElementById('tc-pngtopdf-stat-pages');
    var statSize      = document.getElementById('tc-pngtopdf-stat-size');

    var images = [];
    var pdfBlob = null;
    var pdfFileName = 'converted-images.pdf';
    var scriptPromises = {};

    statLoaded.textContent = '0';
    statPages.textContent = '0';
    statSize.textContent = '-';

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
        uploadText.textContent = 'Uploading images: ' + pct + '%';
        if (done === total) setTimeout(function () { uploadLine.hidden = true; }, 800);
    }

    function setProgress(done, total, label) {
        var pct = total ? Math.round((done / total) * 100) : 0;
        progress.hidden = false;
        progressBar.style.width = pct + '%';
        progressPct.textContent = pct + '%';
        progressLbl.textContent = label || ('Preparing page ' + done + ' of ' + total + '...');
        if (done === total) {
            setTimeout(function () {
                progress.hidden = true;
                progressBar.style.width = '0%';
            }, 700);
        }
    }

    function resetPdfResult() {
        pdfBlob = null;
        btnDownload.disabled = true;
        statPages.textContent = images.length ? String(images.length) : '0';
        statSize.textContent = '-';
    }

    function saveCache() {
        try {
            var payload = {
                quality: Number(qualitySlider.value),
                images: images.map(function (item) {
                    return { name: item.name, type: item.type, dataUrl: item.dataUrl };
                }),
                pdf: null,
                pdfName: pdfFileName,
            };
            if (pdfBlob) {
                var fr = new FileReader();
                fr.onload = function () {
                    payload.pdf = fr.result;
                    sessionStorage.setItem(CACHE_KEY, JSON.stringify(payload));
                };
                fr.readAsDataURL(pdfBlob);
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
        if (!images.length) {
            previewsWrap.hidden = true;
            return;
        }
        images.forEach(function (item, idx) {
            var card = document.createElement('div');
            card.className = 'tc-jc-card';

            var preview = document.createElement('div');
            preview.className = 'tc-jc-card__preview';
            var wrap = document.createElement('div');
            var label = document.createElement('span');
            label.textContent = 'Image ' + (idx + 1);
            var img = document.createElement('img');
            img.src = item.dataUrl;
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

            card.appendChild(preview);
            card.appendChild(body);
            previewGrid.appendChild(card);
        });
        previewsWrap.hidden = false;
    }

    function updateUiState() {
        var hasImages = images.length > 0;
        btnConvert.disabled = !hasImages;
        btnDownAll.disabled = !hasImages;
        btnDownload.disabled = !pdfBlob;
        statLoaded.textContent = String(images.length);
        statPages.textContent = hasImages ? String(images.length) : '0';
        qualityLabel.textContent = qualitySlider.value + '%';
    }

    function addFiles(fileList) {
        var incoming = Array.from(fileList || []).filter(function (file) {
            return /image\/png/i.test(file.type) || /\.png$/i.test(file.name);
        });
        if (!incoming.length) return;

        var total = incoming.length;
        var done = 0;
        incoming.forEach(function (file) {
            var reader = new FileReader();
            reader.onload = function () {
                images.push({ name: file.name, type: file.type || 'image/png', dataUrl: reader.result });
                done += 1;
                setUploadLine(done, total);
                if (done === total) {
                    renderPreviews();
                    resetPdfResult();
                    updateUiState();
                    saveCache();
                }
            };
            reader.onerror = function () {
                done += 1;
                setUploadLine(done, total);
            };
            reader.readAsDataURL(file);
        });
    }

    function dataUrlToBlob(dataUrl, fallbackMime) {
        var parts = dataUrl.split(',');
        var header = parts[0] || '';
        var mimeMatch = header.match(/:(.*?);/);
        var mime = mimeMatch ? mimeMatch[1] : (fallbackMime || 'image/png');
        var binary = atob(parts[1]);
        var len = binary.length;
        var arr = new Uint8Array(len);
        for (var i = 0; i < len; i++) arr[i] = binary.charCodeAt(i);
        return new Blob([arr], { type: mime });
    }

    function imageFromDataUrl(dataUrl) {
        return new Promise(function (resolve, reject) {
            var img = new Image();
            img.onload = function () { resolve(img); };
            img.onerror = reject;
            img.src = dataUrl;
        });
    }

    async function convertToPdf() {
        if (!images.length) return;
        await loadScript(JSPDF_URL);
        var jsPDF = window.jspdf && window.jspdf.jsPDF;
        if (!jsPDF) return;

        var quality = Number(qualitySlider.value) / 100;
        var pdf = new jsPDF({ orientation: 'p', unit: 'mm', format: 'a4' });
        var pageW = 210;
        var pageH = 297;

        for (var i = 0; i < images.length; i++) {
            if (i > 0) pdf.addPage();
            setProgress(i + 1, images.length, 'Building page ' + (i + 1) + ' of ' + images.length + '...');

            var img = await imageFromDataUrl(images[i].dataUrl);
            var ratio = Math.min(pageW / img.width, pageH / img.height);
            var drawW = img.width * ratio;
            var drawH = img.height * ratio;
            var x = (pageW - drawW) / 2;
            var y = (pageH - drawH) / 2;
            pdf.addImage(images[i].dataUrl, 'PNG', x, y, drawW, drawH, undefined, 'FAST', 0);
        }

        var pdfArrayBuffer = pdf.output('arraybuffer');
        pdfBlob = new Blob([pdfArrayBuffer], { type: 'application/pdf' });
        statSize.textContent = formatSize(pdfBlob.size);
        btnDownload.disabled = false;
        saveCache();
        setProgress(images.length, images.length, 'Done');
    }

    function restoreFromCache() {
        try {
            var raw = sessionStorage.getItem(CACHE_KEY);
            if (!raw) return;
            var cached = JSON.parse(raw);
            images = Array.isArray(cached.images) ? cached.images : [];
            if (cached.quality) qualitySlider.value = String(cached.quality);
            if (cached.pdfName) pdfFileName = cached.pdfName;
            if (cached.pdf) {
                pdfBlob = dataUrlToBlob(cached.pdf, 'application/pdf');
                statSize.textContent = formatSize(pdfBlob.size);
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

    qualitySlider.addEventListener('input', function () {
        qualityLabel.textContent = qualitySlider.value + '%';
        resetPdfResult();
        updateUiState();
        saveCache();
    });

    btnConvert.addEventListener('click', function () {
        convertToPdf();
    });

    btnDownload.addEventListener('click', function () {
        if (!pdfBlob) return;
        var link = document.createElement('a');
        link.href = URL.createObjectURL(pdfBlob);
        link.download = pdfFileName;
        document.body.appendChild(link);
        link.click();
        link.remove();
        setTimeout(function () { URL.revokeObjectURL(link.href); }, 800);
    });

    btnDownAll.addEventListener('click', async function () {
        if (!images.length) return;
        await loadScript(JSZIP_URL);
        if (!window.JSZip) return;
        var zip = new window.JSZip();
        images.forEach(function (item, idx) {
            var blob = dataUrlToBlob(item.dataUrl, item.type);
            var ext = /\.png$/i.test(item.name) ? '' : '.png';
            zip.file(item.name || ('image-' + (idx + 1) + ext), blob);
        });
        var content = await zip.generateAsync({ type: 'blob' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(content);
        link.download = 'png-images.zip';
        document.body.appendChild(link);
        link.click();
        link.remove();
        setTimeout(function () { URL.revokeObjectURL(link.href); }, 800);
    });

    btnClear.addEventListener('click', function () {
        images = [];
        pdfBlob = null;
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
