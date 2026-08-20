<?php
/**
 * Widget: Word Frequency Counter
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Word_Frequency extends TextCraft_Tool_Base {

    public function get_name(): string { return 'word_frequency'; }
    public function get_title(): string { return 'Word Frequency Counter'; }
    public function get_icon(): string { return 'eicon-chart-bar'; }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Analyze text to count word frequencies. See how often each word appears, sorted by count, with percentages.
        </div>

        <div class="tc-field-row">
            <?php $this->render_checkbox('wf-case', 'Case-sensitive counting', false); ?>
            <?php $this->render_checkbox('wf-ignore', 'Ignore common words (the, is, and...)', true); ?>
        </div>

        <?php $this->render_textarea('tc-wf-input', 'Paste text to analyze word frequency...', 10); ?>

        <?php $this->render_actions('tc-wf-analyze', 'Analyze Frequency', 'tc-wf-copy', 'Copy'); ?>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-wf-result">
            <div class="tc-label">Word Frequency Results</div>
            <div class="tc-freq-list" id="tc-wf-list"></div>
        </div>
        <?php
    }
}
