<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Random_Emoji extends TextCraft_Tool_Base {
    protected bool $show_preview = true;
    public function get_name(): string { return 'random_emoji'; }
    public function get_title(): string { return 'Random Emoji Picker'; }
    public function get_icon(): string { return 'eicon-smile';
    }
    public function get_keywords(): array { return ['random emoji','emoji picker','random emoji generator','emoji random','pick emoji']; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Pick random emojis from any category. Great for social media posts, messaging, reactions, and adding flair to your content.</div>

        <div class="tc-rsz-options">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Category</h4>
                <?php $this->render_mode_buttons('re-cat', ['all'=>'All','smileys'=>'Smileys','animals'=>'Animals','food'=>'Food','travel'=>'Travel','objects'=>'Objects','symbols'=>'Symbols','flags'=>'Flags'], 'all'); ?>
            </div>
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Quantity <span class="tc-rsz-quality-badge" id="tc-re-num-val">5</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">1</span>
                    <input type="range" class="tc-rsz-slider" id="tc-re-num" min="1" max="50" step="1" value="5">
                    <span class="tc-rsz-slider-max">50</span>
                </div>
            </div>
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Options</h4>
                <div class="tc-rsz-toggles">
                    <label class="tc-rsz-toggle"><input type="checkbox" class="tc-rsz-toggle-input" id="tc-re-unique" checked><span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span><span class="tc-rsz-toggle-text"><b>No duplicates</b></span></label>
                </div>
            </div>
        </div>

        <?php $this->render_progress_bar('tc-re-progress', 'Picking...'); ?>
        <?php $this->render_actions('tc-re-pick', 'Pick Emojis', '', ''); ?>
    <?php }

    protected function render_result_content(array $settings): void {}
    protected function render_result(array $settings): void { ?>
        <div class="tc-result-col"><div class="tc-panel">
            <div class="tc-panel-head"><h3>2 &middot; Picked Emojis</h3><span id="tc-status-chip">Ready</span></div>
            <div class="tc-panel-body">
                <div class="tc-tabs-header"><h4>Results</h4><div class="tc-tabs"><button class="on" data-tab="original">Emojis</button><button data-tab="result">Copy All</button></div></div>
                <div class="tc-preview" data-tab-content="original" id="tc-re-results"><p style="color:var(--muted);font-size:14px;">Click Pick Emojis to get random emojis.</p></div>
                <div class="tc-preview is-hidden" data-tab-content="result" id="tc-re-copy"><p style="color:var(--muted);font-size:14px;">Copy list will appear here.</p></div>
            </div>
        </div></div>
    <?php }
}
