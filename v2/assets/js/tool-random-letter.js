/**
 * Random Letter / String Generator — Tool JS
 *
 * Checkboxes for upper/lower/numbers/symbols, count input,
 * separator select, generate, copy.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var output = document.getElementById('tc-rl-output');
        var generateBtn = document.getElementById('tc-rl-generate');
        if (!output || !generateBtn || output.dataset.tcInit) return;
        output.dataset.tcInit = '1';

        var resultText = document.getElementById('tc-rl-result-text');

        var CHARS_UPPER = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        var CHARS_LOWER = 'abcdefghijklmnopqrstuvwxyz';
        var CHARS_NUMBERS = '0123456789';
        var CHARS_SYMBOLS = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        generateBtn.addEventListener('click', function () {
            var upperEl = document.getElementById('tc-rl-upper');
            var lowerEl = document.getElementById('tc-rl-lower');
            var numbersEl = document.getElementById('tc-rl-numbers');
            var symbolsEl = document.getElementById('tc-rl-symbols');
            var countInput = document.getElementById('tc-rl-count');
            var sepSel = document.getElementById('tc-rl-separator');

            var useUpper = upperEl ? upperEl.checked : false;
            var useLower = lowerEl ? lowerEl.checked : false;
            var useNumbers = numbersEl ? numbersEl.checked : false;
            var useSymbols = symbolsEl ? symbolsEl.checked : false;
            var count = countInput ? Math.max(1, Math.min(10000, parseInt(countInput.value, 10) || 20)) : 20;
            var sepVal = sepSel && sepSel.value ? sepSel.value : 'none';

            var charset = '';
            if (useUpper) charset += CHARS_UPPER;
            if (useLower) charset += CHARS_LOWER;
            if (useNumbers) charset += CHARS_NUMBERS;
            if (useSymbols) charset += CHARS_SYMBOLS;

            if (!charset) {
                TCTP.toast('Please select at least one character type.', '\u26A0\uFE0F');
                return;
            }

            var chars = [];
            var array = new Uint32Array(count);
            window.crypto.getRandomValues(array);
            for (var i = 0; i < count; i++) {
                chars.push(charset[array[i] % charset.length]);
            }

            var sep;
            switch (sepVal) {
                case 'space': sep = ' '; break;
                case 'comma': sep = ','; break;
                case 'newline': sep = '\n'; break;
                default: sep = '';
            }

            var result = chars.join(sep);
            output.value = result;
            if (resultText) resultText.value = result;

            TCTP.toast('Random characters generated!');
        });

        var copyBtn = document.getElementById('tc-rl-copy');
        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                TCTP.copyText(output.value, 'Characters');
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
