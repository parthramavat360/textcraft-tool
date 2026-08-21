<?php
/**
 * Widget: PNG to HEIC Converter
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Png_To_Heic extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'png_to_heic'; }
    public function get_title(): string { return 'PNG to HEIC Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['png to heic', 'convert png to heic', 'heic converter', 'png heic'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert PNG images to HEIC format used by Apple devices. Achieve smaller file sizes with adjustable quality.
        </div>

        <?php $this->render_drop_zone('tc-p2h-drop', 'image/png,.png', 'Drag & drop PNG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-p2h-file'); ?>

        <div class="tc-input-group" style="margin-top:16px">
            <?php $this->render_range('tc-p2h-quality', 1, 100, 85, 'Quality', '%'); ?>
        </div>

        <?php $this->render_progress_bar('tc-p2h-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-p2h-convert', 'Convert to HEIC', 'tc-p2h-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (PNG)</span><span class="tc-stat-value" id="tc-p2h-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (HEIC)</span><span class="tc-stat-value" id="tc-p2h-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-p2h-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-p2h-result">
            <div class="tc-preview" id="tc-p2h-preview">Upload a file to see preview</div>
        </div>
        <?php
    }
}
