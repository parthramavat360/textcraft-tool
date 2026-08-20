<?php
/**
 * Widget: Title Case Converter
 * Converts text using AP/Chicago style title-case rules.
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;

class Widget_Title_Case extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_title_case'; }
    public function get_title(): string { return esc_html__( 'Title Case Converter', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-editor-h2'; }
    protected function render_tool_content( array $settings ): void {
        echo '<p class="tc-text-14 tc-text-muted tc-mb-20">'
            . esc_html__( 'Automatically convert text to title case using standard capitalization rules. Perfect for formatting headlines, article titles, and heading text — all processed privately in your browser.', 'textcraft-tools' )
            . '</p>';
        $this->render_textarea( 'tc-tc-input', esc_html__( 'Your Text', 'textcraft-tools' ), esc_html__( 'Type or paste your text here to convert to title case…', 'textcraft-tools' ), 8 );
        $this->render_button_row( [
            [ 'id' => 'tc-tc-convert', 'label' => '📰 ' . esc_html__( 'Convert', 'textcraft-tools' ), 'variant' => 'primary' ],
            [ 'id' => 'tc-tc-copy',    'label' => '📋 ' . esc_html__( 'Copy',    'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-tc-clear',   'label' => '🗑️ ' . esc_html__( 'Clear',   'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );
        $this->render_stat_bar( [
            [ 'id' => 'tc-tc-words', 'label' => esc_html__( 'Words',      'textcraft-tools' ) ],
            [ 'id' => 'tc-tc-chars', 'label' => esc_html__( 'Characters', 'textcraft-tools' ) ],
        ] );
        echo '<div class="tc-label-row tc-mt-20"><span class="tc-label">' . esc_html__( 'Result', 'textcraft-tools' ) . '</span></div>';
        $this->render_textarea( 'tc-tc-output', '', esc_html__( 'Title Case result will appear here…', 'textcraft-tools' ), 8, true );
        $this->render_inline_script( <<<'JS'
var inp=document.getElementById('tc-tc-input'),out=document.getElementById('tc-tc-output');
if(inp){
    var minor=new Set(['a','an','the','and','but','or','for','nor','on','at','to','by','in','of','up','as','vs','via','per','yet','so']);
    inp.addEventListener('input',function(){ var cc=inp.closest('.tc-tool-card').querySelector('.tc-char-count'); if(cc) cc.textContent=inp.value.length+' characters'; });
    document.getElementById('tc-tc-convert').addEventListener('click',function(){
        var result=inp.value.toLowerCase().split(' ').map(function(w,i){
            if(i===0||!minor.has(w)) return w.replace(/^\w/,function(c){return c.toUpperCase();});
            return w;
        }).join(' ');
        out.value=result;
        document.getElementById('tc-tc-words').textContent=inp.value.trim().split(/\s+/).filter(Boolean).length;
        document.getElementById('tc-tc-chars').textContent=inp.value.length;
    });
    document.getElementById('tc-tc-copy').addEventListener('click',function(){ if(out.value) navigator.clipboard.writeText(out.value); });
    document.getElementById('tc-tc-clear').addEventListener('click',function(){ inp.value=''; out.value=''; ['tc-tc-words','tc-tc-chars'].forEach(function(id){document.getElementById(id).textContent='0';}); });
}
JS );
    }
}
