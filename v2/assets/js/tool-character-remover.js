/**
 * Character Remover — Tool JS
 *
 * Preset mode cards, custom input, toggles, preview tabs, copy.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var PREFIX = 'tc-cr-';
    var cleanedText = '';

    var inp = document.getElementById(PREFIX + 'input');
    if (!inp) return;

    var origPreview = document.getElementById(PREFIX + 'preview-orig');
    var resultPreview = document.getElementById(PREFIX + 'preview-result');
    var customWrap = document.querySelector('.tc-cr-custom-wrap');
    var customInput = document.getElementById(PREFIX + 'custom');
    var selectedChars = ' ';

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

    /* ── Preset mode cards ─────────────────────────────────── */
    document.querySelectorAll('.tc-cr-presets .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.tc-cr-presets .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            var chars = card.getAttribute('data-chars');
            if (chars === 'custom') {
                if (customWrap) customWrap.style.display = '';
                if (customInput) selectedChars = customInput.value;
                else selectedChars = '';
            } else {
                if (customWrap) customWrap.style.display = 'none';
                selectedChars = chars;
            }
        });
    });

    if (customInput) {
        customInput.addEventListener('input', function () {
            selectedChars = customInput.value;
        });
    }

    /* ── Perform remove ────────────────────────────────────── */
    function performRemove() {
        var text = inp.value;
        var caseCb = document.getElementById(PREFIX + 'case');
        var trimCb = document.getElementById(PREFIX + 'trim');
        var dedupCb = document.getElementById(PREFIX + 'dedup');
        var cs = caseCb && caseCb.checked;

        if (!text.trim()) {
            TCTP.toast('Please enter some text first.', '\u26A0\uFE0F');
            return;
        }

        var chars = selectedChars;
        if (document.querySelector('.tc-cr-presets .tc-rsz-mode-card.sel')?.getAttribute('data-chars') === 'custom') {
            chars = customInput ? customInput.value : '';
        }

        if (!chars) {
            TCTP.toast('Pick a preset or enter custom characters.', '\u26A0\uFE0F');
            return;
        }

        var flags = cs ? 'g' : 'gi';
        var escaped = chars.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&');
        var result = text.replace(new RegExp('[' + escaped + ']', flags), '');

        if (trimCb && trimCb.checked) result = result.trim();
        if (dedupCb && dedupCb.checked) result = result.replace(/ {2,}/g, ' ');

        var removed = text.length - result.length;
        cleanedText = result;

        setStat(PREFIX + 'removed', removed.toLocaleString());
        setStat(PREFIX + 'result-len', result.length.toLocaleString());

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
            ? removed + ' character' + (removed === 1 ? '' : 's') + ' removed!'
            : 'No matching characters found.', removed > 0 ? '\u2705' : '\u26A0\uFE0F');
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
            TCTP.setProgress(PREFIX + 'bar', 50, 'Removing...');

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

    /* ── Clear all ──────────────────────────────────────────── */
    var clearBtn = document.getElementById(PREFIX + 'clear');
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            inp.value = '';
            cleanedText = '';

            var presetCards = document.querySelectorAll('.tc-cr-presets .tc-rsz-mode-card');
            presetCards.forEach(function (c) { c.classList.remove('sel'); });
            if (presetCards[0]) presetCards[0].classList.add('sel');
            selectedChars = ' ';
            if (customWrap) customWrap.style.display = 'none';
            if (customInput) customInput.value = '';

            var caseCb = document.getElementById(PREFIX + 'case');
            var trimCb = document.getElementById(PREFIX + 'trim');
            var dedupCb = document.getElementById(PREFIX + 'dedup');
            if (caseCb) caseCb.checked = false;
            if (trimCb) trimCb.checked = false;
            if (dedupCb) dedupCb.checked = false;

            if (origPreview) origPreview.value = '';
            if (resultPreview) resultPreview.value = '';

            updateStats('');
            setStat(PREFIX + 'removed', '0');
            setStat(PREFIX + 'result-len', '0');
            TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Idle');
            TCTP.switchToOriginalTab();
            TCTP.toast('Cleared.', '\uD83E\uDDF9');
        });
    }

    /* ── Init ───────────────────────────────────────────────── */
    updateStats('');

})();
