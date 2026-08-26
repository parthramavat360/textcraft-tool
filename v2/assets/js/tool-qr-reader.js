/**
 * QR Code Reader — uses html5-qrcode library (loaded from CDN)
 * Camera-based or file upload QR scanning, 100% client-side
 */
(function(){
  if(!window.TCTP) return;
  const $ = s => document.querySelector(s);

  let html5QrCode = null;
  let scanning = false;
  let scanCount = 0;
  let history = [];

  function getMethod(){ return document.querySelector('.tc-modes[data-group="qr-method"] .sel')?.dataset.val || 'camera'; }

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

  function addHistory(text, type){
    history.unshift({ text, type, time: new Date().toLocaleTimeString() });
    if(history.length > 20) history.pop();
    renderHistory();
  }

  function renderHistory(){
    const el = $('#tc-qr-history');
    if(!el) return;
    if(history.length === 0){
      el.innerHTML = '<p style="color:var(--muted);font-size:14px;">No scans yet.</p>';
      return;
    }
    el.innerHTML = history.map((h, i) =>
      '<div class="tc-qr-history-item">' +
        '<span class="tc-qr-history-num">#' + (history.length - i) + '</span>' +
        '<span class="tc-qr-history-text">' + h.text.replace(/</g, '&lt;').substring(0, 100) + (h.text.length > 100 ? '...' : '') + '</span>' +
        '<span class="tc-qr-history-type">' + h.type + '</span>' +
        '<span class="tc-qr-history-time">' + h.time + '</span>' +
      '</div>'
    ).join('');
  }

  function showResult(text){
    scanCount++;
    const isUrl = /^https?:\/\//i.test(text);
    const type = isUrl ? 'URL' : (text.includes('@') ? 'Email' : (text.includes('WIFI:') ? 'WiFi' : 'Text'));
    const decoded = $('#tc-qr-decoded');
    const actions = $('#tc-qr-actions');

    if(decoded){
      decoded.innerHTML = isUrl
        ? '<a href="' + text.replace(/"/g, '&quot;') + '" target="_blank" rel="noopener" class="tc-qr-link">' + text.replace(/</g, '&lt;') + '</a>'
        : '<span class="tc-qr-text">' + text.replace(/</g, '&lt;') + '</span>';
    }
    if(actions) actions.style.display = 'flex';

    // Stats
    $('#tc-qr-type').textContent = type;
    $('#tc-qr-length').textContent = text.length + ' chars';
    $('#tc-qr-total').textContent = scanCount;
    $('#tc-qr-status').textContent = type + ' detected';
    $('#tc-qr-count').textContent = scanCount;

    TCTP.switchToResultTab();
    addHistory(text, type);
    TCTP.toast('QR code detected! (' + type + ')', 'success');
  }

  document.addEventListener('DOMContentLoaded', function(){
    const startBtn = $('#tc-qr-start');
    const stopBtn = $('#tc-qr-stop');
    const fileInput = $('#tc-qr-drop-input');
    const dropZone = $('#tc-qr-drop');
    const copyBtn = $('#tc-qr-copy');
    const openBtn = $('#tc-qr-open');

    document.querySelectorAll('.tc-modes[data-group="qr-method"] .tc-btn').forEach(function(btn) {
      btn.addEventListener('click', function(){
        this.closest('.tc-modes').querySelectorAll('.tc-btn').forEach(function(b){ b.classList.remove('sel'); });
        this.classList.add('sel');
        const method = getMethod();
        $('#tc-qr-camera-area').style.display = method === 'camera' ? 'block' : 'none';
        $('#tc-qr-upload-area').style.display = method === 'upload' ? 'block' : 'none';
      });
    });

    if(startBtn){
      startBtn.addEventListener('click', async function(){
        try {
          await loadScript('https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js');
          if(!window.Html5Qrcode){ TCTP.toast('Failed to load QR library','error'); return; }
          if(!html5QrCode) html5QrCode = new Html5Qrcode('tc-qr-reader');
          await html5QrCode.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            (text) => showResult(text),
            function(){}
          );
          scanning = true;
          TCTP.toast('Camera started — point at a QR code','success');
        } catch(e){
          TCTP.toast('Camera error: ' + e.message, 'error');
        }
      });
    }

    if(stopBtn){
      stopBtn.addEventListener('click', async function(){
        if(html5QrCode && scanning){
          try { await html5QrCode.stop(); scanning = false; TCTP.toast('Camera stopped','info'); } catch(e){}
        }
      });
    }

    if(fileInput){
      fileInput.addEventListener('change', async function(e){
        const file = e.target.files[0];
        if(!file) return;
        TCTP.showProgress('tc-qr-progress', 'Scanning image...', 50);
        try {
          await loadScript('https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js');
          if(!window.Html5Qrcode){ TCTP.toast('Failed to load QR library','error'); return; }
          const tempReader = new Html5Qrcode('tc-qr-reader-temp');
          const decodedText = await tempReader.scanFile(file, true);
          TCTP.setProgress('tc-qr-progress', 100, 'Done!');
          showResult(decodedText);
        } catch(err){
          TCTP.setProgress('tc-qr-progress', 100, 'No QR found');
          TCTP.toast('No QR code found in image','warning');
        }
        this.value = '';
      });
    }

    if(dropZone){
      dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('tc-drop-hover'); });
      dropZone.addEventListener('dragleave', () => dropZone.classList.remove('tc-drop-hover'));
      dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('tc-drop-hover');
        const file = e.dataTransfer.files[0];
        if(file && file.type.startsWith('image/')){
          fileInput.files = e.dataTransfer.files;
          fileInput.dispatchEvent(new Event('change'));
        }
      });
      dropZone.addEventListener('click', () => fileInput.click());
    }

    if(copyBtn){
      copyBtn.addEventListener('click', function(){
        const decoded = $('#tc-qr-decoded');
        if(decoded){
          const text = decoded.textContent || decoded.innerText;
          navigator.clipboard.writeText(text);
          this.innerHTML = '<span class="tc-btn-text">Copied!</span>';
          setTimeout(() => this.innerHTML = '<span class="tc-btn-text">Copy Text</span>', 1500);
        }
      });
    }

    if(openBtn){
      openBtn.addEventListener('click', function(){
        const decoded = $('#tc-qr-decoded');
        if(decoded){
          const link = decoded.querySelector('a');
          if(link) window.open(link.href, '_blank');
          else { this.style.display = 'none'; }
        }
      });
    }
  });
})();
