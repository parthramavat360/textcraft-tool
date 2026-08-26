<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Color_Palette_Generator extends TextCraft_Tool_Base {
    public function get_name(): string { return 'color_palette_generator'; }
    public function get_title(): string { return 'Color Palette Generator'; }
    public function get_icon(): string { return 'eicon-palette'; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Generate beautiful color palettes from a base color with harmony rules, WCAG contrast checks, and CSS export.</div>
        <div class="tc-input-group">
            <label class="tc-label">Color Harmony</label>
            <?php $this->render_mode_buttons('pal-harmony', [
                'analogous' => 'Analogous',
                'complementary' => 'Complementary',
                'triadic' => 'Triadic',
                'split' => 'Split-Comp',
                'monochromatic' => 'Monochromatic',
            ], 'analogous'); ?>
        </div>
        <div class="tc-grid-2col">
            <div class="tc-input-group">
                <label class="tc-label">Base Color</label>
                <div style="display:flex;gap:8px;align-items:center">
                    <input type="color" id="pal-base" value="#2563eb" style="width:48px;height:40px;border:none;border-radius:8px;cursor:pointer;background:transparent">
                    <input type="text" class="tc-input" id="pal-hex" value="#2563eb" style="flex:1">
                </div>
            </div>
            <div class="tc-input-group">
                <label class="tc-label">Number of Colors</label>
                <select class="tc-select" id="pal-count">
                    <option value="3">3 Colors</option>
                    <option value="5" selected>5 Colors</option>
                    <option value="7">7 Colors</option>
                </select>
            </div>
        </div>
        <div class="tc-checkboxes">
            <label class="tc-check"><input type="checkbox" id="pal-variants" checked> Show Light/Dark Variants</label>
            <label class="tc-check"><input type="checkbox" id="pal-contrast"> WCAG Contrast Check</label>
        </div>
        <?php $this->render_actions('pal-generate', 'Generate Palette'); ?>
        <div class="tctp-result" id="pal-result" style="display:none">
            <div class="tctp-rsz-tabs">
                <button class="tctp-rsz-tab sel" data-tab="palette">Color Palette</button>
                <button class="tctp-rsz-tab" data-tab="css">CSS Variables</button>
            </div>
            <div class="tctp-rsz-tab-panel" id="pal-output"></div>
            <div class="tctp-rsz-tab-panel" id="pal-css" style="display:none"></div>
        </div>
    <?php }
}
