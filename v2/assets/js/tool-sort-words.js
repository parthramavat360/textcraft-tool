(function(){ 'use strict';
var prefix = 'sw';
var input = document.getElementById('tc-'+prefix+'-input');
var output = document.getElementById('tc-'+prefix+'-output');
var sortBtn = document.getElementById('tc-'+prefix+'-sort');
var copyBtn = document.getElementById('tc-'+prefix+'-copy');
if(!input||!output||!sortBtn||!copyBtn) return;

var modeBtns = document.querySelectorAll('.tc-'+prefix+'-mode');
var currentMode = 'alpha';
var sortLinesCheck = document.getElementById('tc-'+prefix+'-sort-lines');
var caseSensitiveCheck = document.getElementById('tc-'+prefix+'-case-sensitive');

modeBtns.forEach(function(btn){
  btn.addEventListener('click',function(){
    modeBtns.forEach(function(b){ b.classList.remove('active'); });
    btn.classList.add('active');
    currentMode=btn.getAttribute('data-mode')||'alpha';
  });
});

sortBtn.addEventListener('click',function(){
  var text=input.value;
  if(!text){ TCTP.toast('Please enter some text.','warning'); return; }
  var sortLines=sortLinesCheck&&sortLinesCheck.checked;
  var caseSensitive=caseSensitiveCheck&&caseSensitiveCheck.checked;
  var lines=text.split('\n');
  if(sortLines){
    lines.sort(function(a,b){
      return compareStr(a,b,currentMode,caseSensitive);
    });
  } else {
    var allWords=[];
    lines.forEach(function(line){
      var words=line.split(/\s+/).filter(function(w){ return w.length>0; });
      allWords=allWords.concat(words);
    });
    allWords.sort(function(a,b){
      return compareStr(a,b,currentMode,caseSensitive);
    });
    lines=[allWords.join(' ')];
  }
  output.value=lines.join('\n');
  TCTP.activateBtn(sortBtn);
  TCTP.toast('Text sorted.');
});

function compareStr(a,b,mode,cs){
  var aVal=a, bVal=b;
  if(!cs){ aVal=a.toLowerCase(); bVal=b.toLowerCase(); }
  switch(mode){
    case 'alpha': return aVal.localeCompare(bVal);
    case 'alpha-desc': return bVal.localeCompare(aVal);
    case 'length': return a.length-b.length||aVal.localeCompare(bVal);
    case 'length-desc': return b.length-a.length||aVal.localeCompare(bVal);
    case 'random': return Math.random()-0.5;
    default: return aVal.localeCompare(bVal);
  }
}

copyBtn.addEventListener('click',function(){
  TCTP.copyText(output.value);
});
})();