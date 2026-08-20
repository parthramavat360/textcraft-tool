/**
 * Base64 Encode/Decode — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var inp = document.getElementById('tc-b64-input');
    var out = document.getElementById('tc-b64-output');
    var convertBtn = document.getElementById('tc-b64-convert');
    if (!inp || !out || !convertBtn) return;

    var direction = 'encode';

    document.querySelectorAll('.tc-modes[data-group="b64-direction"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            direction = btn.getAttribute('data-val');
        });
    });

    convertBtn.addEventListener('click', function () {
        var text = inp.value;
        if (!text) {
            TCTP.toast('Enter some text first.', '\u26A0\uFE0F');
            return;
        }
        try {
            var result;
            if (direction === 'encode') {
                result = btoa(unescape(encodeURIComponent(text)));
            } else {
                result = decodeURIComponent(escape(atob(text.replace(/\s/g, ''))));
            }
            out.value = result;
            var statusEl = document.getElementById('tc-b64-status');
            if (statusEl) {
                statusEl.textContent = 'Converted ' + text.length + ' characters';
                statusEl.className = 'tc-status tc-status--success';
            }
            TCTP.toast('Conversion complete!');
        } catch (e) {
            out.value = '';
            var statusEl = document.getElementById('tc-b64-status');
            if (statusEl) {
                statusEl.textContent = 'Error: ' + e.message;
                statusEl.className = 'tc-status tc-status--error';
            }
            TCTP.toast('Invalid input!', '\u274C');
        }
    });

    document.getElementById('tc-b64-copy').addEventListener('click', function () {
        TCTP.copyText(out.value, 'Base64 text');
    });

})();
