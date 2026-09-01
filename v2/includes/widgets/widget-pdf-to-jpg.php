<?php
/**
 * Widget: PDF to JPG Converter
 * Premium redesign â€” DPI cards, quality slider, page range, output name, clear all.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Pdf_To_Jpg extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    protected bool $premium = true;

    public function get_name(): string { return 'pdf_to_jpg'; }
    public function get_title(): string { return 'PDF to JPG Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['pdf to jpg', 'pdf to jpeg', 'convert pdf to jpg', 'pdf image extractor'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert each page of a PDF into a high-quality JPG image. Choose resolution, JPEG quality, and page range, then download all pages as a ZIP. Everything runs in your browser &mdash; your files are never uploaded.
        </div>

        <?php $this->render_drop_zone('tc-p2j-drop', '.pdf,application/pdf', 'Drag & drop a PDF here or click to browse'); ?>
        <?php $this->render_file_row('tc-p2j-file'); ?>

        <div class="tc-input-group" >
            <label class="tc-label" >Resolution (DPI)</label>
            <div class="tc-modes tc-modes--cards" data-group="p2j-dpi">
                <button class="tc-btn tc-btn--ghost" data-val="72" type="button">
                    <span class="tc-card-title" >72 DPI</span>
                    <span class="tc-card-desc">Screen &mdash; small file</span>
                </button>
                <button class="tc-btn tc-btn--ghost sel" data-val="150" type="button">
                    <span class="tc-card-title" >150 DPI</span>
                    <span class="tc-card-desc">Standard &mdash; balanced</span>
                </button>
                <button class="tc-btn tc-btn--ghost" data-val="300" type="button">
                    <span class="tc-card-title" >300 DPI</span>
                    <span class="tc-card-desc">Print &mdash; highest quality</span>
                </button>
            </div>
            <p class="tc-lvl-hint" id="tc-p2j-dpi-hint">
                150 DPI &mdash; a good balance of sharpness and file size.
            </p>
        </div>

        <div class="tc-input-group" id="tc-p2j-quality-wrap">
            <div class="tc-range-wrap">
                <label class="tc-range-label"  for="tc-p2j-quality">
                    JPG Quality: <span id="tc-p2j-quality-val">92%</span>
                </label>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min" >10</span>
                    <input type="range" class="tc-range" id="tc-p2j-quality" min="10" max="100" value="92" >
                    <span class="tc-rsz-slider-max" >100</span>
                </div>
                <p class="tc-lvl-hint">Higher quality keeps images crisp but creates larger files.</p>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" >Page Range</label>
            <div class="tc-modes" data-group="p2j-range">
                <button class="tc-btn sel" data-val="all" type="button">All pages</button>
                <button class="tc-btn" data-val="pages" type="button">Selected pages</button>
            </div>
            <p class="tc-lvl-hint" id="tc-p2j-range-hint">
                All pages &mdash; every page in the PDF is exported.
            </p>
        </div>

        <div class="tc-input-group" id="tc-p2j-page-opts" style="display:none">
            <label class="tc-label"  for="tc-p2j-pages">Page numbers (e.g. 1-3, 5, 8)</label>
            <input type="text" class="tc-input"  id="tc-p2j-pages" placeholder="1-5, 8, 11-13">
            <p class="tc-lvl-hint">Comma-separated page numbers and ranges to convert.</p>
        </div>

        <div class="tc-input-group">
            <label class="tc-label"  for="tc-p2j-name">Output file name</label>
            <input type="text" class="tc-input"  id="tc-p2j-name" placeholder="my-pages">
            <p class="tc-lvl-hint">Base name for the downloaded ZIP (leave empty to use your source file name).</p>
        </div>

        <?php $this->render_progress_bar('tc-p2j-progress', 'Converting...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-p2j-convert" type="button">Convert to JPG</button>
            <button class="tc-btn tc-btn--ghost" id="tc-p2j-download" type="button" style="display:none">Download ZIP</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-p2j-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Total Pages</span><span class="tc-stat-value" id="tc-p2j-stat-pages">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Exported</span><span class="tc-stat-value" id="tc-p2j-stat-done">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Output</span><span class="tc-stat-value" id="tc-p2j-stat-size">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
