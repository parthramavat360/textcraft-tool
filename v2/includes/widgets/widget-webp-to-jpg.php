<?php
/**
 * Widget: WebP to JPG Converter
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Webp_To_Jpg extends TextCraft_Tool_Base {

    public function get_name(): string { return 'webp_to_jpg'; }
    public function get_title(): string { return 'WebP to JPG Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['webp to jpg', 'convert webp to jpg', 'webp to jpeg', 'webp converter', 'image converter'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert WebP images to widely-supported JPG format. Adjustable quality for optimal file size and compatibility.
        </div>

        <?php $this->render_drop_zone('tc-w2j-drop', 'image/webp,.webp,.WEBP', 'Drag & drop WebP images here or click to browse'); ?>
        <?php $this->render_file_row('tc-w2j-file'); ?>

        <div class="tc-input-group" style="margin-top:16px">
            <?php $this->render_range('tc-w2j-quality', 1, 100, 92, 'Quality', '%'); ?>
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Background Color (for transparency)</label>
            <div class="tc-color-row">
                <input type="color" class="tc-color" id="tc-w2j-bgcolor" value="#ffffff">
                <span class="tc-color-hex" id="tc-w2j-bgcolor-hex">#ffffff</span>
            </div>
        </div>

        <?php $this->render_progress_bar('tc-w2j-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-w2j-convert', 'Convert to JPG', 'tc-w2j-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (WebP)</span><span class="tc-stat-value" id="tc-w2j-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (JPG)</span><span class="tc-stat-value" id="tc-w2j-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-w2j-stat-saved">-</span></div>
        </div>
        <?php
    }
}
