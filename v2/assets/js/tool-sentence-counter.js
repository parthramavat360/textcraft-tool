(function(){ 'use strict';
var prefix = 'scn';
var input = document.getElementById('tc-'+prefix+'-input');
var analyzeBtn = document.getElementById('tc-'+prefix+'-analyze');
if(!input||!analyzeBtn) return;

var statEls={
  words: document.getElementById('tc-'+prefix+'-words'),
  sentences: document.getElementById('tc-'+prefix+'-sentences'),
  paragraphs: document.getElementById('tc-'+prefix+'-paragraphs'),
  charsNoSpace: document.getElementById('tc-'+prefix+'-chars-no-space'),
  charsWithSpace: document.getElementById('tc-'+prefix+'-chars-with-space'),
  readingTime: document.getElementById('tc-'+prefix+'-reading-time'),
  speakingTime: document.getElementById('tc-'+prefix+'-speaking-time')
};

analyzeBtn.addEventListener('click',function(){
  var text=input.value;
  if(!text){ TCTP.toast('Please enter some text.','warning'); return; }

  var words=(text.match(/[a-zA-Z0-9']+/g)||[]);
  var wordCount=words.length;

  var sentences=(text.match(/[.!?]+(\s|$)/g)||[]).length;
  if(sentences===0&&wordCount>0) sentences=1;

  var paragraphs=text.split(/\n\s*\n/).filter(function(p){ return p.trim().length>0; }).length;
  if(paragraphs===0&&wordCount>0) paragraphs=1;

  var charsNoSpace=text.replace(/\s/g,'').length;
  var charsWithSpace=text.length;

  var readingMin=Math.ceil(wordCount/200);
  var speakingMin=Math.ceil(wordCount/130);

  function fmtTime(min){
    if(min<1) return 'Less than 1 min';
    var h=Math.floor(min/60);
    var m=min%60;
    if(h>0) return h+'h '+m+'m';
    return min+' min';
  }

  if(statEls.words) statEls.words.textContent=wordCount;
  if(statEls.sentences) statEls.sentences.textContent=sentences;
  if(statEls.paragraphs) statEls.paragraphs.textContent=paragraphs;
  if(statEls.charsNoSpace) statEls.charsNoSpace.textContent=charsNoSpace;
  if(statEls.charsWithSpace) statEls.charsWithSpace.textContent=charsWithSpace;
  if(statEls.readingTime) statEls.readingTime.textContent=fmtTime(readingMin);
  if(statEls.speakingTime) statEls.speakingTime.textContent=fmtTime(speakingMin);

  TCTP.activateBtn(analyzeBtn);
  TCTP.toast('Text analyzed.');
});
})();