(function(){ 'use strict';
var inp = document.getElementById('tc-sc-input'); if (!inp) return;
var out = document.getElementById('tc-sc-output');
var btnConvert = document.getElementById('tc-sc-convert');
var btnCopy = document.getElementById('tc-sc-copy');
var optI = document.getElementById('tc-sc-opt-i');
var optAbbr = document.getElementById('tc-sc-opt-abbr');
var elWords = document.getElementById('tc-sc-words');
var elSents = document.getElementById('tc-sc-chars');

btnConvert.addEventListener('click', function(){
    var text = inp.value;
    var preserveAbbr = optAbbr && optAbbr.checked;
    var abbreviations = [];
    if(preserveAbbr){
        text.replace(/\b[A-Z]{2,}\b/g, function(m){ abbreviations.push(m); return '{{TCTP_ABBR_' + (abbreviations.length-1) + '}}'; });
        text.replace(/\b[A-Z][a-z]{1,3}\./g, function(m){ abbreviations.push(m); return '{{TCTP_ABBR_' + (abbreviations.length-1) + '}}'; });
    }
    var result = text.toLowerCase();
    result = result.replace(/(^|[.!?]\s+)([a-z])/g, function(m, sep, ch){ return sep + ch.toUpperCase(); });
    if(optI && optI.checked){
        result = result.replace(/\bi\b/g, 'I');
        result = result.replace(/\bI'm\b/g, function(){ return "I'm"; });
        result = result.replace(/\bI've\b/g, function(){ return "I've"; });
        result = result.replace(/\bI'll\b/g, function(){ return "I'll"; });
        result = result.replace(/\bI'd\b/g, function(){ return "I'd"; });
    }
    if(preserveAbbr){
        abbreviations.forEach(function(abbr, i){
            result = result.replace('{{TCTP_ABBR_' + i + '}}', abbr);
        });
    }
    out.value = result;
    if(elWords) elWords.textContent = result.split(/\s+/).filter(Boolean).length;
    if(elSents) elSents.textContent = result.length;
    if(TCTP && TCTP.activateBtn) TCTP.activateBtn(btnCopy);
});

btnCopy.addEventListener('click', function(){
    if(TCTP && TCTP.copyText) TCTP.copyText(out.value);
});

if(TCTP && TCTP.getStats) TCTP.getStats(inp, [elWords, elSents]);
})();