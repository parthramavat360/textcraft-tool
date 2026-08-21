<?php
/**
 * Widget: PDF to PNG Converter
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
            Convert each page of a PDF to a high-quality PNG image. Choose your preferred DPI. Everything runs in your browser ÃƒÂ¢Ã¢â€šÂ¬Ã¢â‚¬Â your files are never uploaded.
        </div>

        <?php $this->render_drop_zone('tc-p2p-drop', '.pdf,application/pdf', 'Drag & drop a PDF here or click to browse'); ?>
        <?php $this->render_file_row('tc-p2p-file'); ?>

        <div class="tc-input-group" style="margin-top:16px">
            <?php $this->render_select('tc-p2p-dpi', [
                '72'  => '72 DPI (Screen)',
                '150' => '150 DPI (Standard)',
                '300' => '300 DPI (Print)',
            ], 'Select DPI'); ?>
        </div>

        <?php $this->render_progress_bar('tc-p2p-progress', 'Converting...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-p2p-convert" type="button">Convert to PNG</button>
            <button class="tc-btn tc-btn--ghost" id="tc-p2p-download" type="button" style="display:none">Download ZIP</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Pages</span><span class="tc-stat-value" id="tc-p2p-stat-pages">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted</span><span class="tc-stat-value" id="tc-p2p-stat-done">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Status</span><span class="tc-stat-value" id="tc-p2p-stat-status">Ready</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
