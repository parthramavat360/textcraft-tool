/**
 * Passport Photo Maker — Tool JS
 * 100% client-side passport/visa/ID photo creation using the canvas API.
 * Center-crops a headshot to the chosen preset, applies a background colour,
 * and exports a 6×4 inch (300 DPI) print sheet.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var drop = document.getElementById('tc-ppt-drop');
    if (!drop) return;

    // ── Presets (target pixels at 300 DPI) ─────────────────────
    var PRESETS = [
        { name: '2 × 2 in', note: 'US Passport · 51 mm', w: 600, h: 600, title: 'US Passport' },
        { name: '35 × 45 mm', note: 'Visa · Schengen', w: 413, h: 531, title: '35 × 45 mm' },
        { name: '3.5 × 4.5 cm', note: 'ID · biometric', w: 413, h: 531, title: '3.5 × 4.5 cm' },
        { name: '51 × 51 mm', note: 'US visa', w: 602, h: 602, title: '51 × 51 mm' }
    ];
    var SHEET_W = 1800;   // 6 in @ 300 DPI
    var SHEET_H = 1200;   // 4 in @ 300 DPI
    var SHEET_D = 50;     // outer margin (px)
    var SHEET_GAP = 60;   // between photos
    var SHEET_TICK = 24;  // trim tick length

    var source = null;        // HTMLImageElement
    var sourceW = 0, sourceH = 0;
    var presetIdx = 0;
    var zoom = 1.6;
    var face = 0.38;          // 0..1 top-biased vertical anchor
    var bgColor = '#ffffff';
    var outFmt = 'image/jpeg';
    var outExt = 'jpg';
    var madeBlob = null;      // single cropped photo blob
    var sheetBlob = null;
    var dragging = false;
    var dragStart = { x: 0, y: 0, face: 0.38 };

    var imgEl = document.getElementById('tc-ppt-crop-img');
    var frameEl = document.getElementById('tc-ppt-display-frame');
    var dimEl = document.getElementById('tc-ppt-dim');
    var stageEl = document.getElementById('tc-ppt-crop-stage');

    // ── Drop zone ──────────────────────────────────────────────

    TCTP.initDropZone('tc-ppt-drop', 'tc-ppt-drop-input', function (f) {
        if (!f.type.match(/^image\/(jpeg|png|webp)$/)) {
            TCTP.toast('Please select a JPG, PNG, or WebP photo.', '\u26A0\uFE0F');
            return;
        }
        var reader = new FileReader();
        reader.onload = function (e) {
            var img = new Image();
            img.onload = function () {
                source = img;
                sourceW = img.naturalWidth;
                sourceH = img.naturalHeight;
                if (imgEl) {
                    imgEl.src = e.target.result;
                    imgEl.hidden = false;
                }
                if (frameEl) frameEl.style.display = 'block';
                resetCrop();
                TCTP.showFileRow('tc-ppt-file', f);
                var dl = document.getElementById('tc-ppt-download');
                if (dl) dl.disabled = false;
                TCTP.toast('Photo loaded! Adjust crop and hit the button.');
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(f);
    }, 'image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp');

    var removeBtn = document.querySelector('#tc-ppt-file .tc-x');
    if (removeBtn) removeBtn.addEventListener('click', resetAll);

    // ── Crop stage drag interaction ────────────────────────────

    var stageEl = document.getElementById('tc-ppt-crop-stage');
    if (stageEl) {
        stageEl.addEventListener('mousedown', function (ev) {
            if (!source) return;
            dragging = true;
            dragStart.x = ev.clientX;
            dragStart.y = ev.clientY;
            dragStart.face = face;
            ev.preventDefault();
        });
        stageEl.addEventListener('touchstart', function (ev) {
            if (!source) return;
            dragging = true;
            var t = ev.touches[0];
            dragStart.x = t.clientX;
            dragStart.y = t.clientY;
            dragStart.face = face;
            ev.preventDefault();
        }, { passive: false });

        function move(ev) {
            if (!dragging || !source) return;
            var px = ev.clientX || (ev.touches && ev.touches[0].clientX);
            var py = ev.clientY || (ev.touches && ev.touches[0].clientY);
            var rect = stageEl.getBoundingClientRect();
            var dy = (py - dragStart.y) / rect.height;
            face = clamp01(dragStart.face + dy);
            var faceSlider = document.getElementById('tc-ppt-face');
            if (faceSlider) faceSlider.value = Math.round(face * 100);
            drawCrop();
            if (ev.cancelable) ev.preventDefault();
        }
        function up() { dragging = false; }

        stageEl.addEventListener('mousemove', move);
        stageEl.addEventListener('touchmove', move, { passive: false });
        stageEl.addEventListener('mouseup', up);
        stageEl.addEventListener('mouseleave', up);
        stageEl.addEventListener('touchend', up);
    }

    function clamp01(v) { return Math.max(0, Math.min(1, v)); }

    // ── Sliders ────────────────────────────────────────────────

    var zoomInput = document.getElementById('tc-ppt-zoom');
    var faceInput = document.getElementById('tc-ppt-face');
    if (zoomInput) zoomInput.addEventListener('input', function () {
        zoom = (parseInt(zoomInput.value) || 100) / 100;
        drawCrop();
    });
    if (faceInput) faceInput.addEventListener('input', function () {
        face = (parseInt(faceInput.value) || 0) / 100;
        drawCrop();
    });

    // ── Presets ────────────────────────────────────────────────

    var presetBtns = document.querySelectorAll('#tc-ppt-preset-tabs .tc-ppt-preset');
    presetBtns.forEach(function (btn, i) {
        btn.addEventListener('click', function () {
            presetBtns.forEach(function (b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            presetIdx = i;
            resizeFrameToStage();
            updateStats();
        });
    });

    // ── Background ─────────────────────────────────────────────

    var colorInput = document.getElementById('tc-ppt-color');
    var bgBtns = document.querySelectorAll('.tc-ppt-bg');
    bgBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            bgBtns.forEach(function (b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            if (btn.classList.contains('tc-ppt-bg-custom')) {
                if (colorInput) colorInput.click();
                return;
            }
            bgColor = btn.getAttribute('data-color');
            if (colorInput) colorInput.value = bgColor;
            drawCrop();
            updateStats();
        });
    });
    if (colorInput) colorInput.addEventListener('input', function () {
        bgColor = colorInput.value;
        drawCrop();
        updateStats();
    });

    // ── Output format ──────────────────────────────────────────

    var fmtBtns = document.querySelectorAll('.tc-ppt-fmt');
    fmtBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            fmtBtns.forEach(function (b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            outFmt = btn.getAttribute('data-fmt');
            outExt = btn.getAttribute('data-ext');
        });
    });

    // ── Crop rendering (stage + live guide) ────────────────────

    function resetCrop() {
        zoom = 1.6;
        face = 0.38;
        if (zoomInput) zoomInput.value = 160;
        if (faceInput) faceInput.value = 38;
        madeBlob = null;
        sheetBlob = null;
        updateStats();
        resizeFrameToStage();
    }

    function resetAll() {
        source = null;
        sourceW = sourceH = 0;
        madeBlob = sheetBlob = null;
        var dl = document.getElementById('tc-ppt-download');
        var made = document.getElementById('tc-ppt-make');
        if (dl) dl.disabled = true;
        if (made) made.disabled = false;
        TCTP.hideFileRow('tc-ppt-file');
        if (imgEl) { imgEl.hidden = true; imgEl.removeAttribute('src'); }
        if (frameEl) frameEl.style.display = 'none';
        var prev = document.getElementById('tc-ppt-result-preview');
        if (prev) prev.textContent = 'Your passport photo will appear here.';
    }

    // Compute source rectangle for the crop given the current view.
    // Returns the source-pixel rectangle that maps to the full output frame.
    function srcRect() {
        var pw = PRESETS[presetIdx].w;
        var ph = PRESETS[presetIdx].h;
        var asp = pw / ph;

        // Cover: scale so image covers the target aspect.
        var sw, sh;
        if (sourceW / sourceH > asp) {
            sw = sourceH * asp;
            sh = sourceH;
        } else {
            sw = sourceW;
            sh = sourceW / asp;
        }

        // Apply zoom (crop tighter).
        sw = sw / zoom;
        sh = sh / zoom;

        // Horizontal: center.
        var sx = (sourceW - sw) / 2;

        // Vertical: head-biased. face maps 0..1 from top-aligned to bottom.
        var avail = Math.max(0, sourceH - sh);
        var anchor = face * (sourceH / (sourceH - sh || 1));
        anchor = Math.max(0, Math.min(1, anchor));
        var sy = avail * anchor;

        return { sx: sx, sy: sy, sw: sw, sh: sh };
    }

    function drawCrop() {
        if (!source) return;
        var r = srcRect();
        var pw = PRESETS[presetIdx].w;
        var ph = PRESETS[presetIdx].h;
        if (!frameEl || !imgEl) return;

        // Frame reflects the target aspect ratio.
        var frameW = frameEl.clientWidth;
        var frameH = frameW * (ph / pw);

        // Scale factor: frame pixels per source pixel.
        var scale = frameW / r.sw;

        imgEl.style.position = 'absolute';
        imgEl.style.width = (sourceW * scale) + 'px';
        imgEl.style.height = (sourceH * scale) + 'px';
        imgEl.style.left = (-r.sx * scale) + 'px';
        imgEl.style.top = (-r.sy * scale) + 'px';
        imgEl.style.maxWidth = 'none';

        if (dimEl) dimEl.textContent = pw + ' × ' + ph + ' px';
    }

    function resizeFrameToStage() {
        if (!source || !frameEl) return;
        var stageW = stageEl ? stageEl.clientWidth : 300;
        var pw = PRESETS[presetIdx].w;
        var ph = PRESETS[presetIdx].h;
        frameEl.style.width = stageW + 'px';
        frameEl.style.aspectRatio = (pw / ph).toFixed(4);
        // height set by CSS aspect-ratio; recompute via clientWidth
        drawCrop();
    }

    // ── Processing ─────────────────────────────────────────────

    function renderPhotoCanvas() {
        var r = srcRect();
        var pw = PRESETS[presetIdx].w;
        var ph = PRESETS[presetIdx].h;
        var canvas = document.createElement('canvas');
        canvas.width = pw;
        canvas.height = ph;
        var ctx = canvas.getContext('2d');

        ctx.fillStyle = bgColor;
        ctx.fillRect(0, 0, pw, ph);
        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = 'high';
        ctx.drawImage(source, r.sx, r.sy, r.sw, r.sh, 0, 0, pw, ph);
        return canvas;
    }

    function renderSheetFrom(photoCanvas) {
        var pw = photoCanvas.width;
        var ph = photoCanvas.height;

        // Margins for each cell (photo + breathing room) in px.
        var cellPad = 90;
        var innerW = SHEET_W - SHEET_D * 2;
        var innerH = SHEET_H - SHEET_D * 2;

        // Choose the grid (cols x rows) that fits the most photos while
        // keeping each photo as large as possible.
        var best = null;
        for (var c = 1; c <= 8; c++) {
            for (var r = 1; r <= 8; r++) {
                var boxW = (innerW - (c - 1) * SHEET_GAP) / c;
                var boxH = (innerH - (r - 1) * SHEET_GAP) / r;
                var wScale = (boxW - cellPad) / pw;
                var hScale = (boxH - cellPad) / ph;
                var s = Math.max(0.15, Math.min(wScale, hScale));
                var photoW = pw * s;
                var photoH = ph * s;
                var count = c * r;
                if (!best || count > best.count ||
                    (count === best.count && photoW * photoH > best.photoW * best.photoH)) {
                    best = {
                        cols: c, rows: r,
                        cellW: boxW, cellH: boxH,
                        photoW: photoW, photoH: photoH,
                        count: count
                    };
                }
            }
        }

        var canvas = document.createElement('canvas');
        canvas.width = SHEET_W;
        canvas.height = SHEET_H;
        var ctx = canvas.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, SHEET_W, SHEET_H);

        var startX = SHEET_D;
        var startY = SHEET_D;
        for (var row = 0; row < best.rows; row++) {
            for (var col = 0; col < best.cols; col++) {
                var dx = startX + col * (best.cellW + SHEET_GAP) + (best.cellW - best.photoW) / 2;
                var dy = startY + row * (best.cellH + SHEET_GAP) + (best.cellH - best.photoH) / 2;
                ctx.drawImage(photoCanvas, dx, dy, best.photoW, best.photoH);
                // trim marks
                ctx.strokeStyle = '#b9c2ce';
                ctx.lineWidth = 1;
                ctx.strokeRect(dx, dy, best.photoW, best.photoH);
            }
        }

        // sheet label
        ctx.fillStyle = '#0b1220';
        ctx.font = '18px "DM Sans", sans-serif';
        ctx.textAlign = 'left';
        ctx.textBaseline = 'top';
        ctx.fillText(PRESETS[presetIdx].name + ' — ' + best.count + ' prints · 6×4 in @ 300 DPI', SHEET_D, SHEET_H - SHEET_D + 6);

        return canvas;
    }

    var makeBtn = document.getElementById('tc-ppt-make');
    var sheetBtn = document.getElementById('tc-ppt-sheet');

    function requireSource() {
        if (!source) {
            TCTP.toast('Please upload a photo first.', '\u26A0\uFE0F');
            return false;
        }
        return true;
    }

    if (makeBtn) makeBtn.addEventListener('click', function () {
        if (!requireSource()) return;
        TCTP.showProgress('tc-ppt-progress');
        TCTP.setProgress('tc-ppt-progress', 40, 'Cropping photo...');

        var canvas = renderPhotoCanvas();
        canvas.toBlob(function (blob) {
            madeBlob = blob;
            TCTP.setProgress('tc-ppt-progress', 90, 'Preparing preview...');
            TCTP.hideProgress('tc-ppt-progress');

            var url = URL.createObjectURL(blob);
            var prev = document.getElementById('tc-ppt-result-preview');
            if (prev) prev.innerHTML = '<img src="' + url + '" alt="Passport photo">';
            TCTP.showResultPreview(url);
            TCTP.switchToResultTab();

            var dl = document.getElementById('tc-ppt-download');
            if (dl) dl.disabled = false;
            updateStats();
            TCTP.toast('Passport photo ready!');
        }, outFmt, 0.95);
    });

    if (sheetBtn) sheetBtn.addEventListener('click', function () {
        if (!requireSource()) return;
        TCTP.showProgress('tc-ppt-progress');
        TCTP.setProgress('tc-ppt-progress', 60, 'Building print sheet...');
        var photo = renderPhotoCanvas();
        var sheet = renderSheetFrom(photo);
        sheet.toBlob(function (blob) {
            sheetBlob = blob;
            TCTP.setProgress('tc-ppt-progress', 100, 'Done!');
            TCTP.hideProgress('tc-ppt-progress');
            TCTP.downloadBlob(blob, 'passport-photo-sheet-' + (PRESETS[presetIdx].name.replace(/\s+/g, '-').toLowerCase()) + '.' + outExt);
        }, outFmt, 0.92);
    });

    var downloadBtn = document.getElementById('tc-ppt-download');
    if (downloadBtn) downloadBtn.addEventListener('click', function () {
        if (!madeBlob) {
            if (!requireSource()) return;
            TCTP.toast('Create the photo first.', '\u26A0\uFE0F');
            return;
        }
        TCTP.downloadBlob(madeBlob, 'passport-photo-' + (PRESETS[presetIdx].name.replace(/\s+/g, '-').toLowerCase()) + '.' + outExt);
    });

    var copyBtn = document.getElementById('tc-ppt-copy');
    if (copyBtn) copyBtn.addEventListener('click', function () {
        resetAll();
        resetCrop();
    });

    // ── Stats ──────────────────────────────────────────────────

    function updateStats() {
        var statPreset = document.getElementById('tc-ppt-stat-preset');
        var statSize = document.getElementById('tc-ppt-stat-size');
        var statBg = document.getElementById('tc-ppt-stat-bg');
        var p = PRESETS[presetIdx];
        if (statPreset) statPreset.textContent = p.name;
        if (statSize) statSize.textContent = p.w + ' × ' + p.h + ' px';
        if (statBg) statBg.textContent = bgColor;
    }

    updateStats();
    window.addEventListener('resize', function () { if (source) resizeFrameToStage(); });

})();
