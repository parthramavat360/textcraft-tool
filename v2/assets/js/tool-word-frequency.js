/**
 * Word Frequency Counter — Tool JS
 *
 * Counts word frequencies and renders a bar list into #tc-wf-freq.
 *
 * Widget IDs (widget-word-frequency.php):
 *  - checkboxes: tc-wf-case
 *  - inputs: tc-wf-input, tc-wf-ignore-list
 *  - tc-wf-analyze, tc-wf-copy
 *  - tc-wf-freq (results container div)
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

        var freqEl = document.getElementById('tc-wf-freq');
        var copyBtn = document.getElementById('tc-wf-copy');
        var chkCase = document.getElementById('tc-wf-case');
        var ignoreWrap = document.querySelector('.tc-wf-ignore-wrap');
        var ignoreInput = document.getElementById('tc-wf-ignore-list');
        var previewOrig = document.getElementById('tc-wf-preview-orig');
        var freqModes = document.querySelectorAll('.tc-wf-modes .tc-rsz-mode-card');

        var statChars = document.getElementById('tc-wf-chars');
        var statWords = document.getElementById('tc-wf-words');
        var statUnique = document.getElementById('tc-wf-unique');
        var statTop = document.getElementById('tc-wf-top');

        var commonWords = ['the','a','an','and','or','but','in','on','at','to','for','of','is','it','as','by','with','was','are','be','has','had','have','not','this','that','from','they','we','he','she','you','me','my','his','her','our','their','its','do','so','up','out','can','will','just','than','then','also','about','more','some','all','would','could','should','very','if','no','yes','been','being','into','over','such','own','may','did','get','got','let','say','said','too','any','each','which','there','what','when','where','who','how','whom','these','those','am'];

        var currentMode = 'content';
        var lastReport = '';
        var debounceTimer = null;

        // Mode card click
        freqModes.forEach(function (card) {
            card.addEventListener('click', function () {
                freqModes.forEach(function (c) { c.classList.remove('sel'); });
                card.classList.add('sel');
                currentMode = card.getAttribute('data-val');
                if (ignoreWrap) {
                    ignoreWrap.style.display = currentMode === 'custom' ? '' : 'none';
                }
                analyze();
            });
        });

        // Live analysis on input
        inp.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(analyze, 300);
        });

        // Analyze button
        analyzeBtn.addEventListener('click', analyze);

        // Copy
        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                TCTP.copyText(lastReport, 'Word frequency');
            });
        }

        function escapeHtml(s) {
            return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function analyze() {
            var text = inp.value;
            var wordCount = text.trim() ? text.trim().split(/\s+/).length : 0;
            var chars = text.length;

            if (previewOrig) previewOrig.value = text;

            // Stats
            if (statChars) statChars.textContent = chars.toLocaleString();
            if (statWords) statWords.textContent = wordCount.toLocaleString();

            if (!text.trim()) {
                if (statUnique) statUnique.textContent = '0';
                if (statTop) statTop.innerHTML = '&mdash;';
                if (freqEl) freqEl.innerHTML = '<p style="color:#64748b;padding:12px 0">Paste text and click Analyze.</p>';
                TCTP.updateResultPanel('0 chars', '0 words', '—', 'Idle');
                return;
            }

            var words = text.match(/[a-zA-Z0-9']+/g);
            if (!words || !words.length) {
                if (statUnique) statUnique.textContent = '0';
                if (statTop) statTop.innerHTML = '&mdash;';
                if (freqEl) freqEl.innerHTML = '<p style="color:#64748b;padding:12px 0">No words found.</p>';
                TCTP.updateResultPanel(chars.toLocaleString() + ' chars', '0 words', '—', 'No Words');
                return;
            }

            var cs = chkCase && chkCase.checked;

            // Determine ignore list
            var ignoreList = [];
            if (currentMode === 'content' || currentMode === 'all') {
                ignoreList = currentMode === 'content' ? commonWords : [];
            } else if (currentMode === 'custom' && ignoreInput && ignoreInput.value.trim()) {
                ignoreList = ignoreInput.value.split(',').map(function (w) { return w.trim().toLowerCase(); }).filter(Boolean);
            }

            var freq = {};
            words.forEach(function (w) {
                var key = cs ? w : w.toLowerCase();
                if (ignoreList.length && ignoreList.indexOf(key) !== -1) return;
                freq[key] = (freq[key] || 0) + 1;
            });

            var sorted = Object.keys(freq).sort(function (a, b) { return freq[b] - freq[a]; });
            var total = words.length;

            if (!sorted.length) {
                if (statUnique) statUnique.textContent = '0';
                if (statTop) statTop.innerHTML = '&mdash;';
                if (freqEl) freqEl.innerHTML = '<p style="color:#64748b;padding:12px 0">No words left after filtering.</p>';
                TCTP.updateResultPanel(chars.toLocaleString() + ' chars', '0 words', '—', 'Filtered');
                TCTP.toast('No words left after filtering.', '\u26A0\uFE0F');
                return;
            }

            var uniqueCount = sorted.length;
            var maxFreq = freq[sorted[0]];
            var topWord = sorted[0] + ' (' + freq[sorted[0]] + ')';

            // Stats
            if (statUnique) statUnique.textContent = uniqueCount.toLocaleString();
            if (statTop) statTop.textContent = topWord;

            // Build report text
            lastReport = sorted.map(function (w) {
                var pct = total ? (freq[w] / total * 100).toFixed(1) : '0.0';
                return w + ': ' + freq[w] + ' (' + pct + '%)';
            }).join('\n');

            // Build bar chart
            if (freqEl) {
                var html = '';
                sorted.slice(0, 100).forEach(function (w, i) {
                    var pct = total ? (freq[w] / total * 100).toFixed(1) : '0.0';
                    var barW = maxFreq ? (freq[w] / maxFreq * 100) : 0;
                    html += '<div class="tc-freq-bar-row">'
                        + '<span class="tc-freq-word" title="' + escapeHtml(w) + '">' + escapeHtml(w) + '</span>'
                        + '<div class="tc-freq-bar-track"><div class="tc-freq-bar-fill" style="width:' + barW + '%"></div></div>'
                        + '<span class="tc-freq-num">' + freq[w] + ' <small>(' + pct + '%)</small></span>'
                        + '</div>';
                });
                freqEl.innerHTML = html;
            }

            // Result panel stats
            TCTP.updateResultPanel(chars.toLocaleString() + ' chars', uniqueCount.toLocaleString() + ' unique', topWord, 'Done');
            TCTP.switchToResultTab();
            TCTP.toast('Analysis complete. ' + uniqueCount + ' unique words.');
        }

        // Initial render
        analyze();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    new MutationObserver(function () { init(); })
        .observe(document.documentElement, { childList: true, subtree: true });
})();
