/**
 * Repeat Text Generator — Tool JS
 *
 * Repeats text N times with a chosen separator.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var inp = document.getElementById('tc-rt-text');
        var generateBtn = document.getElementById('tc-rt-generate');
        if (!inp || !generateBtn || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var out = document.getElementById('tc-rt-output');
        var copyBtn = document.getElementById('tc-rt-copy');
        var countInput = document.getElementById('tc-rt-count');
        var separatorSelect = document.getElementById('tc-rt-separator');

        // ── Generate button ──────────────────────────────────────

        generateBtn.addEventListener('click', function () {
            var text = inp.value;
            if (!text.trim()) {
                TCTP.toast('Please enter some text.', '\u26A0\uFE0F');
                return;
            }

            var count = parseInt(countInput ? countInput.value : '5', 10) || 1;
            if (count < 1) count = 1;
            if (count > 1000) {
                TCTP.toast('Maximum count is 1,000.', '\u26A0\uFE0F');
                return;
            }

            var sepVal = separatorSelect && separatorSelect.value ? separatorSelect.value : 'newline';
            var sep;
            switch (sepVal) {
                case 'space': sep = ' '; break;
                case 'comma': sep = ','; break;
                case 'none': sep = ''; break;
                case 'newline':
                default: sep = '\n';
            }

            var parts = [];
            for (var i = 0; i < count; i++) { parts.push(text); }

            if (out) out.value = parts.join(sep);
            if (out) TCTP.updateResultPanel(text.length.toLocaleString() + ' chars', out.value.length.toLocaleString() + ' chars', (out.value.length < text.length ? ((1 - out.value.length / text.length) * 100).toFixed(1) + '%' : '0%'), 'Done');
            TCTP.toast('Text repeated ' + count + ' times.');
        });

        // ── Copy ─────────────────────────────────────────────────

        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                TCTP.copyText(out ? out.value : '', 'Repeated text');
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
