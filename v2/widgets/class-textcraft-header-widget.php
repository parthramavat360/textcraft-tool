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
     * Parse pipe-delimited column text into menu-slug => title pairs.
     * Format: one "[menu-slug]|[Column Title]" per line.
     *
     * @param string $text Raw textarea value.
     * @return array List of [ 'col_menu' => slug, 'col_title' => title ].
     */
    private function parse_columns_text( $text ) {
        if ( empty( $text ) ) {
            return [];
        }
        $cols = [];
        $lines = preg_split( '/\r\n|\r|\n/', $text );
        foreach ( $lines as $line ) {
            $line = trim( $line );
            if ( $line === '' ) {
                continue;
            }
            $parts = explode( '|', $line, 2 );
            $slug  = trim( $parts[0] );
            $title = isset( $parts[1] ) ? trim( $parts[1] ) : '';
            if ( $slug !== '' ) {
                $cols[] = [
                    'col_menu'  => $slug,
                    'col_title' => $title,
                ];
            }
        }
        return $cols;
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
                    [ 'nav_label' => 'Home', 'nav_url' => [ 'url' => '/' ] ],
                    [ 'nav_label' => 'About', 'nav_url' => [ 'url' => '#' ] ],
                    [ 'nav_label' => 'Blog', 'nav_url' => [ 'url' => '#' ] ],
                    [ 'nav_label' => 'Contact', 'nav_url' => [ 'url' => '#' ] ],
                ],
                'title_field' => 'nav_label',
            ]
        );

        $this->end_controls_section();

        /* ---------- Mega Menu Groups Controls ---------- */
        $this->start_controls_section(
            'section_mega',
            [ 'label' => __( 'Mega Menu Groups', 'textcrafttoolspro' ) ]
        );

        $this->add_control(
            'mega_info',
            [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw'  => __( 'Each group becomes its own dropdown in the header. Give it a short trigger label. Set "Columns" as one "menu-slug|Column Title" per line — keep labels short so the nav bar stays compact. Create menus under Appearance → Menus.', 'textcrafttoolspro' ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
            ]
        );

        /* Per-group repeater (short trigger + its columns as pipe-delimited text) */
        $mega_group_repeater = new \Elementor\Repeater();

        $mega_group_repeater->add_control(
            'group_label',
            [
                'label'       => __( 'Dropdown Label (short)', 'textcrafttoolspro' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'description' => __( 'Shown in the nav bar. Keep short, e.g. "Images" or "PDF & Compress".', 'textcrafttoolspro' ),
                'default'     => '',
            ]
        );

        $mega_group_repeater->add_control(
            'group_columns_text',
            [
                'label'       => __( 'Columns', 'textcrafttoolspro' ),
                'type'        => \Elementor\Controls_Manager::TEXTAREA,
                'description' => __( 'One per line: menu-slug|Column Title', 'textcrafttoolspro' ),
                'default'     => '',
            ]
        );

        $mega_group_repeater->add_control(
            'group_foot_label',
            [
                'label'   => __( 'Group Footer Link Label (optional)', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Browse all 208 tools →',
            ]
        );

        $mega_group_repeater->add_control(
            'group_foot_url',
            [
                'label'   => __( 'Group Footer Link URL', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => [ 'url' => '#tools' ],
            ]
        );

        $this->add_control(
            'mega_groups',
            [
                'label'       => __( 'Mega Menu Groups', 'textcrafttoolspro' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $mega_group_repeater->get_controls(),
                'default'     => [
                    [
                        'group_label'         => 'PDF & Compress',
                        'group_columns_text'  => "pdf-tools|PDF\ncompression|Compression",
                    ],
                    [
                        'group_label'         => 'Images',
                        'group_columns_text'  => "image-media|Image & Media\nimage-editing|Image Editing",
                    ],
                    [
                        'group_label'         => 'Text & Case',
                        'group_columns_text'  => "text-tools|Text Tools\ncase-converters|Case Converters",
                    ],
                    [
                        'group_label'         => 'Developer',
                        'group_columns_text'  => "developer|Developer\ndata-code-tools|Data & Code",
                    ],
                    [
                        'group_label'         => 'More',
                        'group_columns_text'  => "ciphers-encoding|Ciphers\ncalculators|Calculators\ngenerators|Generators\nfonts-text-styles|Fonts\nai-prompts|AI\nseo-web|SEO\ncheat-sheets|Cheat Sheets\nweb-css-tools|Web & CSS",
                    ],
                ],
                'title_field' => 'group_label',
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
                'default' => 'Browse all 207',
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

        /* --- Mega Menu Groups --- */
        $mega_groups = isset( $settings['mega_groups'] ) ? $settings['mega_groups'] : [];

        /* --- CTA --- */
        $cta_label = esc_html( $settings['cta_label'] );
        $cta_url   = $settings['cta_url']['url'] ? esc_url( $settings['cta_url']['url'] ) : '#tools';
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

                        <!-- Mega Menu Groups (each = one dropdown) -->
                        <?php foreach ( $mega_groups as $group ) :
                            $group_label     = ! empty( $group['group_label'] ) ? $group['group_label'] : 'Tools';
                            $group_cols      = $this->parse_columns_text( isset( $group['group_columns_text'] ) ? $group['group_columns_text'] : '' );
                            $group_foot_label = ! empty( $group['group_foot_label'] ) ? $group['group_foot_label'] : '';
                            $group_foot_url  = ! empty( $group['group_foot_url']['url'] ) ? $group['group_foot_url']['url'] : '#tools';
                        ?>
                            <div class="tctp-mega-wrap">
                                <button class="tctp-mega-trigger" aria-expanded="false">
                                    <?php echo esc_html( $group_label ); ?>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m6 9 6 6 6-6"/></svg>
                                </button>

                                <!-- Mega Menu Panel -->
                                <div class="tctp-mega" role="menu">
                                    <div class="tctp-mega-inner">
                                        <div class="tctp-mega-cols">
                                            <?php foreach ( $group_cols as $col ) :
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
                                                $col_title     = esc_html( $col_title );
                                                $item_count    = count( $menu_items );
                                                $preview_items = array_slice( $menu_items, 0, 10 );

                                                /* Map menu slug -> catalog category key for "View all" deep-link */
                                                $cat_key_map = array(
                                                    'pdf-tools'         => 'pdf',
                                                    'compression'       => 'compress',
                                                    'image-media'       => 'image',
                                                    'image-editing'     => 'image_edit',
                                                    'text-tools'        => 'text',
                                                    'case-converters'   => 'case',
                                                    'developer'         => 'dev',
                                                    'data-code-tools'   => 'dev_convert',
                                                    'ciphers-encoding'  => 'cipher',
                                                    'calculators'       => 'calc',
                                                    'generators'        => 'gen',
                                                    'fonts-text-styles' => 'fonts',
                                                    'ai-prompts'        => 'ai',
                                                    'seo-web'           => 'seo',
                                                    'cheat-sheets'      => 'cheat',
                                                    'web-css-tools'     => 'webdev',
                                                );
                                                $cat_key = isset( $cat_key_map[ $col['col_menu'] ] )
                                                    ? $cat_key_map[ $col['col_menu'] ]
                                                    : '';
                                                $view_all_url = ! empty( $cat_key )
                                                    ? home_url( '/#tools-' . rawurlencode( $cat_key ) )
                                                    : home_url( '/#tools' );
                                            ?>
                                                <div class="tctp-mcol">
                                                    <h5>
                                                        <?php echo $col_title; ?>
                                                        <i><?php echo $item_count; ?></i>
                                                    </h5>
                                                    <?php foreach ( $preview_items as $item ) : ?>
                                                        <a href="<?php echo esc_url( $item['url'] ); ?>">
                                                            <?php echo esc_html( $item['label'] ); ?>
                                                        </a>
                                                    <?php endforeach; ?>
                                                    <a class="tctp-mviewall" href="<?php echo esc_url( $view_all_url ); ?>">
                                                        View all <?php echo (int) $item_count; ?> &rarr;
                                                    </a>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                        <?php if ( ! empty( $group_foot_label ) ) : ?>
                                            <div class="tctp-mega-foot">
                                                <span></span>
                                                <a href="<?php echo esc_url( $group_foot_url ); ?>"><?php echo esc_html( $group_foot_label ); ?></a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
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
                        <# _.each( s.mega_groups, function( group, gi ) { #>
                            <div class="tctp-mega-wrap">
                                <button class="tctp-mega-trigger" aria-expanded="false">
                                    {{{ group.group_label || 'Tools' }}}
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m6 9 6 6 6-6"/></svg>
                                </button>
                                <div class="tctp-mega" role="menu">
                                    <div class="tctp-mega-inner">
                                        <div class="tctp-mega-cols">
                                            <# var _lines = ( group.group_columns_text || '' ).split(/\r?\n/); _.each( _lines, function( _ln ) { var _t = String(_ln||'').trim(); if(!_t) return; var _parts = _t.split('|'); var _title = (_parts[1]||'').trim() || (_parts[0]||'').trim(); #>
                                                <div class="tctp-mcol">
                                                    <h5>{{{ _title }}} <i>&nbsp;</i></h5>
                                                    <p style="font-size:13px;color:#8792a6;margin:0">Select a WP menu in column settings</p>
                                                </div>
                                            <# }); #>
                                        </div>
                                        <# if ( group.group_foot_label ) { #>
                                            <div class="tctp-mega-foot">
                                                <span></span>
                                                <a href="{{{ group.group_foot_url.url || '#tools' }}}">{{{ group.group_foot_label }}}</a>
                                            </div>
                                        <# } #>
                                    </div>
                                </div>
                            </div>
                        <# }); #>
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
