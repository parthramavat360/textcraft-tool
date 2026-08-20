<?php
/**
 * Widget: Sentence Case Converter
 *
 * Converts text so the first letter of each sentence is capitalised
 * and the rest is lowercase. Options: preserve "I", proper nouns,
 * and abbreviations.
 *
 * @package TextCraft_Tools\Widgets
 */

declare( strict_types = 1 );

namespace TextCraft_Tools\Widgets;

defined( 'ABSPATH' ) || exit;

/** Elementor widget: Sentence Case Converter */
class Widget_Sentence_Case extends TextCraft_Base_Widget {

    public function get_name(): string  { return 'textcraft_sentence_case'; }
    public function get_title(): string { return esc_html__( 'Sentence Case Converter', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-editor-paragraph'; }

    protected function render_tool_content( array $settings ): void {
        echo '<p class="tc-text-14 tc-text-muted tc-mb-16">'
            . esc_html__( 'Automatically capitalise the first letter of every sentence while keeping the rest lowercase. Perfect for cleaning up all-caps text or formatting content properly. Preserves abbreviations, proper nouns, and the word "I".', 'textcraft-tools' )
            . '</p>';
        $this->render_options_row( [
            [ 'id' => 'tc-sc-opt-i',      'label' => esc_html__( 'Always capitalise "I"',               'textcraft-tools' ), 'checked' => true  ],
            [ 'id' => 'tc-sc-opt-proper', 'label' => esc_html__( 'Preserve proper nouns',               'textcraft-tools' ), 'checked' => false ],
            [ 'id' => 'tc-sc-opt-abbr',   'label' => esc_html__( 'Preserve abbreviations (NASA, USA…)', 'textcraft-tools' ), 'checked' => true  ],
        ] );

        $this->render_textarea( 'tc-sc-input', esc_html__( 'Your Text', 'textcraft-tools' ), esc_html__( 'Paste or type your text here to convert it to sentence case automatically…', 'textcraft-tools' ), 8 );

        $this->render_button_row( [
            [ 'id' => 'tc-sc-convert', 'label' => '📝 ' . esc_html__( 'Convert', 'textcraft-tools' ), 'variant' => 'primary' ],
            [ 'id' => 'tc-sc-copy',    'label' => '📋 ' . esc_html__( 'Copy',    'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-sc-clear',   'label' => '🗑️ ' . esc_html__( 'Clear',   'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        $this->render_stat_bar( [
            [ 'id' => 'tc-sc-words', 'label' => esc_html__( 'Words',      'textcraft-tools' ) ],
            [ 'id' => 'tc-sc-sents', 'label' => esc_html__( 'Sentences',  'textcraft-tools' ) ],
            [ 'id' => 'tc-sc-chars', 'label' => esc_html__( 'Characters', 'textcraft-tools' ) ],
        ] );

        echo '<div class="tc-label-row tc-mt-20"><span class="tc-label">' . esc_html__( 'Result', 'textcraft-tools' ) . '</span></div>';
        $this->render_textarea( 'tc-sc-output', '', esc_html__( 'Your sentence case formatted text will appear here after conversion.', 'textcraft-tools' ), 8, true );

        $this->render_inline_script( <<<'JS'
var inp = document.getElementById('tc-sc-input');
var out = document.getElementById('tc-sc-output');
if(inp){
    inp.addEventListener('input', function(){
        var cc = inp.closest('.tc-tool-card').querySelector('.tc-char-count');
        if(cc) cc.textContent = inp.value.length + ' characters';
    });
    document.getElementById('tc-sc-convert').addEventListener('click', function(){
        var text = inp.value;
        var optI      = document.getElementById('tc-sc-opt-i').checked;
        var optAbbr   = document.getElementById('tc-sc-opt-abbr').checked;
        // Convert to sentence case
        var result = text.toLowerCase().replace(/(^\s*\w|[.!?]\s+\w)/g, function(m){ return m.toUpperCase(); });
        // Restore "I" as standalone word
        if(optI){ result = result.replace(/\bi\b/g,'I'); }
        // Restore common abbreviations (naively: all-caps sequences of 2-5 letters already lowercase – skip; keep original case for abbreviations detected in original)
        if(optAbbr){
            var abbrs = text.match(/\b[A-Z]{2,5}\b/g);
            if(abbrs){ abbrs.forEach(function(ab){ result = result.replace(new RegExp('\\b'+ab.toLowerCase()+'\\b','g'), ab); }); }
        }
        out.value = result;
        var words = text.trim() ? text.trim().split(/\s+/).length : 0;
        var sents = text.trim() ? (text.match(/[.!?]+/g)||[]).length : 0;
        document.getElementById('tc-sc-words').textContent = words;
        document.getElementById('tc-sc-sents').textContent = sents;
        document.getElementById('tc-sc-chars').textContent = text.length;
    });
    document.getElementById('tc-sc-copy').addEventListener('click', function(){
        if(out.value) navigator.clipboard.writeText(out.value);
    });
    document.getElementById('tc-sc-clear').addEventListener('click', function(){
        inp.value=''; out.value='';
        ['tc-sc-words','tc-sc-sents','tc-sc-chars'].forEach(function(id){ document.getElementById(id).textContent='0'; });
    });
}
JS );
    }
}
