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

    protected bool $premium = true;

    public function get_name(): string { return 'rotate_pdf'; }
    public function get_title(): string { return 'Rotate PDF'; }
    public function get_icon(): string { return 'eicon-editor-rotate'; }

    public function get_keywords(): array {
        return ['rotate pdf', 'pdf rotation', 'turn pdf', 'rotate pdf pages'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Rotate PDF pages by 90&deg; or 180&deg; &mdash; apply to the whole document or just specific pages. Everything runs in your browser, your files are never uploaded.
        </div>

        <?php $this->render_drop_zone('tc-rp-drop', '.pdf,application/pdf', 'Drag & drop a PDF here or click to browse'); ?>
        <?php $this->render_file_row('tc-rp-file'); ?>

        <div class="tc-input-group" style="margin-top:18px">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif">Rotate by</label>
            <div class="tc-modes tc-modes--cards" data-group="rp-rotation">
                <button class="tc-btn tc-btn--ghost sel" data-val="90" type="button">
                    <span class="tc-card-title" style="font-family:'Space Grotesk',system-ui,sans-serif">90&deg; CW</span>
                    <span class="tc-card-desc">Clockwise &mdash; right</span>
                </button>
                <button class="tc-btn tc-btn--ghost" data-val="270" type="button">
                    <span class="tc-card-title" style="font-family:'Space Grotesk',system-ui,sans-serif">90&deg; CCW</span>
                    <span class="tc-card-desc">Counter-clockwise &mdash; left</span>
                </button>
                <button class="tc-btn tc-btn--ghost" data-val="180" type="button">
                    <span class="tc-card-title" style="font-family:'Space Grotesk',system-ui,sans-serif">180&deg;</span>
                    <span class="tc-card-desc">Upside down</span>
                </button>
            </div>
            <p class="tc-lvl-hint" id="tc-rp-rotation-hint">
                90&deg; CW &mdash; rotates every page a quarter turn clockwise.
            </p>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif">Apply to</label>
            <div class="tc-modes tc-modes--cards" data-group="rp-scope">
                <button class="tc-btn tc-btn--ghost sel" data-val="all" type="button">
                    <span class="tc-card-title" style="font-family:'Space Grotesk',system-ui,sans-serif">All pages</span>
                    <span class="tc-card-desc">Rotate the whole document</span>
                </button>
                <button class="tc-btn tc-btn--ghost" data-val="pages" type="button">
                    <span class="tc-card-title" style="font-family:'Space Grotesk',system-ui,sans-serif">Page numbers</span>
                    <span class="tc-card-desc">Rotate only the pages you choose</span>
                </button>
            </div>
            <p class="tc-lvl-hint" id="tc-rp-scope-hint">
                All pages &mdash; every page in the PDF gets rotated.
            </p>
        </div>

        <div class="tc-input-group" id="tc-rp-pages-opts" style="display:none">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-rp-pages">Page numbers (e.g. 1, 3-5)</label>
            <input type="text" class="tc-input" style="font-family:'Space Grotesk',system-ui,sans-serif" id="tc-rp-pages" placeholder="1, 3-5">
            <p class="tc-lvl-hint">Comma-separated page numbers and ranges to rotate.</p>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-rp-optimize">
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text" style="font-family:'Space Grotesk',system-ui,sans-serif">
                    <b>Optimize output size</b>
                    <small>Re-encodes output streams for smaller files (strips some metadata).</small>
                </span>
            </label>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-rp-name">Output file name</label>
            <input type="text" class="tc-input" style="font-family:'Space Grotesk',system-ui,sans-serif" id="tc-rp-name" placeholder="rotated-document">
            <p class="tc-lvl-hint">Leave empty to use your source file name.</p>
        </div>

        <?php $this->render_progress_bar('tc-rp-progress', 'Rotating...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-rp-rotate" type="button">Apply Rotation</button>
            <button class="tc-btn tc-btn--ghost" id="tc-rp-download" type="button" style="display:none">Download</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-rp-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Total Pages</span><span class="tc-stat-value" id="tc-rp-stat-total">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Rotated</span><span class="tc-stat-value" id="tc-rp-stat-rotated">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Status</span><span class="tc-stat-value" id="tc-rp-stat-status">Ready</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
