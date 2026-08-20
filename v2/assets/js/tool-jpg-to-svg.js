(function(){
  'use strict';
  var prefix = 'tc-j2s-';
  var dropEl = document.getElementById(prefix+'drop');
  if(!dropEl) return;

  var convertBtn    = document.getElementById(prefix+'convert');
  var detailSel     = document.getElementById(prefix+'detail');
  var colorModeSel  = document.getElementById(prefix+'color-mode');
  var downloadBtn   = document.getElementById(prefix+'download');
  var preview       = document.getElementById(prefix+'preview');
  var fileRow       = document.getElementById(prefix+'file-row');
  var progressWrap  = document.getElementById(prefix+'progress');
  var statsEl       = document.getElementById(prefix+'stats');

  var file = null;
  var resultSVG = null;
  var loaded = false;

  function loadLib(cb){
    if(loaded){ cb(); return; }
    var s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/imagetracerjs@1.2.6/imagetracer_v1.2.6.js';
    s.onload = function(){ loaded = true; cb(); };
    s.onerror = function(){ TCTP.toast('Failed to load imagetracerjs','error'); };
    document.head.appendChild(s);
  }

  var DETAIL_PRESETS = {
    'high':   { scale: 1,    colorsampling: 2, numberofcolors: 16, mincolorratio: 0, colorquantcycles: 3, pathomit: 0 },
    'medium': { scale: 1,    colorsampling: 2, numberofcolors: 8,  mincolorratio: 0, colorquantcycles: 3, pathomit: 4 },
    'low':    { scale: 1,    colorsampling: 0, numberofcolors: 4,  mincolorratio: 0, colorquantcycles: 2, pathomit: 8 },
    'minimal':{ scale: 0.5,  colorsampling: 0, numberofcolors: 2,  mincolorratio: 0, colorquantcycles: 1, pathomit: 16 }
  };

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
      if(!file){ TCTP.toast('Please drop a JPG image first','warning'); return; }
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
    var detailKey  = detailSel ? detailSel.value : 'medium';
    var colorMode  = colorModeSel ? colorModeSel.value : 'color';
    var opts = DETAIL_PRESETS[detailKey] || DETAIL_PRESETS['medium'];

    if(colorMode === 'monochrome'){
      opts.numberofcolors = 2;
    } else if(colorMode === 'grayscale'){
      opts.numberofcolors = 4;
    }

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

      var imgData = ctx.getImageData(0, 0, c.width, c.height);
      var svgStr = ImageTracer.imagedataToSVG(imgData, opts);

      resultSVG = svgStr;
      TCTP.setProgress(progressWrap, 100);
      TCTP.hideProgress(progressWrap);

      if(preview){
        preview.innerHTML = '';
        preview.innerHTML = svgStr;
      }
      if(downloadBtn) downloadBtn.style.display = '';
      if(statsEl) statsEl.textContent = (svgStr.length / 1024).toFixed(1) + ' KB | SVG';
      TCTP.toast('JPG traced to SVG');
      URL.revokeObjectURL(img.src);
    };
    img.onerror = function(){
      TCTP.hideProgress(progressWrap);
      TCTP.toast('Failed to load image','error');
    };
    img.src = URL.createObjectURL(file);
  }
})();
