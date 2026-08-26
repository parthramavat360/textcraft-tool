<?php
/**
 * Widget: Phonetic Spelling Generator
 * Premium design following case-converter pattern.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Phonetic_Spelling extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'phonetic_spelling'; }
    public function get_title(): string { return 'Phonetic Spelling Generator'; }
    public function get_icon(): string { return 'eicon-megaphone'; }

    public function get_keywords(): array {
        return ['phonetic', 'spelling', 'pronunciation', 'sound', 'guide', 'simplified', 'nato', 'soundalike'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Generate phonetic spellings for any text. Choose between simplified pronunciation guides, NATO alphabet, or sound-alike representations. Useful for clear verbal communication. Works entirely in your browser — no data is sent to any server.
        </div>

        <?php $this->render_textarea('tc-ps-input', 'Enter text to generate phonetic spelling...', 8); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <div class="tc-rsz-mode-cards tc-ps-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="simplified">
                        <span class="tc-rsz-mode-text"><b>Simplified</b><span>Pronunciation guide</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="nato">
                        <span class="tc-rsz-mode-text"><b>NATO</b><span>Military alphabet</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="soundalike">
                        <span class="tc-rsz-mode-text"><b>Sound-Alike</b><span>How it sounds</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <div class="tc-rsz-toggles">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-ps-syllables" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Break into syllables</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-ps-stress">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Mark stressed syllables</b></span>
                    </label>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-ps-bar', 'Generating...'); ?>

        <?php $this->render_actions('tc-ps-generate', 'Generate Phonetic Spelling', 'tc-ps-copy', 'Copy Result'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Characters</span><span class="tc-stat-value" id="tc-ps-chars">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Words</span><span class="tc-stat-value" id="tc-ps-words">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Letters Mapped</span><span class="tc-stat-value" id="tc-ps-mapped">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Output Length</span><span class="tc-stat-value" id="tc-ps-output-len">0</span></div>
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
                        <textarea class="tc-textarea" id="tc-ps-preview-orig" placeholder="Original text will appear here..." readonly rows="10"></textarea>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <textarea class="tc-textarea" id="tc-ps-preview-result" placeholder="Phonetic spelling will appear here..." readonly rows="10"></textarea>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
