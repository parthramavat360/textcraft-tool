<?php
/**
 * Widget: Meta Description Generator
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Meta_Description extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'meta_description'; }
    public function get_title(): string { return 'Meta Description Generator'; }
    public function get_icon(): string { return 'eicon-html'; }

    public function get_keywords(): array {
        return ['meta description generator','seo description','meta tag generator','description length','seo meta'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Generate SEO-optimized meta descriptions that fit within Google's character limit. Enter your page topic and get multiple description options with real-time length checking.
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Page topic or keywords</label>
            <textarea class="tc-textarea" id="tc-md-topic" rows="3" placeholder="What is the page about?&#10;e.g., Best running shoes for marathon, WordPress hosting comparison, Italian restaurant downtown"></textarea>
        </div>

        <div class="tc-rsz-options">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Tone</h4>
                <?php $this->render_mode_buttons('md-tone', [
                    'informative' => 'Informative',
                    'persuasive'  => 'Persuasive',
                    'question'    => 'Question',
                    'urgency'     => 'Urgency / FOMO',
                ], 'informative'); ?>
            </div>
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Options</h4>
                <div class="tc-rsz-toggles">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-md-cta" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Include call-to-action</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-md-emoji">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Include emojis</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-md-focus" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Include primary keyword</b></span>
                    </label>
                </div>
            </div>
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Number of Descriptions <span class="tc-rsz-quality-badge" id="tc-md-num-val">5</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">3</span>
                    <input type="range" class="tc-rsz-slider" id="tc-md-num" min="3" max="10" step="1" value="5">
                    <span class="tc-rsz-slider-max">10</span>
                </div>
            </div>
        </div>

        <?php $this->render_progress_bar('tc-md-progress', 'Generating descriptions...'); ?>

        <?php $this->render_actions('tc-md-generate', 'Generate Descriptions', '', ''); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Generated</span><span class="tc-stat-value" id="tc-md-count">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Best Length</span><span class="tc-stat-value" id="tc-md-bestlen">0</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Generated Descriptions</h3>
                    <span id="tc-status-chip">Ready</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-tabs-header">
                        <h4>Results</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">All Descriptions</button>
                            <button data-tab="result">Length Analysis</button>
                        </div>
                    </div>

                    <div class="tc-preview" data-tab-content="original" id="tc-md-results">
                        <p style="color:var(--muted);font-size:14px;">Enter a topic and click Generate to create meta descriptions.</p>
                    </div>

                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-md-analysis">
                        <p style="color:var(--muted);font-size:14px;">Length analysis will appear after generation.</p>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
