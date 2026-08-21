/**
 * Unicode Translator — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function escapeHtml(s) {
        return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function init() {
        var inp = document.getElementById('tc-unicode-input');
        var out = document.getElementById('tc-unicode-output');
        var convertBtn = document.getElementById('tc-unicode-convert');
        if (!inp || !out || !convertBtn || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var direction = 'to-codepoints';

        // Direction buttons (PHP renders .tc-modes[data-group="unicode-direction"] .tc-btn)
        var group = document.querySelector('.tc-modes[data-group="unicode-direction"]');
        if (group && !group.dataset.tcModes) {
            group.dataset.tcModes = '1';
            var dirBtns = group.querySelectorAll('.tc-btn');
            dirBtns.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    dirBtns.forEach(function (b) { b.classList.remove('sel'); });
                    btn.classList.add('sel');
                    direction = btn.getAttribute('data-val') || 'to-codepoints';
                });
            });
            var selected = group.querySelector('.tc-btn.sel');
            if (selected) direction = selected.getAttribute('data-val') || 'to-codepoints';
        }

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
                cp = cp.replace(/^U\+|^0x|^#/i, '').trim();
                var code = parseInt(cp, 16);
                if (isNaN(code)) return cp;
                try { return String.fromCodePoint(code); } catch (e) { return cp; }
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
            return text.replace(/\\u\{?([0-9A-Fa-f]{1,6})\}?/g, function (_, hex) {
                var code = parseInt(hex, 16);
                try { return String.fromCodePoint(code); } catch (e) { return _; }
            });
        }

        function showDetails(text) {
            var el = document.getElementById('tc-unicode-details');
            if (!el) return;
            if (!text) { el.innerHTML = ''; return; }
            var items = [];
            Array.from(text).slice(0, 200).forEach(function (ch) {
                var code = ch.codePointAt(0);
                items.push({
                    char: ch,
                    code: 'U+' + code.toString(16).toUpperCase().padStart(4, '0'),
                    decimal: code,
                    html: '&#' + code + ';'
                });
            });
            el.innerHTML = '<table class="tc-unicode-tbl"><thead><tr><th>Char</th><th>Code</th><th>Decimal</th><th>HTML</th></tr></thead><tbody>' +
                items.map(function (it) {
                    return '<tr><td class="tc-unicode-char">' + escapeHtml(it.char) + '</td><td><code>' + it.code + '</code></td><td>' + it.decimal + '</td><td><code>' + it.html + '</code></td></tr>';
                }).join('') +
                '</tbody></table>';
        }

        function setStatus(type, message) {
            var statusEl = document.getElementById('tc-unicode-status');
            if (!statusEl) return;
            statusEl.textContent = message;
            statusEl.className = 'tc-status' + (type ? ' tc-status--' + type : '');
        }

        convertBtn.addEventListener('click', function () {
            var text = inp.value;
            if (!text) {
                TCTP.toast('Enter some text first.', '\u26A0\uFE0F');
                return;
            }
            var result = '';
            switch (direction) {
                case 'to-text': result = fromCodePoints(text); break;
                case 'to-escape': result = toEscape(text); break;
                case 'unescape': result = fromEscape(text); break;
                case 'to-codepoints':
                default: result = toCodePoints(text); break;
            }
            out.value = result;
            showDetails(result);
            TCTP.updateResultPanel(text.length.toLocaleString() + ' chars', result.length.toLocaleString() + ' chars', (result.length < text.length ? ((1 - result.length / text.length) * 100).toFixed(1) + '%' : '0%'), 'Done');
            setStatus('success', 'Converted ' + text.length + ' characters');
            TCTP.toast('Conversion complete!');
        });

        var copyBtn = document.getElementById('tc-unicode-copy');
        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                TCTP.copyText(out.value, 'Unicode text');
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
