/**
 * Duplicate Line Remover — Tool JS
 *
 * Mode cards, toggles, preview tabs, copy, result panel stats.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var PREFIX = 'tc-dl-';
    var cleanedText = '';

    var inp = document.getElementById(PREFIX + 'input');
    if (!inp) return;

    var origPreview = document.getElementById(PREFIX + 'preview-orig');
    var resultPreview = document.getElementById(PREFIX + 'preview-result');

    /* ── Mode cards ──────────────────────────────────────────── */
    document.querySelectorAll('.tc-dl-modes .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.tc-dl-modes .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
        });
    });

    function getSelectedMode() {
        var s = document.querySelector('.tc-dl-modes .tc-rsz-mode-card.sel');
        return s ? s.getAttribute('data-val') : 'keep-first';
    }

    /* ── Stats ─────────────────────────────────────────────── */
    function setStat(id, v) {
        var el = document.getElementById(id);
        if (el) el.textContent = v;
    }

    function updateStats(text) {
        var s = TCTP.getStats(text);
        setStat(PREFIX + 'chars', s.chars.toLocaleString());
    }

    /* ── Perform remove ────────────────────────────────────── */
    function performRemove() {
        var text = inp.value;
        var mode = getSelectedMode();
        var caseCb = document.getElementById(PREFIX + 'case');
        var trimCb = document.getElementById(PREFIX + 'trim');
        var blanksCb = document.getElementById(PREFIX + 'blanks');
        var sortCb = document.getElementById(PREFIX + 'sort');
        var cs = caseCb && caseCb.checked;

        if (!text.trim()) {
            TCTP.toast('Paste some text with duplicate lines first.', '\u26A0\uFE0F');
            return;
        }

        var lines = text.split(/\r?\n/);

        if (trimCb && trimCb.checked) lines = lines.map(function (l) { return l.trim(); });
        if (blanksCb && blanksCb.checked) lines = lines.filter(function (l) { return l !== ''; });

        var total = lines.length;

        if (mode === 'remove-all') {
            var seen = {};
            var unique = [];
            lines.forEach(function (line) {
                var key = cs ? line : line.toLowerCase();
                if (!seen[key]) {
                    seen[key] = 1;
                    unique.push(line);
                }
            });
            lines = unique;
        } else if (mode === 'keep-last') {
            var seenIdx = {};
            lines.forEach(function (line, i) {
                var key = cs ? line : line.toLowerCase();
                seenIdx[key] = i;
            });
            var filtered = [];
            lines.forEach(function (line, i) {
                if (seenIdx[(cs ? line : line.toLowerCase())] === i) filtered.push(line);
            });
            lines = filtered;
        } else {
            var seenFirst = {};
            var keepFirst = [];
            lines.forEach(function (line) {
                var key = cs ? line : line.toLowerCase();
                if (!seenFirst[key]) {
                    seenFirst[key] = 1;
                    keepFirst.push(line);
                }
            });
            lines = keepFirst;
        }

        if (sortCb && sortCb.checked) lines.sort(function (a, b) { return a.localeCompare(b); });

        var result = lines.join('\n');
        var removed = total - lines.length;
        cleanedText = result;

        setStat(PREFIX + 'total', total.toLocaleString());
        setStat(PREFIX + 'unique', lines.length.toLocaleString());
        setStat(PREFIX + 'removed', removed.toLocaleString());

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

        TCTP.toast(removed > 0
            ? removed + ' duplicate line' + (removed === 1 ? '' : 's') + ' removed!'
            : 'No duplicates found.', removed > 0 ? '\u2705' : '\u26A0\uFE0F');
    }

    /* ── Live input stats + original preview ────────────────── */
    inp.addEventListener('input', function () {
        updateStats(inp.value);
        if (origPreview) origPreview.value = inp.value;
    });

    /* ── Remove button ─────────────────────────────────────── */
    var removeBtn = document.getElementById(PREFIX + 'remove');
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            TCTP.showProgress(PREFIX + 'bar');
            TCTP.setProgress(PREFIX + 'bar', 50, 'Processing...');

            setTimeout(function () {
                performRemove();
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
