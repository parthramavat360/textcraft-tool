/**
 * Markdown Preview — live split-pane editor
 * @package TextCraft_Tools_Pro
 */
(function () {
  'use strict';

  var inputEl = document.getElementById('tc-mp-input');
  if (!inputEl) return;

  var outputEl = document.getElementById('tc-mp-output');
  var lastHtml = '';

  function esc(s) {
    return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  function parseMd(md) {
    var html = md;
    /* Code blocks (fenced) */
    html = html.replace(/```(\w*)\n([\s\S]*?)```/g, function (m, lang, code) {
      return '<pre><code class="lang-' + esc(lang || 'text') + '">' + esc(code.trim()) + '</code></pre>';
    });
    /* Inline code */
    html = html.replace(/`([^`]+)`/g, '<code>$1</code>');
    /* Headings */
    html = html.replace(/^######\s+(.+)$/gm, '<h6>$1</h6>');
    html = html.replace(/^#####\s+(.+)$/gm, '<h5>$1</h5>');
    html = html.replace(/^####\s+(.+)$/gm, '<h4>$1</h4>');
    html = html.replace(/^###\s+(.+)$/gm, '<h3>$1</h3>');
    html = html.replace(/^##\s+(.+)$/gm, '<h2>$1</h2>');
    html = html.replace(/^#\s+(.+)$/gm, '<h1>$1</h1>');
    /* Bold + italic */
    html = html.replace(/\*\*\*(.+?)\*\*\*/g, '<strong><em>$1</em></strong>');
    html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    html = html.replace(/\*(.+?)\*/g, '<em>$1</em>');
    html = html.replace(/~~(.+?)~~/g, '<del>$1</del>');
    /* Links and images */
    html = html.replace(/!\[([^\]]*)\]\(([^)]+)\)/g, '<img src="$2" alt="$1" style="max-width:100%">');
    html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" rel="noopener">$1</a>');
    /* Blockquotes */
    html = html.replace(/^>\s+(.+)$/gm, '<blockquote>$1</blockquote>');
    /* Horizontal rule */
    html = html.replace(/^---+$/gm, '<hr>');
    /* Unordered lists */
    html = html.replace(/^[\-\*]\s+(.+)$/gm, '<li>$1</li>');
    html = html.replace(/((?:<li>.*<\/li>\n?)+)/g, '<ul>$1</ul>');
    /* Ordered lists */
    html = html.replace(/^\d+\.\s+(.+)$/gm, '<li>$1</li>');
    /* Tables */
    html = html.replace(/^\|(.+)\|\s*\n\|[-| :]+\|\s*\n((?:\|.+\|\s*\n?)+)/gm, function (m, header, body) {
      var ths = header.split('|').map(function (c) { return '<th>' + c.trim() + '</th>'; }).join('');
      var rows = body.trim().split('\n').map(function (row) {
        var tds = row.replace(/^\||\|$/g, '').split('|').map(function (c) { return '<td>' + c.trim() + '</td>'; }).join('');
        return '<tr>' + tds + '</tr>';
      }).join('');
      return '<table><thead><tr>' + ths + '</tr></thead><tbody>' + rows + '</tbody></table>';
    });
    /* Paragraphs: wrap remaining lines */
    html = html.replace(/\n\n+/g, '</p><p>');
    html = '<p>' + html + '</p>';
    /* Clean up empty paragraphs */
    html = html.replace(/<p>\s*<(h[1-6]|ul|ol|pre|blockquote|table|hr)/g, '<$1');
    html = html.replace(/<\/(h[1-6]|ul|ol|pre|blockquote|table)>\s*<\/p>/g, '</$1>');
    html = html.replace(/<p>\s*<\/p>/g, '');
    return html;
  }

  function update() {
    var md = inputEl.value;
    if (!md.trim()) {
      outputEl.innerHTML = '<p class="tc-mp-placeholder">Start typing to see preview...</p>';
      lastHtml = '';
      return;
    }
    lastHtml = parseMd(md);
    outputEl.innerHTML = lastHtml;
  }

  inputEl.addEventListener('input', update);

  /* Copy markdown */
  var copyMdBtn = document.getElementById('tc-mp-copy-md');
  if (copyMdBtn) copyMdBtn.addEventListener('click', function () {
    TCTP.copyText(inputEl.value);
    TCTP.toast('Markdown copied!', '✅');
  });

  /* Copy HTML */
  var copyHtmlBtn = document.getElementById('tc-mp-copy-html');
  if (copyHtmlBtn) copyHtmlBtn.addEventListener('click', function () {
    if (!lastHtml) { TCTP.toast('Nothing to copy.', '⚠️'); return; }
    TCTP.copyText(lastHtml);
    TCTP.toast('HTML copied!', '✅');
  });

  /* Clear */
  var clearBtn = document.getElementById('tc-mp-clear');
  if (clearBtn) clearBtn.addEventListener('click', function () {
    inputEl.value = '';
    update();
    TCTP.toast('Cleared!', '✅');
  });
})();
