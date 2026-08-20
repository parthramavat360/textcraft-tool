<?php
/**
 * Widget: Rotate PDF
 *
 * @package TextCraft_Tools\Widgets
 */

declare( strict_types = 1 );

namespace TextCraft_Tools\Widgets;

defined( 'ABSPATH' ) || exit;

class Widget_Rotate_Pdf extends TextCraft_Base_Widget {

    public function get_name(): string {
        return 'textcraft_rotate_pdf';
    }

    public function get_title(): string {
        return esc_html__( 'PDF Rotator', 'textcraft-tools' );
    }

    public function get_keywords(): array {
        return [ 'rotate pdf', 'pdf rotator', 'rotate pdf pages online', 'turn pdf', 'free online pdf tool' ];
    }

    public function get_icon(): string {
        return 'eicon-rotate';
    }

    protected function render_tool_content( array $settings ): void {
        echo '<div class="tc-jpgtopdf" data-rotate-pdf>';

        echo '<div class="tc-jc-drop" role="button" tabindex="0" aria-label="' . esc_attr__( 'Click or drag PDF files to rotate their pages', 'textcraft-tools' ) . '">';
        echo '<div class="tc-jc-drop__icon">PDF</div>';
        echo '<p class="tc-jc-drop__title">' . esc_html__( 'Click to upload or drag & drop PDF files', 'textcraft-tools' ) . '</p>';
        echo '<p class="tc-jc-drop__hint">' . esc_html__( 'Rotate PDF pages by 90°, 180°, or 270° — all done securely in your browser', 'textcraft-tools' ) . '</p>';
        echo '<input type="file" class="tc-jc-upload" accept="application/pdf" multiple>';
        echo '</div>';

        echo '<div class="tc-jc-upload-line" hidden><span class="tc-jc-upload-text">' . esc_html__( 'Uploading files: 0%', 'textcraft-tools' ) . '</span></div>';

        
        // Rotation Angle Option
        echo '<div class="tc-jc-option">';
        echo '<label class="tc-label" for="tc-rotatepdf-angle">' . esc_html__( 'Rotation Angle', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-8 tc-flex-wrap">';
        echo '<button type="button" class="tc-angle-btn" data-angle="90">90°</button>';
        echo '<button type="button" class="tc-angle-btn" data-angle="180">180°</button>';
        echo '<button type="button" class="tc-angle-btn" data-angle="270">270°</button>';
        echo '<button type="button" class="tc-angle-btn" data-angle="-90">-90°</button>';
        echo '</div>';
        echo '</div>';

        // Advanced Options Toggle
        echo '<div class="tc-jc-option tc-mt-30">';
        echo '<label class="tc-label tc-d-flex tc-items-center tc-gap-12 tc-cursor-pointer tc-m-0">';
        echo '<input type="checkbox" class="tc-advanced-toggle tc-toggle-checkbox">';
        echo esc_html__( 'Advanced Options', 'textcraft-tools' );
        echo '</label>';
        echo '</div>';

        // Advanced Section (hidden by default)
        echo '<div class="tc-advanced-section tc-mt-30" hidden>';
        
        // Page Range Option
        echo '<div class="tc-jc-option">';
        echo '<label class="tc-label tc-mb-10" for="tc-rotatepdf-pages">' . esc_html__( 'Apply to Specific Pages', 'textcraft-tools' ) . '</label>';
        echo '<input type="text" id="tc-rotatepdf-pages" class="tc-pages-input tc-text-input" placeholder="' . esc_attr__( 'Leave empty for all pages, or specify: 1,3,5-8', 'textcraft-tools' ) . '" >';
        echo '<div class="tc-jc-note tc-mt-20">' . esc_html__( 'Examples: 1,3,5 or 1-10 or 1,3,5-8', 'textcraft-tools' ) . '</div>';
        echo '</div>';

        // Compression Option
        echo '<div class="tc-jc-option">';
        echo '<label class="tc-label tc-mt-20" for="tc-rotatepdf-compression">' . esc_html__( 'Compression Level', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-jc-range">';
        echo '<input type="range" id="tc-rotatepdf-compression" class="tc-compression-slider tc-w-full tc-mb-20" min="0" max="9" value="6">';
        echo '<span class="tc-jc-quality-value tc-min-w-40">6</span>';
        echo '</div>';
        echo '<div class="tc-jc-note">' . esc_html__( '0 = No compression, 9 = Maximum compression', 'textcraft-tools' ) . '</div>';
        echo '</div>';

        echo '</div>';

        // Progress Bar
        echo '<div class="tc-jc-progress" hidden>';
        echo '<div class="tc-jc-progress__row"><span class="tc-jc-progress-label">' . esc_html__( 'Processing...', 'textcraft-tools' ) . '</span><span class="tc-jc-progress-pct">0%</span></div>';
        echo '<div class="tc-jc-progress__track"><div class="tc-jc-progress__bar"></div></div>';
        echo '</div>';

        // Preview Results
        echo '<div class="tc-jc-results" data-rotatepdf-previews hidden>';
        echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'Loaded PDFs', 'textcraft-tools' ) . '</span></div>';
        echo '<div class="tc-jc-grid tc-rotatepdf-preview-grid"></div>';
        echo '</div>';

        // Buttons
        $this->render_button_row(
            [
                [ 'id' => 'tc-rotatepdf-rotate',         'label' => esc_html__( 'Rotate PDF', 'textcraft-tools' ),            'variant' => 'primary', 'disabled' => true ],
                [ 'id' => 'tc-rotatepdf-download-pdf',   'label' => esc_html__( 'Download PDF', 'textcraft-tools' ),          'variant' => 'ghost', 'disabled' => true ],
                [ 'id' => 'tc-rotatepdf-download-all',   'label' => esc_html__( 'Download All (ZIP)', 'textcraft-tools' ),   'variant' => 'ghost', 'disabled' => true ],
                [ 'id' => 'tc-rotatepdf-clear',          'label' => esc_html__( 'Clear All', 'textcraft-tools' ),             'variant' => 'danger' ],
            ]
        );

        // Stats Bar
        $this->render_stat_bar(
            [
                [ 'id' => 'tc-rotatepdf-stat-loaded', 'label' => esc_html__( 'Files Loaded', 'textcraft-tools' ) ],
                [ 'id' => 'tc-rotatepdf-stat-pages',  'label' => esc_html__( 'Total Pages', 'textcraft-tools' ) ],
                [ 'id' => 'tc-rotatepdf-stat-size',   'label' => esc_html__( 'Output Size', 'textcraft-tools' ) ],
            ]
        );


        $this->render_inline_script( <<<'JS'
(function () {
    var root = document.querySelector('[data-rotate-pdf]');
    if (!root) return;

    var PDFLIB_URL = 'https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js';
    var JSZIP_URL = 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js';
    var CACHE_KEY = 'tc_rotatepdf_session';

    var drop              = root.querySelector('.tc-jc-drop');
    var upload            = root.querySelector('.tc-jc-upload');
    var uploadLine        = root.querySelector('.tc-jc-upload-line');
    var uploadText        = root.querySelector('.tc-jc-upload-text');
    var angleButtons      = root.querySelectorAll('.tc-angle-btn');
    var advancedToggle    = root.querySelector('.tc-advanced-toggle');
    var advancedSection   = root.querySelector('.tc-advanced-section');
    var pagesInput        = root.querySelector('.tc-pages-input');
    var compressionSlider = root.querySelector('.tc-compression-slider');
    var compressionLabel  = root.querySelector('.tc-jc-quality-value');
    var progress          = root.querySelector('.tc-jc-progress');
    var progressBar       = root.querySelector('.tc-jc-progress__bar');
    var progressLbl       = root.querySelector('.tc-jc-progress-label');
    var progressPct       = root.querySelector('.tc-jc-progress-pct');
    var previewsWrap      = root.querySelector('[data-rotatepdf-previews]');
    var previewGrid       = root.querySelector('.tc-rotatepdf-preview-grid');
    var btnRotate         = document.getElementById('tc-rotatepdf-rotate');
    var btnDownload       = document.getElementById('tc-rotatepdf-download-pdf');
    var btnDownAll        = document.getElementById('tc-rotatepdf-download-all');
    var btnClear          = document.getElementById('tc-rotatepdf-clear');
    var statLoaded        = document.getElementById('tc-rotatepdf-stat-loaded');
    var statPages         = document.getElementById('tc-rotatepdf-stat-pages');
    var statSize          = document.getElementById('tc-rotatepdf-stat-size');

    var pdfs = [];
    var processedPdf = null;
    var processedFileName = 'rotated-pdf.pdf';
    var selectedAngle = 90;
    var scriptPromises = {};
    var selectedAngleBtn = null;

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
        uploadText.textContent = 'Uploading files: ' + pct + '%';
        if (done === total) setTimeout(function () { uploadLine.hidden = true; }, 800);
    }

    function setProgress(done, total, label) {
        var pct = total ? Math.round((done / total) * 100) : 0;
        progress.hidden = false;
        progressBar.style.width = pct + '%';
        progressPct.textContent = pct + '%';
        progressLbl.textContent = label || ('Processing page ' + done + ' of ' + total + '...');
        if (done === total) {
            setTimeout(function () {
                progress.hidden = true;
                progressBar.style.width = '0%';
            }, 700);
        }
    }

    function parsePageRange(rangeStr, totalPages) {
        if (!rangeStr || !rangeStr.trim()) {
            var allPages = [];
            for (var x = 0; x < totalPages; x++) allPages.push(x);
            return allPages;
        }
        
        var pages = [];
        var seen = {};
        var parts = rangeStr.split(',');
        for (var i = 0; i < parts.length; i++) {
            var part = parts[i].trim();
            if (!part) continue;
            if (part.indexOf('-') > -1) {
                var range = part.split('-');
                var start = parseInt(range[0], 10) - 1;
                var end = parseInt(range[1], 10) - 1;
                if (isNaN(start) || isNaN(end)) continue;
                if (start > end) { var tmp = start; start = end; end = tmp; }
                for (var j = start; j <= end && j < totalPages; j++) {
                    if (j >= 0 && !seen[j]) { pages.push(j); seen[j] = true; }
                }
            } else {
                var pageNum = parseInt(part, 10) - 1;
                if (!isNaN(pageNum) && pageNum < totalPages && pageNum >= 0 && !seen[pageNum]) {
                    pages.push(pageNum);
                    seen[pageNum] = true;
                }
            }
        }
        if (!pages.length) {
            var fallback = [];
            for (var k = 0; k < totalPages; k++) fallback.push(k);
            return fallback;
        }
        return pages;
    }

    function resetPdfResult() {
        processedPdf = null;
        btnDownload.disabled = true;
        statSize.textContent = '-';
    }

    function saveCache() {
        try {
            var payload = {
                angle: selectedAngle,
                compression: Number(compressionSlider.value),
                pagesFilter: pagesInput.value,
                pdfs: pdfs.map(function (item) {
                    return { name: item.name, dataUrl: item.dataUrl, pages: item.pages };
                }),
                processedPdf: null,
                pdfName: processedFileName,
            };
            if (processedPdf) {
                var fr = new FileReader();
                fr.onload = function () {
                    payload.processedPdf = fr.result;
                    try { sessionStorage.setItem(CACHE_KEY, JSON.stringify(payload)); } catch (e) {}
                };
                fr.readAsDataURL(processedPdf);
            } else {
                try { sessionStorage.setItem(CACHE_KEY, JSON.stringify(payload)); } catch (e) {}
            }
        } catch (e) {}
    }

    function clearCache() {
        try { sessionStorage.removeItem(CACHE_KEY); } catch (e) {}
    }

    function renderPreviews() {
        previewGrid.innerHTML = '';
        if (!pdfs.length) {
            previewsWrap.hidden = true;
            return;
        }
        pdfs.forEach(function (item, idx) {
            var card = document.createElement('div');
            card.className = 'tc-jc-card';

            var preview = document.createElement('div');
            preview.className = 'tc-jc-card__preview';
            var wrap = document.createElement('div');
            var label = document.createElement('span');
            label.textContent = 'File ' + (idx + 1);
            var img = document.createElement('img');
            img.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="100" height="120"%3E%3Crect fill="%23f0f0f0" width="100" height="120"/%3E%3Ctext x="50" y="60" font-size="14" text-anchor="middle" fill="%23666"%3EPDF%3C/text%3E%3C/svg%3E';
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
            var pages = document.createElement('div');
            pages.className = 'tc-pages-meta';
            pages.textContent = item.pages + ' pages';
            body.appendChild(name);
            body.appendChild(pages);

            card.appendChild(preview);
            card.appendChild(body);
            previewGrid.appendChild(card);
        });
        previewsWrap.hidden = false;
    }

    function updateUiState() {
        var hasFiles = pdfs.length > 0;
        var hasOriginalBuffer = hasFiles && !!pdfs[0].arrayBuffer;
        btnRotate.disabled = !hasOriginalBuffer;
        btnDownAll.disabled = !hasOriginalBuffer;
        btnDownload.disabled = !processedPdf;
        statLoaded.textContent = String(pdfs.length);
        
        var totalPages = 0;
        for (var i = 0; i < pdfs.length; i++) {
            totalPages += pdfs[i].pages || 0;
        }
        statPages.textContent = totalPages;
        compressionLabel.textContent = compressionSlider.value;
    }

    async function getPdfMetadata(arrayBuffer) {
        try {
            // Use a copy so the original buffer is preserved
            var copy = arrayBuffer.slice(0);
            var pdfDoc = await PDFLib.PDFDocument.load(copy);
            return { pages: pdfDoc.getPageCount() };
        } catch (e) {
            return { pages: 0 };
        }
    }

    async function addFiles(fileList) {
        var incoming = Array.from(fileList || []).filter(function (file) {
            return file.type === 'application/pdf' || /\.pdf$/i.test(file.name);
        });
        if (!incoming.length) return;

        await loadScript(PDFLIB_URL);

        var total = incoming.length;
        var done = 0;
        
        for (var i = 0; i < incoming.length; i++) {
            (function (file) {
                var reader = new FileReader();
                reader.onload = async function () {
                    var arrayBuffer = reader.result;
                    try {
                        var metadata = await getPdfMetadata(arrayBuffer);
                        pdfs.push({
                            name: file.name,
                            pages: metadata.pages,
                            arrayBuffer: arrayBuffer,
                            dataUrl: URL.createObjectURL(new Blob([arrayBuffer], { type: 'application/pdf' }))
                        });
                    } catch (e) {
                        console.error('Error loading PDF:', e);
                    }
                    
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
                reader.readAsArrayBuffer(file);
            })(incoming[i]);
        }
    }

    async function rotatePdf() {
        if (!pdfs.length) return;
        await loadScript(PDFLIB_URL);
        if (!window.PDFLib) return;

        if (!pdfs[0].arrayBuffer) {
            alert('Original PDF data is no longer available. Please re-upload the file.');
            return;
        }

        var compression = Number(compressionSlider.value);
        var pagesFilter = pagesInput.value;

        try {
            // Clone the buffer every time so pdf-lib does not detach the original
            var sourceBuffer = pdfs[0].arrayBuffer.slice(0);
            var pdfDoc = await PDFLib.PDFDocument.load(sourceBuffer);
            var pages = pdfDoc.getPages();
            var pagesToRotate = parsePageRange(pagesFilter, pages.length);

            if (!pagesToRotate.length) {
                alert('No valid pages selected. Please check your page range.');
                return;
            }

            setProgress(0, pagesToRotate.length, 'Preparing rotation...');

            for (var i = 0; i < pagesToRotate.length; i++) {
                var pageIdx = pagesToRotate[i];
                if (pageIdx >= 0 && pageIdx < pages.length) {
                    var page = pages[pageIdx];
                    var currentRotation = page.getRotation().angle || 0;
                    var newRotation = (currentRotation + selectedAngle) % 360;
                    if (newRotation < 0) newRotation += 360;
                    page.setRotation(PDFLib.degrees(newRotation));
                }
                setProgress(i + 1, pagesToRotate.length, 'Rotating page ' + (pageIdx + 1) + '...');
            }

            var pdfBytes = await pdfDoc.save({ useObjectStreams: compression > 5 });
            // Slice to make a standalone copy for the Blob
            processedPdf = new Blob([pdfBytes.slice(0)], { type: 'application/pdf' });
            processedFileName = (pdfs[0].name || 'rotated').replace(/\.pdf$/i, '') + '-rotated.pdf';
            statSize.textContent = formatSize(processedPdf.size);
            btnDownload.disabled = false;
            saveCache();
            setProgress(pagesToRotate.length, pagesToRotate.length, 'Done');
        } catch (e) {
            console.error('Error rotating PDF:', e);
            alert('Error rotating PDF: ' + e.message);
            progress.hidden = true;
            progressBar.style.width = '0%';
        }
    }

    function restoreFromCache() {
        try {
            var raw = sessionStorage.getItem(CACHE_KEY);
            if (!raw) return;
            var cached = JSON.parse(raw);
            
            if (cached.angle) {
                selectedAngle = cached.angle;
                // Set the visual state for cached angle
                angleButtons.forEach(function(btn) {
                    if (parseInt(btn.getAttribute('data-angle'), 10) === cached.angle) {
                        btn.style.background = 'var(--tc-accent)';
                        btn.style.borderColor = 'var(--tc-accent)';
                        selectedAngleBtn = btn;
                    }
                });
            }
            
            if (cached.compression) compressionSlider.value = String(cached.compression);
            if (cached.pagesFilter) pagesInput.value = cached.pagesFilter;
            if (cached.pdfName) processedFileName = cached.pdfName;
            
            if (cached.pdfs && cached.pdfs.length) {
                pdfs = cached.pdfs.map(function(item) {
                    return {
                        name: item.name,
                        pages: item.pages || 0,
                        arrayBuffer: null,
                        dataUrl: item.dataUrl
                    };
                });
            }
            
            if (cached.processedPdf) {
                fetch(cached.processedPdf)
                    .then(function(res) { return res.blob(); })
                    .then(function(blob) {
                        processedPdf = blob;
                        statSize.textContent = formatSize(blob.size);
                        btnDownload.disabled = false;
                    })
                    .catch(function() {});
            }
            
            renderPreviews();
            updateUiState();
        } catch (e) {}
    }

    // Angle Button Handlers
    angleButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            selectedAngle = parseInt(this.getAttribute('data-angle'), 10);
            
            // Reset all buttons to default state
            angleButtons.forEach(function(b) {
                b.style.background = '';
                b.style.borderColor = 'var(--tc-border)';
            });
            
            // Set the clicked button as selected
            this.style.background = 'var(--tc-accent)';
            this.style.borderColor = 'var(--tc-accent)';
            selectedAngleBtn = this;
            
            resetPdfResult();
            updateUiState();
            saveCache();
        });

        // Hover effects on angle buttons
        btn.addEventListener('mouseenter', function() {
            if (this.style.background !== 'var(--tc-accent)') {
                this.style.borderColor = 'var(--tc-accent)';
                this.style.backgroundColor = 'rgba(var(--tc-accent-rgb), 0.05)';
            }
        });
        btn.addEventListener('mouseleave', function() {
            if (this.style.background !== 'var(--tc-accent)') {
                this.style.borderColor = 'var(--tc-border)';
                this.style.backgroundColor = '';
            }
        });
    });

    // Advanced Toggle
    advancedToggle.addEventListener('change', function () {
        advancedSection.hidden = !this.checked;
    });

    // Compression Slider
    compressionSlider.addEventListener('input', function () {
        compressionLabel.textContent = compressionSlider.value;
        saveCache();
    });

    // Drop Zone Events
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

    // File Input Change
    upload.addEventListener('change', function () {
        addFiles(upload.files);
        upload.value = '';
    });

    // Pages Input - listen to both input and change so live edits are captured
    pagesInput.addEventListener('input', function () {
        resetPdfResult();
    });
    pagesInput.addEventListener('change', function () {
        resetPdfResult();
        saveCache();
    });

    // Focus effects on inputs
    pagesInput.addEventListener('focus', function() {
        this.style.borderColor = 'var(--tc-accent)';
    });
    pagesInput.addEventListener('blur', function() {
        this.style.borderColor = 'var(--tc-border)';
    });

    // Rotate Button
    btnRotate.addEventListener('click', function () {
        rotatePdf();
    });

    // Download Button
    btnDownload.addEventListener('click', function () {
        if (!processedPdf) return;
        var link = document.createElement('a');
        link.href = URL.createObjectURL(processedPdf);
        link.download = processedFileName;
        document.body.appendChild(link);
        link.click();
        link.remove();
        setTimeout(function () { URL.revokeObjectURL(link.href); }, 800);
    });

    // Download All Button
    btnDownAll.addEventListener('click', async function () {
        if (!pdfs.length) return;
        await loadScript(JSZIP_URL);
        if (!window.JSZip) return;
        
        var zip = new window.JSZip();
        
        for (var i = 0; i < pdfs.length; i++) {
            (function(item, idx) {
                var fileName = item.name || ('pdf-' + (idx + 1) + '.pdf');
                if (item.arrayBuffer) {
                    // Use a slice copy so the original buffer remains usable
                    zip.file(fileName, new Blob([item.arrayBuffer.slice(0)], { type: 'application/pdf' }));
                }
            })(pdfs[i], i);
        }
        
        var content = await zip.generateAsync({ type: 'blob' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(content);
        link.download = 'pdf-files.zip';
        document.body.appendChild(link);
        link.click();
        link.remove();
        setTimeout(function () { URL.revokeObjectURL(link.href); }, 800);
    });

    // Clear Button
    btnClear.addEventListener('click', function () {
        pdfs = [];
        processedPdf = null;
        pagesInput.value = '';
        
        // Reset all angle buttons to default
        angleButtons.forEach(function(b) {
            b.style.background = '';
            b.style.borderColor = 'var(--tc-border)';
        });
        selectedAngleBtn = null;
        selectedAngle = 90;
        
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
