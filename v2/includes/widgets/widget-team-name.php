<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Team_Name extends TextCraft_Tool_Base {
    protected bool $show_preview = true;
    public function get_name(): string { return 'team_name'; }
    public function get_title(): string { return 'Team Name Generator'; }
    public function get_icon(): string { return 'eicon-team';
    }
    public function get_keywords(): array { return ['team name','group name','squad name','team name ideas','group name generator']; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Generate fun, creative, and catchy team names for work teams, sports teams, quiz teams, gaming squads, and more.</div>

        <div class="tc-input-group">
            <label class="tc-label">Team topic or keyword (optional)</label>
            <input type="text" class="tc-input" id="tc-tn-topic" placeholder="e.g., coding, soccer, marketing, trivia...">
        </div>

        <div class="tc-rsz-options">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Style</h4>
                <?php $this->render_mode_buttons('tn-style', ['funny'=>'Funny','cool'=>'Cool','professional'=>'Professional','creative'=>'Creative','punny'=>'Punny'], 'funny'); ?>
            </div>
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Quantity <span class="tc-rsz-quality-badge" id="tc-tn-num-val">10</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">5</span>
                    <input type="range" class="tc-rsz-slider" id="tc-tn-num" min="5" max="50" step="5" value="10">
                    <span class="tc-rsz-slider-max">50</span>
                </div>
            </div>
        </div>

        <?php $this->render_progress_bar('tc-tn-progress', 'Generating...'); ?>
        <?php $this->render_actions('tc-tn-generate', 'Generate Names', '', ''); ?>
    <?php }

    protected function render_result_content(array $settings): void {}
    protected function render_result(array $settings): void { ?>
        <div class="tc-result-col"><div class="tc-panel">
            <div class="tc-panel-head"><h3>2 &middot; Team Names</h3><span id="tc-status-chip">Ready</span></div>
            <div class="tc-panel-body">
                <div class="tc-tabs-header"><h4>Results</h4><div class="tc-tabs"><button class="on" data-tab="original">All Names</button><button data-tab="result">Copy List</button></div></div>
                <div class="tc-preview" data-tab-content="original" id="tc-tn-names"><p style="color:var(--muted);font-size:14px;">Click Generate to create team names.</p></div>
                <div class="tc-preview is-hidden" data-tab-content="result" id="tc-tn-copylist"><p style="color:var(--muted);font-size:14px;">Copy list will appear here.</p></div>
            </div>
        </div></div>
    <?php }
}
