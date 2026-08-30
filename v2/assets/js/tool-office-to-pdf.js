/**
 * Word / Excel / PowerPoint to PDF — Tool JS
 * Converts .docx, .xlsx/.xls/.csv, and .pptx to PDF entirely in the browser.
 * Uses docx-preview (Word), SheetJS xlsx (Excel), pptxjs (PowerPoint) to render
 * into a styled container, then html2pdf (html2canvas + jsPDF) to produce the
 * final PDF. Falls back to the browser print dialog if capture is empty.
 * 100% client-side.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var drop = document.getElementById('tc-ofp-drop');
    if (!drop) return;

    var currentFmt = 'word';
    var file = null;
    var pdfBlob = null;
    var fileName = 'document';
    var pageSize = 'auto';        // auto | a4 | letter
    var orient = 'portrait';      // portrait | landscape | auto
    var quality = 2;              // 1 | 2 | 3 (render scale)

    // Acceptable extensions per format
    var ACCEPT = {
        word: ['.docx'],
        excel: ['.xlsx', '.xls', '.csv'],
        ppt: ['.pptx']
    };

    function labelFor(fmt) {
        return fmt === 'word' ? 'Word (.docx)'
            : fmt === 'excel' ? 'Excel (.xlsx/.xls/.csv)'
            : 'PowerPoint (.pptx)';
    }

    // ── Option hints ───────────────────────────────────────────

    var FMT_HINTS = {
        word: 'Word \u2014 .docx documents render with their original layout.',
        excel: 'Excel \u2014 .xlsx, .xls and .csv spreadsheets render sheet by sheet.',
        ppt: 'PowerPoint \u2014 .pptx slide decks render slide by slide.'
    };
    var SIZE_HINTS = {
        auto: 'Automatic \u2014 A4 for documents, wider pages for wide slides.',
        a4: 'A4 \u2014 210 \u00d7 297 mm output pages.',
        letter: 'Letter \u2014 8.5 \u00d7 11 in output pages.'
    };
    var ORIENT_HINTS = {
        portrait: 'Portrait \u2014 taller than wide.',
        landscape: 'Landscape \u2014 wider than tall.',
        auto: 'Automatic \u2014 portrait for documents, landscape for wide slides.'
    };
    var QUALITY_LABELS = { 1: 'Standard', 2: 'High', 3: 'Ultra' };

    function updateHints() {
        var fh = document.getElementById('tc-ofp-fmt-hint');
        if (fh) fh.textContent = FMT_HINTS[currentFmt] || FMT_HINTS.word;
        var sh = document.getElementById('tc-ofp-size-hint');
        if (sh) sh.textContent = SIZE_HINTS[pageSize] || SIZE_HINTS.auto;
        var oh = document.getElementById('tc-ofp-orient-hint');
        if (oh) oh.textContent = ORIENT_HINTS[orient] || ORIENT_HINTS.portrait;
        var qv = document.getElementById('tc-ofp-quality-val');
        if (qv) qv.textContent = QUALITY_LABELS[quality] || 'High';
    }

    // ── Format cards ───────────────────────────────────────────

    var fmtBtns = document.querySelectorAll('.tc-modes[data-group="ofp-fmt"] .tc-btn');
    fmtBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            currentFmt = btn.getAttribute('data-val') || 'word';
            var stat = document.getElementById('tc-ofp-stat-type');
            if (stat) stat.textContent = labelFor(currentFmt);
            var dropHint = document.querySelector('#tc-ofp-drop > b');
            if (dropHint) dropHint.textContent = 'Drag & drop a ' + labelFor(currentFmt) + ' file here';
            updateHints();
            resetAll();
        });
    });

    // ── Page size / orientation / quality / name wiring ─────────

    document.querySelectorAll('.tc-modes[data-group="ofp-size"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            pageSize = btn.getAttribute('data-val') || 'auto';
            updateHints();
        });
    });

    document.querySelectorAll('.tc-modes[data-group="ofp-orient"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            orient = btn.getAttribute('data-val') || 'portrait';
            updateHints();
        });
    });

    var qualityInput = document.getElementById('tc-ofp-quality');
    if (qualityInput) qualityInput.addEventListener('input', function () {
        quality = parseInt(qualityInput.value, 10) || 2;
        var qv = document.getElementById('tc-ofp-quality-val');
        if (qv) qv.textContent = QUALITY_LABELS[quality] || 'High';
    });

    // ── CDN script loader ──────────────────────────────────────

    function loadScript(src) {
        return new Promise(function (resolve, reject) {
            if (document.querySelector('script[data-ofp-src="' + src + '"]')) { resolve(); return; }
            var s = document.createElement('script');
            s.src = src;
            s.setAttribute('data-ofp-src', src);
            if (!s.async) s.async = true;
            s.onload = resolve;
            s.onerror = function () { reject(new Error('Failed to load ' + src)); };
            document.head.appendChild(s);
        });
    }

    var CDN = {
        docx: 'https://cdn.jsdelivr.net/npm/docx-preview@0.3.0/dist/docx-preview.min.js',
        xlsx: 'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js',
        pptx: 'https://cdn.jsdelivr.net/gh/meshesha/pptxjs@master/js/pptxjs.js',
        jquery: 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js',
        html2pdf: 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js'
    };

    function ensureLibs(fmt) {
        var libs = [CDN.html2pdf];
        if (fmt === 'word') libs.push(CDN.docx);
        if (fmt === 'excel') libs.push(CDN.xlsx);
        if (fmt === 'ppt') { libs.push(CDN.jquery); libs.push(CDN.pptx); }
        return libs.reduce(function (p, src) {
            return p.then(function () { return loadScript(src); });
        }, Promise.resolve());
    }

    // ── Drop zone ──────────────────────────────────────────────

    TCTP.initDropZone('tc-ofp-drop', 'tc-ofp-drop-input', function (f) {
        var ext = '.' + (f.name.split('.').pop() || '').toLowerCase();
        if (ACCEPT[currentFmt].indexOf(ext) === -1) {
            TCTP.toast('That file type doesn\u2019t match the selected format. Please choose a ' + labelFor(currentFmt) + ' file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        fileName = f.name.replace(/\.[^.]+$/, '');
        pdfBlob = null;
        TCTP.showFileRow('tc-ofp-file', f);
        var dl = document.getElementById('tc-ofp-download');
        if (dl) dl.disabled = true;
        showOriginalInfo(f);
        var resTab = document.getElementById('tc-preview-result');
        if (resTab) resTab.innerHTML = '<div class="tc-preview-placeholder">Your PDF preview will appear here after conversion.</div>';
        TCTP.toast('File loaded: ' + f.name);
    }, '.docx,.xlsx,.xls,.csv,.pptx');

    var removeBtn = document.querySelector('#tc-ofp-file .tc-x');
    if (removeBtn) removeBtn.addEventListener('click', resetAll);

    // ── Per-format renderers ───────────────────────────────────

    // Renders the ArrayBuffer into `container` and returns a Promise that
    // resolves when rendering is done.
    function renderToContainer(fmt, buffer, container) {
        return new Promise(function (resolve, reject) {
            container.innerHTML = '';
            if (fmt === 'word') {
                if (!(window.docx && window.docx.renderAsync)) { reject(new Error('Word renderer unavailable.')); return; }
                var u8 = new Uint8Array(buffer);
                window.docx.renderAsync(u8, container, null, { inWrapper: true, ignoreWidth: false, breakPages: true }).then(function () {
                    resolve();
                }).catch(reject);
            } else if (fmt === 'excel') {
                if (!window.XLSX) { reject(new Error('Excel renderer unavailable.')); return; }
                try {
                    var wb = window.XLSX.read(new Uint8Array(buffer), { type: 'array' });
                    var html = '';
                    var sheetCount = 0;
                    wb.SheetNames.forEach(function (name) {
                        var ws = wb.Sheets[name];
                        html += '<div class="tc-ofp-sheet"><h3 class="tc-ofp-sheet-title">' + name + '</h3>';
                        html += '<div class="tc-ofp-sheet-wrap">' + window.XLSX.utils.sheet_to_html(ws) + '</div></div>';
                        sheetCount++;
                    });
                    container.innerHTML = html;
                    resolve();
                } catch (e) { reject(e); }
            } else { // ppt
                if (!(window.jQuery && window.jQuery.fn.pptxToHtml)) { reject(new Error('PowerPoint renderer unavailable.')); return; }
                var blobUrl = URL.createObjectURL(new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.presentationml.presentation' }));
                jQueryPptx(container, blobUrl).then(resolve).catch(reject);
            }
        });
    }

    function jQueryPptx(container, blobUrl) {
        return new Promise(function (resolve, reject) {
            var target = document.createElement('div');
            target.id = 'tc-ofp-ppt';
            container.appendChild(target);
            // give the slide deck an explicit size so capture has dimensions
            target.style.width = '960px';
            try {
                window.jQuery(target).pptxToHtml({ pptxFileUrl: blobUrl, slideScale: 0.75, slideMode: false }).done(function () {
                    resolve();
                });
            } catch (e) { reject(e); }
            // safety timeout
            setTimeout(function () { resolve(); }, 3000);
        });
    }

    // ── Capture container → PDF via html2pdf ───────────────────

    function resolvePage(fmt) {
        var isPpt = fmt === 'ppt';
        var fmtOut = pageSize === 'letter' ? 'letter' : 'a4'; // auto → a4
        var orientOut = orient === 'portrait' ? 'portrait'
            : orient === 'landscape' ? 'landscape'
            : (isPpt ? 'landscape' : 'portrait');
        return { format: fmtOut, orientation: orientOut };
    }

    function captureToPdf(container) {
        return new Promise(function (resolve, reject) {
            var page = resolvePage(currentFmt);
            var scale = quality === 1 ? 1.5 : quality === 3 ? 3 : 2;
            var opt = {
                margin: [8, 8, 8, 8],
                filename: (fileName || 'converted') + '.pdf',
                image: { type: 'jpeg', quality: 0.9 },
                html2canvas: { scale: scale, useCORS: true, logging: false, windowWidth: container.scrollWidth },
                jsPDF: { unit: 'mm', format: page.format, orientation: page.orientation },
                pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
            };
            window.html2pdf().set(opt).from(container).output('blob').then(function (blob) {
                if (!blob || blob.size < 100) { reject(new Error('empty')); return; }
                resolve(blob);
            }).catch(reject);
        });
    }

    // ── Convert ────────────────────────────────────────────────

    var convertBtn = document.getElementById('tc-ofp-convert');
    var dlBtn = document.getElementById('tc-ofp-download');

    if (convertBtn) convertBtn.addEventListener('click', function () {
        if (!file) {
            TCTP.toast('Please add a ' + labelFor(currentFmt) + ' file first.', '\u26A0\uFE0F');
            return;
        }
        TCTP.showProgress('tc-ofp-progress', 5, 'Loading libraries...');
        ensureLibs(currentFmt).then(function () {
            TCTP.setProgress('tc-ofp-progress', 30, 'Rendering document...');
            return file.arrayBuffer();
        }).then(function (buffer) {
            var container = document.createElement('div');
            container.className = 'tc-ofp-render';
            container.style.width = '710px';
            container.style.background = '#ffffff';
            container.style.padding = '16px';
            container.style.position = 'absolute';
            container.style.left = '-9999px';
            container.style.top = '0';
            container.style.zIndex = '-1';
            document.body.appendChild(container);
            return renderToContainer(currentFmt, buffer, container).then(function () {
                TCTP.setProgress('tc-ofp-progress', 65, 'Generating PDF...');
                return captureToPdf(container).then(function (blob) {
                    container.parentNode && container.parentNode.removeChild(container);
                    return blob;
                });
            }).catch(function (err) {
                // Keep container for print fallback
                window.__tcOfpContainer = container;
                throw err;
            });
        }).then(function (blob) {
            pdfBlob = blob;
            TCTP.setProgress('tc-ofp-progress', 100, 'Done!');
            setTimeout(function () { TCTP.hideProgress('tc-ofp-progress'); }, 500);
            showPdf(blob);
            updateStats(blob);
            var dl = document.getElementById('tc-ofp-download');
            if (dl) dl.disabled = false;
            TCTP.toast('PDF generated! ' + TCTP.formatSize(blob.size), '\u2705');
        }).catch(function (err) {
            TCTP.hideProgress('tc-ofp-progress');
            // html2canvas capture failed — offer print fallback
            var container = window.__tcOfpContainer;
            if (container) {
                var preview = container.cloneNode(true);
                preview.id = '';
                preview.style.position = 'static';
                preview.style.left = 'auto';
                preview.style.visibility = 'visible';
                preview.style.width = '100%';
                var resTab = document.getElementById('tc-preview-result');
                if (resTab) { resTab.innerHTML = ''; resTab.appendChild(preview); }
                TCTP.switchToResultTab();
                TCTP.toast('Rendered below. Use "Print / Save as PDF" to save it.', '\u26A0\uFE0F');
            } else {
                TCTP.toast('Conversion failed: ' + (err && err.message ? err.message : 'unknown error'), '\u26A0\uFE0F');
            }
            if (window.__tcOfpContainer && window.__tcOfpContainer.parentNode) window.__tcOfpContainer.parentNode.removeChild(window.__tcOfpContainer);
        });
    });

    // ── Preview tabs ───────────────────────────────────────────

    // Original preview tab shows an info panel (office files can't be shown as images).
    function showOriginalInfo(f) {
        var info = document.createElement('div');
        info.className = 'tc-ofp-origin';
        info.innerHTML =
            '<div class="tc-ofp-origin-icon">\u{1F4C4}</div>' +
            '<div class="tc-ofp-origin-name">' + f.name + '</div>' +
            '<div class="tc-ofp-origin-meta">' + (f.type || 'File') + ' &middot; ' + TCTP.formatSize(f.size) + '</div>';
        var el = document.getElementById('tc-preview-orig');
        if (el) { el.innerHTML = ''; el.appendChild(info); }
    }

    // Result preview tab shows the generated PDF in an iframe viewer.
    function showPdfPreview(blob) {
        var url = URL.createObjectURL(blob);
        var el = document.getElementById('tc-preview-result');
        if (el) el.innerHTML = '<iframe class="tc-ofp-iframe" src="' + url + '" title="PDF preview"></iframe>';
        return url;
    }

    // ── PDF preview via iframe ─────────────────────────────────

    function showPdf(blob) {
        showPdfPreview(blob);
        // switch to Result tab so the PDF is visible
        TCTP.switchToResultTab();
    }

    // ── Download ───────────────────────────────────────────────

    if (dlBtn) dlBtn.addEventListener('click', function () {
        if (!pdfBlob) {
            if (!file) { TCTP.toast('Add a file first.', '\u26A0\uFE0F'); return; }
            if (window.__tcOfpContainer) {
                window.print();
                return;
            }
            TCTP.toast('Convert to PDF first.', '\u26A0\uFE0F');
            return;
        }
        var outNameInput = document.getElementById('tc-ofp-name');
        var outName = outNameInput ? outNameInput.value.trim() : '';
        if (!outName) outName = fileName || 'converted';
        TCTP.downloadBlob(pdfBlob, outName + '.pdf');
    });

    // ── Stats ──────────────────────────────────────────────────

    function updateStats(blob) {
        var t = document.getElementById('tc-ofp-stat-type');
        if (t) t.textContent = labelFor(currentFmt);
        var p = document.getElementById('tc-ofp-stat-pages');
        if (p) p.textContent = '—';
        var s = document.getElementById('tc-ofp-stat-size');
        if (s && blob) s.textContent = TCTP.formatSize(blob.size);
    }

    function resetAll() {
        file = null;
        pdfBlob = null;
        delete window.__tcOfpContainer;
        var dl = document.getElementById('tc-ofp-download');
        if (dl) dl.disabled = true;
        TCTP.hideFileRow('tc-ofp-file');
        var nameInput = document.getElementById('tc-ofp-name');
        if (nameInput) nameInput.value = '';
        var orig = document.getElementById('tc-preview-orig');
        if (orig) orig.innerHTML = '<div class="tc-preview-placeholder">Original preview will appear here</div>';
        var res = document.getElementById('tc-preview-result');
        if (res) res.innerHTML = '<div class="tc-preview-placeholder">Result preview will appear here</div>';
        var s = document.getElementById('tc-ofp-stat-size');
        if (s) s.textContent = '—';
        var pages = document.getElementById('tc-ofp-stat-pages');
        if (pages) pages.textContent = '—';
        TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Idle');
        TCTP.switchToOriginalTab();
    }

    var clearBtn = document.getElementById('tc-ofp-clear');
    if (clearBtn) clearBtn.addEventListener('click', function () {
        resetAll();
        TCTP.toast('Cleared.', '\uD83E\uDDF9');
    });

    updateHints();
    updateStats(null);

})();
