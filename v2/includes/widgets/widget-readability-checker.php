<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Readability_Checker extends TextCraft_Tool_Base {
    public function get_name(): string { return 'readability_checker'; }
    public function get_title(): string { return 'Readability Checker'; }
    public function get_icon(): string { return 'eicon-read-more'; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Analyze your text readability with Flesch-Kincaid, Gunning Fog, Coleman-Liau, and other scoring algorithms. Get detailed grade levels and reading time estimates.</div>

        <div class="tc-input-group">
            <label class="tc-label">Paste your text</label>
            <textarea class="tc-input tc-textarea" id="rc-text" rows="10" placeholder="Paste your article, blog post, or content here to check readability..."></textarea>
        </div>

        <button class="tc-btn tc-btn--primary" id="rc-analyze"><i class="fa-solid fa-chart-bar"></i> Analyze Readability</button>

        <div class="tctp-result" id="rc-result" style="display:none">
            <div class="tctp-rsz-tabs">
                <button class="tctp-rsz-tab sel" data-tab="scores">Scores</button>
                <button class="tctp-rsz-tab" data-tab="stats">Statistics</button>
                <button class="tctp-rsz-tab" data-tab="tips">Improvement Tips</button>
            </div>
            <div class="tctp-rsz-tab-panel" id="rc-scores"></div>
            <div class="tctp-rsz-tab-panel" id="rc-stats" style="display:none"></div>
            <div class="tctp-rsz-tab-panel" id="rc-tips" style="display:none"></div>
        </div>
    <?php }
}
