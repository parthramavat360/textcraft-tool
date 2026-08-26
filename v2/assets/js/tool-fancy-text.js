/**
 * Fancy Text Generator - Tool JS
 * Converts text into 25+ Unicode font styles in real time.
 * All Unicode chars built via String.fromCodePoint() for encoding safety.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    // ================================================================
    //  UNICODE FONT MAP BUILDER
    // ================================================================

    function buildMap(upperStart, lowerStart) {
        var m = {};
        var u = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        var l = 'abcdefghijklmnopqrstuvwxyz';
        for (var i = 0; i < 26; i++) {
            m[u[i]] = String.fromCodePoint(upperStart + i);
            m[l[i]] = String.fromCodePoint(lowerStart + i);
        }
        return m;
    }

    function applyMap(text, map) {
        return Array.from(text).map(function (c) { return map[c] || c; }).join('');
    }

    // Combining characters
    var COMB_STRIKE  = '\u0336';
    var COMB_UNDER   = '\u0332';
    var COMB_DOT     = '\u0324';
    var COMB_OVER    = '\u0305';

    // ================================================================
    //  FONT MAPS (all built from code points, zero raw UTF-8)
    // ================================================================

    // Mathematical Bold:               U+1D400
    // Mathematical Italic:             U+1D468
    // Mathematical Bold Italic:        U+1D4D0
    // Mathematical Monospace:          U+1D670
    // Mathematical Double-Struck:      U+1D538
    // Mathematical Script:             U+1D49C
    // Mathematical Gothic:             U+1D504
    // Mathematical Sans-Serif:         U+1D5A0
    // Mathematical Sans-Serif Bold:    U+1D5D4
    // Mathematical Sans-Serif Italic:  U+1D608

    var MAP_BOLD       = buildMap(0x1D400, 0x1D41A);
    var MAP_ITALIC     = buildMap(0x1D434, 0x1D44E);
    var MAP_BOLDITALIC = buildMap(0x1D468, 0x1D482);
    var MAP_MONO       = buildMap(0x1D670, 0x1D68A);
    var MAP_DOUBLE     = buildMap(0x1D538, 0x1D552);
    var MAP_SCRIPT     = buildMap(0x1D49C, 0x1D4B6);
    var MAP_GOTHIC     = buildMap(0x1D504, 0x1D51E);
    var MAP_SANS       = buildMap(0x1D5A0, 0x1D5BA);
    var MAP_SANS_BOLD  = buildMap(0x1D5D4, 0x1D5EE);
    var MAP_SANS_ITAL  = buildMap(0x1D608, 0x1D622);
    var MAP_MONO_BOLD  = buildMap(0x1D670, 0x1D68A);
    var MAP_BB         = buildMap(0x1D538, 0x1D552);

    // Small caps - built from Latin Extended-D code points
    var MAP_SMALLCAPS = (function () {
        var m = {};
        var scCodes = [
            0x1D00,0x0299,0x1D04,0x1D05,0x1D07,0xA730,0x0262,0x029C,
            0x026A,0x029F,0x0245,0x026F,0x0270,0x0274,0x0254,0x0275,
            0x0280,0x0280,0xA731,0x027E,0x0287,0x028C,0x028D,0x028E,
            0x028F,0x01B3
        ];
        var lo = 'abcdefghijklmnopqrstuvwxyz';
        for (var i = 0; i < 26; i++) {
            m[lo[i]] = String.fromCodePoint(scCodes[i]);
        }
        m[' '] = ' ';
        return m;
    })();

    // Superscript map
    var SUP_NUM = ['\u2070','\u00B9','\u00B2','\u00B3','\u2074','\u2075','\u2076','\u2077','\u2078','\u2079'];
    var SUB_NUM = ['\u2080','\u2081','\u2082','\u2083','\u2084','\u2085','\u2086','\u2087','\u2088','\u2089'];
    var MAP_SUPER = (function () {
        var m = {};
        var up = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        var lo = 'abcdefghijklmnopqrstuvwxyz';
        var dig = '0123456789';
        var supUpChars = [
            '\u02B0','\u02B1','\u02B2','\u02B3','\u1D57','\u02B9','\u02BA','\u02BB',
            '\u02BC','\u02BD','\u02BE','\u02BF','\u02C0','\u02C1','\u02C6','\u02C7',
            '\u02C8','\u02CC','\u02CD','\u02CE','\u02CF','\u02D0','\u02D1','\u02E0',
            '\u02E1','\u02E2'
        ];
        var supLoChars = [
            '\u2090','\u2091','\u1D62','\u2093','\u1D49','\u2095','\u1D5D','\u2095',
            '\u2071','\u2C7C','\u2096','\u2097','\u2098','\u2099','\u209A','\u209C',
            '\u1D4F','\u2071','\u1D62','\u2C7C','\u1D5F','\u2096','\u1D58','\u2097',
            '\u2098','\u2099'
        ];
        for (var i = 0; i < 26; i++) m[up[i]] = supUpChars[i];
        for (var i = 0; i < 26; i++) m[lo[i]] = supLoChars[i];
        for (var i = 0; i < 10; i++) m[dig[i]] = SUP_NUM[i];
        m['+'] = '\u207A'; m['-'] = '\u207B'; m['='] = '\u207C';
        m['('] = '\u207D'; m[')'] = '\u207E';
        return m;
    })();

    // Subscript map
    var MAP_SUB = (function () {
        var m = {};
        var lo = 'abcdefghijklmnopqrstuvwxyz';
        var dig = '0123456789';
        var subLoChars = [
            '\u2090','\u2083','\u1D62','\u2093','\u2091','\u2095','\u2095','\u2095',
            '\u2071','\u2C7C','\u2096','\u2097','\u2098','\u2099','\u209A','\u209C',
            '\u1D4F','\u2071','\u1D62','\u2C7C','\u1D5F','\u2096','\u1D58','\u2097',
            '\u2098','\u2099'
        ];
        for (var i = 0; i < 26; i++) m[lo[i]] = subLoChars[i] || lo[i];
        for (var i = 0; i < 10; i++) m[dig[i]] = SUB_NUM[i];
        m['+'] = '\u208A'; m['-'] = '\u208B'; m['='] = '\u208C';
        m['('] = '\u208D'; m[')'] = '\u208E';
        return m;
    })();

    // Circled (A=U+24B6, a=U+24D0, 0=U+245D)
    var CIRCLE_UPPER = function (text) {
        return Array.from(text).map(function (c) {
            var code = c.toUpperCase().charCodeAt(0);
            if (code >= 65 && code <= 90) return String.fromCodePoint(0x24B6 + (code - 65));
            var d = parseInt(c);
            if (!isNaN(d)) return String.fromCodePoint(0x245D + d);
            return c;
        }).join('');
    };

    var CIRCLE_LOWER = function (text) {
        return Array.from(text).map(function (c) {
            var code = c.toLowerCase().charCodeAt(0);
            if (code >= 97 && code <= 122) return String.fromCodePoint(0x24D0 + (code - 97));
            return c;
        }).join('');
    };

    // Squared (A=U+1F130)
    var SQUARED = function (text) {
        return Array.from(text.toUpperCase()).map(function (c) {
            var code = c.charCodeAt(0);
            if (code >= 65 && code <= 90) return String.fromCodePoint(0x1F130 + (code - 65));
            return c;
        }).join('');
    };

    // Wide / Fullwidth (A=U+FF21, a=U+FF41, 0=U+FF10, space=U+3000)
    var MAP_WIDE = (function () {
        var m = {};
        var up = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        var lo = 'abcdefghijklmnopqrstuvwxyz';
        var dig = '0123456789';
        for (var i = 0; i < 26; i++) {
            m[up[i]] = String.fromCodePoint(0xFF21 + i);
            m[lo[i]] = String.fromCodePoint(0xFF41 + i);
        }
        for (var i = 0; i < 10; i++) m[dig[i]] = String.fromCodePoint(0xFF10 + i);
        m[' '] = '\u3000';
        return m;
    })();

    // Upside Down
    var MAP_UPDOWN = (function () {
        var m = {};
        m['a'] = '\u0250'; m['b'] = 'q'; m['c'] = '\u0254'; m['d'] = 'p';
        m['e'] = '\u01DD'; m['f'] = '\u025F'; m['g'] = '\u0183'; m['h'] = '\u0265';
        m['i'] = '\u0131'; m['j'] = '\u027E'; m['k'] = '\u029E'; m['l'] = 'l';
        m['m'] = '\u026F'; m['n'] = 'u'; m['o'] = 'o'; m['p'] = 'd';
        m['q'] = 'b'; m['r'] = '\u0279'; m['s'] = 's'; m['t'] = '\u0287';
        m['u'] = 'n'; m['v'] = '\u028C'; m['w'] = '\u028D'; m['x'] = 'x';
        m['y'] = '\u028E'; m['z'] = 'z';
        m['A'] = '\u2200'; m['B'] = '\u18D7'; m['C'] = '\u0186'; m['D'] = '\u18E1';
        m['E'] = '\u018E'; m['F'] = '\u2132'; m['G'] = '\u2141'; m['H'] = 'H';
        m['I'] = 'I'; m['J'] = '\u027E'; m['K'] = '\u029E'; m['L'] = '\u02E5';
        m['M'] = 'W'; m['N'] = 'N'; m['O'] = 'O'; m['P'] = '\u0500';
        m['Q'] = 'Q'; m['R'] = '\u1D0F'; m['S'] = 'S'; m['T'] = '\u22A5';
        m['U'] = '\u2229'; m['V'] = '\u039B'; m['W'] = 'M'; m['X'] = 'X';
        m['Y'] = '\u2144'; m['Z'] = 'Z';
        m['0'] = '0'; m['1'] = '\u0196'; m['2'] = '\u0191'; m['3'] = '\u018A';
        m['4'] = '\u0193'; m['5'] = '\u01BD'; m['6'] = '9'; m['7'] = '\u0190';
        m['8'] = '8'; m['9'] = '6';
        m['.'] = '\u02D9'; m[','] = '\u02CC'; m['?'] = '\u00BF'; m['!'] = '\u00A1';
        m['('] = ')'; m[')'] = '('; m[' '] = ' ';
        return function (text) {
            return Array.from(text).reverse().map(function (c) { return m[c] || c; }).join('');
        };
    })();

    // Bubble (enclosed: A=U+24B6, a=U+24D0)
    var BUBBLE_UPPER = function (text) {
        return Array.from(text.toUpperCase()).map(function (c) {
            var code = c.charCodeAt(0);
            if (code >= 65 && code <= 90) return String.fromCodePoint(0x24B6 + (code - 65));
            var d = parseInt(c);
            if (!isNaN(d)) return String.fromCodePoint(0x245D + d);
            if (c === ' ') return ' ';
            return '(' + c + ')';
        }).join('');
    };

    var BUBBLE_LOWER = function (text) {
        return Array.from(text).map(function (c) {
            var code = c.toLowerCase().charCodeAt(0);
            if (code >= 97 && code <= 122) return String.fromCodePoint(0x24D0 + (code - 97));
            return c;
        }).join('');
    };

    // Combining decorators
    function combining(char) {
        return function (text) {
            return Array.from(text).map(function (c) { return c + char; }).join('');
        };
    }

    // ================================================================
    //  STYLE DEFINITIONS
    // ================================================================

    var STYLES = [
        { id: 'bold',          name: 'Bold',              preview: 'Hello World',  fn: function (t) { return applyMap(t, MAP_BOLD); } },
        { id: 'italic',        name: 'Italic',            preview: 'Hello World',  fn: function (t) { return applyMap(t, MAP_ITALIC); } },
        { id: 'bolditalic',    name: 'Bold Italic',       preview: 'Hello World',  fn: function (t) { return applyMap(t, MAP_BOLDITALIC); } },
        { id: 'monospace',     name: 'Monospace',         preview: 'Hello World',  fn: function (t) { return applyMap(t, MAP_MONO); } },
        { id: 'double',        name: 'Double-Struck',     preview: 'Hello World',  fn: function (t) { return applyMap(t, MAP_DOUBLE); } },
        { id: 'script',        name: 'Script',            preview: 'Hello World',  fn: function (t) { return applyMap(t, MAP_SCRIPT); } },
        { id: 'gothic',        name: 'Gothic',            preview: 'Hello World',  fn: function (t) { return applyMap(t, MAP_GOTHIC); } },
        { id: 'sans',          name: 'Sans-Serif',        preview: 'Hello World',  fn: function (t) { return applyMap(t, MAP_SANS); } },
        { id: 'sansbold',      name: 'Sans-Serif Bold',   preview: 'Hello World',  fn: function (t) { return applyMap(t, MAP_SANS_BOLD); } },
        { id: 'sansitalic',    name: 'Sans-Serif Italic', preview: 'Hello World',  fn: function (t) { return applyMap(t, MAP_SANS_ITAL); } },
        { id: 'mono_bold',     name: 'Monospace Bold',    preview: 'Hello World',  fn: function (t) { return applyMap(t, MAP_MONO_BOLD); } },
        { id: 'smallcaps',     name: 'Small Caps',        preview: 'Hello World',  fn: function (t) { return applyMap(t, MAP_SMALLCAPS); } },
        { id: 'superscript',   name: 'Superscript',       preview: 'Hello World',  fn: function (t) { return applyMap(t, MAP_SUPER); } },
        { id: 'subscript',     name: 'Subscript',         preview: 'Hello World',  fn: function (t) { return applyMap(t, MAP_SUB); } },
        { id: 'circled',       name: 'Circled',           preview: 'Hello',        fn: function (t) { return CIRCLE_UPPER(t); } },
        { id: 'circledlow',    name: 'Circled Lower',     preview: 'hello',        fn: function (t) { return CIRCLE_LOWER(t); } },
        { id: 'squared',       name: 'Squared',           preview: 'ABC',          fn: function (t) { return SQUARED(t); } },
        { id: 'wide',          name: 'Wide / Aesthetic',   preview: 'Hello World',  fn: function (t) { return applyMap(t, MAP_WIDE); } },
        { id: 'strikethrough', name: 'Strikethrough',     preview: 'Hello World',  fn: combining(COMB_STRIKE) },
        { id: 'underline',     name: 'Underline',         preview: 'Hello World',  fn: combining(COMB_UNDER) },
        { id: 'underdot',      name: 'Under Dotted',      preview: 'Hello World',  fn: combining(COMB_DOT) },
        { id: 'overline',      name: 'Overline',          preview: 'Hello World',  fn: combining(COMB_OVER) },
        { id: 'updown',        name: 'Upside Down',       preview: 'Hello World',  fn: function (t) { return MAP_UPDOWN(t); } },
        { id: 'bubble',        name: 'Bubble',            preview: 'Hello',        fn: function (t) { return BUBBLE_LOWER(t); } },
        { id: 'bubblecap',     name: 'Bubble Caps',       preview: 'HELLO',        fn: function (t) { return BUBBLE_UPPER(t); } },
        { id: 'blackboard',    name: 'Blackboard',        preview: 'Hello',        fn: function (t) { return applyMap(t, MAP_BB); } },
    ];

    // ================================================================
    //  INIT
    // ================================================================

    function init() {
        var inp = document.getElementById('tc-ft-input');
        var grid = document.getElementById('tc-ft-grid');
        var search = document.getElementById('tc-ft-search');
        if (!inp || !grid || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var cards = [];

        STYLES.forEach(function (style) {
            var card = document.createElement('div');
            card.className = 'tc-fancy-card';
            card.setAttribute('data-style', style.id);
            card.setAttribute('data-name', style.name.toLowerCase());

            card.innerHTML =
                '<div class="tc-fancy-card-body">' +
                    '<div class="tc-fancy-card-head">' +
                        '<span class="tc-fancy-card-name">' + style.name + '</span>' +
                        '<button class="tc-fancy-copy-btn" type="button" title="Copy to clipboard">' +
                            '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>' +
                            ' Copy' +
                        '</button>' +
                    '</div>' +
                    '<div class="tc-fancy-card-preview" id="tc-ft-' + style.id + '">' +
                        '<span class="tc-fancy-placeholder">' + style.preview + '</span>' +
                    '</div>' +
                '</div>';

            grid.appendChild(card);
            cards.push({ el: card, style: style });

            var copyBtn = card.querySelector('.tc-fancy-copy-btn');
            copyBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                var preview = card.querySelector('.tc-fancy-card-preview');
                var text = preview.textContent || preview.innerText;
                if (text && text !== style.preview) {
                    TCTP.copyText(text, style.name);
                } else {
                    TCTP.toast('Type some text first!', '\u26A0\uFE0F');
                }
            });

            card.addEventListener('click', function () {
                var preview = card.querySelector('.tc-fancy-card-preview');
                var text = preview.textContent || preview.innerText;
                if (text && text !== style.preview) {
                    TCTP.copyText(text, style.name);
                } else {
                    TCTP.toast('Type some text first!', '\u26A0\uFE0F');
                }
            });
        });

        function generate() {
            var text = inp.value;
            cards.forEach(function (item) {
                var el = document.getElementById('tc-ft-' + item.style.id);
                if (!el) return;
                if (!text) {
                    el.innerHTML = '<span class="tc-fancy-placeholder">' + item.style.preview + '</span>';
                } else {
                    el.textContent = item.style.fn(text);
                }
            });
            var wordCount = text.trim() ? text.trim().split(/\s+/).length : 0;
            TCTP.updateResultPanel(
                text.length.toLocaleString() + ' chars',
                cards.length + ' styles',
                wordCount + ' words',
                text ? 'Done' : 'Idle'
            );
        }

        inp.addEventListener('input', generate);

        if (search) {
            search.addEventListener('input', function () {
                var q = search.value.toLowerCase().trim();
                cards.forEach(function (item) {
                    if (!q || item.style.name.toLowerCase().indexOf(q) !== -1 || item.style.id.indexOf(q) !== -1) {
                        item.el.style.display = '';
                    } else {
                        item.el.style.display = 'none';
                    }
                });
            });
        }

        generate();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    new MutationObserver(function () { init(); })
        .observe(document.documentElement, { childList: true, subtree: true });
})();
