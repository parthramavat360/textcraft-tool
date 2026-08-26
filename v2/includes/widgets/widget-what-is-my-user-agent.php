<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_What_Is_My_User_Agent extends TextCraft_Tool_Base {
    protected bool $show_preview = false;
    public function get_name(): string { return 'what_is_my_user_agent'; }
    public function get_title(): string { return 'What Is My User Agent'; }
    public function get_icon(): string { return 'eicon-user-square-2'; }
    public function get_keywords(): array { return ['user agent','browser info','what is my browser','user agent checker','browser version']; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Instantly detect and display your browser user agent string along with detailed information about your browser, OS, device, and screen.</div>

        <div class="tctp-tool-body">
            <div class="tctp-result" id="uad-result">
                <div class="tctp-rsz-tabs">
                    <button class="tctp-rsz-tab sel" data-tab="overview">Overview</button>
                    <button class="tctp-rsz-tab" data-tab="raw">Raw String</button>
                    <button class="tctp-rsz-tab" data-tab="technical">Technical</button>
                </div>

                <div class="tctp-rsz-tab-panel" id="uad-overview">
                    <div id="uad-overview-content"></div>
                </div>
                <div class="tctp-rsz-tab-panel" id="uad-raw" style="display:none;">
                    <div id="uad-raw-content"></div>
                </div>
                <div class="tctp-rsz-tab-panel" id="uad-technical" style="display:none;">
                    <div id="uad-technical-content"></div>
                </div>

                <div style="margin-top:12px; display:flex; gap:8px; flex-wrap:wrap;">
                    <button class="tc-btn tc-btn--ghost tc-btn--sm" id="uad-copy-ua"><i class="fa-regular fa-clipboard"></i> Copy User Agent</button>
                    <button class="tc-btn tc-btn--ghost tc-btn--sm" id="uad-copy-all"><i class="fa-solid fa-copy"></i> Copy All Info</button>
                </div>
            </div>
        </div>
    <?php }
}