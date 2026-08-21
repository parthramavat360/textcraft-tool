/**
 * Word Frequency Counter — Tool JS
 *
 * Counts word frequencies and renders a bar list into #tc-wf-list.
 *
 * Widget IDs (widget-word-frequency.php):
 *  - checkboxes: wf-case, wf-ignore
 *  - tc-wf-input, tc-wf-analyze, tc-wf-copy
 *  - tc-wf-list (results container div — no output textarea)
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var inp = document.getElementById('tc-wf-input');
        var analyzeBtn = document.getElementById('tc-wf-analyze');
        if (!inp || !analyzeBtn || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var listEl = document.getElementById('tc-wf-list');
        var copyBtn = document.getElementById('tc-wf-copy');
        var chkCase = document.getElementById('wf-case');
        var chkIgnore = document.getElementById('wf-ignore');

        var commonWords = ['the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'is', 'it', 'as', 'by', 'with', 'was', 'are', 'be', 'has', 'had', 'have', 'not', 'this', 'that', 'from', 'they', 'we', 'he', 'she', 'you', 'me', 'my', 'his', 'her', 'our', 'their', 'its', 'do', 'so', 'up', 'out', 'can', 'will', 'just', 'than', 'then', 'also', 'about', 'more', 'some', 'all', 'would', 'could', 'should', 'very', 'if', 'no', 'yes', 'been', 'being', 'into', 'over', 'such', 'own', 'may', 'did', 'get', 'got', 'let', 'say', 'said', 'too', 'any', 'each', 'which', 'there', 'what', 'when', 'where', 'who', 'how', 'whom', 'these', 'those', 'am'];

        var lastReport = '';

        function escapeHtml(s) {
            return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        // ── Analyze button ───────────────────────────────────────

        analyzeBtn.addEventListener('click', function () {
            var text = inp.value;
            if (!text.trim()) {
                TCTP.toast('Please enter some text.', '\u26A0\uFE0F');
                return;
            }

            var cs = chkCase && chkCase.checked;
            var ignore = chkIgnore && chkIgnore.checked;

            var words = text.match(/[a-zA-Z0-9']+/g);
            if (!words || !words.length) {
                TCTP.toast('No words found.', '\u26A0\uFE0F');
                return;
            }

            var freq = {};
            words.forEach(function (w) {
                var key = cs ? w : w.toLowerCase();
                if (ignore && commonWords.indexOf(key) !== -1) return;
                freq[key] = (freq[key] || 0) + 1;
            });

            var sorted = Object.keys(freq).sort(function (a, b) { return freq[b] - freq[a]; });
            var total = words.length;

            if (!sorted.length) {
                lastReport = '';
                if (listEl) listEl.innerHTML = '<p style="color:#64748b">No words left after filtering.</p>';
                TCTP.toast('No words left after filtering.', '\u26A0\uFE0F');
                return;
            }

            var maxFreq = freq[sorted[0]];

            lastReport = sorted.map(function (w) {
                var pct = total ? (freq[w] / total * 100).toFixed(1) : '0.0';
                return w + ': ' + freq[w] + ' (' + pct + '%)';
            }).join('\n');

            if (listEl) {
                var html = '';
                sorted.slice(0, 100).forEach(function (w) {
                    var pct = total ? (freq[w] / total * 100).toFixed(1) : '0.0';
                    var barW = maxFreq ? (freq[w] / maxFreq * 100) : 0;
                    html += '<div class="tc-freq-bar-row" style="display:flex;align-items:center;gap:10px;margin-bottom:6px">'
                        + '<span class="tc-freq-word" style="min-width:120px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">' + escapeHtml(w) + '</span>'
                        + '<div class="tc-freq-bar-track" style="flex:1;height:8px;background:#eef1f6;border-radius:4px;overflow:hidden">'
                        + '<div class="tc-freq-bar-fill" style="width:' + barW + '%;height:100%;background:var(--tc-accent,#2563eb)"></div></div>'
                        + '<span class="tc-freq-num" style="min-width:96px;text-align:right;color:#64748b;font-size:.9em">' + freq[w] + ' (' + pct + '%)</span>'
                        + '</div>';
                });
                listEl.innerHTML = html;
            }

            TCTP.updateResultPanel(text.length.toLocaleString() + ' chars', sorted.length.toLocaleString() + ' unique words', '\u2014', 'Done');

            TCTP.toast('Analysis complete. ' + sorted.length + ' unique words.');
        });

        // ── Copy ─────────────────────────────────────────────────

        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                TCTP.copyText(lastReport, 'Word frequency');
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
