<?php
/**
 * Widget: NATO Phonetic Alphabet
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_NATO_Phonetic extends TextCraft_Tool_Base {

    public function get_name(): string { return 'nato_phonetic'; }
    public function get_title(): string { return 'NATO Phonetic Alphabet'; }
    public function get_icon(): string { return 'eicon-megaphone'; }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert text to the NATO phonetic alphabet. Each letter is replaced with its standard phonetic word (e.g. A = Alpha, B = Bravo). Ideal for radio communication clarity.
        </div>

        <?php $this->render_textarea('tc-nato-input', 'Enter text to convert to NATO phonetic alphabet...', 8); ?>

        <div class="tc-input-group">
            <label class="tc-label">Options</label>
            <div class="tc-checkboxes">
                <label class="tc-check"><input type="checkbox" id="tc-nato-include-space" checked> Include spaces between words</label>
                <label class="tc-check"><input type="checkbox" id="tc-nato-uppercase" checked> Show phonetic words in uppercase</label>
                <label class="tc-check"><input type="checkbox" id="tc-nato-include-original"> Show original text alongside</label>
            </div>
        </div>

        <?php $this->render_actions('tc-nato-convert', 'Convert to NATO', 'tc-nato-copy', 'Copy'); ?>

        <div class="tc-label" style="margin-top:16px">NATO Phonetic Output</div>
        <textarea class="tc-textarea" id="tc-nato-output" rows="8" readonly placeholder="NATO phonetic alphabet will appear here..."></textarea>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-nato-result">
            <textarea class="tc-textarea" id="tc-nato-result-text" placeholder="Result will appear here..." readonly rows="8"></textarea>
        </div>
        <?php
    }
}
