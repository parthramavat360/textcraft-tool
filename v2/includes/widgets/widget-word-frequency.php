<?php
/**
 * Widget: Word Frequency Counter
 * Premium design following case-converter pattern.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Word_Frequency extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'word_frequency'; }
    public function get_title(): string { return 'Word Frequency Counter'; }
    public function get_icon(): string { return 'eicon-chart-bar'; }

    public function get_keywords(): array {
        return ['word', 'frequency', 'count', 'analyze', 'text', 'occurrence', 'frequency', 'statistics'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Analyze text to count word frequencies. See how often each word appears, sorted by count, with percentages and visual bars. Works entirely in your browser — no data is sent to any server.
        </div>

        <?php $this->render_textarea('tc-wf-input', 'Paste text to analyze word frequency...', 8); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <div class="tc-rsz-mode-cards tc-wf-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="content">
                        <span class="tc-rsz-mode-text"><b>Content Words</b><span>Ignore common words</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="all">
                        <span class="tc-rsz-mode-text"><b>All Words</b><span>Count everything</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="custom">
                        <span class="tc-rsz-mode-text"><b>Custom Ignore</b><span>Set your own list</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section tc-wf-ignore-wrap" style="display:none">
                <div class="tc-input-group">
                    <label class="tc-label">Ignore Words (comma-separated)</label>
                    <input type="text" class="tc-input" id="tc-wf-ignore-list" placeholder="e.g. the, is, and, of">
                </div>
            </div>

            <div class="tc-rsz-section">
                <div class="tc-rsz-toggles">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-wf-case">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Case-sensitive counting</b></span>
                    </label>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-wf-bar', 'Analyzing...'); ?>

        <?php $this->render_actions('tc-wf-analyze', 'Analyze Frequency', 'tc-wf-copy', 'Copy Results'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Characters</span><span class="tc-stat-value" id="tc-wf-chars">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Words</span><span class="tc-stat-value" id="tc-wf-words">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Unique Words</span><span class="tc-stat-value" id="tc-wf-unique">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Most Common</span><span class="tc-stat-value" id="tc-wf-top">&mdash;</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-wf-freq" id="tc-wf-freq"></div>
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
                        <div><span>Unique Words</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Most Common</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original</button>
                            <button data-tab="result">Frequency</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <textarea class="tc-textarea" id="tc-wf-preview-orig" placeholder="Original text will appear here..." readonly rows="10"></textarea>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <?php $this->render_result_content($settings); ?>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
