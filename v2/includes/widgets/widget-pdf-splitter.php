<?php
/**
 * Widget: PDF Splitter
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Pdf_Splitter extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    protected bool $premium = true;

    public function get_name(): string { return 'pdf_splitter'; }
    public function get_title(): string { return 'PDF Splitter'; }
    public function get_icon(): string { return 'eicon-editor-expand'; }

    public function get_keywords(): array {
        return ['pdf splitter', 'split pdf', 'extract pdf pages', 'pdf separator'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Split a PDF into multiple files &mdash; every N pages, a page range, or individual pages &mdash; and download them as a ZIP. Everything runs in your browser, your files are never uploaded.
        </div>

        <?php $this->render_drop_zone('tc-ps-drop', '.pdf,application/pdf', 'Drag & drop a PDF here or click to browse'); ?>
        <?php $this->render_file_row('tc-ps-file'); ?>

        <div class="tc-input-group" style="margin-top:18px">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif">Split Mode</label>
            <div class="tc-modes tc-modes--cards" data-group="ps-mode">
                <button class="tc-btn tc-btn--ghost sel" data-val="every" type="button">
                    <span class="tc-card-title" style="font-family:'Space Grotesk',system-ui,sans-serif">Every N Pages</span>
                    <span class="tc-card-desc">Group pages in fixed-size chunks</span>
                </button>
                <button class="tc-btn tc-btn--ghost" data-val="range" type="button">
                    <span class="tc-card-title" style="font-family:'Space Grotesk',system-ui,sans-serif">Page Range</span>
                    <span class="tc-card-desc">Extract specific pages you choose</span>
                </button>
                <button class="tc-btn tc-btn--ghost" data-val="individual" type="button">
                    <span class="tc-card-title" style="font-family:'Space Grotesk',system-ui,sans-serif">Individual Pages</span>
                    <span class="tc-card-desc">One PDF per page</span>
                </button>
            </div>
            <p class="tc-lvl-hint" id="tc-ps-mode-hint">
                Every N Pages &mdash; group pages in fixed-size chunks.
            </p>
        </div>

        <div class="tc-input-group" id="tc-ps-every-opts">
            <?php $this->render_range('tc-ps-every', 1, 50, 1, 'Split every', ' pages'); ?>
        </div>

        <div class="tc-input-group" id="tc-ps-range-opts" style="display:none">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif">Page Range (e.g. 1-5, 8, 10-12)</label>
            <input type="text" class="tc-input" style="font-family:'Space Grotesk',system-ui,sans-serif" id="tc-ps-range" placeholder="1-5, 8, 10-12">
        </div>

        <div class="tc-input-group">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif">Output File Naming</label>
            <div class="tc-modes" data-group="ps-name">
                <button class="tc-btn sel" data-val="pages" type="button">Pages</button>
                <button class="tc-btn" data-val="sequential" type="button">Sequential</button>
                <button class="tc-btn" data-val="original" type="button">Original name</button>
            </div>
            <p class="tc-lvl-hint" id="tc-ps-name-hint">
                Pages &mdash; names reflect the page ranges (e.g. pages-1-5.pdf).
            </p>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-ps-optimize">
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text" style="font-family:'Space Grotesk',system-ui,sans-serif">
                    <b>Optimize output size</b>
                    <small>Re-encodes output streams for smaller files (strips some metadata).</small>
                </span>
            </label>
        </div>

        <?php $this->render_progress_bar('tc-ps-progress', 'Splitting...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-ps-split" type="button">Split PDF</button>
            <button class="tc-btn tc-btn--ghost" id="tc-ps-download" type="button" style="display:none">Download ZIP</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-ps-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Total Pages</span><span class="tc-stat-value" id="tc-ps-stat-total">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Output Files</span><span class="tc-stat-value" id="tc-ps-stat-files">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Status</span><span class="tc-stat-value" id="tc-ps-stat-status">Ready</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
