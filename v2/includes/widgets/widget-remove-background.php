<?php
/**
 * Widget: Remove Background
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Remove_Background extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'remove_background'; }
    public function get_title(): string { return 'Remove Background'; }
    public function get_icon(): string { return 'eicon-image-exclude'; }

    public function get_keywords(): array {
        return ['remove background', 'background remover', 'transparent image', 'cut out image'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Automatically remove the background from any image using AI-powered @imgly/background-removal. Produces a transparent PNG.
        </div>

        <?php $this->render_drop_zone('tc-rmbg-drop', 'image/*', 'Drag & drop an image here or click to browse'); ?>
        <?php $this->render_file_row('tc-rmbg-file'); ?>

        <div class="tc-checkboxes" style="margin-top:16px">
            <?php $this->render_checkbox('tc-rmbg-highquality', 'High quality output', true); ?>
        </div>

        <?php $this->render_progress_bar('tc-rmbg-progress', 'Removing background...'); ?>

        <?php $this->render_actions('tc-rmbg-remove', 'Remove Background', 'tc-rmbg-download', 'Download PNG'); ?>

        <?php $this->render_status('tc-rmbg-status'); ?>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
