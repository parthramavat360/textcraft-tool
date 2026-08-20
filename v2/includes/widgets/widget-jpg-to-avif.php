<?php
/**
 * Widget: JPG to AVIF Converter
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Jpg_To_Avif extends TextCraft_Tool_Base {

    public function get_name(): string { return 'jpg_to_avif'; }
    public function get_title(): string { return 'JPG to AVIF Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['jpg to avif', 'convert jpg to avif', 'jpeg to avif', 'avif converter', 'next gen image'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert JPG/JPEG images to AVIF format for superior compression and web performance. AVIF offers up to 50% smaller file sizes than JPEG.
        </div>

        <?php $this->render_drop_zone('tc-j2a-drop', 'image/jpeg,.jpg,.jpeg', 'Drag & drop JPG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-j2a-file'); ?>

        <div class="tc-input-group" style="margin-top:16px">
            <?php $this->render_range('tc-j2a-quality', 1, 100, 80, 'Quality', '%'); ?>
        </div>

        <?php $this->render_progress_bar('tc-j2a-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-j2a-convert', 'Convert to AVIF', 'tc-j2a-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (JPG)</span><span class="tc-stat-value" id="tc-j2a-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (AVIF)</span><span class="tc-stat-value" id="tc-j2a-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-j2a-stat-saved">-</span></div>
        </div>
        <?php
    }
}
