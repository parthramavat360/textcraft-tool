<?php
/**
 * Widget: PDF to PNG Converter
 * Premium redesign — DPI cards, background color, page range, output name, clear all.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Pdf_To_Png extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    protected bool $premium = true;

    public function get_name(): string { return 'pdf_to_png'; }
    public function get_title(): string { return 'PDF to PNG Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['pdf to png', 'convert pdf to png', 'pdf image converter', 'pdf to png online'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert each page of a PDF to a crisp, lossless PNG image. Choose resolution, background color, and page range, then download all pages as a ZIP. Everything runs in your browser &mdash; your files are never uploaded.
        </div>

        <?php $this->render_drop_zone('tc-p2p-drop', '.pdf,application/pdf', 'Drag & drop a PDF here or click to browse'); ?>
        <?php $this->render_file_row('tc-p2p-file'); ?>

        <div class="tc-input-group" style="margin-top:18px">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif">Resolution (DPI)</label>
            <div class="tc-modes tc-modes--cards" data-group="p2p-dpi">
                <button class="tc-btn tc-btn--ghost" data-val="72" type="button">
                    <span class="tc-card-title" style="font-family:'Space Grotesk',system-ui,sans-serif">72 DPI</span>
                    <span class="tc-card-desc">Screen &mdash; small file</span>
                </button>
                <button class="tc-btn tc-btn--ghost sel" data-val="150" type="button">
                    <span class="tc-card-title" style="font-family:'Space Grotesk',system-ui,sans-serif">150 DPI</span>
                    <span class="tc-card-desc">Standard &mdash; balanced</span>
                </button>
                <button class="tc-btn tc-btn--ghost" data-val="300" type="button">
                    <span class="tc-card-title" style="font-family:'Space Grotesk',system-ui,sans-serif">300 DPI</span>
                    <span class="tc-card-desc">Print &mdash; highest quality</span>
                </button>
            </div>
            <p class="tc-lvl-hint" id="tc-p2p-dpi-hint">
                150 DPI &mdash; a good balance of sharpness and file size.
            </p>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif">Background Color <span id="tc-p2p-bg-val" style="font-family:'Space Grotesk',system-ui,sans-serif">#FFFFFF</span></label>
            <div class="tc-range-wrap">
                <input type="color" class="tc-color" id="tc-p2p-bgcolor" value="#ffffff" style="width:52px;height:40px;border:1px solid var(--line);border-radius:8px;background:transparent;cursor:pointer">
                <p class="tc-lvl-hint">Used behind any transparent areas of the page.</p>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif">Page Range</label>
            <div class="tc-modes" data-group="p2p-range">
                <button class="tc-btn sel" data-val="all" type="button">All pages</button>
                <button class="tc-btn" data-val="pages" type="button">Selected pages</button>
            </div>
            <p class="tc-lvl-hint" id="tc-p2p-range-hint">
                All pages &mdash; every page in the PDF is exported.
            </p>
        </div>

        <div class="tc-input-group" id="tc-p2p-page-opts" style="display:none">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-p2p-pages">Page numbers (e.g. 1-3, 5, 8)</label>
            <input type="text" class="tc-input" style="font-family:'Space Grotesk',system-ui,sans-serif" id="tc-p2p-pages" placeholder="1-5, 8, 11-13">
            <p class="tc-lvl-hint">Comma-separated page numbers and ranges to convert.</p>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-p2p-name">Output file name</label>
            <input type="text" class="tc-input" style="font-family:'Space Grotesk',system-ui,sans-serif" id="tc-p2p-name" placeholder="my-pages">
            <p class="tc-lvl-hint">Base name for the downloaded ZIP (leave empty to use your source file name).</p>
        </div>

        <?php $this->render_progress_bar('tc-p2p-progress', 'Converting...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-p2p-convert" type="button">Convert to PNG</button>
            <button class="tc-btn tc-btn--ghost" id="tc-p2p-download" type="button" style="display:none">Download ZIP</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-p2p-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Total Pages</span><span class="tc-stat-value" id="tc-p2p-stat-pages">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Exported</span><span class="tc-stat-value" id="tc-p2p-stat-done">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Output</span><span class="tc-stat-value" id="tc-p2p-stat-size">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
