(function(){
  'use strict';
  var prefix = 'tc-p2pdf-';
  var dropEl = document.getElementById(prefix+'drop');
  if(!dropEl) return;

  var convertBtn   = document.getElementById(prefix+'convert');
  var pageSizeSel  = document.getElementById(prefix+'page-size');
  var downloadBtn  = document.getElementById(prefix+'download');
  var preview      = document.getElementById(prefix+'preview');
  var fileRow      = document.getElementById(prefix+'file-row');
  var progressWrap = document.getElementById(prefix+'progress');
  var statsEl      = document.getElementById(prefix+'stats');

  var files = [];
  var resultBlob = null;
  var loaded = false;

  var PAGE_SIZES = {
    'a4':   { w: 595.28,  h: 841.89  },
    'letter': { w: 612,    h: 792     },
    'legal':  { w: 612,    h: 1008    },
    'a5':   { w: 419.53,  h: 595.28  }
  };

  function loadLib(cb){
    if(loaded){ cb(); return; }
    var s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js';
    s.onload = function(){ loaded = true; cb(); };
    s.onerror = function(){ TCTP.toast('Failed to load pdf-lib','error'); };
    document.head.appendChild(s);
  }

  TCTP.initDropZone(dropEl, function(f, fileList){
    files = Array.from(fileList || []);
    resultBlob = null;
    TCTP.showFileRow(fileRow, files.length + ' file(s)');
    if(preview) preview.innerHTML = '';
    if(statsEl) statsEl.textContent = '';
    if(downloadBtn) downloadBtn.style.display = 'none';
  });

  if(convertBtn){
    convertBtn.addEventListener('click', function(){
      if(!files.length){ TCTP.toast('Please drop PNG images first','warning'); return; }
      loadLib(function(){ doConvert(); });
    });
  }

  if(downloadBtn){
    downloadBtn.addEventListener('click', function(){
      if(resultBlob) TCTP.downloadBlob(resultBlob, 'converted.pdf');
    });
  }

  function doConvert(){
    var sizeKey = pageSizeSel ? pageSizeSel.value : 'a4';
    var pageSize = PAGE_SIZES[sizeKey] || PAGE_SIZES['a4'];

    TCTP.showProgress(progressWrap);
    TCTP.setProgress(progressWrap, 5);

    var pdfDoc = PDFLib.PDFDocument;
    pdfDoc.create().then(function(pdf){
      var total = files.length;
      var processed = 0;
      var margins = 20;

      function addNext(){
        if(processed >= total){
          return pdf.save().then(function(bytes){
            resultBlob = new Blob([bytes], { type: 'application/pdf' });
            TCTP.setProgress(progressWrap, 100);
            TCTP.hideProgress(progressWrap);

            if(preview){
              preview.innerHTML = '';
              var info = document.createElement('div');
              info.textContent = 'PDF created with ' + total + ' page(s)';
              preview.appendChild(info);
            }
            if(downloadBtn) downloadBtn.style.display = '';
            if(statsEl) statsEl.textContent = TCTP.formatSize(resultBlob.size) + ' | ' + total + ' pages';
            TCTP.toast('PDF created');
            var totalIn = 0;
            files.forEach(function(f){ totalIn += f.size; });
            var saved = totalIn > resultBlob.size ? ((1 - resultBlob.size / totalIn) * 100).toFixed(1) : '0';
            TCTP.updateResultPanel(TCTP.formatSize(totalIn), TCTP.formatSize(resultBlob.size), saved + '%', 'Done');
            TCTP.switchToResultTab();
          });
        }

        var f = files[processed];
        var reader = new FileReader();
        reader.onload = function(e){
          var imgBytes = new Uint8Array(e.target.result);
          pdf.embedPng(imgBytes).then(function(image){
            var page = pdf.addPage([pageSize.w, pageSize.h]);
            var availW = pageSize.w - (margins * 2);
            var availH = pageSize.h - (margins * 2);
            var scale = Math.min(availW / image.width, availH / image.height);
            var w = image.width * scale;
            var h = image.height * scale;
            var x = (pageSize.w - w) / 2;
            var y = (pageSize.h - h) / 2;

            page.drawImage(image, { x: x, y: y, width: w, height: h });
            processed++;
            TCTP.setProgress(progressWrap, 5 + Math.round((processed / total) * 90));
            addNext();
          }).catch(function(){
            processed++;
            TCTP.setProgress(progressWrap, 5 + Math.round((processed / total) * 90));
            addNext();
          });
        };
        reader.readAsArrayBuffer(f);
      }

      addNext();
    }).catch(function(err){
      TCTP.hideProgress(progressWrap);
      TCTP.toast('PDF creation failed: ' + err.message, 'error');
    });
  }
})();
