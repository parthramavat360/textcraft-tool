(function(){ 'use strict';
var inp = document.getElementById('tc-tc-input'); if (!inp) return;
var out = document.getElementById('tc-tc-output');
var btnConvert = document.getElementById('tc-tc-convert');
var btnCopy = document.getElementById('tc-tc-copy');
var elWords = document.getElementById('tc-tc-words');
var elChars = document.getElementById('tc-tc-chars');

var minorWords = {'a':1,'an':1,'the':1,'and':1,'but':1,'or':1,'for':1,'nor':1,'on':1,'at':1,'to':1,'by':1,'in':1,'of':1,'up':1,'as':1,'vs':1,'via':1};

btnConvert.addEventListener('click', function(){
    var text = inp.value;
    var result = text.toLowerCase().replace(/\b\w+/g, function(word, offset){
        if(offset === 0) return word.charAt(0).toUpperCase() + word.slice(1);
        if(minorWords[word]) return word;
        return word.charAt(0).toUpperCase() + word.slice(1);
    });
    result = result.replace(/(^|[:\-—\s])(&#\d+;|[\u00C0-\u024F]+|[a-z]+)/g, function(m, pre, word){
        return pre + word.charAt(0).toUpperCase() + word.slice(1);
    });
    out.value = result;
    if(elWords) elWords.textContent = result.split(/\s+/).filter(Boolean).length;
    if(elChars) elChars.textContent = result.length;
    if(TCTP && TCTP.activateBtn) TCTP.activateBtn(btnCopy);
});

btnCopy.addEventListener('click', function(){
    if(TCTP && TCTP.copyText) TCTP.copyText(out.value);
});
})();