(function(){ 'use strict';
var prefix = 'rpt';
var input = document.getElementById('tc-'+prefix+'-input');
var output = document.getElementById('tc-'+prefix+'-output');
var generateBtn = document.getElementById('tc-'+prefix+'-generate');
var copyBtn = document.getElementById('tc-'+prefix+'-copy');
var countInput = document.getElementById('tc-'+prefix+'-count');
var separatorSelect = document.getElementById('tc-'+prefix+'-separator');
if(!input||!output||!generateBtn||!copyBtn) return;

generateBtn.addEventListener('click',function(){
  var text=input.value;
  if(!text){ TCTP.toast('Please enter some text.','warning'); return; }
  var count=parseInt(countInput.value,10)||1;
  if(count<1) count=1;
  if(count>10000){ TCTP.toast('Maximum count is 10,000.','warning'); return; }
  var sep=separatorSelect?separatorSelect.value:'\\n';
  if(sep==='\\n') sep='\n';
  else if(sep==='\\n\\n') sep='\n\n';
  else if(sep==='comma') sep=', ';
  else if(sep==='space') sep=' ';
  else if(sep==='dash') sep=' — ';
  else sep='\n';
  var parts=[];
  for(var i=0;i<count;i++){ parts.push(text); }
  output.value=parts.join(sep);
  TCTP.activateBtn(generateBtn);
  TCTP.toast('Text repeated '+count+' times.');
});

copyBtn.addEventListener('click',function(){
  TCTP.copyText(output.value);
});
})();