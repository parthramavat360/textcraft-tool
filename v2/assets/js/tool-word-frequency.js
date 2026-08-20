(function(){ 'use strict';
var prefix = 'wf';
var input = document.getElementById('tc-'+prefix+'-input');
var analyzeBtn = document.getElementById('tc-'+prefix+'-analyze');
var output = document.getElementById('tc-'+prefix+'-output');
var copyBtn = document.getElementById('tc-'+prefix+'-copy');
if(!input||!analyzeBtn||!output||!copyBtn) return;

var caseSensitive = document.getElementById('tc-'+prefix+'-case-sensitive');
var ignoreCommon = document.getElementById('tc-'+prefix+'-ignore-common');

var commonWords=['the','a','an','and','or','but','in','on','at','to','for','of','is','it','as','by','with','was','are','be','has','had','have','not','this','that','from','they','we','he','she','you','me','my','his','her','our','their','its','do','so','up','out','can','will','just','than','then','also','about','more','some','all','would','could','should','very','if','no','yes','been','being','into','over','such','own','may','did','get','got','let','say','said','too','any','each','which','their','there','what','when','where','who','how','whom','this','these','those','am'];

analyzeBtn.addEventListener('click',function(){
  var text=input.value;
  if(!text){ TCTP.toast('Please enter some text.','warning'); return; }
  var cs=caseSensitive&&caseSensitive.checked;
  var ignore=ignoreCommon&&ignoreCommon.checked;
  var words=text.match(/[a-zA-Z0-9']+/g);
  if(!words||!words.length){ TCTP.toast('No words found.','warning'); return; }

  var freq={};
  words.forEach(function(w){
    var key=cs?w:w.toLowerCase();
    if(ignore&&commonWords.indexOf(key)!==-1) return;
    freq[key]=(freq[key]||0)+1;
  });

  var sorted=Object.keys(freq).sort(function(a,b){ return freq[b]-freq[a]; });
  var total=words.length;
  var lines=sorted.map(function(w,i){
    return (i+1)+'. '+w+': '+freq[w]+' ('+((freq[w]/total*100).toFixed(1))+'%)';
  });

  output.value='Word Frequency Analysis\n'+('─'.repeat(30))+'\n'+lines.join('\n')+'\n\nTotal words: '+total+'\nUnique words: '+sorted.length;
  TCTP.activateBtn(analyzeBtn);
  TCTP.toast('Analysis complete. '+sorted.length+' unique words.');
});

copyBtn.addEventListener('click',function(){
  TCTP.copyText(output.value);
});
})();