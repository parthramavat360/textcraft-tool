/**
 * Morse Code Translator — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var inp = document.getElementById('tc-morse-input');
    var out = document.getElementById('tc-morse-output');
    var translateBtn = document.getElementById('tc-morse-translate');
    if (!inp || !out || !translateBtn) return;

    var MORSE = {
        'A': '.-', 'B': '-...', 'C': '-.-.', 'D': '-..', 'E': '.', 'F': '..-.',
        'G': '--.', 'H': '....', 'I': '..', 'J': '.---', 'K': '-.-', 'L': '.-..',
        'M': '--', 'N': '-.', 'O': '---', 'P': '.--.', 'Q': '--.-', 'R': '.-.',
        'S': '...', 'T': '-', 'U': '..-', 'V': '...-', 'W': '.--', 'X': '-..-',
        'Y': '-.--', 'Z': '--..',
        '0': '-----', '1': '.----', '2': '..---', '3': '...--', '4': '....-',
        '5': '.....', '6': '-....', '7': '--...', '8': '---..', '9': '----.',
        '.': '.-.-.-', ',': '--..--', '?': '..--..', '!': '-.-.--', '/': '-..-.',
        '(': '-.--.', ')': '-.--.-', '&': '.-...', ':': '---...', ';': '-.-.-.',
        '=': '-...-', '+': '.-.-.', '-': '-....-', '_': '..--.-', '"': '.-..-.',
        '$': '...-..', '@': '...-.-', ' ': '/'
    };

    var REVERSE = {};
    Object.keys(MORSE).forEach(function (k) { REVERSE[MORSE[k]] = k; });

    var currentDirection = 'to-morse';

    document.querySelectorAll('.tc-modes[data-group="morse-direction"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            currentDirection = btn.getAttribute('data-val');
        });
    });

    function getSep() {
        var sel = document.getElementById('tc-morse-sep');
        if (!sel) return ' ';
        return sel.value === '\\n' ? '\n' : sel.value;
    }

    function toMorse(text, sep) {
        return text.toUpperCase().split('').map(function (ch) {
            return MORSE[ch] || ch;
        }).join(sep);
    }

    function fromMorse(text) {
        var sep = getSep();
        var normalized = text.replace(/\n/g, ' / ').replace(/\|/g, '/');
        return normalized.split('/').map(function (word) {
            return word.trim().split(new RegExp('\\s*' + sep.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\s*')).map(function (code) {
                return REVERSE[code] || code;
            }).join('');
        }).join(' ');
    }

    function buildRef() {
        var refEl = document.getElementById('tc-morse-ref');
        if (!refEl) return;
        var html = '<div class="tc-ref-grid">';
        'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'.split('').forEach(function (ch) {
            html += '<span class="tc-ref-item"><b>' + ch + '</b> ' + MORSE[ch] + '</span>';
        });
        html += '</div>';
        refEl.innerHTML = html;
    }

    translateBtn.addEventListener('click', function () {
        var text = inp.value;
        if (!text.trim()) {
            TCTP.toast('Enter some text first.', '\u26A0\uFE0F');
            return;
        }
        var sep = getSep();
        var result = currentDirection === 'to-morse' ? toMorse(text, sep) : fromMorse(text);
        out.value = result;
        var statusEl = document.getElementById('tc-morse-status');
        if (statusEl) {
            statusEl.textContent = 'Translated ' + text.length + ' characters';
            statusEl.className = 'tc-status tc-status--success';
        }
        TCTP.toast('Translation complete!');
    });

    document.getElementById('tc-morse-copy').addEventListener('click', function () {
        TCTP.copyText(out.value, 'Morse code');
    });

    buildRef();

})();
