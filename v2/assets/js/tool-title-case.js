/**
 * Title Case Converter — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var inp = document.getElementById('tc-title-input');
        var out = document.getElementById('tc-title-output');
        var btnConvert = document.getElementById('tc-title-convert');
        if (!inp || !btnConvert || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var statusEl = document.getElementById('tc-title-status');

        var minorWords = { 'a':1,'an':1,'the':1,'and':1,'but':1,'or':1,'for':1,'nor':1,'on':1,'at':1,'to':1,'by':1,'in':1,'of':1,'up':1,'as':1,'vs':1,'via':1 };

        function setStat(ids, val) {
            for (var i = 0; i < ids.length; i++) {
                var el = document.getElementById(ids[i]);
                if (el) { el.textContent = val; return; }
            }
        }

        btnConvert.addEventListener('click', function () {
            var text = inp.value;
            if (!text.trim()) {
                TCTP.toast('Please enter some text to convert.', '\u26A0\uFE0F');
                return;
            }

            TCTP.showProgress('tc-title-bar');
            TCTP.setProgress('tc-title-bar', 50, 'Converting...');

            var result = text.toLowerCase().replace(/\b\w+/g, function (word, offset) {
                if (offset === 0) return word.charAt(0).toUpperCase() + word.slice(1);
                if (minorWords[word]) return word;
                return word.charAt(0).toUpperCase() + word.slice(1);
            });
            result = result.replace(/(^|[:\-—\s])(&#\d+;|[\u00C0-\u024F]+|[a-z]+)/g, function (m, pre, word) {
                return pre + word.charAt(0).toUpperCase() + word.slice(1);
            });

            if (out) out.value = result;
            TCTP.updateResultPanel(text.length.toLocaleString() + ' chars', result.length.toLocaleString() + ' chars', (result.length < text.length ? ((1 - result.length / text.length) * 100).toFixed(1) + '%' : '0%'), 'Done');
            setStat(['tc-title-stats-words', 'tc-title-stat-words'], result.split(/\s+/).filter(Boolean).length.toLocaleString());
            setStat(['tc-title-stats-chars', 'tc-title-stat-chars'], result.length.toLocaleString());
            if (statusEl) statusEl.textContent = 'Converted to Title Case.';

            TCTP.setProgress('tc-title-bar', 100, 'Done!');
            TCTP.hideProgress('tc-title-bar');
            TCTP.toast('Converted to Title Case!');
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
