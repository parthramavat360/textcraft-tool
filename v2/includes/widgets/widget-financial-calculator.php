<?php
/**
 * Widget: Financial Calculator
 * Loan/EMI, Compound Interest, GST/VAT, Discount Calculator.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Financial_Calculator extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'financial_calculator'; }
    public function get_title(): string { return 'Financial Calculator'; }
    public function get_icon(): string { return 'eicon-calculator'; }

    public function get_keywords(): array {
        return ['loan calculator', 'emi calculator', 'compound interest', 'gst calculator', 'vat calculator', 'discount calculator', 'financial calculator'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Calculate loan payments, compound interest, GST/VAT, and discounts. All calculations happen instantly in your browser.
        </div>

        <div class="tc-rsz-options">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Calculator</h4>
                <div class="tc-rsz-mode-cards tc-fc-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="emi"><b>Loan / EMI</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="compound"><b>Compound Interest</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="gst"><b>GST / VAT</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="discount"><b>Discount</b></button>
                </div>
            </div>
        </div>

        <!-- EMI Fields -->
        <div class="tc-fc-fields" id="tc-fc-emi-fields">
            <div class="tc-input-group"><label class="tc-label">Loan Amount ($)</label><input type="number" class="tc-input" id="tc-fc-amount" value="100000" min="1"></div>
            <div class="tc-input-group"><label class="tc-label">Annual Interest Rate (%)</label><input type="number" class="tc-input" id="tc-fc-rate" value="8.5" min="0" step="0.1"></div>
            <div class="tc-input-group"><label class="tc-label">Loan Term (years)</label><input type="number" class="tc-input" id="tc-fc-term" value="5" min="1"></div>
        </div>

        <!-- Compound Interest Fields -->
        <div class="tc-fc-fields" id="tc-fc-compound-fields" style="display:none">
            <div class="tc-input-group"><label class="tc-label">Principal ($)</label><input type="number" class="tc-input" id="tc-fc-principal" value="10000" min="1"></div>
            <div class="tc-input-group"><label class="tc-label">Annual Rate (%)</label><input type="number" class="tc-input" id="tc-fc-crate" value="7" min="0" step="0.1"></div>
            <div class="tc-input-group"><label class="tc-label">Years</label><input type="number" class="tc-input" id="tc-fc-years" value="10" min="1"></div>
            <div class="tc-input-group"><label class="tc-label">Compounding</label>
                <select class="tc-select" id="tc-fc-compound">
                    <option value="1">Annually</option>
                    <option value="2">Semi-annually</option>
                    <option value="4">Quarterly</option>
                    <option value="12" selected>Monthly</option>
                    <option value="365">Daily</option>
                </select>
            </div>
        </div>

        <!-- GST Fields -->
        <div class="tc-fc-fields" id="tc-fc-gst-fields" style="display:none">
            <div class="tc-input-group"><label class="tc-label">Amount ($)</label><input type="number" class="tc-input" id="tc-fc-gamount" value="100" min="0"></div>
            <div class="tc-input-group"><label class="tc-label">GST/VAT Rate (%)</label><input type="number" class="tc-input" id="tc-fc-grate" value="18" min="0" step="0.1"></div>
            <div class="tc-input-group"><label class="tc-label">Mode</label>
                <select class="tc-select" id="tc-fc-gmode">
                    <option value="add">Add GST/VAT</option>
                    <option value="remove">Remove GST/VAT</option>
                </select>
            </div>
        </div>

        <!-- Discount Fields -->
        <div class="tc-fc-fields" id="tc-fc-discount-fields" style="display:none">
            <div class="tc-input-group"><label class="tc-label">Original Price ($)</label><input type="number" class="tc-input" id="tc-fc-dprice" value="100" min="0"></div>
            <div class="tc-input-group"><label class="tc-label">Discount (%)</label><input type="number" class="tc-input" id="tc-fc-dpercent" value="20" min="0" max="100" step="0.1"></div>
        </div>

        <div class="tc-fc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-fc-calc" type="button">Calculate</button>
            <button class="tc-btn tc-btn--ghost" id="tc-fc-copy" type="button">Copy Result</button>
        </div>

        <div class="tc-fc-results" id="tc-fc-results" style="display:none">
            <div class="tc-fc-result-grid" id="tc-fc-result-grid"></div>
        </div>

        <?php
    }
}
