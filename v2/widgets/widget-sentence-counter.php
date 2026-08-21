<?php
/**
 * Widget: Sentence Counter
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Sentence_Counter extends TextCraft_Tool_Base {

    public function get_name(): string { return 'sentence_counter'; }
    public function get_title(): string { return 'Sentence Counter'; }
    public function get_icon(): string { return 'eicon-editor-ul'; }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Get a comprehensive count of words, sentences, paragraphs, characters, and estimated reading/speaking times for any text.
        </div>

        <?php $this->render_textarea('tc-sc-input', 'Paste text to count sentences, words, and more...', 10); ?>

        <?php $this->render_actions('tc-sc-analyze', 'Analyze Text', 'tc-sc-clear', 'Clear'); ?>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-sc-result">
            <div class="tc-label">Text Statistics</div>
            <div class="tc-stats-grid" id="tc-sc-stats">
                <div class="tc-stat-card">
                    <span class="tc-stat-card-val" id="tc-sc-words">0</span>
                    <span class="tc-stat-card-label">Words</span>
                </div>
                <div class="tc-stat-card">
                    <span class="tc-stat-card-val" id="tc-sc-sentences">0</span>
                    <span class="tc-stat-card-label">Sentences</span>
                </div>
                <div class="tc-stat-card">
                    <span class="tc-stat-card-val" id="tc-sc-paragraphs">0</span>
                    <span class="tc-stat-card-label">Paragraphs</span>
                </div>
                <div class="tc-stat-card">
                    <span class="tc-stat-card-val" id="tc-sc-chars">0</span>
                    <span class="tc-stat-card-label">Characters</span>
                </div>
                <div class="tc-stat-card">
                    <span class="tc-stat-card-val" id="tc-sc-chars-nosp">0</span>
                    <span class="tc-stat-card-label">Chars (no spaces)</span>
                </div>
                <div class="tc-stat-card">
                    <span class="tc-stat-card-val" id="tc-sc-readtime">0 min</span>
                    <span class="tc-stat-card-label">Reading Time</span>
                </div>
                <div class="tc-stat-card">
                    <span class="tc-stat-card-val" id="tc-sc-speaktime">0 min</span>
                    <span class="tc-stat-card-label">Speaking Time</span>
                </div>
            </div>
        </div>
        <?php
    }
}
