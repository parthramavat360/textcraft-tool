<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;

use Elementor\Controls_Manager;
defined('ABSPATH') || exit;

class Widget_Duplicate_Line extends TextCraft_Tool_Base {

    public function get_name(): string {
        return 'duplicate_line';
    }

    public function get_title(): string {
        return 'Duplicate Line Remover';
    }

    public function get_icon(): string {
        return 'eicon-editor-list-ul';
    }

    protected function register_tool_controls(): void {

        $this->add_control(
            'dl_case_sensitive',
            [
                'label'        => esc_html__('Case Sensitive', 'textcrafttoolspro'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => '',
            ]
        );

        $this->add_control(
            'dl_trim_whitespace',
            [
                'label'        => esc_html__('Trim Whitespace', 'textcrafttoolspro'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'dl_remove_blanks',
            [
                'label'        => esc_html__('Remove Blank Lines', 'textcrafttoolspro'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => '',
            ]
        );

        $this->add_control(
            'dl_sort',
            [
                'label'        => esc_html__('Sort Lines', 'textcrafttoolspro'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => '',
            ]
        );
    }

    protected function render_tool_content(array $settings): void {
        $this->render_textarea('tc-dl-input', 'Paste text with duplicate lines...');
        $this->render_actions('tc-dl-remove', 'Remove Duplicates');
        $this->render_progress_bar('tc-dl-bar', 'Processing...');
        $this->render_status('tc-dl-status');
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-result">
            <textarea class="tc-textarea" id="tc-dl-output" placeholder="Result will appear here..." readonly></textarea>
        </div>
        <?php
        $this->render_stats_panel_row('tc-dl-stats', [
            'total'   => 'Total Lines',
            'unique'  => 'Unique Lines',
            'removed' => 'Duplicates Removed',
        ]);
    }
}
