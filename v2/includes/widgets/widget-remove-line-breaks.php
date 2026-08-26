<?php
/**
 * Widget: Remove Line Breaks
 * Premium design with mode cards, toggles, preview tabs, download.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Remove_Line_Breaks extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'remove_line_breaks'; }
    public function get_title(): string { return 'Remove Line Breaks'; }
    public function get_icon(): string { return 'eicon-editor-paragraph'; }

    public function get_keywords(): array {
        return ['remove', 'line', 'breaks', 'newline', 'whitespace', 'join', 'merge', 'clean', 'text cleaner'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Remove line breaks from text. Choose how to handle line endings — replace with spaces, join all lines, or preserve paragraph breaks.
        </div>

        <?php $this->render_textarea('tc-rlb-input', 'Paste text with line breaks to remove...', 8); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <div class="tc-rsz-mode-cards tc-rlb-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="spaces">
                        <span class="tc-rsz-mode-text"><b>Replace with Spaces</b></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="join">
                        <span class="tc-rsz-mode-text"><b>Join Lines</b></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="paragraphs">
                        <span class="tc-rsz-mode-text"><b>Keep Paragraphs</b></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <div class="tc-rsz-toggles">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-rlb-trim">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Trim whitespace per line</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-rlb-dedup-spaces">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Remove extra spaces</b></span>
                    </label>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-rlb-progress', 'Removing line breaks...'); ?>

        <?php $this->render_actions('tc-rlb-convert', 'Remove Breaks', 'tc-rlb-copy', 'Copy Result'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-rlb-orig">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Result</span><span class="tc-stat-value" id="tc-rlb-result-count">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Lines Removed</span><span class="tc-stat-value" id="tc-rlb-lines-removed">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-rlb-saved">0%</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    /**
     * Override result panel with Original/Converted tabs + textarea preview.
     */
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
                        <div><span>Cleaned</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Difference</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original</button>
                            <button data-tab="result">Cleaned</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <textarea class="tc-textarea" id="tc-rlb-preview-orig" placeholder="Original text will appear here..." readonly rows="10"></textarea>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <textarea class="tc-textarea" id="tc-rlb-preview-result" placeholder="Cleaned text will appear here..." readonly rows="10"></textarea>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
