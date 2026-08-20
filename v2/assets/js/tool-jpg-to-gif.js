(function(){
  'use strict';
  var prefix = 'tc-j2g-';
  var dropEl = document.getElementById(prefix+'drop');
  if(!dropEl) return;

  var convertBtn   = document.getElementById(prefix+'convert');
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
    var urls = [
      'https://cdn.jsdelivr.net/npm/gif.js@0.2.0/dist/gif.js'
    ];
    var i = 0;
    function next(){
      if(i >= urls.length){ loaded = true; cb(); return; }
      var s = document.createElement('script');
      s.src = urls[i]; i++;
      s.onload = next;
      s.onerror = function(){ TCTP.toast('Failed to load gif.js','error'); };
      document.head.appendChild(s);
    }
    next();
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
      if(!file){ TCTP.toast('Please drop an image first','warning'); return; }
      loadLib(function(){ doConvert(); });
    });
  }

  if(downloadBtn){
    downloadBtn.addEventListener('click', function(){
      if(resultBlob) TCTP.downloadBlob(resultBlob, 'converted.gif');
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

      var workerScript = 'https://cdn.jsdelivr.net/npm/gif.js@0.2.0/dist/gif.worker.js';

      var gif = new GIF({
        workers: 2,
        quality: 10,
        workerScript: workerScript
      });

      gif.addFrame(c, { delay: 200, copy: true });

      gif.on('progress', function(p){
        TCTP.setProgress(progressWrap, 50 + Math.round(p * 45));
      });

      gif.on('finished', function(blob){
        resultBlob = blob;
        TCTP.setProgress(progressWrap, 100);
        TCTP.hideProgress(progressWrap);

        if(preview){
          preview.innerHTML = '';
          var prevImg = document.createElement('img');
          prevImg.src = URL.createObjectURL(blob);
          preview.appendChild(prevImg);
        }
        if(downloadBtn) downloadBtn.style.display = '';
        if(statsEl) statsEl.textContent = TCTP.formatSize(blob.size) + ' | GIF';
        TCTP.toast('Image converted to GIF');
        URL.revokeObjectURL(img.src);
      });

      gif.render();
    };
    img.onerror = function(){
      TCTP.hideProgress(progressWrap);
      TCTP.toast('Failed to load image','error');
    };
    img.src = URL.createObjectURL(file);
  }
})();
