<?php
/**
 * Widget: Plain Text Converter
 * Premium design following case-converter pattern.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Plain_Text extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'plain_text'; }
    public function get_title(): string { return 'Plain Text Converter'; }
    public function get_icon(): string { return 'eicon-document-file-o'; }

    public function get_keywords(): array {
        return ['plain', 'text', 'html', 'strip', 'tags', 'convert', 'rich', 'formatting', 'clean'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-txtp">
        <div class="tc-tool-desc">
            Convert HTML and rich text to clean plain text. Strip tags, decode entities, normalize Unicode, and clean whitespace. Works entirely in your browser — no data is sent to any server.
        </div>

        <?php $this->render_textarea('tc-pt-input', 'Paste HTML or rich text here...', 8); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <div class="tc-rsz-mode-cards tc-pt-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="smart">
                        <span class="tc-rsz-mode-text"><b>Smart Convert</b><span>Strip tags + decode + clean</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="strip">
                        <span class="tc-rsz-mode-text"><b>Strip Tags</b><span>Remove &lt;tags&gt; only</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="decode">
                        <span class="tc-rsz-mode-text"><b>Decode Only</b><span>Entities + Unicode</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="minimal">
                        <span class="tc-rsz-mode-text"><b>Minimal</b><span>Just whitespace cleanup</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <div class="tc-rsz-toggles">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-pt-decode" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Decode HTML entities</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-pt-unicode">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Normalize Unicode</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-pt-blanklines">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Remove blank lines</b></span>
                    </label>
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-pt-dedup">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Collapse multiple spaces</b></span>
                    </label>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-pt-bar', 'Converting...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-pt-convert" type="button">Convert to Plain Text</button>
            <button class="tc-btn tc-btn--ghost" id="tc-pt-copy" type="button">Copy Result</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-pt-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Characters</span><span class="tc-stat-value" id="tc-pt-chars">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Words</span><span class="tc-stat-value" id="tc-pt-words">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Tags Removed</span><span class="tc-stat-value" id="tc-pt-tags">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-pt-saved">0%</span></div>
        </div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Result</h3>
                    <span id="tc-status-chip">Idle</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-stats">
                        <div><span>Original</span><b id="tc-stat-orig">&mdash;</b></div>
                        <div><span>Cleaned</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Difference</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original</button>
                            <button data-tab="result">Cleaned</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <textarea class="tc-textarea" id="tc-pt-preview-orig" placeholder="Original text will appear here..." readonly rows="10"></textarea>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <textarea class="tc-textarea" id="tc-pt-preview-result" placeholder="Plain text result will appear here..." readonly rows="10"></textarea>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
