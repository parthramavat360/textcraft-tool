<?php
/**
 * Widget: PDF Compressor
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Pdf_Compressor extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    protected bool $premium = true;

    public function get_name(): string { return 'pdf_compressor'; }
    public function get_title(): string { return 'PDF Compressor'; }
    public function get_icon(): string { return 'eicon-file-download'; }

    public function get_keywords(): array {
        return ['pdf compressor', 'reduce pdf size', 'shrink pdf', 'compress pdf online'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Reduce PDF file size instantly &mdash; your files never leave your browser. Pick a compression level, hit <strong>Compress PDF</strong>, and download the smaller file.
        </div>

        <?php $this->render_drop_zone('tc-pdf-drop', '.pdf,application/pdf', 'Drag & drop a PDF here or click to browse'); ?>
        <?php $this->render_file_row('tc-pdf-file'); ?>

        <div class="tc-input-group" style="margin-top:18px">
            <label class="tc-label">Compression Level</label>
            <div class="tc-modes tc-modes--cards" data-group="pdf-level">
                <button class="tc-btn tc-btn--ghost" data-val="1" type="button">
                    <span class="tc-card-title">Less</span>
                    <span class="tc-card-desc">Near-lossless, keeps text &amp; images crisp</span>
                </button>
                <button class="tc-btn tc-btn--ghost sel" data-val="2" type="button">
                    <span class="tc-card-title">Recommended</span>
                    <span class="tc-card-desc">Best balance of size &amp; quality</span>
                </button>
                <button class="tc-btn tc-btn--ghost" data-val="3" type="button">
                    <span class="tc-card-title">Strong</span>
                    <span class="tc-card-desc">Maximum reduction, slightly softer images</span>
                </button>
            </div>
            <p class="tc-lvl-hint" id="tc-pdf-level-hint">
                Recommended &mdash; the best balance between file size and visual quality.
            </p>
        </div>

        <?php $this->render_progress_bar('tc-pdf-progress', 'Compressing...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-pdf-compress" type="button">Compress PDF</button>
            <button class="tc-btn tc-btn--ghost" id="tc-pdf-download" type="button">Download</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-pdf-clear" type="button">Clear all</button>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}