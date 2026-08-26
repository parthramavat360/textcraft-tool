<?php
/**
 * Widget: Sentence Counter (Word Counter)
 * Premium design with real-time stats, stat cards, toggles, density, preview tabs.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Sentence_Counter extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'sentence_counter'; }
    public function get_title(): string { return 'Sentence Counter'; }
    public function get_icon(): string { return 'eicon-editor-ul'; }

    public function get_keywords(): array {
        return ['word counter','sentence counter','text stats','character count','reading time','word count','text analyzer'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Get a comprehensive count of words, sentences, paragraphs, characters, and estimated reading/speaking times. Statistics update in real time as you type.
        </div>

        <?php $this->render_textarea('tc-sc-input', 'Paste or type your text here to analyze...', 10); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Counting Options</h4>
                <div class="tc-rsz-toggles">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-sc-include-numbers" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Include numbers as words</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-sc-include-punct">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Include punctuation in characters</b></span>
                    </label>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Target Word Count <span class="tc-rsz-quality-badge" id="tc-sc-target-val">500</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">100</span>
                    <input type="range" class="tc-rsz-slider" id="tc-sc-target" min="100" max="10000" step="100" value="500">
                    <span class="tc-rsz-slider-max">10,000</span>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-sc-progress', 'Analyzing...'); ?>

        <?php $this->render_actions('tc-sc-analyze', 'Analyze Text', 'tc-sc-clear', 'Clear'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Words</span><span class="tc-stat-value" id="tc-sc-words">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Characters</span><span class="tc-stat-value" id="tc-sc-chars">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Reading</span><span class="tc-stat-value" id="tc-sc-readtime">0 min</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Speaking</span><span class="tc-stat-value" id="tc-sc-speaktime">0 min</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    /**
     * Override result panel with premium stat cards + word density + preview tabs.
     */
    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Text Statistics</h3>
                    <span id="tc-status-chip">Idle</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-stats">
                        <div><span>Words</span><b id="tc-stat-orig">&mdash;</b></div>
                        <div><span>Sentences</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Target</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>

                    <div class="tc-tabs-header">
                        <h4>Results</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Statistics</button>
                            <button data-tab="result">Word Density</button>
                        </div>
                    </div>

                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <div class="tc-sc-stat-grid">
                            <div class="tc-sc-stat-card">
                                <div class="tc-sc-stat-ic"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16M4 12h16M4 17h10"/></svg></div>
                                <div class="tc-sc-stat-body">
                                    <span class="tc-sc-stat-val" id="tc-sc-s-words">0</span>
                                    <span class="tc-sc-stat-lbl">Words</span>
                                </div>
                            </div>
                            <div class="tc-sc-stat-card">
                                <div class="tc-sc-stat-ic"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg></div>
                                <div class="tc-sc-stat-body">
                                    <span class="tc-sc-stat-val" id="tc-sc-s-sentences">0</span>
                                    <span class="tc-sc-stat-lbl">Sentences</span>
                                </div>
                            </div>
                            <div class="tc-sc-stat-card">
                                <div class="tc-sc-stat-ic"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2z"/><path d="M9 7h6M9 11h6M9 15h4"/></svg></div>
                                <div class="tc-sc-stat-body">
                                    <span class="tc-sc-stat-val" id="tc-sc-s-paragraphs">0</span>
                                    <span class="tc-sc-stat-lbl">Paragraphs</span>
                                </div>
                            </div>
                            <div class="tc-sc-stat-card">
                                <div class="tc-sc-stat-ic"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6h16M4 12h16M4 18h16"/></svg></div>
                                <div class="tc-sc-stat-body">
                                    <span class="tc-sc-stat-val" id="tc-sc-s-chars">0</span>
                                    <span class="tc-sc-stat-lbl">Characters</span>
                                </div>
                            </div>
                            <div class="tc-sc-stat-card">
                                <div class="tc-sc-stat-ic"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6h16M4 12h16M4 18h12"/></svg></div>
                                <div class="tc-sc-stat-body">
                                    <span class="tc-sc-stat-val" id="tc-sc-s-chars-nosp">0</span>
                                    <span class="tc-sc-stat-lbl">Chars (no spaces)</span>
                                </div>
                            </div>
                            <div class="tc-sc-stat-card">
                                <div class="tc-sc-stat-ic"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></div>
                                <div class="tc-sc-stat-body">
                                    <span class="tc-sc-stat-val" id="tc-sc-s-readtime">0 min</span>
                                    <span class="tc-sc-stat-lbl">Reading Time</span>
                                </div>
                            </div>
                            <div class="tc-sc-stat-card">
                                <div class="tc-sc-stat-ic"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg></div>
                                <div class="tc-sc-stat-body">
                                    <span class="tc-sc-stat-val" id="tc-sc-s-speaktime">0 min</span>
                                    <span class="tc-sc-stat-lbl">Speaking Time</span>
                                </div>
                            </div>
                            <div class="tc-sc-stat-card tc-sc-stat-card--accent">
                                <div class="tc-sc-stat-ic"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
                                <div class="tc-sc-stat-body">
                                    <span class="tc-sc-stat-val" id="tc-sc-s-target">0%</span>
                                    <span class="tc-sc-stat-lbl">Target Progress</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <div class="tc-sc-density" id="tc-sc-density">
                            <p style="color:var(--muted);font-size:14px;">Enter text and analyze to see word frequency distribution.</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
