<?php
/**
 * Widget: HEIC to SVG Converter
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Heic_To_Svg extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'heic_to_svg'; }
    public function get_title(): string { return 'HEIC to SVG Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['heic to svg', 'convert heic to svg', 'heic svg converter', 'vectorize heic'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert Apple HEIC images to SVG vector format. Trace bitmap data into scalable vector graphics.
        </div>

        <?php $this->render_drop_zone('tc-h2s-drop', 'image/heic,.heic,.HEIC', 'Drag & drop HEIC images here or click to browse'); ?>
        <?php $this->render_file_row('tc-h2s-file'); ?>

        <div class="tc-checkboxes" style="margin-top:16px">
            <?php $this->render_checkbox('tc-h2s-colors', 'Preserve original colors', true); ?>
        </div>

        <?php $this->render_progress_bar('tc-h2s-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-h2s-convert', 'Convert to SVG', 'tc-h2s-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (HEIC)</span><span class="tc-stat-value" id="tc-h2s-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (SVG)</span><span class="tc-stat-value" id="tc-h2s-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-h2s-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-heic2s-result">
            <div class="tc-preview" id="tc-heic2s-preview">Upload a file to see preview</div>
        </div>
        <?php
    }
}
