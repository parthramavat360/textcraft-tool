<?php
/**
 * Widget: Em Dash Remover
 * Premium design following case-converter pattern.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Em_Dash_Remover extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'em_dash_remover'; }
    public function get_title(): string { return 'Em Dash Remover'; }
    public function get_icon(): string { return 'eicon-minus'; }

    public function get_keywords(): array {
        return ['em', 'dash', 'en', 'remove', 'hyphen', 'replace', 'punctuation', 'clean'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-txtp">
        <div class="tc-tool-desc">
            Remove em dashes, en dashes, and hyphens from your text. Choose what to replace them with. Works entirely in your browser — no data is sent to any server.
        </div>

        <?php $this->render_textarea('tc-edr-input', 'Paste or type your text here...', 8); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <div class="tc-rsz-mode-cards tc-edr-dash-types">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="both">
                        <span class="tc-rsz-mode-text"><b>Em + En</b><span>Both dash types</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="em">
                        <span class="tc-rsz-mode-text"><b>Em Dash</b><span>&mdash; only</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="en">
                        <span class="tc-rsz-mode-text"><b>En Dash</b><span>&ndash; only</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <div class="tc-rsz-mode-cards tc-edr-replace-types">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="remove">
                        <span class="tc-rsz-mode-text"><b>Remove</b><span>Delete dashes</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="space">
                        <span class="tc-rsz-mode-text"><b>Space</b><span>Replace with space</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="hyphen">
                        <span class="tc-rsz-mode-text"><b>Hyphen</b><span>Replace with -</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="comma">
                        <span class="tc-rsz-mode-text"><b>Comma</b><span>Replace with ,</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="custom">
                        <span class="tc-rsz-mode-text"><b>Custom</b><span>Enter your own text</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section tc-edr-custom-wrap" style="display:none">
                <div class="tc-input-group">
                    <label class="tc-label">Custom Replacement</label>
                    <input type="text" class="tc-input" id="tc-edr-custom" placeholder="e.g. -">
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-edr-bar', 'Processing...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-edr-remove" type="button">Remove Dashes</button>
            <button class="tc-btn tc-btn--ghost" id="tc-edr-copy" type="button">Copy Result</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-edr-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Characters</span><span class="tc-stat-value" id="tc-edr-chars">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Words</span><span class="tc-stat-value" id="tc-edr-words">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Em Dashes</span><span class="tc-stat-value" id="tc-edr-em">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">En Dashes</span><span class="tc-stat-value" id="tc-edr-en">0</span></div>
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
                        <textarea class="tc-textarea" id="tc-edr-preview-orig" placeholder="Original text will appear here..." readonly rows="10"></textarea>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <textarea class="tc-textarea" id="tc-edr-preview-result" placeholder="Cleaned text will appear here..." readonly rows="10"></textarea>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
