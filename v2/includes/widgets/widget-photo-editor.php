<?php
/**
 * Widget: Photo Editor (Basic)
 * Canvas-based photo editor with brightness, contrast, saturation, blur,
 * rotate, flip, crop, and text overlay.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Photo_Editor extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'photo_editor'; }
    public function get_title(): string { return 'Photo Editor'; }
    public function get_icon(): string { return 'eicon-image-edit'; }

    public function get_keywords(): array {
        return ['photo editor', 'online photo editor', 'image editor', 'edit photo', 'photo filters', 'image adjustments'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Edit photos directly in your browser. Adjust brightness, contrast, saturation, blur, and more. Add text overlays and apply filters.
        </div>

        <?php $this->render_drop_zone('tc-pe-drop', 'image/*', 'Drag & drop or click to upload a photo'); ?>
        <?php $this->render_file_row('tc-pe-file'); ?>

        <div class="tc-rsz-options tc-imgprem tc-imgprem-mt">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Adjustments</h4>
                <div class="tc-pe-sliders">
                    <div class="tc-rsz-slider-wrap">
                        <label class="tc-label">Brightness: <span id="tc-pe-brightness-val">100%</span></label>
                        <input type="range" class="tc-rsz-slider" id="tc-pe-brightness" min="0" max="200" value="100">
                    </div>
                    <div class="tc-rsz-slider-wrap">
                        <label class="tc-label">Contrast: <span id="tc-pe-contrast-val">100%</span></label>
                        <input type="range" class="tc-rsz-slider" id="tc-pe-contrast" min="0" max="200" value="100">
                    </div>
                    <div class="tc-rsz-slider-wrap">
                        <label class="tc-label">Saturation: <span id="tc-pe-saturate-val">100%</span></label>
                        <input type="range" class="tc-rsz-slider" id="tc-pe-saturate" min="0" max="200" value="100">
                    </div>
                    <div class="tc-rsz-slider-wrap">
                        <label class="tc-label">Blur: <span id="tc-pe-blur-val">0px</span></label>
                        <input type="range" class="tc-rsz-slider" id="tc-pe-blur" min="0" max="20" value="0">
                    </div>
                    <div class="tc-rsz-slider-wrap">
                        <label class="tc-label">Hue Rotate: <span id="tc-pe-hue-val">0°</span></label>
                        <input type="range" class="tc-rsz-slider" id="tc-pe-hue" min="0" max="360" value="0">
                    </div>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Transform</h4>
                <div class="tc-rsz-mode-cards tc-pe-transform-modes">
                    <button class="tc-rsz-mode-card" type="button" data-val="rotate-left" title="Rotate Left">
                        <span class="tc-rsz-mode-text"><b>↺</b><span>Rotate Left</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="rotate-right" title="Rotate Right">
                        <span class="tc-rsz-mode-text"><b>↻</b><span>Rotate Right</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="flip-h" title="Flip Horizontal">
                        <span class="tc-rsz-mode-text"><b>⇔</b><span>Flip H</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="flip-v" title="Flip Vertical">
                        <span class="tc-rsz-mode-text"><b>⇕</b><span>Flip V</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="grayscale" title="Grayscale">
                        <span class="tc-rsz-mode-text"><b>B/W</b><span>Grayscale</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="sepia" title="Sepia">
                        <span class="tc-rsz-mode-text"><b>Sepia</b><span>Vintage</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="invert" title="Invert">
                        <span class="tc-rsz-mode-text"><b>Inv</b><span>Invert</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="reset" title="Reset All">
                        <span class="tc-rsz-mode-text"><b>Reset</b><span>Clear all</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Text Overlay</h4>
                <div class="tc-pe-text-fields">
                    <div class="tc-rsz-dim-field">
                        <label class="tc-rsz-dim-label">Text</label>
                        <input type="text" class="tc-rsz-num tc-pe-text-input" id="tc-pe-text" placeholder="Enter text to overlay">
                    </div>
                    <div class="tc-pe-text-row">
                        <div class="tc-pe-text-color-field">
                            <label class="tc-rsz-dim-label">Color</label>
                            <div class="tc-premium-color-picker" data-picker="tc-pe-text-color">
                                <label class="tc-pcp-swatch" for="tc-pe-text-color"><span class="tc-pcp-swatch-fill" data-swatch="tc-pe-text-color"></span></label>
                                <span class="tc-pcp-hex"></span>
                                <input type="color" class="tc-pcp-input" id="tc-pe-text-color" value="#ffffff">
                                <div class="tc-pcp-swatches" data-palette="tc-pe-text-color">
                                    <button class="tc-pcp-csw" data-val="#ffffff" type="button"></button>
                                    <button class="tc-pcp-csw" data-val="#fffc3d" type="button"></button>
                                    <button class="tc-pcp-csw" data-val="#ff5b5b" type="button"></button>
                                    <button class="tc-pcp-csw" data-val="#00e0c6" type="button"></button>
                                    <button class="tc-pcp-csw" data-val="#3d7bff" type="button"></button>
                                    <button class="tc-pcp-csw" data-val="#000000" type="button"></button>
                                </div>
                            </div>
                        </div>
                        <div class="tc-pe-text-size-field">
                            <label class="tc-rsz-dim-label">Size</label>
                            <div class="tc-rsz-dim-input">
                                <input type="number" class="tc-rsz-num" id="tc-pe-text-size" value="32" min="8" max="200" title="Font size">
                                <span class="tc-rsz-unit">px</span>
                            </div>
                        </div>
                    </div>
                    <div class="tc-pe-text-pos-field">
                        <label class="tc-rsz-dim-label">Position</label>
                        <div class="tc-pe-pos-pick" id="tc-pe-pos-pick">
                            <div class="tc-pe-pos-trigger" id="tc-pe-pos-trigger" role="button" aria-haspopup="listbox" aria-expanded="false" tabindex="0">
                                <span class="tc-pe-pos-prev" id="tc-pe-pos-prev">Center</span>
                                <svg class="tc-pe-pos-caret" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                            <div class="tc-pe-pos-menu" id="tc-pe-pos-menu" role="listbox">
                                <button class="tc-pe-pos-opt" type="button" role="option" data-val="top">Top</button>
                                <button class="tc-pe-pos-opt" type="button" role="option" data-val="center">Center</button>
                                <button class="tc-pe-pos-opt" type="button" role="option" data-val="bottom">Bottom</button>
                            </div>
                            <select class="tc-rsz-select tc-pe-text-pos tc-pe-pos-native" id="tc-pe-text-pos" aria-hidden="true" tabindex="-1">
                                <option value="top">Top</option>
                                <option value="center" selected>Center</option>
                                <option value="bottom">Bottom</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-pe-bar', 'Processing...'); ?>
        <?php $this->render_actions('tc-pe-apply', 'Apply & Export', 'tc-pe-download', 'Download'); ?>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-pe-preview-wrap" id="tc-pe-preview-wrap" style="display:none">
            <canvas id="tc-pe-canvas" style="max-width:100%;border-radius:8px"></canvas>
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
                            <button data-tab="result">Edited</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">Original preview will appear here</div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <p class="tc-preview-placeholder">Apply edits to see your edited photo here.</p>
                        <?php $this->render_result_content($settings); ?>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
