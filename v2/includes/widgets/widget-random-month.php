<?php
/**
 * Widget: Random Month Generator
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Random_Month extends TextCraft_Tool_Base {

    public function get_name(): string { return 'random_month'; }
    public function get_title(): string { return 'Random Month Generator'; }
    public function get_icon(): string { return 'eicon-calendar'; }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Generate random months with details like number of days and season. Great for scheduling simulations, test data, or planning exercises.
        </div>

        <div class="tc-input-group">
            <label class="tc-label">How Many Months</label>
            <input type="number" class="tc-input" id="tc-rm-count" value="5" min="1" max="100">
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Include Months</label>
            <div class="tc-checkboxes">
                <label class="tc-check"><input type="checkbox" id="tc-rm-jan" checked> January</label>
                <label class="tc-check"><input type="checkbox" id="tc-rm-feb" checked> February</label>
                <label class="tc-check"><input type="checkbox" id="tc-rm-mar" checked> March</label>
                <label class="tc-check"><input type="checkbox" id="tc-rm-apr" checked> April</label>
                <label class="tc-check"><input type="checkbox" id="tc-rm-may" checked> May</label>
                <label class="tc-check"><input type="checkbox" id="tc-rm-jun" checked> June</label>
                <label class="tc-check"><input type="checkbox" id="tc-rm-jul" checked> July</label>
                <label class="tc-check"><input type="checkbox" id="tc-rm-aug" checked> August</label>
                <label class="tc-check"><input type="checkbox" id="tc-rm-sep" checked> September</label>
                <label class="tc-check"><input type="checkbox" id="tc-rm-oct" checked> October</label>
                <label class="tc-check"><input type="checkbox" id="tc-rm-nov" checked> November</label>
                <label class="tc-check"><input type="checkbox" id="tc-rm-dec" checked> December</label>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Show Details</label>
            <div class="tc-checkboxes">
                <label class="tc-check"><input type="checkbox" id="tc-rm-show-name" checked> Month Name</label>
                <label class="tc-check"><input type="checkbox" id="tc-rm-show-days" checked> Number of Days</label>
                <label class="tc-check"><input type="checkbox" id="tc-rm-show-season" checked> Season</label>
            </div>
        </div>

        <?php $this->render_actions('tc-rm-generate', 'Generate Months', 'tc-rm-copy', 'Copy All'); ?>

        <div class="tc-label" style="margin-top:16px">Generated Months</div>
        <textarea class="tc-textarea" id="tc-rm-output" rows="8" readonly placeholder="Your random months will appear here..."></textarea>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-rm-result">
            <textarea class="tc-textarea" id="tc-rm-result-text" placeholder="Result will appear here..." readonly rows="8"></textarea>
        </div>
        <?php
    }
}
