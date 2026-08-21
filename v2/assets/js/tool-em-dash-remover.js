/**
 * Em Dash Remover — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function(){ 'use strict';
var inp = document.getElementById('tc-edr-input'); if (!inp) return;
var out = document.getElementById('tc-edr-output');
var btnRemove = document.getElementById('tc-edr-remove');
var statusEl = document.getElementById('tc-edr-status');
if (!btnRemove) return;

function setStat(ids, val){
    for (var i = 0; i < ids.length; i++) {
        var el = document.getElementById(ids[i]);
        if (el) { el.textContent = val; return; }
    }
}

btnRemove.addEventListener('click', function(){
    var text = inp.value;
    var emCount = 0, enCount = 0;
    if(text.indexOf('\u2014') !== -1){ emCount = (text.match(/\u2014/g) || []).length; text = text.split('\u2014').join(' '); }
    if(text.indexOf('\u2013') !== -1){ enCount = (text.match(/\u2013/g) || []).length; text = text.split('\u2013').join(' '); }
    var totalReplaced = emCount + enCount;
    if(out) out.value = text;
    setStat(['tc-edr-stats-em_count', 'tc-edr-stats-em-count', 'tc-edr-stat-em'], emCount);
    setStat(['tc-edr-stats-en_count', 'tc-edr-stats-en-count', 'tc-edr-stat-en'], enCount);
    setStat(['tc-edr-stats-total', 'tc-edr-stat-total'], totalReplaced);
    if(statusEl) statusEl.textContent = totalReplaced > 0
        ? 'Removed ' + totalReplaced + ' dash' + (totalReplaced === 1 ? '' : 'es') + '.'
        : 'No em or en dashes found.';
    TCTP.toast(totalReplaced > 0 ? totalReplaced + ' dash' + (totalReplaced === 1 ? '' : 'es') + ' removed!' : 'No dashes found.', totalReplaced > 0 ? '\u2705' : '\u26A0\uFE0F');
});
})();
