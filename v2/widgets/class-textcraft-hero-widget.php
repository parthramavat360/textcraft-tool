<?php
/**
 * TextCraft Hero Widget for Elementor.
 *
 * @package TextCraftToolsPro
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class TextCraft_Hero_Widget
 *
 * Elementor widget for the TextCraft hero section with live search.
 */
class TextCraft_Hero_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'tctp_hero';
    }

    public function get_title() {
        return __( 'TextCraft Hero', 'textcrafttoolspro' );
    }

    public function get_icon() {
        return 'eicon-search';
    }

    public function get_categories() {
        return [ 'textcraft-tools' ];
    }

    public function get_keywords() {
        return [ 'hero', 'search', 'banner', 'textcraft' ];
    }

    protected function register_controls() {

        /* ---------- Content Controls ---------- */
        $this->start_controls_section(
            'section_hero',
            [ 'label' => __( 'Hero Content', 'textcrafttoolspro' ) ]
        );

        $this->add_control(
            'eyebrow_text',
            [
                'label'   => __( 'Eyebrow Text', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '74 tools · nothing to install',
            ]
        );

        $this->add_control(
            'heading',
            [
                'label'   => __( 'Heading', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Tools that run in your browser, not on our servers.',
            ]
        );

        $this->add_control(
            'description',
            [
                'label'   => __( 'Description', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'Compress, convert, clean and count — PDF, image, text, SEO and developer utilities. Most process files locally, so your data never leaves the tab.',
            ]
        );

        $this->add_control(
            'search_placeholder',
            [
                'label'   => __( 'Search Placeholder', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Search tools — try "compress pdf" or "word count"',
            ]
        );

        $this->end_controls_section();

        /* ---------- Stats Controls ---------- */
        $this->start_controls_section(
            'section_stats',
            [ 'label' => __( 'Stats', 'textcrafttoolspro' ) ]
        );

        $stats_repeater = new \Elementor\Repeater();

        $stats_repeater->add_control(
            'stat_value',
            [
                'label'   => __( 'Value', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
            ]
        );

        $stats_repeater->add_control(
            'stat_label',
            [
                'label'   => __( 'Label', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'stats',
            [
                'label'       => __( 'Stats Items', 'textcrafttoolspro' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $stats_repeater->get_controls(),
                'default'     => [
                    [ 'stat_value' => '74', 'stat_label' => 'Free tools' ],
                    [ 'stat_value' => '0', 'stat_label' => 'Accounts required' ],
                    [ 'stat_value' => '<1s', 'stat_label' => 'Median run time' ],
                    [ 'stat_value' => '100%', 'stat_label' => 'Local-first where possible' ],
                ],
                'title_field' => 'stat_label',
            ]
        );

        $this->end_controls_section();

        /* ---------- Style: Hero ---------- */
        $this->start_controls_section(
            'style_hero',
            [ 'label' => __( 'Hero', 'textcrafttoolspro' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ]
        );

        $this->add_control(
            'heading_color',
            [
                'label'     => __( 'Heading Color', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tctp-hero h1' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'desc_color',
            [
                'label'     => __( 'Description Color', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tctp-hero p' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'accent_color',
            [
                'label'     => __( 'Accent Color', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tctp-dot' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        /* ---------- Style: Search ---------- */
        $this->start_controls_section(
            'style_search',
            [ 'label' => __( 'Search', 'textcrafttoolspro' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ]
        );

        $this->add_control(
            'search_bg',
            [
                'label'     => __( 'Background', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tctp-search input' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'search_border_color',
            [
                'label'     => __( 'Border Color', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tctp-search input' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'search_focus_border',
            [
                'label'     => __( 'Focus Border', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tctp-search input:focus' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render the widget output on the frontend.
     */
    protected function render() {
        $settings = $this->get_settings_for_display();

        $eyebrow_text     = esc_html( $settings['eyebrow_text'] );
        $heading          = esc_html( $settings['heading'] );
        $description      = esc_html( $settings['description'] );
        $search_placeholder = esc_attr( $settings['search_placeholder'] );
        $stats            = $settings['stats'];
        ?>
        <div class="tctp-hero">
            <div class="tctp-hero-inner">
                <span class="tctp-eyebrow">
                    <span class="tctp-dot"></span>
                    <?php echo $eyebrow_text; ?>
                </span>
                <h1><?php echo $heading; ?></h1>
                <p><?php echo $description; ?></p>
                <div class="tctp-search">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    <input type="text" id="tctp-search-input" placeholder="<?php echo $search_placeholder; ?>" />
                    <span class="tctp-kbd">/</span>
                </div>
                <div class="tctp-stats">
                    <?php foreach ( $stats as $stat ) : ?>
                        <div class="tctp-stat">
                            <b><?php echo esc_html( $stat['stat_value'] ); ?></b>
                            <span><?php echo esc_html( $stat['stat_label'] ); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render the widget output in the editor.
     */
    protected function content_template() {
        ?>
        <# (function(){
            var s = settings;
        #>
        <div class="tctp-hero">
            <div class="tctp-hero-inner">
                <span class="tctp-eyebrow">
                    <span class="tctp-dot"></span>
                    {{{ s.eyebrow_text }}}
                </span>
                <h1>{{{ s.heading }}}</h1>
                <p>{{{ s.description }}}</p>
                <div class="tctp-search">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    <input type="text" placeholder="{{{ s.search_placeholder }}}" />
                    <span class="tctp-kbd">/</span>
                </div>
                <div class="tctp-stats">
                    <# _.each( s.stats, function( stat ) { #>
                        <div class="tctp-stat">
                            <b>{{{ stat.stat_value }}}</b>
                            <span>{{{ stat.stat_label }}}</span>
                        </div>
                    <# }); #>
                </div>
            </div>
        </div>
        <# })();
        #>
        <?php
    }
}
