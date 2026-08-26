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

    public function get_name(): string { return 'pdf_compressor'; }
    public function get_title(): string { return 'PDF Compressor'; }
    public function get_icon(): string { return 'eicon-file-download'; }

    public function get_keywords(): array {
        return ['pdf compressor', 'reduce pdf size', 'shrink pdf', 'compress pdf online'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Reduce PDF file size instantly. Choose between Less, Recommended, or Strong compression. Everything runs in your browser &mdash; your files are never uploaded.
        </div>

        <?php $this->render_drop_zone('tc-pdf-drop', '.pdf,application/pdf', 'Drag & drop a PDF here or click to browse'); ?>
        <?php $this->render_file_row('tc-pdf-file'); ?>

        <div class="tc-input-group" style="margin-top:16px">
            <label class="tc-label">Compression Level</label>
            <div class="tc-modes" data-group="pdf-level">
                <button class="tc-btn tc-btn--ghost" data-val="1" type="button">Less Compression</button>
                <button class="tc-btn tc-btn--ghost sel" data-val="2" type="button">Recommended</button>
                <button class="tc-btn tc-btn--ghost" data-val="3" type="button">Strong Compression</button>
            </div>
        </div>

        <?php $this->render_progress_bar('tc-pdf-progress', 'Compressing...'); ?>

        <?php $this->render_actions('tc-pdf-compress', 'Compress PDF', 'tc-pdf-download', 'Download'); ?>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}