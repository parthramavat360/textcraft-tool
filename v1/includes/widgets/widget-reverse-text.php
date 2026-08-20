<?php
/**
 * Widget: Reverse Text Generator
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;
class Widget_Reverse_Text extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_reverse_text'; }
    public function get_title(): string { return esc_html__( 'Reverse Text Generator', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-flip-horizontal'; }
    protected function render_tool_content( array $settings ): void {

        echo '<p class="tc-text-14 tc-text-muted tc-mb-16">'
            . esc_html__( 'Reverse text, words, lines, or flip text upside down with this fun free online tool. Perfect for creating mirror text, puzzles, and social media posts. All processing is done in your browser.', 'textcraft-tools' )
            . '</p>';

        // ── Mode buttons ──────────────────────────────────────────────────
        echo '<div class="tc-d-flex tc-gap-8 tc-flex-wrap tc-mb-20">';
        $modes = [
            [ 'mode' => 'chars', 'label' => '↔️ ' . esc_html__( 'Reverse Characters', 'textcraft-tools' ), 'active' => true  ],
            [ 'mode' => 'words', 'label' => '🔀 ' . esc_html__( 'Reverse Words',       'textcraft-tools' ), 'active' => false ],
            [ 'mode' => 'lines', 'label' => '↕️ ' . esc_html__( 'Reverse Lines',       'textcraft-tools' ), 'active' => false ],
            [ 'mode' => 'flip',  'label' => '🙃 ' . esc_html__( 'Flip Upside Down',    'textcraft-tools' ), 'active' => false ],
        ];
        foreach ( $modes as $m ) {
            $cls = $m['active'] ? 'tc-btn tc-btn--primary tc-rvt-mode active' : 'tc-btn tc-btn--ghost tc-rvt-mode';
            echo '<button class="' . esc_attr( $cls ) . '" data-mode="' . esc_attr( $m['mode'] ) . '">' . $m['label'] . '</button>';
        }
        echo '</div>';

        // ── Input textarea ────────────────────────────────────────────────
        $this->render_textarea(
            'tc-rvt-input',
            esc_html__( 'Your Text', 'textcraft-tools' ),
            esc_html__( "Type or paste text to reverse characters, words, or lines…\n\nHello World!", 'textcraft-tools' ),
            7
        );

        // ── Buttons ───────────────────────────────────────────────────────
        $this->render_button_row( [
            [ 'id' => 'tc-rvt-reverse', 'label' => '↔️ ' . esc_html__( 'Reverse', 'textcraft-tools' ), 'variant' => 'primary' ],
            [ 'id' => 'tc-rvt-copy',    'label' => '📋 ' . esc_html__( 'Copy',    'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-rvt-clear',   'label' => '🗑️ ' . esc_html__( 'Clear',   'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        // ── Output textarea ───────────────────────────────────────────────
        echo '<div class="tc-label-row tc-mt-20"><span class="tc-label">' . esc_html__( 'Reversed Text', 'textcraft-tools' ) . '</span></div>';
        $this->render_textarea( 'tc-rvt-output', '', '', 7, true );

        // ── Inline JS ─────────────────────────────────────────────────────
        $this->render_inline_script( <<<'JS'
(function(){
    var inp  = document.getElementById('tc-rvt-input');
    var out  = document.getElementById('tc-rvt-output');
    if (!inp) return;

    var mode = 'chars';

    // Char count
    inp.addEventListener('input', function(){
        var cc = inp.closest('.tc-tool-card').querySelector('.tc-char-count');
        if (cc) cc.textContent = inp.value.length + ' characters';
    });

    // Mode buttons
    document.querySelectorAll('.tc-rvt-mode').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('.tc-rvt-mode').forEach(function(b){
                b.classList.remove('active', 'tc-btn--primary');
                b.classList.add('tc-btn--ghost');
            });
            btn.classList.add('active', 'tc-btn--primary');
            btn.classList.remove('tc-btn--ghost');
            mode = btn.dataset.mode;
        });
    });

    // Flip map (upside-down Unicode chars)
    var FLIP_MAP = {
        'a':'ɐ','b':'q','c':'ɔ','d':'p','e':'ǝ','f':'ɟ','g':'ƃ','h':'ɥ','i':'ı','j':'ɾ',
        'k':'ʞ','l':'l','m':'ɯ','n':'u','o':'o','p':'d','q':'b','r':'ɹ','s':'s','t':'ʇ',
        'u':'n','v':'ʌ','w':'ʍ','x':'x','y':'ʎ','z':'z',
        'A':'∀','B':'ᗺ','C':'Ↄ','D':'ᗡ','E':'Ǝ','F':'Ⅎ','G':'פ','H':'H','I':'I','J':'ɾ',
        'K':'ʞ','L':'˥','M':'W','N':'N','O':'O','P':'Ԁ','Q':'Q','R':'ᴚ','S':'S','T':'┴',
        'U':'∩','V':'Λ','W':'M','X':'X','Y':'⅄','Z':'Z',
        '0':'0','1':'Ɩ','2':'ᄅ','3':'Ɛ','4':'ㄣ','5':'ϛ','6':'9','7':'ㄥ','8':'8','9':'6',
        '.':'˙',',':'\\','?':'¿','!':'¡','(':')',')':'(',' ':' '};

    // Reverse button
    document.getElementById('tc-rvt-reverse').addEventListener('click', function(){
        var text = inp.value;
        if (!text) { out.value = 'Enter some text above to start reversing.'; return; }
        var result = '';
        switch (mode) {
            case 'chars':
                result = Array.from(text).reverse().join('');
                break;
            case 'words':
                result = text.split('\n').map(function(line){
                    return line.split(' ').reverse().join(' ');
                }).join('\n');
                break;
            case 'lines':
                result = text.split('\n').reverse().join('\n');
                break;
            case 'flip':
                result = Array.from(text).reverse().map(function(c){
                    return FLIP_MAP[c] || c;
                }).join('');
                break;
        }
        out.value = result;
    });

    // Copy
    document.getElementById('tc-rvt-copy').addEventListener('click', function(){
        if (!out.value) return;
        navigator.clipboard.writeText(out.value).then(function(){
            var btn = document.getElementById('tc-rvt-copy');
            btn.textContent = '✅ Copied!';
            setTimeout(function(){ btn.textContent = '📋 Copy'; }, 2000);
        });
    });

    // Clear
    document.getElementById('tc-rvt-clear').addEventListener('click', function(){
        inp.value = ''; out.value = '';
        var cc = inp.closest('.tc-tool-card').querySelector('.tc-char-count');
        if (cc) cc.textContent = '0 characters';
    });
})();
JS
        );
    }
}