/**
 * JSON Formatter & Validator — Tool JS
 *
 * Formats, minifies, and validates JSON with preview tabs and live stats.
 *
 * Widget IDs (widget-json-formatter.php):
 *  - tc-jf-input, tc-jf-output
 *  - tc-jf-sort-keys
 *  - tc-jf-indent
 *  - tc-jf-apply, tc-jf-copy
 *  - tc-jf-result-text
 *  - tc-jf-preview-orig
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var inp = document.getElementById('tc-jf-input');
        var applyBtn = document.getElementById('tc-jf-apply');
        if (!inp || !applyBtn || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        // Clear any browser autofill / stale content that is not valid JSON
        if (inp.value && inp.value.trim().charAt(0) !== '{' && inp.value.trim().charAt(0) !== '[') {
            inp.value = '';
        }

        var resultText = document.getElementById('tc-jf-result-text');
        var previewOrig = document.getElementById('tc-jf-preview-orig');
        var sortCb = document.getElementById('tc-jf-sort-keys');
        var indentSel = document.getElementById('tc-jf-indent');
        var copyBtn = document.getElementById('tc-jf-copy');
        var inputSizeEl = document.getElementById('tc-jf-input-size');
        var outputSizeEl = document.getElementById('tc-jf-output-size');
        var sizeChangeEl = document.getElementById('tc-jf-size-change');
        var statusValEl = document.getElementById('tc-jf-status-val');
        var modeCards = document.querySelectorAll('.tc-jf-modes .tc-rsz-mode-card');

        var currentMode = 'format';
        var lastOutput = '';

        // Mode card click
        modeCards.forEach(function (card) {
            card.addEventListener('click', function () {
                modeCards.forEach(function (c) { c.classList.remove('sel'); });
                card.classList.add('sel');
                currentMode = card.getAttribute('data-val') || 'format';
            });
        });

        function updateInputStats() {
            var chars = inp.value.length;
            var lines = inp.value ? inp.value.split('\n').length : 0;
            if (inputSizeEl) inputSizeEl.textContent = chars.toLocaleString() + ' chars';
        }

        function setStatus(msg, type) {
            if (statusValEl) {
                statusValEl.textContent = msg;
                statusValEl.className = 'tc-stat-value' + (type ? ' tc-status--' + type : '');
            }
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

        function getIndent() {
            if (!indentSel) return 2;
            var val = indentSel.value;
            if (val === 'tab') return '\t';
            return parseInt(val, 10) || 2;
        }

        function process() {
            var raw = inp.value.trim();
            var rawLen = raw.length;

            updateInputStats();

            if (previewOrig) previewOrig.value = inp.value;

            if (!raw) {
                if (resultText) resultText.value = '';
                if (outputSizeEl) outputSizeEl.textContent = '0 chars';
                if (sizeChangeEl) sizeChangeEl.innerHTML = '&mdash;';
                setStatus('Idle', '');
                TCTP.updateResultPanel('0 chars', '0 chars', '—', 'Idle');
                return;
            }

            var firstChar = raw.charAt(0);
            if (firstChar !== '{' && firstChar !== '[') {
                inp.value = '';
                if (resultText) resultText.value = '';
                if (outputSizeEl) outputSizeEl.textContent = '0 chars';
                if (sizeChangeEl) sizeChangeEl.innerHTML = '&mdash;';
                setStatus('Idle', '');
                TCTP.toast('Textarea was auto-filled with non-JSON text. Cleared. Please paste your JSON.', '\u26A0\uFE0F');
                return;
            }

            var parsed;
            try {
                parsed = JSON.parse(raw);
            } catch (e) {
                if (resultText) resultText.value = '';
                if (outputSizeEl) outputSizeEl.textContent = 'Error';
                if (sizeChangeEl) sizeChangeEl.innerHTML = '&mdash;';
                setStatus('Invalid JSON', 'error');
                TCTP.updateResultPanel(rawLen.toLocaleString() + ' chars', 'Error', '—', 'Invalid');
                TCTP.toast('Invalid JSON: ' + e.message, '\u274C');
                return;
            }

            if (sortCb && sortCb.checked) {
                parsed = sortObjectKeys(parsed);
            }

            var output = '';
            switch (currentMode) {
                case 'minify':
                    output = JSON.stringify(parsed);
                    setStatus('Minified', 'success');
                    break;
                case 'validate':
                    output = JSON.stringify(parsed, null, 2);
                    setStatus('Valid JSON', 'success');
                    break;
                case 'format':
                default:
                    output = JSON.stringify(parsed, null, getIndent());
                    setStatus('Formatted', 'success');
                    break;
            }

            lastOutput = output;
            if (resultText) resultText.value = output;

            var outLen = output.length;
            if (outputSizeEl) outputSizeEl.textContent = outLen.toLocaleString() + ' chars';

            var change = '';
            if (currentMode === 'minify' && rawLen > 0) {
                var pct = ((1 - outLen / rawLen) * 100).toFixed(1);
                change = '-' + pct + '%';
            } else if (currentMode === 'format' && rawLen > 0) {
                var diff = outLen - rawLen;
                change = (diff > 0 ? '+' : '') + diff.toLocaleString() + ' chars';
            } else {
                change = '0%';
            }
            if (sizeChangeEl) sizeChangeEl.textContent = change;

            TCTP.updateResultPanel(
                rawLen.toLocaleString() + ' chars',
                outLen.toLocaleString() + ' chars',
                change,
                currentMode === 'minify' ? 'Minified' : currentMode === 'validate' ? 'Valid' : 'Formatted'
            );
            TCTP.switchToResultTab();
            TCTP.toast('JSON ' + currentMode + 'd successfully!');
        }

        // Apply button
        applyBtn.addEventListener('click', function () {
            process();
        });

        // On input: clear result if input emptied, sync original preview
        inp.addEventListener('input', function () {
            updateInputStats();
            if (previewOrig) previewOrig.value = inp.value;
            if (!inp.value.trim()) {
                if (resultText) resultText.value = '';
                lastOutput = '';
                if (outputSizeEl) outputSizeEl.textContent = '0 chars';
                if (sizeChangeEl) sizeChangeEl.innerHTML = '&mdash;';
                setStatus('Idle', '');
            }
        });

        // Indent change triggers reprocess
        if (indentSel) {
            indentSel.addEventListener('change', function () {
                if (lastOutput) process();
            });
        }

        // Sort keys change triggers reprocess
        if (sortCb) {
            sortCb.addEventListener('change', function () {
                if (lastOutput) process();
            });
        }

        // Copy
        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                TCTP.copyText(lastOutput, 'JSON');
            });
        }

        updateInputStats();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Late autofill guard: some browsers autofill AFTER DOMContentLoaded
    window.addEventListener('load', function () {
        var el = document.getElementById('tc-jf-input');
        if (el && el.value && el.value.trim().charAt(0) !== '{' && el.value.trim().charAt(0) !== '[') {
            el.value = '';
        }
    });

    new MutationObserver(function () { init(); })
        .observe(document.documentElement, { childList: true, subtree: true });
})();
