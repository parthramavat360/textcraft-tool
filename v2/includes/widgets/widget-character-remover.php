<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;

use Elementor\Controls_Manager;
defined('ABSPATH') || exit;

class Widget_Character_Remover extends TextCraft_Tool_Base {

    public function get_name(): string {
        return 'character_remover';
    }

    public function get_title(): string {
        return 'Character Remover';
    }

    public function get_icon(): string {
        return 'eicon-cursor-move';
    }

    protected function register_tool_controls(): void {

        $this->add_control(
            'cr_custom_chars',
            [
                'label'       => esc_html__('Custom Characters', 'textcrafttoolspro'),
                'type'        => Controls_Manager::TEXT,
                'default'     => '',
                'placeholder' => esc_html__('e.g. @#$%^&*', 'textcrafttoolspro'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'cr_case_sensitive',
            [
                'label'        => esc_html__('Case Sensitive', 'textcrafttoolspro'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => '',
            ]
        );
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-preset-group" id="tc-cr-presets">
            <span class="tc-preset-label">Quick Presets:</span>
            <button class="tc-btn tc-btn--ghost" type="button" data-chars=" ">Spaces</button>
            <button class="tc-btn tc-btn--ghost" type="button" data-chars=".,;:!?">Punctuation</button>
            <button class="tc-btn tc-btn--ghost" type="button" data-chars="0123456789">Numbers</button>
            <button class="tc-btn tc-btn--ghost" type="button" data-chars="@#$%^&*(){}[]|<>~`">Special Chars</button>
            <button class="tc-btn tc-btn--ghost" type="button" data-chars="&quot;'">Quotes</button>
        </div>
        <?php
        $this->render_textarea('tc-cr-input', 'Paste or type your text here...');
        $this->render_checkbox('tc-cr-case', 'Case Sensitive', false);
        $this->render_actions('tc-cr-remove', 'Remove Characters');
        $this->render_progress_bar('tc-cr-bar', 'Removing...');
        $this->render_status('tc-cr-status');
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-result">
            <textarea class="tc-textarea" id="tc-cr-output" placeholder="Result will appear here..." readonly></textarea>
        </div>
        <?php
        $this->render_stats_panel_row('tc-cr-stats', [
            'removed'  => 'Characters Removed',
            'original' => 'Original Length',
            'result'   => 'Result Length',
        ]);
    }
}
