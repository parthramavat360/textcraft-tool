/**
 * Random Date Generator — Tool JS
 *
 * Start/end date inputs, format select, count input,
 * generate, copy.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var output = document.getElementById('tc-rd-output');
    if (!output) return;

    var resultText = document.getElementById('tc-rd-result-text');

    function padZero(n) { return n < 10 ? '0' + n : '' + n; }

    var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'];

    function formatDate(date, format) {
        var y = date.getFullYear();
        var m = padZero(date.getMonth() + 1);
        var d = padZero(date.getDate());

        switch (format) {
            case 'm/d/Y': return m + '/' + d + '/' + y;
            case 'd/m/Y': return d + '/' + m + '/' + y;
            case 'written': return MONTHS[date.getMonth()] + ' ' + parseInt(d, 10) + ', ' + y;
            case 'Y-m-d':
            default: return y + '-' + m + '-' + d;
        }
    }

    var generateBtn = document.getElementById('tc-rd-generate');
    if (generateBtn) generateBtn.addEventListener('click', function () {
        var startInput = document.getElementById('tc-rd-start');
        var endInput = document.getElementById('tc-rd-end');
        var formatSel = document.getElementById('tc-rd-format');
        var countInput = document.getElementById('tc-rd-count');

        var dateFormat = formatSel && formatSel.value ? formatSel.value : 'Y-m-d';
        var startDate = startInput && startInput.value ? new Date(startInput.value) : new Date('2020-01-01');
        var endDate = endInput && endInput.value ? new Date(endInput.value) : new Date('2026-12-31');
        var count = countInput ? Math.max(1, Math.min(1000, parseInt(countInput.value, 10) || 10)) : 10;

        if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
            TCTP.toast('Please enter valid dates.', '\u26A0\uFE0F');
            return;
        }

        if (startDate >= endDate) {
            TCTP.toast('Start date must be before end date.', '\u26A0\uFE0F');
            return;
        }

        var startMs = startDate.getTime();
        var endMs = endDate.getTime();
        var results = [];

        for (var i = 0; i < count; i++) {
            var randomMs = startMs + Math.random() * (endMs - startMs);
            results.push(formatDate(new Date(randomMs), dateFormat));
        }

        output.value = results.join('\n');
        if (resultText) resultText.value = results.join('\n');

        TCTP.toast(count + ' random dates generated!');
    });

    var copyBtn = document.getElementById('tc-rd-copy');
    if (copyBtn) copyBtn.addEventListener('click', function () {
        TCTP.copyText(output.value, 'Dates');
    });

})();
