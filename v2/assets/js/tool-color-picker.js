/**
 * Color Picker & Converter — Tool JS
 * Pick colors and convert between HEX, RGB, HSL, CMYK.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var mainInput = document.getElementById('tc-color-main');
    if (!mainInput) return;

    var previewBig = document.getElementById('tc-color-preview-big');
    var hexInput = document.getElementById('tc-color-hex');
    var rgbInput = document.getElementById('tc-color-rgb');
    var hslInput = document.getElementById('tc-color-hsl');
    var cmykInput = document.getElementById('tc-color-cmyk');
    var shadesContainer = document.getElementById('tc-color-shades');
    var harmonyRow = document.getElementById('tc-color-harmony-row');
    var harmonyCards = document.querySelectorAll('.tc-color-harmony-modes .tc-rsz-mode-card');
    var previewCircle = document.getElementById('tc-color-preview-circle');
    var previewPanel = document.getElementById('tc-color-preview-panel');
    var harmonyMode = 'complementary';

    // ── Color conversions ──────────────────────────────────────

    function hexToRGB(hex) {
        hex = hex.replace('#', '');
        if (hex.length === 3) hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
        return {
            r: parseInt(hex.substring(0, 2), 16),
            g: parseInt(hex.substring(2, 4), 16),
            b: parseInt(hex.substring(4, 6), 16)
        };
    }

    function rgbToHex(r, g, b) {
        return '#' + [r, g, b].map(function (x) {
            var h = Math.max(0, Math.min(255, Math.round(x))).toString(16);
            return h.length === 1 ? '0' + h : h;
        }).join('');
    }

    function rgbToHSL(r, g, b) {
        r /= 255; g /= 255; b /= 255;
        var max = Math.max(r, g, b), min = Math.min(r, g, b);
        var h, s, l = (max + min) / 2;

        if (max === min) {
            h = s = 0;
        } else {
            var d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            if (max === r) h = ((g - b) / d + (g < b ? 6 : 0)) / 6;
            else if (max === g) h = ((b - r) / d + 2) / 6;
            else h = ((r - g) / d + 4) / 6;
        }

        return { h: Math.round(h * 360), s: Math.round(s * 100), l: Math.round(l * 100) };
    }

    function hslToRGB(h, s, l) {
        h /= 360; s /= 100; l /= 100;
        var r, g, b;

        if (s === 0) {
            r = g = b = l;
        } else {
            function hue2rgb(p, q, t) {
                if (t < 0) t += 1;
                if (t > 1) t -= 1;
                if (t < 1/6) return p + (q - p) * 6 * t;
                if (t < 1/2) return q;
                if (t < 2/3) return p + (q - p) * (2/3 - t) * 6;
                return p;
            }
            var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
            var p = 2 * l - q;
            r = hue2rgb(p, q, h + 1/3);
            g = hue2rgb(p, q, h);
            b = hue2rgb(p, q, h - 1/3);
        }

        return { r: Math.round(r * 255), g: Math.round(g * 255), b: Math.round(b * 255) };
    }

    function rgbToCMYK(r, g, b) {
        r /= 255; g /= 255; b /= 255;
        var k = 1 - Math.max(r, g, b);
        if (k === 1) return { c: 0, m: 0, y: 0, k: 100 };
        return {
            c: Math.round(((1 - r - k) / (1 - k)) * 100),
            m: Math.round(((1 - g - k) / (1 - k)) * 100),
            y: Math.round(((1 - b - k) / (1 - k)) * 100),
            k: Math.round(k * 100)
        };
    }

    // ── Update all values ──────────────────────────────────────

    function updateFromHex(hex) {
        var rgb = hexToRGB(hex);
        var hsl = rgbToHSL(rgb.r, rgb.g, rgb.b);
        var cmyk = rgbToCMYK(rgb.r, rgb.g, rgb.b);

        if (previewBig) previewBig.style.background = hex;
        if (previewCircle) previewCircle.style.background = hex;
        if (hexInput) hexInput.value = hex;
        if (rgbInput) rgbInput.value = 'rgb(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ')';
        if (hslInput) hslInput.value = 'hsl(' + hsl.h + ', ' + hsl.s + '%, ' + hsl.l + '%)';
        if (cmykInput) cmykInput.value = 'cmyk(' + cmyk.c + '%, ' + cmyk.m + '%, ' + cmyk.y + '%, ' + cmyk.k + '%)';

        var origStat = document.getElementById('tc-stat-orig');
        if (origStat) origStat.textContent = hex;

        updateShades(hsl);
        updateHarmony(hsl);
    }

    // ── Shades & Tints ─────────────────────────────────────────

    function updateShades(hsl) {
        if (!shadesContainer) return;
        shadesContainer.innerHTML = '';
        var steps = 11;

        for (var i = 0; i < steps; i++) {
            var lightness = Math.round((i / (steps - 1)) * 100);
            var rgb = hslToRGB(hsl.h, hsl.s, lightness);
            var hex = rgbToHex(rgb.r, rgb.g, rgb.b);

            var swatch = document.createElement('div');
            swatch.className = 'tc-color-swatch';
            swatch.style.background = hex;
            swatch.title = hex;
            swatch.setAttribute('data-hex', hex);
            swatch.addEventListener('click', function () {
                var h = this.getAttribute('data-hex');
                mainInput.value = h;
                updateFromHex(h);
            });
            shadesContainer.appendChild(swatch);
        }
    }

    // ── Harmony colors ─────────────────────────────────────────

    function updateHarmony(hsl) {
        if (!harmonyRow) return;
        harmonyRow.innerHTML = '';
        var colors = [];

        if (harmonyMode === 'complementary') {
            colors = [
                { h: hsl.h, s: hsl.s, l: hsl.l },
                { h: (hsl.h + 180) % 360, s: hsl.s, l: hsl.l }
            ];
        } else if (harmonyMode === 'analogous') {
            colors = [
                { h: (hsl.h - 30 + 360) % 360, s: hsl.s, l: hsl.l },
                { h: hsl.h, s: hsl.s, l: hsl.l },
                { h: (hsl.h + 30) % 360, s: hsl.s, l: hsl.l }
            ];
        } else if (harmonyMode === 'triadic') {
            colors = [
                { h: hsl.h, s: hsl.s, l: hsl.l },
                { h: (hsl.h + 120) % 360, s: hsl.s, l: hsl.l },
                { h: (hsl.h + 240) % 360, s: hsl.s, l: hsl.l }
            ];
        } else if (harmonyMode === 'split') {
            colors = [
                { h: hsl.h, s: hsl.s, l: hsl.l },
                { h: (hsl.h + 150) % 360, s: hsl.s, l: hsl.l },
                { h: (hsl.h + 210) % 360, s: hsl.s, l: hsl.l }
            ];
        }

        var countEl = document.getElementById('tc-stat-saved');
        if (countEl) countEl.textContent = colors.length;

        var modeLabel = document.getElementById('tc-stat-comp');
        if (modeLabel) modeLabel.textContent = harmonyMode.charAt(0).toUpperCase() + harmonyMode.slice(1);

        colors.forEach(function (c) {
            var rgb = hslToRGB(c.h, c.s, c.l);
            var hex = rgbToHex(rgb.r, rgb.g, rgb.b);

            var swatch = document.createElement('div');
            swatch.className = 'tc-color-harmony-swatch';
            swatch.setAttribute('data-hex', hex);

            var circle = document.createElement('div');
            circle.className = 'tc-color-harmony-circle';
            circle.style.background = hex;

            var label = document.createElement('span');
            label.textContent = hex;

            swatch.appendChild(circle);
            swatch.appendChild(label);
            swatch.addEventListener('click', function () {
                var h = this.getAttribute('data-hex');
                mainInput.value = h;
                updateFromHex(h);
            });
            harmonyRow.appendChild(swatch);
        });

        // Update result panel
        var resultEl = document.getElementById('tc-color-result');
        if (resultEl) {
            resultEl.innerHTML = '';
            colors.forEach(function (c) {
                var rgb = hslToRGB(c.h, c.s, c.l);
                var hex = rgbToHex(rgb.r, rgb.g, rgb.b);
                var item = document.createElement('div');
                item.className = 'tc-color-harmony-result-item';
                item.innerHTML = '<div class="tc-color-harmony-result-circle" style="background:' + hex + '"></div>' +
                    '<span>' + hex + '</span>';
                item.addEventListener('click', function () {
                    mainInput.value = hex;
                    updateFromHex(hex);
                });
                resultEl.appendChild(item);
            });
        }
    }

    // ── Main picker input ──────────────────────────────────────

    mainInput.addEventListener('input', function () {
        updateFromHex(mainInput.value);
    });

    // ── Harmony mode cards ─────────────────────────────────────

    harmonyCards.forEach(function (card) {
        card.addEventListener('click', function () {
            harmonyCards.forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            harmonyMode = card.getAttribute('data-val') || 'complementary';
            var hsl = rgbToHSL(hexToRGB(mainInput.value).r, hexToRGB(mainInput.value).g, hexToRGB(mainInput.value).b);
            updateHarmony(hsl);
        });
    });

    // ── Copy buttons ───────────────────────────────────────────

    document.querySelectorAll('.tc-color-copy').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var targetId = btn.getAttribute('data-target');
            var input = document.getElementById(targetId);
            if (input) {
                TCTP.copyText(input.value);
                TCTP.toast('Copied: ' + input.value, '\u2705');
            }
        });
    });

    // ── Init ───────────────────────────────────────────────────

    updateFromHex(mainInput.value);
})();
