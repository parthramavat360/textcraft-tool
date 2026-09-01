<?php
/**
 * Widget: Blur Face / Objects
 * Blur specific areas of an image by drawing rectangles.
 * 100% client-side using canvas API.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Blur_Face extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'blur_face'; }
    public function get_title(): string { return 'Blur Face / Objects'; }
    public function get_icon(): string { return 'eicon-bullseye'; }

    public function get_keywords(): array {
        return ['blur face', 'blur object', 'censor image', 'blur area', 'pixelate face', 'redact image', 'blur photo'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Blur faces, license plates, or any area in a photo. Draw rectangles over the areas you want to blur. All processing happens in your browser — no upload needed.
        </div>

        <?php $this->render_drop_zone('tc-blur-drop', 'image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif', 'Drag & drop an image here or click to browse'); ?>
        <?php $this->render_file_row('tc-blur-file'); ?>

        <div class="tc-rsz-options tc-imgprem">

            <div class="tc-rsz-section tc-blur-workspace" id="tc-blur-workspace" style="display:none">
                <h4 class="tc-rsz-heading">Draw rectangles over areas to blur</h4>
                <div class="tc-blur-canvas-wrap" id="tc-blur-canvas-wrap">
                    <canvas id="tc-blur-canvas"></canvas>
                    <div class="tc-blur-overlay" id="tc-blur-overlay"></div>
                </div>
                <div class="tc-blur-actions-row">
                    <button class="tc-blur-clear-btn" id="tc-blur-clear" type="button">Clear All</button>
                    <button class="tc-blur-undo-btn" id="tc-blur-undo" type="button">Undo Last</button>
                    <span class="tc-blur-count" id="tc-blur-count">0 areas selected</span>
                </div>
            </div>

            <div class="tc-rsz-section" id="tc-blur-strength-section" style="display:none">
                <h4 class="tc-rsz-heading">Blur Strength <span class="tc-rsz-quality-badge" id="tc-blur-strength-val">10px</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">Light</span>
                    <input type="range" class="tc-rsz-slider" id="tc-blur-strength" min="2" max="50" value="10" step="1">
                    <span class="tc-rsz-slider-max">Heavy</span>
                </div>
            </div>

            <div class="tc-rsz-section" id="tc-blur-mode-section" style="display:none">
                <h4 class="tc-rsz-heading">Blur Mode</h4>
                <div class="tc-rsz-mode-cards tc-blur-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="gaussian">
                        <span class="tc-rsz-mode-text"><b>Gaussian</b><span>Smooth blur</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="pixelate">
                        <span class="tc-rsz-mode-text"><b>Pixelate</b><span>Mosaic effect</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section" id="tc-blur-format-section" style="display:none">
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

            <div class="tc-rsz-section tc-hide" id="tc-blur-quality-wrap">
                <h4 class="tc-rsz-heading">Quality <span class="tc-rsz-quality-badge" id="tc-blur-quality-val">92%</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">Low</span>
                    <input type="range" class="tc-rsz-slider" id="tc-blur-quality" min="10" max="100" value="92">
                    <span class="tc-rsz-slider-max">Max</span>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-blur-progress', 'Blurring...'); ?>

        <?php $this->render_actions('tc-blur-apply', 'Apply Blur', 'tc-blur-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-blur-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Output</span><span class="tc-stat-value" id="tc-blur-stat-out">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Areas</span><span class="tc-stat-value" id="tc-blur-stat-areas">0</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-blur-result" id="tc-blur-result">
            <p style="color:#64748b;padding:12px 0">Blurred image will appear here after you click Apply Blur.</p>
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
                            <button data-tab="result">Blurred</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <div class="tc-blur-result-preview" id="tc-blur-preview-orig" style="display:flex;align-items:center;justify-content:center;min-height:200px;background:#0d1321;border-radius:8px;overflow:hidden">
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
