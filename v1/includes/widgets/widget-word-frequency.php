<?php
/**
 * Widget: Word Frequency Counter
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;
class Widget_Word_Frequency extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_word_frequency'; }
    public function get_title(): string { return esc_html__( 'Word Frequency Counter', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-posts-grid'; }
    protected function render_tool_content( array $settings ): void {

        // ── Options row ───────────────────────────────────────────────────
        echo '<div class="tc-d-flex tc-gap-20 tc-flex-wrap tc-mb-20 tc-card-surface tc-p-14-16">';

        $checkboxes = [
            [ 'id' => 'tc-wf-case',  'label' => esc_html__( 'Case-sensitive',        'textcraft-tools' ), 'checked' => false ],
            [ 'id' => 'tc-wf-stop',  'label' => esc_html__( 'Ignore common words',   'textcraft-tools' ), 'checked' => true  ],
            [ 'id' => 'tc-wf-punct', 'label' => esc_html__( 'Strip punctuation',     'textcraft-tools' ), 'checked' => true  ],
        ];
        foreach ( $checkboxes as $cb ) {
echo '<label class="tc-flex-check tc-text-13">';
			echo '<input type="checkbox" id="' . esc_attr( $cb['id'] ) . '"' . ( $cb['checked'] ? ' checked' : '' ) . ' class="tc-checkbox"> ';
			echo $cb['label'];
			echo '</label>';
        }

        // Min length
        echo '<div class="tc-d-flex tc-items-center tc-gap-8 tc-text-13 tc-text-muted">';
        echo '<label for="tc-wf-minlen">' . esc_html__( 'Min length:', 'textcraft-tools' ) . '</label>';
        echo '<input type="number" id="tc-wf-minlen" class="tc-text-13 tc-wf-minlen" value="2" min="1" max="20">';
        echo '</div>';

        echo '</div>';

        // ── Input textarea ────────────────────────────────────────────────
        echo '<div class="tc-label-row">';
        echo '<label class="tc-label">' . esc_html__( 'Your Text', 'textcraft-tools' ) . '</label>';
        echo '<span id="tc-wf-wordcount" class="tc-char-count">0 ' . esc_html__( 'words', 'textcraft-tools' ) . '</span>';
        echo '</div>';
        $this->render_textarea(
            'tc-wf-input',
            '',
            esc_html__( 'Paste your text here to analyze word frequency. The tool will count every word and show you which words appear most often — useful for SEO, writing analysis, and more.', 'textcraft-tools' ),
            9
        );

        // ── Action buttons ────────────────────────────────────────────────
        $this->render_button_row( [
            [ 'id' => 'tc-wf-analyze', 'label' => '📊 ' . esc_html__( 'Analyze Frequency', 'textcraft-tools' ), 'variant' => 'primary' ],
            [ 'id' => 'tc-wf-export',  'label' => '📥 ' . esc_html__( 'Export CSV',         'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-wf-clear',   'label' => '🗑️ ' . esc_html__( 'Clear',              'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        // ── Results section (hidden until analyze) ────────────────────────
        echo '<div id="tc-wf-results" class="tc-mt-20 tc-hidden">';

        // Results header: label + filter + sort
        echo '<div class="tc-wf-header">';
        echo '<div class="tc-label">' . esc_html__( 'Word Frequency Results', 'textcraft-tools' ) . '</div>';
        echo '<div class="tc-d-flex tc-gap-8 tc-items-center">';
        echo '<input type="text" id="tc-wf-filter" class="tc-text-13 tc-wf-filter" placeholder="' . esc_attr__( 'Filter words…', 'textcraft-tools' ) . '">';
        echo '<select id="tc-wf-sort" class="tc-text-13 tc-wf-sort">';
        $sort_opts = [
            'freq-desc'  => esc_html__( 'Most frequent ↓',  'textcraft-tools' ),
            'freq-asc'   => esc_html__( 'Least frequent ↑', 'textcraft-tools' ),
            'alpha-asc'  => esc_html__( 'A–Z',              'textcraft-tools' ),
            'alpha-desc' => esc_html__( 'Z–A',              'textcraft-tools' ),
            'len-desc'   => esc_html__( 'Longest ↓',        'textcraft-tools' ),
        ];
        foreach ( $sort_opts as $val => $label ) {
            echo '<option value="' . esc_attr( $val ) . '">' . $label . '</option>';
        }
        echo '</select></div></div>';

        // Stats bar
        $this->render_stat_bar( [
            [ 'id' => 'tc-wf-stat-total',  'label' => esc_html__( 'Total Words',  'textcraft-tools' ) ],
            [ 'id' => 'tc-wf-stat-unique', 'label' => esc_html__( 'Unique Words', 'textcraft-tools' ) ],
            [ 'id' => 'tc-wf-stat-rich',   'label' => esc_html__( 'Richness',     'textcraft-tools' ) ],
            [ 'id' => 'tc-wf-stat-top',    'label' => esc_html__( 'Top Word',     'textcraft-tools' ) ],
        ] );

        // Table
        echo '<div class="tc-wf-table-wrap">';
        echo '<table class="tc-wf-table">';
        echo '<thead><tr class="tc-wf-th">';
        foreach ( [ '#', 'Word', 'Count', '%', 'Frequency' ] as $th ) {
            $align_class = in_array( $th, [ 'Count', '%' ], true ) ? 'tc-text-right' : '';
            echo '<th class="tc-wf-th-cell ' . $align_class . '">' . esc_html( $th ) . '</th>';
        }
        echo '</tr></thead>';
        echo '<tbody id="tc-wf-tbody"></tbody>';
        echo '</table></div>';

        echo '</div>'; // end results section

        // ── Inline JS ─────────────────────────────────────────────────────
        $this->render_inline_script( <<<'JS'
(function(){
    var inp = document.getElementById('tc-wf-input');
    if (!inp) return;

    var allFreqData = [];

    var STOP = new Set(['the','a','an','and','or','but','in','on','at','to','for','of','with','by','from','up','about','into','over','after','before','is','are','was','were','be','been','being','have','has','had','do','does','did','will','would','could','should','may','might','shall','can','this','that','these','those','it','its','i','my','me','we','our','you','your','he','his','him','she','her','they','their','them','not','no','so','as','if','then','than','too','very','just','also','more','most','other','some','such','well','what','when','where','who','which','how','all','any','each','both','few','through','during','above','below','between']);

    // Live word count
    inp.addEventListener('input', function(){
        var words = inp.value.trim().split(/\s+/).filter(Boolean);
        document.getElementById('tc-wf-wordcount').textContent = words.length + ' words';
    });

    // ── Sort helper ───────────────────────────────────────────────────
    function sortData(data, by) {
        var d = data.slice();
        if      (by === 'freq-desc')  d.sort(function(a,b){ return b[1]-a[1]; });
        else if (by === 'freq-asc')   d.sort(function(a,b){ return a[1]-b[1]; });
        else if (by === 'alpha-asc')  d.sort(function(a,b){ return a[0].localeCompare(b[0]); });
        else if (by === 'alpha-desc') d.sort(function(a,b){ return b[0].localeCompare(a[0]); });
        else if (by === 'len-desc')   d.sort(function(a,b){ return b[0].length-a[0].length; });
        return d;
    }

    // ── Render table ──────────────────────────────────────────────────
    function renderTable(data) {
        var filter  = document.getElementById('tc-wf-filter').value.toLowerCase();
        var filtered = filter ? data.filter(function(e){ return e[0].toLowerCase().indexOf(filter) !== -1; }) : data;
        var total   = data.reduce(function(s,e){ return s+e[1]; }, 0);
        var maxCount = data[0] ? data[0][1] : 1;
        var tbody   = document.getElementById('tc-wf-tbody');
        tbody.innerHTML = filtered.slice(0, 500).map(function(e, i){
            var pct  = total > 0 ? (e[1]/total*100).toFixed(2) : '0.00';
            var barW = Math.max(3, Math.round(e[1]/maxCount*100));
            return '<tr>'
                + '<td class="tc-text-muted">' + (i+1) + '</td>'
                + '<td class="tc-text-primary tc-font-semibold">' + e[0] + '</td>'
                + '<td class="tc-text-right tc-accent-value">' + e[1] + '</td>'
                + '<td class="tc-text-right tc-text-muted">' + pct + '%</td>'
                + '<td><div style="height:6px;border-radius:99px;background:var(--tc-accent);opacity:.7;width:' + barW + '%;"></div></td>'
                + '</tr>';
        }).join('');
    }

    // ── Analyze ───────────────────────────────────────────────────────
    document.getElementById('tc-wf-analyze').addEventListener('click', function(){
        var text      = inp.value;
        if (!text.trim()) return;
        var caseSens  = document.getElementById('tc-wf-case').checked;
        var ignStop   = document.getElementById('tc-wf-stop').checked;
        var stripPunct= document.getElementById('tc-wf-punct').checked;
        var minLen    = parseInt(document.getElementById('tc-wf-minlen').value) || 2;

        var words = text.match(/\S+/g) || [];
        if (stripPunct) words = words.map(function(w){ return w.replace(/^[^a-zA-Z0-9]+|[^a-zA-Z0-9]+$/g,''); });
        if (!caseSens)  words = words.map(function(w){ return w.toLowerCase(); });
        words = words.filter(function(w){ return w.length >= minLen; });
        if (ignStop) words = words.filter(function(w){ return !STOP.has(w.toLowerCase()); });

        var freq = {};
        words.forEach(function(w){ freq[w] = (freq[w]||0)+1; });
        allFreqData = Object.keys(freq).map(function(k){ return [k, freq[k]]; }).sort(function(a,b){ return b[1]-a[1]; });

        var total  = words.length;
        var unique = allFreqData.length;
        var richness = total > 0 ? Math.round(unique/total*100) : 0;

        document.getElementById('tc-wf-stat-total').textContent  = total;
        document.getElementById('tc-wf-stat-unique').textContent = unique;
        document.getElementById('tc-wf-stat-rich').textContent   = richness + '%';
        document.getElementById('tc-wf-stat-top').textContent    = allFreqData[0] ? allFreqData[0][0] : '—';

        renderTable(sortData(allFreqData, document.getElementById('tc-wf-sort').value));
        document.getElementById('tc-wf-results').style.display = 'block';
    });

    // Sort change
    document.getElementById('tc-wf-sort').addEventListener('change', function(){
        if (!allFreqData.length) return;
        renderTable(sortData(allFreqData, this.value));
    });

    // Filter input
    document.getElementById('tc-wf-filter').addEventListener('input', function(){
        if (!allFreqData.length) return;
        renderTable(sortData(allFreqData, document.getElementById('tc-wf-sort').value));
    });

    // ── Export CSV ────────────────────────────────────────────────────
    document.getElementById('tc-wf-export').addEventListener('click', function(){
        if (!allFreqData.length) { alert('Please analyze text first.'); return; }
        var total = allFreqData.reduce(function(s,e){ return s+e[1]; }, 0);
        var csv   = 'Word,Count,Percentage\n' + allFreqData.map(function(e){
            return '"' + e[0] + '",' + e[1] + ',' + (e[1]/total*100).toFixed(2) + '%';
        }).join('\n');
        var blob = new Blob([csv], { type: 'text/csv' });
        var a    = document.createElement('a');
        a.href   = URL.createObjectURL(blob);
        a.download = 'word-frequency.csv';
        a.click();
        URL.revokeObjectURL(a.href);
    });

    // ── Clear ─────────────────────────────────────────────────────────
    document.getElementById('tc-wf-clear').addEventListener('click', function(){
        inp.value = '';
        allFreqData = [];
        document.getElementById('tc-wf-results').style.display = 'none';
        document.getElementById('tc-wf-wordcount').textContent = '0 words';
        ['tc-wf-stat-total','tc-wf-stat-unique','tc-wf-stat-rich'].forEach(function(id){
            document.getElementById(id).textContent = '0';
        });
        document.getElementById('tc-wf-stat-top').textContent = '—';
    });
})();
JS
        );
    }
}
