<?php
/**
 * Strip Elementor Widget
 *
 * Full-width trust/benefits bar matching the HTML design.
 *
 * @package    TextCraftToolsPro
 * @subpackage Widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class TextCraft_Strip_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'tctp_strip';
    }

    public function get_title() {
        return __( 'Strip', 'textcrafttoolspro' );
    }

    public function get_icon() {
        return 'eicon-call-to-action';
    }

    public function get_categories() {
        return [ 'textcrafttools' ];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_strip',
            [ 'label' => __( 'Features', 'textcrafttoolspro' ) ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'title',
            [
                'label'   => __( 'Title', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
            ]
        );

        $repeater->add_control(
            'description',
            [
                'label' => __( 'Description', 'textcrafttoolspro' ),
                'type'  => \Elementor\Controls_Manager::TEXTAREA,
                'rows'  => 2,
            ]
        );

        $this->add_control(
            'features',
            [
                'label'       => __( 'Feature Items', 'textcrafttoolspro' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => [
                    [
                        'title'       => 'Processed locally',
                        'description' => 'Most tools never send your file anywhere — the work happens inside your browser tab.',
                    ],
                    [
                        'title'       => 'No account, no limits',
                        'description' => 'No sign-up wall, no daily quota, no watermark on anything you export.',
                    ],
                    [
                        'title'       => 'Built for speed',
                        'description' => 'Every tool loads on its own lightweight page and runs in under a second.',
                    ],
                ],
                'title_field' => 'title',
            ]
        );

        $this->end_controls_section();

        /* ---------- Style ---------- */
        $this->start_controls_section(
            'style_strip',
            [
                'label' => __( 'Strip', 'textcrafttoolspro' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'bg_color',
            [
                'label'     => __( 'Background', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#f6f8fb',
                'selectors' => [
                    '{{SELECTOR}} .tctp-strip' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'border_color',
            [
                'label'     => __( 'Border Color', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#e4e9f0',
                'selectors' => [
                    '{{SELECTOR}} .tctp-strip' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => __( 'Title Color', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#0b1220',
                'selectors' => [
                    '{{SELECTOR}} .tctp-strip-item h3' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'desc_color',
            [
                'label'     => __( 'Description Color', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#4a5568',
                'selectors' => [
                    '{{SELECTOR}} .tctp-strip-item p' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $features = $settings['features'];

        $this->add_render_attribute( 'wrapper', 'class', 'tctp-strip' );
        ?>
        <div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
            <div class="tctp-strip-inner">
                <?php foreach ( $features as $feature ) : ?>
                    <div class="tctp-strip-item">
                        <h3><?php echo esc_html( $feature['title'] ); ?></h3>
                        <p><?php echo esc_html( $feature['description'] ); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    protected function content_template() {}
}
