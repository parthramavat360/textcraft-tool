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
    var panX = 0.5;           // 0..1 horizontal crop-window position (left->right)
    var panY = 0.38;          // 0..1 vertical crop-window position (top->bottom)
    var bgColor = '#ffffff';
    var outFmt = 'image/jpeg';
    var outExt = 'jpg';
    var sheetCount = 0;       // 0 = auto
    var resScale = 1;         // output upscale: 1, 2, or 4
    var madeBlob = null;      // single cropped photo blob
    var sheetBlob = null;
    var dragging = false;
    var dragStart = { x: 0, y: 0, panX: 0.5, panY: 0.38 };
    var labelText = '';
    var labelColor = '#0b1220';

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
            dragStart.panX = panX;
            dragStart.panY = panY;
            ev.preventDefault();
        });
        stageEl.addEventListener('touchstart', function (ev) {
            if (!source) return;
            dragging = true;
            var t = ev.touches[0];
            dragStart.x = t.clientX;
            dragStart.y = t.clientY;
            dragStart.panX = panX;
            dragStart.panY = panY;
            ev.preventDefault();
        }, { passive: false });

        function move(ev) {
            if (!dragging || !source) return;
            var px = ev.clientX || (ev.touches && ev.touches[0].clientX);
            var py = ev.clientY || (ev.touches && ev.touches[0].clientY);
            var rect = stageEl.getBoundingClientRect();
            var r = srcRect();
            var scale = (rect.width || 1) / (r.sw || 1);
            var availX = Math.max(1, sourceW - r.sw);
            var availY = Math.max(1, sourceH - r.sh);
            var dx = px - dragStart.x;
            var dy = py - dragStart.y;
            panX = clamp01(dragStart.panX - (dx / scale) / availX);
            panY = clamp01(dragStart.panY - (dy / scale) / availY);
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
    var zoomValue = document.getElementById('tc-ppt-zoom-value');
    if (zoomInput) zoomInput.addEventListener('input', function () {
        zoom = (parseInt(zoomInput.value) || 100) / 100;
        if (zoomValue) zoomValue.textContent = zoomInput.value + '%';
        drawCrop();
    });

    // ── Guides toggle ───────────────────────────────────────────

    var guidesOn = document.getElementById('tc-ppt-guides-on');
    var guidesEl = document.getElementById('tc-ppt-guides');
    if (guidesOn && guidesEl) guidesOn.addEventListener('change', function () {
        guidesEl.hidden = !guidesOn.checked;
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

    // ── Bottom label ──────────────────────────────────────────

    var labelInput = document.getElementById('tc-ppt-label-text');
    var labelColorInput = document.getElementById('tc-ppt-label-color');
    if (labelInput) labelInput.addEventListener('input', function () {
        labelText = labelInput.value;
        liveLabelPreview();
    });
    if (labelColorInput) labelColorInput.addEventListener('input', function () {
        labelColor = labelColorInput.value;
        liveLabelPreview();
    });

    function liveLabelPreview() {
        // The label is part of the output render; refresh the result live so
        // the user sees the label without re-creating the photo.
        updateStats();
        if (!source) return;
        var c = renderPhotoCanvas();
        c.toBlob(function (blob) {
            if (!blob) return;
            var url = URL.createObjectURL(blob);
            TCTP.showResultPreview(url);
            TCTP.switchToResultTab();
            madeBlob = blob;
            var dl = document.getElementById('tc-ppt-download');
            if (dl) dl.disabled = false;
        }, outFmt, 0.95);
    }

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

    // ── Photo count ────────────────────────────────────────────

    var countBtns = document.querySelectorAll('#tc-ppt-count-pills .tc-ppt-count');
    countBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            countBtns.forEach(function (b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            sheetCount = parseInt(btn.getAttribute('data-count'), 10) || 0;
        });
    });

    // ── Resolution ─────────────────────────────────────────────

    var resBtns = document.querySelectorAll('#tc-ppt-res-pills .tc-ppt-count');
    resBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            resBtns.forEach(function (b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            resScale = parseInt(btn.getAttribute('data-scale'), 10) || 1;
        });
    });

    // ── Crop rendering (stage + live guide) ────────────────────

    function resetCrop() {
        zoom = 1.6;
        panX = 0.5;
        panY = 0.38;
        if (zoomInput) zoomInput.value = 160;
        if (zoomValue) zoomValue.textContent = '160%';
        madeBlob = null;
        sheetBlob = null;
        updateStats();
        resizeFrameToStage();
    }

    function resetAll() {
        source = null;
        sourceW = sourceH = 0;
        madeBlob = sheetBlob = null;
        labelText = '';
        if (labelInput) labelInput.value = '';
        var dl = document.getElementById('tc-ppt-download');
        var made = document.getElementById('tc-ppt-make');
        if (dl) dl.disabled = true;
        if (made) made.disabled = false;
        TCTP.hideFileRow('tc-ppt-file');
        if (imgEl) { imgEl.hidden = true; imgEl.removeAttribute('src'); }
        if (frameEl) frameEl.style.display = 'none';
        var prevRes = document.getElementById('tc-preview-result');
        if (prevRes) prevRes.innerHTML = 'Photo preview will appear here';
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

        // Free pan: drag moves the crop window across the available travel.
        var availX = Math.max(0, sourceW - sw);
        var availY = Math.max(0, sourceH - sh);
        var sx = availX * clamp01(panX);
        var sy = availY * clamp01(panY);

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
        var pw = PRESETS[presetIdx].w * resScale;
        var ph = PRESETS[presetIdx].h * resScale;
        var canvas = document.createElement('canvas');
        canvas.width = pw;
        canvas.height = ph;
        var ctx = canvas.getContext('2d');

        ctx.fillStyle = bgColor;
        ctx.fillRect(0, 0, pw, ph);
        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = 'high';
        ctx.drawImage(source, r.sx, r.sy, r.sw, r.sh, 0, 0, pw, ph);
        drawLabel(ctx, pw, ph);
        return canvas;
    }

    function drawLabel(ctx, pw, ph) {
        var t = labelText;
        if (!t) return;
        var barH = Math.max(22, Math.round(ph * 0.11));
        ctx.fillStyle = 'rgba(255,255,255,0.92)';
        ctx.fillRect(0, ph - barH, pw, barH);
        ctx.fillStyle = 'rgba(0,0,0,0.12)';
        ctx.fillRect(0, ph - barH, pw, 1);
        ctx.fillStyle = labelColor;
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.font = '600 ' + Math.max(9, Math.round(ph * 0.05)) + 'px "Space Grotesk", "Arial", sans-serif';
        ctx.fillText(t, pw / 2, ph - barH / 2);
    }

    function renderSheetFrom(photoCanvas) {
        var pw = photoCanvas.width;
        var ph = photoCanvas.height;
        var S = resScale;
        var SH_W = SHEET_W * S;
        var SH_H = SHEET_H * S;
        var SH_D = SHEET_D * S;
        var SH_GAP = SHEET_GAP * S;
        var SH_TICK = SHEET_TICK * S;

        // Margins for each cell (photo + breathing room) in px.
        var cellPad = 90 * S;
        var innerW = SH_W - SH_D * 2;
        var innerH = SH_H - SH_D * 2;

        // Choose the grid (cols x rows) that fits the most photos while
        // keeping each photo as large as possible. If a count was chosen,
        // restrict to grids with exactly that many photos.
        var best = null;
        for (var c = 1; c <= 8; c++) {
            for (var r = 1; r <= 8; r++) {
                var count = c * r;
                if (sheetCount > 0 && count !== sheetCount) continue;
                var boxW = (innerW - (c - 1) * SH_GAP) / c;
                var boxH = (innerH - (r - 1) * SH_GAP) / r;
                var wScale = (boxW - cellPad) / pw;
                var hScale = (boxH - cellPad) / ph;
                var s = Math.max(0.15, Math.min(wScale, hScale));
                var photoW = pw * s;
                var photoH = ph * s;
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
        canvas.width = SH_W;
        canvas.height = SH_H;
        var ctx = canvas.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, SH_W, SH_H);

        var startX = SH_D;
        var startY = SH_D;
        for (var row = 0; row < best.rows; row++) {
            for (var col = 0; col < best.cols; col++) {
                var dx = startX + col * (best.cellW + SH_GAP) + (best.cellW - best.photoW) / 2;
                var dy = startY + row * (best.cellH + SH_GAP) + (best.cellH - best.photoH) / 2;
                ctx.drawImage(photoCanvas, dx, dy, best.photoW, best.photoH);
                // trim marks
                ctx.strokeStyle = '#b9c2ce';
                ctx.lineWidth = Math.max(1, S);
                ctx.strokeRect(dx, dy, best.photoW, best.photoH);
            }
        }

        // sheet label
        ctx.fillStyle = '#0b1220';
        ctx.font = (18 * S) + 'px "DM Sans", sans-serif';
        ctx.textAlign = 'left';
        ctx.textBaseline = 'top';
        ctx.fillText(PRESETS[presetIdx].name + ' — ' + best.count + ' prints · 6×4 in @ ' + (300 * S) + ' DPI', SH_D, SH_H - SH_D + 6 * S);

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
