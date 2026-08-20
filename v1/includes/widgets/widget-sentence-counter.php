<?php
/**
 * Widget: Online Sentence Counter
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;

class Widget_Sentence_Counter extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_sentence_counter'; }
    public function get_title(): string { return esc_html__( 'Sentence Counter', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-counter'; }
    protected function render_tool_content( array $settings ): void {

        echo '<p class="tc-text-14 tc-text-muted tc-mb-16">'
            . esc_html__( 'Count sentences, words, characters, paragraphs, and more with this free online text analyser. Get live statistics, reading time, speaking time, and advanced metrics — all processed privately in your browser.', 'textcraft-tools' )
            . '</p>';

        // Primary stat boxes grid
        $stat_boxes = [
            'tc-sct-sentences' => __( 'Sentences',        'textcraft-tools' ),
            'tc-sct-words'     => __( 'Words',            'textcraft-tools' ),
            'tc-sct-chars'     => __( 'Characters',       'textcraft-tools' ),
            'tc-sct-chars-ns'  => __( 'Chars (no spaces)','textcraft-tools' ),
            'tc-sct-paragraphs'=> __( 'Paragraphs',       'textcraft-tools' ),
            'tc-sct-read'      => __( 'Reading Time',     'textcraft-tools' ),
        ];

        echo '<div class="tc-stat-box-grid">';
        foreach ( $stat_boxes as $id => $label ) {
            echo '<div class="tc-stat-box">';
            echo '<div id="' . esc_attr( $id ) . '" class="tc-stat-number">0</div>';
            echo '<div class="tc-stat-label">' . esc_html( $label ) . '</div>';
            echo '</div>';
        }
        echo '</div>';

        // Input label with live info
        echo '<div class="tc-d-flex tc-justify-between tc-items-center tc-mb-6">';
        echo '<label class="tc-label" for="tc-sct-input">' . esc_html__( 'Your Text', 'textcraft-tools' ) . '</label>';
        echo '<span id="tc-sct-live-info" class="tc-adv-label">' . esc_html__( 'Live counting…', 'textcraft-tools' ) . '</span>';
        echo '</div>';

        $this->render_textarea(
            'tc-sct-input',
            '',
            esc_html__( 'Paste or type your text here to count sentences, words, characters, paragraphs, and reading time. All statistics update live as you write — try it now!', 'textcraft-tools' ),
            10
        );

        // Action buttons
        $this->render_button_row( [
            [ 'id' => 'tc-sct-copy',  'label' => '📋 ' . esc_html__( 'Copy Text', 'textcraft-tools' ), 'variant' => 'ghost'  ],
            [ 'id' => 'tc-sct-clear', 'label' => '🗑️ ' . esc_html__( 'Clear',     'textcraft-tools' ), 'variant' => 'danger' ],
        ] );

        // Advanced Statistics panel
        echo '<div class="tc-adv-panel">';
        echo '<h3 class="tc-adv-heading">' . esc_html__( 'Advanced Statistics', 'textcraft-tools' ) . '</h3>';
        echo '<div class="tc-adv-grid">';

        $adv_stats = [
            'tc-sct-wpersent' => __( 'Avg words / sentence', 'textcraft-tools' ),
            'tc-sct-cperword' => __( 'Avg chars / word',     'textcraft-tools' ),
            'tc-sct-unique'   => __( 'Unique words',         'textcraft-tools' ),
            'tc-sct-longest'  => __( 'Longest word',         'textcraft-tools' ),
            'tc-sct-speak'    => __( 'Speaking time',        'textcraft-tools' ),
        ];
        foreach ( $adv_stats as $id => $label ) {
            echo '<div class="tc-adv-row">';
            echo '<span class="tc-adv-label">' . esc_html( $label ) . '</span>';
            echo '<span id="' . esc_attr( $id ) . '" class="tc-adv-value">0</span>';
            echo '</div>';
        }

        echo '</div>'; // end adv grid
        echo '</div>'; // end advanced panel

        // Inline JS
        $this->render_inline_script( <<<'JS'
(function () {
    var inp = document.getElementById('tc-sct-input');
    if (!inp) return;

    function update() {
        var text  = inp.value;
        var words = text.trim() ? text.trim().split(/\s+/) : [];

        var sentences  = text.trim()
            ? ( text.match(/[^.!?]+[.!?]+/g) || [] ).length || ( text.trim() ? 1 : 0 )
            : 0;
        var paragraphs = text.trim()
            ? text.split(/\n\s*\n/).filter(function (p) { return p.trim(); }).length
            : 0;
        var charsNoSpace = text.replace(/\s/g, '').length;
        var readTime     = Math.max(1, Math.ceil(words.length / 200));
        var speakTime    = Math.max(1, Math.ceil(words.length / 130));

        // Primary stats
        document.getElementById('tc-sct-sentences').textContent  = sentences;
        document.getElementById('tc-sct-words').textContent      = words.length;
        document.getElementById('tc-sct-chars').textContent      = text.length;
        document.getElementById('tc-sct-chars-ns').textContent   = charsNoSpace;
        document.getElementById('tc-sct-paragraphs').textContent = paragraphs;
        document.getElementById('tc-sct-read').textContent       = words.length < 200 ? '< 1 min' : readTime + ' min';

        // Advanced stats
        var wpersent = sentences > 0 ? (words.length / sentences).toFixed(1) : 0;
        var cperword  = words.length > 0 ? (charsNoSpace / words.length).toFixed(1) : 0;
        var unique    = new Set(
            words.map(function (w) { return w.toLowerCase().replace(/[^a-z]/g, ''); })
        ).size;
        var longest   = words.reduce(function (a, b) {
            return b.replace(/[^a-z]/gi, '').length > a.replace(/[^a-z]/gi, '').length ? b : a;
        }, '');

        document.getElementById('tc-sct-wpersent').textContent = wpersent;
        document.getElementById('tc-sct-cperword').textContent = cperword;
        document.getElementById('tc-sct-unique').textContent   = unique;
        document.getElementById('tc-sct-longest').textContent  = longest.replace(/[^a-zA-Z]/g, '') || '—';
        document.getElementById('tc-sct-speak').textContent    = words.length < 130 ? '< 1 min' : speakTime + ' min';
    }

    inp.addEventListener('input', update);

    // Copy
    document.getElementById('tc-sct-copy').addEventListener('click', function () {
        if (!inp.value) return;
        var btn = document.getElementById('tc-sct-copy');
        navigator.clipboard.writeText(inp.value).then(function () {
            btn.textContent = '✅ Copied!';
            setTimeout(function () { btn.textContent = '📋 Copy Text'; }, 2000);
        });
    });

    // Clear
    document.getElementById('tc-sct-clear').addEventListener('click', function () {
        inp.value = '';
        update();
    });

    update();
})();
JS
        );
    }
}