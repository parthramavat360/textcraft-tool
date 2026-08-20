/**
 * Password Generator — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var CHARS = {
        upper: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        lower: 'abcdefghijklmnopqrstuvwxyz',
        numbers: '0123456789',
        symbols: '!@#$%^&*()-_=+[]{}|;:,.<>?'
    };

    var lenSlider = document.getElementById('tc-pw-len');
    var lenVal = document.getElementById('tc-pw-len-val');
    var out = document.getElementById('tc-pw-output');
    if (!lenSlider || !out) return;

    var PRESETS = {
        basic:  { len: 8,  upper: true,  lower: true,  numbers: true,  symbols: false },
        medium: { len: 12, upper: true,  lower: true,  numbers: true,  symbols: true  },
        strong: { len: 16, upper: true,  lower: true,  numbers: true,  symbols: true  },
        ultra:  { len: 32, upper: true,  lower: true,  numbers: true,  symbols: true  },
        pin:    { len: 6,  upper: false, lower: false, numbers: true,  symbols: false }
    };

    lenSlider.addEventListener('input', function () {
        lenVal.textContent = lenSlider.value;
    });

    // Presets
    document.querySelectorAll('[data-preset]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var p = PRESETS[btn.getAttribute('data-preset')];
            if (!p) return;
            lenSlider.value = p.len;
            lenVal.textContent = p.len;
            document.getElementById('tc-pw-upper').checked = p.upper;
            document.getElementById('tc-pw-lower').checked = p.lower;
            document.getElementById('tc-pw-numbers').checked = p.numbers;
            document.getElementById('tc-pw-symbols').checked = p.symbols;
            TCTP.activateBtn(btn);
        });
    });

    function buildPool() {
        var pool = '';
        if (document.getElementById('tc-pw-upper').checked) pool += CHARS.upper;
        if (document.getElementById('tc-pw-lower').checked) pool += CHARS.lower;
        if (document.getElementById('tc-pw-numbers').checked) pool += CHARS.numbers;
        if (document.getElementById('tc-pw-symbols').checked) pool += CHARS.symbols;

        var excludeSet = {};
        if (document.getElementById('tc-pw-no-ambiguous').checked) {
            ['0', 'O', 'l', 'I'].forEach(function (c) { excludeSet[c] = true; });
        }
        document.getElementById('tc-pw-exclude').value.split('').forEach(function (c) {
            excludeSet[c] = true;
        });

        return pool.split('').filter(function (c) { return !excludeSet[c]; }).join('');
    }

    function randomChar(pool) {
        var arr = new Uint32Array(1);
        var limit = Math.floor(4294967296 / pool.length) * pool.length;
        var idx;
        do { crypto.getRandomValues(arr); idx = arr[0]; } while (idx >= limit);
        return pool[idx % pool.length];
    }

    function cryptoShuffle(arr) {
        for (var i = arr.length - 1; i > 0; i--) {
            var a = new Uint32Array(1);
            crypto.getRandomValues(a);
            var j = a[0] % (i + 1);
            var tmp = arr[i]; arr[i] = arr[j]; arr[j] = tmp;
        }
        return arr;
    }

    function generateOne(pool, length, minEach) {
        if (!pool.length) return null;
        var pw = [];
        if (minEach) {
            var sets = [];
            if (document.getElementById('tc-pw-upper').checked) sets.push(CHARS.upper);
            if (document.getElementById('tc-pw-lower').checked) sets.push(CHARS.lower);
            if (document.getElementById('tc-pw-numbers').checked) sets.push(CHARS.numbers);
            if (document.getElementById('tc-pw-symbols').checked) sets.push(CHARS.symbols);
            sets.forEach(function (s) {
                var available = s.split('').filter(function (c) { return pool.indexOf(c) !== -1; }).join('');
                if (available.length && pw.length < length) pw.push(randomChar(available));
            });
        }
        while (pw.length < length) pw.push(randomChar(pool));
        return cryptoShuffle(pw).join('');
    }

    function analyseStrength(pw, pool) {
        var entropy = pw.length * Math.log2(Math.max(pool.length, 1));
        var label, color;
        if (entropy < 28)      { label = 'Very Weak'; color = '#b45309'; }
        else if (entropy < 40) { label = 'Weak'; color = '#f97316'; }
        else if (entropy < 60) { label = 'Fair'; color = '#eab308'; }
        else if (entropy < 80) { label = 'Strong'; color = '#22c55e'; }
        else                   { label = 'Very Strong'; color = '#d4a24c'; }
        return { label: label, color: color, entropy: entropy };
    }

    // Generate
    document.getElementById('tc-pw-generate').addEventListener('click', function () {
        var length = parseInt(lenSlider.value) || 16;
        var count = Math.max(1, Math.min(100, parseInt(document.getElementById('tc-pw-count').value) || 1));
        var minEach = document.getElementById('tc-pw-min-each').checked;
        var pool = buildPool();

        if (!pool.length) {
            out.value = 'Please select at least one character type.';
            return;
        }

        var passwords = [];
        for (var i = 0; i < count; i++) {
            var pw = generateOne(pool, length, minEach);
            if (pw) passwords.push(pw);
        }

        out.value = passwords.join('\n');

        if (passwords.length === 1) {
            var str = analyseStrength(passwords[0], pool);
            var bar = document.getElementById('tc-pw-strength-bar');
            var lbl = document.getElementById('tc-pw-strength-label');
            if (bar) { bar.style.width = Math.min(100, str.entropy) + '%'; bar.style.background = str.color; }
            if (lbl) { lbl.textContent = str.label; lbl.style.color = str.color; }
        }

        TCTP.toast(passwords.length + ' password(s) generated!');
    });

    // Copy
    document.getElementById('tc-pw-copy').addEventListener('click', function () {
        TCTP.copyText(out.value, 'Password');
    });

})();