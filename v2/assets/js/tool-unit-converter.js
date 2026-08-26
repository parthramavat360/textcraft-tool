/**
 * Unit Converter — Length, Weight, Temperature, Volume, Speed, Data
 * @package TextCraft_Tools_Pro
 */
(function () {
  'use strict';

  var fromSelect = document.getElementById('tc-uc-from');
  if (!fromSelect) return;

  var toSelect = document.getElementById('tc-uc-to');
  var fromVal = document.getElementById('tc-uc-from-val');
  var toVal = document.getElementById('tc-uc-to-val');
  var formulaEl = document.getElementById('tc-uc-formula');
  var swapBtn = document.getElementById('tc-uc-swap');
  var category = 'length';

  var units = {
    length: [
      { name: 'Meters', abbr: 'm', factor: 1 },
      { name: 'Kilometers', abbr: 'km', factor: 1000 },
      { name: 'Centimeters', abbr: 'cm', factor: 0.01 },
      { name: 'Millimeters', abbr: 'mm', factor: 0.001 },
      { name: 'Miles', abbr: 'mi', factor: 1609.344 },
      { name: 'Yards', abbr: 'yd', factor: 0.9144 },
      { name: 'Feet', abbr: 'ft', factor: 0.3048 },
      { name: 'Inches', abbr: 'in', factor: 0.0254 },
    ],
    weight: [
      { name: 'Kilograms', abbr: 'kg', factor: 1 },
      { name: 'Grams', abbr: 'g', factor: 0.001 },
      { name: 'Milligrams', abbr: 'mg', factor: 0.000001 },
      { name: 'Pounds', abbr: 'lb', factor: 0.453592 },
      { name: 'Ounces', abbr: 'oz', factor: 0.0283495 },
      { name: 'Metric Tons', abbr: 't', factor: 1000 },
    ],
    temp: [
      { name: 'Celsius', abbr: '°C' },
      { name: 'Fahrenheit', abbr: '°F' },
      { name: 'Kelvin', abbr: 'K' },
    ],
    volume: [
      { name: 'Liters', abbr: 'L', factor: 1 },
      { name: 'Milliliters', abbr: 'mL', factor: 0.001 },
      { name: 'Gallons (US)', abbr: 'gal', factor: 3.78541 },
      { name: 'Quarts (US)', abbr: 'qt', factor: 0.946353 },
      { name: 'Pints (US)', abbr: 'pt', factor: 0.473176 },
      { name: 'Cups (US)', abbr: 'cup', factor: 0.236588 },
      { name: 'Fl Oz (US)', abbr: 'fl oz', factor: 0.0295735 },
    ],
    speed: [
      { name: 'km/h', abbr: 'km/h', factor: 1 },
      { name: 'mph', abbr: 'mph', factor: 1.60934 },
      { name: 'm/s', abbr: 'm/s', factor: 3.6 },
      { name: 'knots', abbr: 'kn', factor: 1.852 },
      { name: 'ft/s', abbr: 'ft/s', factor: 1.09728 },
    ],
    data: [
      { name: 'Bytes', abbr: 'B', factor: 1 },
      { name: 'Kilobytes', abbr: 'KB', factor: 1024 },
      { name: 'Megabytes', abbr: 'MB', factor: 1048576 },
      { name: 'Gigabytes', abbr: 'GB', factor: 1073741824 },
      { name: 'Terabytes', abbr: 'TB', factor: 1099511627776 },
      { name: 'Bits', abbr: 'bit', factor: 0.125 },
    ],
  };

  function populateUnits() {
    var list = units[category] || [];
    fromSelect.innerHTML = '';
    toSelect.innerHTML = '';
    list.forEach(function (u, i) {
      fromSelect.innerHTML += '<option value="' + i + '">' + u.name + ' (' + u.abbr + ')</option>';
      toSelect.innerHTML += '<option value="' + i + '">' + u.name + ' (' + u.abbr + ')</option>';
    });
    if (list.length > 1) toSelect.value = '1';
    convert();
  }

  function convertTemp(val, from, to) {
    var list = units.temp;
    var f = list[from].abbr;
    var t = list[to].abbr;
    /* Convert to Celsius first */
    var celsius;
    if (f === '°C') celsius = val;
    else if (f === '°F') celsius = (val - 32) * 5 / 9;
    else celsius = val - 273.15;
    /* Convert from Celsius */
    if (t === '°C') return celsius;
    if (t === '°F') return celsius * 9 / 5 + 32;
    return celsius + 273.15;
  }

  function convert() {
    var fi = parseInt(fromSelect.value) || 0;
    var ti = parseInt(toSelect.value) || 0;
    var val = parseFloat(fromVal.value) || 0;
    var list = units[category] || [];
    var result;

    if (category === 'temp') {
      result = convertTemp(val, fi, ti);
    } else {
      var baseVal = val * list[fi].factor;
      result = baseVal / list[ti].factor;
    }

    toVal.value = result.toFixed(6).replace(/\.?0+$/, '');
    if (formulaEl) {
      var fromUnit = list[fi].abbr;
      var toUnit = list[ti].abbr;
      if (category === 'temp') {
        formulaEl.textContent = val + ' ' + fromUnit + ' = ' + toVal.value + ' ' + toUnit;
      } else {
        var factor = list[fi].factor / list[ti].factor;
        formulaEl.textContent = '1 ' + fromUnit + ' = ' + factor.toFixed(6).replace(/\.?0+$/, '') + ' ' + toUnit;
      }
    }
  }

  document.querySelectorAll('.tc-uc-modes .tc-rsz-mode-card').forEach(function (card) {
    card.addEventListener('click', function () {
      document.querySelectorAll('.tc-uc-modes .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
      card.classList.add('sel');
      category = card.getAttribute('data-val') || 'length';
      populateUnits();
    });
  });

  fromSelect.addEventListener('change', convert);
  toSelect.addEventListener('change', convert);
  fromVal.addEventListener('input', convert);

  if (swapBtn) {
    swapBtn.addEventListener('click', function () {
      var tmp = fromSelect.value;
      fromSelect.value = toSelect.value;
      toSelect.value = tmp;
      convert();
    });
  }

  populateUnits();
})();
