<?php
/**
 * Widget: Whitespace Remover
 * Premium design following case-converter pattern.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Whitespace_Remover extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'whitespace_remover'; }
    public function get_title(): string { return 'Whitespace Remover'; }
    public function get_icon(): string { return 'eicon-editor-removeformatting'; }

    public function get_keywords(): array {
        return ['whitespace', 'spaces', 'tabs', 'trim', 'clean', 'text', 'remove', 'extra', 'blank'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-txtp">
        <div class="tc-tool-desc">
            Clean up messy text by removing extra whitespace. Trim lines, collapse spaces, strip tabs, and remove blank lines. Works entirely in your browser — no data is sent to any server.
        </div>

        <?php $this->render_textarea('tc-ws-input', 'Paste messy text here...', 8); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <div class="tc-rsz-mode-cards tc-ws-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="smart">
                        <span class="tc-rsz-mode-text"><b>Smart Clean</b><span>Trim + collapse spaces</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="trim">
                        <span class="tc-rsz-mode-text"><b>Trim Lines</b><span>Strip leading/trailing</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="collapse">
                        <span class="tc-rsz-mode-text"><b>Collapse Spaces</b><span>Multi-space to one</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="aggressive">
                        <span class="tc-rsz-mode-text"><b>Aggressive</b><span>All whitespace removed</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <div class="tc-rsz-toggles">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-ws-tabs" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Remove tab characters</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-ws-blanklines">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Remove blank lines</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-ws-globaltrim">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Trim entire text block</b></span>
                    </label>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-ws-bar', 'Cleaning...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-ws-clean" type="button">Clean Whitespace</button>
            <button class="tc-btn tc-btn--ghost" id="tc-ws-copy" type="button">Copy Result</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-ws-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Characters</span><span class="tc-stat-value" id="tc-ws-chars">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Words</span><span class="tc-stat-value" id="tc-ws-words">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Lines</span><span class="tc-stat-value" id="tc-ws-lines">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-ws-saved">0%</span></div>
        </div>
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
                        <textarea class="tc-textarea" id="tc-ws-preview-orig" placeholder="Original text will appear here..." readonly rows="10"></textarea>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <textarea class="tc-textarea" id="tc-ws-preview-result" placeholder="Cleaned text will appear here..." readonly rows="10"></textarea>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
