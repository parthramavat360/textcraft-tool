<?php
/**
 * Widget: Text Diff
 * Side-by-side text comparison with highlighted differences.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Text_Diff extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'text_diff'; }
    public function get_title(): string { return 'Text Diff'; }
    public function get_icon(): string { return 'eicon-dual-button'; }

    public function get_keywords(): array {
        return ['text diff', 'text compare', 'compare texts', 'diff tool', 'text difference', 'compare documents'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Compare two texts side-by-side. Differences are highlighted line by line. Perfect for checking revisions, code changes, or document edits.
        </div>

        <div class="tc-diff-grid">
            <div class="tc-diff-col">
                <h4 class="tc-diff-col-title">Original Text</h4>
                <textarea class="tc-textarea tc-diff-textarea" id="tc-diff-left" placeholder="Paste the original text here..." rows="10"></textarea>
            </div>
            <div class="tc-diff-col">
                <h4 class="tc-diff-col-title">Changed Text</h4>
                <textarea class="tc-textarea tc-diff-textarea" id="tc-diff-right" placeholder="Paste the modified text here..." rows="10"></textarea>
            </div>
        </div>

        <div class="tc-diff-options">
            <label class="tc-rsz-toggle">
                <input type="checkbox" class="tc-rsz-toggle-input" id="tc-diff-ignore-case">
                <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                <span class="tc-rsz-toggle-text"><b>Ignore case</b></span>
            </label>
            <label class="tc-rsz-toggle">
                <input type="checkbox" class="tc-rsz-toggle-input" id="tc-diff-ignore-space">
                <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                <span class="tc-rsz-toggle-text"><b>Ignore whitespace</b></span>
            </label>
            <label class="tc-rsz-toggle">
                <input type="checkbox" class="tc-rsz-toggle-input" id="tc-diff-word-level" checked>
                <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                <span class="tc-rsz-toggle-text"><b>Word-level diff</b></span>
            </label>
        </div>

        <div class="tc-diff-actions">
            <button class="tc-btn tc-btn--accent" id="tc-diff-compare" type="button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 3h5v5"/><path d="M8 3H3v5"/><path d="M12 22v-8.3a4 4 0 0 0-1.172-2.872L3 3"/><path d="m15 9 6-6"/></svg>
                Compare
            </button>
            <button class="tc-btn tc-btn--ghost" id="tc-diff-swap" type="button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="7 16 3 12 7 8"/><polyline points="17 8 21 12 17 16"/><line x1="3" y1="12" x2="21" y2="12"/></svg>
                Swap
            </button>
            <button class="tc-btn tc-btn--ghost" id="tc-diff-clear" type="button">Clear</button>
        </div>

        <div class="tc-diff-stats" id="tc-diff-stats" style="display:none">
            <div class="tc-diff-stat-card added">
                <span class="tc-diff-stat-num" id="tc-diff-stat-added">0</span>
                <span class="tc-diff-stat-label">Lines Added</span>
            </div>
            <div class="tc-diff-stat-card removed">
                <span class="tc-diff-stat-num" id="tc-diff-stat-removed">0</span>
                <span class="tc-diff-stat-label">Lines Removed</span>
            </div>
            <div class="tc-diff-stat-card unchanged">
                <span class="tc-diff-stat-num" id="tc-diff-stat-unchanged">0</span>
                <span class="tc-diff-stat-label">Unchanged</span>
            </div>
        </div>

        <div class="tc-diff-result" id="tc-diff-result" style="display:none">
            <div class="tc-diff-result-header">
                <h4>Differences</h4>
                <div class="tc-diff-legend">
                    <span class="tc-diff-legend-item added">+ Added</span>
                    <span class="tc-diff-legend-item removed">- Removed</span>
                    <span class="tc-diff-legend-item unchanged">= Same</span>
                </div>
            </div>
            <div class="tc-diff-output" id="tc-diff-output"></div>
        </div>

        <?php
    }

    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 · Differences</h3>
                    <span id="tc-status-chip">Ready</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-stats">
                        <div><span>Added</span><b id="tc-stat-orig">0</b></div>
                        <div><span>Removed</span><b id="tc-stat-comp">0</b></div>
                        <div class="saved"><span>Unchanged</span><b id="tc-stat-saved">0</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Result</h4>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <div id="tc-diff-preview" class="tc-diff-preview-box">
                            <p class="tc-diff-placeholder">Paste two texts and click Compare</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
