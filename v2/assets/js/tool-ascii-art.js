/**
 * ASCII Art Generator — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function(){
  'use strict';
  var drop = document.getElementById('tc-ascii-drop');
  if(!drop) return;

  var generateBtn = document.getElementById('tc-ascii-generate');
  var output      = document.getElementById('tc-ascii-output');
  var densitySel  = document.getElementById('tc-ascii-density');
  var formatSel   = document.getElementById('tc-ascii-format');
  var widthRange  = document.getElementById('tc-ascii-width');
  var statusEl    = document.getElementById('tc-ascii-status');
  var copyBtn     = document.getElementById('tc-ascii-copy');

  var file = null;

  var CHARS_LIGHT = ' .\'`^",:;Il!i><~+_-?][}{1)(|\\/tfjrxnuvczXYUJCLQ0OZmwqpdbkhao*#MW&8%B@$';
  var CHARS_DARK  = '$@B%8&WM#*oahkbdpqwmZO0QLCJUYXzcvunxrjft/\\|()1{}[]?-_+~<>i!lI;:,"^`\'. ';
  var CHARS_BLOCKS  = '\u2588\u2593\u2592\u2591 ';
  var CHARS_SYMBOLS = '$@#%*+=-:.!~ ';

  function charSetFor(fmt){
    if(fmt === 'blocks') return CHARS_BLOCKS;
    if(fmt === 'symbols') return CHARS_SYMBOLS;
    if(fmt === 'dark') return CHARS_DARK;
    return CHARS_LIGHT;
  }

  function densityTarget(val){
    if(val === 'detailed') return 200;
    if(val === 'simple') return 60;
    return 120;
  }

  TCTP.initDropZone('tc-ascii-drop', 'tc-ascii-drop-input', function(f){
    if(!f.type.match(/image\//)){
      TCTP.toast('Please select an image file.', '\u26A0\uFE0F');
      return;
    }
    file = f;
    TCTP.showFileRow('tc-ascii-file', f);
    if(output) output.value = '';
    if(statusEl) statusEl.textContent = '';
  }, 'image/*');

  var removeBtn = document.querySelector('#tc-ascii-file .tc-x');
  if(removeBtn){
    removeBtn.addEventListener('click', function(){
      file = null;
      TCTP.hideFileRow('tc-ascii-file');
      if(output) output.value = '';
      if(statusEl) statusEl.textContent = '';
    });
  }

  if(generateBtn){
    generateBtn.addEventListener('click', function(){
      if(!file){ TCTP.toast('Please drop an image first', '\u26A0\uFE0F'); return; }
      doConvert();
    });
  }

  function doConvert(){
    var densityVal = densitySel ? densitySel.value : 'medium';
    var fmt        = formatSel  ? formatSel.value : 'characters';
    var maxW       = widthRange ? parseInt(widthRange.value, 10) : 120;
    var charSet    = charSetFor(fmt);
    var target     = Math.min(densityTarget(densityVal), maxW);

    TCTP.showProgress('tc-ascii-progress');
    TCTP.setProgress('tc-ascii-progress', 10, 'Generating...');

    var img = new Image();
    img.onload = function(){
      TCTP.setProgress('tc-ascii-progress', 30, 'Generating...');
      var scale = target / Math.max(img.naturalWidth, img.naturalHeight);
      var w = Math.max(1, Math.round(img.naturalWidth * scale));
      var h = Math.max(1, Math.round(img.naturalHeight * scale * 0.55));

      var c = document.createElement('canvas');
      c.width = w; c.height = h;
      var ctx = c.getContext('2d');
      ctx.drawImage(img, 0, 0, w, h);
      TCTP.setProgress('tc-ascii-progress', 50, 'Generating...');

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

      TCTP.setProgress('tc-ascii-progress', 90, 'Generating...');
      var ascii = lines.join('\n');
      if(output) output.value = ascii;
      if(statusEl) statusEl.textContent = w + '\u00D7' + h + ' chars | ' + ascii.length + ' total characters';
      TCTP.updateResultPanel((file.size / 1024).toFixed(1) + ' KB', ascii.length.toLocaleString() + ' chars', '\u2014', 'Done');
      TCTP.setProgress('tc-ascii-progress', 100, 'Done!');
      TCTP.hideProgress('tc-ascii-progress');
      TCTP.toast('ASCII art generated!');
    };
    img.onerror = function(){
      TCTP.hideProgress('tc-ascii-progress');
      TCTP.toast('Failed to load image', '\u274C');
    };
    img.src = URL.createObjectURL(file);
  }

  if(copyBtn){
    copyBtn.addEventListener('click', function(){
      TCTP.copyText(output ? output.value : '', 'ASCII art');
    });
  }
})();
