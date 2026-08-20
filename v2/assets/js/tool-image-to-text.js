(function(){
  'use strict';
  var prefix = 'tc-itt-';
  var dropEl = document.getElementById(prefix+'drop');
  if(!dropEl) return;

  var recognizeBtn = document.getElementById(prefix+'recognize');
  var output       = document.getElementById(prefix+'output');
  var fileRow      = document.getElementById(prefix+'file-row');
  var progressWrap = document.getElementById(prefix+'progress');
  var statsEl      = document.getElementById(prefix+'stats');

  var file = null;
  var loaded = false;

  function loadLib(cb){
    if(loaded){ cb(); return; }
    var s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js';
    s.onload = function(){ loaded = true; cb(); };
    s.onerror = function(){ TCTP.toast('Failed to load tesseract.js','error'); };
    document.head.appendChild(s);
  }

  TCTP.initDropZone(dropEl, function(f){
    file = f;
    TCTP.showFileRow(fileRow, f.name);
    if(output) output.textContent = '';
    if(statsEl) statsEl.textContent = '';
  });

  if(recognizeBtn){
    recognizeBtn.addEventListener('click', function(){
      if(!file){ TCTP.toast('Please drop an image first','warning'); return; }
      loadLib(function(){ doOCR(); });
    });
  }

  function doOCR(){
    TCTP.showProgress(progressWrap);
    TCTP.setProgress(progressWrap, 5);

    var url = URL.createObjectURL(file);
    Tesseract.recognize(url, 'eng', {
      logger: function(m){
        if(m.status === 'recognizing text'){
          TCTP.setProgress(progressWrap, 10 + Math.round(m.progress * 80));
        }
      }
    }).then(function(result){
      TCTP.setProgress(progressWrap, 100);
      TCTP.hideProgress(progressWrap);
      var text = result.data.text || '';
      if(output){
        var ta = document.createElement('textarea');
        ta.className = 'tctp-ocr-output';
        ta.value = text;
        ta.rows = 10;
        output.innerHTML = '';
        output.appendChild(ta);
      }
      if(statsEl){
        var words = text.trim() ? text.trim().split(/\s+/).length : 0;
        statsEl.textContent = words + ' words | ' + text.length + ' characters';
      }
      TCTP.toast('OCR completed');
      URL.revokeObjectURL(url);
    }).catch(function(err){
      TCTP.hideProgress(progressWrap);
      TCTP.toast('OCR failed: ' + err.message, 'error');
      URL.revokeObjectURL(url);
    });
  }
})();
