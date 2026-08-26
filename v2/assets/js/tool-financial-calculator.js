/**
 * Financial Calculator — EMI, Compound Interest, GST/VAT, Discount
 * @package TextCraft_Tools_Pro
 */
(function () {
  'use strict';

  var calcBtn = document.getElementById('tc-fc-calc');
  if (!calcBtn) return;

  var mode = 'emi';
  var resultsEl = document.getElementById('tc-fc-results');
  var resultGrid = document.getElementById('tc-fc-result-grid');

  function fmt(n) { return '$' + n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','); }

  /* ── Mode cards ─────────────────────────────────────────── */
  document.querySelectorAll('.tc-fc-modes .tc-rsz-mode-card').forEach(function (card) {
    card.addEventListener('click', function () {
      document.querySelectorAll('.tc-fc-modes .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
      card.classList.add('sel');
      mode = card.getAttribute('data-val') || 'emi';
      document.getElementById('tc-fc-emi-fields').style.display = mode === 'emi' ? '' : 'none';
      document.getElementById('tc-fc-compound-fields').style.display = mode === 'compound' ? '' : 'none';
      document.getElementById('tc-fc-gst-fields').style.display = mode === 'gst' ? '' : 'none';
      document.getElementById('tc-fc-discount-fields').style.display = mode === 'discount' ? '' : 'none';
    });
  });

  function showResults(html) {
    if (resultsEl) resultsEl.style.display = '';
    if (resultGrid) resultGrid.innerHTML = html;
  }

  function calcEMI() {
    var P = parseFloat(document.getElementById('tc-fc-amount').value) || 0;
    var r = (parseFloat(document.getElementById('tc-fc-rate').value) || 0) / 100 / 12;
    var n = (parseInt(document.getElementById('tc-fc-term').value) || 1) * 12;
    if (P <= 0) { TCTP.toast('Enter valid amount.', '⚠️'); return; }
    var emi = r === 0 ? P / n : P * r * Math.pow(1 + r, n) / (Math.pow(1 + r, n) - 1);
    var total = emi * n;
    var interest = total - P;
    showResults(
      '<div class="tc-fc-card"><span class="tc-fc-label">Monthly EMI</span><span class="tc-fc-value">' + fmt(emi) + '</span></div>' +
      '<div class="tc-fc-card"><span class="tc-fc-label">Total Amount</span><span class="tc-fc-value">' + fmt(total) + '</span></div>' +
      '<div class="tc-fc-card"><span class="tc-fc-label">Total Interest</span><span class="tc-fc-value tc-fc-warn">' + fmt(interest) + '</span></div>' +
      '<div class="tc-fc-card"><span class="tc-fc-label">Principal</span><span class="tc-fc-value">' + fmt(P) + '</span></div>'
    );
  }

  function calcCompound() {
    var P = parseFloat(document.getElementById('tc-fc-principal').value) || 0;
    var r = (parseFloat(document.getElementById('tc-fc-crate').value) || 0) / 100;
    var t = parseInt(document.getElementById('tc-fc-years').value) || 1;
    var n = parseInt(document.getElementById('tc-fc-compound').value) || 12;
    if (P <= 0) { TCTP.toast('Enter valid principal.', '⚠️'); return; }
    var A = P * Math.pow(1 + r / n, n * t);
    var interest = A - P;
    showResults(
      '<div class="tc-fc-card"><span class="tc-fc-label">Final Amount</span><span class="tc-fc-value">' + fmt(A) + '</span></div>' +
      '<div class="tc-fc-card"><span class="tc-fc-label">Interest Earned</span><span class="tc-fc-value tc-fc-warn">' + fmt(interest) + '</span></div>' +
      '<div class="tc-fc-card"><span class="tc-fc-label">Principal</span><span class="tc-fc-value">' + fmt(P) + '</span></div>' +
      '<div class="tc-fc-card"><span class="tc-fc-label">Growth</span><span class="tc-fc-value">' + ((A / P - 1) * 100).toFixed(1) + '%</span></div>'
    );
  }

  function calcGST() {
    var A = parseFloat(document.getElementById('tc-fc-gamount').value) || 0;
    var r = (parseFloat(document.getElementById('tc-fc-grate').value) || 0) / 100;
    var mode = document.getElementById('tc-fc-gmode').value;
    if (mode === 'add') {
      var gst = A * r;
      var total = A + gst;
      showResults(
        '<div class="tc-fc-card"><span class="tc-fc-label">GST/VAT Amount</span><span class="tc-fc-value">' + fmt(gst) + '</span></div>' +
        '<div class="tc-fc-card"><span class="tc-fc-label">Total (with tax)</span><span class="tc-fc-value tc-fc-warn">' + fmt(total) + '</span></div>' +
        '<div class="tc-fc-card"><span class="tc-fc-label">Base Amount</span><span class="tc-fc-value">' + fmt(A) + '</span></div>'
      );
    } else {
      var base = A / (1 + r);
      var gst = A - base;
      showResults(
        '<div class="tc-fc-card"><span class="tc-fc-label">GST/VAT Amount</span><span class="tc-fc-value">' + fmt(gst) + '</span></div>' +
        '<div class="tc-fc-card"><span class="tc-fc-label">Base Amount (tax excl.)</span><span class="tc-fc-value tc-fc-warn">' + fmt(base) + '</span></div>' +
        '<div class="tc-fc-card"><span class="tc-fc-label">Total (with tax)</span><span class="tc-fc-value">' + fmt(A) + '</span></div>'
      );
    }
  }

  function calcDiscount() {
    var P = parseFloat(document.getElementById('tc-fc-dprice').value) || 0;
    var d = (parseFloat(document.getElementById('tc-fc-dpercent').value) || 0) / 100;
    var saved = P * d;
    var final_ = P - saved;
    showResults(
      '<div class="tc-fc-card"><span class="tc-fc-label">Final Price</span><span class="tc-fc-value tc-fc-warn">' + fmt(final_) + '</span></div>' +
      '<div class="tc-fc-card"><span class="tc-fc-label">You Save</span><span class="tc-fc-value">' + fmt(saved) + '</span></div>' +
      '<div class="tc-fc-card"><span class="tc-fc-label">Original Price</span><span class="tc-fc-value">' + fmt(P) + '</span></div>' +
      '<div class="tc-fc-card"><span class="tc-fc-label">Discount</span><span class="tc-fc-value">' + (d * 100).toFixed(1) + '%</span></div>'
    );
  }

  calcBtn.addEventListener('click', function () {
    switch (mode) {
      case 'emi': calcEMI(); break;
      case 'compound': calcCompound(); break;
      case 'gst': calcGST(); break;
      case 'discount': calcDiscount(); break;
    }
    TCTP.toast('Calculated!', '✅');
  });

  var copyBtn = document.getElementById('tc-fc-copy');
  if (copyBtn) {
    copyBtn.addEventListener('click', function () {
      var text = resultGrid ? resultGrid.innerText : '';
      if (!text) { TCTP.toast('Calculate first.', '⚠️'); return; }
      TCTP.copyText(text);
      TCTP.toast('Copied!', '✅');
    });
  }
})();
