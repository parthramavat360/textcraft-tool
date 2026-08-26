/**
 * JSON Tools — CSV↔JSON, JSON↔YAML, JSON→TypeScript
 * @package TextCraft_Tools_Pro
 */
(function () {
  'use strict';

  var convertBtn = document.getElementById('tc-jt-convert');
  if (!convertBtn) return;

  var inputEl  = document.getElementById('tc-jt-input');
  var outputEl = document.getElementById('tc-jt-output');
  var mode = 'csv-to-json';

  /* ── Mode cards ─────────────────────────────────────────── */

  document.querySelectorAll('.tc-jt-modes .tc-rsz-mode-card').forEach(function (card) {
    card.addEventListener('click', function () {
      document.querySelectorAll('.tc-jt-modes .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
      card.classList.add('sel');
      mode = card.getAttribute('data-val') || 'csv-to-json';
    });
  });

  /* ── CSV ↔ JSON ─────────────────────────────────────────── */

  function csvToJson(csv) {
    var lines = csv.trim().split('\n');
    if (lines.length < 2) return [];
    var headers = lines[0].split(',').map(function (h) { return h.trim().replace(/^["']|["']$/g, ''); });
    var result = [];
    for (var i = 1; i < lines.length; i++) {
      var vals = lines[i].split(',').map(function (v) { return v.trim().replace(/^["']|["']$/g, ''); });
      var obj = {};
      headers.forEach(function (h, idx) { obj[h] = vals[idx] || ''; });
      result.push(obj);
    }
    return result;
  }

  function jsonToCsv(json) {
    var data = typeof json === 'string' ? JSON.parse(json) : json;
    if (!Array.isArray(data)) data = [data];
    if (!data.length) return '';
    var headers = Object.keys(data[0]);
    var lines = [headers.join(',')];
    data.forEach(function (row) {
      lines.push(headers.map(function (h) { return '"' + String(row[h] || '').replace(/"/g, '""') + '"'; }).join(','));
    });
    return lines.join('\n');
  }

  /* ── JSON ↔ YAML ────────────────────────────────────────── */

  function jsonToYaml(obj, indent) {
    indent = indent || 0;
    var spaces = '  '.repeat(indent);
    var result = '';
    if (Array.isArray(obj)) {
      obj.forEach(function (item) {
        if (typeof item === 'object' && item !== null) {
          result += spaces + '-\n' + jsonToYaml(item, indent + 1);
        } else {
          result += spaces + '- ' + JSON.stringify(item) + '\n';
        }
      });
    } else if (typeof obj === 'object' && obj !== null) {
      Object.keys(obj).forEach(function (key) {
        var val = obj[key];
        if (typeof val === 'object' && val !== null) {
          result += spaces + key + ':\n' + jsonToYaml(val, indent + 1);
        } else {
          result += spaces + key + ': ' + JSON.stringify(val) + '\n';
        }
      });
    }
    return result;
  }

  function yamlToJson(yaml) {
    var result = {};
    var lines = yaml.trim().split('\n');
    var currentKey = null;
    lines.forEach(function (line) {
      var trimmed = line.replace(/^\s+/, '');
      if (trimmed.startsWith('- ')) {
        if (!Array.isArray(result[currentKey])) result[currentKey] = [];
        result[currentKey].push(trimmed.slice(2).replace(/^["']|["']$/g, ''));
      } else {
        var colonIdx = trimmed.indexOf(':');
        if (colonIdx > -1) {
          currentKey = trimmed.slice(0, colonIdx).trim();
          var val = trimmed.slice(colonIdx + 1).trim();
          if (val === '') return;
          if (val === 'true') result[currentKey] = true;
          else if (val === 'false') result[currentKey] = false;
          else if (!isNaN(val)) result[currentKey] = Number(val);
          else result[currentKey] = val.replace(/^["']|["']$/g, '');
        }
      }
    });
    return result;
  }

  /* ── JSON → TypeScript ──────────────────────────────────── */

  function jsonToTs(obj, name) {
    name = name || 'RootObject';
    if (Array.isArray(obj)) {
      if (!obj.length) return 'type ' + name + ' = any[];';
      return 'interface ' + name + ' {\n' + jsonToTsObj(obj[0], '  ') + '\n}';
    }
    return 'interface ' + name + ' {\n' + jsonToTsObj(obj, '  ') + '\n}';
  }

  function jsonToTsObj(obj, indent) {
    var result = '';
    Object.keys(obj).forEach(function (key) {
      var val = obj[key];
      var tsType;
      if (val === null) tsType = 'any';
      else if (typeof val === 'boolean') tsType = 'boolean';
      else if (typeof val === 'number') tsType = 'number';
      else if (typeof val === 'string') tsType = 'string';
      else if (Array.isArray(val)) tsType = val.length ? 'any[]' : 'any[]';
      else if (typeof val === 'object') tsType = '{ ' + Object.keys(val).map(function (k) { return k + ': any'; }).join('; ') + ' }';
      else tsType = 'any';
      result += indent + key + ': ' + tsType + ';\n';
    });
    return result;
  }

  /* ── Convert ────────────────────────────────────────────── */

  convertBtn.addEventListener('click', function () {
    var input = inputEl ? inputEl.value.trim() : '';
    if (!input) { TCTP.toast('Paste some data first.', '⚠️'); return; }

    try {
      var result = '';
      switch (mode) {
        case 'csv-to-json':
          result = JSON.stringify(csvToJson(input), null, 2);
          break;
        case 'json-to-csv':
          result = jsonToCsv(input);
          break;
        case 'json-to-yaml':
          result = '---\n' + jsonToYaml(JSON.parse(input));
          break;
        case 'yaml-to-json':
          result = JSON.stringify(yamlToJson(input), null, 2);
          break;
        case 'json-to-ts':
          result = jsonToTs(JSON.parse(input));
          break;
        case 'format':
          result = JSON.stringify(JSON.parse(input), null, 2);
          break;
      }
      if (outputEl) outputEl.textContent = result;
      TCTP.toast('Converted!', '✅');
    } catch (e) {
      if (outputEl) outputEl.textContent = 'Error: ' + e.message;
      TCTP.toast('Conversion error: ' + e.message, '⚠️');
    }
  });

  /* Copy */
  var copyBtn = document.getElementById('tc-jt-copy');
  if (copyBtn) {
    copyBtn.addEventListener('click', function () {
      var text = outputEl ? outputEl.textContent : '';
      if (!text || text.includes('Output will') || text.includes('Error')) { TCTP.toast('Convert first.', '⚠️'); return; }
      TCTP.copyText(text);
      TCTP.toast('Copied!', '✅');
    });
  }

  /* Swap */
  var swapBtn = document.getElementById('tc-jt-swap');
  if (swapBtn) {
    swapBtn.addEventListener('click', function () {
      var outText = outputEl ? outputEl.textContent : '';
      if (inputEl && outText && !outText.includes('Output will')) {
        inputEl.value = outText;
        outputEl.textContent = 'Swapped! Click Convert again.';
      }
    });
  }
})();
