/**
 * Age Calculator — Tool JS
 * Calculate exact age from birth date.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var calcBtn = document.getElementById('tc-age-calculate');
    if (!calcBtn) return;

    // ── Populate day select ────────────────────────────────────

    var daySelect = document.getElementById('tc-age-day');
    if (daySelect) {
        for (var i = 1; i <= 31; i++) {
            var opt = document.createElement('option');
            opt.value = i;
            opt.textContent = i;
            daySelect.appendChild(opt);
        }
        daySelect.value = 15;
    }

    // ── Zodiac signs ───────────────────────────────────────────

    function getZodiac(month, day) {
        var signs = [
            { name: 'Capricorn', start: [1, 1], end: [1, 19] },
            { name: 'Aquarius', start: [1, 20], end: [2, 18] },
            { name: 'Pisces', start: [2, 19], end: [3, 20] },
            { name: 'Aries', start: [3, 21], end: [4, 19] },
            { name: 'Taurus', start: [4, 20], end: [5, 20] },
            { name: 'Gemini', start: [5, 21], end: [6, 20] },
            { name: 'Cancer', start: [6, 21], end: [7, 22] },
            { name: 'Leo', start: [7, 23], end: [8, 22] },
            { name: 'Virgo', start: [8, 23], end: [9, 22] },
            { name: 'Libra', start: [9, 23], end: [10, 22] },
            { name: 'Scorpio', start: [10, 23], end: [11, 21] },
            { name: 'Sagittarius', start: [11, 22], end: [12, 21] },
            { name: 'Capricorn', start: [12, 22], end: [12, 31] }
        ];

        for (var i = 0; i < signs.length; i++) {
            var s = signs[i];
            if ((month === s.start[0] && day >= s.start[1]) || (month === s.end[0] && day <= s.end[1])) {
                return s.name;
            }
        }
        return 'Capricorn';
    }

    // ── Calculate ──────────────────────────────────────────────

    calcBtn.addEventListener('click', function () {
        var day = parseInt(daySelect ? daySelect.value : 15, 10);
        var month = parseInt(document.getElementById('tc-age-month').value, 10);
        var year = parseInt(document.getElementById('tc-age-year').value, 10);

        if (isNaN(year) || year < 1900 || year > 2026) {
            TCTP.toast('Please enter a valid year (1900-2026).', '\u26A0\uFE0F');
            return;
        }

        var birthDate = new Date(year, month, day);
        var targetInput = document.getElementById('tc-age-target');
        var targetDate = targetInput && targetInput.value ? new Date(targetInput.value) : new Date();

        if (birthDate > targetDate) {
            TCTP.toast('Birth date cannot be in the future.', '\u26A0\uFE0F');
            return;
        }

        // Calculate years, months, days
        var y = targetDate.getFullYear() - birthDate.getFullYear();
        var m = targetDate.getMonth() - birthDate.getMonth();
        var d = targetDate.getDate() - birthDate.getDate();

        if (d < 0) {
            m--;
            var prevMonth = new Date(targetDate.getFullYear(), targetDate.getMonth(), 0);
            d += prevMonth.getDate();
        }
        if (m < 0) {
            y--;
            m += 12;
        }

        // Total days
        var diffMs = targetDate.getTime() - birthDate.getTime();
        var totalDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
        var totalHours = Math.floor(diffMs / (1000 * 60 * 60));

        // Next birthday
        var nextBday = new Date(targetDate.getFullYear(), birthDate.getMonth(), birthDate.getDate());
        if (nextBday <= targetDate) {
            nextBday = new Date(targetDate.getFullYear() + 1, birthDate.getMonth(), birthDate.getDate());
        }
        var daysUntilBday = Math.ceil((nextBday.getTime() - targetDate.getTime()) / (1000 * 60 * 60 * 24));

        // Zodiac
        var zodiac = getZodiac(birthDate.getMonth() + 1, birthDate.getDate());

        // Display
        var results = document.getElementById('tc-age-results');
        if (results) results.style.display = '';

        var ageBig = document.getElementById('tc-age-result-age');
        if (ageBig) ageBig.textContent = y;

        var ageSub = document.getElementById('tc-age-result-sub');
        if (ageSub) ageSub.textContent = 'years, ' + m + ' months, ' + d + ' days';

        var elMonths = document.getElementById('tc-age-months');
        if (elMonths) elMonths.textContent = (y * 12 + m);

        var elDays = document.getElementById('tc-age-days');
        if (elDays) elDays.textContent = totalDays.toLocaleString();

        var elHours = document.getElementById('tc-age-hours');
        if (elHours) elHours.textContent = totalHours.toLocaleString();

        var elTotalDays = document.getElementById('tc-age-total-days');
        if (elTotalDays) elTotalDays.textContent = totalDays.toLocaleString();

        var elZodiac = document.getElementById('tc-age-zodiac');
        if (elZodiac) elZodiac.textContent = zodiac;

        var elBday = document.getElementById('tc-age-birthday');
        if (elBday) elBday.textContent = daysUntilBday + ' days';

        TCTP.toast('Age calculated: ' + y + ' years, ' + m + ' months, ' + d + ' days', '\u2705');
    });
})();
