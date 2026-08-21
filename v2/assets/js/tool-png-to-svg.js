/**
 * PNG to SVG Converter — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function(){
  'use strict';
  var drop = document.getElementById('tc-p2svg-drop');
  if(!drop) return;

  var convertBtn  = document.getElementById('tc-p2svg-convert');
  var downloadBtn = document.getElementById('tc-p2svg-download');
  var traceChk    = document.getElementById('tc-p2svg-trace');
  var transChk    = document.getElementById('tc-p2svg-transparency');

  var file = null;
  var resultSVG = null;
  var potraceLoaded = false;
  var tracerLoaded = false;

  function loadScript(src, cb, errMsg){
    var s = document.createElement('script');
    s.src = src;
    s.onload = cb;
    s.onerror = function(){ TCTP.toast(errMsg, '\u274C'); };
    document.head.appendChild(s);
  }

  function loadLib(cb){
    var colorTrace = traceChk ? traceChk.checked : false;
    var steps = [];
    if(!potraceLoaded) steps.push(['https://cdn.jsdelivr.net/npm/potrace@2.1.8/build/potrace.min.js', function(){ potraceLoaded = true; }, 'Failed to load Potrace']);
    if(colorTrace && !tracerLoaded) steps.push(['https://cdn.jsdelivr.net/npm/imagetracerjs@1.2.6/imagetracer_v1.2.6.js', function(){ tracerLoaded = true; }, 'Failed to load imagetracerjs']);
    if(!steps.length){ cb(); return; }
    (function next(){
      if(!steps.length){ cb(); return; }
      var step = steps.shift();
      loadScript(step[0], function(){ step[1](); next(); }, step[2]);
    })();
  }

  function setStat(id, val){
    var el = document.getElementById(id);
    if(el) el.textContent = val;
  }

  TCTP.initDropZone('tc-p2svg-drop', 'tc-p2svg-drop-input', function(f){
    if(!f.type.match(/image\/png/) && !/\.png$/i.test(f.name)){
      TCTP.toast('Please select a PNG file.', '\u26A0\uFE0F');
      return;
    }
    file = f;
    resultSVG = null;
    TCTP.showFileRow('tc-p2svg-file', f);
    if(downloadBtn) downloadBtn.style.display = 'none';
    setStat('tc-p2svg-stat-comp', '-');
    setStat('tc-p2svg-stat-saved', '-');
  }, 'image/png,.png');

  var removeBtn = document.querySelector('#tc-p2svg-file .tc-x');
  if(removeBtn){
    removeBtn.addEventListener('click', function(){
      file = null;
      resultSVG = null;
      TCTP.hideFileRow('tc-p2svg-file');
      if(downloadBtn) downloadBtn.style.display = 'none';
    });
  }

  if(convertBtn){
    convertBtn.addEventListener('click', function(){
      if(!file){ TCTP.toast('Please select a PNG file first.', '\u26A0\uFE0F'); return; }
      loadLib(doConvert);
    });
  }

  if(downloadBtn){
    downloadBtn.addEventListener('click', function(){
      if(!resultSVG){ TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
      var blob = new Blob([resultSVG], { type: 'image/svg+xml' });
      var name = (file ? file.name.replace(/\.png$/i, '') : 'image') + '.svg';
      TCTP.downloadBlob(blob, name);
    });
  }

  function doConvert(){
    var colorTrace = traceChk ? traceChk.checked : false;
    var keepTransparency = transChk ? transChk.checked : true;

    TCTP.showProgress('tc-p2svg-progress');
    TCTP.setProgress('tc-p2svg-progress', 30, 'Tracing...');

    var img = new Image();
    img.onload = function(){
      var c = document.createElement('canvas');
      c.width = img.naturalWidth;
      c.height = img.naturalHeight;
      var ctx = c.getContext('2d');
      if(!keepTransparency){
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, c.width, c.height);
      }
      ctx.drawImage(img, 0, 0);

      TCTP.setProgress('tc-p2svg-progress', 50, 'Tracing...');

      try {
        var svgStr;
        if(colorTrace && window.Potrace && typeof window.ImageTracer !== 'undefined'){
          var imgData = ctx.getImageData(0, 0, c.width, c.height);
          svgStr = window.ImageTracer.imagedataToSVG(imgData, { numberofcolors: 8, pathomit: 4 });
        } else {
          var potrace = new window.Potrace();
          potrace.loadImageFromCanvas(c);
          potrace.setParameters({
            threshold: 128,
            turdsize: 2,
            turnpolicy: 'turnpolicy_minority',
            opttolerance: 0.2
          });
          svgStr = potrace.getSVG();
        }

        resultSVG = svgStr;
        TCTP.setProgress('tc-p2svg-progress', 100, 'Done!');
        TCTP.hideProgress('tc-p2svg-progress');

        setStat('tc-p2svg-stat-orig', file ? TCTP.formatSize(file.size) : '-');
        setStat('tc-p2svg-stat-comp', (resultSVG.length / 1024).toFixed(1) + ' KB');
        setStat('tc-p2svg-stat-saved', 'SVG');
        TCTP.updateResultPanel(TCTP.formatSize(file.size), (resultSVG.length / 1024).toFixed(1) + ' KB', 'SVG', 'Done');
        if(downloadBtn) downloadBtn.style.display = '';
        TCTP.toast('Converted to SVG!');
      } catch(err){
        TCTP.hideProgress('tc-p2svg-progress');
        TCTP.toast('Tracing failed: ' + err.message, '\u274C');
      }
    };
    img.onerror = function(){
      TCTP.hideProgress('tc-p2svg-progress');
      TCTP.toast('Failed to load image', '\u274C');
    };
    img.src = URL.createObjectURL(file);
  }
})();
