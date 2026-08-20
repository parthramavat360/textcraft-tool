<?php
/**
 * Widget: URL Encode/Decode
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_URL_Encode_Decode extends TextCraft_Tool_Base {

    public function get_name(): string { return 'url_encode_decode'; }
    public function get_title(): string { return 'URL Encode/Decode'; }
    public function get_icon(): string { return 'eicon-link'; }

    public function get_keywords(): array {
        return ['url encode', 'url decode', 'percent encoding', 'url escaping', 'query string'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Encode and decode URL strings with percent-encoding. Useful for handling query parameters, special characters in URLs, and form data.
        </div>

        <?php $this->render_mode_buttons('url-direction', [
            'encode'      => 'Encode All',
            'decode'      => 'Decode All',
            'encode-comp' => 'Encode Component',
            'encode-full' => 'Encode Full URL',
        ], 'encode'); ?>

        <?php $this->render_textarea('tc-url-input', 'Enter URL or text to encode/decode...', 8); ?>

        <?php $this->render_actions('tc-url-convert', 'Convert', 'tc-url-copy', 'Copy'); ?>

        <div class="tc-label" style="margin-top:16px">Output</div>
        <textarea class="tc-textarea" id="tc-url-output" rows="8" readonly placeholder="Result will appear here..."></textarea>

        <?php $this->render_status('tc-url-status'); ?>
        <?php
    }
}
