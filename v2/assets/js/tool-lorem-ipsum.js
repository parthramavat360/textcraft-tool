/**
 * Lorem Ipsum Generator — Premium
 * Generate placeholder text in paragraphs, sentences, or words.
 *
 * @package TextCraft_Tools_Pro
 */
(function () {
  'use strict';

  var genBtn = document.getElementById('tc-li-generate');
  if (!genBtn) return;

  var mode = 'paragraphs';
  var generatedText = '';

  var modeCards    = document.querySelectorAll('.tc-li-modes .tc-rsz-mode-card');
  var countSlider  = document.getElementById('tc-li-count');
  var countVal     = document.getElementById('tc-li-count-val');
  var startCheck   = document.getElementById('tc-li-start');
  var htmlCheck    = document.getElementById('tc-li-html');
  var numbersCheck = document.getElementById('tc-li-numbers');
  var output       = document.getElementById('tc-li-output');

  /* ── Word pools ──────────────────────────────────────────── */

  var words = [
    'lorem','ipsum','dolor','sit','amet','consectetur','adipiscing','elit',
    'sed','do','eiusmod','tempor','incididunt','ut','labore','et','dolore',
    'magna','aliqua','enim','ad','minim','veniam','quis','nostrud',
    'exercitation','ullamco','laboris','nisi','aliquip','ex','ea','commodo',
    'consequat','duis','aute','irure','in','reprehenderit','voluptate',
    'velit','esse','cillum','fugiat','nulla','pariatur','excepteur','sint',
    'occaecat','cupidatat','non','proident','sunt','culpa','qui','officia',
    'deserunt','mollit','anim','id','est','laborum','perspiciatis','unde',
    'omnis','iste','natus','error','voluptatem','accusantium','doloremque',
    'laudantium','totam','rem','aperiam','eaque','ipsa','quae','ab','illo',
    'inventore','veritatis','quasi','architecto','beatae','vitae','dicta',
    'explicabo','nemo','ipsam','quia','voluptas','aspernatur','aut','odit',
    'fugit','consequuntur','magni','dolores','ratione','sequi','nesciunt',
    'neque','porro','quisquam','nihil','impedit','quo','minus','maxime',
    'placeat','facere','possimus','omnis','repellat','temporibus','quibusdam',
    'officiis','debitis','rerum','necessitatibus','saepe','eveniet','voluptates',
    'repudiandae','molestiae','recusandae','ducimus','blanditiis','praesentium',
    'voluptatum','deleniti','atque','corrupti','quos','quas','molestias',
    'excepturi','occaecati','cupiditate','similique'
  ];

  var startPhrase = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

  /* ── Generate functions ──────────────────────────────────── */

  function randomWord() {
    return words[Math.floor(Math.random() * words.length)];
  }

  function generateSentence() {
    var len = 8 + Math.floor(Math.random() * 15);
    var parts = [];
    for (var i = 0; i < len; i++) parts.push(randomWord());
    parts[0] = parts[0].charAt(0).toUpperCase() + parts[0].slice(1);
    return parts.join(' ') + '.';
  }

  function generateParagraph() {
    var sentenceCount = 3 + Math.floor(Math.random() * 6);
    var parts = [];
    for (var i = 0; i < sentenceCount; i++) parts.push(generateSentence());
    return parts.join(' ');
  }

  /* ── Mode cards ──────────────────────────────────────────── */

  modeCards.forEach(function (card) {
    card.addEventListener('click', function () {
      modeCards.forEach(function (c) { c.classList.remove('sel'); });
      card.classList.add('sel');
      mode = card.getAttribute('data-val') || 'paragraphs';
    });
  });

  /* ── Count slider ────────────────────────────────────────── */

  if (countSlider) {
    countSlider.addEventListener('input', function () {
      if (countVal) countVal.textContent = countSlider.value;
    });
  }

  /* ── Generate ────────────────────────────────────────────── */

  genBtn.addEventListener('click', function () {
    var count      = parseInt(countSlider ? countSlider.value : 5, 10);
    var useStart   = startCheck  ? startCheck.checked  : true;
    var useHTML    = htmlCheck   ? htmlCheck.checked   : false;
    var useNumbers = numbersCheck ? numbersCheck.checked : false;
    var result     = [];

    if (mode === 'paragraphs') {
      for (var i = 0; i < count; i++) {
        var p = generateParagraph();
        if (i === 0 && useStart) p = startPhrase + ' ' + p;
        if (useNumbers) p = '<b>¶ ' + (i + 1) + '.</b> ' + p;
        result.push(useHTML ? '<p>' + p + '</p>' : p);
      }
      generatedText = useHTML ? result.join('\n\n') : result.join('\n\n');
    } else if (mode === 'sentences') {
      for (var i = 0; i < count; i++) {
        var s = generateSentence();
        if (i === 0 && useStart) s = startPhrase;
        result.push(s);
      }
      generatedText = result.join(' ');
    } else {
      for (var i = 0; i < count; i++) {
        result.push(randomWord());
      }
      if (useStart && result.length > 0) {
        result[0] = 'Lorem';
        if (result.length > 1) result[1] = 'ipsum';
      }
      generatedText = result.join(' ');
    }

    /* Update output panel */
    if (output) {
      output.innerHTML = '';
      if (mode === 'paragraphs') {
        var paragraphs = generatedText.split('\n\n');
        paragraphs.forEach(function (p, idx) {
          var el = document.createElement('div');
          el.className = 'tc-li-para';
          if (useNumbers) el.innerHTML = '<span class="tc-li-para-num">¶ ' + (idx + 1) + '</span>';
          var textEl = document.createElement('p');
          textEl.textContent = p.replace(/<[^>]*>/g, '');
          el.appendChild(textEl);
          output.appendChild(el);
        });
      } else {
        var el = document.createElement('p');
        el.style.lineHeight = '1.7';
        el.style.margin = '0';
        el.textContent = generatedText;
        output.appendChild(el);
      }
    }

    /* Stats */
    var wordCount    = generatedText.replace(/<[^>]*>/g, '').split(/\s+/).length;
    var charCount    = generatedText.replace(/<[^>]*>/g, '').length;
    var paraCount    = mode === 'paragraphs' ? generatedText.split('\n\n').length : 1;
    var sentenceCount = (generatedText.match(/[.!?]+/g) || []).length;

    var statParas = document.getElementById('tc-li-stat-paras');
    var statWords = document.getElementById('tc-li-stat-words');
    var statChars = document.getElementById('tc-li-stat-chars');
    var statSents = document.getElementById('tc-li-stat-sentences');
    if (statParas) statParas.textContent = paraCount;
    if (statWords) statWords.textContent = wordCount;
    if (statChars) statChars.textContent = charCount;
    if (statSents) statSents.textContent = sentenceCount;

    TCTP.updateResultPanel(paraCount, wordCount, charCount, 'Done');
    TCTP.switchToResultTab();

    /* Formatted preview */
    var formattedBox = document.getElementById('tc-li-preview-formatted');
    if (formattedBox) {
      formattedBox.innerHTML = '';
      if (mode === 'paragraphs') {
        paragraphs.forEach(function (p, idx) {
          var para = document.createElement('p');
          para.style.marginBottom = '14px';
          para.style.lineHeight = '1.75';
          para.style.color = 'var(--body, #cbd5e1)';
          para.style.fontSize = '14px';
          if (useNumbers) {
            var num = document.createElement('span');
            num.style.color = 'var(--accent, #0b1220)';
            num.style.fontWeight = '700';
            num.style.marginRight = '6px';
            num.textContent = '¶ ' + (idx + 1);
            para.appendChild(num);
          }
          var txt = document.createElement('span');
          txt.textContent = p.replace(/<[^>]*>/g, '');
          para.appendChild(txt);
          formattedBox.appendChild(para);
        });
      } else {
        var para = document.createElement('p');
        para.style.lineHeight = '1.75';
        para.style.color = 'var(--body, #cbd5e1)';
        para.style.fontSize = '14px';
        para.style.margin = '0';
        para.textContent = generatedText;
        formattedBox.appendChild(para);
      }
    }

    /* Plain text preview */
    var plainBox = document.getElementById('tc-li-preview-plain');
    if (plainBox) {
      plainBox.textContent = '';
      var pre = document.createElement('pre');
      pre.style.cssText = 'margin:0;white-space:pre-wrap;word-wrap:break-word;font-family:inherit;font-size:13px;color:var(--body,#cbd5e1);line-height:1.7';
      pre.textContent = generatedText.replace(/<[^>]*>/g, '');
      plainBox.appendChild(pre);
    }

    TCTP.toast('Generated ' + wordCount + ' words!', '✅');
  });

  /* ── Copy ─────────────────────────────────────────────────── */

  var copyBtn = document.getElementById('tc-li-copy');
  if (copyBtn) {
    copyBtn.addEventListener('click', function () {
      if (!generatedText) { TCTP.toast('Generate text first.', '⚠️'); return; }
      TCTP.copyText(generatedText.replace(/<[^>]*>/g, ''));
      TCTP.toast('Copied to clipboard!', '✅');
    });
  }

  /* ── Download ─────────────────────────────────────────────── */

  var dlBtn = document.getElementById('tc-li-download');
  if (dlBtn) {
    dlBtn.addEventListener('click', function () {
      if (!generatedText) { TCTP.toast('Generate text first.', '⚠️'); return; }
      var blob = new Blob([generatedText.replace(/<[^>]*>/g, '')], { type: 'text/plain' });
      var a = document.createElement('a');
      a.href = URL.createObjectURL(blob);
      a.download = 'lorem-ipsum.txt';
      a.click();
      URL.revokeObjectURL(a.href);
      TCTP.toast('Downloaded!', '✅');
    });
  }
})();
