/**
 * Em Dash Remover — Tool JS
 *
 * Mode cards, replace modes, preview tabs, copy, result panel stats.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var PREFIX = 'tc-edr-';
    var cleanedText = '';

    var inp = document.getElementById(PREFIX + 'input');
    if (!inp) return;

    var origPreview = document.getElementById(PREFIX + 'preview-orig');
    var resultPreview = document.getElementById(PREFIX + 'preview-result');
    var customWrap = document.querySelector('.tc-edr-custom-wrap');
    var customInput = document.getElementById(PREFIX + 'custom');

    /* ── Dash type cards ────────────────────────────────────── */
    document.querySelectorAll('.tc-edr-dash-types .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.tc-edr-dash-types .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
        });
    });

    function getDashType() {
        var s = document.querySelector('.tc-edr-dash-types .tc-rsz-mode-card.sel');
        return s ? s.getAttribute('data-val') : 'both';
    }

    /* ── Replace type cards ─────────────────────────────────── */
    document.querySelectorAll('.tc-edr-replace-types .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.tc-edr-replace-types .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            if (card.getAttribute('data-val') === 'custom') {
                if (customWrap) customWrap.style.display = '';
            } else {
                if (customWrap) customWrap.style.display = 'none';
            }
        });
    });

    function getReplacement() {
        var s = document.querySelector('.tc-edr-replace-types .tc-rsz-mode-card.sel');
        var val = s ? s.getAttribute('data-val') : 'remove';
        if (val === 'custom' && customInput) return customInput.value;
        if (val === 'space') return ' ';
        if (val === 'hyphen') return '-';
        if (val === 'comma') return ',';
        return '';
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

    /* ── Perform remove ────────────────────────────────────── */
    function performRemove() {
        var text = inp.value;
        var dashType = getDashType();
        var replacement = getReplacement();

        if (!text.trim()) {
            TCTP.toast('Paste some text first.', '\u26A0\uFE0F');
            return;
        }

        var emDash = '\u2014';
        var enDash = '\u2013';

        var emCount = 0;
        var enCount = 0;

        if (dashType === 'both' || dashType === 'em') {
            emCount = (text.match(new RegExp(emDash.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'g')) || []).length;
        }
        if (dashType === 'both' || dashType === 'en') {
            enCount = (text.match(new RegExp(enDash.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'g')) || []).length;
        }

        var result = text;
        if (dashType === 'both' || dashType === 'em') {
            result = result.split(emDash).join(replacement);
        }
        if (dashType === 'both' || dashType === 'en') {
            result = result.split(enDash).join(replacement);
        }

        var totalReplaced = emCount + enCount;
        cleanedText = result;

        setStat(PREFIX + 'em', emCount.toLocaleString());
        setStat(PREFIX + 'en', enCount.toLocaleString());

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

        TCTP.toast(totalReplaced > 0
            ? totalReplaced + ' dash' + (totalReplaced === 1 ? '' : 'es') + ' removed!'
            : 'No dashes found.', totalReplaced > 0 ? '\u2705' : '\u26A0\uFE0F');
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
