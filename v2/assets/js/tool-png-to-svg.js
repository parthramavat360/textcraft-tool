(function(){
  'use strict';
  var prefix = 'tc-p2s-';
  var dropEl = document.getElementById(prefix+'drop');
  if(!dropEl) return;

  var convertBtn   = document.getElementById(prefix+'convert');
  var downloadBtn  = document.getElementById(prefix+'download');
  var preview      = document.getElementById(prefix+'preview');
  var fileRow      = document.getElementById(prefix+'file-row');
  var progressWrap = document.getElementById(prefix+'progress');
  var statsEl      = document.getElementById(prefix+'stats');

  var file = null;
  var resultSVG = null;
  var potraceLoaded = false;

  function loadLib(cb){
    if(potraceLoaded){ cb(); return; }
    var s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/potrace@2.1.8/build/potrace.min.js';
    s.onload = function(){ potraceLoaded = true; cb(); };
    s.onerror = function(){ TCTP.toast('Failed to load Potrace','error'); };
    document.head.appendChild(s);
  }

  TCTP.initDropZone(dropEl, function(f){
    file = f;
    resultSVG = null;
    TCTP.showFileRow(fileRow, f.name);
    if(preview) preview.innerHTML = '';
    if(statsEl) statsEl.textContent = '';
    if(downloadBtn) downloadBtn.style.display = 'none';
  });

  if(convertBtn){
    convertBtn.addEventListener('click', function(){
      if(!file){ TCTP.toast('Please drop a PNG image first','warning'); return; }
      loadLib(function(){ doConvert(); });
    });
  }

  if(downloadBtn){
    downloadBtn.addEventListener('click', function(){
      if(resultSVG){
        var blob = new Blob([resultSVG], { type: 'image/svg+xml' });
        TCTP.downloadBlob(blob, 'converted.svg');
      }
    });
  }

  function doConvert(){
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
        var potrace = new Potrace();
        potrace.loadImageFromCanvas(c);
        potrace.setParameters({
          threshold: 128,
          turdsize: 2,
          turnpolicy: 'turnpolicy_minority',
          opttolerance: 0.2
        });

        TCTP.setProgress(progressWrap, 80);
        resultSVG = potrace.getSVG();
        TCTP.setProgress(progressWrap, 100);
        TCTP.hideProgress(progressWrap);

        if(preview){
          preview.innerHTML = '';
          preview.innerHTML = resultSVG;
        }
        if(downloadBtn) downloadBtn.style.display = '';
        if(statsEl) statsEl.textContent = (resultSVG.length / 1024).toFixed(1) + ' KB | SVG';
        TCTP.toast('PNG traced to SVG');
      } catch(err){
        TCTP.hideProgress(progressWrap);
        TCTP.toast('Tracing failed: ' + err.message, 'error');
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
