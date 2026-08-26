<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Random_Name extends TextCraft_Tool_Base {
    protected bool $show_preview = true;
    public function get_name(): string { return 'random_name'; }
    public function get_title(): string { return 'Random Name Generator'; }
    public function get_icon(): string { return 'eicon-user';
    }
    public function get_keywords(): array { return ['random name','name generator','fake name','random person','name picker']; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Generate random first and last names for characters, usernames, testing, or creative projects. Filter by gender and quantity.</div>

        <div class="tc-rsz-options">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Gender</h4>
                <?php $this->render_mode_buttons('rn-gender', ['any'=>'Any','male'=>'Male','female'=>'Female'], 'any'); ?>
            </div>
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Name Format</h4>
                <?php $this->render_mode_buttons('rn-format', ['full'=>'First + Last','first'=>'First Name Only','last'=>'Last Name Only','fullmiddle'=>'First Middle Last'], 'full'); ?>
            </div>
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Quantity <span class="tc-rsz-quality-badge" id="tc-rn-num-val">10</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">1</span>
                    <input type="range" class="tc-rsz-slider" id="tc-rn-num" min="1" max="100" step="1" value="10">
                    <span class="tc-rsz-slider-max">100</span>
                </div>
            </div>
        </div>

        <?php $this->render_progress_bar('tc-rn-progress', 'Generating...'); ?>
        <?php $this->render_actions('tc-rn-generate', 'Generate Names', '', ''); ?>
    <?php }

    protected function render_result_content(array $settings): void {}
    protected function render_result(array $settings): void { ?>
        <div class="tc-result-col"><div class="tc-panel">
            <div class="tc-panel-head"><h3>2 &middot; Generated Names</h3><span id="tc-status-chip">Ready</span></div>
            <div class="tc-panel-body">
                <div class="tc-tabs-header"><h4>Results</h4><div class="tc-tabs"><button class="on" data-tab="original">All Names</button><button data-tab="result">Copy List</button></div></div>
                <div class="tc-preview" data-tab-content="original" id="tc-rn-names"><p style="color:var(--muted);font-size:14px;">Click Generate to create random names.</p></div>
                <div class="tc-preview is-hidden" data-tab-content="result" id="tc-rn-copylist"><p style="color:var(--muted);font-size:14px;">Copy list will appear here.</p></div>
            </div>
        </div></div>
    <?php }
}
