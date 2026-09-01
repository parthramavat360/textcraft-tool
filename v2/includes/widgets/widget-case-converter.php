<?php
/**
 * Widget: Case Converter
 * Premium design with mode cards, toggles, preview tabs, download.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Case_Converter extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'case_converter'; }
    public function get_title(): string { return 'Case Converter'; }
    public function get_icon(): string { return 'eicon-text'; }

    public function get_keywords(): array {
        return ['case', 'uppercase', 'lowercase', 'title', 'sentence', 'alternating', 'inverse', 'convert', 'case changer', 'text transform'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-txtp">
        <div class="tc-tool-desc">
            Convert text between uppercase, lowercase, sentence case, title case, and more. Works entirely in your browser — no data is sent to any server.
        </div>

        <?php $this->render_textarea('tc-cc-input', 'Type or paste your text here to convert case...', 8); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <div class="tc-rsz-mode-cards tc-cc-types">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="uppercase"><span class="tc-rsz-mode-text"><b>UPPERCASE</b></span></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="lowercase"><span class="tc-rsz-mode-text"><b>lowercase</b></span></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="sentence"><span class="tc-rsz-mode-text"><b>Sentence</b></span></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="title"><span class="tc-rsz-mode-text"><b>Title Case</b></span></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="capitalized"><span class="tc-rsz-mode-text"><b>Capitalized</b></span></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="alternating"><span class="tc-rsz-mode-text"><b>aLtErNaTiNg</b></span></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="inverse"><span class="tc-rsz-mode-text"><b>InVeRsE</b></span></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="camel"><span class="tc-rsz-mode-text"><b>camelCase</b></span></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="pascal"><span class="tc-rsz-mode-text"><b>PascalCase</b></span></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="snake"><span class="tc-rsz-mode-text"><b>snake_case</b></span></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="kebab"><span class="tc-rsz-mode-text"><b>kebab-case</b></span></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="dot"><span class="tc-rsz-mode-text"><b>dot.case</b></span></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="constant"><span class="tc-rsz-mode-text"><b>CONSTANT_CASE</b></span></button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <div class="tc-rsz-toggles">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-cc-trim">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Trim whitespace</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-cc-dedup-spaces">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Remove extra spaces</b></span>
                    </label>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-cc-progress', 'Converting...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-cc-convert" type="button">Convert Text</button>
            <button class="tc-btn tc-btn--ghost" id="tc-cc-copy" type="button">Copy Result</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-cc-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Characters</span><span class="tc-stat-value" id="tc-cc-chars">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Words</span><span class="tc-stat-value" id="tc-cc-words">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Sentences</span><span class="tc-stat-value" id="tc-cc-sentences">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Lines</span><span class="tc-stat-value" id="tc-cc-lines">0</span></div>
        </div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    /**
     * Override result panel with Original/Converted tabs + textarea preview.
     */
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
                        <div><span>Converted</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Difference</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original</button>
                            <button data-tab="result">Converted</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <textarea class="tc-textarea" id="tc-cc-preview-orig" placeholder="Original text will appear here..." readonly rows="10"></textarea>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <textarea class="tc-textarea" id="tc-cc-preview-result" placeholder="Converted text will appear here..." readonly rows="10"></textarea>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
