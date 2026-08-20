/**
 * Random Choice Picker — Tool JS
 *
 * Textarea for choices, generate button with spin animation,
 * shows result prominently. Stats: total, pick count.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var textarea = document.getElementById('tc-rch-input');
    if (!textarea) return;

    var pickCount = 0;

    var generateBtn = document.getElementById('tc-rch-generate');
    if (generateBtn) generateBtn.addEventListener('click', function () {
        var text = textarea.value.trim();
        if (!text) {
            TCTP.toast('Please enter some choices.', '\u26A0\uFE0F');
            return;
        }

        var choices = text.split('\n').map(function (line) {
            return line.trim();
        }).filter(function (line) {
            return line.length > 0;
        });

        if (choices.length === 0) {
            TCTP.toast('Please enter at least one choice.', '\u26A0\uFE0F');
            return;
        }

        var resultEl = document.getElementById('tc-rch-result');
        if (!resultEl) return;

        generateBtn.disabled = true;
        resultEl.classList.add('tc-rch-spinning');

        var spinCount = 0;
        var maxSpins = 20;
        var spinInterval = setInterval(function () {
            var tempIdx = Math.floor(Math.random() * choices.length);
            resultEl.textContent = choices[tempIdx];
            spinCount++;
            if (spinCount >= maxSpins) {
                clearInterval(spinInterval);
                var finalIdx = Math.floor(Math.random() * choices.length);
                resultEl.textContent = choices[finalIdx];
                resultEl.classList.remove('tc-rch-spinning');
                resultEl.classList.add('tc-rch-reveal');
                generateBtn.disabled = false;

                setTimeout(function () {
                    resultEl.classList.remove('tc-rch-reveal');
                }, 600);

                pickCount++;
                var totalEl = document.getElementById('tc-rch-stat-total');
                var pickEl = document.getElementById('tc-rch-stat-picks');
                if (totalEl) totalEl.textContent = choices.length;
                if (pickEl) pickEl.textContent = pickCount;

                var statsEl = document.getElementById('tc-rch-stats');
                if (statsEl) statsEl.style.display = '';
            }
        }, 80);
    });

})();
