/**
 * Hash Generator — Tool JS
 * Uses SubtleCrypto API for SHA-*, fallback MD5 implementation.
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var inp = document.getElementById('tc-hash-input');
    var generateBtn = document.getElementById('tc-hash-generate');
    if (!inp || !generateBtn) return;

    /* Simple MD5 implementation */
    function md5(string) {
        function md5cycle(x, k) {
            var a = x[0], b = x[1], c = x[2], d = x[3];
            a = ff(a, b, c, d, k[0], 7, -680876936); d = ff(d, a, b, c, k[1], 12, -389564586);
            c = ff(c, d, a, b, k[2], 17, 606105819); b = ff(b, c, d, a, k[3], 22, -1044525330);
            a = ff(a, b, c, d, k[4], 7, -176418897); d = ff(d, a, b, c, k[5], 12, 1200080426);
            c = ff(c, d, a, b, k[6], 17, -1473231341); b = ff(b, c, d, a, k[7], 22, -45705983);
            a = ff(a, b, c, d, k[8], 7, 1770035416); d = ff(d, a, b, c, k[9], 12, -1958414417);
            c = ff(c, d, a, b, k[10], 17, -42063); b = ff(b, c, d, a, k[11], 22, -1990404162);
            a = ff(a, b, c, d, k[12], 7, 1804603682); d = ff(d, a, b, c, k[13], 12, -40341101);
            c = ff(c, d, a, b, k[14], 17, -1502002290); b = ff(b, c, d, a, k[15], 22, 1236535329);
            a = gg(a, b, c, d, k[1], 5, -165796510); d = gg(d, a, b, c, k[6], 9, -1069501632);
            c = gg(c, d, a, b, k[11], 14, 643717713); b = gg(b, c, d, a, k[0], 20, -373897302);
            a = gg(a, b, c, d, k[5], 5, -701558691); d = gg(d, a, b, c, k[10], 9, 38016083);
            c = gg(c, d, a, b, k[15], 14, -660478335); b = gg(b, c, d, a, k[4], 20, -405537848);
            a = gg(a, b, c, d, k[9], 5, 568446438); d = gg(d, a, b, c, k[14], 9, -1019803690);
            c = gg(c, d, a, b, k[3], 14, -187363961); b = gg(b, c, d, a, k[8], 20, 1163531501);
            a = gg(a, b, c, d, k[13], 5, -1444681467); d = gg(d, a, b, c, k[2], 9, -51403784);
            c = gg(c, d, a, b, k[7], 14, 1735328473); b = gg(b, c, d, a, k[12], 20, -1926607734);
            a = hh(a, b, c, d, k[5], 4, -378558); d = hh(d, a, b, c, k[8], 11, -2022574463);
            c = hh(c, d, a, b, k[11], 16, 1839030562); b = hh(b, c, d, a, k[14], 23, -35309556);
            a = hh(a, b, c, d, k[1], 4, -1530992060); d = hh(d, a, b, c, k[4], 11, 1272893353);
            c = hh(c, d, a, b, k[7], 16, -155497632); b = hh(b, c, d, a, k[10], 23, -1094730640);
            a = hh(a, b, c, d, k[13], 4, 681279174); d = hh(d, a, b, c, k[0], 11, -358537222);
            c = hh(c, d, a, b, k[3], 16, -722521979); b = hh(b, c, d, a, k[6], 23, 76029189);
            a = hh(a, b, c, d, k[9], 4, -640364487); d = hh(d, a, b, c, k[12], 11, -421815835);
            c = hh(c, d, a, b, k[15], 16, 530742520); b = hh(b, c, d, a, k[2], 23, -995338651);
            a = ii(a, b, c, d, k[0], 6, -198630844); d = ii(d, a, b, c, k[7], 10, 1126891415);
            c = ii(c, d, a, b, k[14], 15, -1416354905); b = ii(b, c, d, a, k[5], 21, -57434055);
            a = ii(a, b, c, d, k[12], 6, 1700485571); d = ii(d, a, b, c, k[3], 10, -1894986606);
            c = ii(c, d, a, b, k[10], 15, -1051523); b = ii(b, c, d, a, k[1], 21, -2054922799);
            a = ii(a, b, c, d, k[8], 6, 1873313359); d = ii(d, a, b, c, k[15], 10, -30611744);
            c = ii(c, d, a, b, k[6], 15, -1560198380); b = ii(b, c, d, a, k[13], 21, 1309151649);
            a = ii(a, b, c, d, k[4], 6, -145523070); d = ii(d, a, b, c, k[11], 10, -1120210379);
            c = ii(c, d, a, b, k[2], 15, 718787259); b = ii(b, c, d, a, k[9], 21, -343485551);
            x[0] = add32(a, x[0]); x[1] = add32(b, x[1]);
            x[2] = add32(c, x[2]); x[3] = add32(d, x[3]);
        }
        function cmn(q, a, b, x, s, t) { a = add32(add32(a, q), add32(x, t)); return add32((a << s) | (a >>> (32 - s)), b); }
        function ff(a, b, c, d, x, s, t) { return cmn((b & c) | ((~b) & d), a, b, x, s, t); }
        function gg(a, b, c, d, x, s, t) { return cmn((b & d) | (c & (~d)), a, b, x, s, t); }
        function hh(a, b, c, d, x, s, t) { return cmn(b ^ c ^ d, a, b, x, s, t); }
        function ii(a, b, c, d, x, s, t) { return cmn(c ^ (b | (~d)), a, b, x, s, t); }
        function md5blk(s) {
            var md5blks = [], i;
            for (i = 0; i < 64; i += 4) {
                md5blks[i >> 2] = s.charCodeAt(i) + (s.charCodeAt(i + 1) << 8) + (s.charCodeAt(i + 2) << 16) + (s.charCodeAt(i + 3) << 24);
            }
            return md5blks;
        }
        function add32(a, b) { return (a + b) & 0xFFFFFFFF; }
        function rhex(n) {
            var s = '', j = 0;
            for (; j < 4; j++) s += '0123456789abcdef'.charAt((n >> (j * 8 + 4)) & 0x0F) + '0123456789abcdef'.charAt((n >> (j * 8)) & 0x0F);
            return s;
        }
        var n = string.length, state = [1732584193, -271733879, -1732584194, 271733878], i;
        var tail = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        for (i = 64; i <= n; i += 64) { md5cycle(state, md5blk(string.substring(i - 64, i))); }
        string = string.substring(i - 64);
        for (i = 0; i < string.length; i++) { tail[i >> 2] |= string.charCodeAt(i) << ((i % 4) << 3); }
        tail[i >> 2] |= 0x80 << ((i % 4) << 3);
        if (i > 55) { md5cycle(state, tail); tail = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]; }
        tail[14] = n * 8;
        md5cycle(state, tail);
        return rhex(state[0]) + rhex(state[1]) + rhex(state[2]) + rhex(state[3]);
    }

    async function sha(algo, text) {
        var enc = new TextEncoder();
        var data = enc.encode(text);
        var hash = await crypto.subtle.digest(algo, data);
        return Array.from(new Uint8Array(hash)).map(function (b) { return b.toString(16).padStart(2, '0'); }).join('');
    }

    function getAlgorithms() {
        var algos = [];
        if (document.getElementById('tc-hash-md5') && document.getElementById('tc-hash-md5').checked) algos.push('MD5');
        if (document.getElementById('tc-hash-sha1') && document.getElementById('tc-hash-sha1').checked) algos.push('SHA-1');
        if (document.getElementById('tc-hash-sha256') && document.getElementById('tc-hash-sha256').checked) algos.push('SHA-256');
        if (document.getElementById('tc-hash-sha512') && document.getElementById('tc-hash-sha512').checked) algos.push('SHA-512');
        return algos;
    }

    generateBtn.addEventListener('click', async function () {
        var text = inp.value;
        if (!text) {
            TCTP.toast('Enter some text first.', '\u26A0\uFE0F');
            return;
        }
        var algos = getAlgorithms();
        if (!algos.length) {
            TCTP.toast('Select at least one algorithm.', '\u26A0\uFE0F');
            return;
        }
        var uppercase = document.getElementById('tc-hash-uppercase') && document.getElementById('tc-hash-uppercase').checked;
        var resultsEl = document.getElementById('tc-hash-results');
        var html = '';

        for (var i = 0; i < algos.length; i++) {
            var algo = algos[i];
            var hash;
            try {
                if (algo === 'MD5') {
                    hash = md5(text);
                } else {
                    var algMap = { 'SHA-1': 'SHA-1', 'SHA-256': 'SHA-256', 'SHA-512': 'SHA-512' };
                    hash = await sha(algMap[algo], text);
                }
                if (uppercase) hash = hash.toUpperCase();
                html += '<div class="tc-hash-row"><label class="tc-label">' + algo + ' (' + hash.length * 4 + ' bits)</label><div class="tc-hash-val"><code id="tc-hash-' + algo.replace(/[^a-z0-9]/gi, '') + '">' + hash + '</code><button class="tc-btn tc-btn--ghost tc-btn--sm" onclick="TCTP.copyText(\'' + hash + '\', \'' + algo + '\')">Copy</button></div></div>';
            } catch (e) {
                html += '<div class="tc-hash-row"><label class="tc-label">' + algo + '</label><div class="tc-hash-val tc-hash-error">Error: ' + e.message + '</div></div>';
            }
        }
        if (resultsEl) resultsEl.innerHTML = html;
        TCTP.updateResultPanel(text.length.toLocaleString() + ' chars', algos.length + ' hash(es)', 'N/A', 'Done');
        var statusEl = document.getElementById('tc-hash-status');
        if (statusEl) {
            statusEl.textContent = 'Generated ' + algos.length + ' hash(es) from ' + text.length + ' characters';
            statusEl.className = 'tc-status tc-status--success';
        }
        TCTP.toast('Hashes generated!');
    });

    var copyAllBtn = document.getElementById('tc-hash-copy-all');
    if (copyAllBtn) {
        copyAllBtn.addEventListener('click', function () {
            var resultsEl = document.getElementById('tc-hash-results');
            TCTP.copyText(resultsEl ? resultsEl.innerText : '', 'All hashes');
        });
    }

})();
