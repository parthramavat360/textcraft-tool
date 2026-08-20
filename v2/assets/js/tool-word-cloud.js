(function(){ 'use strict';
var prefix = 'wc';
var input = document.getElementById('tc-'+prefix+'-input');
var generateBtn = document.getElementById('tc-'+prefix+'-generate');
var container = document.getElementById('tc-'+prefix+'-container');
if(!input||!generateBtn||!container) return;

var stopWords=['the','a','an','and','or','but','in','on','at','to','for','of','is','it','as','by','with','was','are','be','has','had','have','not','this','that','from','they','we','he','she','you','me','my','his','her','our','their','its','no','yes','if','do','so','up','out','can','will','just','than','them','then','also','about','more','some','all','would','could','should','very'];

generateBtn.addEventListener('click',function(){
  var text=input.value;
  if(!text){ TCTP.toast('Please enter some text.','warning'); return; }
  var words=text.toLowerCase().match(/[a-z0-9]+/g);
  if(!words||!words.length){ TCTP.toast('No words found.','warning'); return; }

  var freq={};
  words.forEach(function(w){
    if(w.length<2) return;
    freq[w]=(freq[w]||0)+1;
  });

  var sorted=Object.keys(freq).sort(function(a,b){ return freq[b]-freq[a]; });
  var top=sorted.slice(0,80);
  if(!top.length){ TCTP.toast('No valid words found.','warning'); return; }

  var maxFreq=freq[top[0]];
  var minFreq=freq[top[top.length-1]];
  var colors=['#e74c3c','#3498db','#2ecc71','#f39c12','#9b59b6','#1abc9c','#e67e22','#34495e','#e91e63','#00bcd4'];

  container.innerHTML='';
  top.forEach(function(word){
    var span=document.createElement('span');
    span.textContent=word;
    span.className='tc-wc-word';
    var ratio=maxFreq===minFreq?1:(freq[word]-minFreq)/(maxFreq-minFreq);
    var size=Math.round(14+ratio*46);
    span.style.fontSize=size+'px';
    span.style.color=colors[Math.floor(Math.random()*colors.length)];
    span.style.display='inline-block';
    span.style.padding='4px 8px';
    span.style.lineHeight='1.2';
    span.title=word+': '+freq[word];
    container.appendChild(span);
  });

  TCTP.activateBtn(generateBtn);
  TCTP.toast('Word cloud generated ('+top.length+' words).');
});
})();