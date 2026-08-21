/**
 * Wingdings Converter — Tool JS
 *
 * Converts text to Wingdings-style Unicode symbols and back.
 *
 * Widget IDs (widget-wingdings.php):
 *  - mode group .tc-modes[data-group="wd-mode"] → data-val: to_wingdings, from_wingdings
 *  - tc-wd-input, tc-wd-convert, tc-wd-copy, tc-wd-output
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var inp = document.getElementById('tc-wd-input');
        var convertBtn = document.getElementById('tc-wd-convert');
        if (!inp || !convertBtn || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var out = document.getElementById('tc-wd-output');
        var copyBtn = document.getElementById('tc-wd-copy');
        var modeWrap = document.querySelector('.tc-modes[data-group="wd-mode"]');
        var currentMode = 'to_wingdings';

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
                    currentMode = btn.getAttribute('data-val') || 'to_wingdings';
                });
            });
        }

        // ── Conversion maps ──────────────────────────────────────

        var toWingdings = {
            'A': '\u2701', 'B': '\u2702', 'C': '\u2703', 'D': '\u2704', 'E': '\u2705',
            'F': '\u2706', 'G': '\u2707', 'H': '\u2708', 'I': '\u2709', 'J': '\u270A',
            'K': '\u270B', 'L': '\u270C', 'M': '\u270D', 'N': '\u270E', 'O': '\u270F',
            'P': '\u2710', 'Q': '\u2711', 'R': '\u2712', 'S': '\u2713', 'T': '\u2714',
            'U': '\u2715', 'V': '\u2716', 'W': '\u2717', 'X': '\u2718', 'Y': '\u2719',
            'Z': '\u271A', 'a': '\u271B', 'b': '\u271C', 'c': '\u271D', 'd': '\u271E',
            'e': '\u271F', 'f': '\u2720', 'g': '\u2721', 'h': '\u2722', 'i': '\u2723',
            'j': '\u2724', 'k': '\u2725', 'l': '\u2726', 'm': '\u2727', 'n': '\u2728',
            'o': '\u2729', 'p': '\u272A', 'q': '\u272B', 'r': '\u272C', 's': '\u272D',
            't': '\u272E', 'u': '\u272F', 'v': '\u2730', 'w': '\u2731', 'x': '\u2732',
            'y': '\u2733', 'z': '\u2734', '0': '\u2735', '1': '\u2736', '2': '\u2737',
            '3': '\u2738', '4': '\u2739', '5': '\u273A', '6': '\u273B', '7': '\u273C',
            '8': '\u273D', '9': '\u273E'
        };

        var fromWingdings = {};
        Object.keys(toWingdings).forEach(function (k) { fromWingdings[toWingdings[k]] = k; });

        // ── Convert button ───────────────────────────────────────

        convertBtn.addEventListener('click', function () {
            var text = inp.value;
            if (!text.trim()) {
                TCTP.toast('Please enter some text.', '\u26A0\uFE0F');
                return;
            }
            if (!out) return;

            var result = '';
            if (currentMode === 'to_wingdings') {
                for (var i = 0; i < text.length; i++) {
                    result += toWingdings[text[i]] || text[i];
                }
            } else {
                for (var j = 0; j < text.length; j++) {
                    result += fromWingdings[text[j]] || text[j];
                }
            }

            out.value = result;
            TCTP.updateResultPanel(text.length.toLocaleString() + ' chars', result.length.toLocaleString() + ' chars', '0%', 'Done');
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
