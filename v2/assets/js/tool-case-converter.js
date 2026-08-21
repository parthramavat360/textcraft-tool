/**
 * Case Converter — Tool JS
 *
 * Converts text between uppercase, lowercase, sentence case, title case,
 * capitalized case, alternating case, and inverse case.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var prefix = 'tc-cc-';
        var inp = document.getElementById(prefix + 'input');
        var out = document.getElementById(prefix + 'output');
        if (!inp || !out || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var statusEl = document.getElementById(prefix + 'status');
        var modeWrap = document.querySelector('.tc-modes[data-group="case-type"]');

        // ── Stats ────────────────────────────────────────────────

        function updateStats(text) {
            var s = TCTP.getStats(text);
            var set = function (id, v) { var el = document.getElementById(id); if (el) el.textContent = v; };
            set(prefix + 'chars', s.chars.toLocaleString());
            set(prefix + 'words', s.words.toLocaleString());
            set(prefix + 'sentences', s.sentences.toLocaleString());
            set(prefix + 'lines', s.lines.toLocaleString());
        }

        // ── Conversion functions ─────────────────────────────────

        function toSentenceCase(s) {
            return s.toLowerCase().replace(/(^|[.!?]\s+)([a-z])/g, function (m, sep, ch) {
                return sep + ch.toUpperCase();
            });
        }

        function toTitleCase(s) {
            var small = ['a', 'an', 'the', 'and', 'but', 'or', 'nor', 'for', 'yet', 'so', 'at', 'by', 'in', 'of', 'on', 'to', 'up', 'as', 'is'];
            return s.toLowerCase().replace(/\b\w+/g, function (word, offset) {
                if (offset > 0 && small.indexOf(word) !== -1) return word;
                return word.charAt(0).toUpperCase() + word.slice(1);
            });
        }

        function toCapitalizedCase(s) {
            return s.toLowerCase().replace(/\b\w+/g, function (w) {
                return w.charAt(0).toUpperCase() + w.slice(1);
            });
        }

        function toAlternatingCase(s) {
            return s.split('').map(function (ch, i) {
                return i % 2 === 0 ? ch.toLowerCase() : ch.toUpperCase();
            }).join('');
        }

        function toInverseCase(s) {
            return s.split('').map(function (ch) {
                if (ch === ch.toUpperCase()) return ch.toLowerCase();
                return ch.toUpperCase();
            }).join('');
        }

        // ── Apply conversion ─────────────────────────────────────

        function applyConversion(caseType, sourceText) {
            if (!sourceText.trim()) {
                TCTP.toast('Please enter some text to convert.', '\u26A0\uFE0F');
                return;
            }

            var result = '';
            switch (caseType) {
                case 'uppercase':   result = sourceText.toUpperCase(); break;
                case 'lowercase':   result = sourceText.toLowerCase(); break;
                case 'sentence':    result = toSentenceCase(sourceText); break;
                case 'title':       result = toTitleCase(sourceText); break;
                case 'capitalized': result = toCapitalizedCase(sourceText); break;
                case 'alternating': result = toAlternatingCase(sourceText); break;
                case 'inverse':     result = toInverseCase(sourceText); break;
                default: return;
            }

            TCTP.showProgress(prefix + 'progress');
            TCTP.setProgress(prefix + 'progress', 60, 'Converting...');

            out.value = result;
            TCTP.updateResultPanel(sourceText.length.toLocaleString() + ' chars', result.length.toLocaleString() + ' chars', (result.length < sourceText.length ? ((1 - result.length / sourceText.length) * 100).toFixed(1) + '%' : '0%'), 'Done');
            updateStats(result);
            if (statusEl) statusEl.textContent = 'Converted to ' + caseType + ' case.';

            TCTP.setProgress(prefix + 'progress', 100, 'Done!');
            TCTP.hideProgress(prefix + 'progress');
            TCTP.toast('Converted to ' + caseType + ' case!');
        }

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
                    applyConversion(btn.getAttribute('data-val'), inp.value);
                });
            });
        }

        // ── Convert button ───────────────────────────────────────

        var convertBtn = document.getElementById(prefix + 'convert');
        if (convertBtn) {
            convertBtn.addEventListener('click', function () {
                var activeBtn = modeWrap ? modeWrap.querySelector('.tc-btn.sel') : null;
                var mode = activeBtn ? activeBtn.getAttribute('data-val') : 'uppercase';
                applyConversion(mode, inp.value);
            });
        }

        // ── Copy ─────────────────────────────────────────────────

        var copyBtn = document.getElementById(prefix + 'copy');
        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                TCTP.copyText(out.value, 'Result');
            });
        }

        // ── Input stats ──────────────────────────────────────────

        inp.addEventListener('input', function () {
            updateStats(inp.value);
        });

        updateStats('');
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
