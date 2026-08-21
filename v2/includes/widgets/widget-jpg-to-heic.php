<?php
/**
 * Widget: JPG to HEIC Converter
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Jpg_To_Heic extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'jpg_to_heic'; }
    public function get_title(): string { return 'JPG to HEIC Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['jpg to heic', 'convert jpg to heic', 'jpeg to heic', 'heic converter'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert JPG/JPEG images to HEIC format used by Apple devices. Smaller file sizes with excellent image quality.
        </div>

        <?php $this->render_drop_zone('tc-j2h-drop', 'image/jpeg,.jpg,.jpeg', 'Drag & drop JPG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-j2h-file'); ?>

        <div class="tc-input-group" style="margin-top:16px">
            <?php $this->render_range('tc-j2h-quality', 1, 100, 85, 'Quality', '%'); ?>
        </div>

        <?php $this->render_progress_bar('tc-j2h-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-j2h-convert', 'Convert to HEIC', 'tc-j2h-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (JPG)</span><span class="tc-stat-value" id="tc-j2h-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (HEIC)</span><span class="tc-stat-value" id="tc-j2h-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-j2h-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-j2h-result">
            <div class="tc-preview" id="tc-j2h-preview">Upload a file to see preview</div>
        </div>
        <?php
    }
}
