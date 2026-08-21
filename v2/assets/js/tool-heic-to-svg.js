(function(){
  'use strict';
  var prefix = 'tc-h2s-';
  var dropEl = document.getElementById(prefix+'drop');
  if(!dropEl) return;

  var convertBtn = document.getElementById(prefix+'convert');
  var downloadBtn = document.getElementById(prefix+'download');
  var preview     = document.getElementById(prefix+'preview');
  var fileRow     = document.getElementById(prefix+'file-row');
  var progressWrap = document.getElementById(prefix+'progress');
  var statsEl     = document.getElementById(prefix+'stats');

  var file = null;
  var resultSVG = null;
  var heicLoaded = false;
  var potraceLoaded = false;

  function loadHeic(cb){
    if(heicLoaded){ cb(); return; }
    var s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/heic2any@0.0.4/dist/heic2any.min.js';
    s.onload = function(){ heicLoaded = true; cb(); };
    s.onerror = function(){ TCTP.toast('Failed to load heic2any','error'); };
    document.head.appendChild(s);
  }

  function loadPotrace(cb){
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
      if(!file){ TCTP.toast('Please drop a HEIC image first','warning'); return; }
      loadHeic(function(){
        loadPotrace(function(){ doConvert(); });
      });
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

    heic2any({ blob: file, toType: 'image/png' }).then(function(pngBlob){
      TCTP.setProgress(progressWrap, 40);
      var img = new Image();
      img.onload = function(){
        TCTP.setProgress(progressWrap, 50);
        var c = document.createElement('canvas');
        c.width = img.naturalWidth;
        c.height = img.naturalHeight;
        var ctx = c.getContext('2d');
        ctx.drawImage(img, 0, 0);

        var imgData = ctx.getImageData(0, 0, c.width, c.height);
        TCTP.setProgress(progressWrap, 60);

        var params = Potrace.Params;
        params.threshold = 128;
        params.turbo = true;

        var potrace = new Potrace();
        potrace.loadImageFromCanvas(c);
        potrace.setParameters(params);

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
        TCTP.updateResultPanel(TCTP.formatSize(file.size), (resultSVG.length / 1024).toFixed(1) + ' KB', 'SVG', 'Done');
        TCTP.toast('HEIC converted to SVG');
        URL.revokeObjectURL(img.src);
      };
      img.src = URL.createObjectURL(pngBlob);
    }).catch(function(err){
      TCTP.hideProgress(progressWrap);
      TCTP.toast('Conversion failed: ' + err.message, 'error');
    });
  }
})();
