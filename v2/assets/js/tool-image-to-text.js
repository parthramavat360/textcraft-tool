/**
 * Image to Text (OCR) — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function(){
  'use strict';
  var drop = document.getElementById('tc-ocr-drop');
  if(!drop) return;

  var extractBtn = document.getElementById('tc-ocr-extract');
  var output     = document.getElementById('tc-ocr-output');
  var statusEl   = document.getElementById('tc-ocr-status');
  var copyBtn    = document.getElementById('tc-ocr-copy');

  var file = null;
  var lang = 'eng';
  var loaded = false;

  document.querySelectorAll('.tc-modes[data-group="ocr-lang"] .tc-btn').forEach(function(btn){
    btn.addEventListener('click', function(){
      TCTP.activateBtn(btn);
      lang = btn.getAttribute('data-val') || 'eng';
    });
  });

  function loadLib(cb){
    if(loaded){ cb(); return; }
    var s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js';
    s.onload = function(){ loaded = true; cb(); };
    s.onerror = function(){ TCTP.toast('Failed to load tesseract.js', '\u274C'); };
    document.head.appendChild(s);
  }

  TCTP.initDropZone('tc-ocr-drop', 'tc-ocr-drop-input', function(f){
    if(!f.type.match(/image\//)){
      TCTP.toast('Please select an image file.', '\u26A0\uFE0F');
      return;
    }
    file = f;
    TCTP.showFileRow('tc-ocr-file', f);
    if(output) output.value = '';
    if(statusEl) statusEl.textContent = '';
  }, 'image/*');

  var removeBtn = document.querySelector('#tc-ocr-file .tc-x');
  if(removeBtn){
    removeBtn.addEventListener('click', function(){
      file = null;
      TCTP.hideFileRow('tc-ocr-file');
      if(output) output.value = '';
      if(statusEl) statusEl.textContent = '';
    });
  }

  if(extractBtn){
    extractBtn.addEventListener('click', function(){
      if(!file){ TCTP.toast('Please drop an image first', '\u26A0\uFE0F'); return; }
      loadLib(doOCR);
    });
  }

  function doOCR(){
    TCTP.showProgress('tc-ocr-progress');
    TCTP.setProgress('tc-ocr-progress', 5, 'Recognizing text...');
    if(statusEl) statusEl.textContent = 'Recognizing text...';

    var url = URL.createObjectURL(file);
    Tesseract.recognize(url, lang, {
      logger: function(m){
        if(m.status === 'recognizing text'){
          TCTP.setProgress('tc-ocr-progress', 10 + Math.round(m.progress * 80), 'Recognizing text...');
        }
      }
    }).then(function(result){
      TCTP.setProgress('tc-ocr-progress', 100, 'Done!');
      TCTP.hideProgress('tc-ocr-progress');
      var text = result.data.text || '';
      if(output) output.value = text;
      TCTP.updateResultPanel((file.size / 1024).toFixed(1) + ' KB', text.length.toLocaleString() + ' chars', '\u2014', 'Done');
      TCTP.switchToResultTab();
      if(statusEl){
        var words = text.trim() ? text.trim().split(/\s+/).length : 0;
        statusEl.textContent = words + ' words | ' + text.length + ' characters extracted';
      }
      TCTP.toast('Text extracted!');
      URL.revokeObjectURL(url);
    }).catch(function(err){
      TCTP.hideProgress('tc-ocr-progress');
      TCTP.toast('OCR failed: ' + err.message, '\u274C');
      if(statusEl) statusEl.textContent = '';
      URL.revokeObjectURL(url);
    });
  }

  if(copyBtn){
    copyBtn.addEventListener('click', function(){
      TCTP.copyText(output ? output.value : '', 'Extracted text');
    });
  }
})();
