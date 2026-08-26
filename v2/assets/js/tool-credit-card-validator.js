/**
 * tool-credit-card-validator.js
 */
(function(){
  var $=jQuery, btn=document.getElementById('ccv-validate');
  if(!btn)return;

  var input=document.getElementById('ccv-number');
  var result=document.getElementById('ccv-result');

  var CARD_TYPES=[
    {name:'Visa',pattern:/^4[0-9]{12}(?:[0-9]{3})?$/,color:'#1a1f71',icon:'fa-brands fa-cc-visa'},
    {name:'Mastercard',pattern:/^5[1-5][0-9]{14}$|^2(?:2(?:2[1-9]|[3-9][0-9])|[3-6][0-9][0-9]|7(?:[01][0-9]|20))[0-9]{12}$/,color:'#eb001b',icon:'fa-brands fa-cc-mastercard'},
    {name:'American Express',pattern:/^3[47][0-9]{13}$/,color:'#006fcf',icon:'fa-brands fa-cc-amex'},
    {name:'Discover',pattern:/^6(?:011|5[0-9]{2})[0-9]{12}$/,color:'#ff6000',icon:'fa-brands fa-cc-discover'},
    {name:'Diners Club',pattern:/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/,color:'#004080',icon:'fa-solid fa-credit-card'},
    {name:'JCB',pattern:/^(?:2131|1800|35\d{3})\d{11}$/,color:'#0e4c96',icon:'fa-solid fa-credit-card'},
    {name:'UnionPay',pattern:/^62[0-9]{14,17}$/,color:'#e21836',icon:'fa-solid fa-credit-card'},
    {name:'Maestro',pattern:/^(?:5018|5020|5038|5893|6304|6759|6761|6762|6763)[0-9]{8,15}$/,color:'#ff5f00',icon:'fa-solid fa-credit-card'}
  ];

  function detectType(num){
    for(var i=0;i<CARD_TYPES.length;i++){
      if(CARD_TYPES[i].pattern.test(num)) return CARD_TYPES[i];
    }
    return null;
  }

  function luhn(num){
    var arr=num.split('').reverse().map(Number);
    var sum=0;
    for(var i=0;i<arr.length;i++){
      var d=arr[i];
      if(i%2===1){d*=2;if(d>9) d-=9;}
      sum+=d;
    }
    return sum%10===0;
  }

  function maskCard(num){
    var clean=num.replace(/\s/g,'');
    if(clean.length<=8) return clean;
    return clean.substring(0,4)+' '+clean.substring(4,8)+' '+('*'.repeat(clean.length-8).match(/.{1,4}/g)||[]).join(' ')+' '+clean.substring(clean.length-4);
  }

  btn.addEventListener('click',function(){
    var raw=input.value.replace(/\s/g,'');
    if(raw.length<12) return;

    window.TCTP.showProgress();
    btn.disabled=true;

    setTimeout(function(){
      var isValid=luhn(raw);
      var cardType=detectType(raw);
      var typeName=cardType?cardType.name:'Unknown';
      var typeColor=cardType?cardType.color:'#666';
      var typeIcon=cardType?cardType.icon:'fa-solid fa-credit-card';

      var masked=maskCard(input.value.replace(/\s/g,''));

      var html='<div class="tc-ccv-result-wrap">';
      html+='<div class="tc-ccv-card-display" style="border-left:4px solid '+typeColor+';">';
      html+='<div class="tc-ccv-card-type"><i class="'+typeIcon+'"></i> '+typeName+'</div>';
      html+='<div class="tc-ccv-card-number">'+masked+'</div>';
      html+='<div class="tc-ccv-card-status '+(isValid?'valid':'invalid')+'">';
      html+='<i class="fa-solid '+(isValid?'fa-check-circle':'fa-times-circle')+'"></i> ';
      html+=isValid?'Valid Card Number':'Invalid Card Number';
      html+='</div></div>';

      html+='<div class="tc-ccv-details">';
      html+='<div class="tc-ccv-detail-row"><b>Number:</b> '+raw+'</div>';
      html+='<div class="tc-ccv-detail-row"><b>Length:</b> '+raw.length+' digits</div>';
      html+='<div class="tc-ccv-detail-row"><b>Type:</b> '+typeName+'</div>';
      html+='<div class="tc-ccv-detail-row"><b>Luhn Check:</b> '+(isValid?'Passed':'Failed')+'</div>';
      html+='<div class="tc-ccv-detail-row"><b>First Digit:</b> '+raw[0]+'</div>';
      html+='<div class="tc-ccv-detail-row"><b>Issuer Network:</b> ';
      if(raw[0]==='4') html+='Visa';
      else if(raw[0]==='5') html+='Mastercard';
      else if(raw[0]==='3') html+='American Express / Diners Club';
      else if(raw[0]==='6') html+='Discover / UnionPay';
      else html+='Unknown';
      html+='</div></div>';

      if(isValid){
        html+='<div class="tc-ccv-note tc-ccv-note-success"><i class="fa-solid fa-info-circle"></i> This card number passes the Luhn algorithm check and matches a known card format. Note: This only validates the number format - it does not check if the card exists or has funds.</div>';
      } else {
        html+='<div class="tc-ccv-note tc-ccv-note-error"><i class="fa-solid fa-exclamation-triangle"></i> This card number fails the Luhn algorithm check or does not match any known card format. The number is invalid.</div>';
      }
      html+='</div>';

      document.getElementById('ccv-result-card').innerHTML=html;
      result.style.display='block';
      window.TCTP.hideProgress();
      btn.disabled=false;
      window.TCTP.switchToResultTab();

      // Store for copy
      window.TCTP.copyText=window.TCTP.copyText||function(){};
      document.querySelector('[data-copy="ccv-result-text"]').addEventListener('click',function(){
        window.TCTP.copyText('Card: '+raw+'\nType: '+typeName+'\nLength: '+raw.length+'\nLuhn: '+(isValid?'Passed':'Failed')+'\nValid: '+(isValid?'Yes':'No'));
      });
    },200);
  });
})();