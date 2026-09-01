/**
 * Remove Underscores — Tool JS
 *
 * Premium design: mode cards, toggles, preview tabs, copy, live stats.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var PREFIX = 'tc-ru-';
    var cleanedText = '';

    var inp = document.getElementById(PREFIX + 'input');
    if (!inp) return;

    var origPreview = document.getElementById(PREFIX + 'preview-orig');
    var resultPreview = document.getElementById(PREFIX + 'preview-result');
    var customWrap = document.querySelector('.tc-ru-custom-wrap');
    var customInput = document.getElementById(PREFIX + 'custom');

    /* ── Mode cards ─────────────────────────────────────────── */
    document.querySelectorAll('.tc-ru-modes .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.tc-ru-modes .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            if (card.getAttribute('data-val') === 'custom') {
                if (customWrap) customWrap.style.display = '';
            } else {
                if (customWrap) customWrap.style.display = 'none';
            }
        });
    });

    function getMode() {
        var s = document.querySelector('.tc-ru-modes .tc-rsz-mode-card.sel');
        return s ? s.getAttribute('data-val') : 'space';
    }

    function getReplacement() {
        var mode = getMode();
        if (mode === 'custom' && customInput) return customInput.value;
        if (mode === 'hyphen') return '-';
        if (mode === 'remove') return '';
        return ' ';
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

    /* ── Perform replace ────────────────────────────────────── */
    function performConvert() {
        var text = inp.value;

        if (!text.trim()) {
            TCTP.toast('Paste some text first.', '\u26A0\uFE0F');
            return;
        }

        var replacement = getReplacement();
        var underlineCount = (text.match(/_/g) || []).length;
        var result = text.replace(/_+/g, replacement);

        var collapseSpaces = document.getElementById(PREFIX + 'collapsespaces');
        var trimWs = document.getElementById(PREFIX + 'trim');
        if (collapseSpaces && collapseSpaces.checked) {
            result = result.replace(/ {2,}/g, ' ');
        }
        if (trimWs && trimWs.checked) {
            result = result.trim();
        }

        cleanedText = result;

        setStat(PREFIX + 'count', underlineCount.toLocaleString());
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

        TCTP.toast(underlineCount > 0
            ? underlineCount + ' underscore' + (underlineCount === 1 ? '' : 's') + ' processed!'
            : 'No underscores found.', underlineCount > 0 ? '\u2705' : '\u26A0\uFE0F');
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
            TCTP.showProgress(PREFIX + 'bar');
            TCTP.setProgress(PREFIX + 'bar', 50, 'Processing...');

            setTimeout(function () {
                performConvert();
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

            var modeCards = document.querySelectorAll('.tc-ru-modes .tc-rsz-mode-card');
            modeCards.forEach(function (c) { c.classList.remove('sel'); });
            if (modeCards[0]) modeCards[0].classList.add('sel');
            if (customWrap) customWrap.style.display = 'none';
            if (customInput) customInput.value = '';

            var collapseCb = document.getElementById(PREFIX + 'collapsespaces');
            var trimCb = document.getElementById(PREFIX + 'trim');
            if (collapseCb) collapseCb.checked = false;
            if (trimCb) trimCb.checked = false;

            if (origPreview) origPreview.value = '';
            if (resultPreview) resultPreview.value = '';

            ['chars', 'words', 'count', 'result-len'].forEach(function (k) {
                setStat(PREFIX + k, '0');
            });
            TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Idle');
            TCTP.switchToOriginalTab();
            TCTP.toast('Cleared.', '\uD83E\uDDF9');
        });
    }

    /* ── Init ───────────────────────────────────────────────── */
    updateStats('');

})();
