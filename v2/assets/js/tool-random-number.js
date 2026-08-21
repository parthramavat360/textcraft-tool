/**
 * Random Number Generator — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var numType = 'integer';
    var sepMode = 'newline';
    var out = document.getElementById('tc-rn-output');
    var generateBtn = document.getElementById('tc-rn-generate');
    if (!out || !generateBtn) return;

    var PRESETS = {
        dice:    { min: 1,  max: 6,  count: 1,  unique: false, sort: false },
        coin:    { min: 0,  max: 1,  count: 1,  unique: false, sort: false },
        percent: { min: 0,  max: 100, count: 10, unique: false, sort: false },
        lottery: { min: 1,  max: 49,  count: 6,  unique: true,  sort: true  },
        pin:     { min: 1000, max: 9999, count: 1, unique: false, sort: false }
    };

    // Type buttons
    document.querySelectorAll('.tc-modes[data-group="rn-type"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            numType = btn.getAttribute('data-val');
            var decRow = document.getElementById('tc-rn-decimal-row');
            if (decRow) decRow.style.display = numType === 'decimal' ? '' : 'none';
        });
    });

    // Separator buttons
    document.querySelectorAll('.tc-modes[data-group="rn-sep"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            sepMode = btn.getAttribute('data-val');
        });
    });

    // Presets
    document.querySelectorAll('[data-preset]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var p = PRESETS[btn.getAttribute('data-preset')];
            if (!p) return;
            document.getElementById('tc-rn-min').value = p.min;
            document.getElementById('tc-rn-max').value = p.max;
            document.getElementById('tc-rn-count').value = p.count;
            document.getElementById('tc-rn-nodup').checked = !!p.unique;
            document.getElementById('tc-rn-sort').checked = !!p.sort;
            TCTP.activateBtn(btn);
        });
    });

    function genOne(minVal, maxVal, decPlaces) {
        if (numType === 'decimal') {
            return parseFloat((Math.random() * (maxVal - minVal) + minVal).toFixed(decPlaces));
        }
        if (numType === 'even') {
            var n = Math.round(Math.random() * (maxVal - minVal) + minVal);
            if (n % 2 !== 0) n = (n + 1 <= maxVal) ? n + 1 : n - 1;
            return n;
        }
        if (numType === 'odd') {
            var n = Math.round(Math.random() * (maxVal - minVal) + minVal);
            if (n % 2 === 0) n = (n + 1 <= maxVal) ? n + 1 : n - 1;
            return n;
        }
        return Math.floor(Math.random() * (maxVal - minVal + 1)) + Math.ceil(minVal);
    }

    function formatNum(n) {
        return n.toLocaleString('en-US');
    }

    generateBtn.addEventListener('click', function () {
        var minVal = parseFloat(document.getElementById('tc-rn-min').value);
        var maxVal = parseFloat(document.getElementById('tc-rn-max').value);
        var count = Math.max(1, Math.min(1000, parseInt(document.getElementById('tc-rn-count').value) || 10));
        var nodup = document.getElementById('tc-rn-nodup').checked;
        var doSort = document.getElementById('tc-rn-sort').checked;
        var decPlaces = parseInt(document.getElementById('tc-rn-decimal-places').value) || 2;

        if (isNaN(minVal) || isNaN(maxVal)) { out.value = 'Please enter valid min and max values.'; return; }
        if (minVal > maxVal) { out.value = 'Minimum must be <= Maximum.'; return; }

        var numbers = [], seen = {};
        var attempts = 0;
        while (numbers.length < count && attempts < count * 100) {
            attempts++;
            var n = genOne(minVal, maxVal, decPlaces);
            if (nodup && seen[n]) continue;
            seen[n] = true;
            numbers.push(n);
        }

        if (doSort) numbers.sort(function (a, b) { return a - b; });

        var formatted = numbers.map(function (n) { return numType === 'decimal' ? n.toFixed(decPlaces) : String(n); });
        switch (sepMode) {
            case 'comma': out.value = formatted.join(', '); break;
            case 'space': out.value = formatted.join(' '); break;
            case 'json': out.value = JSON.stringify(numbers, null, 2); break;
            default: out.value = formatted.join('\n');
        }

        TCTP.updateResultPanel('N/A', numbers.length + ' number(s)', 'N/A', 'Done');

        var minRes = Math.min.apply(null, numbers);
        var maxRes = Math.max.apply(null, numbers);
        var sum = numbers.reduce(function (s, n) { return s + n; }, 0);

        var statCount = document.getElementById('tc-rn-stat-count');
        var statMin = document.getElementById('tc-rn-stat-min');
        var statMax = document.getElementById('tc-rn-stat-max');
        var statAvg = document.getElementById('tc-rn-stat-avg');
        if (statCount) statCount.textContent = numbers.length;
        if (statMin) statMin.textContent = formatNum(minRes);
        if (statMax) statMax.textContent = formatNum(maxRes);
        if (statAvg) statAvg.textContent = (sum / numbers.length).toFixed(2);
        TCTP.toast(numbers.length + ' numbers generated!');
    });

    var copyBtn = document.getElementById('tc-rn-copy');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            TCTP.copyText(out.value, 'Numbers');
        });
    }

})();