<?php
/**
 * Widget: Random IP Address Generator
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Random_IP extends TextCraft_Tool_Base {

    public function get_name(): string { return 'random_ip'; }
    public function get_title(): string { return 'Random IP Address Generator'; }
    public function get_icon(): string { return 'eicon-globe'; }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Generate random IPv4 or IPv6 addresses for testing, development, or network simulations. All addresses are generated locally.
        </div>

        <div class="tc-input-group">
            <label class="tc-label">IP Version</label>
            <?php $this->render_mode_buttons('rip-version', [
                'ipv4' => 'IPv4',
                'ipv6' => 'IPv6',
            ], 'ipv4'); ?>
        </div>

        <div class="tc-input-group">
            <label class="tc-label">How Many</label>
            <input type="number" class="tc-input" id="tc-rip-count" value="10" min="1" max="1000">
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Options</label>
            <div class="tc-checkboxes">
                <label class="tc-check"><input type="checkbox" id="tc-rip-no-private" checked> Exclude private ranges</label>
                <label class="tc-check"><input type="checkbox" id="tc-rip-no-loopback" checked> Exclude loopback (127.x / ::1)</label>
            </div>
        </div>

        <?php $this->render_actions('tc-rip-generate', 'Generate IPs', 'tc-rip-copy', 'Copy All'); ?>

        <div class="tc-label" style="margin-top:16px">Generated IP Addresses</div>
        <textarea class="tc-textarea" id="tc-rip-output" rows="10" readonly placeholder="Your random IP addresses will appear here..."></textarea>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-rip-result">
            <textarea class="tc-textarea" id="tc-rip-result-text" placeholder="Result will appear here..." readonly rows="8"></textarea>
        </div>
        <?php
    }
}
