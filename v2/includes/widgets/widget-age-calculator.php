<?php
/**
 * Widget: Age Calculator
 * Calculate exact age from birth date.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Age_Calculator extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'age_calculator'; }
    public function get_title(): string { return 'Age Calculator'; }
    public function get_icon(): string { return 'eicon-date'; }

    public function get_keywords(): array {
        return ['age calculator', 'calculate age', 'how old am i', 'age from birthday', 'birthday calculator', 'exact age'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Calculate your exact age in years, months, and days from your date of birth. Also shows total days lived, next birthday countdown, and zodiac sign.
        </div>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Date of Birth</h4>
                <div class="tc-age-fields">
                    <div class="tc-age-field">
                        <label class="tc-rsz-dim-label">Day</label>
                        <select class="tc-rsz-select" id="tc-age-day"></select>
                    </div>
                    <div class="tc-age-field">
                        <label class="tc-rsz-dim-label">Month</label>
                        <select class="tc-rsz-select" id="tc-age-month">
                            <option value="0">January</option><option value="1">February</option>
                            <option value="2">March</option><option value="3">April</option>
                            <option value="4">May</option><option value="5">June</option>
                            <option value="6">July</option><option value="7">August</option>
                            <option value="8">September</option><option value="9">October</option>
                            <option value="10">November</option><option value="11">December</option>
                        </select>
                    </div>
                    <div class="tc-age-field">
                        <label class="tc-rsz-dim-label">Year</label>
                        <input type="number" class="tc-rsz-num" id="tc-age-year" value="1995" min="1900" max="2026">
                    </div>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Calculate On (optional)</h4>
                <div class="tc-age-fields">
                    <div class="tc-age-field" style="flex:1">
                        <input type="date" class="tc-age-date-input" id="tc-age-target">
                    </div>
                </div>
                <p class="tc-age-hint">Leave blank to use today's date</p>
            </div>

        </div>

        <button class="tc-age-calc-btn" id="tc-age-calculate" type="button">Calculate Age</button>

        <div class="tc-age-results" id="tc-age-results" style="display:none">
            <div class="tc-age-result-card">
                <div class="tc-age-result-big" id="tc-age-result-age">-</div>
                <div class="tc-age-result-sub" id="tc-age-result-sub">years</div>
            </div>
            <div class="tc-age-result-grid">
                <div class="tc-age-mini-card">
                    <span class="tc-age-mini-val" id="tc-age-months">-</span>
                    <span class="tc-age-mini-label">Months</span>
                </div>
                <div class="tc-age-mini-card">
                    <span class="tc-age-mini-val" id="tc-age-days">-</span>
                    <span class="tc-age-mini-label">Days</span>
                </div>
                <div class="tc-age-mini-card">
                    <span class="tc-age-mini-val" id="tc-age-hours">-</span>
                    <span class="tc-age-mini-label">Hours lived</span>
                </div>
                <div class="tc-age-mini-card">
                    <span class="tc-age-mini-val" id="tc-age-total-days">-</span>
                    <span class="tc-age-mini-label">Total days</span>
                </div>
                <div class="tc-age-mini-card">
                    <span class="tc-age-mini-val" id="tc-age-zodiac">-</span>
                    <span class="tc-age-mini-label">Zodiac</span>
                </div>
                <div class="tc-age-mini-card">
                    <span class="tc-age-mini-val" id="tc-age-birthday">-</span>
                    <span class="tc-age-mini-label">Next birthday</span>
                </div>
            </div>
        </div>
        <?php
    }
}
