<?php
/**
 * Widget: JPG to PDF Converter
 * Premium card-based design with page size cards, toggles, and margins.
 *
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
            Convert JPG/JPEG images to PDF documents. Choose page size, margins, and orientation. Batch convert multiple images into one PDF. Everything runs in your browser &mdash; your files are never uploaded.
        </div>

        <?php $this->render_drop_zone('tc-j2pdf-drop', 'image/jpeg,.jpg,.jpeg', 'Drag & drop JPG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-j2pdf-file'); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Page Size</h4>
                <div class="tc-rsz-mode-cards tc-j2pdf-sizes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="a4">
                        <span class="tc-rsz-mode-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </span>
                        <span class="tc-rsz-mode-text">
                            <b>A4</b>
                            <span>210 &times; 297 mm</span>
                        </span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="letter">
                        <span class="tc-rsz-mode-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </span>
                        <span class="tc-rsz-mode-text">
                            <b>Letter</b>
                            <span>8.5 &times; 11 in</span>
                        </span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="legal">
                        <span class="tc-rsz-mode-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </span>
                        <span class="tc-rsz-mode-text">
                            <b>Legal</b>
                            <span>8.5 &times; 14 in</span>
                        </span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="auto">
                        <span class="tc-rsz-mode-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                        </span>
                        <span class="tc-rsz-mode-text">
                            <b>Auto</b>
                            <span>Fit to image</span>
                        </span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Margins <span class="tc-rsz-quality-badge" id="tc-j2pdf-margins-val">20 px</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">None</span>
                    <input type="range" class="tc-rsz-slider" id="tc-j2pdf-margins" min="0" max="80" value="20">
                    <span class="tc-rsz-slider-max">Wide</span>
                </div>
            </div>

            <div class="tc-rsz-toggles">
                <label class="tc-rsz-toggle">
                    <input type="checkbox" class="tc-rsz-toggle-input" id="tc-j2pdf-fit" checked>
                    <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                    <span class="tc-rsz-toggle-text">
                        <b>Fit image to page</b>
                        <span>Scale image to fit within margins</span>
                    </span>
                </label>
                <label class="tc-rsz-toggle">
                    <input type="checkbox" class="tc-rsz-toggle-input" id="tc-j2pdf-landscape">
                    <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                    <span class="tc-rsz-toggle-text">
                        <b>Landscape orientation</b>
                        <span>Rotate page layout 90&deg;</span>
                    </span>
                </label>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-j2pdf-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-j2pdf-convert', 'Convert to PDF', 'tc-j2pdf-download', 'Download PDF'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-j2pdf-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">PDF Size</span><span class="tc-stat-value" id="tc-j2pdf-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Pages</span><span class="tc-stat-value" id="tc-j2pdf-stat-pages">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
