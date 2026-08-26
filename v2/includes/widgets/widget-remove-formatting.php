<?php
/**
 * Widget: Remove Text Formatting
 * Premium design following case-converter pattern.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Remove_Formatting extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'remove_formatting'; }
    public function get_title(): string { return 'Remove Text Formatting'; }
    public function get_icon(): string { return 'eicon-eraser'; }

    public function get_keywords(): array {
        return ['remove', 'formatting', 'strip', 'html', 'tags', 'clean', 'text', 'unicode', 'bold', 'italic', 'plain'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Strip HTML tags, inline styles, scripts, comments, Unicode bold/italic styling, and decode entities. Works entirely in your browser — no data is sent to any server.
        </div>

        <?php $this->render_textarea('tc-rf-input', 'Paste formatted or styled text here...', 8); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <div class="tc-rsz-mode-cards tc-rf-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="all">
                        <span class="tc-rsz-mode-text"><b>All Formatting</b><span>Strip everything</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="html">
                        <span class="tc-rsz-mode-text"><b>HTML Tags</b><span>Remove &lt;tags&gt; only</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="unicode">
                        <span class="tc-rsz-mode-text"><b>Unicode Styling</b><span>Bold, italic, cursive</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="smart">
                        <span class="tc-rsz-mode-text"><b>Smart Clean</b><span>Aggressive cleaning</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <div class="tc-rsz-toggles">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-rf-decode" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Decode HTML entities</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-rf-dedup">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Remove extra spaces</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-rf-trim">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Trim whitespace</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-rf-nbsp">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Remove non-breaking spaces</b></span>
                    </label>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-rf-bar', 'Cleaning...'); ?>

        <?php $this->render_actions('tc-rf-clean', 'Clean Formatting', 'tc-rf-copy', 'Copy Result'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Characters</span><span class="tc-stat-value" id="tc-rf-chars">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Words</span><span class="tc-stat-value" id="tc-rf-words">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Tags Removed</span><span class="tc-stat-value" id="tc-rf-tags">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-rf-saved">0%</span></div>
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
                        <textarea class="tc-textarea" id="tc-rf-preview-orig" placeholder="Original text will appear here..." readonly rows="10"></textarea>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <textarea class="tc-textarea" id="tc-rf-preview-result" placeholder="Cleaned text will appear here..." readonly rows="10"></textarea>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
