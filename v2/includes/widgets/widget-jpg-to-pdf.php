<?php
/**
 * Widget: JPG to PDF Converter
 * Premium redesign — page size cards, margins, orientation, fit/optimize
 * switches, output file name, clear all.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Jpg_To_Pdf extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    protected bool $premium = true;

    public function get_name(): string { return 'jpg_to_pdf'; }
    public function get_title(): string { return 'JPG to PDF Converter'; }
    public function get_icon(): string { return 'eicon-file-download'; }

    public function get_keywords(): array {
        return ['jpg to pdf', 'convert jpg to pdf', 'jpeg to pdf', 'image to pdf', 'photo to pdf'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert JPG/JPEG images to a PDF document. Pick a page size, margins and orientation, then batch all your images into one PDF. Everything runs in your browser &mdash; your files are never uploaded.
        </div>

        <?php $this->render_drop_zone('tc-j2pdf-drop', 'image/jpeg,.jpg,.jpeg', 'Drag & drop JPG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-j2pdf-file'); ?>

        <div class="tc-input-group" style="margin-top:18px">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif">Page Size</label>
            <div class="tc-modes tc-modes--cards" data-group="j2pdf-size">
                <button class="tc-btn tc-btn--ghost sel" data-val="a4" type="button">
                    <span class="tc-card-title">A4</span>
                    <span class="tc-card-desc">210 &times; 297 mm</span>
                </button>
                <button class="tc-btn tc-btn--ghost" data-val="letter" type="button">
                    <span class="tc-card-title">Letter</span>
                    <span class="tc-card-desc">8.5 &times; 11 in</span>
                </button>
                <button class="tc-btn tc-btn--ghost" data-val="legal" type="button">
                    <span class="tc-card-title">Legal</span>
                    <span class="tc-card-desc">8.5 &times; 14 in</span>
                </button>
                <button class="tc-btn tc-btn--ghost" data-val="auto" type="button">
                    <span class="tc-card-title">Auto</span>
                    <span class="tc-card-desc">Fit to image size</span>
                </button>
            </div>
            <p class="tc-lvl-hint" id="tc-j2pdf-size-hint">
                A4 &mdash; standard international page size, 210 &times; 297 mm.
            </p>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif">Orientation</label>
            <div class="tc-modes" data-group="j2pdf-orient">
                <button class="tc-btn sel" data-val="portrait" type="button">Portrait</button>
                <button class="tc-btn" data-val="landscape" type="button">Landscape</button>
            </div>
            <p class="tc-lvl-hint" id="tc-j2pdf-orient-hint">
                Portrait &mdash; pages taller than they are wide.
            </p>
        </div>

        <div class="tc-input-group" id="tc-j2pdf-margins-wrap">
            <div class="tc-range-wrap">
                <label class="tc-range-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-j2pdf-margins">
                    Margins: <span id="tc-j2pdf-margins-val">20 px</span>
                </label>
                <input type="range" class="tc-range" id="tc-j2pdf-margins" min="0" max="80" value="20">
                <p class="tc-lvl-hint">Space reserved around each image on its page, from 0 to 80 px.</p>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-j2pdf-fit" checked>
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text">
                    <b>Fit image to page</b>
                    <small>Scale each image to fit within the page margins.</small>
                </span>
            </label>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-j2pdf-optimize">
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text">
                    <b>Optimize output size</b>
                    <small>Re-encodes output streams for smaller files (strips some metadata).</small>
                </span>
            </label>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-j2pdf-name">Output file name</label>
            <input type="text" class="tc-input" style="font-family:'Space Grotesk',system-ui,sans-serif" id="tc-j2pdf-name" placeholder="my-images">
            <p class="tc-lvl-hint">Leave empty to use the source image name (single) or "converted" (multiple).</p>
        </div>

        <?php $this->render_progress_bar('tc-j2pdf-progress', 'Converting...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-j2pdf-convert" type="button">Convert to PDF</button>
            <button class="tc-btn tc-btn--ghost" id="tc-j2pdf-download" type="button" style="display:none">Download</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-j2pdf-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Images</span><span class="tc-stat-value" id="tc-j2pdf-stat-count">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-j2pdf-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">PDF Size</span><span class="tc-stat-value" id="tc-j2pdf-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Pages</span><span class="tc-stat-value" id="tc-j2pdf-stat-pages">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
