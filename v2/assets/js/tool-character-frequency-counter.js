/**
 * Character Frequency Counter — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var inp = document.getElementById('tc-freq-input');
    var analyzeBtn = document.getElementById('tc-freq-analyze');
    if (!inp || !analyzeBtn) return;

    var currentMode = 'chars';

    document.querySelectorAll('.tc-modes[data-group="freq-mode"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            currentMode = btn.getAttribute('data-val');
        });
    });

    function analyze() {
        var text = inp.value;
        if (!text) {
            TCTP.toast('Enter some text first.', '\u26A0\uFE0F');
            return;
        }

        var caseSensitive = document.getElementById('tc-freq-case') && document.getElementById('tc-freq-case').checked;
        var includeSpaces = document.getElementById('tc-freq-spaces') && document.getElementById('tc-freq-spaces').checked;

        var items = {};

        if (currentMode === 'chars') {
            var str = caseSensitive ? text : text.toLowerCase();
            for (var i = 0; i < str.length; i++) {
                var ch = str[i];
                if (!includeSpaces && /\s/.test(ch)) continue;
                items[ch] = (items[ch] || 0) + 1;
            }
        } else if (currentMode === 'words') {
            var words = text.trim().split(/\s+/);
            words.forEach(function (w) {
                var key = caseSensitive ? w : w.toLowerCase();
                items[key] = (items[key] || 0) + 1;
            });
        } else {
            var lines = text.split('\n');
            lines.forEach(function (l, i) {
                items['Line ' + (i + 1)] = (items['Line ' + (i + 1)] || 0) + l.length;
            });
        }

        var sorted = Object.keys(items).sort(function (a, b) { return items[b] - items[a]; });
        var total = Object.values(items).reduce(function (a, b) { return a + b; }, 0);
        var maxCount = items[sorted[0]] || 1;

        var tableEl = document.getElementById('tc-freq-table');
        var chartEl = document.getElementById('tc-freq-chart');

        if (tableEl) {
            tableEl.innerHTML = '<table class="tc-freq-tbl"><thead><tr><th>Item</th><th>Count</th><th>%</th></tr></thead><tbody>' +
                sorted.map(function (k) {
                    var pct = ((items[k] / total) * 100).toFixed(2);
                    return '<tr><td><code>' + escapeHtml(k) + '</code></td><td>' + items[k] + '</td><td>' + pct + '%</td></tr>';
                }).join('') +
                '</tbody></table>';
        }

        if (chartEl) {
            chartEl.innerHTML = sorted.slice(0, 30).map(function (k) {
                var pct = (items[k] / maxCount) * 100;
                return '<div class="tc-freq-bar-row"><span class="tc-freq-bar-label">' + escapeHtml(k) + '</span><div class="tc-freq-bar-track"><div class="tc-freq-bar-fill" style="width:' + pct + '%"></div></div><span class="tc-freq-bar-count">' + items[k] + '</span></div>';
            }).join('');
        }

        var statusEl = document.getElementById('tc-freq-status');
        if (statusEl) {
            statusEl.textContent = 'Analyzed: ' + total + ' total ' + currentMode + ', ' + sorted.length + ' unique';
            statusEl.className = 'tc-status tc-status--success';
        }
        TCTP.toast('Analysis complete!');
    }

    function escapeHtml(s) {
        return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    analyzeBtn.addEventListener('click', analyze);

    document.getElementById('tc-freq-copy').addEventListener('click', function () {
        var tableEl = document.getElementById('tc-freq-table');
        TCTP.copyText(tableEl ? tableEl.innerText : '', 'Frequency table');
    });

})();
