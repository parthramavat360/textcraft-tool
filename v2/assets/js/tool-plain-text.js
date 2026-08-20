(function(){ 'use strict';
var inp = document.getElementById('tc-pt-input'); if (!inp) return;
var out = document.getElementById('tc-pt-output');
var btnConvert = document.getElementById('tc-pt-convert');
var btnCopy = document.getElementById('tc-pt-copy');
var btnClear = document.getElementById('tc-pt-clear');
var chkHtml = document.getElementById('tc-pt-html');
var chkEntities = document.getElementById('tc-pt-entities');
var chkBlank = document.getElementById('tc-pt-blank-lines');
var chkTrim = document.getElementById('tc-pt-trim-spaces');
var chkUnicode = document.getElementById('tc-pt-unicode');
var elTags = document.getElementById('tc-pt-tags');
var elBefore = document.getElementById('tc-pt-before');
var elAfter = document.getElementById('tc-pt-after');

btnConvert.addEventListener('click', function(){
    var text = inp.value;
    var tagsBefore = 0;
    if(chkHtml && chkHtml.checked){
        var before = text.length;
        text = text.replace(/<[^>]+>/g, '');
        tagsBefore = (before - text.length);
    }
    if(chkEntities && chkEntities.checked){
        var ta = document.createElement('textarea');
        ta.innerHTML = text;
        text = ta.value;
        text = text.replace(/&#(\d+);/g, function(m, code){ return String.fromCharCode(parseInt(code, 10)); });
        text = text.replace(/&#x([0-9a-f]+);/gi, function(m, code){ return String.fromCharCode(parseInt(code, 16)); });
    }
    if(chkUnicode && chkUnicode.checked){
        text = text.replace(/[\u200B-\u200D\uFEFF]/g, '');
        text = text.replace(/\u00A0/g, ' ');
        text = text.replace(/[\u2000-\u200A\u202F\u205F\u3000]/g, ' ');
    }
    if(chkBlank && chkBlank.checked){
        text = text.replace(/([ \t]*\n){2,}/g, '\n');
    }
    if(chkTrim && chkTrim.checked){
        text = text.replace(/[ \t]+/g, ' ');
        text = text.split('\n').map(function(l){ return l.trim(); }).join('\n');
    }
    if(chkBlank && chkBlank.checked){
        text = text.replace(/\n{3,}/g, '\n\n');
    }
    text = text.trim();
    out.value = text;
    if(elTags) elTags.textContent = tagsBefore;
    if(elBefore) elBefore.textContent = inp.value.length;
    if(elAfter) elAfter.textContent = text.length;
    if(TCTP && TCTP.activateBtn) TCTP.activateBtn(btnCopy);
});

btnCopy.addEventListener('click', function(){
    if(TCTP && TCTP.copyText) TCTP.copyText(out.value);
});
btnClear.addEventListener('click', function(){
    inp.value = '';
    out.value = '';
    if(elTags) elTags.textContent = '0';
    if(elBefore) elBefore.textContent = '0';
    if(elAfter) elAfter.textContent = '0';
});
})();