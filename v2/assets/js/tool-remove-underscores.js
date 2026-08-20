(function(){ 'use strict';
var prefix = 'ru';
var input = document.getElementById('tc-'+prefix+'-input');
var output = document.getElementById('tc-'+prefix+'-output');
var convertBtn = document.getElementById('tc-'+prefix+'-convert');
var copyBtn = document.getElementById('tc-'+prefix+'-copy');
if(!input||!output||!convertBtn||!copyBtn) return;

var modeReplace = document.querySelector('.tc-'+prefix+'-mode[data-mode="replace-with-space"]');
var modeRemove = document.querySelector('.tc-'+prefix+'-mode[data-mode="remove-all"]');
var currentMode = 'replace-with-space';

if(modeReplace){ modeReplace.addEventListener('click',function(){
  currentMode='replace-with-space';
  modeReplace.classList.add('active');
  modeRemove.classList.remove('active');
}); }
if(modeRemove){ modeRemove.addEventListener('click',function(){
  currentMode='remove-all';
  modeRemove.classList.add('active');
  modeReplace.classList.remove('active');
}); }

convertBtn.addEventListener('click',function(){
  var text=input.value;
  if(!text){ TCTP.toast('Please enter some text.','warning'); return; }
  var result;
  if(currentMode==='replace-with-space'){
    result=text.replace(/_+/g,' ');
  } else {
    result=text.replace(/_/g,'');
  }
  output.value=result;
  TCTP.activateBtn(convertBtn);
  TCTP.toast('Underscores processed.');
});

copyBtn.addEventListener('click',function(){
  TCTP.copyText(output.value);
});
})();