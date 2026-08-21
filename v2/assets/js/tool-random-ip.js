/**
 * Random IP Address Generator — Tool JS
 *
 * IPv4/IPv6 mode buttons, count input, checkboxes (no private, no loopback),
 * generate, copy.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var output = document.getElementById('tc-rip-output');
        var generateBtn = document.getElementById('tc-rip-generate');
        if (!output || !generateBtn || output.dataset.tcInit) return;
        output.dataset.tcInit = '1';

        var ipMode = 'ipv4';

        // Version buttons (PHP renders .tc-modes[data-group="rip-version"] .tc-btn)
        var group = document.querySelector('.tc-modes[data-group="rip-version"]');
        if (group && !group.dataset.tcModes) {
            group.dataset.tcModes = '1';
            var verBtns = group.querySelectorAll('.tc-btn');
            verBtns.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    verBtns.forEach(function (b) { b.classList.remove('sel'); });
                    btn.classList.add('sel');
                    ipMode = btn.getAttribute('data-val') || 'ipv4';
                });
            });
            var selected = group.querySelector('.tc-btn.sel');
            if (selected) ipMode = selected.getAttribute('data-val') || 'ipv4';
        }

        function randomIPv4(noPrivate, noLoopback) {
            var a, b, c, d;
            do {
                a = Math.floor(Math.random() * 256);
                b = Math.floor(Math.random() * 256);
                c = Math.floor(Math.random() * 256);
                d = Math.floor(Math.random() * 256);
            } while (
                (noLoopback && a === 127) ||
                (noPrivate && (
                    a === 10 ||
                    (a === 172 && b >= 16 && b <= 31) ||
                    (a === 192 && b === 168) ||
                    (a === 0) ||
                    (a === 100 && b >= 64 && b <= 127) ||
                    (a === 169 && b === 254)
                ))
            );
            return a + '.' + b + '.' + c + '.' + d;
        }

        function randomIPv6(noPrivate, noLoopback) {
            var groups = [];
            for (var i = 0; i < 8; i++) {
                groups.push(Math.floor(Math.random() * 0x10000).toString(16));
            }

            var ip = groups.join(':');

            if (noLoopback && ip === '0:0:0:0:0:0:0:1') {
                return randomIPv6(noPrivate, noLoopback);
            }

            if (noPrivate && (
                ip.indexOf('fc') === 0 || ip.indexOf('fd') === 0 ||
                ip.indexOf('fe80') === 0
            )) {
                return randomIPv6(noPrivate, noLoopback);
            }

            return ip;
        }

        generateBtn.addEventListener('click', function () {
            var countInput = document.getElementById('tc-rip-count');
            var count = countInput ? Math.max(1, Math.min(1000, parseInt(countInput.value, 10) || 10)) : 10;

            var noPrivateEl = document.getElementById('tc-rip-no-private');
            var noLoopbackEl = document.getElementById('tc-rip-no-loopback');
            var noPrivate = noPrivateEl ? noPrivateEl.checked : false;
            var noLoopback = noLoopbackEl ? noLoopbackEl.checked : false;

            var results = [];
            for (var i = 0; i < count; i++) {
                if (ipMode === 'ipv4') {
                    results.push(randomIPv4(noPrivate, noLoopback));
                } else {
                    results.push(randomIPv6(noPrivate, noLoopback));
                }
            }

            var joined = results.join('\n');
            output.value = joined;

            var resultText = document.getElementById('tc-rip-result-text');
            if (resultText) resultText.value = joined;

            TCTP.updateResultPanel('N/A', count + ' IP address(es)', 'N/A', 'Done');

            TCTP.toast(count + ' ' + ipMode.toUpperCase() + ' addresses generated!');
        });

        var copyBtn = document.getElementById('tc-rip-copy');
        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                TCTP.copyText(output.value, 'IP addresses');
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
