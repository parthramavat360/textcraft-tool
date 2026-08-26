<?php
/**
 * Widget: Pig Latin Translator
 * Premium design following case-converter pattern.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Pig_Latin extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'pig_latin'; }
    public function get_title(): string { return 'Pig Latin Translator'; }
    public function get_icon(): string { return 'eicon-integration'; }

    public function get_keywords(): array {
        return ['pig', 'latin', 'translate', 'language', 'fun', 'english', 'converter', 'decoder'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Translate English text to Pig Latin and back. Supports both -way and -yay vowel suffixes. Works entirely in your browser — no data is sent to any server.
        </div>

        <?php $this->render_textarea('tc-pl-input', 'Enter English text to translate to Pig Latin...', 8); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <div class="tc-rsz-mode-cards tc-pl-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="to_pig">
                        <span class="tc-rsz-mode-text"><b>English &rarr; Pig Latin</b><span>Translate to Pig Latin</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="from_pig">
                        <span class="tc-rsz-mode-text"><b>Pig Latin &rarr; English</b><span>Decode Pig Latin</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <div class="tc-rsz-toggles">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-pl-keep-case" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Preserve capitalization</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-pl-yay">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Use -yay suffix instead of -way</b></span>
                    </label>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-pl-bar', 'Translating...'); ?>

        <?php $this->render_actions('tc-pl-translate', 'Translate', 'tc-pl-copy', 'Copy Result'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Characters</span><span class="tc-stat-value" id="tc-pl-chars">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Words</span><span class="tc-stat-value" id="tc-pl-words">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Translated</span><span class="tc-stat-value" id="tc-pl-translated">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Output Length</span><span class="tc-stat-value" id="tc-pl-output-len">0</span></div>
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
                        <div><span>Translated</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Difference</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original</button>
                            <button data-tab="result">Translated</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <textarea class="tc-textarea" id="tc-pl-preview-orig" placeholder="Original text will appear here..." readonly rows="10"></textarea>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <textarea class="tc-textarea" id="tc-pl-preview-result" placeholder="Pig Latin translation will appear here..." readonly rows="10"></textarea>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
