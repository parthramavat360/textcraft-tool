<?php
/**
 * Widget: Remove Background
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Remove_Background extends TextCraft_Tool_Base {

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

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-rmbg-result" style="text-align:center;padding:20px;">
            <div id="tc-rmbg-preview" style="display:none;background-image:linear-gradient(45deg,#e0e0e0 25%,transparent 25%,transparent 75%,#e0e0e0 75%,#e0e0e0),linear-gradient(45deg,#e0e0e0 25%,transparent 25%,transparent 75%,#e0e0e0 75%,#e0e0e0);background-size:20px 20px;background-position:0 0,10px 10px;padding:10px;border-radius:8px;">
                <img id="tc-rmbg-img" src="" alt="Result" style="max-width:100%;border-radius:4px;">
            </div>
            <p id="tc-rmbg-placeholder" style="color:#999;margin-top:40px;">Result will appear here...</p>
        </div>
        <?php
    }
}
