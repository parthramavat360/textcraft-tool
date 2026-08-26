<?php
/**
 * Widget: GIF Compressor
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Gif_Compressor extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'gif_compressor'; }
    public function get_title(): string { return 'GIF Compressor'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['gif compressor', 'reduce gif size', 'compress gif', 'optimize gif'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Compress animated and static GIF images. Reduce colors, resize dimensions, and skip frames to shrink file size. If the result is larger than the original, we keep the original.
        </div>

        <?php $this->render_drop_zone('tc-gif-drop', 'image/gif,.gif', 'Drag & drop a GIF image here or click to browse'); ?>
        <?php $this->render_file_row('tc-gif-file'); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Colors <span class="tc-rsz-quality-badge" id="tc-gif-colors-val">64</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">16</span>
                    <input type="range" class="tc-rsz-slider" id="tc-gif-colors" min="4" max="256" value="64" step="4">
                    <span class="tc-rsz-slider-max">256</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Resize <span class="tc-rsz-quality-badge" id="tc-gif-scale-val">100%</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">25%</span>
                    <input type="range" class="tc-rsz-slider" id="tc-gif-scale" min="25" max="100" value="100" step="5">
                    <span class="tc-rsz-slider-max">100%</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Frame Skip <span class="tc-rsz-quality-badge" id="tc-gif-skip-val">None</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">None</span>
                    <input type="range" class="tc-rsz-slider" id="tc-gif-skip" min="0" max="5" value="0">
                    <span class="tc-rsz-slider-max">Heavy</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Loop</h4>
                <div class="tc-rsz-slider-wrap">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-gif-loop" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                    </label>
                    <span class="tc-rsz-slider-min" style="font-size:12px;opacity:0.6">Loop animation forever</span>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-gif-progress', 'Compressing...'); ?>

        <?php $this->render_actions('tc-gif-compress', 'Compress GIF', 'tc-gif-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-gif-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Compressed</span><span class="tc-stat-value" id="tc-gif-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-gif-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
