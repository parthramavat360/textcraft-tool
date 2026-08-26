<?php
/**
 * Widget: Flip Image
 * Flip JPG, PNG, WebP, GIF images horizontally, vertically, or both.
 * 100% client-side using canvas API.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Flip_Image extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'flip_image'; }
    public function get_title(): string { return 'Flip Image'; }
    public function get_icon(): string { return 'eicon-flow'; }

    public function get_keywords(): array {
        return ['flip image', 'mirror image', 'flip jpg', 'flip png', 'horizontal flip', 'vertical flip', 'reverse image'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Flip JPG, PNG, WebP or GIF images horizontally, vertically, or both. Mirror your photo in one click. All processing happens in your browser — no upload needed.
        </div>

        <?php $this->render_drop_zone('tc-flip-drop', 'image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif', 'Drag & drop an image here or click to browse'); ?>
        <?php $this->render_file_row('tc-flip-file'); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section" id="tc-flip-preview-section" style="display:none">
                <h4 class="tc-rsz-heading">Preview</h4>
                <div class="tc-flip-preview-wrap" id="tc-flip-preview-wrap">
                    <canvas id="tc-flip-canvas"></canvas>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Flip Direction</h4>
                <div class="tc-rsz-mode-cards tc-flip-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="horizontal">
                        <span class="tc-rsz-mode-text"><b>Horizontal</b><span>Mirror left to right</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="vertical">
                        <span class="tc-rsz-mode-text"><b>Vertical</b><span>Mirror top to bottom</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="both">
                        <span class="tc-rsz-mode-text"><b>Both</b><span>Rotate 180°</span></span>
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

            <div class="tc-rsz-section tc-hide" id="tc-flip-quality-wrap">
                <h4 class="tc-rsz-heading">Quality <span class="tc-rsz-quality-badge" id="tc-flip-quality-val">92%</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">Low</span>
                    <input type="range" class="tc-rsz-slider" id="tc-flip-quality" min="10" max="100" value="92">
                    <span class="tc-rsz-slider-max">Max</span>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-flip-progress', 'Flipping...'); ?>

        <?php $this->render_actions('tc-flip-apply', 'Flip Image', 'tc-flip-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-flip-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Output</span><span class="tc-stat-value" id="tc-flip-stat-out">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Direction</span><span class="tc-stat-value" id="tc-flip-stat-dir">Horizontal</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-flip-result" id="tc-flip-result">
            <p style="color:#64748b;padding:12px 0">Flipped image will appear here after you click Flip.</p>
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
                            <button data-tab="result">Flipped</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <div class="tc-flip-result-preview" id="tc-flip-preview-orig" style="display:flex;align-items:center;justify-content:center;min-height:200px;background:#0d1321;border-radius:8px;overflow:hidden">
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
