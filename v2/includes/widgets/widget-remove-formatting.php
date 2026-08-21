<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;

use Elementor\Controls_Manager;

defined('ABSPATH') || exit;

class Widget_Remove_Formatting extends TextCraft_Tool_Base {

    public function get_name(): string {
        return 'remove_formatting';
    }

    public function get_title(): string {
        return 'Remove Text Formatting';
    }

    public function get_icon(): string {
        return 'eicon-eraser';
    }

    protected function register_tool_controls(): void {

        $this->add_control(
            'rf_strip_html',
            [
                'label'        => esc_html__('Strip HTML Tags', 'textcrafttoolspro'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'rf_remove_styles',
            [
                'label'        => esc_html__('Remove Inline Styles', 'textcrafttoolspro'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'rf_remove_scripts',
            [
                'label'        => esc_html__('Remove Script Tags', 'textcrafttoolspro'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'rf_remove_comments',
            [
                'label'        => esc_html__('Remove HTML Comments', 'textcrafttoolspro'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'rf_decode_entities',
            [
                'label'        => esc_html__('Decode HTML Entities', 'textcrafttoolspro'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );
    }

    protected function render_tool_content(array $settings): void {
        $this->render_drop_zone('tc-rf-drop', 'text/html,.html,.htm,.doc,.docx', 'Drop a formatted document');
        $this->render_file_row('tc-rf-file');
        $this->render_textarea('tc-rf-input', 'Paste formatted text here...');
        $this->render_actions('tc-rf-clean', 'Clean Formatting');
        $this->render_progress_bar('tc-rf-bar', 'Cleaning...');
        $this->render_status('tc-rf-status');
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-result">
            <textarea class="tc-textarea" id="tc-rf-output" placeholder="Clean text will appear here..." readonly></textarea>
        </div>
        <?php
    }
}
