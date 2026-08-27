<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Ip_Address_Lookup extends TextCraft_Tool_Base {
    public function get_name(): string { return 'ip_address_lookup'; }
    public function get_title(): string { return 'IP Address Lookup'; }
    public function get_icon(): string { return 'eicon-device-server'; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Find your public IP address and get detailed geolocation information including country, city, ISP, timezone, and coordinates. Enter any IP to look it up.</div>

        <div class="tc-input-group">
            <label class="tc-label">Enter IP Address (optional — blank = your IP)</label>
            <input type="text" class="tc-input" id="ip-input" placeholder="e.g. 8.8.8.8 or leave blank for your IP">
        </div>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--primary" id="ip-lookup">Lookup IP</button>
        </div>

        <div class="tc-result-panel" id="ip-result" style="display:none">
            <div class="tc-result-header">
                <span class="tc-status-chip" id="ip-status">Ready</span>
            </div>
            <div class="tc-result-body" id="ip-output"></div>
        </div>
    <?php }

    protected function render_result_content(array $settings): void { ?>
        <div></div>
    <?php }
}
