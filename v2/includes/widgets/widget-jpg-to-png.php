<?php
/**
 * Widget: JPG to PNG Converter
 * Premium design with background color, iOS downscale, preview tabs.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Jpg_To_Png extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'jpg_to_png'; }
    public function get_title(): string { return 'JPG to PNG Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['jpg to png', 'convert jpg to png', 'jpeg to png', 'image converter'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert JPG/JPEG images to PNG format with lossless quality. Choose a background color for transparency and enable iOS downscaling for large images.
        </div>

        <?php $this->render_drop_zone('tc-j2p-drop', 'image/jpeg,.jpg,.jpeg', 'Drag & drop JPG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-j2p-file'); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Background Color</h4>
                <div class="tc-rsz-slider-wrap">
                    <input type="color" class="tc-color" id="tc-j2p-bgcolor" value="#ffffff" style="width:40px;height:36px;border:1.5px solid var(--line);border-radius:8px;cursor:pointer;padding:2px">
                    <span class="tc-rsz-quality-badge" id="tc-j2p-bgcolor-hex">#ffffff</span>
                    <span style="font-size:12px;opacity:0.6">Fill color behind the image (PNG supports transparency)</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Options</h4>
                <div class="tc-rsz-slider-wrap">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-j2p-ios" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                    </label>
                    <span style="font-size:12px;opacity:0.6">Auto-downscale large images on iOS (4096px max)</span>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-j2p-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-j2p-convert', 'Convert to PNG', 'tc-j2p-download', 'Download PNG'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (JPG)</span><span class="tc-stat-value" id="tc-j2p-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (PNG)</span><span class="tc-stat-value" id="tc-j2p-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Difference</span><span class="tc-stat-value" id="tc-j2p-stat-diff">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    /**
     * Override result panel with JPG→PNG specific labels.
     */
    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Converted PNG</h3>
                    <span id="tc-status-chip">Idle</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-stats">
                        <div><span>Original (JPG)</span><b id="tc-stat-orig">&mdash;</b></div>
                        <div><span>Converted (PNG)</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Difference</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original JPG</button>
                            <button data-tab="result">Converted PNG</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">Original JPG will appear here</div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">Converted PNG will appear here</div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
