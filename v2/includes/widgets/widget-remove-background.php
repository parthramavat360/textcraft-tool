<?php
/**
 * Widget: Remove Background
 * Premium design with format toggle, edge smooth, stats.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Remove_Background extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'remove_background'; }
    public function get_title(): string { return 'Remove Background'; }
    public function get_icon(): string { return 'eicon-image-exclude'; }

    public function get_keywords(): array {
        return ['remove background', 'background remover', 'transparent image', 'cut out image'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Automatically remove the background from any image using AI. Produces a transparent PNG. Everything runs in your browser — your images are never uploaded.
        </div>

        <?php $this->render_drop_zone('tc-rmbg-drop', 'image/*', 'Drag & drop an image here or click to browse'); ?>
        <?php $this->render_file_row('tc-rmbg-file'); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">High Quality</h4>
                <div class="tc-rsz-slider-wrap">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-rmbg-highquality" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                    </label>
                    <span class="tc-rsz-slider-min" style="font-size:12px;opacity:0.6">Use ISNet model (slower, more accurate)</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Output Format</h4>
                <div class="tc-rsz-slider-wrap">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-rmbg-webp">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                    </label>
                    <span class="tc-rsz-slider-min" style="font-size:12px;opacity:0.6">Save as WebP (smaller) instead of PNG</span>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-rmbg-progress', 'Removing background...'); ?>

        <?php $this->render_actions('tc-rmbg-remove', 'Remove Background', 'tc-rmbg-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-rmbg-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Result</span><span class="tc-stat-value" id="tc-rmbg-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Format</span><span class="tc-stat-value" id="tc-rmbg-stat-fmt">PNG</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
