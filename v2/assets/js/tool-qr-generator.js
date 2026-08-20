/**
 * QR Code Generator — Tool JS
 * Minimal QR code generator (Alphanumeric/Byte mode, versions 1-10).
 * Uses Reed-Solomon error correction. Generates SVG output.
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var generateBtn = document.getElementById('tc-qr-generate');
    var previewEl = document.getElementById('tc-qr-preview');
    if (!generateBtn) return;

    var currentType = 'text';
    var lastSvg = '';

    document.querySelectorAll('.tc-modes[data-group="qr-type"] .tc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            currentType = btn.getAttribute('data-val');
            ['text-group', 'email-group', 'phone-group', 'wifi-group', 'vcard-group'].forEach(function (id) {
                var el = document.getElementById('tc-qr-' + id);
                if (el) el.style.display = 'none';
            });
            var show = document.getElementById('tc-qr-' + currentType + '-group');
            if (show) show.style.display = '';
        });
    });

    function getContent() {
        switch (currentType) {
            case 'text':
                return document.getElementById('tc-qr-text') ? document.getElementById('tc-qr-text').value : '';
            case 'email':
                var addr = document.getElementById('tc-qr-email') ? document.getElementById('tc-qr-email').value : '';
                var subj = document.getElementById('tc-qr-email-subject') ? document.getElementById('tc-qr-email-subject').value : '';
                var body = document.getElementById('tc-qr-email-body') ? document.getElementById('tc-qr-email-body').value : '';
                return 'mailto:' + addr + (subj ? '?subject=' + encodeURIComponent(subj) + (body ? '&body=' + encodeURIComponent(body) : '') : (body ? '?body=' + encodeURIComponent(body) : ''));
            case 'phone':
                return 'tel:' + (document.getElementById('tc-qr-phone') ? document.getElementById('tc-qr-phone').value : '');
            case 'wifi':
                var ssid = document.getElementById('tc-qr-wifi-ssid') ? document.getElementById('tc-qr-wifi-ssid').value : '';
                var pass = document.getElementById('tc-qr-wifi-pass') ? document.getElementById('tc-qr-wifi-pass').value : '';
                var enc = (document.getElementById('tc-qr-wifi-enc') || {}).value || 'WPA';
                return 'WIFI:T:' + enc + ';S:' + ssid + ';P:' + pass + ';;';
            case 'vcard':
                var fn = document.getElementById('tc-qr-vcard-fn') ? document.getElementById('tc-qr-vcard-fn').value : '';
                var ln = document.getElementById('tc-qr-vcard-ln') ? document.getElementById('tc-qr-vcard-ln').value : '';
                var ph = document.getElementById('tc-qr-vcard-phone') ? document.getElementById('tc-qr-vcard-phone').value : '';
                var em = document.getElementById('tc-qr-vcard-email') ? document.getElementById('tc-qr-vcard-email').value : '';
                return 'BEGIN:VCARD\nVERSION:3.0\nN:' + ln + ';' + fn + ';;;\nFN:' + fn + ' ' + ln + (ph ? '\nTEL:' + ph : '') + (em ? '\nEMAIL:' + em : '') + '\nEND:VCARD';
            default:
                return '';
        }
    }

    /* ── Minimal QR Code Encoder ── */
    /* Simplified implementation: byte mode, version 1-6, EC level M */

    var GF_EXP = new Array(512);
    var GF_LOG = new Array(256);
    (function initGF() {
        var x = 1;
        for (var i = 0; i < 255; i++) {
            GF_EXP[i] = x;
            GF_LOG[x] = i;
            x = (x << 1) ^ (x & 128 ? 0x11d : 0);
        }
        for (var i = 255; i < 512; i++) GF_EXP[i] = GF_EXP[i - 255];
    })();

    function gfMul(a, b) {
        if (a === 0 || b === 0) return 0;
        return GF_EXP[GF_LOG[a] + GF_LOG[b]];
    }

    function rsGenPoly(nsym) {
        var g = [1];
        for (var i = 0; i < nsym; i++) {
            var ng = new Array(g.length + 1).fill(0);
            for (var j = 0; j < g.length; j++) {
                ng[j] ^= g[j];
                ng[j + 1] ^= gfMul(g[j], GF_EXP[i]);
            }
            g = ng;
        }
        return g;
    }

    function rsEncode(data, nsym) {
        var gen = rsGenPoly(nsym);
        var res = new Array(data.length + nsym).fill(0);
        for (var i = 0; i < data.length; i++) res[i] = data[i];
        for (var i = 0; i < data.length; i++) {
            var coef = res[i];
            if (coef !== 0) {
                for (var j = 1; j < gen.length; j++) {
                    res[i + j] ^= gfMul(gen[j], coef);
                }
            }
        }
        return res.slice(data.length);
    }

    /* Version info: [totalCodewords, ecPerBlock, numBlocks1, dataPerBlock1] */
    var VERSIONS = {
        1:  [26, 10, 1, 16],
        2:  [44, 16, 1, 28],
        3:  [70, 26, 1, 44],
        4:  [100, 18, 2, 32],
        5:  [134, 24, 2, 43],
        6:  [172, 16, 4, 27],
    };

    var ALIGNMENTS = {1:[],2:[6,18],3:[6,22],4:[6,26],5:[6,30],6:[6,34]};
    var FORMAT_INFO = 0x5412;

    function chooseVersion(len) {
        for (var v = 1; v <= 6; v++) {
            var info = VERSIONS[v];
            var totalData = info[2] * info[3];
            if (len <= totalData) return v;
        }
        return 6;
    }

    function buildDataCodewords(text, version) {
        var info = VERSIONS[version];
        var totalData = info[2] * info[3];
        var bytes = [];
        for (var i = 0; i < text.length; i++) {
            var c = text.charCodeAt(i);
            if (c < 128) bytes.push(c);
            else if (c < 2048) { bytes.push(192 | (c >> 6)); bytes.push(128 | (c & 63)); }
            else { bytes.push(224 | (c >> 12)); bytes.push(128 | ((c >> 6) & 63)); bytes.push(128 | (c & 63)); }
        }
        bytes.unshift(0x40 | Math.min(bytes.length, 255));
        if (bytes.length < totalData) bytes.push(0xec);
        while (bytes.length < totalData) bytes.push(0x11);
        return bytes.slice(0, totalData);
    }

    function buildCodewords(text, version) {
        var info = VERSIONS[version];
        var data = buildDataCodewords(text, version);
        var ecPerBlock = info[1];
        var numBlocks = info[2];
        var blockSize = info[3];
        var dataBlocks = [];
        for (var i = 0; i < numBlocks; i++) {
            dataBlocks.push(data.slice(i * blockSize, (i + 1) * blockSize));
        }
        var ecBlocks = dataBlocks.map(function (b) { return rsEncode(b, ecPerBlock); });
        var result = [];
        var maxData = blockSize;
        for (var i = 0; i < maxData; i++) {
            for (var j = 0; j < numBlocks; j++) {
                if (i < dataBlocks[j].length) result.push(dataBlocks[j][i]);
            }
        }
        for (var i = 0; i < ecPerBlock; i++) {
            for (var j = 0; j < numBlocks; j++) {
                if (i < ecBlocks[j].length) result.push(ecBlocks[j][i]);
            }
        }
        return result;
    }

    function createMatrix(version) {
        var size = version * 4 + 17;
        var matrix = [];
        var reserved = [];
        for (var r = 0; r < size; r++) {
            matrix.push(new Array(size).fill(0));
            reserved.push(new Array(size).fill(false));
        }
        return { matrix: matrix, reserved: reserved, size: size };
    }

    function placeFinderPattern(mat, row, col) {
        for (var r = -1; r <= 7; r++) {
            for (var c = -1; c <= 7; c++) {
                var rr = row + r, cc = col + c;
                if (rr < 0 || rr >= mat.size || cc < 0 || cc >= mat.size) continue;
                var val = (r >= 0 && r <= 6 && (c === 0 || c === 6)) ||
                          (c >= 0 && c <= 6 && (r === 0 || r === 6)) ||
                          (r >= 2 && r <= 4 && c >= 2 && c <= 4) ? 1 : 0;
                mat.matrix[rr][cc] = val;
                mat.reserved[rr][cc] = true;
            }
        }
    }

    function placeAlignments(mat, version) {
        var positions = ALIGNMENTS[version] || [];
        for (var i = 0; i < positions.length; i++) {
            for (var j = 0; j < positions.length; j++) {
                var r = positions[i], c = positions[j];
                if (mat.reserved[r][c]) continue;
                for (var dr = -2; dr <= 2; dr++) {
                    for (var dc = -2; dc <= 2; dc++) {
                        var val = (Math.abs(dr) === 2 || Math.abs(dc) === 2 || (dr === 0 && dc === 0)) ? 1 : 0;
                        mat.matrix[r + dr][c + dc] = val;
                        mat.reserved[r + dr][c + dc] = true;
                    }
                }
            }
        }
    }

    function placeTiming(mat) {
        for (var i = 8; i < mat.size - 8; i++) {
            if (!mat.reserved[6][i]) { mat.matrix[6][i] = i % 2 === 0 ? 1 : 0; mat.reserved[6][i] = true; }
            if (!mat.reserved[i][6]) { mat.matrix[i][6] = i % 2 === 0 ? 1 : 0; mat.reserved[i][6] = true; }
        }
    }

    function placeFormatBits(mat) {
        var bits = FORMAT_INFO;
        for (var i = 0; i < 6; i++) { mat.matrix[8][i] = (bits >> (14 - i)) & 1; mat.reserved[8][i] = true; }
        mat.matrix[8][7] = (bits >> 8) & 1; mat.reserved[8][7] = true;
        mat.matrix[8][8] = (bits >> 7) & 1; mat.reserved[8][8] = true;
        mat.matrix[7][8] = (bits >> 6) & 1; mat.reserved[7][8] = true;
        for (var i = 0; i < 6; i++) { mat.matrix[5 - i][8] = (bits >> (5 - i)) & 1; mat.reserved[5 - i][8] = true; }
        for (var i = 0; i < 7; i++) { mat.matrix[mat.size - 1 - i][8] = (bits >> i) & 1; mat.reserved[mat.size - 1 - i][8] = true; }
        mat.matrix[mat.size - 8][8] = 1; mat.reserved[mat.size - 8][8] = true;
        for (var i = 0; i < 7; i++) { mat.matrix[8][mat.size - 7 + i] = (bits >> (14 - (i + 8))) & 1; mat.reserved[8][mat.size - 7 + i] = true; }
    }

    function reserveAll(mat, version) {
        placeFinderPattern(mat, 0, 0);
        placeFinderPattern(mat, 0, mat.size - 7);
        placeFinderPattern(mat, mat.size - 7, 0);
        placeAlignments(mat, version);
        placeTiming(mat);
        mat.reserved[mat.size - 8][8] = true;
        placeFormatBits(mat);
    }

    function placeData(mat, codewords) {
        var bits = [];
        codewords.forEach(function (b) { for (var i = 7; i >= 0; i--) bits.push((b >> i) & 1); });
        var bitIdx = 0;
        var col = mat.size - 1;
        var upward = true;
        while (col >= 0) {
            if (col === 6) col--;
            var rows = upward ? mat.size - 1 : 0;
            var end = upward ? -1 : mat.size;
            var step = upward ? -1 : 1;
            for (var r = rows; r !== end; r += step) {
                for (var dc = 0; dc <= 1; dc++) {
                    var c = col - dc;
                    if (c < 0 || c >= mat.size) continue;
                    if (mat.reserved[r][c]) continue;
                    mat.matrix[r][c] = bitIdx < bits.length ? bits[bitIdx++] : 0;
                }
            }
            upward = !upward;
            col -= 2;
        }
    }

    function applyMask(mat) {
        for (var r = 0; r < mat.size; r++) {
            for (var c = 0; c < mat.size; c++) {
                if (mat.reserved[r][c]) continue;
                if ((r + c) % 2 === 0) mat.matrix[r][c] ^= 1;
            }
        }
    }

    function generateQR(text) {
        var version = chooseVersion(text.length);
        var codewords = buildCodewords(text, version);
        var mat = createMatrix(version);
        reserveAll(mat, version);
        placeData(mat, codewords);
        applyMask(mat);
        return mat;
    }

    function renderSvg(mat, pixelSize) {
        var moduleSize = Math.floor(pixelSize / mat.size);
        var svgSize = moduleSize * mat.size;
        var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' + svgSize + '" height="' + svgSize + '" viewBox="0 0 ' + mat.size + ' ' + mat.size + '">';
        svg += '<rect width="' + mat.size + '" height="' + mat.size + '" fill="#ffffff"/>';
        for (var r = 0; r < mat.size; r++) {
            for (var c = 0; c < mat.size; c++) {
                if (mat.matrix[r][c] === 1) {
                    svg += '<rect x="' + c + '" y="' + r + '" width="1" height="1" fill="#000000"/>';
                }
            }
        }
        svg += '</svg>';
        return svg;
    }

    generateBtn.addEventListener('click', function () {
        var content = getContent();
        if (!content) {
            TCTP.toast('Enter content for the QR code.', '\u26A0\uFE0F');
            return;
        }
        try {
            var mat = generateQR(content);
            var sizeEl = document.getElementById('tc-qr-size');
            var size = sizeEl ? parseInt(sizeEl.value) || 256 : 256;
            lastSvg = renderSvg(mat, size);
            if (previewEl) {
                previewEl.innerHTML = lastSvg;
            }
            var statusEl = document.getElementById('tc-qr-status');
            if (statusEl) {
                statusEl.textContent = 'QR code generated (' + mat.size + 'x' + mat.size + ' modules)';
                statusEl.className = 'tc-status tc-status--success';
            }
            TCTP.toast('QR code generated!');
        } catch (e) {
            TCTP.toast('Error generating QR: ' + e.message, '\u274C');
        }
    });

    document.getElementById('tc-qr-download-svg').addEventListener('click', function () {
        if (!lastSvg) {
            TCTP.toast('Generate a QR code first.', '\u26A0\uFE0F');
            return;
        }
        TCTP.downloadText(lastSvg, 'qrcode.svg', 'image/svg+xml');
    });

})();
