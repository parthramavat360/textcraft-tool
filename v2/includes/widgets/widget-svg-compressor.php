<?php
/**
 * Widget: SVG Compressor
 * Premium design with precision slider, toggle options.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Svg_Compressor extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'svg_compressor'; }
    public function get_title(): string { return 'SVG Compressor'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['svg compressor', 'reduce svg size', 'compress svg', 'optimize svg'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Optimize SVG files by removing metadata, comments, and rounding path precision. Everything runs in your browser — your files are never uploaded.
        </div>

        <?php $this->render_drop_zone('tc-svg-drop', 'image/svg+xml,.svg', 'Drag & drop an SVG file here or click to browse'); ?>
        <?php $this->render_file_row('tc-svg-file'); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Precision <span class="tc-rsz-quality-badge" id="tc-svg-precision-val">3</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">0</span>
                    <input type="range" class="tc-rsz-slider" id="tc-svg-precision" min="0" max="10" value="3">
                    <span class="tc-rsz-slider-max">10</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Remove Metadata</h4>
                <div class="tc-rsz-slider-wrap">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-svg-meta" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                    </label>
                    <span class="tc-rsz-slider-min" style="font-size:12px;opacity:0.6">Strip editor metadata, titles, descriptions</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Remove Comments</h4>
                <div class="tc-rsz-slider-wrap">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-svg-comments" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                    </label>
                    <span class="tc-rsz-slider-min" style="font-size:12px;opacity:0.6">Remove all HTML/XML comments</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Minify Path Data</h4>
                <div class="tc-rsz-slider-wrap">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-svg-paths" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                    </label>
                    <span class="tc-rsz-slider-min" style="font-size:12px;opacity:0.6">Round numbers and remove whitespace in d="" attributes</span>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-svg-progress', 'Compressing...'); ?>

        <?php $this->render_actions('tc-svg-compress', 'Compress SVG', 'tc-svg-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-svg-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Compressed</span><span class="tc-stat-value" id="tc-svg-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-svg-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
