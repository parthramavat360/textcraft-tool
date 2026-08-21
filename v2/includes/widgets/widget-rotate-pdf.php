<?php
/**
 * Widget: Rotate PDF
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Rotate_Pdf extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'rotate_pdf'; }
    public function get_title(): string { return 'Rotate PDF'; }
    public function get_icon(): string { return 'eicon-editor-rotate'; }

    public function get_keywords(): array {
        return ['rotate pdf', 'pdf rotation', 'turn pdf', 'rotate pdf pages'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Rotate all pages in a PDF document. Choose 90Â° clockwise, 90Â° counter-clockwise, or 180Â° rotation. Everything runs in your browser â€” your files are never uploaded.
        </div>

        <?php $this->render_drop_zone('tc-rp-drop', '.pdf,application/pdf', 'Drag & drop a PDF here or click to browse'); ?>
        <?php $this->render_file_row('tc-rp-file'); ?>

        <div class="tc-input-group" style="margin-top:16px">
            <label class="tc-label">Rotation</label>
            <div class="tc-modes" data-group="rp-rotation">
                <button class="tc-btn tc-btn--ghost sel" data-val="90" type="button">90Â° CW</button>
                <button class="tc-btn tc-btn--ghost" data-val="270" type="button">90Â° CCW</button>
                <button class="tc-btn tc-btn--ghost" data-val="180" type="button">180Â°</button>
            </div>
        </div>

        <?php $this->render_progress_bar('tc-rp-progress', 'Rotating...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-rp-rotate" type="button">Apply Rotation</button>
            <button class="tc-btn tc-btn--ghost" id="tc-rp-download" type="button" style="display:none">Download</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Total Pages</span><span class="tc-stat-value" id="tc-rp-stat-total">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Rotated</span><span class="tc-stat-value" id="tc-rp-stat-rotated">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Status</span><span class="tc-stat-value" id="tc-rp-stat-status">Ready</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-rp-result">
            <div class="tc-preview" id="tc-rp-preview">Upload a file to see preview</div>
        </div>
        <?php
    }
}
