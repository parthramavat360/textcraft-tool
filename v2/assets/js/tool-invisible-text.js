/**
 * Invisible Text Generator — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var grid = document.getElementById('tc-invisible-grid');
        var btnGenerate = document.getElementById('tc-it-generate');
        var anchor = grid || btnGenerate;
        if (!anchor || anchor.dataset.tcInit) return;
        anchor.dataset.tcInit = '1';

        var countInput = document.getElementById('tc-it-count');
        var resultArea = document.getElementById('tc-it-result-area');
        var generated = document.getElementById('tc-it-generated');
        var btnCopyAll = document.getElementById('tc-it-copy');
        var statusEl = document.getElementById('tc-it-status');

        function setStatus(msg) {
            if (statusEl) statusEl.textContent = msg;
        }

        if (grid) {
            grid.querySelectorAll('.tc-invisible-item').forEach(function (item) {
                var btn = item.querySelector('button[data-copy]');
                if (!btn) return;
                btn.addEventListener('click', function () {
                    var name = item.getAttribute('data-name') || 'Character';
                    TCTP.copyText(item.getAttribute('data-char') || btn.getAttribute('data-copy'), name);
                    setStatus('Copied ' + name + '.');
                });
            });
        }

        if (btnGenerate) {
            btnGenerate.addEventListener('click', function () {
                var count = parseInt(countInput && countInput.value, 10);
                if (isNaN(count) || count < 1) count = 1;
                if (count > 1000) count = 1000;

                var result = '';
                for (var i = 0; i < count; i++) result += '\u200B';

                if (generated) generated.value = result;
                if (resultArea) resultArea.style.display = '';
                TCTP.updateResultPanel(count.toLocaleString() + ' chars', result.length.toLocaleString() + ' chars', '0%', 'Done');
                setStatus('Generated ' + count + ' invisible character' + (count === 1 ? '' : 's') + '.');
                TCTP.toast('Generated ' + count + ' invisible character' + (count === 1 ? '' : 's') + '!');
            });
        }

        if (btnCopyAll) {
            btnCopyAll.addEventListener('click', function () {
                TCTP.copyText(generated ? generated.value : '', 'Invisible text');
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
