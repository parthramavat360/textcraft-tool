<?php
/**
 * Widget: Find and Replace
 * Premium design following case-converter pattern.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Find_Replace extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'find_replace'; }
    public function get_title(): string { return 'Find and Replace'; }
    public function get_icon(): string { return 'eicon-search'; }

    public function get_keywords(): array {
        return ['find', 'replace', 'search', 'text', 'regex', 'pattern', 'substitute', 'swap'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-txtp">
        <div class="tc-tool-desc">
            Search and replace words or phrases in any text. Supports normal text, regex patterns, and whole-word matching. Works entirely in your browser — no data is sent to any server.
        </div>

        <?php $this->render_textarea('tc-fr-input', 'Paste or type the text to search through...', 8); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <div class="tc-rsz-mode-cards tc-fr-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="normal">
                        <span class="tc-rsz-mode-text"><b>Normal</b></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="regex">
                        <span class="tc-rsz-mode-text"><b>Regex</b></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="whole">
                        <span class="tc-rsz-mode-text"><b>Whole Word</b></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <div class="tc-rsz-toggles">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-fr-case">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Case-sensitive</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-fr-all" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Replace all matches</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-fr-trim">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Trim whitespace</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-fr-dedup">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Remove extra spaces</b></span>
                    </label>
                </div>
            </div>

        </div>

        <div class="tc-fr-fields">
            <div class="tc-fr-field">
                <div class="tc-input-group">
                    <label class="tc-label">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        Find
                    </label>
                    <div class="tc-fr-field-input">
                        <input type="text" class="tc-input" id="tc-fr-find" placeholder="Enter text or pattern to find...">
                    </div>
                </div>
            </div>
            <div class="tc-fr-arrow">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
            </div>
            <div class="tc-fr-field">
                <div class="tc-input-group">
                    <label class="tc-label">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                        Replace with
                    </label>
                    <div class="tc-fr-field-input">
                        <input type="text" class="tc-input" id="tc-fr-replace" placeholder="Replacement text (leave blank to delete)">
                    </div>
                </div>
            </div>
        </div>

        <?php $this->render_progress_bar('tc-fr-progress', 'Replacing...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-fr-convert" type="button">Find &amp; Replace</button>
            <button class="tc-btn tc-btn--ghost" id="tc-fr-copy" type="button">Copy Result</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-fr-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Characters</span><span class="tc-stat-value" id="tc-fr-chars">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Words</span><span class="tc-stat-value" id="tc-fr-words">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Matches</span><span class="tc-stat-value" id="tc-fr-matches">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Replaced</span><span class="tc-stat-value" id="tc-fr-replaced">0</span></div>
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
                        <div><span>Replaced</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Difference</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original</button>
                            <button data-tab="result">Replaced</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <textarea class="tc-textarea" id="tc-fr-preview-orig" placeholder="Original text will appear here..." readonly rows="10"></textarea>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <textarea class="tc-textarea" id="tc-fr-preview-result" placeholder="Replaced text will appear here..." readonly rows="10"></textarea>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
