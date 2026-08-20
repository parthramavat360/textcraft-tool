(function(){
  'use strict';
  var prefix = 'tc-p2heic-';
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
    s.src = 'https://cdn.jsdelivr.net/npm/libheif-js@1.17.6/libheif-js/libheif.js';
    s.onload = function(){ loaded = true; cb(); };
    s.onerror = function(){ TCTP.toast('Failed to load HEIC encoder','error'); };
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
      if(!file){ TCTP.toast('Please drop a PNG image first','warning'); return; }
      loadLib(function(){ doConvert(); });
    });
  }

  if(downloadBtn){
    downloadBtn.addEventListener('click', function(){
      if(resultBlob) TCTP.downloadBlob(resultBlob, 'converted.heic');
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

      TCTP.setProgress(progressWrap, 50);

      try {
        var encoder = new libheif.HeifEncoder();
        var imageData = ctx.getImageData(0, 0, c.width, c.height);
        var heifBuffer = encoder.encode(imageData.data, c.width, c.height, q);

        resultBlob = new Blob([heifBuffer], { type: 'image/heic' });
        TCTP.setProgress(progressWrap, 100);
        TCTP.hideProgress(progressWrap);

        if(preview){
          preview.innerHTML = '';
          var prevImg = document.createElement('img');
          prevImg.src = URL.createObjectURL(resultBlob);
          preview.appendChild(prevImg);
        }
        if(downloadBtn) downloadBtn.style.display = '';
        if(statsEl) statsEl.textContent = TCTP.formatSize(resultBlob.size) + ' | HEIC';
        TCTP.toast('PNG converted to HEIC');
      } catch(err){
        TCTP.hideProgress(progressWrap);
        TCTP.toast('HEIC encoding failed: ' + err.message, 'error');
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
