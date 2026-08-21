<?php
/**
 * Widget: Morse Code Translator
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Morse_Code_Translator extends TextCraft_Tool_Base {

    public function get_name(): string { return 'morse_code_translator'; }
    public function get_title(): string { return 'Morse Code Translator'; }
    public function get_icon(): string { return 'eicon-text'; }

    public function get_keywords(): array {
        return ['morse code', 'morse translator', 'code translator', 'telegraph code', 'dot dash'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Translate text to Morse code and vice versa. Uses standard International Morse Code with support for letters, numbers, and common punctuation.
        </div>

        <?php $this->render_mode_buttons('morse-direction', [
            'to-morse'   => 'Text \u2192 Morse',
            'from-morse' => 'Morse \u2192 Text',
        ], 'to-morse'); ?>

        <?php $this->render_textarea('tc-morse-input', 'Enter text to translate...', 8); ?>

        <div class="tc-input-group">
            <label class="tc-label">Separator</label>
            <?php $this->render_select('tc-morse-sep', [
                ' '   => 'Space (default)',
                ' | ' => 'Pipe ( | )',
                '\n'  => 'Newline',
            ], 'Choose separator'); ?>
        </div>

        <?php $this->render_actions('tc-morse-translate', 'Translate', 'tc-morse-copy', 'Copy'); ?>

        <div class="tc-label" style="margin-top:16px">Morse Code Reference</div>
        <div class="tc-morse-ref" id="tc-morse-ref"></div>

        <?php $this->render_status('tc-morse-status'); ?>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-morse-result">
            <textarea class="tc-textarea" id="tc-morse-output" placeholder="Result will appear here..." readonly rows="8"></textarea>
        </div>
        <?php
    }
}
