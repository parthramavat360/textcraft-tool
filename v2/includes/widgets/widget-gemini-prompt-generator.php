<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Gemini_Prompt_Generator extends TextCraft_Tool_Base {
    public function get_name(): string { return 'gemini_prompt_generator'; }
    public function get_title(): string { return 'Gemini Prompt Generator'; }
    public function get_icon(): string { return 'eicon-star'; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Generate optimized prompts for Google Gemini AI with multimodal context and reasoning support.</div>
        <div class="tc-input-group">
            <label class="tc-label">Prompt Type</label>
            <?php $this->render_mode_buttons('gg-type', [
                'explain' => 'Explain',
                'create' => 'Create',
                'plan' => 'Plan',
                'debug' => 'Debug',
                'learn' => 'Learn',
                'transform' => 'Transform',
            ], 'explain'); ?>
        </div>
        <div class="tc-input-group">
            <label class="tc-label">What do you want Gemini to help with?</label>
            <textarea class="tc-input tc-textarea" id="gg-topic" rows="3" placeholder="Describe your task, question, or goal..."></textarea>
        </div>
        <div class="tc-grid-2col">
            <div class="tc-input-group">
                <label class="tc-label">Multimodal</label>
                <select class="tc-select" id="gg-modal">
                    <option value="text" selected>Text Only</option>
                    <option value="image">Include Image Context</option>
                    <option value="code">Code-Focused</option>
                </select>
            </div>
            <div class="tc-input-group">
                <label class="tc-label">Output Length</label>
                <select class="tc-select" id="gg-length">
                    <option value="concise">Concise</option>
                    <option value="detailed" selected>Detailed</option>
                    <option value="exhaustive">Exhaustive</option>
                </select>
            </div>
        </div>
        <div class="tc-checkboxes">
            <label class="tc-check"><input type="checkbox" id="gg-reason" checked> Show Reasoning</label>
            <label class="tc-check"><input type="checkbox" id="gg-citations"> Request Sources</label>
            <label class="tc-check"><input type="checkbox" id="gg-variety"> Multiple Variations</label>
        </div>
        <?php $this->render_actions('gg-generate', 'Generate Prompt'); ?>
        <div class="tctp-result" id="gg-result" style="display:none">
            <div class="tctp-rsz-tabs">
                <button class="tctp-rsz-tab sel" data-tab="prompt">Generated Prompt</button>
                <button class="tctp-rsz-tab" data-tab="tips">Tips</button>
            </div>
            <div class="tctp-rsz-tab-panel" id="gg-output"></div>
            <div class="tctp-rsz-tab-panel" id="gg-tips" style="display:none"></div>
        </div>
    <?php }
}
