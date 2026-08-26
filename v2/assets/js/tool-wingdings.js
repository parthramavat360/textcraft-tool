/**
 * Wingdings Converter — Tool JS
 *
 * Premium design: mode cards, toggles, preview tabs, copy, live stats.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var PREFIX = 'tc-wd-';
    var cleanedText = '';

    var inp = document.getElementById(PREFIX + 'input');
    if (!inp) return;

    var origPreview = document.getElementById(PREFIX + 'preview-orig');
    var resultPreview = document.getElementById(PREFIX + 'preview-result');

    /* ── Mode cards ─────────────────────────────────────────── */
    document.querySelectorAll('.tc-wd-modes .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.tc-wd-modes .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
        });
    });

    function getMode() {
        var s = document.querySelector('.tc-wd-modes .tc-rsz-mode-card.sel');
        return s ? s.getAttribute('data-val') : 'to_wingdings';
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

    /* ── Conversion maps ────────────────────────────────────── */
    var toWingdings = {
        'A': '\u2701', 'B': '\u2702', 'C': '\u2703', 'D': '\u2704', 'E': '\u2705',
        'F': '\u2706', 'G': '\u2707', 'H': '\u2708', 'I': '\u2709', 'J': '\u270A',
        'K': '\u270B', 'L': '\u270C', 'M': '\u270D', 'N': '\u270E', 'O': '\u270F',
        'P': '\u2710', 'Q': '\u2711', 'R': '\u2712', 'S': '\u2713', 'T': '\u2714',
        'U': '\u2715', 'V': '\u2716', 'W': '\u2717', 'X': '\u2718', 'Y': '\u2719',
        'Z': '\u271A', 'a': '\u271B', 'b': '\u271C', 'c': '\u271D', 'd': '\u271E',
        'e': '\u271F', 'f': '\u2720', 'g': '\u2721', 'h': '\u2722', 'i': '\u2723',
        'j': '\u2724', 'k': '\u2725', 'l': '\u2726', 'm': '\u2727', 'n': '\u2728',
        'o': '\u2729', 'p': '\u272A', 'q': '\u272B', 'r': '\u272C', 's': '\u272D',
        't': '\u272E', 'u': '\u272F', 'v': '\u2730', 'w': '\u2731', 'x': '\u2732',
        'y': '\u2733', 'z': '\u2734', '0': '\u2735', '1': '\u2736', '2': '\u2737',
        '3': '\u2738', '4': '\u2739', '5': '\u273A', '6': '\u273B', '7': '\u273C',
        '8': '\u273D', '9': '\u273E'
    };

    var fromWingdings = {};
    Object.keys(toWingdings).forEach(function (k) { fromWingdings[toWingdings[k]] = k; });

    /* ── Perform convert ────────────────────────────────────── */
    function performConvert() {
        var text = inp.value;

        if (!text.trim()) {
            TCTP.toast('Paste some text first.', '\u26A0\uFE0F');
            return;
        }

        var mode = getMode();
        var preserveSpaces = document.getElementById(PREFIX + 'preserve-spaces');
        var preserveNewlines = document.getElementById(PREFIX + 'preserve-newlines');

        var doPreserveSpaces = preserveSpaces ? preserveSpaces.checked : true;
        var doPreserveNewlines = preserveNewlines ? preserveNewlines.checked : true;

        var converted = 0;
        var skipped = 0;
        var result = '';

        if (mode === 'to_wingdings') {
            for (var i = 0; i < text.length; i++) {
                var ch = text[i];
                if (ch === ' ' && doPreserveSpaces) { result += ' '; continue; }
                if (ch === '\n' && doPreserveNewlines) { result += '\n'; continue; }
                if (ch === '\r' || ch === '\t') { result += ch; continue; }
                if (toWingdings[ch]) { result += toWingdings[ch]; converted++; }
                else { result += ch; skipped++; }
            }
        } else {
            for (var j = 0; j < text.length; j++) {
                var ch2 = text[j];
                if (ch2 === ' ' && doPreserveSpaces) { result += ' '; continue; }
                if (ch2 === '\n' && doPreserveNewlines) { result += '\n'; continue; }
                if (ch2 === '\r' || ch2 === '\t') { result += ch2; continue; }
                if (fromWingdings[ch2]) { result += fromWingdings[ch2]; converted++; }
                else { result += ch2; skipped++; }
            }
        }

        cleanedText = result;

        setStat(PREFIX + 'converted', converted.toLocaleString());
        setStat(PREFIX + 'skipped', skipped.toLocaleString());

        TCTP.updateResultPanel(
            text.length.toLocaleString() + ' chars',
            result.length.toLocaleString() + ' chars',
            '0%',
            'Done'
        );

        if (origPreview) origPreview.value = text;
        if (resultPreview) resultPreview.value = result;

        TCTP.toast(converted > 0
            ? converted + ' character' + (converted === 1 ? '' : 's') + ' converted!'
            : 'No convertible characters found.', converted > 0 ? '\u2705' : '\u26A0\uFE0F');
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

    /* ── Init ───────────────────────────────────────────────── */
    updateStats('');

})();
