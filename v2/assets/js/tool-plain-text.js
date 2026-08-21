/**
 * Plain Text Converter — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    function init() {
        var inp = document.getElementById('tc-pt-input');
        var out = document.getElementById('tc-pt-output');
        var btnConvert = document.getElementById('tc-pt-convert');
        if (!inp || !btnConvert || inp.dataset.tcInit) return;
        inp.dataset.tcInit = '1';

        var statusEl = document.getElementById('tc-pt-status');

        // Option defaults mirror the Elementor controls
        // (pt_strip_html=yes, pt_decode_entities=yes, pt_remove_blanks='',
        //  pt_trim_spaces=yes, pt_normalize_unicode='').
        var stripHtml = true;
        var decodeEntities = true;
        var removeBlanks = false;
        var trimSpaces = true;
        var normalizeUnicode = false;

        function setStat(ids, val) {
            for (var i = 0; i < ids.length; i++) {
                var el = document.getElementById(ids[i]);
                if (el) { el.textContent = val; return; }
            }
        }

        function convert() {
            var text = inp.value;
            if (!text.trim()) {
                TCTP.toast('Paste some HTML or rich text first.', '\u26A0\uFE0F');
                return;
            }

            TCTP.showProgress('tc-pt-bar');
            TCTP.setProgress('tc-pt-bar', 40, 'Converting...');

            var tagsRemoved = 0;

            if (stripHtml) {
                var before = text.length;
                text = text.replace(/<[^>]+>/g, '');
                tagsRemoved = before - text.length;
            }
            if (decodeEntities) {
                var ta = document.createElement('textarea');
                ta.innerHTML = text;
                text = ta.value;
                text = text.replace(/&#(\d+);/g, function (m, code) { return String.fromCharCode(parseInt(code, 10)); });
                text = text.replace(/&#x([0-9a-f]+);/gi, function (m, code) { return String.fromCharCode(parseInt(code, 16)); });
            }
            if (normalizeUnicode) {
                text = text.replace(/[\u200B-\u200D\uFEFF]/g, '');
                text = text.replace(/\u00A0/g, ' ');
                text = text.replace(/[\u2000-\u200A\u202F\u205F\u3000]/g, ' ');
            }
            if (removeBlanks) {
                text = text.replace(/([ \t]*\n){2,}/g, '\n');
            }
            if (trimSpaces) {
                text = text.replace(/[ \t]+/g, ' ');
                text = text.split('\n').map(function (l) { return l.trim(); }).join('\n');
            }
            if (removeBlanks) {
                text = text.replace(/\n{3,}/g, '\n\n');
            }
            text = text.trim();

            if (out) out.value = text;
            setStat(['tc-pt-stats-tags_removed', 'tc-pt-stats-tags-removed', 'tc-pt-stat-tags'], tagsRemoved.toLocaleString());
            setStat(['tc-pt-stats-before', 'tc-pt-stat-before'], inp.value.length.toLocaleString());
            setStat(['tc-pt-stats-after', 'tc-pt-stat-after'], text.length.toLocaleString());
            if (statusEl) statusEl.textContent = 'Converted to plain text.';

            TCTP.setProgress('tc-pt-bar', 100, 'Done!');
            TCTP.hideProgress('tc-pt-bar');
            TCTP.toast('Converted to plain text!');
        }

        btnConvert.addEventListener('click', convert);

        // Drop zone + file row
        function onFile(file) {
            var reader = new FileReader();
            reader.onload = function () {
                inp.value = String(reader.result || '');
                TCTP.showFileRow('tc-pt-file', file);
                convert();
            };
            reader.readAsText(file);
        }
        TCTP.initDropZone('tc-pt-drop', 'tc-pt-drop-input', onFile);

        var fileRow = document.getElementById('tc-pt-file');
        if (fileRow) {
            var closeBtn = fileRow.querySelector('.tc-x');
            if (closeBtn) {
                closeBtn.addEventListener('click', function () {
                    TCTP.hideFileRow('tc-pt-file');
                });
            }
        }
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
