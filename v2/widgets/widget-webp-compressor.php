<?php
/**
 * Widget: WebP Compressor
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Webp_Compressor extends TextCraft_Tool_Base {

    public function get_name(): string { return 'webp_compressor'; }
    public function get_title(): string { return 'WebP Compressor'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['webp compressor', 'reduce webp size', 'compress webp', 'optimize webp'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Compress WebP images to reduce file size while maintaining quality. Adjust the quality slider for lossy compression. Everything runs in your browser — your files are never uploaded.
        </div>

        <?php $this->render_drop_zone('tc-wp-drop', 'image/webp,.webp', 'Drag & drop WebP images here or click to browse'); ?>
        <?php $this->render_file_row('tc-wp-file'); ?>

        <div class="tc-input-group" style="margin-top:16px">
            <?php $this->render_range('tc-wp-quality', 10, 100, 90, 'Quality', '%'); ?>
        </div>

        <?php $this->render_progress_bar('tc-wp-progress', 'Compressing...'); ?>

        <?php $this->render_actions('tc-wp-compress', 'Compress WebP', 'tc-wp-download', 'Download'); ?>

        <div class="tc-stats-row" id="tc-wp-stats">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-wp-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Compressed</span><span class="tc-stat-value" id="tc-wp-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-wp-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-wp-result">
            <div class="tc-preview" id="tc-wp-preview">Upload a file to see preview</div>
        </div>
        <?php
    }
}
