/**
 * JPG to SVG Converter — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function(){
  'use strict';
  var drop = document.getElementById('tc-j2svg-drop');
  if(!drop) return;

  var convertBtn   = document.getElementById('tc-j2svg-convert');
  var detailSel    = document.getElementById('tc-j2svg-detail');
  var colorModeSel = document.getElementById('tc-j2svg-color');
  var pathsRange   = document.getElementById('tc-j2svg-paths');
  var pathsVal     = document.getElementById('tc-j2svg-paths-val');
  var downloadBtn  = document.getElementById('tc-j2svg-download');

  var file = null;
  var resultSVG = null;
  var loaded = false;

  function loadLib(cb){
    if(loaded){ cb(); return; }
    var s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/imagetracerjs@1.2.6/imagetracer_v1.2.6.js';
    s.onload = function(){ loaded = true; cb(); };
    s.onerror = function(){ TCTP.toast('Failed to load imagetracerjs', '\u274C'); };
    document.head.appendChild(s);
  }

  function setStat(id, val){
    var el = document.getElementById(id);
    if(el) el.textContent = val;
  }

  var DETAIL_PRESETS = {
    'high':   { scale: 1,   colorsampling: 2, numberofcolors: 16, mincolorratio: 0, colorquantcycles: 3, pathomit: 0 },
    'medium': { scale: 1,   colorsampling: 2, numberofcolors: 8,  mincolorratio: 0, colorquantcycles: 3, pathomit: 4 },
    'low':    { scale: 1,   colorsampling: 0, numberofcolors: 4,  mincolorratio: 0, colorquantcycles: 2, pathomit: 8 }
  };

  TCTP.initDropZone('tc-j2svg-drop', 'tc-j2svg-drop-input', function(f){
    if(!f.type.match(/image\/jpe?g/) && !/\.jpe?g$/i.test(f.name)){
      TCTP.toast('Please select a JPG/JPEG file.', '\u26A0\uFE0F');
      return;
    }
    file = f;
    resultSVG = null;
    TCTP.showFileRow('tc-j2svg-file', f);
    if(downloadBtn) downloadBtn.style.display = 'none';
    setStat('tc-j2svg-stat-comp', '-');
    setStat('tc-j2svg-stat-saved', '-');
  }, 'image/jpeg,.jpg,.jpeg');

  var removeBtn = document.querySelector('#tc-j2svg-file .tc-x');
  if(removeBtn){
    removeBtn.addEventListener('click', function(){
      file = null;
      resultSVG = null;
      TCTP.hideFileRow('tc-j2svg-file');
      if(downloadBtn) downloadBtn.style.display = 'none';
    });
  }

  if(pathsRange && pathsVal){
    pathsVal.textContent = pathsRange.value + ' paths';
    pathsRange.addEventListener('input', function(){
      pathsVal.textContent = pathsRange.value + ' paths';
    });
  }

  if(convertBtn){
    convertBtn.addEventListener('click', function(){
      if(!file){ TCTP.toast('Please select a JPG file first.', '\u26A0\uFE0F'); return; }
      loadLib(doConvert);
    });
  }

  if(downloadBtn){
    downloadBtn.addEventListener('click', function(){
      if(!resultSVG){ TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
      var blob = new Blob([resultSVG], { type: 'image/svg+xml' });
      var name = (file ? file.name.replace(/\.jpe?g$/i, '') : 'image') + '.svg';
      TCTP.downloadBlob(blob, name);
    });
  }

  function doConvert(){
    var detailKey = detailSel ? detailSel.value : 'medium';
    var colorMode = colorModeSel ? colorModeSel.value : 'color';
    var opts = DETAIL_PRESETS[detailKey] || DETAIL_PRESETS['medium'];

    if(colorMode === 'bw'){
      opts.numberofcolors = 2;
    } else if(colorMode === 'grayscale'){
      opts.numberofcolors = 4;
    }

    TCTP.showProgress('tc-j2svg-progress');
    TCTP.setProgress('tc-j2svg-progress', 30, 'Tracing...');

    var img = new Image();
    img.onload = function(){
      var c = document.createElement('canvas');
      c.width = img.naturalWidth;
      c.height = img.naturalHeight;
      var ctx = c.getContext('2d');
      ctx.drawImage(img, 0, 0);

      TCTP.setProgress('tc-j2svg-progress', 50, 'Tracing...');

      try {
        var imgData = ctx.getImageData(0, 0, c.width, c.height);
        var svgStr = window.ImageTracer.imagedataToSVG(imgData, opts);

        resultSVG = svgStr;
        TCTP.setProgress('tc-j2svg-progress', 100, 'Done!');
        TCTP.hideProgress('tc-j2svg-progress');

        setStat('tc-j2svg-stat-orig', file ? TCTP.formatSize(file.size) : '-');
        setStat('tc-j2svg-stat-comp', (svgStr.length / 1024).toFixed(1) + ' KB');
        setStat('tc-j2svg-stat-saved', 'SVG');
        TCTP.updateResultPanel(TCTP.formatSize(file.size), (svgStr.length / 1024).toFixed(1) + ' KB', 'SVG', 'Done');
        if(downloadBtn) downloadBtn.style.display = '';
        TCTP.toast('Converted to SVG!');
      } catch(err){
        TCTP.hideProgress('tc-j2svg-progress');
        TCTP.toast('Conversion failed: ' + err.message, '\u274C');
      }
    };
    img.onerror = function(){
      TCTP.hideProgress('tc-j2svg-progress');
      TCTP.toast('Failed to load image', '\u274C');
    };
    img.src = URL.createObjectURL(file);
  }
})();
