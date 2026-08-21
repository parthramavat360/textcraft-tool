/**
 * Reverse Text — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var FLIP_MAP = {
        'a': '\u0250', 'b': 'q', 'c': '\u0254', 'd': 'p', 'e': '\u01DD', 'f': '\u025F',
        'g': '\u0183', 'h': '\u0265', 'i': '\u0131', 'j': '\u027E', 'k': '\u029E',
        'l': 'l', 'm': '\u026F', 'n': 'u', 'o': 'o', 'p': 'd', 'q': 'b',
        'r': '\u0279', 's': 's', 't': '\u0287', 'u': 'n', 'v': '\u028C',
        'w': '\u028D', 'x': 'x', 'y': '\u028E', 'z': 'z',
        'A': '\u2200', 'B': '\u18D7', 'C': '\u0186', 'D': '\u18E1', 'E': '\u018E',
        'F': '\u2132', 'G': '\u2141', 'H': 'H', 'I': 'I', 'J': '\u027E',
        'K': '\u029E', 'L': '\u02E5', 'M': 'W', 'N': 'N', 'O': 'O', 'P': '\u0500',
        'Q': 'Q', 'R': '\u1D0F', 'S': 'S', 'T': '\u22A5', 'U': '\u2229',
        'V': '\u039B', 'W': 'M', 'X': 'X', 'Y': '\u2144', 'Z': 'Z',
        '0': '0', '1': '\u0196', '2': '\u0191', '3': '\u018A', '4': '\u0193',
        '5': '\u01BD', '6': '9', '7': '\u0190', '8': '8', '9': '6',
        '.': '\u02D9', ',': '\\', '?': '\u00BF', '!': '\u00A1', '(': ')', ')': '(', ' ': ' '
    };

    function init() {
        var inp = document.getElementById('tc-rvt-input');
        var out = document.getElementById('tc-rvt-output');
        var reverseBtn = document.getElementById('tc-rvt-reverse');
        if (!inp || !out || !reverseBtn || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var mode = 'chars';

        // Mode buttons (PHP renders .tc-modes[data-group="rev-mode"] .tc-btn)
        var group = document.querySelector('.tc-modes[data-group="rev-mode"]');
        if (group && !group.dataset.tcModes) {
            group.dataset.tcModes = '1';
            var modeBtns = group.querySelectorAll('.tc-btn');
            modeBtns.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    modeBtns.forEach(function (b) { b.classList.remove('sel'); });
                    btn.classList.add('sel');
                    mode = btn.getAttribute('data-val') || 'chars';
                });
            });
            var selected = group.querySelector('.tc-btn.sel');
            if (selected) mode = selected.getAttribute('data-val') || 'chars';
        }

        reverseBtn.addEventListener('click', function () {
            var text = inp.value;
            if (!text) { TCTP.toast('Enter some text above to start reversing.', '\u26A0\uFE0F'); return; }

            var result = '';
            switch (mode) {
                case 'words':
                    result = text.split('\n').map(function (line) {
                        return line.split(/\s+/).filter(Boolean).reverse().join(' ');
                    }).join('\n');
                    break;
                case 'lines':
                    result = text.split('\n').reverse().join('\n');
                    break;
                case 'flip':
                    result = Array.from(text).reverse().map(function (c) {
                        return FLIP_MAP[c] || c;
                    }).join('');
                    break;
                case 'chars':
                default:
                    result = Array.from(text).reverse().join('');
                    break;
            }
            out.value = result;
            TCTP.updateResultPanel(text.length.toLocaleString() + ' chars', result.length.toLocaleString() + ' chars', (result.length < text.length ? ((1 - result.length / text.length) * 100).toFixed(1) + '%' : '0%'), 'Done');
            TCTP.toast('Text reversed!');
        });

        var copyBtn = document.getElementById('tc-rvt-copy');
        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                TCTP.copyText(out.value, 'Reversed text');
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
