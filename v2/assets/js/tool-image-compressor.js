/**
 * Image Compressor (Unified) — Tool JS
 * 100% client-side image compression using canvas API.
 * Batch support for multiple images.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var dropEl = document.getElementById('tc-comp-drop');
    if (!dropEl) return;

    var files = [];
    var compressedResults = [];
    var fileList = document.getElementById('tc-comp-file-list');
    var filesContainer = document.getElementById('tc-comp-files');
    var countEl = document.getElementById('tc-comp-count');
    var applyBtn = document.getElementById('tc-comp-apply');
    var dlBtn = document.getElementById('tc-comp-download');

    // ── Drop zone ──────────────────────────────────────────────

    TCTP.initDropZone('tc-comp-drop', 'tc-comp-drop-input', function (f) {
        if (!f.type.match(/^image\/(jpeg|png|webp|gif)$/)) {
            TCTP.toast('Please select JPG, PNG, WebP, or GIF images.', '\u26A0\uFE0F');
            return;
        }
        files.push(f);
        compressedResults = [];
        if (dlBtn) dlBtn.style.display = 'none';
        updateFileList();
        TCTP.toast('Added: ' + f.name, '\u2705');
    }, 'image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif');

    // ── File list ──────────────────────────────────────────────

    function updateFileList() {
        if (!filesContainer) return;
        if (files.length === 0) {
            if (fileList) fileList.style.display = 'none';
            return;
        }
        if (fileList) fileList.style.display = '';
        if (countEl) countEl.textContent = files.length + ' file' + (files.length !== 1 ? 's' : '');

        filesContainer.innerHTML = '';
        files.forEach(function (f, i) {
            var row = document.createElement('div');
            row.className = 'tc-comp-file-row';

            var info = document.createElement('div');
            info.className = 'tc-comp-file-info';
            info.innerHTML = '<span class="tc-comp-file-name">' + f.name + '</span>' +
                '<span class="tc-comp-file-size">' + TCTP.formatSize(f.size) + '</span>';

            var removeBtn = document.createElement('button');
            removeBtn.className = 'tc-comp-file-remove';
            removeBtn.type = 'button';
            removeBtn.textContent = '\u2715';
            removeBtn.addEventListener('click', function () {
                files.splice(i, 1);
                compressedResults.splice(i, 1);
                updateFileList();
            });

            row.appendChild(info);
            row.appendChild(removeBtn);
            filesContainer.appendChild(row);
        });

        // Show first image preview
        if (files.length > 0) {
            var prevOrig = document.getElementById('tc-comp-preview-orig');
            if (prevOrig) {
                prevOrig.innerHTML = '';
                var reader = new FileReader();
                reader.onload = function (e) {
                    var img = new Image();
                    img.src = e.target.result;
                    img.style.maxWidth = '100%';
                    img.style.maxHeight = '300px';
                    img.style.borderRadius = '8px';
                    img.style.objectFit = 'contain';
                    img.alt = 'Preview';
                    prevOrig.appendChild(img);
                };
                reader.readAsDataURL(files[0]);
            }
        }
    }

    // ── Quality slider ─────────────────────────────────────────

    var qualitySlider = document.getElementById('tc-comp-quality');
    var qualityVal = document.getElementById('tc-comp-quality-val');
    if (qualitySlider) {
        qualitySlider.addEventListener('input', function () {
            if (qualityVal) qualityVal.textContent = qualitySlider.value + '%';
        });
    }

    var maxWSlider = document.getElementById('tc-comp-maxw');
    var maxWVal = document.getElementById('tc-comp-maxw-val');
    if (maxWSlider) {
        maxWSlider.addEventListener('input', function () {
            var v = parseInt(maxWSlider.value, 10);
            if (maxWVal) maxWVal.textContent = v === 0 ? 'Off' : v + 'px';
        });
    }

    // ── Compress ───────────────────────────────────────────────

    function compressImage(file, quality, maxWidth) {
        return new Promise(function (resolve) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = new Image();
                img.onload = function () {
                    var w = img.naturalWidth;
                    var h = img.naturalHeight;

                    if (maxWidth > 0 && w > maxWidth) {
                        h = Math.round(h * (maxWidth / w));
                        w = maxWidth;
                    }

                    var canvas = document.createElement('canvas');
                    canvas.width = w;
                    canvas.height = h;
                    var ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, w, h);

                    canvas.toBlob(function (blob) {
                        resolve({
                            name: file.name,
                            originalSize: file.size,
                            compressedSize: blob.size,
                            blob: blob,
                            width: w,
                            height: h,
                            type: file.type
                        });
                    }, file.type === 'image/png' ? 'image/png' : 'image/jpeg', quality / 100);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    if (applyBtn) {
        applyBtn.addEventListener('click', function () {
            if (files.length === 0) {
                TCTP.toast('Please add at least one image.', '\u26A0\uFE0F');
                return;
            }

            var quality = parseInt(qualitySlider ? qualitySlider.value : 80, 10);
            var maxWidth = parseInt(maxWSlider ? maxWSlider.value : 0, 10);

            TCTP.showProgress('tc-comp-progress', 10, 'Compressing ' + files.length + ' file' + (files.length !== 1 ? 's' : '') + '...');

            var promises = files.map(function (f, i) {
                return compressImage(f, quality, maxWidth).then(function (result) {
                    var pct = Math.round(10 + ((i + 1) / files.length) * 80);
                    TCTP.setProgress('tc-comp-progress', pct, 'Compressing ' + (i + 1) + '/' + files.length + '...');
                    return result;
                });
            });

            Promise.all(promises).then(function (results) {
                compressedResults = results;
                TCTP.setProgress('tc-comp-progress', 100, 'Done!');
                setTimeout(function () { TCTP.hideProgress('tc-comp-progress'); }, 600);

                var totalOrig = 0;
                var totalComp = 0;
                results.forEach(function (r) {
                    totalOrig += r.originalSize;
                    totalComp += r.compressedSize;
                });

                var savedPct = totalOrig > 0 ? ((1 - totalComp / totalOrig) * 100).toFixed(1) + '%' : '0%';

                var origStat = document.getElementById('tc-comp-stat-orig');
                if (origStat) origStat.textContent = TCTP.formatSize(totalOrig);
                var outStat = document.getElementById('tc-comp-stat-out');
                if (outStat) outStat.textContent = TCTP.formatSize(totalComp);
                var savedStat = document.getElementById('tc-comp-stat-saved');
                if (savedStat) savedStat.textContent = savedPct;

                TCTP.updateResultPanel(
                    TCTP.formatSize(totalOrig),
                    TCTP.formatSize(totalComp),
                    savedPct,
                    'Done'
                );
                TCTP.switchToResultTab();

                // Show result list
                var resultEl = document.getElementById('tc-comp-result');
                if (resultEl) {
                    resultEl.innerHTML = '';
                    results.forEach(function (r) {
                        var item = document.createElement('div');
                        item.className = 'tc-comp-result-item';

                        var saved = r.originalSize > 0 ? ((1 - r.compressedSize / r.originalSize) * 100).toFixed(1) : 0;
                        var isSmaller = r.compressedSize < r.originalSize;

                        item.innerHTML = '<div class="tc-comp-result-info">' +
                            '<span class="tc-comp-result-name">' + r.name + '</span>' +
                            '<span class="tc-comp-result-sizes">' + TCTP.formatSize(r.originalSize) + ' \u2192 ' + TCTP.formatSize(r.compressedSize) + '</span>' +
                            '<span class="tc-comp-result-saved' + (isSmaller ? '' : ' larger') + '">' +
                            (isSmaller ? '-' : '+') + Math.abs(saved) + '%</span>' +
                            '</div>';

                        var dlBtnItem = document.createElement('button');
                        dlBtnItem.className = 'tc-comp-result-dl';
                        dlBtnItem.type = 'button';
                        dlBtnItem.textContent = 'Download';
                        dlBtnItem.addEventListener('click', function () {
                            var a = document.createElement('a');
                            a.href = URL.createObjectURL(r.blob);
                            var ext = r.type === 'image/png' ? '.png' : '.jpg';
                            a.download = r.name.replace(/\.[^.]+$/, '') + '-compressed' + ext;
                            a.click();
                            URL.revokeObjectURL(a.href);
                        });

                        item.appendChild(dlBtnItem);
                        resultEl.appendChild(item);
                    });
                }

                if (dlBtn) {
                    dlBtn.style.display = '';
                    dlBtn.onclick = function () {
                        results.forEach(function (r, i) {
                            setTimeout(function () {
                                var a = document.createElement('a');
                                a.href = URL.createObjectURL(r.blob);
                                var ext = r.type === 'image/png' ? '.png' : '.jpg';
                                a.download = r.name.replace(/\.[^.]+$/, '') + '-compressed' + ext;
                                a.click();
                                URL.revokeObjectURL(a.href);
                            }, i * 200);
                        });
                    };
                }

                TCTP.toast('Compressed ' + results.length + ' image' + (results.length !== 1 ? 's' : '') + '! Saved ' + savedPct, '\u2705');
            });
        });
    }
})();
