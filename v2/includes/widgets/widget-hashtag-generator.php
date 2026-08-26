<?php
/**
 * Widget: Hashtag Generator
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Hashtag_Generator extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'hashtag_generator'; }
    public function get_title(): string { return 'Hashtag Generator'; }
    public function get_icon(): string { return 'eicon-hashtag'; }

    public function get_keywords(): array {
        return ['hashtag generator','instagram hashtags','tiktok hashtags','social media hashtags','hashtag maker'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Generate relevant hashtags for Instagram, TikTok, Twitter, and LinkedIn. Enter your topic or paste a caption to get trending hashtag sets organized by popularity.
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Topic or caption</label>
            <textarea class="tc-textarea" id="tc-ht-topic" rows="3" placeholder="Enter your post topic or paste your caption...&#10;e.g., Healthy food recipes, fitness motivation, travel photography"></textarea>
        </div>

        <div class="tc-rsz-options">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Platform</h4>
                <?php $this->render_mode_buttons('ht-platform', [
                    'instagram' => 'Instagram',
                    'tiktok'    => 'TikTok',
                    'twitter'   => 'Twitter / X',
                    'linkedin'  => 'LinkedIn',
                ], 'instagram'); ?>
            </div>
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Options</h4>
                <div class="tc-rsz-toggles">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-ht-niche" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Include niche hashtags</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-ht-branded" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Include branded suggestions</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-ht-count" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Show estimated post count</b></span>
                    </label>
                </div>
            </div>
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Number of Hashtags <span class="tc-rsz-quality-badge" id="tc-ht-num-val">20</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">10</span>
                    <input type="range" class="tc-rsz-slider" id="tc-ht-num" min="10" max="50" step="5" value="20">
                    <span class="tc-rsz-slider-max">50</span>
                </div>
            </div>
        </div>

        <?php $this->render_progress_bar('tc-ht-progress', 'Generating hashtags...'); ?>

        <?php $this->render_actions('tc-ht-generate', 'Generate Hashtags', '', ''); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Total</span><span class="tc-stat-value" id="tc-ht-total">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Copied</span><span class="tc-stat-value" id="tc-ht-copied">0</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Generated Hashtags</h3>
                    <span id="tc-status-chip">Ready</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-tabs-header">
                        <h4>Results</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">All Hashtags</button>
                            <button data-tab="result">By Category</button>
                        </div>
                    </div>

                    <div class="tc-preview" data-tab-content="original" id="tc-ht-results">
                        <p style="color:var(--muted);font-size:14px;">Enter a topic and click Generate to create hashtags.</p>
                    </div>

                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-ht-categories">
                        <p style="color:var(--muted);font-size:14px;">Hashtags will be categorized after generation.</p>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
