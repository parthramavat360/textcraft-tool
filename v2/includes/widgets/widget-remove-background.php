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

    protected bool $premium = true;

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

        <div class="tc-input-group" style="margin-top:18px">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-rmbg-highquality" checked>
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text" style="font-family:'Space Grotesk',system-ui,sans-serif">
                    <b>High Quality</b>
                    <small>Use the ISNet model (slower, more accurate).</small>
                </span>
            </label>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-rmbg-webp">
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text" style="font-family:'Space Grotesk',system-ui,sans-serif">
                    <b>Output Format: WebP</b>
                    <small>Save as WebP (smaller) instead of PNG.</small>
                </span>
            </label>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-rmbg-name">Output file name</label>
            <input type="text" class="tc-input" style="font-family:'Space Grotesk',system-ui,sans-serif" id="tc-rmbg-name" placeholder="image-no-bg">
            <p class="tc-lvl-hint">Leave empty to use your source file name.</p>
        </div>

        <?php $this->render_progress_bar('tc-rmbg-progress', 'Removing background...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-rmbg-remove" type="button">Remove Background</button>
            <button class="tc-btn tc-btn--ghost" id="tc-rmbg-download" type="button" style="display:none">Download</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-rmbg-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-rmbg-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Result</span><span class="tc-stat-value" id="tc-rmbg-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Format</span><span class="tc-stat-value" id="tc-rmbg-stat-fmt">PNG</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
