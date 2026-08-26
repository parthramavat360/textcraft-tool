<?php
/**
 * Widget: Social Media Character Counter
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Social_Char_Counter extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'social_char_counter'; }
    public function get_title(): string { return 'Social Media Character Counter'; }
    public function get_icon(): string { return 'eicon-text-editor'; }

    public function get_keywords(): array {
        return ['character counter','twitter character count','instagram character limit','social media counter','tweet length'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Check your text against character limits for Twitter, Instagram, Facebook, LinkedIn, TikTok, and YouTube. See exactly how many characters you have left in real time.
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Your text</label>
            <textarea class="tc-textarea" id="tc-sc-text" rows="6" placeholder="Type or paste your text here to check character counts..."></textarea>
        </div>

        <div class="tc-rsz-options">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Target Platform</h4>
                <?php $this->render_mode_buttons('sc-platform', [
                    'twitter'   => 'Twitter / X',
                    'instagram' => 'Instagram',
                    'facebook'  => 'Facebook',
                    'linkedin'  => 'LinkedIn',
                    'tiktok'    => 'TikTok',
                    'youtube'   => 'YouTube',
                ], 'twitter'); ?>
            </div>
        </div>

        <?php $this->render_progress_bar('tc-sc-progress', 'Counting...'); ?>

        <?php $this->render_actions('tc-sc-count', 'Count Characters', 'tc-sc-clear', 'Clear'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Characters</span><span class="tc-stat-value" id="tc-sc-chars">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Words</span><span class="tc-stat-value" id="tc-sc-words">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Remaining</span><span class="tc-stat-value" id="tc-sc-remaining">0</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Character Counts</h3>
                    <span id="tc-status-chip">Ready</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-tabs-header">
                        <h4>Results</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Platform Limits</button>
                            <button data-tab="result">Tips</button>
                        </div>
                    </div>

                    <div class="tc-preview" data-tab-content="original" id="tc-sc-platforms">
                        <p style="color:var(--muted);font-size:14px;">Enter text to see character counts for all platforms.</p>
                    </div>

                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-sc-tips">
                        <p style="color:var(--muted);font-size:14px;">Character count tips will appear here.</p>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
