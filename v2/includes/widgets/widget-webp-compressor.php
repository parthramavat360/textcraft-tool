<?php
/**
 * Widget: WebP Compressor
 * Premium redesign — quality slider, downscale, output name, clear all.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Webp_Compressor extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    protected bool $premium = true;

    public function get_name(): string { return 'webp_compressor'; }
    public function get_title(): string { return 'WebP Compressor'; }
    public function get_icon(): string { return 'eicon-image-video'; }

    public function get_keywords(): array {
        return ['webp compressor', 'compress webp', 'reduce webp size', 'optimize webp'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Compress WebP images right in your browser. Tune quality and optional downscale to shrink files &mdash; nothing is ever uploaded.
        </div>

        <?php $this->render_drop_zone('tc-wp-drop', 'image/webp,.webp', 'Drag & drop a WebP image here or click to browse'); ?>
        <?php $this->render_file_row('tc-wp-file'); ?>

        <div class="tc-input-group" style="margin-top:18px">
            <div class="tc-range-wrap">
                <label class="tc-range-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-wp-quality">
                    Quality: <span id="tc-wp-quality-val">85%</span>
                </label>
                <input type="range" class="tc-range" id="tc-wp-quality" min="10" max="100" value="85">
                <p class="tc-lvl-hint">Lower quality shrinks the file more but may soften details.</p>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-wp-resize">
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text" style="font-family:'Space Grotesk',system-ui,sans-serif">
                    <b>Downscale</b>
                    <small>Reduce the image to a maximum dimension.</small>
                </span>
            </label>
        </div>

        <div class="tc-input-group" id="tc-wp-slider-section" style="display:none">
            <div class="tc-range-wrap">
                <label class="tc-range-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-wp-maxdim">
                    Max Dimension: <span id="tc-wp-dim-val">1200px</span>
                </label>
                <input type="range" class="tc-range" id="tc-wp-maxdim" min="320" max="2048" value="1200" step="32">
                <p class="tc-lvl-hint">Largest allowed width or height in pixels.</p>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-wp-name">Output file name</label>
            <input type="text" class="tc-input" style="font-family:'Space Grotesk',system-ui,sans-serif" id="tc-wp-name" placeholder="my-image">
            <p class="tc-lvl-hint">Leave empty to use your source file name.</p>
        </div>

        <?php $this->render_progress_bar('tc-wp-progress', 'Compressing...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-wp-compress" type="button">Compress</button>
            <button class="tc-btn tc-btn--ghost" id="tc-wp-download" type="button" style="display:none">Download</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-wp-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-wp-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Compressed</span><span class="tc-stat-value" id="tc-wp-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-wp-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
