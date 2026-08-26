/**
 * Time Zone Converter — world clock comparison
 * @package TextCraft_Tools_Pro
 */
(function () {
  'use strict';

  var sourceSelect = document.getElementById('tc-tz-source');
  if (!sourceSelect) return;

  var gridEl = document.getElementById('tc-tz-grid');

  var commonTimezones = [
    { name: 'UTC / GMT', tz: 'UTC' },
    { name: 'US Eastern (ET)', tz: 'America/New_York' },
    { name: 'US Central (CT)', tz: 'America/Chicago' },
    { name: 'US Mountain (MT)', tz: 'America/Denver' },
    { name: 'US Pacific (PT)', tz: 'America/Los_Angeles' },
    { name: 'London (GMT/BST)', tz: 'Europe/London' },
    { name: 'Paris (CET/CEST)', tz: 'Europe/Paris' },
    { name: 'Berlin (CET/CEST)', tz: 'Europe/Berlin' },
    { name: 'Moscow (MSK)', tz: 'Europe/Moscow' },
    { name: 'Dubai (GST)', tz: 'Asia/Dubai' },
    { name: 'Mumbai (IST)', tz: 'Asia/Kolkata' },
    { name: 'Singapore (SGT)', tz: 'Asia/Singapore' },
    { name: 'Hong Kong (HKT)', tz: 'Asia/Hong_Kong' },
    { name: 'Tokyo (JST)', tz: 'Asia/Tokyo' },
    { name: 'Sydney (AEST)', tz: 'Australia/Sydney' },
    { name: 'Auckland (NZST)', tz: 'Pacific/Auckland' },
    { name: 'São Paulo (BRT)', tz: 'America/Sao_Paulo' },
    { name: 'Toronto (ET)', tz: 'America/Toronto' },
  ];

  /* Populate source */
  commonTimezones.forEach(function (tz, i) {
    sourceSelect.innerHTML += '<option value="' + tz.tz + '">' + tz.name + '</option>';
  });
  /* Try to detect user timezone */
  try {
    var userTz = Intl.DateTimeFormat().resolvedOptions().timeZone;
    if (userTz) sourceSelect.value = userTz;
  } catch (e) {}

  function formatTime(date, tz) {
    try {
      return date.toLocaleTimeString('en-US', { timeZone: tz, hour: '2-digit', minute: '2-digit', hour12: true });
    } catch (e) { return '--:--'; }
  }

  function formatDate(date, tz) {
    try {
      return date.toLocaleDateString('en-US', { timeZone: tz, weekday: 'short', month: 'short', day: 'numeric' });
    } catch (e) { return ''; }
  }

  function getOffset(date, tz) {
    try {
      var str = date.toLocaleString('en-US', { timeZone: tz, timeZoneName: 'shortOffset' });
      var match = str.match(/GMT([+-]\d{1,2}(?::\d{2})?)/);
      return match ? match[1] : '';
    } catch (e) { return ''; }
  }

  function render() {
    var now = new Date();
    var srcTz = sourceSelect.value || 'UTC';
    var html = '';

    commonTimezones.forEach(function (tzObj) {
      var time = formatTime(now, tzObj.tz);
      var date = formatDate(now, tzObj.tz);
      var offset = getOffset(now, tzObj.tz);
      var isSrc = tzObj.tz === srcTz;
      html += '<div class="tc-tz-card' + (isSrc ? ' tc-tz-card--src' : '') + '">' +
        '<div class="tc-tz-name">' + tzObj.name + '</div>' +
        '<div class="tc-tz-time">' + time + '</div>' +
        '<div class="tc-tz-date">' + date + '</div>' +
        '<div class="tc-tz-offset">UTC' + offset + '</div>' +
        '</div>';
    });

    if (gridEl) gridEl.innerHTML = html;
  }

  sourceSelect.addEventListener('change', render);
  render();
  setInterval(render, 30000);
})();
