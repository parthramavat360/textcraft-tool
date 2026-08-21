/**
 * Random Month Generator — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var out = document.getElementById('tc-rm-output');
        var generateBtn = document.getElementById('tc-rm-generate');
        if (!out || !generateBtn || out.dataset.tcInit) return;
        out.dataset.tcInit = '1';

        var resultText = document.getElementById('tc-rm-result-text');

        var MONTHS = [
            { name: 'January',   days: 31, season: 'Winter' },
            { name: 'February',  days: 28, season: 'Winter' },
            { name: 'March',     days: 31, season: 'Spring' },
            { name: 'April',     days: 30, season: 'Spring' },
            { name: 'May',       days: 31, season: 'Spring' },
            { name: 'June',      days: 30, season: 'Summer' },
            { name: 'July',      days: 31, season: 'Summer' },
            { name: 'August',    days: 31, season: 'Summer' },
            { name: 'September', days: 30, season: 'Autumn' },
            { name: 'October',   days: 31, season: 'Autumn' },
            { name: 'November',  days: 30, season: 'Autumn' },
            { name: 'December',  days: 31, season: 'Winter' }
        ];

        var CHECK_IDS = [
            'tc-rm-jan', 'tc-rm-feb', 'tc-rm-mar', 'tc-rm-apr',
            'tc-rm-may', 'tc-rm-jun', 'tc-rm-jul', 'tc-rm-aug',
            'tc-rm-sep', 'tc-rm-oct', 'tc-rm-nov', 'tc-rm-dec'
        ];

        function getSelectedMonths() {
            var selected = [];
            for (var i = 0; i < CHECK_IDS.length; i++) {
                var cb = document.getElementById(CHECK_IDS[i]);
                if (cb && cb.checked) {
                    selected.push(MONTHS[i]);
                }
            }
            return selected;
        }

        function isLeapYear(year) {
            return (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
        }

        function randomInt(max) {
            var arr = new Uint32Array(1);
            crypto.getRandomValues(arr);
            return arr[0] % max;
        }

        generateBtn.addEventListener('click', function () {
            var countInput = document.getElementById('tc-rm-count');
            var count = countInput ? Math.max(1, Math.min(100, parseInt(countInput.value, 10) || 5)) : 5;

            var showName = document.getElementById('tc-rm-show-name');
            var showDays = document.getElementById('tc-rm-show-days');
            var showSeason = document.getElementById('tc-rm-show-season');
            var doName = showName ? showName.checked : true;
            var doDays = showDays ? showDays.checked : true;
            var doSeason = showSeason ? showSeason.checked : true;

            var pool = getSelectedMonths();
            if (!pool.length) {
                TCTP.toast('Please select at least one month.', '\u26A0\uFE0F');
                return;
            }

            var thisYear = new Date().getFullYear();
            var febDays = isLeapYear(thisYear) ? 29 : 28;

            var lines = [];
            for (var i = 0; i < count; i++) {
                var m = pool[randomInt(pool.length)];
                var parts = [];
                if (doName) parts.push(m.name);
                if (doDays) {
                    var days = m.name === 'February' ? febDays : m.days;
                    parts.push(days + ' days');
                }
                if (doSeason) parts.push(m.season);
                lines.push(parts.join(' | '));
            }

            var joined = lines.join('\n');
            out.value = joined;
            if (resultText) resultText.value = joined;

            TCTP.updateResultPanel('N/A', count + ' month(s)', 'N/A', 'Done');

            TCTP.toast(count + ' random month(s) generated!');
        });

        var copyBtn = document.getElementById('tc-rm-copy');
        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                TCTP.copyText(out.value, 'Months');
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
