<?php
/**
 * Widget: JPG Compressor
 * Premium redesign — quality slider, downscale, output name, clear all.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Jpg_Compressor extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    protected bool $premium = true;

    public function get_name(): string { return 'jpg_compressor'; }
    public function get_title(): string { return 'JPG Compressor'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['jpg compressor', 'jpeg compressor', 'reduce jpg size', 'compress jpeg'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Compress JPG/JPEG images directly in your browser with adjustable quality and optional downscale. Everything runs locally &mdash; your files are never uploaded.
        </div>

        <?php $this->render_drop_zone('tc-jpg-drop', 'image/jpeg,.jpg,.jpeg', 'Drag & drop a JPG image here or click to browse'); ?>
        <?php $this->render_file_row('tc-jpg-file'); ?>

        <div class="tc-input-group" id="tc-jpg-quality-wrap" style="margin-top:18px">
            <div class="tc-range-wrap">
                <label class="tc-range-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-jpg-quality">
                    Quality: <span id="tc-jpg-quality-val">92%</span>
                </label>
                <input type="range" class="tc-range" id="tc-jpg-quality" min="20" max="95" value="92">
                <p class="tc-lvl-hint">Lower quality shrinks the file more but may soften details.</p>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-jpg-resize">
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text" style="font-family:'Space Grotesk',system-ui,sans-serif">
                    <b>Downscale</b>
                    <small>Reduce the image to a maximum dimension.</small>
                </span>
            </label>
        </div>

        <div class="tc-input-group" id="tc-jpg-slider-section" style="display:none">
            <div class="tc-range-wrap">
                <label class="tc-range-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-jpg-maxdim">
                    Max Dimension: <span id="tc-jpg-dim-val">1200px</span>
                </label>
                <input type="range" class="tc-range" id="tc-jpg-maxdim" min="320" max="2048" value="1200" step="32">
                <p class="tc-lvl-hint">Largest allowed width or height in pixels.</p>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-jpg-name">Output file name</label>
            <input type="text" class="tc-input" style="font-family:'Space Grotesk',system-ui,sans-serif" id="tc-jpg-name" placeholder="my-image">
            <p class="tc-lvl-hint">Leave empty to use your source file name.</p>
        </div>

        <?php $this->render_progress_bar('tc-jpg-progress', 'Compressing...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-jpg-compress" type="button">Compress</button>
            <button class="tc-btn tc-btn--ghost" id="tc-jpg-download" type="button" style="display:none">Download</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-jpg-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-jpg-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Compressed</span><span class="tc-stat-value" id="tc-jpg-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-jpg-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
