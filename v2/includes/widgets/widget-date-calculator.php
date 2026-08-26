<?php
/**
 * Widget: Date Calculator
 * Calculate days between dates and add/subtract days from a date.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Date_Calculator extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'date_calculator'; }
    public function get_title(): string { return 'Date Calculator'; }
    public function get_icon(): string { return 'eicon-calendar'; }

    public function get_keywords(): array {
        return ['date calculator', 'days between dates', 'date difference', 'add days', 'subtract days', 'date math'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Calculate the exact number of days between two dates, or add/subtract a specific number of days from any starting date.
        </div>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Mode</h4>
                <div class="tc-rsz-mode-cards tc-date-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="diff">
                        <span class="tc-rsz-mode-text"><b>Between Dates</b><span>Count days between</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="add">
                        <span class="tc-rsz-mode-text"><b>Add Days</b><span>Add to start date</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="sub">
                        <span class="tc-rsz-mode-text"><b>Subtract Days</b><span>Subtract from start</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section" id="tc-date-diff-fields">
                <div class="tc-date-fields">
                    <div class="tc-date-field">
                        <label class="tc-rsz-dim-label">Start Date</label>
                        <input type="date" class="tc-rsz-num tc-date-input" id="tc-date-start">
                    </div>
                    <div class="tc-date-field">
                        <label class="tc-rsz-dim-label">End Date</label>
                        <input type="date" class="tc-rsz-num tc-date-input" id="tc-date-end">
                    </div>
                </div>
            </div>

            <div class="tc-rsz-section" id="tc-date-add-fields" style="display:none">
                <div class="tc-date-fields">
                    <div class="tc-date-field">
                        <label class="tc-rsz-dim-label">Start Date</label>
                        <input type="date" class="tc-rsz-num tc-date-input" id="tc-date-base">
                    </div>
                    <div class="tc-date-field">
                        <label class="tc-rsz-dim-label">Days to Add</label>
                        <input type="number" class="tc-rsz-num tc-date-input" id="tc-date-days" value="30" min="1" max="36500">
                    </div>
                </div>
            </div>

            <div class="tc-rsz-section" id="tc-date-sub-fields" style="display:none">
                <div class="tc-date-fields">
                    <div class="tc-date-field">
                        <label class="tc-rsz-dim-label">Start Date</label>
                        <input type="date" class="tc-rsz-num tc-date-input" id="tc-date-base-sub">
                    </div>
                    <div class="tc-date-field">
                        <label class="tc-rsz-dim-label">Days to Subtract</label>
                        <input type="number" class="tc-rsz-num tc-date-input" id="tc-date-days-sub" value="30" min="1" max="36500">
                    </div>
                </div>
            </div>

        </div>

        <button class="tc-age-calc-btn" id="tc-date-calculate" type="button">Calculate</button>

        <div class="tc-bmi-results" id="tc-date-results" style="display:none">
            <div class="tc-age-result-grid" id="tc-date-result-grid">
                <div class="tc-age-mini-card">
                    <span class="tc-age-mini-val" id="tc-date-result-days">-</span>
                    <span class="tc-age-mini-label">Total Days</span>
                </div>
                <div class="tc-age-mini-card">
                    <span class="tc-age-mini-val" id="tc-date-result-weeks">-</span>
                    <span class="tc-age-mini-label">Weeks & Days</span>
                </div>
                <div class="tc-age-mini-card">
                    <span class="tc-age-mini-val" id="tc-date-result-months">-</span>
                    <span class="tc-age-mini-label">Months & Days</span>
                </div>
            </div>
            <div class="tc-age-result-grid" style="margin-top:12px">
                <div class="tc-age-mini-card">
                    <span class="tc-age-mini-val" id="tc-date-result-date">-</span>
                    <span class="tc-age-mini-label" id="tc-date-result-date-label">Result Date</span>
                </div>
                <div class="tc-age-mini-card">
                    <span class="tc-age-mini-val" id="tc-date-result-day">-</span>
                    <span class="tc-age-mini-label">Day of Week</span>
                </div>
            </div>
        </div>
        <?php
    }
}
