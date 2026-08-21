<?php
/**
 * Widget: JPG to WebP Converter
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Jpg_To_Webp extends TextCraft_Tool_Base {

    public function get_name(): string { return 'jpg_to_webp'; }
    public function get_title(): string { return 'JPG to WebP Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['jpg to webp', 'convert jpg to webp', 'jpeg to webp', 'webp converter'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert JPG/JPEG images to WebP format for smaller file sizes and faster web loading. Adjustable quality from lossy to lossless.
        </div>

        <?php $this->render_drop_zone('tc-j2w-drop', 'image/jpeg,.jpg,.jpeg', 'Drag & drop JPG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-j2w-file'); ?>

        <div class="tc-input-group" style="margin-top:16px">
            <label class="tc-label">Quality: <strong id="tc-j2w-quality-val">92</strong>%</label>
            <input type="range" class="tc-range" id="tc-j2w-quality" min="1" max="100" value="92">
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Presets</label>
            <div class="tc-modes" data-group="j2w-quality">
                <button class="tc-btn tc-btn--ghost" data-val="60" type="button">Small (60%)</button>
                <button class="tc-btn tc-btn--ghost" data-val="75" type="button">Good (75%)</button>
                <button class="tc-btn tc-btn--ghost sel" data-val="82" type="button">Best (82%)</button>
                <button class="tc-btn tc-btn--ghost" data-val="95" type="button">HQ (95%)</button>
                <button class="tc-btn tc-btn--ghost" data-val="100" type="button">Lossless</button>
            </div>
        </div>

        <?php $this->render_progress_bar('tc-j2w-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-j2w-convert', 'Convert to WebP', 'tc-j2w-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (JPG)</span><span class="tc-stat-value" id="tc-j2w-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (WebP)</span><span class="tc-stat-value" id="tc-j2w-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-j2w-stat-saved">-</span></div>
        </div>
        <?php
    }
}