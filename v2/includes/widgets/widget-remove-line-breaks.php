<?php
/**
 * Widget: Remove Line Breaks
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Remove_Line_Breaks extends TextCraft_Tool_Base {

    public function get_name(): string { return 'remove_line_breaks'; }
    public function get_title(): string { return 'Remove Line Breaks'; }
    public function get_icon(): string { return 'eicon-editor-paragraph'; }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Remove line breaks from text. Choose how to handle line endings — replace with spaces, join all lines, or preserve paragraph breaks.
        </div>

        <div class="tc-modes" data-group="rlb-mode">
            <button class="tc-btn tc-btn--ghost sel" data-val="spaces" type="button">Replace with Spaces</button>
            <button class="tc-btn tc-btn--ghost" data-val="join" type="button">Join Lines</button>
            <button class="tc-btn tc-btn--ghost" data-val="paragraphs" type="button">Keep Paragraphs</button>
        </div>

        <?php $this->render_textarea('tc-rlb-input', 'Paste text with line breaks to remove...', 8); ?>

        <?php $this->render_actions('tc-rlb-convert', 'Remove Breaks', 'tc-rlb-copy', 'Copy'); ?>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-rlb-result">
            <textarea class="tc-textarea" id="tc-rlb-output" placeholder="Result will appear here..." readonly rows="8"></textarea>
        </div>
        <?php
    }
}
