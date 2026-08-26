<?php
/**
 * Widget: Tip Calculator
 * Calculate tip amounts, total bill, and per-person split.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Tip_Calculator extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'tip_calculator'; }
    public function get_title(): string { return 'Tip Calculator'; }
    public function get_icon(): string { return 'eicon-dollar'; }

    public function get_keywords(): array {
        return ['tip calculator', 'calculate tip', 'bill splitter', 'tip amount', 'how much tip'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Split your restaurant bill and calculate the perfect tip. Enter your bill amount, select a tip percentage, and choose how many ways to split.
        </div>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Bill Amount ($)</h4>
                <input type="number" class="tc-rsz-num tc-tip-input" id="tc-tip-bill" value="50.00" min="0.01" max="99999" step="0.01" placeholder="Enter bill total">
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Tip Percentage</h4>
                <div class="tc-rsz-mode-cards tc-tip-pct-modes">
                    <button class="tc-rsz-mode-card" type="button" data-val="10"><span class="tc-rsz-mode-text"><b>10%</b><span>Average</span></span></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="15"><span class="tc-rsz-mode-text"><b>15%</b><span>Good</span></span></button>
                    <button class="tc-rsz-mode-card sel" type="button" data-val="20"><span class="tc-rsz-mode-text"><b>20%</b><span>Great</span></span></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="25"><span class="tc-rsz-mode-text"><b>25%</b><span>Excellent</span></span></button>
                </div>
                <div style="margin-top:10px">
                    <label class="tc-rsz-dim-label">Custom Tip %</label>
                    <input type="number" class="tc-rsz-num tc-tip-input" id="tc-tip-custom" value="" min="0" max="100" step="1" placeholder="Custom % (overrides above)">
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Split Between (People)</h4>
                <input type="number" class="tc-rsz-num tc-tip-input" id="tc-tip-people" value="1" min="1" max="100">
            </div>

        </div>

        <div class="tc-tip-results" id="tc-tip-results">
            <div class="tc-age-result-grid">
                <div class="tc-age-mini-card tc-tip-card-tip">
                    <span class="tc-age-mini-val" id="tc-tip-amount">-</span>
                    <span class="tc-age-mini-label">Tip Amount</span>
                </div>
                <div class="tc-age-mini-card tc-tip-card-total">
                    <span class="tc-age-mini-val" id="tc-tip-total">-</span>
                    <span class="tc-age-mini-label">Total Bill</span>
                </div>
                <div class="tc-age-mini-card tc-tip-card-person">
                    <span class="tc-age-mini-val" id="tc-tip-per-person">-</span>
                    <span class="tc-age-mini-label">Per Person</span>
                </div>
            </div>
        </div>
        <?php
    }
}
