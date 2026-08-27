(function () {
    'use strict';
    var $ = function (s, p) { return (p || document).querySelector(s); };
    var output = $('#stt-output');
    if (!output) return;

    var startBtn = $('#stt-start');
    var langSelect = $('#stt-lang');
    var continuousCheck = $('#stt-continuous');
    var interimCheck = $('#stt-interim');
    var statusEl = $('#stt-status');

    var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    var recognition = null;
    var isListening = false;
    var finalTranscript = '';

    if (!SpeechRecognition) {
        startBtn.disabled = true;
        startBtn.innerHTML = '<i class="fa-solid fa-microphone-slash"></i> Not Supported';
        statusEl.innerHTML = '<span style="color:#ef4444">Speech recognition is not supported in your browser. Please use Chrome, Edge, or Safari.</span>';
        return;
    }

    recognition = new SpeechRecognition();
    recognition.continuous = true;
    recognition.interimResults = true;

    startBtn.addEventListener('click', function () {
        if (isListening) {
            recognition.stop();
            return;
        }

        finalTranscript = output.value;
        recognition.lang = langSelect.value;
        recognition.continuous = continuousCheck.checked;
        recognition.interimResults = interimCheck.checked;

        try {
            recognition.start();
        } catch (e) {
            statusEl.innerHTML = '<span style="color:#ef4444">Error starting recognition: ' + e.message + '</span>';
        }
    });

    recognition.onstart = function () {
        isListening = true;
        startBtn.innerHTML = '<i class="fa-solid fa-microphone-slash"></i> Stop Listening';
        startBtn.classList.add('tc-btn--danger');
        startBtn.classList.remove('tc-btn--primary');
        statusEl.innerHTML = '<span style="color:#22c55e"><i class="fa-solid fa-circle" style="animation:pulse 1s infinite;margin-right:6px"></i>Listening... Speak now</span>';
    };

    recognition.onend = function () {
        isListening = false;
        startBtn.innerHTML = '<i class="fa-solid fa-microphone"></i> Start Listening';
        startBtn.classList.remove('tc-btn--danger');
        startBtn.classList.add('tc-btn--primary');
        statusEl.innerHTML = '<span style="color:#94a3b8">Stopped. Click start to resume.</span>';
    };

    recognition.onerror = function (e) {
        if (e.error === 'no-speech') {
            statusEl.innerHTML = '<span style="color:#eab308">No speech detected. Try again.</span>';
        } else if (e.error === 'not-allowed') {
            statusEl.innerHTML = '<span style="color:#ef4444">Microphone access denied. Please allow microphone access.</span>';
        } else {
            statusEl.innerHTML = '<span style="color:#ef4444">Error: ' + e.error + '</span>';
        }
    };

    recognition.onresult = function (event) {
        var interim = '';
        for (var i = event.resultIndex; i < event.results.length; i++) {
            if (event.results[i].isFinal) {
                finalTranscript += event.results[i][0].transcript + ' ';
            } else {
                interim += event.results[i][0].transcript;
            }
        }
        output.value = finalTranscript + interim;
        output.scrollTop = output.scrollHeight;
    };

    $('#stt-copy').addEventListener('click', function () {
        TCTP.copyText(output.value);
    });

    $('#stt-clear').addEventListener('click', function () {
        output.value = '';
        finalTranscript = '';
    });

    $('#stt-download').addEventListener('click', function () {
        var text = output.value;
        if (!text) { TCTP.toast('No text to download', '\u26a0\ufe0f'); return; }
        var blob = new Blob([text], { type: 'text/plain' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'speech-to-text-' + Date.now() + '.txt';
        a.click();
        URL.revokeObjectURL(url);
        TCTP.toast('Downloaded!', '\u2705');
    });
})();
