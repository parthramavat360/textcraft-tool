<?php
/**
 * Widget: Find and Replace Text
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;

class Widget_Find_Replace extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_find_replace'; }
    public function get_title(): string { return esc_html__( 'Find and Replace', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-search'; }
    protected function render_tool_content( array $settings ): void {
        echo '<p class="tc-text-14 tc-text-muted tc-mb-16">'
            . esc_html__( 'Search and replace words or phrases in any text. Supports case-sensitive matching, whole-word mode, and regular expressions. Your text stays private and is processed entirely in your browser.', 'textcraft-tools' )
            . '</p>';
        echo '<div class="tc-grid-2col tc-mb-16">';
        echo '<div><label class="tc-label tc-d-block tc-mb-6">'.esc_html__('Find','textcraft-tools').'</label><input type="text" id="tc-fr-find" class="tc-text-input" placeholder="Enter text or pattern to find…"></div>';
        echo '<div><label class="tc-label tc-d-block tc-mb-6">'.esc_html__('Replace With','textcraft-tools').'</label><input type="text" id="tc-fr-replace" class="tc-text-input" placeholder="Replacement text (leave blank to delete matches)"></div>';
        echo '</div>';
        $this->render_options_row([
            ['id'=>'tc-fr-case','label'=>esc_html__('Case-sensitive','textcraft-tools'),'checked'=>false],
            ['id'=>'tc-fr-whole','label'=>esc_html__('Whole word only','textcraft-tools'),'checked'=>false],
            ['id'=>'tc-fr-regex','label'=>esc_html__('Use regex','textcraft-tools'),'checked'=>false],
            ['id'=>'tc-fr-all','label'=>esc_html__('Replace all occurrences','textcraft-tools'),'checked'=>true],
        ]);
        $this->render_textarea('tc-fr-input',esc_html__('Your Text','textcraft-tools'),esc_html__('Paste or type the text you want to search through…','textcraft-tools'),5);
        $this->render_button_row([
            ['id'=>'tc-fr-do','label'=>'🔎 '.esc_html__('Replace','textcraft-tools'),'variant'=>'primary'],
            ['id'=>'tc-fr-copy','label'=>'📋 '.esc_html__('Copy Result','textcraft-tools'),'variant'=>'ghost'],
            ['id'=>'tc-fr-clear','label'=>'🗑️ '.esc_html__('Clear','textcraft-tools'),'variant'=>'danger'],
        ]);
        $this->render_stat_bar([
            ['id'=>'tc-fr-matches','label'=>esc_html__('Matches Found','textcraft-tools')],
            ['id'=>'tc-fr-replaced','label'=>esc_html__('Replacements Made','textcraft-tools')],
        ]);
        echo '<div id="tc-fr-err" class="tc-fr-error"></div>';
        echo '<div class="tc-label-row tc-mt-20"><span class="tc-label">'.esc_html__('Result','textcraft-tools').'</span></div>';
        $this->render_textarea('tc-fr-output','','',5,true);
        $this->render_inline_script(<<<'JS'
var inp=document.getElementById('tc-fr-input'),out=document.getElementById('tc-fr-output'),err=document.getElementById('tc-fr-err');
if(inp){
    inp.addEventListener('input',function(){var cc=inp.closest('.tc-tool-card').querySelector('.tc-char-count');if(cc)cc.textContent=inp.value.length+' characters';});
    document.getElementById('tc-fr-do').addEventListener('click',function(){
        err.style.display='none';
        var findStr=document.getElementById('tc-fr-find').value;
        if(!findStr){err.textContent='Please enter a search term or pattern to find.';err.style.display='block';return;}
        var repStr=document.getElementById('tc-fr-replace').value;
        var cs=document.getElementById('tc-fr-case').checked;
        var whole=document.getElementById('tc-fr-whole').checked;
        var regex=document.getElementById('tc-fr-regex').checked;
        var all=document.getElementById('tc-fr-all').checked;
        var src=regex?findStr:findStr.replace(/[.*+?^${}()|[\]\\]/g,'\\$&');
        if(whole&&!regex)src='\\b'+src+'\\b';
        var flags=(cs?'':'i')+(all?'g':'');
        try{
            var re=new RegExp(src,flags);
            var count=0;
            var result=inp.value.replace(re,function(m){count++;return repStr;});
            out.value=result;
            document.getElementById('tc-fr-matches').textContent=count;
            document.getElementById('tc-fr-replaced').textContent=count;
        }catch(e){err.textContent='Invalid regex: '+e.message;err.style.display='block';}
    });
    document.getElementById('tc-fr-copy').addEventListener('click',function(){if(out.value)navigator.clipboard.writeText(out.value);});
    document.getElementById('tc-fr-clear').addEventListener('click',function(){inp.value='';out.value='';document.getElementById('tc-fr-find').value='';document.getElementById('tc-fr-replace').value='';['tc-fr-matches','tc-fr-replaced'].forEach(function(id){document.getElementById(id).textContent='0';});});
}
JS);
    }
}
