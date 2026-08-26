<?php
/**
 * Widget: JSON Formatter & Validator
 * Premium design following case-converter pattern.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_JSON_Formatter extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'json_formatter'; }
    public function get_title(): string { return 'JSON Formatter & Validator'; }
    public function get_icon(): string { return 'eicon-code'; }

    public function get_keywords(): array {
        return ['json', 'format', 'validate', 'beautify', 'minify', 'prettify', 'lint', 'code'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <textarea class="tc-textarea" id="tc-jf-input" placeholder='Paste your JSON here...\n\nExample: {"name": "John", "age": 30}' rows="8" autocomplete="new-password" spellcheck="false" data-form-type="other"></textarea>

        <div class="tc-tool-desc" style="margin-top:4px;font-size:12px;color:#94a3b8">
            Paste raw JSON to format, validate, or minify. Works 100% in your browser — no data leaves your device.
        </div>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <div class="tc-rsz-mode-cards tc-jf-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="format">
                        <span class="tc-rsz-mode-text"><b>Format</b><span>Pretty-print JSON</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="minify">
                        <span class="tc-rsz-mode-text"><b>Minify</b><span>Compress JSON</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="validate">
                        <span class="tc-rsz-mode-text"><b>Validate</b><span>Check syntax only</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <div class="tc-rsz-toggles">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-jf-sort-keys">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Sort keys alphabetically</b></span>
                    </label>
                </div>
            </div>

            <div class="tc-rsz-section">
                <div class="tc-input-group">
                    <label class="tc-label">Indent Size</label>
                    <?php $this->render_select('tc-jf-indent', [
                        '2'    => '2 Spaces',
                        '4'    => '4 Spaces',
                        'tab'  => 'Tab Character',
                    ]); ?>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-jf-bar', 'Formatting...'); ?>

        <?php $this->render_actions('tc-jf-apply', 'Apply', 'tc-jf-copy', 'Copy Results'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Input Size</span><span class="tc-stat-value" id="tc-jf-input-size">0 chars</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Output Size</span><span class="tc-stat-value" id="tc-jf-output-size">0 chars</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Size Change</span><span class="tc-stat-value" id="tc-jf-size-change">&mdash;</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Status</span><span class="tc-stat-value" id="tc-jf-status-val">Idle</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <textarea class="tc-textarea" id="tc-jf-result-text" placeholder="Formatted JSON will appear here..." readonly rows="10"></textarea>
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
                        <div><span>Input</span><b id="tc-stat-orig">&mdash;</b></div>
                        <div><span>Output</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Saved</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original</button>
                            <button data-tab="result">Formatted</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <textarea class="tc-textarea" id="tc-jf-preview-orig" placeholder="Original JSON will appear here..." readonly rows="10"></textarea>
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
