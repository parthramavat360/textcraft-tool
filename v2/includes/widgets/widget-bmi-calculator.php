<?php
/**
 * Widget: BMI Calculator
 * Calculate Body Mass Index from height and weight.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Bmi_Calculator extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'bmi_calculator'; }
    public function get_title(): string { return 'BMI Calculator'; }
    public function get_icon(): string { return 'eicon-heart'; }

    public function get_keywords(): array {
        return ['bmi calculator', 'body mass index', 'bmi chart', 'calculate bmi', 'bmi score', 'healthy weight'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Calculate your Body Mass Index (BMI) instantly. Enter your height and weight to see your BMI category and healthy weight range.
        </div>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Unit System</h4>
                <div class="tc-rsz-mode-cards tc-bmi-unit-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="metric">
                        <span class="tc-rsz-mode-text"><b>Metric</b><span>kg / cm</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="imperial">
                        <span class="tc-rsz-mode-text"><b>Imperial</b><span>lbs / ft+in</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section" id="tc-bmi-metric-fields">
                <h4 class="tc-rsz-heading">Your Details</h4>
                <div class="tc-bmi-fields">
                    <div class="tc-bmi-field">
                        <label class="tc-rsz-dim-label">Weight (kg)</label>
                        <input type="number" class="tc-rsz-num tc-bmi-input" id="tc-bmi-weight-kg" value="70" min="10" max="500" step="0.1">
                    </div>
                    <div class="tc-bmi-field">
                        <label class="tc-rsz-dim-label">Height (cm)</label>
                        <input type="number" class="tc-rsz-num tc-bmi-input" id="tc-bmi-height-cm" value="170" min="50" max="300" step="0.1">
                    </div>
                </div>
            </div>

            <div class="tc-rsz-section" id="tc-bmi-imperial-fields" style="display:none">
                <h4 class="tc-rsz-heading">Your Details</h4>
                <div class="tc-bmi-fields">
                    <div class="tc-bmi-field">
                        <label class="tc-rsz-dim-label">Weight (lbs)</label>
                        <input type="number" class="tc-rsz-num tc-bmi-input" id="tc-bmi-weight-lbs" value="154" min="20" max="1000" step="0.1">
                    </div>
                    <div class="tc-bmi-field-group">
                        <div class="tc-bmi-field">
                            <label class="tc-rsz-dim-label">Feet</label>
                            <input type="number" class="tc-rsz-num tc-bmi-input" id="tc-bmi-ft" value="5" min="1" max="9">
                        </div>
                        <div class="tc-bmi-field">
                            <label class="tc-rsz-dim-label">Inches</label>
                            <input type="number" class="tc-rsz-num tc-bmi-input" id="tc-bmi-in" value="7" min="0" max="11">
                        </div>
                    </div>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Age & Gender (optional)</h4>
                <div class="tc-bmi-fields">
                    <div class="tc-bmi-field">
                        <label class="tc-rsz-dim-label">Age</label>
                        <input type="number" class="tc-rsz-num tc-bmi-input" id="tc-bmi-age" value="30" min="2" max="120">
                    </div>
                    <div class="tc-bmi-field">
                        <label class="tc-rsz-dim-label">Gender</label>
                        <select class="tc-rsz-select" id="tc-bmi-gender">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                </div>
            </div>

        </div>

        <button class="tc-age-calc-btn" id="tc-bmi-calculate" type="button">Calculate BMI</button>

        <div class="tc-bmi-results" id="tc-bmi-results" style="display:none">
            <div class="tc-bmi-score-card" id="tc-bmi-score-card">
                <div class="tc-bmi-score-big" id="tc-bmi-score">-</div>
                <div class="tc-bmi-score-label" id="tc-bmi-category">-</div>
            </div>
            <div class="tc-bmi-gauge-wrap">
                <div class="tc-bmi-gauge">
                    <div class="tc-bmi-gauge-bar"></div>
                    <div class="tc-bmi-gauge-pointer" id="tc-bmi-pointer"></div>
                </div>
                <div class="tc-bmi-gauge-labels">
                    <span>Underweight</span><span>Normal</span><span>Overweight</span><span>Obese</span>
                </div>
            </div>
            <div class="tc-age-result-grid" style="margin-top:16px">
                <div class="tc-age-mini-card">
                    <span class="tc-age-mini-val" id="tc-bmi-healthy-low">-</span>
                    <span class="tc-age-mini-label">Healthy Low (kg)</span>
                </div>
                <div class="tc-age-mini-card">
                    <span class="tc-age-mini-val" id="tc-bmi-healthy-high">-</span>
                    <span class="tc-age-mini-label">Healthy High (kg)</span>
                </div>
                <div class="tc-age-mini-card">
                    <span class="tc-age-mini-val" id="tc-bmi-category-icon">-</span>
                    <span class="tc-age-mini-label">Status</span>
                </div>
            </div>
        </div>
        <?php
    }
}
