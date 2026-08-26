(function () {
    'use strict';
    var $ = function (s, p) { return (p || document).querySelector(s); };
    if (!$('.tc-palette')) return;

    var harmony = 'analogous';
    var baseColor = $('#pal-base');
    var hexInput = $('#pal-hex');
    var count = $('#pal-count');
    var variants = $('#pal-variants');
    var contrast = $('#pal-contrast');
    var generateBtn = $('#pal-generate');
    var resultEl = $('#pal-result');
    var output = $('#pal-output');
    var cssOutput = $('#pal-css');

    TCTP.initModeGroup('pal-harmony', function (val) { harmony = val; });

    baseColor.addEventListener('input', function () { hexInput.value = baseColor.value; });
    hexInput.addEventListener('input', function () {
        if (/^#[0-9a-f]{6}$/i.test(hexInput.value)) baseColor.value = hexInput.value;
    });

    function hexToHsl(hex) {
        var r = parseInt(hex.slice(1, 3), 16) / 255;
        var g = parseInt(hex.slice(3, 5), 16) / 255;
        var b = parseInt(hex.slice(5, 7), 16) / 255;
        var max = Math.max(r, g, b), min = Math.min(r, g, b);
        var h, s, l = (max + min) / 2;
        if (max === min) { h = s = 0; } else {
            var d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
                case r: h = ((g - b) / d + (g < b ? 6 : 0)) / 6; break;
                case g: h = ((b - r) / d + 2) / 6; break;
                case b: h = ((r - g) / d + 4) / 6; break;
            }
        }
        return [Math.round(h * 360), Math.round(s * 100), Math.round(l * 100)];
    }

    function hslToHex(h, s, l) {
        h = ((h % 360) + 360) % 360;
        s = Math.max(0, Math.min(100, s)) / 100;
        l = Math.max(0, Math.min(100, l)) / 100;
        var c = (1 - Math.abs(2 * l - 1)) * s;
        var x = c * (1 - Math.abs((h / 60) % 2 - 1));
        var m = l - c / 2;
        var r, g, b;
        if (h < 60) { r = c; g = x; b = 0; }
        else if (h < 120) { r = x; g = c; b = 0; }
        else if (h < 180) { r = 0; g = c; b = x; }
        else if (h < 240) { r = 0; g = x; b = c; }
        else if (h < 300) { r = x; g = 0; b = c; }
        else { r = c; g = 0; b = x; }
        var toHex = function (v) { var h = Math.round((v + m) * 255).toString(16); return h.length === 1 ? '0' + h : h; };
        return '#' + toHex(r) + toHex(g) + toHex(b);
    }

    function getContrast(hex) {
        var r = parseInt(hex.slice(1, 3), 16) / 255;
        var g = parseInt(hex.slice(3, 5), 16) / 255;
        var b = parseInt(hex.slice(5, 7), 16) / 255;
        var luminance = 0.2126 * r + 0.7152 * g + 0.0722 * b;
        return luminance > 0.179 ? '#000000' : '#ffffff';
    }

    generateBtn.addEventListener('click', function () {
        var base = hexToHsl(hexInput.value);
        var num = parseInt(count.value);
        var colors = [];

        if (harmony === 'analogous') {
            var spread = 30;
            for (var i = 0; i < num; i++) colors.push(hslToHex(base[0] + (i - Math.floor(num / 2)) * spread, base[1], base[2]));
        } else if (harmony === 'complementary') {
            var comp = (base[0] + 180) % 360;
            for (var i = 0; i < num; i++) { var t = i / (num - 1); colors.push(hslToHex(base[0] + t * (comp - base[0]), base[1] - t * 10, base[2])); }
        } else if (harmony === 'triadic') {
            var t1 = (base[0] + 120) % 360, t2 = (base[0] + 240) % 360;
            colors.push(hslToHex(base[0], base[1], base[2]));
            for (var i = 1; i < num; i++) { var angle = i % 3 === 1 ? t1 : t2; colors.push(hslToHex(angle, base[1], base[2] + Math.floor(i / 3) * 15)); }
        } else if (harmony === 'split') {
            var s1 = (base[0] + 150) % 360, s2 = (base[0] + 210) % 360;
            colors.push(hslToHex(base[0], base[1], base[2]));
            for (var i = 1; i < num; i++) { var angle = i % 2 === 1 ? s1 : s2; colors.push(hslToHex(angle, base[1], base[2] + i * 10)); }
        } else {
            for (var i = 0; i < num; i++) { var l = Math.max(20, Math.min(85, base[2] - 30 + (i * (60 / (num - 1))))); colors.push(hslToHex(base[0], base[1], l)); }
        }

        var html = '<div style="display:flex;gap:8px;flex-wrap:wrap">';
        colors.forEach(function (c) {
            var fg = getContrast(c);
            html += '<div style="flex:1;min-width:100px;border-radius:12px;overflow:hidden;background:#1e293b"><div style="height:80px;background:' + c + '"></div><div style="padding:10px;text-align:center"><div style="font-size:12px;font-weight:700;color:' + fg + ';background:' + c + ';padding:4px 8px;border-radius:6px;display:inline-block">' + c.toUpperCase() + '</div></div></div>';
        });
        html += '</div>';

        if (variants.checked) {
            html += '<div style="margin-top:20px"><h4 style="color:#94a3b8;font-size:13px;margin-bottom:12px">Light & Dark Variants</h4><div style="display:flex;gap:8px;flex-wrap:wrap">';
            colors.forEach(function (c) {
                var hsl = hexToHsl(c);
                var light = hslToHex(hsl[0], hsl[1], Math.min(95, hsl[2] + 25));
                var dark = hslToHex(hsl[0], hsl[1], Math.max(5, hsl[2] - 25));
                html += '<div style="flex:1;min-width:80px;border-radius:8px;overflow:hidden"><div style="height:30px;background:' + light + '"></div><div style="height:30px;background:' + c + '"></div><div style="height:30px;background:' + dark + '"></div></div>';
            });
            html += '</div></div>';
        }

        if (contrast.checked) {
            html += '<div style="margin-top:20px"><h4 style="color:#94a3b8;font-size:13px;margin-bottom:12px">WCAG Contrast Ratios (vs white)</h4><div style="display:flex;gap:6px;flex-wrap:wrap">';
            colors.forEach(function (c) {
                var r = parseInt(c.slice(1, 3), 16) / 255, g = parseInt(c.slice(3, 5), 16) / 255, b = parseInt(c.slice(5, 7), 16) / 255;
                var l1 = 0.2126 * r + 0.7152 * g + 0.0722 * b + 0.05;
                var ratio = (1.05 / l1).toFixed(2);
                var level = ratio >= 7 ? 'AAA' : ratio >= 4.5 ? 'AA' : 'Fail';
                var col = ratio >= 7 ? '#22c55e' : ratio >= 4.5 ? '#eab308' : '#ef4444';
                html += '<div style="padding:6px 10px;background:#0f172a;border-radius:8px;font-size:11px;color:' + col + '">' + c.toUpperCase() + ': ' + ratio + ':1 (' + level + ')</div>';
            });
            html += '</div></div>';
        }

        output.innerHTML = html;

        var css = ':root {\n';
        colors.forEach(function (c, i) { css += '  --color-' + (i + 1) + ': ' + c + ';\n'; });
        css += '}';
        cssOutput.innerHTML = '<pre style="background:#0f172a;color:#e2e8f0;padding:16px;border-radius:12px;font-size:13px;line-height:1.6;overflow-x:auto;border:1px solid rgba(148,163,184,0.15)">' + escHtml(css) + '</pre>' +
            '<button class="tc-btn tc-btn--ghost tc-copy-btn" data-copy="' + escAttr(css) + '" style="margin-top:12px"><i class="fa-regular fa-copy"></i> Copy CSS</button>';

        resultEl.style.display = '';
        TCTP.initTabs(resultEl);
        TCTP.toast('Palette generated!', '\u2705');
    });

    function escHtml(s) { return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
    function escAttr(s) { return s.replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/\n/g,' '); }
})();
