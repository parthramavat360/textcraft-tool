/**
 * Speech to Text — Web Speech Recognition API
 * @package TextCraft_Tools_Pro
 */
(function () {
  'use strict';

  var micBtn = document.getElementById('tc-stt-mic');
  if (!micBtn) return;

  var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  var outputEl    = document.getElementById('tc-stt-output');
  var statusEl    = document.getElementById('tc-stt-status');
  var langSel     = document.getElementById('tc-stt-lang');
  var continuousCb = document.getElementById('tc-stt-continuous');
  var punctCb     = document.getElementById('tc-stt-punct');
  var supportedEl = document.getElementById('tc-stt-supported');

  if (!SpeechRecognition) {
    if (supportedEl) supportedEl.style.display = '';
    micBtn.disabled = true;
    micBtn.style.opacity = '0.3';
    return;
  }

  var recognition = new SpeechRecognition();
  var isListening = false;
  var startTime = 0;
  var timerInterval = null;
  var finalTranscript = '';

  recognition.continuous = true;
  recognition.interimResults = true;
  recognition.maxAlternatives = 1;

  /* ── Language ──────────────────────────────────────────── */

  if (langSel) {
    recognition.lang = langSel.value;
    langSel.addEventListener('change', function () {
      recognition.lang = langSel.value;
    });
  }

  /* ── Continuous toggle ─────────────────────────────────── */

  if (continuousCb) {
    continuousCb.addEventListener('change', function () {
      recognition.continuous = continuousCb.checked;
    });
  }

  /* ── Timer ─────────────────────────────────────────────── */

  function formatTime(secs) {
    var m = Math.floor(secs / 60);
    var s = secs % 60;
    return m + ':' + (s < 10 ? '0' : '') + s;
  }

  function startTimer() {
    startTime = Date.now();
    timerInterval = setInterval(function () {
      var elapsed = Math.floor((Date.now() - startTime) / 1000);
      var timeEl = document.getElementById('tc-stt-stat-time');
      if (timeEl) timeEl.textContent = formatTime(elapsed);
    }, 1000);
  }

  function stopTimer() {
    if (timerInterval) clearInterval(timerInterval);
    timerInterval = null;
  }

  /* ── Update stats ──────────────────────────────────────── */

  function updateStats() {
    var text = outputEl ? outputEl.value : '';
    var words = text.trim() ? text.trim().split(/\s+/).length : 0;
    var chars = text.length;

    var wEl = document.getElementById('tc-stt-stat-words');
    var cEl = document.getElementById('tc-stt-stat-chars');
    if (wEl) wEl.textContent = words;
    if (cEl) cEl.textContent = chars;

    /* Update result panel */
    var sO = document.getElementById('tc-stat-orig');
    var sC = document.getElementById('tc-stat-comp');
    var sS = document.getElementById('tc-stat-saved');
    if (sO) sO.textContent = words;
    if (sC) sC.textContent = chars;
    if (sS) sS.textContent = formatTime(Math.floor((Date.now() - startTime) / 1000));

    /* Preview */
    var preview = document.getElementById('tc-stt-preview');
    if (preview) {
      preview.innerHTML = '';
      if (text) {
        var p = document.createElement('p');
        p.style.cssText = 'margin:0;line-height:1.75;color:var(--body,#cbd5e1);font-size:14px';
        p.textContent = text;
        preview.appendChild(p);
      } else {
        var ph = document.createElement('p');
        ph.className = 'tc-stt-placeholder';
        ph.textContent = 'Your transcript will appear here';
        preview.appendChild(ph);
      }
    }
  }

  /* ── Recognition events ────────────────────────────────── */

  recognition.onstart = function () {
    isListening = true;
    micBtn.classList.add('listening');
    if (statusEl) statusEl.textContent = 'Listening...';
    startTimer();
  };

  recognition.onresult = function (event) {
    var interim = '';
    for (var i = event.resultIndex; i < event.results.length; i++) {
      var transcript = event.results[i][0].transcript;
      if (event.results[i].isFinal) {
        finalTranscript += transcript + ' ';
      } else {
        interim += transcript;
      }
    }
    if (outputEl) {
      outputEl.value = finalTranscript + interim;
    }
    updateStats();
  };

  recognition.onerror = function (event) {
    if (event.error === 'no-speech') {
      if (statusEl) statusEl.textContent = 'No speech detected. Try again.';
    } else if (event.error !== 'aborted') {
      if (statusEl) statusEl.textContent = 'Error: ' + event.error;
    }
    stopListening();
  };

  recognition.onend = function () {
    if (isListening && continuousCb && continuousCb.checked) {
      /* Auto-restart for continuous mode */
      try { recognition.start(); } catch (e) { stopListening(); }
    } else {
      stopListening();
    }
  };

  function stopListening() {
    isListening = false;
    micBtn.classList.remove('listening');
    stopTimer();
    if (statusEl) {
      var text = outputEl ? outputEl.value : '';
      statusEl.textContent = text ? 'Finished' : 'Click the microphone to start';
    }
  }

  /* ── Microphone button ─────────────────────────────────── */

  micBtn.addEventListener('click', function () {
    if (isListening) {
      recognition.stop();
      stopListening();
    } else {
      finalTranscript = '';
      if (outputEl) outputEl.value = '';
      updateStats();
      try {
        recognition.start();
      } catch (e) {
        if (statusEl) statusEl.textContent = 'Error starting recognition. Try again.';
      }
    }
  });

  /* ── Copy ──────────────────────────────────────────────── */

  var copyBtn = document.getElementById('tc-stt-copy');
  if (copyBtn) {
    copyBtn.addEventListener('click', function () {
      var text = outputEl ? outputEl.value : '';
      if (!text) { TCTP.toast('No text to copy.', '⚠️'); return; }
      TCTP.copyText(text);
      TCTP.toast('Copied to clipboard!', '✅');
    });
  }

  /* ── Clear ─────────────────────────────────────────────── */

  var clearBtn = document.getElementById('tc-stt-clear');
  if (clearBtn) {
    clearBtn.addEventListener('click', function () {
      finalTranscript = '';
      if (outputEl) outputEl.value = '';
      stopListening();
      updateStats();
      var chip = document.getElementById('tc-status-chip');
      if (chip) chip.textContent = 'Ready';
    });
  }

  /* ── Download ──────────────────────────────────────────── */

  var dlBtn = document.getElementById('tc-stt-download');
  if (dlBtn) {
    dlBtn.addEventListener('click', function () {
      var text = outputEl ? outputEl.value : '';
      if (!text) { TCTP.toast('No text to download.', '⚠️'); return; }
      var blob = new Blob([text], { type: 'text/plain' });
      var a = document.createElement('a');
      a.href = URL.createObjectURL(blob);
      a.download = 'transcript.txt';
      a.click();
      URL.revokeObjectURL(a.href);
      TCTP.toast('Downloaded!', '✅');
    });
  }

})();
