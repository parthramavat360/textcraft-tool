/**
 * Photo Editor (Basic)
 * @package TextCraft_Tools_Pro
 */
(function () {
  if (!window.TCTP) return;

  const drop = document.getElementById('tc-pe-drop');
  const fileRow = document.getElementById('tc-pe-file');
  const canvas = document.getElementById('tc-pe-canvas');
  const previewWrap = document.getElementById('tc-pe-preview-wrap');
  const applyBtn = document.getElementById('tc-pe-apply');
  const downloadBtn = document.getElementById('tc-pe-download');
  if (!drop || !canvas) return;

  const ctx = canvas.getContext('2d');
  let uploadedImg = null;
  let exportBlob = null;

  /* Sliders */
  const sliders = {
    brightness: document.getElementById('tc-pe-brightness'),
    contrast:   document.getElementById('tc-pe-contrast'),
    saturate:   document.getElementById('tc-pe-saturate'),
    blur:       document.getElementById('tc-pe-blur'),
    hue:        document.getElementById('tc-pe-hue'),
  };
  const valLabels = {
    brightness: document.getElementById('tc-pe-brightness-val'),
    contrast:   document.getElementById('tc-pe-contrast-val'),
    saturate:   document.getElementById('tc-pe-saturate-val'),
    blur:       document.getElementById('tc-pe-blur-val'),
    hue:        document.getElementById('tc-pe-hue-val'),
  };

  /* Filters state */
  let filters = { brightness: 100, contrast: 100, saturate: 100, blur: 0, hue: 0 };
  let extraFilters = []; // grayscale, sepia, invert
  let rotation = 0;
  let flipH = false;
  let flipV = false;

  function updateLabels() {
    if (valLabels.brightness) valLabels.brightness.textContent = filters.brightness + '%';
    if (valLabels.contrast) valLabels.contrast.textContent = filters.contrast + '%';
    if (valLabels.saturate) valLabels.saturate.textContent = filters.saturate + '%';
    if (valLabels.blur) valLabels.blur.textContent = filters.blur + 'px';
    if (valLabels.hue) valLabels.hue.textContent = filters.hue + '°';
  }

  function buildFilterString() {
    let s = 'brightness(' + filters.brightness + '%) contrast(' + filters.contrast + '%) saturate(' + filters.saturate + '%) blur(' + filters.blur + 'px) hue-rotate(' + filters.hue + 'deg)';
    extraFilters.forEach(f => s += ' ' + f);
    return s;
  }

  function renderPreview() {
    if (!uploadedImg || !previewWrap) return;
    previewWrap.style.display = 'block';

    let w = uploadedImg.naturalWidth;
    let h = uploadedImg.naturalHeight;
    if (rotation === 90 || rotation === 270) { const t = w; w = h; h = t; }

    canvas.width = w;
    canvas.height = h;
    ctx.clearRect(0, 0, w, h);

    ctx.save();
    ctx.filter = buildFilterString();
    ctx.translate(w / 2, h / 2);
    if (flipH) ctx.scale(-1, 1);
    if (flipV) ctx.scale(1, -1);
    ctx.rotate((rotation * Math.PI) / 180);
    ctx.drawImage(uploadedImg, -uploadedImg.naturalWidth / 2, -uploadedImg.naturalHeight / 2, uploadedImg.naturalWidth, uploadedImg.naturalHeight);
    ctx.restore();

    /* Text overlay */
    const text = document.getElementById('tc-pe-text');
    const textColor = document.getElementById('tc-pe-text-color');
    const textSize = document.getElementById('tc-pe-text-size');
    const textPos = document.getElementById('tc-pe-text-pos');

    if (text && text.value.trim()) {
      ctx.save();
      const size = parseInt(textSize.value, 10) || 32;
      ctx.font = 'bold ' + size + 'px "Space Grotesk", "Arial Black", sans-serif';
      ctx.textAlign = 'center';
      ctx.fillStyle = textColor ? textColor.value : '#ffffff';
      ctx.strokeStyle = 'rgba(0,0,0,0.7)';
      ctx.lineWidth = Math.max(2, size / 12);
      const pos = textPos ? textPos.value : 'center';
      let y = pos === 'top' ? size + 20 : pos === 'bottom' ? h - 20 : h / 2;
      ctx.strokeText(text.value, w / 2, y);
      ctx.fillText(text.value, w / 2, y);
      ctx.restore();
    }
  }

  /* Slider events */
  Object.keys(sliders).forEach(key => {
    if (!sliders[key]) return;
    sliders[key].addEventListener('input', function() {
      if (key === 'blur') {
        filters[key] = parseInt(this.value, 10);
      } else {
        filters[key] = parseInt(this.value, 10);
      }
      updateLabels();
      renderPreview();
    });
  });

  /* Transform buttons */
  document.querySelectorAll('.tc-pe-transform-modes .tc-rsz-mode-card').forEach(card => {
    card.addEventListener('click', () => {
      const val = card.dataset.val;
      if (val === 'rotate-left') { rotation = (rotation + 270) % 360; }
      else if (val === 'rotate-right') { rotation = (rotation + 90) % 360; }
      else if (val === 'flip-h') { flipH = !flipH; }
      else if (val === 'flip-v') { flipV = !flipV; }
      else if (val === 'grayscale') {
        const has = extraFilters.includes('grayscale(1)');
        extraFilters = extraFilters.filter(f => !f.startsWith('grayscale'));
        if (!has) extraFilters.push('grayscale(1)');
      }
      else if (val === 'sepia') {
        const has = extraFilters.includes('sepia(1)');
        extraFilters = extraFilters.filter(f => !f.startsWith('sepia'));
        if (!has) extraFilters.push('sepia(1)');
      }
      else if (val === 'invert') {
        const has = extraFilters.includes('invert(1)');
        extraFilters = extraFilters.filter(f => !f.startsWith('invert'));
        if (!has) extraFilters.push('invert(1)');
      }
      else if (val === 'reset') {
        rotation = 0; flipH = false; flipV = false;
        extraFilters = [];
        filters = { brightness: 100, contrast: 100, saturate: 100, blur: 0, hue: 0 };
        Object.keys(sliders).forEach(k => { if (sliders[k]) sliders[k].value = filters[k]; });
        updateLabels();
      }
      renderPreview();
    });
  });

  /* File upload */
  TCTP.initDropZone('tc-pe-drop', 'tc-pe-drop-input', function(file) {
    TCTP.showFileRow('tc-pe-file', file.name, TCTP.formatSize(file.size));
    const reader = new FileReader();
    reader.onload = function(e) {
      const img = new Image();
      img.onload = function() {
        uploadedImg = img;
        renderPreview();
      };
      img.src = e.target.result;
    };
    reader.readAsDataURL(file);
  }, 'image/*');

  if (fileRow) {
    fileRow.querySelector('.tc-x').addEventListener('click', function() {
      uploadedImg = null;
      TCTP.hideFileRow('tc-pe-file');
      if (previewWrap) previewWrap.style.display = 'none';
    });
  }

  /* Apply / Export */
  if (applyBtn) {
    applyBtn.addEventListener('click', function() {
      if (!uploadedImg) { TCTP.toast('Upload a photo first', 'error'); return; }
      renderPreview();
      canvas.toBlob(function(blob) {
        exportBlob = blob;
        TCTP.updateResultPanel('', TCTP.formatSize(blob.size), '', 'Done');
        TCTP.switchToResultTab();
        TCTP.toast('Photo processed! Click Download to save.');
      }, 'image/png', 1.0);
    });
  }

  if (downloadBtn) {
    downloadBtn.addEventListener('click', function() {
      if (!exportBlob) { TCTP.toast('Apply edits first', 'error'); return; }
      const a = document.createElement('a');
      a.href = URL.createObjectURL(exportBlob);
      a.download = 'edited-photo.png';
      a.click();
      URL.revokeObjectURL(a.href);
    });
  }
})();
