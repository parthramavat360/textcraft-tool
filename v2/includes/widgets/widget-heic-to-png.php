<?php
/**
 * Widget: HEIC to PNG Converter
 * Premium design with iOS downscale, preview tabs.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Heic_To_Png extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'heic_to_png'; }
    public function get_title(): string { return 'HEIC to PNG Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['heic to png', 'convert heic to png', 'heic png converter', 'apple image converter'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert Apple HEIC images to PNG format with full transparency support. Lossless conversion preserves image quality.
        </div>

        <?php $this->render_drop_zone('tc-h2p-drop', 'image/heic,.heic,.HEIC', 'Drag & drop HEIC images here or click to browse'); ?>
        <?php $this->render_file_row('tc-h2p-file'); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Options</h4>
                <div class="tc-rsz-slider-wrap">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-h2p-ios" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Auto-downscale large images on iOS (4096px max)</b></span>
                    </label>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-h2p-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-h2p-convert', 'Convert to PNG', 'tc-h2p-download', 'Download PNG'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (HEIC)</span><span class="tc-stat-value" id="tc-h2p-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (PNG)</span><span class="tc-stat-value" id="tc-h2p-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-h2p-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    /**
     * Override result panel with HEIC→PNG specific labels.
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
                        <div><span>Original (HEIC)</span><b id="tc-stat-orig">&mdash;</b></div>
                        <div><span>Converted (PNG)</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Saved</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original HEIC</button>
                            <button data-tab="result">Converted PNG</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">Original HEIC will appear here</div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">Converted PNG will appear here</div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
