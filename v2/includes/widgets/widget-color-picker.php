<?php
/**
 * Widget: Color Picker & Converter
 * Pick colors and convert between HEX, RGB, HSL formats.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Color_Picker extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'color_picker'; }
    public function get_title(): string { return 'Color Picker & Converter'; }
    public function get_icon(): string { return 'eicon-paint-brush'; }

    public function get_keywords(): array {
        return ['color picker', 'color converter', 'hex to rgb', 'rgb to hex', 'hsl converter', 'color code', 'color tool'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Pick any color and instantly convert between HEX, RGB, HSL, and CMYK formats. Copy color codes with one click. All processing happens in your browser.
        </div>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Pick a Color</h4>
                <div class="tc-color-picker-area">
                    <div class="tc-color-preview-big" id="tc-color-preview-big" style="background:#0b1220"></div>
                    <input type="color" id="tc-color-main" value="#0b1220" class="tc-color-main-input">
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Color Values</h4>
                <div class="tc-color-values">
                    <div class="tc-color-val-row">
                        <div class="tc-input-group">
                            <label class="tc-label">HEX</label>
                            <div class="tc-color-val-input">
                                <input type="text" class="tc-input" id="tc-color-hex" value="#0b1220" readonly>
                                <button class="tc-color-copy" data-target="tc-color-hex" type="button">Copy</button>
                            </div>
                        </div>
                    </div>
                    <div class="tc-color-val-row">
                        <div class="tc-input-group">
                            <label class="tc-label">RGB</label>
                            <div class="tc-color-val-input">
                                <input type="text" class="tc-input" id="tc-color-rgb" value="rgb(37, 99, 235)" readonly>
                                <button class="tc-color-copy" data-target="tc-color-rgb" type="button">Copy</button>
                            </div>
                        </div>
                    </div>
                    <div class="tc-color-val-row">
                        <div class="tc-input-group">
                            <label class="tc-label">HSL</label>
                            <div class="tc-color-val-input">
                                <input type="text" class="tc-input" id="tc-color-hsl" value="hsl(221, 83%, 53%)" readonly>
                                <button class="tc-color-copy" data-target="tc-color-hsl" type="button">Copy</button>
                            </div>
                        </div>
                    </div>
                    <div class="tc-color-val-row">
                        <div class="tc-input-group">
                            <label class="tc-label">CMYK</label>
                            <div class="tc-color-val-input">
                                <input type="text" class="tc-input" id="tc-color-cmyk" value="cmyk(84%, 58%, 0%, 8%)" readonly>
                                <button class="tc-color-copy" data-target="tc-color-cmyk" type="button">Copy</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Shades & Tints</h4>
                <div class="tc-color-shades" id="tc-color-shades"></div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Harmony</h4>
                <div class="tc-rsz-mode-cards tc-color-harmony-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="complementary">
                        <span class="tc-rsz-mode-text"><b>Complementary</b><span>Opposite color</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="analogous">
                        <span class="tc-rsz-mode-text"><b>Analogous</b><span>Nearby colors</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="triadic">
                        <span class="tc-rsz-mode-text"><b>Triadic</b><span>Three evenly spaced</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="split">
                        <span class="tc-rsz-mode-text"><b>Split</b><span>Split complementary</span></span>
                    </button>
                </div>
                <div class="tc-color-harmony-row" id="tc-color-harmony-row"></div>
            </div>

        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
                        <div id="tc-color-result">
            <p class="tc-color-result-empty">Pick a color to see harmony colors</p>
        </div>
        <?php
    }

    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Harmony</h3>
                    <span id="tc-status-chip">Ready</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-stats">
                        <div><span>HEX</span><b id="tc-stat-orig">#0b1220</b></div>
                        <div><span>Mode</span><b id="tc-stat-comp">Complementary</b></div>
                        <div class="saved"><span>Colors</span><b id="tc-stat-saved">2</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Current</button>
                            <button data-tab="result">Harmony</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <div class="tc-color-preview-panel" id="tc-color-preview-panel">
                            <div class="tc-color-preview-circle" id="tc-color-preview-circle" style="background:#0b1220"></div>
                        </div>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <?php $this->render_result_content($settings); ?>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
