(function(){ 'use strict';
var inp = document.getElementById('tc-dl-input'); if (!inp) return;
var out = document.getElementById('tc-dl-output');
var btnRemove = document.getElementById('tc-dl-remove');
var btnCopy = document.getElementById('tc-dl-copy');
var btnClear = document.getElementById('tc-dl-clear');
var chkCase = document.getElementById('tc-dl-case');
var chkTrim = document.getElementById('tc-dl-trim');
var chkBlank = document.getElementById('tc-dl-blank');
var chkSort = document.getElementById('tc-dl-sort');
var elTotal = document.getElementById('tc-dl-total');
var elUnique = document.getElementById('tc-dl-unique');
var elRemoved = document.getElementById('tc-dl-removed');

btnRemove.addEventListener('click', function(){
    var lines = inp.value.split(/\r?\n/);
    var doTrim = chkTrim && chkTrim.checked;
    var caseInsensitive = chkCase && chkCase.checked;
    var removeBlanks = chkBlank && chkBlank.checked;
    var doSort = chkSort && chkSort.checked;

    if(doTrim) lines = lines.map(function(l){ return l.trim(); });
    if(removeBlanks) lines = lines.filter(function(l){ return l !== ''; });

    var total = lines.length;
    var seen = {};
    var unique = [];
    lines.forEach(function(line){
        var key = caseInsensitive ? line.toLowerCase() : line;
        if(!seen[key]){
            seen[key] = 1;
            unique.push(line);
        }
    });

    if(doSort) unique.sort(function(a,b){ return a.localeCompare(b); });

    var removed = total - unique.length;
    out.value = unique.join('\n');
    if(elTotal) elTotal.textContent = total;
    if(elUnique) elUnique.textContent = unique.length;
    if(elRemoved) elRemoved.textContent = removed;
    if(TCTP && TCTP.activateBtn) TCTP.activateBtn(btnCopy);
});

btnCopy.addEventListener('click', function(){
    if(TCTP && TCTP.copyText) TCTP.copyText(out.value);
});
btnClear.addEventListener('click', function(){
    inp.value = '';
    out.value = '';
    if(elTotal) elTotal.textContent = '0';
    if(elUnique) elUnique.textContent = '0';
    if(elRemoved) elRemoved.textContent = '0';
});
})();