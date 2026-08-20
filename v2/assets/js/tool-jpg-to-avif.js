(function(){
  'use strict';
  var prefix = 'tc-j2a-';
  var dropEl = document.getElementById(prefix+'drop');
  if(!dropEl) return;

  var convertBtn   = document.getElementById(prefix+'convert');
  var qualityRange = document.getElementById(prefix+'quality');
  var qualityVal   = document.getElementById(prefix+'quality-val');
  var downloadBtn  = document.getElementById(prefix+'download');
  var preview      = document.getElementById(prefix+'preview');
  var fileRow      = document.getElementById(prefix+'file-row');
  var progressWrap = document.getElementById(prefix+'progress');
  var statsEl      = document.getElementById(prefix+'stats');

  var file = null;
  var resultBlob = null;
  var loaded = false;

  function loadLib(cb){
    if(loaded){ cb(); return; }
    var s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/avif-wasm@0.9.0/dist/avif_wasm_sync.js';
    s.onload = function(){ loaded = true; cb(); };
    s.onerror = function(){ TCTP.toast('Failed to load AVIF encoder','error'); };
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

  if(qualityRange && qualityVal){
    qualityRange.addEventListener('input', function(){
      qualityVal.textContent = qualityRange.value + '%';
    });
  }

  if(convertBtn){
    convertBtn.addEventListener('click', function(){
      if(!file){ TCTP.toast('Please drop an image first','warning'); return; }
      loadLib(function(){ doConvert(); });
    });
  }

  if(downloadBtn){
    downloadBtn.addEventListener('click', function(){
      if(resultBlob) TCTP.downloadBlob(resultBlob, 'converted.avif');
    });
  }

  function doConvert(){
    var q = qualityRange ? parseInt(qualityRange.value,10) : 80;
    TCTP.showProgress(progressWrap);
    TCTP.setProgress(progressWrap, 10);

    var img = new Image();
    img.onload = function(){
      TCTP.setProgress(progressWrap, 30);
      var c = document.createElement('canvas');
      c.width = img.naturalWidth;
      c.height = img.naturalHeight;
      var ctx = c.getContext('2d');
      ctx.drawImage(img, 0, 0);

      var imageData = ctx.getImageData(0, 0, c.width, c.height);
      TCTP.setProgress(progressWrap, 50);

      try {
        var avifEncoder = AvifEncoder;
        var encoder = new avifEncoder();
        encoder.setSpeed(6);
        encoder.setQuality(q);
        var encoded = encoder.encode(imageData.data, c.width, c.height);
        resultBlob = new Blob([encoded], { type: 'image/avif' });

        TCTP.setProgress(progressWrap, 100);
        TCTP.hideProgress(progressWrap);

        if(preview){
          preview.innerHTML = '';
          var prevImg = document.createElement('img');
          prevImg.src = URL.createObjectURL(resultBlob);
          preview.appendChild(prevImg);
        }
        if(downloadBtn) downloadBtn.style.display = '';
        if(statsEl) statsEl.textContent = TCTP.formatSize(resultBlob.size) + ' | AVIF';
        TCTP.toast('Image converted to AVIF');
      } catch(err){
        TCTP.hideProgress(progressWrap);
        TCTP.toast('AVIF encoding failed: ' + err.message, 'error');
      }
      URL.revokeObjectURL(img.src);
    };
    img.onerror = function(){
      TCTP.hideProgress(progressWrap);
      TCTP.toast('Failed to load image','error');
    };
    img.src = URL.createObjectURL(file);
  }
})();
