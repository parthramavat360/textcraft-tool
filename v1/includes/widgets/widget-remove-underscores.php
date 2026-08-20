<?php
/**
 * Widget: Remove Underscores
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;
class Widget_Remove_Underscores extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_remove_underscores'; }
    public function get_title(): string { return esc_html__( 'Underscore Remover', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-minus-circle-o'; }
    protected function render_tool_content( array $settings ): void {

        echo '<p class="tc-text-14 tc-text-muted tc-mb-16">'
            . esc_html__( 'Replace or remove underscores from your text in one click. Choose spaces, hyphens, or custom replacements. Perfect for cleaning up file names, database fields, and snake_case text.', 'textcraft-tools' )
            . '</p>';

        // --- Replace With buttons ---
        echo '<div class="tc-mb-20">';
        echo '<label class="tc-section-label tc-mb-12 tc-label-spaced">' . esc_html__( 'Replace Underscores With', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-8 tc-flex-wrap">';
        $opts = [
            ['val' => ' ',      'label' => esc_html__( 'Space', 'textcraft-tools' ),           'active' => true],
            ['val' => '',       'label' => esc_html__( 'Nothing (remove)', 'textcraft-tools' ), 'active' => false],
            ['val' => '-',      'label' => esc_html__( 'Hyphen (-)', 'textcraft-tools' ),       'active' => false],
            ['val' => ', ',     'label' => esc_html__( 'Comma', 'textcraft-tools' ),            'active' => false],
            ['val' => 'custom', 'label' => esc_html__( 'Custom…', 'textcraft-tools' ),          'active' => false],
        ];
        foreach ( $opts as $o ) {
            $cls = $o['active'] ? 'tc-btn tc-btn--primary tc-ru-rep-opt active' : 'tc-btn tc-btn--ghost tc-ru-rep-opt';
            printf(
                '<button class="%s" data-val="%s">%s</button>',
                esc_attr( $cls ),
                esc_attr( $o['val'] ),
                $o['label']
            );
        }
        echo '</div>';
        echo '<input type="text" id="tc-ru-custom-val" placeholder="' . esc_attr__( 'Custom replacement…', 'textcraft-tools' ) . '" class="tc-text-input tc-mt-10 tc-hidden">';
        echo '</div>';

        // --- Options checkboxes ---
        $this->render_options_row([
            ['id' => 'tc-ru-capitalize', 'label' => esc_html__( 'Capitalize words after underscore', 'textcraft-tools' ), 'checked' => false],
            ['id' => 'tc-ru-leading',    'label' => esc_html__( 'Remove leading/trailing underscores', 'textcraft-tools' ), 'checked' => true],
            ['id' => 'tc-ru-double',     'label' => esc_html__( 'Replace multiple consecutive underscores', 'textcraft-tools' ), 'checked' => true],
        ]);

        // --- Input textarea ---
        $this->render_textarea(
            'tc-ru-input',
            esc_html__( 'Your Text', 'textcraft-tools' ),
            esc_html__( "Paste your text containing underscores here…\n\nhello_world\nthis_is_snake_case\nuser_full_name_field\nsome__double__underscores", 'textcraft-tools' ),
            8
        );

        // --- Action buttons ---
        $this->render_button_row([
            ['id' => 'tc-ru-remove', 'label' => '〰️ ' . esc_html__( 'Remove Underscores', 'textcraft-tools' ), 'variant' => 'primary'],
            ['id' => 'tc-ru-copy',   'label' => '📋 ' . esc_html__( 'Copy', 'textcraft-tools' ),              'variant' => 'ghost'],
            ['id' => 'tc-ru-clear',  'label' => '🗑️ ' . esc_html__( 'Clear', 'textcraft-tools' ),             'variant' => 'danger'],
        ]);

        // --- Stats bar ---
        $this->render_stat_bar([
            ['id' => 'tc-ru-count',  'label' => esc_html__( 'Underscores Removed', 'textcraft-tools' )],
            ['id' => 'tc-ru-before', 'label' => esc_html__( 'Before', 'textcraft-tools' )],
            ['id' => 'tc-ru-after',  'label' => esc_html__( 'After', 'textcraft-tools' )],
        ]);

        // --- Output textarea ---
        echo '<div class="tc-label-row tc-mt-20"><span class="tc-label">' . esc_html__( 'Result', 'textcraft-tools' ) . '</span></div>';
        $this->render_textarea( 'tc-ru-output', '', '', 8, true );

        // --- Inline JS ---
        $this->render_inline_script( <<<'JS'
(function(){
    var inp = document.getElementById('tc-ru-input');
    var out = document.getElementById('tc-ru-output');
    if (!inp) return;

    var replaceVal = ' ';

    // Char count
    inp.addEventListener('input', function(){
        var cc = inp.closest('.tc-tool-card').querySelector('.tc-char-count');
        if (cc) cc.textContent = inp.value.length + ' characters';
    });

    // Replace-with option buttons
    document.querySelectorAll('.tc-ru-rep-opt').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('.tc-ru-rep-opt').forEach(function(b){
                b.classList.remove('active','tc-btn--primary');
                b.classList.add('tc-btn--ghost');
            });
            btn.classList.add('active','tc-btn--primary');
            btn.classList.remove('tc-btn--ghost');
            var custom = document.getElementById('tc-ru-custom-val');
            if (btn.dataset.val === 'custom') {
                custom.style.display = 'block';
                replaceVal = custom.value;
            } else {
                custom.style.display = 'none';
                replaceVal = btn.dataset.val;
            }
        });
    });

    // Custom input live update
    document.getElementById('tc-ru-custom-val').addEventListener('input', function(e){
        replaceVal = e.target.value;
    });

    // Remove button
    document.getElementById('tc-ru-remove').addEventListener('click', function(){
        var text      = inp.value;
        var count     = (text.match(/_/g) || []).length;
        var capitalize = document.getElementById('tc-ru-capitalize').checked;
        var leading    = document.getElementById('tc-ru-leading').checked;
        var dbl        = document.getElementById('tc-ru-double').checked;

        if (dbl)     text = text.replace(/_+/g, '_');
        if (leading) text = text.replace(/^_+|_+$/gm, '');

        if (capitalize) {
            text = text.replace(/_([a-z])/g, function(_, c){ return replaceVal + c.toUpperCase(); });
            text = text.replace(/_/g, replaceVal);
        } else {
            text = text.replace(/_/g, replaceVal);
        }

        out.value = text;
        document.getElementById('tc-ru-count').textContent  = count;
        document.getElementById('tc-ru-before').textContent = inp.value.length;
        document.getElementById('tc-ru-after').textContent  = text.length;
    });

    // Copy button
    document.getElementById('tc-ru-copy').addEventListener('click', function(){
        if (!out.value) return;
        navigator.clipboard.writeText(out.value).then(function(){
            var btn = document.getElementById('tc-ru-copy');
            btn.textContent = '✅ ' + 'Copied!';
            setTimeout(function(){ btn.textContent = '📋 Copy'; }, 2000);
        });
    });

    // Clear button
    document.getElementById('tc-ru-clear').addEventListener('click', function(){
        inp.value = ''; out.value = '';
        ['tc-ru-count','tc-ru-before','tc-ru-after'].forEach(function(id){
            document.getElementById(id).textContent = '0';
        });
        var cc = inp.closest('.tc-tool-card').querySelector('.tc-char-count');
        if (cc) cc.textContent = '0 characters';
    });
})();
JS
        );
    }
}