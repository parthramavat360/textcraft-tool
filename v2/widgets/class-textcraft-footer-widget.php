<?php
/**
 * TextCraft Footer Widget for Elementor.
 *
 * @package TextCraftToolsPro
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class TextCraft_Footer_Widget
 *
 * Elementor widget for the TextCraft site footer.
 */
class TextCraft_Footer_Widget extends \Elementor\Widget_Base {

    /**
     * Get widget name.
     *
     * @return string
     */
    public function get_name() {
        return 'tctp_footer';
    }

    /**
     * Get widget title.
     *
     * @return string
     */
    public function get_title() {
        return __( 'TextCraft Footer', 'textcrafttoolspro' );
    }

    /**
     * Get widget icon.
     *
     * @return string
     */
    public function get_icon() {
        return 'eicon-footer';
    }

    /**
     * Get widget categories.
     *
     * @return array
     */
    public function get_categories() {
        return [ 'textcraft-tools' ];
    }

    /**
     * Get widget keywords.
     *
     * @return array
     */
    public function get_keywords() {
        return [ 'footer', 'copyright', 'links', 'textcraft' ];
    }

    /**
     * Register controls.
     */
    protected function register_controls() {

        /* ---------- Brand Controls ---------- */
        $this->start_controls_section(
            'section_brand',
            [ 'label' => __( 'Brand', 'textcrafttoolspro' ) ]
        );

        $this->add_control(
            'brand_abbr',
            [
                'label'   => __( 'Brand Abbreviation', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'TC',
            ]
        );

        $this->add_control(
            'brand_text',
            [
                'label'   => __( 'Brand Name', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'TextCraft',
            ]
        );

        $this->add_control(
            'brand_url',
            [
                'label'   => __( 'Brand Link', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => [ 'url' => '/' ],
            ]
        );

        $this->add_control(
            'brand_description',
            [
                'label'   => __( 'Description', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'Seventy-four browser utilities for people who just need the job done.',
                'rows'    => 3,
            ]
        );

        $this->end_controls_section();

        /* ---------- Link Columns Controls ---------- */
        $this->start_controls_section(
            'section_columns',
            [ 'label' => __( 'Link Columns', 'textcrafttoolspro' ) ]
        );

        $col_repeater = new \Elementor\Repeater();

        $col_repeater->add_control(
            'col_title',
            [
                'label'   => __( 'Column Title', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
            ]
        );

        $col_repeater->add_control(
            'col_links',
            [
                'label'       => __( 'Links (one per line)', 'textcrafttoolspro' ),
                'type'        => \Elementor\Controls_Manager::TEXTAREA,
                'description' => __( 'Format: Label | URL', 'textcrafttoolspro' ),
            ]
        );

        $this->add_control(
            'footer_columns',
            [
                'label'       => __( 'Footer Columns', 'textcrafttoolspro' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $col_repeater->get_controls(),
                'default'     => [
                    [
                        'col_title' => 'Tools',
                        'col_links' => "PDF|#pdf\nImage|#image\nText|#text\nDeveloper|#dev",
                    ],
                    [
                        'col_title' => 'Company',
                        'col_links' => "About|#\nBlog|#\nContact|#",
                    ],
                    [
                        'col_title' => 'Legal',
                        'col_links' => "Privacy|#\nTerms|#\nDisclaimer|#",
                    ],
                ],
                'title_field' => 'col_title',
            ]
        );

        $this->end_controls_section();

        /* ---------- Copyright Controls ---------- */
        $this->start_controls_section(
            'section_copyright',
            [ 'label' => __( 'Copyright Bar', 'textcrafttoolspro' ) ]
        );

        $this->add_control(
            'copyright_left',
            [
                'label'   => __( 'Left Text', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '© 2026 TextCraft Tools',
            ]
        );

        $this->add_control(
            'copyright_right',
            [
                'label'   => __( 'Right Text', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Made for fast work.',
            ]
        );

        $this->end_controls_section();

        /* ---------- Style: Footer ---------- */
        $this->start_controls_section(
            'style_footer',
            [ 'label' => __( 'Footer', 'textcrafttoolspro' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ]
        );

        $this->add_control(
            'footer_bg',
            [
                'label'     => __( 'Background Color', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tctp-footer' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'footer_text_color',
            [
                'label'     => __( 'Text Color', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tctp-footer' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        /* ---------- Style: Brand ---------- */
        $this->start_controls_section(
            'style_brand',
            [ 'label' => __( 'Brand', 'textcrafttoolspro' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ]
        );

        $this->add_control(
            'brand_color',
            [
                'label'     => __( 'Brand Color', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tctp-fbrand' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'brand_abbr_bg',
            [
                'label'     => __( 'Abbreviation BG', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tctp-fmark' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'brand_abbr_color',
            [
                'label'     => __( 'Abbreviation Color', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tctp-fmark' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        /* ---------- Style: Column Headings ---------- */
        $this->start_controls_section(
            'style_columns',
            [ 'label' => __( 'Column Headings', 'textcrafttoolspro' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ]
        );

        $this->add_control(
            'col_heading_color',
            [
                'label'     => __( 'Heading Color', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tctp-fcol h4' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'col_link_color',
            [
                'label'     => __( 'Link Color', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tctp-fcol a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'col_link_hover_color',
            [
                'label'     => __( 'Link Hover Color', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tctp-fcol a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        /* ---------- Style: Copyright ---------- */
        $this->start_controls_section(
            'style_copyright',
            [ 'label' => __( 'Copyright Bar', 'textcrafttoolspro' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ]
        );

        $this->add_control(
            'copyright_color',
            [
                'label'     => __( 'Text Color', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tctp-fcopy' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'copyright_border_color',
            [
                'label'     => __( 'Border Color', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tctp-fcopy' => 'border-top-color: {{VALUE}}',
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

        /* --- Brand --- */
        $brand_abbr = esc_html( $settings['brand_abbr'] );
        $brand_text = esc_html( $settings['brand_text'] );
        $brand_url  = $settings['brand_url']['url'] ? esc_url( $settings['brand_url']['url'] ) : '/';
        $brand_desc = esc_html( $settings['brand_description'] );

        /* --- Columns --- */
        $columns = $settings['footer_columns'];

        /* --- Copyright --- */
        $copyright_left  = esc_html( $settings['copyright_left'] );
        $copyright_right = esc_html( $settings['copyright_right'] );
        ?>
        <footer class="tctp-footer">
            <div class="tctp-fwrap">
                <div class="tctp-fgrid">
                    <!-- Brand Column -->
                    <div class="tctp-fbrand-col">
                        <a class="tctp-fbrand" href="<?php echo $brand_url; ?>">
                            <span class="tctp-fmark"><?php echo $brand_abbr; ?></span>
                            <?php echo $brand_text; ?>
                        </a>
                        <?php if ( $brand_desc ) : ?>
                            <p class="tctp-fdesc"><?php echo $brand_desc; ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Link Columns -->
                    <?php foreach ( $columns as $col ) : ?>
                        <div class="tctp-fcol">
                            <h4><?php echo esc_html( $col['col_title'] ); ?></h4>
                            <ul>
                                <?php
                                $lines = array_filter( array_map( 'trim', explode( "\n", $col['col_links'] ) ) );
                                foreach ( $lines as $line ) :
                                    $parts = array_map( 'trim', explode( '|', $line, 2 ) );
                                    $label = isset( $parts[0] ) ? esc_html( $parts[0] ) : '';
                                    $url   = isset( $parts[1] ) ? esc_url( $parts[1] ) : '#';
                                    if ( $label ) :
                                ?>
                                    <li><a href="<?php echo $url; ?>"><?php echo $label; ?></a></li>
                                <?php
                                    endif;
                                endforeach;
                                ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Copyright Bar -->
                <div class="tctp-fcopy">
                    <span><?php echo $copyright_left; ?></span>
                    <span><?php echo $copyright_right; ?></span>
                </div>
            </div>
        </footer>
        <?php
    }

    /**
     * Render the widget output in the editor.
     */
    protected function content_template() {
        ?>
        <# (function(){
            var s = settings;
            var brandUrl = ( s.brand_url && s.brand_url.url ) ? s.brand_url.url : '/';
        #>
        <footer class="tctp-footer">
            <div class="tctp-fwrap">
                <div class="tctp-fgrid">
                    <div class="tctp-fbrand-col">
                        <a class="tctp-fbrand" href="{{ brandUrl }}">
                            <span class="tctp-fmark">{{{ s.brand_abbr }}}</span>
                            {{{ s.brand_text }}}
                        </a>
                        <# if( s.brand_description ) { #>
                            <p class="tctp-fdesc">{{{ s.brand_description }}}</p>
                        <# } #>
                    </div>
                    <# _.each( s.footer_columns, function( col ) { #>
                        <div class="tctp-fcol">
                            <h4>{{{ col.col_title }}}</h4>
                            <ul>
                                <# var lines = col.col_links.split('\n'); _.each( lines, function( line ) {
                                    var parts = line.split('|');
                                    if( parts[0] && parts[0].trim() ) {
                                #>
                                    <li><a href="{{{ parts[1] ? parts[1].trim() : '#' }}}">{{{ parts[0].trim() }}}</a></li>
                                <# } }); #>
                            </ul>
                        </div>
                    <# }); #>
                </div>
                <div class="tctp-fcopy">
                    <span>{{{ s.copyright_left }}}</span>
                    <span>{{{ s.copyright_right }}}</span>
                </div>
            </div>
        </footer>
        <# })();
        #>
        <?php
    }
}
