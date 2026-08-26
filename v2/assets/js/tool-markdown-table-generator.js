/**
 * Markdown Table Generator — visual table builder
 * @package TextCraft_Tools_Pro
 */
(function () {
  'use strict';

  var gridEl = document.getElementById('tc-mtg-grid');
  if (!gridEl) return;

  var colsSelect = document.getElementById('tc-mtg-cols');
  var rowsSelect = document.getElementById('tc-mtg-rows');
  var alignSelect = document.getElementById('tc-mtg-align');
  var outputEl = document.getElementById('tc-mtg-output');
  var lastMd = '';

  function buildGrid() {
    var cols = parseInt(colsSelect.value) || 3;
    var rows = parseInt(rowsSelect.value) || 3;
    var html = '<table class="tc-mtg-table"><thead><tr>';
    for (var c = 0; c < cols; c++) {
      html += '<th><input class="tc-textfield tc-mtg-cell" data-r="0" data-c="' + c + '" value="Header ' + (c + 1) + '"></th>';
    }
    html += '</tr></thead><tbody>';
    for (var r = 1; r < rows; r++) {
      html += '<tr>';
      for (var c = 0; c < cols; c++) {
        html += '<td><input class="tc-textfield tc-mtg-cell" data-r="' + r + '" data-c="' + c + '" value="Cell ' + r + ',' + (c + 1) + '"></td>';
      }
      html += '</tr>';
    }
    html += '</tbody></table>';
    gridEl.innerHTML = html;
  }

  function generateMd() {
    var cols = parseInt(colsSelect.value) || 3;
    var rows = parseInt(rowsSelect.value) || 3;
    var align = alignSelect.value || 'left';
    var sep = align === 'center' ? ':---:' : align === 'right' ? '---:' : ':---';

    var cells = {};
    gridEl.querySelectorAll('.tc-mtg-cell').forEach(function (inp) {
      var r = inp.getAttribute('data-r');
      var c = inp.getAttribute('data-c');
      cells[r + '-' + c] = inp.value.trim() || ' ';
    });

    var lines = [];
    /* Header row */
    var hdr = '|';
    for (var c = 0; c < cols; c++) hdr += ' ' + (cells['0-' + c] || ' ') + ' |';
    lines.push(hdr);
    /* Separator */
    var sepLine = '|';
    for (var c = 0; c < cols; c++) sepLine += ' ' + sep + ' |';
    lines.push(sepLine);
    /* Data rows */
    for (var r = 1; r < rows; r++) {
      var row = '|';
      for (var c = 0; c < cols; c++) row += ' ' + (cells[r + '-' + c] || ' ') + ' |';
      lines.push(row);
    }

    lastMd = lines.join('\n');
    if (outputEl) outputEl.textContent = lastMd;
  }

  colsSelect.addEventListener('change', buildGrid);
  rowsSelect.addEventListener('change', buildGrid);
  alignSelect.addEventListener('change', generateMd);

  var genBtn = document.getElementById('tc-mtg-generate');
  if (genBtn) genBtn.addEventListener('click', function () {
    generateMd();
    TCTP.toast('Table generated!', '✅');
  });

  var copyBtn = document.getElementById('tc-mtg-copy');
  if (copyBtn) copyBtn.addEventListener('click', function () {
    generateMd();
    if (!lastMd) { TCTP.toast('Generate first.', '⚠️'); return; }
    TCTP.copyText(lastMd);
    TCTP.toast('Copied!', '✅');
  });

  var dlBtn = document.getElementById('tc-mtg-download');
  if (dlBtn) dlBtn.addEventListener('click', function () {
    generateMd();
    if (!lastMd) { TCTP.toast('Generate first.', '⚠️'); return; }
    var blob = new Blob([lastMd], { type: 'text/markdown' });
    var a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'table.md';
    a.click();
    URL.revokeObjectURL(a.href);
    TCTP.toast('Downloaded!', '✅');
  });

  buildGrid();
})();
