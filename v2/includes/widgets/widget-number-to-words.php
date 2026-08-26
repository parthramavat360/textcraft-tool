<?php
/**
 * Widget: Number to Words
 * Convert numbers to English words (123 = "one hundred twenty three").
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Number_To_Words extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'number_to_words'; }
    public function get_title(): string { return 'Number to Words'; }
    public function get_icon(): string { return 'eicon-shortcode'; }

    public function get_keywords(): array {
        return ['number to words', 'number in words', 'convert number to words', 'number to text', 'spell number'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert any number into English words. Supports integers, decimals, and currency formatting.
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Enter number</label>
            <input type="text" class="tc-input" id="tc-nw-input" placeholder="Enter a number (e.g. 12345.67)" inputmode="decimal" value="1234567">
        </div>

        <div class="tc-rsz-options" style="margin-top:16px">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Format</h4>
                <div class="tc-rsz-mode-cards tc-nw-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="words">
                        <span class="tc-rsz-mode-text"><b>Words</b><span>one million...</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="currency">
                        <span class="tc-rsz-mode-text"><b>Currency</b><span>$ amount</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="ordinal">
                        <span class="tc-rsz-mode-text"><b>Ordinal</b><span>1st, 2nd...</span></span>
                    </button>
                </div>
            </div>
        </div>

        <div class="tc-nw-currency-row" id="tc-nw-currency-row" style="display:none">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Currency</h4>
                <div class="tc-rsz-mode-cards tc-nw-curr-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="USD">$ USD</button>
                    <button class="tc-rsz-mode-card" type="button" data-val="EUR">€ EUR</button>
                    <button class="tc-rsz-mode-card" type="button" data-val="GBP">£ GBP</button>
                    <button class="tc-rsz-mode-card" type="button" data-val="INR">₹ INR</button>
                </div>
            </div>
        </div>

        <div class="tc-nw-actions">
            <button class="tc-btn tc-btn--accent" id="tc-nw-convert" type="button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 3 21 3 21 8"/><line x1="4" y1="20" x2="21" y2="3"/><polyline points="21 16 21 21 16 21"/><line x1="15" y1="15" x2="21" y2="21"/></svg>
                Convert
            </button>
            <button class="tc-btn tc-btn--ghost" id="tc-nw-copy" type="button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                Copy
            </button>
        </div>

        <div class="tc-nw-output" id="tc-nw-output">
            <p class="tc-nw-placeholder">Result will appear here</p>
        </div>

        <?php
    }

    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 · Result</h3>
                    <span id="tc-status-chip">Ready</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-stats">
                        <div><span>Number</span><b id="tc-stat-orig">0</b></div>
                        <div><span>Words</span><b id="tc-stat-comp">0</b></div>
                        <div class="saved"><span>Characters</span><b id="tc-stat-saved">0</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Output</h4>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <div id="tc-nw-preview" class="tc-nw-preview-box">
                            <p class="tc-nw-placeholder">Result will appear here</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
