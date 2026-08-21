<?php
/**
 * Widget: JPG to PNG Converter
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Jpg_To_Png extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'jpg_to_png'; }
    public function get_title(): string { return 'JPG to PNG Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['jpg to png', 'convert jpg to png', 'jpeg to png', 'image converter'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert JPG/JPEG images to PNG format instantly. Preserves transparency and supports batch conversion with ZIP download.
        </div>

        <?php $this->render_drop_zone('tc-j2p-drop', 'image/jpeg,.jpg,.jpeg', 'Drag & drop JPG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-j2p-file'); ?>

        <?php $this->render_progress_bar('tc-j2p-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-j2p-convert', 'Convert to PNG', 'tc-j2p-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (JPG)</span><span class="tc-stat-value" id="tc-j2p-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (PNG)</span><span class="tc-stat-value" id="tc-j2p-stat-comp">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Difference</span><span class="tc-stat-value" id="tc-j2p-stat-diff">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
