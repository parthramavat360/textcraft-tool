/**
 * Sentence Counter — Tool JS
 *
 * Counts words, sentences, paragraphs, characters and reading/speaking times.
 *
 * Widget IDs (widget-sentence-counter.php):
 *  - tc-sc-input, tc-sc-analyze, tc-sc-clear
 *  - stat values: tc-sc-words, tc-sc-sentences, tc-sc-paragraphs,
 *    tc-sc-chars, tc-sc-chars-nosp, tc-sc-readtime, tc-sc-speaktime
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var inp = document.getElementById('tc-sc-input');
        var analyzeBtn = document.getElementById('tc-sc-analyze');
        if (!inp || !analyzeBtn || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var clearBtn = document.getElementById('tc-sc-clear');

        var statEls = {
            words: document.getElementById('tc-sc-words'),
            sentences: document.getElementById('tc-sc-sentences'),
            paragraphs: document.getElementById('tc-sc-paragraphs'),
            charsWithSpace: document.getElementById('tc-sc-chars'),
            charsNoSpace: document.getElementById('tc-sc-chars-nosp'),
            readingTime: document.getElementById('tc-sc-readtime'),
            speakingTime: document.getElementById('tc-sc-speaktime')
        };

        function fmtTime(min) {
            if (min < 1) return '< 1 min';
            var h = Math.floor(min / 60);
            var m = min % 60;
            if (h > 0) return h + 'h ' + m + 'm';
            return min + ' min';
        }

        function resetStats() {
            if (statEls.words) statEls.words.textContent = '0';
            if (statEls.sentences) statEls.sentences.textContent = '0';
            if (statEls.paragraphs) statEls.paragraphs.textContent = '0';
            if (statEls.charsWithSpace) statEls.charsWithSpace.textContent = '0';
            if (statEls.charsNoSpace) statEls.charsNoSpace.textContent = '0';
            if (statEls.readingTime) statEls.readingTime.textContent = '0 min';
            if (statEls.speakingTime) statEls.speakingTime.textContent = '0 min';
        }

        // ── Analyze button ───────────────────────────────────────

        analyzeBtn.addEventListener('click', function () {
            var text = inp.value;
            if (!text.trim()) {
                TCTP.toast('Please enter some text.', '\u26A0\uFE0F');
                return;
            }

            var words = (text.match(/[a-zA-Z0-9']+/g) || []);
            var wordCount = words.length;

            var sentences = (text.match(/[.!?]+(\s|$)/g) || []).length;
            if (sentences === 0 && wordCount > 0) sentences = 1;

            var paragraphs = text.split(/\n\s*\n/).filter(function (p) { return p.trim().length > 0; }).length;
            if (paragraphs === 0 && wordCount > 0) paragraphs = 1;

            var charsNoSpace = text.replace(/\s/g, '').length;
            var charsWithSpace = text.length;

            var readingMin = Math.ceil(wordCount / 200);
            var speakingMin = Math.ceil(wordCount / 130);

            if (statEls.words) statEls.words.textContent = wordCount.toLocaleString();
            if (statEls.sentences) statEls.sentences.textContent = sentences.toLocaleString();
            if (statEls.paragraphs) statEls.paragraphs.textContent = paragraphs.toLocaleString();
            if (statEls.charsWithSpace) statEls.charsWithSpace.textContent = charsWithSpace.toLocaleString();
            if (statEls.charsNoSpace) statEls.charsNoSpace.textContent = charsNoSpace.toLocaleString();
            if (statEls.readingTime) statEls.readingTime.textContent = fmtTime(readingMin);
            if (statEls.speakingTime) statEls.speakingTime.textContent = fmtTime(speakingMin);

            TCTP.updateResultPanel(charsWithSpace.toLocaleString() + ' chars', sentences.toLocaleString() + ' sentences', '\u2014', 'Done');

            TCTP.toast('Text analyzed.');
        });

        // ── Clear button ─────────────────────────────────────────

        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                inp.value = '';
                resetStats();
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
