<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Duplicate_Word extends TextCraft_Tool_Base {

    public function get_name(): string {
        return 'duplicate_word';
    }

    public function get_title(): string {
        return 'Duplicate Word Finder';
    }

    public function get_icon(): string {
        return 'eicon-search-bold';
    }

    protected function register_tool_controls(): void {

        $this->add_control(
            'dw_case_sensitive',
            [
                'label'        => esc_html__('Case Sensitive', 'textcrafttoolspro'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => '',
            ]
        );

        $this->add_control(
            'dw_ignore_common',
            [
                'label'        => esc_html__('Ignore Common Words', 'textcrafttoolspro'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );
    }

    protected function render_tool_content(array $settings): void {
        $this->render_textarea('tc-dw-input', 'Paste or type your text here...');
        $this->render_actions('tc-dw-find', 'Find Duplicates');
        $this->render_progress_bar('tc-dw-bar', 'Scanning...');
        $this->render_status('tc-dw-status');
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-result">
            <div class="tc-dw-tags" id="tc-dw-tags"></div>
            <div class="tc-dw-freq" id="tc-dw-freq"></div>
        </div>
        <?php
        $this->render_stats_panel_row('tc-dw-stats', [
            'duplicates' => 'Duplicate Words',
            'total'      => 'Total Words',
        ]);
    }
}
