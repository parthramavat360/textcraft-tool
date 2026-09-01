<?php
/**
 * Widget: PNG to JPG Converter
 * Premium design with quality slider, presets, background color, iOS downscale, preview tabs.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Png_To_Jpg extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    protected bool $premium = true;

    public function get_name(): string { return 'png_to_jpg'; }
    public function get_title(): string { return 'PNG to JPG Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['png to jpg', 'convert png to jpg', 'png to jpeg', 'image converter', 'png to jfif'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert PNG images to JPG format with adjustable quality and custom background color for transparent areas. JPG offers smaller file sizes for photographs.
        </div>

        <?php $this->render_drop_zone('tc-pn2j-drop', 'image/png,.png', 'Drag & drop PNG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-pn2j-file'); ?>

        <div class="tc-input-group" >
            <label class="tc-range-label"  for="tc-pn2j-quality">
                Quality: <span id="tc-pn2j-quality-badge">92</span>
            </label>
            <div class="tc-rsz-slider-wrap">
                <span class="tc-rsz-slider-min" >1</span>
                <input type="range" class="tc-range" id="tc-pn2j-quality" min="1" max="100" value="92" >
                <span class="tc-rsz-slider-max" >100</span>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-range-label" >Presets</label>
            <div class="tc-modes" data-group="pn2j-quality" >
                <button class="tc-btn tc-btn--ghost" data-val="70" type="button" >Small (70%)</button>
                <button class="tc-btn tc-btn--ghost" data-val="82" type="button" >Good (82%)</button>
                <button class="tc-btn tc-btn--ghost sel" data-val="92" type="button" >Best (92%)</button>
                <button class="tc-btn tc-btn--ghost" data-val="98" type="button" >Max (98%)</button>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-range-label"  for="tc-pn2j-bgcolor">
                Background Color: <span id="tc-pn2j-bgcolor-hex">#ffffff</span>
            </label>
            <div class="tc-premium-color-picker" data-picker="tc-pn2j-bgcolor">
                <label class="tc-pcp-swatch" for="tc-pn2j-bgcolor"><span class="tc-pcp-swatch-fill" data-swatch="tc-pn2j-bgcolor"></span></label>
                <span class="tc-pcp-hex"></span>
                <input type="color" class="tc-pcp-input" id="tc-pn2j-bgcolor" value="#ffffff">
                <div class="tc-pcp-swatches" data-palette="tc-pn2j-bgcolor">
                    <button class="tc-pcp-csw" data-val="#ffffff" type="button"></button>
                    <button class="tc-pcp-csw" data-val="#f1f5f9" type="button"></button>
                    <button class="tc-pcp-csw" data-val="#0b1220" type="button"></button>
                    <button class="tc-pcp-csw" data-val="#ff0000" type="button"></button>
                    <button class="tc-pcp-csw" data-val="#00bfff" type="button"></button>
                    <button class="tc-pcp-csw" data-val="#00ff00" type="button"></button>
                </div>
            </div>
            <p class="tc-lvl-hint">Fill color for transparent areas (JPG has no transparency).</p>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-pn2j-ios" checked>
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text" >
                    <b>iOS-Compatible Downscale</b>
                    <small>Auto-downscale large images (4096px max) for iOS compatibility.</small>
                </span>
            </label>
        </div>

        <div class="tc-input-group">
            <label class="tc-label"  for="tc-pn2j-name">Output file name</label>
            <input type="text" class="tc-input"  id="tc-pn2j-name" placeholder="my-image">
            <p class="tc-lvl-hint">Leave empty to use your source file name.</p>
        </div>

        <?php $this->render_progress_bar('tc-pn2j-progress', 'Converting...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-pn2j-convert" type="button">Convert to JPG</button>
            <button class="tc-btn tc-btn--ghost" id="tc-pn2j-download" type="button" style="display:none">Download JPG</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-pn2j-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (PNG)</span><span class="tc-stat-value" id="tc-pn2j-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (JPG)</span><span class="tc-stat-value" id="tc-pn2j-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-pn2j-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    /**
     * Override result panel with PNGâ†’JPG specific labels.
     */
    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Converted JPG</h3>
                    <span id="tc-status-chip">Idle</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-stats">
                        <div><span>Original (PNG)</span><b id="tc-stat-orig">&mdash;</b></div>
                        <div><span>Converted (JPG)</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Saved</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original PNG</button>
                            <button data-tab="result">Converted JPG</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">Original PNG will appear here</div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">Converted JPG will appear here</div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
