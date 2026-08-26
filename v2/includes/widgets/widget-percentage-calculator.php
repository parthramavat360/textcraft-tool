<?php
/**
 * Widget: Percentage Calculator
 * Calculate percentages, increases, decreases, and differences.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Percentage_Calculator extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'percentage_calculator'; }
    public function get_title(): string { return 'Percentage Calculator'; }
    public function get_icon(): string { return 'eicon-percentage'; }

    public function get_keywords(): array {
        return ['percentage calculator', 'percent calculator', 'percentage increase', 'percentage decrease', 'what percent', 'calculate percentage'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Calculate percentages, percentage increases, decreases, and differences between two numbers. Quick and easy online calculator.
        </div>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">What is X% of Y?</h4>
                <div class="tc-pct-calc-row">
                    <input type="number" class="tc-rsz-num" id="tc-pct-a" placeholder="X" style="width:80px">
                    <span class="tc-pct-label">% of</span>
                    <input type="number" class="tc-rsz-num" id="tc-pct-b" placeholder="Y" style="width:100px">
                    <span class="tc-pct-label">=</span>
                    <span class="tc-pct-result" id="tc-pct-result-1">?</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">X is what % of Y?</h4>
                <div class="tc-pct-calc-row">
                    <input type="number" class="tc-rsz-num" id="tc-pct-c" placeholder="X" style="width:100px">
                    <span class="tc-pct-label">is</span>
                    <span class="tc-pct-result" id="tc-pct-result-2">?</span>
                    <span class="tc-pct-label">% of</span>
                    <input type="number" class="tc-rsz-num" id="tc-pct-d" placeholder="Y" style="width:100px">
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Percentage Change</h4>
                <div class="tc-pct-calc-row">
                    <input type="number" class="tc-rsz-num" id="tc-pct-e" placeholder="From" style="width:100px">
                    <span class="tc-pct-label">\u2192</span>
                    <input type="number" class="tc-rsz-num" id="tc-pct-f" placeholder="To" style="width:100px">
                    <span class="tc-pct-label">=</span>
                    <span class="tc-pct-result" id="tc-pct-result-3">?</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Percentage Difference</h4>
                <div class="tc-pct-calc-row">
                    <input type="number" class="tc-rsz-num" id="tc-pct-g" placeholder="Value 1" style="width:100px">
                    <span class="tc-pct-label">vs</span>
                    <input type="number" class="tc-rsz-num" id="tc-pct-h" placeholder="Value 2" style="width:100px">
                    <span class="tc-pct-label">=</span>
                    <span class="tc-pct-result" id="tc-pct-result-4">?</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Add / Subtract %</h4>
                <div class="tc-pct-calc-row">
                    <input type="number" class="tc-rsz-num" id="tc-pct-i" placeholder="Value" style="width:100px">
                    <span class="tc-pct-label">+</span>
                    <input type="number" class="tc-rsz-num" id="tc-pct-j" placeholder="%" style="width:60px">
                    <span class="tc-pct-label">%=</span>
                    <span class="tc-pct-result" id="tc-pct-result-5">?</span>
                </div>
                <div class="tc-pct-calc-row" style="margin-top:8px">
                    <input type="number" class="tc-rsz-num" id="tc-pct-k" placeholder="Value" style="width:100px">
                    <span class="tc-pct-label">\u2212</span>
                    <input type="number" class="tc-rsz-num" id="tc-pct-l" placeholder="%" style="width:60px">
                    <span class="tc-pct-label">%=</span>
                    <span class="tc-pct-result" id="tc-pct-result-6">?</span>
                </div>
            </div>

        </div>
        <?php
    }
}
