<?php
/**
 * Widget: Phonetic Spelling Tool
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;

class Widget_Phonetic_Spelling extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_phonetic_spelling'; }
    public function get_title(): string { return esc_html__( 'Phonetic Spelling', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-volume-on'; }
    protected function render_tool_content( array $settings ): void {

        echo '<p class="tc-text-14 tc-text-muted tc-mb-16">'
            . esc_html__( 'Convert English words into phonetic spellings using simplified pronunciation guides or the official NATO alphabet. A helpful free tool for language learners, teachers, and anyone improving their spelling skills.', 'textcraft-tools' )
            . '</p>';

        // Mode buttons
        echo '<div class="tc-d-flex tc-gap-8 tc-mb-20 tc-flex-wrap" id="tc-ph-mode-group">';
        $modes = [
            'simplified' => __( 'Simplified',   'textcraft-tools' ),
            'nato'       => __( 'NATO Alphabet', 'textcraft-tools' ),
            'sounds'     => __( 'Sound Guide',   'textcraft-tools' ),
        ];
        $first = true;
        foreach ( $modes as $val => $label ) {
            $active  = $first ? ' tc-btn-active' : '';
            $variant = $first ? 'primary' : 'secondary';
            echo '<button class="tc-btn tc-btn--' . esc_attr( $variant ) . ' tc-ph-mode-btn' . $active . '" data-mode="' . esc_attr( $val ) . '">' . esc_html( $label ) . '</button>';
            $first = false;
        }
        echo '</div>';

        // Input label with char count
        echo '<div class="tc-d-flex tc-justify-between tc-items-center tc-mb-6">';
        echo '<label class="tc-label" for="tc-ph-input">' . esc_html__( 'Your Text', 'textcraft-tools' ) . '</label>';
        echo '<span id="tc-ph-char-count" class="tc-text-12 tc-text-muted">0 ' . esc_html__( 'characters', 'textcraft-tools' ) . '</span>';
        echo '</div>';

        $this->render_textarea(
            'tc-ph-input',
            '',
            esc_html__( 'Type or paste words to get their phonetic spelling… e.g. Hello, beautiful, necessary', 'textcraft-tools' ),
            6
        );

        // Action buttons
        $this->render_button_row( [
            [ 'id' => 'tc-ph-convert', 'label' => '🔊 ' . esc_html__( 'Convert to Phonetic', 'textcraft-tools' ), 'variant' => 'primary' ],
            [ 'id' => 'tc-ph-copy',    'label' => '📋 ' . esc_html__( 'Copy',                 'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-ph-clear',   'label' => '🗑️ ' . esc_html__( 'Clear',               'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        // Rich HTML output div (not a textarea — required for coloured inline spans)
        echo '<div class="tc-label-row tc-mt-20"><span class="tc-label">' . esc_html__( 'Phonetic Result', 'textcraft-tools' ) . '</span></div>';
        echo '<div id="tc-ph-output" class="tc-ph-output">';
        echo esc_html__( 'Your phonetic spelling result will appear here after you enter text and click Convert to Phonetic.', 'textcraft-tools' );
        echo '</div>';

        // Common Phonetic Patterns section
        echo '<div class="tc-card-surface tc-mt-32 tc-p-20">';
        echo '<h3 class="tc-text-16 tc-font-bold tc-text-primary tc-m-0 tc-mb-14">' . esc_html__( 'Common Phonetic Patterns', 'textcraft-tools' ) . '</h3>';
        echo '<div class="tc-example-grid">';

        $patterns = [
            [ 'ph → f',       'phone = fone'      ],
            [ 'ck → k',       'check = chek'      ],
            [ 'gh → f',       'laugh = laf'       ],
            [ 'tion → shun',  'nation = nayshun'  ],
            [ 'ea → ee',      'teach = teech'     ],
            [ 'ou → ow',      'found = fownd'     ],
            [ 'igh → eye',    'night = nite'      ],
            [ 'wr → r',       'write = rite'      ],
            [ 'kn → n',       'know = no'         ],
            [ 'mb → m',       'lamb = lam'        ],
            [ 'qu → kw',      'queen = kween'     ],
            [ 'ai → ay',      'rain = rayn'       ],
        ];

        foreach ( $patterns as $p ) {
            echo '<div class="tc-example-card">';
            echo '<div class="tc-text-13 tc-accent-value tc-mb-4">' . esc_html( $p[0] ) . '</div>';
            echo '<div class="tc-text-12 tc-text-muted tc-font-mono">' . esc_html( $p[1] ) . '</div>';
            echo '</div>';
        }

        echo '</div>'; // end patterns grid
        echo '</div>'; // end patterns panel

        // Inline JS
        $this->render_inline_script( <<<'JS'
(function () {
    var inp       = document.getElementById('tc-ph-input');
    var out       = document.getElementById('tc-ph-output');
    var charCount = document.getElementById('tc-ph-char-count');
    var mode      = 'simplified';

    if (!inp || !out) return;

    // Live char count
    inp.addEventListener('input', function () {
        if (charCount) charCount.textContent = inp.value.length + ' characters';
    });

    // Mode buttons
    document.querySelectorAll('.tc-ph-mode-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.tc-ph-mode-btn').forEach(function (b) {
                b.classList.remove('tc-btn-active', 'tc-btn--primary');
                b.classList.add('tc-btn--secondary');
            });
            btn.classList.add('tc-btn-active', 'tc-btn--primary');
            btn.classList.remove('tc-btn--secondary');
            mode = btn.getAttribute('data-mode');
        });
    });

    // Data maps
    var NATO = {
        a:'Alpha', b:'Bravo',   c:'Charlie', d:'Delta',   e:'Echo',    f:'Foxtrot',
        g:'Golf',  h:'Hotel',   i:'India',   j:'Juliet',  k:'Kilo',    l:'Lima',
        m:'Mike',  n:'November',o:'Oscar',   p:'Papa',    q:'Quebec',  r:'Romeo',
        s:'Sierra',t:'Tango',   u:'Uniform', v:'Victor',  w:'Whiskey', x:'X-ray',
        y:'Yankee',z:'Zulu'
    };

    function simplifyWord(word) {
        var w = word.toLowerCase();
        w = w.replace(/ph/g,   'f');
        w = w.replace(/tion/g, 'shun');
        w = w.replace(/sion/g, 'shun');
        w = w.replace(/igh/g,  'eye');
        w = w.replace(/ck/g,   'k');
        w = w.replace(/wr/g,   'r');
        w = w.replace(/kn/g,   'n');
        w = w.replace(/mb$/g,  'm');
        w = w.replace(/qu/g,   'kw');
        w = w.replace(/gh/gi,  '');
        w = w.replace(/ea/g,   'ee');
        w = w.replace(/ai/g,   'ay');
        w = w.replace(/ay/g,   'ay');
        w = w.replace(/oa/g,   'oh');
        w = w.replace(/oo/g,   'oo');
        w = w.replace(/ou/g,   'ow');
        return w;
    }

    // Convert
    document.getElementById('tc-ph-convert').addEventListener('click', function () {
        var text = inp.value.trim();
        if (!text) { out.textContent = 'Please enter some text.'; return; }

        var result = '';

        if (mode === 'simplified') {
            var parts = text.split(/\b/);
            result = parts.map(function (w) {
                return /[a-zA-Z]/.test(w)
                    ? '<span class="tc-text-primary">' + simplifyWord(w) + '</span>'
                    : w;
            }).join('');

        } else if (mode === 'nato') {
            result = text.toUpperCase().split('').map(function (ch) {
                var lc = ch.toLowerCase();
                return NATO[lc]
                    ? '<span class="tc-ps-code">' + NATO[lc] + '</span>'
                    : ch;
            }).join('');

        } else {
            // Sound Guide
            result = text.split(/\b/).map(function (w) {
                if (!/[a-zA-Z]/.test(w)) return w;
                return w
                    .replace(/th/gi, '<u class="tc-text-accent">th(th)</u>')
                    .replace(/ch/gi, '<u class="tc-text-accent">ch(ch)</u>')
                    .replace(/sh/gi, '<u class="tc-text-accent">sh(sh)</u>');
            }).join('');
        }

        out.innerHTML = result;
    });

    // Copy (innerText strips HTML tags)
    document.getElementById('tc-ph-copy').addEventListener('click', function () {
        var text = out.innerText;
        if (!text || text === 'Your phonetic spelling result will appear here after you enter text and click Convert to Phonetic.') return;
        var btn = document.getElementById('tc-ph-copy');
        navigator.clipboard.writeText(text).then(function () {
            btn.textContent = '✅ Copied!';
            setTimeout(function () { btn.textContent = '📋 Copy'; }, 2000);
        });
    });

    // Clear
    document.getElementById('tc-ph-clear').addEventListener('click', function () {
        inp.value       = '';
        out.textContent = 'Your phonetic spelling result will appear here after you enter text and click Convert to Phonetic.';
        if (charCount) charCount.textContent = '0 characters';
    });
})();
JS
        );
    }
}