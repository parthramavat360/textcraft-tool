/**
 * Em Dash Remover — Tool JS
 *
 * Removes em dashes (—) and en dashes (–) from text, reports removal counts.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var inp = document.getElementById('tc-edr-input');
        var btnRemove = document.getElementById('tc-edr-remove');
        if (!inp || !btnRemove || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var out = document.getElementById('tc-edr-output');
        var statusEl = document.getElementById('tc-edr-status');
        var barId = 'tc-edr-bar';

        function setStat(ids, val) {
            for (var i = 0; i < ids.length; i++) {
                var el = document.getElementById(ids[i]);
                if (el) { el.textContent = val; return; }
            }
        }

        btnRemove.addEventListener('click', function () {
            var text = inp.value;
            if (!text.trim()) {
                TCTP.toast('Paste some text first.', '\u26A0\uFE0F');
                return;
            }

            TCTP.showProgress(barId);
            TCTP.setProgress(barId, 50, 'Processing...');

            var emCount = (text.match(/\u2014/g) || []).length;
            var enCount = (text.match(/\u2013/g) || []).length;
            text = text.split('\u2014').join(' ').split('\u2013').join(' ');

            var totalReplaced = emCount + enCount;

            if (out) out.value = text;
            TCTP.updateResultPanel(inp.value.length.toLocaleString() + ' chars', text.length.toLocaleString() + ' chars', (text.length < inp.value.length ? ((1 - text.length / inp.value.length) * 100).toFixed(1) + '%' : '0%'), 'Done');
            setStat(['tc-edr-stats-em_count', 'tc-edr-stats-em-count', 'tc-edr-stat-em'], emCount.toLocaleString());
            setStat(['tc-edr-stats-en_count', 'tc-edr-stats-en-count', 'tc-edr-stat-en'], enCount.toLocaleString());
            setStat(['tc-edr-stats-total', 'tc-edr-stat-total', 'tc-edr-total'], totalReplaced.toLocaleString());

            if (statusEl) {
                statusEl.textContent = totalReplaced > 0
                    ? 'Removed ' + totalReplaced + ' dash' + (totalReplaced === 1 ? '' : 'es') + '.'
                    : 'No em or en dashes found.';
            }

            TCTP.setProgress(barId, 100, 'Done!');
            TCTP.hideProgress(barId);
            TCTP.toast(totalReplaced > 0
                ? totalReplaced + ' dash' + (totalReplaced === 1 ? '' : 'es') + ' removed!'
                : 'No dashes found.', totalReplaced > 0 ? '\u2705' : '\u26A0\uFE0F');
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
