<?php
/**
 * Widget: Unicode Translator
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Unicode_Translator extends TextCraft_Tool_Base {

    public function get_name(): string { return 'unicode_translator'; }
    public function get_title(): string { return 'Unicode Translator'; }
    public function get_icon(): string { return 'eicon-text'; }

    public function get_keywords(): array {
        return ['unicode', 'unicode translator', 'utf-8', 'character encoding', 'unicode escape'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert text between Unicode characters, UTF-8 code points, escape sequences, and HTML entities. View character details including name and category.
        </div>

        <?php $this->render_mode_buttons('unicode-direction', [
            'to-codepoints' => 'Text \u2192 Code Points',
            'to-text'       => 'Code Points \u2192 Text',
            'to-escape'     => 'Text \u2192 Escape',
            'unescape'      => 'Escape \u2192 Text',
        ], 'to-codepoints'); ?>

        <?php $this->render_textarea('tc-unicode-input', 'Enter text or Unicode code points...', 8); ?>

        <?php $this->render_actions('tc-unicode-convert', 'Convert', 'tc-unicode-copy', 'Copy'); ?>

        <div class="tc-label" style="margin-top:16px">Output</div>
        <textarea class="tc-textarea" id="tc-unicode-output" rows="8" readonly placeholder="Result will appear here..."></textarea>

        <div class="tc-label" style="margin-top:16px">Character Details</div>
        <div class="tc-unicode-details" id="tc-unicode-details"></div>

        <?php $this->render_status('tc-unicode-status'); ?>
        <?php
    }
}
