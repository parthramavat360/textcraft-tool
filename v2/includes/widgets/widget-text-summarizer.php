<?php
/**
 * Widget: Text Summarizer
 * Extractive summarization using word frequency + position scoring.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Text_Summarizer extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'text_summarizer'; }
    public function get_title(): string { return 'Text Summarizer'; }
    public function get_icon(): string { return 'eicon-text-editor'; }

    public function get_keywords(): array {
        return ['text summarizer', 'summarize text', 'summary generator', 'text summary', 'article summarizer', 'summarize article'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Summarize any text instantly. Paste an article and get a concise summary by extracting the most important sentences.
        </div>

        <textarea class="tc-textarea" id="tc-ts-input" placeholder="Paste the text you want to summarize..." rows="10"></textarea>

        <div class="tc-rsz-options" style="margin-top:16px">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Summary Length</h4>
                <div class="tc-rsz-mode-cards tc-ts-modes">
                    <button class="tc-rsz-mode-card" type="button" data-val="short">
                        <span class="tc-rsz-mode-text"><b>Short</b><span>~15% of text</span></span>
                    </button>
                    <button class="tc-rsz-mode-card sel" type="button" data-val="medium">
                        <span class="tc-rsz-mode-text"><b>Medium</b><span>~30% of text</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="long">
                        <span class="tc-rsz-mode-text"><b>Long</b><span>~50% of text</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="custom">
                        <span class="tc-rsz-mode-text"><b>Custom</b><span>Set sentence count</span></span>
                    </button>
                </div>
            </div>
            <div class="tc-rsz-section tc-ts-custom-row" id="tc-ts-custom-row" style="display:none">
                <h4 class="tc-rsz-heading">Sentences <span class="tc-rsz-quality-badge" id="tc-ts-custom-val">3</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">1</span>
                    <input type="range" class="tc-rsz-slider" id="tc-ts-custom-count" min="1" max="20" value="3">
                    <span class="tc-rsz-slider-max">20</span>
                </div>
            </div>
        </div>

        <div class="tc-ts-options">
            <label class="tc-rsz-toggle">
                <input type="checkbox" class="tc-rsz-toggle-input" id="tc-ts-highlight" checked>
                <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                <span class="tc-rsz-toggle-text"><b>Highlight key sentences in original</b></span>
            </label>
            <label class="tc-rsz-toggle">
                <input type="checkbox" class="tc-rsz-toggle-input" id="tc-ts-bullet">
                <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                <span class="tc-rsz-toggle-text"><b>Output as bullet points</b></span>
            </label>
        </div>

        <div class="tc-ts-actions">
            <button class="tc-btn tc-btn--accent" id="tc-ts-summarize" type="button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Summarize
            </button>
            <button class="tc-btn tc-btn--ghost" id="tc-ts-copy" type="button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                Copy
            </button>
            <button class="tc-btn tc-btn--ghost" id="tc-ts-clear" type="button">Clear</button>
        </div>

        <div class="tc-ts-stat-cards">
            <div class="tc-ts-stat-card">
                <span class="tc-ts-stat-num" id="tc-ts-stat-original">0</span>
                <span class="tc-ts-stat-label">Original Words</span>
            </div>
            <div class="tc-ts-stat-card">
                <span class="tc-ts-stat-num" id="tc-ts-stat-summary">0</span>
                <span class="tc-ts-stat-label">Summary Words</span>
            </div>
            <div class="tc-ts-stat-card">
                <span class="tc-ts-stat-num" id="tc-ts-stat-reduction">0%</span>
                <span class="tc-ts-stat-label">Reduction</span>
            </div>
            <div class="tc-ts-stat-card">
                <span class="tc-ts-stat-num" id="tc-ts-stat-sentences">0</span>
                <span class="tc-ts-stat-label">Key Sentences</span>
            </div>
        </div>

        <div class="tc-ts-output" id="tc-ts-output" style="display:none">
            <div class="tc-ts-output-header">
                <h4>Summary</h4>
                <span class="tc-ts-output-badge" id="tc-ts-output-badge"></span>
            </div>
            <div class="tc-ts-output-body" id="tc-ts-output-body"></div>
        </div>

        <div class="tc-ts-highlighted" id="tc-ts-highlighted" style="display:none">
            <h4>Highlighted Original</h4>
            <div class="tc-ts-highlighted-body" id="tc-ts-highlighted-body"></div>
        </div>

        <?php
    }

    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 · Summary</h3>
                    <span id="tc-status-chip">Ready</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-stats">
                        <div><span>Original</span><b id="tc-stat-orig">0</b></div>
                        <div><span>Summary</span><b id="tc-stat-comp">0</b></div>
                        <div class="saved"><span>Reduction</span><b id="tc-stat-saved">0%</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Output</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Summary</button>
                            <button data-tab="result">Highlighted</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <div id="tc-ts-preview-summary" class="tc-ts-preview-box">
                            <p class="tc-ts-placeholder">Summary will appear here</p>
                        </div>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <div id="tc-ts-preview-highlight" class="tc-ts-preview-box">
                            <p class="tc-ts-placeholder">Highlighted original will appear here</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
