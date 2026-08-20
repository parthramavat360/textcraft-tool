(function(){ 'use strict';
var prefix = 'wg';
var input = document.getElementById('tc-'+prefix+'-input');
var output = document.getElementById('tc-'+prefix+'-output');
var convertBtn = document.getElementById('tc-'+prefix+'-convert');
var copyBtn = document.getElementById('tc-'+prefix+'-copy');
if(!input||!output||!convertBtn||!copyBtn) return;

var modeTo = document.querySelector('.tc-'+prefix+'-mode[data-mode="to-wingdings"]');
var modeFrom = document.querySelector('.tc-'+prefix+'-mode[data-mode="from-wingdings"]');
var currentMode = 'to-wingdings';

if(modeTo){ modeTo.addEventListener('click',function(){
  currentMode='to-wingdings';
  modeTo.classList.add('active');
  modeFrom.classList.remove('active');
}); }
if(modeFrom){ modeFrom.addEventListener('click',function(){
  currentMode='from-wingdings';
  modeFrom.classList.add('active');
  modeTo.classList.remove('active');
}); }

var toMap={};
var fromMap={};
var ascii='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
var wingdings='\uD83D\uDD44\uD83D\uDD45\uD83D\uDD46\uD83D\uDD47\uD83D\uDD48\uD83D\uDD49\uD83D\uDD4A\uD83D\uDD4B\uD83D\uDD4C\uD83D\uDD4D\uD83D\uDD4E\uD83D\uDD4F\uD83D\uDD50\uD83D\uDD51\uD83D\uDD52\uD83D\uDD53\uD83D\uDD54\uD83D\uDD55\uD83D\uDD56\uD83D\uDD57\uD83D\uDD58\uD83D\uDD59\uD83D\uDD5A\uD83D\uDD5B\uD83D\uDD5C\uD83D\uDD5D\uD83D\uDD5E\uD83D\uDD5F\uD83D\uDD60\uD83D\uDD61\uD83D\uDD62';

var toWingdings = {
  'A':'\u2701','B':'\u2702','C':'\u2703','D':'\u2704','E':'\u2705',
  'F':'\u2706','G':'\u2707','H':'\u2708','I':'\u2709','J':'\u270A',
  'K':'\u270B','L':'\u270C','M':'\u270D','N':'\u270E','O':'\u270F',
  'P':'\u2710','Q':'\u2711','R':'\u2712','S':'\u2713','T':'\u2714',
  'U':'\u2715','V':'\u2716','W':'\u2717','X':'\u2718','Y':'\u2719',
  'Z':'\u271A','a':'\u271B','b':'\u271C','c':'\u271D','d':'\u271E',
  'e':'\u271F','f':'\u2720','g':'\u2721','h':'\u2722','i':'\u2723',
  'j':'\u2724','k':'\u2725','l':'\u2726','m':'\u2727','n':'\u2728',
  'o':'\u2729','p':'\u272A','q':'\u272B','r':'\u272C','s':'\u272D',
  't':'\u272E','u':'\u272F','v':'\u2730','w':'\u2731','x':'\u2732',
  'y':'\u2733','z':'\u2734','0':'\u2735','1':'\u2736','2':'\u2737',
  '3':'\u2738','4':'\u2739','5':'\u273A','6':'\u273B','7':'\u273C',
  '8':'\u273D','9':'\u273E'
};

var fromWingdings={};
Object.keys(toWingdings).forEach(function(k){ fromWingdings[toWingdings[k]]=k; });

convertBtn.addEventListener('click',function(){
  var text=input.value;
  if(!text){ TCTP.toast('Please enter some text.','warning'); return; }
  var result='';
  if(currentMode==='to-wingdings'){
    for(var i=0;i<text.length;i++){
      var ch=text[i];
      result+=toWingdings[ch]||ch;
    }
  } else {
    for(var i=0;i<text.length;i++){
      var ch=text[i];
      result+=fromWingdings[ch]||ch;
    }
  }
  output.value=result;
  TCTP.activateBtn(convertBtn);
  TCTP.toast('Conversion complete.');
});

copyBtn.addEventListener('click',function(){
  TCTP.copyText(output.value);
});
})();