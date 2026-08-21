/**
 * Random Choice Picker — Tool JS
 *
 * Textarea for choices, pick button with spin animation,
 * shows result prominently. Stats: total, pick count.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var textarea = document.getElementById('tc-rc-input');
        var pickBtn = document.getElementById('tc-rc-pick');
        if (!textarea || !pickBtn || textarea.dataset.tcInit) return;
        textarea.dataset.tcInit = '1';

        var pickCountInput = document.getElementById('tc-rc-pick-count');
        var allowDupChk = document.getElementById('tc-rc-allow-dup');
        var clearBtn = document.getElementById('tc-rc-clear');
        var displayWrap = document.getElementById('tc-rc-display');
        var spinResult = document.getElementById('tc-rc-spin-result');
        var output = document.getElementById('tc-rc-output');
        var resultText = document.getElementById('tc-rc-result-text');

        var pickCount = 0;

        function getChoices() {
            return textarea.value.split('\n').map(function (line) {
                return line.trim();
            }).filter(function (line) {
                return line.length > 0;
            });
        }

        function updateStats(total) {
            var totalEl = document.getElementById('tc-rc-stat-total');
            var picksEl = document.getElementById('tc-rc-stat-picks');
            if (totalEl) totalEl.textContent = total;
            if (picksEl) picksEl.textContent = pickCount;
        }

        pickBtn.addEventListener('click', function () {
            var choices = getChoices();
            if (!choices.length) {
                TCTP.toast('Please enter at least one choice.', '\u26A0\uFE0F');
                return;
            }

            var howMany = pickCountInput ? Math.max(1, Math.min(100, parseInt(pickCountInput.value, 10) || 1)) : 1;
            var allowDup = allowDupChk ? allowDupChk.checked : false;
            if (!allowDup && howMany > choices.length) {
                TCTP.toast('Not enough unique choices \u2014 allow duplicates or lower the pick count.', '\u26A0\uFE0F');
                return;
            }

            function pickWinners() {
                var pool = choices.slice();
                var winners = [];
                for (var i = 0; i < howMany; i++) {
                    if (allowDup) {
                        winners.push(choices[Math.floor(Math.random() * choices.length)]);
                    } else {
                        var idx = Math.floor(Math.random() * pool.length);
                        winners.push(pool.splice(idx, 1)[0]);
                    }
                }
                return winners;
            }

            // If the spin display is missing, pick instantly instead of failing
            if (!spinResult) {
                finishPick(pickWinners());
                return;
            }

            pickBtn.disabled = true;
            if (displayWrap) displayWrap.style.display = '';
            spinResult.classList.add('tc-rch-spinning');

            var spins = 0;
            var maxSpins = 20;

            var spinInterval = setInterval(function () {
                spinResult.textContent = choices[Math.floor(Math.random() * choices.length)];
                spins++;
                if (spins >= maxSpins) {
                    clearInterval(spinInterval);
                    var winners = pickWinners();
                    spinResult.textContent = winners.join(', ');
                    spinResult.classList.remove('tc-rch-spinning');
                    spinResult.classList.add('tc-rch-reveal');
                    pickBtn.disabled = false;

                    setTimeout(function () {
                        spinResult.classList.remove('tc-rch-reveal');
                    }, 600);

                    finishPick(winners);
                }
            }, 80);

            function finishPick(winners) {
                pickCount++;
                updateStats(choices.length);

                var entry = 'Pick #' + pickCount + ': ' + winners.join(', ');
                if (output) output.value = output.value ? output.value + '\n' + entry : entry;
                if (resultText) resultText.value = winners.join('\n');

                TCTP.toast('Picked: ' + winners.join(', '));
            }
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                textarea.value = '';
                if (output) output.value = '';
                if (resultText) resultText.value = '';
                if (spinResult) spinResult.textContent = '';
                if (displayWrap) displayWrap.style.display = 'none';
                if (pickCountInput) pickCountInput.value = '1';
                pickCount = 0;
                updateStats(0);
                TCTP.toast('Cleared.', '\uD83D\uDD01');
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
