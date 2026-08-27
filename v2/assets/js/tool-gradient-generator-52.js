/**
 * Gradient Generator — Create CSS gradients with multiple color stops.
 */
(function () {
    'use strict';
    if (!document.getElementById('gg-angle')) return;

    var type = 'linear';
    var colorContainer = document.getElementById('gg-colors');
    var angleSlider = document.getElementById('gg-angle');
    var angleVal = document.getElementById('gg-angle-val');
    var preview = document.getElementById('gg-preview');
    var cssOutput = document.getElementById('gg-css');
    var resultPanel = document.getElementById('gg-result');
    var statusEl = document.getElementById('gg-status');
    var generateBtn = document.getElementById('gg-generate');
    var addColorBtn = document.getElementById('gg-add-color');
    var copyBtn = document.getElementById('gg-copy');

    TCTP.initModeGroup('.tc-modes[data-group="gg-type"]', function (val) {
        type = val;
    });

    angleSlider.addEventListener('input', function () {
        angleVal.textContent = angleSlider.value + 'deg';
    });

    addColorBtn.addEventListener('click', function () {
        var rows = colorContainer.querySelectorAll('.gg-color-row');
        var idx = rows.length + 1;
        var hex = '#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0');
        var row = document.createElement('div');
        row.className = 'tc-input-group gg-color-row';
        row.style.cssText = 'display:flex;gap:8px;align-items:center';
        row.innerHTML =
            '<input type="color" class="tc-input gg-color-input" value="' + hex + '" style="width:50px;height:40px;padding:2px;cursor:pointer">' +
            '<input type="text" class="tc-input gg-stop-input" placeholder="' + (idx * 33) + '%" value="' + (idx * 33) + '%" style="width:80px">' +
            '<button class="tc-btn tc-btn--outline gg-remove" style="padding:4px 8px;font-size:12px">X</button>';
        row.querySelector('.gg-remove').addEventListener('click', function () {
            row.remove();
        });
        colorContainer.appendChild(row);
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('gg-remove')) {
            e.target.closest('.gg-color-row').remove();
        }
    });

    function getColors() {
        var rows = colorContainer.querySelectorAll('.gg-color-row');
        var colors = [];
        rows.forEach(function (row) {
            var colorInput = row.querySelector('[type="color"]');
            var stopInput = row.querySelector('[type="text"]');
            if (colorInput && stopInput) {
                colors.push({ color: colorInput.value, stop: stopInput.value || 'auto' });
            }
        });
        return colors;
    }

    function generate() {
        var colors = getColors();
        if (colors.length < 2) { TCTP.toast('Add at least 2 colors.', '\u26A0\uFE0F'); return; }

        var stops = colors.map(function (c) {
            return c.color + (c.stop !== 'auto' ? ' ' + c.stop : '');
        }).join(', ');

        var css = '';
        if (type === 'linear') {
            css = 'background: linear-gradient(' + angleSlider.value + 'deg, ' + stops + ');';
        } else if (type === 'radial') {
            css = 'background: radial-gradient(circle, ' + stops + ');';
        } else {
            css = 'background: conic-gradient(' + stops + ');';
        }

        preview.style.background = css.replace('background: ', '');
        cssOutput.textContent = css;
        resultPanel.style.display = '';
        statusEl.textContent = type + ' gradient';
        TCTP.toast('Gradient generated!');
    }

    generateBtn.addEventListener('click', generate);
    copyBtn.addEventListener('click', function () { TCTP.copyText(cssOutput.textContent, 'CSS'); });

    generate();
})();
