<?php
/**
 * Widget: Duplicate Line Remover
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;

class Widget_Duplicate_Line extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_duplicate_line'; }
    public function get_title(): string { return esc_html__( 'Duplicate Line Remover', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-editor-list-ul'; }
    protected function render_tool_content( array $settings ): void {
        echo '<p class="tc-text-14 tc-text-muted tc-mb-16">'
            . esc_html__( 'Remove repeated lines from any text in one click. Choose whether to sort results, trim whitespace, or keep blank lines — all done privately in your browser.', 'textcraft-tools' )
            . '</p>';
        $this->render_options_row( [
            [ 'id' => 'tc-dl-case',  'label' => esc_html__( 'Case-sensitive',     'textcraft-tools' ), 'checked' => false ],
            [ 'id' => 'tc-dl-trim',  'label' => esc_html__( 'Trim whitespace',    'textcraft-tools' ), 'checked' => true  ],
            [ 'id' => 'tc-dl-blank', 'label' => esc_html__( 'Remove blank lines', 'textcraft-tools' ), 'checked' => false ],
            [ 'id' => 'tc-dl-sort', 'label' => esc_html__( 'Sort results A–Z', 'textcraft-tools' ), 'checked' => false ],
        ] );
        $this->render_textarea( 'tc-dl-input', esc_html__( 'Your Text', 'textcraft-tools' ), esc_html__( 'Paste or type text with duplicate lines to clean up…', 'textcraft-tools' ), 9 );
        $this->render_button_row( [
            [ 'id' => 'tc-dl-remove', 'label' => '📋 ' . esc_html__( 'Remove Duplicates', 'textcraft-tools' ), 'variant' => 'primary' ],
            [ 'id' => 'tc-dl-copy',   'label' => '📋 ' . esc_html__( 'Copy', 'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-dl-clear',  'label' => '🗑️ ' . esc_html__( 'Clear', 'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );
        $this->render_stat_bar( [
            [ 'id' => 'tc-dl-total',   'label' => esc_html__( 'Total Lines',   'textcraft-tools' ) ],
            [ 'id' => 'tc-dl-unique',  'label' => esc_html__( 'Unique Lines',  'textcraft-tools' ) ],
            [ 'id' => 'tc-dl-removed', 'label' => esc_html__( 'Removed',       'textcraft-tools' ) ],
        ] );
        echo '<div class="tc-label-row tc-mt-20"><span class="tc-label">' . esc_html__( 'Result', 'textcraft-tools' ) . '</span></div>';
        $this->render_textarea( 'tc-dl-output', '', '', 9, true );
        $this->render_inline_script( <<<'JS'
var inp=document.getElementById('tc-dl-input'),out=document.getElementById('tc-dl-output');
if(inp){
    inp.addEventListener('input',function(){ var cc=inp.closest('.tc-tool-card').querySelector('.tc-char-count'); if(cc) cc.textContent=inp.value.length+' characters'; });
    document.getElementById('tc-dl-remove').addEventListener('click',function(){
        var caseSens=document.getElementById('tc-dl-case').checked;
        var trim=document.getElementById('tc-dl-trim').checked;
        var removeBlank=document.getElementById('tc-dl-blank').checked;
        var sort = document.getElementById('tc-dl-sort').checked;
        var lines=inp.value.split('\n');
        var seen=new Set();
        var unique=lines.filter(function(line){
            var key=trim?line.trim():line;
            if(!caseSens) key=key.toLowerCase();
            if(removeBlank&&!key.trim()) return false;
            if(seen.has(key)) return false;
            seen.add(key); return true;
        });
        var result = sort ? [...unique].sort((a,b) => a.localeCompare(b)) : unique;
        out.value=result.join('\n');
        document.getElementById('tc-dl-total').textContent=lines.length;
        document.getElementById('tc-dl-unique').textContent=unique.length;
        document.getElementById('tc-dl-removed').textContent=lines.length-unique.length;
    });
    document.getElementById('tc-dl-copy').addEventListener('click',function(){ if(out.value) navigator.clipboard.writeText(out.value); });
    document.getElementById('tc-dl-clear').addEventListener('click',function(){ inp.value=''; out.value=''; ['tc-dl-total','tc-dl-unique','tc-dl-removed'].forEach(function(id){document.getElementById(id).textContent='0';}); });
}
JS );
    }
}
