<?php
/**
 * Widget: Wingdings Converter
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Wingdings extends TextCraft_Tool_Base {

    public function get_name(): string { return 'wingdings'; }
    public function get_title(): string { return 'Wingdings Converter'; }
    public function get_icon(): string { return 'eicon-font'; }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert text to Wingdings symbols and back. Uses Unicode Wingdings mapping for browser-compatible symbol generation.
        </div>

        <div class="tc-modes" data-group="wd-mode">
            <button class="tc-btn tc-btn--ghost sel" data-val="to_wingdings" type="button">Text → Wingdings</button>
            <button class="tc-btn tc-btn--ghost" data-val="from_wingdings" type="button">Wingdings → Text</button>
        </div>

        <?php $this->render_textarea('tc-wd-input', 'Type text to convert to Wingdings...', 8); ?>

        <?php $this->render_actions('tc-wd-convert', 'Convert', 'tc-wd-copy', 'Copy'); ?>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-wd-result">
            <textarea class="tc-textarea" id="tc-wd-output" placeholder="Result will appear here..." readonly rows="8"></textarea>
        </div>
        <?php
    }
}
