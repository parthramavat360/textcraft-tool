<?php
/**
 * Widget: Repeat Text Generator
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;
class Widget_Repeat_Text extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_repeat_text'; }
    public function get_title(): string { return esc_html__( 'Text Repeater', 'textcraft-tools' ); }

    public function get_keywords(): array {
        return [ 'text repeater', 'repeat text online', 'text generator', 'repeat string', 'free online text tool' ];
    }
    public function get_icon(): string  { return 'eicon-refresh'; }
    protected function render_tool_content( array $settings ): void {
        echo '<p class="tc-text-14 tc-text-muted tc-mb-20">' . esc_html__( 'Repeat any text multiple times with customizable separators. Useful for generating test data, creating dividers, or filling placeholder content — all processed instantly in your browser.', 'textcraft-tools' ) . '</p>';

        // ── Top controls grid ─────────────────────────────────────────────
        echo '<div class="tc-grid-2col tc-mb-20">';

        // Repeat Count
        echo '<div>';
        echo '<label class="tc-section-label tc-mb-6">' . esc_html__( 'Repeat Count', 'textcraft-tools' ) . '</label>';
        echo '<input type="number" id="tc-rt-times" value="5" min="1" max="10000" class="tc-rt-input">';
        echo '</div>';

        // Separator
        echo '<div>';
        echo '<label class="tc-section-label tc-mb-6">' . esc_html__( 'Separator Between Repetitions', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-6 tc-mb-6 tc-flex-wrap">';
        $sep_opts = [
            [ 'val' => ' ',      'label' => esc_html__( 'Space',    'textcraft-tools' ), 'active' => true  ],
            [ 'val' => '\n',     'label' => esc_html__( 'New Line', 'textcraft-tools' ), 'active' => false ],
            [ 'val' => ', ',     'label' => esc_html__( 'Comma',    'textcraft-tools' ), 'active' => false ],
            [ 'val' => '',       'label' => esc_html__( 'None',     'textcraft-tools' ), 'active' => false ],
            [ 'val' => 'custom', 'label' => esc_html__( 'Custom',   'textcraft-tools' ), 'active' => false ],
        ];
        foreach ( $sep_opts as $opt ) {
            $cls = $opt['active'] ? 'tc-btn tc-btn--primary tc-rt-sep-opt active' : 'tc-btn tc-btn--ghost tc-rt-sep-opt';
            echo '<button class="' . esc_attr( $cls ) . ' tc-btn-xs" data-val="' . esc_attr( $opt['val'] ) . '">' . $opt['label'] . '</button>';
        }
        echo '</div>';
        echo '<input type="text" id="tc-rt-sep-custom" class="tc-rt-sep-input tc-hidden" placeholder="' . esc_attr__( 'Custom separator…', 'textcraft-tools' ) . '">';
        echo '</div>';

        echo '</div>'; // end grid

        // ── Input textarea ────────────────────────────────────────────────
        $this->render_textarea(
            'tc-rt-input',
            esc_html__( 'Text to Repeat', 'textcraft-tools' ),
            esc_html__( "Type or paste the text you want to repeat…\n\nHello World!", 'textcraft-tools' ),
            5
        );

        // ── Buttons ───────────────────────────────────────────────────────
        $this->render_button_row( [
            [ 'id' => 'tc-rt-generate', 'label' => '🔂 ' . esc_html__( 'Generate',     'textcraft-tools' ), 'variant' => 'primary' ],
            [ 'id' => 'tc-rt-copy',     'label' => '📋 ' . esc_html__( 'Copy Result',  'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-rt-clear',    'label' => '🗑️ ' . esc_html__( 'Clear',        'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        // ── Stats bar ─────────────────────────────────────────────────────
        $this->render_stat_bar( [
            [ 'id' => 'tc-rt-reps',  'label' => esc_html__( 'Repetitions',       'textcraft-tools' ) ],
            [ 'id' => 'tc-rt-words', 'label' => esc_html__( 'Total Words',        'textcraft-tools' ) ],
            [ 'id' => 'tc-rt-chars', 'label' => esc_html__( 'Total Characters',   'textcraft-tools' ) ],
        ] );

        // ── Output textarea ───────────────────────────────────────────────
        echo '<div class="tc-label-row tc-mt-20"><span class="tc-label">' . esc_html__( 'Generated Text', 'textcraft-tools' ) . '</span></div>';
        $this->render_textarea( 'tc-rt-output', '', '', 10, true );

        // ── Inline JS ─────────────────────────────────────────────────────
        $this->render_inline_script( <<<'JS'
(function(){
    var inp = document.getElementById('tc-rt-input');
    var out = document.getElementById('tc-rt-output');
    if (!inp) return;

    var sepVal = ' ';

    // Char count
    inp.addEventListener('input', function(){
        var cc = inp.closest('.tc-tool-card').querySelector('.tc-char-count');
        if (cc) cc.textContent = inp.value.length + ' characters';
    });

    // Separator option buttons
    document.querySelectorAll('.tc-rt-sep-opt').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('.tc-rt-sep-opt').forEach(function(b){
                b.classList.remove('active', 'tc-btn--primary');
                b.classList.add('tc-btn--ghost');
            });
            btn.classList.add('active', 'tc-btn--primary');
            btn.classList.remove('tc-btn--ghost');

            var custom = document.getElementById('tc-rt-sep-custom');
            if (btn.dataset.val === 'custom') {
                custom.style.display = 'block';
                sepVal = custom.value;
            } else {
                custom.style.display = 'none';
                sepVal = btn.dataset.val === '\\n' ? '\n' : btn.dataset.val;
            }
        });
    });

    // Custom separator live update
    document.getElementById('tc-rt-sep-custom').addEventListener('input', function(e){
        sepVal = e.target.value;
    });

    // Generate
    document.getElementById('tc-rt-generate').addEventListener('click', function(){
        var text  = inp.value;
        if (!text.trim()) { out.value = 'Please enter some text to repeat.'; return; }
        var count  = Math.min(parseInt(document.getElementById('tc-rt-times').value) || 1, 10000);
        var result = Array(count).fill(text).join(sepVal);
        out.value  = result;
        document.getElementById('tc-rt-reps').textContent  = count;
        document.getElementById('tc-rt-words').textContent = result.trim().split(/\s+/).filter(Boolean).length;
        document.getElementById('tc-rt-chars').textContent = result.length;
    });

    // Copy
    document.getElementById('tc-rt-copy').addEventListener('click', function(){
        if (!out.value) return;
        navigator.clipboard.writeText(out.value).then(function(){
            var btn = document.getElementById('tc-rt-copy');
            btn.textContent = '✅ Copied!';
            setTimeout(function(){ btn.textContent = '📋 Copy Result'; }, 2000);
        });
    });

    // Clear
    document.getElementById('tc-rt-clear').addEventListener('click', function(){
        inp.value = ''; out.value = '';
        ['tc-rt-reps','tc-rt-words','tc-rt-chars'].forEach(function(id){
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