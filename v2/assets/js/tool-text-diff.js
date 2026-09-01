(function () {
    'use strict';
    var $ = function (s, p) { return (p || document).querySelector(s); };
    var diffA = $('#diff-a');
    if (!diffA) return;

    var diffB = $('#diff-b');
    var compareBtn = $('#diff-compare');
    var resultEl = $('#diff-result');
    var visualEl = $('#diff-visual');
    var unifiedEl = $('#diff-unified');
    var statsEl = $('#diff-stats');
    var ignoreCase = $('#diff-ignore-case');
    var ignoreSpace = $('#diff-ignore-space');

    function diffLines(a, b) {
        var aLines = a.split('\n');
        var bLines = b.split('\n');

        function normalize(s) {
            if (ignoreCase.checked) s = s.toLowerCase();
            if (ignoreSpace.checked) s = s.replace(/\s+/g, ' ').trim();
            return s;
        }

        var result = [];
        var maxLen = Math.max(aLines.length, bLines.length);

        for (var i = 0; i < maxLen; i++) {
            var aLine = i < aLines.length ? aLines[i] : '';
            var bLine = i < bLines.length ? bLines[i] : '';

            if (normalize(aLine) === normalize(bLine)) {
                result.push({ type: 'equal', a: aLine, b: bLine, line: i + 1 });
            } else {
                result.push({ type: 'changed', a: aLine, b: bLine, line: i + 1 });
            }
        }
        return result;
    }

    function highlightWordDiff(a, b) {
        var aWords = a.split(/(\s+)/);
        var bWords = b.split(/(\s+)/);
        var aHtml = '', bHtml = '';

        var maxLen = Math.max(aWords.length, bWords.length);
        for (var i = 0; i < maxLen; i++) {
            var aw = i < aWords.length ? aWords[i] : '';
            var bw = i < bWords.length ? bWords[i] : '';
            if (aw === bw) {
                aHtml += escHtml(aw);
                bHtml += escHtml(bw);
            } else {
                aHtml += '<span class="tctp-diff-del">' + escHtml(aw) + '</span>';
                bHtml += '<span class="tctp-diff-add">' + escHtml(bw) + '</span>';
            }
        }
        return { a: aHtml, b: bHtml };
    }

    function renderVisual(result) {
        var html = '<div class="tctp-diff-visual">';
        result.forEach(function (r) {
            if (r.type === 'equal') {
                html += '<div class="tctp-diff-line tctp-diff-eq"><span class="tctp-diff-ln">' + r.line + '</span><span class="tctp-diff-code">' + escHtml(r.a) + '</span><span class="tctp-diff-code">' + escHtml(r.b) + '</span></div>';
            } else {
                var wd = highlightWordDiff(r.a, r.b);
                html += '<div class="tctp-diff-line tctp-diff-ch"><span class="tctp-diff-ln">' + r.line + '</span><span class="tctp-diff-code">' + wd.a + '</span><span class="tctp-diff-code">' + wd.b + '</span></div>';
            }
        });
        html += '</div>';
        visualEl.innerHTML = html;
    }

    function renderUnified(result) {
        var text = '';
        result.forEach(function (r) {
            if (r.type === 'equal') {
                text += '  ' + r.a + '\n';
            } else {
                if (r.a) text += '- ' + r.a + '\n';
                if (r.b) text += '+ ' + r.b + '\n';
            }
        });
        unifiedEl.innerHTML = '<pre class="tctp-diff-unified-pre">' + escHtml(text) + '</pre>';
    }

    function renderStats(result, a, b) {
        var added = 0, removed = 0, unchanged = 0;
        result.forEach(function (r) {
            if (r.type === 'equal') unchanged++;
            else { added++; removed++; }
        });
        var total = added + removed + unchanged;
        var pct = total > 0 ? Math.round((unchanged / total) * 100) : 100;

        statsEl.innerHTML =
            '<div class="tctp-diff-stats-grid">' +
            '<div class="tctp-diff-stat-card"><div class="tctp-diff-stat-num tctp-diff-stat-green">' + unchanged + '</div><div class="tctp-diff-stat-lbl">Unchanged</div></div>' +
            '<div class="tctp-diff-stat-card"><div class="tctp-diff-stat-num tctp-diff-stat-green">' + added + '</div><div class="tctp-diff-stat-lbl">Lines Added</div></div>' +
            '<div class="tctp-diff-stat-card"><div class="tctp-diff-stat-num tctp-diff-stat-red">' + removed + '</div><div class="tctp-diff-stat-lbl">Lines Removed</div></div>' +
            '<div class="tctp-diff-stat-card"><div class="tctp-diff-stat-num tctp-diff-stat-dark">' + pct + '%</div><div class="tctp-diff-stat-lbl">Similarity</div></div>' +
            '</div>' +
            '<div class="tctp-diff-stats-footer">' +
            '<div class="tctp-diff-bar"><div class="tctp-diff-bar-fill" style="width:' + pct + '%"></div></div>' +
            '<div class="tctp-diff-bar-cap">Original: ' + a.split('\n').length + ' lines | Modified: ' + b.split('\n').length + ' lines</div></div>';
    }

    function showResult() {
        resultEl.classList.add('tc-diff-open');
    }

    function hideResult() {
        resultEl.classList.remove('tc-diff-open');
    }

    /* ── Tabs ─────────────────────────────────────────────── */
    var tabs = [];
    resultEl.querySelectorAll('.tctp-rsz-tab').forEach(function (btn) {
        tabs.push(btn);
    });
    function activateTab(tab) {
        tabs.forEach(function (b) { b.classList.toggle('sel', b === tab); });
        var name = tab.getAttribute('data-tab');
        ['diff-visual', 'diff-unified', 'diff-stats'].forEach(function (id) {
            var panel = document.getElementById(id);
            if (!panel) return;
            if (id === 'diff-visual') {
                panel.classList.toggle('tc-diff-open', name === 'visual');
            } else if (id === 'diff-unified') {
                panel.classList.toggle('tc-diff-open', name === 'unified');
            } else if (id === 'diff-stats') {
                panel.classList.toggle('tc-diff-open', name === 'stats');
            }
        });
    }
    tabs.forEach(function (btn) {
        btn.addEventListener('click', function () {
            activateTab(btn);
        });
    });

    compareBtn.addEventListener('click', function () {
        var a = diffA.value;
        var b = diffB.value;
        if (!a && !b) { TCTP.toast('Enter text in both fields', '\u26a0\ufe0f'); return; }

        var result = diffLines(a, b);
        renderVisual(result);
        renderUnified(result);
        renderStats(result, a, b);

        activateTab(tabs[0]);
        showResult();
    });

    /* ── Clear all ────────────────────────────────────────── */
    var clearBtn = $('#diff-clear');
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            diffA.value = '';
            diffB.value = '';
            visualEl.innerHTML = '';
            unifiedEl.innerHTML = '';
            statsEl.innerHTML = '';
            ignoreCase.checked = false;
            ignoreSpace.checked = true;
            activateTab(tabs[0]);
            hideResult();
            TCTP.toast('Cleared.', '\uD83E\uDDF9');
        });
    }

    function escHtml(s) { return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }
})();
