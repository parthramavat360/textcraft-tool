/**
 * SVG Compressor — Tool JS
 *
 * Client-side SVG optimization: remove comments, minify paths,
 * round decimals at configurable precision. No external libs.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var file = null;
    var optimizedSvg = null;
    var precision = 2;

    var drop = document.getElementById('tc-svg-drop');
    if (!drop) return;

    var precisionSlider = document.getElementById('tc-svg-precision');
    var precisionVal = document.getElementById('tc-svg-precision-val');
    if (precisionSlider) {
        precisionSlider.addEventListener('input', function () {
            precision = parseInt(precisionSlider.value);
            if (precisionVal) precisionVal.textContent = precision;
        });
    }

    TCTP.initDropZone('tc-svg-drop', 'tc-svg-drop-input', function (f) {
        if (!f.type.match(/image\/svg\+xml/) && !/\.svg$/i.test(f.name)) {
            TCTP.toast('Please select an SVG file.', '\u26A0\uFE0F');
            return;
        }
        file = f;
        optimizedSvg = null;
        TCTP.showFileRow('tc-svg-file', f);
        var statsEl = document.getElementById('tc-svg-stats');
        if (statsEl) statsEl.style.display = 'none';
    }, 'image/svg+xml,.svg');

    var removeBtn = document.querySelector('#tc-svg-file .tctp-x, #tc-svg-file .tc-x');
    if (removeBtn) removeBtn.addEventListener('click', function () {
        file = null;
        optimizedSvg = null;
        TCTP.hideFileRow('tc-svg-file');
        var statsEl = document.getElementById('tc-svg-stats');
        if (statsEl) statsEl.style.display = 'none';
    });

    function minifySvg(svgText, prec) {
        var result = svgText;
        result = result.replace(/<!--[\s\S]*?-->/g, '');
        result = result.replace(/<\?[\s\S]*?\?>/g, '');
        result = result.replace(/\s+/g, ' ');
        result = result.replace(/>\s+</g, '><');
        result = result.replace(/\s+\/>/g, '/>');
        result = result.replace(/\s+>/g, '>');
        result = result.replace(/<\s+/g, '<');

        var factor = Math.pow(10, prec);
        result = result.replace(/(\d+\.\d+)/g, function (match) {
            return parseFloat(match).toFixed(prec);
        });

        result = result.replace(/(<[^>]+?)(\s+)([^=]+?)=\s*"([^"]*?)\s+"/g, function (m, before, sp, attr, val) {
            return before + ' ' + attr + '="' + val.trim() + '"';
        });

        result = result.replace(/<([a-zA-Z]+)((?:\s+[^>]*)?)\/>/g, function (m, tag, attrs) {
            return '<' + tag + attrs + '/>';
        });

        return result.trim();
    }

    var compressBtn = document.getElementById('tc-svg-compress');
    if (compressBtn) compressBtn.addEventListener('click', function () {
        if (!file) { TCTP.toast('Please select an SVG file first.', '\u26A0\uFE0F'); return; }

        TCTP.showProgress('tc-svg-progress');
        TCTP.setProgress('tc-svg-progress', 30, 'Reading SVG...');

        var reader = new FileReader();
        reader.onload = function (e) {
            TCTP.setProgress('tc-svg-progress', 60, 'Optimizing...');
            var svgText = e.target.result;
            var origSize = new Blob([svgText]).size;

            optimizedSvg = minifySvg(svgText, precision);
            var compSize = new Blob([optimizedSvg]).size;
            var saved = origSize > compSize ? ((1 - compSize / origSize) * 100).toFixed(1) : '0';

            var origEl = document.getElementById('tc-svg-stat-orig');
            var compEl = document.getElementById('tc-svg-stat-comp');
            var savedEl = document.getElementById('tc-svg-stat-saved');
            if (origEl) origEl.textContent = TCTP.formatSize(origSize);
            if (compEl) compEl.textContent = TCTP.formatSize(compSize);
            if (savedEl) savedEl.textContent = saved + '%';

            var statsEl = document.getElementById('tc-svg-stats');
            if (statsEl) statsEl.style.display = '';

            TCTP.setProgress('tc-svg-progress', 100, 'Done!');
            TCTP.toast('Optimized! Saved ' + saved + '%');
        };
        reader.readAsText(file);
    });

    var downloadBtn = document.getElementById('tc-svg-download');
    if (downloadBtn) downloadBtn.addEventListener('click', function () {
        if (!optimizedSvg) { TCTP.toast('Nothing to download yet.', '\u26A0\uFE0F'); return; }
        var name = (file ? file.name.replace(/\.svg$/i, '') : 'image') + '-compressed.svg';
        TCTP.downloadText(optimizedSvg, name, 'image/svg+xml');
    });

})();
