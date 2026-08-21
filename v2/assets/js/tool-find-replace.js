/**
 * Find and Replace — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var inp = document.getElementById('tc-fr-input');
        var out = document.getElementById('tc-fr-output');
        var doBtn = document.getElementById('tc-fr-do');
        if (!inp || !out || !doBtn || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var errEl = document.getElementById('tc-fr-err');

        function showError(msg) {
            if (!errEl) return;
            errEl.textContent = msg;
            errEl.style.display = 'block';
        }

        function clearError() {
            if (!errEl) return;
            errEl.textContent = '';
            errEl.style.display = 'none';
        }

        doBtn.addEventListener('click', function () {
            clearError();

            var findInput = document.getElementById('tc-fr-find');
            var findStr = findInput ? findInput.value : '';
            if (!findStr) {
                showError('Please enter a search term or pattern to find.');
                return;
            }

            var repInput = document.getElementById('tc-fr-replace');
            var repStr = repInput ? repInput.value : '';
            var caseCb = document.getElementById('tc-fr-case');
            var wholeCb = document.getElementById('tc-fr-whole');
            var regexCb = document.getElementById('tc-fr-regex');
            var allCb = document.getElementById('tc-fr-all');
            var cs = caseCb ? caseCb.checked : false;
            var whole = wholeCb ? wholeCb.checked : false;
            var regex = regexCb ? regexCb.checked : false;
            var all = allCb ? allCb.checked : true;

            var src = regex ? findStr : findStr.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            if (whole && !regex) src = '\\b' + src + '\\b';

            var flags = (cs ? '' : 'i') + (all ? 'g' : '');

            try {
                var re = new RegExp(src, flags);
                var count = 0;
                var result = inp.value.replace(re, function () { count++; return repStr; });
                out.value = result;
                var matchesEl = document.getElementById('tc-fr-matches');
                var replacedEl = document.getElementById('tc-fr-replaced');
                if (matchesEl) matchesEl.textContent = count;
                if (replacedEl) replacedEl.textContent = count;
                TCTP.updateResultPanel(inp.value.length.toLocaleString() + ' chars', result.length.toLocaleString() + ' chars', (result.length < inp.value.length ? ((1 - result.length / inp.value.length) * 100).toFixed(1) + '%' : '0%'), 'Done');
                TCTP.toast('Found ' + count + ' match(es) and replaced.');
            } catch (e) {
                showError('Invalid regex: ' + e.message);
            }
        });

        var copyBtn = document.getElementById('tc-fr-copy');
        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                TCTP.copyText(out.value, 'Result');
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
