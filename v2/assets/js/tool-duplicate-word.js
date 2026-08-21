/**
 * Duplicate Word Finder — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var inp = document.getElementById('tc-dw-input');
        var btnFind = document.getElementById('tc-dw-find');
        if (!inp || !btnFind || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var elTags = document.getElementById('tc-dw-tags');
        var elFreq = document.getElementById('tc-dw-freq');
        var statusEl = document.getElementById('tc-dw-status');

        var commonWords = { 'the':1,'a':1,'an':1,'and':1,'or':1,'but':1,'in':1,'on':1,'at':1,'to':1,'for':1,'of':1,'with':1,'by':1,'is':1,'it':1,'as':1,'was':1,'be':1,'are':1,'this':1,'that':1,'from':1,'if':1,'not':1,'so':1,'no':1,'do':1,'we':1,'he':1,'she':1,'me':1,'my':1,'our':1,'us':1,'you':1,'your':1,'they':1,'them':1,'his':1,'her':1 };

        function setStat(ids, val) {
            for (var i = 0; i < ids.length; i++) {
                var el = document.getElementById(ids[i]);
                if (el) { el.textContent = val; return; }
            }
        }

        function analyze() {
            var text = inp.value;
            if (!text.trim()) {
                if (elTags) elTags.innerHTML = '';
                if (elFreq) elFreq.innerHTML = '';
                setStat(['tc-dw-stats-duplicates', 'tc-dw-stat-duplicates'], '0');
                setStat(['tc-dw-stats-total', 'tc-dw-stat-total'], '0');
                if (statusEl) statusEl.textContent = '';
                return null;
            }

            TCTP.showProgress('tc-dw-bar');
            TCTP.setProgress('tc-dw-bar', 50, 'Scanning...');

            var words = text.match(/[\w']+/g) || [];
            var caseInsensitive = false;
            var ignoreCommon = true;

            var freq = {};
            var maxFreq = 0;
            words.forEach(function (w) {
                var key = caseInsensitive ? w.toLowerCase() : w;
                freq[key] = (freq[key] || 0) + 1;
                if (freq[key] > maxFreq) maxFreq = freq[key];
            });

            var duplicates = {};
            Object.keys(freq).forEach(function (k) {
                if (freq[k] > 1) {
                    if (ignoreCommon && commonWords[k]) return;
                    duplicates[k] = freq[k];
                }
            });

            var dupCount = Object.keys(duplicates).length;
            setStat(['tc-dw-stats-duplicates', 'tc-dw-stat-duplicates'], dupCount.toLocaleString());
            setStat(['tc-dw-stats-total', 'tc-dw-stat-total'], words.length.toLocaleString());

            var tagsHtml = '';
            Object.keys(duplicates).forEach(function (w) {
                tagsHtml += '<span class="tc-dup-tag">' + w + ' <small>x' + duplicates[w] + '</small></span>';
            });
            if (elTags) elTags.innerHTML = tagsHtml || '<span class="tc-dup-none">No duplicates found</span>';

            var barsHtml = '';
            Object.keys(freq).sort(function (a, b) { return freq[b] - freq[a]; }).forEach(function (w) {
                var pct = maxFreq ? (freq[w] / maxFreq * 100) : 0;
                barsHtml += '<div class="tc-freq-bar-row"><span class="tc-freq-word">' + w + '</span><div class="tc-freq-bar-track"><div class="tc-freq-bar-fill" style="width:' + pct + '%"></div></div><span class="tc-freq-num">' + freq[w] + '</span></div>';
            });
            if (elFreq) elFreq.innerHTML = barsHtml;

            if (statusEl) statusEl.textContent = dupCount > 0
                ? dupCount + ' duplicate word' + (dupCount === 1 ? '' : 's') + ' found.'
                : 'No duplicate words found.';

            TCTP.setProgress('tc-dw-bar', 100, 'Done!');
            TCTP.hideProgress('tc-dw-bar');

            return duplicates;
        }

        btnFind.addEventListener('click', function () {
            var duplicates = analyze();
            if (!duplicates) {
                TCTP.toast('Please enter some text first.', '\u26A0\uFE0F');
                return;
            }
            var count = Object.keys(duplicates).length;
            TCTP.toast(count > 0
                ? count + ' duplicate word' + (count === 1 ? '' : 's') + ' found!'
                : 'No duplicates found.', count > 0 ? '\u2705' : '\u26A0\uFE0F');
        });

        inp.addEventListener('input', analyze);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Re-init after Elementor AJAX re-render
    new MutationObserver(function () { init(); })
        .observe(document.documentElement, { childList: true, subtree: true });
})();
