<?php
/**
 * Widget: Whitespace Remover
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;
class Widget_Whitespace_Remover extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_whitespace_remover'; }
    public function get_title(): string { return esc_html__( 'Whitespace Remover', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-code'; }
    protected function render_tool_content( array $settings ): void {

        echo '<p class="tc-text-14 tc-text-muted tc-mb-20">'
            . esc_html__( 'Remove extra spaces, tabs, and blank lines from your text in one click. Clean up messy copy, normalize whitespace, and prepare clean text for publishing — all processed locally in your browser.', 'textcraft-tools' )
            . '</p>';

        // --- Checkbox options grid (matching PHP tool exactly) ---
        $options = [
            [
                'id'      => 'tc-ws-double',
                'checked' => true,
                'title'   => esc_html__( 'Remove double spaces', 'textcraft-tools' ),
                'desc'    => esc_html__( 'Collapse multiple spaces into one', 'textcraft-tools' ),
            ],
            [
                'id'      => 'tc-ws-tabs',
                'checked' => true,
                'title'   => esc_html__( 'Convert tabs to spaces', 'textcraft-tools' ),
                'desc'    => esc_html__( 'Replace \t with a single space', 'textcraft-tools' ),
            ],
            [
                'id'      => 'tc-ws-leading',
                'checked' => true,
                'title'   => esc_html__( 'Trim leading spaces', 'textcraft-tools' ),
                'desc'    => esc_html__( 'Remove spaces at line start', 'textcraft-tools' ),
            ],
            [
                'id'      => 'tc-ws-trailing',
                'checked' => true,
                'title'   => esc_html__( 'Trim trailing spaces', 'textcraft-tools' ),
                'desc'    => esc_html__( 'Remove spaces at line end', 'textcraft-tools' ),
            ],
            [
                'id'      => 'tc-ws-nbsp',
                'checked' => true,
                'title'   => esc_html__( 'Remove non-breaking spaces', 'textcraft-tools' ),
                'desc'    => esc_html__( 'Replace &nbsp; (\u00A0) with space', 'textcraft-tools' ),
            ],
            [
                'id'      => 'tc-ws-all',
                'checked' => false,
                'title'   => esc_html__( 'Remove ALL spaces', 'textcraft-tools' ),
                'desc'    => esc_html__( 'Strip every whitespace character', 'textcraft-tools' ),
            ],
        ];

        echo '<div class="tc-opts-grid">';
        foreach ( $options as $opt ) {
            $checked = $opt['checked'] ? 'checked' : '';
            echo '<label class="tc-opt-label">';
            printf(
                '<input type="checkbox" id="%s" %s class="tc-accent-checkbox">',
                esc_attr( $opt['id'] ),
                $checked
            );
            echo '<div>';
            echo '<div class="tc-text-13 tc-font-semibold tc-text-primary">' . $opt['title'] . '</div>';
            echo '<div class="tc-text-11 tc-text-muted tc-mt-2">' . $opt['desc'] . '</div>';
            echo '</div>';
            echo '</label>';
        }
        echo '</div>';

        // --- Input textarea ---
        $this->render_textarea(
            'tc-ws-input',
            esc_html__( 'Your Text', 'textcraft-tools' ),
            esc_html__( "Paste   your   text   here   with   extra   spaces…\n   Leading spaces here\nTrailing spaces here   \n\tTab characters\there", 'textcraft-tools' ),
            9
        );

        // --- Action buttons ---
        $this->render_button_row([
            ['id' => 'tc-ws-remove', 'label' => '⬜ ' . esc_html__( 'Remove Whitespace', 'textcraft-tools' ), 'variant' => 'primary'],
            ['id' => 'tc-ws-copy',   'label' => '📋 ' . esc_html__( 'Copy', 'textcraft-tools' ),             'variant' => 'ghost'],
            ['id' => 'tc-ws-clear',  'label' => '🗑️ ' . esc_html__( 'Clear', 'textcraft-tools' ),            'variant' => 'danger'],
        ]);

        // --- Stats bar ---
        $this->render_stat_bar([
            ['id' => 'tc-ws-removed', 'label' => esc_html__( 'Spaces Removed', 'textcraft-tools' )],
            ['id' => 'tc-ws-before',  'label' => esc_html__( 'Before', 'textcraft-tools' )],
            ['id' => 'tc-ws-after',   'label' => esc_html__( 'After', 'textcraft-tools' )],
        ]);

        // --- Output textarea ---
        echo '<div class="tc-label-row tc-mt-20"><span class="tc-label">' . esc_html__( 'Result', 'textcraft-tools' ) . '</span></div>';
        $this->render_textarea( 'tc-ws-output', '', '', 9, true );

        // --- Inline JS ---
        $this->render_inline_script( <<<'JS'
(function(){
    var inp = document.getElementById('tc-ws-input');
    var out = document.getElementById('tc-ws-output');
    if (!inp) return;

    // Char count
    inp.addEventListener('input', function(){
        var cc = inp.closest('.tc-tool-card').querySelector('.tc-char-count');
        if (cc) cc.textContent = inp.value.length + ' characters';
    });

    // Remove button
    document.getElementById('tc-ws-remove').addEventListener('click', function(){
        var text   = inp.value;
        var before = text.length;

        if (document.getElementById('tc-ws-all').checked) {
            text = text.replace(/\s/g, '');
        } else {
            if (document.getElementById('tc-ws-nbsp').checked)     text = text.replace(/\u00A0/g, ' ');
            if (document.getElementById('tc-ws-tabs').checked)     text = text.replace(/\t/g, ' ');
            if (document.getElementById('tc-ws-double').checked)   text = text.replace(/ {2,}/g, ' ');
            if (document.getElementById('tc-ws-leading').checked)  text = text.replace(/^ +/gm, '');
            if (document.getElementById('tc-ws-trailing').checked) text = text.replace(/ +$/gm, '');
        }

        out.value = text;
        document.getElementById('tc-ws-removed').textContent = before - text.length;
        document.getElementById('tc-ws-before').textContent  = before;
        document.getElementById('tc-ws-after').textContent   = text.length;
    });

    // Copy button
    document.getElementById('tc-ws-copy').addEventListener('click', function(){
        if (!out.value) return;
        navigator.clipboard.writeText(out.value).then(function(){
            var btn = document.getElementById('tc-ws-copy');
            btn.textContent = '✅ Copied!';
            setTimeout(function(){ btn.textContent = '📋 Copy'; }, 2000);
        });
    });

    // Clear button
    document.getElementById('tc-ws-clear').addEventListener('click', function(){
        inp.value = ''; out.value = '';
        ['tc-ws-removed','tc-ws-before','tc-ws-after'].forEach(function(id){
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