<?php
/**
 * Widget: Cipher Tools
 * Caesar, ROT13, A1Z26, Vigenere, Atbash, AES, UTF-8, HTML Entity, Unicode Inspector.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Cipher_Tools extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'cipher_tools'; }
    public function get_title(): string { return 'Cipher Tools'; }
    public function get_icon(): string { return 'eicon-lock'; }

    public function get_keywords(): array {
        return ['caesar cipher', 'rot13', 'vigenere', 'atbash', 'a1z26', 'aes encrypt', 'utf8 encoder', 'html entity', 'unicode inspector', 'cipher', 'encode', 'decode'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Encrypt, decrypt, and encode text with classic ciphers and modern encodings. All processing happens in your browser.
        </div>

        <div class="tc-rsz-options">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Tool</h4>
                <div class="tc-rsz-mode-cards tc-ct-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="caesar"><b>Caesar</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="rot13"><b>ROT13</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="a1z26"><b>A1Z26</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="vigenere"><b>Vigenere</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="atbash"><b>Atbash</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="aes"><b>AES</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="utf8"><b>UTF-8</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="html"><b>HTML Entity</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="unicode"><b>Unicode</b></button>
                </div>
            </div>
        </div>

        <div class="tc-ct-extra" id="tc-ct-extra">
            <div class="tc-rsz-section" id="tc-ct-shift-wrap">
                <label class="tc-label"><b>Shift (1-25):</b></label>
                <input type="range" class="tc-rsz-range" id="tc-ct-shift" min="1" max="25" value="3">
                <span id="tc-ct-shift-val">3</span>
            </div>
            <div class="tc-rsz-section" id="tc-ct-key-wrap" style="display:none">
                <div class="tc-input-group">
                    <label class="tc-label"><b>Key:</b></label>
                    <input type="text" class="tc-input" id="tc-ct-key" placeholder="Enter keyword (letters only)" style="max-width:300px">
                </div>
            </div>
            <div class="tc-rsz-section" id="tc-ct-password-wrap" style="display:none">
                <div class="tc-input-group">
                    <label class="tc-label"><b>Password:</b></label>
                    <input type="text" class="tc-input" id="tc-ct-password" placeholder="Enter password for AES encryption" style="max-width:300px">
                </div>
            </div>
        </div>

        <div class="tc-ct-io">
            <div class="tc-ct-col">
                <h4 class="tc-du-col-title" id="tc-ct-input-label">Input</h4>
                <textarea class="tc-textarea tc-ct-textarea" id="tc-ct-input" placeholder="Enter text to encode/decode..." rows="8"></textarea>
            </div>
            <div class="tc-ct-col">
                <h4 class="tc-du-col-title" id="tc-ct-output-label">Output</h4>
                <pre class="tc-du-output" id="tc-ct-output"><code>Output will appear here</code></pre>
            </div>
        </div>

        <div class="tc-ct-actions">
            <button class="tc-btn tc-btn--accent" id="tc-ct-convert" type="button">Encrypt / Decrypt</button>
            <button class="tc-btn tc-btn--ghost" id="tc-ct-copy" type="button">Copy Output</button>
        </div>

        <?php
    }
}
