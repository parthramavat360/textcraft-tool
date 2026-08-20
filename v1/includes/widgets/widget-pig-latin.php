<?php
/**
 * Widget: Pig Latin Translator
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;
class Widget_Pig_Latin extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_pig_latin'; }
    public function get_title(): string { return esc_html__( 'Pig Latin Translator', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-integration'; }
    protected function render_tool_content( array $settings ): void {

        echo '<p class="tc-text-14 tc-text-muted tc-mb-16">'
            . esc_html__( 'Translate English text into Pig Latin for fun, games, or learning. This free online tool converts words using classic Pig Latin rules — move consonants to the end and add suffixes.', 'textcraft-tools' )
            . '</p>';

        // ── Rules info box ────────────────────────────────────────────────
        echo '<div class="tc-card-surface tc-p-14-16 tc-text-13 tc-text-muted tc-mb-20">';
        echo '<strong class="tc-text-primary">📖 ' . esc_html__( 'Pig Latin Rules:', 'textcraft-tools' ) . '</strong> ';
        echo esc_html__( 'Words starting with consonants → move consonant(s) to end + "ay" (e.g.', 'textcraft-tools' ) . ' <em>pig → igpay</em>). ';
        echo esc_html__( 'Words starting with vowels → add "way" or "yay" to end (e.g.', 'textcraft-tools' ) . ' <em>apple → appleway</em>).';
        echo '</div>';

        // ── Vowel suffix radio buttons ────────────────────────────────────
        echo '<div class="tc-d-flex tc-gap-8 tc-flex-wrap tc-mb-16">';
echo '<label class="tc-flex-check tc-text-13 tc-text-muted">';
echo '<input type="radio" name="tc-pl-vowel" value="way" checked class="tc-checkbox"> ' . esc_html__( 'Add "way" after vowels', 'textcraft-tools' );
echo '</label>';
echo '<label class="tc-flex-check tc-text-13 tc-text-muted">';
        echo '<input type="radio" name="tc-pl-vowel" value="yay" class="tc-checkbox"> ' . esc_html__( 'Add "yay" after vowels', 'textcraft-tools' );
        echo '</label>';
        echo '</div>';

        // ── Input textarea ────────────────────────────────────────────────
        $this->render_textarea(
            'tc-pl-input',
            esc_html__( 'English Text', 'textcraft-tools' ),
            esc_html__( 'Type or paste English text here… e.g. Hello my friend, how are you today?', 'textcraft-tools' ),
            5
        );

        // ── Action buttons ────────────────────────────────────────────────
        $this->render_button_row( [
            [ 'id' => 'tc-pl-translate', 'label' => '🐷 ' . esc_html__( 'Translate to Pig Latin', 'textcraft-tools' ), 'variant' => 'primary' ],
            [ 'id' => 'tc-pl-copy',      'label' => '📋 ' . esc_html__( 'Copy',                   'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-pl-clear',     'label' => '🗑️ ' . esc_html__( 'Clear',                  'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        // ── Output textarea ───────────────────────────────────────────────
        echo '<div class="tc-label-row tc-mt-20"><span class="tc-label">' . esc_html__( 'Pig Latin Translation', 'textcraft-tools' ) . '</span></div>';
        $this->render_textarea( 'tc-pl-output', '', esc_html__( 'Ig-Pay Atin-Lay ill-way appear-way ere-hay…', 'textcraft-tools' ), 5, true );

        // ── Examples section ──────────────────────────────────────────────
        $examples = [
            [ 'Hello',     'Ellohay'    ],
            [ 'World',     'Orldway'    ],
            [ 'Apple',     'Appleway'   ],
            [ 'Computer',  'Omputercay' ],
            [ 'Friend',    'Iendfray'   ],
            [ 'Beautiful', 'Eautifulbay'],
        ];
        echo '<div class="tc-card-surface tc-mt-32 tc-p-24">';
        echo '<h3 class="tc-text-20 tc-mb-16">' . esc_html__( 'Examples', 'textcraft-tools' ) . '</h3>';
        echo '<div class="tc-pattern-grid">';
        foreach ( $examples as $ex ) {
            echo '<div class="tc-pattern-card">';
            echo '<span class="tc-text-primary tc-font-semibold">' . esc_html( $ex[0] ) . '</span>';
            echo '<span class="tc-text-13 tc-accent-value">→ ' . esc_html( $ex[1] ) . '</span>';
            echo '</div>';
        }
        echo '</div></div>';

        // ── Inline JS ─────────────────────────────────────────────────────
        $this->render_inline_script( <<<'JS'
(function(){
    var inp = document.getElementById('tc-pl-input');
    var out = document.getElementById('tc-pl-output');
    if (!inp) return;

    var VOWELS = 'aeiouAEIOU';

    // Char count
    inp.addEventListener('input', function(){
        var cc = inp.closest('.tc-tool-card').querySelector('.tc-char-count');
        if (cc) cc.textContent = inp.value.length + ' characters';
    });

    function toPigLatin(word, vowelSuffix) {
        if (!word.match(/[a-zA-Z]/)) return word;
        var isCapitalized = word[0] === word[0].toUpperCase() && word[0] !== word[0].toLowerCase();
        var isAllCaps     = word === word.toUpperCase() && /[A-Z]/.test(word);
        var lower         = word.toLowerCase();

        var result;

        // Vowel start
        if (VOWELS.indexOf(lower[0]) !== -1) {
            result = lower + vowelSuffix;
        } else {
            // Consonant cluster — special case: 'qu' moves together
            var cluster = '';
            var i = 0;
            if (lower.slice(0, 2) === 'qu') {
                cluster = 'qu'; i = 2;
            } else {
                while (i < lower.length && VOWELS.indexOf(lower[i]) === -1) {
                    cluster += lower[i]; i++;
                }
            }
            result = lower.slice(i) + cluster + 'ay';
            if (!result.replace(/ay$/, '')) result = lower + 'ay'; // all consonants
        }

        if (isAllCaps)     return result.toUpperCase();
        if (isCapitalized) return result[0].toUpperCase() + result.slice(1);
        return result;
    }

    // Translate button
    document.getElementById('tc-pl-translate').addEventListener('click', function(){
        var text = inp.value;
        var vowelSuffix = document.querySelector('input[name="tc-pl-vowel"]:checked').value;
        if (!text.trim()) { out.value = 'Please enter some text to translate.'; return; }
        out.value = text.replace(/([a-zA-Z]+)/g, function(match){ return toPigLatin(match, vowelSuffix); });
    });

    // Copy
    document.getElementById('tc-pl-copy').addEventListener('click', function(){
        if (!out.value) return;
        navigator.clipboard.writeText(out.value).then(function(){
            var btn = document.getElementById('tc-pl-copy');
            btn.textContent = '✅ Copied!';
            setTimeout(function(){ btn.textContent = '📋 Copy'; }, 2000);
        });
    });

    // Clear
    document.getElementById('tc-pl-clear').addEventListener('click', function(){
        inp.value = ''; out.value = '';
        var cc = inp.closest('.tc-tool-card').querySelector('.tc-char-count');
        if (cc) cc.textContent = '0 characters';
    });
})();
JS
        );
    }
}