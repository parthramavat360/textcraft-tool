<?php
/**
 * Widget: NATO Phonetic Alphabet
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;

class Widget_Nato_Phonetic extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_nato_phonetic'; }
    public function get_title(): string { return esc_html__( 'NATO Phonetic Alphabet', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-headphones'; }
    protected function render_tool_content( array $settings ): void {

        echo '<p class="tc-text-14 tc-text-muted tc-mb-16">'
            . esc_html__( 'Translate text into the official NATO phonetic alphabet — Alpha, Bravo, Charlie. Perfect for spelling out names, call signs, codes, or any communication that needs crystal-clear clarity.', 'textcraft-tools' )
            . '</p>';

        // Input label with char count
        echo '<div class="tc-label-row tc-mb-6">';
        echo '<label class="tc-label" for="tc-nato-input">' . esc_html__( 'Your Text', 'textcraft-tools' ) . '</label>';
        echo '<span class="tc-char-count" id="tc-nato-char-count">0 ' . esc_html__( 'characters', 'textcraft-tools' ) . '</span>';
        echo '</div>';

        $this->render_textarea(
            'tc-nato-input',
            '',
            esc_html__( 'Type or paste text to translate… e.g. Hello World', 'textcraft-tools' ),
            5
        );

        // Action buttons
        $this->render_button_row( [
            [ 'id' => 'tc-nato-convert', 'label' => '🪖 ' . esc_html__( 'Translate', 'textcraft-tools' ), 'variant' => 'primary' ],
            [ 'id' => 'tc-nato-copy',    'label' => '📋 ' . esc_html__( 'Copy',      'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-nato-clear',   'label' => '🗑️ ' . esc_html__( 'Clear',     'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        // Rich HTML output display (mirrors the original div output, not a textarea)
        echo '<div class="tc-label-row tc-mt-20"><span class="tc-label">' . esc_html__( 'NATO Translation', 'textcraft-tools' ) . '</span></div>';
        echo '<div id="tc-nato-output" class="tc-nato-output">';
        echo esc_html__( 'Your NATO phonetic translation will appear here after you type text above and click Translate.', 'textcraft-tools' );
        echo '</div>';

        // NATO Phonetic Alphabet Reference grid
        echo '<div class="tc-mt-32">';
        echo '<h3 class="tc-text-16 tc-font-bold tc-text-primary tc-m-0 tc-mb-14 tc-text-center">' . esc_html__( 'NATO Phonetic Alphabet Reference', 'textcraft-tools' ) . '</h3>';
        echo '<div class="tc-grid-nato">';

        $nato_map = [
            'A' => 'Alpha',   'B' => 'Bravo',    'C' => 'Charlie', 'D' => 'Delta',
            'E' => 'Echo',    'F' => 'Foxtrot',  'G' => 'Golf',    'H' => 'Hotel',
            'I' => 'India',   'J' => 'Juliet',   'K' => 'Kilo',    'L' => 'Lima',
            'M' => 'Mike',    'N' => 'November', 'O' => 'Oscar',   'P' => 'Papa',
            'Q' => 'Quebec',  'R' => 'Romeo',    'S' => 'Sierra',  'T' => 'Tango',
            'U' => 'Uniform', 'V' => 'Victor',   'W' => 'Whiskey', 'X' => 'X-ray',
            'Y' => 'Yankee',  'Z' => 'Zulu',
        ];

        foreach ( $nato_map as $letter => $word ) {
            echo '<div class="tc-nato-cell">';
            echo '<div class="tc-nato-letter">' . esc_html( $letter ) . '</div>';
            echo '<div class="tc-text-11 tc-text-muted tc-mt-2">' . esc_html( $word ) . '</div>';
            echo '</div>';
        }

        echo '</div>'; // end grid
        echo '</div>'; // end reference section

        // Inline JS
        $this->render_inline_script( <<<'JS'
(function () {
    var NATO = {
        A:'Alpha', B:'Bravo', C:'Charlie', D:'Delta', E:'Echo', F:'Foxtrot',
        G:'Golf',  H:'Hotel', I:'India',   J:'Juliet',K:'Kilo', L:'Lima',
        M:'Mike',  N:'November',O:'Oscar', P:'Papa',  Q:'Quebec',R:'Romeo',
        S:'Sierra',T:'Tango', U:'Uniform', V:'Victor',W:'Whiskey',X:'X-ray',
        Y:'Yankee',Z:'Zulu',
        '0':'Zero','1':'One','2':'Two','3':'Three','4':'Four',
        '5':'Five','6':'Six','7':'Seven','8':'Eight','9':'Nine'
    };

    var inp      = document.getElementById('tc-nato-input');
    var out      = document.getElementById('tc-nato-output');
    var charCount = document.getElementById('tc-nato-char-count');

    if (!inp || !out) return;

    // Live char count
    inp.addEventListener('input', function () {
        if (charCount) charCount.textContent = inp.value.length + ' characters';
    });

    // Translate
    document.getElementById('tc-nato-convert').addEventListener('click', function () {
        var text = inp.value;
        if (!text.trim()) {
            out.textContent = 'Please enter some text to translate.';
            return;
        }

        var upper   = text.toUpperCase();
        var words   = [];
        var wordGroup = [];

        for (var i = 0; i < upper.length; i++) {
            var ch = upper[i];
            if (ch === ' ' || ch === '\n') {
                if (wordGroup.length) {
                    words.push(
                        '<span class="tc-pill-tag">'
                        + wordGroup.join(' · ')
                        + '</span>'
                    );
                    wordGroup = [];
                }
                if (ch === '\n') words.push('<br>');
            } else if (NATO[ch]) {
                wordGroup.push('<strong class="tc-text-primary">' + NATO[ch] + '</strong>');
            } else {
                wordGroup.push('<span class="tc-text-accent">' + ch + '</span>');
            }
        }

        if (wordGroup.length) {
            words.push(
                '<span class="tc-pill-tag">'
                + wordGroup.join(' · ')
                + '</span>'
            );
        }

        out.innerHTML = words.join(' ');
    });

    // Copy (uses innerText to get plain text from the rich HTML output)
    document.getElementById('tc-nato-copy').addEventListener('click', function () {
        var text = out.innerText;
        if (!text || text === 'Your NATO phonetic translation will appear here after you type text above and click Translate.') return;
        var btn = document.getElementById('tc-nato-copy');
        navigator.clipboard.writeText(text).then(function () {
            btn.textContent = '✅ Copied!';
            setTimeout(function () { btn.textContent = '📋 Copy'; }, 2000);
        });
    });

    // Clear
    document.getElementById('tc-nato-clear').addEventListener('click', function () {
        inp.value       = '';
        out.textContent = 'Your NATO phonetic translation will appear here after you type text above and click Translate.';
        if (charCount) charCount.textContent = '0 characters';
    });
})();
JS
        );
    }
}