<?php
/**
 * Benefits Section Elementor Widget
 *
 * "Everything you need, nothing you don't" — ben-grid of feature cards.
 * Pixel-perfect match to the textcraft-tool-page-full.html design.
 *
 * @package    TextCraftToolsPro
 * @subpackage Widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TextCraft_Benefits_Section_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'tctp_benefits_section';
	}

	public function get_title() {
		return __( 'TCTP: Benefits Section', 'textcrafttoolspro' );
	}

	public function get_icon() {
		return 'eicon-pojome';
	}

	public function get_categories() {
		return [ 'textcrafttools' ];
	}

	private function tctp_add_section_controls() {
		/* Kicker */
		$this->add_control( 'kicker', [
			'label'   => __( 'Kicker Label', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Benefits',
		] );

		/* Title */
		$this->add_control( 'title', [
			'label'   => __( 'Title', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => "Everything you need, nothing you don't",
		] );

		/* Description */
		$this->add_control( 'description', [
			'label'   => __( 'Description', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'rows'    => 2,
			'default' => 'A focused workspace built around one job — done fast, done privately, and done without a single upload.',
		] );
	}

	private function tctp_add_benefits_repeater() {
		$repeater = new \Elementor\Repeater();

		$repeater->add_control( 'icon', [
			'label' => __( 'Icon', 'textcrafttoolspro' ),
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

		$this->add_control( 'benefits', [
			'label'       => __( 'Benefit Items', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => [
				[ 'icon' => '⚡', 'title' => 'Instant results', 'description' => 'Processing starts the moment your file lands. Most documents finish in under a second, with no queue and no waiting room.' ],
				[ 'icon' => '🔒', 'title' => 'Complete privacy', 'description' => 'Your file never leaves the tab. Nothing is uploaded, cached on a server, or logged anywhere.' ],
				[ 'icon' => '🎯', 'title' => 'Accurate output', 'description' => 'Formatting, fonts, links and structure stay exactly as you left them — only the excess weight is removed.' ],
				[ 'icon' => '∞', 'title' => 'No limits', 'description' => 'No daily quota, no file caps that force an upgrade, no watermark stamped on your work.' ],
				[ 'icon' => '📱', 'title' => 'Works everywhere', 'description' => 'Desktop, tablet or phone — the same tool, the same result, in any modern browser.' ],
				[ 'icon' => '🆓', 'title' => 'Free, no signup', 'description' => 'Open the page and start working. No account, no email, no credit card, ever.' ],
			],
			'title_field' => 'title',
		] );
	}

	private function tctp_section_style_controls() {
		$this->add_control( 'section_bg', [
			'label'     => __( 'Section Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#ffffff',
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
				'{{WRAPPER}} .tcs-ben' => 'background: {{VALUE}}',
			],
		] );
		$this->add_control( 'card_border', [
			'label'     => __( 'Card Border', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#e4e9f0',
			'selectors' => [
				'{{WRAPPER}} .tcs-ben' => 'border-color: {{VALUE}}',
			],
		] );
		$this->add_control( 'icon_bg', [
			'label'     => __( 'Icon Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#eaf0ff',
			'selectors' => [
				'{{WRAPPER}} .tcs-ben-ic' => 'background: {{VALUE}}',
			],
		] );
		$this->add_control( 'icon_color', [
			'label'     => __( 'Icon Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#2563eb',
			'selectors' => [
				'{{WRAPPER}} .tcs-ben-ic' => 'color: {{VALUE}}',
			],
		] );
		$this->add_control( 'ben_title_color', [
			'label'     => __( 'Card Title Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#0b1220',
			'selectors' => [
				'{{WRAPPER}} .tcs-ben h3' => 'color: {{VALUE}}',
			],
		] );
		$this->add_control( 'ben_desc_color', [
			'label'     => __( 'Card Text Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#4a5568',
			'selectors' => [
				'{{WRAPPER}} .tcs-ben p' => 'color: {{VALUE}}',
			],
		] );
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_benefits', [
			'label' => __( 'Content', 'textcrafttoolspro' ),
		] );
		$this->tctp_add_section_controls();
		$this->tctp_add_benefits_repeater();
		$this->end_controls_section();

		$this->start_controls_section( 'style_benefits', [
			'label' => __( 'Style', 'textcrafttoolspro' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );
		$this->tctp_section_style_controls();
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrap', 'class', 'tcs-section tcs-benefits' );
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
				<div class="tcs-ben-grid">
					<?php foreach ( $settings['benefits'] as $ben ) : ?>
						<div class="tcs-ben">
							<?php if ( ! empty( $ben['icon'] ) ) : ?>
								<div class="tcs-ben-ic"><?php echo esc_html( $ben['icon'] ); ?></div>
							<?php endif; ?>
							<h3><?php echo esc_html( $ben['title'] ); ?></h3>
							<p><?php echo esc_html( $ben['description'] ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}

	protected function content_template() {}
}
