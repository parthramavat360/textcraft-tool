/**
 * Developer Utilities — Hex↔Text, Binary↔Dec, Slugify, UTM, JWT, HTML→MD
 * @package TextCraft_Tools_Pro
 */
(function () {
  'use strict';

  var convertBtn = document.getElementById('tc-du-convert');
  if (!convertBtn) return;

  var inputEl    = document.getElementById('tc-du-input');
  var outputEl   = document.getElementById('tc-du-output');
  var inputLabel = document.getElementById('tc-du-input-label');
  var outputLabel = document.getElementById('tc-du-output-label');
  var utmFields  = document.getElementById('tc-du-utm-fields');
  var inputArea  = document.querySelector('.tc-du-inputs');
  var mode = 'hex-text';

  /* ── Mode cards ─────────────────────────────────────────── */

  document.querySelectorAll('.tc-du-modes .tc-rsz-mode-card').forEach(function (card) {
    card.addEventListener('click', function () {
      document.querySelectorAll('.tc-du-modes .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
      card.classList.add('sel');
      mode = card.getAttribute('data-val') || 'hex-text';
      var isUtm = mode === 'utm';
      if (utmFields) utmFields.style.display = isUtm ? '' : 'none';
      if (inputArea) inputArea.style.display = isUtm ? 'none' : '';
      var labels = {
        'hex-text': ['Hex values (e.g. 48 65 6C 6C 6F)', 'Text'],
        'bin-dec': ['Binary (e.g. 01001000 01100101)', 'Decimal'],
        'slugify': ['Text to slugify', 'URL slug'],
        'jwt': ['Paste JWT token', 'Decoded payload'],
        'html-md': ['HTML code', 'Markdown']
      };
      var l = labels[mode] || ['Input', 'Output'];
      if (inputLabel) inputLabel.textContent = l[0];
      if (outputLabel) outputLabel.textContent = l[1];
    });
  });

  /* ── Converters ─────────────────────────────────────────── */

  function hexToText(hex) {
    hex = hex.replace(/[^0-9a-fA-F\s]/g, '').replace(/\s+/g, '');
    var result = '';
    for (var i = 0; i < hex.length; i += 2) {
      result += String.fromCharCode(parseInt(hex.substr(i, 2), 16));
    }
    return result;
  }

  function textToHex(text) {
    var result = '';
    for (var i = 0; i < text.length; i++) {
      result += text.charCodeAt(i).toString(16).padStart(2, '0').toUpperCase() + ' ';
    }
    return result.trim();
  }

  function binToDec(bin) {
    return bin.trim().split(/\s+/).map(function (b) { return parseInt(b, 2); }).join(' ');
  }

  function decToBin(dec) {
    return dec.trim().split(/\s+/).map(function (d) { return parseInt(d).toString(2).padStart(8, '0'); }).join(' ');
  }

  function slugify(text) {
    return text.toLowerCase()
      .replace(/[^\w\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .replace(/^-|-$/g, '');
  }

  function buildUTM() {
    var url = document.getElementById('tc-du-utm-url').value.trim();
    var source = document.getElementById('tc-du-utm-source').value.trim();
    var medium = document.getElementById('tc-du-utm-medium').value.trim();
    var campaign = document.getElementById('tc-du-utm-campaign').value.trim();
    var term = document.getElementById('tc-du-utm-term').value.trim();
    var content = document.getElementById('tc-du-utm-content').value.trim();
    if (!url) return 'Please enter a URL';
    var sep = url.includes('?') ? '&' : '?';
    var params = [];
    if (source) params.push('utm_source=' + encodeURIComponent(source));
    if (medium) params.push('utm_medium=' + encodeURIComponent(medium));
    if (campaign) params.push('utm_campaign=' + encodeURIComponent(campaign));
    if (term) params.push('utm_term=' + encodeURIComponent(term));
    if (content) params.push('utm_content=' + encodeURIComponent(content));
    return params.length ? url + sep + params.join('&') : url;
  }

  function decodeJWT(token) {
    try {
      var parts = token.split('.');
      if (parts.length < 2) throw new Error('Invalid JWT format');
      var payload = JSON.parse(atob(parts[1].replace(/-/g, '+').replace(/_/g, '/')));
      var header = JSON.parse(atob(parts[0].replace(/-/g, '+').replace(/_/g, '/')));
      return JSON.stringify({ header: header, payload: payload }, null, 2);
    } catch (e) {
      return 'Error: Invalid JWT token — ' + e.message;
    }
  }

  function htmlToMarkdown(html) {
    var md = html;
    md = md.replace(/<h1[^>]*>(.*?)<\/h1>/gi, '# $1\n\n');
    md = md.replace(/<h2[^>]*>(.*?)<\/h2>/gi, '## $1\n\n');
    md = md.replace(/<h3[^>]*>(.*?)<\/h3>/gi, '### $1\n\n');
    md = md.replace(/<h4[^>]*>(.*?)<\/h4>/gi, '#### $1\n\n');
    md = md.replace(/<strong[^>]*>(.*?)<\/strong>/gi, '**$1**');
    md = md.replace(/<b[^>]*>(.*?)<\/b>/gi, '**$1**');
    md = md.replace(/<em[^>]*>(.*?)<\/em>/gi, '*$1*');
    md = md.replace(/<i[^>]*>(.*?)<\/i>/gi, '*$1*');
    md = md.replace(/<a[^>]*href="([^"]*)"[^>]*>(.*?)<\/a>/gi, '[$2]($1)');
    md = md.replace(/<img[^>]*src="([^"]*)"[^>]*alt="([^"]*)"[^>]*\/?>/gi, '![$2]($1)');
    md = md.replace(/<img[^>]*src="([^"]*)"[^>]*\/?>/gi, '![]($1)');
    md = md.replace(/<li[^>]*>(.*?)<\/li>/gi, '- $1\n');
    md = md.replace(/<br\s*\/?>/gi, '\n');
    md = md.replace(/<p[^>]*>(.*?)<\/p>/gi, '$1\n\n');
    md = md.replace(/<div[^>]*>(.*?)<\/div>/gi, '$1\n');
    md = md.replace(/<code[^>]*>(.*?)<\/code>/gi, '`$1`');
    md = md.replace(/<pre[^>]*>(.*?)<\/pre>/gi, '```\n$1\n```\n');
    md = md.replace(/<[^>]+>/g, '');
    md = md.replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"');
    md = md.replace(/\n{3,}/g, '\n\n');
    return md.trim();
  }

  /* ── Convert ────────────────────────────────────────────── */

  convertBtn.addEventListener('click', function () {
    var result = '';
    try {
      if (mode === 'hex-text') {
        var input = (inputEl ? inputEl.value : '').trim();
        /* Detect if input looks like hex or text */
        if (/^[0-9a-fA-F\s]+$/.test(input.replace(/\s/g, '')) && input.replace(/\s/g, '').length % 2 === 0 && input.replace(/\s/g, '').length > 2) {
          result = hexToText(input);
        } else {
          result = textToHex(input);
        }
      } else if (mode === 'bin-dec') {
        var input = (inputEl ? inputEl.value : '').trim();
        if (/^[01\s]+$/.test(input)) {
          result = binToDec(input);
        } else {
          result = decToBin(input);
        }
      } else if (mode === 'slugify') {
        result = slugify(inputEl ? inputEl.value : '');
      } else if (mode === 'utm') {
        result = buildUTM();
      } else if (mode === 'jwt') {
        result = decodeJWT(inputEl ? inputEl.value.trim() : '');
      } else if (mode === 'html-md') {
        result = htmlToMarkdown(inputEl ? inputEl.value : '');
      }
    } catch (e) {
      result = 'Error: ' + e.message;
    }
    if (outputEl) outputEl.textContent = result || 'No output';
    TCTP.toast('Converted!', '✅');
  });

  var copyBtn = document.getElementById('tc-du-copy');
  if (copyBtn) {
    copyBtn.addEventListener('click', function () {
      var text = outputEl ? outputEl.textContent : '';
      if (!text || text.includes('Output will') || text.includes('Error')) { TCTP.toast('Convert first.', '⚠️'); return; }
      TCTP.copyText(text);
      TCTP.toast('Copied!', '✅');
    });
  }
})();
