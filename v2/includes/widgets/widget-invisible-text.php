<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;

use Elementor\Controls_Manager;
defined('ABSPATH') || exit;

class Widget_Invisible_Text extends TextCraft_Tool_Base {

    public function get_name(): string {
        return 'invisible_text';
    }

    public function get_title(): string {
        return 'Invisible Text Generator';
    }

    public function get_icon(): string {
        return 'eicon-eye-slash-o';
    }

    protected function register_tool_controls(): void {

        $this->add_control(
            'it_type',
            [
                'label'   => esc_html__('Character Type', 'textcrafttoolspro'),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'zero_width_space'     => esc_html__('Zero-Width Space (U+200B)', 'textcrafttoolspro'),
                    'zero_width_joiner'    => esc_html__('Zero-Width Joiner (U+200D)', 'textcrafttoolspro'),
                    'zero_width_non_joiner' => esc_html__('Zero-Width Non-Joiner (U+200C)', 'textcrafttoolspro'),
                    'braille_blank'        => esc_html__('Braille Blank (U+2800)', 'textcrafttoolspro'),
                    'military_symbol'      => esc_html__('Ogham Space (U+1680)', 'textcrafttoolspro'),
                    'mongolian_vowel'      => esc_html__('Mongolian Vowel Separator (U+180E)', 'textcrafttoolspro'),
                ],
                'default' => 'zero_width_space',
            ]
        );

        $this->add_control(
            'it_count',
            [
                'label'       => esc_html__('Character Count', 'textcrafttoolspro'),
                'type'        => Controls_Manager::NUMBER,
                'default'     => 10,
                'min'         => 1,
                'max'         => 1000,
                'label_block' => true,
            ]
        );
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-invisible-grid" id="tc-invisible-grid">
            <div class="tc-invisible-item" data-char="&#x200B;" data-name="Zero-Width Space">
                <span class="tc-invisible-preview">â€‹</span>
                <span class="tc-invisible-label">U+200B</span>
                <button class="tc-btn tc-btn--ghost tc-btn--sm" type="button" data-copy="&#x200B;">Copy</button>
            </div>
            <div class="tc-invisible-item" data-char="&#x200D;" data-name="Zero-Width Joiner">
                <span class="tc-invisible-preview">â€</span>
                <span class="tc-invisible-label">U+200D</span>
                <button class="tc-btn tc-btn--ghost tc-btn--sm" type="button" data-copy="&#x200D;">Copy</button>
            </div>
            <div class="tc-invisible-item" data-char="&#x200C;" data-name="Zero-Width Non-Joiner">
                <span class="tc-invisible-preview">â€Œ</span>
                <span class="tc-invisible-label">U+200C</span>
                <button class="tc-btn tc-btn--ghost tc-btn--sm" type="button" data-copy="&#x200C;">Copy</button>
            </div>
            <div class="tc-invisible-item" data-char="&#x2800;" data-name="Braille Blank">
                <span class="tc-invisible-preview">â €</span>
                <span class="tc-invisible-label">U+2800</span>
                <button class="tc-btn tc-btn--ghost tc-btn--sm" type="button" data-copy="&#x2800;">Copy</button>
            </div>
            <div class="tc-invisible-item" data-char="&#x1680;" data-name="Ogham Space Mark">
                <span class="tc-invisible-preview">áš€</span>
                <span class="tc-invisible-label">U+1680</span>
                <button class="tc-btn tc-btn--ghost tc-btn--sm" type="button" data-copy="&#x1680;">Copy</button>
            </div>
            <div class="tc-invisible-item" data-char="&#x180E;" data-name="Mongolian Vowel Separator">
                <span class="tc-invisible-preview">á Ž</span>
                <span class="tc-invisible-label">U+180E</span>
                <button class="tc-btn tc-btn--ghost tc-btn--sm" type="button" data-copy="&#x180E;">Copy</button>
            </div>
        </div>
        <div class="tc-generator-row">
            <label class="tc-range-label"><?php echo esc_html__('Generate:', 'textcrafttoolspro'); ?></label>
            <input type="number" class="tc-input tc-input--sm" id="tc-it-count" min="1" max="1000" value="10">
            <button class="tc-btn tc-btn--accent" id="tc-it-generate" type="button">Generate</button>
        </div>
        <?php
        $this->render_status('tc-it-status');
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-it-result">
            <textarea class="tc-textarea" id="tc-it-result-area" placeholder="Result will appear here..." readonly rows="8"></textarea>
        </div>
        <?php
    }
}
