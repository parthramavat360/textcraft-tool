/**
 * Find and Replace — Tool JS
 *
 * Mode cards, live match counter, toggles, preview tabs, copy, result panel stats.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var PREFIX = 'tc-fr-';
    var replacedText = '';

    var inp = document.getElementById(PREFIX + 'input');
    if (!inp) return;

    var origPreview = document.getElementById(PREFIX + 'preview-orig');
    var resultPreview = document.getElementById(PREFIX + 'preview-result');
    var findInput = document.getElementById(PREFIX + 'find');
    var replaceInput = document.getElementById(PREFIX + 'replace');

    /* ── Mode cards ──────────────────────────────────────────── */
    document.querySelectorAll('.tc-fr-modes .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.tc-fr-modes .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
        });
    });

    function getSelectedMode() {
        var s = document.querySelector('.tc-fr-modes .tc-rsz-mode-card.sel');
        return s ? s.getAttribute('data-val') : 'normal';
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
    }

    /* ── Perform find & replace ─────────────────────────────── */
    function performReplace() {
        var findStr = findInput ? findInput.value : '';
        var repStr = replaceInput ? replaceInput.value : '';
        var sourceText = inp.value;
        var mode = getSelectedMode();
        var caseCb = document.getElementById(PREFIX + 'case');
        var allCb = document.getElementById(PREFIX + 'all');
        var trimCb = document.getElementById(PREFIX + 'trim');
        var dedupCb = document.getElementById(PREFIX + 'dedup');
        var cs = caseCb && caseCb.checked;
        var all = allCb ? allCb.checked : true;

        if (!sourceText.trim()) {
            TCTP.toast('Paste some text first!', '\u26A0\uFE0F');
            return;
        }
        if (!findStr) {
            TCTP.toast('Enter something to find.', '\u26A0\uFE0F');
            return;
        }

        var escaped, flags, re, count = 0, result;

        try {
            if (mode === 'regex') {
                flags = (cs ? '' : 'i') + (all ? 'g' : '');
                re = new RegExp(findStr, flags);
            } else if (mode === 'whole') {
                escaped = findStr.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                flags = (cs ? '' : 'i') + (all ? 'g' : '');
                re = new RegExp('\\b' + escaped + '\\b', flags);
            } else {
                escaped = findStr.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                flags = (cs ? '' : 'i') + (all ? 'g' : '');
                re = new RegExp(escaped, flags);
            }
        } catch (e) {
            TCTP.toast('Invalid regex: ' + e.message, '\u274C');
            return;
        }

        result = sourceText.replace(re, function () { count++; return repStr; });

        if (trimCb && trimCb.checked) {
            result = result.trim();
        }
        if (dedupCb && dedupCb.checked) {
            result = result.replace(/ {2,}/g, ' ');
        }

        replacedText = result;

        setStat(PREFIX + 'matches', count.toLocaleString());
        setStat(PREFIX + 'replaced', count.toLocaleString());

        TCTP.updateResultPanel(
            sourceText.length.toLocaleString() + ' chars',
            result.length.toLocaleString() + ' chars',
            (sourceText.length !== result.length
                ? ((result.length < sourceText.length ? '+' : '-') +
                   Math.abs(((result.length - sourceText.length) / sourceText.length) * 100).toFixed(1) + '%')
                : '0%'),
            'Done'
        );

        if (origPreview) origPreview.value = sourceText;
        if (resultPreview) resultPreview.value = result;

        TCTP.toast('Found ' + count + ' match(es) and replaced!');
    }

    /* ── Live input stats + original preview ────────────────── */
    inp.addEventListener('input', function () {
        updateStats(inp.value);
        if (origPreview) origPreview.value = inp.value;
    });

    /* ── Convert button ─────────────────────────────────────── */
    var convertBtn = document.getElementById(PREFIX + 'convert');
    if (convertBtn) {
        convertBtn.addEventListener('click', function () {
            TCTP.showProgress(PREFIX + 'progress');
            TCTP.setProgress(PREFIX + 'progress', 50, 'Replacing...');

            setTimeout(function () {
                performReplace();
                TCTP.setProgress(PREFIX + 'progress', 100, 'Done!');
                TCTP.hideProgress(PREFIX + 'progress');
                TCTP.switchToResultTab();
            }, 80);
        });
    }

    /* ── Copy ───────────────────────────────────────────────── */
    var copyBtn = document.getElementById(PREFIX + 'copy');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            TCTP.copyText(replacedText, 'Result');
        });
    }

    /* ── Clear all ──────────────────────────────────────────── */
    var clearBtn = document.getElementById(PREFIX + 'clear');
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            inp.value = '';
            replacedText = '';
            if (findInput) findInput.value = '';
            if (replaceInput) replaceInput.value = '';

            var modeCards = document.querySelectorAll('.tc-fr-modes .tc-rsz-mode-card');
            modeCards.forEach(function (c) { c.classList.remove('sel'); });
            if (modeCards[0]) modeCards[0].classList.add('sel');

            var caseCb = document.getElementById(PREFIX + 'case');
            var allCb = document.getElementById(PREFIX + 'all');
            var trimCb = document.getElementById(PREFIX + 'trim');
            var dedupCb = document.getElementById(PREFIX + 'dedup');
            if (caseCb) caseCb.checked = false;
            if (allCb) allCb.checked = true;
            if (trimCb) trimCb.checked = false;
            if (dedupCb) dedupCb.checked = false;

            if (origPreview) origPreview.value = '';
            if (resultPreview) resultPreview.value = '';

            updateStats('');
            setStat(PREFIX + 'matches', '0');
            setStat(PREFIX + 'replaced', '0');
            TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Idle');
            TCTP.switchToOriginalTab();
            TCTP.toast('Cleared.', '\uD83E\uDDF9');
        });
    }

    /* ── Init ───────────────────────────────────────────────── */
    updateStats('');

})();
