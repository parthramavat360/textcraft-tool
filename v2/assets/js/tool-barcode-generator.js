/**
 * Barcode Generator — uses JsBarcode library (loaded from CDN)
 * Generates barcodes as SVG/PNG, 100% client-side
 */
(function(){
  if(!window.TCTP) return;
  const $ = s => document.querySelector(s);

  let totalGenerated = 0;

  function loadScript(src){
    return new Promise((resolve, reject) => {
      if(document.querySelector('script[src="'+src+'"]')){ resolve(); return; }
      const s = document.createElement('script');
      s.src = src;
      s.onload = resolve;
      s.onerror = reject;
      document.head.appendChild(s);
    });
  }

  function getFormat(){ return document.querySelector('.tc-modes[data-group="bg-format"] .sel')?.dataset.val || 'CODE128'; }
  function getWidth(){ return parseFloat($('#tc-bg-width')?.value || '2'); }
  function getHeight(){ return parseInt($('#tc-bg-height')?.value || '100'); }
  function showText(){ return $('#tc-bg-text')?.checked; }

  function getFormatLabel(f){
    const m = {CODE128:'CODE128',EAN13:'EAN-13',EAN8:'EAN-8',UPCA:'UPC-A',CODE39:'CODE39',ITF14:'ITF-14',MSI:'MSI'};
    return m[f] || f;
  }

  document.addEventListener('DOMContentLoaded', function(){
    const btn = $('#tc-bg-generate');
    if(!btn) return;

    $$('.tc-modes[data-group="bg-format"] .tc-btn').forEach(c => c.addEventListener('click', function(){
      this.closest('.tc-modes').querySelectorAll('.tc-btn').forEach(x => x.classList.remove('sel'));
      this.classList.add('sel');
      const fmt = this.dataset.val;
      $('#tc-bg-stat-format').textContent = getFormatLabel(fmt);
    }));

    $('#tc-bg-width')?.addEventListener('input', function(){ $('#tc-bg-width-val').textContent = this.value; });
    $('#tc-bg-height')?.addEventListener('input', function(){ $('#tc-bg-height-val').textContent = this.value; });

    $('#tc-bg-value')?.addEventListener('input', function(){
      const format = getFormat();
      const val = this.value;
      if(format === 'EAN13' && val.length > 13) this.value = val.slice(0, 13);
      if(format === 'EAN8' && val.length > 8) this.value = val.slice(0, 8);
      if(format === 'UPCA' && val.length > 12) this.value = val.slice(0, 12);
      $('#tc-bg-stat-chars').textContent = this.value.length;
    });

    btn.addEventListener('click', async function(){
      const value = $('#tc-bg-value')?.value?.trim();
      if(!value){ TCTP.toast('Please enter a value to encode','warning'); return; }

      const format = getFormat();
      const width = getWidth();
      const height = getHeight();
      const text = showText();

      TCTP.showProgress('tc-bg-progress', 'Generating barcode...', 30);

      try {
        await loadScript('https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js');
      } catch(e){}

      TCTP.setProgress('tc-bg-progress', 60, 'Rendering...');

      setTimeout(() => {
        if(!window.JsBarcode){ TCTP.toast('Barcode library failed to load','error'); return; }

        const output = $('#tc-bg-output');
        if(!output) return;

        output.innerHTML = '<svg id="tc-bg-svg"></svg>';
        const svg = output.querySelector('svg');

        try {
          JsBarcode(svg, value, {
            format: format,
            width: width,
            height: height,
            displayValue: text,
            font: 'monospace',
            fontSize: 14,
            margin: 10,
            background: '#ffffff',
            lineColor: '#000000'
          });

          totalGenerated++;
          TCTP.setProgress('tc-bg-progress', 100, 'Done!');

          // Update stats
          $('#tc-stat-orig').textContent = getFormatLabel(format);
          $('#tc-stat-comp').textContent = value.substring(0, 20) + (value.length > 20 ? '...' : '');
          $('#tc-stat-saved').textContent = totalGenerated;
          $('#tc-bg-stat-chars').textContent = value.length;
          $('#tc-bg-stat-total').textContent = totalGenerated;

          TCTP.switchToResultTab();
          TCTP.toast('Barcode generated!','success');

          // SVG download
          $('#tc-bg-dl-svg').onclick = function(){
            const svgData = new XMLSerializer().serializeToString(svg);
            const blob = new Blob([svgData], {type: 'image/svg+xml'});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'barcode-' + value + '.svg';
            a.click();
            URL.revokeObjectURL(url);
          };

          // PNG download
          $('#tc-bg-dl-png').onclick = function(){
            const svgData = new XMLSerializer().serializeToString(svg);
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const img = new Image();
            img.onload = function(){
              canvas.width = img.width * 2;
              canvas.height = img.height * 2;
              ctx.fillStyle = '#ffffff';
              ctx.fillRect(0, 0, canvas.width, canvas.height);
              ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
              canvas.toBlob(function(blob){
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'barcode-' + value + '.png';
                a.click();
                URL.revokeObjectURL(url);
              });
            };
            img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
          };

          // Copy image
          $('#tc-bg-copy-img').onclick = function(){
            const svgData = new XMLSerializer().serializeToString(svg);
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const img = new Image();
            img.onload = function(){
              canvas.width = img.width;
              canvas.height = img.height;
              ctx.fillStyle = '#ffffff';
              ctx.fillRect(0, 0, canvas.width, canvas.height);
              ctx.drawImage(img, 0, 0);
              canvas.toBlob(function(blob){
                navigator.clipboard.write([new ClipboardItem({'image/png': blob})]);
                TCTP.toast('Image copied to clipboard!','success');
              });
            };
            img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
          };

        } catch(e){
          TCTP.toast('Error: ' + e.message, 'error');
          output.innerHTML = '<div class="tc-bg-empty" style="color:#ef4444">Error: ' + e.message + '</div>';
        }
      }, 100);
    });
  });
})();
