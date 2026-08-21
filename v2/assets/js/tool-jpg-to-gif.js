/**
 * JPG to GIF Converter — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function(){
  'use strict';
  var drop = document.getElementById('tc-j2gif-drop');
  if(!drop) return;

  var convertBtn  = document.getElementById('tc-j2gif-convert');
  var downloadBtn = document.getElementById('tc-j2gif-download');
  var colorsSel   = document.getElementById('tc-j2gif-colors');

  var file = null;
  var resultBlob = null;
  var loaded = false;

  function loadLib(cb){
    if(loaded){ cb(); return; }
    var s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/gif.js@0.2.0/dist/gif.js';
    s.onload = function(){ loaded = true; cb(); };
    s.onerror = function(){ TCTP.toast('Failed to load gif.js', '\u274C'); };
    document.head.appendChild(s);
  }

  TCTP.initDropZone('tc-j2gif-drop', 'tc-j2gif-drop-input', function(f){
    if(!f.type.match(/image\/jpe?g/) && !/\.jpe?g$/i.test(f.name)){
      TCTP.toast('Please select a JPG/JPEG file.', '\u26A0\uFE0F');
      return;
    }
    file = f;
    resultBlob = null;
    TCTP.showFileRow('tc-j2gif-file', f);
    if(downloadBtn) downloadBtn.style.display = 'none';
    setStat('tc-j2gif-stat-comp', '-');
    setStat('tc-j2gif-stat-saved', '-');
  }, 'image/jpeg,.jpg,.jpeg');

  var removeBtn = document.querySelector('#tc-j2gif-file .tc-x');
  if(removeBtn){
    removeBtn.addEventListener('click', function(){
      file = null;
      resultBlob = null;
      TCTP.hideFileRow('tc-j2gif-file');
      if(downloadBtn) downloadBtn.style.display = 'none';
    });
  }

  function setStat(id, val){
    var el = document.getElementById(id);
    if(el) el.textContent = val;
  }

  if(convertBtn){
    convertBtn.addEventListener('click', function(){
      if(!file){ TCTP.toast('Please select a JPG file first.', '\u26A0\uFE0F'); return; }
      loadLib(doConvert);
    });
  }

  if(downloadBtn){
    downloadBtn.addEventListener('click', function(){
      if(!resultBlob){ TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
      var name = (file ? file.name.replace(/\.jpe?g$/i, '') : 'image') + '.gif';
      TCTP.downloadBlob(resultBlob, name);
    });
  }

  function doConvert(){
    TCTP.showProgress('tc-j2gif-progress');
    TCTP.setProgress('tc-j2gif-progress', 30, 'Converting...');

    var img = new Image();
    img.onload = function(){
      var c = document.createElement('canvas');
      c.width = img.naturalWidth;
      c.height = img.naturalHeight;
      var ctx = c.getContext('2d');
      ctx.drawImage(img, 0, 0);

      TCTP.setProgress('tc-j2gif-progress', 50, 'Encoding...');

      var gif = new window.GIF({
        workers: 2,
        quality: 10,
        workerScript: 'https://cdn.jsdelivr.net/npm/gif.js@0.2.0/dist/gif.worker.js',
        colors: colorsSel ? parseInt(colorsSel.value, 10) || 256 : 256
      });

      gif.addFrame(c, { delay: 200, copy: true });

      gif.on('progress', function(p){
        TCTP.setProgress('tc-j2gif-progress', 50 + Math.round(p * 45), 'Encoding...');
      });

      gif.on('finished', function(blob){
        resultBlob = blob;
        TCTP.setProgress('tc-j2gif-progress', 100, 'Done!');
        TCTP.hideProgress('tc-j2gif-progress');

        setStat('tc-j2gif-stat-orig', file ? TCTP.formatSize(file.size) : '-');
        setStat('tc-j2gif-stat-comp', TCTP.formatSize(blob.size));
        if(file){
          var saved = file.size > blob.size ? ((1 - blob.size / file.size) * 100).toFixed(1) : '0';
          setStat('tc-j2gif-stat-saved', saved + '%');
        } else {
          setStat('tc-j2gif-stat-saved', '-');
        }
        if(downloadBtn) downloadBtn.style.display = '';
        TCTP.toast('Converted to GIF!');
      });

      gif.render();
    };
    img.onerror = function(){
      TCTP.hideProgress('tc-j2gif-progress');
      TCTP.toast('Failed to load image', '\u274C');
    };
    img.src = URL.createObjectURL(file);
  }
})();
