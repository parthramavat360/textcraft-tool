/**
 * Case Converter — Tool JS
 *
 * Premium: mode cards, live stats, preview tabs, trim/dedup toggles,
 * copy, download .txt, result panel stats.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var PREFIX = 'tc-cc-';
    var convertedText = '';

    var inp = document.getElementById(PREFIX + 'input');
    if (!inp) return;

    var origPreview = document.getElementById(PREFIX + 'preview-orig');
    var resultPreview = document.getElementById(PREFIX + 'preview-result');

    /* ── Mode cards ────────────────────────────────────────── */
    document.querySelectorAll('.tc-cc-types .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.tc-cc-types .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
        });
    });

    function getSelectedCase() {
        var s = document.querySelector('.tc-cc-types .tc-rsz-mode-card.sel');
        return s ? s.getAttribute('data-val') : 'uppercase';
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
        setStat(PREFIX + 'sentences', s.sentences.toLocaleString());
        setStat(PREFIX + 'lines', s.lines.toLocaleString());
    }

    /* ── Conversion functions ──────────────────────────────── */
    function toSentenceCase(s) {
        return s.toLowerCase().replace(/(^|[.!?]\s+)([a-z])/g, function (m, sep, ch) {
            return sep + ch.toUpperCase();
        });
    }

    function toTitleCase(s) {
        var small = ['a', 'an', 'the', 'and', 'but', 'or', 'nor', 'for', 'yet', 'so', 'at', 'by', 'in', 'of', 'on', 'to', 'up', 'as', 'is'];
        return s.toLowerCase().replace(/\b\w+/g, function (word, offset) {
            if (offset > 0 && small.indexOf(word) !== -1) return word;
            return word.charAt(0).toUpperCase() + word.slice(1);
        });
    }

    function toCapitalizedCase(s) {
        return s.toLowerCase().replace(/\b\w+/g, function (w) {
            return w.charAt(0).toUpperCase() + w.slice(1);
        });
    }

    function toAlternatingCase(s) {
        return s.split('').map(function (ch, i) {
            return i % 2 === 0 ? ch.toLowerCase() : ch.toUpperCase();
        }).join('');
    }

    function toInverseCase(s) {
        return s.split('').map(function (ch) {
            if (ch === ch.toUpperCase()) return ch.toLowerCase();
            return ch.toUpperCase();
        }).join('');
    }

    function toWords(s) {
        return s.replace(/[^a-zA-Z0-9]+/g, ' ').trim().split(/\s+/).filter(Boolean);
    }

    function toCamelCase(s) {
        var words = toWords(s);
        return words.map(function (w, i) {
            var lower = w.toLowerCase();
            return i === 0 ? lower : lower.charAt(0).toUpperCase() + lower.slice(1);
        }).join('');
    }

    function toPascalCase(s) {
        return toWords(s).map(function (w) {
            var lower = w.toLowerCase();
            return lower.charAt(0).toUpperCase() + lower.slice(1);
        }).join('');
    }

    function toSnakeCase(s) {
        return toWords(s).map(function (w) { return w.toLowerCase(); }).join('_');
    }

    function toKebabCase(s) {
        return toWords(s).map(function (w) { return w.toLowerCase(); }).join('-');
    }

    function toDotCase(s) {
        return toWords(s).map(function (w) { return w.toLowerCase(); }).join('.');
    }

    function toConstantCase(s) {
        return toWords(s).map(function (w) { return w.toUpperCase(); }).join('_');
    }

    /* ── Apply conversion ──────────────────────────────────── */
    function applyConversion(caseType, sourceText) {
        if (!sourceText.trim()) {
            TCTP.toast('Please enter some text to convert.', 'Warning');
            return;
        }

        var result = '';
        switch (caseType) {
            case 'uppercase':   result = sourceText.toUpperCase(); break;
            case 'lowercase':   result = sourceText.toLowerCase(); break;
            case 'sentence':    result = toSentenceCase(sourceText); break;
            case 'title':       result = toTitleCase(sourceText); break;
            case 'capitalized': result = toCapitalizedCase(sourceText); break;
            case 'alternating': result = toAlternatingCase(sourceText); break;
            case 'inverse':     result = toInverseCase(sourceText); break;
            case 'camel':       result = toCamelCase(sourceText); break;
            case 'pascal':      result = toPascalCase(sourceText); break;
            case 'snake':       result = toSnakeCase(sourceText); break;
            case 'kebab':       result = toKebabCase(sourceText); break;
            case 'dot':         result = toDotCase(sourceText); break;
            case 'constant':    result = toConstantCase(sourceText); break;
            default: return;
        }

        /* ── Toggles ── */
        var trim = document.getElementById(PREFIX + 'trim');
        var dedup = document.getElementById(PREFIX + 'dedup-spaces');

        if (trim && trim.checked) {
            result = result.trim();
        }
        if (dedup && dedup.checked) {
            result = result.replace(/ {2,}/g, ' ');
        }

        TCTP.showProgress(PREFIX + 'progress');
        TCTP.setProgress(PREFIX + 'progress', 50, 'Converting...');

        convertedText = result;

        /* ── Result panel stats ── */
        TCTP.updateResultPanel(
            sourceText.length.toLocaleString() + ' chars',
            result.length.toLocaleString() + ' chars',
            (sourceText.length !== result.length
                ? ((result.length < sourceText.length ? '+' : '-') +
                   Math.abs(((result.length - sourceText.length) / sourceText.length) * 100).toFixed(1) + '%')
                : '0%'),
            'Done'
        );

        updateStats(result);

        /* ── Preview tabs ── */
        if (origPreview) origPreview.value = sourceText;
        if (resultPreview) resultPreview.value = result;

        TCTP.setProgress(PREFIX + 'progress', 100, 'Done!');
        TCTP.hideProgress(PREFIX + 'progress');
        TCTP.switchToResultTab();
        TCTP.toast('Converted to ' + caseType + ' case!');
    }

    /* ── Live input stats + original preview ────────────────── */
    inp.addEventListener('input', function () {
        updateStats(inp.value);
        if (origPreview) origPreview.value = inp.value;
    });

    /* ── Mode card click → convert immediately ──────────────── */
    document.querySelectorAll('.tc-cc-types .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            if (inp.value.trim()) {
                applyConversion(card.getAttribute('data-val'), inp.value);
            }
        });
    });

    /* ── Convert button ─────────────────────────────────────── */
    var convertBtn = document.getElementById(PREFIX + 'convert');
    if (convertBtn) {
        convertBtn.addEventListener('click', function () {
            applyConversion(getSelectedCase(), inp.value);
        });
    }

    /* ── Copy ───────────────────────────────────────────────── */
    var copyBtn = document.getElementById(PREFIX + 'copy');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            TCTP.copyText(convertedText, 'Result');
        });
    }

    /* ── Clear all ──────────────────────────────────────────── */
    var clearBtn = document.getElementById(PREFIX + 'clear');
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            inp.value = '';
            convertedText = '';

            var modeCards = document.querySelectorAll('.tc-cc-types .tc-rsz-mode-card');
            modeCards.forEach(function (c) { c.classList.remove('sel'); });
            if (modeCards[0]) modeCards[0].classList.add('sel');

            var trimCb = document.getElementById(PREFIX + 'trim');
            var dedupCb = document.getElementById(PREFIX + 'dedup-spaces');
            if (trimCb) trimCb.checked = false;
            if (dedupCb) dedupCb.checked = false;

            if (origPreview) origPreview.value = '';
            if (resultPreview) resultPreview.value = '';

            ['chars', 'words', 'sentences', 'lines'].forEach(function (k) {
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
