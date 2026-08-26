<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Fake_Name_Generator extends TextCraft_Tool_Base {
    protected bool $show_preview = false;
    public function get_name(): string { return 'fake_name_generator'; }
    public function get_title(): string { return 'Fake Name Generator'; }
    public function get_icon(): string { return 'eicon-user-6'; }
    public function get_keywords(): array { return ['fake name','random name','identity generator','fake address','random identity','name generator']; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Generate realistic fake identities with names, addresses, phone numbers, emails, and more. Useful for testing, development, and placeholder data.</div>

        <div class="tctp-tool-body">
            <div class="tctp-ctrls">
                <div class="tctp-flex" style="gap:12px; flex-wrap:wrap; align-items:flex-end;">
                    <div class="tc-input-group" style="width:160px;">
                        <label class="tc-label">Region</label>
                        <select class="tc-select" id="fng-region">
                            <option value="us">United States</option>
                            <option value="uk">United Kingdom</option>
                            <option value="ca">Canada</option>
                            <option value="au">Australia</option>
                            <option value="de">Germany</option>
                            <option value="fr">France</option>
                            <option value="in">India</option>
                            <option value="br">Brazil</option>
                            <option value="jp">Japan</option>
                        </select>
                    </div>
                    <div class="tc-input-group" style="width:140px;">
                        <label class="tc-label">Gender</label>
                        <select class="tc-select" id="fng-gender">
                            <option value="random">Random</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="tc-input-group" style="width:120px;">
                        <label class="tc-label">Count</label>
                        <select class="tc-select" id="fng-count">
                            <option value="1">1</option>
                            <option value="5" selected>5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                    <button class="tc-btn tc-btn--primary" id="fng-generate">
                        <i class="fa-solid fa-user-plus"></i> Generate
                    </button>
                </div>
            </div>

            <?php $this->render_progress_bar('Generating fake identities...'); ?>

            <div class="tctp-result" id="fng-result" style="display:none;">
                <div class="tctp-rsz-tabs">
                    <button class="tctp-rsz-tab sel" data-tab="cards">Cards</button>
                    <button class="tctp-rsz-tab" data-tab="table">Table</button>
                    <button class="tctp-rsz-tab" data-tab="json">JSON</button>
                </div>

                <div class="tctp-rsz-tab-panel" id="fng-cards"></div>
                <div class="tctp-rsz-tab-panel" id="fng-table" style="display:none;"></div>
                <div class="tctp-rsz-tab-panel" id="fng-json" style="display:none;"><pre class="tctp-code-pre" id="fng-json-pre"></pre></div>

                <div style="margin-top:12px; display:flex; gap:8px; flex-wrap:wrap;">
                    <button class="tc-btn tc-btn--ghost tc-btn--sm" id="fng-copy-all"><i class="fa-regular fa-clipboard"></i> Copy All</button>
                    <button class="tc-btn tc-btn--ghost tc-btn--sm" id="fng-copy-json"><i class="fa-solid fa-code"></i> Copy JSON</button>
                    <button class="tc-btn tc-btn--ghost tc-btn--sm" id="fng-copy-csv"><i class="fa-solid fa-file-csv"></i> Copy CSV</button>
                </div>
            </div>
        </div>
    <?php }
}