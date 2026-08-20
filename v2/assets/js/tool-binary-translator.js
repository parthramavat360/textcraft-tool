/**
 * Binary Translator — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var inp = document.getElementById('tc-bin-input');
    var out = document.getElementById('tc-bin-output');
    var convertBtn = document.getElementById('tc-bin-convert');
    if (!inp || !out || !convertBtn) return;

    var direction = 'text-to-binary';

    document.querySelectorAll('.tc-modes[data-group="binary-direction"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            direction = btn.getAttribute('data-val');
        });
    });

    function sep() {
        return document.getElementById('tc-bin-spaces') && document.getElementById('tc-bin-spaces').checked ? ' ' : '';
    }

    function textToBinary(text) {
        return Array.from(text).map(function (ch) {
            return ch.charCodeAt(0).toString(2).padStart(8, '0');
        }).join(sep());
    }

    function binaryToText(binary) {
        var clean = binary.replace(/[^01]/g, '');
        var result = '';
        for (var i = 0; i + 8 <= clean.length; i += 8) {
            result += String.fromCharCode(parseInt(clean.substr(i, 8), 2));
        }
        return result;
    }

    function textToHex(text) {
        return Array.from(text).map(function (ch) {
            return ch.charCodeAt(0).toString(16).padStart(2, '0');
        }).join(sep());
    }

    function hexToText(hex) {
        var clean = hex.replace(/[^0-9a-fA-F]/g, '');
        var result = '';
        for (var i = 0; i + 2 <= clean.length; i += 2) {
            result += String.fromCharCode(parseInt(clean.substr(i, 2), 16));
        }
        return result;
    }

    function textToDecimal(text) {
        return Array.from(text).map(function (ch) {
            return ch.charCodeAt(0).toString();
        }).join(sep());
    }

    function decimalToText(decimal) {
        var parts = decimal.trim().split(/[\s,;]+/);
        return parts.map(function (p) {
            var n = parseInt(p, 10);
            return isNaN(n) ? p : String.fromCharCode(n);
        }).join('');
    }

    convertBtn.addEventListener('click', function () {
        var text = inp.value;
        if (!text) {
            TCTP.toast('Enter some text first.', '\u26A0\uFE0F');
            return;
        }
        var result = '';
        switch (direction) {
            case 'text-to-binary': result = textToBinary(text); break;
            case 'binary-to-text': result = binaryToText(text); break;
            case 'text-to-hex': result = textToHex(text); break;
            case 'hex-to-text': result = hexToText(text); break;
            case 'text-to-decimal': result = textToDecimal(text); break;
            case 'decimal-to-text': result = decimalToText(text); break;
        }
        out.value = result;
        var statusEl = document.getElementById('tc-bin-status');
        if (statusEl) {
            statusEl.textContent = 'Converted ' + text.length + ' characters';
            statusEl.className = 'tc-status tc-status--success';
        }
        TCTP.toast('Conversion complete!');
    });

    document.getElementById('tc-bin-copy').addEventListener('click', function () {
        TCTP.copyText(out.value, 'Converted text');
    });

})();
