<?php
/**
 * Widget: PDF to JPG Converter
 * Premium card-based DPI, quality slider, page range.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Pdf_To_Jpg extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'pdf_to_jpg'; }
    public function get_title(): string { return 'PDF to JPG Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['pdf to jpg', 'pdf to jpeg', 'convert pdf to jpg', 'pdf image extractor'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert each page of a PDF into a high-quality JPG image. Choose DPI, JPEG quality, and page range. Everything runs in your browser &mdash; your files are never uploaded.
        </div>

        <?php $this->render_drop_zone('tc-p2j-drop', '.pdf,application/pdf', 'Drag & drop a PDF here or click to browse'); ?>
        <?php $this->render_file_row('tc-p2j-file'); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Resolution (DPI)</h4>
                <div class="tc-rsz-mode-cards tc-p2j-dpi-cards">
                    <button class="tc-rsz-mode-card" type="button" data-val="72">
                        <span class="tc-rsz-mode-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="2"/><path d="M7 7h.01"/><path d="M17 7h.01"/><path d="M7 17h.01"/><path d="M17 17h.01"/></svg>
                        </span>
                        <span class="tc-rsz-mode-text">
                            <b>72 DPI</b>
                            <span>Screen &mdash; small file</span>
                        </span>
                    </button>
                    <button class="tc-rsz-mode-card sel" type="button" data-val="150">
                        <span class="tc-rsz-mode-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                        </span>
                        <span class="tc-rsz-mode-text">
                            <b>150 DPI</b>
                            <span>Standard &mdash; balanced</span>
                        </span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="300">
                        <span class="tc-rsz-mode-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                        </span>
                        <span class="tc-rsz-mode-text">
                            <b>300 DPI</b>
                            <span>Print &mdash; highest quality</span>
                        </span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">JPG Quality <span class="tc-rsz-quality-badge" id="tc-p2j-quality-val">92%</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">Low</span>
                    <input type="range" class="tc-rsz-slider" id="tc-p2j-quality" min="10" max="100" value="92">
                    <span class="tc-rsz-slider-max">Max</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Page Range</h4>
                <div class="tc-input-group">
                    <label class="tc-label">Pages</label>
                    <input type="text" class="tc-input" id="tc-p2j-range" placeholder="All pages (e.g. 1-5, 8, 11-13)" autocomplete="off">
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-p2j-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-p2j-convert', 'Convert to JPG', 'tc-p2j-download', 'Download ZIP'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Total Pages</span><span class="tc-stat-value" id="tc-p2j-stat-pages">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Exported</span><span class="tc-stat-value" id="tc-p2j-stat-done">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Output</span><span class="tc-stat-value" id="tc-p2j-stat-size">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
