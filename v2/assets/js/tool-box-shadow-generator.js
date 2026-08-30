/**
 * Box Shadow Generator — Tool JS
 * Visual CSS box-shadow builder with live preview.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var xInput = document.getElementById('tc-bs-x');
    if (!xInput) return;

    var yInput = document.getElementById('tc-bs-y');
    var blurInput = document.getElementById('tc-bs-blur');
    var spreadInput = document.getElementById('tc-bs-spread');
    var opacityInput = document.getElementById('tc-bs-opacity');
    var colorInput = document.getElementById('tc-bs-color');
    var colorHex = document.getElementById('tc-bs-color-hex');
    var xVal = document.getElementById('tc-bs-x-val');
    var yVal = document.getElementById('tc-bs-y-val');
    var blurVal = document.getElementById('tc-bs-blur-val');
    var spreadVal = document.getElementById('tc-bs-spread-val');
    var opacityVal = document.getElementById('tc-bs-opacity-val');
    var previewBox = document.getElementById('tc-bs-preview-box');
    var codeOutput = document.getElementById('tc-bs-code');
    var layersContainer = document.getElementById('tc-bs-layers');

    var currentStyle = 'outset';
    var layers = [];
    var layerIndex = 0;

    var presets = {
        soft:       { x: 0, y: 4, blur: 12, spread: 0, opacity: 25, color: '#000000', style: 'outset' },
        hard:       { x: 4, y: 4, blur: 0, spread: 0, opacity: 30, color: '#000000', style: 'outset' },
        glow:       { x: 0, y: 0, blur: 20, spread: 0, opacity: 50, color: '#0b1220', style: 'outset' },
        neumorphism: { x: 6, y: 6, blur: 12, spread: 0, opacity: 15, color: '#000000', style: 'outset' },
        floating:   { x: 0, y: 8, blur: 24, spread: 0, opacity: 20, color: '#000000', style: 'outset' },
        inset:      { x: 0, y: 2, blur: 4, spread: 0, opacity: 25, color: '#000000', style: 'inset' }
    };

    // ── Helpers ──────────────────────────────────────────────

    function hexToRgba(hex, alpha) {
        hex = hex.replace('#', '');
        if (hex.length === 3) hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
        var r = parseInt(hex.substring(0, 2), 16);
        var g = parseInt(hex.substring(2, 4), 16);
        var b = parseInt(hex.substring(4, 6), 16);
        return 'rgba(' + r + ',' + g + ',' + b + ',' + (alpha / 100).toFixed(2) + ')';
    }

    function buildSingleShadow(cfg) {
        var inset = cfg.style === 'inset' ? 'inset ' : '';
        return inset + cfg.x + 'px ' + cfg.y + 'px ' + cfg.blur + 'px ' + cfg.spread + 'px ' + hexToRgba(cfg.color, cfg.opacity);
    }

    function getCurrentConfig() {
        return {
            x: parseInt(xInput.value),
            y: parseInt(yInput.value),
            blur: parseInt(blurInput.value),
            spread: parseInt(spreadInput.value),
            opacity: parseInt(opacityInput.value),
            color: colorInput.value,
            style: currentStyle
        };
    }

    // ── Update ───────────────────────────────────────────────

    function updatePreview() {
        var cfg = getCurrentConfig();
        xVal.textContent = cfg.x + 'px';
        yVal.textContent = cfg.y + 'px';
        blurVal.textContent = cfg.blur + 'px';
        spreadVal.textContent = cfg.spread + 'px';
        opacityVal.textContent = cfg.opacity + '%';

        var shadow = buildSingleShadow(cfg);
        previewBox.style.boxShadow = shadow;
        codeOutput.value = 'box-shadow: ' + shadow + ';';
    }

    function updateAllLayersPreview() {
        if (layers.length === 0) {
            updatePreview();
            return;
        }
        var shadows = layers.map(function (l) { return buildSingleShadow(l); });
        shadows.push(buildSingleShadow(getCurrentConfig()));
        previewBox.style.boxShadow = shadows.join(', ');
        codeOutput.value = 'box-shadow: ' + shadows.join(',\n       ') + ';';
    }

    function renderLayers() {
        if (!layersContainer) return;
        layersContainer.innerHTML = '';
        layers.forEach(function (layer, idx) {
            var div = document.createElement('div');
            div.className = 'tc-bs-layer-item';
            div.innerHTML =
                '<div class="tc-bs-layer-swatch" style="background:' + buildSingleShadow(layer) + '"></div>' +
                '<span class="tc-bs-layer-label">Layer ' + (idx + 1) + ': ' + layer.x + 'px ' + layer.y + 'px ' + layer.blur + 'px ' + layer.spread + 'px</span>' +
                '<button class="tc-btn tc-btn--ghost tc-bs-layer-del" data-idx="' + idx + '" type="button"><i class="fa-solid fa-xmark"></i></button>';
            layersContainer.appendChild(div);
        });

        layersContainer.querySelectorAll('.tc-bs-layer-del').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var i = parseInt(btn.getAttribute('data-idx'));
                layers.splice(i, 1);
                renderLayers();
                updateAllLayersPreview();
            });
        });
    }

    // ── Presets ──────────────────────────────────────────────

    document.querySelectorAll('.tc-bs-preset-cards .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.tc-bs-preset-cards .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            var key = card.getAttribute('data-preset');
            var p = presets[key];
            if (!p) return;
            xInput.value = p.x;
            yInput.value = p.y;
            blurInput.value = p.blur;
            spreadInput.value = p.spread;
            opacityInput.value = p.opacity;
            colorInput.value = p.color;
            colorHex.value = p.color;

            // Set style
            currentStyle = p.style;
            document.querySelectorAll('.tc-bs-style-cards .tc-rsz-mode-card').forEach(function (sc) {
                sc.classList.toggle('sel', sc.getAttribute('data-val') === currentStyle);
            });

            updatePreview();
        });
    });

    // ── Style toggle ─────────────────────────────────────────

    document.querySelectorAll('.tc-bs-style-cards .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.tc-bs-style-cards .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            currentStyle = card.getAttribute('data-val') || 'outset';
            updatePreview();
        });
    });

    // ── Sliders ──────────────────────────────────────────────

    [xInput, yInput, blurInput, spreadInput, opacityInput].forEach(function (el) {
        el.addEventListener('input', updatePreview);
    });

    // ── Color ────────────────────────────────────────────────

    colorInput.addEventListener('input', function () {
        colorHex.value = colorInput.value;
        updatePreview();
    });

    colorHex.addEventListener('input', function () {
        if (/^#[0-9a-f]{6}$/i.test(colorHex.value)) {
            colorInput.value = colorHex.value;
            updatePreview();
        }
    });

    // ── Add layer ────────────────────────────────────────────

    var addLayerBtn = document.getElementById('tc-bs-add-layer');
    if (addLayerBtn) {
        addLayerBtn.addEventListener('click', function () {
            layers.push(getCurrentConfig());
            renderLayers();
            updateAllLayersPreview();
            TCTP.toast('Shadow layer added', '\u2705');
        });
    }

    // ── Clear layers ─────────────────────────────────────────

    var clearBtn = document.getElementById('tc-bs-clear-layers');
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            layers = [];
            renderLayers();
            updatePreview();
            TCTP.toast('All layers cleared', '\ud83d\uddd1\ufe0f');
        });
    }

    // ── Copy CSS ─────────────────────────────────────────────

    var copyCssBtn = document.getElementById('tc-bs-copy-css');
    if (copyCssBtn) {
        copyCssBtn.addEventListener('click', function () {
            TCTP.copyText(codeOutput.value);
            TCTP.toast('CSS copied!', '\u2705');
        });
    }

    var copyAllBtn = document.getElementById('tc-bs-copy-all');
    if (copyAllBtn) {
        copyAllBtn.addEventListener('click', function () {
            TCTP.copyText(codeOutput.value);
            TCTP.toast('All layers CSS copied!', '\u2705');
        });
    }

    // ── Init ─────────────────────────────────────────────────

    updatePreview();
})();
