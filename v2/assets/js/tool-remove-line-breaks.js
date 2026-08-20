(function(){ 'use strict';
var inp = document.getElementById('tc-rlb-input'); if (!inp) return;
var out = document.getElementById('tc-rlb-output');
var btnConvert = document.getElementById('tc-rlb-convert');
var btnCopy = document.getElementById('tc-rlb-copy');

function getMode(){
    var checked = document.querySelector('input[name="tc-rlb-mode"]:checked');
    return checked ? checked.value : 'spaces';
}

btnConvert.addEventListener('click', function(){
    var text = inp.value;
    var mode = getMode();
    switch(mode){
        case 'spaces':
            text = text.replace(/\r?\n/g, ' ');
            break;
        case 'join':
            text = text.replace(/\r?\n/g, '');
            break;
        case 'paragraphs':
            text = text.replace(/\r?\n\r?\n+/g, '\n\n');
            text = text.replace(/\r?\n/g, ' ');
            text = text.replace(/  +/g, ' ');
            break;
    }
    text = text.replace(/ +/g, ' ').trim();
    out.value = text;
    if(TCTP && TCTP.activateBtn) TCTP.activateBtn(btnCopy);
});

btnCopy.addEventListener('click', function(){
    if(TCTP && TCTP.copyText) TCTP.copyText(out.value);
});
})();