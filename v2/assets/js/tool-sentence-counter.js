/**
 * Sentence Counter (Word Counter) — Tool JS
 *
 * Premium: real-time stats, stat cards, word density, target progress,
 * counting toggles, preview tabs.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var PREFIX = 'tc-sc-';
    var inp = document.getElementById(PREFIX + 'input');
    if (!inp) return;

    var targetSlider = document.getElementById(PREFIX + 'target');
    var targetVal = document.getElementById(PREFIX + 'target-val');
    var includeNumbers = document.getElementById(PREFIX + 'include-numbers');
    var includePunct = document.getElementById(PREFIX + 'include-punct');
    var analyzed = false;

    /* ── Target slider ─────────────────────────────────────── */
    if (targetSlider && targetVal) {
        targetSlider.addEventListener('input', function () {
            targetVal.textContent = Number(targetSlider.value).toLocaleString();
            if (analyzed) runAnalysis();
        });
    }

    /* ── Toggles ───────────────────────────────────────────── */
    [includeNumbers, includePunct].forEach(function (el) {
        if (el) el.addEventListener('change', function () { if (analyzed) runAnalysis(); });
    });

    /* ── Formatting helpers ────────────────────────────────── */
    function fmtTime(min) {
        if (min < 1) return '< 1 min';
        var h = Math.floor(min / 60);
        var m = min % 60;
        if (h > 0) return h + 'h ' + m + 'm';
        return min + ' min';
    }

    function setEl(id, v) {
        var el = document.getElementById(id);
        if (el) el.textContent = v;
    }

    /* ── Core analysis ─────────────────────────────────────── */
    function runAnalysis() {
        var text = inp.value;
        if (!text.trim()) {
            resetAll();
            return;
        }

        /* Words */
        var wordPattern = includeNumbers && includeNumbers.checked
            ? /[a-zA-Z0-9']+/g
            : /[a-zA-Z']+/g;
        var words = (text.match(wordPattern) || []);
        var wordCount = words.length;

        /* Sentences */
        var sentences = (text.match(/[.!?]+(\s|$)/g) || []).length;
        if (sentences === 0 && wordCount > 0) sentences = 1;

        /* Paragraphs */
        var paragraphs = text.split(/\n\s*\n/).filter(function (p) { return p.trim().length > 0; }).length;
        if (paragraphs === 0 && wordCount > 0) paragraphs = 1;

        /* Characters */
        var charsWithSpace = text.length;
        var charsNoSpace = text.replace(/\s/g, '').length;
        if (includePunct && includePunct.checked) {
            charsNoSpace = text.replace(/[\s]/g, '').length;
        } else {
            charsNoSpace = text.replace(/[\s\p{P}]/gu, '').length || text.replace(/\s/g, '').length;
        }

        /* Times */
        var readingMin = Math.ceil(wordCount / 200);
        var speakingMin = Math.ceil(wordCount / 130);

        /* Target */
        var target = targetSlider ? parseInt(targetSlider.value, 10) : 500;
        var targetPct = Math.min(Math.round((wordCount / target) * 100), 100);

        /* ── Update stats row ── */
        setEl(PREFIX + 'words', wordCount.toLocaleString());
        setEl(PREFIX + 'chars', charsWithSpace.toLocaleString());
        setEl(PREFIX + 'readtime', fmtTime(readingMin));
        setEl(PREFIX + 'speaktime', fmtTime(speakingMin));

        /* ── Update result panel top stats ── */
        setEl('tc-stat-orig', wordCount.toLocaleString() + ' words');
        setEl('tc-stat-comp', sentences.toLocaleString() + ' sentences');
        setEl('tc-stat-saved', targetPct + '% of ' + target.toLocaleString());

        /* ── Update stat cards ── */
        setEl(PREFIX + 's-words', wordCount.toLocaleString());
        setEl(PREFIX + 's-sentences', sentences.toLocaleString());
        setEl(PREFIX + 's-paragraphs', paragraphs.toLocaleString());
        setEl(PREFIX + 's-chars', charsWithSpace.toLocaleString());
        setEl(PREFIX + 's-chars-nosp', charsNoSpace.toLocaleString());
        setEl(PREFIX + 's-readtime', fmtTime(readingMin));
        setEl(PREFIX + 's-speaktime', fmtTime(speakingMin));
        setEl(PREFIX + 's-target', targetPct + '%');

        /* ── Status chip ── */
        var chip = document.getElementById('tc-status-chip');
        if (chip) chip.textContent = 'Done';

        /* ── Word density ── */
        buildDensity(words);
    }

    /* ── Word density ──────────────────────────────────────── */
    function buildDensity(words) {
        var freq = {};
        words.forEach(function (w) {
            var low = w.toLowerCase();
            if (low.length < 2) return;
            freq[low] = (freq[low] || 0) + 1;
        });

        var sorted = Object.keys(freq).map(function (k) { return { word: k, count: freq[k] }; })
            .sort(function (a, b) { return b.count - a.count; })
            .slice(0, 20);

        var container = document.getElementById(PREFIX + 'density');
        if (!container) return;

        if (sorted.length === 0) {
            container.innerHTML = '<p style="color:var(--muted);font-size:14px;">No significant words found.</p>';
            return;
        }

        var maxCount = sorted[0].count;
        var totalWords = words.length;
        var html = '<div class="tc-sc-density-head"><b>Top ' + sorted.length + ' Words</b><span>' + totalWords.toLocaleString() + ' total words</span></div>';

        sorted.forEach(function (item, i) {
            var pct = maxCount > 0 ? Math.round((item.count / maxCount) * 100) : 0;
            var wordPct = totalWords > 0 ? ((item.count / totalWords) * 100).toFixed(1) : '0';
            html += '<div class="tc-sc-density-row">' +
                '<span class="tc-sc-density-rank">' + (i + 1) + '</span>' +
                '<span class="tc-sc-density-word">' + escapeHtml(item.word) + '</span>' +
                '<div class="tc-sc-density-bar-wrap"><div class="tc-sc-density-bar" style="width:' + pct + '%"></div></div>' +
                '<span class="tc-sc-density-count">' + item.count + ' <small>(' + wordPct + '%)</small></span>' +
                '</div>';
        });

        container.innerHTML = html;
    }

    function escapeHtml(s) {
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    /* ── Reset ─────────────────────────────────────────────── */
    function resetAll() {
        var ids = ['s-words', 's-sentences', 's-paragraphs', 's-chars', 's-chars-nosp', 's-readtime', 's-speaktime', 's-target'];
        ids.forEach(function (id) {
            setEl(PREFIX + id, id === 's-target' ? '0%' : '0');
        });
        setEl(PREFIX + 'words', '0');
        setEl(PREFIX + 'chars', '0');
        setEl(PREFIX + 'readtime', '0 min');
        setEl(PREFIX + 'speaktime', '0 min');
        setEl('tc-stat-orig', '\u2014');
        setEl('tc-stat-comp', '\u2014');
        setEl('tc-stat-saved', '\u2014');
        var chip = document.getElementById('tc-status-chip');
        if (chip) chip.textContent = 'Idle';
        var density = document.getElementById(PREFIX + 'density');
        if (density) density.innerHTML = '<p style="color:var(--muted);font-size:14px;">Enter text and analyze to see word frequency distribution.</p>';
        analyzed = false;
    }

    /* ── Real-time update on input ─────────────────────────── */
    var debounceTimer = null;
    inp.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () {
            runAnalysis();
            analyzed = true;
        }, 200);
    });

    /* ── Analyze button ────────────────────────────────────── */
    var analyzeBtn = document.getElementById(PREFIX + 'analyze');
    if (analyzeBtn) {
        analyzeBtn.addEventListener('click', function () {
            if (!inp.value.trim()) {
                TCTP.toast('Please enter some text.', 'Warning');
                return;
            }
            TCTP.showProgress(PREFIX + 'progress');
            TCTP.setProgress(PREFIX + 'progress', 50, 'Analyzing...');
            setTimeout(function () {
                runAnalysis();
                analyzed = true;
                TCTP.setProgress(PREFIX + 'progress', 100, 'Done!');
                TCTP.hideProgress(PREFIX + 'progress');
                TCTP.switchToResultTab();
                TCTP.toast('Text analyzed successfully!');
            }, 300);
        });
    }

    /* ── Clear button ──────────────────────────────────────── */
    var clearBtn = document.getElementById(PREFIX + 'clear');
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            inp.value = '';
            resetAll();
            TCTP.toast('Cleared.');
        });
    }

    /* ── Init empty ────────────────────────────────────────── */
    resetAll();

})();
