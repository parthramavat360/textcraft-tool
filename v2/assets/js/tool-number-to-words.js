(function () {
    'use strict';
    var $ = function (s, p) { return (p || document).querySelector(s); };
    var numInput = $('#ntw-number');
    if (!numInput) return;

    var formatSelect = $('#ntw-format');
    var currencyGroup = $('#ntw-currency-group');
    var currencySelect = $('#ntw-currency');
    var capitalizeCheck = $('#ntw-capitalize');
    var andCheck = $('#ntw-and');
    var convertBtn = $('#ntw-convert');
    var resultEl = $('#ntw-result');
    var wordsEl = $('#ntw-words');
    var upperEl = $('#ntw-upper');

    formatSelect.addEventListener('change', function () {
        currencyGroup.style.display = this.value === 'currency' ? '' : 'none';
    });

    var ones = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];
    var tens = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];
    var scales = ['', 'thousand', 'million', 'billion', 'trillion', 'quadrillion'];

    var ordinals = {
        'one': 'first', 'two': 'second', 'three': 'third', 'five': 'fifth',
        'eight': 'eighth', 'nine': 'ninth', 'twelve': 'twelfth'
    };

    function threeDigits(n) {
        if (n === 0) return '';
        var h = Math.floor(n / 100);
        var r = n % 100;
        var parts = [];
        if (h > 0) parts.push(ones[h] + ' hundred');
        if (r > 0) {
            if (r < 20) parts.push(ones[r]);
            else {
                var t = Math.floor(r / 10);
                var o = r % 10;
                parts.push(tens[t] + (o > 0 ? '-' + ones[o] : ''));
            }
        }
        return parts.join(andCheck.checked ? ' and ' : ' ');
    }

    function numberToWords(numStr) {
        var negative = '';
        if (numStr.charAt(0) === '-') { negative = 'negative '; numStr = numStr.substring(1); }

        var parts = numStr.split('.');
        var intPart = parts[0].replace(/^0+/, '') || '0';
        var decPart = parts.length > 1 ? parts[1] : '';

        var intNum = parseInt(intPart, 10);
        if (isNaN(intNum)) return 'Not a valid number';

        if (intNum === 0) {
            var result = 'zero';
            if (decPart) result += ' point ' + digitWords(decPart);
            return negative + result;
        }

        var groups = [];
        while (intNum > 0) {
            groups.unshift(intNum % 1000);
            intNum = Math.floor(intNum / 1000);
        }

        var words = [];
        for (var i = 0; i < groups.length; i++) {
            if (groups[i] === 0) continue;
            var scaleIdx = groups.length - 1 - i;
            var groupWords = threeDigits(groups[i]);
            if (scales[scaleIdx]) groupWords += ' ' + scales[scaleIdx];
            words.push(groupWords);
        }

        var result = negative + words.join(andCheck.checked ? ' and ' : ' ');

        if (decPart) {
            result += ' point ' + digitWords(decPart);
        }

        return result;
    }

    function digitWords(d) {
        var wordDigits = {
            '0': 'zero', '1': 'one', '2': 'two', '3': 'three', '4': 'four',
            '5': 'five', '6': 'six', '7': 'seven', '8': 'eight', '9': 'nine'
        };
        return d.split('').map(function (c) { return wordDigits[c] || c; }).join(' ');
    }

    function applyOrdinal(words) {
        var lastWord = words.split(/[\s-]+/).pop();
        if (ordinals[lastWord]) {
            return words.substring(0, words.length - lastWord.length) + ordinals[lastWord];
        }
        if (lastWord.endsWith('y')) {
            return words.substring(0, words.length - 1) + 'ieth';
        }
        return words + 'th';
    }

    convertBtn.addEventListener('click', function () {
        var val = numInput.value.trim();
        if (!val) { TCTP.toast('Enter a number', '\u26a0\ufe0f'); return; }
        if (isNaN(val.replace('-', '').replace('.', ''))) { TCTP.toast('Invalid number', '\u26a0\ufe0f'); return; }

        var words = numberToWords(val);

        if (formatSelect.value === 'ordinal') {
            words = applyOrdinal(words);
        } else if (formatSelect.value === 'currency') {
            var cur = currencySelect.value;
            words = cur + ' ' + words;
        }

        if (capitalizeCheck.checked) {
            words = words.charAt(0).toUpperCase() + words.slice(1);
        }

        TCTP.copyText(words);

        wordsEl.innerHTML = '<div style="background:#0f172a;padding:16px;border-radius:12px;border:1px solid rgba(148,163,184,0.12);color:#e2e8f0;font-size:16px;line-height:1.6;word-break:break-word">' + escHtml(words) + '</div>';
        upperEl.innerHTML = '<div style="background:#0f172a;padding:16px;border-radius:12px;border:1px solid rgba(148,163,184,0.12);color:#e2e8f0;font-size:16px;line-height:1.6;word-break:break-word;text-transform:uppercase">' + escHtml(words) + '</div>';

        resultEl.style.display = '';
        TCTP.initTabs(resultEl);
        TCTP.toast('Copied to clipboard!', '\u2705');
    });

    numInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') convertBtn.click();
    });

    function escHtml(s) { return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }
})();
