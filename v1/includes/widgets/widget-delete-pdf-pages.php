<?php
/**
 * Widget: Delete PDF Pages (FIXED - Final)
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );

namespace TextCraft_Tools\Widgets;

defined( 'ABSPATH' ) || exit;

class Widget_Delete_Pdf_Pages extends TextCraft_Base_Widget {
	public function get_name(): string  { return 'textcraft_delete_pdf_pages'; }
	public function get_title(): string { return esc_html__( 'PDF Page Remover', 'textcraft-tools' ); }

	public function get_keywords(): array {
		return [ 'delete pdf pages', 'remove pdf pages', 'pdf page remover', 'extract pdf pages online', 'free online pdf tool' ];
	}
	public function get_icon(): string  { return 'eicon-document-file'; }

	protected function render_tool_content( array $settings ): void {

		// Drop zone.
		echo '<div id="tc-dpdf-drop" role="button" tabindex="0" aria-label="' . esc_attr__( 'Click or drag PDF files', 'textcraft-tools' ) . '" '
			. 'class="tc-drop-zone">';
		echo '<div class="tc-drop-icon">📄</div>';
		echo '<p class="tc-drop-title">' . esc_html__( 'Click to upload or drag & drop PDF files', 'textcraft-tools' ) . '</p>';
		echo '<p class="tc-drop-desc">' . esc_html__( 'PDF files only — up to 10 files at once', 'textcraft-tools' ) . '</p>';
		echo '<p class="tc-drop-desc-sm">' . esc_html__( 'Select specific pages to remove — all processing is done locally in your browser for privacy', 'textcraft-tools' ) . '</p>';
		echo '<input type="file" id="tc-dpdf-upload" accept="application/pdf" multiple class="tc-d-none">';
		echo '</div>';

		// Upload progress.
		echo '<div id="tc-dpdf-upload-line" class="tc-upload-line">';
		echo '<span id="tc-dpdf-upload-text">' . esc_html__( 'Uploading PDFs: 0%', 'textcraft-tools' ) . '</span>';
		echo '</div>';

		// Processing progress.
		echo '<div id="tc-dpdf-proc-progress" class="tc-progress-wrap">';
		echo '<div class="tc-progress-row">';
		echo '<span id="tc-dpdf-proc-label" class="tc-progress-label">' . esc_html__( 'Processing…', 'textcraft-tools' ) . '</span>';
		echo '<span id="tc-dpdf-proc-pct" class="tc-progress-pct">0%</span>';
		echo '</div>';
		echo '<div class="tc-progress-bg">';
		echo '<div id="tc-dpdf-proc-bar" class="tc-progress-bar"></div>';
		echo '</div>';
		echo '</div>';

		// Loaded previews.
		echo '<div id="tc-dpdf-previews" class="tc-preview-wrap">';
		echo '<div class="tc-label-row tc-mb-8"><span class="tc-label">' . esc_html__( 'Loaded PDF Files', 'textcraft-tools' ) . '</span></div>';
		echo '<div id="tc-dpdf-preview-grid" class="tc-dpdf-grid-sm"></div>';
		echo '</div>';

		// Options section.
		echo '<div class="tc-settings-grid">';

		echo '<div>';
		echo '<label class="tc-label tc-d-block tc-mb-8" for="tc-dpdf-mode">' . esc_html__( 'Delete Mode', 'textcraft-tools' ) . '</label>';
		echo '<select id="tc-dpdf-mode" class="tc-select">';
		echo '<option value="specific">' . esc_html__( 'Delete Specific Pages', 'textcraft-tools' ) . '</option>';
		echo '<option value="range">' . esc_html__( 'Delete Page Range', 'textcraft-tools' ) . '</option>';
		echo '<option value="keep">' . esc_html__( 'Keep Only Selected Pages', 'textcraft-tools' ) . '</option>';
		echo '</select>';
		echo '</div>';

		echo '<div>';
		echo '<label class="tc-label tc-d-block tc-mb-8" for="tc-dpdf-compress">' . esc_html__( 'Compression', 'textcraft-tools' ) . '</label>';
		echo '<select id="tc-dpdf-compress" class="tc-select">';
		echo '<option value="none">' . esc_html__( 'None', 'textcraft-tools' ) . '</option>';
		echo '<option value="medium" selected>' . esc_html__( 'Medium', 'textcraft-tools' ) . '</option>';
		echo '</select>';
		echo '</div>';

		echo '<div class="tc-flex-col-end">';
		$this->render_options_row(
			[
				[
					'id'      => 'tc-dpdf-opt-prefix',
					'label'   => esc_html__( 'Add "modified_" prefix', 'textcraft-tools' ),
					'checked' => false,
				],
				[
					'id'      => 'tc-dpdf-opt-preserve-meta',
					'label'   => esc_html__( 'Preserve metadata', 'textcraft-tools' ),
					'checked' => true,
				],
			]
		);
		echo '</div>';

		echo '<div class="tc-flex-end">';
		echo '<div class="tc-info-card">';
		echo esc_html__( 'All processing stays in your browser — nothing is uploaded to any server. Files cached until you close this tab.', 'textcraft-tools' );
		echo '</div>';
		echo '</div>';

		echo '</div>';

		// Actions.
		$this->render_button_row(
			[
				[
					'id'       => 'tc-dpdf-process',
					'label'    => '⚙️ ' . esc_html__( 'Delete Pages', 'textcraft-tools' ),
					'variant'  => 'primary',
					'disabled' => true,
				],
				[
					'id'      => 'tc-dpdf-download-all',
					'label'   => '📦 ' . esc_html__( 'Download All', 'textcraft-tools' ),
					'variant' => 'ghost',
				],
				[
					'id'      => 'tc-dpdf-clear',
					'label'   => '🗑️ ' . esc_html__( 'Clear', 'textcraft-tools' ),
					'variant' => 'danger',
				],
			]
		);

		// Stats.
		$this->render_stat_bar(
			[
				[ 'id' => 'tc-dpdf-stat-loaded',    'label' => esc_html__( 'Files Loaded', 'textcraft-tools' ) ],
				[ 'id' => 'tc-dpdf-stat-processed', 'label' => esc_html__( 'Processed', 'textcraft-tools' ) ],
				[ 'id' => 'tc-dpdf-stat-pages',     'label' => esc_html__( 'Pages Deleted', 'textcraft-tools' ) ],
				[ 'id' => 'tc-dpdf-stat-size',      'label' => esc_html__( 'Size Reduction', 'textcraft-tools' ) ],
			]
		);

		// Results.
		echo '<div id="tc-dpdf-cards" class="tc-results-wrap">';
		echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'Processed PDF Files', 'textcraft-tools' ) . '</span></div>';
		echo '<div id="tc-dpdf-grid" class="tc-grid-cards-lg"></div>';
		echo '</div>';

		// Alert Toast
		echo '<div id="tc-dpdf-alert" class="tc-toast tc-text-14 tc-hidden" role="alert"></div>';

		// Modal
		echo '<div id="tc-dpdf-modal-backdrop" class="tc-modal-backdrop tc-hidden"></div>';
		echo '<div id="tc-dpdf-modal" class="tc-modal-box tc-hidden">';
		echo '<h3 class="tc-modal-title">' . esc_html__( 'Select Pages to Delete', 'textcraft-tools' ) . '</h3>';
		echo '<div id="tc-dpdf-modal-content" class="tc-mb-20"></div>';
		echo '<div class="tc-modal-footer">';
		echo '<button id="tc-dpdf-modal-cancel" class="tc-btn tc-btn--ghost tc-text-14">' . esc_html__( 'Cancel', 'textcraft-tools' ) . '</button>';
		echo '<button id="tc-dpdf-modal-apply" class="tc-btn tc-btn--primary tc-text-14">' . esc_html__( 'Delete Selected', 'textcraft-tools' ) . '</button>';
		echo '</div>';
		echo '</div>';

		// Libraries with worker setup
		echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js" defer crossorigin="anonymous"></script>';
		echo '<script>';
		echo 'if(window.pdfjsLib){window.pdfjsLib.GlobalWorkerOptions.workerSrc="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";}';
		echo '</script>';

		echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js" defer crossorigin="anonymous"></script>';
		echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" defer crossorigin="anonymous"></script>';

		$this->render_inline_script( <<<'JS'
(function () {
    var drop        = document.getElementById('tc-dpdf-drop');
    var fileInp     = document.getElementById('tc-dpdf-upload');
    var previewWrap = document.getElementById('tc-dpdf-previews');
    var previewGrid = document.getElementById('tc-dpdf-preview-grid');
    var uploadLine  = document.getElementById('tc-dpdf-upload-line');
    var uploadText  = document.getElementById('tc-dpdf-upload-text');
    var procWrap    = document.getElementById('tc-dpdf-proc-progress');
    var procBar     = document.getElementById('tc-dpdf-proc-bar');
    var procPct     = document.getElementById('tc-dpdf-proc-pct');
    var procLabel   = document.getElementById('tc-dpdf-proc-label');
    var grid        = document.getElementById('tc-dpdf-grid');
    var cardsWrap   = document.getElementById('tc-dpdf-cards');
    var btnProc     = document.getElementById('tc-dpdf-process');
    var btnDlAll    = document.getElementById('tc-dpdf-download-all');
    var btnClear    = document.getElementById('tc-dpdf-clear');
    var modeInp     = document.getElementById('tc-dpdf-mode');
    var modalBd     = document.getElementById('tc-dpdf-modal-backdrop');
    var modal       = document.getElementById('tc-dpdf-modal');
    var modalContent = document.getElementById('tc-dpdf-modal-content');
    var modalCancel = document.getElementById('tc-dpdf-modal-cancel');
    var modalApply  = document.getElementById('tc-dpdf-modal-apply');
    var alertEl     = document.getElementById('tc-dpdf-alert');

    var files   = [];
    var results = [];
    var currentEditingFile = null;

    var DB_NAME  = 'tc_dpdf_cache';
    var DB_STORE = 'sessions';
    var DB_KEY   = 'pdf_delete_pages';
    var db       = null;

    if (btnDlAll) btnDlAll.style.display = 'none';

    function showAlert(msg, type) {
        type = type || 'info';
        alertEl.textContent = msg;
        var colors = { 'error': '#b45309', 'success': '#22c55e', 'info': '#d4a24c' };
        alertEl.style.background = colors[type] || colors['info'];
        alertEl.style.display = 'block';
        if (alertEl.timeoutId) clearTimeout(alertEl.timeoutId);
        alertEl.timeoutId = setTimeout(function () { alertEl.style.display = 'none'; }, 4000);
    }

    // IndexedDB
    function openDB(cb) {
        if (db) { cb(db); return; }
        var req = indexedDB.open(DB_NAME, 1);
        req.onupgradeneeded = function (e) {
            var database = e.target.result;
            if (!database.objectStoreNames.contains(DB_STORE)) {
                database.createObjectStore(DB_STORE, { keyPath: 'id' });
            }
        };
        req.onsuccess = function (e) { db = e.target.result; cb(db); };
        req.onerror = function () { cb(null); };
    }

    function cacheSet(data) {
        openDB(function (database) {
            if (!database) return;
            try {
                database.transaction(DB_STORE, 'readwrite').objectStore(DB_STORE).put({
                    id: DB_KEY,
                    data: data,
                    timestamp: Date.now()
                });
            } catch (e) {}
        });
    }

    function cacheGet(cb) {
        openDB(function (database) {
            if (!database) { cb(null); return; }
            try {
                var req = database.transaction(DB_STORE).objectStore(DB_STORE).get(DB_KEY);
                req.onsuccess = function () { cb(req.result ? req.result.data : null); };
                req.onerror = function () { cb(null); };
            } catch (e) { cb(null); }
        });
    }

    function cacheClear() {
        openDB(function (database) {
            if (!database) return;
            try {
                database.transaction(DB_STORE, 'readwrite').objectStore(DB_STORE).delete(DB_KEY);
            } catch (e) {}
        });
    }

    function setUploadLine(text, show) {
        uploadText.textContent = text;
        uploadLine.style.display = show ? 'block' : 'none';
    }

    function formatSize(bytes) {
        if (!bytes) return '—';
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    function resetStats() {
        document.getElementById('tc-dpdf-stat-loaded').textContent = '0';
        document.getElementById('tc-dpdf-stat-processed').textContent = '0';
        document.getElementById('tc-dpdf-stat-pages').textContent = '—';
        document.getElementById('tc-dpdf-stat-size').textContent = '—';
    }

    function addPreviewThumb(item) {
        var thumb = document.createElement('div');
        thumb.className = 'tc-thumb-card';
        thumb.innerHTML = '<div class="tc-conv-thumb-lg tc-flex-center tc-mb-6 tc-dp-icon-grad">📄</div><p class="tc-text-10 tc-text-muted tc-text-ellipsis tc-m-0" title="' + item.name + '">' + item.name + '</p><p class="tc-text-muted tc-text-9 tc-mt-4">' + item.pages + ' pages</p>';
        previewGrid.appendChild(thumb);
    }

    function refreshPreviews(items) {
        previewGrid.innerHTML = '';
        if (!items.length) { previewWrap.style.display = 'none'; return; }
        items.forEach(function (item) { addPreviewThumb(item); });
        previewWrap.style.display = 'block';
    }

    function restoreFromCache() {
        cacheGet(function (cached) {
            if (!cached || !cached.results || !cached.results.length) return;
            results = cached.results;
            files = [];
            if (cached.mode) modeInp.value = cached.mode;
            refreshPreviews(results.map(function (item) { return { name: item.origName, pages: item.finalPageCount }; }));
            renderCards(results);
            document.getElementById('tc-dpdf-stat-loaded').textContent = results.length;
            document.getElementById('tc-dpdf-stat-processed').textContent = results.length;
            var totalDeleted = results.reduce(function (sum, item) { return sum + item.deletedCount; }, 0);
            document.getElementById('tc-dpdf-stat-pages').textContent = totalDeleted;
            var totalSaved = results.reduce(function (sum, item) { return sum + Math.max(0, (item.origSize || 0) - (item.finalSize || 0)); }, 0);
            document.getElementById('tc-dpdf-stat-size').textContent = formatSize(totalSaved);
            btnProc.disabled = false;
            if (btnDlAll) btnDlAll.style.display = 'inline-flex';
        });
    }

    drop.addEventListener('click', function () { fileInp.click(); });
    drop.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); fileInp.click(); } });
    drop.addEventListener('dragover', function (e) { e.preventDefault(); drop.style.borderColor = 'var(--tc-accent)'; });
    drop.addEventListener('dragleave', function () { drop.style.borderColor = 'var(--tc-border)'; });
    drop.addEventListener('drop', function (e) { e.preventDefault(); drop.style.borderColor = 'var(--tc-border)'; addFiles(e.dataTransfer.files); });
    fileInp.addEventListener('change', function () { addFiles(fileInp.files); });

    function addFiles(fileList) {
	    var incoming = Array.from(fileList || []).filter(function (file) { return file.type === 'application/pdf'; }).slice(0, Math.max(0, 10 - files.length));
	    if (!incoming.length) { showAlert('Please select valid PDF files', 'error'); return; }

	    var loaded = 0;
	    setUploadLine('Uploading PDFs: 0%', true);

	    incoming.forEach(function (file) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	            loaded += 1;
	            try {
	                var arrayBuffer = e.target.result;
	                // Convert to Uint8Array for pdf-lib
	                var uint8Array = new Uint8Array(arrayBuffer);
	                
	                // Use pdf-lib to get page count, NOT PDF.js
	                window.PDFLib.PDFDocument.load(uint8Array).then(function (pdfDoc) {
	                    var pageCount = pdfDoc.getPageCount();
	                    files.push({
	                        name: file.name,
	                        arrayBuffer: uint8Array,
	                        size: file.size,
	                        pages: pageCount
	                    });
	                    addPreviewThumb({ name: file.name, pages: pageCount });
	                    previewWrap.style.display = 'block';
	                    document.getElementById('tc-dpdf-stat-loaded').textContent = files.length;
	                    var pct = Math.round((loaded / incoming.length) * 100);
	                    setUploadLine('Uploading PDFs: ' + pct + '%', true);
	                    if (loaded === incoming.length) {
	                        btnProc.disabled = false;
	                        setUploadLine('', false);
	                        showAlert('All PDFs loaded successfully', 'success');
	                    }
	                }).catch(function (err) {
	                    showAlert('Error reading PDF: ' + file.name, 'error');
	                    console.error('PDF load error:', err);
	                });
	            } catch (err) {
	                showAlert('Error processing file: ' + file.name, 'error');
	                console.error('File process error:', err);
	            }
	        };
	        reader.onerror = function () { showAlert('Error reading file: ' + file.name, 'error'); };
	        reader.readAsArrayBuffer(file);
	    });
	}


    function openPageSelector(fileIndex) {
        if (fileIndex < 0 || fileIndex >= files.length) return;
        var file = files[fileIndex];
        currentEditingFile = fileIndex;
        modalContent.innerHTML = '';
        var modeVal = modeInp.value;
        var pageCount = file.pages;
        var container = document.createElement('div');

        if (modeVal === 'specific') {
            container.innerHTML = '<div class="tc-mb-16"><label class="tc-text-13 tc-text-primary tc-d-block tc-mb-8 tc-font-semibold">Page numbers to delete (comma-separated):</label><input type="text" id="tc-dpdf-pages-input" placeholder="e.g., 1,3,5" class="tc-font-mono-inline tc-dp-input" /><p class="tc-text-11 tc-text-muted tc-mt-6">Total pages: ' + pageCount + '</p></div>';
        } else if (modeVal === 'range') {
            container.innerHTML = '<div class="tc-grid-2col tc-mb-16 tc-gap-12"><div><label class="tc-text-13 tc-text-primary tc-d-block tc-mb-8 tc-font-semibold">From:</label><input type="number" id="tc-dpdf-range-from" min="1" max="' + pageCount + '" value="1" class="tc-dp-input" /></div><div><label class="tc-text-13 tc-text-primary tc-d-block tc-mb-8 tc-font-semibold">To:</label><input type="number" id="tc-dpdf-range-to" min="1" max="' + pageCount + '" value="' + pageCount + '" class="tc-dp-input" /></div></div>';
        } else if (modeVal === 'keep') {
            container.innerHTML = '<div class="tc-mb-16"><label class="tc-text-13 tc-text-primary tc-d-block tc-mb-8 tc-font-semibold">Page numbers to keep (comma-separated):</label><input type="text" id="tc-dpdf-keep-input" placeholder="e.g., 1,2,3" class="tc-font-mono-inline tc-dp-input" /><p class="tc-text-11 tc-text-muted tc-mt-6">Total pages: ' + pageCount + '</p></div>';
        }

        modalContent.appendChild(container);
        modalBd.style.display = 'block';
        modal.style.display = 'block';
        var inp = modalContent.querySelector('input');
        if (inp) setTimeout(function () { inp.focus(); }, 100);
    }

    function closeModal() {
        modalBd.style.display = 'none';
        modal.style.display = 'none';
        currentEditingFile = null;
    }

    modalCancel.addEventListener('click', closeModal);
    modalBd.addEventListener('click', function (e) { if (e.target === modalBd) closeModal(); });

    modalApply.addEventListener('click', async function () {
        if (currentEditingFile === null || currentEditingFile < 0 || currentEditingFile >= files.length) return;
        var file = files[currentEditingFile];
        var modeVal = modeInp.value;
        var pageCount = file.pages;
        var pagesToDelete = [];

        if (modeVal === 'specific') {
            var input = document.getElementById('tc-dpdf-pages-input').value.trim();
            if (!input) { showAlert('Please enter page numbers', 'error'); return; }
            pagesToDelete = input.split(',').map(function (x) { return parseInt(x.trim(), 10); }).filter(function (x) { return !isNaN(x) && x > 0 && x <= pageCount; });
        } else if (modeVal === 'range') {
            var from = parseInt(document.getElementById('tc-dpdf-range-from').value, 10);
            var to = parseInt(document.getElementById('tc-dpdf-range-to').value, 10);
            if (isNaN(from) || isNaN(to)) { showAlert('Please enter valid page numbers', 'error'); return; }
            from = Math.max(1, Math.min(from, pageCount));
            to = Math.max(1, Math.min(to, pageCount));
            var start = Math.min(from, to);
            var end = Math.max(from, to);
            for (var i = start; i <= end; i++) pagesToDelete.push(i);
        } else if (modeVal === 'keep') {
            var keepInput = document.getElementById('tc-dpdf-keep-input').value.trim();
            if (!keepInput) { showAlert('Please enter page numbers to keep', 'error'); return; }
            var keep = keepInput.split(',').map(function (x) { return parseInt(x.trim(), 10); }).filter(function (x) { return !isNaN(x) && x > 0 && x <= pageCount; });
            for (var j = 1; j <= pageCount; j++) { if (keep.indexOf(j) === -1) pagesToDelete.push(j); }
        }

        if (pagesToDelete.length === 0) { showAlert('No valid pages selected', 'error'); return; }
        closeModal();
        await processPDF(file, currentEditingFile, pagesToDelete);
    });

    async function processPDF(file, index, toDelete) {
	    try {
	        if (!window.PDFLib || !window.PDFLib.PDFDocument) { 
	            showAlert('PDF library not ready. Reload page.', 'error'); 
	            return; 
	        }

	        procWrap.style.display = 'block';

	        // Load PDF - use the Uint8Array directly without storing
	        var pdfDoc = await window.PDFLib.PDFDocument.load(file.arrayBuffer);
	        var totalPages = pdfDoc.getPageCount();
	        var pagesToKeep = [];

	        for (var p = 1; p <= totalPages; p++) {
	            if (toDelete.indexOf(p) === -1) pagesToKeep.push(p - 1);
	        }

	        if (pagesToKeep.length === 0) { 
	            showAlert('No pages remaining after deletion', 'error'); 
	            procWrap.style.display = 'none'; 
	            return; 
	        }

	        var newPdfDoc = await window.PDFLib.PDFDocument.create();

	        for (var i = 0; i < pagesToKeep.length; i++) {
	            var pageIndex = pagesToKeep[i];
	            var pages = await newPdfDoc.copyPages(pdfDoc, [pageIndex]);
	            newPdfDoc.addPage(pages[0]);

	            var pct = Math.round(((i + 1) / pagesToKeep.length) * 100);
	            procBar.style.width = pct + '%';
	            procPct.textContent = pct + '%';
	            procLabel.textContent = 'Processing page ' + (i + 1) + ' of ' + pagesToKeep.length;
	        }

	        var newPdfBytes = await newPdfDoc.save();
	        var newSize = newPdfBytes.length;

	        var prefix = document.getElementById('tc-dpdf-opt-prefix').checked ? 'modified_' : '';
	        var resultName = prefix + file.name.replace(/\.pdf$/i, '') + '_deleted-' + toDelete.length + 'p.pdf';

	        results.push({
	            name: resultName,
	            origName: file.name,
	            pdfBytes: newPdfBytes,
	            finalPageCount: pagesToKeep.length,
	            deletedCount: toDelete.length,
	            origPageCount: totalPages,
	            origSize: file.size,
	            finalSize: newSize
	        });

	        document.getElementById('tc-dpdf-stat-processed').textContent = results.length;
	        var totalDeleted = results.reduce(function (sum, item) { return sum + item.deletedCount; }, 0);
	        document.getElementById('tc-dpdf-stat-pages').textContent = totalDeleted;
	        var totalSaved = results.reduce(function (sum, item) { return sum + Math.max(0, (item.origSize || 0) - (item.finalSize || 0)); }, 0);
	        document.getElementById('tc-dpdf-stat-size').textContent = formatSize(totalSaved);

	        cacheSet({ mode: modeInp.value, results: results });

	        setTimeout(function () {
	            procWrap.style.display = 'none';
	            procBar.style.width = '0%';
	            renderCards(results);
	            if (btnDlAll) btnDlAll.style.display = 'inline-flex';
	            showAlert('PDF processed successfully!', 'success');
	        }, 500);

	    } catch (err) {
	        console.error('PDF processing error:', err);
	        procWrap.style.display = 'none';
	        showAlert('Error: ' + (err.message || 'Processing failed'), 'error');
	    }
	}	


    btnProc.addEventListener('click', function () {
        if (!files.length) { showAlert('Please upload at least one PDF', 'error'); return; }
        openPageSelector(0);
    });

    function renderCards(items) {
        grid.innerHTML = '';
        items.forEach(function (item) {
            var savingsPercent = item.origSize > 0 ? ((1 - item.finalSize / item.origSize) * 100).toFixed(1) : '0.0';
            var savingsText = savingsPercent >= 0 ? '▼ ' + savingsPercent + '% smaller' : '▲ ' + Math.abs(parseFloat(savingsPercent)).toFixed(1) + '% larger';
            var savingsColor = savingsPercent >= 0 ? '#22c55e' : '#b45309';
            var card = document.createElement('div');
            card.className = 'tc-result-card';
            var downloadUrl = URL.createObjectURL(new Blob([item.pdfBytes], { type: 'application/pdf' }));
            card.innerHTML = '<div class="tc-dp-result-icon tc-d-flex tc-items-center tc-justify-center tc-mb-10">📄</div><p class="tc-text-11 tc-font-semibold tc-text-primary tc-text-ellipsis tc-m-0 tc-mb-4" title="' + item.name + '">' + item.name + '</p><p class="tc-text-10 tc-text-secondary tc-m-0 tc-mb-2">' + item.finalPageCount + 'p / -' + item.deletedCount + 'p</p><p class="tc-text-10 tc-m-0 tc-mb-4" style="color:' + savingsColor + '">' + savingsText + '</p><p class="tc-text-10 tc-text-secondary tc-m-0 tc-mb-8">' + formatSize(item.origSize) + ' → ' + formatSize(item.finalSize) + '</p><a href="' + downloadUrl + '" download="' + item.name + '" class="tc-btn tc-btn--primary tc-card-dl-btn">⬇️ Download</a>';
            grid.appendChild(card);
        });
        cardsWrap.style.display = 'block';
    }

    if (btnDlAll) {
        btnDlAll.addEventListener('click', async function () {
            if (!results.length) { showAlert('No processed files', 'error'); return; }
            if (typeof JSZip === 'undefined') { showAlert('ZIP library loading...', 'info'); return; }
            btnDlAll.disabled = true;
            btnDlAll.textContent = '⏳ Zipping…';
            try {
                var zip = new JSZip();
                results.forEach(function (item) { zip.file(item.name, item.pdfBytes, { binary: true }); });
                var blob = await zip.generateAsync({ type: 'blob' });
                var a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = 'pdfs-' + new Date().getTime() + '.zip';
                a.click();
                showAlert('ZIP downloaded successfully', 'success');
            } catch (err) {
                console.error('ZIP error:', err);
                showAlert('Error creating ZIP', 'error');
            }
            btnDlAll.disabled = false;
            btnDlAll.textContent = '📦 Download All';
        });
    }

    btnClear.addEventListener('click', function () {
        files = [];
        results = [];
        fileInp.value = '';
        previewGrid.innerHTML = '';
        grid.innerHTML = '';
        previewWrap.style.display = 'none';
        cardsWrap.style.display = 'none';
        procWrap.style.display = 'none';
        procBar.style.width = '0%';
        setUploadLine('', false);
        resetStats();
        btnProc.disabled = true;
        if (btnDlAll) btnDlAll.style.display = 'none';
        closeModal();
        cacheClear();
        showAlert('All cleared', 'success');
    });

    resetStats();
    restoreFromCache();
})();
JS
		);
	}
}
