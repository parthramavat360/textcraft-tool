<?php
/**
 * Widget: Developer Utilities
 * Hex↔Text, Decimal↔Binary, Slugify, UTM Builder, JWT Decoder, HTML→Markdown.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Dev_Utilities extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'dev_utilities'; }
    public function get_title(): string { return 'Developer Utilities'; }
    public function get_icon(): string { return 'eicon-settings'; }

    public function get_keywords(): array {
        return ['developer tools', 'hex to text', 'text to hex', 'binary converter', 'slugify', 'jwt decoder', 'utm builder', 'html to markdown'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Collection of developer utilities: hex/text conversion, binary/decimal, URL slugify, UTM builder, JWT decoder, and HTML to Markdown.
        </div>

        <div class="tc-rsz-options">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Tool</h4>
                <div class="tc-rsz-mode-cards tc-du-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="hex-text"><b>Hex ↔ Text</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="bin-dec"><b>Binary ↔ Dec</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="slugify"><b>Slugify</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="utm"><b>UTM Builder</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="jwt"><b>JWT Decoder</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="html-md"><b>HTML → MD</b></button>
                </div>
            </div>
        </div>

        <div class="tc-du-inputs">
            <div class="tc-du-col">
                <h4 class="tc-du-col-title" id="tc-du-input-label">Input</h4>
                <textarea class="tc-textarea tc-du-textarea" id="tc-du-input" placeholder="Enter text or hex values..." rows="8"></textarea>
            </div>
            <div class="tc-du-col">
                <h4 class="tc-du-col-title" id="tc-du-output-label">Output</h4>
                <pre class="tc-du-output" id="tc-du-output"><code>Output will appear here</code></pre>
            </div>
        </div>

        <!-- UTM Builder fields (hidden by default) -->
        <div class="tc-du-utm-fields" id="tc-du-utm-fields" style="display:none">
            <div class="tc-input-group"><label class="tc-label">Website URL</label><input type="text" class="tc-input" id="tc-du-utm-url" placeholder="https://example.com/page"></div>
            <div class="tc-input-group"><label class="tc-label">Campaign Source</label><input type="text" class="tc-input" id="tc-du-utm-source" placeholder="google"></div>
            <div class="tc-input-group"><label class="tc-label">Campaign Medium</label><input type="text" class="tc-input" id="tc-du-utm-medium" placeholder="cpc"></div>
            <div class="tc-input-group"><label class="tc-label">Campaign Name</label><input type="text" class="tc-input" id="tc-du-utm-campaign" placeholder="spring_sale"></div>
            <div class="tc-input-group"><label class="tc-label">Campaign Term (optional)</label><input type="text" class="tc-input" id="tc-du-utm-term" placeholder="running+shoes"></div>
            <div class="tc-input-group"><label class="tc-label">Campaign Content (optional)</label><input type="text" class="tc-input" id="tc-du-utm-content" placeholder="banner"></div>
        </div>

        <div class="tc-du-actions">
            <button class="tc-btn tc-btn--accent" id="tc-du-convert" type="button">Convert</button>
            <button class="tc-btn tc-btn--ghost" id="tc-du-copy" type="button">Copy</button>
        </div>

        <?php
    }
}
