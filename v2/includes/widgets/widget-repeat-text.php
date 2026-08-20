<?php
/**
 * Widget: Repeat Text Generator
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Repeat_Text extends TextCraft_Tool_Base {

    public function get_name(): string { return 'repeat_text'; }
    public function get_title(): string { return 'Repeat Text Generator'; }
    public function get_icon(): string { return 'eicon-redo'; }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Repeat any text a specified number of times with your choice of separator. Great for generating placeholder text, test data, or patterns.
        </div>

        <div class="tc-field">
            <label class="tc-label" for="tc-rt-text">Text to Repeat</label>
            <input type="text" class="tc-input" id="tc-rt-text" placeholder="Enter text to repeat...">
        </div>

        <div class="tc-field">
            <label class="tc-label" for="tc-rt-count">Repeat Count (1–1000)</label>
            <input type="number" class="tc-input" id="tc-rt-count" min="1" max="1000" value="5" placeholder="5">
        </div>

        <div class="tc-field">
            <label class="tc-label" for="tc-rt-separator">Separator</label>
            <?php $this->render_select('tc-rt-separator', [
                'newline' => 'Newline',
                'space'   => 'Space',
                'comma'   => 'Comma',
                'none'    => 'None (joined)',
            ], 'Choose separator'); ?>
        </div>

        <?php $this->render_actions('tc-rt-generate', 'Repeat Text', 'tc-rt-copy', 'Copy'); ?>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-rt-result">
            <textarea class="tc-textarea" id="tc-rt-output" placeholder="Repeated text will appear here..." readonly rows="8"></textarea>
        </div>
        <?php
    }
}
