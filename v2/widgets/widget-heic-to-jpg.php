<?php
/**
 * Widget: HEIC to JPG Converter
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Heic_To_Jpg extends TextCraft_Tool_Base {

    public function get_name(): string { return 'heic_to_jpg'; }
    public function get_title(): string { return 'HEIC to JPG Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['heic to jpg', 'convert heic', 'heic converter', 'heic to jpeg', 'apple image converter'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert Apple HEIC images to JPG format for universal compatibility. Adjustable quality with instant preview.
        </div>

        <?php $this->render_drop_zone('tc-h2j-drop', 'image/heic,.heic,.HEIC', 'Drag & drop HEIC images here or click to browse'); ?>
        <?php $this->render_file_row('tc-h2j-file'); ?>

        <div class="tc-input-group" style="margin-top:16px">
            <?php $this->render_range('tc-h2j-quality', 1, 100, 92, 'Quality', '%'); ?>
        </div>

        <?php $this->render_progress_bar('tc-h2j-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-h2j-convert', 'Convert to JPG', 'tc-h2j-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (HEIC)</span><span class="tc-stat-value" id="tc-h2j-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (JPG)</span><span class="tc-stat-value" id="tc-h2j-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-h2j-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-heic2j-result">
            <div class="tc-preview" id="tc-heic2j-preview">Upload a file to see preview</div>
        </div>
        <?php
    }
}
