<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;

use Elementor\Controls_Manager;
defined('ABSPATH') || exit;

class Widget_Pig_Latin extends TextCraft_Tool_Base {

    public function get_name(): string {
        return 'pig_latin';
    }

    public function get_title(): string {
        return 'Pig Latin Translator';
    }

    public function get_icon(): string {
        return 'eicon-integration';
    }

    protected function register_tool_controls(): void {

        $this->add_control(
            'pl_vowel_suffix',
            [
                'label'   => esc_html__('Vowel Suffix', 'textcrafttoolspro'),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'way' => [
                        'title' => esc_html__('-way', 'textcrafttoolspro'),
                        'icon'  => 'eicon-check',
                    ],
                    'yay' => [
                        'title' => esc_html__('-yay', 'textcrafttoolspro'),
                        'icon'  => 'eicon-check',
                    ],
                ],
                'default'     => 'way',
                'label_block' => false,
            ]
        );
    }

    protected function render_tool_content(array $settings): void {
        $this->render_textarea('tc-pl-input', 'Enter English text to translate to Pig Latin...');
        $this->render_actions('tc-pl-translate', 'Translate to Pig Latin');
        $this->render_progress_bar('tc-pl-bar', 'Translating...');
        $this->render_status('tc-pl-status');
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-result">
            <textarea class="tc-textarea" id="tc-pl-output" placeholder="Pig Latin translation will appear here..." readonly></textarea>
        </div>
        <?php
    }
}
