/**
 * Unicode Translator — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var inp = document.getElementById('tc-unicode-input');
    var out = document.getElementById('tc-unicode-output');
    var convertBtn = document.getElementById('tc-unicode-convert');
    if (!inp || !out || !convertBtn) return;

    var direction = 'to-codepoints';

    document.querySelectorAll('.tc-modes[data-group="unicode-direction"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            direction = btn.getAttribute('data-val');
        });
    });

    function toCodePoints(text) {
        var result = [];
        for (var i = 0; i < text.length; i++) {
            var code = text.codePointAt(i);
            if (code > 0xFFFF) i++;
            result.push('U+' + code.toString(16).toUpperCase().padStart(4, '0'));
        }
        return result.join(' ');
    }

    function fromCodePoints(text) {
        return text.trim().split(/[\s,;]+/).map(function (cp) {
            cp = cp.replace(/^U\+|^0x|^#/, '').trim();
            var code = parseInt(cp, 16);
            if (isNaN(code)) return cp;
            return String.fromCodePoint(code);
        }).join('');
    }

    function toEscape(text) {
        var result = [];
        for (var i = 0; i < text.length; i++) {
            var code = text.codePointAt(i);
            if (code > 0xFFFF) i++;
            if (code < 128) {
                result.push(text[i]);
            } else {
                result.push('\\u' + code.toString(16).toUpperCase().padStart(4, '0'));
            }
        }
        return result.join('');
    }

    function fromEscape(text) {
        return text.replace(/\\u([0-9A-Fa-f]{4,5})/g, function (_, hex) {
            return String.fromCodePoint(parseInt(hex, 16));
        });
    }

    function showDetails(text) {
        var el = document.getElementById('tc-unicode-details');
        if (!el) return;
        var items = [];
        var arr = Array.from(text);
        arr.forEach(function (ch) {
            var code = ch.codePointAt(0);
            items.push({
                char: ch,
                code: 'U+' + code.toString(16).toUpperCase().padStart(4, '0'),
                decimal: code,
                html: '&#' + code + ';',
                hex: '0x' + code.toString(16).toUpperCase()
            });
        });
        el.innerHTML = '<table class="tc-unicode-tbl"><thead><tr><th>Char</th><th>Code</th><th>Decimal</th><th>HTML</th></tr></thead><tbody>' +
            items.map(function (it) {
                return '<tr><td class="tc-unicode-char">' + escapeHtml(it.char) + '</td><td><code>' + it.code + '</code></td><td>' + it.decimal + '</td><td><code>' + it.html + '</code></td></tr>';
            }).join('') +
            '</tbody></table>';
    }

    function escapeHtml(s) {
        return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    convertBtn.addEventListener('click', function () {
        var text = inp.value;
        if (!text) {
            TCTP.toast('Enter some text first.', '\u26A0\uFE0F');
            return;
        }
        var result = '';
        switch (direction) {
            case 'to-codepoints': result = toCodePoints(text); break;
            case 'to-text': result = fromCodePoints(text); break;
            case 'to-escape': result = toEscape(text); break;
            case 'unescape': result = fromEscape(text); break;
        }
        out.value = result;
        showDetails(result);
        var statusEl = document.getElementById('tc-unicode-status');
        if (statusEl) {
            statusEl.textContent = 'Converted ' + text.length + ' characters';
            statusEl.className = 'tc-status tc-status--success';
        }
        TCTP.toast('Conversion complete!');
    });

    document.getElementById('tc-unicode-copy').addEventListener('click', function () {
        TCTP.copyText(out.value, 'Unicode text');
    });

})();
