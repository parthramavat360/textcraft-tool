<?php
/**
 * Widget: WebP to PNG Converter
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Webp_To_Png extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'webp_to_png'; }
    public function get_title(): string { return 'WebP to PNG Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['webp to png', 'convert webp', 'webp converter', 'webp image'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert WebP images to PNG format instantly. Preserves full quality and transparency. Everything runs in your browser ÃƒÂ¢Ã¢â€šÂ¬Ã¢â‚¬Â your files are never uploaded.
        </div>

        <?php $this->render_drop_zone('tc-w2p-drop', 'image/webp,.webp', 'Drag & drop WebP images here or click to browse'); ?>
        <?php $this->render_file_row('tc-w2p-file'); ?>

        <?php $this->render_progress_bar('tc-w2p-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-w2p-convert', 'Convert to PNG', 'tc-w2p-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-w2p-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted</span><span class="tc-stat-value" id="tc-w2p-stat-comp">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Difference</span><span class="tc-stat-value" id="tc-w2p-stat-diff">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
