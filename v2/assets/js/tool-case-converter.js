/**
 * Case Converter — Tool JS
 *
 * Converts text between uppercase, lowercase, sentence case, title case,
 * capitalized case, alternating case, and inverse case.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var prefix = 'tc-cc-';
    var inp = document.getElementById(prefix + 'input');
    var out = document.getElementById(prefix + 'output');
    if (!inp || !out) return;

    var activeCase = null;

    // ── Stats ───────────────────────────────────────────────

    function updateStats(text) {
        var s = TCTP.getStats(text);
        var set = function (id, v) { var el = document.getElementById(id); if (el) el.textContent = v; };
        set(prefix + 'chars', s.chars.toLocaleString());
        set(prefix + 'words', s.words.toLocaleString());
        set(prefix + 'sentences', s.sentences.toLocaleString());
        set(prefix + 'lines', s.lines.toLocaleString());
    }

    // ── Conversion functions ────────────────────────────────

    function toUpperCase(s) { return s.toUpperCase(); }
    function toLowerCase(s) { return s.toLowerCase(); }

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

    // ── Apply conversion ────────────────────────────────────

    function applyConversion(caseType, sourceText) {
        if (!sourceText.trim()) {
            TCTP.toast('Please enter some text to convert.', '\u26A0\uFE0F');
            return;
        }

        var result = '';
        switch (caseType) {
            case 'uppercase':   result = toUpperCase(sourceText); break;
            case 'lowercase':   result = toLowerCase(sourceText); break;
            case 'sentence':    result = toSentenceCase(sourceText); break;
            case 'title':       result = toTitleCase(sourceText); break;
            case 'capitalized': result = toCapitalizedCase(sourceText); break;
            case 'alternating': result = toAlternatingCase(sourceText); break;
            case 'inverse':     result = toInverseCase(sourceText); break;
            default: return;
        }

        out.value = result;
        activeCase = caseType;
        updateStats(result);
    }

    // ── Mode buttons ────────────────────────────────────────

    document.querySelectorAll('.tctp-modes[data-group="case-type"] .tctp-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            applyConversion(btn.getAttribute('data-val'), inp.value);
        });
    });

    // ── Convert button ──────────────────────────────────────

    var convertBtn = document.getElementById(prefix + 'convert');
    if (convertBtn) {
        convertBtn.addEventListener('click', function () {
            var activeBtn = document.querySelector('.tctp-modes[data-group="case-type"] .tctp-btn.sel');
            var mode = activeBtn ? activeBtn.getAttribute('data-val') : 'uppercase';
            applyConversion(mode, inp.value);
        });
    }

    // ── Copy ────────────────────────────────────────────────

    var copyBtn = document.getElementById(prefix + 'copy');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            TCTP.copyText(out.value, 'Result');
        });
    }

    // ── Input stats ─────────────────────────────────────────

    inp.addEventListener('input', function () {
        updateStats(inp.value);
    });

    // ── Init ────────────────────────────────────────────────

    updateStats('');

})();