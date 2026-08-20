(function(){
  'use strict';
  var prefix = 'tc-rbg-';
  var dropEl = document.getElementById(prefix+'drop');
  if(!dropEl) return;

  var removeBtn = document.getElementById(prefix+'remove');
  var preview   = document.getElementById(prefix+'preview');
  var downloadBtn = document.getElementById(prefix+'download');
  var fileRow   = document.getElementById(prefix+'file-row');
  var progressWrap = document.getElementById(prefix+'progress');
  var statsEl   = document.getElementById(prefix+'stats');

  var file = null;
  var resultBlob = null;
  var loaded = false;

  function loadLib(cb){
    if(loaded){ cb(); return; }
    var s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/@imgly/background-removal@1.5.16/dist/index.umd.js';
    s.onload = function(){ loaded = true; cb(); };
    s.onerror = function(){ TCTP.toast('Failed to load background-removal library','error'); };
    document.head.appendChild(s);
  }

  TCTP.initDropZone(dropEl, function(f){
    file = f;
    resultBlob = null;
    TCTP.showFileRow(fileRow, f.name);
    if(preview){
      preview.innerHTML = '';
      var img = document.createElement('img');
      img.src = URL.createObjectURL(f);
      preview.appendChild(img);
    }
    if(statsEl) statsEl.textContent = '';
    if(downloadBtn) downloadBtn.style.display = 'none';
  });

  if(removeBtn){
    removeBtn.addEventListener('click', function(){
      if(!file){ TCTP.toast('Please drop an image first','warning'); return; }
      loadLib(function(){ doRemove(); });
    });
  }

  if(downloadBtn){
    downloadBtn.addEventListener('click', function(){
      if(resultBlob) TCTP.downloadBlob(resultBlob, 'no-bg.png');
    });
  }

  function doRemove(){
    TCTP.showProgress(progressWrap);
    TCTP.setProgress(progressWrap, 5);

    var url = URL.createObjectURL(file);
    var imgBlob = file;

    removeBackground(imgBlob, {
      progress: function(key, current, total){
        var pct = Math.round((current / total) * 90) + 5;
        TCTP.setProgress(progressWrap, pct);
      }
    }).then(function(blob){
      resultBlob = blob;
      TCTP.setProgress(progressWrap, 100);
      TCTP.hideProgress(progressWrap);

      if(preview){
        var oldImg = preview.querySelector('img');
        if(oldImg) URL.revokeObjectURL(oldImg.src);
        preview.innerHTML = '';
        var newImg = document.createElement('img');
        newImg.src = URL.createObjectURL(blob);
        preview.appendChild(newImg);
      }

      if(downloadBtn) downloadBtn.style.display = '';

      var size = TCTP.formatSize(blob.size);
      if(statsEl) statsEl.textContent = 'Result: ' + size + ' | ' + blob.type;
      TCTP.toast('Background removed');
      URL.revokeObjectURL(url);
    }).catch(function(err){
      TCTP.hideProgress(progressWrap);
      TCTP.toast('Failed: ' + err.message, 'error');
      URL.revokeObjectURL(url);
    });
  }
})();
