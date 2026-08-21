/**
 * Sentence Counter — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function(){ 'use strict';
var input = document.getElementById('tc-sc-input');
var analyzeBtn = document.getElementById('tc-sc-analyze');
var clearBtn = document.getElementById('tc-sc-clear');
if(!input||!analyzeBtn) return;

var statEls={
  words: document.getElementById('tc-sc-words'),
  sentences: document.getElementById('tc-sc-sentences'),
  paragraphs: document.getElementById('tc-sc-paragraphs'),
  charsWithSpace: document.getElementById('tc-sc-chars'),
  charsNoSpace: document.getElementById('tc-sc-chars-nosp'),
  readingTime: document.getElementById('tc-sc-readtime'),
  speakingTime: document.getElementById('tc-sc-speaktime')
};

function fmtTime(min){
  if(min<1) return '< 1 min';
  var h=Math.floor(min/60);
  var m=min%60;
  if(h>0) return h+'h '+m+'m';
  return min+' min';
}

analyzeBtn.addEventListener('click',function(){
  var text=input.value;
  if(!text.trim()){ TCTP.toast('Please enter some text.','\u26A0\uFE0F'); return; }

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

  if(statEls.words) statEls.words.textContent=wordCount;
  if(statEls.sentences) statEls.sentences.textContent=sentences;
  if(statEls.paragraphs) statEls.paragraphs.textContent=paragraphs;
  if(statEls.charsWithSpace) statEls.charsWithSpace.textContent=charsWithSpace;
  if(statEls.charsNoSpace) statEls.charsNoSpace.textContent=charsNoSpace;
  if(statEls.readingTime) statEls.readingTime.textContent=fmtTime(readingMin);
  if(statEls.speakingTime) statEls.speakingTime.textContent=fmtTime(speakingMin);

  TCTP.toast('Text analyzed.');
});

if(clearBtn){
  clearBtn.addEventListener('click',function(){
    input.value='';
    if(statEls.words) statEls.words.textContent='0';
    if(statEls.sentences) statEls.sentences.textContent='0';
    if(statEls.paragraphs) statEls.paragraphs.textContent='0';
    if(statEls.charsWithSpace) statEls.charsWithSpace.textContent='0';
    if(statEls.charsNoSpace) statEls.charsNoSpace.textContent='0';
    if(statEls.readingTime) statEls.readingTime.textContent='0 min';
    if(statEls.speakingTime) statEls.speakingTime.textContent='0 min';
  });
}
})();
