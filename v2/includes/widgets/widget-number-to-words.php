<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Number_To_Words extends TextCraft_Tool_Base {
    public function get_name(): string { return 'number_to_words'; }
    public function get_title(): string { return 'Number to Words'; }
    public function get_icon(): string { return 'eicon-shortcode'; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Convert any number into written words. Supports integers, decimals, and multiple currencies. 123 becomes "one hundred twenty three".</div>

        <div class="tc-input-group">
            <label class="tc-label">Enter a number</label>
            <input type="text" class="tc-input" id="ntw-number" placeholder="e.g. 1234567.89" value="">
        </div>

        <div class="tc-grid-2col">
            <div class="tc-input-group">
                <label class="tc-label">Format</label>
                <select class="tc-select" id="ntw-format">
                    <option value="cardinal" selected>Cardinal (one hundred twenty three)</option>
                    <option value="ordinal">Ordinal (one hundred twenty third)</option>
                    <option value="currency">Currency ($ one hundred twenty three)</option>
                </select>
            </div>
            <div class="tc-input-group" id="ntw-currency-group" style="display:none">
                <label class="tc-label">Currency</label>
                <select class="tc-select" id="ntw-currency">
                    <option value="$">USD ($)</option>
                    <option value="EUR">EUR</option>
                    <option value="GBP">GBP</option>
                    <option value="INR">INR</option>
                    <option value="JPY">JPY</option>
                </select>
            </div>
        </div>

        <div class="tc-checkboxes">
            <label class="tc-check"><input type="checkbox" id="ntw-capitalize" checked> Capitalize first letter</label>
            <label class="tc-check"><input type="checkbox" id="ntw-and"> Include "and" (one hundred and twenty three)</label>
        </div>

        <button class="tc-btn tc-btn--primary" id="ntw-convert"><i class="fa-solid fa-wand-magic-sparkles"></i> Convert</button>

        <div class="tctp-result" id="ntw-result" style="display:none">
            <div class="tc-label">Result</div>
            <div class="tctp-rsz-tabs">
                <button class="tctp-rsz-tab sel" data-tab="words">In Words</button>
                <button class="tctp-rsz-tab" data-tab="upper">UPPERCASE</button>
            </div>
            <div class="tctp-rsz-tab-panel" id="ntw-words"></div>
            <div class="tctp-rsz-tab-panel" id="ntw-upper" style="display:none"></div>
        </div>
    <?php }
}
