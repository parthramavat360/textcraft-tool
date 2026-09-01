<?php
/**
 * Widget: Meme Generator
 * Add top/bottom text to images in classic meme style.
 * 100% client-side using canvas API.
 * Premium design: sliders, color pickers, presets, font family, clear-all.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Meme_Generator extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    protected bool $premium = true;

    public function get_name(): string { return 'meme_generator'; }
    public function get_title(): string { return 'Meme Generator'; }
    public function get_icon(): string { return 'eicon-comment'; }

    public function get_keywords(): array {
        return ['meme generator', 'create meme', 'meme maker', 'add text to image', 'meme font', 'meme creator'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Create memes by adding bold top and bottom text to any image. Classic meme style with white text and black outline. All processing happens in your browser — no upload needed.
        </div>

        <?php $this->render_drop_zone('tc-meme-drop', 'image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif', 'Drag & drop an image here or click to browse'); ?>
        <?php $this->render_file_row('tc-meme-file'); ?>

        <div class="tc-rsz-options tc-meme-options">

            <div class="tc-rsz-section" id="tc-meme-preview-section">
                <h4 class="tc-rsz-heading">Live Preview</h4>
                <div class="tc-meme-toolbar" id="tc-meme-toolbar">
                    <button class="tc-btn tc-btn--ghost tc-meme-tool" type="button" data-tool="text">&#9998; Text</button>
                    <button class="tc-btn tc-btn--ghost tc-meme-tool" type="button" data-tool="arrow">&#8599; Arrow</button>
                    <button class="tc-btn tc-btn--ghost tc-meme-tool" type="button" data-tool="box">&#9633; Box</button>
                    <button class="tc-btn tc-btn--ghost tc-meme-tool" type="button" data-tool="line">&#9472; Line</button>
                    <button class="tc-btn tc-btn--ghost tc-meme-tool" type="button" data-tool="select">&#128266; Select</button>
                    <button class="tc-btn tc-btn--ghost tc-btn--clear tc-meme-tool" id="tc-meme-del" type="button">&#10005; Delete</button>
                </div>
                <div class="tc-meme-preview-wrap" id="tc-meme-preview-wrap">
                    <canvas id="tc-meme-canvas" class="tc-meme-canvas"></canvas>
                    <p class="tc-meme-placeholder" id="tc-meme-placeholder">Upload an image to start — then click a tool (Text / Arrow / Box / Line) and drag on the image to create.</p>
                </div>
                <p class="tc-meme-hint">Click an element to select it, then drag to move. Style options apply to the currently selected element.</p>
            </div>

            <div class="tc-rsz-section" id="tc-meme-text-section">
                <h4 class="tc-rsz-heading">Text Blocks</h4>
                <div class="tc-meme-block-list" id="tc-meme-block-list"></div>
                <button class="tc-btn tc-btn--ghost tc-meme-add-text" id="tc-meme-add-text" type="button">&#43; Add Text Block</button>
            </div>

            <div class="tc-rsz-section" id="tc-meme-style-section">
                <h4 class="tc-rsz-heading">Style</h4>
                <p class="tc-meme-selection" id="tc-meme-selection">Nothing selected — click an element on the image or add one.</p>

                <div class="tc-input-group" id="tc-meme-align-group">
                    <label class="tc-range-label">Align</label>
                    <div class="tc-modes" data-group="meme-align">
                        <button class="tc-btn tc-btn--ghost" data-align="top" type="button" title="Top">&#8593;</button>
                        <button class="tc-btn tc-btn--ghost" data-align="vcenter" type="button" title="Vertical center">&#8597;</button>
                        <button class="tc-btn tc-btn--ghost" data-align="bottom" type="button" title="Bottom">&#8595;</button>
                        <button class="tc-btn tc-btn--ghost" data-align="left" type="button" title="Left">&#8592;</button>
                        <button class="tc-btn tc-btn--ghost" data-align="hcenter" type="button" title="Horizontal center">&#8596;</button>
                        <button class="tc-btn tc-btn--ghost" data-align="right" type="button" title="Right">&#8594;</button>
                    </div>
                </div>

                <div class="tc-input-group" id="tc-meme-fontsize-group">
                    <label class="tc-range-label" for="tc-meme-fontsize">
                        <span class="tc-meme-opt-name">Font Size</span>
                        <span class="tc-meme-opt-badge" id="tc-meme-size-val">48</span>
                    </label>
                    <div class="tc-rsz-slider-wrap">
                        <span class="tc-rsz-slider-min">Small</span>
                        <input type="range" class="tc-range" id="tc-meme-fontsize" min="16" max="120" value="48" step="2">
                        <span class="tc-rsz-slider-max">Big</span>
                    </div>
                </div>

                <div class="tc-input-group" id="tc-meme-font-group">
                    <label class="tc-range-label">Font Family</label>
                    <div class="tc-modes" data-group="meme-font">
                        <button class="tc-btn tc-btn--ghost sel" data-val="Impact, Arial Black, sans-serif" type="button">Impact</button>
                        <button class="tc-btn tc-btn--ghost" data-val="Arial Black, Arial, sans-serif" type="button">Arial Black</button>
                        <button class="tc-btn tc-btn--ghost" data-val="'Comic Sans MS', cursive, sans-serif" type="button">Comic</button>
                        <button class="tc-btn tc-btn--ghost" data-val="Georgia, serif" type="button">Serif</button>
                        <button class="tc-btn tc-btn--ghost" data-val="Courier New, monospace" type="button">Mono</button>
                    </div>
                </div>

                <div class="tc-input-group" id="tc-meme-stroke-group">
                    <label class="tc-range-label" for="tc-meme-strokewidth">
                        <span class="tc-meme-opt-name">Stroke Width</span>
                        <span class="tc-meme-opt-badge" id="tc-meme-stroke-val">3</span>
                    </label>
                    <div class="tc-rsz-slider-wrap">
                        <span class="tc-rsz-slider-min">0</span>
                        <input type="range" class="tc-range" id="tc-meme-strokewidth" min="0" max="10" value="3" step="1">
                        <span class="tc-rsz-slider-max">10</span>
                    </div>
                </div>

                <div class="tc-input-group tc-meme-color-group" data-picker="tc-meme-color">
                    <label class="tc-range-label" for="tc-meme-color">
                        <span class="tc-meme-opt-name">Text Color</span>
                    </label>
                    <div class="tc-meme-color-picker">
                        <label class="tc-meme-swatch" for="tc-meme-color"><span class="tc-meme-swatch-fill" data-swatch="tc-meme-color"></span></label>
                        <span class="tc-meme-color-hex" id="tc-meme-color-hex">#ffffff</span>
                        <input type="color" class="tc-meme-color-input" id="tc-meme-color" value="#ffffff">
                        <div class="tc-meme-swatches" data-palette="tc-meme-color">
                            <button class="tc-meme-csw" data-val="#ffffff" type="button"></button>
                            <button class="tc-meme-csw" data-val="#ffd700" type="button"></button>
                            <button class="tc-meme-csw" data-val="#ff0000" type="button"></button>
                            <button class="tc-meme-csw" data-val="#00ff00" type="button"></button>
                            <button class="tc-meme-csw" data-val="#00bfff" type="button"></button>
                            <button class="tc-meme-csw" data-val="#ff69b4" type="button"></button>
                            <button class="tc-meme-csw" data-val="#000000" type="button"></button>
                        </div>
                    </div>
                </div>

                <div class="tc-input-group tc-meme-color-group" data-picker="tc-meme-stroke">
                    <label class="tc-range-label" for="tc-meme-stroke">
                        <span class="tc-meme-opt-name">Stroke Color</span>
                    </label>
                    <div class="tc-meme-color-picker">
                        <label class="tc-meme-swatch" for="tc-meme-stroke"><span class="tc-meme-swatch-fill" data-swatch="tc-meme-stroke"></span></label>
                        <span class="tc-meme-color-hex" id="tc-meme-stroke-hex">#000000</span>
                        <input type="color" class="tc-meme-color-input" id="tc-meme-stroke" value="#000000">
                        <div class="tc-meme-swatches" data-palette="tc-meme-stroke">
                            <button class="tc-meme-csw" data-val="#000000" type="button"></button>
                            <button class="tc-meme-csw" data-val="#ffffff" type="button"></button>
                            <button class="tc-meme-csw" data-val="#ff0000" type="button"></button>
                            <button class="tc-meme-csw" data-val="#111111" type="button"></button>
                            <button class="tc-meme-csw" data-val="#333333" type="button"></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tc-rsz-section" id="tc-meme-format-section">
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
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-meme-progress', 'Creating meme...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-meme-apply" type="button">Generate Meme</button>
            <button class="tc-btn tc-btn--ghost" id="tc-meme-download" type="button" style="display:none">Download</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-meme-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-meme-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Output</span><span class="tc-stat-value" id="tc-meme-stat-out">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Text</span><span class="tc-stat-value" id="tc-meme-stat-text">0 chars</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-meme-result" id="tc-meme-result">
            <p class="tc-meme-result-empty">Your meme will appear here after you click Generate.</p>
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
                            <button data-tab="result">Meme</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <div class="tc-meme-preview-empty">
                            <p>Upload an image to see preview</p>
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
