<?php
/**
 * Widget: UUID Generator
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;

class Widget_Uuid_Generator extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_uuid_generator'; }
    public function get_title(): string { return esc_html__( 'UUID Generator', 'textcraft-tools' ); }

    public function get_keywords(): array {
        return [ 'uuid generator', 'generate uuid', 'ulid generator', 'nanoid generator', 'free online developer tool' ];
    }
    public function get_icon(): string  { return 'eicon-shortcode'; }
    protected function render_tool_content( array $settings ): void {

        // Row 1: Version buttons + Count
        echo '<div class="tc-grid-settings-sm">';

        echo '<div>';
		echo '<label class="tc-label tc-d-block tc-mb-8">' . esc_html__( 'UUID Version', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap" id="tc-uu-ver-group">';
        $versions = [
            'v1'     => [ 'label' => 'v1',    'sub' => __( 'Time-based',  'textcraft-tools' ) ],
            'v4'     => [ 'label' => 'v4',    'sub' => __( 'Random',      'textcraft-tools' ) ],
            'v5'     => [ 'label' => 'v5',    'sub' => __( 'Name-based',  'textcraft-tools' ) ],
            'ulid'   => [ 'label' => 'ULID',  'sub' => __( 'Sortable',    'textcraft-tools' ) ],
            'nanoid' => [ 'label' => 'NanoID','sub' => __( 'Short',       'textcraft-tools' ) ],
        ];
        foreach ( $versions as $val => $info ) {
            $active  = $val === 'v4' ? ' tc-btn-active' : '';
            $variant = $val === 'v4' ? 'primary' : 'secondary';
            echo '<button class="tc-btn tc-btn--' . esc_attr( $variant ) . ' tc-uu-ver-btn' . $active . '" data-ver="' . esc_attr( $val ) . '">';
            echo esc_html( $info['label'] );
            echo '<br><small>' . esc_html( $info['sub'] ) . '</small>';
            echo '</button>';
        }
        echo '</div>';
        echo '</div>';

        echo '<div>';
		echo '<label class="tc-label tc-d-block tc-mb-8">' . esc_html__( 'Number to Generate', 'textcraft-tools' ) . '</label>';
        echo '<input type="number" id="tc-uu-count" class="tc-text-input" value="10" min="1" max="1000">';
        echo '</div>';

        echo '</div>'; // end row 1

        // v5 Options panel
        echo '<div id="tc-uu-v5-options" class="tc-card-surface tc-p-16 tc-mb-16 tc-hidden">';
        echo '<p class="tc-label tc-m-0-10">' . esc_html__( 'v5 Settings — Name-based (SHA-1)', 'textcraft-tools' ) . '</p>';
        echo '<div class="tc-grid-2col-12">';
        echo '<div>';
		echo '<label class="tc-label tc-d-block tc-mb-6">' . esc_html__( 'Namespace', 'textcraft-tools' ) . '</label>';
        echo '<select id="tc-uu-v5-namespace" class="tc-text-input">';
        echo '<option value="dns">DNS — 6ba7b810-9dad-11d1-80b4-00c04fd430c8</option>';
        echo '<option value="url">URL — 6ba7b811-9dad-11d1-80b4-00c04fd430c8</option>';
        echo '<option value="oid">OID — 6ba7b812-9dad-11d1-80b4-00c04fd430c8</option>';
        echo '<option value="x500">X.500 — 6ba7b814-9dad-11d1-80b4-00c04fd430c8</option>';
        echo '</select>';
        echo '</div>';
        echo '<div>';
		echo '<label class="tc-label tc-d-block tc-mb-6">' . esc_html__( 'Name (one per line for bulk)', 'textcraft-tools' ) . '</label>';
        echo '<textarea id="tc-uu-v5-name" rows="3" class="tc-text-input" placeholder="example.com&#10;my-app&#10;user-123"></textarea>';
        echo '</div>';
        echo '</div>';
        echo '</div>'; // end v5 options

        // NanoID Options panel
        echo '<div id="tc-uu-nanoid-options" class="tc-card-surface tc-p-16 tc-mb-16 tc-hidden">';
        echo '<p class="tc-label tc-m-0-10">' . esc_html__( 'NanoID Settings', 'textcraft-tools' ) . '</p>';
        echo '<div class="tc-grid-2col-12">';
        echo '<div>';
		echo '<label class="tc-label tc-d-block tc-mb-6">' . esc_html__( 'Length', 'textcraft-tools' ) . '</label>';
        echo '<input type="number" id="tc-uu-nanoid-length" class="tc-text-input" value="21" min="4" max="64">';
        echo '</div>';
        echo '<div>';
		echo '<label class="tc-label tc-d-block tc-mb-6">' . esc_html__( 'Alphabet', 'textcraft-tools' ) . '</label>';
        echo '<select id="tc-uu-nanoid-alphabet" class="tc-text-input">';
        echo '<option value="default">'      . esc_html__( 'Default (A–Z a–z 0–9 _-)', 'textcraft-tools' ) . '</option>';
        echo '<option value="alphanumeric">' . esc_html__( 'Alphanumeric only',         'textcraft-tools' ) . '</option>';
        echo '<option value="lowercase">'    . esc_html__( 'Lowercase + numbers',       'textcraft-tools' ) . '</option>';
        echo '<option value="numbers">'      . esc_html__( 'Numbers only',              'textcraft-tools' ) . '</option>';
        echo '<option value="hex">'          . esc_html__( 'Hex (0–9 a–f)',             'textcraft-tools' ) . '</option>';
        echo '</select>';
        echo '</div>';
        echo '</div>';
        echo '</div>'; // end nanoid options

        // Row 2: Output Format buttons
		echo '<div class="tc-mb-16">';
		echo '<label class="tc-label tc-d-block tc-mb-8">' . esc_html__( 'Output Format', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap" id="tc-uu-fmt-group">';
        $formats = [
            'standard'  => [ 'label' => __( 'Standard',   'textcraft-tools' ), 'sub' => 'xxxxxxxx-xxxx-…' ],
            'uppercase' => [ 'label' => __( 'UPPERCASE',  'textcraft-tools' ), 'sub' => '' ],
            'no-hyphens'=> [ 'label' => __( 'No Hyphens', 'textcraft-tools' ), 'sub' => 'xxxxxxxxxxxxxxxx' ],
            'braces'    => [ 'label' => __( 'Braces',     'textcraft-tools' ), 'sub' => '{xxxxxxxx-…}' ],
            'urn'       => [ 'label' => __( 'URN',        'textcraft-tools' ), 'sub' => 'urn:uuid:…' ],
        ];
        $first = true;
        foreach ( $formats as $val => $info ) {
            $active  = $first ? ' tc-btn-active' : '';
            $variant = $first ? 'primary' : 'secondary';
            echo '<button class="tc-btn tc-btn--' . esc_attr( $variant ) . ' tc-uu-fmt-btn' . $active . '" data-fmt="' . esc_attr( $val ) . '">';
            echo esc_html( $info['label'] );
            if ( $info['sub'] ) echo '<br><small>' . esc_html( $info['sub'] ) . '</small>';
            echo '</button>';
            $first = false;
        }
        echo '</div>';
        echo '</div>'; // end row 2

        // Row 3: Separator + Options
        echo '<div class="tc-grid-settings-sm">';

        echo '<div>';
		echo '<label class="tc-label tc-d-block tc-mb-8">' . esc_html__( 'Separator', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap" id="tc-uu-sep-group">';
        $seps = [
            'newline' => __( 'New Line',   'textcraft-tools' ),
            'comma'   => __( 'Comma',      'textcraft-tools' ),
            'json'    => __( 'JSON Array', 'textcraft-tools' ),
            'sql'     => __( 'SQL List',   'textcraft-tools' ),
        ];
        $first = true;
        foreach ( $seps as $val => $label ) {
            $active  = $first ? ' tc-btn-active' : '';
            $variant = $first ? 'primary' : 'secondary';
            echo '<button class="tc-btn tc-btn--' . esc_attr( $variant ) . ' tc-uu-sep-btn' . $active . '" data-sep="' . esc_attr( $val ) . '">' . esc_html( $label ) . '</button>';
            $first = false;
        }
        echo '</div>';
        echo '</div>';

        echo '<div class="tc-options tc-flex-col-end">';
        $this->render_options_row( [
            [ 'id' => 'tc-uu-opt-prefix',    'label' => esc_html__( 'Add custom prefix',           'textcraft-tools' ), 'checked' => false ],
            [ 'id' => 'tc-uu-opt-timestamp', 'label' => esc_html__( 'Include generation timestamp', 'textcraft-tools' ), 'checked' => false ],
        ] );
        echo '<div id="tc-uu-prefix-row" class="tc-mt-4 tc-hidden">';
        echo '<input type="text" id="tc-uu-prefix-value" class="tc-text-input" placeholder="' . esc_attr__( 'e.g. user_, order_, id-', 'textcraft-tools' ) . '">';
        echo '</div>';
        echo '</div>';

        echo '</div>'; // end row 3

        // Quick Presets
		echo '<div class="tc-mb-16">';
		echo '<label class="tc-label tc-d-block tc-mb-8">' . esc_html__( 'Quick Presets', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap">';
        $presets = [
            'single'    => __( 'Generate 1',         'textcraft-tools' ),
            'batch10'   => __( 'Batch of 10',        'textcraft-tools' ),
            'batch100'  => __( 'Batch of 100',       'textcraft-tools' ),
            'guid'      => __( 'Windows GUID {…}',   'textcraft-tools' ),
            'urn-batch' => __( 'URN Batch (10)',      'textcraft-tools' ),
            'sql-batch' => __( 'SQL IN() Batch',      'textcraft-tools' ),
        ];
        foreach ( $presets as $val => $label ) {
            echo '<button class="tc-btn tc-btn--secondary tc-uu-preset-btn" data-preset="' . esc_attr( $val ) . '">' . esc_html( $label ) . '</button>';
        }
        echo '</div>';
        echo '</div>'; // end presets

        // Action buttons
        $this->render_button_row( [
            [ 'id' => 'tc-uu-generate', 'label' => '⚡ ' . esc_html__( 'Generate UUIDs', 'textcraft-tools' ), 'variant' => 'primary' ],
            [ 'id' => 'tc-uu-copy',     'label' => '📋 ' . esc_html__( 'Copy All',       'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-uu-clear',    'label' => '🗑️ ' . esc_html__( 'Clear',          'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        // Stats bar
        $this->render_stat_bar( [
            [ 'id' => 'tc-uu-stat-count', 'label' => esc_html__( 'Generated',    'textcraft-tools' ) ],
            [ 'id' => 'tc-uu-stat-ver',   'label' => esc_html__( 'Version',      'textcraft-tools' ) ],
            [ 'id' => 'tc-uu-stat-fmt',   'label' => esc_html__( 'Format',       'textcraft-tools' ) ],
            [ 'id' => 'tc-uu-stat-time',  'label' => esc_html__( 'Generated At', 'textcraft-tools' ) ],
        ] );

        // Output textarea
		echo '<div class="tc-label-row tc-mt-20"><span class="tc-label">' . esc_html__( 'Generated UUIDs', 'textcraft-tools' ) . '</span></div>';
        $this->render_textarea( 'tc-uu-output', '', esc_html__( 'Your generated UUIDs, ULIDs, or NanoIDs will appear here — click Generate UUIDs to start…', 'textcraft-tools' ), 12, true );

        // UUID Validator panel
        echo '<div class="tc-card-surface tc-mt-20 tc-p-16">';
        echo '<p class="tc-label tc-m-0-10">🔍 ' . esc_html__( 'UUID Validator', 'textcraft-tools' ) . '</p>';
        echo '<div class="tc-d-flex tc-gap-10 tc-items-start tc-flex-wrap">';
        echo '<input type="text" id="tc-uu-validate-input" class="tc-text-input tc-text-13 tc-flex-1 tc-font-mono tc-min-w-260" placeholder="' . esc_attr__( 'Paste a UUID or ULID to validate against RFC 4122…', 'textcraft-tools' ) . '">';
		echo '<button class="tc-btn tc-btn--secondary tc-flex-shrink-0" id="tc-uu-validate-btn">' . esc_html__( 'Validate', 'textcraft-tools' ) . '</button>';
        echo '</div>';
        echo '<div id="tc-uu-validate-result" class="tc-text-13 tc-mt-10 tc-p-10-14 tc-hidden tc-radius-sm"></div>';
        echo '</div>'; // end validator

        // Inline JS
        $this->render_inline_script( <<<'JS'
(function () {
    var uuidVer = 'v4';
    var fmtMode = 'standard';
    var sepMode = 'newline';

    var out       = document.getElementById('tc-uu-output');
    var statCount = document.getElementById('tc-uu-stat-count');
    var statVer   = document.getElementById('tc-uu-stat-ver');
    var statFmt   = document.getElementById('tc-uu-stat-fmt');
    var statTime  = document.getElementById('tc-uu-stat-time');

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

    // Version buttons
    document.querySelectorAll('.tc-uu-ver-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            activateGroup('.tc-uu-ver-btn', btn, 'ver', function (v) { uuidVer = v; });
            document.getElementById('tc-uu-v5-options').style.display     = uuidVer === 'v5'     ? 'block' : 'none';
            document.getElementById('tc-uu-nanoid-options').style.display  = uuidVer === 'nanoid' ? 'block' : 'none';
            var isSpecial = (uuidVer === 'ulid' || uuidVer === 'nanoid');
            document.querySelectorAll('.tc-uu-fmt-btn').forEach(function (fb) {
                fb.disabled      = isSpecial;
                fb.style.opacity = isSpecial ? '0.4' : '1';
            });
        });
    });

    // Format buttons
    document.querySelectorAll('.tc-uu-fmt-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            activateGroup('.tc-uu-fmt-btn', btn, 'fmt', function (v) { fmtMode = v; });
        });
    });

    // Separator buttons
    document.querySelectorAll('.tc-uu-sep-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            activateGroup('.tc-uu-sep-btn', btn, 'sep', function (v) { sepMode = v; });
        });
    });

    // Prefix toggle
    document.getElementById('tc-uu-opt-prefix').addEventListener('change', function () {
        document.getElementById('tc-uu-prefix-row').style.display = this.checked ? 'block' : 'none';
    });

    // Presets
    var PRESETS = {
        'single':    { count: 1,   ver: 'v4', fmt: 'standard', sep: 'newline' },
        'batch10':   { count: 10,  ver: 'v4', fmt: 'standard', sep: 'newline' },
        'batch100':  { count: 100, ver: 'v4', fmt: 'standard', sep: 'newline' },
        'guid':      { count: 10,  ver: 'v4', fmt: 'braces',   sep: 'newline' },
        'urn-batch': { count: 10,  ver: 'v4', fmt: 'urn',      sep: 'newline' },
        'sql-batch': { count: 10,  ver: 'v4', fmt: 'standard', sep: 'sql'     },
    };

    document.querySelectorAll('.tc-uu-preset-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var p = PRESETS[btn.getAttribute('data-preset')];
            if (!p) return;
            document.getElementById('tc-uu-count').value = p.count;

            var vb = document.querySelector('.tc-uu-ver-btn[data-ver="' + p.ver + '"]');
            if (vb) { activateGroup('.tc-uu-ver-btn', vb, 'ver', function (v) { uuidVer = v; }); }

            var fb = document.querySelector('.tc-uu-fmt-btn[data-fmt="' + p.fmt + '"]');
            if (fb) { activateGroup('.tc-uu-fmt-btn', fb, 'fmt', function (v) { fmtMode = v; }); }

            var sb = document.querySelector('.tc-uu-sep-btn[data-sep="' + p.sep + '"]');
            if (sb) { activateGroup('.tc-uu-sep-btn', sb, 'sep', function (v) { sepMode = v; }); }

            document.getElementById('tc-uu-v5-options').style.display    = 'none';
            document.getElementById('tc-uu-nanoid-options').style.display = 'none';
            document.querySelectorAll('.tc-uu-fmt-btn').forEach(function (b) { b.disabled = false; b.style.opacity = '1'; });
        });
    });

    // ── Crypto helpers ──
    function cryptoRandBytes(n) {
        var arr = new Uint8Array(n);
        crypto.getRandomValues(arr);
        return arr;
    }
    function hex2(b) { return b.toString(16).padStart(2, '0'); }

    // ── Generators ──
    function genV4() {
        var b = cryptoRandBytes(16);
        b[6] = (b[6] & 0x0f) | 0x40;
        b[8] = (b[8] & 0x3f) | 0x80;
        return [
            Array.from(b.slice(0,4)),  Array.from(b.slice(4,6)),
            Array.from(b.slice(6,8)),  Array.from(b.slice(8,10)),
            Array.from(b.slice(10,16))
        ].map(function (s) { return s.map(hex2).join(''); }).join('-');
    }

    function genV1() {
        var now    = Date.now();
        var t100ns = BigInt(now) * 10000n + 122192928000000000n;
        var tLow   = Number(t100ns & 0xFFFFFFFFn);
        var tMid   = Number((t100ns >> 32n) & 0xFFFFn);
        var tHiVer = Number((t100ns >> 48n) & 0x0FFFn) | 0x1000;
        var b      = cryptoRandBytes(8);
        b[0] = (b[0] & 0x3f) | 0x80;
        var tLowHex  = tLow.toString(16).padStart(8, '0');
        var tMidHex  = tMid.toString(16).padStart(4, '0');
        var tHiHex   = tHiVer.toString(16).padStart(4, '0');
        var clockHex = Array.from(b.slice(0,2)).map(hex2).join('');
        var nodeHex  = Array.from(b.slice(2,8)).map(hex2).join('');
        return tLowHex + '-' + tMidHex + '-' + tHiHex + '-' + clockHex + '-' + nodeHex;
    }

    async function genV5(namespaceKey, name) {
        var NS = {
            dns:  [0x6b,0xa7,0xb8,0x10,0x9d,0xad,0x11,0xd1,0x80,0xb4,0x00,0xc0,0x4f,0xd4,0x30,0xc8],
            url:  [0x6b,0xa7,0xb8,0x11,0x9d,0xad,0x11,0xd1,0x80,0xb4,0x00,0xc0,0x4f,0xd4,0x30,0xc8],
            oid:  [0x6b,0xa7,0xb8,0x12,0x9d,0xad,0x11,0xd1,0x80,0xb4,0x00,0xc0,0x4f,0xd4,0x30,0xc8],
            x500: [0x6b,0xa7,0xb8,0x14,0x9d,0xad,0x11,0xd1,0x80,0xb4,0x00,0xc0,0x4f,0xd4,0x30,0xc8],
        };
        var nsBytes   = new Uint8Array(NS[namespaceKey] || NS.dns);
        var nameBytes = new TextEncoder().encode(name);
        var combined  = new Uint8Array(nsBytes.length + nameBytes.length);
        combined.set(nsBytes); combined.set(nameBytes, nsBytes.length);
        var hashBuf = await crypto.subtle.digest('SHA-1', combined);
        var h = new Uint8Array(hashBuf);
        h[6] = (h[6] & 0x0f) | 0x50;
        h[8] = (h[8] & 0x3f) | 0x80;
        return [
            Array.from(h.slice(0,4)),  Array.from(h.slice(4,6)),
            Array.from(h.slice(6,8)),  Array.from(h.slice(8,10)),
            Array.from(h.slice(10,16))
        ].map(function (s) { return s.map(hex2).join(''); }).join('-');
    }

    var CROCKFORD = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';
    function genULID() {
        var tsMs = Date.now(), t = tsMs, tPart = '';
        for (var i = 9; i >= 0; i--) { tPart = CROCKFORD[t % 32] + tPart; t = Math.floor(t / 32); }
        var rBytes = cryptoRandBytes(10), rVal = 0n, rPart = '';
        rBytes.forEach(function (b) { rVal = (rVal << 8n) | BigInt(b); });
        for (var j = 15; j >= 0; j--) { rPart = CROCKFORD[Number(rVal % 32n)] + rPart; rVal >>= 5n; }
        return tPart + rPart;
    }

    function genNanoID(length, alphabetKey) {
        var ALPHABETS = {
            default:      'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-',
            alphanumeric: 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',
            lowercase:    'abcdefghijklmnopqrstuvwxyz0123456789',
            numbers:      '0123456789',
            hex:          '0123456789abcdef',
        };
        var alphabet = ALPHABETS[alphabetKey] || ALPHABETS['default'];
        var bytes = cryptoRandBytes(length * 2), id = '';
        for (var i = 0; i < bytes.length && id.length < length; i++) {
            id += alphabet[bytes[i] % alphabet.length];
        }
        return id.slice(0, length);
    }

    // ── Format & output ──
    function applyFormat(uuid) {
        if (uuidVer === 'ulid' || uuidVer === 'nanoid') return uuid;
        switch (fmtMode) {
            case 'uppercase':  return uuid.toUpperCase();
            case 'no-hyphens': return uuid.replace(/-/g, '');
            case 'braces':     return '{' + uuid + '}';
            case 'urn':        return 'urn:uuid:' + uuid;
            default:           return uuid;
        }
    }

    function buildOutput(items) {
        var usePrefix = document.getElementById('tc-uu-opt-prefix').checked;
        var prefix    = usePrefix ? (document.getElementById('tc-uu-prefix-value').value || '') : '';
        var withTime  = document.getElementById('tc-uu-opt-timestamp').checked;
        var nowStr    = new Date().toISOString();
        var lines = items.map(function (id) {
            var line = prefix + id;
            if (withTime) line += '  # ' + nowStr;
            return line;
        });
        switch (sepMode) {
            case 'comma': return lines.join(',\n');
            case 'json':  return JSON.stringify(items.map(function (id) { return prefix + id; }), null, 2);
            case 'sql':   return '(\n  \'' + items.map(function (id) { return prefix + id; }).join("',\n  '") + '\'\n)';
            default:      return lines.join('\n');
        }
    }

    // ── Generate ──
    document.getElementById('tc-uu-generate').addEventListener('click', async function () {
        var count  = Math.max(1, Math.min(1000, parseInt(document.getElementById('tc-uu-count').value) || 10));
        var btnGen = document.getElementById('tc-uu-generate');
        btnGen.textContent = '⏳ Generating…';
        btnGen.disabled    = true;

        try {
            var items = [];
            if (uuidVer === 'v5') {
                var nsKey = document.getElementById('tc-uu-v5-namespace').value;
                var names = document.getElementById('tc-uu-v5-name').value
                    .split('\n').map(function (s) { return s.trim(); }).filter(Boolean);
                if (!names.length) {
                    out.value = '⚠️ Please enter at least one name for UUID v5 generation.';
                    return;
                }
                for (var i = 0; i < count; i++) {
                    var uuid = await genV5(nsKey, names[i % names.length]);
                    items.push(applyFormat(uuid));
                }
            } else {
                var nanoLen  = parseInt(document.getElementById('tc-uu-nanoid-length').value) || 21;
                var nanoAlph = document.getElementById('tc-uu-nanoid-alphabet').value;
                for (var j = 0; j < count; j++) {
                    var raw;
                    if (uuidVer === 'v1')          raw = genV1();
                    else if (uuidVer === 'ulid')   raw = genULID();
                    else if (uuidVer === 'nanoid') raw = genNanoID(nanoLen, nanoAlph);
                    else                           raw = genV4();
                    items.push(applyFormat(raw));
                }
            }

            out.value = buildOutput(items);

            var now = new Date();
            statCount.textContent = items.length;
            statVer.textContent   = uuidVer.toUpperCase();
            statFmt.textContent   = fmtMode;
            statTime.textContent  = now.toLocaleTimeString();
        } finally {
            btnGen.textContent = '⚡ Generate UUIDs';
            btnGen.disabled    = false;
        }
    });

    // ── Copy ──
    document.getElementById('tc-uu-copy').addEventListener('click', function () {
        if (!out.value) return;
        var btn = document.getElementById('tc-uu-copy');
        navigator.clipboard.writeText(out.value).then(function () {
            btn.textContent = '✅ Copied!';
            setTimeout(function () { btn.textContent = '📋 Copy All'; }, 2000);
        });
    });

    // ── Clear ──
    document.getElementById('tc-uu-clear').addEventListener('click', function () {
        out.value             = '';
        statCount.textContent = '0';
        statVer.textContent   = '—';
        statFmt.textContent   = '—';
        statTime.textContent  = '—';
        document.getElementById('tc-uu-validate-result').style.display = 'none';
        document.getElementById('tc-uu-validate-input').value          = '';
    });

    // ── Validator ──
    var UUID_REGEX = /^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i;
    var ULID_REGEX = /^[0-9A-HJKMNP-TV-Z]{26}$/;

    function runValidate() {
        var raw   = document.getElementById('tc-uu-validate-input').value.trim()
                        .replace(/^\{|\}$/g, '').replace(/^urn:uuid:/i, '');
        var resEl = document.getElementById('tc-uu-validate-result');
        resEl.style.display = 'block';

        if (UUID_REGEX.test(raw)) {
            var ver     = raw[14];
            var variant = parseInt(raw[19], 16);
            var varStr  = (variant >= 8 && variant <= 11) ? 'RFC 4122' : 'Non-standard';
            resEl.style.background = 'rgba(34,197,94,0.12)';
            resEl.style.border     = '1px solid rgba(34,197,94,0.4)';
            resEl.style.color      = 'var(--tc-text-primary)';
            resEl.innerHTML = '✅ <strong>Valid UUID v' + ver + '</strong> &nbsp;·&nbsp; Variant: ' + varStr + ' &nbsp;·&nbsp; <span class="tc-font-mono-inline">' + raw.toLowerCase() + '</span>';
        } else if (ULID_REGEX.test(raw)) {
            resEl.style.background = 'rgba(34,197,94,0.12)';
            resEl.style.border     = '1px solid rgba(34,197,94,0.4)';
            resEl.style.color      = 'var(--tc-text-primary)';
            resEl.innerHTML = '✅ <strong>Valid ULID</strong> &nbsp;·&nbsp; <span class="tc-font-mono-inline">' + raw.toUpperCase() + '</span>';
        } else if (!raw) {
            resEl.style.background = 'rgba(234,179,8,0.12)';
            resEl.style.border     = '1px solid rgba(234,179,8,0.4)';
            resEl.style.color      = 'var(--tc-text-primary)';
            resEl.innerHTML = '⚠️ Please paste a UUID or ULID to validate.';
        } else {
            resEl.style.background = 'rgba(180,83,9,0.12)';
            resEl.style.border     = '1px solid rgba(180,83,9,0.4)';
            resEl.style.color      = 'var(--tc-text-primary)';
            resEl.innerHTML = '❌ <strong>Invalid UUID / ULID</strong> — does not match RFC 4122 or ULID format. Checked: <span class="tc-font-mono-inline">' + raw.slice(0, 60) + '</span>';
        }
    }

    document.getElementById('tc-uu-validate-btn').addEventListener('click', runValidate);
    document.getElementById('tc-uu-validate-input').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') runValidate();
    });
})();
JS
        );
    }
}
