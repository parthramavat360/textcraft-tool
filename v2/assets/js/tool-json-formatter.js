/**
 * JSON Formatter & Validator — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var inp = document.getElementById('tc-jf-input');
    var out = document.getElementById('tc-jf-output');
    var applyBtn = document.getElementById('tc-jf-apply');
    if (!inp || !out || !applyBtn) return;

    var currentAction = 'format';

    document.querySelectorAll('.tctp-modes[data-group="jf-action"] .tctp-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            currentAction = btn.getAttribute('data-val');
        });
    });

    function getIndent() {
        var sel = document.getElementById('tc-jf-indent');
        if (!sel) return 2;
        var val = sel.value;
        if (val === 'tab') return '\t';
        return parseInt(val) || 2;
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
        el.className = 'tc-jf-status';
        if (type === 'valid') {
            el.classList.add('tc-jf-status--valid');
        } else if (type === 'invalid') {
            el.classList.add('tc-jf-status--invalid');
        }
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
            setStatus('invalid', 'Invalid JSON: ' + e.message);
            out.value = '';
            TCTP.toast('Invalid JSON!', '\u274C');
            return;
        }

        var sortCb = document.getElementById('tc-jf-sort-keys');
        if (sortCb && sortCb.checked) {
            parsed = sortObjectKeys(parsed);
        }

        switch (currentAction) {
            case 'format':
                out.value = JSON.stringify(parsed, null, getIndent());
                setStatus('valid', 'Valid JSON - Formatted');
                TCTP.toast('JSON formatted!');
                break;

            case 'minify':
                out.value = JSON.stringify(parsed);
                setStatus('valid', 'Valid JSON - Minified');
                TCTP.toast('JSON minified!');
                break;

            case 'validate':
                out.value = JSON.stringify(parsed, null, 2);
                setStatus('valid', 'Valid JSON (' + raw.length + ' chars)');
                TCTP.toast('JSON is valid!');
                break;
        }
    });

    document.getElementById('tc-jf-copy').addEventListener('click', function () {
        TCTP.copyText(out.value, 'JSON');
    });

})();
