<?php
/**
 * Widget: PNG to WebP Converter
 * Premium design with quality slider, presets, iOS downscale, preview tabs.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Png_To_Webp extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    protected bool $premium = true;

    public function get_name(): string { return 'png_to_webp'; }
    public function get_title(): string { return 'PNG to WebP Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['png to webp', 'convert png to webp', 'webp converter', 'optimize png'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert PNG images to WebP format for faster web loading. Excellent compression with transparency support and adjustable quality.
        </div>

        <?php $this->render_drop_zone('tc-p2w-drop', 'image/png,.png', 'Drag & drop PNG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-p2w-file'); ?>

        <div class="tc-input-group" style="margin-top:18px">
            <label class="tc-range-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-p2w-quality">
                Quality: <span id="tc-p2w-quality-badge">92</span>
            </label>
            <div class="tc-range-wrap">
                <span class="tc-range-min">1</span>
                <input type="range" class="tc-range" id="tc-p2w-quality" min="1" max="100" value="92">
                <span class="tc-range-max">100</span>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-range-label" style="font-family:'Space Grotesk',system-ui,sans-serif">Presets</label>
            <div class="tc-modes" data-group="p2w-quality" style="margin-top:8px">
                <button class="tc-btn tc-btn--ghost" data-val="60" type="button" style="font-family:'Space Grotesk',system-ui,sans-serif">Small (60%)</button>
                <button class="tc-btn tc-btn--ghost" data-val="75" type="button" style="font-family:'Space Grotesk',system-ui,sans-serif">Good (75%)</button>
                <button class="tc-btn tc-btn--ghost sel" data-val="92" type="button" style="font-family:'Space Grotesk',system-ui,sans-serif">Best (92%)</button>
                <button class="tc-btn tc-btn--ghost" data-val="95" type="button" style="font-family:'Space Grotesk',system-ui,sans-serif">HQ (95%)</button>
                <button class="tc-btn tc-btn--ghost" data-val="100" type="button" style="font-family:'Space Grotesk',system-ui,sans-serif">Lossless</button>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-p2w-ios" checked>
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text" style="font-family:'Space Grotesk',system-ui,sans-serif">
                    <b>iOS-Compatible Downscale</b>
                    <small>Auto-downscale large images (4096px max) for iOS compatibility.</small>
                </span>
            </label>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-p2w-name">Output file name</label>
            <input type="text" class="tc-input" style="font-family:'Space Grotesk',system-ui,sans-serif" id="tc-p2w-name" placeholder="my-image">
            <p class="tc-lvl-hint">Leave empty to use your source file name.</p>
        </div>

        <?php $this->render_progress_bar('tc-p2w-progress', 'Converting...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-p2w-convert" type="button">Convert to WebP</button>
            <button class="tc-btn tc-btn--ghost" id="tc-p2w-download" type="button" style="display:none">Download WebP</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-p2w-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (PNG)</span><span class="tc-stat-value" id="tc-p2w-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (WebP)</span><span class="tc-stat-value" id="tc-p2w-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-p2w-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    /**
     * Override result panel with PNG→WebP specific labels.
     */
    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Converted WebP</h3>
                    <span id="tc-status-chip">Idle</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-stats">
                        <div><span>Original (PNG)</span><b id="tc-stat-orig">&mdash;</b></div>
                        <div><span>Converted (WebP)</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Saved</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original PNG</button>
                            <button data-tab="result">Converted WebP</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">Original PNG will appear here</div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">Converted WebP will appear here</div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
