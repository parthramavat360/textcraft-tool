<?php
/**
 * Widget: Code Formatter
 * Universal code formatter/beautifier/minifier for HTML, CSS, JS, XML, YAML, SQL.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Code_Formatter extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'code_formatter'; }
    public function get_title(): string { return 'Code Formatter'; }
    public function get_icon(): string { return 'eicon-code'; }

    public function get_keywords(): array {
        return ['code formatter', 'html formatter', 'css formatter', 'js formatter', 'beautify code', 'minify code', 'prettify'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Format, beautify, or minify HTML, CSS, JavaScript, XML, YAML, and SQL code. Paste your code, pick a mode, and get clean output.
        </div>

        <div class="tc-rsz-options">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Language</h4>
                <div class="tc-rsz-mode-cards tc-cf-lang-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="html"><b>HTML</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="css"><b>CSS</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="js"><b>JavaScript</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="xml"><b>XML</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="sql"><b>SQL</b></button>
                </div>
            </div>
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Action</h4>
                <div class="tc-rsz-mode-cards tc-cf-action-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="beautify"><b>Beautify</b><span>Format nicely</span></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="minify"><b>Minify</b><span>Compress</span></button>
                </div>
            </div>
        </div>

        <textarea class="tc-textarea tc-cf-input" id="tc-cf-input" placeholder="Paste your code here..." rows="12"></textarea>

        <div class="tc-cf-actions">
            <button class="tc-btn tc-btn--accent" id="tc-cf-format" type="button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 3 21 3 21 8"/><line x1="4" y1="20" x2="21" y2="3"/></svg>
                Format
            </button>
            <button class="tc-btn tc-btn--ghost" id="tc-cf-copy" type="button">Copy</button>
            <button class="tc-btn tc-btn--ghost" id="tc-cf-clear" type="button">Clear</button>
        </div>

        <div class="tc-cf-output-wrap">
            <h4 class="tc-rsz-heading">Output</h4>
            <pre class="tc-cf-output" id="tc-cf-output"><code>Paste code and click Format</code></pre>
        </div>

        <?php
    }
}
