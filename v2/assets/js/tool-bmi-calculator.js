/**
 * BMI Calculator
 * @package TextCraft_Tools_Pro
 */
(function () {
  if (!window.TCTP) return;

  const $ = (sel, ctx = document) => ctx.querySelector(sel);
  const unitCards = document.querySelectorAll('.tc-bmi-unit-modes .tc-rsz-mode-card');
  const metricFields = document.getElementById('tc-bmi-metric-fields');
  const imperialFields = document.getElementById('tc-bmi-imperial-fields');
  const inputs = document.querySelectorAll('.tc-bmi-input');
  const calculateBtn = document.getElementById('tc-bmi-calculate');
  const results = document.getElementById('tc-bmi-results');
  const scoreEl = document.getElementById('tc-bmi-score');

  if (!calculateBtn || !results) return;
  const categoryEl = document.getElementById('tc-bmi-category');
  const scoreCard = document.getElementById('tc-bmi-score-card');
  const pointer = document.getElementById('tc-bmi-pointer');
  const healthyLow = document.getElementById('tc-bmi-healthy-low');
  const healthyHigh = document.getElementById('tc-bmi-healthy-high');
  const categoryIcon = document.getElementById('tc-bmi-category-icon');

  let currentUnit = 'metric';

  unitCards.forEach(card => {
    card.addEventListener('click', () => {
      unitCards.forEach(c => c.classList.remove('sel'));
      card.classList.add('sel');
      currentUnit = card.dataset.val;
      if (currentUnit === 'metric') {
        metricFields.style.display = '';
        imperialFields.style.display = 'none';
      } else {
        metricFields.style.display = 'none';
        imperialFields.style.display = '';
      }
    });
  });

  function getCategory(bmi) {
    if (bmi < 18.5) return { label: 'Underweight', color: '#f59e0b', icon: '📉' };
    if (bmi < 25) return { label: 'Normal Weight', color: '#10b981', icon: '✅' };
    if (bmi < 30) return { label: 'Overweight', color: '#f97316', icon: '⚠️' };
    return { label: 'Obese', color: '#ef4444', icon: '🔴' };
  }

  function getHealthyRange(heightCm) {
    const h = heightCm / 100;
    const low = (18.5 * h * h).toFixed(1);
    const high = (24.9 * h * h).toFixed(1);
    return { low, high };
  }

  function calculate() {
    let weightKg, heightCm;
    if (currentUnit === 'metric') {
      weightKg = parseFloat($('#tc-bmi-weight-kg').value);
      heightCm = parseFloat($('#tc-bmi-height-cm').value);
    } else {
      const lbs = parseFloat($('#tc-bmi-weight-lbs').value);
      const ft = parseFloat($('#tc-bmi-ft').value) || 0;
      const inch = parseFloat($('#tc-bmi-in').value) || 0;
      weightKg = lbs * 0.453592;
      heightCm = (ft * 12 + inch) * 2.54;
    }

    if (!weightKg || !heightCm || weightKg < 10 || heightCm < 50) {
      TCTP.toast('Please enter valid height and weight', 'error');
      return;
    }

    const heightM = heightCm / 100;
    const bmi = weightKg / (heightM * heightM);
    const cat = getCategory(bmi);
    const range = getHealthyRange(heightCm);

    scoreEl.textContent = bmi.toFixed(1);
    scoreEl.style.color = cat.color;
    categoryEl.textContent = cat.label;
    scoreCard.style.borderColor = cat.color;

    healthyLow.textContent = range.low + ' kg';
    healthyHigh.textContent = range.high + ' kg';
    categoryIcon.textContent = cat.icon;

    const maxBmi = 40;
    const pct = Math.min(bmi / maxBmi * 100, 100);
    pointer.style.left = pct + '%';

    results.style.display = 'block';
  }

  calculateBtn.addEventListener('click', calculate);
  inputs.forEach(input => {
    input.addEventListener('input', calculate);
  });
})();
