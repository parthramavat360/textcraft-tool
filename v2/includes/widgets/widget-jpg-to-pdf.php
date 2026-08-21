<?php
/**
 * Widget: JPG to PDF Converter
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Jpg_To_Pdf extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'jpg_to_pdf'; }
    public function get_title(): string { return 'JPG to PDF Converter'; }
    public function get_icon(): string { return 'eicon-file-download'; }

    public function get_keywords(): array {
        return ['jpg to pdf', 'convert jpg to pdf', 'jpeg to pdf', 'image to pdf', 'photo to pdf'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert JPG/JPEG images to PDF documents. Choose page size and margins for perfect document formatting.
        </div>

        <?php $this->render_drop_zone('tc-j2pdf-drop', 'image/jpeg,.jpg,.jpeg', 'Drag & drop JPG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-j2pdf-file'); ?>

        <div class="tc-input-group" style="margin-top:16px">
            <?php $this->render_select('tc-j2pdf-pagesize', [
                'a4'     => 'A4 (210 x 297 mm)',
                'letter' => 'Letter (8.5 x 11 in)',
                'auto'   => 'Auto (fit to image)',
            ], 'Page size'); ?>
        </div>

        <div class="tc-input-group">
            <?php $this->render_range('tc-j2pdf-margins', 0, 100, 20, 'Margins', 'px'); ?>
        </div>

        <div class="tc-checkboxes">
            <?php $this->render_checkbox('tc-j2pdf-fit', 'Fit image to page', true); ?>
            <?php $this->render_checkbox('tc-j2pdf-landscape', 'Landscape orientation', false); ?>
        </div>

        <?php $this->render_progress_bar('tc-j2pdf-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-j2pdf-convert', 'Convert to PDF', 'tc-j2pdf-download', 'Download PDF'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (JPG)</span><span class="tc-stat-value" id="tc-j2pdf-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (PDF)</span><span class="tc-stat-value" id="tc-j2pdf-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Pages</span><span class="tc-stat-value" id="tc-j2pdf-stat-pages">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
