/**
 * Text to Speech — Web Speech API
 * @package TextCraft_Tools_Pro
 */
(function () {
  if (!window.TCTP) return;

  const input    = document.getElementById('tc-tts-input');
  const voiceSel = document.getElementById('tc-tts-voice');
  const rateSlider  = document.getElementById('tc-tts-rate');
  const pitchSlider = document.getElementById('tc-tts-pitch');
  const volSlider   = document.getElementById('tc-tts-vol');
  const playBtn  = document.getElementById('tc-tts-play');
  const pauseBtn = document.getElementById('tc-tts-pause');
  const stopBtn  = document.getElementById('tc-tts-stop');
  const status   = document.getElementById('tc-tts-status');
  if (!input || !playBtn) return;

  const synth = window.speechSynthesis;
  let utterance = null;
  let voices = [];
  let isPaused = false;

  /* ── Load voices ──────────────────────────────────────────── */

  function loadVoices() {
    voices = synth.getVoices();
    if (!voices.length) return;
    voiceSel.innerHTML = '';

    /* Group by language */
    const grouped = {};
    voices.forEach(function(v, i) {
      const lang = v.lang.split('-')[0];
      if (!grouped[lang]) grouped[lang] = [];
      grouped[lang].push({ voice: v, index: i });
    });

    /* Sort languages, prefer English first */
    const langs = Object.keys(grouped).sort(function(a, b) {
      if (a === 'en') return -1;
      if (b === 'en') return 1;
      return a.localeCompare(b);
    });

    langs.forEach(function(lang) {
      const optgroup = document.createElement('optgroup');
      optgroup.label = lang.toUpperCase();
      grouped[lang].forEach(function(item) {
        const opt = document.createElement('option');
        opt.value = item.index;
        opt.textContent = item.voice.name + ' (' + item.voice.lang + ')';
        if (item.voice.default) opt.selected = true;
        optgroup.appendChild(opt);
      });
      voiceSel.appendChild(optgroup);
    });

    /* Select first English voice if available */
    var enVoice = voices.findIndex(function(v) { return v.lang.startsWith('en'); });
    if (enVoice >= 0) voiceSel.value = enVoice;
  }

  loadVoices();
  if (synth.onvoiceschanged !== undefined) {
    synth.onvoiceschanged = loadVoices;
  }

  /* ── Slider labels ────────────────────────────────────────── */

  if (rateSlider) {
    rateSlider.addEventListener('input', function() {
      document.getElementById('tc-tts-rate-val').textContent = parseFloat(this.value).toFixed(1) + '×';
    });
  }
  if (pitchSlider) {
    pitchSlider.addEventListener('input', function() {
      document.getElementById('tc-tts-pitch-val').textContent = parseFloat(this.value).toFixed(1);
    });
  }
  if (volSlider) {
    volSlider.addEventListener('input', function() {
      document.getElementById('tc-tts-vol-val').textContent = this.value + '%';
    });
  }

  /* ── Play ─────────────────────────────────────────────────── */

  function setStatus(msg, type) {
    if (!status) return;
    status.textContent = msg;
    status.className = 'tc-tts-status' + (type ? ' tc-tts-status--' + type : '');
  }

  playBtn.addEventListener('click', function() {
    var text = input.value.trim();
    if (!text) {
      TCTP.toast('Please enter some text to speak.', 'error');
      return;
    }

    /* If paused, resume */
    if (isPaused && utterance) {
      synth.resume();
      isPaused = false;
      setStatus('Playing...', 'playing');
      updateButtons(true);
      return;
    }

    /* Cancel any ongoing speech */
    synth.cancel();

    utterance = new SpeechSynthesisUtterance(text);

    /* Set voice */
    var voiceIdx = parseInt(voiceSel.value, 10);
    if (!isNaN(voiceIdx) && voices[voiceIdx]) {
      utterance.voice = voices[voiceIdx];
    }

    /* Set rate, pitch, volume */
    utterance.rate  = parseFloat(rateSlider ? rateSlider.value : 1);
    utterance.pitch = parseFloat(pitchSlider ? pitchSlider.value : 1);
    utterance.volume = (volSlider ? parseInt(volSlider.value, 10) : 100) / 100;

    utterance.onstart = function() {
      setStatus('Speaking...', 'playing');
      updateButtons(true);
    };

    utterance.onend = function() {
      setStatus('Finished', 'done');
      updateButtons(false);
      isPaused = false;
    };

    utterance.onerror = function(e) {
      if (e.error === 'canceled') return;
      setStatus('Error: ' + e.error, 'error');
      updateButtons(false);
    };

    /* Chrome bug: long texts need this workaround */
    var chars = text.length;
    if (chars > 200) {
      /* Split into chunks for Chrome */
      var chunks = [];
      var sentences = text.match(/[^.!?]+[.!?]+\s*/g) || [text];
      var current = '';
      sentences.forEach(function(s) {
        if ((current + s).length > 180) {
          if (current) chunks.push(current);
          current = s;
        } else {
          current += s;
        }
      });
      if (current) chunks.push(current);

      var idx = 0;
      function speakNext() {
        if (idx >= chunks.length) return;
        var u = new SpeechSynthesisUtterance(chunks[idx]);
        u.voice = utterance.voice;
        u.rate = utterance.rate;
        u.pitch = utterance.pitch;
        u.volume = utterance.volume;
        u.onend = function() {
          idx++;
          if (idx < chunks.length) {
            speakNext();
          } else {
            setStatus('Finished', 'done');
            updateButtons(false);
            isPaused = false;
          }
        };
        u.onerror = function(e) {
          if (e.error !== 'canceled') {
            setStatus('Error: ' + e.error, 'error');
            updateButtons(false);
          }
        };
        synth.speak(u);
      }
      speakNext();
    } else {
      synth.speak(utterance);
    }
  });

  /* ── Pause ────────────────────────────────────────────────── */

  pauseBtn.addEventListener('click', function() {
    if (synth.speaking && !synth.paused) {
      synth.pause();
      isPaused = true;
      setStatus('Paused', 'paused');
    } else if (isPaused) {
      synth.resume();
      isPaused = false;
      setStatus('Playing...', 'playing');
    }
  });

  /* ── Stop ─────────────────────────────────────────────────── */

  stopBtn.addEventListener('click', function() {
    synth.cancel();
    isPaused = false;
    setStatus('Stopped', '');
    updateButtons(false);
  });

  /* ── Button states ────────────────────────────────────────── */

  function updateButtons(playing) {
    playBtn.disabled = playing;
    pauseBtn.disabled = !playing;
    stopBtn.disabled = !playing;
  }

  /* ── Keyboard shortcut ────────────────────────────────────── */

  input.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && e.ctrlKey) {
      e.preventDefault();
      playBtn.click();
    }
  });

})();
