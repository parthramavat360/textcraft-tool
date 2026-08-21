/**
 * Base64 Encode/Decode — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var inp = document.getElementById('tc-b64-input');
        var out = document.getElementById('tc-b64-output');
        var convertBtn = document.getElementById('tc-b64-convert');
        if (!inp || !out || !convertBtn || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var direction = 'encode';

        // Direction buttons (PHP renders .tc-modes[data-group="b64-direction"] .tc-btn)
        var group = document.querySelector('.tc-modes[data-group="b64-direction"]');
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

        function setStatus(type, message) {
            var statusEl = document.getElementById('tc-b64-status');
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
            try {
                var result;
                if (direction === 'encode') {
                    result = btoa(unescape(encodeURIComponent(text)));
                } else {
                    result = decodeURIComponent(escape(atob(text.replace(/\s/g, ''))));
                }
                out.value = result;
                TCTP.updateResultPanel(text.length.toLocaleString() + ' chars', result.length.toLocaleString() + ' chars', (result.length < text.length ? ((1 - result.length / text.length) * 100).toFixed(1) + '%' : '0%'), 'Done');
                setStatus('success', 'Converted ' + text.length + ' characters');
                TCTP.toast('Conversion complete!');
            } catch (e) {
                out.value = '';
                setStatus('error', 'Error: ' + e.message);
                TCTP.toast('Invalid input!', '\u274C');
            }
        });

        var copyBtn = document.getElementById('tc-b64-copy');
        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                TCTP.copyText(out.value, 'Base64 text');
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
