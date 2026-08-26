<?php
/**
 * Widget: Wingdings Converter
 * Premium design following case-converter pattern.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Wingdings extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'wingdings'; }
    public function get_title(): string { return 'Wingdings Converter'; }
    public function get_icon(): string { return 'eicon-font'; }

    public function get_keywords(): array {
        return ['wingdings', 'symbols', 'convert', 'text', 'emoji', 'font', 'decorative', 'special'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert text to Wingdings symbols and back. Create decorative text, symbol lists, or decode Wingdings content. Works entirely in your browser — no data is sent to any server.
        </div>

        <?php $this->render_textarea('tc-wd-input', 'Type or paste text to convert...', 8); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <div class="tc-rsz-mode-cards tc-wd-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="to_wingdings">
                        <span class="tc-rsz-mode-text"><b>Text &rarr; Wingdings</b><span>Convert to symbols</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="from_wingdings">
                        <span class="tc-rsz-mode-text"><b>Wingdings &rarr; Text</b><span>Decode symbols</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <div class="tc-rsz-toggles">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-wd-preserve-spaces" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Preserve spaces</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-wd-preserve-newlines" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Preserve line breaks</b></span>
                    </label>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-wd-bar', 'Converting...'); ?>

        <?php $this->render_actions('tc-wd-convert', 'Convert', 'tc-wd-copy', 'Copy Result'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Characters</span><span class="tc-stat-value" id="tc-wd-chars">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Words</span><span class="tc-stat-value" id="tc-wd-words">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Converted</span><span class="tc-stat-value" id="tc-wd-converted">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Skipped</span><span class="tc-stat-value" id="tc-wd-skipped">0</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Result</h3>
                    <span id="tc-status-chip">Idle</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-stats">
                        <div><span>Original</span><b id="tc-stat-orig">&mdash;</b></div>
                        <div><span>Converted</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Difference</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original</button>
                            <button data-tab="result">Converted</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <textarea class="tc-textarea" id="tc-wd-preview-orig" placeholder="Original text will appear here..." readonly rows="10"></textarea>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <textarea class="tc-textarea" id="tc-wd-preview-result" placeholder="Converted text will appear here..." readonly rows="10"></textarea>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
