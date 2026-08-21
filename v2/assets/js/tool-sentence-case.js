/**
 * Sentence Case Converter — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var inp = document.getElementById('tc-sentence-input');
        var out = document.getElementById('tc-sentence-output');
        var btnConvert = document.getElementById('tc-sentence-convert');
        if (!inp || !btnConvert || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var statusEl = document.getElementById('tc-sentence-status');

        function setStat(ids, val) {
            for (var i = 0; i < ids.length; i++) {
                var el = document.getElementById(ids[i]);
                if (el) { el.textContent = val; return; }
            }
        }

        btnConvert.addEventListener('click', function () {
            var text = inp.value;
            if (!text.trim()) {
                TCTP.toast('Please enter some text.', '\u26A0\uFE0F');
                return;
            }

            TCTP.showProgress('tc-sentence-bar');
            TCTP.setProgress('tc-sentence-bar', 40, 'Converting...');

            var abbreviations = [];
            text = text.replace(/\b[A-Z]{2,}\b/g, function (m) {
                abbreviations.push(m);
                return '{{TCTP_ABBR_' + (abbreviations.length - 1) + '}}';
            });
            text = text.replace(/\b[A-Z][a-z]{1,3}\./g, function (m) {
                abbreviations.push(m);
                return '{{TCTP_ABBR_' + (abbreviations.length - 1) + '}}';
            });

            var result = text.toLowerCase();
            result = result.replace(/(^|[.!?]\s+)([a-z])/g, function (m, sep, ch) { return sep + ch.toUpperCase(); });

            result = result.replace(/\bi\b/g, 'I');
            result = result.replace(/\bi'm\b/g, "I'm");
            result = result.replace(/\bi've\b/g, "I've");
            result = result.replace(/\bi'll\b/g, "I'll");
            result = result.replace(/\bi'd\b/g, "I'd");

            abbreviations.forEach(function (abbr, i) {
                result = result.replace('{{TCTP_ABBR_' + i + '}}', abbr);
            });

            if (out) out.value = result;
            TCTP.updateResultPanel(inp.value.length.toLocaleString() + ' chars', result.length.toLocaleString() + ' chars', (result.length < inp.value.length ? ((1 - result.length / inp.value.length) * 100).toFixed(1) + '%' : '0%'), 'Done');
            setStat(['tc-sentence-stats-words', 'tc-sentence-stat-words'], result.split(/\s+/).filter(Boolean).length.toLocaleString());
            setStat(['tc-sentence-stats-sentences', 'tc-sentence-stat-sentences'], String((result.match(/[.!?]+(\s|$)/g) || []).length || (result.trim() ? 1 : 0)));
            setStat(['tc-sentence-stats-chars', 'tc-sentence-stat-chars'], result.length.toLocaleString());

            TCTP.setProgress('tc-sentence-bar', 100, 'Done!');
            TCTP.hideProgress('tc-sentence-bar');
            if (statusEl) statusEl.textContent = 'Converted to sentence case.';
            TCTP.toast('Converted to sentence case!');
        });
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
