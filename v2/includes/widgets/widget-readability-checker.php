<?php
/**
 * Widget: Readability Checker
 * Flesch-Kincaid, Coleman-Liau, SMOG, reading level, and detailed analysis.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Readability_Checker extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'readability_checker'; }
    public function get_title(): string { return 'Readability Checker'; }
    public function get_icon(): string { return 'eicon-text-editor'; }

    public function get_keywords(): array {
        return ['readability checker', 'flesch kincaid', 'reading level', 'text readability', 'grade level', 'readability score'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Check the readability of any text using Flesch-Kincaid, Coleman-Liau, SMOG and other grading formulas. Paste your text and get instant analysis.
        </div>

        <textarea class="tc-textarea" id="tc-rc-input" placeholder="Paste your text here to check readability..." rows="8"></textarea>

        <div class="tc-rsz-options" style="margin-top:16px">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Target Grade Level</h4>
                <div class="tc-rsz-mode-cards tc-rc-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="general">
                        <span class="tc-rsz-mode-text"><b>General</b><span>All audiences</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="academic">
                        <span class="tc-rsz-mode-text"><b>Academic</b><span>College level</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="business">
                        <span class="tc-rsz-mode-text"><b>Business</b><span>Professional</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="child">
                        <span class="tc-rsz-mode-text"><b>Child</b><span>Grade 5-8</span></span>
                    </button>
                </div>
            </div>
        </div>

        <div class="tc-rc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-rc-analyze" type="button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Analyze Readability
            </button>
            <button class="tc-btn tc-btn--ghost" id="tc-rc-clear" type="button">Clear</button>
        </div>

        <div class="tc-rc-results" id="tc-rc-results" style="display:none">

            <div class="tc-rc-score-hero">
                <div class="tc-rc-score-ring" id="tc-rc-score-ring">
                    <svg viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="54" fill="none" stroke="var(--line,#1e3050)" stroke-width="8"/>
                        <circle cx="60" cy="60" r="54" fill="none" stroke="var(--accent,#2563eb)" stroke-width="8" stroke-dasharray="339.292" stroke-dashoffset="339.292" stroke-linecap="round" transform="rotate(-90 60 60)" id="tc-rc-ring-fill"/>
                    </svg>
                    <div class="tc-rc-score-num" id="tc-rc-score-num">0</div>
                    <div class="tc-rc-score-label" id="tc-rc-score-label">Reading Ease</div>
                </div>
                <div class="tc-rc-level-badge" id="tc-rc-level-badge">—</div>
            </div>

            <div class="tc-rc-metrics">
                <div class="tc-rc-metric">
                    <span class="tc-rc-metric-val" id="tc-rc-fk-grade">—</span>
                    <span class="tc-rc-metric-label">Flesch-Kincaid Grade</span>
                </div>
                <div class="tc-rc-metric">
                    <span class="tc-rc-metric-val" id="tc-rc-coleman">—</span>
                    <span class="tc-rc-metric-label">Coleman-Liau Index</span>
                </div>
                <div class="tc-rc-metric">
                    <span class="tc-rc-metric-val" id="tc-rc-smog">—</span>
                    <span class="tc-rc-metric-label">SMOG Index</span>
                </div>
                <div class="tc-rc-metric">
                    <span class="tc-rc-metric-val" id="tc-rc-ari">—</span>
                    <span class="tc-rc-metric-label">Automated Readability</span>
                </div>
                <div class="tc-rc-metric">
                    <span class="tc-rc-metric-val" id="tc-rc-dale">—</span>
                    <span class="tc-rc-metric-label">Dale-Chall Score</span>
                </div>
                <div class="tc-rc-metric">
                    <span class="tc-rc-metric-val" id="tc-rc-linx">—</span>
                    <span class="tc-rc-metric-label">Linsear Write</span>
                </div>
            </div>

            <div class="tc-rc-stats-grid">
                <div class="tc-rc-stat-card">
                    <span class="tc-rc-stat-num" id="tc-rc-words">0</span>
                    <span class="tc-rc-stat-label">Words</span>
                </div>
                <div class="tc-rc-stat-card">
                    <span class="tc-rc-stat-num" id="tc-rc-sentences">0</span>
                    <span class="tc-rc-stat-label">Sentences</span>
                </div>
                <div class="tc-rc-stat-card">
                    <span class="tc-rc-stat-num" id="tc-rc-syllables">0</span>
                    <span class="tc-rc-stat-label">Syllables</span>
                </div>
                <div class="tc-rc-stat-card">
                    <span class="tc-rc-stat-num" id="tc-rc-avg-wps">0</span>
                    <span class="tc-rc-stat-label">Avg Words/Sentence</span>
                </div>
                <div class="tc-rc-stat-card">
                    <span class="tc-rc-stat-num" id="tc-rc-avg-sps">0</span>
                    <span class="tc-rc-stat-label">Avg Syllables/Word</span>
                </div>
                <div class="tc-rc-stat-card">
                    <span class="tc-rc-stat-num" id="tc-rc-characters">0</span>
                    <span class="tc-rc-stat-label">Characters</span>
                </div>
            </div>

            <div class="tc-rc-suggestions" id="tc-rc-suggestions"></div>

        </div>

        <?php
    }

    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 · Analysis</h3>
                    <span id="tc-status-chip">Ready</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-stats">
                        <div><span>Words</span><b id="tc-stat-orig">0</b></div>
                        <div><span>Sentences</span><b id="tc-stat-comp">0</b></div>
                        <div class="saved"><span>Syllables</span><b id="tc-stat-saved">0</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Details</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Scores</button>
                            <button data-tab="result">Tips</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <div id="tc-rc-detail-scores" class="tc-rc-detail-box">
                            <p class="tc-rc-placeholder">Click Analyze to check readability</p>
                        </div>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <div id="tc-rc-detail-tips" class="tc-rc-detail-box">
                            <p class="tc-rc-placeholder">Improvement suggestions will appear here</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
