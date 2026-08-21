/**
 * Remove Line Breaks — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var inp = document.getElementById('tc-rlb-input');
        var out = document.getElementById('tc-rlb-output');
        var btnConvert = document.getElementById('tc-rlb-convert');
        if (!inp || !out || !btnConvert || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var currentMode = 'spaces';

        // Mode buttons (PHP renders .tc-modes[data-group="rlb-mode"] .tc-btn)
        var group = document.querySelector('.tc-modes[data-group="rlb-mode"]');
        if (group && !group.dataset.tcModes) {
            group.dataset.tcModes = '1';
            var modeBtns = group.querySelectorAll('.tc-btn');
            modeBtns.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    modeBtns.forEach(function (b) { b.classList.remove('sel'); });
                    btn.classList.add('sel');
                    currentMode = btn.getAttribute('data-val') || 'spaces';
                });
            });
            var selected = group.querySelector('.tc-btn.sel');
            if (selected) currentMode = selected.getAttribute('data-val') || 'spaces';
        }

        btnConvert.addEventListener('click', function () {
            var text = inp.value;
            if (!text.trim()) {
                TCTP.toast('Paste some text with line breaks first.', '\u26A0\uFE0F');
                return;
            }
            switch (currentMode) {
                case 'join':
                    text = text.replace(/\r?\n/g, '');
                    break;
                case 'paragraphs':
                    text = text.replace(/\r?\n\r?\n+/g, '\n\n');
                    text = text.replace(/\r?\n/g, ' ');
                    text = text.replace(/  +/g, ' ');
                    break;
                case 'spaces':
                default:
                    text = text.replace(/\r?\n/g, ' ');
                    break;
            }
            text = text.replace(/ +/g, ' ').trim();
            out.value = text;
            TCTP.updateResultPanel(inp.value.length.toLocaleString() + ' chars', text.length.toLocaleString() + ' chars', (text.length < inp.value.length ? ((1 - text.length / inp.value.length) * 100).toFixed(1) + '%' : '0%'), 'Done');
            TCTP.toast('Line breaks removed!');
        });

        var btnCopy = document.getElementById('tc-rlb-copy');
        if (btnCopy) {
            btnCopy.addEventListener('click', function () {
                TCTP.copyText(out.value, 'Result');
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Re-init after Elementor AJAX re-render
    new MutationObserver(function () { init(); })
        .observe(document.documentElement, { childList: true, subtree: true });
})();
