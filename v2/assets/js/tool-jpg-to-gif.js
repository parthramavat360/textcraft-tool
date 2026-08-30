/**
 * JPG to GIF Converter - Tool JS (Premium, omggif-based)
 * @package TextCraft_Tools_Pro
 */
(function(){
  'use strict';
  var PREFIX = 'tc-j2gif-';
  var drop = document.getElementById(PREFIX + 'drop');
  if(!drop) return;

  var PROGRESS_ID = PREFIX + 'progress';
  var convertBtn  = document.getElementById(PREFIX + 'convert');
  var downloadBtn = document.getElementById(PREFIX + 'download');
  var clearBtn    = document.getElementById(PREFIX + 'clear');
  var file = null;
  var resultBlob = null;
  var converting = false;
  var selectedColors = 128;

  var delaySlider = document.getElementById(PREFIX + 'delay');
  var delayBadge  = document.getElementById(PREFIX + 'delay-val');
  var qualSlider  = document.getElementById(PREFIX + 'quality');
  var qualBadge   = document.getElementById(PREFIX + 'quality-val');
  var iosToggle   = document.getElementById(PREFIX + 'ios');

  if(delaySlider && delayBadge){
    delaySlider.addEventListener('input', function(){ delayBadge.textContent = this.value; });
  }
  if(qualSlider && qualBadge){
    qualSlider.addEventListener('input', function(){ qualBadge.textContent = this.value; });
  }

  var colorGroup = document.querySelector('[data-group="j2gif-colors"]');
  if(colorGroup){
    TCTP.initModeGroup(colorGroup, function(val){
      selectedColors = parseInt(val, 10) || 128;
    });
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
    if(resultEl) resultEl.innerHTML = 'Converted GIF will appear here';
    TCTP.switchToOriginalTab && TCTP.switchToOriginalTab();
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
      converting = false;
      TCTP.hideFileRow(PREFIX + 'file');
      if(downloadBtn) downloadBtn.style.display = 'none';
    });
  }

  if(convertBtn){
    convertBtn.addEventListener('click', function(){
      if(converting) return;
      if(!file){ TCTP.toast('Please select a JPG file first.'); return; }
      if(typeof omggif === 'undefined'){ TCTP.toast('Library still loading, please try again.'); return; }
      doConvert();
    });
  }

  if(downloadBtn){
    downloadBtn.addEventListener('click', function(){
      if(!resultBlob){ TCTP.toast('Nothing to download yet.'); return; }
      var nameInput = document.getElementById(PREFIX + 'name');
      var base = (nameInput && nameInput.value.trim()) ? nameInput.value.trim().replace(/\.gif$/i, '') : (file ? file.name.replace(/\.jpe?g$/i, '') : 'image');
      TCTP.downloadBlob(resultBlob, base + '.gif');
    });
  }

  if(clearBtn){
    clearBtn.addEventListener('click', function(){
      file = null;
      resultBlob = null;
      converting = false;
      var row = document.getElementById(PREFIX + 'file');
      if(row){ row.style.display = 'none'; row.classList.remove('visible'); }
      if(downloadBtn) downloadBtn.style.display = 'none';
      var sOrig = document.getElementById(PREFIX + 'stat-orig');
      var sComp = document.getElementById(PREFIX + 'stat-comp');
      var sSaved = document.getElementById(PREFIX + 'stat-saved');
      if(sOrig) sOrig.textContent = '-';
      if(sComp) sComp.textContent = '-';
      if(sSaved) sSaved.textContent = '-';
      var nameInput = document.getElementById(PREFIX + 'name');
      if(nameInput) nameInput.value = '';
      var origP = document.getElementById('tc-preview-orig');
      if(origP) origP.innerHTML = '<span style="color:var(--muted);font-size:13px">Original preview will appear here</span>';
      var resP = document.getElementById('tc-preview-result');
      if(resP) resP.innerHTML = '<span style="color:var(--muted);font-size:13px">Result preview will appear here</span>';
      TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Ready');
      TCTP.switchToOriginalTab && TCTP.switchToOriginalTab();
    });
  }

  function nextPow2(n){ var p = 2; while(p < n) p <<= 1; return p; }

  function medianCut(rgb, numColors){
    if(rgb.length === 0) return [[0,0,0]];
    if(numColors <= 1){
      var r=0,g=0,b=0; for(var i=0;i<rgb.length;i++){r+=rgb[i][0];g+=rgb[i][1];b+=rgb[i][2];}
      var n=rgb.length||1; return [[Math.round(r/n),Math.round(g/n),Math.round(b/n)]];
    }
    var buckets = [rgb];
    while(buckets.length < numColors){
      var maxR=-1, maxI=0;
      for(var bi=0;bi<buckets.length;bi++){
        var mins=[255,255,255],maxs=[0,0,0],bk=buckets[bi];
        for(var i=0;i<bk.length;i++){for(var c=0;c<3;c++){if(bk[i][c]<mins[c])mins[c]=bk[i][c];if(bk[i][c]>maxs[c])maxs[c]=bk[i][c];}}
        var rng=[maxs[0]-mins[0],maxs[1]-mins[1],maxs[2]-mins[2]];var mx=0,ch=0;
        for(var c=0;c<3;c++){if(rng[c]>mx){mx=rng[c];ch=c;}}
        if(mx>maxR){maxR=mx;maxI=bi;}
      }
      if(maxR<=0)break;
      var bucket=buckets.splice(maxI,1)[0];
      var mins2=[255,255,255],maxs2=[0,0,0];
      for(var i=0;i<bucket.length;i++){for(var c=0;c<3;c++){if(bucket[i][c]<mins2[c])mins2[c]=bucket[i][c];if(bucket[i][c]>maxs2[c])maxs2[c]=bucket[i][c];}}
      var rng2=[maxs2[0]-mins2[0],maxs2[1]-mins2[1],maxs2[2]-mins2[2]];var ch2=0;
      for(var c=1;c<3;c++){if(rng2[c]>rng2[ch2])ch2=c;}
      bucket.sort(function(a,b){return a[ch2]-b[ch2];});
      var mid=Math.floor(bucket.length/2);
      buckets.push(bucket.slice(0,mid),bucket.slice(mid));
    }
    var res=[];
    for(var bi=0;bi<buckets.length;bi++){
      var rr=0,gg=0,bb=0;
      for(var i=0;i<buckets[bi].length;i++){rr+=buckets[bi][i][0];gg+=buckets[bi][i][1];bb+=buckets[bi][i][2];}
      var n=buckets[bi].length||1;
      res.push([Math.round(rr/n),Math.round(gg/n),Math.round(bb/n)]);
    }
    return res;
  }

  function quantizeImage(imageData, w, h, numColors){
    var data = imageData.data;
    var len = w * h;
    numColors = Math.max(2, Math.min(256, numColors));

    var rgb = [];
    for(var i = 0; i < len; i++){
      var off = i * 4;
      rgb.push([data[off], data[off+1], data[off+2]]);
    }

    var palette = medianCut(rgb, numColors);

    var indexed = new Uint8Array(len);
    for(var i = 0; i < len; i++){
      var r = data[i*4], g = data[i*4+1], b = data[i*4+2];
      var best = 0, bestD = Infinity;
      for(var p = 0; p < palette.length; p++){
        var dr = r - palette[p][0], dg = g - palette[p][1], db = b - palette[p][2];
        var d = dr*dr + dg*dg + db*db;
        if(d < bestD){ bestD = d; best = p; if(d === 0) break; }
      }
      indexed[i] = best;
    }

    var pw = nextPow2(Math.max(2, palette.length));
    var palettePacked = [];
    for(var p = 0; p < palette.length; p++){
      palettePacked.push((palette[p][0] << 16) | (palette[p][1] << 8) | palette[p][2]);
    }
    while(palettePacked.length < pw) palettePacked.push(0);

    return { palette: palettePacked, indexed: indexed };
  }

  function doConvert(){
    converting = true;
    TCTP.showProgress(PROGRESS_ID);
    TCTP.setProgress(PROGRESS_ID, 5, 'Loading image...');

    var img = new Image();
    img.onload = function(){
      TCTP.setProgress(PROGRESS_ID, 15, 'Processing...');

      var ow = img.naturalWidth, oh = img.naturalHeight;
      var w = ow, h = oh;
      var MAX_DIM = 1500;
      if(Math.max(w, h) > MAX_DIM){
        var scale = MAX_DIM / Math.max(w, h);
        w = Math.round(w * scale);
        h = Math.round(h * scale);
      }

      var c = document.createElement('canvas');
      c.width = w; c.height = h;
      var ctx = c.getContext('2d');
      ctx.drawImage(img, 0, 0, w, h);
      var imageData = ctx.getImageData(0, 0, w, h);

      TCTP.setProgress(PROGRESS_ID, 30, 'Quantizing colors...');

      var result = quantizeImage(imageData, w, h, selectedColors);

      TCTP.setProgress(PROGRESS_ID, 60, 'Encoding GIF...');

      var delay = delaySlider ? parseInt(delaySlider.value, 10) || 200 : 200;
      var delayCs = Math.max(1, Math.round(delay / 10));

      var bufSize = w * h * 2 + 4096;
      var buf = new Uint8Array(bufSize);
      var writer = new omggif.GifWriter(buf, w, h, { loop: 0 });
      try {
        writer.addFrame(0, 0, w, h, result.indexed, {
          palette: result.palette,
          delay: delayCs,
          disposal: 2
        });
      } catch(e) {
        TCTP.hideProgress(PROGRESS_ID);
        converting = false;
        TCTP.toast('Encoding failed: ' + (e.message || 'Unknown error'));
        return;
      }
      var endPos = writer.end();
      var gifBytes = new Uint8Array(buf.buffer, 0, endPos);

      TCTP.setProgress(PROGRESS_ID, 90, 'Finalizing...');

      var blob = new Blob([gifBytes], { type: 'image/gif' });
      resultBlob = blob;

      TCTP.setProgress(PROGRESS_ID, 100, 'Done!');
      TCTP.hideProgress(PROGRESS_ID);

      var origSize = file ? file.size : 0;
      var saved = origSize > 0 && origSize > blob.size
        ? ((1 - blob.size / origSize) * 100).toFixed(1) + '%'
        : '0%';

      var statsOrig = document.getElementById(PREFIX + 'stat-orig');
      var statsComp = document.getElementById(PREFIX + 'stat-comp');
      var statsSaved = document.getElementById(PREFIX + 'stat-saved');
      if(statsOrig) statsOrig.textContent = TCTP.formatSize(origSize);
      if(statsComp) statsComp.textContent = TCTP.formatSize(blob.size);
      if(statsSaved) statsSaved.textContent = saved;

      TCTP.updateResultPanel(TCTP.formatSize(origSize), TCTP.formatSize(blob.size), saved, 'Done');

      showResultPreview(blob);
      TCTP.switchToResultTab && TCTP.switchToResultTab();
      if(downloadBtn) downloadBtn.style.display = '';
      converting = false;
      TCTP.toast('Converted to GIF!');
    };

    img.onerror = function(){
      TCTP.hideProgress(PROGRESS_ID);
      converting = false;
      TCTP.toast('Failed to load image');
    };
    img.src = URL.createObjectURL(file);
  }
})();
