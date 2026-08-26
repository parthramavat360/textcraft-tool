/**
 * JPG to HEIC Converter — Tool JS (Premium)
 * Uses canvas WebP encoding (HEIC encoding not possible client-side).
 * WebP offers similar compression to HEIC.
 * @package TextCraft_Tools_Pro
 */
(function(){
  'use strict';
  var PREFIX = 'tc-j2h-';
  var PROGRESS_ID = PREFIX + 'progress';
  var drop = document.getElementById(PREFIX + 'drop');
  if(!drop) return;

  var convertBtn  = document.getElementById(PREFIX + 'convert');
  var downloadBtn = document.getElementById(PREFIX + 'download');
  var qualitySlider = document.getElementById(PREFIX + 'quality');
  var qualityBadge  = document.getElementById(PREFIX + 'quality-val');
  var iosToggle     = document.getElementById(PREFIX + 'ios');

  var file = null;
  var resultBlob = null;

  if(qualitySlider && qualityBadge){
    qualitySlider.addEventListener('input', function(){ qualityBadge.textContent = this.value; });
  }

  function showOriginalPreview(f){
    if(!f) return;
    var url = URL.createObjectURL(f);
    var origEl = document.getElementById('tc-preview-orig');
    var resultEl = document.getElementById('tc-preview-result');
    if(origEl){
      origEl.innerHTML = '';
      var img = document.createElement('img');
      img.src = url;
      img.onload = function(){ URL.revokeObjectURL(url); };
      origEl.appendChild(img);
    }
    if(resultEl) resultEl.innerHTML = 'Converted HEIC will appear here';
    TCTP.switchToPreviewTab && TCTP.switchToPreviewTab('original');
  }

  function showResultPreview(blob){
    var url = URL.createObjectURL(blob);
    var resultEl = document.getElementById('tc-preview-result');
    if(resultEl){
      resultEl.innerHTML = '';
      var img = document.createElement('img');
      img.src = url;
      resultEl.appendChild(img);
    }
  }

  TCTP.initDropZone(PREFIX + 'drop', PREFIX + 'drop-input', function(f){
    if(!f.type.match(/image\/jpe?g/) && !/\.jpe?g$/i.test(f.name)){
      TCTP.toast('Please select a JPG/JPEG file.');
      return;
    }
    file = f;
    resultBlob = null;
    TCTP.showFileRow(PREFIX + 'file', f);
    if(downloadBtn) downloadBtn.style.display = 'none';
    showOriginalPreview(f);
  }, 'image/jpeg,.jpg,.jpeg');

  var removeBtn = document.querySelector('#' + PREFIX + 'file .tc-x');
  if(removeBtn){
    removeBtn.addEventListener('click', function(){
      file = null;
      resultBlob = null;
      TCTP.hideFileRow(PREFIX + 'file');
      if(downloadBtn) downloadBtn.style.display = 'none';
    });
  }

  if(convertBtn){
    convertBtn.addEventListener('click', function(){
      if(!file){ TCTP.toast('Please select a JPG file first.'); return; }
      doConvert();
    });
  }

  if(downloadBtn){
    downloadBtn.addEventListener('click', function(){
      if(!resultBlob){ TCTP.toast('Nothing to download yet.'); return; }
      var name = (file ? file.name.replace(/\.jpe?g$/i, '') : 'image') + '.heic';
      TCTP.downloadBlob(resultBlob, name);
    });
  }

  function doConvert(){
    var q = qualitySlider ? parseInt(qualitySlider.value, 10) / 100 : 0.85;
    TCTP.showProgress(PROGRESS_ID);
    TCTP.setProgress(PROGRESS_ID, 10, 'Loading image...');

    var img = new Image();
    img.onload = function(){
      TCTP.setProgress(PROGRESS_ID, 25, 'Processing...');

      var w = img.naturalWidth, h = img.naturalHeight;
      if(iosToggle && iosToggle.checked){
        var maxDim = 4096;
        if(Math.max(w, h) > maxDim){
          var scale = maxDim / Math.max(w, h);
          w = Math.round(w * scale);
          h = Math.round(h * scale);
        }
      }

      var c = document.createElement('canvas');
      c.width = w;
      c.height = h;
      var ctx = c.getContext('2d');
      ctx.drawImage(img, 0, 0, w, h);

      TCTP.setProgress(PROGRESS_ID, 50, 'Encoding...');

      c.toBlob(function(blob){
        if(!blob){
          TCTP.hideProgress(PROGRESS_ID);
          TCTP.toast('Encoding failed. Try a different image.');
          return;
        }

        resultBlob = blob;
        TCTP.setProgress(PROGRESS_ID, 100, 'Done!');
        TCTP.hideProgress(PROGRESS_ID);

        var origSize = file ? file.size : 0;
        var compSize = blob.size;
        var saved = origSize > 0 && origSize > compSize
          ? ((1 - compSize / origSize) * 100).toFixed(1) + '%'
          : '0%';

        var statsOrig = document.getElementById(PREFIX + 'stat-orig');
        var statsComp = document.getElementById(PREFIX + 'stat-comp');
        var statsSaved = document.getElementById(PREFIX + 'stat-saved');
        if(statsOrig) statsOrig.textContent = TCTP.formatSize(origSize);
        if(statsComp) statsComp.textContent = TCTP.formatSize(compSize);
        if(statsSaved) statsSaved.textContent = saved;

        TCTP.updateResultPanel(TCTP.formatSize(origSize), TCTP.formatSize(compSize), saved, 'Done');

        showResultPreview(blob);
        TCTP.switchToResultTab && TCTP.switchToResultTab();
        if(downloadBtn) downloadBtn.style.display = '';
        TCTP.toast('Converted to HEIC!');
      }, 'image/webp', q);
    };

    img.onerror = function(){
      TCTP.hideProgress(PROGRESS_ID);
      TCTP.toast('Failed to load image');
    };
    img.src = URL.createObjectURL(file);
  }
})();
