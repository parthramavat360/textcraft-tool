/**
 * Sort Words & Lines — Tool JS
 *
 * Sorts words or lines alphabetically, by length, or randomly.
 *
 * Widget IDs (widget-sort-words.php):
 *  - mode group .tc-modes[data-group="sw-sort"] → data-val: alpha_asc,
 *    alpha_desc, length_asc, length_desc, random
 *  - checkboxes: sw-lines (default on), sw-case
 *  - tc-sw-input, tc-sw-sort, tc-sw-copy, tc-sw-output
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var inp = document.getElementById('tc-sw-input');
        var sortBtn = document.getElementById('tc-sw-sort');
        if (!inp || !sortBtn || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var out = document.getElementById('tc-sw-output');
        var copyBtn = document.getElementById('tc-sw-copy');
        var sortLinesCheck = document.getElementById('sw-lines');
        var caseSensitiveCheck = document.getElementById('sw-case');
        var modeWrap = document.querySelector('.tc-modes[data-group="sw-sort"]');
        var currentMode = 'alpha_asc';

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
                    currentMode = btn.getAttribute('data-val') || 'alpha_asc';
                });
            });
        }

        // ── Comparison ───────────────────────────────────────────

        function compareStr(a, b, mode, cs) {
            var aVal = a, bVal = b;
            if (!cs) { aVal = a.toLowerCase(); bVal = b.toLowerCase(); }
            switch (mode) {
                case 'alpha_desc': return bVal.localeCompare(aVal);
                case 'length_asc': return a.length - b.length || aVal.localeCompare(bVal);
                case 'length_desc': return b.length - a.length || aVal.localeCompare(bVal);
                case 'random': return Math.random() - 0.5;
                case 'alpha_asc':
                default: return aVal.localeCompare(bVal);
            }
        }

        // ── Sort button ──────────────────────────────────────────

        sortBtn.addEventListener('click', function () {
            var text = inp.value;
            if (!text.trim()) {
                TCTP.toast('Please enter some text.', '\u26A0\uFE0F');
                return;
            }
            if (!out) return;

            var sortLines = sortLinesCheck && sortLinesCheck.checked;
            var caseSensitive = caseSensitiveCheck && caseSensitiveCheck.checked;

            var lines = text.split('\n');
            if (sortLines) {
                lines.sort(function (a, b) {
                    return compareStr(a, b, currentMode, caseSensitive);
                });
            } else {
                var allWords = [];
                lines.forEach(function (line) {
                    var words = line.split(/\s+/).filter(function (w) { return w.length > 0; });
                    allWords = allWords.concat(words);
                });
                allWords.sort(function (a, b) {
                    return compareStr(a, b, currentMode, caseSensitive);
                });
                lines = [allWords.join(' ')];
            }

            out.value = lines.join('\n');
            TCTP.updateResultPanel(text.length.toLocaleString() + ' chars', lines.join('\n').length.toLocaleString() + ' chars', (lines.join('\n').length < text.length ? ((1 - lines.join('\n').length / text.length) * 100).toFixed(1) + '%' : '0%'), 'Done');
            TCTP.toast('Text sorted.');
        });

        // ── Copy ─────────────────────────────────────────────────

        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                TCTP.copyText(out ? out.value : '', 'Sorted result');
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
