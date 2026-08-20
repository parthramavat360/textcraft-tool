(function(){ 'use strict';
var prefix = 'apa';
var titleInput = document.getElementById('tc-'+prefix+'-title');
var authorInput = document.getElementById('tc-'+prefix+'-author');
var institutionInput = document.getElementById('tc-'+prefix+'-institution');
var courseInput = document.getElementById('tc-'+prefix+'-course');
var instructorInput = document.getElementById('tc-'+prefix+'-instructor');
var dateInput = document.getElementById('tc-'+prefix+'-date');
var runningHeadInput = document.getElementById('tc-'+prefix+'-running-head');
var generateBtn = document.getElementById('tc-'+prefix+'-generate');
var output = document.getElementById('tc-'+prefix+'-output');
var copyBtn = document.getElementById('tc-'+prefix+'-copy');
if(!generateBtn||!output) return;

generateBtn.addEventListener('click',function(){
  var title=titleInput?titleInput.value.trim():'';
  var author=authorInput?authorInput.value.trim():'';
  var institution=institutionInput?institutionInput.value.trim():'';
  var course=courseInput?courseInput.value.trim():'';
  var instructor=instructorInput?instructorInput.value.trim():'';
  var dateStr=dateInput?dateInput.value.trim():'';
  var runningHead=runningHeadInput?runningHeadInput.value.trim():'';

  if(!title){ TCTP.toast('Please enter a title.','warning'); return; }
  if(!author){ TCTP.toast('Please enter author name(s).','warning'); return; }
  if(!institution){ TCTP.toast('Please enter institution name.','warning'); return; }

  var dateFormatted='';
  if(dateStr){
    var d=new Date(dateStr);
    if(!isNaN(d.getTime())){
      var months=['January','February','March','April','May','June','July','August','September','October','November','December'];
      dateFormatted=months[d.getMonth()]+' '+d.getDate()+', '+d.getFullYear();
    } else {
      dateFormatted=dateStr;
    }
  } else {
    var now=new Date();
    var months=['January','February','March','April','May','June','July','August','September','October','November','December'];
    dateFormatted=months[now.getMonth()]+' '+now.getDate()+', '+now.getFullYear();
  }

  var lines=[];
  if(runningHead){
    lines.push('Running head: '+runningHead.toUpperCase());
    lines.push('');
  }
  lines.push(author);
  lines.push(institution);
  if(course) lines.push(course);
  if(instructor) lines.push(instructor);
  lines.push(dateFormatted);
  lines.push('');
  lines.push(title);
  lines.push('');

  output.value=lines.join('\n');
  TCTP.activateBtn(generateBtn);
  TCTP.toast('APA title page generated.');
});

if(copyBtn){
  copyBtn.addEventListener('click',function(){
    TCTP.copyText(output.value);
  });
}
})();