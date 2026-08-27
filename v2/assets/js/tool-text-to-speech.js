(function () {
    'use strict';
    var $ = function (s, p) { return (p || document).querySelector(s); };
    var ttsText = $('#tts-text');
    if (!ttsText) return;

    var voiceSelect = $('#tts-voice');
    var langSelect = $('#tts-lang');
    var speedRange = $('#tts-speed');
    var pitchRange = $('#tts-pitch');
    var volRange = $('#tts-vol');
    var playBtn = $('#tts-play');
    var pauseBtn = $('#tts-pause');
    var stopBtn = $('#tts-stop');
    var resultEl = $('#tts-result');
    var statusEl = $('#tts-status');
    var speedVal = $('#tts-speed-val');
    var pitchVal = $('#tts-pitch-val');
    var volVal = $('#tts-vol-val');

    var synth = window.speechSynthesis;
    var utterance = null;
    var voices = [];

    function loadVoices() {
        voices = synth.getVoices();
        voiceSelect.innerHTML = '';
        if (voices.length === 0) {
            voiceSelect.innerHTML = '<option value="">No voices available</option>';
            return;
        }
        var lang = langSelect.value || 'en-US';
        var filtered = voices.filter(function (v) { return v.lang.indexOf(lang.split('-')[0]) === 0; });
        if (filtered.length === 0) filtered = voices;

        filtered.forEach(function (v, i) {
            var opt = document.createElement('option');
            opt.value = voices.indexOf(v);
            opt.textContent = v.name + ' (' + v.lang + ')';
            if (v.default) opt.selected = true;
            voiceSelect.appendChild(opt);
        });
    }

    if (synth.onvoiceschanged !== undefined) {
        synth.onvoiceschanged = loadVoices;
    }
    loadVoices();

    langSelect.addEventListener('change', loadVoices);

    speedRange.addEventListener('input', function () { speedVal.textContent = this.value; });
    pitchRange.addEventListener('input', function () { pitchVal.textContent = this.value; });
    volRange.addEventListener('input', function () { volVal.textContent = this.value; });

    function showStatus(msg, icon) {
        resultEl.style.display = '';
        statusEl.innerHTML = '<div style="display:flex;align-items:center;gap:10px;padding:14px;background:#0f172a;border-radius:12px;border:1px solid rgba(148,163,184,0.12);color:#e2e8f0;font-size:14px"><i class="fa-solid ' + icon + '" style="color:#2563eb;font-size:18px"></i>' + msg + '</div>';
    }

    function hideStatus() {
        resultEl.style.display = 'none';
    }

    playBtn.addEventListener('click', function () {
        var text = ttsText.value.trim();
        if (!text) { TCTP.toast('Enter some text first', '\u26a0\ufe0f'); return; }

        if (synth.speaking && synth.paused) {
            synth.resume();
            showStatus('Speaking...', 'fa-volume-high');
            playBtn.innerHTML = '<i class="fa-solid fa-pause"></i> Pause';
            return;
        }

        synth.cancel();
        utterance = new SpeechSynthesisUtterance(text);
        utterance.rate = parseFloat(speedRange.value);
        utterance.pitch = parseFloat(pitchRange.value);
        utterance.volume = parseInt(volRange.value) / 100;

        var voiceIdx = parseInt(voiceSelect.value);
        if (!isNaN(voiceIdx) && voices[voiceIdx]) {
            utterance.voice = voices[voiceIdx];
            utterance.lang = voices[voiceIdx].lang;
        } else {
            utterance.lang = langSelect.value;
        }

        utterance.onstart = function () {
            showStatus('Speaking... (' + Math.ceil(text.split(/\s+/).length / (150 * utterance.rate)) + ' min estimated)', 'fa-volume-high');
            playBtn.innerHTML = '<i class="fa-solid fa-pause"></i> Pause';
            pauseBtn.disabled = false;
            stopBtn.disabled = false;
        };

        utterance.onend = function () {
            showStatus('Speech complete!', 'fa-check-circle');
            playBtn.innerHTML = '<i class="fa-solid fa-play"></i> Speak';
            pauseBtn.disabled = true;
            stopBtn.disabled = true;
            setTimeout(hideStatus, 5000);
        };

        utterance.onerror = function (e) {
            if (e.error !== 'canceled') {
                showStatus('Error: ' + e.error, 'fa-exclamation-circle');
            }
        };

        synth.speak(utterance);
    });

    pauseBtn.addEventListener('click', function () {
        if (synth.speaking) {
            if (synth.paused) {
                synth.resume();
                playBtn.innerHTML = '<i class="fa-solid fa-pause"></i> Pause';
                showStatus('Speaking...', 'fa-volume-high');
            } else {
                synth.pause();
                playBtn.innerHTML = '<i class="fa-solid fa-play"></i> Resume';
                showStatus('Paused', 'fa-pause-circle');
            }
        }
    });

    stopBtn.addEventListener('click', function () {
        synth.cancel();
        playBtn.innerHTML = '<i class="fa-solid fa-play"></i> Speak';
        pauseBtn.disabled = true;
        stopBtn.disabled = true;
        hideStatus();
    });

    $('#tts-copy-text').addEventListener('click', function () {
        TCTP.copyText(ttsText.value);
    });
})();
