/**
 * Upscale Image
 * @package TextCraft_Tools_Pro
 */
(function () {
  if (!window.TCTP) return;

  const drop = document.getElementById('tc-up-drop');
  const fileRow = document.getElementById('tc-up-file');
  const startBtn = document.getElementById('tc-up-start');
  const downloadBtn = document.getElementById('tc-up-download');
  const canvas = document.getElementById('tc-up-canvas');
  const info = document.getElementById('tc-up-info');
  if (!drop || !canvas) return;

  const ctx = canvas.getContext('2d');
  let uploadedFile = null;
  let upscaledBlob = null;

  const modeCards = document.querySelectorAll('.tc-up-modes .tc-rsz-mode-card');
  const interpCards = document.querySelectorAll('.tc-up-interp-modes .tc-rsz-mode-card');

  let factor = 2;
  let interpolation = 'high';

  modeCards.forEach(card => {
    card.addEventListener('click', () => {
      modeCards.forEach(c => c.classList.remove('sel'));
      card.classList.add('sel');
      factor = parseInt(card.dataset.val, 10);
    });
  });

  interpCards.forEach(card => {
    card.addEventListener('click', () => {
      interpCards.forEach(c => c.classList.remove('sel'));
      card.classList.add('sel');
      interpolation = card.dataset.val;
    });
  });

  TCTP.initDropZone('tc-up-drop', 'tc-up-drop-input', function(file) {
    uploadedFile = file;
    TCTP.showFileRow('tc-up-file', file.name, TCTP.formatSize(file.size));
  }, 'image/*');

  if (fileRow) {
    fileRow.querySelector('.tc-x').addEventListener('click', function() {
      uploadedFile = null;
      TCTP.hideFileRow('tc-up-file');
      if (document.getElementById('tc-preview-orig')) document.getElementById('tc-preview-orig').innerHTML = '';
      if (document.getElementById('tc-preview-result')) document.getElementById('tc-preview-result').innerHTML = '';
      canvas.style.display = 'none';
      if (info) info.style.display = 'none';
    });
  }

  startBtn.addEventListener('click', function() {
    if (!uploadedFile) {
      TCTP.toast('Please upload an image first', 'error');
      return;
    }

    TCTP.showProgress('tc-up-bar', 0, 'Loading image...');
    const reader = new FileReader();
    reader.onload = function(e) {
      const img = new Image();
      img.onload = function() {
        TCTP.showProgress('tc-up-bar', 30, 'Processing...');

        const newW = Math.round(img.width * factor);
        const newH = Math.round(img.height * factor);

        canvas.width = newW;
        canvas.height = newH;

        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = interpolation === 'high' ? 'high' : 'medium';

        ctx.drawImage(img, 0, 0, newW, newH);

        TCTP.showProgress('tc-up-bar', 70, 'Encoding...');

        canvas.toBlob(function(blob) {
          upscaledBlob = blob;
          TCTP.showProgress('tc-up-bar', 100, 'Done!');

          document.getElementById('tc-up-orig-size').textContent = TCTP.formatSize(uploadedFile.size);
          document.getElementById('tc-up-new-size').textContent = TCTP.formatSize(blob.size);
          document.getElementById('tc-up-dimensions').textContent = newW + ' × ' + newH;
          if (info) info.style.display = 'block';

          TCTP.updateResultPanel(
            TCTP.formatSize(uploadedFile.size),
            TCTP.formatSize(blob.size),
            '',
            'Done'
          );
          TCTP.switchToResultTab();

          setTimeout(() => TCTP.hideProgress('tc-up-bar'), 2000);
        }, 'image/png', 1.0);
      };
      img.src = e.target.result;
    };
    reader.readAsDataURL(uploadedFile);
  });

  if (downloadBtn) {
    downloadBtn.addEventListener('click', function() {
      if (!upscaledBlob) {
        TCTP.toast('Upscale an image first', 'error');
        return;
      }
      const a = document.createElement('a');
      a.href = URL.createObjectURL(upscaledBlob);
      a.download = 'upscaled-' + factor + 'x.png';
      a.click();
      URL.revokeObjectURL(a.href);
    });
  }
})();
