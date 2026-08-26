/**
 * Sort Words & Lines — Tool JS
 *
 * Premium design: mode cards, toggles, preview tabs, copy, live stats.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var PREFIX = 'tc-sw-';
    var cleanedText = '';

    var inp = document.getElementById(PREFIX + 'input');
    if (!inp) return;

    var origPreview = document.getElementById(PREFIX + 'preview-orig');
    var resultPreview = document.getElementById(PREFIX + 'preview-result');

    /* ── Mode cards ─────────────────────────────────────────── */
    document.querySelectorAll('.tc-sw-modes .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.tc-sw-modes .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
        });
    });

    function getMode() {
        var s = document.querySelector('.tc-sw-modes .tc-rsz-mode-card.sel');
        return s ? s.getAttribute('data-val') : 'alpha_asc';
    }

    /* ── Stats ─────────────────────────────────────────────── */
    function setStat(id, v) {
        var el = document.getElementById(id);
        if (el) el.textContent = v;
    }

    function updateStats(text) {
        var s = TCTP.getStats(text);
        setStat(PREFIX + 'chars', s.chars.toLocaleString());
        setStat(PREFIX + 'words', s.words.toLocaleString());
        setStat(PREFIX + 'lines-count', s.lines.toLocaleString());

        var words = text.trim() ? text.trim().split(/\s+/) : [];
        var unique = {};
        words.forEach(function (w) { unique[w.toLowerCase()] = true; });
        setStat(PREFIX + 'unique', Object.keys(unique).length.toLocaleString());
    }

    /* ── Comparison ────────────────────────────────────────── */
    function compareStr(a, b, mode, cs) {
        var aVal = a, bVal = b;
        if (!cs) { aVal = a.toLowerCase(); bVal = b.toLowerCase(); }
        switch (mode) {
            case 'alpha_desc': return bVal.localeCompare(aVal);
            case 'length_asc': return a.length - b.length || aVal.localeCompare(bVal);
            case 'length_desc': return b.length - a.length || aVal.localeCompare(bVal);
            case 'random': return Math.random() - 0.5;
            case 'alpha_asc':
            default: return aVal.localeCompare(bVal);
        }
    }

    /* ── Perform sort ──────────────────────────────────────── */
    function performSort() {
        var text = inp.value;

        if (!text.trim()) {
            TCTP.toast('Paste some text first.', '\u26A0\uFE0F');
            return;
        }

        var mode = getMode();
        var sortLines = document.getElementById(PREFIX + 'lines');
        var caseSensitive = document.getElementById(PREFIX + 'case');
        var removeBlanks = document.getElementById(PREFIX + 'remove-blanks');

        var doSortLines = sortLines ? sortLines.checked : true;
        var doCaseSensitive = caseSensitive ? caseSensitive.checked : false;
        var doRemoveBlanks = removeBlanks ? removeBlanks.checked : false;

        var lines = text.split('\n');

        if (doRemoveBlanks) {
            lines = lines.filter(function (l) { return l.trim().length > 0; });
        }

        if (doSortLines) {
            lines.sort(function (a, b) {
                return compareStr(a, b, mode, doCaseSensitive);
            });
        } else {
            var allWords = [];
            lines.forEach(function (line) {
                var words = line.split(/\s+/).filter(function (w) { return w.length > 0; });
                allWords = allWords.concat(words);
            });
            allWords.sort(function (a, b) {
                return compareStr(a, b, mode, doCaseSensitive);
            });
            lines = [allWords.join(' ')];
        }

        var result = lines.join('\n');
        cleanedText = result;

        TCTP.updateResultPanel(
            text.length.toLocaleString() + ' chars',
            result.length.toLocaleString() + ' chars',
            (text.length !== result.length
                ? ((result.length < text.length ? '+' : '-') +
                   Math.abs(((result.length - text.length) / text.length) * 100).toFixed(1) + '%')
                : '0%'),
            'Done'
        );

        if (origPreview) origPreview.value = text;
        if (resultPreview) resultPreview.value = result;

        TCTP.toast('Text sorted!', '\u2705');
    }

    /* ── Live input stats + original preview ────────────────── */
    inp.addEventListener('input', function () {
        updateStats(inp.value);
        if (origPreview) origPreview.value = inp.value;
    });

    /* ── Sort button ─────────────────────────────────────── */
    var sortBtn = document.getElementById(PREFIX + 'sort');
    if (sortBtn) {
        sortBtn.addEventListener('click', function () {
            TCTP.showProgress(PREFIX + 'bar');
            TCTP.setProgress(PREFIX + 'bar', 50, 'Sorting...');

            setTimeout(function () {
                performSort();
                TCTP.setProgress(PREFIX + 'bar', 100, 'Done!');
                TCTP.hideProgress(PREFIX + 'bar');
                TCTP.switchToResultTab();
            }, 80);
        });
    }

    /* ── Copy ───────────────────────────────────────────────── */
    var copyBtn = document.getElementById(PREFIX + 'copy');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            TCTP.copyText(cleanedText, 'Result');
        });
    }

    /* ── Init ───────────────────────────────────────────────── */
    updateStats('');

})();
