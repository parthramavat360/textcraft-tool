<?php
/**
 * Widget: PDF Splitter
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Pdf_Splitter extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'pdf_splitter'; }
    public function get_title(): string { return 'PDF Splitter'; }
    public function get_icon(): string { return 'eicon-editor-expand'; }

    public function get_keywords(): array {
        return ['pdf splitter', 'split pdf', 'extract pdf pages', 'pdf separator'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Split a PDF into multiple files. Choose to split every N pages, extract a page range, or split into individual pages. Download as a ZIP archive. Everything runs in your browser ÃƒÂ¢Ã¢â€šÂ¬Ã¢â‚¬Â your files are never uploaded.
        </div>

        <?php $this->render_drop_zone('tc-ps-drop', '.pdf,application/pdf', 'Drag & drop a PDF here or click to browse'); ?>
        <?php $this->render_file_row('tc-ps-file'); ?>

        <div class="tc-input-group" style="margin-top:16px">
            <label class="tc-label">Split Mode</label>
            <div class="tc-modes" data-group="ps-mode">
                <button class="tc-btn tc-btn--ghost sel" data-val="every" type="button">Every N Pages</button>
                <button class="tc-btn tc-btn--ghost" data-val="range" type="button">Page Range</button>
                <button class="tc-btn tc-btn--ghost" data-val="individual" type="button">Individual Pages</button>
            </div>
        </div>

        <div class="tc-input-group" id="tc-ps-every-opts">
            <?php $this->render_range('tc-ps-every', 1, 50, 1, 'Split every', ' pages'); ?>
        </div>

        <div class="tc-input-group" id="tc-ps-range-opts" style="display:none">
            <label class="tc-label">Page Range (e.g. 1-5, 8, 10-12)</label>
            <input type="text" class="tc-input" id="tc-ps-range" placeholder="1-5, 8, 10-12">
        </div>

        <?php $this->render_progress_bar('tc-ps-progress', 'Splitting...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-ps-split" type="button">Split PDF</button>
            <button class="tc-btn tc-btn--ghost" id="tc-ps-download" type="button" style="display:none">Download ZIP</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Total Pages</span><span class="tc-stat-value" id="tc-ps-stat-total">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Output Files</span><span class="tc-stat-value" id="tc-ps-stat-files">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Status</span><span class="tc-stat-value" id="tc-ps-stat-status">Ready</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
