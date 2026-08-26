<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Coin_Flipper extends TextCraft_Tool_Base {
    protected bool $show_preview = true;
    public function get_name(): string { return 'coin_flipper'; }
    public function get_title(): string { return 'Coin Flipper'; }
    public function get_icon(): string { return 'eicon-diamond';
    }
    public function get_keywords(): array { return ['coin flip','flip coin','heads or tails','random choice','coin toss']; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Flip a virtual coin to make random decisions. Heads or tails — perfect for settling debates, quick choices, and 50/50 decisions.</div>

        <div class="tc-rsz-options">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Flips at Once <span class="tc-rsz-quality-badge" id="tc-cf-count-val">1</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">1</span>
                    <input type="range" class="tc-rsz-slider" id="tc-cf-count" min="1" max="100" step="1" value="1">
                    <span class="tc-rsz-slider-max">100</span>
                </div>
            </div>
        </div>

        <?php $this->render_progress_bar('tc-cf-progress', 'Flipping...'); ?>
        <?php $this->render_actions('tc-cf-flip', 'Flip Coin', '', ''); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Heads</span><span class="tc-stat-value" id="tc-cf-heads">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Tails</span><span class="tc-stat-value" id="tc-cf-tails">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Total Flips</span><span class="tc-stat-value" id="tc-cf-total">0</span></div>
        </div>
    <?php }

    protected function render_result_content(array $settings): void {}
    protected function render_result(array $settings): void { ?>
        <div class="tc-result-col"><div class="tc-panel">
            <div class="tc-panel-head"><h3>2 &middot; Flip Results</h3><span id="tc-status-chip">Ready</span></div>
            <div class="tc-panel-body">
                <div class="tc-tabs-header"><h4>Results</h4><div class="tc-tabs"><button class="on" data-tab="original">Flip History</button><button data-tab="result">Statistics</button></div></div>
                <div class="tc-preview" data-tab-content="original" id="tc-cf-results"><p style="color:var(--muted);font-size:14px;">Click Flip Coin to flip.</p></div>
                <div class="tc-preview is-hidden" data-tab-content="result" id="tc-cf-stats"><p style="color:var(--muted);font-size:14px;">Statistics will appear after flipping.</p></div>
            </div>
        </div></div>
    <?php }
}
