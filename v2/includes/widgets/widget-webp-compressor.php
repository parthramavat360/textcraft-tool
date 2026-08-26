<?php
/**
 * Widget: WebP Compressor
 * Premium quality slider, downscale toggle.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Webp_Compressor extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'webp_compressor'; }
    public function get_title(): string { return 'WebP Compressor'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['webp compressor', 'reduce webp size', 'compress webp', 'optimize webp'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Compress WebP images with adjustable quality. Lower quality = smaller file size. Optional downscale available below. Everything runs in your browser.
        </div>

        <?php $this->render_drop_zone('tc-wp-drop', 'image/webp,.webp', 'Drag & drop a WebP image here or click to browse'); ?>
        <?php $this->render_file_row('tc-wp-file'); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Quality <span class="tc-rsz-quality-badge" id="tc-wp-quality-val">90%</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">Smallest</span>
                    <input type="range" class="tc-rsz-slider" id="tc-wp-quality" min="10" max="100" value="90">
                    <span class="tc-rsz-slider-max">Best</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Downscale <span class="tc-rsz-quality-badge" id="tc-wp-resize-val">Off</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-wp-resize">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Downscale</b></span>
                    </label>
                    <span class="tc-rsz-slider-min" style="font-size:12px;opacity:0.6">Resize to max <strong id="tc-wp-maxdim-val">1200</strong>px</span>
                </div>
            </div>

            <div class="tc-rsz-section" id="tc-wp-slider-section" style="display:none">
                <h4 class="tc-rsz-heading">Max Dimension <span class="tc-rsz-quality-badge" id="tc-wp-dim-val">1200px</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">320</span>
                    <input type="range" class="tc-rsz-slider" id="tc-wp-maxdim" min="320" max="2048" value="1200" step="32">
                    <span class="tc-rsz-slider-max">2048</span>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-wp-progress', 'Compressing...'); ?>

        <?php $this->render_actions('tc-wp-compress', 'Compress WebP', 'tc-wp-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-wp-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Compressed</span><span class="tc-stat-value" id="tc-wp-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-wp-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
