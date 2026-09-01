/**
 * Duplicate Word Finder — Tool JS
 *
 * Mode cards, toggles, preview tabs, copy, result panel stats.
 *
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var PREFIX = 'tc-dw-';
    var duplicateText = '';

    var inp = document.getElementById(PREFIX + 'input');
    if (!inp) return;

    var origPreview = document.getElementById(PREFIX + 'preview-orig');
    var resultPreview = document.getElementById(PREFIX + 'preview-result');
    var elTags = document.getElementById(PREFIX + 'tags');
    var elFreq = document.getElementById(PREFIX + 'freq');
    var customWrap = document.querySelector('.tc-dw-custom-wrap');
    var ignoreInput = document.getElementById(PREFIX + 'ignore');

    var COMMON_WORDS = { 'the':1,'a':1,'an':1,'and':1,'or':1,'but':1,'in':1,'on':1,'at':1,'to':1,'for':1,'of':1,'with':1,'by':1,'is':1,'it':1,'as':1,'was':1,'be':1,'are':1,'this':1,'that':1,'from':1,'if':1,'not':1,'so':1,'no':1,'do':1,'we':1,'he':1,'she':1,'me':1,'my':1,'our':1,'us':1,'you':1,'your':1,'they':1,'them':1,'his':1,'her':1,'has':1,'had':1,'have':1,'been':1,'will':1,'would':1,'can':1,'could':1,'may':1,'might':1,'shall':1,'should':1,'very':1,'just':1,'than':1,'then':1,'also':1,'when':1,'what':1,'how':1,'all':1,'each':1,'its':1,'more':1,'most':1,'other':1,'some':1,'such':1,'only':1,'into':1,'over':1,'after':1,'before':1,'between':1,'through':1,'during':1,'about':1,'up':1,'out':1,'off':1,'down':1 };

    /* ── Mode cards ──────────────────────────────────────────── */
    document.querySelectorAll('.tc-dw-modes .tc-rsz-mode-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.tc-dw-modes .tc-rsz-mode-card').forEach(function (c) { c.classList.remove('sel'); });
            card.classList.add('sel');
            var val = card.getAttribute('data-val');
            if (val === 'custom') {
                if (customWrap) customWrap.style.display = '';
            } else {
                if (customWrap) customWrap.style.display = 'none';
            }
        });
    });

    function getSelectedMode() {
        var s = document.querySelector('.tc-dw-modes .tc-rsz-mode-card.sel');
        return s ? s.getAttribute('data-val') : 'all';
    }

    /* ── Stats ─────────────────────────────────────────────── */
    function setStat(id, v) {
        var el = document.getElementById(id);
        if (el) el.textContent = v;
    }

    function updateStats(text) {
        var s = TCTP.getStats(text);
        setStat(PREFIX + 'chars', s.chars.toLocaleString());
    }

    /* ── Analyze ────────────────────────────────────────────── */
    function analyze() {
        var text = inp.value;
        var mode = getSelectedMode();
        var caseCb = document.getElementById(PREFIX + 'case');
        var minCb = document.getElementById(PREFIX + 'min');
        var cs = caseCb && caseCb.checked;
        var minLen = minCb && minCb.checked ? 2 : 1;

        if (!text.trim()) {
            if (elTags) elTags.innerHTML = '';
            if (elFreq) elFreq.innerHTML = '';
            setStat(PREFIX + 'total', '0');
            setStat(PREFIX + 'unique', '0');
            setStat(PREFIX + 'duplicates', '0');
            if (origPreview) origPreview.value = '';
            if (resultPreview) resultPreview.value = '';
            duplicateText = '';
            return null;
        }

        var words = text.match(/[\w']+/g) || [];

        var ignoreSet = {};
        if (mode === 'content') {
            Object.keys(COMMON_WORDS).forEach(function (k) { ignoreSet[k] = 1; });
        } else if (mode === 'custom' && ignoreInput && ignoreInput.value.trim()) {
            ignoreInput.value.split(',').forEach(function (w) {
                var trimmed = w.trim().toLowerCase();
                if (trimmed) ignoreSet[trimmed] = 1;
            });
        }

        var freq = {};
        var maxFreq = 0;
        words.forEach(function (w) {
            var key = cs ? w : w.toLowerCase();
            if (key.length < minLen) return;
            if (ignoreSet[key]) return;
            freq[key] = (freq[key] || 0) + 1;
            if (freq[key] > maxFreq) maxFreq = freq[key];
        });

        var duplicates = {};
        Object.keys(freq).forEach(function (k) {
            if (freq[k] > 1) duplicates[k] = freq[k];
        });

        var dupCount = Object.keys(duplicates).length;
        var uniqueCount = Object.keys(freq).length;

        setStat(PREFIX + 'total', words.length.toLocaleString());
        setStat(PREFIX + 'unique', uniqueCount.toLocaleString());
        setStat(PREFIX + 'duplicates', dupCount.toLocaleString());

        var tagsHtml = '';
        Object.keys(duplicates).sort(function (a, b) { return duplicates[b] - duplicates[a]; }).forEach(function (w) {
            tagsHtml += '<span class="tc-dup-tag">' + w + ' <small>x' + duplicates[w] + '</small></span>';
        });
        if (elTags) elTags.innerHTML = tagsHtml || '<span class="tc-dup-none">No duplicates found</span>';

        var barsHtml = '';
        Object.keys(freq).sort(function (a, b) { return freq[b] - freq[a]; }).slice(0, 20).forEach(function (w) {
            var pct = maxFreq ? (freq[w] / maxFreq * 100) : 0;
            barsHtml += '<div class="tc-freq-bar-row"><span class="tc-freq-word">' + w + '</span><div class="tc-freq-bar-track"><div class="tc-freq-bar-fill" style="width:' + pct + '%"></div></div><span class="tc-freq-num">' + freq[w] + '</span></div>';
        });
        if (elFreq) elFreq.innerHTML = barsHtml;

        var dupList = Object.keys(duplicates).sort(function (a, b) { return duplicates[b] - duplicates[a]; }).map(function (w) { return w + ' (x' + duplicates[w] + ')'; }).join('\n');
        duplicateText = dupList || 'No duplicates found';

        if (origPreview) origPreview.value = text;
        if (resultPreview) resultPreview.value = duplicateText;

        TCTP.updateResultPanel(
            text.length.toLocaleString() + ' chars',
            words.length.toLocaleString() + ' words',
            dupCount > 0 ? dupCount.toLocaleString() + ' dups' : '0%',
            'Done'
        );

        return duplicates;
    }

    /* ── Live input stats + original preview ────────────────── */
    var analyzeTimer;
    inp.addEventListener('input', function () {
        updateStats(inp.value);
        if (origPreview) origPreview.value = inp.value;
        clearTimeout(analyzeTimer);
        analyzeTimer = setTimeout(function () { analyze(); }, 300);
    });

    /* ── Find button ────────────────────────────────────────── */
    var findBtn = document.getElementById(PREFIX + 'find');
    if (findBtn) {
        findBtn.addEventListener('click', function () {
            TCTP.showProgress(PREFIX + 'bar');
            TCTP.setProgress(PREFIX + 'bar', 50, 'Scanning...');

            setTimeout(function () {
                var duplicates = analyze();
                TCTP.setProgress(PREFIX + 'bar', 100, 'Done!');
                TCTP.hideProgress(PREFIX + 'bar');
                TCTP.switchToResultTab();

                var count = duplicates ? Object.keys(duplicates).length : 0;
                TCTP.toast(count > 0
                    ? count + ' duplicate word' + (count === 1 ? '' : 's') + ' found!'
                    : 'No duplicates found.', count > 0 ? '\u2705' : '\u26A0\uFE0F');
            }, 80);
        });
    }

    /* ── Copy ───────────────────────────────────────────────── */
    var copyBtn = document.getElementById(PREFIX + 'copy');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            TCTP.copyText(duplicateText, 'Results');
        });
    }

    /* ── Clear all ──────────────────────────────────────────── */
    var clearBtn = document.getElementById(PREFIX + 'clear');
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            inp.value = '';
            duplicateText = '';

            var modeCards = document.querySelectorAll('.tc-dw-modes .tc-rsz-mode-card');
            modeCards.forEach(function (c) { c.classList.remove('sel'); });
            if (modeCards[0]) modeCards[0].classList.add('sel');
            if (customWrap) customWrap.style.display = 'none';
            if (ignoreInput) ignoreInput.value = '';

            var caseCb = document.getElementById(PREFIX + 'case');
            var minCb = document.getElementById(PREFIX + 'min');
            if (caseCb) caseCb.checked = false;
            if (minCb) minCb.checked = false;

            if (origPreview) origPreview.value = '';
            if (resultPreview) resultPreview.value = '';
            if (elTags) elTags.innerHTML = '';
            if (elFreq) elFreq.innerHTML = '';

            updateStats('');
            setStat(PREFIX + 'total', '0');
            setStat(PREFIX + 'unique', '0');
            setStat(PREFIX + 'duplicates', '0');
            TCTP.updateResultPanel('\u2014', '\u2014', '\u2014', 'Idle');
            TCTP.switchToOriginalTab();
            TCTP.toast('Cleared.', '\uD83E\uDDF9');
        });
    }

    /* ── Init ───────────────────────────────────────────────── */
    updateStats('');

})();
