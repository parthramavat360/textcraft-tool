<?php
/**
 * Widget: PNG to SVG Converter
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Png_To_Svg extends TextCraft_Tool_Base {

    public function get_name(): string { return 'png_to_svg'; }
    public function get_title(): string { return 'PNG to SVG Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['png to svg', 'convert png to svg', 'vectorize png', 'png svg converter'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert PNG raster images to scalable SVG vector graphics. Trace your images into resolution-independent vectors.
        </div>

        <?php $this->render_drop_zone('tc-p2svg-drop', 'image/png,.png', 'Drag & drop PNG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-p2svg-file'); ?>

        <div class="tc-checkboxes" style="margin-top:16px">
            <?php $this->render_checkbox('tc-p2svg-trace', 'Enable color tracing', true); ?>
            <?php $this->render_checkbox('tc-p2svg-transparency', 'Preserve transparency', true); ?>
        </div>

        <?php $this->render_progress_bar('tc-p2svg-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-p2svg-convert', 'Convert to SVG', 'tc-p2svg-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (PNG)</span><span class="tc-stat-value" id="tc-p2svg-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (SVG)</span><span class="tc-stat-value" id="tc-p2svg-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-p2svg-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-p2s-result">
            <div class="tc-preview" id="tc-p2s-preview">Upload a file to see preview</div>
        </div>
        <?php
    }
}
