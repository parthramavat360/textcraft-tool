/**
 * WiFi QR Code Generator — Generate QR codes that connect to WiFi networks.
 * Uses minimal QR code generation via the QR Code Generator API.
 */
(function () {
    'use strict';
    if (!document.getElementById('wifi-ssid')) return;

    var ssidInput = document.getElementById('wifi-ssid');
    var passInput = document.getElementById('wifi-pass');
    var securitySelect = document.getElementById('wifi-security');
    var hiddenToggle = document.getElementById('wifi-hidden');
    var generateBtn = document.getElementById('wifi-generate');
    var downloadBtn = document.getElementById('wifi-download');
    var resultPanel = document.getElementById('wifi-result');
    var canvas = document.getElementById('wifi-qr-canvas');
    var statusEl = document.getElementById('wifi-status');

    generateBtn.addEventListener('click', function () {
        var ssid = ssidInput.value.trim();
        var pass = passInput.value;
        var security = securitySelect.value;
        var hidden = hiddenToggle.checked;

        if (!ssid) { TCTP.toast('Please enter a network name.', '\u26A0\uFE0F'); return; }

        var wifiString = 'WIFI:T:' + security + ';S:' + ssid + ';P:' + pass + (hidden ? ';H:true' : '') + ';;';
        generateQR(wifiString);
    });

    function generateQR(text) {
        var qr = generateQRMatrix(text);
        var size = qr.length;
        var cellSize = Math.max(4, Math.floor(400 / size));
        var margin = cellSize * 4;
        canvas.width = size * cellSize + margin * 2;
        canvas.height = size * cellSize + margin * 2;
        var ctx = canvas.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#000000';
        for (var r = 0; r < size; r++) {
            for (var c = 0; c < size; c++) {
                if (qr[r][c]) {
                    ctx.fillRect(margin + c * cellSize, margin + r * cellSize, cellSize, cellSize);
                }
            }
        }
        resultPanel.style.display = '';
        statusEl.textContent = 'QR Code Generated';
        TCTP.toast('QR code generated!');
    }

    downloadBtn.addEventListener('click', function () {
        canvas.toBlob(function (blob) {
            TCTP.downloadBlob(blob, 'wifi-qr-code.png');
        });
    });

    function generateQRMatrix(text) {
        var data = encode(text);
        var version = 1;
        var modules = 21;
        var ecLevel = 0;
        var totalCodewords = 26;
        var ecCodewords = 7;
        var groups = [[1, 19]];

        var matrix = [];
        var reserved = [];
        for (var i = 0; i < modules; i++) {
            matrix[i] = [];
            reserved[i] = [];
            for (var j = 0; j < modules; j++) {
                matrix[i][j] = false;
                reserved[i][j] = false;
            }
        }

        placeFinder(matrix, reserved, 0, 0);
        placeFinder(matrix, reserved, modules - 7, 0);
        placeFinder(matrix, reserved, 0, modules - 7);

        placeAlign(matrix, reserved, modules);

        for (var i = 8; i < modules - 8; i++) {
            matrix[6][i] = i % 2 === 0;
            reserved[6][i] = true;
            matrix[i][6] = i % 2 === 0;
            reserved[i][6] = true;
        }

        var dataBits = [];
        for (var i = 0; i < data.length; i++) {
            dataBits.push(data[i]);
        }

        var remainderBits = modules === 21 ? 0 : 0;
        while (dataBits.length < totalCodewords * 8) {
            dataBits.push(0);
        }

        var dataBytes = [];
        for (var i = 0; i < dataBits.length; i += 8) {
            var byte = 0;
            for (var j = 0; j < 8; j++) {
                byte = (byte << 1) | (dataBits[i + j] || 0);
            }
            dataBytes.push(byte);
        }

        for (var i = 0; i < dataBytes.length; i++) {
            var bits = [];
            for (var b = 7; b >= 0; b--) {
                bits.push((dataBytes[i] >> b) & 1);
            }
            var offset = i * 8;
            for (var j = 0; j < 8; j++) {
                var pos = offset + j;
                if (pos < dataBits.length) dataBits[pos] = bits[j];
            }
        }

        var bitIndex = 0;
        var col = modules - 1;
        var upward = true;
        while (col >= 0) {
            if (col === 6) col--;
            for (var row = 0; row < modules; row++) {
                var actualRow = upward ? modules - 1 - row : row;
                for (var dc = 0; dc < 2; dc++) {
                    var c = col - dc;
                    if (c >= 0 && !reserved[actualRow][c]) {
                        matrix[actualRow][c] = bitIndex < dataBits.length ? !!dataBits[bitIndex] : false;
                        bitIndex++;
                    }
                }
            }
            col -= 2;
            upward = !upward;
        }

        var bestMask = 0;
        var bestPenalty = Infinity;
        for (var m = 0; m < 8; m++) {
            var testMatrix = applyMask(matrix, reserved, m, modules);
            placeFormatInfo(testMatrix, reserved, m, modules);
            var penalty = calculatePenalty(testMatrix, modules);
            if (penalty < bestPenalty) {
                bestPenalty = penalty;
                bestMask = m;
            }
        }

        var finalMatrix = applyMask(matrix, reserved, bestMask, modules);
        placeFormatInfo(finalMatrix, reserved, bestMask, modules);

        return finalMatrix;
    }

    function placeFinder(matrix, reserved, row, col) {
        for (var r = -1; r <= 7; r++) {
            for (var c = -1; c <= 7; c++) {
                var rr = row + r, cc = col + c;
                if (rr >= 0 && rr < matrix.length && cc >= 0 && cc < matrix.length) {
                    if (r >= 0 && r <= 6 && (c === 0 || c === 6)) {
                        matrix[rr][cc] = true; reserved[rr][cc] = true;
                    } else if (c >= 0 && c <= 6 && (r === 0 || r === 6)) {
                        matrix[rr][cc] = true; reserved[rr][cc] = true;
                    } else if (r >= 2 && r <= 4 && c >= 2 && c <= 4) {
                        matrix[rr][cc] = true; reserved[rr][cc] = true;
                    } else if (r === -1 || r === 7 || c === -1 || c === 7) {
                        matrix[rr][cc] = false; reserved[rr][cc] = true;
                    }
                }
            }
        }
    }

    function placeAlign(matrix, reserved, modules) {
        var positions = [6, modules - 7];
        for (var pi = 0; pi < positions.length; pi++) {
            for (var pj = 0; pj < positions.length; pj++) {
                var row = positions[pi], col = positions[pj];
                if (reserved[row][col]) continue;
                for (var r = -2; r <= 2; r++) {
                    for (var c = -2; c <= 2; c++) {
                        var rr = row + r, cc = col + c;
                        if (rr >= 0 && rr < modules && cc >= 0 && cc < modules) {
                            matrix[rr][cc] = (Math.abs(r) === 2 || Math.abs(c) === 2 || (r === 0 && c === 0));
                            reserved[rr][cc] = true;
                        }
                    }
                }
            }
        }
    }

    function applyMask(matrix, reserved, mask, modules) {
        var result = [];
        for (var r = 0; r < modules; r++) {
            result[r] = [];
            for (var c = 0; c < modules; c++) {
                result[r][c] = matrix[r][c];
                if (!reserved[r][c]) {
                    var flip = false;
                    switch (mask) {
                        case 0: flip = (r + c) % 2 === 0; break;
                        case 1: flip = r % 2 === 0; break;
                        case 2: flip = c % 3 === 0; break;
                        case 3: flip = (r + c) % 3 === 0; break;
                        case 4: flip = (Math.floor(r / 2) + Math.floor(c / 3)) % 2 === 0; break;
                        case 5: flip = (r * c) % 2 + (r * c) % 3 === 0; break;
                        case 6: flip = ((r * c) % 2 + (r * c) % 3) % 2 === 0; break;
                        case 7: flip = ((r + c) % 2 + (r * c) % 3) % 2 === 0; break;
                    }
                    if (flip) result[r][c] = !result[r][c];
                }
            }
        }
        return result;
    }

    function placeFormatInfo(matrix, reserved, mask, modules) {
        var ecLevelBits = 1;
        var formatBits = (ecLevelBits << 3) | mask;
        var bch = formatBits;
        var gen = 0x537;
        for (var i = 0; i < 10; i++) {
            if (bch & (1 << (14 - i))) bch ^= gen << (4 - i);
        }
        var format = ((ecLevelBits << 3) | mask) << 10 | bch;
        format ^= 0x5412;

        for (var i = 0; i < 6; i++) {
            matrix[8][i] = !!(format & (1 << (14 - i)));
            reserved[8][i] = true;
        }
        matrix[8][7] = !!(format & (1 << 8));
        reserved[8][7] = true;
        matrix[8][8] = !!(format & (1 << 7));
        reserved[8][8] = true;
        matrix[7][8] = !!(format & (1 << 6));
        reserved[7][8] = true;
        for (var i = 0; i < 6; i++) {
            matrix[5 - i][8] = !!(format & (1 << (5 - i)));
            reserved[5 - i][8] = true;
        }

        for (var i = 0; i < 8; i++) {
            matrix[modules - 1 - i][8] = !!(format & (1 << i));
            reserved[modules - 1 - i][8] = true;
        }
        for (var i = 0; i < 7; i++) {
            matrix[8][modules - 7 + i] = !!(format & (1 << (14 - i)));
            reserved[8][modules - 7 + i] = true;
        }
    }

    function calculatePenalty(matrix, modules) {
        var penalty = 0;
        for (var r = 0; r < modules; r++) {
            var count = 1;
            for (var c = 1; c < modules; c++) {
                if (matrix[r][c] === matrix[r][c - 1]) count++;
                else { if (count >= 5) penalty += count - 2; count = 1; }
            }
            if (count >= 5) penalty += count - 2;
        }
        for (var c = 0; c < modules; c++) {
            var count = 1;
            for (var r = 1; r < modules; r++) {
                if (matrix[r][c] === matrix[r - 1][c]) count++;
                else { if (count >= 5) penalty += count - 2; count = 1; }
            }
            if (count >= 5) penalty += count - 2;
        }
        return penalty;
    }

    function encode(text) {
        var bytes = [];
        for (var i = 0; i < text.length; i++) {
            var code = text.charCodeAt(i);
            if (code < 0x80) bytes.push(code);
            else if (code < 0x800) { bytes.push(0xC0 | (code >> 6)); bytes.push(0x80 | (code & 0x3F)); }
            else { bytes.push(0xE0 | (code >> 12)); bytes.push(0x80 | ((code >> 6) & 0x3F)); bytes.push(0x80 | (code & 0x3F)); }
        }

        var modeIndicator = [0, 1, 0, 0];
        var charCountBits = [8, 16, 16];
        var charCount = [];
        var len = bytes.length;
        for (var i = 7; i >= 0; i--) charCount.push((len >> i) & 1);

        var dataBits = modeIndicator.concat(charCount);
        for (var i = 0; i < bytes.length; i++) {
            for (var b = 7; b >= 0; b--) dataBits.push((bytes[i] >> b) & 1);
        }

        var terminator = [0, 0, 0, 0];
        dataBits = dataBits.concat(terminator);

        while (dataBits.length % 8 !== 0) dataBits.push(0);

        var padBytes = [0xEC, 0x11];
        var padIdx = 0;
        while (dataBits.length < 152) {
            var pb = padBytes[padIdx % 2];
            for (var b = 7; b >= 0; b--) dataBits.push((pb >> b) & 1);
            padIdx++;
        }

        return dataBits;
    }
})();
