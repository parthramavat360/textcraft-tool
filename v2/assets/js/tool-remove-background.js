(function () {
    'use strict';
    var drop = document.getElementById('tc-rmbg-drop');
    if (!drop) return;
    var file = null;
    var resultBlob = null;
    var removeBgModule = null;
    var hqCheck = document.getElementById('tc-rmbg-highquality');
    var webpCheck = document.getElementById('tc-rmbg-webp');
    function setStat(id, val) { var el = document.getElementById(id); if (el) el.textContent = val; }
    async function loadLib() {
        if (removeBgModule) return;
        removeBgModule = await import('https://cdn.jsdelivr.net/npm/@imgly/background-removal@1.5.8/dist/index.mjs');
    }
    TCTP.initDropZone('tc-rmbg-drop', 'tc-rmbg-drop-input', function (f) {
        if (!f.type.match(/image\//)) { TCTP.toast('Please select an image file.', '\u26A0\uFE0F'); return; }
        file = f; resultBlob = null;
        TCTP.showFileRow('tc-rmbg-file', f);
        var dl = document.getElementById('tc-rmbg-download'); if (dl) dl.style.display = 'none';
        setStat('tc-rmbg-stat-orig', TCTP.formatSize(f.size));
        setStat('tc-rmbg-stat-comp', '-');
        setStat('tc-rmbg-stat-fmt', webpCheck && webpCheck.checked ? 'WebP' : 'PNG');
        var reader = new FileReader();
        reader.onload = function (ev) {
            TCTP.showOriginalPreview(ev.target.result);
            TCTP.switchToOriginalTab();
        };
        reader.readAsDataURL(f);
    }, 'image/*');
    var removeFileBtn = document.querySelector('#tc-rmbg-file .tc-x');
    if (removeFileBtn) removeFileBtn.addEventListener('click', function () {
        file = null; resultBlob = null; TCTP.hideFileRow('tc-rmbg-file');
        setStat('tc-rmbg-stat-orig', '-'); setStat('tc-rmbg-stat-comp', '-'); setStat('tc-rmbg-stat-fmt', 'PNG');
    });
    if (webpCheck) webpCheck.addEventListener('change', function () {
        setStat('tc-rmbg-stat-fmt', webpCheck.checked ? 'WebP' : 'PNG');
    });
    var removeBtn = document.getElementById('tc-rmbg-remove');
    if (removeBtn) removeBtn.addEventListener('click', async function () {
        if (!file) { TCTP.toast('Please select an image first.', '\u26A0\uFE0F'); return; }
        try {
            await loadLib();
        } catch (e) {
            TCTP.toast('Failed to load AI library.', '\u274C');
            return;
        }
        doRemove();
    });
    function doRemove() {
        var fn = removeBgModule && removeBgModule.removeBackground;
        if (typeof fn !== 'function') { TCTP.toast('Background-removal library unavailable.', '\u274C'); return; }
        TCTP.showProgress('tc-rmbg-progress');
        TCTP.setProgress('tc-rmbg-progress', 5, 'Loading AI model...');
        var options = {
            progress: function (key, current, total) {
                var pct = Math.min(95, Math.round((current / total) * 90) + 5);
                var msg = key === 'compute:inference' ? 'Analyzing image...' : 'Loading model...';
                TCTP.setProgress('tc-rmbg-progress', pct, msg);
            }
        };
        if (hqCheck && hqCheck.checked) options.model = 'isnet';
        fn(file, options).then(function (blob) {
            var convertToWebp = webpCheck && webpCheck.checked;
            if (convertToWebp) {
                var url = URL.createObjectURL(blob);
                var img = new Image();
                img.onload = function () {
                    var canvas = document.createElement('canvas');
                    canvas.width = img.naturalWidth;
                    canvas.height = img.naturalHeight;
                    canvas.getContext('2d').drawImage(img, 0, 0);
                    URL.revokeObjectURL(url);
                    canvas.toBlob(function (webpBlob) {
                        finishResult(webpBlob || blob, 'WebP');
                    }, 'image/webp', 0.9);
                };
                img.onerror = function () {
                    URL.revokeObjectURL(url);
                    finishResult(blob, 'PNG');
                };
                img.src = url;
            } else {
                finishResult(blob, 'PNG');
            }
        }).catch(function (err) {
            TCTP.hideProgress('tc-rmbg-progress');
            TCTP.toast('Failed: ' + err.message, '\u274C');
        });
    }
    function finishResult(blob, fmt) {
        resultBlob = blob;
        TCTP.setProgress('tc-rmbg-progress', 100, 'Done!');
        TCTP.hideProgress('tc-rmbg-progress');
        setStat('tc-rmbg-stat-comp', TCTP.formatSize(blob.size));
        setStat('tc-rmbg-stat-fmt', fmt);
        TCTP.updateResultPanel(TCTP.formatSize(file.size), TCTP.formatSize(blob.size), (file.size > blob.size ? ((1 - blob.size / file.size) * 100).toFixed(1) + '%' : '0%'), 'Done');
        TCTP.showResultPreview(URL.createObjectURL(blob));
        TCTP.switchToResultTab();
        TCTP.toast('Background removed!');
        var dl = document.getElementById('tc-rmbg-download'); if (dl) dl.style.display = '';
    }
    var downloadBtn = document.getElementById('tc-rmbg-download');
    if (downloadBtn) downloadBtn.addEventListener('click', function () {
        if (!resultBlob) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
        var ext = webpCheck && webpCheck.checked ? '.webp' : '.png';
        var nameInput = document.getElementById('tc-rmbg-name');
        var base = (nameInput && nameInput.value.trim()) ? nameInput.value.trim().replace(/\.[^.]+$/, '') : (file ? file.name.replace(/\.[^.]+$/, '') : 'image');
        TCTP.downloadBlob(resultBlob, base + ext);
    });

    var clearBtn = document.getElementById('tc-rmbg-clear');
    if (clearBtn) clearBtn.addEventListener('click', function () {
        file = null; resultBlob = null;
        TCTP.hideFileRow('tc-rmbg-file');
        setStat('tc-rmbg-stat-orig', '-'); setStat('tc-rmbg-stat-comp', '-'); setStat('tc-rmbg-stat-fmt', 'PNG');
        var dl = document.getElementById('tc-rmbg-download'); if (dl) dl.style.display = 'none';
        var nameInput = document.getElementById('tc-rmbg-name'); if (nameInput) nameInput.value = '';
        var origP = document.getElementById('tc-preview-orig');
        if (origP) origP.innerHTML = '<span style="color:var(--muted);font-size:13px">Original preview will appear here</span>';
        var resP = document.getElementById('tc-preview-result');
        if (resP) resP.innerHTML = '<span style="color:var(--muted);font-size:13px">Result preview will appear here</span>';
        TCTP.switchToOriginalTab();
    });
})();
