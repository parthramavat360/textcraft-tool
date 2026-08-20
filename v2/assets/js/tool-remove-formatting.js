(function(){ 'use strict';
var inp = document.getElementById('tc-rf-input'); if (!inp) return;
var out = document.getElementById('tc-rf-output');
var btnConvert = document.getElementById('tc-rf-convert');
var btnCopy = document.getElementById('tc-rf-copy');
var btnClear = document.getElementById('tc-rf-clear');
var chkHtml = document.getElementById('tc-rf-html');
var chkEntities = document.getElementById('tc-rf-entities');
var chkScripts = document.getElementById('tc-rf-scripts');
var chkComments = document.getElementById('tc-rf-comments');

btnConvert.addEventListener('click', function(){
    var text = inp.value;
    if(chkComments && chkComments.checked){
        text = text.replace(/<!--[\s\S]*?-->/g, '');
    }
    if(chkScripts && chkScripts.checked){
        text = text.replace(/<script[\s\S]*?<\/script>/gi, '');
        text = text.replace(/<style[\s\S]*?<\/style>/gi, '');
    }
    if(chkHtml && chkHtml.checked){
        text = text.replace(/<[^>]+>/g, '');
    }
    if(chkEntities && chkEntities.checked){
        var ta = document.createElement('textarea');
        ta.innerHTML = text;
        text = ta.value;
        text = text.replace(/&#(\d+);/g, function(m, code){ return String.fromCharCode(parseInt(code, 10)); });
        text = text.replace(/&#x([0-9a-f]+);/gi, function(m, code){ return String.fromCharCode(parseInt(code, 16)); });
    }
    text = text.trim();
    out.value = text;
    if(TCTP && TCTP.activateBtn) TCTP.activateBtn(btnCopy);
});

btnCopy.addEventListener('click', function(){
    if(TCTP && TCTP.copyText) TCTP.copyText(out.value);
});
btnClear.addEventListener('click', function(){
    inp.value = '';
    out.value = '';
});
})();