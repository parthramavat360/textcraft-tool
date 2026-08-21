<?php
/**
 * Widget: JPG Compressor
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Jpg_Compressor extends TextCraft_Tool_Base {

    public function get_name(): string { return 'jpg_compressor'; }
    public function get_title(): string { return 'JPG Compressor'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['jpg compressor', 'jpeg compressor', 'reduce jpg size', 'compress jpeg'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Compress JPG/JPEG images directly in your browser. Supports batch processing with ZIP download. Quality adjustable from 20-95%.
        </div>

        <?php $this->render_drop_zone('tc-jpg-drop', 'image/jpeg,.jpg,.jpeg', 'Drag & drop JPG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-jpg-file'); ?>

        <div class="tc-input-group" style="margin-top:16px">
            <label class="tc-label">Quality: <strong id="tc-jpg-quality-val">92</strong>%</label>
            <input type="range" class="tc-range" id="tc-jpg-quality" min="20" max="95" value="92">
        </div>

        <?php $this->render_progress_bar('tc-jpg-progress', 'Compressing...'); ?>

        <?php $this->render_actions('tc-jpg-compress', 'Compress', 'tc-jpg-download', 'Download ZIP'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-jpg-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Compressed</span><span class="tc-stat-value" id="tc-jpg-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-jpg-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-jpg-result">
            <div class="tc-preview" id="tc-jpg-preview">Upload a file to see preview</div>
        </div>
        <?php
    }
}