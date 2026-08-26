/**
 * Words to Number — convert English number words to digits
 * @package TextCraft_Tools_Pro
 */
(function () {
  'use strict';

  var convertBtn = document.getElementById('tc-wn-convert');
  if (!convertBtn) return;

  var inputEl  = document.getElementById('tc-wn-input');
  var outputEl = document.getElementById('tc-wn-output');

  /* ── Word-to-number map ─────────────────────────────────── */

  var wordMap = {
    'zero':0,'one':1,'two':2,'three':3,'four':4,'five':5,'six':6,'seven':7,'eight':8,'nine':9,
    'ten':10,'eleven':11,'twelve':12,'thirteen':13,'fourteen':14,'fifteen':15,'sixteen':16,
    'seventeen':17,'eighteen':18,'nineteen':19,'twenty':20,'thirty':30,'forty':40,'fifty':50,
    'sixty':60,'seventy':70,'eighty':80,'ninety':90,
    'hundred':100,'thousand':1000,'million':1e6,'billion':1e9,'trillion':1e12,
    'quadrillion':1e15,'quintillion':1e18,
    'first':1,'second':2,'third':3,'fourth':4,'fifth':5,'sixth':6,'seventh':7,'eighth':8,'ninth':9,'tenth':10
  };

  function wordsToNumber(text) {
    text = text.toLowerCase().replace(/[^a-z\s\-]/g, '').replace(/\s+/g, ' ').trim();

    /* Handle "minus" */
    var negative = false;
    if (text.startsWith('minus ')) { negative = true; text = text.slice(6); }
    else if (text.startsWith('negative ')) { negative = true; text = text.slice(9); }

    var words = text.split(' ');
    var current = 0;
    var total = 0;
    var result = 0;

    for (var i = 0; i < words.length; i++) {
      var w = words[i];
      if (w === 'and') continue;
      if (w === 'a' || w === 'an') { current = 1; continue; }

      var val = wordMap[w];
      if (val === undefined) {
        /* Check hyphenated words like "twenty-one" */
        var parts = w.split('-');
        if (parts.length === 2) {
          val = (wordMap[parts[0]] || 0) + (wordMap[parts[1]] || 0);
        }
        if (val === undefined || val === 0) continue;
      }

      if (val >= 1e15) { current = current * val; total += current; current = 0; }
      else if (val >= 1e12) { current = current * val; total += current; current = 0; }
      else if (val >= 1e9) { current = current * val; total += current; current = 0; }
      else if (val >= 1e6) { current = current * val; total += current; current = 0; }
      else if (val >= 1000) { current = current * val; total += current; current = 0; }
      else if (val >= 100) { current = current * val; }
      else { current += val; }
    }

    result = total + current;
    if (negative) result = -result;

    return result;
  }

  /* ── Convert ────────────────────────────────────────────── */

  function convert() {
    var raw = (inputEl ? inputEl.value : '').trim();
    if (!raw) { TCTP.toast('Enter some number words.', '⚠️'); return; }

    var num = wordsToNumber(raw);
    if (isNaN(num)) { TCTP.toast('Could not parse number words.', '⚠️'); return; }

    var numStr = num.toLocaleString('en-US');

    if (outputEl) {
      outputEl.innerHTML = '<div class="tc-wn-result-text">' + numStr + '</div>';
    }

    var sO = document.getElementById('tc-stat-orig');
    var sC = document.getElementById('tc-stat-comp');
    var sS = document.getElementById('tc-stat-saved');
    if (sO) sO.textContent = raw.slice(0, 20) + (raw.length > 20 ? '...' : '');
    if (sC) sC.textContent = numStr;
    if (sS) sS.textContent = numStr.replace(/[^0-9\-]/g, '').length;

    var preview = document.getElementById('tc-wn-preview');
    if (preview) {
      preview.innerHTML = '<div style="font-size:24px;font-weight:700;color:var(--accent,#2563eb)">' + numStr + '</div>';
    }

    var chip = document.getElementById('tc-status-chip');
    if (chip) chip.textContent = 'Done';

    TCTP.switchToResultTab();
    TCTP.toast('Converted: ' + numStr, '✅');
  }

  convertBtn.addEventListener('click', convert);

  /* Live convert */
  if (inputEl) {
    inputEl.addEventListener('input', function () {
      if (inputEl.value.trim()) convert();
    });
  }

  /* Copy */
  var copyBtn = document.getElementById('tc-wn-copy');
  if (copyBtn) {
    copyBtn.addEventListener('click', function () {
      var out = outputEl ? outputEl.textContent : '';
      if (!out || out.includes('Result will')) { TCTP.toast('Convert first.', '⚠️'); return; }
      TCTP.copyText(out.trim());
      TCTP.toast('Copied!', '✅');
    });
  }

  convert();
})();
