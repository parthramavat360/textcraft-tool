<?php
/**
 * Widget: Small Text Generator
 * Generate small text using superscript, subscript, and small caps Unicode.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Small_Text extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'small_text'; }
    public function get_title(): string { return 'Small Text Generator'; }
    public function get_icon(): string { return 'eicon-text'; }

    public function get_keywords(): array {
        return ['small text', 'small font', 'tiny text', 'mini text', 'small text generator', 'small letters', 'superscript', 'subscript', 'small caps'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Generate small text in multiple styles — superscript, subscript, small caps, and tiny letters. Type or paste your text and copy any style.
        </div>

        <?php $this->render_textarea('tc-sm-input', 'Type or paste your text here...', 4); ?>

        <div class="tc-sm-grid" id="tc-sm-grid"></div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div id="tc-sm-result">
            <p style="color:var(--tc-text-dim,#94a3b8);font-size:13px;text-align:center;padding:20px 0">Click any card above to copy the small text.</p>
        </div>
        <?php
    }
}
