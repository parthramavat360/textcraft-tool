<?php
/**
 * Widget: Markdown Preview
 * Live split-pane Markdown editor with preview.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Markdown_Preview extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'markdown_preview'; }
    public function get_title(): string { return 'Markdown Preview'; }
    public function get_icon(): string { return 'eicon-text-editor'; }

    public function get_keywords(): array {
        return ['markdown preview', 'markdown editor', 'live preview', 'markdown viewer'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Write Markdown on the left, see the live rendered preview on the right. Supports headings, bold, italic, links, images, code blocks, lists, and tables.
        </div>

        <div class="tc-mp-split">
            <div class="tc-mp-editor">
                <h4 class="tc-mp-col-title">Markdown Editor</h4>
                <textarea class="tc-textarea tc-mp-input" id="tc-mp-input" placeholder="# Heading&#10;&#10;Write your **markdown** here...&#10;&#10;- Item 1&#10;- Item 2&#10;&#10;`code` and ```code blocks```" rows="20"></textarea>
            </div>
            <div class="tc-mp-preview">
                <h4 class="tc-mp-col-title">Preview</h4>
                <div class="tc-mp-output" id="tc-mp-output"><p class="tc-mp-placeholder">Start typing to see preview...</p></div>
            </div>
        </div>

        <div class="tc-mp-actions">
            <button class="tc-btn tc-btn--accent" id="tc-mp-copy-md" type="button">Copy Markdown</button>
            <button class="tc-btn tc-btn--ghost" id="tc-mp-copy-html" type="button">Copy HTML</button>
            <button class="tc-btn tc-btn--ghost" id="tc-mp-clear" type="button">Clear</button>
        </div>

        <?php
    }
}
