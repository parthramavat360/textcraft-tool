<?php
/**
 * Widget: JPG to HEIC Converter
 * Premium design with quality slider, iOS downscale, preview tabs.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Jpg_To_Heic extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    protected bool $premium = true;

    public function get_name(): string { return 'jpg_to_heic'; }
    public function get_title(): string { return 'JPG to HEIC Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['jpg to heic', 'convert jpg to heic', 'jpeg to heic', 'heic converter'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert JPG/JPEG images to HEIC format used by Apple devices. Excellent quality with smaller file sizes. Adjust quality to balance size and clarity.
        </div>

        <?php $this->render_drop_zone('tc-j2h-drop', 'image/jpeg,.jpg,.jpeg', 'Drag & drop JPG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-j2h-file'); ?>

        <div class="tc-input-group" >
            <label class="tc-range-label"  for="tc-j2h-quality">
                Quality: <span id="tc-j2h-quality-val">85</span>%
            </label>
            <div class="tc-rsz-slider-wrap">
                <span class="tc-rsz-slider-min" >1</span>
                <input type="range" class="tc-range" id="tc-j2h-quality" min="1" max="100" value="85" >
                <span class="tc-rsz-slider-max" >100</span>
            </div>
            <p class="tc-lvl-hint">Higher quality = larger file. Note: HEIC is encoded as WebP in-browser for compatibility.</p>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-j2h-ios" checked>
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text" >
                    <b>iOS-Compatible Downscale</b>
                    <small>Auto-downscale large images (4096px max) for iOS compatibility.</small>
                </span>
            </label>
        </div>

        <div class="tc-input-group">
            <label class="tc-label"  for="tc-j2h-name">Output file name</label>
            <input type="text" class="tc-input"  id="tc-j2h-name" placeholder="my-image">
            <p class="tc-lvl-hint">Leave empty to use your source file name.</p>
        </div>

        <?php $this->render_progress_bar('tc-j2h-progress', 'Converting...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-j2h-convert" type="button">Convert to HEIC</button>
            <button class="tc-btn tc-btn--ghost" id="tc-j2h-download" type="button" style="display:none">Download HEIC</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-j2h-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (JPG)</span><span class="tc-stat-value" id="tc-j2h-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (HEIC)</span><span class="tc-stat-value" id="tc-j2h-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-j2h-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

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
                        <div><span>Original (JPG)</span><b id="tc-stat-orig">&mdash;</b></div>
                        <div><span>Converted (HEIC)</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Saved</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original JPG</button>
                            <button data-tab="result">Converted HEIC</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">Original JPG will appear here</div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">Converted HEIC will appear here</div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
