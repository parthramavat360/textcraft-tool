<?php
/**
 * Widget: SVG Compressor
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Svg_Compressor extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'svg_compressor'; }
    public function get_title(): string { return 'SVG Compressor'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['svg compressor', 'reduce svg size', 'compress svg', 'optimize svg'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Compress SVG files by optimizing paths and removing unnecessary data. Adjust precision for finer control. Everything runs in your browser â€” your files are never uploaded.
        </div>

        <?php $this->render_drop_zone('tc-svg-drop', 'image/svg+xml,.svg', 'Drag & drop SVG files here or click to browse'); ?>
        <?php $this->render_file_row('tc-svg-file'); ?>

        <div class="tc-input-group" style="margin-top:16px">
            <?php $this->render_range('tc-svg-precision', 0, 10, 3, 'Precision', ''); ?>
        </div>

        <?php $this->render_progress_bar('tc-svg-progress', 'Compressing...'); ?>

        <?php $this->render_actions('tc-svg-compress', 'Compress SVG', 'tc-svg-download', 'Download'); ?>

        <div class="tc-stats-row" id="tc-svg-stats">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-svg-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Compressed</span><span class="tc-stat-value" id="tc-svg-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-svg-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-svg-result">
            <div class="tc-preview" id="tc-svg-preview">Upload a file to see preview</div>
        </div>
        <?php
    }
}
