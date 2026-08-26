<?php
/**
 * Widget: Rotate Image
 * Rotate JPG, PNG, WebP, GIF images by 90°, 180°, 270°, or custom angle.
 * Flip horizontally/vertically. 100% client-side using canvas API.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Rotate_Image extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'rotate_image'; }
    public function get_title(): string { return 'Rotate Image'; }
    public function get_icon(): string { return 'eicon-rotate'; }

    public function get_keywords(): array {
        return ['rotate image', 'image rotator', 'rotate jpg', 'rotate png', 'flip image', 'turn image', 'rotate photo'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Rotate JPG, PNG, WebP or GIF images by 90°, 180°, 270°, or any custom angle. Flip horizontally or vertically. All processing happens in your browser — no upload needed.
        </div>

        <?php $this->render_drop_zone('tc-rot-drop', 'image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif', 'Drag & drop an image here or click to browse'); ?>
        <?php $this->render_file_row('tc-rot-file'); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section tc-rot-preview-section" id="tc-rot-preview-section" style="display:none">
                <h4 class="tc-rsz-heading">Preview</h4>
                <div class="tc-rot-preview-wrap" id="tc-rot-preview-wrap">
                    <canvas id="tc-rot-canvas"></canvas>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Rotate</h4>
                <div class="tc-rsz-mode-cards tc-rot-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="90">
                        <span class="tc-rsz-mode-text"><b>90° CW</b><span>Quarter turn right</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="-90">
                        <span class="tc-rsz-mode-text"><b>90° CCW</b><span>Quarter turn left</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="180">
                        <span class="tc-rsz-mode-text"><b>180°</b><span>Upside down</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="custom">
                        <span class="tc-rsz-mode-text"><b>Custom</b><span>Any angle</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section tc-rot-custom-wrap" id="tc-rot-custom-wrap" style="display:none">
                <h4 class="tc-rsz-heading">Angle <span class="tc-rsz-quality-badge" id="tc-rot-angle-val">45°</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">-180°</span>
                    <input type="range" class="tc-rsz-slider" id="tc-rot-angle" min="-180" max="180" value="45" step="1">
                    <span class="tc-rsz-slider-max">180°</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Flip</h4>
                <div class="tc-rsz-mode-cards tc-rot-flip-modes">
                    <button class="tc-rsz-mode-card" type="button" data-val="none">
                        <span class="tc-rsz-mode-text"><b>None</b><span>No flip</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="horizontal">
                        <span class="tc-rsz-mode-text"><b>Horizontal</b><span>Left ↔ Right</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="vertical">
                        <span class="tc-rsz-mode-text"><b>Vertical</b><span>Top ↔ Bottom</span></span>
                    </button>
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

            <div class="tc-rsz-section tc-hide" id="tc-rot-quality-wrap">
                <h4 class="tc-rsz-heading">Quality <span class="tc-rsz-quality-badge" id="tc-rot-quality-val">92%</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">Low</span>
                    <input type="range" class="tc-rsz-slider" id="tc-rot-quality" min="10" max="100" value="92">
                    <span class="tc-rsz-slider-max">Max</span>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-rot-progress', 'Rotating...'); ?>

        <?php $this->render_actions('tc-rot-apply', 'Rotate Image', 'tc-rot-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-rot-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Output</span><span class="tc-stat-value" id="tc-rot-stat-out">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Rotation</span><span class="tc-stat-value" id="tc-rot-stat-angle">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-rot-result" id="tc-rot-result">
            <p style="color:#64748b;padding:12px 0">Rotated image will appear here after you click Rotate.</p>
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
                        <div><span>Output</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Saved</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original</button>
                            <button data-tab="result">Rotated</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <div class="tc-rot-result-preview" id="tc-rot-preview-orig" style="display:flex;align-items:center;justify-content:center;min-height:200px;background:#0d1321;border-radius:8px;overflow:hidden">
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
