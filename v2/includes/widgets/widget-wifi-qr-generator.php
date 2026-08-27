<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Wifi_Qr_Generator extends TextCraft_Tool_Base {
    public function get_name(): string { return 'wifi_qr_generator'; }
    public function get_title(): string { return 'WiFi QR Code Generator'; }
    public function get_icon(): string { return 'eicon-qr'; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Generate a QR code that connects devices to your WiFi network instantly. Just scan with your phone camera. Supports WPA/WPA2/WEP and open networks.</div>
        <div class="tc-rsz-toggles">
            <label class="tc-rsz-toggle">
                <input type="checkbox" class="tc-rsz-toggle-input" id="wifi-hidden">
                <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                <span class="tc-rsz-toggle-text">Hidden Network</span>
            </label>
        </div>
        <div class="tc-input-group">
            <label class="tc-label">Network Name (SSID)</label>
            <input type="text" class="tc-input" id="wifi-ssid" placeholder="MyWiFiNetwork">
        </div>
        <div class="tc-input-group">
            <label class="tc-label">Password</label>
            <input type="password" class="tc-input" id="wifi-pass" placeholder="Enter password">
        </div>
        <div class="tc-input-group">
            <label class="tc-label">Security Type</label>
            <select class="tc-input tc-select" id="wifi-security">
                <option value="WPA">WPA/WPA2/WPA3</option>
                <option value="WEP">WEP</option>
                <option value="nopass">None (Open)</option>
            </select>
        </div>
        <div class="tc-actions">
            <button class="tc-btn tc-btn--primary" id="wifi-generate">Generate QR Code</button>
        </div>
        <div class="tc-result-panel" id="wifi-result" style="display:none">
            <div class="tc-result-header">
                <span class="tc-status-chip" id="wifi-status">QR Code</span>
                <button class="tc-btn tc-btn--outline" id="wifi-download">Download PNG</button>
            </div>
            <div class="tc-result-body" style="text-align:center;padding:20px">
                <canvas id="wifi-qr-canvas" style="max-width:100%;image-rendering:pixelated"></canvas>
                <p style="margin-top:12px;color:#6b7280;font-size:13px">Scan with your phone camera to connect</p>
            </div>
        </div>
    <?php }

    protected function render_result_content(array $settings): void { ?>
        <div></div>
    <?php }
}
