/**
 * Random IP Address Generator — Tool JS
 *
 * IPv4/IPv6 mode buttons, count input, checkboxes (no private, no loopback),
 * generate, copy, download.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var ipMode = 'ipv4';
    var output = document.getElementById('tc-rip-output');
    if (!output) return;

    document.querySelectorAll('.tctp-modes[data-group="rip-mode"] .tctp-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            ipMode = btn.getAttribute('data-val') || 'ipv4';
        });
    });

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

    var generateBtn = document.getElementById('tc-rip-generate');
    if (generateBtn) generateBtn.addEventListener('click', function () {
        var countInput = document.getElementById('tc-rip-count');
        var count = countInput ? Math.max(1, Math.min(1000, parseInt(countInput.value) || 10)) : 10;

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

        output.value = results.join('\n');

        var countEl = document.getElementById('tc-rip-stat-count');
        if (countEl) countEl.textContent = count;

        var modeEl = document.getElementById('tc-rip-stat-mode');
        if (modeEl) modeEl.textContent = ipMode.toUpperCase();

        var statsEl = document.getElementById('tc-rip-stats');
        if (statsEl) statsEl.style.display = '';

        TCTP.toast(count + ' ' + ipMode.toUpperCase() + ' addresses generated!');
    });

    var copyBtn = document.getElementById('tc-rip-copy');
    if (copyBtn) copyBtn.addEventListener('click', function () {
        TCTP.copyText(output.value, 'IP addresses');
    });

    var downloadBtn = document.getElementById('tc-rip-download');
    if (downloadBtn) downloadBtn.addEventListener('click', function () {
        TCTP.downloadText(output.value, 'random-ips.txt');
    });

})();
