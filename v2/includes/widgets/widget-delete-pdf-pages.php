<?php
/**
 * Widget: Delete PDF Pages
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Delete_Pdf_Pages extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    protected bool $premium = true;

    public function get_name(): string { return 'delete_pdf_pages'; }
    public function get_title(): string { return 'Delete PDF Pages'; }
    public function get_icon(): string { return 'eicon-trash'; }

    public function get_keywords(): array {
        return ['delete pdf pages', 'remove pdf pages', 'pdf page remover', 'edit pdf'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Remove unwanted pages from a PDF &mdash; select them by clicking thumbnails or typing page numbers, then download the result. Everything runs in your browser, your files are never uploaded.
        </div>

        <?php $this->render_drop_zone('tc-dp-drop', '.pdf,application/pdf', 'Drag & drop a PDF here or click to browse'); ?>
        <?php $this->render_file_row('tc-dp-file'); ?>

        <div class="tc-input-group" style="margin-top:18px">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif">Select pages by</label>
            <div class="tc-modes tc-modes--cards" data-group="dp-method">
                <button class="tc-btn tc-btn--ghost sel" data-val="click" type="button">
                    <span class="tc-card-title" style="font-family:'Space Grotesk',system-ui,sans-serif">Click thumbnails</span>
                    <span class="tc-card-desc">Tap page previews to toggle</span>
                </button>
                <button class="tc-btn tc-btn--ghost" data-val="numbers" type="button">
                    <span class="tc-card-title" style="font-family:'Space Grotesk',system-ui,sans-serif">Enter numbers</span>
                    <span class="tc-card-desc">Type ranges like 1-3, 5, 8</span>
                </button>
            </div>
            <p class="tc-lvl-hint" id="tc-dp-method-hint">
                Click thumbnails &mdash; tap each page preview to toggle its selection.
            </p>
        </div>

        <div class="tc-input-group" id="tc-dp-numbers-opts" style="display:none">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-dp-pages">Page numbers (e.g. 1-3, 5, 8)</label>
            <input type="text" class="tc-input" style="font-family:'Space Grotesk',system-ui,sans-serif" id="tc-dp-pages" placeholder="2-4, 6">
            <p class="tc-lvl-hint">Comma-separated page numbers and ranges to act on.</p>
        </div>

        <div id="tc-dp-grid-wrap" style="display:none">
            <div class="tc-input-group" style="margin-bottom:4px">
                <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif">Page Thumbnails</label>
            </div>
            <div class="tc-page-grid" id="tc-dp-grid"></div>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif">Action</label>
            <div class="tc-modes" data-group="dp-action">
                <button class="tc-btn sel" data-val="delete" type="button">Delete selected</button>
                <button class="tc-btn" data-val="keep" type="button">Keep only selected</button>
            </div>
            <p class="tc-lvl-hint" id="tc-dp-action-hint">
                Delete selected &mdash; removes the chosen pages and keeps the rest.
            </p>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-dp-optimize">
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text" style="font-family:'Space Grotesk',system-ui,sans-serif">
                    <b>Optimize output size</b>
                    <small>Re-encodes output streams for smaller files (strips some metadata).</small>
                </span>
            </label>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-dp-name">Output file name</label>
            <input type="text" class="tc-input" style="font-family:'Space Grotesk',system-ui,sans-serif" id="tc-dp-name" placeholder="cleaned-document">
            <p class="tc-lvl-hint">Leave empty to use your source file name.</p>
        </div>

        <?php $this->render_progress_bar('tc-dp-progress', 'Processing...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-dp-delete" type="button">Delete Selected Pages</button>
            <button class="tc-btn tc-btn--ghost" id="tc-dp-download" type="button" style="display:none">Download</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-dp-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Total Pages</span><span class="tc-stat-value" id="tc-dp-stat-total">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Selected</span><span class="tc-stat-value" id="tc-dp-stat-selected">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Remaining</span><span class="tc-stat-value" id="tc-dp-stat-remaining">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
