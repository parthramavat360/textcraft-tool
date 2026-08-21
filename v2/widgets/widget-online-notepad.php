<?php
/**
 * Widget: Online Notepad
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Online_Notepad extends TextCraft_Tool_Base {

    public function get_name(): string { return 'online_notepad'; }
    public function get_title(): string { return 'Online Notepad'; }
    public function get_icon(): string { return 'eicon-editor-heading'; }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            A simple browser-based notepad with word, character, and line counting. Notes are auto-saved to your browser's local storage and persist across sessions.
        </div>

        <textarea class="tc-textarea tc-notepad" id="tc-onp-input" rows="16" placeholder="Start typing your notes here..." style="min-height:320px"></textarea>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Words</span><span class="tc-stat-value" id="tc-onp-words">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Characters</span><span class="tc-stat-value" id="tc-onp-chars">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Lines</span><span class="tc-stat-value" id="tc-onp-lines">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-onp-saved">No</span></div>
        </div>

        <div class="tc-actions" style="margin-top:12px">
            <button class="tc-btn tc-btn--accent" id="tc-onp-save" type="button">Save</button>
            <button class="tc-btn tc-btn--ghost" id="tc-onp-load" type="button">Load Saved</button>
            <button class="tc-btn tc-btn--ghost" id="tc-onp-clear" type="button">Clear</button>
            <button class="tc-btn tc-btn--ghost" id="tc-onp-export" type="button">Export as TXT</button>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-onp-result">
            <div class="tc-label">Notes Preview</div>
            <textarea class="tc-textarea" id="tc-onp-output" placeholder="Your saved notes preview..." readonly rows="10"></textarea>
        </div>
        <?php
    }
}
