<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Perplexity_Prompt_Generator extends TextCraft_Tool_Base {
    public function get_name(): string { return 'perplexity_prompt_generator'; }
    public function get_title(): string { return 'Perplexity AI Prompt Generator'; }
    public function get_icon(): string { return 'eicon-wand-magic-sparkles'; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Generate optimized prompts for Perplexity AI search engine with citations, follow-ups, and structured output.</div>
        <div class="tc-input-group">
            <label class="tc-label">Prompt Type</label>
            <?php $this->render_mode_buttons('pg-type', [
                'research' => 'Research',
                'summary' => 'Summary',
                'explain' => 'Explain',
                'compare' => 'Compare',
                'creative' => 'Creative',
                'technical' => 'Technical',
            ], 'research'); ?>
        </div>
        <div class="tc-input-group">
            <label class="tc-label">Topic / Question</label>
            <textarea class="tc-input tc-textarea" id="pg-topic" rows="3" placeholder="What do you want to search for?"></textarea>
        </div>
        <div class="tc-grid-2col">
            <div class="tc-input-group">
                <label class="tc-label">Depth</label>
                <select class="tc-select" id="pg-depth">
                    <option value="quick">Quick Answer</option>
                    <option value="detailed" selected>Detailed Analysis</option>
                    <option value="comprehensive">Comprehensive Deep Dive</option>
                </select>
            </div>
            <div class="tc-input-group">
                <label class="tc-label">Audience</label>
                <select class="tc-select" id="pg-audience">
                    <option value="general" selected>General</option>
                    <option value="expert">Expert / Technical</option>
                    <option value="student">Student</option>
                    <option value="beginner">Beginner</option>
                </select>
            </div>
        </div>
        <div class="tc-checkboxes">
            <label class="tc-check"><input type="checkbox" id="pg-citations" checked> Request Citations</label>
            <label class="tc-check"><input type="checkbox" id="pg-sources" checked> Multiple Sources</label>
            <label class="tc-check"><input type="checkbox" id="pg-followup"> Include Follow-ups</label>
        </div>
        <?php $this->render_actions('pg-generate', 'Generate Prompt'); ?>
        <div class="tctp-result" id="pg-result" style="display:none">
            <div class="tctp-rsz-tabs">
                <button class="tctp-rsz-tab sel" data-tab="prompt">Generated Prompt</button>
                <button class="tctp-rsz-tab" data-tab="tips">Tips</button>
            </div>
            <div class="tctp-rsz-tab-panel" id="pg-output"></div>
            <div class="tctp-rsz-tab-panel" id="pg-tips" style="display:none"></div>
        </div>
    <?php }
}
