(function(){ 'use strict';
var inp = document.getElementById('tc-cr-input'); if (!inp) return;
var charsInp = document.getElementById('tc-cr-chars');
var btnRemove = document.getElementById('tc-cr-remove');
var btnCopy = document.getElementById('tc-cr-copy');
var btnClear = document.getElementById('tc-cr-clear');
var chkCase = document.getElementById('tc-cr-case-sensitive');
var elRemoved = document.getElementById('tc-cr-stat-removed');
var elOrig = document.getElementById('tc-cr-stat-orig');
var elResult = document.getElementById('tc-cr-stat-result');

var presets = document.querySelectorAll('.tc-cr-preset-btn[data-chars]');
presets.forEach(function(btn){
    btn.addEventListener('click', function(){
        charsInp.value = btn.getAttribute('data-chars');
        btnRemove.click();
    });
});

function doRemove(){
    var text = inp.value;
    var chars = charsInp.value;
    if(!chars){ if(TCTP && TCTP.toast) TCTP.toast('Enter characters to remove', 'error'); return; }
    var flags = chkCase && chkCase.checked ? 'g' : 'gi';
    var origLen = text.length;
    var pattern = '[' + chars.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&') + ']';
    var result = text.replace(new RegExp(pattern, flags), '');
    var removed = origLen - result.length;
    inp.value = result;
    if(elRemoved) elRemoved.textContent = removed;
    if(elOrig) elOrig.textContent = origLen;
    if(elResult) elResult.textContent = result.length;
}

btnRemove.addEventListener('click', doRemove);
btnCopy.addEventListener('click', function(){
    if(TCTP && TCTP.copyText) TCTP.copyText(inp.value);
});
btnClear.addEventListener('click', function(){
    inp.value = '';
    charsInp.value = '';
    if(elRemoved) elRemoved.textContent = '0';
    if(elOrig) elOrig.textContent = '0';
    if(elResult) elResult.textContent = '0';
});
})();