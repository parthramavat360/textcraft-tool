<?php
/**
 * Widget: Random Month Generator
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;
class Widget_Random_Month extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_random_month'; }
    public function get_title(): string { return esc_html__( 'Random Month Generator', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-calendar'; }
    protected function render_tool_content( array $settings ): void {
        echo '<p class="tc-text-14 tc-text-muted tc-mb-20">' . esc_html__( 'Pick random months by season, quarter, or half-year. Great for scheduling demos, testing date logic, or classroom activities. All processing is done privately in your browser.', 'textcraft-tools' ) . '</p>';

        // Row 1: Count + Filter
        echo '<div class="tc-grid-settings-sm">';

        echo '<div>';
        echo '<label class="tc-label tc-d-block tc-mb-6">' . esc_html__( 'How Many Months', 'textcraft-tools' ) . '</label>';
        echo '<input type="number" id="tc-rm-count" class="tc-text-input" value="3" min="1" max="1000">';
        echo '</div>';

        echo '<div>';
        echo '<label class="tc-label tc-d-block tc-mb-6">' . esc_html__( 'Filter by Season or Quarter', 'textcraft-tools' ) . '</label>';
        echo '<select id="tc-rm-filter" class="tc-text-input">';
        echo '<option value="all">'  . esc_html__( 'All 12 Months', 'textcraft-tools' )          . '</option>';
        echo '<optgroup label="── ' . esc_html__( 'Seasons', 'textcraft-tools' )   . ' ──">';
        echo '<option value="spring">🌸 ' . esc_html__( 'Spring (Mar, Apr, May)', 'textcraft-tools' ) . '</option>';
        echo '<option value="summer">☀️ ' . esc_html__( 'Summer (Jun, Jul, Aug)', 'textcraft-tools' ) . '</option>';
        echo '<option value="autumn">🍂 ' . esc_html__( 'Autumn (Sep, Oct, Nov)', 'textcraft-tools' ) . '</option>';
        echo '<option value="winter">❄️ ' . esc_html__( 'Winter (Dec, Jan, Feb)', 'textcraft-tools' ) . '</option>';
        echo '</optgroup>';
        echo '<optgroup label="── ' . esc_html__( 'Quarters', 'textcraft-tools' )  . ' ──">';
        echo '<option value="q1">Q1 (' . esc_html__( 'Jan, Feb, Mar', 'textcraft-tools' ) . ')</option>';
        echo '<option value="q2">Q2 (' . esc_html__( 'Apr, May, Jun', 'textcraft-tools' ) . ')</option>';
        echo '<option value="q3">Q3 (' . esc_html__( 'Jul, Aug, Sep', 'textcraft-tools' ) . ')</option>';
        echo '<option value="q4">Q4 (' . esc_html__( 'Oct, Nov, Dec', 'textcraft-tools' ) . ')</option>';
        echo '</optgroup>';
        echo '<optgroup label="── ' . esc_html__( 'Half-Year', 'textcraft-tools' ) . ' ──">';
        echo '<option value="h1">H1 (' . esc_html__( 'Jan–Jun', 'textcraft-tools' ) . ')</option>';
        echo '<option value="h2">H2 (' . esc_html__( 'Jul–Dec', 'textcraft-tools' ) . ')</option>';
        echo '</optgroup>';
        echo '</select>';
        echo '</div>';

        echo '</div>'; // end row 1

        // Row 2: Output Format buttons
        echo '<div class="tc-mb-16">';
        echo '<label class="tc-label tc-d-block tc-mb-8">' . esc_html__( 'Output Format', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap" id="tc-rm-fmt-group">';
        $formats = [
            'long'         => __( 'Full Name | January',       'textcraft-tools' ),
            'short'        => __( 'Abbreviated | Jan',         'textcraft-tools' ),
            'number'       => __( 'Month Number | 01',         'textcraft-tools' ),
            'number-plain' => __( 'Number (no pad) | 1',       'textcraft-tools' ),
            'upper'        => __( 'UPPERCASE | JANUARY',       'textcraft-tools' ),
            'with-number'  => __( 'Name + Number | January (1)', 'textcraft-tools' ),
        ];
        $first = true;
        foreach ( $formats as $val => $label ) {
            $parts   = explode( '|', $label );
            $main    = trim( $parts[0] );
            $preview = isset( $parts[1] ) ? trim( $parts[1] ) : '';
            $active  = $first ? ' tc-btn-active' : '';
            $variant = $first ? 'primary' : 'secondary';
            echo '<button class="tc-btn tc-btn--' . esc_attr( $variant ) . ' tc-rm-fmt-btn' . $active . '" data-fmt="' . esc_attr( $val ) . '">';
            echo esc_html( $main );
            if ( $preview ) echo '<br><small>' . esc_html( $preview ) . '</small>';
            echo '</button>';
            $first = false;
        }
        echo '</div>';
        echo '</div>'; // end row 2

        // Row 3: Separator + Options
        echo '<div class="tc-grid-settings-sm">';

        echo '<div>';
        echo '<label class="tc-label tc-d-block tc-mb-8">' . esc_html__( 'Separator', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap" id="tc-rm-sep-group">';
        $seps = [
            'newline' => __( 'New Line', 'textcraft-tools' ),
            'comma'   => __( 'Comma',    'textcraft-tools' ),
            'space'   => __( 'Space',    'textcraft-tools' ),
            'pipe'    => __( 'Pipe ( | )', 'textcraft-tools' ),
        ];
        $first = true;
        foreach ( $seps as $val => $label ) {
            $active  = $first ? ' tc-btn-active' : '';
            $variant = $first ? 'primary' : 'secondary';
            echo '<button class="tc-btn tc-btn--' . esc_attr( $variant ) . ' tc-rm-sep-btn' . $active . '" data-sep="' . esc_attr( $val ) . '">' . esc_html( $label ) . '</button>';
            $first = false;
        }
        echo '</div>';
        echo '</div>';

        echo '<div class="tc-flex-col-end">';
        $this->render_options_row( [
            [ 'id' => 'tc-rm-nodup', 'label' => esc_html__( 'No duplicate months', 'textcraft-tools' ), 'checked' => true  ],
            [ 'id' => 'tc-rm-sort',  'label' => esc_html__( 'Sort chronologically', 'textcraft-tools' ), 'checked' => false ],
        ] );
        echo '</div>';

        echo '</div>'; // end row 3

        // Quick Presets
        echo '<div class="tc-mb-16">';
        echo '<label class="tc-label tc-d-block tc-mb-8">' . esc_html__( 'Quick Presets', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap">';
        $presets = [
            'one'     => __( 'Pick 1 Month',      'textcraft-tools' ),
            'quarter' => __( 'Random Quarter (3)', 'textcraft-tools' ),
            'half'    => __( 'Half Year (6)',       'textcraft-tools' ),
            'all12'   => __( 'All 12 Months',      'textcraft-tools' ),
        ];
        foreach ( $presets as $val => $label ) {
            echo '<button class="tc-btn tc-btn--secondary tc-rm-preset-btn" data-preset="' . esc_attr( $val ) . '">' . esc_html( $label ) . '</button>';
        }
        echo '</div>';
        echo '</div>'; // end presets

        // Action buttons
        $this->render_button_row( [
            [ 'id' => 'tc-rm-generate', 'label' => '📅 ' . esc_html__( 'Generate Months', 'textcraft-tools' ), 'variant' => 'primary' ],
            [ 'id' => 'tc-rm-copy',     'label' => '📋 ' . esc_html__( 'Copy Result',     'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-rm-clear',    'label' => '🗑️ ' . esc_html__( 'Clear',           'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        // Stats bar
        echo '<div class="tc-stats-bar tc-mt-16 tc-d-flex tc-items-center tc-gap-16">';
        echo '<div class="tc-stat-item"><span class="tc-stat-label">' . esc_html__( 'Generated', 'textcraft-tools' ) . '</span><span class="tc-stat-value" id="tc-rm-stat-count">0</span></div>';
        echo '<div class="tc-stat-sep"></div>';
        echo '<div class="tc-stat-item"><span class="tc-stat-label">' . esc_html__( 'Pool Size', 'textcraft-tools' ) . '</span><span class="tc-stat-value" id="tc-rm-stat-pool">12</span></div>';
        echo '<div class="tc-stat-sep"></div>';
        echo '<div class="tc-stat-item"><span class="tc-stat-label">' . esc_html__( 'Generations', 'textcraft-tools' ) . '</span><span class="tc-stat-value" id="tc-rm-stat-spins">0</span></div>';
        echo '</div>'; // end stats bar

        // Output textarea
        echo '<div class="tc-label-row tc-mt-20"><span class="tc-label">' . esc_html__( 'Generated Months', 'textcraft-tools' ) . '</span></div>';
        $this->render_textarea( 'tc-rm-output', '', esc_html__( 'Your random months will appear here. Filter by season or quarter, then click Generate Months.', 'textcraft-tools' ), 10, true );

        // Visual month grid
        echo '<div id="tc-rm-visual" class="tc-mt-20 tc-hidden">';
        echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'Visual Overview', 'textcraft-tools' ) . '</span></div>';
        echo '<div id="tc-rm-grid" class="tc-grid-12col"></div>';
        echo '</div>';

        // Inline JS
        $this->render_inline_script( <<<'JS'
(function () {
    var ALL_MONTHS = [
        { num:1,  long:'January',   short:'Jan' },
        { num:2,  long:'February',  short:'Feb' },
        { num:3,  long:'March',     short:'Mar' },
        { num:4,  long:'April',     short:'Apr' },
        { num:5,  long:'May',       short:'May' },
        { num:6,  long:'June',      short:'Jun' },
        { num:7,  long:'July',      short:'Jul' },
        { num:8,  long:'August',    short:'Aug' },
        { num:9,  long:'September', short:'Sep' },
        { num:10, long:'October',   short:'Oct' },
        { num:11, long:'November',  short:'Nov' },
        { num:12, long:'December',  short:'Dec' },
    ];

    var POOLS = {
        all:    [1,2,3,4,5,6,7,8,9,10,11,12],
        spring: [3,4,5],
        summer: [6,7,8],
        autumn: [9,10,11],
        winter: [12,1,2],
        q1:     [1,2,3],
        q2:     [4,5,6],
        q3:     [7,8,9],
        q4:     [10,11,12],
        h1:     [1,2,3,4,5,6],
        h2:     [7,8,9,10,11,12],
    };

    var outputFmt = 'long';
    var sepMode   = 'newline';
    var spins     = 0;

    var out      = document.getElementById('tc-rm-output');
    var countEl  = document.getElementById('tc-rm-count');
    var filterEl = document.getElementById('tc-rm-filter');
    var nodupEl  = document.getElementById('tc-rm-nodup');
    var sortEl   = document.getElementById('tc-rm-sort');
    var poolStat = document.getElementById('tc-rm-stat-pool');
    var cntStat  = document.getElementById('tc-rm-stat-count');
    var spinStat = document.getElementById('tc-rm-stat-spins');
    var visual   = document.getElementById('tc-rm-visual');
    var grid     = document.getElementById('tc-rm-grid');

    if (!out) return;

    // --- Button group helper ---
    function activateGroup(selector, clicked, dataKey, setter) {
        document.querySelectorAll(selector).forEach(function (b) {
            b.classList.remove('tc-btn-active', 'tc-btn--primary');
            b.classList.add('tc-btn--secondary');
        });
        clicked.classList.add('tc-btn-active', 'tc-btn--primary');
        clicked.classList.remove('tc-btn--secondary');
        setter(clicked.getAttribute('data-' + dataKey));
    }

    document.querySelectorAll('.tc-rm-fmt-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            activateGroup('.tc-rm-fmt-btn', btn, 'fmt', function (v) { outputFmt = v; });
        });
    });

    document.querySelectorAll('.tc-rm-sep-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            activateGroup('.tc-rm-sep-btn', btn, 'sep', function (v) { sepMode = v; });
        });
    });

    // Update pool stat on filter change
    filterEl.addEventListener('change', function () {
        var pool = POOLS[filterEl.value] || POOLS.all;
        poolStat.textContent = pool.length;
    });

    // Presets
    document.querySelectorAll('.tc-rm-preset-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var p = btn.getAttribute('data-preset');
            if (p === 'one')     { countEl.value = 1; }
            if (p === 'quarter') { countEl.value = 3; nodupEl.checked = true; }
            if (p === 'half')    { countEl.value = 6; nodupEl.checked = true; }
            if (p === 'all12')   {
                countEl.value = 12;
                nodupEl.checked = true;
                sortEl.checked  = true;
                filterEl.value  = 'all';
                poolStat.textContent = 12;
            }
        });
    });

    // Fisher-Yates shuffle
    function shuffle(arr) {
        var a = arr.slice();
        for (var i = a.length - 1; i > 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var tmp = a[i]; a[i] = a[j]; a[j] = tmp;
        }
        return a;
    }

    function formatMonth(m) {
        switch (outputFmt) {
            case 'short':        return m.short;
            case 'number':       return String(m.num).padStart(2, '0');
            case 'number-plain': return String(m.num);
            case 'upper':        return m.long.toUpperCase();
            case 'with-number':  return m.long + ' (' + m.num + ')';
            default:             return m.long;
        }
    }

    function getSep() {
        switch (sepMode) {
            case 'comma': return ', ';
            case 'space': return ' ';
            case 'pipe':  return ' | ';
            default:      return '\n';
        }
    }

    // Generate
    document.getElementById('tc-rm-generate').addEventListener('click', function () {
        var count     = Math.max(1, Math.min(1000, parseInt(countEl.value) || 3));
        var filterKey = filterEl.value;
        var nodup     = nodupEl.checked;
        var doSort    = sortEl.checked;
        var poolNums  = POOLS[filterKey] || POOLS.all;
        var pool      = poolNums.map(function (n) { return ALL_MONTHS[n - 1]; });

        if (nodup && count > pool.length) {
            out.value = '⚠️ Cannot generate ' + count + ' unique months from a pool of only ' + pool.length + '. Disable "No duplicate months" or widen your filter.';
            return;
        }

        var picked;
        if (nodup) {
            picked = shuffle(pool).slice(0, count);
        } else {
            picked = [];
            for (var i = 0; i < count; i++) {
                picked.push(pool[Math.floor(Math.random() * pool.length)]);
            }
        }

        if (doSort) picked.sort(function (a, b) { return a.num - b.num; });

        out.value = picked.map(formatMonth).join(getSep());

        spins++;
        cntStat.textContent  = picked.length;
        poolStat.textContent = pool.length;
        spinStat.textContent = spins;

        // Visual grid
        var pickedNums = {};
        picked.forEach(function (m) { pickedNums[m.num] = true; });

        grid.innerHTML = ALL_MONTHS.map(function (m) {
            var inPool     = poolNums.indexOf(m.num) !== -1;
            var isSelected = !!pickedNums[m.num];
            var bg      = isSelected ? 'var(--tc-accent)'        : inPool ? 'var(--tc-surface-2)' : 'var(--tc-surface-1)';
            var color   = isSelected ? '#fff'                      : inPool ? 'var(--tc-text-primary)' : 'var(--tc-text-secondary)';
            var border  = isSelected ? 'var(--tc-accent)'        : 'var(--tc-border)';
            var opacity = inPool ? '1' : '0.45';
            var subClr  = isSelected ? 'rgba(255,255,255,0.8)'    : 'var(--tc-text-secondary)';
            return '<div class="tc-text-center" style="background:' + bg + ';border:1.5px solid ' + border + ';border-radius:var(--tc-radius-md);padding:8px 4px;opacity:' + opacity + ';transition:all .2s;">'
                 + '<div class="tc-text-11 tc-font-bold" style="color:' + color + ';">' + m.short + '</div>'
                 + '<div class="tc-text-10" style="color:' + subClr + ';">' + m.num + '</div>'
                 + '</div>';
        }).join('');
        visual.style.display = 'block';
    });

    // Copy
    document.getElementById('tc-rm-copy').addEventListener('click', function () {
        if (!out.value) return;
        var btn = document.getElementById('tc-rm-copy');
        navigator.clipboard.writeText(out.value).then(function () {
            btn.textContent = '✅ Copied!';
            setTimeout(function () { btn.textContent = '📋 Copy Result'; }, 2000);
        });
    });

    // Clear
    document.getElementById('tc-rm-clear').addEventListener('click', function () {
        out.value = '';
        visual.style.display = 'none';
        cntStat.textContent = '0';
    });
})();
JS
        );
    }
}