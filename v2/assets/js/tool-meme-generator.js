/**
 * Meme Generator — Tool JS
 * 100% client-side meme generation using canvas API.
 * Object model: multiple draggable text blocks + arrow/box/line annotations.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var dropEl = document.getElementById('tc-meme-drop');
    if (!dropEl) return;

    // ── State ────────────────────────────────────────────────────
    var file = null;
    var imgEl = null;
    var naturalW = 0, naturalH = 0;
    var displayW = 0, displayH = 0;
    var outputFormat = 'original';
    var memeBlob = null;
    var activeTool = 'select';
    var selectedIndex = -1;
    var blockSeq = 0;
    var dragBlockIdx = null;

    // Elements are normalized (0-1) against the natural image so the
    // small preview and the full-resolution export stay perfectly in sync.
    var objects = [];

    var canvas = document.getElementById('tc-meme-canvas');
    var previewWrap = document.getElementById('tc-meme-preview-wrap');
    var placeholder = document.getElementById('tc-meme-placeholder');
    var fmtCards = document.querySelectorAll('.tc-rsz-format-row .tc-rsz-fmt');
    var applyBtn = document.getElementById('tc-meme-apply');
    var dlBtn = document.getElementById('tc-meme-download');
    var clearBtn = document.getElementById('tc-meme-clear');
    var styleSection = document.getElementById('tc-meme-style-section');

    var colorInput = document.getElementById('tc-meme-color');
    var strokeInput = document.getElementById('tc-meme-stroke');
    var fontSlider = document.getElementById('tc-meme-fontsize');
    var fontVal = document.getElementById('tc-meme-size-val');
    var strokeSlider = document.getElementById('tc-meme-strokewidth');
    var strokeVal = document.getElementById('tc-meme-stroke-val');

    // Default style palette for newly created elements
    var defColor = '#ffffff';
    var defStroke = '#000000';
    var defStrokeW = 3;
    var defFont = 'Impact, Arial Black, sans-serif';
    var defFontSize = 48;

    // ── Element factories ────────────────────────────────────────
    function addTextBlock(text) {
        var obj = {
            type: 'text',
            id: 'b' + (++blockSeq),
            text: text || '',
            color: defColor,
            strokeColor: defStroke,
            strokeWidth: defStrokeW,
            fontFamily: defFont,
            fontSize: defFontSize,
            x: 0.5,
            y: 0.08
        };
        objects.push(obj);
        selectedIndex = objects.length - 1;
        return obj;
    }

    function addShape(kind) {
        var obj = {
            type: 'shape',
            kind: kind,
            x1: 0.15, y1: 0.15,
            x2: 0.85, y2: 0.85,
            color: '#ef4444',
            strokeWidth: defStrokeW + 1
        };
        objects.push(obj);
        return obj;
    }

    function selectByToggle(index) {
        if (index >= 0 && index < objects.length) {
            selectedIndex = index;
        } else {
            selectedIndex = -1;
        }
        renderPreview();
        syncStyleUI();
        syncBlockList();
    }

    // ── Rendering helpers ────────────────────────────────────────
    function getFontPx(obj, renderH) {
        return Math.round(obj.fontSize * (renderH / 400));
    }

    function measureLinesCtx(ctx, text, fontSize, w, font) {
        var words = text.toUpperCase().split(' ');
        var lines = [];
        var currentLine = words[0] || '';
        ctx.font = 'bold ' + fontSize + 'px ' + (font || defFont);
        for (var i = 1; i < words.length; i++) {
            var testLine = currentLine + ' ' + words[i];
            if (ctx.measureText(testLine).width > w * 0.92) {
                lines.push(currentLine);
                currentLine = words[i];
            } else {
                currentLine = testLine;
            }
        }
        lines.push(currentLine);
        return { lines: lines, lineHeight: fontSize * 1.1 };
    }

    function obj_getFont(obj) {
        return obj.fontFamily || defFont;
    }

    function drawTextObj(ctx, obj, W, H) {
        if (!obj.text) return;
        var fontSize = getFontPx(obj, H);
        var xx = obj.x * W;
        var yy = obj.y * H;
        ctx.save();
        ctx.font = 'bold ' + fontSize + 'px ' + obj_getFont(obj);
        ctx.textAlign = 'center';
        ctx.textBaseline = 'top';
        var words = obj.text.toUpperCase().split(' ');
        var lines = [];
        var currentLine = words[0] || '';
        for (var i = 1; i < words.length; i++) {
            var testLine = currentLine + ' ' + words[i];
            if (ctx.measureText(testLine).width > W * 0.92) {
                lines.push(currentLine);
                currentLine = words[i];
            } else {
                currentLine = testLine;
            }
        }
        lines.push(currentLine);
        var lineHeight = fontSize * 1.1;
        for (var j = 0; j < lines.length; j++) {
            var lineY = yy + j * lineHeight;
            ctx.strokeStyle = obj.strokeColor;
            ctx.lineWidth = obj.strokeWidth * (H / 400);
            ctx.lineJoin = 'round';
            ctx.miterLimit = 2;
            ctx.strokeText(lines[j], xx, lineY);
            ctx.fillStyle = obj.color;
            ctx.fillText(lines[j], xx, lineY);
        }
        ctx.restore();
    }

    function textBounds(obj, W, H) {
        var fontSize = getFontPx(obj, H);
        var ctx = canvas ? canvas.getContext('2d') : null;
        var m = measureLinesCtx(ctx, obj.text, fontSize, W, obj_getFont(obj));
        var maxW = 0;
        if (ctx) {
            ctx.save();
            ctx.font = 'bold ' + fontSize + 'px ' + obj_getFont(obj);
            var words = obj.text.toUpperCase().split(' ');
            for (var i = 0; i < m.lines.length; i++) {
                var w = ctx.measureText(m.lines[i]).width;
                if (w > maxW) maxW = w;
            }
            ctx.restore();
        }
        var total = m.lines.length * m.lineHeight;
        return {
            x: obj.x * W - maxW / 2,
            y: obj.y * H,
            w: maxW,
            h: total,
            cx: obj.x * W,
            cy: obj.y * H + total / 2
        };
    }

    function drawShapeObj(ctx, obj, W, H) {
        var x1 = obj.x1 * W, y1 = obj.y1 * H;
        var x2 = obj.x2 * W, y2 = obj.y2 * H;
        ctx.save();
        ctx.strokeStyle = obj.color;
        ctx.fillStyle = obj.color;
        ctx.lineWidth = Math.max(1, obj.strokeWidth * (H / 400));
        ctx.lineCap = 'round';
        if (obj.kind === 'arrow') {
            ctx.beginPath();
            ctx.moveTo(x1, y1);
            ctx.lineTo(x2, y2);
            ctx.stroke();
            var ang = Math.atan2(y2 - y1, x2 - x1);
            var len = 12 * (H / 400) + ctx.lineWidth;
            var h1 = Math.PI * 0.28;
            ctx.beginPath();
            ctx.moveTo(x2, y2);
            ctx.lineTo(x2 - len * Math.cos(ang - h1), y2 - len * Math.sin(ang - h1));
            ctx.lineTo(x2 - len * Math.cos(ang + h1), y2 - len * Math.sin(ang + h1));
            ctx.closePath();
            ctx.fill();
        } else if (obj.kind === 'box') {
            ctx.strokeRect(Math.min(x1, x2), Math.min(y1, y2), Math.abs(x2 - x1), Math.abs(y2 - y1));
        } else {
            ctx.beginPath();
            ctx.moveTo(x1, y1);
            ctx.lineTo(x2, y2);
            ctx.stroke();
        }
        ctx.restore();
    }

    function getBounds(obj, W, H) {
        if (obj.type === 'text') {
            return textBounds(obj, W, H);
        }
        var x1 = obj.x1 * W, y1 = obj.y1 * H;
        var x2 = obj.x2 * W, y2 = obj.y2 * H;
        return {
            x: Math.min(x1, x2), y: Math.min(y1, y2),
            w: Math.abs(x2 - x1), h: Math.abs(y2 - y1),
            cx: (x1 + x2) / 2, cy: (y1 + y2) / 2
        };
    }

    // Returns array of handle points [ {x,y,type} ] for the selected element
    var HANDLE_KEYS = ['nw', 'n', 'ne', 'e', 'se', 's', 'sw', 'w'];
    function getHandles(W, H) {
        if (selectedIndex < 0 || selectedIndex >= objects.length) return [];
        var b = getBounds(objects[selectedIndex], W, H);
        var cx = b.cx, cy = b.cy;
        var pts = {
            nw: { x: b.x, y: b.y },
            n: { x: cx, y: b.y },
            ne: { x: b.x + b.w, y: b.y },
            e: { x: b.x + b.w, y: cy },
            se: { x: b.x + b.w, y: b.y + b.h },
            s: { x: cx, y: b.y + b.h },
            sw: { x: b.x, y: b.y + b.h },
            w: { x: b.x, y: cy }
        };
        var out = [];
        for (var i = 0; i < HANDLE_KEYS.length; i++) {
            var k = HANDLE_KEYS[i];
            out.push({ x: pts[k].x, y: pts[k].y, type: k });
        }
        return out;
    }

    var HANDLE_HIT = 10;
    function handleHitTest(px, py, W, H) {
        var hs = getHandles(W, H);
        for (var i = 0; i < hs.length; i++) {
            if (Math.abs(px - hs[i].x) < HANDLE_HIT && Math.abs(py - hs[i].y) < HANDLE_HIT) return hs[i].type;
        }
        return null;
    }

    function handleCursor(type) {
        var map = { nw: 'nwse-resize', se: 'nwse-resize', ne: 'nesw-resize', sw: 'nesw-resize', n: 'ns-resize', s: 'ns-resize', e: 'ew-resize', w: 'ew-resize' };
        return map[type] || 'default';
    }

    // Fixed (opposite) anchor point on the box for a given handle
    function fixPoint(b, handle) {
        var cx = b.cx, cy = b.cy, w2 = b.w / 2, h2 = b.h / 2;
        switch (handle) {
            case 'nw': return { x: cx + w2, y: cy + h2 };
            case 'ne': return { x: cx - w2, y: cy + h2 };
            case 'sw': return { x: cx + w2, y: cy - h2 };
            case 'se': return { x: cx - w2, y: cy - h2 };
            case 'n': return { x: cx, y: cy + h2 };
            case 's': return { x: cx, y: cy - h2 };
            case 'e': return { x: cx - w2, y: cy };
            case 'w': return { x: cx + w2, y: cy };
        }
        return { x: cx, y: cy };
    }

    var SHAPE_HANDLE_KEYS = {
        nw: ['L', 'T'], n: ['T'], ne: ['R', 'T'],
        e: ['R'], se: ['R', 'B'], s: ['B'], sw: ['L', 'B'], w: ['L']
    };
    function shapeResizeKeys(handle, obj) {
        // Map handle to the actual x1/x2, y1/y2 property names to mutate
        var L = obj.x1 < obj.x2 ? 'x1' : 'x2';
        var R = obj.x1 < obj.x2 ? 'x2' : 'x1';
        var T = obj.y1 < obj.y2 ? 'y1' : 'y2';
        var B = obj.y1 < obj.y2 ? 'y2' : 'y1';
        var mapKey = { L: L, R: R, T: T, B: B };
        var arr = SHAPE_HANDLE_KEYS[handle] || [];
        var out = {};
        for (var i = 0; i < arr.length; i++) { out[mapKey[arr[i]]] = true; }
        return out;
    }

    function drawSelectionOutline(ctx, W, H) {
        if (selectedIndex < 0 || selectedIndex >= objects.length) return;
        var obj = objects[selectedIndex];
        var b = getBounds(obj, W, H);
        ctx.save();
        ctx.strokeStyle = '#0ea5e9';
        ctx.lineWidth = 1.5;
        ctx.setLineDash([4, 4]);
        ctx.strokeRect(b.x - 4, b.y - 4, b.w + 8, b.h + 8);
        ctx.setLineDash([]);
        ctx.fillStyle = '#ffffff';
        ctx.strokeStyle = '#0ea5e9';
        ctx.lineWidth = 1;
        var hs = getHandles(W, H);
        for (var i = 0; i < hs.length; i++) {
            ctx.fillRect(hs[i].x - 3.5, hs[i].y - 3.5, 7, 7);
            ctx.strokeRect(hs[i].x - 3.5, hs[i].y - 3.5, 7, 7);
        }
        ctx.restore();
    }

    // ── Render ───────────────────────────────────────────────────
    function renderPreview() {
        if (!canvas || !imgEl) return;
        var ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, displayW, displayH);
        ctx.drawImage(imgEl, 0, 0, displayW, displayH);

        for (var i = 0; i < objects.length; i++) {
            var obj = objects[i];
            if (obj.type === 'text') drawTextObj(ctx, obj, displayW, displayH);
            else drawShapeObj(ctx, obj, displayW, displayH);
        }
        drawSelectionOutline(ctx, displayW, displayH);

        var totalChars = objects.reduce(function (n, o) {
            return n + (o.type === 'text' ? o.text.length : 0);
        }, 0);
        if (totalChars === 0 && !objects.some(function (o) { return o.type === 'shape'; })) totalChars = 0;
        var textStat = document.getElementById('tc-meme-stat-text');
        if (textStat) textStat.textContent = objects.length + ' elem, ' + totalChars + ' chars';
    }

    var debounceTimer = null;
    function debouncedRender() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(renderPreview, 80);
    }

    // ── Hit testing ──────────────────────────────────────────────
    function hitTest(nx, ny, W, H) {
        // iterate topmost last
        for (var i = objects.length - 1; i >= 0; i--) {
            var obj = objects[i];
            if (obj.type === 'text' && obj.text) {
                var b = textBounds(obj, W, H);
                if (nx >= b.x && nx <= b.x + b.w && ny >= b.y && ny <= b.y + b.h) return i;
            } else if (obj.type === 'shape') {
                var x1 = obj.x1 * W, y1 = obj.y1 * H;
                var x2 = obj.x2 * W, y2 = obj.y2 * H;
                var minX = Math.min(x1, x2) - 10, minY = Math.min(y1, y2) - 10;
                var maxX = Math.max(x1, x2) + 10, maxY = Math.max(y1, y2) + 10;
                if (nx >= minX && nx <= maxX && ny >= minY && ny <= maxY) return i;
            }
        }
        return -1;
    }

    function canvasPoint(e) {
        var rect = canvas.getBoundingClientRect();
        var px = (e.clientX - rect.left) * (canvas.width / rect.width);
        var py = (e.clientY - rect.top) * (canvas.height / rect.height);
        return { x: px, y: py };
    }

    // ── Pointer interaction ─────────────────────────────────────
    var dragState = null;

    function onPointerDown(e) {
        if (!imgEl || !canvas) return;
        var p = canvasPoint(e);
        var nx = p.x / displayW, ny = p.y / displayH;

        if (activeTool === 'select') {
            var handle = handleHitTest(p.x, p.y, displayW, displayH);
            if (handle && selectedIndex >= 0) {
                var selObj = objects[selectedIndex];
                var sb = getBounds(selObj, displayW, displayH);
                dragState = {
                    mode: 'resize',
                    index: selectedIndex,
                    handle: handle,
                    sx: p.x, sy: p.y,
                    font0: selObj.fontSize || 0,
                    anchor0: fixPoint(sb, handle),
                    dist0: Math.max(1, Math.hypot(p.x - fixPoint(sb, handle).x, p.y - fixPoint(sb, handle).y)),
                    shapeKeys: selObj.type === 'shape' ? shapeResizeKeys(handle, selObj) : null
                };
                return;
            }
            var idx = hitTest(p.x, p.y, displayW, displayH);
            if (idx >= 0) {
                selectByToggle(idx);
                dragState = {
                    mode: 'move',
                    index: idx,
                    offX: objects[idx].x - nx,
                    offY: objects[idx].y - ny
                };
            } else {
                selectByToggle(-1);
            }
            return;
        }

        // Shape drawing mode
        if (activeTool === 'arrow' || activeTool === 'box' || activeTool === 'line') {
            var kind = activeTool;
            var nc = canvasPoint(e);
            dragState = {
                mode: 'draw',
                kind: kind,
                sx: nc.x / displayW,
                sy: nc.y / displayH,
                preview: null
            };
            return;
        }

        // Text placed by clicking canvas
        if (activeTool === 'text') {
            // Use a visible default so the text immediately shows; select-all in the
            // sidebar input lets the user overwrite it instantly.
            var obj = {
                type: 'text',
                id: 'b' + (++blockSeq),
                text: 'YOUR TEXT',
                color: defColor,
                strokeColor: defStroke,
                strokeWidth: defStrokeW,
                fontFamily: defFont,
                fontSize: defFontSize,
                x: nx,
                y: ny
            };
            objects.push(obj);
            selectedIndex = objects.length - 1;
            renderPreview();
            syncBlockList();
            syncStyleUI();
            setActiveTool('select');
            var lastRow = document.querySelector('#tc-meme-block-list .tc-meme-block-row:last-child input');
            if (lastRow) {
                lastRow.focus();
                lastRow.select();
                lastRow.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            }
            return;
        }
    }

    function onPointerMove(e) {
        if (!dragState || !canvas) return;
        var p = canvasPoint(e);
        var nx = p.x / displayW, ny = p.y / displayH;

        if (dragState.mode === 'move') {
            var t = objects[dragState.index];
            if (!t) return;
            t.x = Math.max(0, Math.min(1, dragState.offX + nx));
            t.y = Math.max(0, Math.min(1, dragState.offY + ny));
            renderPreview();
        } else if (dragState.mode === 'resize') {
            var r = objects[dragState.index];
            if (!r) return;
            if (r.type === 'shape' && dragState.shapeKeys) {
                var keys = dragState.shapeKeys;
                if (keys.x1) r.x1 = nx;
                if (keys.x2) r.x2 = nx;
                if (keys.y1) r.y1 = ny;
                if (keys.y2) r.y2 = ny;
            } else {
                // Text: scale font by distance from the fixed anchor point
                var curAnchor = fixPoint(getBounds(r, displayW, displayH), dragState.handle);
                var dist = Math.max(1, Math.hypot(p.x - curAnchor.x, p.y - curAnchor.y));
                var newSize = dragState.font0 * (dist / dragState.dist0);
                r.fontSize = Math.max(8, Math.min(200, Math.round(newSize)));
                // Shift so the fixed anchor (captured at drag start) stays put
                var nb = getBounds(r, displayW, displayH);
                var ap = fixPoint(nb, dragState.handle);
                r.x = (nb.cx + (dragState.anchor0.x - ap.x)) / displayW;
                r.y = (nb.cy + (dragState.anchor0.y - ap.y)) / displayH - nb.h / (2 * displayH);
            }
            renderPreview();
        } else if (dragState.mode === 'draw') {
            var kind = dragState.kind;
            if (!dragState.preview) {
                dragState.preview = { type: 'shape', kind: kind, color: '#ef4444', strokeWidth: defStrokeW + 1, x1: dragState.sx, y1: dragState.sy, x2: nx, y2: ny };
                objects.push(dragState.preview);
            } else {
                dragState.preview.x2 = nx;
                dragState.preview.y2 = ny;
            }
            renderPreview();
        }
    }

    function onPointerUp(e) {
        if (!dragState) return;
        var p = canvasPoint(e);
        var nx = p.x / displayW, ny = p.y / displayH;
        if (dragState.mode === 'draw') {
            var prev = dragState.preview;
            if (prev) {
                var dist = Math.hypot((prev.x2 - prev.x1) * displayW, (prev.y2 - prev.y1) * displayH);
                if (dist < 6) {
                    // Tiny drag = just a click; remove the temporary shape
                    objects.pop();
                    selectedIndex = -1;
                } else {
                    selectedIndex = objects.length - 1;
                    prev.x2 = nx;
                    prev.y2 = ny;
                }
                dragState.preview = null;
            }
            resetTool();
            renderPreview();
            syncBlockList();
            syncStyleUI();
        } else if (dragState.mode === 'resize') {
            renderPreview();
            syncStyleUI();
        }
        dragState = null;
    }

    function resetTool() {
        setActiveTool('select');
        if (canvas) canvas.classList.remove('dragging');
    }

    // ── Toolbar ─────────────────────────────────────────────────
    function setActiveTool(tool) {
        activeTool = tool;
        document.querySelectorAll('.tc-meme-toolbar .tc-meme-tool').forEach(function (b) {
            if (b.id === 'tc-meme-del') return;
            if (b.getAttribute('data-tool') === tool) b.classList.add('sel');
            else b.classList.remove('sel');
        });
        if (canvas) {
            if (tool === 'select' || tool === 'text') canvas.style.cursor = 'default';
            else canvas.style.cursor = 'crosshair';
        }
    }

    document.querySelectorAll('.tc-meme-toolbar .tc-meme-tool[data-tool]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            setActiveTool(btn.getAttribute('data-tool'));
        });
    });

    // Delete button
    var delBtn = document.getElementById('tc-meme-del');
    if (delBtn) {
        delBtn.addEventListener('click', function () {
            if (selectedIndex < 0) { TCTP.toast('Nothing selected to delete.', '\u26A0\uFE0F'); return; }
            objects.splice(selectedIndex, 1);
            selectedIndex = -1;
            renderPreview();
            syncBlockList();
            syncStyleUI();
            TCTP.toast('Element deleted.', '\u2705');
        });
    }

    var addTextBtn = document.getElementById('tc-meme-add-text');
    if (addTextBtn) {
        addTextBtn.addEventListener('click', function () {
            var obj = addTextBlock('');
            selectedIndex = objects.length - 1;
            setActiveTool('select');
            renderPreview();
            syncBlockList();
            syncStyleUI();
            var lastRow = document.querySelector('#tc-meme-block-list .tc-meme-block-row:last-child input');
            if (lastRow) {
                lastRow.focus();
                lastRow.select();
                lastRow.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            }
        });
    }

    // ── Block list ──────────────────────────────────────────────
    function syncBlockList() {
        var list = document.getElementById('tc-meme-block-list');
        if (!list) return;
        list.innerHTML = '';
        var txts = 0;
        objects.forEach(function (obj, i) {
            if (obj.type === 'shape') return;
            txts++;
            var row = document.createElement('div');
            row.className = 'tc-meme-block-row' + (i === selectedIndex ? ' sel' : '');
            row.setAttribute('data-index', i);

            var idx = document.createElement('span');
            idx.className = 'tc-meme-block-idx';
            idx.textContent = txts;

            var tt = document.createElement('span');
            tt.className = 'tc-meme-block-tt';
            tt.textContent = '\u270E';

            var inp = document.createElement('input');
            inp.type = 'text';
            inp.className = 'tc-meme-block-input';
            inp.placeholder = 'MEME TEXT';
            inp.autocomplete = 'off';
            inp.value = obj.text;
            inp.addEventListener('input', function () {
                obj.text = inp.value;
                renderPreview();
            });
            inp.addEventListener('focus', function () {
                selectedIndex = i;
                refreshSelectionHighlight();
                syncStyleUI();
                renderPreview();
            });

            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'tc-meme-block-del';
            del.innerHTML = '\u00D7';
            del.title = 'Remove text block';
            del.addEventListener('click', function (ev) {
                ev.stopPropagation();
                objects.splice(i, 1);
                selectedIndex = -1;
                renderPreview();
                syncBlockList();
                syncStyleUI();
            });

            row.appendChild(idx);
            row.appendChild(tt);
            row.appendChild(inp);
            row.appendChild(del);
            row.addEventListener('click', function (ev) {
                ev.stopPropagation();
                selectedIndex = i;
                refreshSelectionHighlight();
                syncStyleUI();
                renderPreview();
                inp.focus();
            });

            // Drag to reorder text blocks (also controls stacking order on canvas).
            // Only the grip handle is draggable so the text input stays fully interactive.
            tt.draggable = true;
            tt.addEventListener('dragstart', function (e) {
                dragBlockIdx = i;
                row.classList.add('dragging');
                if (e.dataTransfer) { e.dataTransfer.effectAllowed = 'move'; e.dataTransfer.setData('text/plain', String(i)); }
            });
            tt.addEventListener('dragend', function () {
                dragBlockIdx = null;
                row.classList.remove('dragging');
                document.querySelectorAll('#tc-meme-block-list .tc-meme-block-row').forEach(function (r) {
                    r.classList.remove('drag-over');
                });
            });
            row.addEventListener('dragover', function (e) {
                e.preventDefault();
                if (e.dataTransfer) e.dataTransfer.dropEffect = 'move';
                row.classList.add('drag-over');
            });
            row.addEventListener('dragleave', function () {
                row.classList.remove('drag-over');
            });
            row.addEventListener('drop', function (e) {
                e.preventDefault();
                e.stopPropagation();
                row.classList.remove('drag-over');
                if (dragBlockIdx === null || dragBlockIdx === i) return;
                reorderTextBlocks(dragBlockIdx, i);
                dragBlockIdx = null;
            });
            list.appendChild(row);
        });
    }

    // Toggle .sel on existing rows without rebuilding them (keeps the focused input alive)
    function refreshSelectionHighlight() {
        var list = document.getElementById('tc-meme-block-list');
        if (!list) return;
        Array.prototype.forEach.call(list.querySelectorAll('.tc-meme-block-row'), function (row) {
            row.classList.toggle('sel', parseInt(row.getAttribute('data-index'), 10) === selectedIndex);
        });
    }

    // Reorder two text blocks in the objects array, preserving shapes & other elements
    function reorderTextBlocks(from, to) {
        if (from === to) return;
        var texts = objects.filter(function (o) { return o.type === 'text'; });
        if (from < 0 || from >= texts.length || to < 0 || to >= texts.length) return;
        var moved = texts.splice(from, 1)[0];
        var insertAt = to > texts.length ? texts.length : to;
        texts.splice(insertAt, 0, moved);
        // Rebuild array: shapes keep relative order, texts placed in new order
        var ti = 0;
        var rebuilt = [];
        objects.forEach(function (o) {
            if (o.type === 'text') { rebuilt.push(texts[ti++]); }
            else { rebuilt.push(o); }
        });
        objects = rebuilt;
        var newIdx = -1;
        objects.forEach(function (o, k) { if (o === moved) newIdx = k; });
        if (newIdx >= 0) { selectedIndex = newIdx; }
        renderPreview();
        syncBlockList();
        syncStyleUI();
    }

    // ── Style UI (applies to selected element) ──────────────────
    function syncStyleUI() {
        var sel = (selectedIndex >= 0 && selectedIndex < objects.length) ? objects[selectedIndex] : null;
        var label = document.getElementById('tc-meme-selection');
        var isText = sel && sel.type === 'text';
        var isShape = sel && sel.type === 'shape';

        if (label) {
            if (!sel) label.textContent = 'Nothing selected — click an element on the image or add one.';
            else if (isText) label.textContent = 'Selected: Text block';
            else label.textContent = 'Selected: ' + (sel.kind === 'arrow' ? 'Arrow' : sel.kind === 'box' ? 'Box' : 'Line');
        }

        if (fontSlider && isText) fontSlider.value = sel.fontSize;
        if (fontVal && isText) fontVal.textContent = sel.fontSize;
        if (strokeSlider && (isText || isShape)) strokeSlider.value = sel.strokeWidth;
        if (strokeVal && (isText || isShape)) strokeVal.textContent = sel.strokeWidth;
        if (colorInput && (isText || isShape)) {
            colorInput.value = isText ? sel.color : sel.color;
            setColorValue('tc-meme-color', colorInput.value);
        }
        if (strokeInput && isText) {
            strokeInput.value = sel.strokeColor;
            setColorValue('tc-meme-stroke', sel.strokeColor);
        }

        var fontGroup = document.getElementById('tc-meme-font-group');
        var sizeGroup = document.getElementById('tc-meme-fontsize-group');
        var strokeGroup = document.getElementById('tc-meme-stroke-group');
        var alignGroup = document.getElementById('tc-meme-align-group');
        if (fontGroup) fontGroup.style.display = isText || !sel ? '' : 'none';
        if (sizeGroup) sizeGroup.style.display = isText || !sel ? '' : 'none';
        if (strokeGroup) strokeGroup.style.display = sel ? '' : 'none';
        if (alignGroup) alignGroup.style.display = sel ? '' : 'none';
        // color & stroke color groups (outer input-group divs)
        var colorGroup = styleSection ? styleSection.querySelector('.tc-meme-color-group[data-picker="tc-meme-color"]') : null;
        var scGroup = styleSection ? styleSection.querySelector('.tc-meme-color-group[data-picker="tc-meme-stroke"]') : null;
        if (colorGroup) colorGroup.style.display = sel ? '' : 'none';
        // stroke color has no meaning for shapes (they use 'text color'); hide for shapes
        if (scGroup) scGroup.style.display = isText || !sel ? '' : 'none';

        // font family selection
        if (isText) {
            document.querySelectorAll('.tc-meme-options [data-group="meme-font"] .tc-btn').forEach(function (b) {
                b.classList.toggle('sel', b.getAttribute('data-val') === sel.fontFamily);
            });
        }

        if (fontSlider) fontSlider.disabled = !sel;
        if (colorInput) colorInput.disabled = !sel;
        if (strokeInput) strokeInput.disabled = !sel;
    }

    // ── Style listeners ─────────────────────────────────────────
    if (fontSlider) {
        fontSlider.addEventListener('input', function () {
            if (fontVal) fontVal.textContent = fontSlider.value;
            var sel = (selectedIndex >= 0 && selectedIndex < objects.length) ? objects[selectedIndex] : null;
            if (sel && sel.type === 'text') { sel.fontSize = parseInt(fontSlider.value, 10) || 48; renderPreview(); }
        });
    }

    if (strokeSlider) {
        strokeSlider.addEventListener('input', function () {
            if (strokeVal) strokeVal.textContent = strokeSlider.value;
            var sel = (selectedIndex >= 0 && selectedIndex < objects.length) ? objects[selectedIndex] : null;
            if (sel) { sel.strokeWidth = parseInt(strokeSlider.value, 10) || 0; renderPreview(); }
        });
    }

    if (colorInput) {
        colorInput.addEventListener('input', function () {
            setColorValue('tc-meme-color', colorInput.value);
            var sel = (selectedIndex >= 0 && selectedIndex < objects.length) ? objects[selectedIndex] : null;
            if (sel) { sel.color = colorInput.value; renderPreview(); }
        });
    }
    if (strokeInput) {
        strokeInput.addEventListener('input', function () {
            setColorValue('tc-meme-stroke', strokeInput.value);
            var sel = (selectedIndex >= 0 && selectedIndex < objects.length) ? objects[selectedIndex] : null;
            if (sel && sel.type === 'text') { sel.strokeColor = strokeInput.value; renderPreview(); }
        });
    }

    document.querySelectorAll('.tc-meme-options [data-group="meme-font"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var group = btn.closest('[data-group="meme-font"]');
            if (group) group.querySelectorAll('.sel').forEach(function (b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            var sel = (selectedIndex >= 0 && selectedIndex < objects.length) ? objects[selectedIndex] : null;
            if (sel && sel.type === 'text') { sel.fontFamily = btn.getAttribute('data-val') || defFont; renderPreview(); }
        });
    });

    document.querySelectorAll('#tc-meme-align-group [data-align]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            alignSelected(btn.getAttribute('data-align'));
        });
    });

    function alignSelected(align) {
        if (selectedIndex < 0 || selectedIndex >= objects.length) return;
        var obj = objects[selectedIndex];
        var b = getBounds(obj, displayW, displayH);
        var target = { top: b.y, centerV: displayH / 2, bottom: displayH };
        // Resolve the two independent axes (vertical: top/center/bottom, horizontal: left/center/right)
        var vTarget, hTarget = null;
        switch (align) {
            case 'top': vTarget = 0; break;
            case 'vcenter': vTarget = (displayH - b.h) / 2; break;
            case 'bottom': vTarget = displayH - b.h; break;
            case 'left': hTarget = 0; break;
            case 'hcenter': hTarget = (displayW - b.w) / 2; break;
            case 'right': hTarget = displayW - b.w; break;
            default: return;
        }
        if (vTarget !== undefined) setObjY(obj, vTarget, b);
        if (hTarget !== null) setObjX(obj, hTarget, b);
        renderPreview();
    }

    function setObjY(obj, targetTopPx, b) {
        if (obj.type === 'text') {
            obj.y = targetTopPx / displayH;
            return;
        }
        var top = Math.min(obj.y1, obj.y2);
        var dy = (targetTopPx - top * displayH) / displayH;
        obj.y1 += dy;
        obj.y2 += dy;
    }

    function setObjX(obj, targetLeftPx, b) {
        if (obj.type === 'text') {
            obj.x = (targetLeftPx + b.w / 2) / displayW;
            return;
        }
        var left = Math.min(obj.x1, obj.x2);
        var dx = (targetLeftPx - left * displayW) / displayW;
        obj.x1 += dx;
        obj.x2 += dx;
    }

    // ── Color picker presets & sync ─────────────────────────────
    function setColorValue(pickerId, val) {
        var hex = document.getElementById(pickerId + '-hex');
        if (hex) hex.textContent = val;
        var fill = document.querySelector('[data-swatch="' + pickerId + '"]');
        if (fill) fill.style.background = val;
        var input = document.getElementById(pickerId);
        if (input) input.value = val;
    }

    function initColorPickers() {
        document.querySelectorAll('.tc-meme-csw').forEach(function (btn) {
            btn.style.background = btn.getAttribute('data-val');
            btn.addEventListener('click', function () {
                var palette = btn.closest('[data-palette]');
                var pickerId = palette ? palette.getAttribute('data-palette') : null;
                if (!pickerId) return;
                var sel = (selectedIndex >= 0 && selectedIndex < objects.length) ? objects[selectedIndex] : null;
                var val = btn.getAttribute('data-val');
                if (pickerId === 'tc-meme-color') {
                    if (sel) { sel.color = val; }
                } else if (pickerId === 'tc-meme-stroke') {
                    if (sel && sel.type === 'text') { sel.strokeColor = val; }
                }
                setColorValue(pickerId, val);
                renderPreview();
            });
        });
        if (colorInput) setColorValue('tc-meme-color', colorInput.value);
        if (strokeInput) setColorValue('tc-meme-stroke', strokeInput.value);
    }

    if (document.querySelector('.tc-meme-csw')) {
        initColorPickers();
    }

    // ── Drop zone ───────────────────────────────────────────────
    TCTP.initDropZone('tc-meme-drop', 'tc-meme-drop-input', function (f) {
        if (!f.type.match(/^image\/(jpeg|png|webp|gif)$/)) {
            TCTP.toast('Please select a JPG, PNG, WebP, or GIF image.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        memeBlob = null;
        if (dlBtn) dlBtn.style.display = 'none';
        TCTP.showFileRow('tc-meme-file', f);
        loadImage(f);
    }, 'image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif');

    var removeBtn = document.querySelector('#tc-meme-file .tc-x');
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            file = null;
            imgEl = null;
            memeBlob = null;
            objects = [];
            selectedIndex = -1;
            if (dlBtn) dlBtn.style.display = 'none';
            TCTP.hideFileRow('tc-meme-file');
            resetPreview();
            syncBlockList();
            TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Idle');
            var resultEl = document.getElementById('tc-meme-result');
            if (resultEl) resultEl.innerHTML = '<p class="tc-meme-result-empty">Your meme will appear here after you click Generate.</p>';
        });
    }

    // ── Reset / placeholder ─────────────────────────────────────
    function resetPreview() {
        if (canvas) {
            canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
            canvas.width = 300;
            canvas.height = 150;
            canvas.style.display = 'none';
        }
        if (placeholder) placeholder.style.display = '';
        var stat = document.getElementById('tc-meme-stat-orig');
        if (stat) stat.textContent = '-';
        var tstat = document.getElementById('tc-meme-stat-text');
        if (tstat) tstat.textContent = '0 elems, 0 chars';
    }

    // ── Load image ──────────────────────────────────────────────
    function loadImage(f) {
        var reader = new FileReader();
        reader.onload = function (e) {
            imgEl = new Image();
            imgEl.onload = function () {
                naturalW = imgEl.naturalWidth;
                naturalH = imgEl.naturalHeight;

                if (!canvas) return;
                canvas.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';

                var wrapW = previewWrap ? previewWrap.clientWidth : 600;
                var maxH = 400;
                displayW = Math.min(wrapW, naturalW);
                displayH = Math.round(displayW * (naturalH / naturalW));
                if (displayH > maxH) {
                    displayH = maxH;
                    displayW = Math.round(displayH * (naturalW / naturalH));
                }

                canvas.width = displayW;
                canvas.height = displayH;

                renderPreview();

                var origStat = document.getElementById('tc-meme-stat-orig');
                if (origStat) origStat.textContent = naturalW + '\u00D7' + naturalH;

                TCTP.updateResultPanel(naturalW + '\u00D7' + naturalH, '\u2014', '\u2014', 'Ready');
                TCTP.switchToOriginalTab();

                TCTP.toast('Image loaded! Add text or draw with the tools.', '\u2705');
            };
            imgEl.src = e.target.result;

            var origPrev = document.getElementById('tc-preview-orig');
            if (origPrev) {
                TCTP.showOriginalPreview(e.target.result);
            }
        };
        reader.readAsDataURL(f);
    }

    // ── Format ──────────────────────────────────────────────────
    fmtCards.forEach(function (btn) {
        btn.addEventListener('click', function () {
            fmtCards.forEach(function (b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            outputFormat = btn.getAttribute('data-val') || 'original';
        });
    });

    // ── No-element helper ───────────────────────────────────────
    function showNoTextMessage() {
        var el = document.getElementById('tc-meme-msg');
        if (!el) {
            el = document.createElement('div');
            el.id = 'tc-meme-msg';
            el.className = 'tc-inline-msg';
            el.textContent = 'No elements yet — add text blocks or draw an arrow, box, or line, then click Generate.';
            var actions = document.querySelector('#tc-meme-apply');
            if (actions && actions.parentNode) {
                actions.parentNode.insertBefore(el, actions.parentNode.querySelector('#tc-meme-download'));
            }
        }
        el.style.display = '';
        el.classList.add('show');
        var chip = document.getElementById('tc-status-chip');
        if (chip) chip.textContent = 'Add elements';
    }

    function clearNoTextMessage() {
        var el = document.getElementById('tc-meme-msg');
        if (el) el.style.display = 'none';
        var chip = document.getElementById('tc-status-chip');
        if (chip && chip.textContent === 'Add elements') chip.textContent = 'Idle';
    }

    // ── Generate meme ───────────────────────────────────────────
    if (applyBtn) {
        applyBtn.addEventListener('click', function () {
            if (!imgEl) {
                TCTP.toast('Please upload an image first.', '\u26A0\uFE0F');
                return;
            }
            if (objects.length === 0) {
                TCTP.toast('Please add at least one element.', '\u26A0\uFE0F');
                showNoTextMessage();
                return;
            }
            clearNoTextMessage();

            TCTP.showProgress('tc-meme-progress', 50, 'Creating meme...');

            var outCanvas = document.createElement('canvas');
            outCanvas.width = naturalW;
            outCanvas.height = naturalH;
            var ctx = outCanvas.getContext('2d');
            ctx.drawImage(imgEl, 0, 0, naturalW, naturalH);

            for (var i = 0; i < objects.length; i++) {
                var obj = objects[i];
                if (obj.type === 'text') drawTextObj(ctx, obj, naturalW, naturalH);
                else drawShapeObj(ctx, obj, naturalW, naturalH);
            }

            var mime = outputFormat === 'original' ? (file ? file.type : 'image/png') : outputFormat;
            var quality = 0.92;

            outCanvas.toBlob(function (blob) {
                memeBlob = blob;
                TCTP.setProgress('tc-meme-progress', 100, 'Done!');
                setTimeout(function () { TCTP.hideProgress('tc-meme-progress'); }, 600);

                var outStat = document.getElementById('tc-meme-stat-out');
                if (outStat) outStat.textContent = TCTP.formatSize(blob.size);

                var saved = file ? (((1 - blob.size / file.size) * 100).toFixed(1) + '%') : '\u2014';
                TCTP.updateResultPanel(naturalW + '\u00D7' + naturalH, TCTP.formatSize(blob.size), saved, 'Done');
                TCTP.switchToResultTab();

                var resultEl = document.getElementById('tc-meme-result');
                if (resultEl) {
                    resultEl.innerHTML = '';
                    var url = URL.createObjectURL(blob);
                    var img = document.createElement('img');
                    img.src = url;
                    img.alt = 'Generated meme';
                    resultEl.appendChild(img);
                }

                if (dlBtn) {
                    dlBtn.style.display = '';
                    dlBtn.onclick = function () {
                        var a = document.createElement('a');
                        a.href = URL.createObjectURL(blob);
                        a.download = 'meme.' + (mime === 'image/jpeg' ? 'jpg' : 'png');
                        a.click();
                        URL.revokeObjectURL(a.href);
                    };
                }

                TCTP.toast('Meme created!', '\u2705');
            }, mime, quality);
        });
    }

    // ── Wire up canvas pointer events ───────────────────────────
    if (canvas) {
        canvas.addEventListener('pointerdown', function (e) {
            e.preventDefault();
            try { canvas.setPointerCapture(e.pointerId); } catch (err) { /* ignore */ }
            onPointerDown(e);
        });
        canvas.addEventListener('pointermove', function (e) {
            onPointerMove(e);
        });
        canvas.addEventListener('pointerup', function (e) {
            onPointerUp(e);
        });
        canvas.addEventListener('pointercancel', function () {
            if (dragState && dragState.mode === 'draw' && dragState.preview) {
                objects.pop();
            }
            dragState = null;
            resetTool();
            renderPreview();
            syncBlockList();
            syncStyleUI();
        });
    }

    // ── Clear all ───────────────────────────────────────────────
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            file = null;
            imgEl = null;
            memeBlob = null;
            objects = [];
            selectedIndex = -1;
            if (dlBtn) dlBtn.style.display = 'none';
            TCTP.hideFileRow('tc-meme-file');
            if (fontSlider) fontSlider.value = 48;
            if (fontVal) fontVal.textContent = '48';
            if (strokeSlider) strokeSlider.value = 3;
            if (strokeVal) strokeVal.textContent = '3';
            if (colorInput) colorInput.value = '#ffffff';
            var ch = document.getElementById('tc-meme-color-hex');
            if (ch) ch.textContent = '#ffffff';
            var cFill = document.querySelector('[data-swatch="tc-meme-color"]');
            if (cFill) cFill.style.background = '#ffffff';
            if (strokeInput) strokeInput.value = '#000000';
            var sh = document.getElementById('tc-meme-stroke-hex');
            if (sh) sh.textContent = '#000000';
            var sFill = document.querySelector('[data-swatch="tc-meme-stroke"]');
            if (sFill) sFill.style.background = '#000000';
            document.querySelectorAll('.tc-meme-options [data-group="meme-font"] .tc-btn').forEach(function (b) {
                b.classList.remove('sel');
                if (b.getAttribute('data-val') === 'Impact, Arial Black, sans-serif') b.classList.add('sel');
            });
            defFont = 'Impact, Arial Black, sans-serif';
            fmtCards.forEach(function (b) {
                b.classList.remove('sel');
                if (b.getAttribute('data-val') === 'original') b.classList.add('sel');
            });
            outputFormat = 'original';
            resetPreview();
            syncBlockList();
            syncStyleUI();
            TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Ready');
            var resultEl = document.getElementById('tc-meme-result');
            if (resultEl) resultEl.innerHTML = '<p class="tc-meme-result-empty">Your meme will appear here after you click Generate.</p>';
            var origP = document.getElementById('tc-preview-orig');
            if (origP) origP.innerHTML = '<div class="tc-meme-preview-empty"><p>Upload an image to see preview</p></div>';
            TCTP.switchToOriginalTab();
            TCTP.toast('Cleared everything. Start fresh!', '\u2705');
        });
    }

    // Test support: read-only snapshot of the selected element (used by automated checks)
    window.__memeSel = function () {
        if (selectedIndex < 0 || selectedIndex >= objects.length) return null;
        var o = objects[selectedIndex];
        var b = getBounds(o, displayW, displayH);
        return {
            type: o.type, fontSize: o.fontSize, x: o.x, y: o.y,
            bx: b.x, by: b.y, bw: b.w, bh: b.h,
            handles: getHandles(displayW, displayH)
        };
    };
})();
