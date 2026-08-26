<?php
/**
 * Widget: PNG to HEIC Converter
 * Premium design with quality slider, iOS downscale, preview tabs.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Png_To_Heic extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'png_to_heic'; }
    public function get_title(): string { return 'PNG to HEIC Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['png to heic', 'convert png to heic', 'heic converter', 'png heic'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert PNG images to HEIC format used by Apple devices. Excellent quality with smaller file sizes. Adjust quality to balance size and clarity.
        </div>

        <?php $this->render_drop_zone('tc-p2h-drop', 'image/png,.png', 'Drag & drop PNG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-p2h-file'); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Quality <span class="tc-rsz-quality-badge" id="tc-p2h-quality-val">85</span>%</h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">1</span>
                    <input type="range" class="tc-rsz-slider" id="tc-p2h-quality" min="1" max="100" value="85">
                    <span class="tc-rsz-slider-max">100</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Presets</h4>
                <div class="tc-modes" data-group="p2h-quality">
                    <button class="tc-btn tc-btn--ghost" data-val="60" type="button">Small (60%)</button>
                    <button class="tc-btn tc-btn--ghost" data-val="75" type="button">Good (75%)</button>
                    <button class="tc-btn tc-btn--ghost sel" data-val="85" type="button">Best (85%)</button>
                    <button class="tc-btn tc-btn--ghost" data-val="95" type="button">HQ (95%)</button>
                    <button class="tc-btn tc-btn--ghost" data-val="100" type="button">Lossless</button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Options</h4>
                <div class="tc-rsz-slider-wrap">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-p2h-ios" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Auto-downscale large images on iOS (4096px max)</b></span>
                    </label>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-p2h-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-p2h-convert', 'Convert to HEIC', 'tc-p2h-download', 'Download HEIC'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (PNG)</span><span class="tc-stat-value" id="tc-p2h-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (HEIC)</span><span class="tc-stat-value" id="tc-p2h-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-p2h-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    /**
     * Override result panel with PNG→HEIC specific labels.
     */
    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Converted HEIC</h3>
                    <span id="tc-status-chip">Idle</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-stats">
                        <div><span>Original (PNG)</span><b id="tc-stat-orig">&mdash;</b></div>
                        <div><span>Converted (HEIC)</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Saved</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original PNG</button>
                            <button data-tab="result">Converted HEIC</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">Original PNG will appear here</div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">Converted HEIC will appear here</div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
