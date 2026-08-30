/**
 * GIF Compressor — Tool JS
 * Premium redesign. Colors, resize, frame skip, loop, output file name,
 * Clear all (also clears previews). omggif-based compression.
 *
 * @package TextCraft_Tools_Pro
 */
(function () {
    'use strict';
    var file = null;
    var compressedBlob = null;
    var drop = document.getElementById('tc-gif-drop');
    if (!drop) return;
    function readFileBuffer(blob) {
        return new Promise(function (resolve, reject) {
            if (blob.arrayBuffer) { blob.arrayBuffer().then(resolve, reject); return; }
            var r = new FileReader();
            r.onload = function () { resolve(r.result); };
            r.onerror = reject;
            r.readAsArrayBuffer(blob);
        });
    }
    function setStat(id, val) { var el = document.getElementById(id); if (el) el.textContent = val; }
    function resetStats() { setStat('tc-gif-stat-orig','-'); setStat('tc-gif-stat-comp','-'); setStat('tc-gif-stat-saved','-'); }
    var colorsSlider = document.getElementById('tc-gif-colors');
    var colorsVal = document.getElementById('tc-gif-colors-val');
    var scaleSlider = document.getElementById('tc-gif-scale');
    var scaleVal = document.getElementById('tc-gif-scale-val');
    var skipSlider = document.getElementById('tc-gif-skip');
    var skipVal = document.getElementById('tc-gif-skip-val');
    var loopCheck = document.getElementById('tc-gif-loop');
    var SKIP_LABELS = ['None','1','2','3','4','5'];
    if (colorsSlider && colorsVal) colorsSlider.addEventListener('input', function () { colorsVal.textContent = colorsSlider.value; });
    if (scaleSlider && scaleVal) scaleSlider.addEventListener('input', function () { scaleVal.textContent = scaleSlider.value + '%'; });
    if (skipSlider && skipVal) skipSlider.addEventListener('input', function () { skipVal.textContent = SKIP_LABELS[parseInt(skipSlider.value,10)] || 'None'; });
    TCTP.initDropZone('tc-gif-drop','tc-gif-drop-input',function(f){
        if(!f.type.match(/image\/gif/)&&!/\.gif$/i.test(f.name)){TCTP.toast('Please select a GIF file.','\u26A0\uFE0F');return;}
        file=f;compressedBlob=null;
        TCTP.showFileRow('tc-gif-file',f);
        var dl=document.getElementById('tc-gif-download');if(dl)dl.style.display='none';
        resetStats();
        var reader=new FileReader();reader.onload=function(ev){TCTP.showOriginalPreview(ev.target.result);TCTP.switchToOriginalTab();};reader.readAsDataURL(f);
    },'image/gif,.gif');
    var removeBtn=document.querySelector('#tc-gif-file .tc-x');
    if(removeBtn)removeBtn.addEventListener('click',function(){file=null;compressedBlob=null;TCTP.hideFileRow('tc-gif-file');resetStats();var dl=document.getElementById('tc-gif-download');if(dl)dl.style.display='none';});
    function decodeGif(buffer){
        try{var bytes=new Uint8Array(buffer);var reader=new omggif.GifReader(bytes);var num=reader.numFrames();if(num<1)return null;
        var tp=reader.width*reader.height*4;var frames=[];
        for(var i=0;i<num;i++){try{var px=new Uint8Array(tp);reader.decodeAndBlitFrameRGBA(i,px);var info=reader.frameInfo(i);
        frames.push({data:px,width:reader.width,height:reader.height,delay:(info.delay||10)*10,disposal:info.disposal});}catch(e){break;}}
        var lc=typeof reader.loopCount==='function'?reader.loopCount():0;
        return{width:reader.width,height:reader.height,frames:frames,loopCount:lc};}catch(e){return null;}
    }
    function medianCut(rgb,numColors){
        if(rgb.length===0)return[[0,0,0]];if(numColors<=1){var r=0,g=0,b=0;for(var i=0;i<rgb.length;i++){r+=rgb[i][0];g+=rgb[i][1];b+=rgb[i][2];}var n=rgb.length||1;return[[Math.round(r/n),Math.round(g/n),Math.round(b/n)]];}
        var buckets=[rgb];while(buckets.length<numColors){var maxR=-1,maxI=0;
        for(var bi=0;bi<buckets.length;bi++){var mins=[255,255,255],maxs=[0,0,0];var bk=buckets[bi];
        for(var i=0;i<bk.length;i++){for(var c=0;c<3;c++){if(bk[i][c]<mins[c])mins[c]=bk[i][c];if(bk[i][c]>maxs[c])maxs[c]=bk[i][c];}}
        var rng=[maxs[0]-mins[0],maxs[1]-mins[1],maxs[2]-mins[2]];var mx=0,ch=0;
        for(var c=0;c<3;c++){if(rng[c]>mx){mx=rng[c];ch=c;}}if(mx>maxR){maxR=mx;maxI=bi;}}
        if(maxR<=0)break;var bucket=buckets.splice(maxI,1)[0];
        var mins2=[255,255,255],maxs2=[0,0,0];for(var i=0;i<bucket.length;i++){for(var c=0;c<3;c++){if(bucket[i][c]<mins2[c])mins2[c]=bucket[i][c];if(bucket[i][c]>maxs2[c])maxs2[c]=bucket[i][c];}}
        var rng2=[maxs2[0]-mins2[0],maxs2[1]-mins2[1],maxs2[2]-mins2[2]];var ch2=0;for(var c=1;c<3;c++){if(rng2[c]>rng2[ch2])ch2=c;}
        bucket.sort(function(a,b){return a[ch2]-b[ch2];});var mid=Math.floor(bucket.length/2);buckets.push(bucket.slice(0,mid),bucket.slice(mid));}
        var res=[];for(var bi=0;bi<buckets.length;bi++){var rr=0,gg=0,bb=0;for(var i=0;i<buckets[bi].length;i++){rr+=buckets[bi][i][0];gg+=buckets[bi][i][1];bb+=buckets[bi][i][2];}var n=buckets[bi].length||1;res.push([Math.round(rr/n),Math.round(gg/n),Math.round(bb/n)]);}return res;
    }
    function quantizePixels(pixels,numColors){
        var len=pixels.length;var rgb=[];
        for(var i=0;i<len;i+=4){if(pixels[i+3]===0)continue;rgb.push([pixels[i],pixels[i+1],pixels[i+2]]);}
        if(rgb.length===0)return{palette:[0],map:new Uint8Array(len/4)};
        var palette=medianCut(rgb,numColors);var map=new Uint8Array(len/4);
        for(var i=0;i<len;i+=4){var idx=i/4;if(pixels[i+3]===0){map[idx]=0;continue;}
        var best=0,bestD=Infinity;for(var p=0;p<palette.length;p++){
        var dr=pixels[i]-palette[p][0],dg=pixels[i+1]-palette[p][1],db=pixels[i+2]-palette[p][2];var d=dr*dr+dg*dg+db*db;
        if(d<bestD){bestD=d;best=p;}if(d===0)break;}map[idx]=best;}
        var palPacked=[];for(var p=0;p<palette.length;p++){palPacked.push((palette[p][0]<<16)|(palette[p][1]<<8)|palette[p][2]);}
        return{palette:palPacked,map:map};
    }
    function scaleCanvas(src,pct){if(pct>=100)return src;var w=Math.round(src.width*pct/100);var h=Math.round(src.height*pct/100);
    if(w<1)w=1;if(h<1)h=1;var c=document.createElement('canvas');c.width=w;c.height=h;var ctx=c.getContext('2d');ctx.imageSmoothingEnabled=true;ctx.drawImage(src,0,0,w,h);return c;}
    function nextPow2(n){var p=2;while(p<n)p<<=1;return p;}
    function encodeGif(decoded,numColors,scalePct,skipFrames,loop){
        var outW=Math.round(decoded.width*scalePct/100);var outH=Math.round(decoded.height*scalePct/100);
        if(outW<1)outW=1;if(outH<1)outH=1;
        numColors=Math.max(2,Math.min(256,nextPow2(numColors)));
        var bufSize=outW*outH*decoded.frames.length*5+1024;var buf=new Uint8Array(bufSize);
        var writer=new omggif.GifWriter(buf,outW,outH,{loop:loop?0:null});
        for(var i=0;i<decoded.frames.length;i++){
            if(skipFrames>0&&i>0&&i%(skipFrames+1)!==0)continue;
            var frame=decoded.frames[i];
            var src=document.createElement('canvas');src.width=decoded.width;src.height=decoded.height;
            var sCtx=src.getContext('2d');var imgD=sCtx.createImageData(decoded.width,decoded.height);imgD.data.set(frame.data);sCtx.putImageData(imgD,0,0);
            var scaled=scaleCanvas(src,scalePct);
            var sCtx2=scaled.getContext('2d');var sData=sCtx2.getImageData(0,0,scaled.width,scaled.height);
            var qResult=quantizePixels(sData.data,numColors);
            var delay=Math.round(frame.delay/10)||10;if(delay<2)delay=2;
            var pw=nextPow2(Math.max(2,qResult.palette.length));
            while(qResult.palette.length<pw)qResult.palette.push(0);
            try{writer.addFrame(0,0,scaled.width,scaled.height,qResult.map,{palette:qResult.palette,delay:delay,disposal:2});}catch(e){break;}
        }
        var endPos=writer.end();
        return new Uint8Array(buf.buffer,0,endPos);
    }
    function showResult(origSize,blob,usedOrig){
        var cSize=blob.size;var saved=origSize>cSize?((1-cSize/origSize)*100).toFixed(1):'0';
        setStat('tc-gif-stat-orig',TCTP.formatSize(origSize));setStat('tc-gif-stat-comp',TCTP.formatSize(cSize));setStat('tc-gif-stat-saved',saved+'%');
        TCTP.updateResultPanel(TCTP.formatSize(origSize),TCTP.formatSize(cSize),saved+'%','Done');
        TCTP.showResultPreview(URL.createObjectURL(blob));TCTP.switchToResultTab();TCTP.setProgress('tc-gif-progress',100,'Done!');
        if(usedOrig){TCTP.toast('Original is already optimal. No compression applied.','\u2139\uFE0F');}
        else if(saved!=='0'){TCTP.toast('Compressed! Saved '+saved+'%');}else{TCTP.toast('Image is already optimally compressed.');}
        compressedBlob=blob;var dl=document.getElementById('tc-gif-download');if(dl)dl.style.display='';
    }
    var compressBtn=document.getElementById('tc-gif-compress');
    if(compressBtn){compressBtn.addEventListener('click',function(){
        if(!file){TCTP.toast('Please select a GIF file first.','\u26A0\uFE0F');return;}
        if(typeof omggif==='undefined'){TCTP.toast('Library still loading, please try again.','\u26A0\uFE0F');return;}
        var numColors=parseInt(colorsSlider?colorsSlider.value:'64',10)||64;
        var scalePct=parseInt(scaleSlider?scaleSlider.value:'100',10)||100;
        var skipFrames=parseInt(skipSlider?skipSlider.value:'0',10)||0;
        var loop=loopCheck?loopCheck.checked:true;
        TCTP.showProgress('tc-gif-progress');TCTP.setProgress('tc-gif-progress',10,'Reading GIF...');
        readFileBuffer(file).then(function(buffer){
            TCTP.setProgress('tc-gif-progress',25,'Decoding frames...');
            var decoded=decodeGif(buffer);
            if(!decoded||decoded.frames.length===0){TCTP.hideProgress('tc-gif-progress');TCTP.toast('Failed to decode GIF.','\u274C');return;}
            TCTP.setProgress('tc-gif-progress',40,'Encoding with '+numColors+' colors...');
            var encoded=encodeGif(decoded,numColors,scalePct,skipFrames,loop);
            var blob=new Blob([encoded],{type:'image/gif'});
            if(blob.size>=file.size){showResult(file.size,file,true);return;}
            showResult(file.size,blob,false);
        }).catch(function(err){
            TCTP.hideProgress('tc-gif-progress');TCTP.toast('Failed: '+(err.message||'Unknown error'),'\u274C');
        });
    });}
    var downloadBtn=document.getElementById('tc-gif-download');
    if(downloadBtn){downloadBtn.addEventListener('click',function(){
        if(!compressedBlob){TCTP.toast('Nothing to download yet.','\u26A0\uFE0F');return;}
        var nameInput=document.getElementById('tc-gif-name');
        var base=(nameInput&&nameInput.value.trim())?nameInput.value.trim().replace(/\.gif$/i,''):(file?file.name.replace(/\.gif$/i,''):'animation');
        TCTP.downloadBlob(compressedBlob,base+'-compressed.gif');
    });}
    var clearBtn=document.getElementById('tc-gif-clear');
    if(clearBtn){clearBtn.addEventListener('click',function(){
        file=null;compressedBlob=null;
        TCTP.hideFileRow('tc-gif-file');
        resetStats();
        var dl=document.getElementById('tc-gif-download');if(dl)dl.style.display='none';
        var prevOrig=document.getElementById('tc-preview-orig');if(prevOrig)prevOrig.innerHTML='<span style="color:var(--muted);font-size:13px">Original preview will appear here</span>';
        var prevRes=document.getElementById('tc-preview-result');if(prevRes)prevRes.innerHTML='<span style="color:var(--muted);font-size:13px">Result preview will appear here</span>';
        TCTP.switchToOriginalTab();
        var nameInput=document.getElementById('tc-gif-name');if(nameInput)nameInput.value='';
    });}
})();
