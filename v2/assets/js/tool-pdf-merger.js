/**
 * PDF Merger — Tool JS
 *
 * Multi-file drop zone, premium file list with reorder (up/down) + remove,
 * merge button, download. Premium options:
 *   - Merge Mode: Append / Interleave
 *   - Page Size: Keep original / A4 / Letter
 *   - Orientation: Keep / Portrait / Landscape
 *   - Optimize output size toggle
 *   - Blank page between files toggle
 * Requires pdf-lib + pdf.js loaded dynamically.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var files = [];
    var mergedBlob = null;
    var mergeMode = 'append';
    var pageSize = 'auto';
    var orientation = 'keep';
    var optimize = false;
    var separator = false;

    var drop = document.getElementById('tc-pm-drop');
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

    async function ensurePdfLib() {
        if (!window.PDFLib) {
            await loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js');
        }
    }

    async function ensurePdfJs() {
        if (!window.pdfjsLib) {
            await loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js');
            window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        }
    }

    function renderPageToImage(arrayBuffer, pageNum) {
        pageNum = pageNum || 1;
        return window.pdfjsLib.getDocument({ data: new Uint8Array(arrayBuffer) }).promise.then(function (pdf) {
            if (pageNum > pdf.numPages) pageNum = 1;
            return pdf.getPage(pageNum);
        }).then(function (page) {
            var vp = page.getViewport({ scale: 1.5 });
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

    /* ------------------------------------------------------------------ */
    /*  Hints                                                              */
    /* ------------------------------------------------------------------ */

    var MODE_HINTS = {
        append: 'Append \u2014 all pages of each file, one after another.',
        interleave: 'Interleave \u2014 alternate pages across your files.'
    };
    var SIZE_HINTS = {
        auto: 'Keep original \u2014 every page keeps its current dimensions.',
        a4: 'A4 \u2014 every page is sized to 210 \u00d7 297 mm.',
        letter: 'Letter \u2014 every page is sized to 8.5 \u00d7 11 inches.'
    };
    var ORIENT_HINTS = {
        keep: 'Keep \u2014 pages stay in their original orientation.',
        portrait: 'Portrait \u2014 taller than wide.',
        landscape: 'Landscape \u2014 wider than tall.'
    };

    function updateHints() {
        var mh = document.getElementById('tc-pm-mode-hint');
        if (mh && MODE_HINTS[mergeMode]) mh.textContent = MODE_HINTS[mergeMode];
        var sh = document.getElementById('tc-pm-size-hint');
        if (sh && SIZE_HINTS[pageSize]) sh.textContent = SIZE_HINTS[pageSize];
        var oh = document.getElementById('tc-pm-orient-hint');
        if (oh && ORIENT_HINTS[orientation]) oh.textContent = ORIENT_HINTS[orientation];
    }

    /* ------------------------------------------------------------------ */
    /*  File list                                                          */
    /* ------------------------------------------------------------------ */

    function renderFileList() {
        var list = document.getElementById('tc-pm-list');
        if (!list) return;
        list.innerHTML = '';

        var totalSize = 0;
        files.forEach(function (f, idx) {
            totalSize += f.size;
            var li = document.createElement('li');
            li.className = 'tc-pm-item';

            var numSpan = document.createElement('span');
            numSpan.className = 'tc-pm-num';
            numSpan.textContent = (idx + 1);

            var icon = document.createElement('span');
            icon.className = 'tc-pm-icon';
            icon.textContent = '\uD83D\uDCC4';

            var nameSpan = document.createElement('span');
            nameSpan.className = 'tc-pm-name';
            nameSpan.textContent = f.name;

            var sizeSpan = document.createElement('span');
            sizeSpan.className = 'tc-pm-size';
            sizeSpan.textContent = TCTP.formatSize(f.size);

            var upBtn = document.createElement('button');
            upBtn.className = 'tc-pm-up';
            upBtn.innerHTML = '\u25B2';
            upBtn.title = 'Move up';
            upBtn.disabled = idx === 0;
            upBtn.addEventListener('click', function () {
                if (idx > 0) {
                    var temp = files[idx];
                    files[idx] = files[idx - 1];
                    files[idx - 1] = temp;
                    renderFileList();
                }
            });

            var downBtn = document.createElement('button');
            downBtn.className = 'tc-pm-down';
            downBtn.innerHTML = '\u25BC';
            downBtn.title = 'Move down';
            downBtn.disabled = idx === files.length - 1;
            downBtn.addEventListener('click', function () {
                if (idx < files.length - 1) {
                    var temp = files[idx];
                    files[idx] = files[idx + 1];
                    files[idx + 1] = temp;
                    renderFileList();
                }
            });

            var removeBtn = document.createElement('button');
            removeBtn.className = 'tc-pm-remove tc-pm-btns-del';
            removeBtn.innerHTML = '\u2715';
            removeBtn.title = 'Remove';
            removeBtn.addEventListener('click', function () {
                files.splice(idx, 1);
                if (!files.length) mergedBlob = null;
                renderFileList();
            });

            var btnGroup = document.createElement('span');
            btnGroup.className = 'tc-pm-btns';
            btnGroup.appendChild(upBtn);
            btnGroup.appendChild(downBtn);
            btnGroup.appendChild(removeBtn);

            li.appendChild(numSpan);
            li.appendChild(icon);
            li.appendChild(nameSpan);
            li.appendChild(sizeSpan);
            li.appendChild(btnGroup);
            list.appendChild(li);
        });

        var countEl = document.getElementById('tc-pm-stat-count');
        var sizeEl = document.getElementById('tc-pm-stat-size');
        if (countEl) countEl.textContent = files.length;
        if (sizeEl) sizeEl.textContent = files.length ? TCTP.formatSize(totalSize) : '-';

        var mergedEl = document.getElementById('tc-pm-stat-merged');
        if (mergedEl) mergedEl.textContent = '-';

        var dlBtn = document.getElementById('tc-pm-download');
        if (dlBtn) dlBtn.style.display = 'none';

        if (!files.length) {
            list.style.display = 'none';
        } else {
            list.style.display = '';
        }
    }

    /* ------------------------------------------------------------------ */
    /*  Reset                                                              */
    /* ------------------------------------------------------------------ */

    function resetTool() {
        files = [];
        mergedBlob = null;
        renderFileList();
        TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Idle');
        var orig = document.getElementById('tc-preview-orig');
        var result = document.getElementById('tc-preview-result');
        if (orig) orig.innerHTML = '<span style="color:var(--muted);font-size:13px">Original preview will appear here</span>';
        if (result) result.innerHTML = '<span style="color:var(--muted);font-size:13px">Merged preview will appear here</span>';
        TCTP.switchToOriginalTab();
    }

    /* ------------------------------------------------------------------ */
    /*  Option wiring                                                      */
    /* ------------------------------------------------------------------ */

    document.querySelectorAll('.tc-modes[data-group="pm-mode"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            mergeMode = btn.getAttribute('data-val') || 'append';
            updateHints();
        });
    });

    document.querySelectorAll('.tc-modes[data-group="pm-size"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            pageSize = btn.getAttribute('data-val') || 'auto';
            updateHints();
        });
    });

    document.querySelectorAll('.tc-modes[data-group="pm-orient"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            orientation = btn.getAttribute('data-val') || 'keep';
            updateHints();
        });
    });

    var optimizeInput = document.getElementById('tc-pm-optimize');
    if (optimizeInput) optimizeInput.addEventListener('change', function () {
        optimize = optimizeInput.checked;
    });

    var separatorInput = document.getElementById('tc-pm-separator');
    if (separatorInput) separatorInput.addEventListener('change', function () {
        separator = separatorInput.checked;
    });

    var inputEl = document.getElementById('tc-pm-drop-input');
    if (inputEl) inputEl.multiple = true;

    function addFiles(fileList) {
        var added = [];
        for (var i = 0; i < fileList.length; i++) {
            var f = fileList[i];
            if (f.type !== 'application/pdf' && !/\.pdf$/i.test(f.name)) {
                TCTP.toast('Please select PDF files only.', '\u26A0\uFE0F');
                continue;
            }
            files.push(f);
            added.push(f);
        }
        if (!added.length) return;
        renderFileList();

        if (files.length === added.length) {
            Promise.all([ensurePdfLib(), ensurePdfJs()]).then(function () {
                return added[0].arrayBuffer();
            }).then(function (ab) {
                return renderPageToImage(ab, 1);
            }).then(function (dataUrl) {
                TCTP.showOriginalPreview(dataUrl);
                TCTP.switchToOriginalTab();
            }).catch(function () {});
        }
    }

    if (drop.addEventListener) {
        drop.addEventListener('dragover', function (e) { e.preventDefault(); drop.classList.add('hot'); });
        drop.addEventListener('dragleave', function () { drop.classList.remove('hot'); });
        drop.addEventListener('drop', function (e) {
            e.preventDefault();
            drop.classList.remove('hot');
            if (e.dataTransfer && e.dataTransfer.files) addFiles(e.dataTransfer.files);
        });
        drop.addEventListener('click', function () { if (inputEl) inputEl.click(); });
    }
    if (inputEl && inputEl.addEventListener) {
        inputEl.addEventListener('change', function () {
            addFiles(inputEl.files);
            inputEl.value = '';
        });
    }

    var clearBtn = document.getElementById('tc-pm-clear');
    if (clearBtn) clearBtn.addEventListener('click', function () {
        resetTool();
        TCTP.toast('Cleared.', '\uD83E\uDDF9');
    });

    updateHints();

    /* ------------------------------------------------------------------ */
    /*  Page normalization                                                 */
    /* ------------------------------------------------------------------ */

    function applyNorm(page) {
        var s = page.getSize();
        var w = s.width;
        var h = s.height;

        if (pageSize === 'a4') { w = 595.28; h = 841.89; }
        else if (pageSize === 'letter') { w = 612; h = 792; }

        if (orientation === 'portrait' && w > h) { var t = w; w = h; h = t; }
        if (orientation === 'landscape' && w < h) { var t2 = w; w = h; h = t2; }

        page.setSize(w, h);
        page.setRotation(window.PDFLib.degrees(0));
    }

    /* ------------------------------------------------------------------ */
    /*  Merge button                                                       */
    /* ------------------------------------------------------------------ */

    var mergeBtn = document.getElementById('tc-pm-merge');
    if (mergeBtn) mergeBtn.addEventListener('click', async function () {
        if (files.length < 2) {
            TCTP.toast('Please add at least 2 PDF files.', '\u26A0\uFE0F');
            return;
        }

        TCTP.showProgress('tc-pm-progress');
        TCTP.setProgress('tc-pm-progress', 10, 'Loading pdf-lib...');

        try {
            await ensurePdfLib();
            TCTP.setProgress('tc-pm-progress', 20, 'Reading PDFs...');

            var srcs = [];
            for (var fi = 0; fi < files.length; fi++) {
                var ab = await files[fi].arrayBuffer();
                srcs.push(await window.PDFLib.PDFDocument.load(new Uint8Array(ab.slice(0))));
            }

            var mergedPdf = await window.PDFLib.PDFDocument.create();
            var totalIn = 0;
            files.forEach(function (f) { totalIn += f.size; });

            if (mergeMode === 'interleave') {
                TCTP.setProgress('tc-pm-progress', 40, 'Interleaving pages...');
                var maxPages = 0;
                srcs.forEach(function (s) { if (s.getPageCount() > maxPages) maxPages = s.getPageCount(); });
                var added = 0;
                for (var p = 0; p < maxPages; p++) {
                    for (var si = 0; si < srcs.length; si++) {
                        if (p < srcs[si].getPageCount()) {
                            var one = await mergedPdf.copyPages(srcs[si], [p]);
                            applyNorm(one[0]);
                            mergedPdf.addPage(one[0]);
                            added++;
                            TCTP.setProgress('tc-pm-progress', 40 + Math.round((added / (maxPages * srcs.length)) * 40), 'Interleaving...');
                        }
                    }
                }
            } else {
                for (var i = 0; i < srcs.length; i++) {
                    TCTP.setProgress('tc-pm-progress', 40 + Math.round((i / srcs.length) * 40), 'Merging file ' + (i + 1) + '/' + srcs.length + '...');
                    var pages = await mergedPdf.copyPages(srcs[i], srcs[i].getPageIndices());
                    pages.forEach(function (pg) { applyNorm(pg); mergedPdf.addPage(pg); });
                    if (separator && i < srcs.length - 1) {
                        mergedPdf.addPage([595.28, 841.89]);
                    }
                }
            }

            TCTP.setProgress('tc-pm-progress', 85, 'Saving...');
            var saveOpts = optimize
                ? { useObjectStreams: true, updateMetadata: false }
                : { useObjectStreams: false, updateMetadata: true };
            var bytes = await mergedPdf.save(saveOpts);
            mergedBlob = new Blob([bytes], { type: 'application/pdf' });

            TCTP.setProgress('tc-pm-progress', 100, 'Done!');
            TCTP.hideProgress('tc-pm-progress');

            var mergedSize = mergedBlob.size;
            var mergedEl = document.getElementById('tc-pm-stat-merged');
            if (mergedEl) mergedEl.textContent = TCTP.formatSize(mergedSize);

            var dlBtn = document.getElementById('tc-pm-download');
            if (dlBtn) dlBtn.style.display = '';

            var saved = totalIn > mergedSize ? ((1 - mergedSize / totalIn) * 100).toFixed(1) : '0';
            TCTP.updateResultPanel(TCTP.formatSize(totalIn), TCTP.formatSize(mergedSize), saved + '%', 'Done');
            TCTP.toast('Merged ' + files.length + ' PDFs!');
            TCTP.switchToResultTab();

            try {
                var mergedAb = await mergedBlob.arrayBuffer();
                var compDataUrl = await renderPageToImage(mergedAb, 1);
                TCTP.showResultPreview(compDataUrl);
            } catch (_) {}
        } catch (err) {
            TCTP.toast('Merge failed: ' + err.message, '\u274C');
            TCTP.hideProgress('tc-pm-progress');
        }
    });

    var downloadBtn = document.getElementById('tc-pm-download');
    if (downloadBtn) downloadBtn.addEventListener('click', function () {
        if (!mergedBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
        TCTP.downloadBlob(mergedBlob, 'merged.pdf');
    });

})();
