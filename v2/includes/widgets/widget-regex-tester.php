<?php
/**
 * Widget: Regex Tester & Debugger
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Regex_Tester extends TextCraft_Tool_Base {

    public function get_name(): string { return 'regex_tester'; }
    public function get_title(): string { return 'Regex Tester & Debugger'; }
    public function get_icon(): string { return 'eicon-search'; }

    public function get_keywords(): array {
        return ['regex tester', 'regular expression', 'regex debugger', 'pattern matching', 'regex validator'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Test and debug regular expressions with real-time matching, highlighting, and capture groups. Supports JavaScript regex syntax.
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Regular Expression</label>
            <div class="tc-flex-row">
                <span class="tc-input-addon">/</span>
                <input type="text" class="tc-input tc-input--flex" id="tc-regex-pattern" placeholder="Enter regex pattern...">
                <span class="tc-input-addon">/</span>
                <input type="text" class="tc-input tc-input--narrow" id="tc-regex-flags" value="g" placeholder="flags">
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Test String</label>
            <?php $this->render_textarea('tc-regex-input', 'Enter text to test against the pattern...', 10); ?>
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Quick Patterns</label>
            <div class="tc-modes" data-group="regex-quick">
                <button class="tc-btn tc-btn--ghost" data-val="email" type="button">Email</button>
                <button class="tc-btn tc-btn--ghost" data-val="url" type="button">URL</button>
                <button class="tc-btn tc-btn--ghost" data-val="phone" type="button">Phone</button>
                <button class="tc-btn tc-btn--ghost" data-val="ip" type="button">IP Address</button>
                <button class="tc-btn tc-btn--ghost" data-val="date" type="button">Date</button>
                <button class="tc-btn tc-btn--ghost" data-val="hex" type="button">Hex Color</button>
            </div>
        </div>

        <?php $this->render_actions('tc-regex-test', 'Test Pattern', 'tc-regex-copy-matches', 'Copy Matches'); ?>

        <div class="tc-input-group" style="margin-top:16px">
            <label class="tc-label">Matches <span id="tc-regex-count">0 found</span></label>
            <div class="tc-regex-matches" id="tc-regex-matches"></div>
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Capture Groups</label>
            <div class="tc-regex-groups" id="tc-regex-groups"></div>
        </div>

        <?php $this->render_status('tc-regex-status'); ?>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-regex-result">
            <textarea class="tc-textarea" id="tc-regex-result-text" placeholder="Highlighted matches will appear here..." readonly rows="10"></textarea>
        </div>
        <?php
    }
}
