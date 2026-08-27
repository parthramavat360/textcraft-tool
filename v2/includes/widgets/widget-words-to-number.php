<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Words_To_Number extends TextCraft_Tool_Base {
    public function get_name(): string { return 'words_to_number'; }
    public function get_title(): string { return 'Words to Number'; }
    public function get_icon(): string { return 'eicon-sort-alphabet-asc'; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Convert written numbers to digits. Supports cardinal numbers (one → 1), ordinal (first → 1st), currency (fifty dollars → $50), and Roman numerals.</div>
        <div class="tc-tool-actions-bar">
            <div class="tc-modes" data-group="wt-mode">
                <button class="tc-btn tc-btn--ghost sel" data-val="cardinal">Cardinal</button>
                <button class="tc-btn tc-btn--ghost" data-val="ordinal">Ordinal</button>
                <button class="tc-btn tc-btn--ghost" data-val="currency">Currency</button>
            </div>
            <button class="tc-btn tc-btn--outline" id="wt-copy">Copy</button>
        </div>
        <div class="tc-rsz-toggles">
            <label class="tc-rsz-toggle">
                <input type="checkbox" class="tc-rsz-toggle-input" id="wt-roman">
                <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                <span class="tc-rsz-toggle-text">Roman Numerals</span>
            </label>
        </div>
        <div class="tc-input-group">
            <textarea class="tc-input tc-input--textarea" id="wt-input" rows="4" placeholder="Enter written numbers (e.g., one hundred twenty three, fifty six million)..."></textarea>
        </div>
        <div class="tc-actions">
            <button class="tc-btn tc-btn--primary" id="wt-convert">Convert</button>
        </div>
        <div class="tc-result-panel" id="wt-result" style="display:none">
            <div class="tc-result-header">
                <span class="tc-status-chip" id="wt-status">Ready</span>
            </div>
            <div class="tc-result-body" id="wt-output"></div>
        </div>
    <?php }

    protected function render_result_content(array $settings): void { ?>
        <div></div>
    <?php }
}
