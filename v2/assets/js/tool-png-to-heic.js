/**
 * PNG to HEIC Converter â€” Tool JS
 * @package TextCraft_Tools_Pro
 */

(function(){
  'use strict';
  var drop = document.getElementById('tc-p2h-drop');
  if(!drop) return;

  var convertBtn   = document.getElementById('tc-p2h-convert');
  var qualityRange = document.getElementById('tc-p2h-quality');
  var qualityVal   = document.getElementById('tc-p2h-quality-val');
  var downloadBtn  = document.getElementById('tc-p2h-download');

  var file = null;
  var resultBlob = null;
  var loaded = false;

  function loadLib(cb){
    if(loaded){ cb(); return; }
    var s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/libheif-js@1.17.6/libheif-js/libheif.js';
    s.onload = function(){ loaded = true; cb(); };
    s.onerror = function(){ TCTP.toast('Failed to load HEIC encoder', '\u274C'); };
    document.head.appendChild(s);
  }

  function setStat(id, val){
    var el = document.getElementById(id);
    if(el) el.textContent = val;
  }

  TCTP.initDropZone('tc-p2h-drop', 'tc-p2h-drop-input', function(f){
    if(!f.type.match(/image\/png/) && !/\.png$/i.test(f.name)){
      TCTP.toast('Please select a PNG file.', '\u26A0\uFE0F');
      return;
    }
    file = f;
    resultBlob = null;
    TCTP.showFileRow('tc-p2h-file', f);
    if(downloadBtn) downloadBtn.style.display = 'none';
    setStat('tc-p2h-stat-comp', '-');
    setStat('tc-p2h-stat-saved', '-');
  }, 'image/png,.png');

  var removeBtn = document.querySelector('#tc-p2h-file .tc-x');
  if(removeBtn){
    removeBtn.addEventListener('click', function(){
      file = null;
      resultBlob = null;
      TCTP.hideFileRow('tc-p2h-file');
      if(downloadBtn) downloadBtn.style.display = 'none';
    });
  }

  if(qualityRange){
    if(qualityVal) qualityVal.textContent = qualityRange.value + '%';
    qualityRange.addEventListener('input', function(){
      if(qualityVal) qualityVal.textContent = qualityRange.value + '%';
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
      if(!resultBlob){ TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
      var name = (file ? file.name.replace(/\.png$/i, '') : 'image') + '.heic';
      TCTP.downloadBlob(resultBlob, name);
    });
  }

  function doConvert(){
    var q = qualityRange ? parseInt(qualityRange.value, 10) : 85;
    TCTP.showProgress('tc-p2h-progress');
    TCTP.setProgress('tc-p2h-progress', 30, 'Converting...');

    var img = new Image();
    img.onload = function(){
      var c = document.createElement('canvas');
      c.width = img.naturalWidth;
      c.height = img.naturalHeight;
      var ctx = c.getContext('2d');
      ctx.drawImage(img, 0, 0);

      TCTP.setProgress('tc-p2h-progress', 50, 'Encoding...');

      try {
        var encoder = new window.libheif.HeifEncoder();
        var imageData = ctx.getImageData(0, 0, c.width, c.height);
        var heifBuffer = encoder.encode(imageData.data, c.width, c.height, q);

        resultBlob = new Blob([heifBuffer], { type: 'image/heic' });
        TCTP.setProgress('tc-p2h-progress', 100, 'Done!');
        TCTP.hideProgress('tc-p2h-progress');

        setStat('tc-p2h-stat-orig', file ? TCTP.formatSize(file.size) : '-');
        setStat('tc-p2h-stat-comp', TCTP.formatSize(resultBlob.size));
        if(file && file.size > 0){
          var saved = file.size > resultBlob.size ? ((1 - resultBlob.size / file.size) * 100).toFixed(1) : '0';
          setStat('tc-p2h-stat-saved', saved + '%');
        } else {
          setStat('tc-p2h-stat-saved', '-');
        }
        TCTP.updateResultPanel(TCTP.formatSize(file.size), TCTP.formatSize(resultBlob.size), (file.size > resultBlob.size ? ((1 - resultBlob.size / file.size) * 100).toFixed(1) : '0') + '%', 'Done');
                            TCTP.showResultPreview(URL.createObjectURL(resultBlob));
        TCTP.switchToResultTab();
        if(downloadBtn) downloadBtn.style.display = '';
        TCTP.toast('Converted to HEIC!');
      } catch(err){
        TCTP.hideProgress('tc-p2h-progress');
        TCTP.toast('HEIC encoding failed: ' + err.message, '\u274C');
      }
    };
    img.onerror = function(){
      TCTP.hideProgress('tc-p2h-progress');
      TCTP.toast('Failed to load image', '\u274C');
    };
    img.src = URL.createObjectURL(file);
  }
})();
