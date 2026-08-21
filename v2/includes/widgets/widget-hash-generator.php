<?php
/**
 * Widget: Hash Generator
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Hash_Generator extends TextCraft_Tool_Base {

    public function get_name(): string { return 'hash_generator'; }
    public function get_title(): string { return 'Hash Generator'; }
    public function get_icon(): string { return 'eicon-fingerprint'; }

    public function get_keywords(): array {
        return ['hash generator', 'md5', 'sha1', 'sha256', 'checksum', 'hash function'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Generate cryptographic hashes from any text. Supports MD5, SHA-1, SHA-256, and SHA-512 algorithms. All hashing is done in your browser.
        </div>

        <?php $this->render_textarea('tc-hash-input', 'Enter text to hash...', 8); ?>

        <div class="tc-input-group">
            <label class="tc-label">Algorithms</label>
            <div class="tc-checkboxes">
                <?php $this->render_checkbox('tc-hash-md5', 'MD5', true); ?>
                <?php $this->render_checkbox('tc-hash-sha1', 'SHA-1', true); ?>
                <?php $this->render_checkbox('tc-hash-sha256', 'SHA-256', true); ?>
                <?php $this->render_checkbox('tc-hash-sha512', 'SHA-512', false); ?>
            </div>
        </div>

        <div class="tc-input-group">
            <div class="tc-checkboxes">
                <?php $this->render_checkbox('tc-hash-uppercase', 'Output uppercase', false); ?>
            </div>
        </div>

        <?php $this->render_actions('tc-hash-generate', 'Generate Hashes', 'tc-hash-copy-all', 'Copy All'); ?>

        <?php $this->render_status('tc-hash-status'); ?>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-hash-result">
            <textarea class="tc-textarea" id="tc-hash-results" placeholder="Hash results will appear here..." readonly rows="8"></textarea>
        </div>
        <?php
    }
}
