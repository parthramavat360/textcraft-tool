<?php
/**
 * Widget: Character Remover
 *
 * Matches the original PHP tool exactly:
 *  - Quick-select preset buttons (Spaces, Punctuation, Numbers,
 *    Special Chars, Quotes) that populate the characters input
 *  - Free-text "Characters to Remove" input (monospaced)
 *  - Case-sensitive checkbox
 *  - Character-by-character removal (iterates each char in the
 *    pattern string and strips it — same logic as the PHP original)
 *  - Output is written back into the same input textarea
 *  - Stats bar: Removed · Original · Result (character counts)
 *
 * @package TextCraft_Tools\Widgets
 * @version 1.0.0
 */

declare( strict_types = 1 );

namespace TextCraft_Tools\Widgets;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

/** Elementor widget: Character Remover */
class Widget_Character_Remover extends TextCraft_Base_Widget {

    // ── Identity ──────────────────────────────────────────────

    public function get_name(): string  { return 'textcraft_character_remover'; }
    public function get_title(): string { return esc_html__( 'Character Remover', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-cursor-move'; }

    public function get_keywords(): array {
        return [ 'character', 'remover', 'remove', 'delete', 'strip', 'clean', 'text', 'free online text tool', 'character cleaner' ];
    }

    // ── Extra panel controls ──────────────────────────────────

    /** Add widget-specific content controls inside the base Content section. */
    protected function register_tool_controls(): void {

        // ── Preset buttons ────────────────────────────────────
        $this->add_control(
            'show_presets',
            [
                'label'        => esc_html__( 'Show Preset Buttons', 'textcraft-tools' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Yes', 'textcraft-tools' ),
                'label_off'    => esc_html__( 'No', 'textcraft-tools' ),
                'return_value' => 'yes',
                'default'      => 'yes',
                'description'  => esc_html__( 'Show quick-select preset buttons for common character types to remove.', 'textcraft-tools' ),
            ]
        );

        // ── Textarea rows ─────────────────────────────────────
        $this->add_control(
            'textarea_rows',
            [
                'label'   => esc_html__( 'Textarea Rows', 'textcraft-tools' ),
                'type'    => Controls_Manager::NUMBER,
                'default' => 8,
                'min'     => 3,
                'max'     => 30,
            ]
        );
    }

    // ── Render ────────────────────────────────────────────────

    /** @inheritDoc */
    protected function render_tool_content( array $settings ): void {

        $show_presets = ( 'yes' === ( $settings['show_presets'] ?? 'yes' ) );
        $rows         = max( 3, (int) ( $settings['textarea_rows'] ?? 8 ) );

        // ── Tool description ─────────────────────────────────
        echo '<p class="tc-text-14 tc-text-muted tc-mb-16">'
            . esc_html__( 'Remove unwanted characters from your text in seconds. Choose from quick presets or type your own — all processing is done securely in your browser.', 'textcraft-tools' )
            . '</p>';

        // ── "Characters to Remove" section ───────────────────
        echo '<div class="tc-cr-chars-section">';

        echo '<label class="tc-label tc-d-block tc-mb-10">'
            . esc_html__( 'Characters to Remove', 'textcraft-tools' )
            . '</label>';

        // ── Quick-select preset buttons ───────────────────────
        if ( $show_presets ) {
            /**
             * Presets exactly match the original PHP:
             *  data-char=" "                  → Spaces
             *  data-char=".,!?;:"             → Punctuation
             *  data-char="0123456789"          → Numbers
             *  data-char="@#$%^&*()_+-=[]{}|" → Special Chars
             *  data-char='"                    → Quotes
             */
            $presets = [
                [ 'label' => esc_html__( 'Spaces',       'textcraft-tools' ), 'chars' => ' '                     ],
                [ 'label' => esc_html__( 'Punctuation',  'textcraft-tools' ), 'chars' => '.,!?;:'                ],
                [ 'label' => esc_html__( 'Numbers',      'textcraft-tools' ), 'chars' => '0123456789'             ],
                [ 'label' => esc_html__( 'Special Chars','textcraft-tools' ), 'chars' => '@#$%^&*()_+-=[]{}|'    ],
                [ 'label' => esc_html__( 'Quotes',       'textcraft-tools' ), 'chars' => '\'"'                   ],
            ];

            echo '<div class="tc-cr-presets" role="group" aria-label="' . esc_attr__( 'Quick character presets', 'textcraft-tools' ) . '">';
            foreach ( $presets as $preset ) {
                printf(
                    '<button type="button" class="tc-btn tc-btn--secondary tc-cr-preset-btn" data-chars="%s" aria-label="%s">%s</button>',
                    esc_attr( $preset['chars'] ),
                    /* translators: %s: preset label */
                    esc_attr( sprintf( __( 'Remove %s', 'textcraft-tools' ), $preset['label'] ) ),
                    esc_html( $preset['label'] )
                );
            }
            echo '</div>'; // .tc-cr-presets
        }

        // ── Characters input (monospaced) ─────────────────────
        echo '<input'
            . ' type="text"'
            . ' id="tc-cr-chars"'
            . ' class="tc-text-input tc-cr-chars-input"'
             . ' placeholder="' . esc_attr__( 'Type characters to remove, e.g. @#$ or spaces', 'textcraft-tools' ) . '"'
            . ' autocomplete="off"'
            . ' spellcheck="false"'
            . '>';

        // ── Case-sensitive checkbox ───────────────────────────
        echo '<div class="tc-cr-options">';
        echo '<label class="tc-option">';
        echo '<input type="checkbox" id="tc-cr-case-sensitive">';
        echo '<span>' . esc_html__( 'Case-sensitive removal', 'textcraft-tools' ) . '</span>';
        echo '</label>';
        echo '</div>'; // .tc-cr-options

        echo '</div>'; // .tc-cr-chars-section

        // ── Main textarea (input + output combined) ───────────
        echo '<div class="tc-label-row">';
        echo '<label for="tc-cr-input" class="tc-label">' . esc_html__( 'Your Text', 'textcraft-tools' ) . '</label>';
        echo '<span class="tc-char-count" id="tc-cr-char-count" aria-live="polite">0 ' . esc_html__( 'characters', 'textcraft-tools' ) . '</span>';
        echo '</div>';

        echo '<textarea'
            . ' id="tc-cr-input"'
            . ' class="tc-textarea tc-textarea--input"'
            . ' placeholder="' . esc_attr__( 'Paste or type your text to remove unwanted characters…', 'textcraft-tools' ) . '"'
            . ' rows="' . esc_attr( (string) $rows ) . '"'
            . ' spellcheck="false"'
            . '></textarea>';

        // ── Action buttons ────────────────────────────────────
        $this->render_button_row( [
            [
                'id'      => 'tc-cr-remove',
                'label'   => '✂️ ' . esc_html__( 'Remove Characters', 'textcraft-tools' ),
                'variant' => 'primary',
            ],
            [
                'id'      => 'tc-cr-copy',
                'label'   => '📋 ' . esc_html__( 'Copy', 'textcraft-tools' ),
                'variant' => 'ghost',
            ],
            [
                'id'      => 'tc-cr-clear',
                'label'   => '🗑️ ' . esc_html__( 'Clear', 'textcraft-tools' ),
                'variant' => 'danger',
            ],
        ] );

        // ── Stats bar: Removed · Original · Result ────────────
        // Matches original PHP exactly: stat-removed, stat-orig, stat-result
        $this->render_stat_bar( [
            [ 'id' => 'tc-cr-stat-removed', 'label' => esc_html__( 'Removed',  'textcraft-tools' ) ],
            [ 'id' => 'tc-cr-stat-orig',    'label' => esc_html__( 'Original', 'textcraft-tools' ) ],
            [ 'id' => 'tc-cr-stat-result',  'label' => esc_html__( 'Result',   'textcraft-tools' ) ],
        ] );

        // ── Inline JavaScript ─────────────────────────────────
        $this->render_inline_script( $this->get_script() );
    }

    // ── JavaScript ────────────────────────────────────────────

    /**
     * Return the self-contained widget JS.
     *
     * Removal logic matches the original PHP exactly:
     *   for(const c of chars) { result = result.split(c).join(''); }
     * This iterates every character in the pattern string individually
     * and strips each one — NOT a regex match.
     */
    private function get_script(): string {
        return <<<'JS'
/* ── TextCraft: Character Remover ── */
(function(){
'use strict';

// ── Scope all queries to this widget's card ────────────────
var card = (function(){
    var el = document.getElementById('tc-cr-chars');
    return el ? el.closest('.tc-tool-card') : null;
})();
if(!card) return;

var inp       = card.querySelector('#tc-cr-input');
var charsInp  = card.querySelector('#tc-cr-chars');
var charCount = card.querySelector('#tc-cr-char-count');
var btnRemove = card.querySelector('#tc-cr-remove');
var btnCopy   = card.querySelector('#tc-cr-copy');
var btnClear  = card.querySelector('#tc-cr-clear');
var caseCb    = card.querySelector('#tc-cr-case-sensitive');
var presets   = card.querySelectorAll('.tc-cr-preset-btn');

if(!inp || !charsInp) return;

// ── Helpers ────────────────────────────────────────────────
function setCount(text){
    if(charCount) charCount.textContent = text.length + (text.length === 1 ? ' character' : ' characters');
}

function setStat(id, val){
    var el = card.querySelector('#' + id);
    if(el) el.textContent = val;
}

// ── Live character count on input ──────────────────────────
inp.addEventListener('input', function(){
    setCount(inp.value);
});

// ── Preset buttons — populate the chars input ──────────────
presets.forEach(function(btn){
    btn.addEventListener('click', function(){
        charsInp.value = btn.dataset.chars;
        charsInp.focus();
    });
});

// ── Remove button ──────────────────────────────────────────
btnRemove.addEventListener('click', function(){
    var chars = charsInp.value;

    if(!chars){
        // Nothing to remove — show alert matching original behaviour.
        alert('Please enter some characters to remove first.');
        return;
    }

    var orig   = inp.value;
    var result = orig;
    var isCaseSensitive = caseCb && caseCb.checked;

    if(isCaseSensitive){
        // Case-sensitive: strip each character exactly as typed.
        for(var i = 0; i < chars.length; i++){
            var c = chars[i];
            result = result.split(c).join('');
        }
    } else {
        // Case-insensitive: strip both upper and lower variants of each char.
        for(var j = 0; j < chars.length; j++){
            var ch = chars[j];
            result = result.split(ch.toLowerCase()).join('');
            result = result.split(ch.toUpperCase()).join('');
        }
    }

    var removed = orig.length - result.length;

    // Write result back into the same textarea (matches original).
    inp.value = result;
    setCount(result);

    // Update stats bar.
    setStat('tc-cr-stat-removed', removed);
    setStat('tc-cr-stat-orig',    orig.length);
    setStat('tc-cr-stat-result',  result.length);
});

// ── Copy button ────────────────────────────────────────────
btnCopy.addEventListener('click', function(){
    if(!inp.value) return;
    navigator.clipboard.writeText(inp.value).then(function(){
        var prev = btnCopy.textContent;
        btnCopy.textContent = '✅ Copied!';
        setTimeout(function(){ btnCopy.textContent = prev; }, 2000);
    });
});

// ── Clear button ───────────────────────────────────────────
btnClear.addEventListener('click', function(){
    inp.value      = '';
    charsInp.value = '';
    setCount('');
    setStat('tc-cr-stat-removed', 0);
    setStat('tc-cr-stat-orig',    0);
    setStat('tc-cr-stat-result',  0);
    inp.focus();
});

})();
JS;
    }
}