<?php
namespace TextCraft_Tools_Pro;

if (!defined('ABSPATH')) exit;

class Widget_Perplexity_Prompt_Generator extends Widget_Base {
    protected $slug = 'perplexity-prompt-generator';
    protected $name = 'Perplexity AI Prompt Generator';
    protected $description = 'Generate optimized prompts for Perplexity AI search engine';
    protected $icon = '\uf15c fa-wand-magic-sparkles';

    public function render($instance = [], $args = []) {
        ?>
<div class="tc-w tc-prompt-gen" data-widget="perplexity-prompt-generator">
    <?php $this->output_header(); ?>

    <div class="tc-modes" data-group="type">
        <button class="tc-btn tc-btn--ghost sel" data-val="research">Research</button>
        <button class="tc-btn tc-btn--ghost" data-val="summary">Summary</button>
        <button class="tc-btn tc-btn--ghost" data-val="explain">Explain</button>
        <button class="tc-btn tc-btn--ghost" data-val="compare">Compare</button>
        <button class="tc-btn tc-btn--ghost" data-val="creative">Creative</button>
        <button class="tc-btn tc-btn--ghost" data-val="technical">Technical</button>
    </div>

    <div class="tc-input-group">
        <label class="tc-label">Topic / Question</label>
        <textarea class="tc-input" id="pg-topic" rows="3" placeholder="What do you want to search for?"></textarea>
    </div>

    <div class="tc-input-row">
        <div class="tc-input-group" style="flex:1">
            <label class="tc-label">Depth</label>
            <select class="tc-select" id="pg-depth">
                <option value="quick">Quick Answer</option>
                <option value="detailed" selected>Detailed Analysis</option>
                <option value="comprehensive">Comprehensive Deep Dive</option>
            </select>
        </div>
        <div class="tc-input-group" style="flex:1">
            <label class="tc-label">Audience</label>
            <select class="tc-select" id="pg-audience">
                <option value="general" selected>General</option>
                <option value="expert">Expert / Technical</option>
                <option value="student">Student</option>
                <option value="beginner">Beginner</option>
            </select>
        </div>
    </div>

    <div class="tc-rsz-toggles">
        <label class="tc-rsz-toggle"><input type="checkbox" id="pg-citations" class="tc-rsz-toggle-input" checked><span class="tc-rsz-toggle-track"></span><span class="tc-rsz-toggle-text">Request Citations</span></label>
        <label class="tc-rsz-toggle"><input type="checkbox" id="pg-sources" class="tc-rsz-toggle-input" checked><span class="tc-rsz-toggle-track"></span><span class="tc-rsz-toggle-text">Multiple Sources</span></label>
        <label class="tc-rsz-toggle"><input type="checkbox" id="pg-followup" class="tc-rsz-toggle-input"><span class="tc-rsz-toggle-track"></span><span class="tc-rsz-toggle-text">Include Follow-ups</span></label>
    </div>

    <button class="tc-btn tc-btn--primary" id="pg-generate"><i class="fa-solid fa-wand-magic-sparkles"></i> Generate Prompt</button>

    <div class="tc-rsz-result-panel">
        <div class="tc-rsz-result-tab-bar">
            <button class="tc-rsz-tab sel" data-tab="prompt">Generated Prompt</button>
            <button class="tc-rsz-tab" data-tab="tips">Tips</button>
        </div>
        <div class="tc-rsz-result-tab-content sel" data-content="prompt">
            <div class="tc-rsz-result-area" id="pg-output"></div>
        </div>
        <div class="tc-rsz-result-tab-content" data-content="tips">
            <div class="tc-rsz-result-area tc-rsz-result-tips" id="pg-tips"></div>
        </div>
    </div>

    <?php $this->output_footer(); ?>
</div>
<?php
    }

    public function enqueue_scripts() {
        wp_enqueue_script('tool-perplexity-prompt-generator', TCTP_URL . '/assets/js/tool-perplexity-prompt-generator.js', ['tool-shared'], TCTP_VERSION, true);
    }
}
