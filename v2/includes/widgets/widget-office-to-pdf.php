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

    protected bool $premium = true;

    public function get_name(): string { return 'office_to_pdf'; }
    public function get_title(): string { return 'Word/Excel/PPT to PDF'; }
    public function get_icon(): string { return 'eicon-file-download'; }

    public function get_keywords(): array {
        return ['word to pdf', 'excel to pdf', 'powerpoint to pdf', 'docx to pdf', 'xlsx to pdf', 'pptx to pdf', 'convert to pdf', 'office to pdf', 'ppt to pdf', 'xls to pdf'];
    }

    protected function render_tool_content( array $settings ): void {
        ?>
        <div class="tc-tool-desc">
            Convert Word (.docx), Excel (.xlsx, .xls, .csv), and PowerPoint (.pptx) files to PDF &mdash; rendered locally in your browser and exported as a single PDF. Fully private, nothing is ever uploaded.
        </div>

        <div class="tc-input-group" >
            <label class="tc-label" >Document Type</label>
            <div class="tc-modes tc-modes--cards" data-group="ofp-fmt">
                <button class="tc-btn tc-btn--ghost sel" data-val="word" type="button">
                    <span class="tc-card-title" >Word</span>
                    <span class="tc-card-desc">.docx document</span>
                </button>
                <button class="tc-btn tc-btn--ghost" data-val="excel" type="button">
                    <span class="tc-card-title" >Excel</span>
                    <span class="tc-card-desc">.xlsx &middot; .xls &middot; .csv</span>
                </button>
                <button class="tc-btn tc-btn--ghost" data-val="ppt" type="button">
                    <span class="tc-card-title" >PowerPoint</span>
                    <span class="tc-card-desc">.pptx slide deck</span>
                </button>
            </div>
            <p class="tc-lvl-hint" id="tc-ofp-fmt-hint">
                Pick your file type so the right converter is used.
            </p>
        </div>

        <?php $this->render_drop_zone( 'tc-ofp-drop', '.docx,.xlsx,.xls,.csv,.pptx', 'Drag & drop your Word, Excel, or PowerPoint file here' ); ?>
        <?php $this->render_file_row( 'tc-ofp-file' ); ?>

        <div class="tc-input-group">
            <label class="tc-label" >Page Size</label>
            <div class="tc-modes" data-group="ofp-size">
                <button class="tc-btn sel" data-val="auto" type="button">Automatic</button>
                <button class="tc-btn" data-val="a4" type="button">A4</button>
                <button class="tc-btn" data-val="letter" type="button">Letter</button>
            </div>
            <p class="tc-lvl-hint" id="tc-ofp-size-hint">
                Automatic &mdash; A4 for most documents, larger pages for wide slides.
            </p>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" >Page Orientation</label>
            <div class="tc-modes" data-group="ofp-orient">
                <button class="tc-btn sel" data-val="portrait" type="button">Portrait</button>
                <button class="tc-btn" data-val="landscape" type="button">Landscape</button>
                <button class="tc-btn" data-val="auto" type="button">Automatic</button>
            </div>
            <p class="tc-lvl-hint" id="tc-ofp-orient-hint">
                Portrait &mdash; the default for letters and documents.
            </p>
        </div>

        <div class="tc-input-group">
            <div class="tc-range-wrap">
                <label class="tc-range-label"  for="tc-ofp-quality">
                    Render Quality: <span id="tc-ofp-quality-val">High</span>
                </label>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min" >1</span>
                    <input type="range" class="tc-range" id="tc-ofp-quality" min="1" max="3" step="1" value="2" >
                    <span class="tc-rsz-slider-max" >3</span>
                </div>
                <p class="tc-lvl-hint">Higher quality produces crisper text and images but larger PDF files.</p>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-label"  for="tc-ofp-name">Output file name</label>
            <input type="text" class="tc-input"  id="tc-ofp-name" placeholder="my-converted-document">
            <p class="tc-lvl-hint">Leave empty to use your source file name.</p>
        </div>

        <?php $this->render_progress_bar( 'tc-ofp-progress', 'Converting to PDF...' ); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-ofp-convert" type="button">Convert to PDF</button>
            <button class="tc-btn tc-btn--ghost" id="tc-ofp-download" type="button" disabled>Download PDF</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-ofp-clear" type="button">Clear all</button>
        </div>

        <?php
    }

    protected function render_result_content( array $settings ): void {
        // Result is shown in the Preview tabs (Original / PDF Result). No extra box needed.
        ?>
        <?php
    }
}
