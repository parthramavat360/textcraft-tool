<?php
/**
 * Widget: Character Frequency Counter
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Character_Frequency_Counter extends TextCraft_Tool_Base {

    public function get_name(): string { return 'character_frequency_counter'; }
    public function get_title(): string { return 'Character Frequency Counter'; }
    public function get_icon(): string { return 'eicon-chart-bar'; }

    public function get_keywords(): array {
        return ['character frequency', 'letter frequency', 'text analysis', 'char count', 'frequency analysis'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Analyze character, word, and line frequency in any text. See exact counts, percentages, and visual bar charts for each character.
        </div>

        <?php $this->render_textarea('tc-freq-input', 'Paste or type your text here to analyze...', 12); ?>

        <div class="tc-input-group">
            <label class="tc-label">Count Mode</label>
            <?php $this->render_mode_buttons('freq-mode', [
                'chars'  => 'Characters',
                'words'  => 'Words',
                'lines'  => 'Lines',
            ], 'chars'); ?>
        </div>

        <div class="tc-input-group">
            <div class="tc-checkboxes">
                <?php $this->render_checkbox('tc-freq-case', 'Case sensitive', false); ?>
                <?php $this->render_checkbox('tc-freq-spaces', 'Include spaces', false); ?>
            </div>
        </div>

        <?php $this->render_actions('tc-freq-analyze', 'Analyze', 'tc-freq-copy', 'Copy Results'); ?>

        <div class="tc-label" style="margin-top:16px">Frequency Table</div>
        <div class="tc-freq-table" id="tc-freq-table"></div>

        <div class="tc-label" style="margin-top:16px">Visual Chart</div>
        <div class="tc-freq-chart" id="tc-freq-chart"></div>

        <?php $this->render_status('tc-freq-status'); ?>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-freq-result">
            <textarea class="tc-textarea" id="tc-freq-output" placeholder="Frequency results will appear here..." readonly rows="8"></textarea>
        </div>
        <?php
    }
}
