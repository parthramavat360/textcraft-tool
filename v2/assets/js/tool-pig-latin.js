(function(){ 'use strict';
var inp = document.getElementById('tc-pl-input'); if (!inp) return;
var out = document.getElementById('tc-pl-output');
var btnTranslate = document.getElementById('tc-pl-translate');
var btnCopy = document.getElementById('tc-pl-copy');
var btnClear = document.getElementById('tc-pl-clear');
var vowelRadio = document.getElementById('tc-pl-vowel');

function pigLatin(word){
    if(!word) return word;
    var isUpper = word[0] === word[0].toUpperCase();
    var lower = word.toLowerCase();
    var vowels = 'aeiou';
    if(vowels.indexOf(lower[0]) !== -1){
        return isUpper ? (lower + 'yay').replace(/^\w/, function(c){ return c.toUpperCase(); }) : lower + 'yay';
    }
    var cluster = '';
    for(var i = 0; i < lower.length; i++){
        if(vowels.indexOf(lower[i]) !== -1) break;
        cluster += lower[i];
    }
    var rest = lower.slice(cluster.length);
    var result = rest + cluster + 'ay';
    if(isUpper) result = result.charAt(0).toUpperCase() + result.slice(1);
    return result;
}

btnTranslate.addEventListener('click', function(){
    var text = inp.value;
    var result = text.replace(/\b[a-zA-Z']+\b/g, function(word){ return pigLatin(word); });
    out.value = result;
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