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
        unifiedEl.innerHTML = '<pre style="background:#0f172a;color:#e2e8f0;padding:16px;border-radius:12px;font-size:13px;line-height:1.6;overflow-x:auto;border:1px solid rgba(148,163,184,0.15);margin:0;white-space:pre-wrap">' + escHtml(text) + '</pre>';
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
            '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:12px;margin-bottom:16px">' +
            '<div style="background:#0f172a;padding:16px;border-radius:12px;text-align:center;border:1px solid rgba(148,163,184,0.12)"><div style="font-size:28px;font-weight:700;color:#22c55e">' + unchanged + '</div><div style="color:#94a3b8;font-size:12px">Unchanged</div></div>' +
            '<div style="background:#0f172a;padding:16px;border-radius:12px;text-align:center;border:1px solid rgba(148,163,184,0.12)"><div style="font-size:28px;font-weight:700;color:#22c55e">' + added + '</div><div style="color:#94a3b8;font-size:12px">Lines Added</div></div>' +
            '<div style="background:#0f172a;padding:16px;border-radius:12px;text-align:center;border:1px solid rgba(148,163,184,0.12)"><div style="font-size:28px;font-weight:700;color:#ef4444">' + removed + '</div><div style="color:#94a3b8;font-size:12px">Lines Removed</div></div>' +
            '<div style="background:#0f172a;padding:16px;border-radius:12px;text-align:center;border:1px solid rgba(148,163,184,0.12)"><div style="font-size:28px;font-weight:700;color:#0b1220">' + pct + '%</div><div style="color:#94a3b8;font-size:12px">Similarity</div></div>' +
            '</div>' +
            '<div style="background:#0f172a;padding:16px;border-radius:12px;border:1px solid rgba(148,163,184,0.12)">' +
            '<div style="background:#1e293b;height:8px;border-radius:4px;overflow:hidden"><div style="background:linear-gradient(90deg,#22c55e,#0b1220);height:100%;width:' + pct + '%;transition:width 0.3s"></div></div>' +
            '<div style="margin-top:8px;color:#94a3b8;font-size:12px">Original: ' + a.split('\n').length + ' lines | Modified: ' + b.split('\n').length + ' lines</div></div>';
    }

    compareBtn.addEventListener('click', function () {
        var a = diffA.value;
        var b = diffB.value;
        if (!a && !b) { TCTP.toast('Enter text in both fields', '\u26a0\ufe0f'); return; }

        var result = diffLines(a, b);
        renderVisual(result);
        renderUnified(result);
        renderStats(result, a, b);

        resultEl.style.display = '';
        TCTP.initTabs(resultEl);
    });

    function escHtml(s) { return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }
})();
