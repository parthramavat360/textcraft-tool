/**
 * Pig Latin Translator — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var inp = document.getElementById('tc-pl-input');
        var out = document.getElementById('tc-pl-output');
        var btnTranslate = document.getElementById('tc-pl-translate');
        if (!inp || !btnTranslate || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var statusEl = document.getElementById('tc-pl-status');

        function pigLatin(word) {
            if (!word) return word;
            var isUpper = word[0] === word[0].toUpperCase();
            var lower = word.toLowerCase();
            var vowels = 'aeiou';
            if (vowels.indexOf(lower[0]) !== -1) {
                var way = lower + 'way';
                return isUpper ? way.charAt(0).toUpperCase() + way.slice(1) : way;
            }
            var cluster = '';
            for (var i = 0; i < lower.length; i++) {
                if (vowels.indexOf(lower[i]) !== -1) break;
                cluster += lower[i];
            }
            var result = lower.slice(cluster.length) + cluster + 'ay';
            if (isUpper) result = result.charAt(0).toUpperCase() + result.slice(1);
            return result;
        }

        btnTranslate.addEventListener('click', function () {
            var text = inp.value;
            if (!text.trim()) {
                TCTP.toast('Please enter some text to translate.', '\u26A0\uFE0F');
                return;
            }

            TCTP.showProgress('tc-pl-bar');
            TCTP.setProgress('tc-pl-bar', 50, 'Translating...');

            var result = text.replace(/\b[a-zA-Z']+\b/g, function (word) { return pigLatin(word); });

            if (out) out.value = result;
            if (statusEl) statusEl.textContent = 'Translated to Pig Latin.';

            TCTP.updateResultPanel(inp.value.length.toLocaleString() + ' chars', result.length.toLocaleString() + ' chars', (result.length < inp.value.length ? ((1 - result.length / inp.value.length) * 100).toFixed(1) + '%' : '0%'), 'Done');

            TCTP.setProgress('tc-pl-bar', 100, 'Done!');
            TCTP.hideProgress('tc-pl-bar');
            TCTP.toast('Translated to Pig Latin!');
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
