<?php
/**
 * Widget: SVG Compressor
 * Premium redesign â€” precision slider, toggle options, output name, clear all.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Svg_Compressor extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    protected bool $premium = true;

    public function get_name(): string { return 'svg_compressor'; }
    public function get_title(): string { return 'SVG Compressor'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['svg compressor', 'reduce svg size', 'compress svg', 'optimize svg'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Optimize SVG files by removing metadata, comments, and rounding path precision. Everything runs in your browser â€” your files are never uploaded.
        </div>

        <?php $this->render_drop_zone('tc-svg-drop', 'image/svg+xml,.svg', 'Drag & drop an SVG file here or click to browse'); ?>
        <?php $this->render_file_row('tc-svg-file'); ?>

        <div class="tc-input-group" >
            <div class="tc-range-wrap">
                <label class="tc-range-label"  for="tc-svg-precision">
                    Precision: <span id="tc-svg-precision-val">3</span>
                </label>
                <input type="range" class="tc-range" id="tc-svg-precision" min="0" max="10" value="3">
                <p class="tc-lvl-hint">Lower precision trims more decimals for smaller files.</p>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-svg-meta" checked>
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text" >
                    <b>Remove Metadata</b>
                    <small>Strip editor metadata, titles, descriptions.</small>
                </span>
            </label>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-svg-comments" checked>
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text" >
                    <b>Remove Comments</b>
                    <small>Remove all HTML/XML comments.</small>
                </span>
            </label>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-svg-paths" checked>
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text" >
                    <b>Minify Path Data</b>
                    <small>Round numbers and remove whitespace in d="" attributes.</small>
                </span>
            </label>
        </div>

        <div class="tc-input-group">
            <label class="tc-label"  for="tc-svg-name">Output file name</label>
            <input type="text" class="tc-input"  id="tc-svg-name" placeholder="my-graphic">
            <p class="tc-lvl-hint">Leave empty to use your source file name.</p>
        </div>

        <?php $this->render_progress_bar('tc-svg-progress', 'Compressing...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-svg-compress" type="button">Compress SVG</button>
            <button class="tc-btn tc-btn--ghost" id="tc-svg-download" type="button" style="display:none">Download</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-svg-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-svg-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Compressed</span><span class="tc-stat-value" id="tc-svg-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-svg-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
