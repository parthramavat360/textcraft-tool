<?php
/**
 * Widget: Instagram Caption Generator
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Instagram_Caption extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'instagram_caption'; }
    public function get_title(): string { return 'Instagram Caption Generator'; }
    public function get_icon(): string { return 'eicon-social-icons'; }

    public function get_keywords(): array {
        return ['instagram caption','ig caption','caption generator','social media caption','instagram post caption'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Generate engaging Instagram captions with emojis, hashtags, and calls-to-action. Choose a tone, enter your topic, and get multiple caption options instantly.
        </div>

        <div class="tc-input-group">
            <label class="tc-label">What's your post about?</label>
            <textarea class="tc-textarea" id="tc-ig-topic" rows="4" placeholder="Describe your photo, video, or post topic...&#10;e.g., Beach sunset vacation, new product launch, workout motivation"></textarea>
        </div>

        <div class="tc-rsz-options">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Caption Tone</h4>
                <?php $this->render_mode_buttons('ig-tone', [
                    'casual'   => 'Casual',
                    'professional' => 'Professional',
                    'funny'    => 'Funny',
                    'inspirational' => 'Inspirational',
                    'educational' => 'Educational',
                    'promotional' => 'Promotional',
                ], 'casual'); ?>
            </div>
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Options</h4>
                <div class="tc-rsz-toggles">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-ig-emojis" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Include emojis</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-ig-hashtags" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Include hashtags</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-ig-cta" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Include call-to-action</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-ig-linebreaks">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Line breaks between sections</b></span>
                    </label>
                </div>
            </div>
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Caption Length <span class="tc-rsz-quality-badge" id="tc-ig-length-val">Medium</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">Short</span>
                    <input type="range" class="tc-rsz-slider" id="tc-ig-length" min="1" max="3" step="1" value="2">
                    <span class="tc-rsz-slider-max">Long</span>
                </div>
            </div>
        </div>

        <?php $this->render_progress_bar('tc-ig-progress', 'Generating captions...'); ?>

        <?php $this->render_actions('tc-ig-generate', 'Generate Captions', '', ''); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Captions</span><span class="tc-stat-value" id="tc-ig-count">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Avg Length</span><span class="tc-stat-value" id="tc-ig-avglen">0 chars</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Generated Captions</h3>
                    <span id="tc-status-chip">Ready</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-tabs-header">
                        <h4>Results</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">All Captions</button>
                            <button data-tab="result">Hashtag Sets</button>
                        </div>
                    </div>

                    <div class="tc-preview" data-tab-content="original" id="tc-ig-captions">
                        <p style="color:var(--muted);font-size:14px;">Enter a topic and click Generate to create Instagram captions.</p>
                    </div>

                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-ig-hashtag-sets">
                        <p style="color:var(--muted);font-size:14px;">Hashtag sets will appear here after generation.</p>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
