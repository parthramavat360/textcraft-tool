/**
 * NATO Phonetic Alphabet — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var inp = document.getElementById('tc-nato-input');
    var out = document.getElementById('tc-nato-output');
    var convertBtn = document.getElementById('tc-nato-convert');
    if (!inp || !out || !convertBtn) return;

    var NATO = {
        'A': 'Alpha',    'B': 'Bravo',     'C': 'Charlie',  'D': 'Delta',
        'E': 'Echo',     'F': 'Foxtrot',   'G': 'Golf',     'H': 'Hotel',
        'I': 'India',    'J': 'Juliett',   'K': 'Kilo',     'L': 'Lima',
        'M': 'Mike',     'N': 'November',  'O': 'Oscar',    'P': 'Papa',
        'Q': 'Quebec',   'R': 'Romeo',     'S': 'Sierra',   'T': 'Tango',
        'U': 'Uniform',  'V': 'Victor',    'W': 'Whiskey',  'X': 'X-ray',
        'Y': 'Yankee',   'Z': 'Zulu',
        '0': 'Zero',     '1': 'One',       '2': 'Two',      '3': 'Three',
        '4': 'Four',     '5': 'Five',      '6': 'Six',      '7': 'Seven',
        '8': 'Eight',    '9': 'Niner'
    };

    function convertToNATO(text, spaceSep, upperCase, showOriginal) {
        var words = text.split(/\s+/);
        var resultLines = [];

        for (var w = 0; w < words.length; w++) {
            var word = words[w];
            var parts = [];
            for (var i = 0; i < word.length; i++) {
                var ch = word[i].toUpperCase();
                var mapped = NATO[ch];
                if (mapped) {
                    parts.push(mapped);
                } else {
                    parts.push(word[i]);
                }
            }
            var line;
            if (upperCase) {
                line = parts.join(spaceSep ? ' - ' : '-');
            } else {
                line = parts.join(spaceSep ? ' - ' : '-').toLowerCase();
                line = line.replace(/\b\w/g, function (c) { return c.toUpperCase(); });
            }
            if (showOriginal) {
                line = word + ' -> ' + line;
            }
            resultLines.push(line);
        }

        return resultLines.join('\n');
    }

    convertBtn.addEventListener('click', function () {
        var text = inp.value.trim();
        if (!text) {
            TCTP.toast('Please enter some text.', '\u26A0\uFE0F');
            return;
        }

        var spaceSep = document.getElementById('tc-nato-include-space');
        var upperCase = document.getElementById('tc-nato-uppercase');
        var showOrig = document.getElementById('tc-nato-include-original');

        var result = convertToNATO(
            text,
            spaceSep ? spaceSep.checked : true,
            upperCase ? upperCase.checked : true,
            showOrig ? showOrig.checked : false
        );

        out.value = result;
        TCTP.toast('NATO conversion complete!');
    });

    document.getElementById('tc-nato-copy').addEventListener('click', function () {
        TCTP.copyText(out.value, 'NATO text');
    });

})();
