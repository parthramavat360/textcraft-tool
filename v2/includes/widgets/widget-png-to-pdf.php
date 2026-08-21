<?php
/**
 * Widget: PNG to PDF Converter
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Png_To_Pdf extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'png_to_pdf'; }
    public function get_title(): string { return 'PNG to PDF Converter'; }
    public function get_icon(): string { return 'eicon-file-download'; }

    public function get_keywords(): array {
        return ['png to pdf', 'convert png to pdf', 'image to pdf', 'photo to pdf'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert PNG images to PDF documents. Choose page size and orientation for clean document formatting.
        </div>

        <?php $this->render_drop_zone('tc-p2pdf-drop', 'image/png,.png', 'Drag & drop PNG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-p2pdf-file'); ?>

        <div class="tc-input-group" style="margin-top:16px">
            <?php $this->render_select('tc-p2pdf-pagesize', [
                'a4'     => 'A4 (210 x 297 mm)',
                'letter' => 'Letter (8.5 x 11 in)',
                'auto'   => 'Auto (fit to image)',
            ], 'Page size'); ?>
        </div>

        <div class="tc-input-group">
            <?php $this->render_select('tc-p2pdf-orientation', [
                'portrait'  => 'Portrait',
                'landscape' => 'Landscape',
            ], 'Orientation'); ?>
        </div>

        <div class="tc-checkboxes">
            <?php $this->render_checkbox('tc-p2pdf-fit', 'Fit image to page', true); ?>
        </div>

        <?php $this->render_progress_bar('tc-p2pdf-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-p2pdf-convert', 'Convert to PDF', 'tc-p2pdf-download', 'Download PDF'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (PNG)</span><span class="tc-stat-value" id="tc-p2pdf-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (PDF)</span><span class="tc-stat-value" id="tc-p2pdf-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Pages</span><span class="tc-stat-value" id="tc-p2pdf-stat-pages">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
