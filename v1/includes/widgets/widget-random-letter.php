<?php
/**
 * Widget: Random Letter Generator (LetterDraw)
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;
class Widget_Random_Letter extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_random_letter'; }
    public function get_title(): string { return esc_html__( 'Random Letter Generator', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-type-tool'; }
    protected function render_tool_content( array $settings ): void {
        echo '<p class="tc-text-14 tc-text-muted tc-mb-20">' . esc_html__( 'Generate random letters from the alphabet — vowels, consonants, or custom sets. Perfect for word games, practice tests, or creative writing prompts. Works entirely in your browser.', 'textcraft-tools' ) . '</p>';

        // ── Row 1: Count + Case ───────────────────────────────────────────
        echo '<div class="tc-settings-grid">';

        // Count
        echo '<div>';
        echo '<label class="tc-label-upper tc-mb-8">' . esc_html__( 'How Many Letters', 'textcraft-tools' ) . '</label>';
        echo '<input type="number" id="tc-rl-count" value="10" min="1" max="1000" class="tc-input-md tc-font-bold">';
        echo '</div>';

        // Letter Case buttons
        echo '<div>';
        echo '<label class="tc-label-upper tc-mb-8">' . esc_html__( 'Letter Case', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap">';
        $cases = [
            [ 'case' => 'upper', 'label' => 'UPPERCASE', 'active' => true  ],
            [ 'case' => 'lower', 'label' => 'lowercase', 'active' => false ],
            [ 'case' => 'mixed', 'label' => 'Mixed',     'active' => false ],
        ];
        foreach ( $cases as $c ) {
            $cls = $c['active'] ? 'tc-btn tc-btn--primary tc-rl-case active' : 'tc-btn tc-btn--ghost tc-rl-case';
            echo '<button class="' . esc_attr( $cls ) . '" data-case="' . esc_attr( $c['case'] ) . '">' . esc_html( $c['label'] ) . '</button>';
        }
        echo '</div></div>';

        echo '</div>'; // end row 1

        // ── Row 2: Letter Type ────────────────────────────────────────────
        echo '<div class="tc-mb-20">';
        echo '<label class="tc-label-upper tc-mb-8">' . esc_html__( 'Letter Type', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap">';
        $types = [
            [ 'type' => 'all',        'label' => 'All (A–Z)',                                          'active' => true  ],
            [ 'type' => 'vowels',     'label' => 'Vowels Only<br><small>A E I O U</small>',            'active' => false ],
            [ 'type' => 'consonants', 'label' => 'Consonants Only',                                    'active' => false ],
            [ 'type' => 'custom',     'label' => 'Custom Set',                                         'active' => false ],
        ];
        foreach ( $types as $t ) {
            $cls = $t['active'] ? 'tc-btn tc-btn--primary tc-rl-type active' : 'tc-btn tc-btn--ghost tc-rl-type';
            echo '<button class="' . esc_attr( $cls ) . '" data-type="' . esc_attr( $t['type'] ) . '">' . $t['label'] . '</button>';
        }
        echo '</div>';
        echo '<div id="tc-rl-custom-row" class="tc-mt-10 tc-hidden">';
        echo '<input type="text" id="tc-rl-custom" placeholder="' . esc_attr__( 'Type letters to include, e.g. AEIOU or BCDF…', 'textcraft-tools' ) . '" maxlength="52" class="tc-text-14 tc-input-md">';
        echo '</div>';
        echo '</div>'; // end row 2

        // ── Row 3: Separator + Options ────────────────────────────────────
        echo '<div class="tc-settings-grid">';

        // Separator buttons
        echo '<div>';
        echo '<label class="tc-label-upper tc-mb-8">' . esc_html__( 'Separator', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap">';
        $seps = [
            [ 'sep' => 'space',   'label' => esc_html__( 'Space',    'textcraft-tools' ), 'active' => true  ],
            [ 'sep' => 'newline', 'label' => esc_html__( 'New Line', 'textcraft-tools' ), 'active' => false ],
            [ 'sep' => 'comma',   'label' => esc_html__( 'Comma',    'textcraft-tools' ), 'active' => false ],
            [ 'sep' => 'none',    'label' => esc_html__( 'None',     'textcraft-tools' ), 'active' => false ],
            [ 'sep' => 'dash',    'label' => esc_html__( 'Dash',     'textcraft-tools' ), 'active' => false ],
        ];
        foreach ( $seps as $s ) {
            $cls = $s['active'] ? 'tc-btn tc-btn--primary tc-rl-sep active' : 'tc-btn tc-btn--ghost tc-rl-sep';
            echo '<button class="' . esc_attr( $cls ) . '" data-sep="' . esc_attr( $s['sep'] ) . '">' . $s['label'] . '</button>';
        }
        echo '</div></div>';

        // Checkboxes
        echo '<div class="tc-flex-col-end">';
echo '<label class="tc-text-13 tc-flex-check-sm">';
echo '<input type="checkbox" id="tc-rl-unique" class="tc-checkbox"> ' . esc_html__( 'No duplicate letters', 'textcraft-tools' );
echo '</label>';
echo '<label class="tc-text-13 tc-flex-check-sm">';
        echo '<input type="checkbox" id="tc-rl-sort" class="tc-checkbox"> ' . esc_html__( 'Sort alphabetically', 'textcraft-tools' );
        echo '</label>';
        echo '</div>';

        echo '</div>'; // end row 3

        // ── Quick Presets ─────────────────────────────────────────────────
        echo '<div class="tc-mb-20">';
        echo '<p class="tc-section-label tc-m-0 tc-mb-8">' . esc_html__( 'Quick Presets', 'textcraft-tools' ) . '</p>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap">';
        $presets = [
            'scrabble'   => esc_html__( 'Scrabble Hand (7)',       'textcraft-tools' ),
            'full-alpha' => esc_html__( 'Full Alphabet (26)',       'textcraft-tools' ),
            'word'       => esc_html__( 'Random Word Length (4–8)', 'textcraft-tools' ),
            'initials'   => esc_html__( 'Initials (3)',             'textcraft-tools' ),
        ];
        foreach ( $presets as $key => $label ) {
            echo '<button class="tc-btn tc-btn--ghost tc-rl-preset" data-preset="' . esc_attr( $key ) . '">' . $label . '</button>';
        }
        echo '</div></div>';

        // ── Action buttons ────────────────────────────────────────────────
        $this->render_button_row( [
            [ 'id' => 'tc-rl-generate', 'label' => '🔠 ' . esc_html__( 'Generate Letters', 'textcraft-tools' ), 'variant' => 'primary' ],
            [ 'id' => 'tc-rl-copy',     'label' => '📋 ' . esc_html__( 'Copy Result',      'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-rl-clear',    'label' => '🗑️ ' . esc_html__( 'Clear',            'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        // ── Stats bar ─────────────────────────────────────────────────────
        $this->render_stat_bar( [
            [ 'id' => 'tc-rl-stat-gen',  'label' => esc_html__( 'Generated',   'textcraft-tools' ) ],
            [ 'id' => 'tc-rl-stat-vow',  'label' => esc_html__( 'Vowels',      'textcraft-tools' ) ],
            [ 'id' => 'tc-rl-stat-con',  'label' => esc_html__( 'Consonants',  'textcraft-tools' ) ],
        ] );

        // ── Output textarea ───────────────────────────────────────────────
        echo '<div class="tc-label-row tc-mt-20"><span class="tc-label">' . esc_html__( 'Generated Letters', 'textcraft-tools' ) . '</span></div>';
        $this->render_textarea( 'tc-rl-output', '', esc_html__( 'Your random letters will appear here. Choose case, type, and count then click Generate Letters.', 'textcraft-tools' ), 10, true );

        // ── Frequency chart ───────────────────────────────────────────────
        echo '<div id="tc-rl-freq-section" class="tc-hidden tc-mt-20">';
        echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'Letter Frequency', 'textcraft-tools' ) . '</span></div>';
        echo '<div id="tc-rl-freq-chart" class="tc-d-flex tc-flex-wrap tc-gap-6 tc-mt-10 tc-p-14-16 tc-card-surface tc-items-end"></div>';
        echo '</div>';

        // ── Inline JS ─────────────────────────────────────────────────────
        $this->render_inline_script( <<<'JS'
(function(){
    var out = document.getElementById('tc-rl-output');
    if (!out) return;

    var VOWELS     = ['A','E','I','O','U'];
    var ALL        = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');
    var CONSONANTS = ALL.filter(function(l){ return VOWELS.indexOf(l) === -1; });

    var letterCase = 'upper';
    var letterType = 'all';
    var sepMode    = 'space';

    // ── Button group helper ───────────────────────────────────────────
    function activateGroup(selector, btn) {
        document.querySelectorAll(selector).forEach(function(b){
            b.classList.remove('active','tc-btn--primary'); b.classList.add('tc-btn--ghost');
        });
        btn.classList.add('active','tc-btn--primary'); btn.classList.remove('tc-btn--ghost');
    }

    // Case buttons
    document.querySelectorAll('.tc-rl-case').forEach(function(btn){
        btn.addEventListener('click', function(){ activateGroup('.tc-rl-case', btn); letterCase = btn.dataset.case; });
    });

    // Type buttons
    document.querySelectorAll('.tc-rl-type').forEach(function(btn){
        btn.addEventListener('click', function(){
            activateGroup('.tc-rl-type', btn);
            letterType = btn.dataset.type;
            document.getElementById('tc-rl-custom-row').style.display = letterType === 'custom' ? 'block' : 'none';
        });
    });

    // Separator buttons
    document.querySelectorAll('.tc-rl-sep').forEach(function(btn){
        btn.addEventListener('click', function(){ activateGroup('.tc-rl-sep', btn); sepMode = btn.dataset.sep; });
    });

    // ── Presets ───────────────────────────────────────────────────────
    document.querySelectorAll('.tc-rl-preset').forEach(function(btn){
        btn.addEventListener('click', function(){
            var p = btn.dataset.preset;
            var countEl = document.getElementById('tc-rl-count');
            if (p === 'scrabble')   { countEl.value = 7; }
            if (p === 'full-alpha') { countEl.value = 26; document.getElementById('tc-rl-unique').checked = true; document.getElementById('tc-rl-sort').checked = true; }
            if (p === 'word')       { countEl.value = Math.floor(Math.random() * 5) + 4; }
            if (p === 'initials')   { countEl.value = 3; }
        });
    });

    // ── Pool builder ──────────────────────────────────────────────────
    function getPool() {
        if (letterType === 'vowels')     return VOWELS.slice();
        if (letterType === 'consonants') return CONSONANTS.slice();
        if (letterType === 'custom') {
            var raw = document.getElementById('tc-rl-custom').value.toUpperCase().replace(/[^A-Z]/g,'');
            var unique = [];
            raw.split('').forEach(function(c){ if (unique.indexOf(c) === -1) unique.push(c); });
            return unique.length ? unique : ALL.slice();
        }
        return ALL.slice();
    }

    function applyCase(letter) {
        if (letterCase === 'lower') return letter.toLowerCase();
        if (letterCase === 'mixed') return Math.random() > 0.5 ? letter : letter.toLowerCase();
        return letter;
    }

    function getSep() {
        switch (sepMode) {
            case 'newline': return '\n';
            case 'comma':   return ', ';
            case 'none':    return '';
            case 'dash':    return '-';
            default:        return ' ';
        }
    }

    // ── Generate ──────────────────────────────────────────────────────
    document.getElementById('tc-rl-generate').addEventListener('click', function(){
        var count  = Math.max(1, Math.min(1000, parseInt(document.getElementById('tc-rl-count').value) || 10));
        var unique = document.getElementById('tc-rl-unique').checked;
        var sortIt = document.getElementById('tc-rl-sort').checked;
        var pool   = getPool();

        if (!pool.length) { out.value = '⚠️ No letters in your custom set. Please add some letters.'; return; }
        if (unique && count > pool.length) {
            out.value = '⚠️ Cannot generate ' + count + ' unique letters from a pool of only ' + pool.length + '. Disable "No duplicate letters" or expand your pool.';
            return;
        }

        var letters = [];
        if (unique) {
            // Fisher-Yates shuffle then slice
            var shuffled = pool.slice();
            for (var i = shuffled.length - 1; i > 0; i--) {
                var j = Math.floor(Math.random() * (i + 1));
                var tmp = shuffled[i]; shuffled[i] = shuffled[j]; shuffled[j] = tmp;
            }
            letters = shuffled.slice(0, count);
        } else {
            for (var k = 0; k < count; k++) {
                letters.push(pool[Math.floor(Math.random() * pool.length)]);
            }
        }

        if (sortIt) letters.sort();

        var cased = letters.map(applyCase);
        out.value = cased.join(getSep());

        // Stats
        var upper      = cased.map(function(l){ return l.toUpperCase(); });
        var vowelCount = upper.filter(function(l){ return VOWELS.indexOf(l) !== -1; }).length;
        document.getElementById('tc-rl-stat-gen').textContent = cased.length;
        document.getElementById('tc-rl-stat-vow').textContent = vowelCount;
        document.getElementById('tc-rl-stat-con').textContent = cased.length - vowelCount;

        // Frequency chart
        var freq = {};
        upper.forEach(function(l){ freq[l] = (freq[l] || 0) + 1; });
        var freqEntries = Object.keys(freq).sort().map(function(l){ return [l, freq[l]]; });
        var maxFreq = Math.max.apply(null, freqEntries.map(function(e){ return e[1]; }));
        var chartEl = document.getElementById('tc-rl-freq-chart');
        chartEl.innerHTML = freqEntries.map(function(e){
            var barH = Math.round((e[1] / maxFreq) * 40) + 4;
            return '<div class="tc-text-center tc-min-w-32">'
                + '<div style="height:' + barH + 'px;background:var(--tc-accent);border-radius:3px 3px 0 0;opacity:0.85;"></div>'
                + '<div class="tc-text-11 tc-font-bold tc-text-primary tc-mt-3">' + e[0] + '</div>'
                + '<div class="tc-text-10 tc-text-muted">' + e[1] + '</div>'
                + '</div>';
        }).join('');
        document.getElementById('tc-rl-freq-section').style.display = freqEntries.length > 1 ? 'block' : 'none';
    });

    // ── Copy ──────────────────────────────────────────────────────────
    document.getElementById('tc-rl-copy').addEventListener('click', function(){
        if (!out.value) return;
        navigator.clipboard.writeText(out.value).then(function(){
            var btn = document.getElementById('tc-rl-copy');
            btn.textContent = '✅ Copied!';
            setTimeout(function(){ btn.textContent = '📋 Copy Result'; }, 2000);
        });
    });

    // ── Clear ─────────────────────────────────────────────────────────
    document.getElementById('tc-rl-clear').addEventListener('click', function(){
        out.value = '';
        document.getElementById('tc-rl-freq-section').style.display = 'none';
        ['tc-rl-stat-gen','tc-rl-stat-vow','tc-rl-stat-con'].forEach(function(id){
            document.getElementById(id).textContent = '0';
        });
    });
})();
JS
        );
    }
}