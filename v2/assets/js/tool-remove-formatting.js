/**
 * Remove Text Formatting — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function(){ 'use strict';
var inp = document.getElementById('tc-rf-input'); if (!inp) return;
var out = document.getElementById('tc-rf-output');
var btnClean = document.getElementById('tc-rf-clean');
var statusEl = document.getElementById('tc-rf-status');
if (!btnClean) return;

function cleanText(text){
    text = text.replace(/<!--[\s\S]*?-->/g, '');
    text = text.replace(/<script[\s\S]*?<\/script>/gi, '');
    text = text.replace(/<style[\s\S]*?<\/style>/gi, '');
    text = text.replace(/<[^>]+>/g, ' ');
    var ta = document.createElement('textarea');
    ta.innerHTML = text;
    text = ta.value;
    text = text.replace(/&#(\d+);/g, function(m, code){ return String.fromCharCode(parseInt(code, 10)); });
    text = text.replace(/&#x([0-9a-f]+);/gi, function(m, code){ return String.fromCharCode(parseInt(code, 16)); });
    text = text.replace(/[ \t]+/g, ' ');
    text = text.replace(/\n{3,}/g, '\n\n');
    return text.trim();
}

btnClean.addEventListener('click', function(){
    var text = inp.value;
    if(!text.trim()){
        TCTP.toast('Paste some formatted text first.', '\u26A0\uFE0F');
        return;
    }
    TCTP.showProgress('tc-rf-bar');
    TCTP.setProgress('tc-rf-bar', 50, 'Cleaning...');
    var cleaned = cleanText(text);
    if(out) out.value = cleaned;
    TCTP.setProgress('tc-rf-bar', 100, 'Done!');
    TCTP.hideProgress('tc-rf-bar');
    if(statusEl) statusEl.textContent = cleaned.length + ' characters of clean text.';
    TCTP.toast('Formatting removed!');
});

TCTP.initDropZone('tc-rf-drop', 'tc-rf-drop-input', function(f){
    var reader = new FileReader();
    reader.onload = function(e){
        inp.value = e.target.result;
        TCTP.showFileRow('tc-rf-file', f);
        if(statusEl) statusEl.textContent = 'Loaded ' + f.name + ' \u2014 click Clean Formatting.';
    };
    reader.onerror = function(){
        TCTP.toast('Failed to read file.', '\u274C');
    };
    reader.readAsText(f);
}, 'text/html,.html,.htm,.doc,.docx');

var removeFileBtn = document.querySelector('#tc-rf-file .tc-x');
if(removeFileBtn){
    removeFileBtn.addEventListener('click', function(){
        TCTP.hideFileRow('tc-rf-file');
    });
}
})();
