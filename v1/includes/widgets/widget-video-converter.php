<?php
/**
 * Widget: Online Video Converter
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;

class Widget_Video_Converter extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_video_converter'; }
    public function get_title(): string { return esc_html__( 'Video Converter', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-video-camera'; }
    protected function render_tool_content( array $settings ): void {
        echo '<div id="tc-vc-drop" class="tc-vc-drop-zone" role="button" tabindex="0">';
        echo '<div class="tc-drop-icon">🎬</div>';
        echo '<p class="tc-text-15 tc-font-semibold tc-text-primary tc-m-0-6">'.esc_html__('Click to upload or drag and drop your video file','textcraft-tools').'</p>';
        echo '<p class="tc-text-13 tc-text-muted tc-m-0-4">'.esc_html__('Convert video files between formats — MP4, WebM, AVI, MOV, MKV, and GIF. Powered by FFmpeg.wasm for client-side conversion with complete privacy — no files are ever uploaded.','textcraft-tools').'</p>';
        echo '<p class="tc-text-12 tc-text-muted tc-m-0">⚠️ '.esc_html__('Large files may take several minutes — conversion runs entirely on your device for security','textcraft-tools').'</p>';
        echo '<input type="file" id="tc-vc-upload" accept="video/*" class="tc-d-none"></div>';
        echo '<div id="tc-vc-info" class="tc-p-14-16 tc-vc-info tc-hidden">';
        echo '<div class="tc-d-flex tc-items-center tc-gap-12"><span class="tc-text-28">🎬</span><div><p id="tc-vc-fname" class="tc-text-14 tc-font-bold tc-text-primary tc-m-0 tc-mb-4"></p><p id="tc-vc-fmeta" class="tc-text-12 tc-text-muted tc-m-0"></p></div></div></div>';
        echo '<div class="tc-settings-grid">';
        echo '<div><label class="tc-label tc-d-block tc-mb-6">'.esc_html__('Output Format','textcraft-tools').'</label><select id="tc-vc-format" class="tc-text-input"><option value="mp4">MP4 (H.264)</option><option value="webm">WebM (VP8)</option><option value="avi">AVI</option><option value="mov">MOV</option><option value="gif">GIF (first 10s)</option><option value="mp3">MP3 (audio only)</option></select></div>';
        echo '<div><label class="tc-label tc-d-block tc-mb-6">'.esc_html__('Quality (CRF — lower = better)','textcraft-tools').'</label><div class="tc-d-flex tc-items-center tc-gap-10"><input type="range" id="tc-vc-crf" min="18" max="51" value="28" class="tc-slider"><span id="tc-vc-crfval" class="tc-accent-value">28</span></div></div>';
        echo '</div>';
        $this->render_button_row([
            ['id'=>'tc-vc-convert','label'=>'🎬 '.esc_html__('Convert Video','textcraft-tools'),'variant'=>'primary','disabled'=>true],
            ['id'=>'tc-vc-clear','label'=>'🗑️ '.esc_html__('Clear','textcraft-tools'),'variant'=>'danger'],
        ]);
        echo '<div id="tc-vc-progress" class="tc-mt-16 tc-mb-16 tc-hidden"><div class="tc-d-flex tc-justify-between tc-mb-6"><span id="tc-vc-plabel" class="tc-text-13 tc-text-muted">Loading FFmpeg…</span><span id="tc-vc-ppct" class="tc-text-13 tc-accent-value">0%</span></div><div class="tc-progress-bg"><div id="tc-vc-pbar" class="tc-progress-fill--gradient"></div></div></div>';
        echo '<div id="tc-vc-result" class="tc-vc-result tc-hidden"><p class="tc-text-15 tc-font-bold tc-text-primary tc-m-0 tc-mb-14">✅ '.esc_html__('Conversion Complete!','textcraft-tools').'</p><div id="tc-vc-dl"></div></div>';
        $this->render_inline_script(<<<'JS'
var drop=document.getElementById('tc-vc-drop'),fileInp=document.getElementById('tc-vc-upload');
var btnConv=document.getElementById('tc-vc-convert'),prog=document.getElementById('tc-vc-progress');
var pbar=document.getElementById('tc-vc-pbar'),ppct=document.getElementById('tc-vc-ppct'),plabel=document.getElementById('tc-vc-plabel');
var uploadedFile=null;
document.getElementById('tc-vc-crf').addEventListener('input',function(){document.getElementById('tc-vc-crfval').textContent=this.value;});
drop.addEventListener('click',function(){fileInp.click();});
drop.addEventListener('dragover',function(e){e.preventDefault();drop.style.borderColor='var(--tc-accent)';});
drop.addEventListener('dragleave',function(){drop.style.borderColor='';});
drop.addEventListener('drop',function(e){e.preventDefault();drop.style.borderColor='';if(e.dataTransfer.files[0])loadFile(e.dataTransfer.files[0]);});
fileInp.addEventListener('change',function(){if(fileInp.files[0])loadFile(fileInp.files[0]);});
function loadFile(file){
    uploadedFile=file;
    var info=document.getElementById('tc-vc-info');
    document.getElementById('tc-vc-fname').textContent=file.name;
    document.getElementById('tc-vc-fmeta').textContent=(file.size/1024/1024).toFixed(2)+' MB';
    info.style.display='flex';btnConv.disabled=false;
}
btnConv.addEventListener('click',function(){
    if(!uploadedFile){return;}
    // FFmpeg.wasm requires SharedArrayBuffer (COOP/COEP headers).
    // We detect support and warn gracefully if unavailable.
    if(typeof SharedArrayBuffer==='undefined'){
        document.getElementById('tc-vc-result').style.display='block';
        document.getElementById('tc-vc-dl').innerHTML='<p class="tc-text-14 tc-text-error">⚠️ Video conversion requires <strong>Cross-Origin Isolation</strong> headers (COOP + COEP) on your server. Please contact your host or system administrator to enable these headers for this page.</p>';
        return;
    }
    prog.style.display='block';btnConv.disabled=true;
    plabel.textContent='Loading FFmpeg…';pbar.style.width='5%';ppct.textContent='5%';
    var script=document.createElement('script');
    script.src='https://unpkg.com/@ffmpeg/ffmpeg@0.12.6/dist/umd/ffmpeg.js';
    script.onload=async function(){
        try{
            var fmt=document.getElementById('tc-vc-format').value;
            var crf=document.getElementById('tc-vc-crf').value;
            var {createFFmpeg,fetchFile}=FFmpeg;
            var ff=createFFmpeg({log:false,progress:function(p){var pct=Math.round((p.ratio||0)*100);pbar.style.width=pct+'%';ppct.textContent=pct+'%';plabel.textContent='Converting…';}});
            await ff.load();
            ff.FS('writeFile','input.'+uploadedFile.name.split('.').pop(),await fetchFile(uploadedFile));
            var outName='output.'+fmt;
            var args=['-i','input.'+uploadedFile.name.split('.').pop(),'-crf',crf];
            if(fmt==='gif')args.push('-t','10','-vf','fps=10,scale=480:-1:flags=lanczos');
            if(fmt==='mp3')args.push('-vn','-q:a','2');
            args.push(outName);
            await ff.run(...args);
            var data=ff.FS('readFile',outName);
            var blob=new Blob([data.buffer],{type:'video/'+fmt});
            var url=URL.createObjectURL(blob);
            document.getElementById('tc-vc-result').style.display='block';
            document.getElementById('tc-vc-dl').innerHTML='<a href="'+url+'" download="converted.'+fmt+'" class="tc-btn tc-btn--primary">⬇️ Download '+fmt.toUpperCase()+'</a>';
            prog.style.display='none';btnConv.disabled=false;
        }catch(e){plabel.textContent='Error: '+e.message;btnConv.disabled=false;}
    };
    document.head.appendChild(script);
});
document.getElementById('tc-vc-clear').addEventListener('click',function(){uploadedFile=null;fileInp.value='';document.getElementById('tc-vc-info').style.display='none';document.getElementById('tc-vc-result').style.display='none';prog.style.display='none';btnConv.disabled=true;});
JS);
    }
}
