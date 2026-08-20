<?php
/**
 * Widget: GIF Compressor
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Gif_Compressor extends TextCraft_Tool_Base {

    public function get_name(): string { return 'gif_compressor'; }
    public function get_title(): string { return 'GIF Compressor'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['gif compressor', 'reduce gif size', 'compress gif', 'optimize gif'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Compress animated and static GIF images. Choose quality level and whether to preserve all frames. Everything runs in your browser — your files are never uploaded.
        </div>

        <?php $this->render_drop_zone('tc-gif-drop', 'image/gif,.gif', 'Drag & drop GIF images here or click to browse'); ?>
        <?php $this->render_file_row('tc-gif-file'); ?>

        <div class="tc-input-group" style="margin-top:16px">
            <?php $this->render_range('tc-gif-quality', 10, 100, 70, 'Quality', '%'); ?>
        </div>

        <div class="tc-checkboxes">
            <?php $this->render_checkbox('tc-gif-frames', 'Preserve all animation frames', true); ?>
        </div>

        <?php $this->render_progress_bar('tc-gif-progress', 'Compressing...'); ?>

        <?php $this->render_actions('tc-gif-compress', 'Compress GIF', 'tc-gif-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-gif-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Compressed</span><span class="tc-stat-value" id="tc-gif-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-gif-stat-saved">-</span></div>
        </div>
        <?php
    }
}
