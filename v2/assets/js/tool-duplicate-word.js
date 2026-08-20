(function(){ 'use strict';
var inp = document.getElementById('tc-dw-input'); if (!inp) return;
var elFind = document.getElementById('tc-dw-find');
var btnCopy = document.getElementById('tc-dw-copy');
var btnClear = document.getElementById('tc-dw-clear');
var chkCase = document.getElementById('tc-dw-case');
var chkIgnoreCommon = document.getElementById('tc-dw-ignore-common');
var elCount = document.getElementById('tc-dw-count');
var elTotal = document.getElementById('tc-dw-total');
var elTags = document.getElementById('tc-dw-duplicates-tags');
var elBars = document.getElementById('tc-dw-freq-bars');

var commonWords = {'the':1,'a':1,'an':1,'and':1,'or':1,'but':1,'in':1,'on':1,'at':1,'to':1,'for':1,'of':1,'with':1,'by':1,'is':1,'it':1,'as':1,'was':1,'be':1,'are':1,'this':1,'that':1,'from':1,'if':1,'not':1,'so':1,'no':1,'do':1,'we':1,'he':1,'she':1,'me':1,'my':1,'our':1,'us':1,'you':1,'your':1,'they':1,'them':1,'his':1,'her':1};

function analyze(){
    var text = inp.value;
    if(!text.trim()){ elTags.innerHTML = ''; elBars.innerHTML = ''; return; }
    var words = text.match(/[\w']+/g) || [];
    if(!words.length){ elTags.innerHTML = ''; elBars.innerHTML = ''; return; }
    var caseInsensitive = chkCase && chkCase.checked;
    var ignoreCommon = chkIgnoreCommon && chkIgnoreCommon.checked;
    var freq = {};
    words.forEach(function(w){
        var key = caseInsensitive ? w.toLowerCase() : w;
        freq[key] = (freq[key] || 0) + 1;
    });
    var duplicates = {};
    Object.keys(freq).forEach(function(k){
        if(freq[k] > 1){
            if(ignoreCommon && commonWords[k]) return;
            duplicates[k] = freq[k];
        }
    });
    var dupCount = Object.keys(duplicates).length;
    if(elCount) elCount.textContent = dupCount;
    if(elTotal) elTotal.textContent = words.length;
    var tagsHtml = '';
    Object.keys(duplicates).forEach(function(w){
        tagsHtml += '<span class="tc-dup-tag">' + w + ' <small>x' + duplicates[w] + '</small></span>';
    });
    if(elTags) elTags.innerHTML = tagsHtml || '<span class="tc-dup-none">No duplicates found</span>';
    var maxFreq = Math.max.apply(null, words.map(function(w){ return freq[caseInsensitive ? w.toLowerCase() : w]; }));
    var barsHtml = '';
    Object.keys(freq).sort(function(a,b){ return freq[b] - freq[a]; }).forEach(function(w){
        var pct = maxFreq ? (freq[w] / maxFreq * 100) : 0;
        barsHtml += '<div class="tc-freq-bar-row"><span class="tc-freq-word">' + w + '</span><div class="tc-freq-bar-track"><div class="tc-freq-bar-fill" style="width:' + pct + '%"></div></div><span class="tc-freq-num">' + freq[w] + '</span></div>';
    });
    if(elBars) elBars.innerHTML = barsHtml;
}

inp.addEventListener('input', analyze);
btnCopy.addEventListener('click', function(){
    if(TCTP && TCTP.copyText) TCTP.copyText(inp.value);
});
btnClear.addEventListener('click', function(){
    inp.value = '';
    if(elTags) elTags.innerHTML = '';
    if(elBars) elBars.innerHTML = '';
    if(elCount) elCount.textContent = '0';
    if(elTotal) elTotal.textContent = '0';
});
})();