/**
 * CSS Gradient Previewer — Tool JS
 * Visual CSS gradient builder with live preview.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var preview = document.getElementById('tc-grad-preview');
    if (!preview) return;

    var angleInput = document.getElementById('tc-grad-angle');
    var angleVal = document.getElementById('tc-grad-angle-val');
    var codeOutput = document.getElementById('tc-grad-code');
    var stopsContainer = document.getElementById('tc-grad-stops');
    var angleSection = document.querySelector('.tc-grad-angle-section');
    var shapeSection = document.querySelector('.tc-grad-shape-section');

    var gradType = 'linear';
    var gradShape = 'circle';
    var gradPosition = 'top';
    var stops = [
        { color: '#667eea', position: 0 },
        { color: '#764ba2', position: 100 }
    ];
    var stopIdCounter = 0;

    // ── Presets ──────────────────────────────────────────────

    var presetData = [
        { colors: '#667eea,#764ba2', angle: 135 },
        { colors: '#f093fb,#f5576c', angle: 135 },
        { colors: '#4facfe,#00f2fe', angle: 135 },
        { colors: '#43e97b,#38f9d7', angle: 135 },
        { colors: '#fa709a,#fee140', angle: 135 },
        { colors: '#a18cd1,#fbc2eb', angle: 135 },
        { colors: '#fccb90,#d57eeb', angle: 135 },
        { colors: '#e0c3fc,#8ec5fc', angle: 135 },
        { colors: '#f5576c,#ff6b6b', angle: 135 },
        { colors: '#0ba360,#3cba92', angle: 135 }
    ];

    var presetSwatches = document.querySelectorAll('.tc-grad-preset-swatch');
    presetSwatches.forEach(function (sw, i) {
        var p = presetData[i];
        if (p) {
            sw.style.background = 'linear-gradient(135deg, ' + p.colors + ')';
        }
        sw.addEventListener('click', function () {
            if (!p) return;
            var colors = p.colors.split(',');
            stops = colors.map(function (c, idx) {
                return { color: c.trim(), position: Math.round((idx / (colors.length - 1)) * 100) };
            });
            angleInput.value = p.angle;
            renderStops();
            updateGradient();
        });
    });

    // ── Gradient type ────────────────────────────────────────

    document.querySelectorAll('.tc-grad-type-cards .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.tc-grad-type-cards .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            gradType = card.getAttribute('data-val') || 'linear';
            angleSection.style.display = gradType === 'linear' ? '' : 'none';
            shapeSection.style.display = gradType === 'radial' ? '' : 'none';
            updateGradient();
        });
    });

    // ── Shape ────────────────────────────────────────────────

    document.querySelectorAll('.tc-grad-shape-cards .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.tc-grad-shape-cards .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            gradShape = card.getAttribute('data-val') || 'circle';
            updateGradient();
        });
    });

    // ── Position ─────────────────────────────────────────────

    document.querySelectorAll('.tc-grad-pos-cards .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.tc-grad-pos-cards .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            gradPosition = card.getAttribute('data-val') || 'top';
            updateGradient();
        });
    });

    // ── Angle slider ─────────────────────────────────────────

    angleInput.addEventListener('input', function () {
        angleVal.textContent = angleInput.value + 'deg';
        updateGradient();
    });

    // ── Stops management ─────────────────────────────────────

    function renderStops() {
        if (!stopsContainer) return;
        stopsContainer.innerHTML = '';
        stops.forEach(function (stop, idx) {
            var id = 'tc-grad-stop-' + (++stopIdCounter);
            var div = document.createElement('div');
            div.className = 'tc-grad-stop-row';
            div.innerHTML =
                '<div class="tc-input-group">' +
                    '<label class="tc-label">Color ' + (idx + 1) + '</label>' +
                    '<div class="tc-grad-stop-inputs">' +
                        '<input type="color" class="tc-grad-stop-color" data-idx="' + idx + '" value="' + stop.color + '">' +
                        '<input type="range" class="tc-bs-range tc-grad-stop-pos" data-idx="' + idx + '" min="0" max="100" value="' + stop.position + '">' +
                        '<span class="tc-bs-val tc-grad-stop-val">' + stop.position + '%</span>' +
                    '</div>' +
                '</div>' +
                (stops.length > 2 ? '<button class="tc-btn tc-btn--ghost tc-grad-stop-del" data-idx="' + idx + '" type="button"><i class="fa-solid fa-xmark"></i></button>' : '');
            stopsContainer.appendChild(div);
        });

        // Bind events
        stopsContainer.querySelectorAll('.tc-grad-stop-color').forEach(function (inp) {
            inp.addEventListener('input', function () {
                var i = parseInt(inp.getAttribute('data-idx'));
                stops[i].color = inp.value;
                updateGradient();
            });
        });

        stopsContainer.querySelectorAll('.tc-grad-stop-pos').forEach(function (inp) {
            inp.addEventListener('input', function () {
                var i = parseInt(inp.getAttribute('data-idx'));
                stops[i].position = parseInt(inp.value);
                var valEl = inp.parentNode.querySelector('.tc-grad-stop-val');
                if (valEl) valEl.textContent = inp.value + '%';
                updateGradient();
            });
        });

        stopsContainer.querySelectorAll('.tc-grad-stop-del').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var i = parseInt(btn.getAttribute('data-idx'));
                stops.splice(i, 1);
                renderStops();
                updateGradient();
            });
        });
    }

    // Add stop
    var addStopBtn = document.getElementById('tc-grad-add-stop');
    if (addStopBtn) {
        addStopBtn.addEventListener('click', function () {
            var lastPos = stops.length > 0 ? stops[stops.length - 1].position : 50;
            var newPos = Math.min(100, lastPos + 10);
            stops.push({ color: '#ffffff', position: newPos });
            renderStops();
            updateGradient();
        });
    }

    // ── Build CSS ────────────────────────────────────────────

    function buildCSS() {
        var sorted = stops.slice().sort(function (a, b) { return a.position - b.position; });
        var colorList = sorted.map(function (s) { return s.color + ' ' + s.position + '%'; }).join(', ');

        if (gradType === 'linear') {
            return 'background: linear-gradient(' + angleInput.value + 'deg, ' + colorList + ');';
        } else if (gradType === 'radial') {
            return 'background: radial-gradient(' + gradShape + ' at ' + gradPosition + ', ' + colorList + ');';
        } else {
            return 'background: conic-gradient(from ' + angleInput.value + 'deg at ' + gradPosition + ', ' + colorList + ');';
        }
    }

    function buildPreviewCSS() {
        var sorted = stops.slice().sort(function (a, b) { return a.position - b.position; });
        var colorList = sorted.map(function (s) { return s.color + ' ' + s.position + '%'; }).join(', ');

        if (gradType === 'linear') {
            return 'linear-gradient(' + angleInput.value + 'deg, ' + colorList + ')';
        } else if (gradType === 'radial') {
            return 'radial-gradient(' + gradShape + ' at ' + gradPosition + ', ' + colorList + ')';
        } else {
            return 'conic-gradient(from ' + angleInput.value + 'deg at ' + gradPosition + ', ' + colorList + ')';
        }
    }

    // ── Update ───────────────────────────────────────────────

    function updateGradient() {
        var css = buildCSS();
        var previewCSS = buildPreviewCSS();
        preview.style.background = previewCSS;
        if (codeOutput) codeOutput.value = css;
    }

    // ── Copy CSS ─────────────────────────────────────────────

    var copyBtn = document.getElementById('tc-grad-copy-css');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            TCTP.copyText(codeOutput.value);
            TCTP.toast('CSS copied!', '\u2705');
        });
    }

    // ── Init ─────────────────────────────────────────────────

    renderStops();
    updateGradient();
})();
