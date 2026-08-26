/**
 * tool-what-is-my-user-agent.js
 */
(function(){
  var result=document.getElementById('uad-result');
  if(!result)return;

  var ua=navigator.userAgent;
  var platform=navigator.platform||'Unknown';
  var language=navigator.language||'Unknown';
  var languages=(navigator.languages||[]).join(', ')||language;
  var cookiesEnabled=navigator.cookieEnabled;
  var doNotTrack=navigator.doNotTrack||window.doNotTrack||'Unknown';
  var online=navigator.onLine;
  var touchPoints=navigator.maxTouchPoints||0;
  var hardwareConcurrency=navigator.hardwareConcurrency||'Unknown';
  var deviceMemory=navigator.deviceMemory||'Unknown';
  var pdfViewer=navigator.pdfViewerEnabled!==undefined?navigator.pdfViewerEnabled:'Unknown';

  // Screen info
  var screenW=screen.width;
  var screenH=screen.height;
  var screenColor=screen.colorDepth||'Unknown';
  var availW=screen.availWidth;
  var availH=screen.availHeight;
  var windowW=window.innerWidth;
  var windowH=window.innerHeight;
  var dpr=window.devicePixelRatio||1;

  // Parse UA
  function parseUA(ua){
    var browser='Unknown', browserVer='Unknown', os='Unknown', osVer='';
    var device='Desktop', model='';

    // OS detection
    if(ua.indexOf('Windows NT 10')>-1){os='Windows';osVer='10/11';}
    else if(ua.indexOf('Windows NT 6.3')>-1){os='Windows';osVer='8.1';}
    else if(ua.indexOf('Windows NT 6.2')>-1){os='Windows';osVer='8';}
    else if(ua.indexOf('Windows NT 6.1')>-1){os='Windows';osVer='7';}
    else if(ua.indexOf('Mac OS X')>-1){os='macOS';var m=ua.match(/Mac OS X (\d+[._]\d+[._]?\d*)/);if(m) osVer=m[1].replace(/_/g,'.');}
    else if(ua.indexOf('Android')>-1){os='Android';var m=ua.match(/Android (\d+[\.\d]*)/);if(m) osVer=m[1];device='Mobile';}
    else if(ua.indexOf('iPhone')>-1||ua.indexOf('iPad')>-1){os='iOS';var m=ua.match(/OS (\d+_\d+)/);if(m) osVer=m[1].replace('_','.');device=ua.indexOf('iPad')>-1?'Tablet':'Mobile';}
    else if(ua.indexOf('Linux')>-1){os='Linux';}
    else if(ua.indexOf('CrOS')>-1){os='ChromeOS';}

    // Browser detection
    if(ua.indexOf('Edg/')>-1){browser='Microsoft Edge';var m=ua.match(/Edg\/(\d+[\.\d]*)/);if(m) browserVer=m[1];}
    else if(ua.indexOf('OPR/')>-1||ua.indexOf('Opera')>-1){browser='Opera';var m=ua.match(/OPR\/(\d+[\.\d]*)/);if(m) browserVer=m[1];}
    else if(ua.indexOf('Chrome/')>-1&&ua.indexOf('Edg/')===-1){browser='Google Chrome';var m=ua.match(/Chrome\/(\d+[\.\d]*)/);if(m) browserVer=m[1];}
    else if(ua.indexOf('Firefox/')>-1){browser='Mozilla Firefox';var m=ua.match(/Firefox\/(\d+[\.\d]*)/);if(m) browserVer=m[1];}
    else if(ua.indexOf('Safari/')>-1&&ua.indexOf('Chrome')===-1){browser='Apple Safari';var m=ua.match(/Version\/(\d+[\.\d]*)/);if(m) browserVer=m[1];}

    // Mobile device detection
    if(ua.indexOf('Mobile')>-1) device='Mobile';
    else if(ua.indexOf('Tablet')>-1||ua.indexOf('iPad')>-1) device='Tablet';

    return {browser:browser,browserVer:browserVer,os:os,osVer:osVer,device:device};
  }

  var parsed=parseUA(ua);

  // Render Overview
  var ov='<div class="tctp-uad-grid">';
  ov+=card('fa-brands fa-chrome','Browser',parsed.browser+' '+parsed.browserVer);
  ov+=card('fa-solid fa-desktop','Operating System',parsed.os+(parsed.osVer?' '+parsed.osVer:''));
  ov+=card('fa-solid fa-mobile-screen','Device Type',parsed.device);
  ov+=card('fa-solid fa-language','Language',language);
  ov+=card('fa-solid fa-expand','Resolution',screenW+' x '+screenH+' ('+screenColor+'-bit)');
  ov+=card('fa-solid fa-arrows-left-right-to-line','Viewport',windowW+' x '+windowH);
  ov+=card('fa-solid fa-magnifying-glass','Pixel Ratio',dpr+'x');
  ov+=card('fa-solid fa-globe','Online',online?'Yes':'No');
  ov+=card('fa-solid fa-cookie','Cookies',cookiesEnabled?'Enabled':'Disabled');
  ov+=card('fa-solid fa-hand','Touch',touchPoints>0?touchPoints+' point(s)':'No');
  ov+=card('fa-solid fa-microchip','CPU Cores',typeof hardwareConcurrency==='number'?hardwareConcurrency:hardwareConcurrency);
  ov+=card('fa-solid fa-memory','Device Memory',typeof deviceMemory==='number'?deviceMemory+' GB':deviceMemory);
  ov+='</div>';
  document.getElementById('uad-overview-content').innerHTML=ov;

  // Render Raw
  document.getElementById('uad-raw-content').innerHTML=
    '<div class="tctp-uad-raw-box"><div class="tctp-uad-raw-label">User Agent String</div>'+
    '<div class="tctp-uad-raw-value" id="uad-raw-string">'+escHtml(ua)+'</div></div>';

  // Render Technical
  var tech='<table class="tctp-table"><tbody>';
  tech+=tr('User Agent',ua);
  tech+=tr('Platform',platform);
  tech+=tr('Browser',parsed.browser);
  tech+=tr('Browser Version',parsed.browserVer);
  tech+=tr('Operating System',parsed.os);
  tech+=tr('OS Version',parsed.osVer||'N/A');
  tech+=tr('Device Type',parsed.device);
  tech+=tr('Language',language);
  tech+=tr('Languages',languages);
  tech+=tr('Cookies Enabled',cookiesEnabled?'Yes':'No');
  tech+=tr('Do Not Track',doNotTrack);
  tech+=tr('Online',online?'Yes':'No');
  tech+=tr('Touch Points',touchPoints);
  tech+=tr('Hardware Concurrency',hardwareConcurrency);
  tech+=tr('Device Memory',typeof deviceMemory==='number'?deviceMemory+' GB':deviceMemory);
  tech+=tr('PDF Viewer',pdfViewer);
  tech+=tr('Screen Width',screenW);
  tech+=tr('Screen Height',screenH);
  tech+=tr('Color Depth',screenColor);
  tech+=tr('Available Width',availW);
  tech+=tr('Available Height',availH);
  tech+=tr('Window Width',windowW);
  tech+=tr('Window Height',windowH);
  tech+=tr('Device Pixel Ratio',dpr);
  tech+=tr('Java Enabled',navigator.javaEnabled()?'Yes':'No');
  tech+='</tbody></table>';
  document.getElementById('uad-technical-content').innerHTML=tech;

  function card(icon,label,value){
    return '<div class="tctp-uad-card"><div class="tctp-uad-card-icon"><i class="'+icon+'"></i></div>'+
      '<div class="tctp-uad-card-label">'+label+'</div>'+
      '<div class="tctp-uad-card-value">'+escHtml(value)+'</div></div>';
  }
  function tr(k,v){return '<tr><td><b>'+k+'</b></td><td>'+escHtml(String(v))+'</td></tr>';}
  function escHtml(s){var d=document.createElement('div');d.textContent=s;return d.innerHTML;}

  // Copy buttons
  document.getElementById('uad-copy-ua').addEventListener('click',function(){
    var ta=document.createElement('textarea');ta.value=ua;document.body.appendChild(ta);ta.select();document.execCommand('copy');document.body.removeChild(ta);
  });
  document.getElementById('uad-copy-all').addEventListener('click',function(){
    var info='Browser: '+parsed.browser+' '+parsed.browserVer+'\nOS: '+parsed.os+' '+parsed.osVer+'\nDevice: '+parsed.device+'\nLanguage: '+language+'\nScreen: '+screenW+'x'+screenH+'\nViewport: '+windowW+'x'+windowH+'\nUser Agent: '+ua;
    var ta=document.createElement('textarea');ta.value=info;document.body.appendChild(ta);ta.select();document.execCommand('copy');document.body.removeChild(ta);
  });

  // Tabs
  document.querySelectorAll('.tctp-rsz-tab[data-tab]').forEach(function(tab){
    tab.addEventListener('click',function(){
      document.querySelectorAll('.tctp-rsz-tab').forEach(function(t){t.classList.remove('sel')});
      tab.classList.add('sel');
      var id=tab.getAttribute('data-tab');
      document.querySelectorAll('.tctp-rsz-tab-panel').forEach(function(p){p.style.display='none'});
      var map={overview:'uad-overview',raw:'uad-raw',technical:'uad-technical'};
      var panel=document.getElementById(map[id]);
      if(panel) panel.style.display='';
    });
  });
})();