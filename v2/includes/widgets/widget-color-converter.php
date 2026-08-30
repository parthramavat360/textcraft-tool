<?php
/**
 * Widget: Color Converter
 * Convert between HEX, RGB, HSL, HSV color formats.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Color_Converter extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'color_converter'; }
    public function get_title(): string { return 'Color Converter'; }
    public function get_icon(): string { return 'eicon-brush'; }

    public function get_keywords(): array {
        return ['color converter', 'hex to rgb', 'rgb to hsl', 'color format', 'hex color', 'hsl converter', 'color code'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert color codes between HEX, RGB, HSL, and HSV formats instantly. Paste any color code and get all conversions at once.
        </div>

        <div class="tc-cc-input-row">
            <div class="tc-cc-preview" id="tc-cc-preview"></div>
            <div class="tc-input-group">
                <label class="tc-label">Enter color</label>
                <input type="text" class="tc-input tc-cc-input" id="tc-cc-input" placeholder="#0b1220, rgb(37,99,235), hsl(217,83%,53%)" value="#0b1220">
            </div>
            <input type="color" class="tc-cc-picker" id="tc-cc-picker" value="#0b1220">
        </div>

        <div class="tc-cc-formats" id="tc-cc-formats">

            <div class="tc-cc-format-card">
                <h4 class="tc-cc-format-title">HEX</h4>
                <div class="tc-cc-format-row">
                    <div class="tc-input-group">
                        <label class="tc-label">HEX</label>
                        <input type="text" class="tc-input tc-cc-field" id="tc-cc-hex" readonly>
                    </div>
                    <button class="tc-cc-copy-btn" data-target="tc-cc-hex" type="button">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    </button>
                </div>
            </div>

            <div class="tc-cc-format-card">
                <h4 class="tc-cc-format-title">RGB</h4>
                <div class="tc-cc-format-row">
                    <div class="tc-input-group">
                        <label class="tc-label">RGB</label>
                        <input type="text" class="tc-input tc-cc-field" id="tc-cc-rgb" readonly>
                    </div>
                    <button class="tc-cc-copy-btn" data-target="tc-cc-rgb" type="button">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    </button>
                </div>
                <div class="tc-cc-sliders">
                    <label class="tc-cc-slider-row"><span class="tc-cc-slider-label">R</span><input type="range" class="tc-cc-range" id="tc-cc-r" min="0" max="255" value="37"><span class="tc-cc-slider-val" id="tc-cc-r-val">37</span></label>
                    <label class="tc-cc-slider-row"><span class="tc-cc-slider-label">G</span><input type="range" class="tc-cc-range" id="tc-cc-g" min="0" max="255" value="99"><span class="tc-cc-slider-val" id="tc-cc-g-val">99</span></label>
                    <label class="tc-cc-slider-row"><span class="tc-cc-slider-label">B</span><input type="range" class="tc-cc-range" id="tc-cc-b" min="0" max="255" value="235"><span class="tc-cc-slider-val" id="tc-cc-b-val">235</span></label>
                </div>
            </div>

            <div class="tc-cc-format-card">
                <h4 class="tc-cc-format-title">HSL</h4>
                <div class="tc-cc-format-row">
                    <div class="tc-input-group">
                        <label class="tc-label">HSL</label>
                        <input type="text" class="tc-input tc-cc-field" id="tc-cc-hsl" readonly>
                    </div>
                    <button class="tc-cc-copy-btn" data-target="tc-cc-hsl" type="button">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    </button>
                </div>
            </div>

            <div class="tc-cc-format-card">
                <h4 class="tc-cc-format-title">HSV</h4>
                <div class="tc-cc-format-row">
                    <div class="tc-input-group">
                        <label class="tc-label">HSV</label>
                        <input type="text" class="tc-input tc-cc-field" id="tc-cc-hsv" readonly>
                    </div>
                    <button class="tc-cc-copy-btn" data-target="tc-cc-hsv" type="button">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    </button>
                </div>
            </div>

            <div class="tc-cc-format-card">
                <h4 class="tc-cc-format-title">CSS</h4>
                <div class="tc-cc-format-row">
                    <div class="tc-input-group">
                        <label class="tc-label">CSS</label>
                        <input type="text" class="tc-input tc-cc-field" id="tc-cc-css" readonly>
                    </div>
                    <button class="tc-cc-copy-btn" data-target="tc-cc-css" type="button">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    </button>
                </div>
            </div>

            <div class="tc-cc-format-card">
                <h4 class="tc-cc-format-title">RGBA</h4>
                <div class="tc-cc-format-row">
                    <div class="tc-input-group">
                        <label class="tc-label">RGBA</label>
                        <input type="text" class="tc-input tc-cc-field" id="tc-cc-rgba" readonly>
                    </div>
                    <button class="tc-cc-copy-btn" data-target="tc-cc-rgba" type="button">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    </button>
                </div>
            </div>

        </div>

        <?php
    }
}
