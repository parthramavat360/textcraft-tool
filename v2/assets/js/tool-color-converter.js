/**
 * Color Converter — HEX / RGB / HSL / HSV
 * @package TextCraft_Tools_Pro
 */
(function () {
  'use strict';

  var inputEl   = document.getElementById('tc-cc-input');
  var pickerEl  = document.getElementById('tc-cc-picker');
  var previewEl = document.getElementById('tc-cc-preview');
  if (!inputEl) return;

  var hexField = document.getElementById('tc-cc-hex');
  var rgbField = document.getElementById('tc-cc-rgb');
  var hslField = document.getElementById('tc-cc-hsl');
  var hsvField = document.getElementById('tc-cc-hsv');
  var cssField = document.getElementById('tc-cc-css');
  var rgbaField = document.getElementById('tc-cc-rgba');
  var rSlider = document.getElementById('tc-cc-r');
  var gSlider = document.getElementById('tc-cc-g');
  var bSlider = document.getElementById('tc-cc-b');

  /* ── Color conversion functions ──────────────────────────── */

  function hexToRgb(hex) {
    hex = hex.replace(/^#/, '');
    if (hex.length === 3) hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
    if (hex.length !== 6) return null;
    var num = parseInt(hex, 16);
    if (isNaN(num)) return null;
    return { r: (num >> 16) & 255, g: (num >> 8) & 255, b: num & 255 };
  }

  function rgbToHex(r, g, b) {
    return '#' + [r, g, b].map(function (v) {
      var h = Math.max(0, Math.min(255, Math.round(v))).toString(16);
      return h.length === 1 ? '0' + h : h;
    }).join('');
  }

  function rgbToHsl(r, g, b) {
    r /= 255; g /= 255; b /= 255;
    var max = Math.max(r, g, b), min = Math.min(r, g, b);
    var h, s, l = (max + min) / 2;
    if (max === min) { h = s = 0; }
    else {
      var d = max - min;
      s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
      if (max === r) h = ((g - b) / d + (g < b ? 6 : 0)) / 6;
      else if (max === g) h = ((b - r) / d + 2) / 6;
      else h = ((r - g) / d + 4) / 6;
    }
    return { h: Math.round(h * 360), s: Math.round(s * 100), l: Math.round(l * 100) };
  }

  function hslToRgb(h, s, l) {
    h /= 360; s /= 100; l /= 100;
    var r, g, b;
    if (s === 0) { r = g = b = l; }
    else {
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

  function rgbToHsv(r, g, b) {
    r /= 255; g /= 255; b /= 255;
    var max = Math.max(r, g, b), min = Math.min(r, g, b);
    var h, s, v = max;
    var d = max - min;
    s = max === 0 ? 0 : d / max;
    if (max === min) { h = 0; }
    else {
      if (max === r) h = ((g - b) / d + (g < b ? 6 : 0)) / 6;
      else if (max === g) h = ((b - r) / d + 2) / 6;
      else h = ((r - g) / d + 4) / 6;
    }
    return { h: Math.round(h * 360), s: Math.round(s * 100), v: Math.round(v * 100) };
  }

  function hsvToRgb(h, s, v) {
    h /= 360; s /= 100; v /= 100;
    var r, g, b;
    var i = Math.floor(h * 6);
    var f = h * 6 - i;
    var p = v * (1 - s);
    var q = v * (1 - f * s);
    var t = v * (1 - (1 - f) * s);
    switch (i % 6) {
      case 0: r = v; g = t; b = p; break;
      case 1: r = q; g = v; b = p; break;
      case 2: r = p; g = v; b = t; break;
      case 3: r = p; g = q; b = v; break;
      case 4: r = t; g = p; b = v; break;
      case 5: r = v; g = p; b = q; break;
    }
    return { r: Math.round(r * 255), g: Math.round(g * 255), b: Math.round(b * 255) };
  }

  /* ── Parse input ─────────────────────────────────────────── */

  function parseColor(str) {
    str = str.trim();
    var m;

    /* HEX */
    if (/^#?[0-9a-fA-F]{3,8}$/.test(str)) {
      var hex = str.startsWith('#') ? str : '#' + str;
      return hexToRgb(hex);
    }

    /* rgb(r, g, b) */
    m = str.match(/rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/);
    if (m) return { r: parseInt(m[1]), g: parseInt(m[2]), b: parseInt(m[3]) };

    /* hsl(h, s%, l%) */
    m = str.match(/hsla?\(\s*(\d+)\s*,\s*(\d+)%?\s*,\s*(\d+)%?/);
    if (m) return hslToRgb(parseInt(m[1]), parseInt(m[2]), parseInt(m[3]));

    /* hsv(h, s%, v%) */
    m = str.match(/hsva?\(\s*(\d+)\s*,\s*(\d+)%?\s*,\s*(\d+)%?/);
    if (m) return hsvToRgb(parseInt(m[1]), parseInt(m[2]), parseInt(m[3]));

    return null;
  }

  /* ── Update all fields ───────────────────────────────────── */

  function update(rgb) {
    if (!rgb) return;
    var r = rgb.r, g = rgb.g, b = rgb.b;
    var hex = rgbToHex(r, g, b);
    var hsl = rgbToHsl(r, g, b);
    var hsv = rgbToHsv(r, g, b);

    if (hexField) hexField.value = hex.toUpperCase();
    if (rgbField) rgbField.value = 'rgb(' + r + ', ' + g + ', ' + b + ')';
    if (hslField) hslField.value = 'hsl(' + hsl.h + ', ' + hsl.s + '%, ' + hsl.l + '%)';
    if (hsvField) hsvField.value = 'hsv(' + hsv.h + ', ' + hsv.s + '%, ' + hsv.v + '%)';
    if (cssField) cssField.value = hex;
    if (rgbaField) rgbaField.value = 'rgba(' + r + ', ' + g + ', ' + b + ', 1)';

    if (pickerEl) pickerEl.value = hex;
    if (previewEl) previewEl.style.background = hex;

    if (rSlider) { rSlider.value = r; document.getElementById('tc-cc-r-val').textContent = r; }
    if (gSlider) { gSlider.value = g; document.getElementById('tc-cc-g-val').textContent = g; }
    if (bSlider) { bSlider.value = b; document.getElementById('tc-cc-b-val').textContent = b; }
  }

  /* ── Input events ────────────────────────────────────────── */

  var debounce;
  inputEl.addEventListener('input', function () {
    clearTimeout(debounce);
    debounce = setTimeout(function () {
      var rgb = parseColor(inputEl.value);
      if (rgb) update(rgb);
    }, 200);
  });

  if (pickerEl) {
    pickerEl.addEventListener('input', function () {
      var rgb = hexToRgb(pickerEl.value);
      if (rgb) { update(rgb); inputEl.value = pickerEl.value; }
    });
  }

  /* RGB sliders */
  [rSlider, gSlider, bSlider].forEach(function (sl) {
    if (sl) {
      sl.addEventListener('input', function () {
        var r = parseInt(rSlider.value, 10);
        var g = parseInt(gSlider.value, 10);
        var b = parseInt(bSlider.value, 10);
        var hex = rgbToHex(r, g, b);
        inputEl.value = hex;
        update({ r: r, g: g, b: b });
      });
    }
  });

  /* ── Copy buttons ────────────────────────────────────────── */

  document.querySelectorAll('.tc-cc-copy-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var targetId = btn.getAttribute('data-target');
      var field = document.getElementById(targetId);
      if (field && field.value) {
        TCTP.copyText(field.value);
        TCTP.toast('Copied ' + field.value, '✅');
      }
    });
  });

  /* ── Initial conversion ──────────────────────────────────── */

  var rgb = parseColor(inputEl.value);
  if (rgb) update(rgb);

})();
