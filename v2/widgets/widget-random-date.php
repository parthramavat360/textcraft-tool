<?php
/**
 * Widget: Random Date Generator
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Random_Date extends TextCraft_Tool_Base {

    public function get_name(): string { return 'random_date'; }
    public function get_title(): string { return 'Random Date Generator'; }
    public function get_icon(): string { return 'eicon-calendar'; }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Generate random dates within a specified range. Useful for test data, scheduling samples, or creative writing.
        </div>

        <div class="tc-grid-2col">
            <div class="tc-input-group">
                <label class="tc-label">Start Date</label>
                <input type="date" class="tc-input" id="tc-rd-start" value="2020-01-01">
            </div>
            <div class="tc-input-group">
                <label class="tc-label">End Date</label>
                <input type="date" class="tc-input" id="tc-rd-end" value="2026-12-31">
            </div>
        </div>

        <div class="tc-grid-2col">
            <div class="tc-input-group">
                <label class="tc-label">Date Format</label>
                <?php $this->render_select('tc-rd-format', [
                    'Y-m-d'     => 'YYYY-MM-DD',
                    'm/d/Y'     => 'MM/DD/YYYY',
                    'd/m/Y'     => 'DD/MM/YYYY',
                    'written'   => 'Written (e.g. January 15, 2025)',
                ], 'Choose format'); ?>
            </div>
            <div class="tc-input-group">
                <label class="tc-label">How Many Dates</label>
                <input type="number" class="tc-input" id="tc-rd-count" value="10" min="1" max="1000">
            </div>
        </div>

        <?php $this->render_actions('tc-rd-generate', 'Generate Dates', 'tc-rd-copy', 'Copy All'); ?>

        <div class="tc-label" style="margin-top:16px">Generated Dates</div>
        <textarea class="tc-textarea" id="tc-rd-output" rows="10" readonly placeholder="Your random dates will appear here..."></textarea>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-rd-result">
            <textarea class="tc-textarea" id="tc-rd-result-text" placeholder="Result will appear here..." readonly rows="8"></textarea>
        </div>
        <?php
    }
}
