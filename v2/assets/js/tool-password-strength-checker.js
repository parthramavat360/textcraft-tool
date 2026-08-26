document.addEventListener('DOMContentLoaded', function() {
  var pw = document.getElementById('tc-pwc-password');
  if (!pw) return;

  var toggle = document.getElementById('tc-pwc-toggle');
  var checkBtn = document.getElementById('tc-pwc-check');
  var strengthSection = document.getElementById('tc-pwc-strength-section');
  var meterFill = document.getElementById('tc-pwc-meter-fill');
  var strengthLabel = document.getElementById('tc-pwc-strength-label');
  var statsDiv = document.getElementById('tc-pwc-stats');
  var detailsDiv = document.getElementById('tc-pwc-details');
  var scoreEl = document.getElementById('tc-pwc-score');
  var entropyEl = document.getElementById('tc-pwc-entropy');
  var lengthEl = document.getElementById('tc-pwc-length');
  var onlineSlow = document.getElementById('tc-pwc-online-slow');
  var onlineFast = document.getElementById('tc-pwc-online-fast');
  var offlineFast = document.getElementById('tc-pwc-offline-fast');
  var offlineSlow = document.getElementById('tc-pwc-offline-slow');
  var checklist = document.getElementById('tc-pwc-checklist');
  var tips = document.getElementById('tc-pwc-tips');
  var resultCard = document.getElementById('tc-pwc-result-card');

  toggle.addEventListener('click', function() {
    pw.type = pw.type === 'password' ? 'text' : 'password';
  });

  checkBtn.addEventListener('click', function() {
    var val = pw.value;
    if (!val) { return; }

    var analysis = analyzePassword(val);
    renderAnalysis(analysis, val);
  });

  pw.addEventListener('input', function() {
    if (pw.value.length > 0) {
      var analysis = analyzePassword(pw.value);
      renderAnalysis(analysis, pw.value);
    } else {
      strengthSection.style.display = 'none';
      statsDiv.style.display = 'none';
      detailsDiv.style.display = 'none';
      resultCard.innerHTML = '<p style="color:var(--muted);font-size:14px;text-align:center">Enter a password above to see the analysis.</p>';
    }
  });

  function analyzePassword(pw) {
    var len = pw.length;
    var score = 0;
    var hasLower = /[a-z]/.test(pw);
    var hasUpper = /[A-Z]/.test(pw);
    var hasDigit = /[0-9]/.test(pw);
    var hasSymbol = /[^a-zA-Z0-9]/.test(pw);
    var hasSpaces = /\s/.test(pw);

    var charsetSize = 0;
    if (hasLower) charsetSize += 26;
    if (hasUpper) charsetSize += 26;
    if (hasDigit) charsetSize += 10;
    if (hasSymbol) charsetSize += 33;
    if (hasSpaces) charsetSize += 1;
    if (charsetSize === 0) charsetSize = 26;

    var entropy = len * Math.log2(charsetSize);

    var sequential = checkSequential(pw);
    var repeated = checkRepeated(pw);
    var dictionary = checkDictionary(pw);
    var commonPatterns = checkCommonPatterns(pw);

    var checks = [];
    var tipsList = [];

    if (len >= 8) { score++; checks.push({text: 'Length 8+ characters', pass: true}); }
    else { checks.push({text: 'Length 8+ characters', pass: false}); tipsList.push('Use at least 8 characters. 12+ is recommended.'); }

    if (len >= 12) { score++; checks.push({text: 'Length 12+ characters', pass: true}); }
    else { checks.push({text: 'Length 12+ characters', pass: false}); tipsList.push('Aim for 12 or more characters for better security.'); }

    var complexityScore = [hasLower, hasUpper, hasDigit, hasSymbol].filter(Boolean).length;
    if (complexityScore >= 3) { score++; checks.push({text: 'Uses 3+ character types', pass: true}); }
    else { checks.push({text: 'Uses 3+ character types', pass: false}); tipsList.push('Mix uppercase, lowercase, numbers, and symbols.'); }

    if (!sequential && !repeated && !dictionary && !commonPatterns) { score++; checks.push({text: 'No common patterns', pass: true}); }
    else {
      checks.push({text: 'No common patterns', pass: false});
      if (sequential) tipsList.push('Avoid sequential characters like "abc" or "123".');
      if (repeated) tipsList.push('Avoid repeating characters like "aaa" or "111".');
      if (dictionary) tipsList.push('Contains a common word. Use random characters instead.');
      if (commonPatterns) tipsList.push('Contains a common password pattern (e.g., "password", "qwerty").');
    }

    if (hasSpaces) { checks.push({text: 'Contains spaces (good!)', pass: true}); }

    var label, level, color;
    if (score <= 1) { label = 'Very Weak'; level = 1; color = '#dc2626'; }
    else if (score === 2) { label = 'Weak'; level = 2; color = '#f97316'; }
    else if (score === 3) { label = 'Fair'; level = 3; color = '#eab308'; }
    else if (score === 4) { label = 'Strong'; level = 4; color = '#22c55e'; }
    else { label = 'Very Strong'; level = 5; color = '#15803d'; }

    if (len >= 16 && complexityScore >= 3 && !sequential && !repeated) {
      label = 'Very Strong'; level = 5; color = '#15803d'; score = 5;
    }

    var guessesPerSec = Math.pow(2, entropy);

    return {
      score: score,
      maxScore: 4,
      label: label,
      level: level,
      color: color,
      entropy: entropy,
      len: len,
      charsetSize: charsetSize,
      guessesPerSec: guessesPerSec,
      checks: checks,
      tips: tipsList
    };
  }

  function checkSequential(pw) {
    var lower = pw.toLowerCase();
    for (var i = 0; i <= lower.length - 3; i++) {
      var a = lower.charCodeAt(i), b = lower.charCodeAt(i+1), c = lower.charCodeAt(i+2);
      if (b - a === 1 && c - b === 1) return true;
      if (a - b === 1 && b - c === 1) return true;
    }
    return false;
  }

  function checkRepeated(pw) {
    for (var i = 0; i <= pw.length - 3; i++) {
      if (pw[i] === pw[i+1] && pw[i+1] === pw[i+2]) return true;
    }
    return false;
  }

  function checkDictionary(pw) {
    var common = ['password','letmein','welcome','monkey','dragon','master','qwerty','login','abc123','iloveyou','admin','sunshine','princess','football','shadow','trustno1','batman','access','hello','charlie','donald','password1','123456','12345678','1234567890','passw0rd','p@ssw0rd','pass@word','123456789','qwerty123','111111','1234567','12345','12345678910','baseball','soccer','hockey','superman','michael','ashley','jessica','maggie','summer','winter','spring','jordan','robert','daniel','jennifer','thomas','computer','whatever','ninja','mustang','hunter','batman','summer99','access14','tigger','buster','soccer1','harley','batman1','solo','photon','matrix'];
    var lower = pw.toLowerCase().replace(/[^a-z0-9]/g, '');
    for (var i = 0; i < common.length; i++) {
      if (lower === common[i]) return true;
    }
    return false;
  }

  function checkCommonPatterns(pw) {
    var lower = pw.toLowerCase();
    var patterns = ['password','qwerty','asdf','zxcv','1234','abcdef','abcd','1111','0000','aaaa'];
    for (var i = 0; i < patterns.length; i++) {
      if (lower.indexOf(patterns[i]) !== -1) return true;
    }
    return false;
  }

  function formatCrackTime(guesses) {
    var seconds = guesses / 1e3;
    if (seconds < 1) return 'Instant';
    if (seconds < 60) return Math.round(seconds) + ' seconds';
    var minutes = seconds / 60;
    if (minutes < 60) return Math.round(minutes) + ' minutes';
    var hours = minutes / 60;
    if (hours < 24) return Math.round(hours) + ' hours';
    var days = hours / 24;
    if (days < 365) return Math.round(days) + ' days';
    var years = days / 365.25;
    if (years < 1e3) return Math.round(years) + ' years';
    if (years < 1e6) return Math.round(years / 1e3) + 'K years';
    if (years < 1e9) return Math.round(years / 1e6) + 'M years';
    return Math.round(years / 1e9) + 'B years';
  }

  function renderAnalysis(analysis, raw) {
    strengthSection.style.display = 'block';
    statsDiv.style.display = 'flex';
    detailsDiv.style.display = 'block';

    meterFill.style.width = (analysis.score / 5 * 100) + '%';
    meterFill.style.background = analysis.color;
    strengthLabel.textContent = analysis.label;
    strengthLabel.style.color = analysis.color;

    scoreEl.textContent = analysis.score + '/4';
    entropyEl.textContent = Math.round(analysis.entropy) + ' bits';
    lengthEl.textContent = analysis.len + ' chars';

    var online100 = analysis.guessesPerSec / 100;
    var online1000 = analysis.guessesPerSec / 1000;
    var offline1e7 = analysis.guessesPerSec / 1e7;
    var offline1e4 = analysis.guessesPerSec / 1e4;

    onlineSlow.textContent = formatCrackTime(online100);
    onlineFast.textContent = formatCrackTime(online1000);
    offlineFast.textContent = formatCrackTime(offline1e7);
    offlineSlow.textContent = formatCrackTime(offline1e4);

    checklist.innerHTML = '';
    for (var i = 0; i < analysis.checks.length; i++) {
      var c = analysis.checks[i];
      var li = document.createElement('li');
      li.className = 'tc-pwc-check-item' + (c.pass ? ' pass' : ' fail');
      li.innerHTML = (c.pass ? '<span class="tc-pwc-check-icon">&#10003;</span> ' : '<span class="tc-pwc-check-icon">&#10007;</span> ') + c.text;
      checklist.appendChild(li);
    }

    tips.innerHTML = '';
    if (analysis.tips.length === 0) {
      var li = document.createElement('li');
      li.className = 'tc-pwc-tip-item good';
      li.innerHTML = '<span class="tc-pwc-check-icon">&#10003;</span> Your password looks great!';
      tips.appendChild(li);
    } else {
      for (var j = 0; j < analysis.tips.length; j++) {
        var li2 = document.createElement('li');
        li2.className = 'tc-pwc-tip-item';
        li2.textContent = analysis.tips[j];
        tips.appendChild(li2);
      }
    }

    var resultHTML = '<div class="tc-pwc-result-grid">';
    resultHTML += '<div class="tc-pwc-result-item"><span class="tc-pwc-result-label">Strength</span><span class="tc-pwc-result-val" style="color:' + analysis.color + ';font-weight:700;font-size:18px">' + analysis.label + '</span></div>';
    resultHTML += '<div class="tc-pwc-result-item"><span class="tc-pwc-result-label">Entropy</span><span class="tc-pwc-result-val">' + Math.round(analysis.entropy) + ' bits</span></div>';
    resultHTML += '<div class="tc-pwc-result-item"><span class="tc-pwc-result-label">Score</span><span class="tc-pwc-result-val">' + analysis.score + '/4</span></div>';
    resultHTML += '<div class="tc-pwc-result-item"><span class="tc-pwc-result-label">Length</span><span class="tc-pwc-result-val">' + analysis.len + ' chars</span></div>';
    resultHTML += '</div>';
    resultHTML += '<div class="tc-pwc-result-bar" style="margin-top:12px;height:6px;border-radius:3px;background:#e5e7eb;overflow:hidden"><div style="height:100%;width:' + (analysis.score / 5 * 100) + '%;background:' + analysis.color + ';border-radius:3px;transition:width .3s"></div></div>';
    resultCard.innerHTML = resultHTML;
  }
});
