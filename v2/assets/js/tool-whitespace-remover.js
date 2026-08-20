(function(){ 'use strict';
var prefix = 'wr';
var input = document.getElementById('tc-'+prefix+'-input');
var output = document.getElementById('tc-'+prefix+'-output');
var cleanBtn = document.getElementById('tc-'+prefix+'-clean');
var copyBtn = document.getElementById('tc-'+prefix+'-copy');
if(!input||!output||!cleanBtn||!copyBtn) return;

var trimLines = document.getElementById('tc-'+prefix+'-trim-lines');
var extraSpaces = document.getElementById('tc-'+prefix+'-extra-spaces');
var removeTabs = document.getElementById('tc-'+prefix+'-tabs');
var leadingTrailing = document.getElementById('tc-'+prefix+'-leading-trailing');

cleanBtn.addEventListener('click',function(){
  var text=input.value;
  if(!text){ TCTP.toast('Please enter some text.','warning'); return; }
  var lines=text.split('\n');

  if(leadingTrailing&&leadingTrailing.checked){
    lines=lines.map(function(l){ return l.replace(/^\s+|\s+$/g,''); });
  }
  if(trimLines&&trimLines.checked){
    lines=lines.map(function(l){ return l.trim(); });
  }
  if(removeTabs&&removeTabs.checked){
    lines=lines.map(function(l){ return l.replace(/\t/g,' '); });
  }
  if(extraSpaces&&extraSpaces.checked){
    lines=lines.map(function(l){ return l.replace(/ {2,}/g,' '); });
  }

  output.value=lines.join('\n');
  TCTP.activateBtn(cleanBtn);
  TCTP.toast('Whitespace cleaned.');
});

copyBtn.addEventListener('click',function(){
  TCTP.copyText(output.value);
});
})();