/**
 * Border Radius Generator — Tool JS
 * Visual CSS border-radius builder with live preview.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var tlSlider = document.getElementById('tc-br-tl');
    if (!tlSlider) return;

    var trSlider = document.getElementById('tc-br-tr');
    var brSlider = document.getElementById('tc-br-br');
    var blSlider = document.getElementById('tc-br-bl');
    var tlNum = document.getElementById('tc-br-tl-num');
    var trNum = document.getElementById('tc-br-tr-num');
    var brNum = document.getElementById('tc-br-br-num');
    var blNum = document.getElementById('tc-br-bl-num');
    var previewBox = document.getElementById('tc-br-preview-box');
    var codeOutput = document.getElementById('tc-br-code');
    var linkBtn = document.getElementById('tc-br-link-btn');

    var linked = true;

    var sliders = [tlSlider, trSlider, brSlider, blSlider];
    var nums = [tlNum, trNum, brNum, blNum];

    // ── Update ───────────────────────────────────────────────

    function getValues() {
        return [
            parseInt(tlSlider.value),
            parseInt(trSlider.value),
            parseInt(brSlider.value),
            parseInt(blSlider.value)
        ];
    }

    function updatePreview() {
        var v = getValues();
        var css = v[0] + 'px ' + v[1] + 'px ' + v[2] + 'px ' + v[3] + 'px';
        previewBox.style.borderRadius = css;
        codeOutput.value = 'border-radius: ' + css + ';';
    }

    function syncFromSlider(changedIdx) {
        if (linked) {
            var val = parseInt(sliders[changedIdx].value);
            sliders.forEach(function (s, i) {
                if (i !== changedIdx) s.value = val;
                nums[i].value = val;
            });
        } else {
            nums[changedIdx].value = sliders[changedIdx].value;
        }
        updatePreview();
    }

    function syncFromNum(changedIdx) {
        var val = parseInt(nums[changedIdx].value) || 0;
        val = Math.max(0, Math.min(999, val));
        if (linked) {
            sliders.forEach(function (s, i) {
                s.value = Math.min(val, 200);
                nums[i].value = val;
            });
        } else {
            sliders[changedIdx].value = Math.min(val, 200);
        }
        updatePreview();
    }

    // ── Slider events ────────────────────────────────────────

    sliders.forEach(function (s, i) {
        s.addEventListener('input', function () { syncFromSlider(i); });
    });

    nums.forEach(function (n, i) {
        n.addEventListener('input', function () { syncFromNum(i); });
    });

    // ── Link toggle ──────────────────────────────────────────

    if (linkBtn) {
        linkBtn.addEventListener('click', function () {
            linked = !linked;
            linkBtn.innerHTML = linked
                ? '<i class="fa-solid fa-link"></i> Linked'
                : '<i class="fa-solid fa-link-slash"></i> Unlinked';
            linkBtn.classList.toggle('tc-btn--ghost', !linked);
            linkBtn.classList.toggle('tc-btn--primary', linked);
            TCTP.toast(linked ? 'Corners linked' : 'Corners unlinked', linked ? '\ud83d\udd17' : '\u26d4\ufe0f');
        });
    }

    // ── Presets ──────────────────────────────────────────────

    document.querySelectorAll('.tc-br-preset-cards .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.tc-br-preset-cards .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            var tl = parseInt(card.getAttribute('data-tl')) || 0;
            var tr = parseInt(card.getAttribute('data-tr')) || 0;
            var br = parseInt(card.getAttribute('data-br')) || 0;
            var bl = parseInt(card.getAttribute('data-bl')) || 0;
            tlSlider.value = Math.min(tl, 200); tlNum.value = tl;
            trSlider.value = Math.min(tr, 200); trNum.value = tr;
            brSlider.value = Math.min(br, 200); brNum.value = br;
            blSlider.value = Math.min(bl, 200); blNum.value = bl;
            updatePreview();
        });
    });

    // ── Copy CSS ─────────────────────────────────────────────

    var copyBtn = document.getElementById('tc-br-copy-css');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            TCTP.copyText(codeOutput.value);
            TCTP.toast('CSS copied!', '\u2705');
        });
    }

    // ── Init ─────────────────────────────────────────────────

    updatePreview();
})();
