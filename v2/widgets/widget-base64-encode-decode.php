<?php
/**
 * Widget: Base64 Encode/Decode
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Base64_Encode_Decode extends TextCraft_Tool_Base {

    public function get_name(): string { return 'base64_encode_decode'; }
    public function get_title(): string { return 'Base64 Encode/Decode'; }
    public function get_icon(): string { return 'eicon-code'; }

    public function get_keywords(): array {
        return ['base64', 'base64 encode', 'base64 decode', 'encoding', 'decoding'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Encode and decode Base64 strings. Base64 is commonly used for encoding binary data as text, useful in email, URLs, and data transfer.
        </div>

        <?php $this->render_mode_buttons('b64-direction', [
            'encode' => 'Encode (Text \u2192 Base64)',
            'decode' => 'Decode (Base64 \u2192 Text)',
        ], 'encode'); ?>

        <?php $this->render_textarea('tc-b64-input', 'Enter text or Base64 string...', 10); ?>

        <?php $this->render_actions('tc-b64-convert', 'Convert', 'tc-b64-copy', 'Copy'); ?>

        <?php $this->render_status('tc-b64-status'); ?>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-b64-result">
            <textarea class="tc-textarea" id="tc-b64-output" placeholder="Result will appear here..." readonly rows="8"></textarea>
        </div>
        <?php
    }
}
