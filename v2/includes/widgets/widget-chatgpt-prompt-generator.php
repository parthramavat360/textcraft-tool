<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Chatgpt_Prompt_Generator extends TextCraft_Tool_Base {
    public function get_name(): string { return 'chatgpt_prompt_generator'; }
    public function get_title(): string { return 'ChatGPT Prompt Generator'; }
    public function get_icon(): string { return 'eicon-comments'; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Generate optimized prompts for ChatGPT with persona, few-shot examples, and chain-of-thought techniques.</div>
        <div class="tc-input-group">
            <label class="tc-label">Prompt Type</label>
            <?php $this->render_mode_buttons('cgt-type', [
                'general' => 'General',
                'writing' => 'Writing',
                'coding' => 'Coding',
                'marketing' => 'Marketing',
                'education' => 'Education',
                'business' => 'Business',
            ], 'general'); ?>
        </div>
        <div class="tc-input-group">
            <label class="tc-label">What do you want ChatGPT to do?</label>
            <textarea class="tc-input tc-textarea" id="cgt-topic" rows="3" placeholder="Describe your task, question, or goal..."></textarea>
        </div>
        <div class="tc-grid-2col">
            <div class="tc-input-group">
                <label class="tc-label">Role</label>
                <select class="tc-select" id="cgt-role">
                    <option value="none" selected>No specific role</option>
                    <option value="expert">Domain Expert</option>
                    <option value="teacher">Teacher/Tutor</option>
                    <option value="copywriter">Copywriter</option>
                    <option value="developer">Developer</option>
                </select>
            </div>
            <div class="tc-input-group">
                <label class="tc-label">Detail Level</label>
                <select class="tc-select" id="cgt-detail">
                    <option value="brief">Brief</option>
                    <option value="moderate" selected>Moderate</option>
                    <option value="thorough">Thorough</option>
                </select>
            </div>
        </div>
        <div class="tc-checkboxes">
            <label class="tc-check"><input type="checkbox" id="cgt-persona"> Add Persona</label>
            <label class="tc-check"><input type="checkbox" id="cgt-fewshot"> Few-Shot Examples</label>
            <label class="tc-check"><input type="checkbox" id="cgt-chain" checked> Chain of Thought</label>
        </div>
        <?php $this->render_actions('cgt-generate', 'Generate Prompt'); ?>
        <div class="tctp-result" id="cgt-result" style="display:none">
            <div class="tctp-rsz-tabs">
                <button class="tctp-rsz-tab sel" data-tab="prompt">Generated Prompt</button>
                <button class="tctp-rsz-tab" data-tab="tips">Tips</button>
            </div>
            <div class="tctp-rsz-tab-panel" id="cgt-output"></div>
            <div class="tctp-rsz-tab-panel" id="cgt-tips" style="display:none"></div>
        </div>
    <?php }
}
