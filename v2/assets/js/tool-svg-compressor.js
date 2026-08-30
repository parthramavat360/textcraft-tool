/**
 * SVG Compressor — Tool JS
 * Premium redesign. Precision slider, remove metadata/comments/minify paths,
 * output file name, Clear all (also clears previews).
 *
 * @package TextCraft_Tools_Pro
 */
(function () {
    'use strict';
    var file = null;
    var optimizedSvg = null;
    var drop = document.getElementById('tc-svg-drop');
    if (!drop) return;
    var precisionSlider = document.getElementById('tc-svg-precision');
    var precisionVal = document.getElementById('tc-svg-precision-val');
    var metaCheck = document.getElementById('tc-svg-meta');
    var commentsCheck = document.getElementById('tc-svg-comments');
    var pathsCheck = document.getElementById('tc-svg-paths');
    if (precisionSlider && precisionVal) {
        precisionSlider.addEventListener('input', function () { precisionVal.textContent = precisionSlider.value; });
    }
    function setStat(id, val) { var el = document.getElementById(id); if (el) el.textContent = val; }
    function resetStats() { setStat('tc-svg-stat-orig', '-'); setStat('tc-svg-stat-comp', '-'); setStat('tc-svg-stat-saved', '-'); }
    TCTP.initDropZone('tc-svg-drop', 'tc-svg-drop-input', function (f) {
        if (!f.type.match(/image\/svg\+xml/) && !/\.svg$/i.test(f.name)) {
            TCTP.toast('Please select an SVG file.', '\u26A0\uFE0F'); return;
        }
        file = f; optimizedSvg = null;
        TCTP.showFileRow('tc-svg-file', f);
        var dl = document.getElementById('tc-svg-download'); if (dl) dl.style.display = 'none';
        resetStats();
        var reader = new FileReader();
        reader.onload = function (ev) {
            TCTP.showOriginalPreview(ev.target.result, 'image/svg+xml');
            TCTP.switchToOriginalTab();
        };
        reader.readAsDataURL(f);
    }, 'image/svg+xml,.svg');
    var removeBtn = document.querySelector('#tc-svg-file .tc-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        file = null; optimizedSvg = null; TCTP.hideFileRow('tc-svg-file');
        resetStats();
        var dl = document.getElementById('tc-svg-download'); if (dl) dl.style.display = 'none';
    });
    function roundNumber(match) {
        var num = parseFloat(match);
        if (isNaN(num)) return match;
        var prec = parseInt(precisionSlider ? precisionSlider.value : '3', 10);
        var rounded = parseFloat(num.toFixed(prec));
        var str = String(rounded);
        if (str.indexOf('.') === -1 && match.indexOf('.') !== -1) return str + '.0';
        return str;
    }
    function minifySvg(svgText) {
        var result = svgText;
        if (commentsCheck && commentsCheck.checked) {
            result = result.replace(/<!--[\s\S]*?-->/g, '');
        }
        result = result.replace(/<\?[\s\S]*?\?>/g, '');
        if (metaCheck && metaCheck.checked) {
            result = result.replace(/<metadata[\s\S]*?<\/metadata>/gi, '');
            result = result.replace(/<title[\s\S]*?<\/title>/gi, '');
            result = result.replace(/<desc[\s\S]*?<\/desc>/gi, '');
            result = result.replace(/\s*xmlns:(?:sodipodi|inkscape|dc|cc|rdf)[^\s>]*/gi, '');
            result = result.replace(/\s*(?:sodipodi|inkscape):[a-z-]+="[^"]*"/gi, '');
            result = result.replace(/<sodipodi:[\s\S]*?<\/sodipodi:[\s\S]*?>/gi, '');
            result = result.replace(/<inkscape:[\s\S]*?<\/inkscape:[\s\S]*?>/gi, '');
            result = result.replace(/\s*data-(?:name|publisher|format|creator|title|subject|date)="[^"]*"/gi, '');
        }
        if (pathsCheck && pathsCheck.checked) {
            result = result.replace(/(\d+\.\d+)/g, roundNumber);
            result = result.replace(/\s*,\s*/g, ',');
            result = result.replace(/\s+/g, ' ');
        }
        result = result.replace(/>\s+</g, '><');
        result = result.replace(/\s+\/>/g, '/>');
        result = result.replace(/\s+>/g, '>');
        result = result.replace(/<\s+/g, '<');
        result = result.replace(/<((?:path|circle|rect|ellipse|line|polyline|polygon)(\s+[^>]*?)?)\s*>\s*<\/\1>/gi, '<$1/>');
        result = result.replace(/\s{2,}/g, ' ');
        return result.trim();
    }
    var compressBtn = document.getElementById('tc-svg-compress');
    if (compressBtn) compressBtn.addEventListener('click', function () {
        if (!file) { TCTP.toast('Please select an SVG file first.', '\u26A0\uFE0F'); return; }
        TCTP.showProgress('tc-svg-progress');
        TCTP.setProgress('tc-svg-progress', 20, 'Reading SVG...');
        var reader = new FileReader();
        reader.onload = function (e) {
            TCTP.setProgress('tc-svg-progress', 50, 'Optimizing...');
            var svgText = e.target.result;
            var origSize = new Blob([svgText]).size;
            setTimeout(function () {
                optimizedSvg = minifySvg(svgText);
                var compSize = new Blob([optimizedSvg]).size;
                var saved = origSize > compSize ? ((1 - compSize / origSize) * 100).toFixed(1) : '0';
                setStat('tc-svg-stat-orig', TCTP.formatSize(origSize));
                setStat('tc-svg-stat-comp', TCTP.formatSize(compSize));
                setStat('tc-svg-stat-saved', saved + '%');
                TCTP.updateResultPanel(TCTP.formatSize(origSize), TCTP.formatSize(compSize), saved + '%', 'Done');
                TCTP.showResultPreview('data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(optimizedSvg))));
                TCTP.switchToResultTab();
                TCTP.setProgress('tc-svg-progress', 100, 'Done!');
                if (saved !== '0') { TCTP.toast('Optimized! Saved ' + saved + '%'); }
                else { TCTP.toast('SVG is already optimized.'); }
                var dl = document.getElementById('tc-svg-download'); if (dl) dl.style.display = '';
            }, 50);
        };
        reader.readAsText(file);
    });
    var downloadBtn = document.getElementById('tc-svg-download');
    if (downloadBtn) downloadBtn.addEventListener('click', function () {
        if (!optimizedSvg) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
        var nameInput = document.getElementById('tc-svg-name');
        var base = (nameInput && nameInput.value.trim()) ? nameInput.value.trim().replace(/\.svg$/i, '') : (file ? file.name.replace(/\.svg$/i, '') : 'image');
        TCTP.downloadText(optimizedSvg, base + '-compressed.svg', 'image/svg+xml');
    });
    var clearBtn = document.getElementById('tc-svg-clear');
    if (clearBtn) clearBtn.addEventListener('click', function () {
        file = null; optimizedSvg = null;
        TCTP.hideFileRow('tc-svg-file');
        resetStats();
        var dl = document.getElementById('tc-svg-download'); if (dl) dl.style.display = 'none';
        var prevOrig = document.getElementById('tc-preview-orig'); if (prevOrig) prevOrig.innerHTML = '<span style="color:var(--muted);font-size:13px">Original preview will appear here</span>';
        var prevRes = document.getElementById('tc-preview-result'); if (prevRes) prevRes.innerHTML = '<span style="color:var(--muted);font-size:13px">Result preview will appear here</span>';
        TCTP.switchToOriginalTab();
        var nameInput = document.getElementById('tc-svg-name'); if (nameInput) nameInput.value = '';
    });
})();
