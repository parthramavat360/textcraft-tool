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

    protected bool $premium = true;

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

        <div class="tc-input-group" >
            <label class="tc-range-label"  for="tc-j2p-bgcolor">
                Background Color: <span id="tc-j2p-bgcolor-hex">#ffffff</span>
            </label>
            <div class="tc-premium-color-picker" data-picker="tc-j2p-bgcolor">
                <label class="tc-pcp-swatch" for="tc-j2p-bgcolor"><span class="tc-pcp-swatch-fill" data-swatch="tc-j2p-bgcolor"></span></label>
                <span class="tc-pcp-hex"></span>
                <input type="color" class="tc-pcp-input" id="tc-j2p-bgcolor" value="#ffffff">
                <div class="tc-pcp-swatches" data-palette="tc-j2p-bgcolor">
                    <button class="tc-pcp-csw" data-val="#ffffff" type="button"></button>
                    <button class="tc-pcp-csw" data-val="#0b1220" type="button"></button>
                    <button class="tc-pcp-csw" data-val="#ff0000" type="button"></button>
                    <button class="tc-pcp-csw" data-val="#00bfff" type="button"></button>
                    <button class="tc-pcp-csw" data-val="#00ff00" type="button"></button>
                </div>
            </div>
            <p class="tc-lvl-hint">Fill color behind the image (PNG supports transparency).</p>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-j2p-ios" checked>
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text" >
                    <b>iOS-Compatible Downscale</b>
                    <small>Auto-downscale large images (4096px max) for iOS compatibility.</small>
                </span>
            </label>
        </div>

        <div class="tc-input-group">
            <label class="tc-label"  for="tc-j2p-name">Output file name</label>
            <input type="text" class="tc-input"  id="tc-j2p-name" placeholder="my-image">
            <p class="tc-lvl-hint">Leave empty to use your source file name.</p>
        </div>

        <?php $this->render_progress_bar('tc-j2p-progress', 'Converting...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-j2p-convert" type="button">Convert to PNG</button>
            <button class="tc-btn tc-btn--ghost" id="tc-j2p-download" type="button" style="display:none">Download PNG</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-j2p-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (JPG)</span><span class="tc-stat-value" id="tc-j2p-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (PNG)</span><span class="tc-stat-value" id="tc-j2p-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Difference</span><span class="tc-stat-value" id="tc-j2p-stat-diff">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    /**
     * Override result panel with JPGâ†’PNG specific labels.
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
