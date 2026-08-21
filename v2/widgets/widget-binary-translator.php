<?php
/**
 * Widget: Binary Translator
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Binary_Translator extends TextCraft_Tool_Base {

    public function get_name(): string { return 'binary_translator'; }
    public function get_title(): string { return 'Binary Translator'; }
    public function get_icon(): string { return 'eicon-code'; }

    public function get_keywords(): array {
        return ['binary translator', 'binary to text', 'text to binary', 'binary converter', 'binary code'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert between text, binary, decimal, octal, and hexadecimal number systems. Useful for encoding, decoding, and understanding data representation.
        </div>

        <?php $this->render_mode_buttons('binary-direction', [
            'text-to-binary'  => 'Text \u2192 Binary',
            'binary-to-text'  => 'Binary \u2192 Text',
            'text-to-hex'     => 'Text \u2192 Hex',
            'hex-to-text'     => 'Hex \u2192 Text',
            'text-to-decimal' => 'Text \u2192 Decimal',
            'decimal-to-text' => 'Decimal \u2192 Text',
        ], 'text-to-binary'); ?>

        <?php $this->render_textarea('tc-bin-input', 'Enter text, binary, hex, or decimal...', 8); ?>

        <div class="tc-input-group">
            <div class="tc-checkboxes">
                <?php $this->render_checkbox('tc-bin-spaces', 'Add spaces between bytes', true); ?>
            </div>
        </div>

        <?php $this->render_actions('tc-bin-convert', 'Convert', 'tc-bin-copy', 'Copy'); ?>

        <?php $this->render_status('tc-bin-status'); ?>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-bin-result">
            <textarea class="tc-textarea" id="tc-bin-output" placeholder="Result will appear here..." readonly rows="8"></textarea>
        </div>
        <?php
    }
}
