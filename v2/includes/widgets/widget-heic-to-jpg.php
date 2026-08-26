<?php
/**
 * Widget: HEIC to JPG Converter
 * Premium design with quality slider, presets, iOS downscale, preview tabs.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Heic_To_Jpg extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'heic_to_jpg'; }
    public function get_title(): string { return 'HEIC to JPG Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['heic to jpg', 'convert heic', 'heic converter', 'heic to jpeg', 'apple image converter'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert Apple HEIC images to JPG format for universal compatibility. Adjustable quality with instant preview.
        </div>

        <?php $this->render_drop_zone('tc-h2j-drop', 'image/heic,.heic,.HEIC', 'Drag & drop HEIC images here or click to browse'); ?>
        <?php $this->render_file_row('tc-h2j-file'); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Quality <span class="tc-rsz-quality-badge" id="tc-h2j-quality-val">92</span>%</h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">1</span>
                    <input type="range" class="tc-rsz-slider" id="tc-h2j-quality" min="1" max="100" value="92">
                    <span class="tc-rsz-slider-max">100</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Presets</h4>
                <div class="tc-modes" data-group="h2j-quality">
                    <button class="tc-btn tc-btn--ghost" data-val="60" type="button">Small (60%)</button>
                    <button class="tc-btn tc-btn--ghost" data-val="80" type="button">Good (80%)</button>
                    <button class="tc-btn tc-btn--ghost sel" data-val="92" type="button">Best (92%)</button>
                    <button class="tc-btn tc-btn--ghost" data-val="98" type="button">Max (98%)</button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Options</h4>
                <div class="tc-rsz-slider-wrap">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-h2j-ios" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Auto-downscale large images on iOS (4096px max)</b></span>
                    </label>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-h2j-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-h2j-convert', 'Convert to JPG', 'tc-h2j-download', 'Download JPG'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (HEIC)</span><span class="tc-stat-value" id="tc-h2j-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (JPG)</span><span class="tc-stat-value" id="tc-h2j-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-h2j-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    /**
     * Override result panel with HEIC→JPG specific labels.
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
                        <div><span>Original (HEIC)</span><b id="tc-stat-orig">&mdash;</b></div>
                        <div><span>Converted (JPG)</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Saved</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original HEIC</button>
                            <button data-tab="result">Converted JPG</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">Original HEIC will appear here</div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">Converted JPG will appear here</div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
