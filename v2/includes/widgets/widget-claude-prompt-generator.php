<?php
namespace TextCraft_Tools_Pro;

if (!defined('ABSPATH')) exit;

class Widget_Claude_Prompt_Generator extends Widget_Base {
    protected $slug = 'claude-prompt-generator';
    protected $name = 'Claude Prompt Generator';
    protected $description = 'Generate optimized prompts for Anthropic Claude AI';
    protected $icon = '\uf15c fa-robot';

    public function render($instance = [], $args = []) {
        ?>
<div class="tc-w tc-claude-gen" data-widget="claude-prompt-generator">
    <?php $this->output_header(); ?>

    <div class="tc-modes" data-group="type">
        <button class="tc-btn tc-btn--ghost sel" data-val="write">Writing</button>
        <button class="tc-btn tc-btn--ghost" data-val="analyze">Analysis</button>
        <button class="tc-btn tc-btn--ghost" data-val="code">Code</button>
        <button class="tc-btn tc-btn--ghost" data-val="brainstorm">Brainstorm</button>
        <button class="tc-btn tc-btn--ghost" data-val="roleplay">Role-play</button>
        <button class="tc-btn tc-btn--ghost" data-val="socratic">Socratic</button>
    </div>

    <div class="tc-input-group">
        <label class="tc-label">What do you want Claude to do?</label>
        <textarea class="tc-input" id="cg-topic" rows="3" placeholder="Describe your task, question, or topic..."></textarea>
    </div>

    <div class="tc-input-row">
        <div class="tc-input-group" style="flex:1">
            <label class="tc-label">Format</label>
            <select class="tc-select" id="cg-format">
                <option value="none" selected>No format preference</option>
                <option value="markdown">Markdown</option>
                <option value="table">Table</option>
                <option value="bullets">Bullet points</option>
                <option value="numbered">Numbered list</option>
                <option value="code">Code blocks</option>
            </select>
        </div>
        <div class="tc-input-group" style="flex:1">
            <label class="tc-label">Tone</label>
            <select class="tc-select" id="cg-tone">
                <option value="none" selected>Default</option>
                <option value="professional">Professional</option>
                <option value="casual">Casual</option>
                <option value="academic">Academic</option>
                <option value="creative">Creative</option>
            </select>
        </div>
    </div>

    <div class="tc-rsz-toggles">
        <label class="tc-rsz-toggle"><input type="checkbox" id="cg-step" class="tc-rsz-toggle-input" checked><span class="tc-rsz-toggle-track"></span><span class="tc-rsz-toggle-text">Think Step-by-Step</span></label>
        <label class="tc-rsz-toggle"><input type="checkbox" id="cg-examples" class="tc-rsz-toggle-input"><span class="tc-rsz-toggle-track"></span><span class="tc-rsz-toggle-text">Include Examples</span></label>
        <label class="tc-rsz-toggle"><input type="checkbox" id="cg-constraints" class="tc-rsz-toggle-input"><span class="tc-rsz-toggle-track"></span><span class="tc-rsz-toggle-text">Add Constraints</span></label>
    </div>

    <button class="tc-btn tc-btn--primary" id="cg-generate"><i class="fa-solid fa-wand-magic-sparkles"></i> Generate Prompt</button>

    <div class="tc-rsz-result-panel">
        <div class="tc-rsz-result-tab-bar">
            <button class="tc-rsz-tab sel" data-tab="prompt">Generated Prompt</button>
            <button class="tc-rsz-tab" data-tab="tips">Tips</button>
        </div>
        <div class="tc-rsz-result-tab-content sel" data-content="prompt">
            <div class="tc-rsz-result-area" id="cg-output"></div>
        </div>
        <div class="tc-rsz-result-tab-content" data-content="tips">
            <div class="tc-rsz-result-area tc-rsz-result-tips" id="cg-tips"></div>
        </div>
    </div>

    <?php $this->output_footer(); ?>
</div>
<?php
    }

    public function enqueue_scripts() {
        wp_enqueue_script('tool-claude-prompt-generator', TCTP_URL . '/assets/js/tool-claude-prompt-generator.js', ['tool-shared'], TCTP_VERSION, true);
    }
}
