<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Title_Case extends TextCraft_Tool_Base {

    public function get_name(): string {
        return 'title_case';
    }

    public function get_title(): string {
        return 'Title Case Converter';
    }

    public function get_icon(): string {
        return 'eicon-editor-h2';
    }

    protected function render_tool_content(array $settings): void {
        $this->render_textarea('tc-title-input', 'Paste or type your text here...');
        $this->render_actions('tc-title-convert', 'Convert to Title Case');
        $this->render_progress_bar('tc-title-bar', 'Converting...');
        $this->render_status('tc-title-status');
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-result">
            <textarea class="tc-textarea" id="tc-title-output" placeholder="Result will appear here..." readonly></textarea>
        </div>
        <?php
        $this->render_stats_panel_row('tc-title-stats', [
            'words' => 'Words',
            'chars' => 'Characters',
        ]);
    }
}
