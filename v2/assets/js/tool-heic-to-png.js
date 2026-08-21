(function(){
  'use strict';
  var prefix = 'tc-h2p-';
  var dropEl = document.getElementById(prefix+'drop');
  if(!dropEl) return;

  var convertBtn = document.getElementById(prefix+'convert');
  var downloadBtn = document.getElementById(prefix+'download');
  var preview     = document.getElementById(prefix+'preview');
  var fileRow     = document.getElementById(prefix+'file-row');
  var progressWrap = document.getElementById(prefix+'progress');
  var statsEl     = document.getElementById(prefix+'stats');

  var file = null;
  var resultBlob = null;
  var loaded = false;

  function loadLib(cb){
    if(loaded){ cb(); return; }
    var s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/heic2any@0.0.4/dist/heic2any.min.js';
    s.onload = function(){ loaded = true; cb(); };
    s.onerror = function(){ TCTP.toast('Failed to load heic2any','error'); };
    document.head.appendChild(s);
  }

  TCTP.initDropZone(dropEl, function(f){
    file = f;
    resultBlob = null;
    TCTP.showFileRow(fileRow, f.name);
    if(preview) preview.innerHTML = '';
    if(statsEl) statsEl.textContent = '';
    if(downloadBtn) downloadBtn.style.display = 'none';
  });

  if(convertBtn){
    convertBtn.addEventListener('click', function(){
      if(!file){ TCTP.toast('Please drop a HEIC image first','warning'); return; }
      loadLib(function(){ doConvert(); });
    });
  }

  if(downloadBtn){
    downloadBtn.addEventListener('click', function(){
      if(resultBlob) TCTP.downloadBlob(resultBlob, 'converted.png');
    });
  }

  function doConvert(){
    TCTP.showProgress(progressWrap);
    TCTP.setProgress(progressWrap, 10);

    heic2any({
      blob: file,
      toType: 'image/png'
    }).then(function(blob){
      resultBlob = blob;
      TCTP.setProgress(progressWrap, 100);
      TCTP.hideProgress(progressWrap);

      if(preview){
        preview.innerHTML = '';
        var img = document.createElement('img');
        img.src = URL.createObjectURL(blob);
        preview.appendChild(img);
      }
      if(downloadBtn) downloadBtn.style.display = '';
      if(statsEl) statsEl.textContent = TCTP.formatSize(blob.size) + ' | PNG';
      TCTP.updateResultPanel(TCTP.formatSize(file.size), TCTP.formatSize(blob.size), (file.size > 0 ? ((1 - blob.size / file.size) * 100).toFixed(1) : '0') + '%', 'Done');
      TCTP.toast('HEIC converted to PNG');
    }).catch(function(err){
      TCTP.hideProgress(progressWrap);
      TCTP.toast('Conversion failed: ' + err.message, 'error');
    });
  }
})();
