<?php
/**
 * Widget: Random Choice Picker (SpinPick)
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;
class Widget_Random_Choice extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_random_choice'; }
    public function get_title(): string { return esc_html__( 'Random Choice Picker', 'textcraft-tools' ); }

    public function get_keywords(): array {
        return [ 'random picker', 'random choice generator', 'decision maker', 'pick randomly from list', 'free online decision tool' ];
    }
    public function get_icon(): string  { return 'eicon-target'; }
    protected function render_tool_content( array $settings ): void {
        echo '<p class="tc-text-14 tc-text-muted tc-mb-20">' . esc_html__( 'Make quick, unbiased random decisions. Pick from your list of options with custom draw counts — perfect for giveaways, team picks, or settling debates. Everything stays private in your browser.', 'textcraft-tools' ) . '</p>';

        // ── Options grid ──────────────────────────────────────────────────
        echo '<div class="tc-grid-settings">';

        // Pick Mode buttons
        echo '<div>';
        echo '<label class="tc-section-label tc-mb-8">' . esc_html__( 'Pick Mode', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap">';
        $modes = [
            [ 'mode' => 'one',    'label' => esc_html__( 'Pick 1',  'textcraft-tools' ), 'active' => true  ],
            [ 'mode' => 'three',  'label' => esc_html__( 'Pick 3',  'textcraft-tools' ), 'active' => false ],
            [ 'mode' => 'five',   'label' => esc_html__( 'Pick 5',  'textcraft-tools' ), 'active' => false ],
            [ 'mode' => 'custom', 'label' => esc_html__( 'Custom',  'textcraft-tools' ), 'active' => false ],
        ];
        foreach ( $modes as $m ) {
            $cls = $m['active'] ? 'tc-btn tc-btn--primary tc-rc-mode active' : 'tc-btn tc-btn--ghost tc-rc-mode';
            echo '<button class="' . esc_attr( $cls ) . '" data-mode="' . esc_attr( $m['mode'] ) . '">' . $m['label'] . '</button>';
        }
        echo '</div>';
        echo '<input type="number" id="tc-rc-custom-count" value="1" min="1" max="100" placeholder="' . esc_attr__( 'How many to pick…', 'textcraft-tools' ) . '" class="tc-text-15 tc-p-10-12 tc-rc-custom-input tc-hidden">';
        echo '</div>';

        // Entry Format + No duplicates
        echo '<div>';
        echo '<label class="tc-section-label tc-mb-8">' . esc_html__( 'Entry Format', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap">';
        $fmts = [
            [ 'fmt' => 'line',  'label' => esc_html__( 'One per line',      'textcraft-tools' ), 'active' => true  ],
            [ 'fmt' => 'comma', 'label' => esc_html__( 'Comma-separated',   'textcraft-tools' ), 'active' => false ],
        ];
        foreach ( $fmts as $f ) {
            $cls = $f['active'] ? 'tc-btn tc-btn--primary tc-rc-fmt active' : 'tc-btn tc-btn--ghost tc-rc-fmt';
            echo '<button class="' . esc_attr( $cls ) . '" data-fmt="' . esc_attr( $f['fmt'] ) . '">' . $f['label'] . '</button>';
        }
        echo '</div>';
        echo '<div class="tc-mt-8">';
        echo '<label class="tc-text-13 tc-d-flex tc-items-center tc-gap-6 tc-cursor-pointer">';
        echo '<input type="checkbox" id="tc-rc-norepeat" checked class="tc-checkbox"> ' . esc_html__( 'No duplicates in result', 'textcraft-tools' );
        echo '</label>';
        echo '</div>';
        echo '</div>';

        echo '</div>'; // end grid

        // ── Input textarea ────────────────────────────────────────────────
        echo '<div class="tc-label-row">';
        echo '<label class="tc-label">' . esc_html__( 'Your Options (one per line)', 'textcraft-tools' ) . '</label>';
        echo '<span id="tc-rc-item-count" class="tc-char-count">0 ' . esc_html__( 'items', 'textcraft-tools' ) . '</span>';
        echo '</div>';
        $this->render_textarea(
            'tc-rc-input',
            '',
            esc_html__( "Pizza\nSushi\nTacos\nBurgers\nPasta\nRamen\n\nAdd as many choices as you like — each on a new line…", 'textcraft-tools' ),
            8
        );

        // ── Buttons ───────────────────────────────────────────────────────
        $this->render_button_row( [
            [ 'id' => 'tc-rc-spin',  'label' => '🎲 ' . esc_html__( 'Pick Random',  'textcraft-tools' ), 'variant' => 'primary' ],
            [ 'id' => 'tc-rc-copy',  'label' => '📋 ' . esc_html__( 'Copy Result',  'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-rc-clear', 'label' => '🗑️ ' . esc_html__( 'Clear',        'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        // ── Stats bar ─────────────────────────────────────────────────────
        $this->render_stat_bar( [
            [ 'id' => 'tc-rc-total',  'label' => esc_html__( 'Total Options', 'textcraft-tools' ) ],
            [ 'id' => 'tc-rc-picked', 'label' => esc_html__( 'Picked',        'textcraft-tools' ) ],
            [ 'id' => 'tc-rc-spins',  'label' => esc_html__( 'Spins',         'textcraft-tools' ) ],
        ] );

        // ── Result display ────────────────────────────────────────────────
        echo '<div class="tc-label-row tc-mt-20">';
        echo '<span class="tc-label">🏆 ' . esc_html__( 'Picked Choice(s)', 'textcraft-tools' ) . '</span>';
        echo '<span id="tc-rc-badge" class="tc-rc-badge tc-hidden">' . esc_html__( 'New!', 'textcraft-tools' ) . '</span>';
        echo '</div>';
        echo '<div id="tc-rc-result" class="tc-rc-result tc-text-16">';
        echo '<p class="tc-text-muted tc-text-center tc-m-0 tc-pt-12">' . esc_html__( 'Your randomly picked choices will appear here. Add your options above and click Pick Random.', 'textcraft-tools' ) . '</p>';
        echo '</div>';
        echo '<textarea id="tc-rc-hidden" class="tc-d-none" readonly></textarea>';

        // ── Inline JS ─────────────────────────────────────────────────────
        $this->render_inline_script( <<<'JS'
(function(){
    var inp      = document.getElementById('tc-rc-input');
    var resultEl = document.getElementById('tc-rc-result');
    var hiddenEl = document.getElementById('tc-rc-hidden');
    if (!inp) return;

    var fmt     = 'line';
    var pickNum = 1;
    var spins   = 0;

    // ── Format buttons ────────────────────────────────────────────────
    document.querySelectorAll('.tc-rc-fmt').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('.tc-rc-fmt').forEach(function(b){
                b.classList.remove('active','tc-btn--primary'); b.classList.add('tc-btn--ghost');
            });
            btn.classList.add('active','tc-btn--primary'); btn.classList.remove('tc-btn--ghost');
            fmt = btn.dataset.fmt;
            updateCount();
        });
    });

    // ── Mode buttons ──────────────────────────────────────────────────
    document.querySelectorAll('.tc-rc-mode').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('.tc-rc-mode').forEach(function(b){
                b.classList.remove('active','tc-btn--primary'); b.classList.add('tc-btn--ghost');
            });
            btn.classList.add('active','tc-btn--primary'); btn.classList.remove('tc-btn--ghost');
            var m = btn.dataset.mode;
            var customInp = document.getElementById('tc-rc-custom-count');
            customInp.style.display = m === 'custom' ? 'block' : 'none';
            pickNum = m === 'one' ? 1 : m === 'three' ? 3 : m === 'five' ? 5 : parseInt(customInp.value) || 1;
        });
    });
    document.getElementById('tc-rc-custom-count').addEventListener('input', function(){
        pickNum = parseInt(this.value) || 1;
    });

    // ── Get items from textarea ───────────────────────────────────────
    function getItems() {
        var raw = inp.value;
        if (!raw.trim()) return [];
        if (fmt === 'comma') return raw.split(',').map(function(s){ return s.trim(); }).filter(Boolean);
        return raw.split('\n').map(function(s){ return s.trim(); }).filter(Boolean);
    }

    // ── Update item count ─────────────────────────────────────────────
    function updateCount() {
        var items = getItems();
        var lbl   = items.length + (items.length !== 1 ? ' items' : ' item');
        document.getElementById('tc-rc-item-count').textContent = lbl;
        document.getElementById('tc-rc-total').textContent      = items.length;
    }
    inp.addEventListener('input', updateCount);

    // ── Fisher-Yates shuffle ──────────────────────────────────────────
    function shuffle(arr) {
        var a = arr.slice();
        for (var i = a.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var tmp = a[i]; a[i] = a[j]; a[j] = tmp;
        }
        return a;
    }

    // ── Escape HTML ───────────────────────────────────────────────────
    function esc(str) {
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    // ── Spin / Pick ───────────────────────────────────────────────────
    document.getElementById('tc-rc-spin').addEventListener('click', function(){
        var items = getItems();
        if (!items.length) {
            resultEl.innerHTML = '<p class="tc-text-center tc-m-0 tc-pt-12 tc-text-red">⚠️ ' + 'Please enter at least one choice first.' + '</p>';
            return;
        }
        var noRep  = document.getElementById('tc-rc-norepeat').checked;
        var n      = noRep ? Math.min(pickNum, items.length) : pickNum;
        var pool   = noRep ? shuffle(items) : items;
        var picked = noRep
            ? pool.slice(0, n)
            : Array.from({ length: n }, function(){ return pool[Math.floor(Math.random() * pool.length)]; });

        spins++;
        document.getElementById('tc-rc-spins').textContent  = spins;
        document.getElementById('tc-rc-picked').textContent = picked.length;
        document.getElementById('tc-rc-total').textContent  = items.length;

        // Fade animate
        resultEl.style.opacity = '0';
        setTimeout(function(){
            resultEl.innerHTML = picked.map(function(item, i){
                return '<div class="tc-d-flex tc-items-center tc-gap-10 tc-py-10" style="' + (i > 0 ? 'border-top:1px solid var(--tc-border);' : '') + '">'
                    + '<span class="tc-rank-badge tc-text-12 tc-font-bold">' + (i + 1) + '</span>'
                    + '<span class="tc-font-semibold tc-text-primary tc-text-1-05rem">' + esc(item) + '</span>'
                    + '</div>';
            }).join('');
            resultEl.style.opacity = '1';
            hiddenEl.value = picked.join('\n');

            // "New!" badge
            var badge = document.getElementById('tc-rc-badge');
            badge.style.display = 'inline-block';
            setTimeout(function(){ badge.style.display = 'none'; }, 2500);
        }, 150);
    });

    // ── Copy ──────────────────────────────────────────────────────────
    document.getElementById('tc-rc-copy').addEventListener('click', function(){
        if (!hiddenEl.value) return;
        navigator.clipboard.writeText(hiddenEl.value).then(function(){
            var btn = document.getElementById('tc-rc-copy');
            btn.textContent = '✅ Copied!';
            setTimeout(function(){ btn.textContent = '📋 Copy Result'; }, 2000);
        });
    });

    // ── Clear ─────────────────────────────────────────────────────────
    document.getElementById('tc-rc-clear').addEventListener('click', function(){
        inp.value = ''; hiddenEl.value = '';
        resultEl.innerHTML = '<p class="tc-text-secondary tc-text-center tc-m-0 tc-pt-12">Your picked choices will appear here — press <strong>Pick Random</strong> to start!</p>';
        ['tc-rc-total','tc-rc-picked'].forEach(function(id){ document.getElementById(id).textContent = '0'; });
        document.getElementById('tc-rc-item-count').textContent = '0 items';
    });

    updateCount();
})();
JS
        );
    }
}