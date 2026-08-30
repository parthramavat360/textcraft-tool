<?php
/**
 * Who It's For Section Elementor Widget
 *
 * "Built for people who work with files all day" — who-grid of audience cards.
 * Pixel-perfect match to the textcraft-tool-page-full.html design.
 *
 * @package    TextCraftToolsPro
 * @subpackage Widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TextCraft_Who_Section_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'tctp_who_section';
	}

	public function get_title() {
		return __( "TCTP: Who It's For Section", 'textcrafttoolspro' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	public function get_categories() {
		return [ 'textcrafttools' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_who', [
			'label' => __( 'Content', 'textcrafttoolspro' ),
		] );

		$this->add_control( 'kicker', [
			'label'   => __( 'Kicker Label', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => "Who it's for",
		] );
		$this->add_control( 'title', [
			'label'   => __( 'Title', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Built for people who work with files all day',
		] );
		$this->add_control( 'description', [
			'label'   => __( 'Description', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'rows'    => 2,
			'default' => 'From single-page forms to 200-page reports, the same workflow fits every kind of user.',
		] );

		$repeater = new \Elementor\Repeater();
		$repeater->add_control( 'avatar', [
			'label' => __( 'Avatar Initials', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::TEXT,
		] );
		$repeater->add_control( 'title', [
			'label' => __( 'Title', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::TEXT,
		] );
		$repeater->add_control( 'description', [
			'label' => __( 'Description', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::TEXTAREA,
			'rows'  => 3,
		] );

		$this->add_control( 'who', [
			'label'       => __( 'Audience Items', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => [
				[ 'avatar' => 'BP', 'title' => 'Business professionals', 'description' => 'Fit reports, decks and proposals inside strict email attachment limits without losing quality.' ],
				[ 'avatar' => 'ST', 'title' => 'Students & academics', 'description' => 'Meet university upload requirements for theses, assignments and research papers in seconds.' ],
				[ 'avatar' => 'LG', 'title' => 'Legal & admin teams', 'description' => 'Handle confidential case files and scanned forms without sending anything to a third party.' ],
				[ 'avatar' => 'DV', 'title' => 'Developers & writers', 'description' => 'A dependable utility for quick, repeatable file work between the real tasks on your list.' ],
			],
			'title_field' => 'title',
		] );

		$this->end_controls_section();

		/* ─── Section ★ ─────────────────────────────────────────── */
		$this->start_controls_section( 'style_who', [
			'label' => __( 'Section', 'textcrafttoolspro' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'section_bg_type', [
			'label'   => __( 'Background Type', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::CHOOSE,
			'options' => [
				'color' => [ 'title' => __( 'Color', 'textcrafttoolspro' ), 'icon' => 'eicon-paint-brush' ],
				'none'  => [ 'title' => __( 'None', 'textcrafttoolspro' ), 'icon' => 'eicon-ban' ],
			],
			'default' => 'color',
		] );
		$this->add_control( 'section_alt', [
			'label'     => __( 'Background Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#f6f8fb',
			'condition' => [ 'section_bg_type' => 'color' ],
			'selectors' => [
				'{{WRAPPER}} .tcs-section' => 'background: {{VALUE}}',
			],
		] );
		$this->add_control( 'section_border', [
			'label'     => __( 'Top Border Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#e4e9f0',
			'selectors' => [
				'{{WRAPPER}} .tcs-section' => 'border-color: {{VALUE}}',
			],
		] );
		$this->add_control( 'section_border_width', [
			'label'      => __( 'Top Border Width', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 10 ] ],
			'default'    => [ 'size' => 1, 'unit' => 'px' ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-section' => 'border-top-width: {{SIZE}}{{UNIT}}',
			],
		] );
		$this->add_responsive_control( 'section_padding', [
			'label'      => __( 'Section Padding', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'default'    => [ 'top' => '72', 'right' => '0', 'bottom' => '72', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-section' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );
		$this->add_responsive_control( 'section_gap', [
			'label'      => __( 'Content Width (max)', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 800, 'max' => 1600 ] ],
			'default'    => [ 'size' => 1200, 'unit' => 'px' ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-wrap' => 'max-width: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->end_controls_section();

		/* ─── Section Heading ★ ─────────────────────────────────── */
		$this->start_controls_section( 'style_heading', [
			'label' => __( 'Heading', 'textcrafttoolspro' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
			'name'     => 'kicker_typography',
			'label'    => __( 'Kicker Typography', 'textcrafttoolspro' ),
			'selector' => '{{WRAPPER}} .tcs-kicker',
		] );
		$this->add_control( 'kicker_bg', [
			'label'     => __( 'Kicker Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#eaf0ff',
			'selectors' => [
				'{{WRAPPER}} .tcs-kicker' => 'background: {{VALUE}}',
			],
		] );
		$this->add_control( 'kicker_color', [
			'label'     => __( 'Kicker Text', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#0b1220',
			'selectors' => [
				'{{WRAPPER}} .tcs-kicker' => 'color: {{VALUE}}',
			],
		] );
		$this->add_control( 'kicker_radius', [
			'label'      => __( 'Kicker Radius', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
			'default'    => [ 'size' => 100, 'unit' => 'px' ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-kicker' => 'border-radius: {{SIZE}}{{UNIT}}',
			],
		] );
		$this->add_control( 'heading_radius', [
			'label'      => __( 'Kicker Padding', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em' ],
			'default'    => [ 'top' => '5', 'right' => '12', 'bottom' => '5', 'left' => '12', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-kicker' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
			'name'     => 'title_typography',
			'label'    => __( 'Title Typography', 'textcrafttoolspro' ),
			'selector' => '{{WRAPPER}} .tcs-shead h2',
		] );
		$this->add_control( 'title_color', [
			'label'     => __( 'Title Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#0b1220',
			'selectors' => [
				'{{WRAPPER}} .tcs-shead h2' => 'color: {{VALUE}}',
			],
		] );
		$this->add_responsive_control( 'title_margin', [
			'label'      => __( 'Title Margin', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em' ],
			'default'    => [ 'top' => '16', 'right' => '0', 'bottom' => '12', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-shead h2' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
			'name'     => 'desc_typography',
			'label'    => __( 'Description Typography', 'textcrafttoolspro' ),
			'selector' => '{{WRAPPER}} .tcs-shead p',
		] );
		$this->add_control( 'desc_color', [
			'label'     => __( 'Description Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#4a5568',
			'selectors' => [
				'{{WRAPPER}} .tcs-shead p' => 'color: {{VALUE}}',
			],
		] );
		$this->add_responsive_control( 'heading_spacing', [
			'label'      => __( 'Heading Bottom Spacing', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
			'default'    => [ 'size' => 34, 'unit' => 'px' ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-shead' => 'margin-bottom: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->end_controls_section();

		/* ─── Grid ★ ─────────────────────────────────────────────── */
		$this->start_controls_section( 'style_grid', [
			'label' => __( 'Grid', 'textcrafttoolspro' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'grid_columns', [
			'label'      => __( 'Columns (Desktop)', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 1, 'max' => 6 ] ],
			'default'    => [ 'size' => 4, 'unit' => 'px' ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-who-grid' => 'grid-template-columns: repeat({{SIZE}}, 1fr)',
			],
		] );
		$this->add_responsive_control( 'grid_gap', [
			'label'      => __( 'Columns Gap', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
			'default'    => [ 'size' => 16, 'unit' => 'px' ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-who-grid' => 'gap: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->end_controls_section();

		/* ─── Cards ★ ─────────────────────────────────────────────── */
		$this->start_controls_section( 'style_cards', [
			'label' => __( 'Cards', 'textcrafttoolspro' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'card_bg', [
			'label'     => __( 'Card Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [
				'{{WRAPPER}} .tcs-who' => 'background: {{VALUE}}',
			],
		] );
		$this->add_control( 'card_border', [
			'label'     => __( 'Card Border', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#e4e9f0',
			'selectors' => [
				'{{WRAPPER}} .tcs-who' => 'border-color: {{VALUE}}',
			],
		] );
		$this->add_control( 'card_radius', [
			'label'      => __( 'Card Radius', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
			'default'    => [ 'size' => 16, 'unit' => 'px' ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-who' => 'border-radius: {{SIZE}}{{UNIT}}',
			],
		] );
		$this->add_responsive_control( 'card_padding', [
			'label'      => __( 'Card Padding', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em' ],
			'default'    => [ 'top' => '22', 'right' => '22', 'bottom' => '22', 'left' => '22', 'unit' => 'px', 'isLinked' => true ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-who' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
			'name'     => 'who_title_typography',
			'label'    => __( 'Card Title Typography', 'textcrafttoolspro' ),
			'selector' => '{{WRAPPER}} .tcs-who h3',
		] );
		$this->add_control( 'who_title_color', [
			'label'     => __( 'Card Title Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#0b1220',
			'selectors' => [
				'{{WRAPPER}} .tcs-who h3' => 'color: {{VALUE}}',
			],
		] );
		$this->add_responsive_control( 'who_title_margin', [
			'label'      => __( 'Card Title Margin', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em' ],
			'default'    => [ 'top' => '0', 'right' => '0', 'bottom' => '6', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-who h3' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
			'name'     => 'who_desc_typography',
			'label'    => __( 'Card Text Typography', 'textcrafttoolspro' ),
			'selector' => '{{WRAPPER}} .tcs-who p',
		] );
		$this->add_control( 'who_desc_color', [
			'label'     => __( 'Card Text Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#4a5568',
			'selectors' => [
				'{{WRAPPER}} .tcs-who p' => 'color: {{VALUE}}',
			],
		] );

		$this->end_controls_section();

		/* ─── Avatar ★ ────────────────────────────────────────────── */
		$this->start_controls_section( 'style_avatar', [
			'label' => __( 'Avatar', 'textcrafttoolspro' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control( 'avatar_size', [
			'label'      => __( 'Avatar Size', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 20, 'max' => 100 ] ],
			'default'    => [ 'size' => 38, 'unit' => 'px' ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-who-av' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
			],
		] );
		$this->add_control( 'avatar_radius', [
			'label'      => __( 'Avatar Radius', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ], '%' => [ 'min' => 0, 'max' => 50 ] ],
			'default'    => [ 'size' => 50, 'unit' => '%' ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-who-av' => 'border-radius: {{SIZE}}{{UNIT}}',
			],
		] );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
			'name'     => 'avatar_typography',
			'label'    => __( 'Avatar Typography', 'textcrafttoolspro' ),
			'selector' => '{{WRAPPER}} .tcs-who-av',
		] );
		$this->add_control( 'avatar_bg', [
			'label'     => __( 'Avatar Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#0b1220',
			'selectors' => [
				'{{WRAPPER}} .tcs-who-av' => 'background: {{VALUE}}',
			],
		] );
		$this->add_control( 'avatar_color', [
			'label'     => __( 'Avatar Text', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [
				'{{WRAPPER}} .tcs-who-av' => 'color: {{VALUE}}',
			],
		] );
		$this->add_responsive_control( 'avatar_margin', [
			'label'      => __( 'Avatar Margin', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em' ],
			'default'    => [ 'top' => '0', 'right' => '0', 'bottom' => '12', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-who-av' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrap', 'class', 'tcs-section is-alt' );
		?>
		<section <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
			<div class="tcs-wrap">
				<div class="tcs-shead">
					<?php if ( ! empty( $settings['kicker'] ) ) : ?>
						<span class="tcs-kicker"><?php echo esc_html( $settings['kicker'] ); ?></span>
					<?php endif; ?>
					<?php if ( ! empty( $settings['title'] ) ) : ?>
						<h2><?php echo esc_html( $settings['title'] ); ?></h2>
					<?php endif; ?>
					<?php if ( ! empty( $settings['description'] ) ) : ?>
						<p><?php echo esc_html( $settings['description'] ); ?></p>
					<?php endif; ?>
				</div>
				<div class="tcs-who-grid">
					<?php foreach ( $settings['who'] as $item ) : ?>
						<div class="tcs-who">
							<?php if ( ! empty( $item['avatar'] ) ) : ?>
								<div class="tcs-who-av"><?php echo esc_html( $item['avatar'] ); ?></div>
							<?php endif; ?>
							<h3><?php echo esc_html( $item['title'] ); ?></h3>
							<p><?php echo esc_html( $item['description'] ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}

	protected function content_template() {}
}
