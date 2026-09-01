<?php
/**
 * Widget: PNG Compressor
 * Premium redesign â€” quality slider, downscale, output name, clear all.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Png_Compressor extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    protected bool $premium = true;

    public function get_name(): string { return 'png_compressor'; }
    public function get_title(): string { return 'PNG Compressor'; }
    public function get_icon(): string { return 'eicon-image-hotspot'; }

    public function get_keywords(): array {
        return ['png compressor', 'reduce png size', 'compress png', 'minify png'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Compress PNG images entirely in your browser. Lower quality reduces color depth for smaller files &mdash; no uploads, ever.
        </div>

        <?php $this->render_drop_zone('tc-png-drop', 'image/png', 'Drag & drop a PNG image here or click to browse'); ?>
        <?php $this->render_file_row('tc-png-file'); ?>

        <div class="tc-input-group" >
            <div class="tc-range-wrap">
                <label class="tc-range-label"  for="tc-png-quality">
                    Quality: <span id="tc-png-quality-val">90%</span>
                </label>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min" >10</span>
                    <input type="range" class="tc-range" id="tc-png-quality" min="10" max="100" value="90" >
                    <span class="tc-rsz-slider-max" >100</span>
                </div>
                <p class="tc-lvl-hint">Lower values quantize colors to make the file much smaller.</p>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-png-resize" checked>
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text" >
                    <b>Downscale</b>
                    <small>Reduce the image to a maximum dimension.</small>
                </span>
            </label>
        </div>

        <div class="tc-input-group" id="tc-png-slider-section">
            <div class="tc-range-wrap">
                <label class="tc-range-label"  for="tc-png-maxdim">
                    Max Dimension: <span id="tc-png-dim-val">1200px</span>
                </label>
                <input type="range" class="tc-range" id="tc-png-maxdim" min="320" max="2048" value="1200" step="32">
                <p class="tc-lvl-hint">Largest allowed width or height in pixels.</p>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-label"  for="tc-png-name">Output file name</label>
            <input type="text" class="tc-input"  id="tc-png-name" placeholder="my-image">
            <p class="tc-lvl-hint">Leave empty to use your source file name.</p>
        </div>

        <?php $this->render_progress_bar('tc-png-progress', 'Compressing...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-png-compress" type="button">Compress</button>
            <button class="tc-btn tc-btn--ghost" id="tc-png-download" type="button" style="display:none">Download</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-png-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-png-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Compressed</span><span class="tc-stat-value" id="tc-png-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-png-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
