/**
 * Whitespace Remover — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var input = document.getElementById('tc-ws-input');
        var output = document.getElementById('tc-ws-output');
        var cleanBtn = document.getElementById('tc-ws-clean');
        var copyBtn = document.getElementById('tc-ws-copy');
        if (!input || !cleanBtn || input.dataset.tcInit) return;
        input.dataset.tcInit = '1';

        var trimLines = document.getElementById('ws-trim');
        var extraSpaces = document.getElementById('ws-extra');
        var removeTabs = document.getElementById('ws-tabs');
        var globalTrim = document.getElementById('ws-global');

        cleanBtn.addEventListener('click', function () {
            var text = input.value;
            if (!text.trim()) {
                TCTP.toast('Please enter some text to clean up.', '\u26A0\uFE0F');
                return;
            }

            var lines = text.split('\n');

            if (removeTabs && removeTabs.checked) {
                lines = lines.map(function (l) { return l.replace(/\t/g, ''); });
            }
            if (extraSpaces && extraSpaces.checked) {
                lines = lines.map(function (l) { return l.replace(/ {2,}/g, ' '); });
            }
            if (trimLines && trimLines.checked) {
                lines = lines.map(function (l) { return l.trim(); });
            }

            text = lines.join('\n');
            if (globalTrim && globalTrim.checked) {
                text = text.replace(/^\s+|\s+$/g, '');
            }

            if (output) output.value = text;
            TCTP.updateResultPanel(input.value.length.toLocaleString() + ' chars', text.length.toLocaleString() + ' chars', (text.length < input.value.length ? ((1 - text.length / input.value.length) * 100).toFixed(1) + '%' : '0%'), 'Done');
            TCTP.toast('Whitespace cleaned!');
        });

        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                TCTP.copyText(output ? output.value : '', 'Cleaned text');
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
