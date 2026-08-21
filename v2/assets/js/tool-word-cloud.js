/**
 * Word Cloud Generator — Tool JS
 *
 * Renders a frequency-sized word cloud into #tc-wc-cloud and a
 * frequency data summary into #tc-wc-output.
 *
 * Widget IDs (widget-word-cloud.php):
 *  - tc-wc-input, tc-wc-generate, tc-wc-clear
 *  - tc-wc-cloud (cloud container div)
 *  - tc-wc-output (frequency data textarea)
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var inp = document.getElementById('tc-wc-input');
        var generateBtn = document.getElementById('tc-wc-generate');
        if (!inp || !generateBtn || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var cloud = document.getElementById('tc-wc-cloud');
        var clearBtn = document.getElementById('tc-wc-clear');
        var out = document.getElementById('tc-wc-output');

        var stopWords = ['the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'is', 'it', 'as', 'by', 'with', 'was', 'are', 'be', 'has', 'had', 'have', 'not', 'this', 'that', 'from', 'they', 'we', 'he', 'she', 'you', 'me', 'my', 'his', 'her', 'our', 'their', 'its', 'no', 'yes', 'if', 'do', 'so', 'up', 'out', 'can', 'will', 'just', 'than', 'them', 'then', 'also', 'about', 'more', 'some', 'all', 'would', 'could', 'should', 'very'];

        var colors = ['#e74c3c', '#3498db', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c', '#e67e22', '#34495e', '#e91e63', '#00bcd4'];

        // ── Generate button ──────────────────────────────────────

        generateBtn.addEventListener('click', function () {
            var text = inp.value;
            if (!text.trim()) {
                TCTP.toast('Please enter some text.', '\u26A0\uFE0F');
                return;
            }

            var words = text.toLowerCase().match(/[a-z0-9']+/g);
            if (!words || !words.length) {
                TCTP.toast('No words found.', '\u26A0\uFE0F');
                return;
            }

            var freq = {};
            words.forEach(function (w) {
                if (w.length < 2) return;
                if (stopWords.indexOf(w) !== -1) return;
                freq[w] = (freq[w] || 0) + 1;
            });

            var sorted = Object.keys(freq).sort(function (a, b) { return freq[b] - freq[a]; });
            var top = sorted.slice(0, 80);
            if (!top.length) {
                TCTP.toast('No valid words found.', '\u26A0\uFE0F');
                return;
            }

            var maxFreq = freq[top[0]];
            var minFreq = freq[top[top.length - 1]];

            if (cloud) {
                cloud.innerHTML = '';
                top.forEach(function (word) {
                    var span = document.createElement('span');
                    span.textContent = word;
                    span.className = 'tc-wc-word';
                    var ratio = maxFreq === minFreq ? 1 : (freq[word] - minFreq) / (maxFreq - minFreq);
                    span.style.fontSize = Math.round(14 + ratio * 46) + 'px';
                    span.style.color = colors[Math.floor(Math.random() * colors.length)];
                    span.style.display = 'inline-block';
                    span.style.padding = '4px 8px';
                    span.style.lineHeight = '1.2';
                    span.title = word + ': ' + freq[word];
                    cloud.appendChild(span);
                });
            }

            if (out) {
                out.value = top.map(function (w) { return w + ': ' + freq[w]; }).join('\n');
            }

            TCTP.updateResultPanel(words.length.toLocaleString() + ' words', top.length.toLocaleString() + ' words', (top.length < words.length ? ((1 - top.length / words.length) * 100).toFixed(1) + '%' : '0%'), 'Done');

            TCTP.toast('Word cloud generated (' + top.length + ' words).');
        });

        // ── Clear button ─────────────────────────────────────────

        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                inp.value = '';
                if (cloud) cloud.innerHTML = '';
                if (out) out.value = '';
                TCTP.toast('Cleared.', '\uD83D\uDDD1\uFE0F');
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
