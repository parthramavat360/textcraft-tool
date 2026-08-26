<?php
/**
 * Widget: Gradient Generator
 * CSS gradient builder with multiple stops, angles, and live preview.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Gradient_Generator extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'gradient_generator'; }
    public function get_title(): string { return 'Gradient Generator'; }
    public function get_icon(): string { return 'eicon-gradient'; }

    public function get_keywords(): array {
        return ['gradient generator', 'css gradient', 'linear gradient', 'radial gradient', 'gradient maker', 'background gradient'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Create beautiful CSS gradients with multiple color stops. Customize type, angle, and colors. Copy the CSS code instantly.
        </div>

        <div class="tc-gr-preview" id="tc-gr-preview"></div>

        <div class="tc-rsz-options" style="margin-top:16px">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Type</h4>
                <div class="tc-rsz-mode-cards tc-gr-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="linear">
                        <span class="tc-rsz-mode-text"><b>Linear</b><span>Straight line</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="radial">
                        <span class="tc-rsz-mode-text"><b>Radial</b><span>Circle outward</span></span>
                    </button>
                </div>
            </div>
            <div class="tc-rsz-section" id="tc-gr-angle-section">
                <h4 class="tc-rsz-heading">Angle <span class="tc-rsz-quality-badge" id="tc-gr-angle-val">90°</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">0°</span>
                    <input type="range" class="tc-rsz-slider" id="tc-gr-angle" min="0" max="360" value="90">
                    <span class="tc-rsz-slider-max">360°</span>
                </div>
            </div>
        </div>

        <div class="tc-gr-stops" id="tc-gr-stops">
            <h4 class="tc-rsz-heading">Color Stops</h4>
        </div>
        <button class="tc-btn tc-btn--ghost tc-gr-add-stop" id="tc-gr-add-stop" type="button">+ Add Color</button>

        <div class="tc-gr-actions">
            <button class="tc-btn tc-btn--accent" id="tc-gr-copy" type="button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                Copy CSS
            </button>
            <button class="tc-btn tc-btn--ghost" id="tc-gr-random" type="button">Random</button>
        </div>

        <div class="tc-gr-css-output" id="tc-gr-css-output">
            <pre id="tc-gr-css-code"></pre>
        </div>

        <?php
    }
}
