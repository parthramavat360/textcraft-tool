/**
 * Text Summarizer — Extractive summarization
 * Uses word frequency + position scoring to pick key sentences.
 * @package TextCraft_Tools_Pro
 */
(function () {
  'use strict';

  var summarizeBtn = document.getElementById('tc-ts-summarize');
  if (!summarizeBtn) return;

  var inputEl     = document.getElementById('tc-ts-input');
  var outputEl    = document.getElementById('tc-ts-output');
  var outputBody  = document.getElementById('tc-ts-output-body');
  var highlightEl = document.getElementById('tc-ts-highlighted');
  var highlightBody = document.getElementById('tc-ts-highlighted-body');
  var mode = 'medium';
  var summaryText = '';

  /* ── Mode cards ─────────────────────────────────────────── */

  var modeCards = document.querySelectorAll('.tc-ts-modes .tc-rsz-mode-card');
  modeCards.forEach(function (card) {
    card.addEventListener('click', function () {
      modeCards.forEach(function (c) { c.classList.remove('sel'); });
      card.classList.add('sel');
      mode = card.getAttribute('data-val') || 'medium';
      var customRow = document.getElementById('tc-ts-custom-row');
      if (customRow) customRow.style.display = mode === 'custom' ? '' : 'none';
    });
  });

  var customSlider = document.getElementById('tc-ts-custom-count');
  var customVal = document.getElementById('tc-ts-custom-val');
  if (customSlider && customVal) {
    customSlider.addEventListener('input', function () {
      customVal.textContent = customSlider.value;
    });
  }

  /* ── Stopwords ──────────────────────────────────────────── */

  var stopwords = new Set([
    'a','an','the','and','or','but','in','on','at','to','for','of','with','by','from',
    'is','am','are','was','were','be','been','being','have','has','had','do','does',
    'did','will','would','could','should','may','might','shall','can','this','that',
    'these','those','it','its','i','me','my','we','our','you','your','he','him','his',
    'she','her','they','them','their','what','which','who','whom','when','where',
    'how','why','all','each','every','both','few','more','most','other','some','such',
    'no','not','only','own','same','so','than','too','very','just','about','above',
    'after','again','also','any','as','because','before','between','during','into',
    'through','until','while','if','then','once','here','there','now','even','still',
    'new','one','two','first','last','long','great','little','own','old','right',
    'big','high','different','small','large','next','early','young','important',
    'public','bad','same','able','free','best','better','sure','true','real',
    'left','right','less','most','us','over','such','need','many','much','well',
    'back','down','off','out','way','make','like','time','know','take','come',
    'think','look','want','give','use','find','tell','ask','work','seem','feel',
    'try','leave','call','keep','let','begin','show','hear','play','run','move',
    'live','believe','hold','bring','happen','must','put','mean','set','put'
  ]);

  /* ── Sentence splitting ─────────────────────────────────── */

  function splitSentences(text) {
    var sentences = text.match(/[^.!?]+[.!?]+[\s]*/g);
    if (!sentences) sentences = [text];
    return sentences.map(function (s) { return s.trim(); }).filter(function (s) { return s.length > 10; });
  }

  /* ── Word frequency ─────────────────────────────────────── */

  function getWordFreq(text) {
    var words = text.toLowerCase().replace(/[^a-z0-9\s]/g, '').split(/\s+/);
    var freq = {};
    var total = 0;
    words.forEach(function (w) {
      if (w.length < 3 || stopwords.has(w)) return;
      freq[w] = (freq[w] || 0) + 1;
      total++;
    });
    /* Normalize */
    Object.keys(freq).forEach(function (w) { freq[w] = freq[w] / total; });
    return freq;
  }

  /* ── Score sentences ────────────────────────────────────── */

  function scoreSentence(sentence, freq, idx, total) {
    var words = sentence.toLowerCase().replace(/[^a-z0-9\s]/g, '').split(/\s+/);
    var score = 0;
    var count = 0;

    words.forEach(function (w) {
      if (freq[w]) { score += freq[w]; count++; }
    });

    /* Normalize by sentence length */
    if (count > 0) score = score * (count / words.length);

    /* Position bonus: first 2 and last 2 sentences */
    if (idx === 0) score *= 1.4;
    else if (idx === 1) score *= 1.2;
    else if (idx === total - 1) score *= 1.3;
    else if (idx === total - 2) score *= 1.1;

    /* Length penalty: very short or very long */
    if (words.length < 5) score *= 0.5;
    if (words.length > 50) score *= 0.8;

    /* Title case bonus (likely a topic sentence) */
    if (/^[A-Z]/.test(sentence) && sentence.split(' ').length < 15) score *= 1.1;

    return score;
  }

  /* ── Summarize ──────────────────────────────────────────── */

  function summarize() {
    var text = inputEl ? inputEl.value.trim() : '';
    if (!text) { TCTP.toast('Paste some text first.', '⚠️'); return; }
    if (text.split(/\s+/).length < 20) { TCTP.toast('Text too short to summarize.', '⚠️'); return; }

    var highlightCb = document.getElementById('tc-ts-highlight');
    var bulletCb    = document.getElementById('tc-ts-bullet');
    var useHighlight = highlightCb ? highlightCb.checked : true;
    var useBullets   = bulletCb ? bulletCb.checked : false;

    /* Split and score */
    var sentences = splitSentences(text);
    if (sentences.length < 3) { TCTP.toast('Need at least 3 sentences.', '⚠️'); return; }

    var freq = getWordFreq(text);
    var total = sentences.length;

    var scored = sentences.map(function (s, i) {
      return { text: s, score: scoreSentence(s, freq, i, total), index: i };
    });

    /* Determine how many sentences to pick */
    var count;
    if (mode === 'custom') {
      count = parseInt(customSlider ? customSlider.value : 3, 10);
    } else if (mode === 'short') {
      count = Math.max(1, Math.ceil(total * 0.15));
    } else if (mode === 'long') {
      count = Math.max(1, Math.ceil(total * 0.5));
    } else {
      count = Math.max(1, Math.ceil(total * 0.3));
    }
    count = Math.min(count, sentences.length);

    /* Sort by score desc, take top, then reorder by original position */
    var top = scored.sort(function (a, b) { return b.score - a.score; })
                     .slice(0, count)
                     .sort(function (a, b) { return a.index - b.index; });

    var summarySentences = top.map(function (t) { return t.text; });
    var topIndices = new Set(top.map(function (t) { return t.index; }));

    summaryText = summarySentences.join(' ');

    /* Output */
    if (outputEl) outputEl.style.display = '';
    if (outputBody) {
      outputBody.innerHTML = '';
      if (useBullets) {
        var ul = document.createElement('ul');
        ul.style.cssText = 'margin:0;padding:0 0 0 16px;list-style:disc';
        summarySentences.forEach(function (s) {
          var li = document.createElement('li');
          li.style.cssText = 'margin-bottom:8px;line-height:1.7;color:var(--body,#cbd5e1);font-size:14px';
          li.textContent = s;
          ul.appendChild(li);
        });
        outputBody.appendChild(ul);
      } else {
        var p = document.createElement('p');
        p.style.cssText = 'margin:0;line-height:1.75;color:var(--body,#cbd5e1);font-size:14px';
        p.textContent = summaryText;
        outputBody.appendChild(p);
      }
    }

    var badge = document.getElementById('tc-ts-output-badge');
    if (badge) badge.textContent = count + ' of ' + total + ' sentences';

    /* Highlighted original */
    if (useHighlight) {
      highlightEl.style.display = '';
      highlightBody.innerHTML = '';
      sentences.forEach(function (s, i) {
        var span = document.createElement('span');
        span.textContent = s + ' ';
        if (topIndices.has(i)) {
          span.style.cssText = 'background:rgba(37,99,235,0.2);color:#93c5fd;padding:2px 4px;border-radius:3px;font-weight:500';
        } else {
          span.style.color = 'var(--muted,#64748b)';
        }
        highlightBody.appendChild(span);
      });
    } else {
      highlightEl.style.display = 'none';
    }

    /* Stats */
    var origWords = text.split(/\s+/).length;
    var summWords = summaryText.split(/\s+/).length;
    var reduction = origWords > 0 ? Math.round((1 - summWords / origWords) * 100) : 0;

    var sO = document.getElementById('tc-stat-orig');
    var sC = document.getElementById('tc-stat-comp');
    var sS = document.getElementById('tc-stat-saved');
    if (sO) sO.textContent = origWords;
    if (sC) sC.textContent = summWords;
    if (sS) sS.textContent = reduction + '%';

    var statOrig = document.getElementById('tc-ts-stat-original');
    var statSumm = document.getElementById('tc-ts-stat-summary');
    var statRed  = document.getElementById('tc-ts-stat-reduction');
    var statSent = document.getElementById('tc-ts-stat-sentences');
    if (statOrig) statOrig.textContent = origWords.toLocaleString();
    if (statSumm) statSumm.textContent = summWords.toLocaleString();
    if (statRed) statRed.textContent = reduction + '%';
    if (statSent) statSent.textContent = count;

    var chip = document.getElementById('tc-status-chip');
    if (chip) chip.textContent = reduction + '% reduction';

    /* Preview */
    var prevSumm = document.getElementById('tc-ts-preview-summary');
    if (prevSumm) {
      prevSumm.innerHTML = '';
      var pp = document.createElement('p');
      pp.style.cssText = 'margin:0;line-height:1.75;color:var(--body,#cbd5e1);font-size:14px';
      pp.textContent = summaryText;
      prevSumm.appendChild(pp);
    }
    var prevHigh = document.getElementById('tc-ts-preview-highlight');
    if (prevHigh && useHighlight) {
      prevHigh.innerHTML = '';
      sentences.forEach(function (s, i) {
        var sp = document.createElement('span');
        sp.textContent = s + ' ';
        if (topIndices.has(i)) {
          sp.style.cssText = 'background:rgba(37,99,235,0.2);color:#93c5fd;padding:2px 4px;border-radius:3px;font-weight:500';
        } else {
          sp.style.color = 'var(--muted,#64748b)';
        }
        prevHigh.appendChild(sp);
      });
    }

    TCTP.switchToResultTab();
    TCTP.toast('Summary: ' + summWords + ' words (' + reduction + '% shorter)', '✅');
  }

  summarizeBtn.addEventListener('click', summarize);

  /* Copy */
  var copyBtn = document.getElementById('tc-ts-copy');
  if (copyBtn) {
    copyBtn.addEventListener('click', function () {
      if (!summaryText) { TCTP.toast('Summarize first.', '⚠️'); return; }
      TCTP.copyText(summaryText);
      TCTP.toast('Copied!', '✅');
    });
  }

  /* Clear */
  var clearBtn = document.getElementById('tc-ts-clear');
  if (clearBtn) {
    clearBtn.addEventListener('click', function () {
      if (inputEl) inputEl.value = '';
      summaryText = '';
      if (outputEl) outputEl.style.display = 'none';
      if (highlightEl) highlightEl.style.display = 'none';
    });
  }
})();
