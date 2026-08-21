/**
 * JSON Formatter & Validator — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var inp = document.getElementById('tc-jf-input');
        var out = document.getElementById('tc-jf-output');
        var applyBtn = document.getElementById('tc-jf-apply');
        if (!inp || !out || !applyBtn || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var currentAction = 'format';

        // Action buttons (PHP renders .tc-modes[data-group="jf-action"] .tc-btn)
        var group = document.querySelector('.tc-modes[data-group="jf-action"]');
        if (group && !group.dataset.tcModes) {
            group.dataset.tcModes = '1';
            var actionBtns = group.querySelectorAll('.tc-btn');
            actionBtns.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    actionBtns.forEach(function (b) { b.classList.remove('sel'); });
                    btn.classList.add('sel');
                    currentAction = btn.getAttribute('data-val') || 'format';
                });
            });
            var selected = group.querySelector('.tc-btn.sel');
            if (selected) currentAction = selected.getAttribute('data-val') || 'format';
        }

        function getIndent() {
            var sel = document.getElementById('tc-jf-indent');
            if (!sel) return 2;
            var val = sel.value;
            if (val === 'tab') return '\t';
            return parseInt(val, 10) || 2;
        }

        function sortObjectKeys(obj) {
            if (Array.isArray(obj)) {
                return obj.map(sortObjectKeys);
            }
            if (obj !== null && typeof obj === 'object' && !(obj instanceof Date)) {
                var sorted = {};
                Object.keys(obj).sort().forEach(function (key) {
                    sorted[key] = sortObjectKeys(obj[key]);
                });
                return sorted;
            }
            return obj;
        }

        function setStatus(type, message) {
            var el = document.getElementById('tc-jf-status');
            if (!el) return;
            el.textContent = message;
            el.className = 'tc-status' + (type ? ' tc-status--' + type : '');
        }

        function setOutput(value) {
            out.value = value;
            // Mirror into the result-column preview (#tc-jf-result-text)
            var mirror = document.getElementById('tc-jf-result-text');
            if (mirror) mirror.value = value;
        }

        applyBtn.addEventListener('click', function () {
            var raw = inp.value.trim();
            if (!raw) {
                TCTP.toast('Please paste some JSON first.', '\u26A0\uFE0F');
                return;
            }

            var parsed;
            try {
                parsed = JSON.parse(raw);
            } catch (e) {
                setStatus('error', 'Invalid JSON: ' + e.message);
                setOutput('');
                TCTP.toast('Invalid JSON!', '\u274C');
                return;
            }

            var sortCb = document.getElementById('tc-jf-sort-keys');
            if (sortCb && sortCb.checked) {
                parsed = sortObjectKeys(parsed);
            }

            switch (currentAction) {
                case 'minify':
                    setOutput(JSON.stringify(parsed));
                    setStatus('success', 'Valid JSON - Minified');
                    TCTP.toast('JSON minified!');
                    break;

                case 'validate':
                    setOutput(JSON.stringify(parsed, null, 2));
                    setStatus('success', 'Valid JSON (' + raw.length + ' chars)');
                    TCTP.toast('JSON is valid!');
                    break;

                case 'format':
                default:
                    setOutput(JSON.stringify(parsed, null, getIndent()));
                    setStatus('success', 'Valid JSON - Formatted');
                    TCTP.toast('JSON formatted!');
                    break;
            }

            TCTP.updateResultPanel(raw.length.toLocaleString() + ' chars', out.value.length.toLocaleString() + ' chars', (out.value.length < raw.length ? ((1 - out.value.length / raw.length) * 100).toFixed(1) + '%' : '0%'), 'Done');
        });

        var copyBtn = document.getElementById('tc-jf-copy');
        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                TCTP.copyText(out.value, 'JSON');
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
