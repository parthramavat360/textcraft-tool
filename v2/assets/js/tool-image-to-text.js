/**
 * Image to Text (OCR) — Tool JS
 *
 * Strongest OCR: canvas preprocessing (grayscale, contrast, sharpen),
 * smart text cleanup (fix broken words, join sentences, clean whitespace),
 * multi-pass OCR for low-confidence results.
 *
 * @package TextCraft_Tools_Pro
 */
(function () {
    'use strict';

    var drop = document.getElementById('tc-ocr-drop');
    if (!drop) return;

    var file = null;
    var extractedText = '';
    var extractedHocr = '';
    var rawText = '';
    var lang = 'eng';
    var outputMode = 'text';
    var loaded = false;

    function setStat(id, val) { var el = document.getElementById(id); if (el) el.textContent = val; }

    /* ── Language buttons ──────────────────────────────────── */
    document.querySelectorAll('.tc-modes[data-group="ocr-lang"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            lang = btn.getAttribute('data-val') || 'eng';
        });
    });

    /* ── Output mode buttons ───────────────────────────────── */
    document.querySelectorAll('.tc-modes[data-group="ocr-output"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            outputMode = btn.getAttribute('data-val') || 'text';
        });
    });

    /* ── Load tesseract.js ─────────────────────────────────── */
    function loadLib(cb) {
        if (loaded) { cb(); return; }
        var s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js';
        s.onload = function () { loaded = true; cb(); };
        s.onerror = function () { TCTP.toast('Failed to load tesseract.js', '\u274C'); };
        document.head.appendChild(s);
    }

    /* ═══════════════════════════════════════════════════════════
       IMAGE PREPROCESSING — canvas-based for best OCR accuracy
       ═══════════════════════════════════════════════════════════ */

    function preprocessImage(blob) {
        return new Promise(function (resolve) {
            var img = new Image();
            var url = URL.createObjectURL(blob);
            img.onload = function () {
                URL.revokeObjectURL(url);
                var w = img.naturalWidth;
                var h = img.naturalHeight;

                /* Upscale small images (OCR needs ~300 DPI equivalent) */
                var scale = 1;
                if (w < 1000 && h < 1000) {
                    scale = Math.min(2.5, 2000 / Math.max(w, h));
                }

                var cw = Math.round(w * scale);
                var ch = Math.round(h * scale);

                var canvas = document.createElement('canvas');
                canvas.width = cw;
                canvas.height = ch;
                var ctx = canvas.getContext('2d');

                /* Draw with interpolation */
                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';
                ctx.drawImage(img, 0, 0, cw, ch);

                var imageData = ctx.getImageData(0, 0, cw, ch);
                var data = imageData.data;

                /* Step 1: Grayscale (weighted luminance) */
                var len = data.length;
                for (var i = 0; i < len; i += 4) {
                    var gray = Math.round(0.299 * data[i] + 0.587 * data[i + 1] + 0.114 * data[i + 2]);
                    data[i] = gray;
                    data[i + 1] = gray;
                    data[i + 2] = gray;
                }

                /* Step 2: Histogram stretch (contrast enhancement) */
                var min = 255, max = 0;
                for (var i = 0; i < len; i += 4) {
                    if (data[i] < min) min = data[i];
                    if (data[i] > max) max = data[i];
                }
                var range = max - min;
                if (range > 20) {
                    var invRange = 255 / range;
                    for (var i = 0; i < len; i += 4) {
                        var v = Math.round((data[i] - min) * invRange);
                        data[i] = v;
                        data[i + 1] = v;
                        data[i + 2] = v;
                    }
                }

                /* Step 3: Unsharp mask (light sharpening) */
                /* Apply a simple 3x3 box blur then subtract */
                var copy = new Uint8ClampedArray(data);
                var bw = cw;
                for (var y = 1; y < ch - 1; y++) {
                    for (var x = 1; x < bw - 1; x++) {
                        var idx = (y * bw + x) * 4;
                        var sum = 0;
                        for (var dy = -1; dy <= 1; dy++) {
                            for (var dx = -1; dx <= 1; dx++) {
                                sum += copy[((y + dy) * bw + (x + dx)) * 4];
                            }
                        }
                        var blur = sum / 9;
                        var sharp = Math.round(1.5 * copy[idx] - 0.5 * blur);
                        if (sharp < 0) sharp = 0;
                        if (sharp > 255) sharp = 255;
                        data[idx] = sharp;
                        data[idx + 1] = sharp;
                        data[idx + 2] = sharp;
                    }
                }

                ctx.putImageData(imageData, 0, 0);
                canvas.toBlob(function (processedBlob) {
                    resolve(processedBlob || blob);
                }, 'image/png');
            };
            img.onerror = function () {
                URL.revokeObjectURL(url);
                resolve(blob);
            };
            img.src = url;
        });
    }

    /* ═══════════════════════════════════════════════════════════
       SMART TEXT CLEANUP
       ═══════════════════════════════════════════════════════════ */

    function cleanOcrText(text) {
        if (!text) return '';

        var t = text;

        /* Remove null bytes and other control chars (keep tabs, newlines, CR) */
        t = t.replace(/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/g, '');

        /* Normalize line endings */
        t = t.replace(/\r\n/g, '\n').replace(/\r/g, '\n');

        /* Fix hyphenated line breaks: "excel-\nlent" → "excellent" */
        t = t.replace(/(\w)-\n(\w)/g, function (m, a, b) {
            if (a === a.toLowerCase() && b === b.toLowerCase()) {
                return a + b;
            }
            return a + '-' + b;
        });

        /* Remove excessive blank lines (keep max 2) */
        t = t.replace(/\n{3,}/g, '\n\n');

        /* Collapse multiple spaces into one (preserve leading whitespace) */
        t = t.replace(/[^\S\n]+/g, ' ');

        /* Trim trailing spaces on each line */
        t = t.replace(/[ \t]+$/gm, '');

        /* Fix spaces before/after punctuation */
        t = t.replace(/\s+([.,;:!?])/g, '$1');
        t = t.replace(/\(\s+/g, '(');
        t = t.replace(/\s+\)/g, ')');

        /* Fix double periods */
        t = t.replace(/\.\.\.\.+/g, '...');

        /* Trim overall */
        t = t.replace(/^\n+|\n+$/g, '');

        return t;
    }

    /* ═══════════════════════════════════════════════════════════
       DROP ZONE
       ═══════════════════════════════════════════════════════════ */

    TCTP.initDropZone('tc-ocr-drop', 'tc-ocr-drop-input', function (f) {
        if (!f.type.match(/image\//)) { TCTP.toast('Please select an image file.', '\u26A0\uFE0F'); return; }
        file = f;
        extractedText = '';
        extractedHocr = '';
        rawText = '';
        TCTP.showFileRow('tc-ocr-file', f);
        var dl = document.getElementById('tc-ocr-download');
        if (dl) dl.style.display = 'none';
        setStat('tc-ocr-stat-orig', TCTP.formatSize(f.size));
        setStat('tc-ocr-stat-words', '-');
        setStat('tc-ocr-stat-lines', '-');
        setStat('tc-ocr-stat-conf', '-');
        var reader = new FileReader();
        reader.onload = function (ev) {
            TCTP.showOriginalPreview(ev.target.result);
            TCTP.switchToOriginalTab();
        };
        reader.readAsDataURL(f);
    }, 'image/*');

    /* ── Remove file button ────────────────────────────────── */
    var removeBtn = document.querySelector('#tc-ocr-file .tc-x');
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            file = null;
            extractedText = '';
            extractedHocr = '';
            rawText = '';
            TCTP.hideFileRow('tc-ocr-file');
            setStat('tc-ocr-stat-orig', '-');
            setStat('tc-ocr-stat-words', '-');
            setStat('tc-ocr-stat-lines', '-');
            setStat('tc-ocr-stat-conf', '-');
        });
    }

    /* ── Extract button ────────────────────────────────────── */
    var extractBtn = document.getElementById('tc-ocr-extract');
    if (extractBtn) {
        extractBtn.addEventListener('click', function () {
            if (!file) { TCTP.toast('Please drop an image first', '\u26A0\uFE0F'); return; }
            loadLib(doOCR);
        });
    }

    /* ═══════════════════════════════════════════════════════════
       OCR ENGINE — with preprocessing & smart cleanup
       ═══════════════════════════════════════════════════════════ */

    function doOCR() {
        var preprocessCheck = document.getElementById('tc-ocr-preprocess');
        var cleanupCheck = document.getElementById('tc-ocr-cleanup');
        var doPreprocess = preprocessCheck ? preprocessCheck.checked : true;
        var doCleanup = cleanupCheck ? cleanupCheck.checked : true;

        TCTP.showProgress('tc-ocr-progress');
        TCTP.setProgress('tc-ocr-progress', 3, 'Preparing image...');
        extractBtn.disabled = true;

        var processBlob = doPreprocess ? preprocessImage(file) : Promise.resolve(file);

        processBlob.then(function (processedBlob) {
            TCTP.setProgress('tc-ocr-progress', 8, 'Starting OCR engine...');

            var url = URL.createObjectURL(processedBlob);
            return Tesseract.recognize(url, lang, {
                logger: function (m) {
                    if (m.status === 'loading language traineddata') {
                        TCTP.setProgress('tc-ocr-progress', 10 + Math.round(m.progress * 25), 'Downloading language model...');
                    } else if (m.status === 'recognizing text') {
                        TCTP.setProgress('tc-ocr-progress', 35 + Math.round(m.progress * 55), 'Recognizing text...');
                    } else if (m.status === 'initializing api') {
                        TCTP.setProgress('tc-ocr-progress', 8, 'Initializing API...');
                    } else if (m.status === 'loading language traineddata (new)') {
                        TCTP.setProgress('tc-ocr-progress', 10 + Math.round(m.progress * 25), 'Loading OCR data...');
                    }
                }
            }).then(function (result) {
                URL.revokeObjectURL(url);

                rawText = result.data.text || '';
                var confidence = Math.round(result.data.confidence || 0);

                /* Multi-pass: if confidence < 60% and preprocessing was off, try again with preprocessing */
                if (confidence < 60 && !doPreprocess) {
                    TCTP.setProgress('tc-ocr-progress', 10, 'Low confidence, retrying with preprocessing...');
                    return preprocessImage(file).then(function (enhancedBlob) {
                        var url2 = URL.createObjectURL(enhancedBlob);
                        return Tesseract.recognize(url2, lang, {
                            logger: function (m) {
                                if (m.status === 'recognizing text') {
                                    TCTP.setProgress('tc-ocr-progress', 35 + Math.round(m.progress * 55), 'Re-recognizing with enhanced image...');
                                }
                            }
                        }).then(function (result2) {
                            URL.revokeObjectURL(url2);
                            if ((result2.data.confidence || 0) > confidence) {
                                rawText = result2.data.text || '';
                                confidence = Math.round(result2.data.confidence || 0);
                            }
                            return { text: rawText, confidence: confidence, data: result2.data };
                        });
                    });
                }

                return { text: rawText, confidence: confidence, data: result.data };
            }).then(function (final) {
                TCTP.setProgress('tc-ocr-progress', 95, 'Formatting text...');

                rawText = final.text;
                extractedHocr = final.data.hocr || '';

                /* Smart text cleanup */
                var cleaned = doCleanup ? cleanOcrText(rawText) : rawText;
                extractedText = cleaned;

                var words = cleaned.trim() ? cleaned.trim().split(/\s+/).length : 0;
                var lines = cleaned.trim() ? cleaned.trim().split(/\n/).length : 0;
                var chars = cleaned.length;
                var conf = final.confidence;

                setStat('tc-ocr-stat-words', words.toLocaleString());
                setStat('tc-ocr-stat-lines', lines.toLocaleString());
                setStat('tc-ocr-stat-conf', conf + '%');

                /* Update result panel */
                TCTP.updateResultPanel(
                    TCTP.formatSize(file.size),
                    words.toLocaleString() + ' words',
                    conf + '% conf.',
                    'Done'
                );

                var displayText = outputMode === 'hocr' ? extractedHocr : extractedText;
                TCTP.showResultText(displayText);
                TCTP.switchToResultTab();

                TCTP.setProgress('tc-ocr-progress', 100, 'Done!');
                TCTP.hideProgress('tc-ocr-progress');
                extractBtn.disabled = false;

                TCTP.toast('Extracted ' + words + ' words (' + conf + '% confidence)');
                var dl = document.getElementById('tc-ocr-download');
                if (dl) dl.style.display = '';
            }).catch(function (err) {
                TCTP.hideProgress('tc-ocr-progress');
                extractBtn.disabled = false;
                TCTP.toast('OCR failed: ' + err.message, '\u274C');
            });
        }).catch(function (err) {
            TCTP.hideProgress('tc-ocr-progress');
            extractBtn.disabled = false;
            TCTP.toast('Preprocessing failed: ' + err.message, '\u274C');
        });
    }

    /* ── Download button ───────────────────────────────────── */
    var downloadBtn = document.getElementById('tc-ocr-download');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function () {
            var text = outputMode === 'hocr' ? extractedHocr : extractedText;
            if (!text) { TCTP.toast('No text to download yet.', '\u26A0\uFE0F'); return; }
            var ext = outputMode === 'hocr' ? '.hocr' : '.txt';
            var nameInput = document.getElementById('tc-ocr-name');
            var base = (nameInput && nameInput.value.trim()) ? nameInput.value.trim().replace(/\.[^.]+$/, '') : (file ? file.name.replace(/\.[^.]+$/, '') : 'image');
            var name = base + '-ocr' + ext;
            var blob = new Blob([text], { type: outputMode === 'hocr' ? 'text/html' : 'text/plain;charset=utf-8' });
            TCTP.downloadBlob(blob, name);
        });
    }

    /* ── Copy button (if present) ──────────────────────────── */
    var copyBtn = document.getElementById('tc-ocr-copy');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            TCTP.copyText(extractedText, 'Extracted text');
        });
    }

    /* ── Clear all ─────────────────────────────────────────── */
    var clearBtn = document.getElementById('tc-ocr-clear');
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            file = null;
            extractedText = '';
            extractedHocr = '';
            rawText = '';
            TCTP.hideFileRow('tc-ocr-file');
            setStat('tc-ocr-stat-orig', '-');
            setStat('tc-ocr-stat-words', '-');
            setStat('tc-ocr-stat-lines', '-');
            setStat('tc-ocr-stat-conf', '-');
            var dlBtn = document.getElementById('tc-ocr-download');
            if (dlBtn) dlBtn.style.display = 'none';
            var nameInput = document.getElementById('tc-ocr-name');
            if (nameInput) nameInput.value = '';
            var origP = document.getElementById('tc-preview-orig');
            if (origP) origP.innerHTML = '<span style="color:var(--muted);font-size:13px">Original preview will appear here</span>';
            var resP = document.getElementById('tc-preview-result');
            if (resP) resP.innerHTML = '<span style="color:var(--muted);font-size:13px">Extracted text will appear here</span>';
            TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Ready');
            TCTP.switchToOriginalTab();
        });
    }
})();
