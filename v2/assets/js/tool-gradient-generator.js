/**
 * Gradient Generator — CSS gradient builder
 * @package TextCraft_Tools_Pro
 */
(function () {
  'use strict';

  var previewEl  = document.getElementById('tc-gr-preview');
  var stopsEl    = document.getElementById('tc-gr-stops');
  var angleEl    = document.getElementById('tc-gr-angle');
  var angleVal   = document.getElementById('tc-gr-angle-val');
  var cssCode    = document.getElementById('tc-gr-css-code');
  var angleSection = document.getElementById('tc-gr-angle-section');
  if (!previewEl) return;

  var type = 'linear';
  var stops = [
    { color: '#667eea', pos: 0 },
    { color: '#764ba2', pos: 100 }
  ];
  var presets = [
    ['#667eea','#764ba2'],['#f093fb','#f5576c'],['#4facfe','#00f2fe'],
    ['#43e97b','#38f9d7'],['#fa709a','#fee140'],['#a18cd1','#fbc2eb'],
    ['#fccb90','#d57eeb'],['#e0c3fc','#8ec5fc'],['#f5576c','#ff6f61'],
    ['#667eea','#f093fb'],['#00c6fb','#005bea'],['#d4fc79','#96e6a1']
  ];

  /* ── Mode cards ─────────────────────────────────────────── */

  var modeCards = document.querySelectorAll('.tc-gr-modes .tc-rsz-mode-card');
  modeCards.forEach(function (card) {
    card.addEventListener('click', function () {
      modeCards.forEach(function (c) { c.classList.remove('sel'); });
      card.classList.add('sel');
      type = card.getAttribute('data-val') || 'linear';
      if (angleSection) angleSection.style.display = type === 'linear' ? '' : 'none';
      render();
    });
  });

  /* ── Angle slider ───────────────────────────────────────── */

  if (angleEl) {
    angleEl.addEventListener('input', function () {
      if (angleVal) angleVal.textContent = angleEl.value + '°';
      render();
    });
  }

  /* ── Render stops UI ────────────────────────────────────── */

  function renderStops() {
    stopsEl.innerHTML = '<h4 class="tc-rsz-heading">Color Stops</h4>';
    stops.forEach(function (stop, idx) {
      var row = document.createElement('div');
      row.className = 'tc-gr-stop-row';
      row.innerHTML =
        '<input type="color" class="tc-gr-stop-picker" value="' + stop.color + '" data-idx="' + idx + '">' +
        '<input type="range" class="tc-cc-range tc-gr-stop-pos" min="0" max="100" value="' + stop.pos + '" data-idx="' + idx + '">' +
        '<span class="tc-gr-stop-val">' + stop.pos + '%</span>' +
        (stops.length > 2 ? '<button class="tc-gr-stop-remove" data-idx="' + idx + '" type="button">&times;</button>' : '');
      stopsEl.appendChild(row);
    });

    /* Events */
    stopsEl.querySelectorAll('.tc-gr-stop-picker').forEach(function (el) {
      el.addEventListener('input', function () {
        stops[parseInt(el.dataset.idx)].color = el.value;
        render();
      });
    });
    stopsEl.querySelectorAll('.tc-gr-stop-pos').forEach(function (el) {
      el.addEventListener('input', function () {
        var idx = parseInt(el.dataset.idx);
        stops[idx].pos = parseInt(el.value);
        el.nextElementSibling.textContent = el.value + '%';
        render();
      });
    });
    stopsEl.querySelectorAll('.tc-gr-stop-remove').forEach(function (el) {
      el.addEventListener('click', function () {
        stops.splice(parseInt(el.dataset.idx), 1);
        renderStops();
        render();
      });
    });
  }

  /* ── Add stop ───────────────────────────────────────────── */

  var addBtn = document.getElementById('tc-gr-add-stop');
  if (addBtn) {
    addBtn.addEventListener('click', function () {
      stops.push({ color: '#' + Math.floor(Math.random()*16777215).toString(16).padStart(6,'0'), pos: 50 });
      stops.sort(function (a, b) { return a.pos - b.pos; });
      renderStops();
      render();
    });
  }

  /* ── Generate CSS ───────────────────────────────────────── */

  function render() {
    var colorStops = stops.map(function (s) { return s.color + ' ' + s.pos + '%'; }).join(', ');
    var css;
    if (type === 'linear') {
      var angle = angleEl ? angleEl.value : 90;
      css = 'linear-gradient(' + angle + 'deg, ' + colorStops + ')';
    } else {
      css = 'radial-gradient(circle, ' + colorStops + ')';
    }

    previewEl.style.background = css;
    if (cssCode) cssCode.textContent = 'background: ' + css + ';';
  }

  /* ── Copy CSS ───────────────────────────────────────────── */

  var copyBtn = document.getElementById('tc-gr-copy');
  if (copyBtn) {
    copyBtn.addEventListener('click', function () {
      var code = cssCode ? cssCode.textContent : '';
      TCTP.copyText(code);
      TCTP.toast('CSS copied!', '✅');
    });
  }

  /* ── Random preset ──────────────────────────────────────── */

  var randomBtn = document.getElementById('tc-gr-random');
  if (randomBtn) {
    randomBtn.addEventListener('click', function () {
      var preset = presets[Math.floor(Math.random() * presets.length)];
      stops = [
        { color: preset[0], pos: 0 },
        { color: preset[1], pos: 100 }
      ];
      if (angleEl) { angleEl.value = Math.floor(Math.random() * 360); if (angleVal) angleVal.textContent = angleEl.value + '°'; }
      renderStops();
      render();
    });
  }

  /* ── Init ───────────────────────────────────────────────── */

  renderStops();
  render();

})();
