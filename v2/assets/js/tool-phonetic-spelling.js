/**
 * Phonetic Spelling Generator — Tool JS
 *
 * Premium design: mode cards, toggles, preview tabs, copy, live stats.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var PREFIX = 'tc-ps-';
    var cleanedText = '';

    var inp = document.getElementById(PREFIX + 'input');
    if (!inp) return;

    var origPreview = document.getElementById(PREFIX + 'preview-orig');
    var resultPreview = document.getElementById(PREFIX + 'preview-result');

    /* ── Mode cards ─────────────────────────────────────────── */
    document.querySelectorAll('.tc-ps-modes .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.tc-ps-modes .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
        });
    });

    function getMode() {
        var s = document.querySelector('.tc-ps-modes .tc-rsz-mode-card.sel');
        return s ? s.getAttribute('data-val') : 'simplified';
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

    /* ── Maps ──────────────────────────────────────────────── */
    var SIMPLIFIED_MAP = {
        'A': 'ay',   'B': 'bee',   'C': 'see',   'D': 'dee',
        'E': 'ee',   'F': 'eff',   'G': 'jee',   'H': 'aitch',
        'I': 'eye',  'J': 'jay',   'K': 'kay',   'L': 'ell',
        'M': 'em',   'N': 'en',    'O': 'oh',    'P': 'pee',
        'Q': 'cue',  'R': 'arr',   'S': 'ess',   'T': 'tee',
        'U': 'you',  'V': 'vee',   'W': 'double-u', 'X': 'ex',
        'Y': 'why',  'Z': 'zee'
    };

    var SOUNDALIKE_MAP = {
        'A': 'eh',   'B': 'buh',   'C': 'kuh',   'D': 'duh',
        'E': 'ee',   'F': 'fuh',   'G': 'guh',   'H': 'huh',
        'I': 'ih',   'J': 'juh',   'K': 'kuh',   'L': 'luh',
        'M': 'muh',  'N': 'nuh',   'O': 'oh',    'P': 'puh',
        'Q': 'kweh', 'R': 'ruh',   'S': 'suh',   'T': 'tuh',
        'U': 'oo',   'V': 'vuh',   'W': 'wuh',   'X': 'iks',
        'Y': 'yuh',  'Z': 'zuh'
    };

    var NATO_MAP = {
        'A': 'Alpha',    'B': 'Bravo',     'C': 'Charlie',  'D': 'Delta',
        'E': 'Echo',     'F': 'Foxtrot',   'G': 'Golf',     'H': 'Hotel',
        'I': 'India',    'J': 'Juliett',   'K': 'Kilo',     'L': 'Lima',
        'M': 'Mike',     'N': 'November',  'O': 'Oscar',    'P': 'Papa',
        'Q': 'Quebec',   'R': 'Romeo',     'S': 'Sierra',   'T': 'Tango',
        'U': 'Uniform',  'V': 'Victor',    'W': 'Whiskey',  'X': 'X-ray',
        'Y': 'Yankee',   'Z': 'Zulu'
    };

    function getMap() {
        var mode = getMode();
        switch (mode) {
            case 'nato':       return NATO_MAP;
            case 'soundalike': return SOUNDALIKE_MAP;
            case 'simplified':
            default:           return SIMPLIFIED_MAP;
        }
    }

    /* ── Syllable breaker ──────────────────────────────────── */
    function breakIntoSyllables(word) {
        var vowels = 'aeiou';
        var lower = word.toLowerCase();
        var syllables = [];
        var current = '';

        for (var i = 0; i < lower.length; i++) {
            current += lower[i];
            var isVowel = vowels.indexOf(lower[i]) !== -1;
            var nextIsVowel = i + 1 < lower.length && vowels.indexOf(lower[i + 1]) !== -1;

            if (isVowel && !nextIsVowel && current.length >= 2 && i + 1 < lower.length) {
                syllables.push(current);
                current = '';
            } else if (!isVowel && nextIsVowel && current.length >= 2 && syllables.length > 0) {
                syllables.push(current);
                current = '';
            }
        }
        if (current) syllables.push(current);
        if (syllables.length === 0 && word.length > 0) syllables.push(word.toLowerCase());
        return syllables;
    }

    function spellWord(word, map, doSyllables, markStress) {
        var upper = word.toUpperCase();
        if (doSyllables) {
            var syllables = breakIntoSyllables(word);
            var parts = [];
            for (var s = 0; s < syllables.length; s++) {
                var syn = syllables[s];
                var phonParts = [];
                for (var i = 0; i < syn.length; i++) {
                    var ch = syn[i].toUpperCase();
                    var mapped = map[ch];
                    phonParts.push(mapped || syn[i]);
                }
                var phonetic = phonParts.join('-');
                if (markStress && s === 0) {
                    phonetic = '[' + phonetic + ']';
                }
                parts.push(phonetic);
            }
            return parts.join(' | ');
        }

        var parts2 = [];
        for (var j = 0; j < upper.length; j++) {
            var mapped2 = map[upper[j]];
            parts2.push(mapped2 || word[j]);
        }
        return parts2.join('-');
    }

    /* ── Perform generate ───────────────────────────────────── */
    function performGenerate() {
        var text = inp.value;

        if (!text.trim()) {
            TCTP.toast('Paste some text first.', '\u26A0\uFE0F');
            return;
        }

        var syllCb = document.getElementById(PREFIX + 'syllables');
        var stressCb = document.getElementById(PREFIX + 'stress');
        var doSyllables = syllCb ? syllCb.checked : true;
        var markStress = stressCb ? stressCb.checked : false;
        var map = getMap();

        var mapped = 0;
        var words = text.split(/\s+/);
        var lines = [];

        for (var w = 0; w < words.length; w++) {
            var word = words[w];
            var letters = word.replace(/[^a-zA-Z]/g, '');
            if (!letters) {
                lines.push(word);
                continue;
            }
            var spelling = spellWord(letters, map, doSyllables, markStress);
            lines.push(word + ' \u2192 ' + spelling);
            mapped += letters.length;
        }

        var result = lines.join('\n');
        cleanedText = result;

        setStat(PREFIX + 'mapped', mapped.toLocaleString());
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

        TCTP.toast('Phonetic spelling generated!', '\u2705');
    }

    /* ── Live input stats + original preview ────────────────── */
    inp.addEventListener('input', function () {
        updateStats(inp.value);
        if (origPreview) origPreview.value = inp.value;
    });

    /* ── Generate button ────────────────────────────────────── */
    var genBtn = document.getElementById(PREFIX + 'generate');
    if (genBtn) {
        genBtn.addEventListener('click', function () {
            TCTP.showProgress(PREFIX + 'bar');
            TCTP.setProgress(PREFIX + 'bar', 50, 'Generating...');

            setTimeout(function () {
                performGenerate();
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
