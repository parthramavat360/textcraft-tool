/**
 * Reverse Text — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var inp = document.getElementById('tc-rvt-input');
    var out = document.getElementById('tc-rvt-output');
    if (!inp) return;

    var mode = 'chars';

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

    // Mode buttons
    document.querySelectorAll('.tctp-modes[data-group="rev-mode"] .tctp-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            mode = btn.getAttribute('data-val');
        });
    });

    // Reverse button
    document.getElementById('tc-rvt-reverse').addEventListener('click', function () {
        var text = inp.value;
        if (!text) { TCTP.toast('Enter some text above to start reversing.', '\u26A0\uFE0F'); return; }

        var result = '';
        switch (mode) {
            case 'chars':
                result = Array.from(text).reverse().join('');
                break;
            case 'words':
                result = text.split('\n').map(function (line) {
                    return line.split(' ').reverse().join(' ');
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
        }
        out.value = result;
        TCTP.toast('Text reversed!');
    });

    // Copy
    document.getElementById('tc-rvt-copy').addEventListener('click', function () {
        TCTP.copyText(out.value, 'Reversed text');
    });

})();