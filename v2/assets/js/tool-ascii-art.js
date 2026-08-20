(function(){
  'use strict';
  var prefix = 'tc-aa-';
  var dropEl = document.getElementById(prefix+'drop');
  if(!dropEl) return;

  var convertBtn = document.getElementById(prefix+'convert');
  var output     = document.getElementById(prefix+'output');
  var densitySel = document.getElementById(prefix+'density');
  var formatSel  = document.getElementById(prefix+'format');
  var widthRange = document.getElementById(prefix+'width');
  var fileRow    = document.getElementById(prefix+'file-row');
  var progressWrap = document.getElementById(prefix+'progress');
  var statsEl    = document.getElementById(prefix+'stats');

  var file = null;

  var loaded = false;
  function loadLib(cb){
    if(loaded){ cb(); return; }
    var s = document.createElement('script');
    s.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
    s.onload = function(){ loaded = true; cb(); };
    s.onerror = function(){ TCTP.toast('Failed to load html2canvas','error'); };
    document.head.appendChild(s);
  }

  var CHARS_LIGHT = ' .\'`^",:;Il!i><~+_-?][}{1)(|\\/tfjrxnuvczXYUJCLQ0OZmwqpdbkhao*#MW&8%B@$';
  var CHARS_DARK  = '$@B%8&WM#*oahkbdpqwmZO0QLCJUYXzcvunxrjft/\\|()1{}[]?-_+~<>i!lI;:,"^`\'. ';

  TCTP.initDropZone(dropEl, function(f){
    file = f;
    TCTP.showFileRow(fileRow, f.name);
    if(output) output.innerHTML = '';
    if(statsEl) statsEl.textContent = '';
  });

  if(convertBtn){
    convertBtn.addEventListener('click', function(){
      if(!file){ TCTP.toast('Please drop an image first','warning'); return; }
      loadLib(function(){ doConvert(); });
    });
  }

  function doConvert(){
    var density = densitySel ? parseInt(densitySel.value,10) : 80;
    var fmt     = formatSel  ? formatSel.value : 'light';
    var maxW    = widthRange ? parseInt(widthRange.value,10) : 100;
    var charSet = fmt === 'dark' ? CHARS_DARK : CHARS_LIGHT;

    TCTP.showProgress(progressWrap);
    TCTP.setProgress(progressWrap, 10);

    var img = new Image();
    img.onload = function(){
      TCTP.setProgress(progressWrap, 30);
      var scale = density / Math.max(img.naturalWidth, img.naturalHeight);
      var w = Math.round(img.naturalWidth * scale);
      var h = Math.round(img.naturalHeight * scale * 0.55);

      var c = document.createElement('canvas');
      c.width = w; c.height = h;
      var ctx = c.getContext('2d');
      ctx.drawImage(img, 0, 0, w, h);
      TCTP.setProgress(progressWrap, 50);

      var data = ctx.getImageData(0, 0, w, h).data;
      var lines = [];
      for(var y = 0; y < h; y++){
        var row = '';
        for(var x = 0; x < w; x++){
          var i = (y * w + x) * 4;
          var gray = 0.299 * data[i] + 0.587 * data[i+1] + 0.114 * data[i+2];
          var idx = Math.round((gray / 255) * (charSet.length - 1));
          row += charSet[idx];
        }
        lines.push(row);
      }

      TCTP.setProgress(progressWrap, 90);
      var ascii = lines.join('\n');
      if(output){
        var pre = document.createElement('pre');
        pre.className = 'tctp-ascii-output';
        pre.textContent = ascii;
        output.innerHTML = '';
        output.appendChild(pre);
      }

      if(statsEl){
        statsEl.textContent = w + 'x' + h + ' chars | ' + ascii.length + ' total characters';
      }
      TCTP.setProgress(progressWrap, 100);
      TCTP.hideProgress(progressWrap);
      TCTP.toast('ASCII art generated');
    };
    img.onerror = function(){
      TCTP.hideProgress(progressWrap);
      TCTP.toast('Failed to load image','error');
    };
    img.src = URL.createObjectURL(file);
  }
})();
