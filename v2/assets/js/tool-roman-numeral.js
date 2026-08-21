/**
 * Roman Numeral Converter — Tool JS
 *
 * Converts numbers to Roman numerals and back (1–3999).
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var inp = document.getElementById('tc-rn-input');
        var convertBtn = document.getElementById('tc-rn-convert');
        if (!inp || !convertBtn || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var out = document.getElementById('tc-rn-output');
        var copyBtn = document.getElementById('tc-rn-copy');
        var modeWrap = document.querySelector('.tc-modes[data-group="rn-mode"]');
        var currentMode = 'to_roman';

        // ── Mode buttons (.tc-modes / .tc-btn per widget markup) ──

        function selectMode(btn) {
            if (modeWrap) {
                modeWrap.querySelectorAll('.tc-btn').forEach(function (b) {
                    b.classList.remove('sel');
                });
            }
            btn.classList.add('sel');
        }

        if (modeWrap) {
            modeWrap.querySelectorAll('.tc-btn[data-val]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    selectMode(btn);
                    currentMode = btn.getAttribute('data-val') || 'to_roman';
                });
            });
        }

        // ── Conversion helpers ───────────────────────────────────

        var lookup = [
            [1000, 'M'], [900, 'CM'], [500, 'D'], [400, 'CD'],
            [100, 'C'], [90, 'XC'], [50, 'L'], [40, 'XL'],
            [10, 'X'], [9, 'IX'], [5, 'V'], [4, 'IV'], [1, 'I']
        ];

        function toRoman(num) {
            if (num < 1 || num > 3999) return '';
            var result = '';
            for (var i = 0; i < lookup.length; i++) {
                while (num >= lookup[i][0]) { result += lookup[i][1]; num -= lookup[i][0]; }
            }
            return result;
        }

        function fromRoman(str) {
            var map = { M: 1000, CM: 900, D: 500, CD: 400, C: 100, XC: 90, L: 50, XL: 40, X: 10, IX: 9, V: 5, IV: 4, I: 1 };
            str = str.toUpperCase().trim();
            var result = 0;
            for (var i = 0; i < str.length; i++) {
                var cur = map[str[i]] || 0;
                var next = map[str[i + 1]] || 0;
                if (cur < next) { result -= cur; } else { result += cur; }
            }
            return result;
        }

        // ── Convert button ───────────────────────────────────────

        convertBtn.addEventListener('click', function () {
            var val = inp.value.trim();
            if (!val) {
                TCTP.toast('Please enter a value.', '\u26A0\uFE0F');
                return;
            }
            if (!out) return;

            if (currentMode === 'to_roman') {
                var num = parseInt(val, 10);
                if (isNaN(num) || num < 1 || num > 3999) {
                    TCTP.toast('Enter a number between 1 and 3999.', '\u26A0\uFE0F');
                    return;
                }
                out.value = toRoman(num);
            } else {
                if (!/^[IVXLCDMivxlcdm]+$/.test(val)) {
                    TCTP.toast('Enter a valid Roman numeral.', '\u26A0\uFE0F');
                    return;
                }
                var result = fromRoman(val);
                if (result < 1 || result > 3999) {
                    TCTP.toast('Result out of range (1-3999).', '\u26A0\uFE0F');
                    return;
                }
                out.value = result;
            }

            TCTP.updateResultPanel(val.length.toLocaleString() + ' chars', String(out.value).length.toLocaleString() + ' chars', (String(out.value).length < val.length ? ((1 - String(out.value).length / val.length) * 100).toFixed(1) + '%' : '0%'), 'Done');

            TCTP.toast('Conversion complete.');
        });

        // ── Copy ─────────────────────────────────────────────────

        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                TCTP.copyText(out ? out.value : '', 'Result');
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
