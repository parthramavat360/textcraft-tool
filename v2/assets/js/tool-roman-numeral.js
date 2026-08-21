(function(){ 'use strict';
var prefix = 'rn';
var input = document.getElementById('tc-'+prefix+'-input');
var output = document.getElementById('tc-'+prefix+'-output');
var convertBtn = document.getElementById('tc-'+prefix+'-convert');
var copyBtn = document.getElementById('tc-'+prefix+'-copy');
if(!input||!output||!convertBtn||!copyBtn) return;

var currentMode = 'to_roman';

document.querySelectorAll('.tc-modes[data-group="rn-mode"] .tc-btn').forEach(function(btn){
  btn.addEventListener('click',function(){
    TCTP.activateBtn(btn);
    currentMode = btn.getAttribute('data-val') || 'to_roman';
  });
});

var lookup=[
  [1000,'M'],[900,'CM'],[500,'D'],[400,'CD'],
  [100,'C'],[90,'XC'],[50,'L'],[40,'XL'],
  [10,'X'],[9,'IX'],[5,'V'],[4,'IV'],[1,'I']
];

function toRoman(num){
  if(num<1||num>3999) return '';
  var result='';
  for(var i=0;i<lookup.length;i++){
    while(num>=lookup[i][0]){ result+=lookup[i][1]; num-=lookup[i][0]; }
  }
  return result;
}

function fromRoman(str){
  var map={M:1000,CM:900,D:500,CD:400,C:100,XC:90,L:50,XL:40,X:10,IX:9,V:5,IV:4,I:1};
  str=str.toUpperCase().trim();
  var result=0;
  for(var i=0;i<str.length;i++){
    var cur=map[str[i]]||0;
    var next=map[str[i+1]]||0;
    if(cur<next){ result-=cur; } else { result+=cur; }
  }
  return result;
}

convertBtn.addEventListener('click',function(){
  var val=input.value.trim();
  if(!val){ TCTP.toast('Please enter a value.','\u26A0\uFE0F'); return; }
  if(currentMode==='to_roman'){
    var num=parseInt(val,10);
    if(isNaN(num)||num<1||num>3999){ TCTP.toast('Enter a number between 1 and 3999.','\u26A0\uFE0F'); return; }
    output.value=toRoman(num);
  } else {
    if(!/^[IVXLCDMivxlcdm]+$/i.test(val)){ TCTP.toast('Enter a valid Roman numeral.','\u26A0\uFE0F'); return; }
    var result=fromRoman(val);
    if(result<1||result>3999){ TCTP.toast('Result out of range (1-3999).','\u26A0\uFE0F'); return; }
    output.value=result;
  }
  TCTP.toast('Conversion complete.');
});

copyBtn.addEventListener('click',function(){
  TCTP.copyText(output.value,'Result');
});
})();