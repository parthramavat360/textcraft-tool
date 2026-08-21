<?php
/**
 * Widget: JPG to GIF Converter
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Jpg_To_Gif extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'jpg_to_gif'; }
    public function get_title(): string { return 'JPG to GIF Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['jpg to gif', 'convert jpg to gif', 'jpeg to gif', 'gif converter', 'image to gif'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert JPG/JPEG images to GIF format. Ideal for simple graphics, logos, and images with limited color palettes.
        </div>

        <?php $this->render_drop_zone('tc-j2gif-drop', 'image/jpeg,.jpg,.jpeg', 'Drag & drop JPG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-j2gif-file'); ?>

        <div class="tc-input-group" style="margin-top:16px">
            <?php $this->render_select('tc-j2gif-colors', [
                '16'  => '16 Colors',
                '32'  => '32 Colors',
                '64'  => '64 Colors',
                '128' => '128 Colors',
                '256' => '256 Colors',
            ], 'Color palette'); ?>
        </div>

        <?php $this->render_progress_bar('tc-j2gif-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-j2gif-convert', 'Convert to GIF', 'tc-j2gif-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (JPG)</span><span class="tc-stat-value" id="tc-j2gif-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (GIF)</span><span class="tc-stat-value" id="tc-j2gif-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-j2gif-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
