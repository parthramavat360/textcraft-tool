<?php
/**
 * Widget: PNG to JPG Converter
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Png_To_Jpg extends TextCraft_Tool_Base {

    public function get_name(): string { return 'png_to_jpg'; }
    public function get_title(): string { return 'PNG to JPG Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['png to jpg', 'convert png to jpg', 'png to jpeg', 'image converter', 'png to jfif'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert PNG images to JPG format with adjustable quality and custom background color for transparent areas.
        </div>

        <?php $this->render_drop_zone('tc-p2j-drop', 'image/png,.png', 'Drag & drop PNG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-p2j-file'); ?>

        <div class="tc-input-group" style="margin-top:16px">
            <?php $this->render_range('tc-p2j-quality', 1, 100, 90, 'Quality', '%'); ?>
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Background Color (for transparency)</label>
            <div class="tc-color-row">
                <input type="color" class="tc-color" id="tc-p2j-bgcolor" value="#ffffff">
                <span class="tc-color-hex" id="tc-p2j-bgcolor-hex">#ffffff</span>
            </div>
        </div>

        <?php $this->render_progress_bar('tc-p2j-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-p2j-convert', 'Convert to JPG', 'tc-p2j-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (PNG)</span><span class="tc-stat-value" id="tc-p2j-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (JPG)</span><span class="tc-stat-value" id="tc-p2j-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-p2j-stat-saved">-</span></div>
        </div>
        <?php
    }
}
