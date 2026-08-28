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

		$this->start_controls_section( 'style_who', [
			'label' => __( 'Style', 'textcrafttoolspro' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'section_alt', [
			'label'     => __( 'Alternate Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#f6f8fb',
			'selectors' => [
				'{{WRAPPER}} .tcs-section' => 'background: {{VALUE}}',
			],
		] );
		$this->add_control( 'section_border', [
			'label'     => __( 'Section Border', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#e4e9f0',
			'selectors' => [
				'{{WRAPPER}} .tcs-section' => 'border-color: {{VALUE}}',
			],
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
			'default'   => '#2563eb',
			'selectors' => [
				'{{WRAPPER}} .tcs-kicker' => 'color: {{VALUE}}',
			],
		] );
		$this->add_control( 'title_color', [
			'label'     => __( 'Title Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#0b1220',
			'selectors' => [
				'{{WRAPPER}} .tcs-shead h2' => 'color: {{VALUE}}',
			],
		] );
		$this->add_control( 'desc_color', [
			'label'     => __( 'Description Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#4a5568',
			'selectors' => [
				'{{WRAPPER}} .tcs-shead p' => 'color: {{VALUE}}',
			],
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
		$this->add_control( 'who_title_color', [
			'label'     => __( 'Card Title Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#0b1220',
			'selectors' => [
				'{{WRAPPER}} .tcs-who h3' => 'color: {{VALUE}}',
			],
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
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrap', 'class', 'tcs-section is-alt tcs-who' );
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
