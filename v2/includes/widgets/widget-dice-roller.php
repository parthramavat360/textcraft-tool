<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Dice_Roller extends TextCraft_Tool_Base {
    protected bool $show_preview = true;
    public function get_name(): string { return 'dice_roller'; }
    public function get_title(): string { return 'Dice Roller'; }
    public function get_icon(): string { return 'eicon-dice';
    }
    public function get_keywords(): array { return ['dice roller','roll dice','virtual dice','random number dice','dice simulator']; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Roll virtual dice with any number of sides. Perfect for board games, D&D, RPGs, and random number generation.</div>

        <div class="tc-rsz-options">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Dice Type</h4>
                <?php $this->render_mode_buttons('dr-sides', ['4'=>'D4','6'=>'D6','8'=>'D8','10'=>'D10','12'=>'D12','20'=>'D20','100'=>'D100'], '6'); ?>
            </div>
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Number of Dice <span class="tc-rsz-quality-badge" id="tc-dr-count-val">1</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">1</span>
                    <input type="range" class="tc-rsz-slider" id="tc-dr-count" min="1" max="10" step="1" value="1">
                    <span class="tc-rsz-slider-max">10</span>
                </div>
            </div>
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Modifier <span class="tc-rsz-quality-badge" id="tc-dr-mod-val">+0</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">-10</span>
                    <input type="range" class="tc-rsz-slider" id="tc-dr-mod" min="-10" max="10" step="1" value="0">
                    <span class="tc-rsz-slider-max">+10</span>
                </div>
            </div>
        </div>

        <?php $this->render_progress_bar('tc-dr-progress', 'Rolling...'); ?>
        <?php $this->render_actions('tc-dr-roll', 'Roll Dice', '', ''); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Rolls</span><span class="tc-stat-value" id="tc-dr-total">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Last Total</span><span class="tc-stat-value" id="tc-dr-last">—</span></div>
        </div>
    <?php }

    protected function render_result_content(array $settings): void {}
    protected function render_result(array $settings): void { ?>
        <div class="tc-result-col"><div class="tc-panel">
            <div class="tc-panel-head"><h3>2 &middot; Dice Results</h3><span id="tc-status-chip">Ready</span></div>
            <div class="tc-panel-body">
                <div class="tc-tabs-header"><h4>Results</h4><div class="tc-tabs"><button class="on" data-tab="original">Roll History</button><button data-tab="result">Statistics</button></div></div>
                <div class="tc-preview" data-tab-content="original" id="tc-dr-results"><p style="color:var(--muted);font-size:14px;">Click Roll Dice to roll.</p></div>
                <div class="tc-preview is-hidden" data-tab-content="result" id="tc-dr-stats"><p style="color:var(--muted);font-size:14px;">Statistics will appear after rolling.</p></div>
            </div>
        </div></div>
    <?php }
}
