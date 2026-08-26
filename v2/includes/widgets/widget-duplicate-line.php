<?php
/**
 * Widget: Duplicate Line Remover
 * Premium design following case-converter pattern.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Duplicate_Line extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'duplicate_line'; }
    public function get_title(): string { return 'Duplicate Line Remover'; }
    public function get_icon(): string { return 'eicon-editor-list-ul'; }

    public function get_keywords(): array {
        return ['duplicate', 'line', 'remove', 'dedup', 'unique', 'copy', 'clean', 'list'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Remove duplicate lines from your text. Choose how to handle matching and sorting. Works entirely in your browser — no data is sent to any server.
        </div>

        <?php $this->render_textarea('tc-dl-input', 'Paste text with duplicate lines...', 8); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <div class="tc-rsz-mode-cards tc-dl-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="keep-first">
                        <span class="tc-rsz-mode-text"><b>Keep First</b></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="keep-last">
                        <span class="tc-rsz-mode-text"><b>Keep Last</b></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="remove-all">
                        <span class="tc-rsz-mode-text"><b>Remove All</b></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <div class="tc-rsz-toggles">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-dl-case">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Case-sensitive</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-dl-trim" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Trim whitespace</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-dl-blanks">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Remove blank lines</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-dl-sort">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Sort alphabetically</b></span>
                    </label>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-dl-bar', 'Processing...'); ?>

        <?php $this->render_actions('tc-dl-remove', 'Remove Duplicates', 'tc-dl-copy', 'Copy Result'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Characters</span><span class="tc-stat-value" id="tc-dl-chars">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Total Lines</span><span class="tc-stat-value" id="tc-dl-total">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Unique</span><span class="tc-stat-value" id="tc-dl-unique">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Removed</span><span class="tc-stat-value" id="tc-dl-removed">0</span></div>
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
                        <textarea class="tc-textarea" id="tc-dl-preview-orig" placeholder="Original text will appear here..." readonly rows="10"></textarea>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <textarea class="tc-textarea" id="tc-dl-preview-result" placeholder="Cleaned text will appear here..." readonly rows="10"></textarea>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
