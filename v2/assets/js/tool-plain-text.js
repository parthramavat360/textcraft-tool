/**
 * Plain Text Converter — Tool JS
 *
 * Premium design: mode cards, toggles, preview tabs, copy, live stats.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var PREFIX = 'tc-pt-';
    var cleanedText = '';

    var inp = document.getElementById(PREFIX + 'input');
    if (!inp) return;

    var origPreview = document.getElementById(PREFIX + 'preview-orig');
    var resultPreview = document.getElementById(PREFIX + 'preview-result');

    /* ── Mode cards ─────────────────────────────────────────── */
    document.querySelectorAll('.tc-pt-modes .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.tc-pt-modes .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
        });
    });

    function getMode() {
        var s = document.querySelector('.tc-pt-modes .tc-rsz-mode-card.sel');
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
    }

    /* ── Cleaning logic ────────────────────────────────────── */
    function countTags(text) {
        return (text.match(/<[^>]+>/g) || []).length;
    }

    function performConvert() {
        var text = inp.value;

        if (!text.trim()) {
            TCTP.toast('Paste some HTML or rich text first.', '\u26A0\uFE0F');
            return;
        }

        var mode = getMode();
        var decodeEl = document.getElementById(PREFIX + 'decode');
        var unicodeEl = document.getElementById(PREFIX + 'unicode');
        var blankEl = document.getElementById(PREFIX + 'blanklines');
        var dedupEl = document.getElementById(PREFIX + 'dedup');

        var doDecode = decodeEl ? decodeEl.checked : false;
        var doUnicode = unicodeEl ? unicodeEl.checked : false;
        var doBlanks = blankEl ? blankEl.checked : false;
        var doDedup = dedupEl ? dedupEl.checked : false;

        var tagsRemoved = 0;

        if (mode === 'smart' || mode === 'strip') {
            tagsRemoved = countTags(text);
            text = text.replace(/<!--[\s\S]*?-->/g, '');
            text = text.replace(/<script[\s\S]*?<\/script>/gi, '');
            text = text.replace(/<style[\s\S]*?<\/style>/gi, '');
            text = text.replace(/<[^>]+>/g, ' ');
        }

        if (mode === 'smart' || mode === 'decode' || doDecode) {
            var ta = document.createElement('textarea');
            ta.innerHTML = text;
            text = ta.value;
            text = text.replace(/&#(\d+);/g, function (m, code) { return String.fromCharCode(parseInt(code, 10)); });
            text = text.replace(/&#x([0-9a-f]+);/gi, function (m, code) { return String.fromCharCode(parseInt(code, 16)); });
        }

        if (doUnicode) {
            text = text.replace(/[\u200B-\u200D\uFEFF]/g, '');
            text = text.replace(/\u00A0/g, ' ');
            text = text.replace(/[\u2000-\u200A\u202F\u205F\u3000]/g, ' ');
        }

        if (mode === 'smart' || doDedup) {
            text = text.replace(/ {2,}/g, ' ');
            text = text.split('\n').map(function (l) { return l.trim(); }).join('\n');
        }

        if (doBlanks) {
            text = text.replace(/([ \t]*\n){2,}/g, '\n');
            text = text.replace(/\n{3,}/g, '\n\n');
        }

        text = text.trim();

        cleanedText = text;

        setStat(PREFIX + 'tags', tagsRemoved.toLocaleString());

        var diff = inp.value.length - text.length;
        var pct = inp.value.length > 0 ? ((diff / inp.value.length) * 100).toFixed(1) : '0';
        setStat(PREFIX + 'saved', (diff > 0 ? '-' : '+') + Math.abs(pct) + '%');

        TCTP.updateResultPanel(
            inp.value.length.toLocaleString() + ' chars',
            text.length.toLocaleString() + ' chars',
            (inp.value.length !== text.length
                ? ((text.length < inp.value.length ? '+' : '-') +
                   Math.abs(((text.length - inp.value.length) / inp.value.length) * 100).toFixed(1) + '%')
                : '0%'),
            'Done'
        );

        if (origPreview) origPreview.value = inp.value;
        if (resultPreview) resultPreview.value = text;

        TCTP.toast('Converted to plain text!', '\u2705');
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
            TCTP.setProgress(PREFIX + 'bar', 50, 'Converting...');

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

            var modeCards = document.querySelectorAll('.tc-pt-modes .tc-rsz-mode-card');
            modeCards.forEach(function (c) { c.classList.remove('sel'); });
            if (modeCards[0]) modeCards[0].classList.add('sel');

            var decodeCb = document.getElementById(PREFIX + 'decode');
            var unicodeCb = document.getElementById(PREFIX + 'unicode');
            var blankCb = document.getElementById(PREFIX + 'blanklines');
            var dedupCb = document.getElementById(PREFIX + 'dedup');
            if (decodeCb) decodeCb.checked = true;
            if (unicodeCb) unicodeCb.checked = false;
            if (blankCb) blankCb.checked = false;
            if (dedupCb) dedupCb.checked = false;

            if (origPreview) origPreview.value = '';
            if (resultPreview) resultPreview.value = '';

            ['chars', 'words', 'tags', 'saved'].forEach(function (k) {
                setStat(PREFIX + k, k === 'tags' ? '0' : (k === 'saved' ? '0%' : '0'));
            });
            TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Idle');
            TCTP.switchToOriginalTab();
            TCTP.toast('Cleared.', '\uD83E\uDDF9');
        });
    }

    /* ── Init ───────────────────────────────────────────────── */
    updateStats('');

})();
