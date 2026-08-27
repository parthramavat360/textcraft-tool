<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Text_Diff extends TextCraft_Tool_Base {
    public function get_name(): string { return 'text_diff'; }
    public function get_title(): string { return 'Text Diff / Compare'; }
    public function get_icon(): string { return 'eicon-random-replace'; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Compare two texts side by side and instantly see the differences highlighted. Perfect for proofreading, code review, and content comparison.</div>

        <div class="tc-grid-2col">
            <div class="tc-input-group">
                <label class="tc-label">Original Text</label>
                <textarea class="tc-input tc-textarea" id="diff-a" rows="12" placeholder="Paste the original text here..."></textarea>
            </div>
            <div class="tc-input-group">
                <label class="tc-label">Modified Text</label>
                <textarea class="tc-input tc-textarea" id="diff-b" rows="12" placeholder="Paste the modified text here..."></textarea>
            </div>
        </div>

        <div class="tc-flex" style="gap:8px;align-items:center">
            <button class="tc-btn tc-btn--primary" id="diff-compare"><i class="fa-solid fa-code-compare"></i> Compare Texts</button>
            <label class="tc-check"><input type="checkbox" id="diff-ignore-case"> Ignore case</label>
            <label class="tc-check"><input type="checkbox" id="diff-ignore-space" checked> Ignore whitespace</label>
        </div>

        <div class="tctp-result" id="diff-result" style="display:none">
            <div class="tctp-rsz-tabs">
                <button class="tctp-rsz-tab sel" data-tab="visual">Visual Diff</button>
                <button class="tctp-rsz-tab" data-tab="unified">Unified Diff</button>
                <button class="tctp-rsz-tab" data-tab="stats">Statistics</button>
            </div>
            <div class="tctp-rsz-tab-panel" id="diff-visual"></div>
            <div class="tctp-rsz-tab-panel" id="diff-unified" style="display:none"></div>
            <div class="tctp-rsz-tab-panel" id="diff-stats" style="display:none"></div>
        </div>
    <?php }
}
