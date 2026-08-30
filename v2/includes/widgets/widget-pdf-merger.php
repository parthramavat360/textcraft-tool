<?php
/**
 * Widget: PDF Merger
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Pdf_Merger extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    protected bool $premium = true;

    public function get_name(): string { return 'pdf_merger'; }
    public function get_title(): string { return 'PDF Merger'; }
    public function get_icon(): string { return 'eicon-file-download'; }

    public function get_keywords(): array {
        return ['pdf merger', 'merge pdf', 'combine pdf', 'join pdf'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Combine multiple PDF documents into one file &mdash; append or interleave pages, normalize page size &amp; orientation, and download the merged result. Everything runs in your browser, your files are never uploaded.
        </div>

        <?php $this->render_drop_zone('tc-pm-drop', '.pdf,application/pdf', 'Drag & drop multiple PDFs here or click to browse'); ?>

        <div class="tc-file-list" id="tc-pm-list" style="display:none"></div>

        <div class="tc-input-group" style="margin-top:18px">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif">Merge Mode</label>
            <div class="tc-modes tc-modes--cards" data-group="pm-mode">
                <button class="tc-btn tc-btn--ghost sel" data-val="append" type="button">
                    <span class="tc-card-title" style="font-family:'Space Grotesk',system-ui,sans-serif">Append</span>
                    <span class="tc-card-desc">All pages of each file, one after another</span>
                </button>
                <button class="tc-btn tc-btn--ghost" data-val="interleave" type="button">
                    <span class="tc-card-title" style="font-family:'Space Grotesk',system-ui,sans-serif">Interleave</span>
                    <span class="tc-card-desc">Alternate pages across files</span>
                </button>
            </div>
            <p class="tc-lvl-hint" id="tc-pm-mode-hint">
                Append &mdash; all pages of each file, one after another.
            </p>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif">Page Size</label>
            <div class="tc-modes" data-group="pm-size">
                <button class="tc-btn sel" data-val="auto" type="button">Keep original</button>
                <button class="tc-btn" data-val="a4" type="button">A4</button>
                <button class="tc-btn" data-val="letter" type="button">Letter</button>
            </div>
            <p class="tc-lvl-hint" id="tc-pm-size-hint">
                Keep original &mdash; every page keeps its current dimensions.
            </p>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif">Orientation</label>
            <div class="tc-modes" data-group="pm-orient">
                <button class="tc-btn sel" data-val="keep" type="button">Keep</button>
                <button class="tc-btn" data-val="portrait" type="button">Portrait</button>
                <button class="tc-btn" data-val="landscape" type="button">Landscape</button>
            </div>
            <p class="tc-lvl-hint" id="tc-pm-orient-hint">
                Keep &mdash; pages stay in their original orientation.
            </p>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-pm-optimize">
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text" style="font-family:'Space Grotesk',system-ui,sans-serif">
                    <b>Optimize output size</b>
                    <small>Re-encodes output streams for smaller files (strips some metadata).</small>
                </span>
            </label>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-pm-separator">
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text" style="font-family:'Space Grotesk',system-ui,sans-serif">
                    <b>Blank page between files</b>
                    <small>Inserts an empty separator page after each merged document.</small>
                </span>
            </label>
        </div>

        <?php $this->render_progress_bar('tc-pm-progress', 'Merging...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-pm-merge" type="button">Merge PDFs</button>
            <button class="tc-btn tc-btn--ghost" id="tc-pm-download" type="button" style="display:none">Download</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-pm-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Files</span><span class="tc-stat-value" id="tc-pm-stat-count">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Total Size</span><span class="tc-stat-value" id="tc-pm-stat-size">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Merged Size</span><span class="tc-stat-value" id="tc-pm-stat-merged">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
