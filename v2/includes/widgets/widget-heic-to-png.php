<?php
/**
 * Widget: HEIC to PNG Converter
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Heic_To_Png extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'heic_to_png'; }
    public function get_title(): string { return 'HEIC to PNG Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['heic to png', 'convert heic to png', 'heic png converter', 'apple image converter'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert Apple HEIC images to PNG format with full transparency support. Lossless conversion preserves image quality.
        </div>

        <?php $this->render_drop_zone('tc-h2p-drop', 'image/heic,.heic,.HEIC', 'Drag & drop HEIC images here or click to browse'); ?>
        <?php $this->render_file_row('tc-h2p-file'); ?>

        <?php $this->render_progress_bar('tc-h2p-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-h2p-convert', 'Convert to PNG', 'tc-h2p-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (HEIC)</span><span class="tc-stat-value" id="tc-h2p-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (PNG)</span><span class="tc-stat-value" id="tc-h2p-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-h2p-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-heic2p-result">
            <div class="tc-preview" id="tc-heic2p-preview">Upload a file to see preview</div>
        </div>
        <?php
    }
}
