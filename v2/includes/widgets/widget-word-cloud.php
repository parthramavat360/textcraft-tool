<?php
/**
 * Widget: Word Cloud Generator
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Word_Cloud extends TextCraft_Tool_Base {

    public function get_name(): string { return 'word_cloud'; }
    public function get_title(): string { return 'Word Cloud Generator'; }
    public function get_icon(): string { return 'eicon-grid-view'; }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Generate a visual word cloud from your text. Words that appear more frequently are displayed larger, giving you an instant visual summary.
        </div>

        <?php $this->render_textarea('tc-wc-input', 'Paste text to generate a word cloud from...', 10); ?>

        <?php $this->render_actions('tc-wc-generate', 'Generate Word Cloud', 'tc-wc-clear', 'Clear'); ?>

        <div class="tc-result-area" style="margin-top:20px">
            <div class="tc-label">Word Cloud</div>
            <div class="tc-cloud-wrap" id="tc-wc-cloud"></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-wc-result">
            <textarea class="tc-textarea" id="tc-wc-output" placeholder="Word frequency data will appear here..." readonly rows="8"></textarea>
        </div>
        <?php
    }
}
