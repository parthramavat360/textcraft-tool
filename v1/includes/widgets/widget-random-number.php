<?php
/**
 * Widget: Random Number Generator
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;

class Widget_Random_Number extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_random_number'; }
    public function get_title(): string { return esc_html__( 'Random Number Generator', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-counter'; }
    protected function render_tool_content( array $settings ): void {
        echo '<p class="tc-text-14 tc-text-muted tc-mb-20">' . esc_html__( 'Generate random numbers with integers, decimals, or multiples. Ideal for giveaways, statistical sampling, or testing — all generated instantly and privately in your browser.', 'textcraft-tools' ) . '</p>';

        // Row 1: Min / Max / Count
        echo '<div class="tc-grid-3col">';
        echo '<div><label class="tc-label tc-d-block tc-mb-6">' . esc_html__( 'Minimum Value', 'textcraft-tools' ) . '</label><input type="number" id="tc-rn2-min" class="tc-text-input" value="1" step="any"></div>';
        echo '<div><label class="tc-label tc-d-block tc-mb-6">' . esc_html__( 'Maximum Value', 'textcraft-tools' ) . '</label><input type="number" id="tc-rn2-max" class="tc-text-input" value="100" step="any"></div>';
        echo '<div><label class="tc-label tc-d-block tc-mb-6">' . esc_html__( 'How Many Numbers', 'textcraft-tools' ) . '</label><input type="number" id="tc-rn2-count" class="tc-text-input" value="10" min="1" max="1000"></div>';
        echo '</div>';

        // Row 2: Number Type buttons
        echo '<div class="tc-mb-16">';
        echo '<label class="tc-label tc-d-block tc-mb-8">' . esc_html__( 'Number Type', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap" id="tc-rn2-type-group">';
        $types = [
            'integer'  => [ 'label' => __( 'Integers',     'textcraft-tools' ), 'sub' => __( 'Whole numbers',  'textcraft-tools' ) ],
            'decimal'  => [ 'label' => __( 'Decimals',     'textcraft-tools' ), 'sub' => __( 'Floating point', 'textcraft-tools' ) ],
            'even'     => [ 'label' => __( 'Even Only',    'textcraft-tools' ), 'sub' => '' ],
            'odd'      => [ 'label' => __( 'Odd Only',     'textcraft-tools' ), 'sub' => '' ],
            'multiple' => [ 'label' => __( 'Multiples of…','textcraft-tools' ), 'sub' => '' ],
        ];
        $first = true;
        foreach ( $types as $val => $info ) {
            $active  = $first ? ' tc-btn-active' : '';
            $variant = $first ? 'primary' : 'secondary';
            echo '<button class="tc-btn tc-btn--' . esc_attr( $variant ) . ' tc-rn2-type-btn' . $active . '" data-type="' . esc_attr( $val ) . '">';
            echo esc_html( $info['label'] );
            if ( $info['sub'] ) echo '<br><small>' . esc_html( $info['sub'] ) . '</small>';
            echo '</button>';
            $first = false;
        }
        echo '</div>';

        // Conditional sub-inputs: decimal places + multiple-of
        echo '<div class="tc-grid-2col-12 tc-mt-12">';
        echo '<div id="tc-rn2-decimal-row" class="tc-hidden">';
        echo '<label class="tc-label tc-d-block tc-mb-6">' . esc_html__( 'Decimal Places', 'textcraft-tools' ) . '</label>';
        echo '<input type="number" id="tc-rn2-decimal-places" class="tc-text-input" value="2" min="1" max="10">';
        echo '</div>';
        echo '<div id="tc-rn2-multiple-row" class="tc-hidden">';
        echo '<label class="tc-label tc-d-block tc-mb-6">' . esc_html__( 'Multiple of', 'textcraft-tools' ) . '</label>';
        echo '<input type="number" id="tc-rn2-multiple-of" class="tc-text-input" value="5" min="1">';
        echo '</div>';
        echo '</div>';
        echo '</div>'; // end row 2

        // Row 3: Separator + Options
        echo '<div class="tc-grid-settings-sm">';

        echo '<div>';
        echo '<label class="tc-label tc-d-block tc-mb-8">' . esc_html__( 'Separator', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap" id="tc-rn2-sep-group">';
        $seps = [
            'newline' => __( 'New Line',   'textcraft-tools' ),
            'comma'   => __( 'Comma',      'textcraft-tools' ),
            'space'   => __( 'Space',      'textcraft-tools' ),
            'json'    => __( 'JSON Array', 'textcraft-tools' ),
        ];
        $first = true;
        foreach ( $seps as $val => $label ) {
            $active  = $first ? ' tc-btn-active' : '';
            $variant = $first ? 'primary' : 'secondary';
            echo '<button class="tc-btn tc-btn--' . esc_attr( $variant ) . ' tc-rn2-sep-btn' . $active . '" data-sep="' . esc_attr( $val ) . '">' . esc_html( $label ) . '</button>';
            $first = false;
        }
        echo '</div>';
        echo '</div>';

        echo '<div class="tc-options tc-flex-col-end">';
        $this->render_options_row( [
            [ 'id' => 'tc-rn2-nodup',        'label' => esc_html__( 'No duplicate numbers',       'textcraft-tools' ), 'checked' => false ],
            [ 'id' => 'tc-rn2-sort',         'label' => esc_html__( 'Sort ascending',              'textcraft-tools' ), 'checked' => false ],
            [ 'id' => 'tc-rn2-comma-format', 'label' => esc_html__( 'Comma-format large numbers',  'textcraft-tools' ), 'checked' => false ],
        ] );
        echo '</div>';

        echo '</div>'; // end row 3

        // Quick Presets
        echo '<div class="tc-mb-16">';
        echo '<label class="tc-label tc-d-block tc-mb-8">' . esc_html__( 'Quick Presets', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap">';
        $presets = [
            'dice'    => '🎲 ' . __( 'Dice Roll (1–6)',     'textcraft-tools' ),
            'coin'    => '🪙 ' . __( 'Coin Flip (0–1)',     'textcraft-tools' ),
            'percent' => '📊 ' . __( 'Percentage (0–100)',  'textcraft-tools' ),
            'lottery' => '🎰 ' . __( 'Lottery (1–49)',      'textcraft-tools' ),
            'pin'     => '🔢 ' . __( 'PIN (1000–9999)',     'textcraft-tools' ),
            'large'   => '🔭 ' . __( 'Large (1M–1B)',       'textcraft-tools' ),
        ];
        foreach ( $presets as $val => $label ) {
            echo '<button class="tc-btn tc-btn--secondary tc-rn2-preset-btn" data-preset="' . esc_attr( $val ) . '">' . esc_html( $label ) . '</button>';
        }
        echo '</div>';
        echo '</div>'; // end presets

        // Action buttons
        $this->render_button_row( [
            [ 'id' => 'tc-rn2-generate', 'label' => '🎲 ' . esc_html__( 'Generate Numbers', 'textcraft-tools' ), 'variant' => 'primary' ],
            [ 'id' => 'tc-rn2-copy',     'label' => '📋 ' . esc_html__( 'Copy All',         'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-rn2-clear',    'label' => '🗑️ ' . esc_html__( 'Clear',            'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        // Stats bar
        $this->render_stat_bar( [
            [ 'id' => 'tc-rn2-stat-count', 'label' => esc_html__( 'Generated',  'textcraft-tools' ) ],
            [ 'id' => 'tc-rn2-stat-min',   'label' => esc_html__( 'Min Result', 'textcraft-tools' ) ],
            [ 'id' => 'tc-rn2-stat-max',   'label' => esc_html__( 'Max Result', 'textcraft-tools' ) ],
            [ 'id' => 'tc-rn2-stat-avg',   'label' => esc_html__( 'Average',    'textcraft-tools' ) ],
        ] );

        // Output textarea
        echo '<div class="tc-label-row tc-mt-20"><span class="tc-label">' . esc_html__( 'Generated Numbers', 'textcraft-tools' ) . '</span></div>';
        $this->render_textarea( 'tc-rn2-output', '', esc_html__( 'Your random numbers will appear here. Set your range and click Generate Numbers to get started.', 'textcraft-tools' ), 12, true );

        // Inline JS
        $this->render_inline_script( <<<'JS'
(function () {
    var numType = 'integer';
    var sepMode = 'newline';

    var out         = document.getElementById('tc-rn2-output');
    var minEl       = document.getElementById('tc-rn2-min');
    var maxEl       = document.getElementById('tc-rn2-max');
    var countEl     = document.getElementById('tc-rn2-count');
    var nodupEl     = document.getElementById('tc-rn2-nodup');
    var sortEl      = document.getElementById('tc-rn2-sort');
    var commaFmtEl  = document.getElementById('tc-rn2-comma-format');
    var decPlacesEl = document.getElementById('tc-rn2-decimal-places');
    var multipleEl  = document.getElementById('tc-rn2-multiple-of');
    var decRow      = document.getElementById('tc-rn2-decimal-row');
    var mulRow      = document.getElementById('tc-rn2-multiple-row');
    var statCount   = document.getElementById('tc-rn2-stat-count');
    var statMin     = document.getElementById('tc-rn2-stat-min');
    var statMax     = document.getElementById('tc-rn2-stat-max');
    var statAvg     = document.getElementById('tc-rn2-stat-avg');

    if (!out) return;

    // Button group helper
    function activateGroup(selector, clicked, dataKey, setter) {
        document.querySelectorAll(selector).forEach(function (b) {
            b.classList.remove('tc-btn-active', 'tc-btn--primary');
            b.classList.add('tc-btn--secondary');
        });
        clicked.classList.add('tc-btn-active', 'tc-btn--primary');
        clicked.classList.remove('tc-btn--secondary');
        setter(clicked.getAttribute('data-' + dataKey));
    }

    // Type buttons
    document.querySelectorAll('.tc-rn2-type-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            activateGroup('.tc-rn2-type-btn', btn, 'type', function (v) { numType = v; });
            decRow.style.display = numType === 'decimal'  ? 'block' : 'none';
            mulRow.style.display = numType === 'multiple' ? 'block' : 'none';
        });
    });

    // Separator buttons
    document.querySelectorAll('.tc-rn2-sep-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            activateGroup('.tc-rn2-sep-btn', btn, 'sep', function (v) { sepMode = v; });
        });
    });

    // Presets
    var PRESETS = {
        dice:    { min: 1,       max: 6,          count: 1,  unique: false, sort: false },
        coin:    { min: 0,       max: 1,          count: 1,  unique: false, sort: false },
        percent: { min: 0,       max: 100,        count: 10, unique: false, sort: false },
        lottery: { min: 1,       max: 49,         count: 6,  unique: true,  sort: true  },
        pin:     { min: 1000,    max: 9999,       count: 1,  unique: false, sort: false },
        large:   { min: 1000000, max: 1000000000, count: 5,  unique: false, sort: false },
    };

    document.querySelectorAll('.tc-rn2-preset-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var p = PRESETS[btn.getAttribute('data-preset')];
            if (!p) return;
            minEl.value   = p.min;
            maxEl.value   = p.max;
            countEl.value = p.count;
            nodupEl.checked = !!p.unique;
            sortEl.checked  = !!p.sort;
            // reset type to integer
            document.querySelectorAll('.tc-rn2-type-btn').forEach(function (b) {
                b.classList.remove('tc-btn-active', 'tc-btn--primary');
                b.classList.add('tc-btn--secondary');
            });
            var intBtn = document.querySelector('.tc-rn2-type-btn[data-type="integer"]');
            if (intBtn) { intBtn.classList.add('tc-btn-active', 'tc-btn--primary'); intBtn.classList.remove('tc-btn--secondary'); }
            numType = 'integer';
            decRow.style.display = 'none';
            mulRow.style.display = 'none';
        });
    });

    function formatNum(n, useComma) {
        if (!useComma) return String(n);
        return n.toLocaleString('en-US');
    }

    function genOne(minVal, maxVal, decPlaces, multipleOf) {
        if (numType === 'decimal') {
            return parseFloat((Math.random() * (maxVal - minVal) + minVal).toFixed(decPlaces));
        }
        if (numType === 'even') {
            var n = Math.round(Math.random() * (maxVal - minVal) + minVal);
            if (n % 2 !== 0) n = (n + 1 <= maxVal) ? n + 1 : n - 1;
            return n;
        }
        if (numType === 'odd') {
            var n = Math.round(Math.random() * (maxVal - minVal) + minVal);
            if (n % 2 === 0) n = (n + 1 <= maxVal) ? n + 1 : n - 1;
            return n;
        }
        if (numType === 'multiple') {
            var lo = Math.ceil(minVal / multipleOf);
            var hi = Math.floor(maxVal / multipleOf);
            if (lo > hi) return null;
            return (Math.floor(Math.random() * (hi - lo + 1)) + lo) * multipleOf;
        }
        // integer
        return Math.floor(Math.random() * (maxVal - minVal + 1)) + Math.ceil(minVal);
    }

    // Generate
    document.getElementById('tc-rn2-generate').addEventListener('click', function () {
        var minVal     = parseFloat(minEl.value);
        var maxVal     = parseFloat(maxEl.value);
        var count      = Math.max(1, Math.min(1000, parseInt(countEl.value) || 10));
        var nodup      = nodupEl.checked;
        var doSort     = sortEl.checked;
        var useComma   = commaFmtEl.checked;
        var decPlaces  = parseInt(decPlacesEl.value) || 2;
        var multipleOf = parseInt(multipleEl.value)  || 5;

        if (isNaN(minVal) || isNaN(maxVal)) { out.value = '⚠️ Please enter valid min and max values.'; return; }
        if (minVal > maxVal)                 { out.value = '⚠️ Minimum must be less than or equal to Maximum.'; return; }

        var numbers = [], seen = new Set();
        var attempts = 0, maxAttempts = count * 100;

        while (numbers.length < count && attempts < maxAttempts) {
            attempts++;
            var n = genOne(minVal, maxVal, decPlaces, multipleOf);
            if (n === null) break;
            var key = String(n);
            if (nodup && seen.has(key)) continue;
            seen.add(key);
            numbers.push(n);
        }

        if (numbers.length === 0) {
            out.value = '⚠️ Could not generate numbers with the current settings. Try widening the range or disabling "No duplicate numbers".';
            return;
        }

        if (doSort) numbers.sort(function (a, b) { return a - b; });

        var formatted = numbers.map(function (n) { return formatNum(n, useComma); });
        switch (sepMode) {
            case 'comma': out.value = formatted.join(', '); break;
            case 'space': out.value = formatted.join(' ');  break;
            case 'json':  out.value = JSON.stringify(numbers, null, 2); break;
            default:      out.value = formatted.join('\n');
        }

        // Stats
        var minRes = Math.min.apply(null, numbers);
        var maxRes = Math.max.apply(null, numbers);
        var sum    = numbers.reduce(function (s, n) { return s + n; }, 0);
        var avg    = sum / numbers.length;

        statCount.textContent = numbers.length;
        statMin.textContent   = formatNum(minRes, false);
        statMax.textContent   = formatNum(maxRes, false);
        statAvg.textContent   = avg.toFixed(numType === 'decimal' ? decPlaces : 2);
    });

    // Copy
    document.getElementById('tc-rn2-copy').addEventListener('click', function () {
        if (!out.value) return;
        var btn = document.getElementById('tc-rn2-copy');
        navigator.clipboard.writeText(out.value).then(function () {
            btn.textContent = '✅ Copied!';
            setTimeout(function () { btn.textContent = '📋 Copy All'; }, 2000);
        });
    });

    // Clear
    document.getElementById('tc-rn2-clear').addEventListener('click', function () {
        out.value = '';
        statCount.textContent = '0';
        statMin.textContent   = '—';
        statMax.textContent   = '—';
        statAvg.textContent   = '—';
    });
})();
JS
        );
    }
}
