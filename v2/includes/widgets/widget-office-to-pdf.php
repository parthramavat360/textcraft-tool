<?php
/**
 * Widget: Word / Excel / PowerPoint to PDF
 * Convert .docx, .xlsx/.xls/.csv, and .pptx files to PDF entirely in the browser.
 * Uses client-side renderers (docx-preview, SheetJS, pptx-preview) plus
 * html2pdf to produce a downloadable PDF. 100% client-side, no upload.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Office_To_Pdf extends TextCraft_Tool_Base {

    protected bool $show_preview = true;
    protected string $preview_orig_label = 'Original';
    protected string $preview_result_label = 'PDF Result';
    protected array $result_stats = [
        ['File type', 'tc-ofp-stat-type'],
        ['Pages', 'tc-ofp-stat-pages'],
        ['Output size', 'tc-ofp-stat-size'],
    ];

    public function get_name(): string { return 'office_to_pdf'; }
    public function get_title(): string { return 'Word/Excel/PPT to PDF'; }
    public function get_icon(): string { return 'eicon-file-download'; }

    public function get_keywords(): array {
        return ['word to pdf', 'excel to pdf', 'powerpoint to pdf', 'docx to pdf', 'xlsx to pdf', 'pptx to pdf', 'convert to pdf', 'office to pdf', 'ppt to pdf', 'xls to pdf'];
    }

    protected function render_tool_content( array $settings ): void {
        ?>
        <div class="tc-tool-desc">
            Convert Word (.docx), Excel (.xlsx, .xls, .csv), and PowerPoint (.pptx) files to PDF.
            Everything is rendered locally in your browser and exported as a single PDF download — no upload, fully private.
        </div>

        <!-- Source format -->
        <div class="tc-rsz-section">
            <h4 class="tc-rsz-heading">Document Type</h4>
            <div class="tc-ofp-fmts" id="tc-ofp-fmt-tabs">
                <button class="tc-ofp-fmt sel" type="button" data-fmt="word">
                    <b>Word</b>
                    <span>.docx document</span>
                </button>
                <button class="tc-ofp-fmt" type="button" data-fmt="excel">
                    <b>Excel</b>
                    <span>.xlsx &middot; .xls &middot; .csv</span>
                </button>
                <button class="tc-ofp-fmt" type="button" data-fmt="ppt">
                    <b>PowerPoint</b>
                    <span>.pptx slide deck</span>
                </button>
            </div>
        </div>

        <div class="tc-rsz-section">
            <p class="tc-ofp-outnote">Pick your file type so the right converter is used. Your document is rendered locally and exported as a single PDF.</p>
        </div>

        <?php $this->render_drop_zone( 'tc-ofp-drop', '.docx,.xlsx,.xls,.csv,.pptx', 'Drag & drop your Word, Excel, or PowerPoint file here' ); ?>
        <?php $this->render_file_row( 'tc-ofp-file' ); ?>

        <?php $this->render_progress_bar( 'tc-ofp-progress', 'Converting to PDF...' ); ?>

        <?php $this->render_actions( 'tc-ofp-convert', 'Convert to PDF', 'tc-ofp-download', 'Download PDF' ); ?>

        <?php
    }

    protected function render_result_content( array $settings ): void {
        // Result is shown in the Preview tabs (Original / PDF Result). No extra box needed.
        ?>
        <?php
    }
}
