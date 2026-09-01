<?php
/**
 * Widget: Upscale Image
 * Upscale images to 2x, 3x, 4x using high-quality canvas interpolation.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Upscale_Image extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'upscale_image'; }
    public function get_title(): string { return 'Upscale Image'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['upscale image', 'image upscaler', 'enlarge image', 'increase resolution', 'upscale photo', 'image resolution'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Increase image resolution using high-quality canvas interpolation. Upscale to 2x, 3x, or 4x while maintaining sharpness.
        </div>

        <?php $this->render_drop_zone('tc-up-drop', 'image/*', 'Drag & drop or click to upload an image'); ?>
        <?php $this->render_file_row('tc-up-file'); ?>

        <div class="tc-rsz-options tc-imgopt tc-imgopt-mt">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Upscale Factor</h4>
                <div class="tc-rsz-mode-cards tc-up-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="2">
                        <span class="tc-rsz-mode-text"><b>2×</b><span>Double</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="3">
                        <span class="tc-rsz-mode-text"><b>3×</b><span>Triple</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="4">
                        <span class="tc-rsz-mode-text"><b>4×</b><span>Quad</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Interpolation</h4>
                <div class="tc-rsz-mode-cards tc-up-interp-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="high">
                        <span class="tc-rsz-mode-text"><b>High Quality</b><span>Best results</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="medium">
                        <span class="tc-rsz-mode-text"><b>Medium</b><span>Faster</span></span>
                    </button>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-up-bar', 'Upscaling...'); ?>
        <?php $this->render_actions('tc-up-start', 'Upscale Image', 'tc-up-download', 'Download'); ?>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-up-preview" id="tc-up-preview">
            <canvas id="tc-up-canvas" style="display:none"></canvas>
            <div class="tc-up-info" id="tc-up-info" style="display:none">
                <div class="tc-age-result-grid">
                    <div class="tc-age-mini-card">
                        <span class="tc-age-mini-val" id="tc-up-orig-size">-</span>
                        <span class="tc-age-mini-label">Original</span>
                    </div>
                    <div class="tc-age-mini-card">
                        <span class="tc-age-mini-val" id="tc-up-new-size">-</span>
                        <span class="tc-age-mini-label">Upscaled</span>
                    </div>
                    <div class="tc-age-mini-card">
                        <span class="tc-age-mini-val" id="tc-up-dimensions">-</span>
                        <span class="tc-age-mini-label">Dimensions</span>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
