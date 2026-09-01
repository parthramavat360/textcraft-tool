<?php
/**
 * Widget: Crop Image
 * Crop JPG, PNG, WebP, GIF images with interactive canvas preview.
 * 100% client-side using canvas API.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Crop_Image extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'crop_image'; }
    public function get_title(): string { return 'Crop Image'; }
    public function get_icon(): string { return 'eicon-crop'; }

    public function get_keywords(): array {
        return ['crop image', 'image cropper', 'crop jpg', 'crop png', 'cut image', 'trim image', 'resize crop'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Crop JPG, PNG, WebP or GIF images by selecting an area or choosing a preset aspect ratio. All processing happens in your browser — no upload needed.
        </div>

        <?php $this->render_drop_zone('tc-crop-drop', 'image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif', 'Drag & drop an image here or click to browse'); ?>
        <?php $this->render_file_row('tc-crop-file'); ?>

        <div class="tc-rsz-options tc-imgopt">

            <div class="tc-rsz-section" id="tc-crop-preview-wrap" style="display:none">
                <h4 class="tc-rsz-heading">Image Preview</h4>
                <div class="tc-crop-canvas-wrap" id="tc-crop-canvas-wrap">
                    <canvas id="tc-crop-canvas"></canvas>
                    <div class="tc-crop-overlay" id="tc-crop-overlay">
                        <div class="tc-crop-box" id="tc-crop-box">
                            <div class="tc-crop-handle tc-crop-handle-nw" data-handle="nw"></div>
                            <div class="tc-crop-handle tc-crop-handle-ne" data-handle="ne"></div>
                            <div class="tc-crop-handle tc-crop-handle-sw" data-handle="sw"></div>
                            <div class="tc-crop-handle tc-crop-handle-se" data-handle="se"></div>
                            <div class="tc-crop-handle tc-crop-handle-n" data-handle="n"></div>
                            <div class="tc-crop-handle tc-crop-handle-s" data-handle="s"></div>
                            <div class="tc-crop-handle tc-crop-handle-e" data-handle="e"></div>
                            <div class="tc-crop-handle tc-crop-handle-w" data-handle="w"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Aspect Ratio</h4>
                <div class="tc-rsz-mode-cards tc-crop-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="free">
                        <span class="tc-rsz-mode-text"><b>Free</b><span>Any dimensions</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="1:1">
                        <span class="tc-rsz-mode-text"><b>1:1</b><span>Square</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="16:9">
                        <span class="tc-rsz-mode-text"><b>16:9</b><span>Widescreen</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="4:3">
                        <span class="tc-rsz-mode-text"><b>4:3</b><span>Standard</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="3:2">
                        <span class="tc-rsz-mode-text"><b>3:2</b><span>Photo</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="9:16">
                        <span class="tc-rsz-mode-text"><b>9:16</b><span>Portrait</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="custom">
                        <span class="tc-rsz-mode-text"><b>Custom</b><span>Set exact size</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section tc-crop-custom-opts" id="tc-crop-custom-opts" style="display:none">
                <h4 class="tc-rsz-heading">Custom Dimensions</h4>
                <div class="tc-rsz-dims">
                    <div class="tc-rsz-dim-field">
                        <label class="tc-rsz-dim-label">Width</label>
                        <div class="tc-rsz-dim-input">
                            <input type="number" class="tc-rsz-num" id="tc-crop-custom-w" placeholder="800" min="1" max="10000">
                            <span class="tc-rsz-unit">px</span>
                        </div>
                    </div>
                    <button class="tc-rsz-lock tc-rsz-lock--on" id="tc-crop-lock" type="button" title="Aspect ratio locked">
                        <svg class="tc-rsz-lock-on" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <svg class="tc-rsz-lock-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></svg>
                    </button>
                    <div class="tc-rsz-dim-field">
                        <label class="tc-rsz-dim-label">Height</label>
                        <div class="tc-rsz-dim-input">
                            <input type="number" class="tc-rsz-num" id="tc-crop-custom-h" placeholder="600" min="1" max="10000">
                            <span class="tc-rsz-unit">px</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Output Format</h4>
                <div class="tc-rsz-format-row">
                    <button class="tc-rsz-fmt sel" type="button" data-val="original">
                        <span class="tc-rsz-fmt-icon">&#9881;</span>
                        <span>Same</span>
                    </button>
                    <button class="tc-rsz-fmt" type="button" data-val="image/jpeg">
                        <span class="tc-rsz-fmt-icon">JPG</span>
                        <span>JPEG</span>
                    </button>
                    <button class="tc-rsz-fmt" type="button" data-val="image/png">
                        <span class="tc-rsz-fmt-icon">PNG</span>
                        <span>PNG</span>
                    </button>
                    <button class="tc-rsz-fmt" type="button" data-val="image/webp">
                        <span class="tc-rsz-fmt-icon">W</span>
                        <span>WebP</span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section tc-hide" id="tc-crop-quality-wrap">
                <h4 class="tc-rsz-heading">Quality <span class="tc-rsz-quality-badge" id="tc-crop-quality-val">92%</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">Low</span>
                    <input type="range" class="tc-rsz-slider" id="tc-crop-quality" min="10" max="100" value="92">
                    <span class="tc-rsz-slider-max">Max</span>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-crop-progress', 'Cropping...'); ?>

        <?php $this->render_actions('tc-crop-apply', 'Crop Image', 'tc-crop-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-crop-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Cropped</span><span class="tc-stat-value" id="tc-crop-stat-crop">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Dimensions</span><span class="tc-stat-value" id="tc-crop-stat-dims">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-crop-result" id="tc-crop-result">
            <p style="color:#64748b;padding:12px 0">Cropped image will appear here after you click Crop.</p>
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
                        <div><span>Cropped</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Saved</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original</button>
                            <button data-tab="result">Cropped</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <div class="tc-crop-result-preview" id="tc-crop-preview-orig" style="display:flex;align-items:center;justify-content:center;min-height:200px;background:#0d1321;border-radius:8px;overflow:hidden">
                            <p style="color:#64748b">Upload an image to see preview</p>
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
