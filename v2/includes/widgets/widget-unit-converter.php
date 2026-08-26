<?php
/**
 * Widget: Unit Converter
 * Length, weight, temperature, volume, speed, data storage conversions.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Unit_Converter extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'unit_converter'; }
    public function get_title(): string { return 'Unit Converter'; }
    public function get_icon(): string { return 'eicon-ruler'; }

    public function get_keywords(): array {
        return ['unit converter', 'length converter', 'temperature converter', 'weight converter', 'km to miles', 'kg to lbs', 'celsius fahrenheit'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert between length, weight, temperature, volume, speed, and data storage units instantly.
        </div>

        <div class="tc-rsz-options">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Category</h4>
                <div class="tc-rsz-mode-cards tc-uc-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="length"><b>Length</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="weight"><b>Weight</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="temp"><b>Temperature</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="volume"><b>Volume</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="speed"><b>Speed</b></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="data"><b>Data</b></button>
                </div>
            </div>
        </div>

        <div class="tc-uc-grid">
            <div class="tc-uc-col">
                <div class="tc-input-group">
                    <label class="tc-label"><b>From</b></label>
                    <select class="tc-select" id="tc-uc-from"></select>
                </div>
                <div class="tc-input-group">
                    <label class="tc-label">Value</label>
                    <input type="number" class="tc-input tc-uc-val" id="tc-uc-from-val" value="1" placeholder="Value">
                </div>
            </div>
            <button class="tc-btn tc-btn--ghost tc-uc-swap" id="tc-uc-swap" type="button">⇄</button>
            <div class="tc-uc-col">
                <div class="tc-input-group">
                    <label class="tc-label"><b>To</b></label>
                    <select class="tc-select" id="tc-uc-to"></select>
                </div>
                <div class="tc-input-group">
                    <label class="tc-label">Result</label>
                    <input type="text" class="tc-input tc-uc-val" id="tc-uc-to-val" readonly placeholder="Result">
                </div>
            </div>
        </div>

        <div class="tc-uc-formula" id="tc-uc-formula"></div>

        <?php
    }
}
