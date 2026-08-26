<?php
/**
 * Widget: JPG to AVIF Converter
 * Premium design with quality slider, presets, iOS downscale, preview tabs.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Jpg_To_Avif extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'jpg_to_avif'; }
    public function get_title(): string { return 'JPG to AVIF Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['jpg to avif', 'convert jpg to avif', 'jpeg to avif', 'avif converter', 'next gen image'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert JPG/JPEG images to AVIF format for superior compression and web performance. AVIF offers up to 50% smaller file sizes than JPEG.
        </div>

        <?php $this->render_drop_zone('tc-j2a-drop', 'image/jpeg,.jpg,.jpeg', 'Drag & drop JPG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-j2a-file'); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Quality <span class="tc-rsz-quality-badge" id="tc-j2a-quality-badge">80</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">1</span>
                    <input type="range" class="tc-rsz-slider" id="tc-j2a-quality" min="1" max="100" value="80">
                    <span class="tc-rsz-slider-max">100</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Presets</h4>
                <div class="tc-modes" data-group="j2a-quality">
                    <button class="tc-btn tc-btn--ghost" data-val="60" type="button">Fast (60%)</button>
                    <button class="tc-btn tc-btn--ghost" data-val="75" type="button">Balanced (75%)</button>
                    <button class="tc-btn tc-btn--ghost sel" data-val="80" type="button">High (80%)</button>
                    <button class="tc-btn tc-btn--ghost" data-val="90" type="button">Premium (90%)</button>
                    <button class="tc-btn tc-btn--ghost" data-val="100" type="button">Lossless</button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Options</h4>
                <div class="tc-rsz-slider-wrap">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-j2a-ios" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                    </label>
                    <span style="font-size:12px;opacity:0.6">Auto-downscale large images on iOS (4096px max)</span>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-j2a-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-j2a-convert', 'Convert to AVIF', 'tc-j2a-download', 'Download AVIF'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (JPG)</span><span class="tc-stat-value" id="tc-j2a-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (AVIF)</span><span class="tc-stat-value" id="tc-j2a-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-j2a-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    /**
     * Override result panel with JPG→AVIF specific labels.
     */
    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Converted AVIF</h3>
                    <span id="tc-status-chip">Idle</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-stats">
                        <div><span>Original (JPG)</span><b id="tc-stat-orig">&mdash;</b></div>
                        <div><span>Converted (AVIF)</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Saved</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original JPG</button>
                            <button data-tab="result">Converted AVIF</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">Original JPG will appear here</div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">Converted AVIF will appear here</div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
