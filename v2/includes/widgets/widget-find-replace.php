<?php
/**
 * Widget: Find and Replace
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Find_Replace extends TextCraft_Tool_Base {

    public function get_name(): string { return 'find_replace'; }
    public function get_title(): string { return 'Find and Replace'; }
    public function get_icon(): string { return 'eicon-search'; }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Search and replace words or phrases in any text. Supports case-sensitive matching, whole-word mode, and regular expressions.
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Find</label>
            <input type="text" class="tc-input" id="tc-fr-find" placeholder="Enter text or pattern to find...">
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Replace With</label>
            <input type="text" class="tc-input" id="tc-fr-replace" placeholder="Replacement text (leave blank to delete)">
        </div>

        <div class="tc-checkboxes">
            <label class="tc-check"><input type="checkbox" id="tc-fr-case"> Case-sensitive</label>
            <label class="tc-check"><input type="checkbox" id="tc-fr-whole"> Whole word only</label>
            <label class="tc-check"><input type="checkbox" id="tc-fr-regex"> Use regex</label>
            <label class="tc-check"><input type="checkbox" id="tc-fr-all" checked> Replace all</label>
        </div>

        <?php $this->render_textarea('tc-fr-input', 'Paste or type the text to search through...', 6); ?>

        <?php $this->render_actions('tc-fr-do', 'Replace', 'tc-fr-copy', 'Copy Result'); ?>

        <?php $this->render_status('tc-fr-err'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item">
                <span class="tc-stat-label">Matches Found</span>
                <span class="tc-stat-value" id="tc-fr-matches">0</span>
            </div>
            <div class="tc-stat-item">
                <span class="tc-stat-label">Replacements Made</span>
                <span class="tc-stat-value" id="tc-fr-replaced">0</span>
            </div>
        </div>

        <div class="tc-result-area" style="margin-top:16px">
            <div class="tc-label">Result</div>
            <textarea class="tc-textarea" id="tc-fr-output" readonly rows="6"></textarea>
        </div>
        <?php
    }
}