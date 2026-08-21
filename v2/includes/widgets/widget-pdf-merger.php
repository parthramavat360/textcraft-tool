<?php
/**
 * Widget: PDF Merger
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Pdf_Merger extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'pdf_merger'; }
    public function get_title(): string { return 'PDF Merger'; }
    public function get_icon(): string { return 'eicon-file-download'; }

    public function get_keywords(): array {
        return ['pdf merger', 'merge pdf', 'combine pdf', 'join pdf'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Merge multiple PDF documents into a single file. Drag to reorder files before merging. Everything runs in your browser ÃƒÂ¢Ã¢â€šÂ¬Ã¢â‚¬Â your files are never uploaded.
        </div>

        <?php $this->render_drop_zone('tc-pm-drop', '.pdf,application/pdf', 'Drag & drop multiple PDFs here or click to browse'); ?>

        <div class="tc-file-list" id="tc-pm-list" style="display:none"></div>

        <?php $this->render_progress_bar('tc-pm-progress', 'Merging...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-pm-merge" type="button">Merge PDFs</button>
            <button class="tc-btn tc-btn--ghost" id="tc-pm-download" type="button" style="display:none">Download</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Files</span><span class="tc-stat-value" id="tc-pm-stat-count">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Total Size</span><span class="tc-stat-value" id="tc-pm-stat-size">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Merged Size</span><span class="tc-stat-value" id="tc-pm-stat-merged">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
