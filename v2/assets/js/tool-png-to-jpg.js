(function(){
  'use strict';
  var prefix = 'tc-p2j-';
  var dropEl = document.getElementById(prefix+'drop');
  if(!dropEl) return;

  var convertBtn   = document.getElementById(prefix+'convert');
  var qualityRange = document.getElementById(prefix+'quality');
  var qualityVal   = document.getElementById(prefix+'quality-val');
  var bgcolorInput = document.getElementById(prefix+'bgcolor');
  var downloadBtn  = document.getElementById(prefix+'download');
  var preview      = document.getElementById(prefix+'preview');
  var fileRow      = document.getElementById(prefix+'file-row');
  var progressWrap = document.getElementById(prefix+'progress');
  var statsEl      = document.getElementById(prefix+'stats');

  var file = null;
  var resultBlob = null;

  TCTP.initDropZone(prefix+'drop', prefix+'drop-input', function(f){
    if (f.type !== 'image/png' && !/\.png$/i.test(f.name)) {
      TCTP.toast('Please select a PNG file.', '\u26A0\uFE0F');
      return;
    }
    file = f;
    resultBlob = null;
    TCTP.showFileRow(prefix+'file-row', f);
    if(preview) preview.innerHTML = '';
    if(statsEl) statsEl.textContent = '';
    if(downloadBtn) downloadBtn.style.display = 'none';
  }, 'image/png,.png');

  if(qualityRange && qualityVal){
    qualityRange.addEventListener('input', function(){
      qualityVal.textContent = qualityRange.value + '%';
    });
  }

  if(convertBtn){
    convertBtn.addEventListener('click', function(){
      if(!file){ TCTP.toast('Please drop a PNG image first','warning'); return; }
      doConvert();
    });
  }

  if(downloadBtn){
    downloadBtn.addEventListener('click', function(){
      if(resultBlob) TCTP.downloadBlob(resultBlob, 'converted.jpg');
    });
  }

  function doConvert(){
    var q = qualityRange ? parseInt(qualityRange.value,10) / 100 : 0.92;
    var bg = bgcolorInput ? bgcolorInput.value : '#ffffff';

    TCTP.showProgress(progressWrap);
    TCTP.setProgress(progressWrap, 10);

    var img = new Image();
    img.onload = function(){
      TCTP.setProgress(progressWrap, 40);
      var c = document.createElement('canvas');
      c.width = img.naturalWidth;
      c.height = img.naturalHeight;
      var ctx = c.getContext('2d');

      ctx.fillStyle = bg;
      ctx.fillRect(0, 0, c.width, c.height);
      ctx.drawImage(img, 0, 0);
      TCTP.setProgress(progressWrap, 70);

      c.toBlob(function(blob){
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
        if(statsEl) statsEl.textContent = TCTP.formatSize(blob.size) + ' | JPG';
        TCTP.updateResultPanel(TCTP.formatSize(file.size), TCTP.formatSize(blob.size), (file.size > 0 ? ((1 - blob.size / file.size) * 100).toFixed(1) : '0') + '%', 'Done');
                            TCTP.showResultPreview(URL.createObjectURL(resultBlob));
        TCTP.switchToResultTab();
        TCTP.toast('PNG converted to JPG');
        URL.revokeObjectURL(img.src);
      }, 'image/jpeg', q);
    };
    img.onerror = function(){
      TCTP.hideProgress(progressWrap);
      TCTP.toast('Failed to load image','error');
    };
    img.src = URL.createObjectURL(file);
  }
})();
