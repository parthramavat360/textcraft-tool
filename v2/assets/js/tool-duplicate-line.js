/**
 * Duplicate Line Remover — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var inp = document.getElementById('tc-dl-input');
        var out = document.getElementById('tc-dl-output');
        var btnRemove = document.getElementById('tc-dl-remove');
        if (!inp || !btnRemove || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var statusEl = document.getElementById('tc-dl-status');

        function setStat(ids, val) {
            for (var i = 0; i < ids.length; i++) {
                var el = document.getElementById(ids[i]);
                if (el) { el.textContent = val; return; }
            }
        }

        btnRemove.addEventListener('click', function () {
            var text = inp.value;
            if (!text.trim()) {
                TCTP.toast('Paste some text with duplicate lines first.', '\u26A0\uFE0F');
                return;
            }

            TCTP.showProgress('tc-dl-bar');
            TCTP.setProgress('tc-dl-bar', 50, 'Processing...');

            var lines = text.split(/\r?\n/);
            var doTrim = true;
            var caseInsensitive = false;
            var removeBlanks = false;
            var doSort = false;

            if (doTrim) lines = lines.map(function (l) { return l.trim(); });
            if (removeBlanks) lines = lines.filter(function (l) { return l !== ''; });

            var total = lines.length;
            var seen = {};
            var unique = [];
            lines.forEach(function (line) {
                var key = caseInsensitive ? line.toLowerCase() : line;
                if (!seen[key]) {
                    seen[key] = 1;
                    unique.push(line);
                }
            });

            if (doSort) unique.sort(function (a, b) { return a.localeCompare(b); });

            var removed = total - unique.length;
            if (out) out.value = unique.join('\n');
            setStat(['tc-dl-stats-total', 'tc-dl-stat-total'], total.toLocaleString());
            setStat(['tc-dl-stats-unique', 'tc-dl-stat-unique'], unique.length.toLocaleString());
            setStat(['tc-dl-stats-removed', 'tc-dl-stat-removed'], removed.toLocaleString());
            if (statusEl) statusEl.textContent = removed + ' duplicate line' + (removed === 1 ? '' : 's') + ' removed.';

            TCTP.setProgress('tc-dl-bar', 100, 'Done!');
            TCTP.hideProgress('tc-dl-bar');
            TCTP.toast(removed > 0
                ? removed + ' duplicate line' + (removed === 1 ? '' : 's') + ' removed!'
                : 'No duplicates found.', removed > 0 ? '\u2705' : '\u26A0\uFE0F');
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
