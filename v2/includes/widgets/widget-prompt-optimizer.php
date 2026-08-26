<?php
namespace TextCraft_Tools_Pro;

if (!defined('ABSPATH')) exit;

class Widget_Prompt_Optimizer extends Widget_Base {
    protected $slug = 'prompt-optimizer';
    protected $name = 'Prompt Optimizer';
    protected $description = 'Optimize and refine any prompt for better AI results';
    protected $icon = '\uf15c fa-bolt';

    public function render($instance = [], $args = []) {
        ?>
<div class="tc-w tc-prompt-opt" data-widget="prompt-optimizer">
    <?php $this->output_header(); ?>

    <div class="tc-input-group">
        <label class="tc-label">Paste your raw prompt</label>
        <textarea class="tc-input" id="po-input" rows="5" placeholder="Paste your existing prompt here and we'll optimize it for better results..."></textarea>
    </div>

    <div class="tc-input-row">
        <div class="tc-input-group" style="flex:1">
            <label class="tc-label">Target AI</label>
            <select class="tc-select" id="po-target">
                <option value="general" selected>General (Any AI)</option>
                <option value="chatgpt">ChatGPT</option>
                <option value="claude">Claude</option>
                <option value="gemini">Gemini</option>
                <option value="midjourney">Midjourney</option>
                <option value="stable-diffusion">Stable Diffusion</option>
            </select>
        </div>
        <div class="tc-input-group" style="flex:1">
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

    <div class="tc-rsz-toggles">
        <label class="tc-rsz-toggle"><input type="checkbox" id="po-context" class="tc-rsz-toggle-input" checked><span class="tc-rsz-toggle-track"></span><span class="tc-rsz-toggle-text">Add Context</span></label>
        <label class="tc-rsz-toggle"><input type="checkbox" id="po-structure" class="tc-rsz-toggle-input" checked><span class="tc-rsz-toggle-track"></span><span class="tc-rsz-toggle-text">Improve Structure</span></label>
        <label class="tc-rsz-toggle"><input type="checkbox" id="po-examples" class="tc-rsz-toggle-input"><span class="tc-rsz-toggle-track"></span><span class="tc-rsz-toggle-text">Add Examples</span></label>
    </div>

    <button class="tc-btn tc-btn--primary" id="po-optimize"><i class="fa-solid fa-bolt"></i> Optimize Prompt</button>

    <div class="tc-rsz-result-panel">
        <div class="tc-rsz-result-tab-bar">
            <button class="tc-rsz-tab sel" data-tab="optimized">Optimized Prompt</button>
            <button class="tc-rsz-tab" data-tab="changes">What Changed</button>
        </div>
        <div class="tc-rsz-result-tab-content sel" data-content="optimized">
            <div class="tc-rsz-result-area" id="po-output"></div>
        </div>
        <div class="tc-rsz-result-tab-content" data-content="changes">
            <div class="tc-rsz-result-area tc-rsz-result-tips" id="po-changes"></div>
        </div>
    </div>

    <?php $this->output_footer(); ?>
</div>
<?php
    }

    public function enqueue_scripts() {
        wp_enqueue_script('tool-prompt-optimizer', TCTP_URL . '/assets/js/tool-prompt-optimizer.js', ['tool-shared'], TCTP_VERSION, true);
    }
}
