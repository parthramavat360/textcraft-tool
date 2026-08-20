/**
 * UUID & ID Generator — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var out = document.getElementById('tc-uid-output');
    var generateBtn = document.getElementById('tc-uid-generate');
    if (!out || !generateBtn) return;

    var currentType = 'uuid_v4';

    document.querySelectorAll('.tctp-modes[data-group="uid-type"] .tctp-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            TCTP.activateBtn(btn);
            currentType = btn.getAttribute('data-val');
            var nanoidRow = document.getElementById('tc-uid-nanoid-len');
            if (nanoidRow) nanoidRow.style.display = currentType === 'nanoid' ? '' : 'none';
        });
    });

    var nanoidRange = document.getElementById('tc-uid-nanoid-range');
    var nanoidVal = document.getElementById('tc-uid-nanoid-len-val');
    if (nanoidRange && nanoidVal) {
        nanoidRange.addEventListener('input', function () {
            nanoidVal.textContent = nanoidRange.value;
        });
    }

    function randomByte() {
        var arr = new Uint8Array(1);
        crypto.getRandomValues(arr);
        return arr[0];
    }

    function randomBytes(n) {
        var arr = new Uint8Array(n);
        crypto.getRandomValues(arr);
        return arr;
    }

    function uuidV4() {
        var bytes = randomBytes(16);
        bytes[6] = (bytes[6] & 0x0f) | 0x40;
        bytes[8] = (bytes[8] & 0x3f) | 0x80;
        var hex = Array.prototype.map.call(bytes, function (b) {
            return b.toString(16).padStart(2, '0');
        }).join('');
        return (
            hex.slice(0, 8) + '-' +
            hex.slice(8, 12) + '-' +
            hex.slice(12, 16) + '-' +
            hex.slice(16, 20) + '-' +
            hex.slice(20)
        );
    }

    function uuidV1() {
        var now = Date.now();
        var timeLow = now & 0xffffffff;
        var timeMid = (now / 0x100000000) & 0xffff;
        var timeHi = ((now / 0x1000000000000) & 0x0fff) | 0x1000;
        var clock = randomBytes(2);
        clock[0] = (clock[0] & 0x3f) | 0x80;
        var node = randomBytes(6);

        function hex32(n) { return (n >>> 0).toString(16).padStart(8, '0'); }
        function hex16(n) { return (n & 0xffff).toString(16).padStart(4, '0'); }

        return (
            hex32(timeLow) + '-' +
            hex16(timeMid) + '-' +
            hex16(timeHi) + '-' +
            clock[0].toString(16).padStart(2, '0') + clock[1].toString(16).padStart(2, '0') + '-' +
            Array.prototype.map.call(node, function (b) { return b.toString(16).padStart(2, '0'); }).join('')
        );
    }

    var ULID_CHARS = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';

    function ulid() {
        var now = Date.now();
        var timePart = '';
        var t = now;
        for (var i = 0; i < 10; i++) {
            timePart = ULID_CHARS[t % 32] + timePart;
            t = Math.floor(t / 32);
        }
        var randPart = '';
        var bytes = randomBytes(10);
        for (var i = 0; i < 16; i++) {
            randPart += ULID_CHARS[bytes[i % bytes.length] % 32];
        }
        return timePart + randPart;
    }

    var NANO_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    function nanoid(length) {
        length = length || 21;
        var bytes = randomBytes(length);
        var id = '';
        for (var i = 0; i < length; i++) {
            id += NANO_CHARS[bytes[i] % NANO_CHARS.length];
        }
        return id;
    }

    function generateOne() {
        switch (currentType) {
            case 'uuid_v4': return uuidV4();
            case 'uuid_v1': return uuidV1();
            case 'ulid':    return ulid();
            case 'nanoid':
                var len = nanoidRange ? parseInt(nanoidRange.value) || 21 : 21;
                return nanoid(len);
            default: return uuidV4();
        }
    }

    generateBtn.addEventListener('click', function () {
        var count = Math.max(1, Math.min(1000, parseInt(document.getElementById('tc-uid-count').value) || 10));
        var uppercase = document.getElementById('tc-uid-uppercase');
        var noDash = document.getElementById('tc-uid-no-dash');
        var doUpper = uppercase && uppercase.checked;
        var doNoDash = noDash && noDash.checked;

        var ids = [];
        for (var i = 0; i < count; i++) {
            var id = generateOne();
            if (doUpper) id = id.toUpperCase();
            if (doNoDash) id = id.replace(/-/g, '');
            ids.push(id);
        }

        out.value = ids.join('\n');
        TCTP.toast(count + ' ID(s) generated!');
    });

    document.getElementById('tc-uid-copy').addEventListener('click', function () {
        TCTP.copyText(out.value, 'IDs');
    });

})();
