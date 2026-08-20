<?php
/**
 * Widget: QR Code Generator
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_QR_Generator extends TextCraft_Tool_Base {

    public function get_name(): string { return 'qr_generator'; }
    public function get_title(): string { return 'QR Code Generator'; }
    public function get_icon(): string { return 'eicon-apps'; }

    public function get_keywords(): array {
        return ['qr code', 'qr generator', 'qr code maker', 'barcode', 'qr image'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Generate QR codes from text, URLs, email addresses, phone numbers, or WiFi credentials. Download as SVG for crisp scaling at any size.
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Content Type</label>
            <?php $this->render_mode_buttons('qr-type', [
                'text'  => 'Text / URL',
                'email' => 'Email',
                'phone' => 'Phone',
                'wifi'  => 'WiFi',
                'vcard' => 'vCard',
            ], 'text'); ?>
        </div>

        <div class="tc-input-group" id="tc-qr-text-group">
            <label class="tc-label">Text or URL</label>
            <input type="text" class="tc-input" id="tc-qr-text" placeholder="Enter text or URL...">
        </div>

        <div class="tc-input-group" id="tc-qr-email-group" style="display:none">
            <label class="tc-label">Email Address</label>
            <input type="email" class="tc-input" id="tc-qr-email" placeholder="user@example.com">
            <label class="tc-label" style="margin-top:8px">Subject (optional)</label>
            <input type="text" class="tc-input" id="tc-qr-email-subject" placeholder="Email subject...">
            <label class="tc-label" style="margin-top:8px">Body (optional)</label>
            <textarea class="tc-textarea" id="tc-qr-email-body" rows="3" placeholder="Email body..."></textarea>
        </div>

        <div class="tc-input-group" id="tc-qr-phone-group" style="display:none">
            <label class="tc-label">Phone Number</label>
            <input type="tel" class="tc-input" id="tc-qr-phone" placeholder="+1234567890">
        </div>

        <div class="tc-input-group" id="tc-qr-wifi-group" style="display:none">
            <label class="tc-label">Network Name (SSID)</label>
            <input type="text" class="tc-input" id="tc-qr-wifi-ssid" placeholder="WiFi network name">
            <label class="tc-label" style="margin-top:8px">Password</label>
            <input type="text" class="tc-input" id="tc-qr-wifi-pass" placeholder="WiFi password">
            <label class="tc-label" style="margin-top:8px">Encryption</label>
            <?php $this->render_select('tc-qr-wifi-enc', [
                'WPA'  => 'WPA/WPA2',
                'WEP'  => 'WEP',
                'nopass' => 'None',
            ]); ?>
        </div>

        <div class="tc-input-group" id="tc-qr-vcard-group" style="display:none">
            <div class="tc-grid-2col">
                <div><label class="tc-label">First Name</label><input type="text" class="tc-input" id="tc-qr-vcard-fn" placeholder="John"></div>
                <div><label class="tc-label">Last Name</label><input type="text" class="tc-input" id="tc-qr-vcard-ln" placeholder="Doe"></div>
            </div>
            <label class="tc-label" style="margin-top:8px">Phone</label>
            <input type="tel" class="tc-input" id="tc-qr-vcard-phone" placeholder="+1234567890">
            <label class="tc-label" style="margin-top:8px">Email</label>
            <input type="email" class="tc-input" id="tc-qr-vcard-email" placeholder="john@example.com">
        </div>

        <div class="tc-grid-2col">
            <div class="tc-input-group">
                <label class="tc-label">Size</label>
                <?php $this->render_range('tc-qr-size', 128, 1024, 256, 'Pixel Size', 'px'); ?>
            </div>
            <div class="tc-input-group">
                <label class="tc-label">Error Correction</label>
                <?php $this->render_select('tc-qr-ecc', [
                    'L' => 'Low (7%)',
                    'M' => 'Medium (15%)',
                    'Q' => 'Quartile (25%)',
                    'H' => 'High (30%)',
                ]); ?>
            </div>
        </div>

        <?php $this->render_actions('tc-qr-generate', 'Generate QR Code', 'tc-qr-download-svg', 'Download SVG'); ?>

        <div class="tc-qr-preview" id="tc-qr-preview"></div>

        <?php $this->render_status('tc-qr-status'); ?>
        <?php
    }
}
