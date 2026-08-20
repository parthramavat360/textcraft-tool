(function(){ 'use strict';
var inp = document.getElementById('tc-ed-input'); if (!inp) return;
var out = document.getElementById('tc-ed-output');
var btnRemove = document.getElementById('tc-ed-remove');
var btnCopy = document.getElementById('tc-ed-copy');
var btnClear = document.getElementById('tc-ed-clear');
var chkEm = document.getElementById('tc-ed-opt-em');
var chkEn = document.getElementById('tc-ed-opt-en');
var chkHyphen = document.getElementById('tc-ed-opt-hyphen');
var elEm = document.getElementById('tc-ed-stat-em');
var elEn = document.getElementById('tc-ed-stat-en');
var elTotal = document.getElementById('tc-ed-stat-total');

document.querySelectorAll('[data-val]').forEach(function(btn){
    btn.addEventListener('click', function(){
        var val = btn.getAttribute('data-val');
        var repl = btn.hasAttribute('data-replace') ? btn.getAttribute('data-replace') : '';
        out.value = inp.value.split(val).join(repl);
        countStats();
        if(TCTP && TCTP.activateBtn) TCTP.activateBtn(btnCopy);
    });
});

btnRemove.addEventListener('click', function(){
    var text = inp.value;
    var emCount = 0, enCount = 0;
    if(chkEm && chkEm.checked){ emCount = (text.match(/\u2014/g) || []).length; text = text.split('\u2014').join(' '); }
    if(chkEn && chkEn.checked){ enCount = (text.match(/\u2013/g) || []).length; text = text.split('\u2013').join(' '); }
    if(chkHyphen && chkHyphen.checked){ text = text.split(' \u2010').join(' ').split('\u2010 ').join(' ').split('\u2010').join('-'); }
    var totalReplaced = emCount + enCount;
    out.value = text;
    if(elEm) elEm.textContent = emCount;
    if(elEn) elEn.textContent = enCount;
    if(elTotal) elTotal.textContent = totalReplaced;
    if(TCTP && TCTP.activateBtn) TCTP.activateBtn(btnCopy);
});

function countStats(){
    var text = out.value || inp.value;
    var emCount = (text.match(/\u2014/g) || []).length;
    var enCount = (text.match(/\u2013/g) || []).length;
    if(elEm) elEm.textContent = emCount;
    if(elEn) elEn.textContent = enCount;
    if(elTotal) elTotal.textContent = emCount + enCount;
}

btnCopy.addEventListener('click', function(){
    if(TCTP && TCTP.copyText) TCTP.copyText(out.value);
});
btnClear.addEventListener('click', function(){
    inp.value = '';
    out.value = '';
    if(elEm) elEm.textContent = '0';
    if(elEn) elEn.textContent = '0';
    if(elTotal) elTotal.textContent = '0';
});
countStats();
})();