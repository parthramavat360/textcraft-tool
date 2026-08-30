/**
 * PDF to PNG — Tool JS
 *
 * Premium DPI cards, background color, page range (all / selected),
 * output file name, clear all. Original + result preview via pdf.js canvas.
 * Download all as ZIP. Requires pdf.js + JSZip loaded dynamically.
 *
 * @package TextCraft_Tools_Pro
 */
(function () {
    'use strict';

    var file = null;
    var dpi = 150;
    var bgColor = '#ffffff';
    var scope = 'all';
    var zipBlob = null;
    var lastName = '';

    var drop = document.getElementById('tc-p2p-drop');
    if (!drop) return;

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

    function ensureLibs() {
        var chain = Promise.resolve();
        if (!window.pdfjsLib) {
            chain = chain.then(function () {
                return loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js');
            }).then(function () {
                window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
            });
        }
        if (!window.JSZip) {
            chain = chain.then(function () {
                return loadScript('https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js');
            });
        }
        return chain;
    }

    function renderPageToImage(arrayBuffer, pageNum, scale) {
        pageNum = pageNum || 1;
        scale = scale || 1.5;
        return window.pdfjsLib.getDocument({ data: new Uint8Array(arrayBuffer) }).promise.then(function (pdf) {
            if (pageNum > pdf.numPages) pageNum = 1;
            return pdf.getPage(pageNum);
        }).then(function (page) {
            var vp = page.getViewport({ scale: scale });
            var canvas = document.createElement('canvas');
            canvas.width = vp.width;
            canvas.height = vp.height;
            var ctx = canvas.getContext('2d');
            return page.render({ canvasContext: ctx, viewport: vp }).promise.then(function () {
                var dataUrl = canvas.toDataURL('image/png');
                canvas.width = 0;
                canvas.height = 0;
                return dataUrl;
            });
        });
    }

    function setStat(id, val) {
        var el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    function copyAB(f) {
        return f.arrayBuffer().then(function (ab) { return ab.slice(0); });
    }

    function parsePageRange(input, max) {
        if (!input || !input.trim()) {
            var all = [];
            for (var i = 1; i <= max; i++) all.push(i);
            return all;
        }
        var pages = {};
        var parts = input.split(',');
        for (var p = 0; p < parts.length; p++) {
            var part = parts[p].trim();
            if (!part) continue;
            var range = part.split('-');
            if (range.length === 2) {
                var start = parseInt(range[0]) || 1;
                var end = parseInt(range[1]) || max;
                if (start < 1) start = 1;
                if (end > max) end = max;
                for (var j = start; j <= end; j++) pages[j] = true;
            } else {
                var num = parseInt(part) || 0;
                if (num >= 1 && num <= max) pages[num] = true;
            }
        }
        var result = [];
        Object.keys(pages).forEach(function (k) { result.push(parseInt(k)); });
        result.sort(function (a, b) { return a - b; });
        if (!result.length) {
            for (var m = 1; m <= max; m++) result.push(m);
        }
        return result;
    }

    var RANGE_HINTS = {
        'all':   'All pages \u2014 every page in the PDF is exported.',
        'pages': 'Selected pages \u2014 only the pages you enter are exported.'
    };
    var DPI_HINTS = {
        '72':  '72 DPI \u2014 screen resolution, smallest files.',
        '150': '150 DPI \u2014 a good balance of sharpness and file size.',
        '300': '300 DPI \u2014 print quality, largest files.'
    };

    function updateHints() {
        var dh = document.getElementById('tc-p2p-dpi-hint');
        if (dh) dh.textContent = DPI_HINTS[dpi] || DPI_HINTS[150];
        var rh = document.getElementById('tc-p2p-range-hint');
        if (rh) rh.textContent = RANGE_HINTS[scope] || RANGE_HINTS['all'];
        var opts = document.getElementById('tc-p2p-page-opts');
        if (opts) opts.style.display = scope === 'pages' ? '' : 'none';
    }

    /* ── Drop zone ─────────────────────────────────────────── */

    TCTP.initDropZone('tc-p2p-drop', 'tc-p2p-drop-input', function (f) {
        if (f.type !== 'application/pdf' && !/\.pdf$/i.test(f.name)) {
            TCTP.toast('Please select a PDF file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        zipBlob = null;
        TCTP.showFileRow('tc-p2p-file', f);
        var dlBtn = document.getElementById('tc-p2p-download');
        if (dlBtn) dlBtn.style.display = 'none';
        setStat('tc-p2p-stat-pages', '-');
        setStat('tc-p2p-stat-done', '-');
        setStat('tc-p2p-stat-size', '-');

        ensureLibs().then(function () {
            return copyAB(file);
        }).then(function (ab) {
            return renderPageToImage(ab.slice(0), 1, 1.5);
        }).then(function (dataUrl) {
            TCTP.showOriginalPreview(dataUrl);
            TCTP.switchToOriginalTab();
            return copyAB(file);
        }).then(function (ab) {
            return window.pdfjsLib.getDocument({ data: new Uint8Array(ab) }).promise;
        }).then(function (pdf) {
            setStat('tc-p2p-stat-pages', pdf.numPages + ' pages');
        }).catch(function () {});
    }, '.pdf,application/pdf');

    /* ── Options wiring ────────────────────────────────────── */

    var dpiCards = document.querySelectorAll('.tc-modes[data-group="p2p-dpi"] .tc-btn');
    dpiCards.forEach(function (card) {
        card.addEventListener('click', function () {
            TCTP.activateBtn(card);
            dpi = parseInt(card.getAttribute('data-val')) || 150;
            updateHints();
        });
    });

    document.querySelectorAll('.tc-modes[data-group="p2p-range"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            scope = btn.getAttribute('data-val') || 'all';
            updateHints();
        });
    });

    var bgInput = document.getElementById('tc-p2p-bgcolor');
    var bgVal = document.getElementById('tc-p2p-bg-val');
    if (bgInput && bgVal) {
        bgInput.addEventListener('input', function () {
            bgColor = bgInput.value;
            bgVal.textContent = bgColor.toUpperCase();
        });
    }

    var removeBtn = document.querySelector('#tc-p2p-file .tc-x');
    if (removeBtn) removeBtn.addEventListener('click', clearAll);

    /* ── Clear all ─────────────────────────────────────────── */

    function clearAll() {
        file = null;
        zipBlob = null;
        TCTP.hideFileRow('tc-p2p-file');
        setStat('tc-p2p-stat-pages', '-');
        setStat('tc-p2p-stat-done', '-');
        setStat('tc-p2p-stat-size', '-');
        var dlBtn = document.getElementById('tc-p2p-download');
        if (dlBtn) dlBtn.style.display = 'none';
        var pagesIn = document.getElementById('tc-p2p-pages');
        if (pagesIn) pagesIn.value = '';
        var nameIn = document.getElementById('tc-p2p-name');
        if (nameIn) nameIn.value = '';
        var orig = document.getElementById('tc-preview-orig');
        if (orig) orig.innerHTML = '<span style="color:var(--muted);font-size:13px">Original preview will appear here</span>';
        var res = document.getElementById('tc-preview-result');
        if (res) res.innerHTML = '<span style="color:var(--muted);font-size:13px">Result preview will appear here</span>';
        TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Ready');
        TCTP.switchToOriginalTab();
    }

    var clearBtn = document.getElementById('tc-p2p-clear');
    if (clearBtn) clearBtn.addEventListener('click', function () {
        clearAll();
        TCTP.toast('Cleared.', '\uD83E\uDDF9');
    });

    /* ── Convert ───────────────────────────────────────────── */

    var convertBtn = document.getElementById('tc-p2p-convert');
    if (convertBtn) {
        convertBtn.addEventListener('click', async function () {
            if (!file) { TCTP.toast('Please select a PDF file first.', '\u26A0\uFE0F'); return; }

            TCTP.showProgress('tc-p2p-progress');
            TCTP.setProgress('tc-p2p-progress', 5, 'Loading libraries...');

            try {
                await ensureLibs();
                TCTP.setProgress('tc-p2p-progress', 10, 'Reading PDF...');

                var ab = await copyAB(file);
                var pdf = await window.pdfjsLib.getDocument({ data: new Uint8Array(ab.slice(0)) }).promise;
                var numPages = pdf.numPages;
                var scale = dpi / 72;

                var pagesToConvert;
                if (scope === 'pages') {
                    var pagesInput = document.getElementById('tc-p2p-pages');
                    var val = pagesInput ? pagesInput.value.trim() : '';
                    if (!val) { TCTP.toast('Please enter page numbers to convert.', '\u26A0\uFE0F'); return; }
                    pagesToConvert = parsePageRange(val, numPages);
                } else {
                    pagesToConvert = parsePageRange('', numPages);
                }

                setStat('tc-p2p-stat-pages', numPages + ' pages');
                setStat('tc-p2p-stat-done', '0');

                var zip = new JSZip();
                var totalPages = pagesToConvert.length;
                var firstPageDataUrl = null;

                for (var idx = 0; idx < totalPages; idx++) {
                    var pageNum = pagesToConvert[idx];
                    var pct = 10 + Math.round(((idx + 0.5) / totalPages) * 80);
                    TCTP.setProgress('tc-p2p-progress', pct, 'Rendering page ' + pageNum + ' (' + (idx + 1) + '/' + totalPages + ')...');

                    var page = await pdf.getPage(pageNum);
                    var vp = page.getViewport({ scale: scale });
                    var canvas = document.createElement('canvas');
                    canvas.width = vp.width;
                    canvas.height = vp.height;
                    var ctx = canvas.getContext('2d');

                    ctx.fillStyle = bgColor;
                    ctx.fillRect(0, 0, canvas.width, canvas.height);

                    await page.render({ canvasContext: ctx, viewport: vp }).promise;

                    var imgData = canvas.toDataURL('image/png');
                    var imgBytes = Uint8Array.from(atob(imgData.split(',')[1]), function (c) { return c.charCodeAt(0); });
                    var paddedNum = String(pageNum).padStart(String(numPages).length, '0');
                    zip.file('page-' + paddedNum + '.png', imgBytes);

                    if (idx === 0) firstPageDataUrl = imgData;

                    setStat('tc-p2p-stat-done', (idx + 1) + ' / ' + totalPages);
                    canvas.width = 0;
                    canvas.height = 0;
                }

                TCTP.setProgress('tc-p2p-progress', 95, 'Creating ZIP...');
                zipBlob = await zip.generateAsync({ type: 'blob' });

                TCTP.setProgress('tc-p2p-progress', 100, 'Done!');

                var nameInput = document.getElementById('tc-p2p-name');
                var custom = nameInput ? nameInput.value.trim() : '';
                lastName = (custom || (file ? file.name.replace(/\.pdf$/i, '') : 'document')) + '-pages.zip';

                var inputSize = file.size;
                var outputSize = zipBlob.size;
                setStat('tc-p2p-stat-size', TCTP.formatSize(outputSize));

                TCTP.updateResultPanel(TCTP.formatSize(inputSize), TCTP.formatSize(outputSize), (inputSize > outputSize ? ((1 - outputSize / inputSize) * 100).toFixed(1) : '0') + '%', 'Done');
                TCTP.toast('Exported ' + totalPages + ' pages as PNG!');

                if (firstPageDataUrl) {
                    TCTP.showResultPreview(firstPageDataUrl);
                }
                TCTP.switchToResultTab();

                var dlBtn = document.getElementById('tc-p2p-download');
                if (dlBtn) dlBtn.style.display = '';
            } catch (err) {
                TCTP.toast('Conversion failed: ' + err.message, '\u274C');
                TCTP.hideProgress('tc-p2p-progress');
            }
        });
    }

    /* ── Download ──────────────────────────────────────────── */

    var downloadBtn = document.getElementById('tc-p2p-download');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function () {
            if (!zipBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
            TCTP.downloadBlob(zipBlob, lastName);
        });
    }

    updateHints();

})();
