<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Claude_Prompt_Generator extends TextCraft_Tool_Base {
    public function get_name(): string { return 'claude_prompt_generator'; }
    public function get_title(): string { return 'Claude Prompt Generator'; }
    public function get_icon(): string { return 'eicon-robot'; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Generate optimized prompts for Anthropic Claude AI with role-based, step-by-step, and few-shot techniques.</div>
        <div class="tc-input-group">
            <label class="tc-label">Prompt Type</label>
            <?php $this->render_mode_buttons('cg-type', [
                'write' => 'Writing',
                'analyze' => 'Analysis',
                'code' => 'Code',
                'brainstorm' => 'Brainstorm',
                'roleplay' => 'Role-play',
                'socratic' => 'Socratic',
            ], 'write'); ?>
        </div>
        <div class="tc-input-group">
            <label class="tc-label">What do you want Claude to do?</label>
            <textarea class="tc-input tc-textarea" id="cg-topic" rows="3" placeholder="Describe your task, question, or topic..."></textarea>
        </div>
        <div class="tc-grid-2col">
            <div class="tc-input-group">
                <label class="tc-label">Format</label>
                <select class="tc-select" id="cg-format">
                    <option value="none" selected>No format preference</option>
                    <option value="markdown">Markdown</option>
                    <option value="table">Table</option>
                    <option value="bullets">Bullet points</option>
                    <option value="numbered">Numbered list</option>
                </select>
            </div>
            <div class="tc-input-group">
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
        <div class="tc-checkboxes">
            <label class="tc-check"><input type="checkbox" id="cg-step" checked> Think Step-by-Step</label>
            <label class="tc-check"><input type="checkbox" id="cg-examples"> Include Examples</label>
            <label class="tc-check"><input type="checkbox" id="cg-constraints"> Add Constraints</label>
        </div>
        <?php $this->render_actions('cg-generate', 'Generate Prompt'); ?>
        <div class="tctp-result" id="cg-result" style="display:none">
            <div class="tctp-rsz-tabs">
                <button class="tctp-rsz-tab sel" data-tab="prompt">Generated Prompt</button>
                <button class="tctp-rsz-tab" data-tab="tips">Tips</button>
            </div>
            <div class="tctp-rsz-tab-panel" id="cg-output"></div>
            <div class="tctp-rsz-tab-panel" id="cg-tips" style="display:none"></div>
        </div>
    <?php }
}
