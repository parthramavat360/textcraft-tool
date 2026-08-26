<?php
/**
 * Widget: NATO Phonetic Alphabet
 * Premium design following case-converter pattern.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_NATO_Phonetic extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'nato_phonetic'; }
    public function get_title(): string { return 'NATO Phonetic Alphabet'; }
    public function get_icon(): string { return 'eicon-megaphone'; }

    public function get_keywords(): array {
        return ['nato', 'phonetic', 'alphabet', 'military', 'radio', 'communication', 'alpha', 'bravo', 'convert'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert text to the NATO phonetic alphabet. Each letter is replaced with its standard phonetic word (e.g. A = Alpha, B = Bravo). Ideal for radio communication clarity. Works entirely in your browser — no data is sent to any server.
        </div>

        <?php $this->render_textarea('tc-nato-input', 'Enter text to convert to NATO phonetic alphabet...', 8); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <div class="tc-rsz-mode-cards tc-nato-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="dash">
                        <span class="tc-rsz-mode-text"><b>Alpha - Bravo</b><span>Dash separated</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="nodash">
                        <span class="tc-rsz-mode-text"><b>AlphaBravo</b><span>No separator</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="newline">
                        <span class="tc-rsz-mode-text"><b>One Per Line</b><span>Newline separated</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="table">
                        <span class="tc-rsz-mode-text"><b>With Original</b><span>Show source text</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <div class="tc-rsz-toggles">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-nato-uppercase" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Uppercase phonetic words</b></span>
                    </label>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-nato-bar', 'Converting...'); ?>

        <?php $this->render_actions('tc-nato-convert', 'Convert to NATO', 'tc-nato-copy', 'Copy Result'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Characters</span><span class="tc-stat-value" id="tc-nato-chars">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Words</span><span class="tc-stat-value" id="tc-nato-words">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Letters Mapped</span><span class="tc-stat-value" id="tc-nato-mapped">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Output Length</span><span class="tc-stat-value" id="tc-nato-output-len">0</span></div>
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
                        <div><span>Phonetic</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Difference</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original</button>
                            <button data-tab="result">Phonetic</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <textarea class="tc-textarea" id="tc-nato-preview-orig" placeholder="Original text will appear here..." readonly rows="10"></textarea>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <textarea class="tc-textarea" id="tc-nato-preview-result" placeholder="NATO phonetic output will appear here..." readonly rows="10"></textarea>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
