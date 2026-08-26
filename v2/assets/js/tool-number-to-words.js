/**
 * Number to Words — convert numbers to English words
 * @package TextCraft_Tools_Pro
 */
(function () {
  'use strict';

  var convertBtn = document.getElementById('tc-nw-convert');
  if (!convertBtn) return;

  var inputEl = document.getElementById('tc-nw-input');
  var outputEl = document.getElementById('tc-nw-output');
  var mode = 'words';
  var currency = 'USD';

  /* ── Mode cards ─────────────────────────────────────────── */

  var modeCards = document.querySelectorAll('.tc-nw-modes .tc-rsz-mode-card');
  modeCards.forEach(function (card) {
    card.addEventListener('click', function () {
      modeCards.forEach(function (c) { c.classList.remove('sel'); });
      card.classList.add('sel');
      mode = card.getAttribute('data-val') || 'words';
      var currRow = document.getElementById('tc-nw-currency-row');
      if (currRow) currRow.style.display = mode === 'currency' ? '' : 'none';
      convert();
    });
  });

  var currCards = document.querySelectorAll('.tc-nw-curr-modes .tc-rsz-mode-card');
  currCards.forEach(function (card) {
    card.addEventListener('click', function () {
      currCards.forEach(function (c) { c.classList.remove('sel'); });
      card.classList.add('sel');
      currency = card.getAttribute('data-val') || 'USD';
      if (mode === 'currency') convert();
    });
  });

  /* ── Number to words engine ─────────────────────────────── */

  var ones = ['','one','two','three','four','five','six','seven','eight','nine','ten','eleven','twelve','thirteen','fourteen','fifteen','sixteen','seventeen','eighteen','nineteen'];
  var tens = ['','','twenty','thirty','forty','fifty','sixty','seventy','eighty','ninety'];

  function convertHundreds(n) {
    var result = '';
    if (n >= 100) { result += ones[Math.floor(n / 100)] + ' hundred'; n %= 100; }
    if (n >= 20) { result += (result ? ' ' : '') + tens[Math.floor(n / 10)]; n %= 10; }
    if (n > 0) result += (result ? ' ' : '') + ones[n];
    return result;
  }

  var scales = [
    { val: 1e18, name: 'quintillion' },
    { val: 1e15, name: 'quadrillion' },
    { val: 1e12, name: 'trillion' },
    { val: 1e9,  name: 'billion' },
    { val: 1e6,  name: 'million' },
    { val: 1e3,  name: 'thousand' }
  ];

  function numberToWords(numStr) {
    if (numStr === '0') return 'zero';

    var negative = numStr[0] === '-';
    if (negative) numStr = numStr.slice(1);

    var parts = numStr.split('.');
    var intPart = parts[0];
    var decPart = parts[1] || '';

    /* Pad to groups of 3 */
    while (intPart.length % 3 !== 0) intPart = '0' + intPart;

    var result = '';
    var groups = intPart.match(/.{1,3}/g) || [];

    for (var i = 0; i < groups.length; i++) {
      var groupNum = parseInt(groups[i], 10);
      if (groupNum === 0) continue;
      var groupWords = convertHundreds(groupNum);
      var scaleIdx = groups.length - 1 - i;
      var scaleName = '';
      if (scaleIdx < scales.length) scaleName = scales[scaleIdx].name;
      else {
        /* Handle larger scales */
        var bigScales = ['','thousand','million','billion','trillion','quadrillion','quintillion','sextillion','septillion'];
        scaleName = bigScales[scaleIdx] || '';
      }
      result += (result ? ' ' : '') + groupWords + (scaleName ? ' ' + scaleName : '');
    }

    if (negative) result = 'minus ' + result;

    /* Decimal part */
    if (decPart) {
      result += ' point';
      for (var j = 0; j < decPart.length; j++) {
        result += ' ' + ones[parseInt(decPart[j], 10)];
      }
    }

    return result;
  }

  /* ── Ordinal suffix ─────────────────────────────────────── */

  function ordinalSuffix(n) {
    var s = ['th','st','nd','rd'];
    var v = n % 100;
    return n + (s[(v-20)%10] || s[v] || s[0]);
  }

  function toOrdinal(numStr) {
    var n = parseInt(numStr, 10);
    if (isNaN(n)) return numStr;
    return ordinalSuffix(n) + ' (' + numberToWords(numStr) + ')';
  }

  /* ── Currency ───────────────────────────────────────────── */

  var currSymbols = { USD: '$', EUR: '€', GBP: '£', INR: '₹' };

  function toCurrency(numStr) {
    var parts = numStr.split('.');
    var intWords = numberToWords(parts[0]);
    var cents = parts[1] ? parts[1].padEnd(2, '0').slice(0, 2) : '00';
    var sym = currSymbols[currency] || '$';
    return sym + ' ' + intWords + ' and ' + cents + '/100';
  }

  /* ── Convert ────────────────────────────────────────────── */

  function convert() {
    var raw = (inputEl ? inputEl.value : '').trim();
    if (!raw) { TCTP.toast('Enter a number.', '⚠️'); return; }

    /* Validate */
    var cleaned = raw.replace(/[^0-9.\-]/g, '');
    if (cleaned === '' || isNaN(parseFloat(cleaned))) {
      TCTP.toast('Invalid number.', '⚠️'); return;
    }

    /* Limit length */
    if (cleaned.replace(/[^0-9]/g, '').length > 30) {
      TCTP.toast('Number too large (max 30 digits).', '⚠️'); return;
    }

    var result;
    if (mode === 'currency') result = toCurrency(cleaned);
    else if (mode === 'ordinal') result = toOrdinal(cleaned);
    else result = numberToWords(cleaned);

    /* Capitalize first letter */
    result = result.charAt(0).toUpperCase() + result.slice(1);

    /* Update output */
    if (outputEl) {
      outputEl.innerHTML = '<div class="tc-nw-result-text">' + result + '</div>';
    }

    var wordCount = result.split(/\s+/).length;
    var sO = document.getElementById('tc-stat-orig');
    var sC = document.getElementById('tc-stat-comp');
    var sS = document.getElementById('tc-stat-saved');
    if (sO) sO.textContent = raw;
    if (sC) sC.textContent = wordCount;
    if (sS) sS.textContent = result.length;

    var preview = document.getElementById('tc-nw-preview');
    if (preview) {
      preview.innerHTML = '<div style="font-size:16px;line-height:1.6;color:var(--ink,#f1f5f9);word-break:break-word">' + result + '</div>';
    }

    var chip = document.getElementById('tc-status-chip');
    if (chip) chip.textContent = 'Done';

    TCTP.switchToResultTab();
    TCTP.toast('Converted!', '✅');
  }

  convertBtn.addEventListener('click', convert);

  /* Live convert on input */
  if (inputEl) {
    inputEl.addEventListener('input', function () {
      if (inputEl.value.trim()) convert();
    });
  }

  /* Copy */
  var copyBtn = document.getElementById('tc-nw-copy');
  if (copyBtn) {
    copyBtn.addEventListener('click', function () {
      var out = outputEl ? outputEl.textContent : '';
      if (!out || out.includes('Result will')) { TCTP.toast('Convert first.', '⚠️'); return; }
      TCTP.copyText(out.trim());
      TCTP.toast('Copied!', '✅');
    });
  }

  /* Auto-convert on load */
  convert();
})();
