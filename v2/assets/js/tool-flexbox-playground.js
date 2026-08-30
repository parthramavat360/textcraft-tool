/**
 * Flexbox Playground — Tool JS
 * Interactive flexbox layout visualizer.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var container = document.getElementById('tc-fb-preview-container');
    if (!container) return;

    var codeOutput = document.getElementById('tc-fb-code');
    var itemsList = document.getElementById('tc-fb-items-list');
    var gapInput = document.getElementById('tc-fb-gap');
    var gapVal = document.getElementById('tc-fb-gap-val');

    var itemColors = ['#0b1220', '#dc2626', '#16a34a', '#ea580c', '#7c3aed', '#0891b2', '#be185d', '#65a30d'];
    var items = [];
    var itemIdCounter = 0;

    var containerProps = {
        display: 'flex',
        flexDirection: 'row',
        flexWrap: 'nowrap',
        justifyContent: 'flex-start',
        alignItems: 'stretch',
        gap: '10px'
    };

    // ── Container property cards ─────────────────────────────

    function bindContainerCards(selector, prop) {
        document.querySelectorAll(selector + ' .tc-rsz-mode-card').forEach(function (card) {
            card.addEventListener('click', function () {
                document.querySelectorAll(selector + ' .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
                card.classList.add('sel');
                containerProps[prop] = card.getAttribute('data-val');
                updatePreview();
            });
        });
    }

    bindContainerCards('.tc-fb-display-cards', 'display');
    bindContainerCards('.tc-fb-dir-cards', 'flexDirection');
    bindContainerCards('.tc-fb-wrap-cards', 'flexWrap');
    bindContainerCards('.tc-fb-justify-cards', 'justifyContent');
    bindContainerCards('.tc-fb-align-cards', 'alignItems');

    // ── Gap slider ───────────────────────────────────────────

    gapInput.addEventListener('input', function () {
        containerProps.gap = gapInput.value + 'px';
        gapVal.textContent = gapInput.value + 'px';
        updatePreview();
    });

    // ── Items ────────────────────────────────────────────────

    function addItem() {
        var idx = items.length;
        items.push({
            id: ++itemIdCounter,
            flexGrow: 0,
            flexShrink: 1,
            flexBasis: 'auto',
            alignSelf: 'auto',
            order: 0,
            color: itemColors[idx % itemColors.length],
            label: 'Item ' + idx
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
                    '<div class="tc-input-group"><label class="tc-label">grow</label>' +
                        '<input type="number" class="tc-input tc-fb-item-input" data-idx="' + idx + '" data-prop="flexGrow" min="0" max="10" value="' + item.flexGrow + '"></div>' +
                    '<div class="tc-input-group"><label class="tc-label">shrink</label>' +
                        '<input type="number" class="tc-input tc-fb-item-input" data-idx="' + idx + '" data-prop="flexShrink" min="0" max="10" value="' + item.flexShrink + '"></div>' +
                    '<div class="tc-input-group"><label class="tc-label">basis</label>' +
                        '<select class="tc-input tc-fb-item-select" data-idx="' + idx + '" data-prop="flexBasis">' +
                            '<option value="auto"' + (item.flexBasis === 'auto' ? ' selected' : '') + '>auto</option>' +
                            '<option value="0"' + (item.flexBasis === '0' ? ' selected' : '') + '>0</option>' +
                            '<option value="100px"' + (item.flexBasis === '100px' ? ' selected' : '') + '>100px</option>' +
                            '<option value="200px"' + (item.flexBasis === '200px' ? ' selected' : '') + '>200px</option>' +
                            '<option value="50%"' + (item.flexBasis === '50%' ? ' selected' : '') + '>50%</option>' +
                        '</select></div>' +
                    '<div class="tc-input-group"><label class="tc-label">align-self</label>' +
                        '<select class="tc-input tc-fb-item-select" data-idx="' + idx + '" data-prop="alignSelf">' +
                            '<option value="auto"' + (item.alignSelf === 'auto' ? ' selected' : '') + '>auto</option>' +
                            '<option value="flex-start"' + (item.alignSelf === 'flex-start' ? ' selected' : '') + '>start</option>' +
                            '<option value="flex-end"' + (item.alignSelf === 'flex-end' ? ' selected' : '') + '>end</option>' +
                            '<option value="center"' + (item.alignSelf === 'center' ? ' selected' : '') + '>center</option>' +
                            '<option value="stretch"' + (item.alignSelf === 'stretch' ? ' selected' : '') + '>stretch</option>' +
                        '</select></div>' +
                '</div>';
            itemsList.appendChild(div);
        });

        // Bind events
        itemsList.querySelectorAll('.tc-fb-item-input').forEach(function (inp) {
            inp.addEventListener('input', function () {
                var i = parseInt(inp.getAttribute('data-idx'));
                var prop = inp.getAttribute('data-prop');
                items[i][prop] = parseInt(inp.value) || 0;
                updatePreview();
            });
        });

        itemsList.querySelectorAll('.tc-fb-item-select').forEach(function (sel) {
            sel.addEventListener('change', function () {
                var i = parseInt(sel.getAttribute('data-idx'));
                var prop = sel.getAttribute('data-prop');
                items[i][prop] = sel.value;
                updatePreview();
            });
        });
    }

    // ── Add/Remove buttons ───────────────────────────────────

    document.getElementById('tc-fb-add-item').addEventListener('click', addItem);
    document.getElementById('tc-fb-remove-item').addEventListener('click', removeItem);

    // ── Update preview ───────────────────────────────────────

    function updatePreview() {
        // Apply container styles
        container.style.display = containerProps.display;
        container.style.flexDirection = containerProps.flexDirection;
        container.style.flexWrap = containerProps.flexWrap;
        container.style.justifyContent = containerProps.justifyContent;
        container.style.alignItems = containerProps.alignItems;
        container.style.gap = containerProps.gap;
        container.style.minHeight = '200px';

        // Clear and rebuild items
        container.innerHTML = '';
        items.forEach(function (item) {
            var div = document.createElement('div');
            div.className = 'tc-fb-preview-item';
            div.style.background = item.color;
            div.style.flexGrow = item.flexGrow;
            div.style.flexShrink = item.flexShrink;
            div.style.flexBasis = item.flexBasis;
            div.style.alignSelf = item.alignSelf;
            div.style.order = item.order;
            div.textContent = item.label;
            container.appendChild(div);
        });

        // Build CSS code
        var css = '.container {\n';
        css += '  display: ' + containerProps.display + ';\n';
        css += '  flex-direction: ' + containerProps.flexDirection + ';\n';
        css += '  flex-wrap: ' + containerProps.flexWrap + ';\n';
        css += '  justify-content: ' + containerProps.justifyContent + ';\n';
        css += '  align-items: ' + containerProps.alignItems + ';\n';
        css += '  gap: ' + containerProps.gap + ';\n';
        css += '}\n\n';

        items.forEach(function (item, idx) {
            var itemCss = '.item-' + (idx + 1) + ' {\n';
            itemCss += '  flex-grow: ' + item.flexGrow + ';\n';
            itemCss += '  flex-shrink: ' + item.flexShrink + ';\n';
            itemCss += '  flex-basis: ' + item.flexBasis + ';\n';
            if (item.alignSelf !== 'auto') itemCss += '  align-self: ' + item.alignSelf + ';\n';
            if (item.order !== 0) itemCss += '  order: ' + item.order + ';\n';
            itemCss += '}\n';
            css += itemCss;
        });

        codeOutput.value = css;
    }

    // ── Copy CSS ─────────────────────────────────────────────

    document.getElementById('tc-fb-copy-css').addEventListener('click', function () {
        TCTP.copyText(codeOutput.value);
        TCTP.toast('CSS copied!', '\u2705');
    });

    // ── Init ─────────────────────────────────────────────────

    addItem();
    addItem();
    addItem();
})();
