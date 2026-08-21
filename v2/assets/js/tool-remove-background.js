/**
 * Remove Background — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function(){
  'use strict';
  var drop = document.getElementById('tc-rmbg-drop');
  if(!drop) return;

  var removeBtn   = document.getElementById('tc-rmbg-remove');
  var previewWrap = document.getElementById('tc-rmbg-preview');
  var previewImg  = document.getElementById('tc-rmbg-img');
  var placeholder = document.getElementById('tc-rmbg-placeholder');
  var downloadBtn = document.getElementById('tc-rmbg-download');
  var statusEl    = document.getElementById('tc-rmbg-status');
  var hqChk       = document.getElementById('tc-rmbg-highquality');

  var file = null;
  var resultBlob = null;
  var loaded = false;

  function loadLib(cb){
    if(loaded){ cb(); return; }
    if(statusEl) statusEl.textContent = 'Loading AI model (first run may take a while)...';
    var s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/@imgly/background-removal@1.5.16/dist/index.umd.js';
    s.onload = function(){ loaded = true; cb(); };
    s.onerror = function(){
      TCTP.toast('Failed to load background-removal library', '\u274C');
      if(statusEl) statusEl.textContent = '';
    };
    document.head.appendChild(s);
  }

  function getRemoveFn(){
    if(typeof window.removeBackground === 'function') return window.removeBackground;
    if(window.bgRemoval && typeof window.bgRemoval.removeBackground === 'function') return window.bgRemoval.removeBackground;
    return null;
  }

  TCTP.initDropZone('tc-rmbg-drop', 'tc-rmbg-drop-input', function(f){
    if(!f.type.match(/image\//)){
      TCTP.toast('Please select an image file.', '\u26A0\uFE0F');
      return;
    }
    file = f;
    resultBlob = null;
    TCTP.showFileRow('tc-rmbg-file', f);
    if(previewWrap) previewWrap.style.display = 'none';
    if(placeholder){ placeholder.style.display = ''; placeholder.textContent = 'Result will appear here...'; }
    if(downloadBtn) downloadBtn.style.display = 'none';
    if(statusEl) statusEl.textContent = '';
  }, 'image/*');

  var removeFileBtn = document.querySelector('#tc-rmbg-file .tc-x');
  if(removeFileBtn){
    removeFileBtn.addEventListener('click', function(){
      file = null;
      resultBlob = null;
      TCTP.hideFileRow('tc-rmbg-file');
      if(previewWrap) previewWrap.style.display = 'none';
      if(placeholder){ placeholder.style.display = ''; placeholder.textContent = 'Result will appear here...'; }
      if(downloadBtn) downloadBtn.style.display = 'none';
    });
  }

  if(removeBtn){
    removeBtn.addEventListener('click', function(){
      if(!file){ TCTP.toast('Please select an image first.', '\u26A0\uFE0F'); return; }
      loadLib(doRemove);
    });
  }

  if(downloadBtn){
    downloadBtn.addEventListener('click', function(){
      if(!resultBlob){ TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
      var name = (file ? file.name.replace(/\.[^.]+$/, '') : 'image') + '-no-bg.png';
      TCTP.downloadBlob(resultBlob, name);
    });
  }

  function doRemove(){
    var fn = getRemoveFn();
    if(!fn){ TCTP.toast('Background-removal library unavailable.', '\u274C'); return; }

    TCTP.showProgress('tc-rmbg-progress');
    TCTP.setProgress('tc-rmbg-progress', 5, 'Removing background...');
    if(placeholder){ placeholder.style.display = ''; placeholder.textContent = 'Processing...'; }

    var options = {
      progress: function(key, current, total){
        var pct = Math.min(95, Math.round((current / total) * 90) + 5);
        TCTP.setProgress('tc-rmbg-progress', pct, 'Removing background...');
      }
    };
    if(hqChk && hqChk.checked) options.model = 'isnet';

    fn(file, options).then(function(blob){
      resultBlob = blob;
      TCTP.setProgress('tc-rmbg-progress', 100, 'Done!');
      TCTP.hideProgress('tc-rmbg-progress');

      if(previewImg) previewImg.src = URL.createObjectURL(blob);
      if(previewWrap) previewWrap.style.display = '';
      if(placeholder) placeholder.style.display = 'none';
      if(downloadBtn) downloadBtn.style.display = '';
      if(statusEl) statusEl.textContent = 'Result: ' + TCTP.formatSize(blob.size);
      TCTP.updateResultPanel(TCTP.formatSize(file.size), TCTP.formatSize(blob.size), (file.size > blob.size ? ((1 - blob.size / file.size) * 100).toFixed(1) + '%' : '0%'), 'Done');
      TCTP.toast('Background removed!');
    }).catch(function(err){
      TCTP.hideProgress('tc-rmbg-progress');
      TCTP.toast('Failed: ' + err.message, '\u274C');
      if(placeholder){ placeholder.textContent = 'Result will appear here...'; }
      if(statusEl) statusEl.textContent = '';
    });
  }
})();
