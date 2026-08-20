<?php
/**
 * Widget: Roman Numeral Date Converter
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;
class Widget_Roman_Numeral extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_roman_numeral'; }
    public function get_title(): string { return esc_html__( 'Roman Numeral Converter', 'textcraft-tools' ); }

    public function get_keywords(): array {
        return [ 'roman numeral converter', 'roman numeral date converter', 'convert to roman numerals', 'roman numeral translator', 'free online converter tool' ];
    }
    public function get_icon(): string  { return 'eicon-calendar'; }
    protected function render_tool_content( array $settings ): void {

        // ── Mode buttons ──────────────────────────────────────────────────
        echo '<div class="tc-d-flex tc-gap-8 tc-mb-24 tc-flex-wrap">';
        $modes = [
            [ 'mode' => 'date',   'label' => '📅 ' . esc_html__( 'Date to Roman',   'textcraft-tools' ), 'active' => true  ],
            [ 'mode' => 'number', 'label' => '🔢 ' . esc_html__( 'Number to Roman', 'textcraft-tools' ), 'active' => false ],
            [ 'mode' => 'decode', 'label' => '🏛️ ' . esc_html__( 'Roman to Number', 'textcraft-tools' ), 'active' => false ],
        ];
        foreach ( $modes as $m ) {
            $cls = $m['active'] ? 'tc-btn tc-btn--primary tc-rn-mode active' : 'tc-btn tc-btn--ghost tc-rn-mode';
            echo '<button class="' . esc_attr( $cls ) . '" data-mode="' . esc_attr( $m['mode'] ) . '">' . $m['label'] . '</button>';
        }
        echo '</div>';

        // ── Date mode panel ───────────────────────────────────────────────
        echo '<div id="tc-rn-panel-date">';
        echo '<div class="tc-grid-3col-fr">';
        $date_fields = [
            [ 'id' => 'tc-rn-day',   'label' => esc_html__( 'Day',   'textcraft-tools' ), 'min' => 1,  'max' => 31,   'ph' => 'DD'   ],
            [ 'id' => 'tc-rn-month', 'label' => esc_html__( 'Month', 'textcraft-tools' ), 'min' => 1,  'max' => 12,   'ph' => 'MM'   ],
            [ 'id' => 'tc-rn-year',  'label' => esc_html__( 'Year',  'textcraft-tools' ), 'min' => 1,  'max' => 3999, 'ph' => 'YYYY' ],
        ];
        foreach ( $date_fields as $f ) {
            echo '<div>';
            echo '<label class="tc-section-label tc-mb-6">' . $f['label'] . '</label>';
            echo '<input type="number" id="' . esc_attr( $f['id'] ) . '" min="' . $f['min'] . '" max="' . $f['max'] . '" placeholder="' . esc_attr( $f['ph'] ) . '" class="tc-rn-input">';
            echo '</div>';
        }
        echo '</div>';
        // Separator buttons
        echo '<div class="tc-d-flex tc-gap-8 tc-mb-16 tc-flex-wrap tc-items-center">';
        echo '<label class="tc-text-13 tc-text-muted">' . esc_html__( 'Separator:', 'textcraft-tools' ) . '</label>';
        $seps = [
            [ 'sep' => '·', 'label' => '· Dot',   'active' => true  ],
            [ 'sep' => '/', 'label' => '/ Slash',  'active' => false ],
            [ 'sep' => '-', 'label' => '- Dash',   'active' => false ],
            [ 'sep' => ' ', 'label' => 'Space',    'active' => false ],
        ];
        foreach ( $seps as $s ) {
            $cls = $s['active'] ? 'tc-btn tc-btn--primary tc-rn-sep active' : 'tc-btn tc-btn--ghost tc-rn-sep';
            echo '<button class="' . esc_attr( $cls ) . '" data-sep="' . esc_attr( $s['sep'] ) . '">' . esc_html( $s['label'] ) . '</button>';
        }
        echo '</div>';
        echo '</div>'; // end date panel

        // ── Number mode panel ─────────────────────────────────────────────
        echo '<div id="tc-rn-panel-number" class="tc-preview-wrap">';
        echo '<label class="tc-section-label tc-mb-6">' . esc_html__( 'Number (1–3999)', 'textcraft-tools' ) . '</label>';
        echo '<input type="number" id="tc-rn-number" min="1" max="3999" placeholder="' . esc_attr__( 'Enter a number between 1 and 3999…', 'textcraft-tools' ) . '" class="tc-rn-input-lg">';
        echo '</div>';

        // ── Decode mode panel ─────────────────────────────────────────────
        echo '<div id="tc-rn-panel-decode" class="tc-preview-wrap">';
        echo '<label class="tc-section-label tc-mb-6">' . esc_html__( 'Roman Numeral', 'textcraft-tools' ) . '</label>';
        echo '<input type="text" id="tc-rn-roman" placeholder="' . esc_attr__( 'e.g. MMXXIV or MCMXCVIII', 'textcraft-tools' ) . '" class="tc-rn-input-lg tc-text-uppercase tc-font-georgia">';
        echo '</div>';

        // ── Convert button ────────────────────────────────────────────────
        echo '<button class="tc-btn tc-btn--primary" id="tc-rn-convert">🏛️ ' . esc_html__( 'Convert', 'textcraft-tools' ) . '</button>';

        // ── Result box ────────────────────────────────────────────────────
        echo '<div id="tc-rn-result-box" class="tc-rn-result-box tc-hidden">';
        echo '<div id="tc-rn-result-main" class="tc-rn-result-main"></div>';
        echo '<div id="tc-rn-result-sub" class="tc-text-14 tc-text-muted"></div>';
        echo '<button class="tc-btn tc-btn--ghost tc-mt-16" id="tc-rn-copy">📋 ' . esc_html__( 'Copy', 'textcraft-tools' ) . '</button>';
        echo '</div>';

        // ── Roman Numeral Reference grid ──────────────────────────────────
        $reference = [ 'I' => 1, 'IV' => 4, 'V' => 5, 'IX' => 9, 'X' => 10, 'XL' => 40, 'L' => 50, 'XC' => 90, 'C' => 100, 'CD' => 400, 'D' => 500, 'CM' => 900, 'M' => 1000 ];
        echo '<div class="tc-rn-ref-section">';
        echo '<h3 class="tc-rn-ref-title">' . esc_html__( 'Roman Numeral Reference', 'textcraft-tools' ) . '</h3>';
        echo '<div class="tc-rn-ref-grid">';
        foreach ( $reference as $roman => $num ) {
            echo '<div class="tc-rn-ref-cell">';
            echo '<div class="tc-rn-ref-roman">' . esc_html( $roman ) . '</div>';
            echo '<div class="tc-rn-ref-num">' . esc_html( $num ) . '</div>';
            echo '</div>';
        }
        echo '</div></div>';

        // ── Inline JS ─────────────────────────────────────────────────────
        $this->render_inline_script( <<<'JS'
(function(){
    var currentMode = 'date';
    var sep = '·';

    // Mode buttons
    document.querySelectorAll('.tc-rn-mode').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('.tc-rn-mode').forEach(function(b){
                b.classList.remove('active','tc-btn--primary');
                b.classList.add('tc-btn--ghost');
            });
            btn.classList.add('active','tc-btn--primary');
            btn.classList.remove('tc-btn--ghost');
            currentMode = btn.dataset.mode;
            ['date','number','decode'].forEach(function(m){
                document.getElementById('tc-rn-panel-' + m).style.display = (m === currentMode) ? 'block' : 'none';
            });
            document.getElementById('tc-rn-result-box').style.display = 'none';
        });
    });

    // Separator buttons
    document.querySelectorAll('.tc-rn-sep').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('.tc-rn-sep').forEach(function(b){
                b.classList.remove('active','tc-btn--primary');
                b.classList.add('tc-btn--ghost');
            });
            btn.classList.add('active','tc-btn--primary');
            btn.classList.remove('tc-btn--ghost');
            sep = btn.dataset.sep;
        });
    });

    // Roman decode input — force uppercase
    var romanInp = document.getElementById('tc-rn-roman');
    if (romanInp) romanInp.addEventListener('input', function(){ this.value = this.value.toUpperCase(); });

    // toRoman
    function toRoman(num) {
        if (num < 1 || num > 3999) return null;
        var vals = [1000,900,500,400,100,90,50,40,10,9,5,4,1];
        var syms = ['M','CM','D','CD','C','XC','L','XL','X','IX','V','IV','I'];
        var result = '';
        for (var i = 0; i < vals.length; i++) {
            while (num >= vals[i]) { result += syms[i]; num -= vals[i]; }
        }
        return result;
    }

    // fromRoman
    function fromRoman(s) {
        var map = {I:1,V:5,X:10,L:50,C:100,D:500,M:1000};
        var result = 0;
        for (var i = 0; i < s.length; i++) {
            var cur = map[s[i]], next = map[s[i+1]];
            if (next && cur < next) { result += next - cur; i++; } else result += cur;
        }
        return result;
    }

    // Convert button
    document.getElementById('tc-rn-convert').addEventListener('click', function(){
        var box      = document.getElementById('tc-rn-result-box');
        var mainEl   = document.getElementById('tc-rn-result-main');
        var subEl    = document.getElementById('tc-rn-result-sub');

        box.style.display = 'block';

        if (currentMode === 'date') {
            var d = parseInt(document.getElementById('tc-rn-day').value)   || 0;
            var m = parseInt(document.getElementById('tc-rn-month').value) || 0;
            var y = parseInt(document.getElementById('tc-rn-year').value)  || 0;
            var parts = [];
            if (d) parts.push(toRoman(d));
            if (m) parts.push(toRoman(m));
            if (y) parts.push(toRoman(y));
            var valid = parts.filter(Boolean);
            if (!valid.length) {
                mainEl.textContent = '';
                subEl.textContent  = 'Please enter at least one date part.';
                return;
            }
            mainEl.textContent = valid.join(sep);
            subEl.textContent  = [d, m, y].filter(Boolean).join('/') + ' in Roman numerals';

        } else if (currentMode === 'number') {
            var n = parseInt(document.getElementById('tc-rn-number').value);
            var r = toRoman(n);
            if (!r) {
                mainEl.textContent = '';
                subEl.textContent  = 'Please enter a number between 1 and 3999.';
                return;
            }
            mainEl.textContent = r;
            subEl.textContent  = n + ' = ' + r;

        } else {
            var s = document.getElementById('tc-rn-roman').value.trim().toUpperCase();
            if (!s || !/^[IVXLCDM]+$/.test(s)) {
                mainEl.textContent = '';
                subEl.textContent  = 'Invalid Roman numeral. Use I, V, X, L, C, D, M only.';
                return;
            }
            var num = fromRoman(s);
            mainEl.textContent = num;
            subEl.textContent  = s + ' = ' + num;
        }
    });

    // Copy
    document.getElementById('tc-rn-copy').addEventListener('click', function(){
        var text = document.getElementById('tc-rn-result-main').textContent;
        if (!text) return;
        navigator.clipboard.writeText(text).then(function(){
            var btn = document.getElementById('tc-rn-copy');
            btn.textContent = '✅ Copied!';
            setTimeout(function(){ btn.textContent = '📋 Copy'; }, 2000);
        });
    });
})();
JS
        );
    }
}