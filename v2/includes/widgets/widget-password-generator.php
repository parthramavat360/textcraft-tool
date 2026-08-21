<?php
/**
 * Widget: Password Generator
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Password_Generator extends TextCraft_Tool_Base {

    public function get_name(): string { return 'password_generator'; }
    public function get_title(): string { return 'Password Generator'; }
    public function get_icon(): string { return 'eicon-lock-user'; }

    public function get_keywords(): array {
        return ['password generator', 'strong password', 'secure password', 'random password'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Create strong, secure passwords with custom length and character sets. Generated entirely in your browser for maximum privacy.
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Password Length: <strong id="tc-pw-len-val">16</strong></label>
            <input type="range" class="tc-range" id="tc-pw-len" min="4" max="128" value="16">
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Character Sets</label>
            <div class="tc-checkboxes">
                <label class="tc-check"><input type="checkbox" id="tc-pw-upper" checked> Uppercase (A-Z)</label>
                <label class="tc-check"><input type="checkbox" id="tc-pw-lower" checked> Lowercase (a-z)</label>
                <label class="tc-check"><input type="checkbox" id="tc-pw-numbers" checked> Numbers (0-9)</label>
                <label class="tc-check"><input type="checkbox" id="tc-pw-symbols" checked> Symbols (!@#$...)</label>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Extra Options</label>
            <div class="tc-checkboxes">
                <label class="tc-check"><input type="checkbox" id="tc-pw-no-ambiguous"> Exclude ambiguous (0, O, l, I)</label>
                <label class="tc-check"><input type="checkbox" id="tc-pw-min-each"> At least 1 of each type</label>
            </div>
        </div>

        <div class="tc-grid-2col">
            <div class="tc-input-group">
                <label class="tc-label">Custom Exclude</label>
                <input type="text" class="tc-input" id="tc-pw-exclude" placeholder="Characters to exclude...">
            </div>
            <div class="tc-input-group">
                <label class="tc-label">Number of Passwords</label>
                <input type="number" class="tc-input" id="tc-pw-count" value="1" min="1" max="100">
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Presets</label>
            <div class="tc-modes">
                <button class="tc-btn tc-btn--ghost" data-preset="basic" type="button">Basic (8)</button>
                <button class="tc-btn tc-btn--ghost" data-preset="medium" type="button">Medium (12)</button>
                <button class="tc-btn tc-btn--ghost sel" data-preset="strong" type="button">Strong (16)</button>
                <button class="tc-btn tc-btn--ghost" data-preset="ultra" type="button">Ultra (32)</button>
                <button class="tc-btn tc-btn--ghost" data-preset="pin" type="button">PIN (6)</button>
            </div>
        </div>

        <?php $this->render_actions('tc-pw-generate', 'Generate Password', 'tc-pw-copy', 'Copy'); ?>

        <div class="tc-strength-wrap" id="tc-pw-strength-section">
            <div class="tc-label">Password Strength</div>
            <div class="tc-strength-bar">
                <div class="tc-strength-fill" id="tc-pw-strength-bar" style="width:0%"></div>
            </div>
            <div class="tc-strength-label" id="tc-pw-strength-label"></div>
        </div>

        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-pw-result">
            <textarea class="tc-textarea" id="tc-pw-output" placeholder="Generated password will appear here..." readonly rows="8"></textarea>
        </div>
        <?php
    }
}
