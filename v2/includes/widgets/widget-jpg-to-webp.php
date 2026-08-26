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

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Quality <span class="tc-rsz-quality-badge" id="tc-j2w-quality-badge">92</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">1</span>
                    <input type="range" class="tc-rsz-slider" id="tc-j2w-quality" min="1" max="100" value="92">
                    <span class="tc-rsz-slider-max">100</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Presets</h4>
                <div class="tc-modes" data-group="j2w-quality">
                    <button class="tc-btn tc-btn--ghost" data-val="60" type="button">Small (60%)</button>
                    <button class="tc-btn tc-btn--ghost" data-val="75" type="button">Good (75%)</button>
                    <button class="tc-btn tc-btn--ghost sel" data-val="82" type="button">Best (82%)</button>
                    <button class="tc-btn tc-btn--ghost" data-val="95" type="button">HQ (95%)</button>
                    <button class="tc-btn tc-btn--ghost" data-val="100" type="button">Lossless</button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Options</h4>
                <div class="tc-rsz-slider-wrap">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-j2w-ios" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                    </label>
                    <span style="font-size:12px;opacity:0.6">Auto-downscale large images on iOS (4096px max)</span>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-j2w-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-j2w-convert', 'Convert to WebP', 'tc-j2w-download', 'Download WebP'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (JPG)</span><span class="tc-stat-value" id="tc-j2w-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (WebP)</span><span class="tc-stat-value" id="tc-j2w-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-j2w-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    /**
     * Override result panel with JPG→WebP specific labels.
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
