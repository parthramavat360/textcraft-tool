<?php
/**
 * Widget: Sort Words Alphabetically
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;
class Widget_Sort_Words extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_sort_words'; }
    public function get_title(): string { return esc_html__( 'Word Sorter', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-sort'; }
    protected function render_tool_content( array $settings ): void {

        echo '<p class="tc-text-14 tc-text-muted tc-mb-20">'
            . esc_html__( 'Sort words alphabetically (A–Z or Z–A) with options for case sensitivity, removal of duplicates, and custom separators. A fast online utility for cleaning up word lists — all done in your browser.', 'textcraft-tools' )
            . '</p>';

        // ── Row 1: Sort Order + Sort By ───────────────────────────────────
        echo '<div class="tc-grid-2col tc-mb-20">';

        // Sort Order
        echo '<div>';
echo '<label class="tc-section-label tc-mb-8">' . esc_html__( 'Sort Order', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-8 tc-flex-wrap">';
        $orders = [
            [ 'order' => 'az',       'label' => 'A–Z',          'active' => true  ],
            [ 'order' => 'za',       'label' => 'Z–A',          'active' => false ],
            [ 'order' => 'len-asc',  'label' => 'Length ↑',     'active' => false ],
            [ 'order' => 'len-desc', 'label' => 'Length ↓',     'active' => false ],
            [ 'order' => 'random',   'label' => '🎲 Random',    'active' => false ],
        ];
        foreach ( $orders as $o ) {
            $cls = $o['active'] ? 'tc-btn tc-btn--primary tc-sw-order active' : 'tc-btn tc-btn--ghost tc-sw-order';
            echo '<button class="' . esc_attr( $cls ) . '" data-order="' . esc_attr( $o['order'] ) . '">' . esc_html( $o['label'] ) . '</button>';
        }
        echo '</div></div>';

        // Sort By
        echo '<div>';
        echo '<label class="tc-section-label tc-mb-8">' . esc_html__( 'Sort By', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-8 tc-flex-wrap">';
        $bys = [
            [ 'by' => 'lines', 'label' => esc_html__( 'Lines',           'textcraft-tools' ), 'active' => true  ],
            [ 'by' => 'words', 'label' => esc_html__( 'Words',           'textcraft-tools' ), 'active' => false ],
            [ 'by' => 'comma', 'label' => esc_html__( 'Comma-separated', 'textcraft-tools' ), 'active' => false ],
        ];
        foreach ( $bys as $b ) {
            $cls = $b['active'] ? 'tc-btn tc-btn--primary tc-sw-by active' : 'tc-btn tc-btn--ghost tc-sw-by';
            echo '<button class="' . esc_attr( $cls ) . '" data-by="' . esc_attr( $b['by'] ) . '">' . $b['label'] . '</button>';
        }
        echo '</div></div>';

        echo '</div>'; // end row 1

        // ── Options row ───────────────────────────────────────────────────
        $this->render_options_row( [
            [ 'id' => 'tc-sw-case',  'label' => esc_html__( 'Case-sensitive',     'textcraft-tools' ), 'checked' => false ],
            [ 'id' => 'tc-sw-dedup', 'label' => esc_html__( 'Remove duplicates',  'textcraft-tools' ), 'checked' => false ],
            [ 'id' => 'tc-sw-blank', 'label' => esc_html__( 'Remove blank lines', 'textcraft-tools' ), 'checked' => true  ],
            [ 'id' => 'tc-sw-trim',  'label' => esc_html__( 'Trim whitespace',    'textcraft-tools' ), 'checked' => true  ],
        ] );

        // ── Input textarea ────────────────────────────────────────────────
        echo '<div class="tc-label-row">';
        echo '<label class="tc-label">' . esc_html__( 'Your Text / List', 'textcraft-tools' ) . '</label>';
        echo '<span id="tc-sw-item-count" class="tc-char-count">0 ' . esc_html__( 'items', 'textcraft-tools' ) . '</span>';
        echo '</div>';
        $this->render_textarea(
            'tc-sw-input',
            '',
            esc_html__( "Paste your list here — one item per line:\nBanana\nApple\nCherry\nDate\nElderberry", 'textcraft-tools' ),
            9
        );

        // ── Action buttons ────────────────────────────────────────────────
        $this->render_button_row( [
            [ 'id' => 'tc-sw-sort',  'label' => '🔤 ' . esc_html__( 'Sort',        'textcraft-tools' ), 'variant' => 'primary' ],
            [ 'id' => 'tc-sw-copy',  'label' => '📋 ' . esc_html__( 'Copy Result', 'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-sw-clear', 'label' => '🗑️ ' . esc_html__( 'Clear',       'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        // ── Stats bar ─────────────────────────────────────────────────────
        $this->render_stat_bar( [
            [ 'id' => 'tc-sw-stat-items', 'label' => esc_html__( 'Items',             'textcraft-tools' ) ],
            [ 'id' => 'tc-sw-stat-after', 'label' => esc_html__( 'After Sort',        'textcraft-tools' ) ],
            [ 'id' => 'tc-sw-stat-dupes', 'label' => esc_html__( 'Duplicates Removed','textcraft-tools' ) ],
        ] );

        // ── Output textarea ───────────────────────────────────────────────
        echo '<div class="tc-label-row tc-mt-20"><span class="tc-label">' . esc_html__( 'Sorted Result', 'textcraft-tools' ) . '</span></div>';
        $this->render_textarea( 'tc-sw-output', '', esc_html__( 'Sorted list will appear here…', 'textcraft-tools' ), 9, true );

        // ── Inline JS ─────────────────────────────────────────────────────
        $this->render_inline_script( <<<'JS'
(function(){
    var inp = document.getElementById('tc-sw-input');
    var out = document.getElementById('tc-sw-output');
    if (!inp) return;

    var order = 'az';
    var by    = 'lines';

    // Live item count
    inp.addEventListener('input', function(){
        var items = inp.value.split('\n').filter(function(l){ return l.trim(); });
        document.getElementById('tc-sw-item-count').textContent = items.length + ' items';
    });

    // Sort Order buttons
    document.querySelectorAll('.tc-sw-order').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('.tc-sw-order').forEach(function(b){ b.classList.remove('active','tc-btn--primary'); b.classList.add('tc-btn--ghost'); });
            btn.classList.add('active','tc-btn--primary'); btn.classList.remove('tc-btn--ghost');
            order = btn.dataset.order;
        });
    });

    // Sort By buttons
    document.querySelectorAll('.tc-sw-by').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('.tc-sw-by').forEach(function(b){ b.classList.remove('active','tc-btn--primary'); b.classList.add('tc-btn--ghost'); });
            btn.classList.add('active','tc-btn--primary'); btn.classList.remove('tc-btn--ghost');
            by = btn.dataset.by;
        });
    });

    // Sort button
    document.getElementById('tc-sw-sort').addEventListener('click', function(){
        var text      = inp.value;
        var caseSens  = document.getElementById('tc-sw-case').checked;
        var dedup     = document.getElementById('tc-sw-dedup').checked;
        var rmBlank   = document.getElementById('tc-sw-blank').checked;
        var trimWS    = document.getElementById('tc-sw-trim').checked;

        var items;
        if      (by === 'lines') items = text.split('\n');
        else if (by === 'words') items = text.split(/\s+/);
        else                     items = text.split(',').map(function(s){ return s.trim(); });

        if (trimWS)  items = items.map(function(i){ return i.trim(); });
        if (rmBlank) items = items.filter(Boolean);

        var before = items.length;

        if (dedup) {
            var seen = {};
            items = items.filter(function(i){
                var k = caseSens ? i : i.toLowerCase();
                if (seen[k]) return false;
                seen[k] = true;
                return true;
            });
        }

        if (order === 'random') {
            for (var i = items.length - 1; i > 0; i--) {
                var j = Math.floor(Math.random() * (i + 1));
                var tmp = items[i]; items[i] = items[j]; items[j] = tmp;
            }
        } else if (order === 'az') {
            items.sort(function(a, b){ return (caseSens ? a : a.toLowerCase()).localeCompare(caseSens ? b : b.toLowerCase()); });
        } else if (order === 'za') {
            items.sort(function(a, b){ return (caseSens ? b : b.toLowerCase()).localeCompare(caseSens ? a : a.toLowerCase()); });
        } else if (order === 'len-asc') {
            items.sort(function(a, b){ return a.length - b.length; });
        } else {
            items.sort(function(a, b){ return b.length - a.length; });
        }

        var joiner = by === 'comma' ? ', ' : '\n';
        out.value = items.join(joiner);

        document.getElementById('tc-sw-stat-items').textContent = before;
        document.getElementById('tc-sw-stat-after').textContent = items.length;
        document.getElementById('tc-sw-stat-dupes').textContent = before - items.length;
    });

    // Copy
    document.getElementById('tc-sw-copy').addEventListener('click', function(){
        if (!out.value) return;
        navigator.clipboard.writeText(out.value).then(function(){
            var btn = document.getElementById('tc-sw-copy');
            btn.textContent = '✅ Copied!';
            setTimeout(function(){ btn.textContent = '📋 Copy Result'; }, 2000);
        });
    });

    // Clear
    document.getElementById('tc-sw-clear').addEventListener('click', function(){
        inp.value = ''; out.value = '';
        ['tc-sw-stat-items','tc-sw-stat-after','tc-sw-stat-dupes'].forEach(function(id){
            document.getElementById(id).textContent = '0';
        });
        document.getElementById('tc-sw-item-count').textContent = '0 items';
    });
})();
JS
        );
    }
}