<?php
/**
 * Widget: Remove Underscores
 * Premium design following case-converter pattern.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Remove_Underscores extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'remove_underscores'; }
    public function get_title(): string { return 'Remove Underscores'; }
    public function get_icon(): string { return 'eicon-minus'; }

    public function get_keywords(): array {
        return ['remove', 'underscores', 'replace', 'space', 'hyphen', 'strip', 'clean', 'text', 'underscore'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-txtp">
        <div class="tc-tool-desc">
            Remove underscores from your text. Replace them with spaces, hyphens, or strip them entirely. Works entirely in your browser — no data is sent to any server.
        </div>

        <?php $this->render_textarea('tc-ru-input', 'Paste or type text containing underscores...', 8); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <div class="tc-rsz-mode-cards tc-ru-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="space">
                        <span class="tc-rsz-mode-text"><b>Space</b><span>Replace with space</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="remove">
                        <span class="tc-rsz-mode-text"><b>Remove</b><span>Strip them out</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="hyphen">
                        <span class="tc-rsz-mode-text"><b>Hyphen</b><span>Replace with -</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="custom">
                        <span class="tc-rsz-mode-text"><b>Custom</b><span>Your own character</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section tc-ru-custom-wrap" style="display:none">
                <div class="tc-input-group">
                    <label class="tc-label">Custom Replacement</label>
                    <input type="text" class="tc-input" id="tc-ru-custom" placeholder="e.g. ." maxlength="5">
                </div>
            </div>

            <div class="tc-rsz-section">
                <div class="tc-rsz-toggles">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-ru-collapsespaces">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Collapse multiple spaces</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-ru-trim">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Trim whitespace</b></span>
                    </label>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-ru-bar', 'Processing...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-ru-convert" type="button">Remove Underscores</button>
            <button class="tc-btn tc-btn--ghost" id="tc-ru-copy" type="button">Copy Result</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-ru-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Characters</span><span class="tc-stat-value" id="tc-ru-chars">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Words</span><span class="tc-stat-value" id="tc-ru-words">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Underscores</span><span class="tc-stat-value" id="tc-ru-count">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Result Length</span><span class="tc-stat-value" id="tc-ru-result-len">0</span></div>
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
                        <textarea class="tc-textarea" id="tc-ru-preview-orig" placeholder="Original text will appear here..." readonly rows="10"></textarea>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <textarea class="tc-textarea" id="tc-ru-preview-result" placeholder="Cleaned text will appear here..." readonly rows="10"></textarea>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
