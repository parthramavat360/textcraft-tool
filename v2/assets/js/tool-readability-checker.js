/**
 * Readability Checker — Flesch-Kincaid, Coleman-Liau, SMOG, ARI, Dale-Chall
 * @package TextCraft_Tools_Pro
 */
(function () {
  'use strict';

  var analyzeBtn = document.getElementById('tc-rc-analyze');
  if (!analyzeBtn) return;

  var inputEl    = document.getElementById('tc-rc-input');
  var resultsEl  = document.getElementById('tc-rc-results');
  var targetMode = 'general';

  /* ── Mode cards ──────────────────────────────────────────── */

  var modeCards = document.querySelectorAll('.tc-rc-modes .tc-rsz-mode-card');
  modeCards.forEach(function (card) {
    card.addEventListener('click', function () {
      modeCards.forEach(function (c) { c.classList.remove('sel'); });
      card.classList.add('sel');
      targetMode = card.getAttribute('data-val') || 'general';
    });
  });

  /* ── Syllable counter ────────────────────────────────────── */

  function countSyllables(word) {
    word = word.toLowerCase().replace(/[^a-z]/g, '');
    if (!word) return 0;
    if (word.length <= 3) return 1;
    word = word.replace(/(?:[^laeiouy]es|ed|[^laeiouy]e)$/, '');
    word = word.replace(/^y/, '');
    var m = word.match(/[aeiouy]{1,2}/g);
    return m ? m.length : 1;
  }

  /* ── Dale-Chall word list (simplified common difficult words) ── */

  var difficultWords = new Set([
    'abandon','abolish','abstract','academic','accelerate','accomplish','accurate',
    'achieve','acquire','address','adequate','advocate','affirm','aggressive',
    'agriculture','allegation','allocate','alter','alternative','ambiguous',
    'amend','analogy','analyze','annual','anticipate','apparent','appetite',
    'arbitrary','assemble','assert','assess','assign','assist','assume',
    'authority','automatic','available','benefit','bureau','capable','capacity',
    'category','challenge','circumstance','coherent','coincide','collaborate',
    'commence','commit','communicate','compact','compensate','competent',
    'comprehensive','conceive','concentrate','concept','conclude','concrete',
    'conduct','confine','confirm','conflict','confront','congress','conscience',
    'conscious','consequence','considerable','consistent','constitute','construct',
    'consult','consume','contemplate','contradict','contribute','controversial',
    'convention','coordinate','correspond','criteria','crucial','currency',
    'curriculum','database','debate','decade','decide','decline','dedicate',
    'definite','demonstrate','deny','deprecate','derive','describe','design',
    'desire','despite','detect','determine','develop','devote','dimension',
    'diminish','discriminate','displace','distinct','distribute','diverse',
    'domestic','dominate','dynamic','economy','edition','educate','element',
    'eliminate','emerge','emphasis','enable','encounter','enhance','enormous',
    'ensure','enterprise','entire','environment','equivalent','erode','establish',
    'estimate','evaluate','eventually','evident','evolve','exceed','except',
    'excess','exclude','execute','exhibit','expand','expertise','explicit',
    'exploit','expose','external','extract','facilitate','factor','feature',
    'federal','finance','flexible','fluctuate','focus','forecast','formula',
    'foundation','framework','function','fundamental','generate','genuine',
    'global','guarantee','guideline','hierarchy','hypothesis','identical',
    'ideology','illustrate','immense','implement','implicit','imply','impose',
    'incentive','incident','incorporate','indicate','individual','inevitable',
    'infrastructure','initial','innocent','innovate','integrate','intellect',
    'intense','interact','interpret','intervene','investigate','involve',
    'isolate','justify','label','legislation','legitimate','liberal',
    'likewise','maintain','manifest','manipulate','mechanism','media',
    'method','migrate','military','minister','minor','modify','monitor',
    'mutual','negotiate','network','neutral','nevertheless','norm',
    'notion','nuclear','objective','oblige','obtain','obvious','occupy',
    'occur','offset','ongoing','option','orient','outcome','output',
    'overall','overlap','overseas','parallel','parameter','participate',
    'partner','passive','perceive','period','persist','perspective','phase',
    'phenomenon','philosophy','policy','portion','pose','potential','precede',
    'precise','predict','predominant','preliminary','presume','previous',
    'primary','principle','proceed','professional','prohibit','project',
    'promote','proportion','prospect','protocol','pursue','qualify','quote',
    'radical','random','ratio','react','recover','regime','region','regulate',
    'reinforce','reject','relevant','reluctant','rely','remove','require',
    'research','reside','resolve','resource','respond','restore','restrict',
    'retain','reveal','revenue','reverse','revolution','role','route',
    'scenario','schedule','scheme','scope','section','sector','select',
    'sequence','significant','similar','simultaneous','specify','sphere',
    'stable','strategy','structure','submit','subordinate','subsequent',
    'substance','substitute','succeed','sufficient','summarize','supplement',
    'survey','survive','suspect','sustain','symbol','sympathy','technique',
    'technology','temporary','tension','terminate','theme','theory','thereby',
    'thesis','tradition','transfer','transform','transition','transmit',
    'transport','trend','trigger','ultimate','undergo','underline','undermine',
    'undertake','uniform','utilize','valid','vary','vehicle','version',
    'violate','virtual','visible','volume','voluntary','welfare','whereas',
    'widespread','withdraw','workforce'
  ]);

  /* ── Main analysis ───────────────────────────────────────── */

  function analyze() {
    var text = inputEl.value.trim();
    if (!text) { TCTP.toast('Please enter some text.', '⚠️'); return; }

    /* Split into sentences */
    var sentences = text.match(/[^.!?]+[.!?]+/g) || [text];
    sentences = sentences.map(function(s) { return s.trim(); }).filter(Boolean);
    var sentenceCount = sentences.length;

    /* Split into words */
    var words = text.match(/[a-zA-Z']+/g) || [];
    var wordCount = words.length;

    if (wordCount === 0) { TCTP.toast('No words found.', '⚠️'); return; }

    /* Count syllables */
    var totalSyllables = 0;
    words.forEach(function (w) { totalSyllables += countSyllables(w); });

    /* Count characters (letters only) */
    var charCount = text.replace(/[^a-zA-Z]/g, '').length;

    /* Count complex words (3+ syllables) */
    var complexWords = 0;
    words.forEach(function (w) { if (countSyllables(w) >= 3) complexWords++; });

    /* Difficult words (Dale-Chall) */
    var difficultCount = 0;
    words.forEach(function (w) { if (difficultWords.has(w.toLowerCase())) difficultCount++; });

    var avgWordsPerSentence = wordCount / Math.max(sentenceCount, 1);
    var avgSyllablesPerWord = totalSyllables / Math.max(wordCount, 1);

    /* ── Flesch Reading Ease ─────────────────────────────── */
    var fleschEase = 206.835 - (1.015 * avgWordsPerSentence) - (84.6 * avgSyllablesPerWord);
    fleschEase = Math.max(0, Math.min(100, fleschEase));

    /* ── Flesch-Kincaid Grade Level ──────────────────────── */
    var fkGrade = (0.39 * avgWordsPerSentence) + (11.8 * avgSyllablesPerWord) - 15.59;

    /* ── Coleman-Liau Index ──────────────────────────────── */
    var L = (charCount / Math.max(wordCount, 1)) * 100;
    var S = (sentenceCount / Math.max(wordCount, 1)) * 100;
    var colemanLiau = (0.0588 * L) - (0.296 * S) - 15.8;

    /* ── SMOG Index ─────────────────────────────────────── */
    var smog = 1.0430 * Math.sqrt(complexWords * (30 / Math.max(sentenceCount, 1))) + 3.1291;

    /* ── Automated Readability Index ─────────────────────── */
    var ari = (4.71 * (charCount / Math.max(wordCount, 1))) + (0.5 * avgWordsPerSentence) - 21.43;

    /* ── Dale-Chall Readability Score ────────────────────── */
    var p = (difficultCount / Math.max(wordCount, 1)) * 100;
    var daleChall = (0.1579 * p) + (0.0496 * avgWordsPerSentence);
    if (p > 5) daleChall += 3.6365;

    /* ── Linsear Write ───────────────────────────────────── */
    var easyWords = 0;
    words.forEach(function (w) { if (countSyllables(w) <= 2) easyWords++; });
    var linsear = ((easyWords * 1) + ((wordCount - easyWords) * 3)) / Math.max(sentenceCount, 1);
    if (linsear > 20) linsear = linsear / 2;
    else linsear = (linsear / 2) - 1;

    /* ── Reading level labels ────────────────────────────── */
    function easeLabel(score) {
      if (score >= 90) return 'Very Easy';
      if (score >= 80) return 'Easy';
      if (score >= 70) return 'Fairly Easy';
      if (score >= 60) return 'Standard';
      if (score >= 50) return 'Fairly Difficult';
      if (score >= 30) return 'Difficult';
      return 'Very Confusing';
    }

    function gradeLabel(score) {
      if (score <= 5) return 'Elementary School';
      if (score <= 8) return 'Middle School';
      if (score <= 12) return 'High School';
      if (score <= 16) return 'College Level';
      return 'Post-Graduate';
    }

    function easeColor(score) {
      if (score >= 80) return '#10b981';
      if (score >= 60) return '#f59e0b';
      if (score >= 40) return '#f97316';
      return '#ef4444';
    }

    /* ── Update UI ───────────────────────────────────────── */

    resultsEl.style.display = '';

    /* Score ring */
    var ringFill = document.getElementById('tc-rc-ring-fill');
    var scoreNum = document.getElementById('tc-rc-score-num');
    var scoreLabel = document.getElementById('tc-rc-score-label');
    var levelBadge = document.getElementById('tc-rc-level-badge');
    var circumference = 2 * Math.PI * 54;
    var offset = circumference - (fleschEase / 100) * circumference;

    if (ringFill) {
      ringFill.style.strokeDashoffset = offset;
      ringFill.style.stroke = easeColor(fleschEase);
    }
    if (scoreNum) {
      scoreNum.textContent = Math.round(fleschEase);
      scoreNum.style.color = easeColor(fleschEase);
    }
    if (scoreLabel) scoreLabel.textContent = easeLabel(fleschEase);
    if (levelBadge) {
      levelBadge.textContent = gradeLabel(fkGrade) + ' (Grade ' + Math.round(fkGrade) + ')';
      levelBadge.style.borderColor = easeColor(fleschEase);
      levelBadge.style.color = easeColor(fleschEase);
    }

    /* Metric cards */
    var setText = function(id, val) { var el = document.getElementById(id); if (el) el.textContent = val; };
    setText('tc-rc-fk-grade', Math.round(fkGrade * 10) / 10);
    setText('tc-rc-coleman', Math.round(colemanLiau * 10) / 10);
    setText('tc-rc-smog', Math.round(smog * 10) / 10);
    setText('tc-rc-ari', Math.round(ari * 10) / 10);
    setText('tc-rc-dale', Math.round(daleChall * 10) / 10);
    setText('tc-rc-linx', Math.round(linsear * 10) / 10);

    /* Stats */
    setText('tc-rc-words', wordCount.toLocaleString());
    setText('tc-rc-sentences', sentenceCount);
    setText('tc-rc-syllables', totalSyllables);
    setText('tc-rc-avg-wps', Math.round(avgWordsPerSentence * 10) / 10);
    setText('tc-rc-avg-sps', Math.round(avgSyllablesPerWord * 100) / 100);
    setText('tc-rc-characters', charCount.toLocaleString());

    /* Update result panel */
    setText('tc-stat-orig', wordCount);
    setText('tc-stat-comp', sentenceCount);
    setText('tc-stat-saved', totalSyllables);
    var chip = document.getElementById('tc-status-chip');
    if (chip) chip.textContent = easeLabel(fleschEase);

    /* Detail scores */
    var detailScores = document.getElementById('tc-rc-detail-scores');
    if (detailScores) {
      detailScores.innerHTML =
        '<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">' +
        '<div class="tc-rc-detail-item"><span class="tc-rc-detail-name">Flesch Reading Ease</span><span class="tc-rc-detail-val" style="color:' + easeColor(fleschEase) + '">' + Math.round(fleschEase) + '/100</span><span class="tc-rc-detail-desc">' + easeLabel(fleschEase) + '</span></div>' +
        '<div class="tc-rc-detail-item"><span class="tc-rc-detail-name">Flesch-Kincaid Grade</span><span class="tc-rc-detail-val">Grade ' + Math.round(fkGrade) + '</span><span class="tc-rc-detail-desc">' + gradeLabel(fkGrade) + '</span></div>' +
        '<div class="tc-rc-detail-item"><span class="tc-rc-detail-name">Coleman-Liau Index</span><span class="tc-rc-detail-val">' + Math.round(colemanLiau * 10) / 10 + '</span><span class="tc-rc-detail-desc">' + gradeLabel(colemanLiau) + '</span></div>' +
        '<div class="tc-rc-detail-item"><span class="tc-rc-detail-name">SMOG Index</span><span class="tc-rc-detail-val">' + Math.round(smog * 10) / 10 + '</span><span class="tc-rc-detail-desc">' + gradeLabel(smog) + '</span></div>' +
        '<div class="tc-rc-detail-item"><span class="tc-rc-detail-name">Automated Readability</span><span class="tc-rc-detail-val">' + Math.round(ari * 10) / 10 + '</span><span class="tc-rc-detail-desc">' + gradeLabel(ari) + '</span></div>' +
        '<div class="tc-rc-detail-item"><span class="tc-rc-detail-name">Dale-Chall Score</span><span class="tc-rc-detail-val">' + Math.round(daleChall * 100) / 100 + '</span><span class="tc-rc-detail-desc">' + (daleChall < 5 ? 'Easy' : 'Difficult') + '</span></div>' +
        '</div>';
    }

    /* Tips */
    var tips = [];
    if (avgWordsPerSentence > 20) tips.push('Your sentences are long (avg ' + Math.round(avgWordsPerSentence) + ' words). Try splitting into shorter sentences for better readability.');
    if (avgSyllablesPerWord > 1.6) tips.push('Your words tend to be complex (avg ' + Math.round(avgSyllablesPerWord * 100) / 100 + ' syllables). Use simpler words where possible.');
    if (complexWords > wordCount * 0.15) tips.push('You have many complex words (3+ syllables). Replace jargon with plain language.');
    if (fleschEase < 50) tips.push('Your text is hard to read (score ' + Math.round(fleschEase) + '). Aim for shorter sentences and common words.');
    if (fleschEase >= 70) tips.push('Good readability! Your text is easy to understand for most readers.');
    if (fkGrade > 12) tips.push('Your text requires college-level reading. Consider simplifying for a broader audience.');
    if (difficultCount > 0) tips.push('Found ' + difficultCount + ' difficult words from the Dale-Chall list. Replace with simpler alternatives.');

    var targetTips = {
      general: 'For general audiences, aim for a Flesch Reading Ease score above 60 (8th grade level).',
      academic: 'For academic writing, a score of 30-50 (college level) is appropriate.',
      business: 'For business communication, aim for 60-70 (8th-10th grade) for clarity.',
      child: 'For children, aim for a Flesch Reading Ease above 80 (5th-6th grade level).'
    };
    tips.push(targetTips[targetMode] || targetTips.general);

    var tipsEl = document.getElementById('tc-rc-detail-tips');
    if (tipsEl) {
      tipsEl.innerHTML = '<ul style="margin:0;padding:0 0 0 16px">' +
        tips.map(function(t) { return '<li style="margin-bottom:10px;line-height:1.6;color:var(--body,#cbd5e1)">' + t + '</li>'; }).join('') +
        '</ul>';
    }

    /* Suggestions box */
    var sugEl = document.getElementById('tc-rc-suggestions');
    if (sugEl) {
      sugEl.innerHTML = '<div class="tc-rc-suggestion-box">' +
        '<h4 style="margin:0 0 10px;color:var(--ink,#f1f5f9);font-size:14px">Quick Summary</h4>' +
        '<p style="margin:0 0 8px;color:var(--body,#cbd5e1);font-size:13px;line-height:1.6">' +
        'Your text has a <b>Reading Ease score of ' + Math.round(fleschEase) + '</b> (' + easeLabel(fleschEase) + ') ' +
        'and is at a <b>Grade ' + Math.round(fkGrade) + '</b> level (' + gradeLabel(fkGrade) + '). ' +
        'It contains <b>' + wordCount + ' words</b> across <b>' + sentenceCount + ' sentences</b>, ' +
        'with an average of <b>' + Math.round(avgWordsPerSentence) + ' words per sentence</b>.</p>' +
        '</div>';
    }

    TCTP.switchToResultTab();
    TCTP.toast('Readability analysis complete!', '✅');
  }

  analyzeBtn.addEventListener('click', analyze);

  /* Clear */
  var clearBtn = document.getElementById('tc-rc-clear');
  if (clearBtn) {
    clearBtn.addEventListener('click', function () {
      inputEl.value = '';
      resultsEl.style.display = 'none';
    });
  }
})();
