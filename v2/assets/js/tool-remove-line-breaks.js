/**
 * Remove Line Breaks — Premium Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var PROGRESS_ID = 'tc-rlb-progress';

    function init() {
        var inp       = document.getElementById('tc-rlb-input');
        var btnConvert = document.getElementById('tc-rlb-convert');
        var btnCopy   = document.getElementById('tc-rlb-copy');
        if (!inp || !btnConvert || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var currentMode = 'spaces';

        /* ── Mode cards ─────────────────────────────────────── */
        var group = document.querySelector('.tc-rsz-mode-cards.tc-rlb-modes');
        if (group) {
            var modeBtns = group.querySelectorAll('.tc-rsz-mode-card');
            modeBtns.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    modeBtns.forEach(function (b) { b.classList.remove('sel'); });
                    btn.classList.add('sel');
                    currentMode = btn.getAttribute('data-val') || 'spaces';
                });
            });
        }

        /* ── Helpers ────────────────────────────────────────── */
        function countLines(text) {
            if (!text) return 0;
            return text.split('\n').length;
        }

        function updateStats() {
            var text = inp.value;
            var origLen = text.length;
            var origLines = countLines(text);

            document.getElementById('tc-rlb-orig').textContent = origLen.toLocaleString();

            // Preview original tab
            var origPreview = document.getElementById('tc-rlb-preview-orig');
            if (origPreview) origPreview.value = text;

            // Update result panel stats
            var tcOrig = document.getElementById('tc-stat-orig');
            if (tcOrig) tcOrig.textContent = origLen.toLocaleString() + ' chars';
        }

        var debounceTimer;
        inp.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(updateStats, 200);
        });

        /* ── Convert ────────────────────────────────────────── */
        btnConvert.addEventListener('click', function () {
            var text = inp.value;
            if (!text.trim()) {
                TCTP.toast('Paste some text with line breaks first.', '\u26A0\uFE0F');
                return;
            }

            var trimLines   = document.getElementById('tc-rlb-trim');
            var dedupSpaces = document.getElementById('tc-rlb-dedup-spaces');
            var origLen     = text.length;
            var origLines   = countLines(text);

            TCTP.showProgress(PROGRESS_ID);

            setTimeout(function () {
                var result = text;

                switch (currentMode) {
                    case 'join':
                        result = result.replace(/\r?\n/g, '');
                        break;
                    case 'paragraphs':
                        result = result.replace(/\r?\n\r?\n+/g, '\n\n');
                        result = result.replace(/\r?\n/g, ' ');
                        result = result.replace(/  +/g, ' ');
                        break;
                    case 'spaces':
                    default:
                        result = result.replace(/\r?\n/g, ' ');
                        break;
                }

                if (trimLines && trimLines.checked) {
                    result = result.split('\n').map(function (l) { return l.trim(); }).join('\n');
                }

                if (dedupSpaces && dedupSpaces.checked) {
                    result = result.replace(/  +/g, ' ');
                }

                result = result.replace(/ +/g, ' ').trim();

                var resultLen  = result.length;
                var resultLines = countLines(result);
                var linesRemoved = origLines - resultLines;
                var savedPct = origLen > 0 ? ((1 - resultLen / origLen) * 100).toFixed(1) + '%' : '0%';

                TCTP.hideProgress(PROGRESS_ID);

                /* Update input stats */
                document.getElementById('tc-rlb-orig').textContent = origLen.toLocaleString();
                document.getElementById('tc-rlb-result-count').textContent = resultLen.toLocaleString();
                document.getElementById('tc-rlb-lines-removed').textContent = linesRemoved.toLocaleString();
                document.getElementById('tc-rlb-saved').textContent = savedPct;

                /* Update result panel header stats */
                TCTP.updateResultPanel(
                    origLen.toLocaleString() + ' chars',
                    resultLen.toLocaleString() + ' chars',
                    savedPct,
                    'Done'
                );

                /* Show result in preview */
                var origPreview   = document.getElementById('tc-rlb-preview-orig');
                var resultPreview = document.getElementById('tc-rlb-preview-result');
                if (origPreview) origPreview.value = text;
                if (resultPreview) resultPreview.value = result;

                TCTP.switchToResultTab();
                TCTP.toast('Line breaks removed!');
            }, 80);
        });

        /* ── Copy ───────────────────────────────────────────── */
        if (btnCopy) {
            btnCopy.addEventListener('click', function () {
                var resultPreview = document.getElementById('tc-rlb-preview-result');
                var val = resultPreview ? resultPreview.value : '';
                TCTP.copyText(val, 'Result');
            });
        }

        /* ── Clear all ──────────────────────────────────────── */
        var btnClear = document.getElementById('tc-rlb-clear');
        if (btnClear) {
            btnClear.addEventListener('click', function () {
                inp.value = '';
                currentMode = 'spaces';

                var modeBtns = document.querySelectorAll('.tc-rsz-mode-cards.tc-rlb-modes .tc-rsz-mode-card');
                modeBtns.forEach(function (b) { b.classList.remove('sel'); });
                if (modeBtns[0]) modeBtns[0].classList.add('sel');

                var trimCb   = document.getElementById('tc-rlb-trim');
                var dedupCb  = document.getElementById('tc-rlb-dedup-spaces');
                if (trimCb)  trimCb.checked = false;
                if (dedupCb) dedupCb.checked = false;

                var origPreview   = document.getElementById('tc-rlb-preview-orig');
                var resultPreview = document.getElementById('tc-rlb-preview-result');
                if (origPreview)   origPreview.value = '';
                if (resultPreview) resultPreview.value = '';

                document.getElementById('tc-rlb-orig').textContent = '0';
                document.getElementById('tc-rlb-result-count').textContent = '0';
                document.getElementById('tc-rlb-lines-removed').textContent = '0';
                document.getElementById('tc-rlb-saved').textContent = '0%';

                TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Idle');
                TCTP.switchToOriginalTab();
                TCTP.toast('Cleared.', '\uD83E\uDDF9');
            });
        }

        /* ── Init stats ─────────────────────────────────────── */
        updateStats();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    new MutationObserver(function () { init(); })
        .observe(document.documentElement, { childList: true, subtree: true });
})();
