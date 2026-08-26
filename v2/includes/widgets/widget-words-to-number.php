<?php
/**
 * Widget: Words to Number
 * Convert English words to numbers ("one hundred twenty three" = 123).
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Words_To_Number extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'words_to_number'; }
    public function get_title(): string { return 'Words to Number'; }
    public function get_icon(): string { return 'eicon-shortcode'; }

    public function get_keywords(): array {
        return ['words to number', 'spell to number', 'text to number', 'convert words to digits'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert English number words into digits. Type "one hundred twenty three" and get 123.
        </div>

        <textarea class="tc-textarea" id="tc-wn-input" placeholder="Type number words (e.g. one million, two hundred thirty four thousand, five hundred sixty seven)..." rows="4">one million two hundred thirty four thousand five hundred sixty seven</textarea>

        <div class="tc-wn-actions">
            <button class="tc-btn tc-btn--accent" id="tc-wn-convert" type="button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 3 21 3 21 8"/><line x1="4" y1="20" x2="21" y2="3"/><polyline points="21 16 21 21 16 21"/><line x1="15" y1="15" x2="21" y2="21"/></svg>
                Convert
            </button>
            <button class="tc-btn tc-btn--ghost" id="tc-wn-copy" type="button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                Copy
            </button>
        </div>

        <div class="tc-wn-output" id="tc-wn-output">
            <p class="tc-wn-placeholder">Result will appear here</p>
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
                        <div><span>Input</span><b id="tc-stat-orig">—</b></div>
                        <div><span>Number</span><b id="tc-stat-comp">0</b></div>
                        <div class="saved"><span>Digits</span><b id="tc-stat-saved">0</b></div>
                    </div>
                    <div class="tc-tabs-header"><h4>Output</h4></div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <div id="tc-wn-preview" class="tc-wn-preview-box">
                            <p class="tc-wn-placeholder">Result will appear here</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
