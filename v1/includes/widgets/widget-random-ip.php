<?php
/**
 * Widget: Random IP Generator (IPForge)
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;
class Widget_Random_Ip extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_random_ip'; }
    public function get_title(): string { return esc_html__( 'Random IP Generator', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-globe'; }
    protected function render_tool_content( array $settings ): void {
        echo '<p class="tc-text-14 tc-text-muted tc-mb-20">' . esc_html__( 'Generate random IPv4 and IPv6 addresses instantly. Choose from public IPs, private ranges, or custom CIDR notation — all generated locally in your browser with no server requests.', 'textcraft-tools' ) . '</p>';

        // ── IP Version + Count ────────────────────────────────────────────
        echo '<div class="tc-grid-settings">';

        // IP Version buttons
        echo '<div>';
        echo '<label class="tc-label-upper tc-mb-8">' . esc_html__( 'IP Version', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-6">';
        $versions = [
            [ 'ver' => 'v4',  'label' => 'IPv4',   'active' => true  ],
            [ 'ver' => 'v6',  'label' => 'IPv6',   'active' => false ],
            [ 'ver' => 'mix', 'label' => 'Mixed',  'active' => false ],
        ];
        foreach ( $versions as $v ) {
            $cls = $v['active'] ? 'tc-btn tc-btn--primary tc-ri-ver active' : 'tc-btn tc-btn--ghost tc-ri-ver';
            echo '<button class="' . esc_attr( $cls ) . ' tc-flex-1" data-ver="' . esc_attr( $v['ver'] ) . '">' . esc_html( $v['label'] ) . '</button>';
        }
        echo '</div></div>';

        // Count
        echo '<div>';
        echo '<label class="tc-label-upper tc-mb-8">' . esc_html__( 'How Many IPs', 'textcraft-tools' ) . '</label>';
        echo '<input type="number" id="tc-ri-count" value="20" min="1" max="1000" class="tc-input-md tc-font-bold">';
        echo '</div>';

        echo '</div>'; // end grid

        // ── IPv4 Options ──────────────────────────────────────────────────
        echo '<div id="tc-ri-v4-options">';
        echo '<p class="tc-section-label tc-m-0 tc-mb-8">' . esc_html__( 'IPv4 Range / Class', 'textcraft-tools' ) . '</p>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap tc-mb-14">';
        $ranges = [
            [ 'range' => 'any',       'label' => 'Any Public',                                          'active' => true  ],
            [ 'range' => 'private-a', 'label' => 'Class A Private<br><small>10.x.x.x</small>',         'active' => false ],
            [ 'range' => 'private-b', 'label' => 'Class B Private<br><small>172.16–31.x.x</small>',    'active' => false ],
            [ 'range' => 'private-c', 'label' => 'Class C Private<br><small>192.168.x.x</small>',      'active' => false ],
            [ 'range' => 'loopback',  'label' => 'Loopback<br><small>127.x.x.x</small>',               'active' => false ],
            [ 'range' => 'custom',    'label' => 'Custom CIDR',                                         'active' => false ],
        ];
        foreach ( $ranges as $r ) {
            $cls = $r['active'] ? 'tc-btn tc-btn--primary tc-ri-range active' : 'tc-btn tc-btn--ghost tc-ri-range';
            echo '<button class="' . esc_attr( $cls ) . '" data-range="' . esc_attr( $r['range'] ) . '">' . $r['label'] . '</button>';
        }
        echo '</div>';
        // Custom CIDR input
        echo '<div id="tc-ri-cidr-row" class="tc-hidden tc-mb-14">';
        echo '<label class="tc-section-label tc-mb-6">' . esc_html__( 'Custom CIDR Notation', 'textcraft-tools' ) . '</label>';
        echo '<input type="text" id="tc-ri-cidr" placeholder="' . esc_attr__( 'e.g. 203.0.113.0/24', 'textcraft-tools' ) . '" value="203.0.113.0/24" class="tc-text-14 tc-input-md">';
        echo '</div>';
        echo '</div>'; // end v4-options

        // ── IPv6 Options ──────────────────────────────────────────────────
        echo '<div id="tc-ri-v6-options" class="tc-hidden">';
        echo '<p class="tc-section-label tc-m-0 tc-mb-8">' . esc_html__( 'IPv6 Format', 'textcraft-tools' ) . '</p>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap tc-mb-14">';
        $v6fmts = [
            [ 'v6' => 'full',       'label' => esc_html__( 'Full (8 groups)',   'textcraft-tools' ), 'active' => true  ],
            [ 'v6' => 'compressed', 'label' => esc_html__( 'Compressed (::)',   'textcraft-tools' ), 'active' => false ],
            [ 'v6' => 'ula',        'label' => esc_html__( 'ULA (fc/fd)',        'textcraft-tools' ), 'active' => false ],
            [ 'v6' => 'link',       'label' => esc_html__( 'Link-local (fe80::)', 'textcraft-tools' ), 'active' => false ],
        ];
        foreach ( $v6fmts as $f ) {
            $cls = $f['active'] ? 'tc-btn tc-btn--primary tc-ri-v6fmt active' : 'tc-btn tc-btn--ghost tc-ri-v6fmt';
            echo '<button class="' . esc_attr( $cls ) . '" data-v6="' . esc_attr( $f['v6'] ) . '">' . $f['label'] . '</button>';
        }
        echo '</div>';
        echo '</div>'; // end v6-options

        // ── Separator + Extra options ─────────────────────────────────────
        echo '<div class="tc-grid-settings">';

        // Separator buttons
        echo '<div>';
        echo '<label class="tc-label-upper tc-mb-8">' . esc_html__( 'Separator', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap">';
        $seps = [
            [ 'sep' => 'newline', 'label' => esc_html__( 'New Line',    'textcraft-tools' ), 'active' => true  ],
            [ 'sep' => 'comma',   'label' => esc_html__( 'Comma',       'textcraft-tools' ), 'active' => false ],
            [ 'sep' => 'space',   'label' => esc_html__( 'Space',       'textcraft-tools' ), 'active' => false ],
            [ 'sep' => 'json',    'label' => esc_html__( 'JSON Array',  'textcraft-tools' ), 'active' => false ],
        ];
        foreach ( $seps as $s ) {
            $cls = $s['active'] ? 'tc-btn tc-btn--primary tc-ri-sep active' : 'tc-btn tc-btn--ghost tc-ri-sep';
            echo '<button class="' . esc_attr( $cls ) . '" data-sep="' . esc_attr( $s['sep'] ) . '">' . $s['label'] . '</button>';
        }
        echo '</div></div>';

        // Checkboxes
        echo '<div class="tc-flex-col-end">';
echo '<label class="tc-text-13 tc-flex-check-sm">';
echo '<input type="checkbox" id="tc-ri-unique" checked class="tc-checkbox"> ' . esc_html__( 'No duplicate IPs', 'textcraft-tools' );
echo '</label>';
echo '<label class="tc-text-13 tc-flex-check-sm">';
        echo '<input type="checkbox" id="tc-ri-port" class="tc-checkbox"> ' . esc_html__( 'Include random port', 'textcraft-tools' );
        echo '</label>';
        echo '</div>';

        echo '</div>'; // end grid

        // ── Action buttons ────────────────────────────────────────────────
        $this->render_button_row( [
            [ 'id' => 'tc-ri-generate', 'label' => '⚡ ' . esc_html__( 'Generate IPs', 'textcraft-tools' ), 'variant' => 'primary' ],
            [ 'id' => 'tc-ri-copy',     'label' => '📋 ' . esc_html__( 'Copy All',     'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-ri-clear',    'label' => '🗑️ ' . esc_html__( 'Clear',        'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        // ── Stats bar ─────────────────────────────────────────────────────
        $this->render_stat_bar( [
            [ 'id' => 'tc-ri-stat-gen', 'label' => esc_html__( 'Generated', 'textcraft-tools' ) ],
            [ 'id' => 'tc-ri-stat-v4',  'label' => esc_html__( 'IPv4',      'textcraft-tools' ) ],
            [ 'id' => 'tc-ri-stat-v6',  'label' => esc_html__( 'IPv6',      'textcraft-tools' ) ],
        ] );

        // ── Output textarea ───────────────────────────────────────────────
        echo '<div class="tc-label-row tc-mt-20"><span class="tc-label">' . esc_html__( 'Generated IP Addresses', 'textcraft-tools' ) . '</span></div>';
        $this->render_textarea( 'tc-ri-output', '', esc_html__( 'Your random IP addresses will appear here. Choose IPv4, IPv6, or Mixed mode and click Generate IPs.', 'textcraft-tools' ), 10, true );

        // ── Inline JS ─────────────────────────────────────────────────────
        $this->render_inline_script( <<<'JS'
(function(){
    var out = document.getElementById('tc-ri-output');
    if (!out) return;

    var ipVersion = 'v4';
    var rangeMode = 'any';
    var v6Format  = 'full';
    var sepMode   = 'newline';

    // ── Version buttons ───────────────────────────────────────────────
    document.querySelectorAll('.tc-ri-ver').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('.tc-ri-ver').forEach(function(b){ b.classList.remove('active','tc-btn--primary'); b.classList.add('tc-btn--ghost'); });
            btn.classList.add('active','tc-btn--primary'); btn.classList.remove('tc-btn--ghost');
            ipVersion = btn.dataset.ver;
            document.getElementById('tc-ri-v4-options').style.display = ipVersion === 'v6'  ? 'none'  : 'block';
            document.getElementById('tc-ri-v6-options').style.display = ipVersion === 'v4'  ? 'none'  : 'block';
        });
    });

    // ── Range buttons ─────────────────────────────────────────────────
    document.querySelectorAll('.tc-ri-range').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('.tc-ri-range').forEach(function(b){ b.classList.remove('active','tc-btn--primary'); b.classList.add('tc-btn--ghost'); });
            btn.classList.add('active','tc-btn--primary'); btn.classList.remove('tc-btn--ghost');
            rangeMode = btn.dataset.range;
            document.getElementById('tc-ri-cidr-row').style.display = rangeMode === 'custom' ? 'block' : 'none';
        });
    });

    // ── IPv6 format buttons ───────────────────────────────────────────
    document.querySelectorAll('.tc-ri-v6fmt').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('.tc-ri-v6fmt').forEach(function(b){ b.classList.remove('active','tc-btn--primary'); b.classList.add('tc-btn--ghost'); });
            btn.classList.add('active','tc-btn--primary'); btn.classList.remove('tc-btn--ghost');
            v6Format = btn.dataset.v6;
        });
    });

    // ── Separator buttons ─────────────────────────────────────────────
    document.querySelectorAll('.tc-ri-sep').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('.tc-ri-sep').forEach(function(b){ b.classList.remove('active','tc-btn--primary'); b.classList.add('tc-btn--ghost'); });
            btn.classList.add('active','tc-btn--primary'); btn.classList.remove('tc-btn--ghost');
            sepMode = btn.dataset.sep;
        });
    });

    // ── IPv4 generators ───────────────────────────────────────────────
    function rand(max) { return Math.floor(Math.random() * (max + 1)); }

    function genPublicIPv4() {
        var a, b, c, d;
        var reserved = [0,10,127,169,172,192,198,203,240];
        var tries = 0;
        do {
            a = rand(223); tries++;
        } while (tries < 100 && (reserved.indexOf(a) !== -1 || a > 223));
        b = rand(255); c = rand(255); d = rand(254) + 1;
        return a + '.' + b + '.' + c + '.' + d;
    }

    function genFromCIDR(cidr) {
        try {
            var parts  = cidr.split('/');
            var base   = parts[0];
            var prefix = parseInt(parts[1]);
            if (isNaN(prefix) || prefix < 0 || prefix > 32) return genPublicIPv4();
            var octets = base.split('.').map(Number);
            if (octets.length !== 4 || octets.some(function(p){ return isNaN(p) || p < 0 || p > 255; })) return genPublicIPv4();
            var baseInt = ((octets[0]<<24)|(octets[1]<<16)|(octets[2]<<8)|octets[3]) >>> 0;
            var size    = prefix === 32 ? 1 : Math.pow(2, 32 - prefix);
            var mask    = prefix === 0 ? 0 : (0xffffffff << (32 - prefix)) >>> 0;
            var netInt  = (baseInt & mask) >>> 0;
            var offset  = Math.floor(Math.random() * size);
            var ipInt   = (netInt + offset) >>> 0;
            return [(ipInt>>>24)&255,(ipInt>>>16)&255,(ipInt>>>8)&255,ipInt&255].join('.');
        } catch(e) { return genPublicIPv4(); }
    }

    function genIPv4ByRange(mode) {
        switch (mode) {
            case 'private-a': return '10.' + rand(255) + '.' + rand(255) + '.' + (rand(254)+1);
            case 'private-b': return '172.' + (rand(15)+16) + '.' + rand(255) + '.' + (rand(254)+1);
            case 'private-c': return '192.168.' + rand(255) + '.' + (rand(254)+1);
            case 'loopback':  return '127.' + rand(255) + '.' + rand(255) + '.' + (rand(254)+1);
            case 'custom':    return genFromCIDR(document.getElementById('tc-ri-cidr').value.trim());
            default:          return genPublicIPv4();
        }
    }

    // ── IPv6 generators ───────────────────────────────────────────────
    function randHex4() { return Math.floor(Math.random() * 0x10000).toString(16).padStart(4,'0'); }

    function genFullIPv6() {
        var g = [];
        for (var i = 0; i < 8; i++) g.push(randHex4());
        return g.join(':');
    }

    function genCompressedIPv6() {
        var groups = [];
        for (var i = 0; i < 8; i++) groups.push(Math.floor(Math.random() * 0x10000));
        var zStart = Math.floor(Math.random() * 6);
        var zLen   = Math.floor(Math.random() * 4) + 1;
        for (var j = zStart; j < Math.min(zStart + zLen, 8); j++) groups[j] = 0;
        var left  = groups.slice(0, zStart).map(function(g){ return g.toString(16); }).join(':');
        var right = groups.slice(zStart + zLen).map(function(g){ return g.toString(16); }).join(':');
        return (left ? left : '') + '::' + (right ? right : '');
    }

    function genULAIPv6() {
        var prefix = Math.random() > 0.5 ? 'fc' : 'fd';
        var rest   = [];
        for (var i = 0; i < 7; i++) rest.push(randHex4());
        return prefix + randHex4().slice(2) + ':' + rest.join(':');
    }

    function genLinkLocalIPv6() {
        var g = [];
        for (var i = 0; i < 4; i++) g.push(randHex4());
        return 'fe80::' + g.join(':');
    }

    function genIPv6(fmt) {
        switch (fmt) {
            case 'compressed': return genCompressedIPv6();
            case 'ula':        return genULAIPv6();
            case 'link':       return genLinkLocalIPv6();
            default:           return genFullIPv6();
        }
    }

    // ── Generate ──────────────────────────────────────────────────────
    document.getElementById('tc-ri-generate').addEventListener('click', function(){
        var count    = Math.max(1, Math.min(1000, parseInt(document.getElementById('tc-ri-count').value) || 20));
        var unique   = document.getElementById('tc-ri-unique').checked;
        var withPort = document.getElementById('tc-ri-port').checked;
        var results  = [];
        var seen     = {};
        var v4cnt = 0, v6cnt = 0;
        var attempts = 0, maxAttempts = count * 20;

        while (results.length < count && attempts < maxAttempts) {
            attempts++;
            var useV6;
            if (ipVersion === 'mix') useV6 = Math.random() > 0.5;
            else useV6 = ipVersion === 'v6';

            var ip = useV6 ? genIPv6(v6Format) : genIPv4ByRange(rangeMode);
            if (withPort) {
                var port = Math.floor(Math.random() * 65535) + 1;
                ip = useV6 ? '[' + ip + ']:' + port : ip + ':' + port;
            }
            if (unique && seen[ip]) continue;
            seen[ip] = true;
            results.push(ip);
            if (useV6) v6cnt++; else v4cnt++;
        }

        var output;
        switch (sepMode) {
            case 'comma':  output = results.join(', '); break;
            case 'space':  output = results.join(' ');  break;
            case 'json':   output = JSON.stringify(results, null, 2); break;
            default:       output = results.join('\n');
        }

        out.value = output;
        document.getElementById('tc-ri-stat-gen').textContent = results.length;
        document.getElementById('tc-ri-stat-v4').textContent  = v4cnt;
        document.getElementById('tc-ri-stat-v6').textContent  = v6cnt;
    });

    // ── Copy ──────────────────────────────────────────────────────────
    document.getElementById('tc-ri-copy').addEventListener('click', function(){
        if (!out.value) return;
        navigator.clipboard.writeText(out.value).then(function(){
            var btn = document.getElementById('tc-ri-copy');
            btn.textContent = '✅ Copied!';
            setTimeout(function(){ btn.textContent = '📋 Copy All'; }, 2000);
        });
    });

    // ── Clear ─────────────────────────────────────────────────────────
    document.getElementById('tc-ri-clear').addEventListener('click', function(){
        out.value = '';
        ['tc-ri-stat-gen','tc-ri-stat-v4','tc-ri-stat-v6'].forEach(function(id){
            document.getElementById(id).textContent = '0';
        });
    });
})();
JS
        );
    }
}