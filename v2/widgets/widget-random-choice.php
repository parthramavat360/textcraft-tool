<?php
/**
 * Widget: Random Choice Picker
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Random_Choice extends TextCraft_Tool_Base {

    public function get_name(): string { return 'random_choice'; }
    public function get_title(): string { return 'Random Choice Picker'; }
    public function get_icon(): string { return 'eicon-random'; }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Enter choices one per line and let the random picker decide for you. Perfect for making fair decisions quickly.
        </div>

        <?php $this->render_textarea('tc-rc-input', 'Enter choices, one per line...', 10); ?>

        <div class="tc-grid-2col">
            <div class="tc-input-group">
                <label class="tc-label">Pick Count</label>
                <input type="number" class="tc-input" id="tc-rc-pick-count" value="1" min="1" max="100">
            </div>
            <div class="tc-input-group">
                <label class="tc-label">Allow Duplicates</label>
                <div class="tc-checkboxes">
                    <label class="tc-check"><input type="checkbox" id="tc-rc-allow-dup" checked> Pick same choice more than once</label>
                </div>
            </div>
        </div>

        <?php $this->render_actions('tc-rc-pick', 'Pick Random Choice', 'tc-rc-clear', 'Clear'); ?>

        <div class="tc-result-area" id="tc-rc-display" style="display:none;margin-top:20px">
            <div class="tc-label">Picked Choice</div>
            <div class="tc-spin-result" id="tc-rc-spin-result"></div>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Total Choices</span><span class="tc-stat-value" id="tc-rc-stat-total">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Pick Count</span><span class="tc-stat-value" id="tc-rc-stat-picks">0</span></div>
        </div>

        <div class="tc-label" style="margin-top:16px">Pick History</div>
        <textarea class="tc-textarea" id="tc-rc-output" rows="6" readonly placeholder="Your pick history will appear here..."></textarea>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-rc-result">
            <textarea class="tc-textarea" id="tc-rc-result-text" placeholder="Result will appear here..." readonly rows="6"></textarea>
        </div>
        <?php
    }
}
