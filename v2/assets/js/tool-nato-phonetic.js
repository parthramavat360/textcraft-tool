/**
 * NATO Phonetic Alphabet — Tool JS
 *
 * Premium design: mode cards, toggles, preview tabs, copy, live stats.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var PREFIX = 'tc-nato-';
    var cleanedText = '';

    var inp = document.getElementById(PREFIX + 'input');
    if (!inp) return;

    var origPreview = document.getElementById(PREFIX + 'preview-orig');
    var resultPreview = document.getElementById(PREFIX + 'preview-result');

    /* ── Mode cards ─────────────────────────────────────────── */
    document.querySelectorAll('.tc-nato-modes .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.tc-nato-modes .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
        });
    });

    function getMode() {
        var s = document.querySelector('.tc-nato-modes .tc-rsz-mode-card.sel');
        return s ? s.getAttribute('data-val') : 'dash';
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

    /* ── NATO map ───────────────────────────────────────────── */
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

    /* ── Perform convert ────────────────────────────────────── */
    function performConvert() {
        var text = inp.value;

        if (!text.trim()) {
            TCTP.toast('Paste some text first.', '\u26A0\uFE0F');
            return;
        }

        var mode = getMode();
        var upperEl = document.getElementById(PREFIX + 'uppercase');
        var doUpper = upperEl ? upperEl.checked : true;

        var mapped = 0;
        var words = text.split(/\s+/);
        var resultLines = [];

        for (var w = 0; w < words.length; w++) {
            var word = words[w];
            var parts = [];
            for (var i = 0; i < word.length; i++) {
                var ch = word[i].toUpperCase();
                var natoWord = NATO[ch];
                if (natoWord) {
                    parts.push(doUpper ? natoWord : natoWord.charAt(0).toUpperCase() + natoWord.slice(1).toLowerCase());
                    mapped++;
                } else {
                    parts.push(word[i]);
                }
            }
            var line;
            if (mode === 'dash') {
                line = parts.join(' - ');
            } else if (mode === 'nodash') {
                line = parts.join('');
            } else if (mode === 'newline') {
                line = parts.join('\n');
            } else if (mode === 'table') {
                line = word + ' \u2192 ' + parts.join(' - ');
            } else {
                line = parts.join(' - ');
            }
            resultLines.push(line);
        }

        var result;
        if (mode === 'newline' && resultLines.length > 1) {
            result = resultLines.join('\n\n');
        } else {
            result = resultLines.join('\n');
        }

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

        TCTP.toast('NATO conversion complete!', '\u2705');
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
