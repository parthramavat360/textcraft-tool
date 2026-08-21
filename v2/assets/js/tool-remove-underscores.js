/**
 * Remove Underscores — Tool JS
 *
 * Replaces underscores with spaces or strips them entirely.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var inp = document.getElementById('tc-ru-input');
        var convertBtn = document.getElementById('tc-ru-convert');
        if (!inp || !convertBtn || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var out = document.getElementById('tc-ru-output');
        var copyBtn = document.getElementById('tc-ru-copy');
        var modeWrap = document.querySelector('.tc-modes[data-group="ru-mode"]');
        var currentMode = 'space';

        // ── Mode buttons (.tc-modes / .tc-btn per widget markup) ──

        function selectMode(btn) {
            if (modeWrap) {
                modeWrap.querySelectorAll('.tc-btn').forEach(function (b) {
                    b.classList.remove('sel');
                });
            }
            btn.classList.add('sel');
        }

        if (modeWrap) {
            modeWrap.querySelectorAll('.tc-btn[data-val]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    selectMode(btn);
                    currentMode = btn.getAttribute('data-val') || 'space';
                });
            });
        }

        // ── Convert button ───────────────────────────────────────

        convertBtn.addEventListener('click', function () {
            var text = inp.value;
            if (!text.trim()) {
                TCTP.toast('Please enter some text.', '\u26A0\uFE0F');
                return;
            }
            var result = currentMode === 'remove' ? text.replace(/_/g, '') : text.replace(/_+/g, ' ');
            if (out) out.value = result;
            TCTP.updateResultPanel(inp.value.length.toLocaleString() + ' chars', result.length.toLocaleString() + ' chars', (result.length < inp.value.length ? ((1 - result.length / inp.value.length) * 100).toFixed(1) + '%' : '0%'), 'Done');
            TCTP.toast('Underscores processed.');
        });

        // ── Copy ─────────────────────────────────────────────────

        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                TCTP.copyText(out ? out.value : '', 'Result');
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
