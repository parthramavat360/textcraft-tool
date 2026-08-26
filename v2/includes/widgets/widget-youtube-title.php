<?php
/**
 * Widget: YouTube Title Generator
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Youtube_Title extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'youtube_title'; }
    public function get_title(): string { return 'YouTube Title Generator'; }
    public function get_icon(): string { return 'eicon-video-camera'; }

    public function get_keywords(): array {
        return ['youtube title generator','youtube title ideas','video title generator','youtube seo','clickbait title'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Generate SEO-optimized YouTube titles that get clicks. Enter your video topic and choose a style to get multiple title options optimized for YouTube search.
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Video topic</label>
            <textarea class="tc-textarea" id="tc-yt-topic" rows="3" placeholder="What is your video about?&#10;e.g., How to cook pasta, Python tutorial for beginners, Product review iPhone 15"></textarea>
        </div>

        <div class="tc-rsz-options">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Title Style</h4>
                <?php $this->render_mode_buttons('yt-style', [
                    'howto'      => 'How-To',
                    'listicle'   => 'Listicle',
                    'question'   => 'Question',
                    'ultimate'   => 'Ultimate Guide',
                    'clickbait'  => 'Curiosity Hook',
                    'review'     => 'Review',
                ], 'howto'); ?>
            </div>
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Options</h4>
                <div class="tc-rsz-toggles">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-yt-emoji" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Include emojis</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-yt-year" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Add year (2024/2025)</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-yt-power" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Power words (Best, Easy, Secret)</b></span>
                    </label>
                </div>
            </div>
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Number of Titles <span class="tc-rsz-quality-badge" id="tc-yt-num-val">10</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">5</span>
                    <input type="range" class="tc-rsz-slider" id="tc-yt-num" min="5" max="20" step="5" value="10">
                    <span class="tc-rsz-slider-max">20</span>
                </div>
            </div>
        </div>

        <?php $this->render_progress_bar('tc-yt-progress', 'Generating titles...'); ?>

        <?php $this->render_actions('tc-yt-generate', 'Generate Titles', '', ''); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Titles</span><span class="tc-stat-value" id="tc-yt-count">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Avg Length</span><span class="tc-stat-value" id="tc-yt-avglen">0 chars</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Generated Titles</h3>
                    <span id="tc-status-chip">Ready</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-tabs-header">
                        <h4>Results</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">All Titles</button>
                            <button data-tab="result">SEO Analysis</button>
                        </div>
                    </div>

                    <div class="tc-preview" data-tab-content="original" id="tc-yt-titles">
                        <p style="color:var(--muted);font-size:14px;">Enter a video topic and click Generate to create titles.</p>
                    </div>

                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-yt-seo">
                        <p style="color:var(--muted);font-size:14px;">SEO analysis will appear after generation.</p>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
