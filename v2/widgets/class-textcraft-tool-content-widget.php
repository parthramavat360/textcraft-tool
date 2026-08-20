<?php
/**
 * Tool Content Elementor Widget
 *
 * Prose (steps, features, about, who, FAQ) + sidebar (related tools, on-page nav, CTA).
 *
 * @package TextCraftToolsPro
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class TextCraft_Tool_Content_Widget extends \Elementor\Widget_Base {

    public function get_name()     { return 'tctp_tool_content'; }
    public function get_title()    { return __( 'Tool Content', 'textcrafttoolspro' ); }
    public function get_icon()     { return 'eicon-text-area'; }
    public function get_categories() { return [ 'textcrafttools' ]; }

    protected function register_controls() {

        /* === How It Works === */
        $this->start_controls_section( 'steps', [ 'label' => __( 'How It Works', 'textcrafttoolspro' ) ] );
        $step_rep = new \Elementor\Repeater();
        $step_rep->add_control( 'step_title', [ 'label' => __( 'Title', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::TEXT ] );
        $step_rep->add_control( 'step_desc', [ 'label' => __( 'Description', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'rows' => 2 ] );
        $this->add_control( 'steps_items', [
            'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $step_rep->get_controls(),
            'default' => [
                [ 'step_title' => 'Step 1', 'step_desc' => 'Description here.' ],
                [ 'step_title' => 'Step 2', 'step_desc' => 'Description here.' ],
                [ 'step_title' => 'Step 3', 'step_desc' => 'Description here.' ],
            ],
            'title_field' => 'step_title',
        ] );
        $this->end_controls_section();

        /* === Key Features === */
        $this->start_controls_section( 'features', [ 'label' => __( 'Key Features', 'textcrafttoolspro' ) ] );
        $feat_rep = new \Elementor\Repeater();
        $feat_rep->add_control( 'feat_icon', [ 'label' => __( 'Icon', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '⚡' ] );
        $feat_rep->add_control( 'feat_title', [ 'label' => __( 'Title', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::TEXT ] );
        $feat_rep->add_control( 'feat_desc', [ 'label' => __( 'Description', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'rows' => 2 ] );
        $this->add_control( 'features_items', [
            'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $feat_rep->get_controls(),
            'default' => [
                [ 'feat_icon' => '⚡', 'feat_title' => 'Feature 1', 'feat_desc' => 'Description.' ],
                [ 'feat_icon' => '🔒', 'feat_title' => 'Feature 2', 'feat_desc' => 'Description.' ],
            ],
            'title_field' => 'feat_title',
        ] );
        $this->end_controls_section();

        /* === About === */
        $this->start_controls_section( 'about', [ 'label' => __( 'About This Tool', 'textcrafttoolspro' ) ] );
        $this->add_control( 'about_text', [
            'label' => __( 'About Text', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'rows' => 4,
        ] );
        $this->end_controls_section();

        /* === Who It's For === */
        $this->start_controls_section( 'who', [ 'label' => __( 'Who It\'s For', 'textcrafttoolspro' ) ] );
        $who_rep = new \Elementor\Repeater();
        $who_rep->add_control( 'who_title', [ 'label' => __( 'Title', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::TEXT ] );
        $who_rep->add_control( 'who_desc', [ 'label' => __( 'Description', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'rows' => 2 ] );
        $this->add_control( 'who_items', [
            'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $who_rep->get_controls(),
            'title_field' => 'who_title',
        ] );
        $this->end_controls_section();

        /* === FAQ === */
        $this->start_controls_section( 'faq', [ 'label' => __( 'FAQ', 'textcrafttoolspro' ) ] );
        $faq_rep = new \Elementor\Repeater();
        $faq_rep->add_control( 'faq_q', [ 'label' => __( 'Question', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::TEXT ] );
        $faq_rep->add_control( 'faq_a', [ 'label' => __( 'Answer', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'rows' => 3 ] );
        $faq_rep->add_control( 'faq_open', [ 'label' => __( 'Open by default?', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::SWITCHER ] );
        $this->add_control( 'faq_items', [
            'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $faq_rep->get_controls(),
            'title_field' => 'faq_q',
        ] );
        $this->end_controls_section();

        /* === Related Tools === */
        $this->start_controls_section( 'related', [ 'label' => __( 'Sidebar — Related Tools', 'textcrafttoolspro' ) ] );
        $rel_rep = new \Elementor\Repeater();
        $rel_rep->add_control( 'rel_name', [ 'label' => __( 'Name', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::TEXT ] );
        $rel_rep->add_control( 'rel_url', [ 'label' => __( 'URL', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::URL ] );
        $this->add_control( 'related_items', [
            'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $rel_rep->get_controls(),
            'title_field' => 'rel_name',
        ] );
        $this->add_control( 'sidebar_heading', [
            'label' => __( 'Sidebar Heading', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '74 tools',
        ] );
        $this->add_control( 'sidebar_desc', [
            'label' => __( 'Sidebar Description', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::TEXTAREA,
            'default' => 'PDF, image, text, SEO and developer utilities — all free and browser-based.',
        ] );
        $this->end_controls_section();

        /* === Style === */
        $this->start_controls_section( 'style', [ 'label' => __( 'Style', 'textcrafttoolspro' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ] );
        $this->add_control( 'h2_color', [
            'label' => __( 'H2 Color', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#0b1220',
            'selectors' => [ '{{SELECTOR}} .tctp-prose h2' => 'color: {{VALUE}}' ],
        ] );
        $this->add_control( 'body_color', [
            'label' => __( 'Body Color', 'textcrafttoolspro' ), 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#4a5568',
            'selectors' => [ '{{SELECTOR}} .tctp-prose p, {{SELECTOR}} .tctp-prose li' => 'color: {{VALUE}}' ],
        ] );
        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();
        $home = home_url( '/' );
        ?>
        <div class="tctp-tc">
            <div class="tctp-tc-inner">
                <!-- Prose -->
                <div class="tctp-prose">
                    <?php if ( ! empty( $s['steps_items'] ) ) : ?>
                        <h2>How it works</h2>
                        <div class="tctp-steps">
                            <?php $i = 1; foreach ( $s['steps_items'] as $step ) : ?>
                                <div class="tctp-step">
                                    <b><?php echo $i++; ?></b>
                                    <h4><?php echo esc_html( $step['step_title'] ); ?></h4>
                                    <p><?php echo esc_html( $step['step_desc'] ); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( ! empty( $s['features_items'] ) ) : ?>
                        <h2>Key features</h2>
                        <div class="tctp-feat">
                            <?php foreach ( $s['features_items'] as $f ) : ?>
                                <div class="tctp-fcard">
                                    <div class="tctp-fcard-ic"><?php echo esc_html( $f['feat_icon'] ); ?></div>
                                    <h4><?php echo esc_html( $f['feat_title'] ); ?></h4>
                                    <p><?php echo esc_html( $f['feat_desc'] ); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( ! empty( $s['about_text'] ) ) : ?>
                        <h2>About this tool</h2>
                        <?php echo wp_kses_post( wpautop( $s['about_text'] ) ); ?>
                    <?php endif; ?>

                    <?php if ( ! empty( $s['who_items'] ) ) : ?>
                        <h2>Who it's for</h2>
                        <ul class="tctp-bullets">
                            <?php foreach ( $s['who_items'] as $w ) : ?>
                                <li><strong><?php echo esc_html( $w['who_title'] ); ?></strong> — <?php echo esc_html( $w['who_desc'] ); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <?php if ( ! empty( $s['faq_items'] ) ) : ?>
                        <h2>Frequently asked questions</h2>
                        <div class="tctp-faq">
                            <?php foreach ( $s['faq_items'] as $faq ) :
                                $open = ! empty( $faq['faq_open'] ) ? ' open' : '';
                            ?>
                                <details<?php echo $open; ?>>
                                    <summary><?php echo esc_html( $faq['faq_q'] ); ?></summary>
                                    <p><?php echo esc_html( $faq['faq_a'] ); ?></p>
                                </details>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <aside class="tctp-aside">
                    <?php if ( ! empty( $s['related_items'] ) ) : ?>
                        <div class="tctp-box">
                            <h4>Related tools</h4>
                            <?php foreach ( $s['related_items'] as $r ) :
                                $url = isset( $r['rel_url']['url'] ) ? $r['rel_url']['url'] : '#';
                            ?>
                                <a class="tctp-rel" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $r['rel_name'] ); ?><i>→</i></a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="tctp-box tctp-cta">
                        <h4><?php echo esc_html( $s['sidebar_heading'] ); ?></h4>
                        <p><?php echo esc_html( $s['sidebar_desc'] ); ?></p>
                        <a class="tctp-btn" href="<?php echo esc_url( $home ); ?>">Browse all tools</a>
                    </div>
                </aside>
            </div>
        </div>
        <?php
    }

    protected function content_template() {}
}
