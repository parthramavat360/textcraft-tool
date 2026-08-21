/**
 * Password Generator — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var lenSlider = document.getElementById('tc-pw-len');
        var out = document.getElementById('tc-pw-output');
        var generateBtn = document.getElementById('tc-pw-generate');
        if (!lenSlider || !out || !generateBtn || out.dataset.tcInit) return;
        out.dataset.tcInit = '1';

        var lenVal = document.getElementById('tc-pw-len-val');

        // Scope preset buttons to this widget so other tools' [data-preset]
        // buttons (e.g. Random Number) are never matched.
        var wrap = out.closest('.tc-workspace-wrap') || document;

        var CHARS = {
            upper: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            lower: 'abcdefghijklmnopqrstuvwxyz',
            numbers: '0123456789',
            symbols: '!@#$%^&*()-_=+[]{}|;:,.<>?'
        };

        var PRESETS = {
            basic:  { len: 8,  upper: true,  lower: true,  numbers: true,  symbols: false },
            medium: { len: 12, upper: true,  lower: true,  numbers: true,  symbols: true  },
            strong: { len: 16, upper: true,  lower: true,  numbers: true,  symbols: true  },
            ultra:  { len: 32, upper: true,  lower: true,  numbers: true,  symbols: true  },
            pin:    { len: 6,  upper: false, lower: false, numbers: true,  symbols: false }
        };

        function setLen(len) {
            lenSlider.value = len;
            if (lenVal) lenVal.textContent = len;
        }

        if (lenSlider.dataset.tcRange !== '1') {
            lenSlider.dataset.tcRange = '1';
            lenSlider.addEventListener('input', function () {
                if (lenVal) lenVal.textContent = lenSlider.value;
            });
        }

        // Presets (PHP renders .tc-modes with .tc-btn[data-preset] buttons)
        wrap.querySelectorAll('[data-preset]').forEach(function (btn) {
            var p = PRESETS[btn.getAttribute('data-preset')];
            if (!p) return;
            btn.addEventListener('click', function () {
                setLen(p.len);
                var up = document.getElementById('tc-pw-upper');
                var lo = document.getElementById('tc-pw-lower');
                var nu = document.getElementById('tc-pw-numbers');
                var sy = document.getElementById('tc-pw-symbols');
                if (up) up.checked = p.upper;
                if (lo) lo.checked = p.lower;
                if (nu) nu.checked = p.numbers;
                if (sy) sy.checked = p.symbols;
                btn.classList.add('sel');
            });
        });

        function isChecked(id) {
            var el = document.getElementById(id);
            return !!(el && el.checked);
        }

        function buildPool() {
            var pool = '';
            if (isChecked('tc-pw-upper')) pool += CHARS.upper;
            if (isChecked('tc-pw-lower')) pool += CHARS.lower;
            if (isChecked('tc-pw-numbers')) pool += CHARS.numbers;
            if (isChecked('tc-pw-symbols')) pool += CHARS.symbols;

            var excludeSet = {};
            if (isChecked('tc-pw-no-ambiguous')) {
                ['0', 'O', 'l', 'I'].forEach(function (c) { excludeSet[c] = true; });
            }
            var excludeInput = document.getElementById('tc-pw-exclude');
            if (excludeInput && excludeInput.value) {
                excludeInput.value.split('').forEach(function (c) {
                    excludeSet[c] = true;
                });
            }

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
                if (isChecked('tc-pw-upper')) sets.push(CHARS.upper);
                if (isChecked('tc-pw-lower')) sets.push(CHARS.lower);
                if (isChecked('tc-pw-numbers')) sets.push(CHARS.numbers);
                if (isChecked('tc-pw-symbols')) sets.push(CHARS.symbols);
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

        generateBtn.addEventListener('click', function () {
            var length = parseInt(lenSlider.value, 10) || 16;
            var countInput = document.getElementById('tc-pw-count');
            var count = countInput ? Math.max(1, Math.min(100, parseInt(countInput.value, 10) || 1)) : 1;
            var minEach = isChecked('tc-pw-min-each');
            var pool = buildPool();

            if (!pool.length) {
                out.value = 'Please select at least one character type.';
                TCTP.toast('Select at least one character type.', '\u26A0\uFE0F');
                return;
            }

            var passwords = [];
            for (var i = 0; i < count; i++) {
                var pw = generateOne(pool, length, minEach);
                if (pw) passwords.push(pw);
            }

            out.value = passwords.join('\n');
            TCTP.updateResultPanel('N/A', passwords.length + ' password(s)', 'N/A', 'Done');

            if (passwords.length === 1) {
                var str = analyseStrength(passwords[0], pool);
                var bar = document.getElementById('tc-pw-strength-bar');
                var lbl = document.getElementById('tc-pw-strength-label');
                if (bar) { bar.style.width = Math.min(100, str.entropy) + '%'; bar.style.background = str.color; }
                if (lbl) { lbl.textContent = str.label; lbl.style.color = str.color; }
            }

            TCTP.toast(passwords.length + ' password(s) generated!');
        });

        var copyBtn = document.getElementById('tc-pw-copy');
        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                TCTP.copyText(out.value, 'Password');
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
