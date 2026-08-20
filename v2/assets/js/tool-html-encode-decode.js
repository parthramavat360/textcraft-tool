/**
 * HTML Encode/Decode — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var inp = document.getElementById('tc-html-input');
    var out = document.getElementById('tc-html-output');
    var convertBtn = document.getElementById('tc-html-convert');
    if (!inp || !out || !convertBtn) return;

    var direction = 'encode';

    document.querySelectorAll('.tc-modes[data-group="html-direction"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            direction = btn.getAttribute('data-val');
        });
    });

    var BASIC_MAP = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };
    var BASIC_REVERSE = {};
    Object.keys(BASIC_MAP).forEach(function (k) { BASIC_REVERSE[BASIC_MAP[k]] = k; });

    var EXTENDED_MAP = {
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
        '\u00a0': '&nbsp;', '\u00a1': '&iexcl;', '\u00a2': '&cent;', '\u00a3': '&pound;',
        '\u00a9': '&copy;', '\u00ae': '&reg;', '\u2122': '&trade;', '\u20ac': '&euro;',
        '\u00ab': '&laquo;', '\u00bb': '&raquo;', '\u2018': '&lsquo;', '\u2019': '&rsquo;',
        '\u201c': '&ldquo;', '\u201d': '&rdquo;', '\u2013': '&ndash;', '\u2014': '&mdash;'
    };

    function encode(text, level) {
        if (level === 'numeric') {
            return Array.from(text).map(function (ch) {
                var code = ch.charCodeAt(0);
                return code < 128 ? BASIC_MAP[ch] || ch : '&#' + code + ';';
            }).join('');
        }
        if (level === 'hex') {
            return Array.from(text).map(function (ch) {
                var code = ch.charCodeAt(0);
                return code < 128 ? BASIC_MAP[ch] || ch : '&#x' + code.toString(16) + ';';
            }).join('');
        }
        var map = level === 'extended' ? EXTENDED_MAP : BASIC_MAP;
        return Array.from(text).map(function (ch) { return map[ch] || ch; }).join('');
    }

    function decode(text) {
        var result = text;
        result = result.replace(/&#x([0-9a-fA-F]+);/g, function (_, hex) {
            return String.fromCharCode(parseInt(hex, 16));
        });
        result = result.replace(/&#(\d+);/g, function (_, dec) {
            return String.fromCharCode(parseInt(dec, 10));
        });
        Object.keys(BASIC_REVERSE).sort(function (a, b) { return b.length - a.length; }).forEach(function (entity) {
            result = result.split(entity).join(BASIC_REVERSE[entity]);
        });
        var namedEntities = {
            '&nbsp;': '\u00a0', '&iexcl;': '\u00a1', '&cent;': '\u00a2', '&pound;': '\u00a3',
            '&copy;': '\u00a9', '&reg;': '\u00ae', '&trade;': '\u2122', '&euro;': '\u20ac',
            '&laquo;': '\u00ab', '&raquo;': '\u00bb', '&lsquo;': '\u2018', '&rsquo;': '\u2019',
            '&ldquo;': '\u201c', '&rdquo;': '\u201d', '&ndash;': '\u2013', '&mdash;': '\u2014'
        };
        Object.keys(namedEntities).forEach(function (entity) {
            result = result.split(entity).join(namedEntities[entity]);
        });
        return result;
    }

    convertBtn.addEventListener('click', function () {
        var text = inp.value;
        if (!text) {
            TCTP.toast('Enter some text first.', '\u26A0\uFE0F');
            return;
        }
        var level = (document.getElementById('tc-html-level') || {}).value || 'basic';
        var result = direction === 'encode' ? encode(text, level) : decode(text);
        out.value = result;
        var statusEl = document.getElementById('tc-html-status');
        if (statusEl) {
            statusEl.textContent = 'Converted ' + text.length + ' characters';
            statusEl.className = 'tc-status tc-status--success';
        }
        TCTP.toast('Conversion complete!');
    });

    document.getElementById('tc-html-copy').addEventListener('click', function () {
        TCTP.copyText(out.value, 'HTML text');
    });

})();
