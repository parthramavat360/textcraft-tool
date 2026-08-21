/**
 * Repeat Text Generator — Tool JS
 * @package TextCraft_Tools_Pro
 */

(function(){ 'use strict';
var input = document.getElementById('tc-rt-text');
var output = document.getElementById('tc-rt-output');
var generateBtn = document.getElementById('tc-rt-generate');
var copyBtn = document.getElementById('tc-rt-copy');
var countInput = document.getElementById('tc-rt-count');
var separatorSelect = document.getElementById('tc-rt-separator');
if(!input||!output||!generateBtn||!copyBtn) return;

generateBtn.addEventListener('click',function(){
  var text=input.value;
  if(!text){ TCTP.toast('Please enter some text.','\u26A0\uFE0F'); return; }
  var count=parseInt(countInput ? countInput.value : '5',10)||1;
  if(count<1) count=1;
  if(count>1000){ TCTP.toast('Maximum count is 1,000.','\u26A0\uFE0F'); return; }
  var sepVal=separatorSelect&&separatorSelect.value?separatorSelect.value:'newline';
  var sep;
  switch(sepVal){
    case 'space': sep=' '; break;
    case 'comma': sep=','; break;
    case 'none': sep=''; break;
    case 'newline':
    default: sep='\n';
  }
  var parts=[];
  for(var i=0;i<count;i++){ parts.push(text); }
  output.value=parts.join(sep);
  TCTP.toast('Text repeated '+count+' times.');
});

copyBtn.addEventListener('click',function(){
  TCTP.copyText(output.value,'Repeated text');
});
})();
