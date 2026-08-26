<?php
/**
 * Widget: Lorem Ipsum Generator
 * Generate placeholder text in paragraphs, sentences, or words.
 * Premium design with stat cards, toggles, copy/download, output tabs.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Lorem_Ipsum extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'lorem_ipsum'; }
    public function get_title(): string { return 'Lorem Ipsum Generator'; }
    public function get_icon(): string { return 'eicon-text'; }

    public function get_keywords(): array {
        return ['lorem ipsum', 'lorem ipsum generator', 'placeholder text', 'dummy text', 'sample text', 'fake text'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Generate Lorem Ipsum placeholder text for your designs. Choose paragraphs, sentences, or words with full control over length and formatting.
        </div>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Generate Mode</h4>
                <div class="tc-rsz-mode-cards tc-li-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="paragraphs">
                        <span class="tc-rsz-mode-icon">¶</span>
                        <span class="tc-rsz-mode-text"><b>Paragraphs</b><span>Full paragraphs</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="sentences">
                        <span class="tc-rsz-mode-icon">.</span>
                        <span class="tc-rsz-mode-text"><b>Sentences</b><span>Individual sentences</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="words">
                        <span class="tc-rsz-mode-icon">W</span>
                        <span class="tc-rsz-mode-text"><b>Words</b><span>Raw word count</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Count <span class="tc-rsz-quality-badge" id="tc-li-count-val">5</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">1</span>
                    <input type="range" class="tc-rsz-slider" id="tc-li-count" min="1" max="100" value="5">
                    <span class="tc-rsz-slider-max">100</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Options</h4>
                <div class="tc-rsz-toggles">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-li-start" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Start with "Lorem ipsum dolor sit amet"</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-li-html">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Wrap paragraphs in &lt;p&gt; tags</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-li-numbers">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Add paragraph numbers</b></span>
                    </label>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-li-progress', 'Generating...'); ?>

        <div class="tc-li-actions">
            <button class="tc-btn tc-btn--accent" id="tc-li-generate" type="button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10"/></svg>
                Generate
            </button>
            <button class="tc-btn tc-btn--ghost" id="tc-li-copy" type="button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                Copy
            </button>
            <button class="tc-btn tc-btn--ghost" id="tc-li-download" type="button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Download
            </button>
        </div>

        <div class="tc-li-output" id="tc-li-output">
            <p class="tc-li-placeholder">Click <b>Generate</b> to create Lorem Ipsum text</p>
        </div>

        <div class="tc-li-stat-cards">
            <div class="tc-li-stat-card">
                <span class="tc-li-stat-icon">¶</span>
                <span class="tc-li-stat-num" id="tc-li-stat-paras">0</span>
                <span class="tc-li-stat-label">Paragraphs</span>
            </div>
            <div class="tc-li-stat-card">
                <span class="tc-li-stat-icon">W</span>
                <span class="tc-li-stat-num" id="tc-li-stat-words">0</span>
                <span class="tc-li-stat-label">Words</span>
            </div>
            <div class="tc-li-stat-card">
                <span class="tc-li-stat-icon">C</span>
                <span class="tc-li-stat-num" id="tc-li-stat-chars">0</span>
                <span class="tc-li-stat-label">Characters</span>
            </div>
            <div class="tc-li-stat-card">
                <span class="tc-li-stat-icon">.</span>
                <span class="tc-li-stat-num" id="tc-li-stat-sentences">0</span>
                <span class="tc-li-stat-label">Sentences</span>
            </div>
        </div>

        <?php
    }

    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 · Output</h3>
                    <span id="tc-status-chip">Ready</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-stats">
                        <div><span>Paragraphs</span><b id="tc-stat-orig">0</b></div>
                        <div><span>Words</span><b id="tc-stat-comp">0</b></div>
                        <div class="saved"><span>Characters</span><b id="tc-stat-saved">0</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Formatted</button>
                            <button data-tab="result">Plain Text</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <div id="tc-li-preview-formatted" class="tc-li-preview-box">
                            <p class="tc-li-placeholder">Click Generate to create text</p>
                        </div>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <div id="tc-li-preview-plain" class="tc-li-preview-box">
                            <p class="tc-li-placeholder">Generated text will appear here</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
