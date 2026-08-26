<?php
/**
 * Widget: Character Remover
 * Premium design following case-converter pattern.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Character_Remover extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'character_remover'; }
    public function get_title(): string { return 'Character Remover'; }
    public function get_icon(): string { return 'eicon-cursor-move'; }

    public function get_keywords(): array {
        return ['character', 'remover', 'delete', 'strip', 'clean', 'text', 'punctuation', 'spaces', 'special'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Remove unwanted characters from your text. Choose a quick preset or enter custom characters to strip. Works entirely in your browser — no data is sent to any server.
        </div>

        <?php $this->render_textarea('tc-cr-input', 'Paste or type your text here...', 8); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <div class="tc-rsz-mode-cards tc-cr-presets">
                    <button class="tc-rsz-mode-card sel" type="button" data-chars=" ">
                        <span class="tc-rsz-mode-text"><b>Spaces</b></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-chars=".,;:!?">
                        <span class="tc-rsz-mode-text"><b>Punctuation</b></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-chars="0123456789">
                        <span class="tc-rsz-mode-text"><b>Numbers</b></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-chars="@#$%^&*(){}[]|<>~`">
                        <span class="tc-rsz-mode-text"><b>Special</b></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-chars="&quot;'">
                        <span class="tc-rsz-mode-text"><b>Quotes</b></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-chars="custom">
                        <span class="tc-rsz-mode-text"><b>Custom</b></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section tc-cr-custom-wrap" style="display:none">
                <div class="tc-input-group">
                    <label class="tc-label">Custom Characters</label>
                    <input type="text" class="tc-input" id="tc-cr-custom" placeholder="e.g. @#$%^&*">
                </div>
            </div>

            <div class="tc-rsz-section">
                <div class="tc-rsz-toggles">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-cr-case">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Case-sensitive</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-cr-trim">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Trim whitespace</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-cr-dedup">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Remove extra spaces</b></span>
                    </label>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-cr-bar', 'Removing...'); ?>

        <?php $this->render_actions('tc-cr-remove', 'Remove Characters', 'tc-cr-copy', 'Copy Result'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Characters</span><span class="tc-stat-value" id="tc-cr-chars">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Words</span><span class="tc-stat-value" id="tc-cr-words">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Removed</span><span class="tc-stat-value" id="tc-cr-removed">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Result Length</span><span class="tc-stat-value" id="tc-cr-result-len">0</span></div>
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
                        <textarea class="tc-textarea" id="tc-cr-preview-orig" placeholder="Original text will appear here..." readonly rows="10"></textarea>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <textarea class="tc-textarea" id="tc-cr-preview-result" placeholder="Cleaned text will appear here..." readonly rows="10"></textarea>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
