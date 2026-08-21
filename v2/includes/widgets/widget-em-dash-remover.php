<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;

use Elementor\Controls_Manager;
defined('ABSPATH') || exit;

class Widget_Em_Dash_Remover extends TextCraft_Tool_Base {

    public function get_name(): string {
        return 'em_dash_remover';
    }

    public function get_title(): string {
        return 'Em Dash Remover';
    }

    public function get_icon(): string {
        return 'eicon-minus';
    }

    protected function register_tool_controls(): void {

        $this->add_control(
            'edr_replace_with',
            [
                'label'   => esc_html__('Replace With', 'textcrafttoolspro'),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'nothing' => esc_html__('Nothing (remove)', 'textcrafttoolspro'),
                    'space'   => esc_html__('Space', 'textcrafttoolspro'),
                    'hyphen'  => esc_html__('Hyphen', 'textcrafttoolspro'),
                    'comma'   => esc_html__('Comma', 'textcrafttoolspro'),
                    'custom'  => esc_html__('Custom', 'textcrafttoolspro'),
                ],
                'default' => 'nothing',
            ]
        );

        $this->add_control(
            'edr_custom_replacement',
            [
                'label'       => esc_html__('Custom Replacement', 'textcrafttoolspro'),
                'type'        => Controls_Manager::TEXT,
                'default'     => '',
                'placeholder' => esc_html__('e.g. -', 'textcrafttoolspro'),
                'condition'   => ['edr_replace_with' => 'custom'],
                'label_block' => true,
            ]
        );

        $this->add_control(
            'edr_remove_em_dash',
            [
                'label'        => esc_html__('Em Dash (â€”)', 'textcrafttoolspro'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'edr_remove_en_dash',
            [
                'label'        => esc_html__('En Dash (â€“)', 'textcrafttoolspro'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'edr_remove_hyphen',
            [
                'label'        => esc_html__('Hyphen (-)', 'textcrafttoolspro'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => '',
            ]
        );
    }

    protected function render_tool_content(array $settings): void {
        $this->render_textarea('tc-edr-input', 'Paste or type your text here...');
        $this->render_actions('tc-edr-remove', 'Remove Dashes');
        $this->render_progress_bar('tc-edr-bar', 'Processing...');
        $this->render_status('tc-edr-status');
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-result">
            <textarea class="tc-textarea" id="tc-edr-output" placeholder="Result will appear here..." readonly></textarea>
        </div>
        <?php
        $this->render_stats_panel_row('tc-edr-stats', [
            'em_count' => 'Em Dashes Removed',
            'en_count' => 'En Dashes Removed',
            'total'    => 'Total Replacements',
        ]);
    }
}
