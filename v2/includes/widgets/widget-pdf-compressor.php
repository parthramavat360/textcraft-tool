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
            Reduce PDF file size instantly &mdash; your files never leave your browser. Pick a compression mode and level, hit <strong>Compress PDF</strong>, and download the smaller file.
        </div>

        <?php $this->render_drop_zone('tc-pdf-drop', '.pdf,application/pdf', 'Drag & drop a PDF here or click to browse'); ?>
        <?php $this->render_file_row('tc-pdf-file'); ?>

        <div class="tc-input-group" style="margin-top:18px">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif">Compression Mode</label>
            <div class="tc-modes" data-group="pdf-mode">
                <button class="tc-btn sel" data-val="auto" type="button">Auto &mdash; smallest file</button>
                <button class="tc-btn" data-val="lossless" type="button">Lossless</button>
                <button class="tc-btn" data-val="maximum" type="button">Maximum size</button>
            </div>
            <p class="tc-lvl-hint" id="tc-pdf-mode-hint">
                Auto runs both methods and keeps the smallest result.
            </p>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif">Compression Level</label>
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

        <div class="tc-input-group" id="tc-pdf-quality-wrap">
            <div class="tc-range-wrap">
                <label class="tc-range-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-pdf-quality">
                    Image Quality: <span id="tc-pdf-quality-val">88%</span>
                </label>
                <input type="range" class="tc-range" id="tc-pdf-quality" min="15" max="100" value="88">
                <p class="tc-lvl-hint">Fine-tune image quality. Lower values shrink files more, higher values keep images sharper.</p>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-pdf-strip-meta" checked>
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text">
                    <b>Remove metadata</b>
                    <small>Strips document title, author, subject, keywords &amp; creator and re-optimizes PDF streams.</small>
                </span>
            </label>
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
