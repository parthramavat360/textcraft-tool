/**
 * Random Letter / String Generator — Tool JS
 *
 * Checkboxes for upper/lower/numbers/symbols, count input,
 * generate, copy, download.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var output = document.getElementById('tc-rlet-output');
    if (!output) return;

    var CHARS_UPPER = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var CHARS_LOWER = 'abcdefghijklmnopqrstuvwxyz';
    var CHARS_NUMBERS = '0123456789';
    var CHARS_SYMBOLS = '!@#$%^&*()_+-=[]{}|;:,.<>?';

    var generateBtn = document.getElementById('tc-rlet-generate');
    if (generateBtn) generateBtn.addEventListener('click', function () {
        var upperEl = document.getElementById('tc-rlet-upper');
        var lowerEl = document.getElementById('tc-rlet-lower');
        var numbersEl = document.getElementById('tc-rlet-numbers');
        var symbolsEl = document.getElementById('tc-rlet-symbols');
        var countInput = document.getElementById('tc-rlet-count');

        var useUpper = upperEl ? upperEl.checked : false;
        var useLower = lowerEl ? lowerEl.checked : false;
        var useNumbers = numbersEl ? numbersEl.checked : false;
        var useSymbols = symbolsEl ? symbolsEl.checked : false;
        var count = countInput ? Math.max(1, Math.min(10000, parseInt(countInput.value) || 16)) : 16;

        var charset = '';
        if (useUpper) charset += CHARS_UPPER;
        if (useLower) charset += CHARS_LOWER;
        if (useNumbers) charset += CHARS_NUMBERS;
        if (useSymbols) charset += CHARS_SYMBOLS;

        if (!charset) {
            TCTP.toast('Please select at least one character type.', '\u26A0\uFE0F');
            return;
        }

        var result = '';
        var array = new Uint32Array(count);
        window.crypto.getRandomValues(array);
        for (var i = 0; i < count; i++) {
            result += charset[array[i] % charset.length];
        }

        output.value = result;

        var lenEl = document.getElementById('tc-rlet-stat-length');
        if (lenEl) lenEl.textContent = count;

        var typesEl = document.getElementById('tc-rlet-stat-types');
        if (typesEl) {
            var types = [];
            if (useUpper) types.push('Upper');
            if (useLower) types.push('Lower');
            if (useNumbers) types.push('Numbers');
            if (useSymbols) types.push('Symbols');
            typesEl.textContent = types.join(', ');
        }

        var statsEl = document.getElementById('tc-rlet-stats');
        if (statsEl) statsEl.style.display = '';

        TCTP.toast('Random string generated!');
    });

    var copyBtn = document.getElementById('tc-rlet-copy');
    if (copyBtn) copyBtn.addEventListener('click', function () {
        TCTP.copyText(output.value, 'String');
    });

    var downloadBtn = document.getElementById('tc-rlet-download');
    if (downloadBtn) downloadBtn.addEventListener('click', function () {
        TCTP.downloadText(output.value, 'random-string.txt');
    });

})();
