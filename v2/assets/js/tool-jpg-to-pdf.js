/**
 * JPG to PDF — Tool JS
 *
 * Multi-file JPG drop → single PDF. Premium page-size cards, orientation,
 * margins slider, fit/optimize switches, output file name, clear all.
 * Original + result preview rendered via image/pdf.js canvas.
 * Requires pdf.js + pdf-lib loaded dynamically.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var files = [];
    var resultBlob = null;
    var lastName = '';

    var drop = document.getElementById('tc-j2pdf-drop');
    if (!drop) return;

    // ── Dynamic script loading ────────────────────────────────

    function loadScript(src) {
        return new Promise(function (resolve, reject) {
            if (document.querySelector('script[src="' + src + '"]')) { resolve(); return; }
            var s = document.createElement('script');
            s.src = src;
            s.onload = resolve;
            s.onerror = reject;
            document.head.appendChild(s);
        });
    }

    function ensurePdfJs() {
        if (window.pdfjsLib) return Promise.resolve();
        return loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js').then(function () {
            window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        });
    }

    function ensurePdfLib() {
        if (window.PDFLib) return Promise.resolve();
        return loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js');
    }

    function setStat(id, val) {
        var el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    // ── Page size maps ────────────────────────────────────────

    var PAGE_SIZES = {
        'a4':     { w: 595.28, h: 841.89 },
        'letter': { w: 612,    h: 792    },
        'legal':  { w: 612,    h: 1008   },
        'auto':   null
    };

    var SIZE_HINTS = {
        'a4':     'A4 \u2014 standard international page size, 210 \u00D7 297 mm.',
        'letter': 'Letter \u2014 North American page size, 8.5 \u00D7 11 in.',
        'legal':  'Legal \u2014 longer North American page size, 8.5 \u00D7 14 in.',
        'auto':   'Auto \u2014 each page is sized to fit its image dimensions.'
    };
    var ORIENT_HINTS = {
        'portrait':  'Portrait \u2014 pages taller than they are wide.',
        'landscape': 'Landscape \u2014 pages wider than they are tall.'
    };

    function updateHints() {
        var sh = document.getElementById('tc-j2pdf-size-hint');
        var sizeKey = getSelectedPageSize();
        if (sh) sh.textContent = SIZE_HINTS[sizeKey] || SIZE_HINTS['a4'];
        var oh = document.getElementById('tc-j2pdf-orient-hint');
        if (oh) oh.textContent = ORIENT_HINTS[getOrientation()] || ORIENT_HINTS['portrait'];
    }

    // ── Drop zone (multi-file) ────────────────────────────────

    var inputEl = document.getElementById('tc-j2pdf-drop-input');
    if (!inputEl) return;
    inputEl.setAttribute('multiple', 'multiple');

    function handleFiles(newFiles) {
        var valid = [];
        for (var i = 0; i < newFiles.length; i++) {
            var f = newFiles[i];
            if (f.type === 'image/jpeg' || /\.jpe?g$/i.test(f.name)) {
                valid.push(f);
            }
        }
        if (!valid.length) {
            TCTP.toast('Please select JPG/JPEG images.', '\u26A0\uFE0F');
            return;
        }
        files = files.concat(valid);
        resultBlob = null;
        var dlBtn = document.getElementById('tc-j2pdf-download');
        if (dlBtn) dlBtn.style.display = 'none';

        showFileList();
        showOriginalPreview();
    }

    drop.addEventListener('click', function () { inputEl.click(); });
    drop.addEventListener('dragover', function (e) {
        e.preventDefault();
        drop.classList.add('hot');
    });
    drop.addEventListener('dragleave', function () {
        drop.classList.remove('hot');
    });
    drop.addEventListener('drop', function (e) {
        e.preventDefault();
        drop.classList.remove('hot');
        if (e.dataTransfer.files.length) handleFiles(e.dataTransfer.files);
    });
    inputEl.addEventListener('change', function () {
        if (inputEl.files.length) handleFiles(inputEl.files);
        inputEl.value = '';
    });

    // ── File list UI ──────────────────────────────────────────

    function showFileList() {
        var row = document.getElementById('tc-j2pdf-file');
        if (!row) return;
        if (!files.length) {
            row.style.display = 'none';
            return;
        }
        var nameEl = row.querySelector('.tc-file-name');
        var sizeEl = row.querySelector('.tc-file-size');
        if (nameEl) nameEl.textContent = files.length === 1 ? files[0].name : files.length + ' images selected';
        var totalSize = 0;
        files.forEach(function (f) { totalSize += f.size; });
        if (sizeEl) sizeEl.textContent = TCTP.formatSize(totalSize);
        row.style.display = '';
        row.classList.add('visible');
    }

    var removeBtn = document.querySelector('#tc-j2pdf-file .tc-x');
    if (removeBtn) removeBtn.addEventListener('click', clearAll);

    // ── Original preview (first image) ────────────────────────

    function showOriginalPreview() {
        if (!files.length) return;
        var reader = new FileReader();
        reader.onload = function (e) {
            TCTP.showOriginalPreview(e.target.result);
            TCTP.switchToOriginalTab();
        };
        reader.readAsDataURL(files[0]);
    }

    // ── Page size cards ───────────────────────────────────────

    var sizeCards = document.querySelectorAll('.tc-modes[data-group="j2pdf-size"] .tc-btn');
    sizeCards.forEach(function (card) {
        card.addEventListener('click', function () {
            TCTP.activateBtn(card);
            updateHints();
        });
    });

    function getSelectedPageSize() {
        var sel = document.querySelector('.tc-modes[data-group="j2pdf-size"] .tc-btn.sel');
        return sel ? sel.getAttribute('data-val') : 'a4';
    }

    // ── Orientation segmented ─────────────────────────────────

    var orientCards = document.querySelectorAll('.tc-modes[data-group="j2pdf-orient"] .tc-btn');
    orientCards.forEach(function (card) {
        card.addEventListener('click', function () {
            TCTP.activateBtn(card);
            updateHints();
        });
    });

    function getOrientation() {
        var sel = document.querySelector('.tc-modes[data-group="j2pdf-orient"] .tc-btn.sel');
        return sel ? sel.getAttribute('data-val') : 'portrait';
    }

    // ── Margins slider ────────────────────────────────────────

    var marginRange = document.getElementById('tc-j2pdf-margins');
    var marginVal = document.getElementById('tc-j2pdf-margins-val');
    if (marginRange && marginVal) {
        marginRange.addEventListener('input', function () {
            marginVal.textContent = marginRange.value + ' px';
        });
    }

    // ── Clear all ─────────────────────────────────────────────

    function clearAll() {
        files = [];
        resultBlob = null;
        var row = document.getElementById('tc-j2pdf-file');
        if (row) { row.style.display = 'none'; row.classList.remove('visible'); }
        var dlBtn = document.getElementById('tc-j2pdf-download');
        if (dlBtn) dlBtn.style.display = 'none';
        var nameInput = document.getElementById('tc-j2pdf-name');
        if (nameInput) nameInput.value = '';
        var orig = document.getElementById('tc-preview-orig');
        if (orig) orig.innerHTML = '<span style="color:var(--muted);font-size:13px">Original preview will appear here</span>';
        var res = document.getElementById('tc-preview-result');
        if (res) res.innerHTML = '<span style="color:var(--muted);font-size:13px">Result preview will appear here</span>';
        setStat('tc-j2pdf-stat-count', '0');
        setStat('tc-j2pdf-stat-orig', '-');
        setStat('tc-j2pdf-stat-comp', '-');
        setStat('tc-j2pdf-stat-pages', '-');
        TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Ready');
        TCTP.switchToOriginalTab();
    }

    var clearBtn = document.getElementById('tc-j2pdf-clear');
    if (clearBtn) clearBtn.addEventListener('click', function () {
        clearAll();
        TCTP.toast('Cleared.', '\uD83E\uDDF9');
    });

    // ── Convert to PDF ────────────────────────────────────────

    var convertBtn = document.getElementById('tc-j2pdf-convert');
    if (convertBtn) convertBtn.addEventListener('click', async function () {
        if (!files.length) {
            TCTP.toast('Please drop JPG images first.', '\u26A0\uFE0F');
            return;
        }

        TCTP.showProgress('tc-j2pdf-progress');
        TCTP.setProgress('tc-j2pdf-progress', 5, 'Loading libraries...');

        try {
            await ensurePdfJs();
            await ensurePdfLib();
            TCTP.setProgress('tc-j2pdf-progress', 15, 'Creating PDF...');

            var sizeKey = getSelectedPageSize();
            var margins = marginRange ? parseInt(marginRange.value, 10) : 20;
            var isLandscape = getOrientation() === 'landscape';
            var fitInput = document.getElementById('tc-j2pdf-fit');
            var optimizeInput = document.getElementById('tc-j2pdf-optimize');
            var doFit = fitInput ? fitInput.checked : true;
            var doOptimize = optimizeInput ? optimizeInput.checked : false;

            var pdfDoc = await window.PDFLib.PDFDocument.create();
            var total = files.length;

            for (var i = 0; i < total; i++) {
                var f = files[i];
                var pct = 15 + Math.round(((i + 0.5) / total) * 75);
                TCTP.setProgress('tc-j2pdf-progress', pct, 'Adding image ' + (i + 1) + ' of ' + total + '...');

                var ab = await f.arrayBuffer();
                var imgBytes = new Uint8Array(ab);

                var image;
                try {
                    image = await pdfDoc.embedJpg(imgBytes);
                } catch (_) {
                    try {
                        image = await pdfDoc.embedPng(imgBytes);
                    } catch (_) {
                        TCTP.toast('Skipping unsupported image: ' + f.name, '\u26A0\uFE0F');
                        continue;
                    }
                }

                var pageW, pageH;

                if (sizeKey === 'auto') {
                    var imgPxW = image.width;
                    var imgPxH = image.height;
                    var marginPx = margins * 0.75;
                    var usableW = imgPxW + marginPx * 2;
                    var usableH = imgPxH + marginPx * 2;
                    pageW = usableW * 72 / 96;
                    pageH = usableH * 72 / 96;
                    if (isLandscape && pageW < pageH) {
                        var tmp = pageW;
                        pageW = pageH;
                        pageH = tmp;
                    }
                } else {
                    var ps = PAGE_SIZES[sizeKey] || PAGE_SIZES['a4'];
                    pageW = ps.w;
                    pageH = ps.h;
                    if (isLandscape) {
                        var tmp2 = pageW;
                        pageW = pageH;
                        pageH = tmp2;
                    }
                }

                var page = pdfDoc.addPage([pageW, pageH]);

                var availW = pageW - (margins * 2 * 72 / 96);
                var availH = pageH - (margins * 2 * 72 / 96);
                var scaleW = availW / image.width;
                var scaleH = availH / image.height;
                var scale = doFit ? Math.min(scaleW, scaleH, 1) : Math.min(scaleW, scaleH);

                var w = image.width * scale;
                var h = image.height * scale;
                var x = (pageW - w) / 2;
                var y = (pageH - h) / 2;

                page.drawImage(image, { x: x, y: y, width: w, height: h });
            }

            TCTP.setProgress('tc-j2pdf-progress', 92, 'Saving PDF...');
            var saveOpts = doOptimize
                ? { useObjectStreams: true, updateMetadata: false }
                : { useObjectStreams: false, updateMetadata: false };
            var newBytes = await pdfDoc.save(saveOpts);
            resultBlob = new Blob([newBytes], { type: 'application/pdf' });

            TCTP.setProgress('tc-j2pdf-progress', 100, 'Done!');

            var totalIn = 0;
            files.forEach(function (f) { totalIn += f.size; });
            var saved = totalIn > resultBlob.size ? ((1 - resultBlob.size / totalIn) * 100).toFixed(1) : '0';

            setStat('tc-j2pdf-stat-count', total);
            setStat('tc-j2pdf-stat-orig', TCTP.formatSize(totalIn));
            setStat('tc-j2pdf-stat-comp', TCTP.formatSize(resultBlob.size));
            setStat('tc-j2pdf-stat-pages', total + (total === 1 ? ' page' : ' pages'));

            var nameInput = document.getElementById('tc-j2pdf-name');
            var custom = nameInput ? nameInput.value.trim() : '';
            if (custom) {
                lastName = custom.replace(/\.pdf$/i, '') + '.pdf';
            } else if (files.length === 1) {
                lastName = files[0].name.replace(/\.[^.]+$/, '') + '.pdf';
            } else {
                lastName = 'converted.pdf';
            }

            TCTP.updateResultPanel(TCTP.formatSize(totalIn), TCTP.formatSize(resultBlob.size), saved + '%', 'Done');
            TCTP.toast('PDF created with ' + total + ' page(s)!');

            var dlBtn = document.getElementById('tc-j2pdf-download');
            if (dlBtn) dlBtn.style.display = '';

            // Result preview: render page 1 via pdf.js
            try {
                var resultAb = await resultBlob.arrayBuffer();
                var pbytes = new Uint8Array(resultAb.slice(0));
                var pdf = await window.pdfjsLib.getDocument({ data: pbytes }).promise;
                var page1 = await pdf.getPage(1);
                var vp = page1.getViewport({ scale: 1.5 });
                var canvas = document.createElement('canvas');
                canvas.width = vp.width;
                canvas.height = vp.height;
                var ctx = canvas.getContext('2d');
                await page1.render({ canvasContext: ctx, viewport: vp }).promise;
                var dataUrl = canvas.toDataURL('image/png');
                canvas.width = 0;
                canvas.height = 0;
                TCTP.showResultPreview(dataUrl);
            } catch (_) {
                // If preview fails, still show result tab
            }

            TCTP.switchToResultTab();
            TCTP.hideProgress('tc-j2pdf-progress');

        } catch (err) {
            TCTP.toast('Conversion failed: ' + err.message, '\u274C');
            TCTP.hideProgress('tc-j2pdf-progress');
        }
    });

    // ── Download ──────────────────────────────────────────────

    var downloadBtn = document.getElementById('tc-j2pdf-download');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function () {
            if (!resultBlob) {
                TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F');
                return;
            }
            TCTP.downloadBlob(resultBlob, lastName);
        });
    }

    updateHints();

})();
