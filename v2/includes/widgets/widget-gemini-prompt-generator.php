<?php
namespace TextCraft_Tools_Pro;

if (!defined('ABSPATH')) exit;

class Widget_Gemini_Prompt_Generator extends Widget_Base {
    protected $slug = 'gemini-prompt-generator';
    protected $name = 'Gemini Prompt Generator';
    protected $description = 'Generate optimized prompts for Google Gemini AI';
    protected $icon = '\uf15c fa-star';

    public function render($instance = [], $args = []) {
        ?>
<div class="tc-w tc-gemini-gen" data-widget="gemini-prompt-generator">
    <?php $this->output_header(); ?>

    <div class="tc-modes" data-group="type">
        <button class="tc-btn tc-btn--ghost sel" data-val="explain">Explain</button>
        <button class="tc-btn tc-btn--ghost" data-val="create">Create</button>
        <button class="tc-btn tc-btn--ghost" data-val="plan">Plan</button>
        <button class="tc-btn tc-btn--ghost" data-val="debug">Debug</button>
        <button class="tc-btn tc-btn--ghost" data-val="learn">Learn</button>
        <button class="tc-btn tc-btn--ghost" data-val="transform">Transform</button>
    </div>

    <div class="tc-input-group">
        <label class="tc-label">What do you want Gemini to help with?</label>
        <textarea class="tc-input" id="gg-topic" rows="3" placeholder="Describe your task, question, or goal..."></textarea>
    </div>

    <div class="tc-input-row">
        <div class="tc-input-group" style="flex:1">
            <label class="tc-label">Multimodal</label>
            <select class="tc-select" id="gg-modal">
                <option value="text" selected>Text Only</option>
                <option value="image">Include Image Context</option>
                <option value="code">Code-Focused</option>
            </select>
        </div>
        <div class="tc-input-group" style="flex:1">
            <label class="tc-label">Output Length</label>
            <select class="tc-select" id="gg-length">
                <option value="concise">Concise</option>
                <option value="detailed" selected>Detailed</option>
                <option value="exhaustive">Exhaustive</option>
            </select>
        </div>
    </div>

    <div class="tc-rsz-toggles">
        <label class="tc-rsz-toggle"><input type="checkbox" id="gg-reason" class="tc-rsz-toggle-input" checked><span class="tc-rsz-toggle-track"></span><span class="tc-rsz-toggle-text">Show Reasoning</span></label>
        <label class="tc-rsz-toggle"><input type="checkbox" id="gg-citations" class="tc-rsz-toggle-input"><span class="tc-rsz-toggle-track"></span><span class="tc-rsz-toggle-text">Request Sources</span></label>
        <label class="tc-rsz-toggle"><input type="checkbox" id="gg-variety" class="tc-rsz-toggle-input"><span class="tc-rsz-toggle-track"></span><span class="tc-rsz-toggle-text">Multiple Variations</span></label>
    </div>

    <button class="tc-btn tc-btn--primary" id="gg-generate"><i class="fa-solid fa-wand-magic-sparkles"></i> Generate Prompt</button>

    <div class="tc-rsz-result-panel">
        <div class="tc-rsz-result-tab-bar">
            <button class="tc-rsz-tab sel" data-tab="prompt">Generated Prompt</button>
            <button class="tc-rsz-tab" data-tab="tips">Tips</button>
        </div>
        <div class="tc-rsz-result-tab-content sel" data-content="prompt">
            <div class="tc-rsz-result-area" id="gg-output"></div>
        </div>
        <div class="tc-rsz-result-tab-content" data-content="tips">
            <div class="tc-rsz-result-area tc-rsz-result-tips" id="gg-tips"></div>
        </div>
    </div>

    <?php $this->output_footer(); ?>
</div>
<?php
    }

    public function enqueue_scripts() {
        wp_enqueue_script('tool-gemini-prompt-generator', TCTP_URL . '/assets/js/tool-gemini-prompt-generator.js', ['tool-shared'], TCTP_VERSION, true);
    }
}
