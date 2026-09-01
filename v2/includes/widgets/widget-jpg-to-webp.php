<?php
/**
 * Widget: JPG to WebP Converter
 * Premium design with quality slider, presets, iOS downscale, preview tabs.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Jpg_To_Webp extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    protected bool $premium = true;

    public function get_name(): string { return 'jpg_to_webp'; }
    public function get_title(): string { return 'JPG to WebP Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['jpg to webp', 'convert jpg to webp', 'jpeg to webp', 'webp converter'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert JPG/JPEG images to WebP format for smaller file sizes and faster web loading. Adjust quality from lossy to lossless.
        </div>

        <?php $this->render_drop_zone('tc-j2w-drop', 'image/jpeg,.jpg,.jpeg', 'Drag & drop JPG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-j2w-file'); ?>

        <div class="tc-input-group" >
            <div class="tc-range-wrap">
                <label class="tc-range-label"  for="tc-j2w-quality">
                    Quality: <span id="tc-j2w-quality-badge">92</span>
                </label>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min" >1</span>
                    <input type="range" class="tc-range" id="tc-j2w-quality" min="1" max="100" value="92" >
                    <span class="tc-rsz-slider-max" >100</span>
                </div>
                <p class="tc-lvl-hint">Higher quality keeps more detail but produces larger files.</p>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" >Presets</label>
            <div class="tc-modes" data-group="j2w-quality">
                <button class="tc-btn tc-btn--ghost" data-val="60" type="button">Small (60%)</button>
                <button class="tc-btn tc-btn--ghost" data-val="75" type="button">Good (75%)</button>
                <button class="tc-btn tc-btn--ghost sel" data-val="82" type="button">Best (82%)</button>
                <button class="tc-btn tc-btn--ghost" data-val="95" type="button">HQ (95%)</button>
                <button class="tc-btn tc-btn--ghost" data-val="100" type="button">Lossless</button>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-j2w-ios" checked>
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text" >
                    <b>iOS-Compatible Downscale</b>
                    <small>Auto-downscale large images (4096px max) for iOS compatibility.</small>
                </span>
            </label>
        </div>

        <div class="tc-input-group">
            <label class="tc-label"  for="tc-j2w-name">Output file name</label>
            <input type="text" class="tc-input"  id="tc-j2w-name" placeholder="my-image">
            <p class="tc-lvl-hint">Leave empty to use your source file name.</p>
        </div>

        <?php $this->render_progress_bar('tc-j2w-progress', 'Converting...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-j2w-convert" type="button">Convert to WebP</button>
            <button class="tc-btn tc-btn--ghost" id="tc-j2w-download" type="button" style="display:none">Download WebP</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-j2w-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (JPG)</span><span class="tc-stat-value" id="tc-j2w-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (WebP)</span><span class="tc-stat-value" id="tc-j2w-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-j2w-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    /**
     * Override result panel with JPGâ†’WebP specific labels.
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
                        <div><span>Original (JPG)</span><b id="tc-stat-orig">&mdash;</b></div>
                        <div><span>Converted (WebP)</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Saved</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original JPG</button>
                            <button data-tab="result">Converted WebP</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">Original JPG will appear here</div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">Converted WebP will appear here</div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
