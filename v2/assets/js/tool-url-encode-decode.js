/**
 * URL Encode/Decode — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var inp = document.getElementById('tc-url-input');
    var out = document.getElementById('tc-url-output');
    var convertBtn = document.getElementById('tc-url-convert');
    if (!inp || !out || !convertBtn) return;

    var direction = 'encode';

    document.querySelectorAll('.tc-modes[data-group="url-direction"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            direction = btn.getAttribute('data-val');
        });
    });

    function encodeComponent(text) {
        return encodeURIComponent(text)
            .replace(/!/g, '%21')
            .replace(/'/g, '%27')
            .replace(/\(/g, '%28')
            .replace(/\)/g, '%29')
            .replace(/\*/g, '%2A')
            .replace(/~/g, '%7E');
    }

    function encodeFullURL(text) {
        try {
            var url = new URL(text);
            var result = url.origin + url.pathname;
            if (url.search) {
                var params = new URLSearchParams(url.search);
                var parts = [];
                params.forEach(function (val, key) {
                    parts.push(encodeComponent(key) + '=' + encodeComponent(val));
                });
                result += '?' + parts.join('&');
            }
            if (url.hash) result += '#' + encodeComponent(url.hash.substring(1));
            return result;
        } catch (e) {
            return encodeComponent(text);
        }
    }

    convertBtn.addEventListener('click', function () {
        var text = inp.value;
        if (!text) {
            TCTP.toast('Enter some text first.', '\u26A0\uFE0F');
            return;
        }
        var result = '';
        switch (direction) {
            case 'encode': result = encodeComponent(text); break;
            case 'decode':
                try { result = decodeURIComponent(text.replace(/\+/g, ' ')); }
                catch (e) { result = unescape(text.replace(/\+/g, ' ')); }
                break;
            case 'encode-comp': result = encodeComponent(text); break;
            case 'encode-full': result = encodeFullURL(text); break;
        }
        out.value = result;
        var statusEl = document.getElementById('tc-url-status');
        if (statusEl) {
            statusEl.textContent = 'Converted ' + text.length + ' characters';
            statusEl.className = 'tc-status tc-status--success';
        }
        TCTP.toast('Conversion complete!');
    });

    document.getElementById('tc-url-copy').addEventListener('click', function () {
        TCTP.copyText(out.value, 'URL text');
    });

})();
