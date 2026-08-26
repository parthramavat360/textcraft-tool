/**
 * Text to Handwriting — uses custom canvas rendering
 * Renders text with handwriting-style fonts, 100% client-side
 */
(function(){
  if(!window.TCTP) return;
  const $ = s => document.querySelector(s);
  const $$ = s => document.querySelectorAll(s);

  const FONTS = {
    cursive: '"Caveat", "Dancing Script", cursive',
    print: '"Patrick Hand", "Kalam", sans-serif',
    messy: '"Rock Salt", "Satisfy", cursive',
    neat: '"Architects Daughter", "Indie Flower", sans-serif',
    elegant: '"Great Vibes", "Alex Brush", cursive'
  };

  const FONT_URLS = [
    'https://fonts.googleapis.com/css2?family=Caveat:wght@400;700&display=swap',
    'https://fonts.googleapis.com/css2?family=Patrick+Hand&display=swap',
    'https://fonts.googleapis.com/css2?family=Rock+Salt&display=swap',
    'https://fonts.googleapis.com/css2?family=Architects+Daughter&display=swap',
    'https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap',
    'https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&display=swap',
    'https://fonts.googleapis.com/css2?family=Kalam:wght@300;400;700&display=swap',
    'https://fonts.googleapis.com/css2?family=Satisfy&display=swap',
    'https://fonts.googleapis.com/css2?family=Indie+Flower&display=swap',
    'https://fonts.googleapis.com/css2?family=Alex+Brush&display=swap'
  ];

  let fontsLoaded = false;

  function loadFonts(){
    if(fontsLoaded) return Promise.resolve();
    return new Promise(resolve => {
      let loaded = 0;
      FONT_URLS.forEach(url => {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = url;
        link.onload = () => { if(++loaded >= FONT_URLS.length) fontsLoaded = true; resolve(); };
        link.onerror = () => { if(++loaded >= FONT_URLS.length) fontsLoaded = true; resolve(); };
        document.head.appendChild(link);
      });
      setTimeout(() => { fontsLoaded = true; resolve(); }, 3000);
    });
  }

  function getStyle(){ return document.querySelector('.tc-modes[data-group="th-style"] .sel')?.dataset.val || 'cursive'; }
  function getPenColor(){ return document.querySelector('.tc-th-color.active')?.dataset.color || '#1a1a2e'; }
  function getPaper(){ return document.querySelector('.tc-modes[data-group="th-paper"] .sel')?.dataset.val || 'lined'; }
  function getFontSize(){ return parseInt($('#tc-th-size')?.value || '18'); }

  function getStyleLabel(s){ return {cursive:'Cursive',print:'Print',messy:'Messy',neat:'Neat',elegant:'Elegant'}[s] || s; }
  function getPaperLabel(p){ return {lined:'Lined',blank:'Blank',grid:'Grid',dotted:'Dotted'}[p] || p; }
  function getColorName(c){ return {'#1a1a2e':'Black','#0000ff':'Blue','#006400':'Green','#8b0000':'Red','#4b0082':'Purple'}[c] || c; }

  function drawPaper(ctx, w, h, paper){
    ctx.fillStyle = '#fffdf7';
    ctx.fillRect(0, 0, w, h);
    ctx.strokeStyle = '#ddd8c4';
    ctx.lineWidth = 1;

    if(paper === 'lined'){
      for(let y = 40; y < h; y += 32){
        ctx.beginPath(); ctx.moveTo(30, y); ctx.lineTo(w - 20, y); ctx.stroke();
      }
      ctx.strokeStyle = '#e8a0a0'; ctx.lineWidth = 1.5;
      ctx.beginPath(); ctx.moveTo(70, 10); ctx.lineTo(70, h - 10); ctx.stroke();
    } else if(paper === 'grid'){
      for(let x = 30; x < w; x += 25){ ctx.beginPath(); ctx.moveTo(x, 10); ctx.lineTo(x, h - 10); ctx.stroke(); }
      for(let y = 30; y < h; y += 25){ ctx.beginPath(); ctx.moveTo(30, y); ctx.lineTo(w - 20, y); ctx.stroke(); }
    } else if(paper === 'dotted'){
      ctx.fillStyle = '#ccc';
      for(let x = 40; x < w; x += 25){ for(let y = 40; y < h; y += 25){ ctx.beginPath(); ctx.arc(x, y, 1, 0, Math.PI * 2); ctx.fill(); } }
    }
  }

  function wrapText(ctx, text, maxWidth, fontSize){
    const paragraphs = text.split('\n');
    const lines = [];
    paragraphs.forEach(para => {
      if(para.trim() === ''){ lines.push(''); return; }
      const words = para.split(' ');
      let currentLine = '';
      words.forEach(word => {
        const testLine = currentLine ? currentLine + ' ' + word : word;
        if(ctx.measureText(testLine).width > maxWidth && currentLine){ lines.push(currentLine); currentLine = word; }
        else currentLine = testLine;
      });
      if(currentLine) lines.push(currentLine);
    });
    return lines;
  }

  function renderHandwriting(text){
    const style = getStyle();
    const color = getPenColor();
    const paper = getPaper();
    const fontSize = getFontSize();
    const lineSpacing = fontSize * 1.8;
    const fontFamily = FONTS[style] || FONTS.cursive;

    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    canvas.width = 800;
    ctx.font = fontSize + 'px ' + fontFamily;

    const lines = wrapText(ctx, text, 700, fontSize);
    const startY = paper === 'lined' ? 40 : 30;
    canvas.height = Math.max(startY + lines.length * lineSpacing + 50, 400);

    drawPaper(ctx, 800, canvas.height, paper);
    ctx.font = fontSize + 'px ' + fontFamily;
    ctx.fillStyle = color;
    ctx.textBaseline = 'top';

    lines.forEach((line, i) => {
      const y = startY + i * lineSpacing;
      ctx.save();
      ctx.translate(Math.random() * 4 - 2, Math.random() * 3 - 1.5);
      ctx.fillText(line, paper === 'lined' ? 80 : 40, y);
      ctx.restore();
    });

    return canvas;
  }

  function updateStats(text){
    const chars = text.length;
    const words = text.trim() ? text.trim().split(/\s+/).length : 0;
    const lines = text.split('\n').length;
    $('#tc-th-chars').textContent = chars;
    $('#tc-th-words').textContent = words;
    $('#tc-th-lines').textContent = lines;
  }

  document.addEventListener('DOMContentLoaded', function(){
    const btn = $('#tc-th-generate');
    const textarea = $('#tc-th-text');
    if(!btn || !textarea) return;

    // Live stats
    textarea.addEventListener('input', () => updateStats(textarea.value));

    document.querySelectorAll('.tc-modes[data-group="th-style"] .tc-btn').forEach(function(btn) {
      btn.addEventListener('click', function(){
        this.closest('.tc-modes').querySelectorAll('.tc-btn').forEach(function(b){ b.classList.remove('sel'); });
        this.classList.add('sel');
      });
    });

    document.querySelectorAll('.tc-modes[data-group="th-paper"] .tc-btn').forEach(function(btn) {
      btn.addEventListener('click', function(){
        this.closest('.tc-modes').querySelectorAll('.tc-btn').forEach(function(b){ b.classList.remove('sel'); });
        this.classList.add('sel');
      });
    });

    // Pen color switching
    $$('.tc-th-color').forEach(c => c.addEventListener('click', function(){
      $$('.tc-th-color').forEach(x => x.classList.remove('active'));
      this.classList.add('active');
    }));

    $('#tc-th-size')?.addEventListener('input', function(){ $('#tc-th-size-val').textContent = this.value; });

    btn.addEventListener('click', async function(){
      const text = textarea.value.trim();
      if(!text){ TCTP.toast('Please enter some text','warning'); return; }

      TCTP.showProgress('tc-th-progress', 'Loading fonts...', 20);
      await loadFonts();
      TCTP.setProgress('tc-th-progress', 60, 'Rendering handwriting...');

      setTimeout(() => {
        TCTP.setProgress('tc-th-progress', 80, 'Drawing...');

        const canvas = renderHandwriting(text);
        const output = $('#tc-th-output');
        if(!output) return;

        output.innerHTML = '';
        output.appendChild(canvas);

        // Update stats in result panel
        const style = getStyle();
        const color = getPenColor();
        const paper = getPaper();
        $('#tc-stat-orig').textContent = getStyleLabel(style);
        $('#tc-stat-comp').textContent = getColorName(color);
        $('#tc-stat-saved').textContent = getPaperLabel(paper);

        TCTP.setProgress('tc-th-progress', 100, 'Done!');
        TCTP.switchToResultTab();
        TCTP.toast('Handwriting generated!','success');

        // PNG download
        $('#tc-th-dl-png').onclick = function(){
          canvas.toBlob(function(blob){
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'handwriting.png';
            a.click();
            URL.revokeObjectURL(url);
          });
        };

        // Copy image
        $('#tc-th-copy-img').onclick = function(){
          canvas.toBlob(function(blob){
            navigator.clipboard.write([new ClipboardItem({'image/png': blob})]);
            TCTP.toast('Image copied!','success');
          });
        };

      }, 150);
    });
  });
})();
