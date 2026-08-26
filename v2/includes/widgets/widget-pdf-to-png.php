<?php
/**
 * Widget: PDF to PNG Converter
 * Premium card-based DPI, background color, page range.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Pdf_To_Png extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'pdf_to_png'; }
    public function get_title(): string { return 'PDF to PNG Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['pdf to png', 'convert pdf to png', 'pdf image converter', 'pdf to png online'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert each page of a PDF to a crisp, lossless PNG image. Choose your DPI, background color, and page range. Everything runs in your browser &mdash; your files are never uploaded.
        </div>

        <?php $this->render_drop_zone('tc-p2p-drop', '.pdf,application/pdf', 'Drag & drop a PDF here or click to browse'); ?>
        <?php $this->render_file_row('tc-p2p-file'); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Resolution (DPI)</h4>
                <div class="tc-rsz-mode-cards tc-p2p-dpi-cards">
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
                <h4 class="tc-rsz-heading">Background Color <span class="tc-rsz-quality-badge" id="tc-p2p-bg-val">#FFFFFF</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <input type="color" class="tc-color" id="tc-p2p-bgcolor" value="#ffffff">
                    <span class="tc-rsz-slider-min" style="font-size:12px;opacity:0.6">Used behind transparent areas</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Page Range</h4>
                <div class="tc-input-group">
                    <label class="tc-label">Pages</label>
                    <input type="text" class="tc-input" id="tc-p2p-range" placeholder="All pages (e.g. 1-5, 8, 11-13)" autocomplete="off">
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-p2p-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-p2p-convert', 'Convert to PNG', 'tc-p2p-download', 'Download ZIP'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Total Pages</span><span class="tc-stat-value" id="tc-p2p-stat-pages">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Exported</span><span class="tc-stat-value" id="tc-p2p-stat-done">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Output</span><span class="tc-stat-value" id="tc-p2p-stat-size">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
