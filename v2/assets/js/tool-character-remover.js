/**
 * Character Remover — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var inp = document.getElementById('tc-cr-input');
        var out = document.getElementById('tc-cr-output');
        var btnRemove = document.getElementById('tc-cr-remove');
        if (!inp || !btnRemove || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var chkCase = document.getElementById('tc-cr-case');
        var statusEl = document.getElementById('tc-cr-status');
        var customChars = '';

        function setStat(ids, val) {
            for (var i = 0; i < ids.length; i++) {
                var el = document.getElementById(ids[i]);
                if (el) { el.textContent = val; return; }
            }
        }

        function doRemove() {
            var text = inp.value;
            if (!text.trim()) {
                TCTP.toast('Please enter some text first.', '\u26A0\uFE0F');
                return;
            }
            if (!customChars) {
                TCTP.toast('Pick a quick preset of characters to remove.', '\u26A0\uFE0F');
                return;
            }

            TCTP.showProgress('tc-cr-bar');
            TCTP.setProgress('tc-cr-bar', 50, 'Removing...');

            var flags = chkCase && chkCase.checked ? 'g' : 'gi';
            var origLen = text.length;
            var pattern = '[' + customChars.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&') + ']';
            var result = text.replace(new RegExp(pattern, flags), '');
            var removed = origLen - result.length;

            if (out) out.value = result;
            setStat(['tc-cr-stats-removed', 'tc-cr-stat-removed'], removed.toLocaleString());
            setStat(['tc-cr-stats-original', 'tc-cr-stat-original', 'tc-cr-stat-orig'], origLen.toLocaleString());
            setStat(['tc-cr-stats-result', 'tc-cr-stat-result'], result.length.toLocaleString());
            if (statusEl) statusEl.textContent = removed + ' character' + (removed === 1 ? '' : 's') + ' removed.';

            TCTP.setProgress('tc-cr-bar', 100, 'Done!');
            TCTP.hideProgress('tc-cr-bar');
            TCTP.toast(removed > 0
                ? removed + ' character' + (removed === 1 ? '' : 's') + ' removed!'
                : 'No matching characters found.', removed > 0 ? '\u2705' : '\u26A0\uFE0F');
        }

        btnRemove.addEventListener('click', doRemove);

        document.querySelectorAll('#tc-cr-presets [data-chars]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                customChars = btn.getAttribute('data-chars') || '';
                doRemove();
            });
        });

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
