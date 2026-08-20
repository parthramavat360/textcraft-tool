/**
 * Random Date Generator — Tool JS
 *
 * Start/end date inputs, format select, count input,
 * generate, copy, download.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var dateFormat = 'YYYY-MM-DD';
    var output = document.getElementById('tc-rdate-output');
    if (!output) return;

    document.querySelectorAll('.tctp-modes[data-group="rdate-format"] .tctp-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            dateFormat = btn.getAttribute('data-val') || 'YYYY-MM-DD';
        });
    });

    function padZero(n) { return n < 10 ? '0' + n : '' + n; }

    function formatDate(date, format) {
        var y = date.getFullYear();
        var m = padZero(date.getMonth() + 1);
        var d = padZero(date.getDate());
        var h = padZero(date.getHours());
        var min = padZero(date.getMinutes());
        var s = padZero(date.getSeconds());

        switch (format) {
            case 'DD/MM/YYYY': return d + '/' + m + '/' + y;
            case 'MM/DD/YYYY': return m + '/' + d + '/' + y;
            case 'YYYY-MM-DD': return y + '-' + m + '-' + d;
            case 'DD-MM-YYYY': return d + '-' + m + '-' + y;
            case 'DD.MM.YYYY': return d + '.' + m + '.' + y;
            case 'YYYY/MM/DD': return y + '/' + m + '/' + d;
            case 'MMMM D, YYYY':
                var months = ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'];
                return months[date.getMonth()] + ' ' + parseInt(d) + ', ' + y;
            case 'YYYY-MM-DD HH:mm:ss': return y + '-' + m + '-' + d + ' ' + h + ':' + min + ':' + s;
            default: return y + '-' + m + '-' + d;
        }
    }

    var generateBtn = document.getElementById('tc-rdate-generate');
    if (generateBtn) generateBtn.addEventListener('click', function () {
        var startInput = document.getElementById('tc-rdate-start');
        var endInput = document.getElementById('tc-rdate-end');
        var countInput = document.getElementById('tc-rdate-count');

        var startDate = startInput ? new Date(startInput.value) : new Date('2020-01-01');
        var endDate = endInput ? new Date(endInput.value) : new Date('2025-12-31');
        var count = countInput ? Math.max(1, Math.min(1000, parseInt(countInput.value) || 10)) : 10;

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

        var countEl = document.getElementById('tc-rdate-stat-count');
        if (countEl) countEl.textContent = count;

        var statsEl = document.getElementById('tc-rdate-stats');
        if (statsEl) statsEl.style.display = '';

        TCTP.toast(count + ' random dates generated!');
    });

    var copyBtn = document.getElementById('tc-rdate-copy');
    if (copyBtn) copyBtn.addEventListener('click', function () {
        TCTP.copyText(output.value, 'Dates');
    });

    var downloadBtn = document.getElementById('tc-rdate-download');
    if (downloadBtn) downloadBtn.addEventListener('click', function () {
        TCTP.downloadText(output.value, 'random-dates.txt');
    });

})();
