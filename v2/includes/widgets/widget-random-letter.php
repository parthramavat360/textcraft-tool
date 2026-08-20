<?php
/**
 * Widget: Random Letter Generator
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Random_Letter extends TextCraft_Tool_Base {

    public function get_name(): string { return 'random_letter'; }
    public function get_title(): string { return 'Random Letter Generator'; }
    public function get_icon(): string { return 'eicon-text'; }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Generate random letters, numbers, or symbols. Useful for creating test data, captchas, codes, or placeholders.
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Character Sets</label>
            <div class="tc-checkboxes">
                <label class="tc-check"><input type="checkbox" id="tc-rl-upper" checked> Uppercase (A-Z)</label>
                <label class="tc-check"><input type="checkbox" id="tc-rl-lower" checked> Lowercase (a-z)</label>
                <label class="tc-check"><input type="checkbox" id="tc-rl-numbers" checked> Numbers (0-9)</label>
                <label class="tc-check"><input type="checkbox" id="tc-rl-symbols"> Symbols (!@#$...)</label>
            </div>
        </div>

        <div class="tc-grid-2col">
            <div class="tc-input-group">
                <label class="tc-label">How Many Characters</label>
                <input type="number" class="tc-input" id="tc-rl-count" value="20" min="1" max="10000">
            </div>
            <div class="tc-input-group">
                <label class="tc-label">Separator</label>
                <?php $this->render_select('tc-rl-separator', [
                    'none'    => 'None',
                    'space'   => 'Space',
                    'comma'   => 'Comma',
                    'newline' => 'New Line',
                ], 'Choose separator'); ?>
            </div>
        </div>

        <?php $this->render_actions('tc-rl-generate', 'Generate Characters', 'tc-rl-copy', 'Copy All'); ?>

        <div class="tc-label" style="margin-top:16px">Generated Characters</div>
        <textarea class="tc-textarea" id="tc-rl-output" rows="8" readonly placeholder="Your random characters will appear here..."></textarea>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-rl-result">
            <textarea class="tc-textarea" id="tc-rl-result-text" placeholder="Result will appear here..." readonly rows="6"></textarea>
        </div>
        <?php
    }
}
