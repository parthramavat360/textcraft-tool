/**
 * Pig Latin Translator — Tool JS
 *
 * Premium design: mode cards, toggles, preview tabs, copy, live stats.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var PREFIX = 'tc-pl-';
    var cleanedText = '';

    var inp = document.getElementById(PREFIX + 'input');
    if (!inp) return;

    var origPreview = document.getElementById(PREFIX + 'preview-orig');
    var resultPreview = document.getElementById(PREFIX + 'preview-result');

    /* ── Mode cards ─────────────────────────────────────────── */
    document.querySelectorAll('.tc-pl-modes .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.tc-pl-modes .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
        });
    });

    function getMode() {
        var s = document.querySelector('.tc-pl-modes .tc-rsz-mode-card.sel');
        return s ? s.getAttribute('data-val') : 'to_pig';
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

    /* ── Pig Latin logic ────────────────────────────────────── */
    function toPigLatin(word, useYay) {
        if (!word) return word;
        var isUpper = word[0] === word[0].toUpperCase();
        var lower = word.toLowerCase();
        var vowels = 'aeiou';
        var suffix = useYay ? 'yay' : 'way';

        if (vowels.indexOf(lower[0]) !== -1) {
            var result = lower + suffix;
            return isUpper ? result.charAt(0).toUpperCase() + result.slice(1) : result;
        }

        var cluster = '';
        for (var i = 0; i < lower.length; i++) {
            if (vowels.indexOf(lower[i]) !== -1) break;
            cluster += lower[i];
        }

        var result2 = lower.slice(cluster.length) + cluster + 'ay';
        if (isUpper) result2 = result2.charAt(0).toUpperCase() + result2.slice(1);
        return result2;
    }

    function fromPigLatin(word) {
        if (!word) return word;
        var isUpper = word[0] === word[0].toUpperCase();
        var lower = word.toLowerCase();

        if (lower.endsWith('way') || lower.endsWith('yay')) {
            var suffix = lower.endsWith('yay') ? 'yay' : 'way';
            var base = lower.slice(0, -suffix.length);
            return isUpper ? base.charAt(0).toUpperCase() + base.slice(1) : base;
        }

        if (lower.endsWith('ay')) {
            var withoutAy = lower.slice(0, -2);
            for (var i = 0; i < withoutAy.length; i++) {
                var moved = withoutAy.slice(i) + withoutAy.slice(0, i + 1);
                var vowels = 'aeiou';
                if (vowels.indexOf(moved[0]) !== -1) {
                    return isUpper ? moved.charAt(0).toUpperCase() + moved.slice(1) : moved;
                }
            }
        }

        return word;
    }

    /* ── Perform translate ──────────────────────────────────── */
    function performTranslate() {
        var text = inp.value;

        if (!text.trim()) {
            TCTP.toast('Paste some text first.', '\u26A0\uFE0F');
            return;
        }

        var mode = getMode();
        var keepCase = document.getElementById(PREFIX + 'keep-case');
        var useYay = document.getElementById(PREFIX + 'yay');

        var doKeepCase = keepCase ? keepCase.checked : true;
        var doUseYay = useYay ? useYay.checked : false;

        var translated = 0;
        var result = text.replace(/\b[a-zA-Z']+\b/g, function (word) {
            translated++;
            if (mode === 'to_pig') {
                return toPigLatin(word, doUseYay);
            } else {
                return fromPigLatin(word);
            }
        });

        if (!doKeepCase) {
            result = result.toLowerCase();
        }

        cleanedText = result;

        setStat(PREFIX + 'translated', translated.toLocaleString());
        setStat(PREFIX + 'output-len', result.length.toLocaleString());

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

        TCTP.toast(translated + ' word' + (translated === 1 ? '' : 's') + ' translated!', '\u2705');
    }

    /* ── Live input stats + original preview ────────────────── */
    inp.addEventListener('input', function () {
        updateStats(inp.value);
        if (origPreview) origPreview.value = inp.value;
    });

    /* ── Translate button ──────────────────────────────────── */
    var translateBtn = document.getElementById(PREFIX + 'translate');
    if (translateBtn) {
        translateBtn.addEventListener('click', function () {
            TCTP.showProgress(PREFIX + 'bar');
            TCTP.setProgress(PREFIX + 'bar', 50, 'Translating...');

            setTimeout(function () {
                performTranslate();
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
