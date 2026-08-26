<?php
/**
 * Widget: PNG Compressor
 * Quality slider (lossy quantization via UPNG), downscale toggle.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Png_Compressor extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'png_compressor'; }
    public function get_title(): string { return 'PNG Compressor'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['png compressor', 'reduce png size', 'compress png online', 'optimize png'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Compress PNG images with adjustable quality and optional downscale. Lower quality = smaller file size. Transparency is preserved. Everything runs in your browser.
        </div>

        <?php $this->render_drop_zone('tc-png-drop', 'image/png,.png', 'Drag & drop a PNG image here or click to browse'); ?>
        <?php $this->render_file_row('tc-png-file'); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Quality <span class="tc-rsz-quality-badge" id="tc-png-quality-val">92%</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">Smallest</span>
                    <input type="range" class="tc-rsz-slider" id="tc-png-quality" min="10" max="100" value="92">
                    <span class="tc-rsz-slider-max">Best</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Downscale <span class="tc-rsz-quality-badge" id="tc-png-resize-val">Off</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-png-resize">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                        <span class="tc-rsz-toggle-text"><b>Downscale</b></span>
                    </label>
                    <span class="tc-rsz-slider-min" style="font-size:12px;opacity:0.6">Resize to max <strong id="tc-png-maxdim-val">1200</strong>px</span>
                </div>
            </div>

            <div class="tc-rsz-section" id="tc-png-slider-section" style="display:none">
                <h4 class="tc-rsz-heading">Max Dimension <span class="tc-rsz-quality-badge" id="tc-png-dim-val">1200px</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">320</span>
                    <input type="range" class="tc-rsz-slider" id="tc-png-maxdim" min="320" max="2048" value="1200" step="32">
                    <span class="tc-rsz-slider-max">2048</span>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-png-progress', 'Compressing...'); ?>

        <?php $this->render_actions('tc-png-compress', 'Compress PNG', 'tc-png-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-png-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Compressed</span><span class="tc-stat-value" id="tc-png-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-png-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
