/**
 * Find and Replace — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function () {
    'use strict';

    var inp = document.getElementById('tc-fr-input');
    var out = document.getElementById('tc-fr-output');
    if (!inp) return;

    document.getElementById('tc-fr-do').addEventListener('click', function () {
        var errEl = document.getElementById('tc-fr-err');
        errEl.style.display = 'none';

        var findStr = document.getElementById('tc-fr-find').value;
        if (!findStr) {
            errEl.textContent = 'Please enter a search term or pattern to find.';
            errEl.style.display = 'block';
            return;
        }

        var repStr = document.getElementById('tc-fr-replace').value;
        var cs = document.getElementById('tc-fr-case').checked;
        var whole = document.getElementById('tc-fr-whole').checked;
        var regex = document.getElementById('tc-fr-regex').checked;
        var all = document.getElementById('tc-fr-all').checked;

        var src = regex ? findStr : findStr.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        if (whole && !regex) src = '\\b' + src + '\\b';

        var flags = (cs ? '' : 'i') + (all ? 'g' : '');

        try {
            var re = new RegExp(src, flags);
            var count = 0;
            var result = inp.value.replace(re, function () { count++; return repStr; });
            out.value = result;
            document.getElementById('tc-fr-matches').textContent = count;
            document.getElementById('tc-fr-replaced').textContent = count;
            TCTP.toast('Found ' + count + ' match(es) and replaced.');
        } catch (e) {
            errEl.textContent = 'Invalid regex: ' + e.message;
            errEl.style.display = 'block';
        }
    });

    document.getElementById('tc-fr-copy').addEventListener('click', function () {
        TCTP.copyText(out.value, 'Result');
    });

})();