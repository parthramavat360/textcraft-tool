/**
 * Binary Translator — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var inp = document.getElementById('tc-bin-input');
        var out = document.getElementById('tc-bin-output');
        var convertBtn = document.getElementById('tc-bin-convert');
        if (!inp || !out || !convertBtn || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var direction = 'text-to-binary';

        // Direction buttons (PHP renders .tc-modes[data-group="binary-direction"] .tc-btn)
        var group = document.querySelector('.tc-modes[data-group="binary-direction"]');
        if (group && !group.dataset.tcModes) {
            group.dataset.tcModes = '1';
            var dirBtns = group.querySelectorAll('.tc-btn');
            dirBtns.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    dirBtns.forEach(function (b) { b.classList.remove('sel'); });
                    btn.classList.add('sel');
                    direction = btn.getAttribute('data-val') || 'text-to-binary';
                });
            });
            var selected = group.querySelector('.tc-btn.sel');
            if (selected) direction = selected.getAttribute('data-val') || 'text-to-binary';
        }

        function sep() {
            var cb = document.getElementById('tc-bin-spaces');
            return cb && cb.checked ? ' ' : '';
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

        function setStatus(type, message) {
            var statusEl = document.getElementById('tc-bin-status');
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
                case 'text-to-binary': result = textToBinary(text); break;
                case 'binary-to-text': result = binaryToText(text); break;
                case 'text-to-hex': result = textToHex(text); break;
                case 'hex-to-text': result = hexToText(text); break;
                case 'text-to-decimal': result = textToDecimal(text); break;
                case 'decimal-to-text': result = decimalToText(text); break;
            }
            out.value = result;
            TCTP.updateResultPanel(text.length.toLocaleString() + ' chars', result.length.toLocaleString() + ' chars', (result.length < text.length ? ((1 - result.length / text.length) * 100).toFixed(1) + '%' : '0%'), 'Done');
            setStatus('success', 'Converted ' + text.length + ' characters');
            TCTP.toast('Conversion complete!');
        });

        var copyBtn = document.getElementById('tc-bin-copy');
        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                TCTP.copyText(out.value, 'Converted text');
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
