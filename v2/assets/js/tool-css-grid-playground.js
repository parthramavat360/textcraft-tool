/**
 * CSS Grid Playground — Tool JS
 * Interactive CSS Grid layout visualizer.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var gridContainer = document.getElementById('tc-grid-preview-container');
    if (!gridContainer) return;

    var colsInput = document.getElementById('tc-grid-cols');
    var rowsInput = document.getElementById('tc-grid-rows');
    var gapInput = document.getElementById('tc-grid-gap');
    var gapVal = document.getElementById('tc-grid-gap-val');
    var codeOutput = document.getElementById('tc-grid-code');
    var itemsList = document.getElementById('tc-grid-items-list');

    var itemColors = ['#0b1220', '#dc2626', '#16a34a', '#ea580c', '#7c3aed', '#0891b2', '#be185d', '#65a30d', '#d97706', '#4f46e5'];
    var items = [];
    var itemIdCounter = 0;

    // ── Presets ──────────────────────────────────────────────

    document.querySelectorAll('.tc-grid-preset-cards .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.tc-grid-preset-cards .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            colsInput.value = card.getAttribute('data-cols');
            rowsInput.value = card.getAttribute('data-rows');
            updatePreview();
        });
    });

    // ── Container inputs ─────────────────────────────────────

    [colsInput, rowsInput].forEach(function (inp) {
        inp.addEventListener('input', updatePreview);
    });

    gapInput.addEventListener('input', function () {
        gapVal.textContent = gapInput.value + 'px';
        updatePreview();
    });

    // ── Items ────────────────────────────────────────────────

    function addItem() {
        var idx = items.length;
        items.push({
            id: ++itemIdCounter,
            gridColumn: 'auto',
            gridRow: 'auto',
            color: itemColors[idx % itemColors.length],
            label: 'Item ' + (idx + 1)
        });
        renderItems();
        updatePreview();
    }

    function removeItem() {
        if (items.length === 0) return;
        items.pop();
        renderItems();
        updatePreview();
    }

    function renderItems() {
        if (!itemsList) return;
        itemsList.innerHTML = '';
        items.forEach(function (item, idx) {
            var div = document.createElement('div');
            div.className = 'tc-fb-item-row';
            div.innerHTML =
                '<div class="tc-fb-item-swatch" style="background:' + item.color + '"></div>' +
                '<span class="tc-fb-item-label">' + item.label + '</span>' +
                '<div class="tc-fb-item-props">' +
                    '<div class="tc-input-group"><label class="tc-label">column</label>' +
                        '<select class="tc-input tc-grid-item-select" data-idx="' + idx + '" data-prop="gridColumn">' +
                            '<option value="auto"' + (item.gridColumn === 'auto' ? ' selected' : '') + '>auto</option>' +
                            '<option value="span 2"' + (item.gridColumn === 'span 2' ? ' selected' : '') + '>span 2</option>' +
                            '<option value="span 3"' + (item.gridColumn === 'span 3' ? ' selected' : '') + '>span 3</option>' +
                            '<option value="1 / 3"' + (item.gridColumn === '1 / 3' ? ' selected' : '') + '>1 / 3</option>' +
                            '<option value="2 / 4"' + (item.gridColumn === '2 / 4' ? ' selected' : '') + '>2 / 4</option>' +
                            '<option value="1 / 4"' + (item.gridColumn === '1 / 4' ? ' selected' : '') + '>1 / 4 (full)</option>' +
                        '</select></div>' +
                    '<div class="tc-input-group"><label class="tc-label">row</label>' +
                        '<select class="tc-input tc-grid-item-select" data-idx="' + idx + '" data-prop="gridRow">' +
                            '<option value="auto"' + (item.gridRow === 'auto' ? ' selected' : '') + '>auto</option>' +
                            '<option value="span 2"' + (item.gridRow === 'span 2' ? ' selected' : '') + '>span 2</option>' +
                            '<option value="1 / 3"' + (item.gridRow === '1 / 3' ? ' selected' : '') + '>1 / 3</option>' +
                        '</select></div>' +
                '</div>';
            itemsList.appendChild(div);
        });

        itemsList.querySelectorAll('.tc-grid-item-select').forEach(function (sel) {
            sel.addEventListener('change', function () {
                var i = parseInt(sel.getAttribute('data-idx'));
                var prop = sel.getAttribute('data-prop');
                items[i][prop] = sel.value;
                updatePreview();
            });
        });
    }

    // ── Buttons ──────────────────────────────────────────────

    document.getElementById('tc-grid-add-item').addEventListener('click', addItem);
    document.getElementById('tc-grid-remove-item').addEventListener('click', removeItem);

    // ── Update preview ───────────────────────────────────────

    function updatePreview() {
        gridContainer.style.display = 'grid';
        gridContainer.style.gridTemplateColumns = colsInput.value;
        gridContainer.style.gridTemplateRows = rowsInput.value;
        gridContainer.style.gap = gapInput.value + 'px';
        gridContainer.style.minHeight = '200px';

        gridContainer.innerHTML = '';
        items.forEach(function (item, idx) {
            var div = document.createElement('div');
            div.className = 'tc-grid-preview-item';
            div.style.background = item.color;
            if (item.gridColumn !== 'auto') div.style.gridColumn = item.gridColumn;
            if (item.gridRow !== 'auto') div.style.gridRow = item.gridRow;
            div.textContent = item.label;
            gridContainer.appendChild(div);
        });

        // Build CSS code
        var css = '.grid-container {\n';
        css += '  display: grid;\n';
        css += '  grid-template-columns: ' + colsInput.value + ';\n';
        css += '  grid-template-rows: ' + rowsInput.value + ';\n';
        css += '  gap: ' + gapInput.value + 'px;\n';
        css += '}\n\n';

        items.forEach(function (item, idx) {
            var itemCss = '.grid-item-' + (idx + 1) + ' {\n';
            if (item.gridColumn !== 'auto') itemCss += '  grid-column: ' + item.gridColumn + ';\n';
            if (item.gridRow !== 'auto') itemCss += '  grid-row: ' + item.gridRow + ';\n';
            itemCss += '}\n';
            css += itemCss;
        });

        codeOutput.value = css;
    }

    // ── Copy CSS ─────────────────────────────────────────────

    document.getElementById('tc-grid-copy-css').addEventListener('click', function () {
        TCTP.copyText(codeOutput.value);
        TCTP.toast('CSS copied!', '\u2705');
    });

    // ── Init ─────────────────────────────────────────────────

    addItem();
    addItem();
    addItem();
})();
