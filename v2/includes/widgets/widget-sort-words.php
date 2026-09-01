<?php
/**
 * Widget: Sort Words & Lines
 * Premium design following case-converter pattern.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Sort_Words extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'sort_words'; }
    public function get_title(): string { return 'Sort Words & Lines'; }
    public function get_icon(): string { return 'eicon-sort-alpha-asc'; }

    public function get_keywords(): array {
        return ['sort', 'words', 'lines', 'alphabetical', 'random', 'order', 'shuffle', 'length', 'reverse'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-txtp">
        <div class="tc-tool-desc">
            Sort words or lines alphabetically, by length, or randomly. Great for organizing lists, creating ranked data, or shuffling text. Works entirely in your browser — no data is sent to any server.
        </div>

        <?php $this->render_textarea('tc-sw-input', 'Paste words or lines to sort...', 8); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <div class="tc-rsz-mode-cards tc-sw-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="alpha_asc">
                        <span class="tc-rsz-mode-text"><b>A &rarr; Z</b><span>Ascending</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="alpha_desc">
                        <span class="tc-rsz-mode-text"><b>Z &rarr; A</b><span>Descending</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="length_asc">
                        <span class="tc-rsz-mode-text"><b>Short &rarr; Long</b><span>By length</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="length_desc">
                        <span class="tc-rsz-mode-text"><b>Long &rarr; Short</b><span>By length</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="random">
                        <span class="tc-rsz-mode-text"><b>Random</b><span>Shuffle order</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <div class="tc-rsz-toggles">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-sw-lines" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Sort lines instead of words</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-sw-case">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Case-sensitive sorting</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-sw-remove-blanks">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Remove blank lines</b></span>
                    </label>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-sw-bar', 'Sorting...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-sw-sort" type="button">Sort</button>
            <button class="tc-btn tc-btn--ghost" id="tc-sw-copy" type="button">Copy Result</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-sw-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Characters</span><span class="tc-stat-value" id="tc-sw-chars">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Words</span><span class="tc-stat-value" id="tc-sw-words">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Lines</span><span class="tc-stat-value" id="tc-sw-lines-count">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Unique Words</span><span class="tc-stat-value" id="tc-sw-unique">0</span></div>
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
                        <div><span>Sorted</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Difference</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original</button>
                            <button data-tab="result">Sorted</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <textarea class="tc-textarea" id="tc-sw-preview-orig" placeholder="Original text will appear here..." readonly rows="10"></textarea>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <textarea class="tc-textarea" id="tc-sw-preview-result" placeholder="Sorted text will appear here..." readonly rows="10"></textarea>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
