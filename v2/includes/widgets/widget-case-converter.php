<?php
/**
 * Widget: Case Converter
 *
 * Converts text to UPPERCASE, lowercase, Sentence case, Title Case,
 * Capitalized Case, aLtErNaTiNg CaSe, and InVeRsE CaSe.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Case_Converter extends TextCraft_Tool_Base {

    public function get_name(): string { return 'case_converter'; }
    public function get_title(): string { return 'Case Converter'; }
    public function get_icon(): string { return 'eicon-text'; }

    public function get_keywords(): array {
        return ['case', 'uppercase', 'lowercase', 'title', 'sentence', 'alternating', 'inverse', 'convert', 'case changer'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert text between uppercase, lowercase, sentence case, title case, and more. Works entirely in your browser — no data is sent to any server.
        </div>

        <div class="tc-modes" data-group="case-type">
            <button class="tc-btn tc-btn--ghost sel" data-val="uppercase" type="button">UPPERCASE</button>
            <button class="tc-btn tc-btn--ghost" data-val="lowercase" type="button">lowercase</button>
            <button class="tc-btn tc-btn--ghost" data-val="sentence" type="button">Sentence case</button>
            <button class="tc-btn tc-btn--ghost" data-val="title" type="button">Title Case</button>
            <button class="tc-btn tc-btn--ghost" data-val="capitalized" type="button">Capitalized Case</button>
            <button class="tc-btn tc-btn--ghost" data-val="alternating" type="button">aLtErNaTiNg</button>
            <button class="tc-btn tc-btn--ghost" data-val="inverse" type="button">InVeRsE CaSe</button>
        </div>

        <?php $this->render_textarea('tc-cc-input', 'Type or paste your text here to convert case...', 8); ?>

        <?php $this->render_progress_bar('tc-cc-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-cc-convert', 'Convert', 'tc-cc-copy', 'Copy'); ?>

        <?php $this->render_status('tc-cc-status'); ?>

        <?php $this->render_stats_panel_row([
            ['id' => 'tc-cc-chars', 'label' => 'Characters'],
            ['id' => 'tc-cc-words', 'label' => 'Words'],
            ['id' => 'tc-cc-sentences', 'label' => 'Sentences'],
            ['id' => 'tc-cc-lines', 'label' => 'Lines'],
        ]); ?>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-cc-result">
            <textarea class="tc-textarea" id="tc-cc-output" placeholder="Result will appear here..." readonly rows="8"></textarea>
        </div>
        <?php
    }

    protected function render_stats_panel_row(array $items): void {
        ?>
        <div class="tc-stats-row">
            <?php foreach ($items as $item): ?>
                <div class="tc-stat-item">
                    <span class="tc-stat-label"><?php echo esc_html($item['label']); ?></span>
                    <span class="tc-stat-value" id="<?php echo esc_attr($item['id']); ?>">0</span>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
}