/**
 * Phonetic Spelling Generator — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var inp = document.getElementById('tc-ps-input');
    var out = document.getElementById('tc-ps-output');
    var genBtn = document.getElementById('tc-ps-generate');
    if (!inp || !out || !genBtn) return;

    var currentMode = 'simplified';

    document.querySelectorAll('.tctp-modes[data-group="ps-mode"] .tctp-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            currentMode = btn.getAttribute('data-val');
        });
    });

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

        var parts = [];
        for (var i = 0; i < upper.length; i++) {
            var mapped = map[upper[i]];
            parts.push(mapped || word[i]);
        }
        return parts.join('-');
    }

    function getMap() {
        switch (currentMode) {
            case 'simplified': return SIMPLIFIED_MAP;
            case 'nato':       return NATO_MAP;
            case 'soundalike': return SOUNDALIKE_MAP;
            default:           return SIMPLIFIED_MAP;
        }
    }

    genBtn.addEventListener('click', function () {
        var text = inp.value.trim();
        if (!text) {
            TCTP.toast('Please enter some text.', '\u26A0\uFE0F');
            return;
        }

        var syllCb = document.getElementById('tc-ps-syllables');
        var stressCb = document.getElementById('tc-ps-stress');
        var doSyllables = syllCb ? syllCb.checked : true;
        var markStress = stressCb ? stressCb.checked : false;
        var map = getMap();

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
            if (currentMode === 'nato' && !doSyllables) {
                lines.push(word + ' -> ' + spelling);
            } else {
                lines.push(word + ' -> ' + spelling);
            }
        }

        out.value = lines.join('\n');
        TCTP.toast('Phonetic spelling generated!');
    });

    document.getElementById('tc-ps-copy').addEventListener('click', function () {
        TCTP.copyText(out.value, 'Phonetic spelling');
    });

})();
