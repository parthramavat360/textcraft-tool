<?php
/**
 * Widget: ASCII Art Generator
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Ascii_Art extends TextCraft_Tool_Base {

    public function get_name(): string { return 'ascii_art'; }
    public function get_title(): string { return 'ASCII Art Generator'; }
    public function get_icon(): string { return 'eicon-code'; }

    public function get_keywords(): array {
        return ['ascii art', 'image to ascii', 'ascii generator', 'text art', 'pixel art'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert any image into ASCII art text. Choose from detailed, medium, or simple density and output as blocks, characters, or symbols.
        </div>

        <?php $this->render_drop_zone('tc-ascii-drop', 'image/*', 'Drag & drop an image here or click to browse'); ?>
        <?php $this->render_file_row('tc-ascii-file'); ?>

        <div class="tc-input-group" style="margin-top:16px">
            <?php $this->render_select('tc-ascii-density', [
                'detailed' => 'Detailed',
                'medium'   => 'Medium',
                'simple'   => 'Simple',
            ], 'Select density'); ?>
        </div>

        <div class="tc-input-group">
            <?php $this->render_select('tc-ascii-format', [
                'blocks'    => 'Blocks',
                'characters' => 'Characters',
                'symbols'   => 'Symbols',
            ], 'Select format'); ?>
        </div>

        <div class="tc-input-group">
            <?php $this->render_range('tc-ascii-width', 40, 300, 120, 'Width (chars)', ' chars'); ?>
        </div>

        <?php $this->render_progress_bar('tc-ascii-progress', 'Generating...'); ?>

        <?php $this->render_actions('tc-ascii-generate', 'Generate ASCII Art', 'tc-ascii-copy', 'Copy'); ?>

        <?php $this->render_status('tc-ascii-status'); ?>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-ascii-result">
            <textarea class="tc-textarea" id="tc-ascii-output" placeholder="ASCII art will appear here..." readonly rows="16" style="font-family:monospace;font-size:6px;line-height:6px;letter-spacing:1px;"></textarea>
        </div>
        <?php
    }
}
