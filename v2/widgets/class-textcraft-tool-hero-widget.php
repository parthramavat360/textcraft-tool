<?php
/**
 * Tool Hero Elementor Widget
 *
 * Breadcrumb, pills, heading, lede — matching HTML tool page design.
 *
 * @package TextCraftToolsPro
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class TextCraft_Tool_Hero_Widget extends \Elementor\Widget_Base {

    public function get_name()     { return 'tctp_tool_hero'; }
    public function get_title()    { return __( 'Tool Hero', 'textcrafttoolspro' ); }
    public function get_icon()     { return 'eicon-banner'; }
    public function get_categories() { return [ 'textcrafttools' ]; }

    protected function register_controls() {
        $this->start_controls_section( 'hero', [ 'label' => __( 'Tool Hero', 'textcrafttoolspro' ) ] );

        $this->add_control( 'breadcrumb_home_label', [
            'label' => __( 'Home Label', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Home',
        ] );
        $this->add_control( 'breadcrumb_category', [
            'label' => __( 'Category Label', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'PDF Tools',
        ] );
        $this->add_control( 'breadcrumb_category_url', [
            'label' => __( 'Category URL', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::URL,
        ] );
        $this->add_control( 'title', [
            'label' => __( 'Title', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'PDF Compressor',
        ] );
        $this->add_control( 'lede', [
            'label' => __( 'Lede', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => 'Shrink PDFs while keeping text sharp and fonts intact.',
        ] );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control( 'pill_text', [ 'label' => __( 'Text', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::TEXT ] );
        $repeater->add_control( 'pill_ok', [ 'label' => __( 'Green pill?', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::SWITCHER ] );
        $this->add_control( 'pills', [
            'label' => __( 'Pills', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [
                [ 'pill_text' => 'Runs locally in your browser', 'pill_ok' => 'yes' ],
                [ 'pill_text' => 'Free forever', 'pill_ok' => '' ],
                [ 'pill_text' => 'No signup', 'pill_ok' => '' ],
                [ 'pill_text' => 'Median run < 1s', 'pill_ok' => '' ],
            ],
            'title_field' => 'pill_text',
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'style', [ 'label' => __( 'Style', 'textcrafttoolspro' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );

        $this->add_control( 'title_color', [
            'label' => __( 'Title Color', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#0b1220',
            'selectors' => [ '{{SELECTOR}} .tctp-th h1' => 'color: {{VALUE}}' ],
        ] );
        $this->add_control( 'lede_color', [
            'label' => __( 'Lede Color', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '#4a5568',
            'selectors' => [ '{{SELECTOR}} .tctp-th .lede' => 'color: {{VALUE}}' ],
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();
        $home_url = home_url( '/' );
        $cat_url  = isset( $s['breadcrumb_category_url']['url'] ) ? $s['breadcrumb_category_url']['url'] : '#';
        ?>
        <div class="tctp-th">
            <div class="tctp-th-inner">
                <div class="tctp-crumbs">
                    <a href="<?php echo esc_url( $home_url ); ?>"><?php echo esc_html( $s['breadcrumb_home_label'] ); ?></a>
                    <span>/</span>
                    <a href="<?php echo esc_url( $cat_url ); ?>"><?php echo esc_html( $s['breadcrumb_category'] ); ?></a>
                    <span>/</span>
                    <span class="current"><?php echo esc_html( $s['title'] ); ?></span>
                </div>
                <div class="tctp-pills">
                    <?php foreach ( $s['pills'] as $pill ) :
                        $cls = ! empty( $pill['pill_ok'] ) ? ' ok' : '';
                    ?>
                        <span class="tctp-pill<?php echo $cls; ?>">
                            <?php if ( ! empty( $pill['pill_ok'] ) ) : ?><span class="tctp-dot"></span><?php endif; ?>
                            <?php echo esc_html( $pill['pill_text'] ); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
                <h1><?php echo esc_html( $s['title'] ); ?></h1>
                <p class="lede"><?php echo esc_html( $s['lede'] ); ?></p>
            </div>
        </div>
        <?php
    }

    protected function content_template() {}
}
