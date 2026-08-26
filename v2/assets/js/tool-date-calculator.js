/**
 * Date Calculator
 * @package TextCraft_Tools_Pro
 */
(function () {
  if (!window.TCTP) return;

  const $ = (sel, ctx = document) => ctx.querySelector(sel);
  const modeCards = document.querySelectorAll('.tc-date-modes .tc-rsz-mode-card');
  const diffFields = $('#tc-date-diff-fields');
  const addFields = $('#tc-date-add-fields');
  const subFields = $('#tc-date-sub-fields');
  const results = $('#tc-date-results');
  const calculateBtn = $('#tc-date-calculate');

  if (!diffFields || !addFields || !subFields) return;

  let currentMode = 'diff';

  const today = new Date().toISOString().split('T')[0];
  const startEl = $('#tc-date-start');
  const endEl = $('#tc-date-end');
  const baseEl = $('#tc-date-base');
  const baseSubEl = $('#tc-date-base-sub');
  if(startEl) startEl.value = today;
  if(endEl) endEl.value = today;
  if(baseEl) baseEl.value = today;
  if(baseSubEl) baseSubEl.value = today;

  modeCards.forEach(card => {
    card.addEventListener('click', () => {
      modeCards.forEach(c => c.classList.remove('sel'));
      card.classList.add('sel');
      currentMode = card.dataset.val;
      diffFields.style.display = currentMode === 'diff' ? '' : 'none';
      addFields.style.display = currentMode === 'add' ? '' : 'none';
      subFields.style.display = currentMode === 'sub' ? '' : 'none';
      results.style.display = 'none';
    });
  });

  function diffMonths(d1, d2) {
    let months = (d2.getFullYear() - d1.getFullYear()) * 12 + (d2.getMonth() - d1.getMonth());
    const temp = new Date(d1);
    temp.setMonth(temp.getMonth() + months);
    if (temp > d2) months--;
    const prev = new Date(d1);
    prev.setMonth(prev.getMonth() + months);
    const remainMs = d2 - prev;
    const remainDays = Math.round(remainMs / 86400000);
    return { months: Math.abs(months), days: Math.abs(remainDays) };
  }

  function showResult(days, resultDate) {
    const weeks = Math.floor(days / 7);
    const remainDays = days % 7;
    const md = diffMonths(
      new Date(resultDate.getTime() - days * 86400000),
      resultDate
    );

    $('#tc-date-result-days').textContent = days.toLocaleString();
    $('#tc-date-result-weeks').textContent = weeks + 'w ' + remainDays + 'd';
    $('#tc-date-result-months').textContent = md.months + 'mo ' + md.days + 'd';
    $('#tc-date-result-date').textContent = resultDate.toLocaleDateString('en-US', {
      year: 'numeric', month: 'long', day: 'numeric'
    });
    $('#tc-date-result-day').textContent = resultDate.toLocaleDateString('en-US', { weekday: 'long' });
    results.style.display = 'block';
  }

  function calculate() {
    if (currentMode === 'diff') {
      const s = new Date($('#tc-date-start').value);
      const e = new Date($('#tc-date-end').value);
      if (isNaN(s) || isNaN(e)) { TCTP.toast('Please select valid dates', 'error'); return; }
      const days = Math.round(Math.abs(e - s) / 86400000);
      $('#tc-date-result-date-label').textContent = 'End Date';
      showResult(days, e > s ? e : s);
    } else {
      const base = new Date($('#tc-date-base').value);
      const daysVal = parseInt(currentMode === 'add' ? $('#tc-date-days').value : $('#tc-date-days-sub').value, 10);
      if (isNaN(base) || !daysVal) { TCTP.toast('Please enter valid date and days', 'error'); return; }
      const sign = currentMode === 'add' ? 1 : -1;
      const result = new Date(base.getTime() + sign * daysVal * 86400000);
      const days = Math.abs(daysVal);
      $('#tc-date-result-date-label').textContent = currentMode === 'add' ? 'Result Date' : 'Result Date';
      showResult(days, result);
    }
  }

  calculateBtn.addEventListener('click', calculate);
})();
