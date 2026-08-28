<?php
/**
 * TextCraft Header Widget for Elementor.
 *
 * @package TextCraftToolsPro
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class TextCraft_Header_Widget
 *
 * Elementor widget for the TextCraft site header with mega menu.
 */
class TextCraft_Header_Widget extends \Elementor\Widget_Base {

    /**
     * Get widget name.
     *
     * @return string
     */
    public function get_name() {
        return 'tctp_header';
    }

    /**
     * Get widget title.
     *
     * @return string
     */
    public function get_title() {
        return __( 'TextCraft Header', 'textcrafttoolspro' );
    }

    /**
     * Get widget icon.
     *
     * @return string
     */
    public function get_icon() {
        return 'eicon-site-map';
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
        return [ 'header', 'navigation', 'menu', 'megamenu', 'textcraft' ];
    }

    /**
     * Get available WordPress menus for use in dropdowns.
     *
     * @return array
     */
    private function get_wp_menus() {
        $menus = wp_get_nav_menus( [ 'orderby' => 'name' ] );
        $options = [ '' => __( '— Select Menu —', 'textcrafttoolspro' ) ];
        foreach ( $menus as $menu ) {
            $options[ $menu->slug ] = $menu->name;
        }
        return $options;
    }

    /**
     * Get menu items from a selected WordPress menu.
     *
     * @param string $menu_slug Menu slug.
     * @return array Menu items with label and URL.
     */
    private function get_menu_items( $menu_slug ) {
        if ( empty( $menu_slug ) ) {
            return [];
        }
        $items = wp_get_nav_menu_items( $menu_slug );
        if ( empty( $items ) || is_wp_error( $items ) ) {
            return [];
        }
        $result = [];
        foreach ( $items as $item ) {
            $result[] = [
                'label' => $item->title,
                'url'   => $item->url,
            ];
        }
        return $result;
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
            'brand_text',
            [
                'label'   => __( 'Brand Name', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'TextCraft',
            ]
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
            'brand_url',
            [
                'label'   => __( 'Brand Link', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => [ 'url' => '/' ],
            ]
        );

        $this->end_controls_section();

        /* ---------- Navigation Controls ---------- */
        $this->start_controls_section(
            'section_nav',
            [ 'label' => __( 'Navigation Links', 'textcrafttoolspro' ) ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'nav_label',
            [
                'label'   => __( 'Label', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
            ]
        );

        $repeater->add_control(
            'nav_url',
            [
                'label' => __( 'Link', 'textcrafttoolspro' ),
                'type'  => \Elementor\Controls_Manager::URL,
            ]
        );

        $this->add_control(
            'nav_links',
            [
                'label'       => __( 'Navigation Items', 'textcrafttoolspro' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => [
                    [ 'nav_label' => 'PDF', 'nav_url' => [ 'url' => '#pdf' ] ],
                    [ 'nav_label' => 'Image', 'nav_url' => [ 'url' => '#image' ] ],
                    [ 'nav_label' => 'Text', 'nav_url' => [ 'url' => '#text' ] ],
                    [ 'nav_label' => 'Developer', 'nav_url' => [ 'url' => '#dev' ] ],
                    [ 'nav_label' => 'Blog', 'nav_url' => [ 'url' => '#' ] ],
                ],
                'title_field' => 'nav_label',
            ]
        );

        $this->end_controls_section();

        /* ---------- Mega Menu Controls ---------- */
        $this->start_controls_section(
            'section_mega',
            [ 'label' => __( 'Mega Menu', 'textcrafttoolspro' ) ]
        );

        $this->add_control(
            'mega_trigger_text',
            [
                'label'   => __( 'Trigger Text', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'All Tools',
            ]
        );

        $this->add_control(
            'mega_info',
            [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw'  => __( 'Select a WordPress menu for each column. Create menus under Appearance → Menus.', 'textcrafttoolspro' ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
            ]
        );

        $mega_col_repeater = new \Elementor\Repeater();

        $mega_col_repeater->add_control(
            'col_menu',
            [
                'label'   => __( 'WordPress Menu', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => $this->get_wp_menus(),
                'default' => '',
            ]
        );

        $mega_col_repeater->add_control(
            'col_title',
            [
                'label'       => __( 'Column Title (override)', 'textcrafttoolspro' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'description' => __( 'Leave empty to use the menu name as title.', 'textcrafttoolspro' ),
            ]
        );

        $this->add_control(
            'mega_columns',
            [
                'label'       => __( 'Mega Menu Columns', 'textcrafttoolspro' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $mega_col_repeater->get_controls(),
                'default'     => [
                    [
                        'col_menu'   => 'pdf-tools',
                        'col_title'  => '',
                    ],
                    [
                        'col_menu'   => 'compression',
                        'col_title'  => '',
                    ],
                    [
                        'col_menu'   => 'image-media',
                        'col_title'  => '',
                    ],
                    [
                        'col_menu'   => 'text-tools',
                        'col_title'  => '',
                    ],
                    [
                        'col_menu'   => 'developer',
                        'col_title'  => '',
                    ],
                ],
                'title_field' => 'col_title',
            ]
        );

        $this->add_control(
            'mega_footer_text',
            [
                'label'   => __( 'Footer Text', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '74 tools across 6 categories — all free, no signup.',
            ]
        );

        $this->add_control(
            'mega_footer_link_label',
            [
                'label'   => __( 'Footer Link Label', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'View the full index →',
            ]
        );

        $this->add_control(
            'mega_footer_link_url',
            [
                'label'   => __( 'Footer Link URL', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => [ 'url' => '#tools' ],
            ]
        );

        $this->end_controls_section();

        /* ---------- CTA Button Controls ---------- */
        $this->start_controls_section(
            'section_cta',
            [ 'label' => __( 'CTA Button', 'textcrafttoolspro' ) ]
        );

        $this->add_control(
            'cta_label',
            [
                'label'   => __( 'Button Text', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Browse all 74',
            ]
        );

        $this->add_control(
            'cta_url',
            [
                'label'   => __( 'Button Link', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => [ 'url' => '#tools' ],
            ]
        );

        $this->end_controls_section();

        /* ---------- Style: Header ---------- */
        $this->start_controls_section(
            'style_header',
            [ 'label' => __( 'Header', 'textcrafttoolspro' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ]
        );

        $this->add_control(
            'header_bg',
            [
                'label'     => __( 'Background Color', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => 'rgba(255,255,255,0.86)',
                'selectors' => [
                    '{{WRAPPER}} .tctp-header' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'header_height',
            [
                'label'     => __( 'Header Height (px)', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::SLIDER,
                'range'     => [ 'px' => [ 'min' => 50, 'max' => 100 ] ],
                'default'   => [ 'size' => 68 ],
                'selectors' => [
                    '{{WRAPPER}} .tctp-nav' => 'height: {{SIZE}}px',
                ],
            ]
        );

        $this->add_control(
            'brand_color',
            [
                'label'     => __( 'Brand Color', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tctp-brand' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'brand_abbr_bg',
            [
                'label'     => __( 'Brand Abbreviation BG', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tctp-mark' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'brand_abbr_color',
            [
                'label'     => __( 'Brand Abbreviation Color', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tctp-mark' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        /* ---------- Style: Navigation ---------- */
        $this->start_controls_section(
            'style_nav',
            [ 'label' => __( 'Navigation', 'textcrafttoolspro' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ]
        );

        $this->add_control(
            'nav_color',
            [
                'label'     => __( 'Link Color', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tctp-nav-links a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'nav_hover_color',
            [
                'label'     => __( 'Link Hover Color', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tctp-nav-links a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        /* ---------- Style: CTA Button ---------- */
        $this->start_controls_section(
            'style_cta',
            [ 'label' => __( 'CTA Button', 'textcrafttoolspro' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ]
        );

        $this->add_control(
            'cta_bg',
            [
                'label'     => __( 'Background', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tctp-btn-primary' => 'background: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'cta_color',
            [
                'label'     => __( 'Text Color', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tctp-btn-primary' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'cta_hover_bg',
            [
                'label'     => __( 'Hover Background', 'textcrafttoolspro' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tctp-btn-primary:hover' => 'background: {{VALUE}}',
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
        $brand_text = esc_html( $settings['brand_text'] );
        $brand_abbr = esc_html( $settings['brand_abbr'] );
        $brand_url  = $settings['brand_url']['url'] ? esc_url( $settings['brand_url']['url'] ) : '/';

        /* --- Nav Links --- */
        $nav_links = $settings['nav_links'];

        /* --- Mega Menu Columns --- */
        $mega_columns = $settings['mega_columns'];

        /* --- CTA --- */
        $cta_label = esc_html( $settings['cta_label'] );
        $cta_url   = $settings['cta_url']['url'] ? esc_url( $settings['cta_url']['url'] ) : '#tools';

        /* --- Mega Footer --- */
        $mega_footer_text       = esc_html( $settings['mega_footer_text'] );
        $mega_footer_link_label = esc_html( $settings['mega_footer_link_label'] );
        $mega_footer_link_url   = $settings['mega_footer_link_url']['url'] ? esc_url( $settings['mega_footer_link_url']['url'] ) : '#tools';
        ?>
        <header class="tctp-header">
            <div class="tctp-wrap tctp-nav">
                <!-- Brand -->
                <a class="tctp-brand" href="<?php echo $brand_url; ?>">
                    <span class="tctp-mark"><?php echo $brand_abbr; ?></span>
                    <?php echo $brand_text; ?>
                </a>

                <!-- Navigation -->
                <nav class="tctp-nav-inner" aria-label="<?php esc_attr_e( 'Primary navigation', 'textcrafttoolspro' ); ?>">
                    <div class="tctp-nav-links">
                        <?php foreach ( $nav_links as $link ) : ?>
                            <a href="<?php echo esc_url( $link['nav_url']['url'] ); ?>">
                                <?php echo esc_html( $link['nav_label'] ); ?>
                            </a>
                        <?php endforeach; ?>

                        <!-- Mega Menu Trigger -->
                        <div class="tctp-mega-wrap">
                            <button class="tctp-mega-trigger" aria-expanded="false" aria-controls="tctp-megamenu">
                                <?php echo esc_html( $settings['mega_trigger_text'] ); ?>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m6 9 6 6 6-6"/></svg>
                            </button>

                            <!-- Mega Menu Panel -->
                            <div class="tctp-mega" id="tctp-megamenu" role="menu">
                                <div class="tctp-mega-inner">
                                    <div class="tctp-mega-cols">
                                        <?php foreach ( $mega_columns as $col ) :
                                            /* Pull links from the selected WordPress menu */
                                            $menu_items = $this->get_menu_items( $col['col_menu'] );
                                            if ( empty( $menu_items ) ) {
                                                continue;
                                            }

                                            /* Column title: use override or fall back to menu name */
                                            $col_title = ! empty( $col['col_title'] ) ? $col['col_title'] : '';
                                            if ( empty( $col_title ) && ! empty( $col['col_menu'] ) ) {
                                                $menus     = wp_get_nav_menus();
                                                foreach ( $menus as $m ) {
                                                    if ( $m->slug === $col['col_menu'] ) {
                                                        $col_title = $m->name;
                                                        break;
                                                    }
                                                }
                                            }
                                            $col_title = esc_html( $col_title );
                                            $item_count = count( $menu_items );
                                        ?>
                                            <div class="tctp-mcol">
                                                <h5>
                                                    <?php echo $col_title; ?>
                                                    <i><?php echo $item_count; ?></i>
                                                </h5>
                                                <?php foreach ( $menu_items as $item ) : ?>
                                                    <a href="<?php echo esc_url( $item['url'] ); ?>">
                                                        <?php echo esc_html( $item['label'] ); ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <!-- Mega Footer -->
                                    <div class="tctp-mega-foot">
                                        <span><?php echo $mega_footer_text; ?></span>
                                        <a href="<?php echo $mega_footer_link_url; ?>"><?php echo $mega_footer_link_label; ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile CTA Button (only inside the slide-out panel) -->
                    <a class="tctp-btn tctp-btn-primary tctp-btn-mobile" href="<?php echo $cta_url; ?>">
                        <?php echo $cta_label; ?>
                    </a>
                </nav>

                <!-- Call-to-action (desktop bar) -->
                <a class="tctp-btn tctp-btn-primary tctp-btn-desktop" href="<?php echo $cta_url; ?>">
                    <?php echo $cta_label; ?>
                </a>

                <!-- Mobile Hamburger -->
                <button class="tctp-hamburger" aria-label="<?php esc_attr_e( 'Toggle menu', 'textcrafttoolspro' ); ?>" aria-expanded="false" aria-controls="tctp-mobile-nav">
                    <span></span><span></span><span></span>
                </button>
            </div>

            <!-- Mobile menu overlay (click to close) -->
            <div class="tctp-overlay" hidden></div>
        </header>
        <?php
    }

    /**
     * Render the widget output in the editor (used for live preview).
     */
    protected function content_template() {
        ?>
        <# (function(){
            var s = settings;
            var brandUrl = ( s.brand_url && s.brand_url.url ) ? s.brand_url.url : '/';
            var ctaUrl = ( s.cta_url && s.cta_url.url ) ? s.cta_url.url : '#tools';
            var megaFootUrl = ( s.mega_footer_link_url && s.mega_footer_link_url.url ) ? s.mega_footer_link_url.url : '#tools';
        #>
        <header class="tctp-header">
            <div class="tctp-wrap tctp-nav">
                <a class="tctp-brand" href="{{ brandUrl }}">
                    <span class="tctp-mark">{{{ s.brand_abbr }}}</span>
                    {{{ s.brand_text }}}
                </a>
                <nav class="tctp-nav-inner">
                    <div class="tctp-nav-links">
                        <# _.each( s.nav_links, function( link ) { #>
                            <a href="{{{ link.nav_url.url }}}">{{{ link.nav_label }}}</a>
                        <# }); #>
                        <div class="tctp-mega-wrap">
                            <button class="tctp-mega-trigger" aria-expanded="false">
                                {{{ s.mega_trigger_text }}}
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m6 9 6 6 6-6"/></svg>
                            </button>
                            <div class="tctp-mega" id="tctp-megamenu" role="menu">
                                <div class="tctp-mega-inner">
                                    <div class="tctp-mega-cols">
                                        <# _.each( s.mega_columns, function( col ) { #>
                                            <div class="tctp-mcol">
                                                <h5>{{{ col.col_title || 'Menu' }}} <i>&nbsp;</i></h5>
                                                <p style="font-size:13px;color:#8792a6;margin:0">Select a WP menu in column settings</p>
                                            </div>
                                        <# }); #>
                                    </div>
                                    <div class="tctp-mega-foot">
                                        <span>{{{ s.mega_footer_text }}}</span>
                                        <a href="{{ megaFootUrl }}">{{{ s.mega_footer_link_label }}}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
                <a class="tctp-btn tctp-btn-primary" href="{{ ctaUrl }}">{{{ s.cta_label }}}</a>
            </div>
        </header>
        <# })();
        #>
        <?php
    }
}
