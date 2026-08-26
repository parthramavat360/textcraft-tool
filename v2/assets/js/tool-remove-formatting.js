/**
 * Remove Text Formatting — Tool JS
 *
 * Premium design: mode cards, toggles, preview tabs, copy, live stats.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var PREFIX = 'tc-rf-';
    var cleanedText = '';

    var inp = document.getElementById(PREFIX + 'input');
    if (!inp) return;

    var origPreview = document.getElementById(PREFIX + 'preview-orig');
    var resultPreview = document.getElementById(PREFIX + 'preview-result');

    /* ── Mode cards ─────────────────────────────────────────── */
    document.querySelectorAll('.' + 'tc-rf-modes' + ' .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.' + 'tc-rf-modes' + ' .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
        });
    });

    function getMode() {
        var s = document.querySelector('.' + 'tc-rf-modes' + ' .tc-rsz-mode-card.sel');
        return s ? s.getAttribute('data-val') : 'all';
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

    function stripUnicodeStyling(text) {
        var map = {
            '\u200B': '', '\u200C': '', '\u200D': '', '\uFEFF': '',
            '\u00A0': ' '
        };

        var boldUpper = { '\u{1D400}': 'A', '\u{1D401}': 'B', '\u{1D402}': 'C', '\u{1D403}': 'D', '\u{1D404}': 'E', '\u{1D405}': 'F', '\u{1D406}': 'G', '\u{1D407}': 'H', '\u{1D408}': 'I', '\u{1D409}': 'J', '\u{1D40A}': 'K', '\u{1D40B}': 'L', '\u{1D40C}': 'M', '\u{1D40D}': 'N', '\u{1D40E}': 'O', '\u{1D40F}': 'P', '\u{1D410}': 'Q', '\u{1D411}': 'R', '\u{1D412}': 'S', '\u{1D413}': 'T', '\u{1D414}': 'U', '\u{1D415}': 'V', '\u{1D416}': 'W', '\u{1D417}': 'X', '\u{1D418}': 'Y', '\u{1D419}': 'Z' };
        var boldLower = { '\u{1D41A}': 'a', '\u{1D41B}': 'b', '\u{1D41C}': 'c', '\u{1D41D}': 'd', '\u{1D41E}': 'e', '\u{1D41F}': 'f', '\u{1D420}': 'g', '\u{1D421}': 'h', '\u{1D422}': 'i', '\u{1D423}': 'j', '\u{1D424}': 'k', '\u{1D425}': 'l', '\u{1D426}': 'm', '\u{1D427}': 'n', '\u{1D428}': 'o', '\u{1D429}': 'p', '\u{1D42A}': 'q', '\u{1D42B}': 'r', '\u{1D42C}': 's', '\u{1D42D}': 't', '\u{1D42E}': 'u', '\u{1D42F}': 'v', '\u{1D430}': 'w', '\u{1D431}': 'x', '\u{1D432}': 'y', '\u{1D433}': 'z' };
        var italicUpper = { '\u{1D434}': 'A', '\u{1D435}': 'B', '\u{1D436}': 'C', '\u{1D437}': 'D', '\u{1D438}': 'E', '\u{1D439}': 'F', '\u{1D43A}': 'G', '\u{1D43B}': 'H', '\u{1D43C}': 'I', '\u{1D43D}': 'J', '\u{1D43E}': 'K', '\u{1D43F}': 'L', '\u{1D440}': 'M', '\u{1D441}': 'N', '\u{1D442}': 'O', '\u{1D443}': 'P', '\u{1D444}': 'Q', '\u{1D445}': 'R', '\u{1D446}': 'S', '\u{1D447}': 'T', '\u{1D448}': 'U', '\u{1D449}': 'V', '\u{1D44A}': 'W', '\u{1D44B}': 'X', '\u{1D44C}': 'Y', '\u{1D44D}': 'Z' };
        var italicLower = { '\u{1D44E}': 'a', '\u{1D44F}': 'b', '\u{1D450}': 'c', '\u{1D451}': 'd', '\u{1D452}': 'e', '\u{1D453}': 'f', '\u{1D454}': 'g', '\u{1D455}': 'h', '\u{1D456}': 'i', '\u{1D457}': 'j', '\u{1D458}': 'k', '\u{1D459}': 'l', '\u{1D45A}': 'm', '\u{1D45B}': 'n', '\u{1D45C}': 'o', '\u{1D45D}': 'p', '\u{1D45E}': 'q', '\u{1D45F}': 'r' };

        Object.keys(map).forEach(function (k) { text = text.split(k).join(map[k]); });
        Object.keys(boldUpper).forEach(function (k) { text = text.split(k).join(boldUpper[k]); });
        Object.keys(boldLower).forEach(function (k) { text = text.split(k).join(boldLower[k]); });
        Object.keys(italicUpper).forEach(function (k) { text = text.split(k).join(italicUpper[k]); });
        Object.keys(italicLower).forEach(function (k) { text = text.split(k).join(italicLower[k]); });

        text = text.replace(/[\u2460-\u2473]/g, function (c) { return String.fromCharCode(c.charCodeAt(0) - 0x2460 + 0x0030); });

        return text;
    }

    function cleanText(text, opts) {
        var tagsRemoved = 0;

        if (opts.mode === 'all' || opts.mode === 'html' || opts.mode === 'smart') {
            tagsRemoved = countTags(text);
            text = text.replace(/<!--[\s\S]*?-->/g, '');
            text = text.replace(/<script[\s\S]*?<\/script>/gi, '');
            text = text.replace(/<style[\s\S]*?<\/style>/gi, '');
            text = text.replace(/<[^>]+>/g, ' ');
        }

        if (opts.mode === 'all' || opts.mode === 'unicode' || opts.mode === 'smart') {
            text = stripUnicodeStyling(text);
        }

        if (opts.decode) {
            var ta = document.createElement('textarea');
            ta.innerHTML = text;
            text = ta.value;
            text = text.replace(/&#(\d+);/g, function (m, code) { return String.fromCharCode(parseInt(code, 10)); });
            text = text.replace(/&#x([0-9a-f]+);/gi, function (m, code) { return String.fromCharCode(parseInt(code, 16)); });
        }

        if (opts.nbsp) {
            text = text.replace(/[\u00A0\u2007\u202F\u2060\uFEFF]/g, ' ');
        }

        if (opts.dedup) {
            text = text.replace(/ {2,}/g, ' ');
        }

        if (opts.trim) {
            text = text.trim();
        }

        return { text: text, tagsRemoved: tagsRemoved };
    }

    /* ── Perform clean ────────────────────────────────────── */
    function performClean() {
        var text = inp.value;

        if (!text.trim()) {
            TCTP.toast('Paste some text first.', '\u26A0\uFE0F');
            return;
        }

        var opts = {
            mode: getMode(),
            decode: document.getElementById(PREFIX + 'decode') ? document.getElementById(PREFIX + 'decode').checked : false,
            dedup: document.getElementById(PREFIX + 'dedup') ? document.getElementById(PREFIX + 'dedup').checked : false,
            trim: document.getElementById(PREFIX + 'trim') ? document.getElementById(PREFIX + 'trim').checked : false,
            nbsp: document.getElementById(PREFIX + 'nbsp') ? document.getElementById(PREFIX + 'nbsp').checked : false
        };

        var result = cleanText(text, opts);
        var cleaned = result.text;
        cleanedText = cleaned;

        setStat(PREFIX + 'tags', result.tagsRemoved.toLocaleString());

        var diff = text.length - cleaned.length;
        var pct = text.length > 0 ? ((diff / text.length) * 100).toFixed(1) : '0';
        setStat(PREFIX + 'saved', (diff > 0 ? '-' : '+') + Math.abs(pct) + '%');

        TCTP.updateResultPanel(
            text.length.toLocaleString() + ' chars',
            cleaned.length.toLocaleString() + ' chars',
            (text.length !== cleaned.length
                ? ((cleaned.length < text.length ? '+' : '-') +
                   Math.abs(((cleaned.length - text.length) / text.length) * 100).toFixed(1) + '%')
                : '0%'),
            'Done'
        );

        if (origPreview) origPreview.value = text;
        if (resultPreview) resultPreview.value = cleaned;

        TCTP.toast('Formatting cleaned!', '\u2705');
    }

    /* ── Live input stats + original preview ────────────────── */
    var debounceTimer = null;
    inp.addEventListener('input', function () {
        updateStats(inp.value);
        if (origPreview) origPreview.value = inp.value;
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () {
            performClean();
        }, 300);
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

    /* ── Init ───────────────────────────────────────────────── */
    updateStats('');

})();
