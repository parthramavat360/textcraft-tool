/**
 * ASCII Art Generator — Tool JS
 *
 * Premium: original preview, ASCII preview in result tab, stats row,
 * invert toggle, download .txt, density/format/width sliders.
 *
 * @package TextCraft_Tools_Pro
 */
(function () {
    'use strict';

    var drop = document.getElementById('tc-ascii-drop');
    if (!drop) return;

    var file = null;
    var generatedAscii = '';
    var density = 'medium';
    var format = 'blocks';

    function setStat(id, val) { var el = document.getElementById(id); if (el) el.textContent = val; }

    /* ── Character sets ────────────────────────────────────── */
    var CHARS_BLOCKS  = '\u2588\u2593\u2592\u2591 ';
    var CHARS_CHARS   = '$@B%8&WM#*oahkbdpqwmZO0QLCJUYXzcvunxrjft/\\|()1{}[]?-_+~<>i!lI;:,"^`\'. ';
    var CHARS_SYMBOLS = '$@#%*+=-:.!~ ';

    function charSetFor(fmt) {
        if (fmt === 'blocks') return CHARS_BLOCKS;
        if (fmt === 'symbols') return CHARS_SYMBOLS;
        return CHARS_CHARS;
    }

    /* ── Density buttons ───────────────────────────────────── */
    document.querySelectorAll('.tc-modes[data-group="ascii-density"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            density = btn.getAttribute('data-val') || 'medium';
        });
    });

    /* ── Format buttons ────────────────────────────────────── */
    document.querySelectorAll('.tc-modes[data-group="ascii-format"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            format = btn.getAttribute('data-val') || 'blocks';
        });
    });

    /* ── Width slider live badge ───────────────────────────── */
    var widthSlider = document.getElementById('tc-ascii-width');
    var widthVal = document.getElementById('tc-ascii-width-val');
    if (widthSlider && widthVal) {
        widthSlider.addEventListener('input', function () {
            widthVal.textContent = widthSlider.value;
        });
    }

    /* ── Drop zone ─────────────────────────────────────────── */
    TCTP.initDropZone('tc-ascii-drop', 'tc-ascii-drop-input', function (f) {
        if (!f.type.match(/image\//)) { TCTP.toast('Please select an image file.', '\u26A0\uFE0F'); return; }
        file = f;
        generatedAscii = '';
        TCTP.showFileRow('tc-ascii-file', f);
        var dl = document.getElementById('tc-ascii-download');
        if (dl) dl.style.display = 'none';
        setStat('tc-ascii-stat-orig', TCTP.formatSize(f.size));
        setStat('tc-ascii-stat-w', '-');
        setStat('tc-ascii-stat-chars', '-');
        setStat('tc-ascii-stat-lines', '-');
        var reader = new FileReader();
        reader.onload = function (ev) {
            TCTP.showOriginalPreview(ev.target.result);
            TCTP.switchToOriginalTab();
        };
        reader.readAsDataURL(f);
    }, 'image/*');

    /* ── Remove file ───────────────────────────────────────── */
    var removeBtn = document.querySelector('#tc-ascii-file .tc-x');
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            file = null;
            generatedAscii = '';
            TCTP.hideFileRow('tc-ascii-file');
            setStat('tc-ascii-stat-orig', '-');
            setStat('tc-ascii-stat-w', '-');
            setStat('tc-ascii-stat-chars', '-');
            setStat('tc-ascii-stat-lines', '-');
            var previewEl = document.getElementById('tc-preview-result');
            if (previewEl) previewEl.classList.remove('is-mono');
        });
    }

    /* ── Generate button ───────────────────────────────────── */
    var generateBtn = document.getElementById('tc-ascii-generate');
    if (generateBtn) {
        generateBtn.addEventListener('click', function () {
            if (!file) { TCTP.toast('Please drop an image first', '\u26A0\uFE0F'); return; }
            doConvert();
        });
    }

    /* ═══════════════════════════════════════════════════════════
       ASCII CONVERSION
       ═══════════════════════════════════════════════════════════ */

    function doConvert() {
        var maxW = widthSlider ? parseInt(widthSlider.value, 10) : 120;
        var charSet = charSetFor(format);
        var invertCheck = document.getElementById('tc-ascii-invert');
        var invert = invertCheck ? invertCheck.checked : false;

        TCTP.showProgress('tc-ascii-progress');
        TCTP.setProgress('tc-ascii-progress', 10, 'Loading image...');
        generateBtn.disabled = true;

        var img = new Image();
        img.onload = function () {
            TCTP.setProgress('tc-ascii-progress', 30, 'Rendering to ASCII...');

            var scale = maxW / Math.max(img.naturalWidth, img.naturalHeight);
            var w = Math.max(1, Math.round(img.naturalWidth * scale));
            var h = Math.max(1, Math.round(img.naturalHeight * scale * 0.55));

            var c = document.createElement('canvas');
            c.width = w;
            c.height = h;
            var ctx = c.getContext('2d');
            ctx.drawImage(img, 0, 0, w, h);

            TCTP.setProgress('tc-ascii-progress', 50, 'Converting pixels...');
            var data = ctx.getImageData(0, 0, w, h).data;

            var lines = [];
            var charLen = charSet.length - 1;
            for (var y = 0; y < h; y++) {
                var row = '';
                for (var x = 0; x < w; x++) {
                    var i = (y * w + x) * 4;
                    var gray = 0.299 * data[i] + 0.587 * data[i + 1] + 0.114 * data[i + 2];
                    if (invert) gray = 255 - gray;
                    var idx = Math.round((gray / 255) * charLen);
                    row += charSet[idx];
                }
                lines.push(row);
            }

            TCTP.setProgress('tc-ascii-progress', 90, 'Formatting...');
            generatedAscii = lines.join('\n');

            var totalChars = generatedAscii.length;

            setStat('tc-ascii-stat-w', w + '\u00D7' + h);
            setStat('tc-ascii-stat-chars', totalChars.toLocaleString());
            setStat('tc-ascii-stat-lines', lines.length.toLocaleString());

            TCTP.updateResultPanel(
                TCTP.formatSize(file.size),
                totalChars.toLocaleString() + ' chars',
                lines.length + ' lines',
                'Done'
            );

            /* Show ASCII in result preview */
            var previewEl = document.getElementById('tc-preview-result');
            if (previewEl) {
                previewEl.classList.add('is-mono');
                previewEl.textContent = generatedAscii;
            }
            TCTP.switchToResultTab();

            TCTP.setProgress('tc-ascii-progress', 100, 'Done!');
            TCTP.hideProgress('tc-ascii-progress');
            generateBtn.disabled = false;

            TCTP.toast('ASCII art generated! ' + w + '\u00D7' + h);
            var dl = document.getElementById('tc-ascii-download');
            if (dl) dl.style.display = '';
        };

        img.onerror = function () {
            TCTP.hideProgress('tc-ascii-progress');
            generateBtn.disabled = false;
            TCTP.toast('Failed to load image', '\u274C');
        };
        img.src = URL.createObjectURL(file);
    }

    /* ── Download ──────────────────────────────────────────── */
    var downloadBtn = document.getElementById('tc-ascii-download');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function () {
            if (!generatedAscii) { TCTP.toast('No ASCII art to download yet.', '\u26A0\uFE0F'); return; }
            var name = (file ? file.name.replace(/\.[^.]+$/, '') : 'image') + '-ascii.txt';
            var blob = new Blob([generatedAscii], { type: 'text/plain;charset=utf-8' });
            TCTP.downloadBlob(blob, name);
        });
    }

    /* ── Copy ──────────────────────────────────────────────── */
    var copyBtn = document.getElementById('tc-ascii-copy');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            TCTP.copyText(generatedAscii, 'ASCII art');
        });
    }
})();
