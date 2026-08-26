<?php
/**
 * Widget: JSON Tools
 * CSV↔JSON, JSON↔YAML, JSON→TypeScript. Multi-mode converter.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Json_Tools extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'json_tools'; }
    public function get_title(): string { return 'JSON Tools'; }
    public function get_icon(): string { return 'eicon-array'; }

    public function get_keywords(): array {
        return ['json tools', 'csv to json', 'json to csv', 'json to yaml', 'yaml to json', 'json to typescript', 'json formatter'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert between JSON, CSV, YAML, and TypeScript. Paste your data, pick a conversion, and get instant output.
        </div>

        <div class="tc-rsz-options">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Conversion</h4>
                <div class="tc-rsz-mode-cards tc-jt-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="csv-to-json"><b>CSV → JSON</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="json-to-csv"><b>JSON → CSV</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="json-to-yaml"><b>JSON → YAML</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="yaml-to-json"><b>YAML → JSON</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="json-to-ts"><b>JSON → TS</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="format"><b>JSON Format</b></button>
                </div>
            </div>
        </div>

        <div class="tc-jt-grid">
            <div class="tc-jt-col">
                <h4 class="tc-jt-col-title">Input</h4>
                <textarea class="tc-textarea tc-jt-textarea" id="tc-jt-input" placeholder="Paste your data here..." rows="12"></textarea>
            </div>
            <div class="tc-jt-col">
                <h4 class="tc-jt-col-title">Output</h4>
                <pre class="tc-jt-output" id="tc-jt-output"><code>Output will appear here</code></pre>
            </div>
        </div>

        <div class="tc-jt-actions">
            <button class="tc-btn tc-btn--accent" id="tc-jt-convert" type="button">Convert</button>
            <button class="tc-btn tc-btn--ghost" id="tc-jt-copy" type="button">Copy Output</button>
            <button class="tc-btn tc-btn--ghost" id="tc-jt-swap" type="button">Swap</button>
        </div>

        <?php
    }
}
