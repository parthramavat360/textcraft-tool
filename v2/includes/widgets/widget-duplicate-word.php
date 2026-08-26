<?php
/**
 * Widget: Duplicate Word Finder
 * Premium design following case-converter pattern.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Duplicate_Word extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'duplicate_word'; }
    public function get_title(): string { return 'Duplicate Word Finder'; }
    public function get_icon(): string { return 'eicon-search-bold'; }

    public function get_keywords(): array {
        return ['duplicate', 'word', 'find', 'repeat', 'frequency', 'count', 'analysis', 'text'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Find repeated words in your text and analyze word frequency. Works entirely in your browser — no data is sent to any server.
        </div>

        <?php $this->render_textarea('tc-dw-input', 'Paste or type your text here...', 8); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <div class="tc-rsz-mode-cards tc-dw-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="all">
                        <span class="tc-rsz-mode-text"><b>All Words</b></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="content">
                        <span class="tc-rsz-mode-text"><b>Content Words</b></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="custom">
                        <span class="tc-rsz-mode-text"><b>Ignore List</b></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section tc-dw-custom-wrap" style="display:none">
                <div class="tc-input-group">
                    <label class="tc-label">Words to Ignore (comma-separated)</label>
                    <input type="text" class="tc-input" id="tc-dw-ignore" placeholder="e.g. the, a, an, and, or">
                </div>
            </div>

            <div class="tc-rsz-section">
                <div class="tc-rsz-toggles">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-dw-case">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Case-sensitive</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-dw-min" data-min="2">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Min 2 letters</b><span>Ignore short words</span></span>
                    </label>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-dw-bar', 'Scanning...'); ?>

        <?php $this->render_actions('tc-dw-find', 'Find Duplicates', 'tc-dw-copy', 'Copy Results'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Characters</span><span class="tc-stat-value" id="tc-dw-chars">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Total Words</span><span class="tc-stat-value" id="tc-dw-total">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Unique Words</span><span class="tc-stat-value" id="tc-dw-unique">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Duplicates</span><span class="tc-stat-value" id="tc-dw-duplicates">0</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-dw-tags" id="tc-dw-tags"></div>
        <div class="tc-dw-freq" id="tc-dw-freq"></div>
        <?php
    }

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
                        <div><span>Analysis</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Duplicates</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original</button>
                            <button data-tab="result">Duplicates</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <textarea class="tc-textarea" id="tc-dw-preview-orig" placeholder="Original text will appear here..." readonly rows="10"></textarea>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <textarea class="tc-textarea" id="tc-dw-preview-result" placeholder="Duplicate words will appear here..." readonly rows="10"></textarea>
                    </div>
                    <?php $this->render_result_content($settings); ?>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
