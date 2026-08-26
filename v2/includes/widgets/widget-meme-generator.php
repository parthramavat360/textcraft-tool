<?php
/**
 * Widget: Meme Generator
 * Add top/bottom text to images in classic meme style.
 * 100% client-side using canvas API.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Meme_Generator extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

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

        <div class="tc-rsz-options">

            <div class="tc-rsz-section" id="tc-meme-preview-section" style="display:none">
                <h4 class="tc-rsz-heading">Preview</h4>
                <div class="tc-meme-preview-wrap" id="tc-meme-preview-wrap">
                    <canvas id="tc-meme-canvas"></canvas>
                </div>
            </div>

            <div class="tc-rsz-section" id="tc-meme-text-section" style="display:none">
                <h4 class="tc-rsz-heading">Text</h4>
                <div class="tc-meme-text-fields">
                    <div class="tc-meme-field">
                        <div class="tc-input-group">
                            <label class="tc-label">Top Text</label>
                            <input type="text" class="tc-input" id="tc-meme-top" placeholder="TOP TEXT" autocomplete="off">
                        </div>
                    </div>
                    <div class="tc-meme-field">
                        <div class="tc-input-group">
                            <label class="tc-label">Bottom Text</label>
                            <input type="text" class="tc-input" id="tc-meme-bottom" placeholder="BOTTOM TEXT" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>

            <div class="tc-rsz-section" id="tc-meme-style-section" style="display:none">
                <h4 class="tc-rsz-heading">Style</h4>
                <div class="tc-meme-style-row">
                    <div class="tc-meme-field">
                        <label class="tc-label">Font Size <span class="tc-rsz-quality-badge" id="tc-meme-size-val">48</span></label>
                        <div class="tc-rsz-slider-wrap">
                            <span class="tc-rsz-slider-min">Small</span>
                            <input type="range" class="tc-rsz-slider" id="tc-meme-fontsize" min="16" max="120" value="48" step="2">
                            <span class="tc-rsz-slider-max">Big</span>
                        </div>
                    </div>
                    <div class="tc-meme-row">
                        <div class="tc-meme-field">
                            <label class="tc-label">Text Color</label>
                            <div class="tc-wm-color-wrap">
                                <input type="color" id="tc-meme-color" value="#ffffff" class="tc-wm-color">
                                <span class="tc-wm-color-hex">#ffffff</span>
                            </div>
                        </div>
                        <div class="tc-meme-field">
                            <label class="tc-label">Stroke Color</label>
                            <div class="tc-wm-color-wrap">
                                <input type="color" id="tc-meme-stroke" value="#000000" class="tc-wm-color">
                                <span class="tc-wm-color-hex">#000000</span>
                            </div>
                        </div>
                    </div>
                    <div class="tc-meme-field">
                        <label class="tc-label">Stroke Width <span class="tc-rsz-quality-badge" id="tc-meme-stroke-val">3</span></label>
                        <div class="tc-rsz-slider-wrap">
                            <span class="tc-rsz-slider-min">0</span>
                            <input type="range" class="tc-rsz-slider" id="tc-meme-strokewidth" min="0" max="10" value="3" step="1">
                            <span class="tc-rsz-slider-max">10</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tc-rsz-section" id="tc-meme-format-section" style="display:none">
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

        <?php $this->render_actions('tc-meme-apply', 'Generate Meme', 'tc-meme-download', 'Download'); ?>

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
            <p style="color:#64748b;padding:12px 0">Meme will appear here after you click Generate.</p>
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
                        <div class="tc-meme-result-preview" id="tc-meme-preview-orig" style="display:flex;align-items:center;justify-content:center;min-height:200px;background:#0d1321;border-radius:8px;overflow:hidden">
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
