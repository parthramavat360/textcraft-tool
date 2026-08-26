/**
 * Percentage Calculator — Tool JS
 * Calculate percentages, increases, decreases, and differences.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var container = document.getElementById('tc-pct-a');
    if (!container) return;

    // ── Calculator 1: X% of Y ─────────────────────────────────

    var a = document.getElementById('tc-pct-a');
    var b = document.getElementById('tc-pct-b');
    var r1 = document.getElementById('tc-pct-result-1');

    function calc1() {
        var x = parseFloat(a.value);
        var y = parseFloat(b.value);
        if (!isNaN(x) && !isNaN(y)) {
            r1.textContent = (x / 100 * y).toLocaleString(undefined, {maximumFractionDigits: 4});
        } else {
            r1.textContent = '?';
        }
    }

    if (a) a.addEventListener('input', calc1);
    if (b) b.addEventListener('input', calc1);

    // ── Calculator 2: X is what % of Y ────────────────────────

    var c = document.getElementById('tc-pct-c');
    var d = document.getElementById('tc-pct-d');
    var r2 = document.getElementById('tc-pct-result-2');

    function calc2() {
        var x = parseFloat(c.value);
        var y = parseFloat(d.value);
        if (!isNaN(x) && !isNaN(y) && y !== 0) {
            r2.textContent = (x / y * 100).toLocaleString(undefined, {maximumFractionDigits: 4}) + '%';
        } else {
            r2.textContent = '?';
        }
    }

    if (c) c.addEventListener('input', calc2);
    if (d) d.addEventListener('input', calc2);

    // ── Calculator 3: Percentage Change ────────────────────────

    var e = document.getElementById('tc-pct-e');
    var f = document.getElementById('tc-pct-f');
    var r3 = document.getElementById('tc-pct-result-3');

    function calc3() {
        var from = parseFloat(e.value);
        var to = parseFloat(f.value);
        if (!isNaN(from) && !isNaN(to) && from !== 0) {
            var change = ((to - from) / Math.abs(from) * 100);
            var sign = change > 0 ? '+' : '';
            r3.textContent = sign + change.toLocaleString(undefined, {maximumFractionDigits: 4}) + '%';
            r3.style.color = change >= 0 ? 'var(--ok)' : '#dc2626';
        } else {
            r3.textContent = '?';
            r3.style.color = '';
        }
    }

    if (e) e.addEventListener('input', calc3);
    if (f) f.addEventListener('input', calc3);

    // ── Calculator 4: Percentage Difference ────────────────────

    var g = document.getElementById('tc-pct-g');
    var h = document.getElementById('tc-pct-h');
    var r4 = document.getElementById('tc-pct-result-4');

    function calc4() {
        var v1 = parseFloat(g.value);
        var v2 = parseFloat(h.value);
        if (!isNaN(v1) && !isNaN(v2)) {
            var avg = (Math.abs(v1) + Math.abs(v2)) / 2;
            if (avg === 0) {
                r4.textContent = '0%';
            } else {
                r4.textContent = (Math.abs(v1 - v2) / avg * 100).toLocaleString(undefined, {maximumFractionDigits: 4}) + '%';
            }
        } else {
            r4.textContent = '?';
        }
    }

    if (g) g.addEventListener('input', calc4);
    if (h) h.addEventListener('input', calc4);

    // ── Calculator 5 & 6: Add/Subtract % ──────────────────────

    var i = document.getElementById('tc-pct-i');
    var j = document.getElementById('tc-pct-j');
    var r5 = document.getElementById('tc-pct-result-5');

    function calc5() {
        var val = parseFloat(i.value);
        var pct = parseFloat(j.value);
        if (!isNaN(val) && !isNaN(pct)) {
            r5.textContent = (val * (1 + pct / 100)).toLocaleString(undefined, {maximumFractionDigits: 4});
        } else {
            r5.textContent = '?';
        }
    }

    if (i) i.addEventListener('input', calc5);
    if (j) j.addEventListener('input', calc5);

    var k = document.getElementById('tc-pct-k');
    var l = document.getElementById('tc-pct-l');
    var r6 = document.getElementById('tc-pct-result-6');

    function calc6() {
        var val = parseFloat(k.value);
        var pct = parseFloat(l.value);
        if (!isNaN(val) && !isNaN(pct)) {
            r6.textContent = (val * (1 - pct / 100)).toLocaleString(undefined, {maximumFractionDigits: 4});
        } else {
            r6.textContent = '?';
        }
    }

    if (k) k.addEventListener('input', calc6);
    if (l) l.addEventListener('input', calc6);
})();
