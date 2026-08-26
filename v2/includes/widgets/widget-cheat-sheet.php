<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Cheat_Sheet extends TextCraft_Tool_Base {
    protected bool $show_preview = true;
    public function get_name(): string { return 'cheat_sheet'; }
    public function get_title(): string { return 'Cheat Sheet'; }
    public function get_icon(): string { return 'eicon-document-file';
    }
    public function get_keywords(): array { return ['cheat sheet','reference','quick reference','syntax guide','reference card']; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Quick reference cheat sheets for Markdown, JSON, and Regex syntax. Bookmark for easy access while coding or writing.</div>

        <div class="tc-rsz-options">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Cheat Sheet</h4>
                <?php $this->render_mode_buttons('cs-type', ['markdown'=>'Markdown','json'=>'JSON','regex'=>'Regex'], 'markdown'); ?>
            </div>
        </div>

        <?php $this->render_actions('tc-cs-show', 'Show Reference', '', ''); ?>
    <?php }

    protected function render_result_content(array $settings): void {}
    protected function render_result(array $settings): void { ?>
        <div class="tc-result-col"><div class="tc-panel">
            <div class="tc-panel-head"><h3>2 &middot; Reference</h3><span id="tc-status-chip">Ready</span></div>
            <div class="tc-panel-body">
                <div class="tc-preview" data-tab-content="original" id="tc-cs-content"><p style="color:var(--muted);font-size:14px;">Select a cheat sheet and click Show Reference.</p></div>
            </div>
        </div></div>
    <?php }
}
