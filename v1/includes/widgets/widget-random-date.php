<?php
/**
 * Widget: Random Date Generator (DateForge)
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;
class Widget_Random_Date extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_random_date'; }
    public function get_title(): string { return esc_html__( 'Random Date Generator', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-date'; }
    protected function render_tool_content( array $settings ): void {
        echo '<p class="tc-text-14 tc-text-muted tc-mb-20">' . esc_html__( 'Generate random dates between any two dates. Perfect for testing date pickers, populating sample data, or creating random date entries — all processed securely in your browser.', 'textcraft-tools' ) . '</p>';

        // ── Date range + Count + Format grid ─────────────────────────────
        echo '<div class="tc-grid-settings">';

        // Start date
        echo '<div>';
        echo '<label class="tc-label-upper tc-mb-8">' . esc_html__( 'Start Date', 'textcraft-tools' ) . '</label>';
        echo '<input type="date" id="tc-rd-start" value="1970-01-01" class="tc-input-md">';
        echo '</div>';

        // End date
        echo '<div>';
        echo '<label class="tc-label-upper tc-mb-8">' . esc_html__( 'End Date', 'textcraft-tools' ) . '</label>';
        echo '<input type="date" id="tc-rd-end" class="tc-input-md">';
        echo '</div>';

        // Count
        echo '<div>';
        echo '<label class="tc-label-upper tc-mb-8">' . esc_html__( 'How Many Dates', 'textcraft-tools' ) . '</label>';
        echo '<input type="number" id="tc-rd-count" value="10" min="1" max="1000" class="tc-input-md tc-font-bold">';
        echo '</div>';

        // Format select
        echo '<div>';
        echo '<label class="tc-label-upper tc-mb-8">' . esc_html__( 'Output Format', 'textcraft-tools' ) . '</label>';
        echo '<select id="tc-rd-format" class="tc-text-14 tc-input-md tc-cursor-pointer tc-p-12">';
        $formats = [
            'YYYY-MM-DD'   => 'YYYY-MM-DD (ISO 8601)',
            'MM/DD/YYYY'   => 'MM/DD/YYYY (US)',
            'DD/MM/YYYY'   => 'DD/MM/YYYY (UK/EU)',
            'DD-MM-YYYY'   => 'DD-MM-YYYY',
            'MMMM D, YYYY' => 'Month D, YYYY (Long)',
            'D MMM YYYY'   => 'D Mon YYYY (Short)',
            'MM-YYYY'      => 'MM-YYYY (Month only)',
            'UNIX'         => 'Unix Timestamp',
        ];
        foreach ( $formats as $val => $label ) {
            echo '<option value="' . esc_attr( $val ) . '">' . esc_html( $label ) . '</option>';
        }
        echo '</select>';
        echo '</div>';

        echo '</div>'; // end grid

        // ── Options row ───────────────────────────────────────────────────
        echo '<div class="tc-rd-options-bar">';
        $checkboxes = [
            [ 'id' => 'tc-rd-unique',   'label' => esc_html__( 'No duplicate dates',          'textcraft-tools' ), 'checked' => true  ],
            [ 'id' => 'tc-rd-sort',     'label' => esc_html__( 'Sort chronologically',         'textcraft-tools' ), 'checked' => false ],
            [ 'id' => 'tc-rd-weekdays', 'label' => esc_html__( 'Weekdays only (Mon–Fri)',      'textcraft-tools' ), 'checked' => false ],
        ];
        foreach ( $checkboxes as $cb ) {
echo '<label class="tc-text-13 tc-flex-check-sm">';
echo '<input type="checkbox" id="' . esc_attr( $cb['id'] ) . '"' . ( $cb['checked'] ? ' checked' : '' ) . ' class="tc-checkbox"> ';
			echo $cb['label'];
			echo '</label>';
        }
        echo '</div>';

        // ── Quick presets ─────────────────────────────────────────────────
        echo '<div class="tc-mb-20">';
        echo '<p class="tc-section-label tc-m-0 tc-mb-8">' . esc_html__( 'Quick Presets', 'textcraft-tools' ) . '</p>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap">';
        $presets = [
            'thisyear' => esc_html__( 'This Year',     'textcraft-tools' ),
            'lastyear' => esc_html__( 'Last Year',     'textcraft-tools' ),
            'decade'   => esc_html__( 'Last 10 Years', 'textcraft-tools' ),
            'century'  => esc_html__( '20th Century',  'textcraft-tools' ),
            'future'   => esc_html__( 'Next 5 Years',  'textcraft-tools' ),
        ];
        foreach ( $presets as $key => $label ) {
            echo '<button class="tc-btn tc-btn--ghost tc-rd-preset" data-preset="' . esc_attr( $key ) . '">' . $label . '</button>';
        }
        echo '</div></div>';

        // ── Action buttons ────────────────────────────────────────────────
        $this->render_button_row( [
            [ 'id' => 'tc-rd-generate', 'label' => '📅 ' . esc_html__( 'Generate Dates', 'textcraft-tools' ), 'variant' => 'primary' ],
            [ 'id' => 'tc-rd-copy',     'label' => '📋 ' . esc_html__( 'Copy All',       'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-rd-clear',    'label' => '🗑️ ' . esc_html__( 'Clear',          'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        // ── Stats bar ─────────────────────────────────────────────────────
        $this->render_stat_bar( [
            [ 'id' => 'tc-rd-stat-gen',      'label' => esc_html__( 'Generated', 'textcraft-tools' ) ],
            [ 'id' => 'tc-rd-stat-earliest', 'label' => esc_html__( 'Earliest',  'textcraft-tools' ) ],
            [ 'id' => 'tc-rd-stat-latest',   'label' => esc_html__( 'Latest',    'textcraft-tools' ) ],
        ] );

        // ── Output textarea ───────────────────────────────────────────────
        echo '<div class="tc-label-row tc-mt-20"><span class="tc-label">' . esc_html__( 'Generated Dates', 'textcraft-tools' ) . '</span></div>';
        $this->render_textarea( 'tc-rd-output', '', esc_html__( 'Your random dates will appear here. Select a date range and format, then click Generate Dates.', 'textcraft-tools' ), 10, true );

        // ── Inline JS ─────────────────────────────────────────────────────
        $this->render_inline_script( <<<'JS'
(function(){
    var out = document.getElementById('tc-rd-output');
    if (!out) return;

    // Set default end date to today
    var todayISO = new Date().toISOString().slice(0, 10);
    document.getElementById('tc-rd-end').value = todayISO;

    var thisYear = new Date().getFullYear();

    // ── Presets ───────────────────────────────────────────────────────
    var PRESETS = {
        thisyear: [thisYear + '-01-01',    thisYear + '-12-31'],
        lastyear: [(thisYear-1) + '-01-01',(thisYear-1) + '-12-31'],
        decade:   [(thisYear-10) + '-01-01', todayISO],
        century:  ['1900-01-01', '1999-12-31'],
        future:   [todayISO,    (thisYear+5) + '-12-31'],
    };
    document.querySelectorAll('.tc-rd-preset').forEach(function(btn){
        btn.addEventListener('click', function(){
            var p = PRESETS[btn.dataset.preset];
            document.getElementById('tc-rd-start').value = p[0];
            document.getElementById('tc-rd-end').value   = p[1];
        });
    });

    // ── Format helpers ────────────────────────────────────────────────
    var MONTHS_LONG  = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    var MONTHS_SHORT = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    function formatDate(d, fmt) {
        var yyyy = d.getFullYear();
        var mm   = String(d.getMonth() + 1).padStart(2, '0');
        var dd   = String(d.getDate()).padStart(2, '0');
        var m    = d.getMonth();
        var day  = d.getDate();
        switch (fmt) {
            case 'YYYY-MM-DD':    return yyyy + '-' + mm + '-' + dd;
            case 'MM/DD/YYYY':    return mm + '/' + dd + '/' + yyyy;
            case 'DD/MM/YYYY':    return dd + '/' + mm + '/' + yyyy;
            case 'DD-MM-YYYY':    return dd + '-' + mm + '-' + yyyy;
            case 'MMMM D, YYYY':  return MONTHS_LONG[m] + ' ' + day + ', ' + yyyy;
            case 'D MMM YYYY':    return day + ' ' + MONTHS_SHORT[m] + ' ' + yyyy;
            case 'MM-YYYY':       return mm + '-' + yyyy;
            case 'UNIX':          return String(Math.floor(d.getTime() / 1000));
            default:              return yyyy + '-' + mm + '-' + dd;
        }
    }

    function isWeekday(d) { var day = d.getDay(); return day !== 0 && day !== 6; }

    // ── Generate ──────────────────────────────────────────────────────
    document.getElementById('tc-rd-generate').addEventListener('click', function(){
        var startStr = document.getElementById('tc-rd-start').value;
        var endStr   = document.getElementById('tc-rd-end').value;
        var count    = Math.max(1, Math.min(1000, parseInt(document.getElementById('tc-rd-count').value) || 10));
        var fmt      = document.getElementById('tc-rd-format').value;
        var unique   = document.getElementById('tc-rd-unique').checked;
        var sortIt   = document.getElementById('tc-rd-sort').checked;
        var wdOnly   = document.getElementById('tc-rd-weekdays').checked;

        if (!startStr || !endStr) { out.value = '⚠️ Please set both a start and end date.'; return; }
        var startTs = new Date(startStr).getTime();
        var endTs   = new Date(endStr).getTime();
        if (startTs > endTs) { out.value = '⚠️ Start date must be before end date.'; return; }

        var msPerDay   = 86400000;
        var totalDays  = Math.floor((endTs - startTs) / msPerDay) + 1;
        var generated  = [];
        var usedTs     = new Set ? new Set() : {};
        var hasSet     = typeof Set !== 'undefined';
        var attempts   = 0;
        var maxAttempts = count * 50;

        while (generated.length < count && attempts < maxAttempts) {
            attempts++;
            var randMs = startTs + Math.floor(Math.random() * totalDays) * msPerDay;
            if (unique && (hasSet ? usedTs.has(randMs) : usedTs[randMs])) continue;
            var d = new Date(randMs);
            if (wdOnly && !isWeekday(d)) continue;
            if (hasSet) usedTs.add(randMs); else usedTs[randMs] = true;
            generated.push({ ts: randMs, d: d });
        }

        if (!generated.length) {
            out.value = '⚠️ Could not generate dates with the current settings. Try widening the range or disabling "Weekdays only".';
            return;
        }

        if (sortIt) generated.sort(function(a, b){ return a.ts - b.ts; });

        out.value = generated.map(function(g){ return formatDate(g.d, fmt); }).join('\n');

        document.getElementById('tc-rd-stat-gen').textContent = generated.length;

        var sorted = generated.slice().sort(function(a,b){ return a.ts - b.ts; });
        document.getElementById('tc-rd-stat-earliest').textContent = formatDate(sorted[0].d, 'YYYY-MM-DD');
        document.getElementById('tc-rd-stat-latest').textContent   = formatDate(sorted[sorted.length - 1].d, 'YYYY-MM-DD');
    });

    // ── Copy ──────────────────────────────────────────────────────────
    document.getElementById('tc-rd-copy').addEventListener('click', function(){
        if (!out.value) return;
        navigator.clipboard.writeText(out.value).then(function(){
            var btn = document.getElementById('tc-rd-copy');
            btn.textContent = '✅ Copied!';
            setTimeout(function(){ btn.textContent = '📋 Copy All'; }, 2000);
        });
    });

    // ── Clear ─────────────────────────────────────────────────────────
    document.getElementById('tc-rd-clear').addEventListener('click', function(){
        out.value = '';
        document.getElementById('tc-rd-stat-gen').textContent      = '0';
        document.getElementById('tc-rd-stat-earliest').textContent = '—';
        document.getElementById('tc-rd-stat-latest').textContent   = '—';
    });
})();
JS
        );
    }
}