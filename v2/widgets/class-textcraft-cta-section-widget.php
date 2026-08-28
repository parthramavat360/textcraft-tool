<?php
/**
 * Ready-to-go CTA Section Elementor Widget
 *
 * Dark autofit band with title, buttons and a blur orb.
 * Pixel-perfect match to the textcraft-tool-page-full.html design.
 *
 * @package    TextCraftToolsPro
 * @subpackage Widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TextCraft_CTA_Section_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'tctp_cta_section';
	}

	public function get_title() {
		return __( 'TCTP: Ready-to-go CTA Section', 'textcrafttoolspro' );
	}

	public function get_icon() {
		return 'eicon-call-to-action';
	}

	public function get_categories() {
		return [ 'textcrafttools' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_cta', [
			'label' => __( 'Content', 'textcrafttoolspro' ),
		] );

		$this->add_control( 'title', [
			'label'   => __( 'Title', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Ready to compress your PDF?',
		] );
		$this->add_control( 'description', [
			'label'   => __( 'Description', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'rows'    => 2,
			'default' => 'Drop a file in the box above — the result is ready before you finish reading this. Free, private and instant.',
		] );
		$this->add_control( 'primary_text', [
			'label'   => __( 'Primary Button Text', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Compress my PDF',
		] );
		$this->add_control( 'primary_url', [
			'label'   => __( 'Primary Button Link', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::URL,
			'default' => [
				'url' => '#',
			],
		] );
		$this->add_control( 'secondary_text', [
			'label'   => __( 'Secondary Button Text', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'View all tools',
		] );
		$this->add_control( 'secondary_url', [
			'label'   => __( 'Secondary Button Link', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::URL,
			'default' => [
				'url' => home_url( '/tools/' ),
			],
		] );
		$this->add_control( 'note', [
			'label'   => __( 'Note (small print)', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'No account needed · Files never leave your device',
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'style_cta', [
			'label' => __( 'Style', 'textcrafttoolspro' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'band_bg', [
			'label'     => __( 'Band Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#0b1220',
			'selectors' => [
				'{{WRAPPER}} .tcs-cta-band' => 'background: {{VALUE}}',
			],
		] );
		$this->add_control( 'orb_color', [
			'label'     => __( 'Blur Orb Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => 'rgba(37,99,235,0.35)',
			'selectors' => [
				'{{WRAPPER}} .tcs-cta-band::after' => 'background: {{VALUE}}',
			],
		] );
		$this->add_control( 'title_color', [
			'label'     => __( 'Title Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [
				'{{WRAPPER}} .tcs-cta-band h2' => 'color: {{VALUE}}',
			],
		] );
		$this->add_control( 'desc_color', [
			'label'     => __( 'Description Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#b6c0d2',
			'selectors' => [
				'{{WRAPPER}} .tcs-cta-band p' => 'color: {{VALUE}}',
			],
		] );
		$this->add_control( 'primary_bg', [
			'label'     => __( 'Primary Button Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [
				'{{WRAPPER}} .tcs-btn-primary' => 'background: {{VALUE}}; border-color: {{VALUE}}',
			],
		] );
		$this->add_control( 'primary_color', [
			'label'     => __( 'Primary Button Text', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#0b1220',
			'selectors' => [
				'{{WRAPPER}} .tcs-btn-primary' => 'color: {{VALUE}}',
			],
		] );
		$this->add_control( 'primary_hover', [
			'label'     => __( 'Primary Button Hover Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#eaf0ff',
			'selectors' => [
				'{{WRAPPER}} .tcs-btn-primary:hover' => 'background: {{VALUE}}; border-color: {{VALUE}}',
			],
		] );
		$this->add_control( 'secondary_border', [
			'label'     => __( 'Secondary Button Border', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => 'rgba(255,255,255,0.35)',
			'selectors' => [
				'{{WRAPPER}} .tcs-btn-secondary' => 'border-color: {{VALUE}}',
			],
		] );
		$this->add_control( 'secondary_color', [
			'label'     => __( 'Secondary Button Text', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [
				'{{WRAPPER}} .tcs-btn-secondary' => 'color: {{VALUE}}',
			],
		] );
		$this->add_control( 'note_color', [
			'label'     => __( 'Note Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#94a3bb',
			'selectors' => [
				'{{WRAPPER}} .tcs-cta-note' => 'color: {{VALUE}}',
			],
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrap', 'class', 'tcs-section tcs-cta' );

		$this->add_render_attribute( 'primary', 'class', 'tcs-btn tcs-btn-primary' );
		if ( ! empty( $settings['primary_url']['url'] ) ) {
			$this->add_link_attributes( 'primary', $settings['primary_url'] );
		}

		$this->add_render_attribute( 'secondary', 'class', 'tcs-btn tcs-btn-secondary' );
		if ( ! empty( $settings['secondary_url']['url'] ) ) {
			$this->add_link_attributes( 'secondary', $settings['secondary_url'] );
		}
		?>
		<section <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
			<div class="tcs-wrap">
				<div class="tcs-cta-band">
					<div>
						<h2><?php echo esc_html( $settings['title'] ); ?></h2>
						<p><?php echo esc_html( $settings['description'] ); ?></p>
						<?php if ( ! empty( $settings['note'] ) ) : ?>
							<p class="tcs-cta-note"><?php echo esc_html( $settings['note'] ); ?></p>
						<?php endif; ?>
					</div>
					<div class="tcs-cta-actions">
						<?php if ( ! empty( $settings['primary_text'] ) ) : ?>
							<a <?php echo $this->get_render_attribute_string( 'primary' ); ?>><?php echo esc_html( $settings['primary_text'] ); ?></a>
						<?php endif; ?>
						<?php if ( ! empty( $settings['secondary_text'] ) ) : ?>
							<a <?php echo $this->get_render_attribute_string( 'secondary' ); ?>><?php echo esc_html( $settings['secondary_text'] ); ?></a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}

	protected function content_template() {}
}
