<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Prompt_Optimizer extends TextCraft_Tool_Base {
    public function get_name(): string { return 'prompt_optimizer'; }
    public function get_title(): string { return 'Prompt Optimizer'; }
    public function get_icon(): string { return 'eicon-bolt'; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Optimize and refine any prompt for better AI results across ChatGPT, Claude, Gemini, and more.</div>
        <div class="tc-input-group">
            <label class="tc-label">Paste your raw prompt</label>
            <textarea class="tc-input tc-textarea" id="po-input" rows="5" placeholder="Paste your existing prompt here and we'll optimize it..."></textarea>
        </div>
        <div class="tc-grid-2col">
            <div class="tc-input-group">
                <label class="tc-label">Target AI</label>
                <select class="tc-select" id="po-target">
                    <option value="general" selected>General (Any AI)</option>
                    <option value="chatgpt">ChatGPT</option>
                    <option value="claude">Claude</option>
                    <option value="gemini">Gemini</option>
                    <option value="midjourney">Midjourney</option>
                </select>
            </div>
            <div class="tc-input-group">
                <label class="tc-label">Optimize For</label>
                <select class="tc-select" id="po-goal">
                    <option value="clarity" selected>Clarity</option>
                    <option value="specificity">Specificity</option>
                    <option value="creativity">Creativity</option>
                    <option value="precision">Precision</option>
                    <option value="engagement">Engagement</option>
                </select>
            </div>
        </div>
        <div class="tc-checkboxes">
            <label class="tc-check"><input type="checkbox" id="po-context" checked> Add Context</label>
            <label class="tc-check"><input type="checkbox" id="po-structure" checked> Improve Structure</label>
            <label class="tc-check"><input type="checkbox" id="po-examples"> Add Examples</label>
        </div>
        <?php $this->render_actions('po-optimize', 'Optimize Prompt'); ?>
        <div class="tctp-result" id="po-result" style="display:none">
            <div class="tctp-rsz-tabs">
                <button class="tctp-rsz-tab sel" data-tab="optimized">Optimized Prompt</button>
                <button class="tctp-rsz-tab" data-tab="changes">What Changed</button>
            </div>
            <div class="tctp-rsz-tab-panel" id="po-output"></div>
            <div class="tctp-rsz-tab-panel" id="po-changes" style="display:none"></div>
        </div>
    <?php }
}
