/**
 * Words to Number — Convert written numbers to digits.
 * Supports cardinal, ordinal, currency, and Roman numerals.
 */
(function () {
    'use strict';
    if (!document.getElementById('wt-input')) return;

    var input = document.getElementById('wt-input');
    var output = document.getElementById('wt-output');
    var result = document.getElementById('wt-result');
    var status = document.getElementById('wt-status');
    var convertBtn = document.getElementById('wt-convert');
    var copyBtn = document.getElementById('wt-copy');
    var romanToggle = document.getElementById('wt-roman');
    var mode = 'cardinal';

    TCTP.initModeGroup('.tc-modes[data-group="wt-mode"]', function (val) {
        mode = val;
    });

    var ones = {
        'zero':0,'one':1,'two':2,'three':3,'four':4,'five':5,'six':6,'seven':7,
        'eight':8,'nine':9,'ten':10,'eleven':11,'twelve':12,'thirteen':13,
        'fourteen':14,'fifteen':15,'sixteen':16,'seventeen':17,'eighteen':18,
        'nineteen':19,'twenty':20,'thirty':30,'forty':40,'fifty':50,
        'sixty':60,'seventy':70,'eighty':80,'ninety':90
    };

    var scales = {
        'thousand': 1000, 'million': 1e6, 'billion': 1e9, 'trillion': 1e12,
        'quadrillion': 1e15, 'quintillion': 1e18, 'sextillion': 1e21
    };

    function wordToNumber(text) {
        text = text.toLowerCase().replace(/[^a-z\s\-]/g, '').replace(/-/g, ' ').trim();
        if (!text) return NaN;

        var words = text.split(/\s+/);
        var current = 0, total = 0, chunk = 0;

        for (var i = 0; i < words.length; i++) {
            var w = words[i];
            if (w === 'and') continue;
            if (w === 'hundred') {
                chunk = chunk === 0 ? 100 : chunk * 100;
            } else if (scales[w]) {
                chunk = chunk === 0 ? scales[w] : chunk * scales[w];
                total += chunk;
                chunk = 0;
            } else if (ones[w] !== undefined) {
                chunk += ones[w];
            } else {
                var num = parseInt(w, 10);
                if (!isNaN(num)) chunk += num;
            }
        }
        total += chunk;
        return total;
    }

    function toOrdinal(n) {
        var s = ['th','st','nd','rd'];
        var v = n % 100;
        return n + (s[(v - 20) % 10] || s[v] || s[0]);
    }

    function toRoman(num) {
        if (num <= 0 || num > 3999999) return '';
        var vals = [1000000,900000,500000,400000,100000,90000,50000,40000,10000,9000,5000,4000,1000,900,500,400,100,90,50,40,10,9,5,4,1];
        var syms = ['M\u0305','C\u0305M\u0305','D\u0305','C\u0305D\u0305','C\u0305','X\u0305C\u0305','L\u0305','X\u0305L\u0305','X\u0305','I\u0305X\u0305','V\u0305','I\u0305V\u0305','M','CM','D','CD','C','XC','L','XL','X','IX','V','IV','I'];
        var result = '';
        for (var i = 0; i < vals.length; i++) {
            while (num >= vals[i]) { result += syms[i]; num -= vals[i]; }
        }
        return result;
    }

    function convert() {
        var text = input.value.trim();
        if (!text) { TCTP.toast('Please enter written numbers.', '\u26A0\uFE0F'); return; }
        status.textContent = 'Converting...';
        result.style.display = '';

        var lines = text.split('\n');
        var results = [];
        var useRoman = romanToggle.checked;

        for (var i = 0; i < lines.length; i++) {
            var line = lines[i].trim();
            if (!line) continue;
            var num = wordToNumber(line);
            if (isNaN(num)) { results.push(line + ' \u2192 [cannot parse]'); continue; }
            var formatted = num.toLocaleString();
            if (mode === 'ordinal') formatted = toOrdinal(num);
            else if (mode === 'currency') formatted = '$' + num.toLocaleString();
            if (useRoman) formatted += ' (' + toRoman(num) + ')';
            results.push(line + ' \u2192 ' + formatted);
        }

        output.textContent = results.join('\n');
        status.textContent = results.length + ' line' + (results.length === 1 ? '' : 's') + ' converted';
        TCTP.toast('Conversion complete!', '\u2705');
    }

    convertBtn.addEventListener('click', convert);
    copyBtn.addEventListener('click', function () { TCTP.copyText(output.textContent, 'Result'); });
})();
