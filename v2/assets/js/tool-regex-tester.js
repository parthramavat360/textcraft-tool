/**
 * Regex Tester & Debugger — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var inp = document.getElementById('tc-regex-input');
    var patternEl = document.getElementById('tc-regex-pattern');
    var flagsEl = document.getElementById('tc-regex-flags');
    var testBtn = document.getElementById('tc-regex-test');
    if (!inp || !patternEl || !testBtn) return;

    var QUICK_PATTERNS = {
        email: '[a-zA-Z0-9._%+\\-]+@[a-zA-Z0-9.\\-]+\\.[a-zA-Z]{2,}',
        url: 'https?:\\/\\/[^\\s]+',
        phone: '\\+?\\d{1,4}[\\s\\-.]?\\(?\\d{1,4}\\)?[\\s\\-.]?\\d{1,4}[\\s\\-.]?\\d{1,9}',
        ip: '\\b(?:\\d{1,3}\\.){3}\\d{1,3}\\b',
        date: '\\d{1,2}[\\/\\-]\\d{1,2}[\\/\\-]\\d{2,4}',
        hex: '#(?:[0-9a-fA-F]{3}){1,2}\\b'
    };

    document.querySelectorAll('.tc-modes[data-group="regex-quick"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            var key = btn.getAttribute('data-val');
            if (QUICK_PATTERNS[key]) {
                patternEl.value = QUICK_PATTERNS[key];
                runTest();
            }
        });
    });

    function runTest() {
        var pattern = patternEl.value.trim();
        var flags = flagsEl.value.trim();
        var text = inp.value;
        var matchesEl = document.getElementById('tc-regex-matches');
        var groupsEl = document.getElementById('tc-regex-groups');
        var countEl = document.getElementById('tc-regex-count');
        var statusEl = document.getElementById('tc-regex-status');
        var resultEl = document.getElementById('tc-regex-result-text');

        if (!pattern) {
            TCTP.toast('Enter a regex pattern first.', '\u26A0\uFE0F');
            return;
        }

        if (!text) {
            TCTP.toast('Enter test text first.', '\u26A0\uFE0F');
            return;
        }

        try {
            var re = new RegExp(pattern, flags);
        } catch (e) {
            if (statusEl) {
                statusEl.textContent = 'Invalid regex: ' + e.message;
                statusEl.className = 'tc-status tc-status--error';
            }
            TCTP.toast('Invalid regex pattern!', '\u274C');
            return;
        }

        var matches = [];
        var m;
        var re2 = new RegExp(pattern, flags);
        while ((m = re2.exec(text)) !== null) {
            matches.push({ value: m[0], index: m.index, groups: m.slice(1) });
            if (!flags.includes('g')) break;
        }

        if (statusEl) {
            statusEl.textContent = matches.length + ' match(es) found';
            statusEl.className = 'tc-status tc-status--success';
        }
        if (countEl) countEl.textContent = matches.length + ' found';

        if (matchesEl) {
            matchesEl.innerHTML = matches.length
                ? matches.map(function (m, i) {
                    return '<div class="tc-match-item"><span class="tc-match-idx">#' + (i + 1) + '</span> <code class="tc-match-val">' + escapeHtml(m.value) + '</code> <span class="tc-match-pos">at index ' + m.index + '</span></div>';
                }).join('')
                : '<div class="tc-empty">No matches found</div>';
        }

        if (groupsEl) {
            var hasGroups = matches.some(function (m) { return m.groups.length > 0; });
            groupsEl.innerHTML = hasGroups
                ? matches.map(function (m, i) {
                    if (!m.groups.length) return '';
                    return '<div class="tc-group-item"><b>Match #' + (i + 1) + ':</b> ' + m.groups.map(function (g, gi) {
                        return '<span class="tc-group-tag">Group ' + (gi + 1) + ': <code>' + escapeHtml(g || '(empty)') + '</code></span>';
                    }).join(' ') + '</div>';
                }).filter(Boolean).join('')
                : '<div class="tc-empty">No capture groups</div>';
        }

        if (resultEl) {
            resultEl.value = JSON.stringify(matches.map(function (m) {
                return { match: m.value, index: m.index, groups: m.groups };
            }), null, 2);
        }

        TCTP.updateResultPanel(text.length.toLocaleString() + ' chars', matches.length.toLocaleString() + ' match(es)', '\u2014', 'Done');

        TCTP.toast(matches.length + ' match(es) found!');
    }

    function escapeHtml(str) {
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    testBtn.addEventListener('click', runTest);

    var copyBtn = document.getElementById('tc-regex-copy-matches');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            var resultEl = document.getElementById('tc-regex-result-text');
            TCTP.copyText(resultEl ? resultEl.value : '', 'Matches');
        });
    }

    patternEl.addEventListener('input', function () {
        if (inp.value && patternEl.value) runTest();
    });

})();
