/**
 * Tip Calculator
 * @package TextCraft_Tools_Pro
 */
(function () {
  if (!window.TCTP) return;

  const $ = (sel, ctx = document) => ctx.querySelector(sel);
  const pctCards = document.querySelectorAll('.tc-tip-pct-modes .tc-rsz-mode-card');
  const inputs = document.querySelectorAll('.tc-tip-input');
  const billInput = $('#tc-tip-bill');
  const customPctInput = $('#tc-tip-custom');
  const peopleInput = $('#tc-tip-people');
  const amountEl = $('#tc-tip-amount');
  const totalEl = $('#tc-tip-total');
  const perPersonEl = $('#tc-tip-per-person');

  if (!billInput || !peopleInput || !amountEl) return;

  let selectedPct = 20;

  pctCards.forEach(card => {
    card.addEventListener('click', () => {
      pctCards.forEach(c => c.classList.remove('sel'));
      card.classList.add('sel');
      selectedPct = parseFloat(card.dataset.val);
      customPctInput.value = '';
      calculate();
    });
  });

  function calculate() {
    const bill = parseFloat(billInput.value) || 0;
    const people = parseInt(peopleInput.value, 10) || 1;
    const customPct = parseFloat(customPctInput.value);
    const pct = isNaN(customPct) ? selectedPct : customPct;

    if (bill <= 0 || pct < 0) {
      amountEl.textContent = '$0.00';
      totalEl.textContent = '$0.00';
      perPersonEl.textContent = '$0.00';
      return;
    }

    const tip = bill * (pct / 100);
    const total = bill + tip;
    const perPerson = total / Math.max(people, 1);

    amountEl.textContent = '$' + tip.toFixed(2);
    totalEl.textContent = '$' + total.toFixed(2);
    perPersonEl.textContent = '$' + perPerson.toFixed(2);
  }

  inputs.forEach(input => {
    input.addEventListener('input', calculate);
  });

  calculate();
})();
