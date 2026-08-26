/**
 * Text Diff — Side-by-side text comparison
 * Uses a simple LCS-based diff algorithm
 * @package TextCraft_Tools_Pro
 */
(function () {
  'use strict';

  var compareBtn = document.getElementById('tc-diff-compare');
  if (!compareBtn) return;

  var leftEl      = document.getElementById('tc-diff-left');
  var rightEl     = document.getElementById('tc-diff-right');
  var outputEl    = document.getElementById('tc-diff-output');
  var resultEl    = document.getElementById('tc-diff-result');
  var statsEl     = document.getElementById('tc-diff-stats');
  var ignoreCase  = document.getElementById('tc-diff-ignore-case');
  var ignoreSpace = document.getElementById('tc-diff-ignore-space');
  var wordLevel   = document.getElementById('tc-diff-word-level');

  /* ── LCS-based line diff ───────────────────────────────── */

  function lcs(a, b) {
    var m = a.length, n = b.length;
    var dp = [];
    for (var i = 0; i <= m; i++) {
      dp[i] = [];
      for (var j = 0; j <= n; j++) {
        if (i === 0 || j === 0) dp[i][j] = 0;
        else if (a[i-1] === b[j-1]) dp[i][j] = dp[i-1][j-1] + 1;
        else dp[i][j] = Math.max(dp[i-1][j], dp[i][j-1]);
      }
    }
    /* Backtrack */
    var result = [];
    var i = m, j = n;
    while (i > 0 && j > 0) {
      if (a[i-1] === b[j-1]) { result.unshift({ type: 'equal', left: i-1, right: j-1 }); i--; j--; }
      else if (dp[i-1][j] >= dp[i][j-1]) i--;
      else j--;
    }
    return result;
  }

  function computeDiff(leftText, rightText) {
    var leftLines  = leftText.split('\n');
    var rightLines = rightText.split('\n');

    var normalize = function(s) {
      if (ignoreCase && ignoreCase.checked) s = s.toLowerCase();
      if (ignoreSpace && ignoreSpace.checked) s = s.replace(/\s+/g, ' ').trim();
      return s;
    };

    var normLeft  = leftLines.map(normalize);
    var normRight = rightLines.map(normalize);

    var matches = lcs(normLeft, normRight);

    /* Build aligned diff */
    var result = [];
    var li = 0, ri = 0;
    var matchIdx = 0;

    while (li < leftLines.length || ri < rightLines.length) {
      if (matchIdx < matches.length) {
        var m = matches[matchIdx];
        /* Lines before match are removed/added */
        while (li < m.left) {
          result.push({ type: 'removed', leftNum: li + 1, rightNum: null, left: leftLines[li], right: '' });
          li++;
        }
        while (ri < m.right) {
          result.push({ type: 'added', leftNum: null, rightNum: ri + 1, left: '', right: rightLines[ri] });
          ri++;
        }
        /* Equal line */
        result.push({ type: 'equal', leftNum: li + 1, rightNum: ri + 1, left: leftLines[li], right: rightLines[ri] });
        li++; ri++; matchIdx++;
      } else {
        /* Remaining lines */
        while (li < leftLines.length) {
          result.push({ type: 'removed', leftNum: li + 1, rightNum: null, left: leftLines[li], right: '' });
          li++;
        }
        while (ri < rightLines.length) {
          result.push({ type: 'added', leftNum: null, rightNum: ri + 1, left: '', right: rightLines[ri] });
          ri++;
        }
      }
    }

    return result;
  }

  /* ── Word-level diff highlight ─────────────────────────── */

  function wordDiff(oldLine, newLine) {
    var oldWords = oldLine.split(/(\s+)/);
    var newWords = newLine.split(/(\s+)/);
    var matches = lcs(oldWords, newWords);

    var oldResult = [], newResult = [];
    var oi = 0, ni = 0, mi = 0;

    while (oi < oldWords.length || ni < newWords.length) {
      if (mi < matches.length) {
        var m = matches[mi];
        while (oi < m.left) { oldResult.push('<del>' + escapeHtml(oldWords[oi]) + '</del>'); oi++; }
        while (ni < m.right) { newResult.push('<ins>' + escapeHtml(newWords[ni]) + '</ins>'); ni++; }
        oldResult.push('<span>' + escapeHtml(oldWords[oi]) + '</span>');
        newResult.push('<span>' + escapeHtml(newWords[ni]) + '</span>');
        oi++; ni++; mi++;
      } else {
        while (oi < oldWords.length) { oldResult.push('<del>' + escapeHtml(oldWords[oi]) + '</del>'); oi++; }
        while (ni < newWords.length) { newResult.push('<ins>' + escapeHtml(newWords[ni]) + '</ins>'); ni++; }
      }
    }

    return { old: oldResult.join(''), new: newResult.join('') };
  }

  function escapeHtml(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }

  /* ── Render ────────────────────────────────────────────── */

  function renderDiff(diff) {
    if (!outputEl) return;
    outputEl.innerHTML = '';

    var table = document.createElement('div');
    table.className = 'tc-diff-table';

    diff.forEach(function (line) {
      var row = document.createElement('div');
      row.className = 'tc-diff-row tc-diff-row--' + line.type;

      var leftNum  = document.createElement('span');
      leftNum.className = 'tc-diff-num';
      leftNum.textContent = line.leftNum || '';

      var rightNum = document.createElement('span');
      rightNum.className = 'tc-diff-num';
      rightNum.textContent = line.rightNum || '';

      var marker = document.createElement('span');
      marker.className = 'tc-diff-marker';
      if (line.type === 'added') marker.textContent = '+';
      else if (line.type === 'removed') marker.textContent = '−';
      else marker.textContent = ' ';

      var content = document.createElement('span');
      content.className = 'tc-diff-content';

      if (line.type === 'equal') {
        content.textContent = line.left;
      } else if (line.type === 'removed') {
        if (wordLevel && wordLevel.checked && line.left && line.right) {
          /* Can't happen in removed lines, just show deleted */
          content.innerHTML = '<del>' + escapeHtml(line.left) + '</del>';
        } else {
          content.innerHTML = '<del>' + escapeHtml(line.left) + '</del>';
        }
      } else {
        if (wordLevel && wordLevel.checked && line.left && line.right) {
          var wd = wordDiff(line.left, line.right);
          content.innerHTML = '<ins>' + wd.new + '</ins>';
        } else {
          content.innerHTML = '<ins>' + escapeHtml(line.right) + '</ins>';
        }
      }

      row.appendChild(leftNum);
      row.appendChild(rightNum);
      row.appendChild(marker);
      row.appendChild(content);
      table.appendChild(row);
    });

    outputEl.appendChild(table);

    /* Stats */
    var added = diff.filter(function(d) { return d.type === 'added'; }).length;
    var removed = diff.filter(function(d) { return d.type === 'removed'; }).length;
    var unchanged = diff.filter(function(d) { return d.type === 'equal'; }).length;

    var sa = document.getElementById('tc-diff-stat-added');
    var sr = document.getElementById('tc-diff-stat-removed');
    var su = document.getElementById('tc-diff-stat-unchanged');
    if (sa) sa.textContent = added;
    if (sr) sr.textContent = removed;
    if (su) su.textContent = unchanged;

    /* Result panel */
    var sO = document.getElementById('tc-stat-orig');
    var sC = document.getElementById('tc-stat-comp');
    var sS = document.getElementById('tc-stat-saved');
    if (sO) sO.textContent = added;
    if (sC) sC.textContent = removed;
    if (sS) sS.textContent = unchanged;
    var chip = document.getElementById('tc-status-chip');
    if (chip) chip.textContent = added + removed === 0 ? 'Identical' : added + ' added, ' + removed + ' removed';

    /* Preview */
    var preview = document.getElementById('tc-diff-preview');
    if (preview) {
      preview.innerHTML = '';
      var pre = document.createElement('div');
      pre.style.cssText = 'font-size:13px;line-height:1.6;font-family:monospace';
      diff.forEach(function(d) {
        var line = document.createElement('div');
        line.style.cssText = 'padding:2px 8px;border-radius:3px;margin-bottom:1px';
        if (d.type === 'added') { line.style.background = 'rgba(16,185,129,0.15)'; line.style.color = '#6ee7b7'; }
        else if (d.type === 'removed') { line.style.background = 'rgba(239,68,68,0.15)'; line.style.color = '#fca5a5'; }
        else { line.style.color = 'var(--body,#cbd5e1)'; }
        line.textContent = (d.type === 'added' ? '+ ' : d.type === 'removed' ? '- ' : '  ') + (d.left || d.right);
        pre.appendChild(line);
      });
      preview.appendChild(pre);
    }

    TCTP.switchToResultTab();
  }

  /* ── Compare ───────────────────────────────────────────── */

  compareBtn.addEventListener('click', function () {
    var leftText  = leftEl ? leftEl.value : '';
    var rightText = rightEl ? rightEl.value : '';

    if (!leftText && !rightText) { TCTP.toast('Paste some text to compare.', '⚠️'); return; }

    var diff = computeDiff(leftText, rightText);
    resultEl.style.display = '';
    statsEl.style.display = '';
    renderDiff(diff);
    TCTP.toast('Comparison complete!', '✅');
  });

  /* ── Swap ──────────────────────────────────────────────── */

  var swapBtn = document.getElementById('tc-diff-swap');
  if (swapBtn) {
    swapBtn.addEventListener('click', function () {
      var tmp = leftEl.value;
      leftEl.value = rightEl.value;
      rightEl.value = tmp;
    });
  }

  /* ── Clear ─────────────────────────────────────────────── */

  var clearBtn = document.getElementById('tc-diff-clear');
  if (clearBtn) {
    clearBtn.addEventListener('click', function () {
      leftEl.value = '';
      rightEl.value = '';
      resultEl.style.display = 'none';
      statsEl.style.display = 'none';
    });
  }

})();
