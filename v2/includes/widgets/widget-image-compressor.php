<?php
/**
 * Widget: Image Compressor (Unified)
 * Premium redesign. Compress JPG, PNG, WebP, GIF images with quality control.
 * Batch support. 100% client-side.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Image_Compressor extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    protected bool $premium = true;

    public function get_name(): string { return 'image_compressor'; }
    public function get_title(): string { return 'Image Compressor'; }
    public function get_icon(): string { return 'eicon-shrink'; }

    public function get_keywords(): array {
        return ['image compressor', 'compress image', 'reduce image size', 'optimize image', 'image optimizer', 'compress jpg', 'compress png', 'compress webp'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Compress JPG, PNG, WebP or GIF images to reduce file size while maintaining quality. Batch support for multiple images. All processing happens in your browser â€” no upload needed.
        </div>

        <?php $this->render_drop_zone('tc-comp-drop', 'image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif', 'Drag & drop images here or click to browse (multiple files supported)'); ?>

        <div class="tc-input-group tc-comp-file-list" id="tc-comp-file-list" style="display:none">
            <label class="tc-label">Files <span class="tc-lvl-hint tc-comp-count-hint" id="tc-comp-count">0 files</span></label>
            <div class="tc-comp-files" id="tc-comp-files"></div>
        </div>

        <div class="tc-input-group">
            <div class="tc-range-wrap">
                <label class="tc-range-label"  for="tc-comp-quality">
                    Quality: <span id="tc-comp-quality-val">80%</span>
                </label>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min" >5</span>
                    <input type="range" class="tc-range" id="tc-comp-quality" min="5" max="100" value="80" >
                    <span class="tc-rsz-slider-max" >100</span>
                </div>
                <p class="tc-lvl-hint">Lower quality shrinks files more but may soften details.</p>
            </div>
        </div>

        <div class="tc-input-group">
            <div class="tc-range-wrap">
                <label class="tc-range-label"  for="tc-comp-maxw">
                    Max Width: <span id="tc-comp-maxw-val">Off</span>
                </label>
                <input type="range" class="tc-range" id="tc-comp-maxw" min="0" max="4096" value="0" step="32">
                <p class="tc-lvl-hint">Scale wide images down to this width (0 = no limit).</p>
            </div>
        </div>

        <?php $this->render_progress_bar('tc-comp-progress', 'Compressing...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-comp-apply" type="button">Compress Images</button>
            <button class="tc-btn tc-btn--ghost" id="tc-comp-download" type="button" style="display:none">Download All</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-comp-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-comp-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Compressed</span><span class="tc-stat-value" id="tc-comp-stat-out">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-comp-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-comp-result" id="tc-comp-result">
            <p>Compressed images will appear here after you click Compress.</p>
        </div>
        <?php
    }

    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Result</h3>
                    <span id="tc-status-chip">Idle</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-stats">
                        <div><span>Original</span><b id="tc-stat-orig">&mdash;</b></div>
                        <div><span>Compressed</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Saved</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original</button>
                            <button data-tab="result">Compressed</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <div class="tc-comp-result-preview" id="tc-comp-preview-orig">
                            <p>Upload images to see preview</p>
                        </div>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <?php $this->render_result_content($settings); ?>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
