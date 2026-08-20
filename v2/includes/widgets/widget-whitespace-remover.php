<?php
/**
 * Widget: Whitespace Remover
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Whitespace_Remover extends TextCraft_Tool_Base {

    public function get_name(): string { return 'whitespace_remover'; }
    public function get_title(): string { return 'Whitespace Remover'; }
    public function get_icon(): string { return 'eicon-editor-removeformatting'; }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Clean up messy text by removing extra whitespace. Trim lines, collapse multiple spaces, strip tabs, and remove leading/trailing whitespace.
        </div>

        <div class="tc-field-row">
            <?php $this->render_checkbox('ws-trim', 'Trim leading/trailing from each line', true); ?>
            <?php $this->render_checkbox('ws-extra', 'Remove extra spaces', true); ?>
            <?php $this->render_checkbox('ws-tabs', 'Remove tab characters', false); ?>
            <?php $this->render_checkbox('ws-global', 'Remove all leading & trailing whitespace', false); ?>
        </div>

        <?php $this->render_textarea('tc-ws-input', 'Paste messy text to clean up...', 8); ?>

        <?php $this->render_actions('tc-ws-clean', 'Clean Whitespace', 'tc-ws-copy', 'Copy'); ?>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-ws-result">
            <textarea class="tc-textarea" id="tc-ws-output" placeholder="Cleaned text will appear here..." readonly rows="8"></textarea>
        </div>
        <?php
    }
}
