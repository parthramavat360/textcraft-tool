/**
 * Code Formatter — Beautify/Minify HTML, CSS, JS, XML, SQL
 * @package TextCraft_Tools_Pro
 */
(function () {
  'use strict';

  var formatBtn = document.getElementById('tc-cf-format');
  if (!formatBtn) return;

  var inputEl   = document.getElementById('tc-cf-input');
  var outputEl  = document.getElementById('tc-cf-output');
  var lang      = 'html';
  var action    = 'beautify';

  /* ── Mode cards ─────────────────────────────────────────── */

  document.querySelectorAll('.tc-cf-lang-modes .tc-rsz-mode-card').forEach(function (card) {
    card.addEventListener('click', function () {
      document.querySelectorAll('.tc-cf-lang-modes .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
      card.classList.add('sel');
      lang = card.getAttribute('data-val') || 'html';
    });
  });

  document.querySelectorAll('.tc-cf-action-modes .tc-rsz-mode-card').forEach(function (card) {
    card.addEventListener('click', function () {
      document.querySelectorAll('.tc-cf-action-modes .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
      card.classList.add('sel');
      action = card.getAttribute('data-val') || 'beautify';
    });
  });

  /* ── Formatters ─────────────────────────────────────────── */

  function beautifyHTML(code) {
    var formatted = '';
    var indent = 0;
    var tab = '  ';
    code = code.replace(/>\s*</g, '>\n<').replace(/\n\s*\n/g, '\n').trim();
    var lines = code.split('\n');
    lines.forEach(function (line) {
      line = line.trim();
      if (!line) return;
      if (/^<\/\w/.test(line)) indent = Math.max(0, indent - 1);
      formatted += tab.repeat(indent) + line + '\n';
      if (/^<\w[^>]*[^\/]>$/.test(line) && !/^<(br|hr|img|input|meta|link|source|area|base|col|embed|param|track|wbr)/i.test(line)) indent++;
    });
    return formatted.trim();
  }

  function beautifyCSS(code) {
    code = code.replace(/\s*{\s*/g, ' {\n  ').replace(/\s*}\s*/g, '\n}\n').replace(/;\s*/g, ';\n  ').replace(/\n  \n/g, '\n');
    return code.trim();
  }

  function beautifyJS(code) {
    code = code.replace(/;\s*/g, ';\n').replace(/{\s*/g, ' {\n  ').replace(/}\s*/g, '\n}\n');
    return code.trim();
  }

  function beautifyXML(code) {
    var formatted = '';
    var indent = 0;
    var tab = '  ';
    code = code.replace(/>\s*</g, '>\n<').trim();
    var lines = code.split('\n');
    lines.forEach(function (line) {
      line = line.trim();
      if (!line) return;
      if (/^<\//.test(line)) indent = Math.max(0, indent - 1);
      formatted += tab.repeat(indent) + line + '\n';
      if (/^<[^\/?][^>]*[^\/]>/.test(line)) indent++;
    });
    return formatted.trim();
  }

  function beautifySQL(code) {
    var keywords = ['SELECT','FROM','WHERE','AND','OR','JOIN','LEFT','RIGHT','INNER','OUTER','ON','GROUP BY','ORDER BY','HAVING','LIMIT','INSERT','INTO','VALUES','UPDATE','SET','DELETE','CREATE','TABLE','ALTER','DROP','INDEX','UNION','ALL','DISTINCT','AS','IN','NOT','NULL','IS','BETWEEN','LIKE','EXISTS','CASE','WHEN','THEN','ELSE','END','OFFSET','FETCH','NEXT','ROWS','ONLY'];
    var result = code.replace(/\s+/g, ' ').trim();
    keywords.forEach(function (kw) {
      var regex = new RegExp('\\b' + kw + '\\b', 'gi');
      result = result.replace(regex, '\n' + kw);
    });
    return result.replace(/^\n/, '').trim();
  }

  function minify(code) {
    return code.replace(/\s+/g, ' ').replace(/\s*([{}();,=:>])\s*/g, '$1').replace(/\s*([{}();,=:>])\s*/g, '$1').trim();
  }

  function format(code) {
    if (!code.trim()) return '';
    if (action === 'minify') return minify(code);
    switch (lang) {
      case 'html': return beautifyHTML(code);
      case 'css': return beautifyCSS(code);
      case 'js': return beautifyJS(code);
      case 'xml': return beautifyXML(code);
      case 'sql': return beautifySQL(code);
      default: return code;
    }
  }

  /* ── Actions ────────────────────────────────────────────── */

  formatBtn.addEventListener('click', function () {
    var code = inputEl ? inputEl.value : '';
    var result = format(code);
    if (outputEl) outputEl.textContent = result || 'No code to format';
    var origLen = code.length;
    var newLen = result.length;
    var pct = origLen > 0 ? Math.round((1 - newLen / origLen) * 100) : 0;
    TCTP.toast(action === 'minify' ? 'Minified: ' + pct + '% smaller' : 'Formatted!', '✅');
  });

  var copyBtn = document.getElementById('tc-cf-copy');
  if (copyBtn) {
    copyBtn.addEventListener('click', function () {
      var text = outputEl ? outputEl.textContent : '';
      if (!text || text === 'No code to format') { TCTP.toast('Format first.', '⚠️'); return; }
      TCTP.copyText(text);
      TCTP.toast('Copied!', '✅');
    });
  }

  var clearBtn = document.getElementById('tc-cf-clear');
  if (clearBtn) {
    clearBtn.addEventListener('click', function () {
      if (inputEl) inputEl.value = '';
      if (outputEl) outputEl.textContent = 'Paste code and click Format';
    });
  }
})();
