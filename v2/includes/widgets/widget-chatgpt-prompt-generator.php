<?php
namespace TextCraft_Tools_Pro;

if (!defined('ABSPATH')) exit;

class Widget_Chatgpt_Prompt_Generator extends Widget_Base {
    protected $slug = 'chatgpt-prompt-generator';
    protected $name = 'ChatGPT Prompt Generator';
    protected $description = 'Generate optimized prompts for ChatGPT';
    protected $icon = '\uf15c fa-comments';

    public function render($instance = [], $args = []) {
        ?>
<div class="tc-w tc-chatgpt-gen" data-widget="chatgpt-prompt-generator">
    <?php $this->output_header(); ?>

    <div class="tc-modes" data-group="type">
        <button class="tc-btn tc-btn--ghost sel" data-val="general">General</button>
        <button class="tc-btn tc-btn--ghost" data-val="writing">Writing</button>
        <button class="tc-btn tc-btn--ghost" data-val="coding">Coding</button>
        <button class="tc-btn tc-btn--ghost" data-val="marketing">Marketing</button>
        <button class="tc-btn tc-btn--ghost" data-val="education">Education</button>
        <button class="tc-btn tc-btn--ghost" data-val="business">Business</button>
    </div>

    <div class="tc-input-group">
        <label class="tc-label">What do you want ChatGPT to do?</label>
        <textarea class="tc-input" id="cgt-topic" rows="3" placeholder="Describe your task, question, or goal..."></textarea>
    </div>

    <div class="tc-input-row">
        <div class="tc-input-group" style="flex:1">
            <label class="tc-label">Role</label>
            <select class="tc-select" id="cgt-role">
                <option value="none" selected>No specific role</option>
                <option value="expert">Domain Expert</option>
                <option value="teacher">Teacher/Tutor</option>
                <option value="copywriter">Copywriter</option>
                <option value="developer">Developer</option>
                <option value="strategist">Strategist</option>
            </select>
        </div>
        <div class="tc-input-group" style="flex:1">
            <label class="tc-label">Detail Level</label>
            <select class="tc-select" id="cgt-detail">
                <option value="brief">Brief</option>
                <option value="moderate" selected>Moderate</option>
                <option value="thorough">Thorough</option>
            </select>
        </div>
    </div>

    <div class="tc-rsz-toggles">
        <label class="tc-rsz-toggle"><input type="checkbox" id="cgt-persona" class="tc-rsz-toggle-input"><span class="tc-rsz-toggle-track"></span><span class="tc-rsz-toggle-text">Add Persona</span></label>
        <label class="tc-rsz-toggle"><input type="checkbox" id="cgt-fewshot" class="tc-rsz-toggle-input"><span class="tc-rsz-toggle-track"></span><span class="tc-rsz-toggle-text">Few-Shot Examples</span></label>
        <label class="tc-rsz-toggle"><input type="checkbox" id="cgt-chain" class="tc-rsz-toggle-input" checked><span class="tc-rsz-toggle-track"></span><span class="tc-rsz-toggle-text">Chain of Thought</span></label>
    </div>

    <button class="tc-btn tc-btn--primary" id="cgt-generate"><i class="fa-solid fa-wand-magic-sparkles"></i> Generate Prompt</button>

    <div class="tc-rsz-result-panel">
        <div class="tc-rsz-result-tab-bar">
            <button class="tc-rsz-tab sel" data-tab="prompt">Generated Prompt</button>
            <button class="tc-rsz-tab" data-tab="tips">Tips</button>
        </div>
        <div class="tc-rsz-result-tab-content sel" data-content="prompt">
            <div class="tc-rsz-result-area" id="cgt-output"></div>
        </div>
        <div class="tc-rsz-result-tab-content" data-content="tips">
            <div class="tc-rsz-result-area tc-rsz-result-tips" id="cgt-tips"></div>
        </div>
    </div>

    <?php $this->output_footer(); ?>
</div>
<?php
    }

    public function enqueue_scripts() {
        wp_enqueue_script('tool-chatgpt-prompt-generator', TCTP_URL . '/assets/js/tool-chatgpt-prompt-generator.js', ['tool-shared'], TCTP_VERSION, true);
    }
}
