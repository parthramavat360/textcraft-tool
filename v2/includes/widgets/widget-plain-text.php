<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;

use Elementor\Controls_Manager;
defined('ABSPATH') || exit;

class Widget_Plain_Text extends TextCraft_Tool_Base {

    public function get_name(): string {
        return 'plain_text';
    }

    public function get_title(): string {
        return 'Plain Text Converter';
    }

    public function get_icon(): string {
        return 'eicon-document-file-o';
    }

    protected function register_tool_controls(): void {

        $this->add_control(
            'pt_strip_html',
            [
                'label'        => esc_html__('Strip HTML Tags', 'textcrafttoolspro'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'pt_decode_entities',
            [
                'label'        => esc_html__('Decode HTML Entities', 'textcrafttoolspro'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'pt_remove_blanks',
            [
                'label'        => esc_html__('Remove Blank Lines', 'textcrafttoolspro'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => '',
            ]
        );

        $this->add_control(
            'pt_trim_spaces',
            [
                'label'        => esc_html__('Trim Extra Spaces', 'textcrafttoolspro'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'pt_normalize_unicode',
            [
                'label'        => esc_html__('Normalize Unicode', 'textcrafttoolspro'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => '',
            ]
        );
    }

    protected function render_tool_content(array $settings): void {
        $this->render_drop_zone('tc-pt-drop', 'text/html,text/plain,.html,.htm,.txt', 'Drop an HTML or text file');
        $this->render_file_row('tc-pt-file');
        $this->render_textarea('tc-pt-input', 'Paste HTML or rich text here...');
        $this->render_actions('tc-pt-convert', 'Convert to Plain Text');
        $this->render_progress_bar('tc-pt-bar', 'Converting...');
        $this->render_status('tc-pt-status');
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-result">
            <textarea class="tc-textarea" id="tc-pt-output" placeholder="Plain text result will appear here..." readonly></textarea>
        </div>
        <?php
        $this->render_stats_panel_row('tc-pt-stats', [
            'tags_removed' => 'Tags Removed',
            'before'       => 'Characters Before',
            'after'        => 'Characters After',
        ]);
    }
}
