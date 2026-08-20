<?php
/**
 * Widget: JSON Formatter
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;

class Widget_Json_Formatter extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_json_formatter'; }
    public function get_title(): string { return esc_html__( 'JSON Formatter & Validator', 'textcraft-tools' ); }

    public function get_keywords(): array {
        return [ 'json formatter', 'json validator', 'json beautifier', 'json minifier', 'json viewer', 'free online json tool' ];
    }
    public function get_icon(): string  { return 'eicon-code'; }
    protected function render_tool_content( array $settings ): void {
        echo '<p class="tc-text-14 tc-text-muted tc-mb-20">' . esc_html__( 'Format, validate, beautify, and minify JSON data instantly. Paste your raw JSON and get a properly indented, human-readable output — or compress it back to a single line. All processing happens in your browser.', 'textcraft-tools' ) . '</p>';

        // Indent size selector
        echo '<div class="tc-grid-2col-16 tc-mb-20">';
        echo '<div>';
        echo '<label for="tc-json-indent" class="tc-label">' . esc_html__( 'Indent Size', 'textcraft-tools' ) . '</label>';
        echo '<select id="tc-json-indent" class="tc-text-input">';
        echo '<option value="2">2 spaces</option>';
        echo '<option value="4">4 spaces</option>';
        echo '<option value="1">1 tab</option>';
        echo '</select>';
        echo '</div>';
        echo '<div>';
        echo '<label class="tc-label tc-d-block">' . esc_html__( 'Options', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-flex-col tc-gap-8">';
        $this->render_options_row( [
            [ 'id' => 'tc-json-sort',   'label' => esc_html__( 'Sort keys alphabetically', 'textcraft-tools' ), 'checked' => false ],
            [ 'id' => 'tc-json-trailing', 'label' => esc_html__( 'Remove trailing commas', 'textcraft-tools' ), 'checked' => true ],
        ] );
        echo '</div>';
        echo '</div>';
        echo '</div>';

        // Input textarea
        $this->render_textarea( 'tc-json-input', esc_html__( 'Input JSON', 'textcraft-tools' ), esc_html__( 'Paste your raw JSON here — e.g. {"name":"John","age":30}', 'textcraft-tools' ), 12 );

        // Action buttons
        $this->render_button_row( [
            [ 'id' => 'tc-json-beautify', 'label' => '✨ ' . esc_html__( 'Beautify', 'textcraft-tools' ), 'variant' => 'primary' ],
            [ 'id' => 'tc-json-minify',   'label' => '📦 ' . esc_html__( 'Minify',   'textcraft-tools' ), 'variant' => 'secondary' ],
            [ 'id' => 'tc-json-validate', 'label' => '✅ ' . esc_html__( 'Validate', 'textcraft-tools' ), 'variant' => 'secondary' ],
            [ 'id' => 'tc-json-copy',     'label' => '📋 ' . esc_html__( 'Copy',     'textcraft-tools' ), 'variant' => 'ghost' ],
            [ 'id' => 'tc-json-clear',    'label' => '🗑️ ' . esc_html__( 'Clear',    'textcraft-tools' ), 'variant' => 'danger' ],
        ] );

        // Validation status
        echo '<div id="tc-json-status" class="tc-mb-16 tc-text-13" style="display:none;"></div>';

        // Stats bar
        $this->render_stat_bar( [
            [ 'id' => 'tc-json-stat-keys',   'label' => esc_html__( 'Keys',  'textcraft-tools' ) ],
            [ 'id' => 'tc-json-stat-depth',   'label' => esc_html__( 'Depth', 'textcraft-tools' ) ],
            [ 'id' => 'tc-json-stat-size',    'label' => esc_html__( 'Size',  'textcraft-tools' ) ],
        ] );

        // Output textarea
        $this->render_textarea( 'tc-json-output', esc_html__( 'Output', 'textcraft-tools' ), esc_html__( 'Formatted JSON will appear here…', 'textcraft-tools' ), 12, true );

        $this->render_inline_script( <<<'JS'
(function () {
    var input  = document.getElementById('tc-json-input');
    var output = document.getElementById('tc-json-output');
    var indent = document.getElementById('tc-json-indent');
    var status = document.getElementById('tc-json-status');
    var statKeys = document.getElementById('tc-json-stat-keys');
    var statDepth = document.getElementById('tc-json-stat-depth');
    var statSize = document.getElementById('tc-json-stat-size');

    if (!input) return;

    function showStatus(msg, ok) {
        status.style.display = 'block';
        status.textContent = msg;
        status.style.color = ok ? '#22c55e' : '#ef4444';
    }

    function countKeys(obj) {
        if (obj === null || typeof obj !== 'object') return 0;
        var n = 0;
        if (Array.isArray(obj)) { obj.forEach(function (v) { n += countKeys(v); }); return n; }
        var keys = Object.keys(obj);
        n = keys.length;
        keys.forEach(function (k) { n += countKeys(obj[k]); });
        return n;
    }

    function getDepth(obj, d) {
        if (obj === null || typeof obj !== 'object') return d || 0;
        var max = d || 0;
        var vals = Array.isArray(obj) ? obj : Object.values(obj);
        vals.forEach(function (v) { var cd = getDepth(v, (d || 0) + 1); if (cd > max) max = cd; });
        return max;
    }

    function sortKeys(obj) {
        if (obj === null || typeof obj !== 'object') return obj;
        if (Array.isArray(obj)) return obj.map(sortKeys);
        var sorted = {};
        Object.keys(obj).sort().forEach(function (k) { sorted[k] = sortKeys(obj[k]); });
        return sorted;
    }

    function removeTrailing(json) {
        return json.replace(/,\s*([\]}])/g, '$1');
    }

    function getIndent() {
        var v = indent.value;
        if (v === '1') return '\t';
        return parseInt(v) || 2;
    }

    function analyse(raw) {
        var keys = countKeys(raw);
        var depth = getDepth(raw);
        var size = new Blob([typeof raw === 'string' ? raw : JSON.stringify(raw)]).size;
        statKeys.textContent = keys;
        statDepth.textContent = depth;
        statSize.textContent = size > 1024 ? (size / 1024).toFixed(1) + ' KB' : size + ' B';
    }

    document.getElementById('tc-json-beautify').addEventListener('click', function () {
        var raw = input.value.trim();
        if (!raw) { output.value = ''; return; }
        try {
            var parsed = JSON.parse(raw);
            if (document.getElementById('tc-json-sort').checked) parsed = sortKeys(parsed);
            output.value = JSON.stringify(parsed, null, getIndent());
            showStatus('✅ Valid JSON — beautified successfully.', true);
            analyse(output.value);
        } catch (e) {
            showStatus('❌ Invalid JSON: ' + e.message, false);
            output.value = '';
        }
    });

    document.getElementById('tc-json-minify').addEventListener('click', function () {
        var raw = input.value.trim();
        if (!raw) { output.value = ''; return; }
        try {
            var parsed = JSON.parse(raw);
            output.value = JSON.stringify(parsed);
            showStatus('✅ Valid JSON — minified to single line.', true);
            analyse(output.value);
        } catch (e) {
            showStatus('❌ Invalid JSON: ' + e.message, false);
            output.value = '';
        }
    });

    document.getElementById('tc-json-validate').addEventListener('click', function () {
        var raw = input.value.trim();
        if (!raw) { showStatus('⚠️ No input to validate.', false); return; }
        try {
            var parsed = JSON.parse(raw);
            var keys = countKeys(parsed);
            var depth = getDepth(parsed);
            showStatus('✅ Valid JSON — ' + keys + ' keys, depth ' + depth + '.', true);
            analyse(parsed);
        } catch (e) {
            var match = e.message.match(/position\s+(\d+)/i);
            var extra = match ? ' (near character ' + match[1] + ')' : '';
            showStatus('❌ Invalid JSON' + extra + ': ' + e.message, false);
        }
    });

    document.getElementById('tc-json-copy').addEventListener('click', function () {
        var text = output.value || input.value;
        if (!text) return;
        navigator.clipboard.writeText(text).then(function () {
            var btn = document.getElementById('tc-json-copy');
            btn.textContent = '✅ Copied!';
            setTimeout(function () { btn.textContent = '📋 Copy'; }, 2000);
        });
    });

    document.getElementById('tc-json-clear').addEventListener('click', function () {
        input.value = '';
        output.value = '';
        status.style.display = 'none';
        statKeys.textContent = '0';
        statDepth.textContent = '—';
        statSize.textContent = '—';
    });
})();
JS
        );
    }
}
