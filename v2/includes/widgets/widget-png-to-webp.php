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

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Quality <span class="tc-rsz-quality-badge" id="tc-p2w-quality-badge">92</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">1</span>
                    <input type="range" class="tc-rsz-slider" id="tc-p2w-quality" min="1" max="100" value="92">
                    <span class="tc-rsz-slider-max">100</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Presets</h4>
                <div class="tc-modes" data-group="p2w-quality">
                    <button class="tc-btn tc-btn--ghost" data-val="60" type="button">Small (60%)</button>
                    <button class="tc-btn tc-btn--ghost" data-val="75" type="button">Good (75%)</button>
                    <button class="tc-btn tc-btn--ghost sel" data-val="92" type="button">Best (92%)</button>
                    <button class="tc-btn tc-btn--ghost" data-val="95" type="button">HQ (95%)</button>
                    <button class="tc-btn tc-btn--ghost" data-val="100" type="button">Lossless</button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Options</h4>
                <div class="tc-rsz-slider-wrap">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-p2w-ios" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Auto-downscale large images on iOS (4096px max)</b></span>
                    </label>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-p2w-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-p2w-convert', 'Convert to WebP', 'tc-p2w-download', 'Download WebP'); ?>

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
