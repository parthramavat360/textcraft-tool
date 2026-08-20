<?php
/**
 * Widget: Random Number Generator
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Random_Number extends TextCraft_Tool_Base {

    public function get_name(): string { return 'random_number'; }
    public function get_title(): string { return 'Random Number Generator'; }
    public function get_icon(): string { return 'eicon-counter'; }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Generate random numbers with integers, decimals, or multiples. Ideal for giveaways, statistical sampling, or testing.
        </div>

        <div class="tc-grid-3col">
            <div class="tc-input-group">
                <label class="tc-label">Minimum Value</label>
                <input type="number" class="tc-input" id="tc-rn-min" value="1" step="any">
            </div>
            <div class="tc-input-group">
                <label class="tc-label">Maximum Value</label>
                <input type="number" class="tc-input" id="tc-rn-max" value="100" step="any">
            </div>
            <div class="tc-input-group">
                <label class="tc-label">How Many</label>
                <input type="number" class="tc-input" id="tc-rn-count" value="10" min="1" max="1000">
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Number Type</label>
            <div class="tc-modes" data-group="rn-type">
                <button class="tc-btn tc-btn--ghost sel" data-val="integer" type="button">Integers</button>
                <button class="tc-btn tc-btn--ghost" data-val="decimal" type="button">Decimals</button>
                <button class="tc-btn tc-btn--ghost" data-val="even" type="button">Even Only</button>
                <button class="tc-btn tc-btn--ghost" data-val="odd" type="button">Odd Only</button>
            </div>
        </div>

        <div class="tc-grid-2col">
            <div class="tc-input-group" id="tc-rn-decimal-row" style="display:none">
                <label class="tc-label">Decimal Places</label>
                <input type="number" class="tc-input" id="tc-rn-decimal-places" value="2" min="1" max="10">
            </div>
            <div class="tc-input-group">
                <label class="tc-label">Separator</label>
                <div class="tc-modes" data-group="rn-sep">
                    <button class="tc-btn tc-btn--ghost sel" data-val="newline" type="button">New Line</button>
                    <button class="tc-btn tc-btn--ghost" data-val="comma" type="button">Comma</button>
                    <button class="tc-btn tc-btn--ghost" data-val="space" type="button">Space</button>
                    <button class="tc-btn tc-btn--ghost" data-val="json" type="button">JSON</button>
                </div>
            </div>
        </div>

        <div class="tc-checkboxes">
            <label class="tc-check"><input type="checkbox" id="tc-rn-nodup"> No duplicates</label>
            <label class="tc-check"><input type="checkbox" id="tc-rn-sort"> Sort ascending</label>
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Quick Presets</label>
            <div class="tc-modes">
                <button class="tc-btn tc-btn--ghost" data-preset="dice" type="button">Dice (1-6)</button>
                <button class="tc-btn tc-btn--ghost" data-preset="coin" type="button">Coin (0-1)</button>
                <button class="tc-btn tc-btn--ghost" data-preset="percent" type="button">% (0-100)</button>
                <button class="tc-btn tc-btn--ghost" data-preset="lottery" type="button">Lottery (1-49)</button>
                <button class="tc-btn tc-btn--ghost" data-preset="pin" type="button">PIN (1000-9999)</button>
            </div>
        </div>

        <?php $this->render_actions('tc-rn-generate', 'Generate Numbers', 'tc-rn-copy', 'Copy All'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Generated</span><span class="tc-stat-value" id="tc-rn-stat-count">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Min Result</span><span class="tc-stat-value" id="tc-rn-stat-min">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Max Result</span><span class="tc-stat-value" id="tc-rn-stat-max">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Average</span><span class="tc-stat-value" id="tc-rn-stat-avg">-</span></div>
        </div>

        <div class="tc-label" style="margin-top:16px">Generated Numbers</div>
        <textarea class="tc-textarea" id="tc-rn-output" rows="10" readonly placeholder="Your random numbers will appear here..."></textarea>
        <?php
    }
}