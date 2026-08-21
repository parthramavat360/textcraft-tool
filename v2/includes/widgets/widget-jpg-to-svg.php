<?php
/**
 * Widget: JPG to SVG Converter
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Jpg_To_Svg extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'jpg_to_svg'; }
    public function get_title(): string { return 'JPG to SVG Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['jpg to svg', 'convert jpg to svg', 'jpeg to svg', 'image to svg', 'vectorize image'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert JPG/JPEG raster images to scalable SVG vector graphics. Adjust detail level and color settings for optimal results.
        </div>

        <?php $this->render_drop_zone('tc-j2svg-drop', 'image/jpeg,.jpg,.jpeg', 'Drag & drop JPG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-j2svg-file'); ?>

        <div class="tc-input-group" style="margin-top:16px">
            <?php $this->render_select('tc-j2svg-detail', [
                'high'   => 'High Detail',
                'medium' => 'Medium Detail',
                'low'    => 'Low Detail',
            ], 'Detail level'); ?>
        </div>

        <div class="tc-input-group">
            <?php $this->render_select('tc-j2svg-color', [
                'color'   => 'Full Color',
                'grayscale' => 'Grayscale',
                'bw'      => 'Black & White',
            ], 'Color mode'); ?>
        </div>

        <div class="tc-input-group">
            <?php $this->render_range('tc-j2svg-paths', 10, 2000, 500, 'Max paths', ' paths'); ?>
        </div>

        <?php $this->render_progress_bar('tc-j2svg-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-j2svg-convert', 'Convert to SVG', 'tc-j2svg-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (JPG)</span><span class="tc-stat-value" id="tc-j2svg-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (SVG)</span><span class="tc-stat-value" id="tc-j2svg-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-j2svg-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
