(function(){ 'use strict';
var prefix = 'ru';
var input = document.getElementById('tc-'+prefix+'-input');
var output = document.getElementById('tc-'+prefix+'-output');
var convertBtn = document.getElementById('tc-'+prefix+'-convert');
var copyBtn = document.getElementById('tc-'+prefix+'-copy');
if(!input||!output||!convertBtn||!copyBtn) return;

var currentMode = 'space';

document.querySelectorAll('.tc-modes[data-group="ru-mode"] .tc-btn').forEach(function(btn){
  btn.addEventListener('click',function(){
    TCTP.activateBtn(btn);
    currentMode = btn.getAttribute('data-val') || 'space';
  });
});

convertBtn.addEventListener('click',function(){
  var text=input.value;
  if(!text){ TCTP.toast('Please enter some text.','\u26A0\uFE0F'); return; }
  var result;
  if(currentMode==='space'){
    result=text.replace(/_+/g,' ');
  } else {
    result=text.replace(/_/g,'');
  }
  output.value=result;
  TCTP.toast('Underscores processed.');
});

copyBtn.addEventListener('click',function(){
  TCTP.copyText(output.value);
});
})();