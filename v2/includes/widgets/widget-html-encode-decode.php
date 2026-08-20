<?php
/**
 * Widget: HTML Encode/Decode
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_HTML_Encode_Decode extends TextCraft_Tool_Base {

    public function get_name(): string { return 'html_encode_decode'; }
    public function get_title(): string { return 'HTML Encode/Decode'; }
    public function get_icon(): string { return 'eicon-code'; }

    public function get_keywords(): array {
        return ['html encode', 'html decode', 'html entities', 'escape html', 'unescape html'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Encode special characters to HTML entities and decode HTML entities back to readable text. Essential for safely displaying user input in web pages.
        </div>

        <?php $this->render_mode_buttons('html-direction', [
            'encode' => 'Encode (Text \u2192 Entities)',
            'decode' => 'Decode (Entities \u2192 Text)',
        ], 'encode'); ?>

        <?php $this->render_textarea('tc-html-input', 'Enter text or HTML entities...', 10); ?>

        <div class="tc-input-group">
            <label class="tc-label">Encoding Level</label>
            <?php $this->render_select('tc-html-level', [
                'basic'    => 'Basic (& < > " \')',
                'extended' => 'Extended (all special chars)',
                'numeric'  => 'Numeric (&#decimal;)',
                'hex'      => 'Hex (&#xhex;)',
            ]); ?>
        </div>

        <?php $this->render_actions('tc-html-convert', 'Convert', 'tc-html-copy', 'Copy'); ?>

        <div class="tc-label" style="margin-top:16px">Output</div>
        <textarea class="tc-textarea" id="tc-html-output" rows="10" readonly placeholder="Result will appear here..."></textarea>

        <?php $this->render_status('tc-html-status'); ?>
        <?php
    }
}
