<?php
/**
 * Widget: Delete PDF Pages
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Delete_Pdf_Pages extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'delete_pdf_pages'; }
    public function get_title(): string { return 'Delete PDF Pages'; }
    public function get_icon(): string { return 'eicon-trash'; }

    public function get_keywords(): array {
        return ['delete pdf pages', 'remove pdf pages', 'pdf page remover', 'edit pdf'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Remove unwanted pages from a PDF document. Click on page thumbnails to select them for deletion, then download the remaining pages. Everything runs in your browser ÃƒÂ¢Ã¢â€šÂ¬Ã¢â‚¬Â your files are never uploaded.
        </div>

        <?php $this->render_drop_zone('tc-dp-drop', '.pdf,application/pdf', 'Drag & drop a PDF here or click to browse'); ?>
        <?php $this->render_file_row('tc-dp-file'); ?>

        <div class="tc-page-grid" id="tc-dp-grid" style="display:none"></div>

        <?php $this->render_progress_bar('tc-dp-progress', 'Processing...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-dp-delete" type="button">Delete Selected Pages</button>
            <button class="tc-btn tc-btn--ghost" id="tc-dp-download" type="button" style="display:none">Download</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Total Pages</span><span class="tc-stat-value" id="tc-dp-stat-total">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Selected</span><span class="tc-stat-value" id="tc-dp-stat-selected">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Remaining</span><span class="tc-stat-value" id="tc-dp-stat-remaining">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
