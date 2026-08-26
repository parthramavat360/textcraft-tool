<?php
/**
 * Widget: PNG to PDF Converter
 * Premium card-based design with page size cards, toggles, and margins.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Png_To_Pdf extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'png_to_pdf'; }
    public function get_title(): string { return 'PNG to PDF Converter'; }
    public function get_icon(): string { return 'eicon-file-download'; }

    public function get_keywords(): array {
        return ['png to pdf', 'convert png to pdf', 'image to pdf', 'photo to pdf'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert PNG images to PDF documents. Choose page size, margins, and orientation. Batch convert multiple images into one PDF. Everything runs in your browser &mdash; your files are never uploaded.
        </div>

        <?php $this->render_drop_zone('tc-p2pdf-drop', 'image/png,.png', 'Drag & drop PNG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-p2pdf-file'); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Page Size</h4>
                <div class="tc-rsz-mode-cards tc-p2pdf-sizes">
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
                <h4 class="tc-rsz-heading">Margins <span class="tc-rsz-quality-badge" id="tc-p2pdf-margins-val">20 px</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">None</span>
                    <input type="range" class="tc-rsz-slider" id="tc-p2pdf-margins" min="0" max="80" value="20">
                    <span class="tc-rsz-slider-max">Wide</span>
                </div>
            </div>

            <div class="tc-rsz-toggles">
                <label class="tc-rsz-toggle">
                    <input type="checkbox" class="tc-rsz-toggle-input" id="tc-p2pdf-fit" checked>
                    <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                    <span class="tc-rsz-toggle-text">
                        <b>Fit image to page</b>
                        <span>Scale image to fit within margins</span>
                    </span>
                </label>
                <label class="tc-rsz-toggle">
                    <input type="checkbox" class="tc-rsz-toggle-input" id="tc-p2pdf-landscape">
                    <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                    <span class="tc-rsz-toggle-text">
                        <b>Landscape orientation</b>
                        <span>Rotate page layout 90&deg;</span>
                    </span>
                </label>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-p2pdf-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-p2pdf-convert', 'Convert to PDF', 'tc-p2pdf-download', 'Download PDF'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-p2pdf-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">PDF Size</span><span class="tc-stat-value" id="tc-p2pdf-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Pages</span><span class="tc-stat-value" id="tc-p2pdf-stat-pages">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    /**
     * Override result panel with PNG→PDF specific labels.
     */
    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Generated PDF</h3>
                    <span id="tc-status-chip">Idle</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-stats">
                        <div><span>Original</span><b id="tc-stat-orig">&mdash;</b></div>
                        <div><span>PDF Size</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Pages</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original PNG</button>
                            <button data-tab="result">Generated PDF</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">Original PNG will appear here</div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">Generated PDF will appear here</div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
