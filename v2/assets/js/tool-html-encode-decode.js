/**
 * HTML Encode/Decode — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

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

    function init() {
        var inp = document.getElementById('tc-html-input');
        var out = document.getElementById('tc-html-output');
        var convertBtn = document.getElementById('tc-html-convert');
        if (!inp || !out || !convertBtn || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var direction = 'encode';

        // Direction buttons (PHP renders .tc-modes[data-group="html-direction"] .tc-btn)
        var group = document.querySelector('.tc-modes[data-group="html-direction"]');
        if (group && !group.dataset.tcModes) {
            group.dataset.tcModes = '1';
            var dirBtns = group.querySelectorAll('.tc-btn');
            dirBtns.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    dirBtns.forEach(function (b) { b.classList.remove('sel'); });
                    btn.classList.add('sel');
                    direction = btn.getAttribute('data-val') || 'encode';
                });
            });
            var selected = group.querySelector('.tc-btn.sel');
            if (selected) direction = selected.getAttribute('data-val') || 'encode';
        }

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

        function setStatus(type, message) {
            var statusEl = document.getElementById('tc-html-status');
            if (!statusEl) return;
            statusEl.textContent = message;
            statusEl.className = 'tc-status' + (type ? ' tc-status--' + type : '');
        }

        convertBtn.addEventListener('click', function () {
            var text = inp.value;
            if (!text) {
                TCTP.toast('Enter some text first.', '\u26A0\uFE0F');
                return;
            }
            var levelSel = document.getElementById('tc-html-level');
            var level = levelSel ? levelSel.value : 'basic';
            if (!level) level = 'basic';
            var result = direction === 'encode' ? encode(text, level) : decode(text);
            out.value = result;
            TCTP.updateResultPanel(text.length.toLocaleString() + ' chars', result.length.toLocaleString() + ' chars', (result.length < text.length ? ((1 - result.length / text.length) * 100).toFixed(1) + '%' : '0%'), 'Done');
            setStatus('success', 'Converted ' + text.length + ' characters');
            TCTP.toast('Conversion complete!');
        });

        var copyBtn = document.getElementById('tc-html-copy');
        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                TCTP.copyText(out.value, 'HTML text');
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Re-init after Elementor AJAX re-render
    new MutationObserver(function () { init(); })
        .observe(document.documentElement, { childList: true, subtree: true });
})();
