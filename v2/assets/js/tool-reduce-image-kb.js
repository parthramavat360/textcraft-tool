/**
 * Reduce Image Size to KB — Tool JS
 * Compress an image to an exact target file size (e.g. 20/50/100/200/500 KB)
 * by binary-searching the compression quality until the output hits the target.
 * 100% client-side using the canvas API.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var drop = document.getElementById('tc-kb-drop');
    if (!drop) return;

    var source = null;       // HTMLImageElement
    var sourceName = '';
    var sourceW = 0, sourceH = 0;

    var targetKb = 50;
    var outMime = 'image/jpeg';
    var outExt = 'jpg';

    var resultBlob = null;
    var originalBytes = 0;

    // ── Drop zone ──────────────────────────────────────────────

    TCTP.initDropZone('tc-kb-drop', 'tc-kb-drop-input', function (f) {
        if (!f.type.match(/^image\/(jpeg|png|webp)$/)) {
            TCTP.toast('Please select a JPG, PNG, or WebP image.', '\u26A0\uFE0F');
            return;
        }
        var reader = new FileReader();
        reader.onload = function (e) {
            var img = new Image();
            img.onload = function () {
                source = img;
                sourceW = img.naturalWidth;
                sourceH = img.naturalHeight;
                sourceName = f.name;
                originalBytes = f.size;
                resultBlob = null;
                TCTP.showFileRow('tc-kb-file', f);
                enableIfReady();
                var prevOrig = document.getElementById('tc-preview-orig');
                if (prevOrig) {
                    prevOrig.innerHTML = '';
                    var pv = new Image();
                    pv.src = e.target.result;
                    pv.style.maxWidth = '100%';
                    pv.style.maxHeight = '300px';
                    pv.style.objectFit = 'contain';
                    pv.style.borderRadius = '8px';
                    prevOrig.appendChild(pv);
                }
                updateOriginStat();
                TCTP.toast('Image loaded! Choose a target and hit Reduce Size.');
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(f);
    }, 'image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp');

    var removeBtn = document.querySelector('#tc-kb-file .tc-x');
    if (removeBtn) removeBtn.addEventListener('click', resetAll);

    // ── Target size tabs ───────────────────────────────────────

    var targetBtns = document.querySelectorAll('#tc-kb-target-tabs .tc-kb-target');
    var customRow = document.getElementById('tc-kb-custom-row');
    var customInput = document.getElementById('tc-kb-custom');

    targetBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            targetBtns.forEach(function (b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            if (btn.classList.contains('tc-kb-custom')) {
                if (customRow) customRow.style.display = 'flex';
                if (customInput) customInput.focus();
                readTarget();
            } else {
                if (customRow) customRow.style.display = 'none';
                targetKb = parseInt(btn.getAttribute('data-kb'), 10) || 50;
                updateStats();
            }
        });
    });

    if (customInput) customInput.addEventListener('input', readTarget);

    function readTarget() {
        var v = parseInt(customInput ? customInput.value : '0', 10);
        if (!customRow || customRow.style.display === 'none') return;
        targetKb = (v > 0) ? v : 0;
        updateStats();
    }

    // ── Output format ──────────────────────────────────────────

    var fmtBtns = document.querySelectorAll('#tc-kb-fmt-tabs .tc-kb-fmt');
    fmtBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            fmtBtns.forEach(function (b) { b.classList.remove('sel'); });
            btn.classList.add('sel');
            outMime = btn.getAttribute('data-mime');
            outExt = btn.getAttribute('data-ext');
        });
    });

    // ── Max width slider ───────────────────────────────────────

    var maxwSlider = document.getElementById('tc-kb-maxw');
    var maxwVal = document.getElementById('tc-kb-maxw-val');
    var maxWidth = 0;
    if (maxwSlider) maxwSlider.addEventListener('input', function () {
        maxWidth = parseInt(maxwSlider.value, 10) || 0;
        if (maxwVal) maxwVal.textContent = maxWidth === 0 ? 'Off' : maxWidth + 'px';
    });

    // ── Compress with a given quality ──────────────────────────

    // Draws source into a canvas (resizing to maxWidth if set) and encodes
    // at the given quality. Returns a Promise<Blob>.
    function encodeAtQuality(quality) {
        return new Promise(function (resolve) {
            var w = sourceW;
            var h = sourceH;
            if (maxWidth > 0 && w > maxWidth) {
                h = Math.round(h * (maxWidth / w));
                w = maxWidth;
            }
            var canvas = document.createElement('canvas');
            canvas.width = w;
            canvas.height = h;
            var ctx = canvas.getContext('2d');
            if (outMime === 'image/png' && /\.(png)$/i.test(outExt)) {
                // keep transparency for PNG
                ctx.clearRect(0, 0, w, h);
            } else {
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, w, h);
            }
            ctx.imageSmoothingEnabled = true;
            ctx.imageSmoothingQuality = 'high';
            ctx.drawImage(source, 0, 0, w, h);
            canvas.toBlob(function (blob) {
                resolve(blob);
            }, outMime, quality / 100);
        });
    }

    function blobToKb(blob) {
        return blob.size / 1024;
    }

    // ── Binary search: find quality closest to target ──────────

    // Returns Promise that resolves to { blob, quality, kb }.
    function minimizeToTarget() {
        var targetBytes = targetKb * 1024;
        var lo = 1;      // minimum quality %
        var hi = 100;    // maximum quality %

        return new Promise(function (resolvePromise) {
            // We want the largest quality that is still <= targetBytes (best
            // quality under target), OR if even q=1 can't get under target, the
            // smallest size we can reach.
            var best = null; // best result <= target
            var floorBlob = null; // smallest overall result

            function step() {
                if (lo > hi) {
                    // Done searching. Prefer a result within target; else the smallest.
                    var finalBlob = best ? best.blob : (floorBlob || null);
                    if (!finalBlob) {
                        resolvePromise(null);
                        return;
                    }
                    resolvePromise({
                        blob: finalBlob,
                        quality: best ? best.quality : (floorBlob ? 1 : 100),
                        kb: blobToKb(finalBlob),
                        onTarget: !!best
                    });
                    return;
                }
                var mid = Math.round((lo + hi) / 2);
                encodeAtQuality(mid).then(function (blob) {
                    var bytes = blob.size;
                    if (!floorBlob || bytes < floorBlob.size) floorBlob = blob;
                    if (bytes <= targetBytes) {
                        // This quality fits; record best and try higher quality.
                        best = { blob: blob, quality: mid };
                        lo = mid + 1;
                    } else {
                        // Too big; try lower quality.
                        hi = mid - 1;
                    }
                    // Report progress roughly
                    var progress = Math.round(20 + 70 * (1 - (hi - lo) / 100));
                    TCTP.setProgress('tc-kb-progress', Math.max(20, Math.min(90, progress)), 'Tuning quality... ' + mid + '%');
                    step();
                });
            }
            step();
        });
    }

    // ── Reduce button ──────────────────────────────────────────

    var reduceBtn = document.getElementById('tc-kb-reduce');
    var dlBtn = document.getElementById('tc-kb-download');

    function requireSource() {
        if (!source) {
            TCTP.toast('Please upload an image first.', '\u26A0\uFE0F');
            return false;
        }
        var t = targetKb;
        if (customRow && customRow.style.display !== 'none' && (!t || t <= 0)) {
            TCTP.toast('Enter a valid custom target size.', '\u26A0\uFE0F');
            return false;
        }
        return true;
    }

    if (reduceBtn) reduceBtn.addEventListener('click', function () {
        if (!requireSource()) return;
        readTarget();
        if (!targetKb || targetKb <= 0) {
            TCTP.toast('Enter a valid target size (KB).', '\u26A0\uFE0F');
            return;
        }
        if (outMime === 'image/png') {
            TCTP.toast('Compressing PNG to an exact size may be limited by its format. Continue optimising...', '\u26A0\uFE0F');
        }

        TCTP.showProgress('tc-kb-progress', 10, 'Preparing image...');

        minimizeToTarget().then(function (result) {
            if (!result || !result.blob) {
                TCTP.hideProgress('tc-kb-progress');
                TCTP.toast('Could not optimise this image.', '\u26A0\uFE0F');
                return;
            }
            resultBlob = result.blob;
            TCTP.setProgress('tc-kb-progress', 100, 'Done!');
            setTimeout(function () { TCTP.hideProgress('tc-kb-progress'); }, 600);

            var outKb = result.kb;
            var targetKbF = targetKb;

            var url = URL.createObjectURL(result.blob);

            // Result panel stats
            var savedPct = originalBytes > 0 && result.blob.size < originalBytes
                ? '-' + Math.round((1 - result.blob.size / originalBytes) * 100) + '%'
                : 'target ' + (targetKbF ? targetKbF + ' KB' : '—');
            TCTP.updateResultPanel(
                TCTP.formatSize(originalBytes),
                TCTP.formatSize(result.blob.size),
                savedPct,
                'Reduced'
            );

            TCTP.showResultPreview(url);
            TCTP.switchToResultTab();
            var dl = document.getElementById('tc-kb-download');
            if (dl) dl.style.display = '';
            enableIfReady();
            updateStats();
            TCTP.toast('Done! Reduced to ' + outKb.toFixed(1) + ' KB.', '\u2705');
        });
    });

    // ── Download ───────────────────────────────────────────────

    if (dlBtn) dlBtn.addEventListener('click', function () {
        if (!resultBlob) {
            if (!requireSource()) return;
            TCTP.toast('Reduction not run yet.', '\u26A0\uFE0F');
            return;
        }
        var nameInput = document.getElementById('tc-kb-name');
        var base = (nameInput && nameInput.value.trim()) ? nameInput.value.trim().replace(/\.[^.]+$/, '') : (sourceName || 'image').replace(/\.[^.]+$/, '');
        TCTP.downloadBlob(resultBlob, base + '-' + targetKb + 'kb.' + outExt);
    });

    // ── Helpers ────────────────────────────────────────────────

    function enableIfReady() {
        var dr = document.getElementById('tc-kb-download');
        if (dr) dr.disabled = !source;
    }

    function updateOriginStat() {
        var orig = document.getElementById('tc-kb-stat-orig');
        if (orig && source) orig.textContent = sourceW + '\u00D7' + sourceH + ' px';
    }

    function resetAll() {
        source = null;
        sourceW = sourceH = 0;
        sourceName = '';
        originalBytes = 0;
        resultBlob = null;
        var dr = document.getElementById('tc-kb-download');
        if (dr) dr.disabled = true;
        TCTP.hideFileRow('tc-kb-file');
        var prevRes = document.getElementById('tc-preview-result');
        if (prevRes) prevRes.innerHTML = 'Reduced preview will appear here';
        var origStat = document.getElementById('tc-kb-stat-orig');
        if (origStat) origStat.textContent = '—';
        var outStat = document.getElementById('tc-kb-stat-out');
        if (outStat) outStat.textContent = '—';
        var prevOrig = document.getElementById('tc-preview-orig');
        if (prevOrig) prevOrig.innerHTML = 'Original preview will appear here';
    }

    function updateStats() {
        var target = document.getElementById('tc-kb-stat-target');
        if (target) target.textContent = targetKb > 0 ? targetKb + ' KB' : '—';
        var out = document.getElementById('tc-kb-stat-out');
        if (out && resultBlob) out.textContent = TCTP.formatSize(resultBlob.size);
    }

    enableIfReady();
    updateStats();

    // ── Clear all ──────────────────────────────────────────────

    var clearBtn = document.getElementById('tc-kb-clear');
    if (clearBtn) clearBtn.addEventListener('click', function () {
        resetAll();
        var prevRes = document.getElementById('tc-preview-result');
        if (prevRes) prevRes.innerHTML = '<span style="color:var(--muted);font-size:13px">Reduced preview will appear here</span>';
        var prevOrig = document.getElementById('tc-preview-orig');
        if (prevOrig) prevOrig.innerHTML = '<span style="color:var(--muted);font-size:13px">Original preview will appear here</span>';
        var dl = document.getElementById('tc-kb-download');
        if (dl) { dl.style.display = 'none'; }
        var nameInput = document.getElementById('tc-kb-name');
        if (nameInput) nameInput.value = '';
        TCTP.switchToOriginalTab();
    });
})();
