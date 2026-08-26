<?php
/**
 * Widget: Watermark Image
 * Add text or image watermark to JPG, PNG, WebP, GIF images.
 * 100% client-side using canvas API.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Watermark_Image extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'watermark_image'; }
    public function get_title(): string { return 'Watermark Image'; }
    public function get_icon(): string { return 'eicon-site-identity'; }

    public function get_keywords(): array {
        return ['watermark image', 'add watermark', 'photo watermark', 'image watermark', 'watermark photo', 'copyright image'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Add a text or image watermark to JPG, PNG, WebP or GIF images. Choose position, opacity, font, and more. All processing happens in your browser — no upload needed.
        </div>

        <?php $this->render_drop_zone('tc-wm-drop', 'image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif', 'Drag & drop an image here or click to browse'); ?>
        <?php $this->render_file_row('tc-wm-file'); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section tc-wm-preview-section" id="tc-wm-preview-section" style="display:none">
                <h4 class="tc-rsz-heading">Preview</h4>
                <div class="tc-wm-preview-wrap" id="tc-wm-preview-wrap">
                    <canvas id="tc-wm-canvas"></canvas>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Watermark Type</h4>
                <div class="tc-rsz-mode-cards tc-wm-type-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="text">
                        <span class="tc-rsz-mode-text"><b>Text</b><span>Custom text watermark</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="image">
                        <span class="tc-rsz-mode-text"><b>Image</b><span>Upload logo/watermark</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section" id="tc-wm-text-opts">
                <h4 class="tc-rsz-heading">Text Settings</h4>
                <div class="tc-wm-text-fields">
                    <div class="tc-wm-field">
                        <div class="tc-input-group">
                            <label class="tc-label">Watermark text</label>
                            <input type="text" class="tc-input" id="tc-wm-text" value="WATERMARK" placeholder="Enter watermark text" autocomplete="off">
                        </div>
                    </div>
                    <div class="tc-wm-row">
                        <div class="tc-wm-field">
                            <label class="tc-rsz-dim-label">Font</label>
                            <div class="tc-rsz-select-wrap">
                                <select id="tc-wm-font" class="tc-rsz-select">
                                    <option value="Arial">Arial</option>
                                    <option value="Georgia">Georgia</option>
                                    <option value="Times New Roman">Times New Roman</option>
                                    <option value="Courier New">Courier New</option>
                                    <option value="Verdana">Verdana</option>
                                    <option value="Impact">Impact</option>
                                </select>
                            </div>
                        </div>
                        <div class="tc-wm-field">
                            <label class="tc-rsz-dim-label">Size</label>
                            <div class="tc-rsz-dim-input">
                                <input type="number" class="tc-rsz-num" id="tc-wm-size" value="48" min="8" max="200">
                                <span class="tc-rsz-unit">px</span>
                            </div>
                        </div>
                    </div>
                    <div class="tc-wm-row">
                        <div class="tc-wm-field">
                            <label class="tc-rsz-dim-label">Color</label>
                            <div class="tc-wm-color-wrap">
                                <input type="color" id="tc-wm-color" value="#ffffff" class="tc-wm-color">
                                <span class="tc-wm-color-hex" id="tc-wm-color-hex">#ffffff</span>
                            </div>
                        </div>
                        <div class="tc-wm-field">
                            <label class="tc-rsz-dim-label">Stroke</label>
                            <div class="tc-wm-color-wrap">
                                <input type="color" id="tc-wm-stroke-color" value="#000000" class="tc-wm-color">
                                <span class="tc-wm-color-hex" id="tc-wm-stroke-hex">#000000</span>
                            </div>
                        </div>
                    </div>
                    <div class="tc-wm-field">
                        <label class="tc-rsz-dim-label">Stroke Width</label>
                        <div class="tc-rsz-slider-wrap">
                            <span class="tc-rsz-slider-min">0</span>
                            <input type="range" class="tc-rsz-slider" id="tc-wm-stroke-width" min="0" max="10" value="1" step="1">
                            <span class="tc-rsz-slider-max">10</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tc-rsz-section" id="tc-wm-image-opts" style="display:none">
                <h4 class="tc-rsz-heading">Watermark Image</h4>
                <div class="tc-wm-logo-drop" id="tc-wm-logo-drop">
                    <p>Drag & drop a logo or click to browse</p>
                    <input type="file" id="tc-wm-logo-input" accept="image/*" style="display:none">
                </div>
                <div class="tc-wm-logo-preview" id="tc-wm-logo-preview" style="display:none">
                    <img id="tc-wm-logo-img" alt="Watermark logo">
                    <button class="tc-wm-logo-remove" id="tc-wm-logo-remove" type="button">✕</button>
                </div>
                <div class="tc-wm-field" id="tc-wm-logo-size-wrap">
                    <label class="tc-rsz-dim-label">Logo Scale</label>
                    <div class="tc-rsz-slider-wrap">
                        <span class="tc-rsz-slider-min">10%</span>
                        <input type="range" class="tc-rsz-slider" id="tc-wm-logo-scale" min="5" max="80" value="20" step="1">
                        <span class="tc-rsz-slider-max">80%</span>
                    </div>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Position</h4>
                <div class="tc-wm-position-grid">
                    <button class="tc-wm-pos" type="button" data-val="top-left">TL</button>
                    <button class="tc-wm-pos" type="button" data-val="top-center">TC</button>
                    <button class="tc-wm-pos" type="button" data-val="top-right">TR</button>
                    <button class="tc-wm-pos" type="button" data-val="center-left">CL</button>
                    <button class="tc-wm-pos tc-wm-pos sel" type="button" data-val="center">C</button>
                    <button class="tc-wm-pos" type="button" data-val="center-right">CR</button>
                    <button class="tc-wm-pos" type="button" data-val="bottom-left">BL</button>
                    <button class="tc-wm-pos" type="button" data-val="bottom-center">BC</button>
                    <button class="tc-wm-pos" type="button" data-val="bottom-right">BR</button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Opacity <span class="tc-rsz-quality-badge" id="tc-wm-opacity-val">50%</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">10%</span>
                    <input type="range" class="tc-rsz-slider" id="tc-wm-opacity" min="5" max="100" value="50" step="1">
                    <span class="tc-rsz-slider-max">100%</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Repeat</h4>
                <div class="tc-rsz-mode-cards tc-wm-repeat-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="once">
                        <span class="tc-rsz-mode-text"><b>Once</b><span>Single watermark</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="tile">
                        <span class="tc-rsz-mode-text"><b>Tile</b><span>Repeat across image</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section" id="tc-wm-tile-opts" style="display:none">
                <h4 class="tc-rsz-heading">Tile Spacing <span class="tc-rsz-quality-badge" id="tc-wm-spacing-val">100px</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">20px</span>
                    <input type="range" class="tc-rsz-slider" id="tc-wm-spacing" min="20" max="500" value="100" step="10">
                    <span class="tc-rsz-slider-max">500px</span>
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

            <div class="tc-rsz-section tc-hide" id="tc-wm-quality-wrap">
                <h4 class="tc-rsz-heading">Quality <span class="tc-rsz-quality-badge" id="tc-wm-quality-val">92%</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">Low</span>
                    <input type="range" class="tc-rsz-slider" id="tc-wm-quality" min="10" max="100" value="92">
                    <span class="tc-rsz-slider-max">Max</span>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-wm-progress', 'Adding watermark...'); ?>

        <?php $this->render_actions('tc-wm-apply', 'Add Watermark', 'tc-wm-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-wm-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Output</span><span class="tc-stat-value" id="tc-wm-stat-out">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-wm-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-wm-result" id="tc-wm-result">
            <p style="color:#64748b;padding:12px 0">Watermarked image will appear here after you click Add Watermark.</p>
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
                            <button data-tab="result">Watermarked</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <div class="tc-wm-result-preview" id="tc-wm-preview-orig" style="display:flex;align-items:center;justify-content:center;min-height:200px;background:#0d1321;border-radius:8px;overflow:hidden">
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
