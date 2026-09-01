/**
 * Whitespace Remover — Tool JS
 *
 * Premium design: mode cards, toggles, preview tabs, copy, live stats.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var PREFIX = 'tc-ws-';
    var cleanedText = '';

    var inp = document.getElementById(PREFIX + 'input');
    if (!inp) return;

    var origPreview = document.getElementById(PREFIX + 'preview-orig');
    var resultPreview = document.getElementById(PREFIX + 'preview-result');

    /* ── Mode cards ─────────────────────────────────────────── */
    document.querySelectorAll('.tc-ws-modes .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.tc-ws-modes .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
        });
    });

    function getMode() {
        var s = document.querySelector('.tc-ws-modes .tc-rsz-mode-card.sel');
        return s ? s.getAttribute('data-val') : 'smart';
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
        setStat(PREFIX + 'lines', s.lines.toLocaleString());
    }

    /* ── Cleaning logic ────────────────────────────────────── */
    function performClean() {
        var text = inp.value;

        if (!text.trim()) {
            TCTP.toast('Paste some text first.', '\u26A0\uFE0F');
            return;
        }

        var mode = getMode();
        var removeTabs = document.getElementById(PREFIX + 'tabs');
        var removeBlank = document.getElementById(PREFIX + 'blanklines');
        var globalTrim = document.getElementById(PREFIX + 'globaltrim');

        var lines = text.split('\n');

        if (removeTabs && removeTabs.checked) {
            lines = lines.map(function (l) { return l.replace(/\t/g, ' '); });
        }

        if (mode === 'smart' || mode === 'trim' || mode === 'aggressive') {
            lines = lines.map(function (l) { return l.trim(); });
        }

        if (mode === 'smart' || mode === 'collapse') {
            lines = lines.map(function (l) { return l.replace(/ {2,}/g, ' '); });
        }

        if (mode === 'aggressive') {
            lines = lines.map(function (l) { return l.replace(/\s+/g, ''); });
        }

        if (removeBlank && removeBlank.checked) {
            lines = lines.filter(function (l) { return l.trim().length > 0; });
        }

        var result = lines.join('\n');

        if (globalTrim && globalTrim.checked) {
            result = result.trim();
        }

        cleanedText = result;

        var diff = text.length - result.length;
        var pct = text.length > 0 ? ((diff / text.length) * 100).toFixed(1) : '0';
        setStat(PREFIX + 'saved', (diff > 0 ? '-' : '+') + Math.abs(pct) + '%');

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

        TCTP.toast(diff > 0
            ? diff.toLocaleString() + ' characters removed!'
            : 'No extra whitespace found.', diff > 0 ? '\u2705' : '\u26A0\uFE0F');
    }

    /* ── Live input stats + original preview ────────────────── */
    inp.addEventListener('input', function () {
        updateStats(inp.value);
        if (origPreview) origPreview.value = inp.value;
    });

    /* ── Clean button ─────────────────────────────────────── */
    var cleanBtn = document.getElementById(PREFIX + 'clean');
    if (cleanBtn) {
        cleanBtn.addEventListener('click', function () {
            TCTP.showProgress(PREFIX + 'bar');
            TCTP.setProgress(PREFIX + 'bar', 50, 'Cleaning...');

            setTimeout(function () {
                performClean();
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

    /* ── Clear all ──────────────────────────────────────────── */
    var clearBtn = document.getElementById(PREFIX + 'clear');
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            inp.value = '';
            cleanedText = '';

            var modeCards = document.querySelectorAll('.tc-ws-modes .tc-rsz-mode-card');
            modeCards.forEach(function (c) { c.classList.remove('sel'); });
            if (modeCards[0]) modeCards[0].classList.add('sel');

            var tabsCb = document.getElementById(PREFIX + 'tabs');
            var blankCb = document.getElementById(PREFIX + 'blanklines');
            var globalCb = document.getElementById(PREFIX + 'globaltrim');
            if (tabsCb) tabsCb.checked = true;
            if (blankCb) blankCb.checked = false;
            if (globalCb) globalCb.checked = false;

            if (origPreview) origPreview.value = '';
            if (resultPreview) resultPreview.value = '';

            ['chars', 'words', 'lines', 'saved'].forEach(function (k) {
                setStat(PREFIX + k, k === 'saved' ? '0%' : '0');
            });
            TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Idle');
            TCTP.switchToOriginalTab();
            TCTP.toast('Cleared.', '\uD83E\uDDF9');
        });
    }

    /* ── Init ───────────────────────────────────────────────── */
    updateStats('');

})();
