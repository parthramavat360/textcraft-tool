<?php
/**
 * Widget: Password Generator
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;

class Widget_Password_Generator extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_password_generator'; }
    public function get_title(): string { return esc_html__( 'Password Generator', 'textcraft-tools' ); }

    public function get_keywords(): array {
        return [ 'password generator', 'strong password', 'secure password creator', 'random password', 'free online security tool' ];
    }
    public function get_icon(): string  { return 'eicon-lock-user'; }
    protected function render_tool_content( array $settings ): void {
        echo '<p class="tc-text-14 tc-text-muted tc-mb-20">' . esc_html__( 'Create strong, secure passwords with custom length and character sets. Generate random passwords with uppercase, lowercase, numbers, and symbols — all created locally in your browser for maximum privacy.', 'textcraft-tools' ) . '</p>';

		// Length slider
		echo '<div class="tc-mb-20">';
		echo '<div class="tc-d-flex tc-flex-between tc-items-center tc-mb-10">';
        echo '<label for="tc-pw-len" class="tc-label">' . esc_html__( 'Password Length', 'textcraft-tools' ) . '</label>';
        echo '<span id="tc-pw-len-val" class="tc-pw-length-val">16</span>';
        echo '</div>';
        echo '<input type="range" id="tc-pw-len" min="4" max="128" value="16" class="tc-range-accent">';
        echo '<div class="tc-d-flex tc-flex-between tc-text-11 tc-text-muted tc-mt-4">';
        echo '<span>4</span><span>32</span><span>64</span><span>96</span><span>128</span>';
        echo '</div>';
        echo '</div>';

		// Character set toggles
		echo '<div class="tc-mb-20">';
		echo '<label class="tc-label tc-d-block tc-mb-10">' . esc_html__( 'Character Sets', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-grid-4col-10">';
        $charsets = [
            'upper'   => [ 'label' => __( 'Uppercase', 'textcraft-tools' ), 'sub' => 'A B C … Z' ],
            'lower'   => [ 'label' => __( 'Lowercase', 'textcraft-tools' ), 'sub' => 'a b c … z' ],
            'numbers' => [ 'label' => __( 'Numbers',   'textcraft-tools' ), 'sub' => '0 1 2 … 9' ],
            'symbols' => [ 'label' => __( 'Symbols',   'textcraft-tools' ), 'sub' => '! @ # $ % ^ & *' ],
        ];
        foreach ( $charsets as $key => $info ) {
            echo '<label class="tc-pw-toggle" id="tc-pw-toggle-' . esc_attr( $key ) . '">';
			echo '<input type="checkbox" id="tc-pw-' . esc_attr( $key ) . '" checked class="tc-d-none">';
            echo '<span class="tc-pw-checkmark">✓</span>';
            echo '<div>';
            echo '<div class="tc-text-13 tc-font-bold tc-text-primary">' . esc_html( $info['label'] ) . '</div>';
            echo '<div class="tc-text-11 tc-text-muted">' . esc_html( $info['sub'] ) . '</div>';
            echo '</div>';
            echo '</label>';
        }
        echo '</div>';
        echo '</div>'; // end character sets

        // Additional options + Custom Exclude / Count
        echo '<div class="tc-grid-2col-16 tc-mb-20">';

        echo '<div>';
		echo '<label class="tc-label tc-d-block tc-mb-8">' . esc_html__( 'Extra Options', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-flex-col tc-gap-8">';
        $this->render_options_row( [
            [ 'id' => 'tc-pw-no-ambiguous', 'label' => esc_html__( 'Exclude ambiguous chars (0, O, l, I)', 'textcraft-tools' ), 'checked' => false ],
            [ 'id' => 'tc-pw-no-similar',   'label' => esc_html__( 'Exclude visually similar (1, |, !)',   'textcraft-tools' ), 'checked' => false ],
            [ 'id' => 'tc-pw-min-each',     'label' => esc_html__( 'At least 1 of each selected type',     'textcraft-tools' ), 'checked' => false ],
        ] );
        echo '</div>';
        echo '</div>';

        echo '<div>';
		echo '<label class="tc-label tc-d-block tc-mb-6">' . esc_html__( 'Custom Exclude', 'textcraft-tools' ) . '</label>';
        echo '<input type="text" id="tc-pw-exclude" class="tc-text-input" placeholder="' . esc_attr__( 'Characters to exclude from passwords, e.g. @#&', 'textcraft-tools' ) . '">';
		echo '<div class="tc-mt-10">';
		echo '<label class="tc-label tc-d-block tc-mb-6">' . esc_html__( 'Number of Passwords', 'textcraft-tools' ) . '</label>';
        echo '<input type="number" id="tc-pw-count" class="tc-text-input" value="1" min="1" max="100">';
        echo '</div>';
        echo '</div>';

        echo '</div>'; // end additional options row

		// Quick presets
		echo '<div class="tc-mb-16">';
		echo '<label class="tc-label tc-d-block tc-mb-8">' . esc_html__( 'Presets', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap">';
        $presets = [
            'basic'  => '🔓 ' . __( 'Basic (8)',       'textcraft-tools' ),
            'medium' => '🔒 ' . __( 'Medium (12)',      'textcraft-tools' ),
            'strong' => '🔐 ' . __( 'Strong (16)',      'textcraft-tools' ),
            'ultra'  => '🛡️ ' . __( 'Ultra (32)',       'textcraft-tools' ),
            'pin'    => '🔢 ' . __( 'Numeric PIN (6)',  'textcraft-tools' ),
            'words'  => '🗝️ ' . __( 'No Symbols (16)', 'textcraft-tools' ),
        ];
        foreach ( $presets as $val => $label ) {
            echo '<button class="tc-btn tc-btn--secondary tc-pw-preset-btn" data-preset="' . esc_attr( $val ) . '">' . esc_html( $label ) . '</button>';
        }
        echo '</div>';
        echo '</div>'; // end presets

        // Action buttons
        $this->render_button_row( [
            [ 'id' => 'tc-pw-generate', 'label' => '🔐 ' . esc_html__( 'Generate Password', 'textcraft-tools' ), 'variant' => 'primary' ],
            [ 'id' => 'tc-pw-copy',     'label' => '📋 ' . esc_html__( 'Copy',              'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-pw-clear',    'label' => '🗑️ ' . esc_html__( 'Clear',             'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        // Strength meter (shown for single password)
        echo '<div id="tc-pw-strength-section" class="tc-pw-strength-wrap">';
        echo '<div class="tc-d-flex tc-flex-between tc-items-center tc-mb-8">';
        echo '<span class="tc-label">' . esc_html__( 'Password Strength', 'textcraft-tools' ) . '</span>';
        echo '<span id="tc-pw-strength-label" class="tc-text-13 tc-font-bold"></span>';
        echo '</div>';
        echo '<div class="tc-pw-strength-bar">';
        echo '<div id="tc-pw-strength-bar" class="tc-pw-strength-fill"></div>';
        echo '</div>';
        echo '<div id="tc-pw-strength-tips" class="tc-pw-strength-tips"></div>';
        echo '</div>'; // end strength section

        // Stats bar
        $this->render_stat_bar( [
            [ 'id' => 'tc-pw-stat-count',   'label' => esc_html__( 'Generated',    'textcraft-tools' ) ],
            [ 'id' => 'tc-pw-stat-length',  'label' => esc_html__( 'Length',       'textcraft-tools' ) ],
            [ 'id' => 'tc-pw-stat-pool',    'label' => esc_html__( 'Pool Size',    'textcraft-tools' ) ],
            [ 'id' => 'tc-pw-stat-entropy', 'label' => esc_html__( 'Entropy (bits)', 'textcraft-tools' ) ],
        ] );

        // Output textarea
		echo '<div class="tc-label-row tc-mt-20"><span class="tc-label">' . esc_html__( 'Generated Password(s)', 'textcraft-tools' ) . '</span></div>';
        $this->render_textarea( 'tc-pw-output', '', esc_html__( 'Your generated password will appear here. Click Generate Password to create a strong, random password.', 'textcraft-tools' ), 5, true );

        // Inline JS
        $this->render_inline_script( <<<'JS'
(function () {
    var CHARS = {
        upper:   'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        lower:   'abcdefghijklmnopqrstuvwxyz',
        numbers: '0123456789',
        symbols: '!@#$%^&*()-_=+[]{}|;:,.<>?',
    };

    var lenSlider    = document.getElementById('tc-pw-len');
    var lenVal       = document.getElementById('tc-pw-len-val');
    var out          = document.getElementById('tc-pw-output');
    var statCount    = document.getElementById('tc-pw-stat-count');
    var statLength   = document.getElementById('tc-pw-stat-length');
    var statPool     = document.getElementById('tc-pw-stat-pool');
    var statEntropy  = document.getElementById('tc-pw-stat-entropy');
    var strengthSect = document.getElementById('tc-pw-strength-section');
    var strengthLbl  = document.getElementById('tc-pw-strength-label');
    var strengthBar  = document.getElementById('tc-pw-strength-bar');
    var strengthTips = document.getElementById('tc-pw-strength-tips');

    if (!lenSlider) return;

    // Slider display
    lenSlider.addEventListener('input', function () { lenVal.textContent = lenSlider.value; });

    // Checkbox toggle visuals
    ['upper', 'lower', 'numbers', 'symbols'].forEach(function (key) {
        var cb      = document.getElementById('tc-pw-' + key);
        var wrapper = document.getElementById('tc-pw-toggle-' + key);
        if (!wrapper) return;
        var check = wrapper.querySelector('.tc-pw-check');
        wrapper.addEventListener('click', function () {
            cb.checked = !cb.checked;
            if (cb.checked) {
                wrapper.style.borderColor = 'var(--tc-accent)';
                check.style.background    = 'var(--tc-accent)';
                check.textContent         = '✓';
            } else {
                wrapper.style.borderColor = 'var(--tc-border)';
                check.style.background    = 'var(--tc-surface-1)';
                check.textContent         = '';
            }
        });
    });

    // Presets
    var PRESETS = {
        basic:  { len: 8,  upper: true,  lower: true,  numbers: true,  symbols: false },
        medium: { len: 12, upper: true,  lower: true,  numbers: true,  symbols: true  },
        strong: { len: 16, upper: true,  lower: true,  numbers: true,  symbols: true  },
        ultra:  { len: 32, upper: true,  lower: true,  numbers: true,  symbols: true  },
        pin:    { len: 6,  upper: false, lower: false, numbers: true,  symbols: false },
        words:  { len: 16, upper: true,  lower: true,  numbers: true,  symbols: false },
    };

    document.querySelectorAll('.tc-pw-preset-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var p = PRESETS[btn.getAttribute('data-preset')];
            if (!p) return;
            lenSlider.value      = p.len;
            lenVal.textContent   = p.len;
            ['upper', 'lower', 'numbers', 'symbols'].forEach(function (key) {
                var cb      = document.getElementById('tc-pw-' + key);
                var wrapper = document.getElementById('tc-pw-toggle-' + key);
                if (!wrapper) return;
                var check = wrapper.querySelector('.tc-pw-check');
                cb.checked                = p[key];
                wrapper.style.borderColor = p[key] ? 'var(--tc-accent)' : 'var(--tc-border)';
                check.style.background    = p[key] ? 'var(--tc-accent)' : 'var(--tc-surface-1)';
                check.textContent         = p[key] ? '✓' : '';
            });
        });
    });

    // Build character pool
    function buildPool() {
        var pool = '';
        if (document.getElementById('tc-pw-upper').checked)   pool += CHARS.upper;
        if (document.getElementById('tc-pw-lower').checked)   pool += CHARS.lower;
        if (document.getElementById('tc-pw-numbers').checked) pool += CHARS.numbers;
        if (document.getElementById('tc-pw-symbols').checked) pool += CHARS.symbols;

        var noAmbiguous = document.getElementById('tc-pw-no-ambiguous').checked;
        var noSimilar   = document.getElementById('tc-pw-no-similar').checked;
        var excludeRaw  = document.getElementById('tc-pw-exclude').value;
        var excludeSet  = {};
        if (noAmbiguous) { ['0','O','l','I'].forEach(function (c) { excludeSet[c] = true; }); }
        if (noSimilar)   { ['1','|','!'].forEach(function (c) { excludeSet[c] = true; }); }
        excludeRaw.split('').forEach(function (c) { excludeSet[c] = true; });

        pool = pool.split('').filter(function (c) { return !excludeSet[c]; }).join('');
        // deduplicate
        var seen = {}, deduped = '';
        pool.split('').forEach(function (c) { if (!seen[c]) { seen[c] = true; deduped += c; } });
        return deduped;
    }

    // Crypto-secure random char
    function randomChar(pool) {
        var arr = new Uint32Array(1);
        var limit = Math.floor(4294967296 / pool.length) * pool.length;
        var idx;
        do { crypto.getRandomValues(arr); idx = arr[0]; } while (idx >= limit);
        return pool[idx % pool.length];
    }

    // Shuffle array in place (crypto)
    function cryptoShuffle(arr) {
        for (var i = arr.length - 1; i > 0; i--) {
            var a = new Uint32Array(1);
            crypto.getRandomValues(a);
            var j = a[0] % (i + 1);
            var tmp = arr[i]; arr[i] = arr[j]; arr[j] = tmp;
        }
        return arr;
    }

    // Generate one password
    function generateOne(pool, length, minEach) {
        if (!pool.length) return null;
        var pw = [];

        if (minEach) {
            var sets = [];
            if (document.getElementById('tc-pw-upper').checked)   sets.push(CHARS.upper.split('').filter(function (c) { return pool.indexOf(c) !== -1; }).join(''));
            if (document.getElementById('tc-pw-lower').checked)   sets.push(CHARS.lower.split('').filter(function (c) { return pool.indexOf(c) !== -1; }).join(''));
            if (document.getElementById('tc-pw-numbers').checked) sets.push(CHARS.numbers.split('').filter(function (c) { return pool.indexOf(c) !== -1; }).join(''));
            if (document.getElementById('tc-pw-symbols').checked) sets.push(CHARS.symbols.split('').filter(function (c) { return pool.indexOf(c) !== -1; }).join(''));
            sets.forEach(function (s) { if (s.length && pw.length < length) pw.push(randomChar(s)); });
        }

        while (pw.length < length) pw.push(randomChar(pool));
        return cryptoShuffle(pw).join('');
    }

    // Strength analysis
    function analyseStrength(pw, pool) {
        var entropy = pw.length * Math.log2(Math.max(pool.length, 1));
        var score, label, colour, tips = [];

        if (entropy < 28)      { score = 10; label = '⚠️ Very Weak';  colour = '#b45309'; }
        else if (entropy < 40) { score = 28; label = '🔓 Weak';       colour = '#f97316'; }
        else if (entropy < 60) { score = 52; label = '🔒 Fair';       colour = '#eab308'; }
        else if (entropy < 80) { score = 72; label = '🔐 Strong';     colour = '#22c55e'; }
        else                   { score = 95; label = '🛡️ Very Strong'; colour = '#d4a24c'; }

        if (pw.length < 12)          tips.push('Use at least 12 characters for better security.');
        if (!/[A-Z]/.test(pw))       tips.push('Add uppercase letters to increase entropy.');
        if (!/[0-9]/.test(pw))       tips.push('Add numbers to strengthen the password.');
        if (!/[^A-Za-z0-9]/.test(pw))tips.push('Add symbols for maximum strength.');

        return { score: score, label: label, colour: colour, entropy: entropy, tips: tips };
    }

    // Generate
    document.getElementById('tc-pw-generate').addEventListener('click', function () {
        var length  = parseInt(lenSlider.value) || 16;
        var count   = Math.max(1, Math.min(100, parseInt(document.getElementById('tc-pw-count').value) || 1));
        var minEach = document.getElementById('tc-pw-min-each').checked;
        var pool    = buildPool();

        if (!pool.length) { out.value = '⚠️ Please select at least one character type.'; return; }

        var passwords = [];
        for (var i = 0; i < count; i++) {
            var pw = generateOne(pool, length, minEach);
            if (pw) passwords.push(pw);
        }

        out.value = passwords.join('\n');

        // Strength meter (single password only)
        if (passwords.length === 1) {
            var s = analyseStrength(passwords[0], pool);
            strengthSect.style.display  = 'block';
            strengthLbl.textContent     = s.label;
            strengthLbl.style.color     = s.colour;
            strengthBar.style.width     = s.score + '%';
            strengthBar.style.background = s.colour;
            strengthTips.innerHTML = s.tips.length
                ? '💡 ' + s.tips.join(' &nbsp;·&nbsp; 💡 ')
                : '✅ Excellent — this password meets all strength criteria.';
            statEntropy.textContent = s.entropy.toFixed(1);
        } else {
            strengthSect.style.display = 'none';
            statEntropy.textContent    = '—';
        }

        statCount.textContent  = passwords.length;
        statLength.textContent = length;
        statPool.textContent   = pool.length;
    });

    // Copy
    document.getElementById('tc-pw-copy').addEventListener('click', function () {
        if (!out.value) return;
        var btn = document.getElementById('tc-pw-copy');
        navigator.clipboard.writeText(out.value).then(function () {
            btn.textContent = '✅ Copied!';
            setTimeout(function () { btn.textContent = '📋 Copy'; }, 2000);
        });
    });

    // Clear
    document.getElementById('tc-pw-clear').addEventListener('click', function () {
        out.value                  = '';
        strengthSect.style.display = 'none';
        statCount.textContent      = '0';
        statLength.textContent     = '—';
        statPool.textContent       = '—';
        statEntropy.textContent    = '—';
    });
})();
JS
        );
    }
}