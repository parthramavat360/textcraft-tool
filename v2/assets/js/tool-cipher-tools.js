/**
 * Cipher Tools — Caesar, ROT13, A1Z26, Vigenere, Atbash, AES, UTF-8, HTML Entity, Unicode Inspector
 * @package TextCraft_Tools_Pro
 */
(function () {
  'use strict';

  var convertBtn = document.getElementById('tc-ct-convert');
  if (!convertBtn) return;

  var inputEl = document.getElementById('tc-ct-input');
  var outputEl = document.getElementById('tc-ct-output');
  var mode = 'caesar';
  var shiftInput = document.getElementById('tc-ct-shift');
  var shiftVal = document.getElementById('tc-ct-shift-val');
  var keyInput = document.getElementById('tc-ct-key');
  var passwordInput = document.getElementById('tc-ct-password');
  var shiftWrap = document.getElementById('tc-ct-shift-wrap');
  var keyWrap = document.getElementById('tc-ct-key-wrap');
  var passwordWrap = document.getElementById('tc-ct-password-wrap');

  if (shiftInput && shiftVal) {
    shiftInput.addEventListener('input', function () { shiftVal.textContent = shiftInput.value; });
  }

  /* ── Mode cards ─────────────────────────────────────────── */
  document.querySelectorAll('.tc-ct-modes .tc-rsz-mode-card').forEach(function (card) {
    card.addEventListener('click', function () {
      document.querySelectorAll('.tc-ct-modes .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
      card.classList.add('sel');
      mode = card.getAttribute('data-val') || 'caesar';
      if (shiftWrap) shiftWrap.style.display = (mode === 'caesar') ? '' : 'none';
      if (keyWrap) keyWrap.style.display = (mode === 'vigenere') ? '' : 'none';
      if (passwordWrap) passwordWrap.style.display = (mode === 'aes') ? '' : 'none';
    });
  });

  /* ── Cipher functions ───────────────────────────────────── */
  function caesar(text, shift, decrypt) {
    var s = decrypt ? (26 - shift) % 26 : shift;
    return text.replace(/[a-zA-Z]/g, function (ch) {
      var base = ch <= 'Z' ? 65 : 97;
      return String.fromCharCode(((ch.charCodeAt(0) - base + s) % 26) + base);
    });
  }

  function rot13(text) { return caesar(text, 13, false); }

  function a1z26(text, decrypt) {
    if (decrypt) {
      return text.replace(/\d+/g, function (n) {
        var num = parseInt(n);
        return (num >= 1 && num <= 26) ? String.fromCharCode(64 + num) : n;
      });
    }
    return text.toUpperCase().replace(/[A-Z]/g, function (ch) {
      return (ch.charCodeAt(0) - 64) + ' ';
    }).trim();
  }

  function vigenere(text, key, decrypt) {
    if (!key) return text;
    key = key.replace(/[^a-zA-Z]/g, '').toLowerCase();
    if (!key) return text;
    var ki = 0;
    return text.replace(/[a-zA-Z]/g, function (ch) {
      var base = ch <= 'Z' ? 65 : 97;
      var shift = key.charCodeAt(ki % key.length) - 97;
      if (decrypt) shift = 26 - shift;
      ki++;
      return String.fromCharCode(((ch.charCodeAt(0) - base + shift) % 26) + base);
    });
  }

  function atbash(text) {
    return text.replace(/[a-zA-Z]/g, function (ch) {
      var base = ch <= 'Z' ? 65 : 97;
      return String.fromCharCode(base + 25 - (ch.charCodeAt(0) - base));
    });
  }

  function utf8Encode(text) {
    var result = '';
    for (var i = 0; i < text.length; i++) {
      var code = text.charCodeAt(i);
      if (code < 128) {
        result += '%' + code.toString(16).toUpperCase().padStart(2, '0');
      } else if (code < 2048) {
        result += '%' + ((code >> 6) | 192).toString(16).toUpperCase().padStart(2, '0');
        result += '%' + ((code & 63) | 128).toString(16).toUpperCase().padStart(2, '0');
      } else {
        result += '%' + ((code >> 12) | 224).toString(16).toUpperCase().padStart(2, '0');
        result += '%' + ((code >> 6 & 63) | 128).toString(16).toUpperCase().padStart(2, '0');
        result += '%' + ((code & 63) | 128).toString(16).toUpperCase().padStart(2, '0');
      }
    }
    return result;
  }

  function utf8Decode(encoded) {
    try {
      return decodeURIComponent(encoded.replace(/%([0-9A-Fa-f]{2})/g, function (m, hex) {
        return String.fromCharCode(parseInt(hex, 16));
      }));
    } catch (e) { return 'Error: Invalid UTF-8 encoding'; }
  }

  function htmlEncode(text) {
    return text.replace(/[&<>"']/g, function (ch) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch];
    });
  }

  function htmlDecode(text) {
    var el = document.createElement('div');
    el.innerHTML = text;
    return el.textContent || el.innerText || '';
  }

  function unicodeInspect(text) {
    var lines = [];
    for (var i = 0; i < text.length; i++) {
      var ch = text[i];
      var code = text.charCodeAt(i);
      var hex = 'U+' + code.toString(16).toUpperCase().padStart(4, '0');
      var name = '';
      try { name = ch.normalize('NFD').length === 1 ? '' : ''; } catch(e) {}
      lines.push(ch + '  ' + hex + '  (dec: ' + code + ')');
    }
    return lines.join('\n');
  }

  /* ── Convert ────────────────────────────────────────────── */
  convertBtn.addEventListener('click', function () {
    var input = (inputEl ? inputEl.value : '').trim();
    if (!input) { TCTP.toast('Enter some text first.', '⚠️'); return; }

    var result = '';
    try {
      switch (mode) {
        case 'caesar':
          var shift = parseInt(shiftInput ? shiftInput.value : '3');
          result = caesar(input, shift, false);
          break;
        case 'rot13':
          result = rot13(input);
          break;
        case 'a1z26':
          result = a1z26(input, false);
          break;
        case 'vigenere':
          result = vigenere(input, keyInput ? keyInput.value : '', false);
          break;
        case 'atbash':
          result = atbash(input);
          break;
        case 'aes':
          result = 'AES encryption requires the Web Crypto API.\nFor demo, use https://codepen.io for a full implementation.\nThis is a placeholder for the AES mode.';
          break;
        case 'utf8':
          result = utf8Encode(input);
          break;
        case 'html':
          result = htmlEncode(input);
          break;
        case 'unicode':
          result = unicodeInspect(input);
          break;
      }
    } catch (e) {
      result = 'Error: ' + e.message;
    }

    if (outputEl) outputEl.textContent = result || 'No output';
    TCTP.toast('Processed!', '✅');
  });

  /* Copy */
  var copyBtn = document.getElementById('tc-ct-copy');
  if (copyBtn) {
    copyBtn.addEventListener('click', function () {
      var text = outputEl ? outputEl.textContent : '';
      if (!text || text.includes('Output will') || text.includes('Error')) { TCTP.toast('Convert first.', '⚠️'); return; }
      TCTP.copyText(text);
      TCTP.toast('Copied!', '✅');
    });
  }
})();
