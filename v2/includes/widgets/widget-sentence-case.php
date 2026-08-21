<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;

use Elementor\Controls_Manager;

defined('ABSPATH') || exit;

class Widget_Sentence_Case extends TextCraft_Tool_Base {

    public function get_name(): string {
        return 'sentence_case';
    }

    public function get_title(): string {
        return 'Sentence Case Converter';
    }

    public function get_icon(): string {
        return 'eicon-editor-paragraph';
    }

    protected function register_tool_controls(): void {

        $this->add_control(
            'sc_capitalize_i',
            [
                'label'        => esc_html__('Capitalize "I"', 'textcrafttoolspro'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('Yes', 'textcrafttoolspro'),
                'label_off'    => esc_html__('No', 'textcrafttoolspro'),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'sc_preserve_abbreviations',
            [
                'label'        => esc_html__('Preserve Abbreviations', 'textcrafttoolspro'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('Yes', 'textcrafttoolspro'),
                'label_off'    => esc_html__('No', 'textcrafttoolspro'),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );
    }

    protected function render_tool_content(array $settings): void {
        $this->render_textarea('tc-sentence-input', 'Paste or type your text here...');
        $this->render_actions('tc-sentence-convert', 'Convert to Sentence Case');
        $this->render_progress_bar('tc-sentence-bar', 'Converting...');
        $this->render_status('tc-sentence-status');
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-result">
            <textarea class="tc-textarea" id="tc-sentence-output" placeholder="Result will appear here..." readonly></textarea>
        </div>
        <?php
        $this->render_stats_panel_row('tc-sentence-stats', [
            'words'     => 'Words',
            'sentences' => 'Sentences',
            'chars'     => 'Characters',
        ]);
    }
}
