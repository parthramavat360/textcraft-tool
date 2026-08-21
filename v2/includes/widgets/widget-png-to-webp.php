<?php
/**
 * Widget: PNG to WebP Converter
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Png_To_Webp extends TextCraft_Tool_Base {

    public function get_name(): string { return 'png_to_webp'; }
    public function get_title(): string { return 'PNG to WebP Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['png to webp', 'convert png to webp', 'webp converter', 'optimize png'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert PNG images to WebP format for faster web loading. Excellent compression with transparency support and adjustable quality.
        </div>

        <?php $this->render_drop_zone('tc-p2w-drop', 'image/png,.png', 'Drag & drop PNG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-p2w-file'); ?>

        <div class="tc-input-group" style="margin-top:16px">
            <?php $this->render_range('tc-p2w-quality', 1, 100, 92, 'Quality', '%'); ?>
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Presets</label>
            <div class="tc-modes" data-group="p2w-quality">
                <button class="tc-btn tc-btn--ghost" data-val="60" type="button">Small (60%)</button>
                <button class="tc-btn tc-btn--ghost" data-val="75" type="button">Good (75%)</button>
                <button class="tc-btn tc-btn--ghost sel" data-val="92" type="button">Best (92%)</button>
                <button class="tc-btn tc-btn--ghost" data-val="95" type="button">HQ (95%)</button>
                <button class="tc-btn tc-btn--ghost" data-val="100" type="button">Lossless</button>
            </div>
        </div>

        <?php $this->render_progress_bar('tc-p2w-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-p2w-convert', 'Convert to WebP', 'tc-p2w-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (PNG)</span><span class="tc-stat-value" id="tc-p2w-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (WebP)</span><span class="tc-stat-value" id="tc-p2w-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-p2w-stat-saved">-</span></div>
        </div>
        <?php
    }
}
