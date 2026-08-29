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

		$this->start_controls_section( 'style_section', [
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
		$this->add_control( 'section_bg', [
			'label'     => __( 'Background Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#ffffff',
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
		$this->add_control( 'section_maxwidth', [
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

		$this->start_controls_section( 'style_band', [
			'label' => __( 'Band', 'textcrafttoolspro' ),
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
		$this->add_control( 'band_radius', [
			'label'      => __( 'Band Radius', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
			'default'    => [ 'size' => 24, 'unit' => 'px' ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-cta-band' => 'border-radius: {{SIZE}}{{UNIT}}',
			],
		] );
		$this->add_responsive_control( 'band_padding', [
			'label'      => __( 'Band Padding', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em' ],
			'default'    => [ 'top' => '56', 'right' => '48', 'bottom' => '56', 'left' => '48', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-cta-band' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );
		$this->add_responsive_control( 'band_gap', [
			'label'      => __( 'Band Columns Gap', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
			'default'    => [ 'size' => 32, 'unit' => 'px' ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-cta-band' => 'gap: {{SIZE}}{{UNIT}}',
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
		$this->add_control( 'orb_size', [
			'label'      => __( 'Blur Orb Size', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 600 ] ],
			'default'    => [ 'size' => 320, 'unit' => 'px' ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-cta-band::after' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
			],
		] );
		$this->add_control( 'orb_blur', [
			'label'      => __( 'Blur Orb Blur', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
			'default'    => [ 'size' => 20, 'unit' => 'px' ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-cta-band::after' => 'filter: blur({{SIZE}}{{UNIT}})',
			],
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'style_text', [
			'label' => __( 'Text', 'textcrafttoolspro' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
			'name'     => 'title_typography',
			'label'    => __( 'Title Typography', 'textcrafttoolspro' ),
			'selector' => '{{WRAPPER}} .tcs-cta-band h2',
		] );
		$this->add_control( 'title_color', [
			'label'     => __( 'Title Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [
				'{{WRAPPER}} .tcs-cta-band h2' => 'color: {{VALUE}}',
			],
		] );
		$this->add_responsive_control( 'title_margin', [
			'label'      => __( 'Title Margin', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em' ],
			'default'    => [ 'top' => '0', 'right' => '0', 'bottom' => '10', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-cta-band h2' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
			'name'     => 'desc_typography',
			'label'    => __( 'Description Typography', 'textcrafttoolspro' ),
			'selector' => '{{WRAPPER}} .tcs-cta-band > div > p:not(.tcs-cta-note)',
		] );
		$this->add_control( 'desc_color', [
			'label'     => __( 'Description Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#b6c0d2',
			'selectors' => [
				'{{WRAPPER}} .tcs-cta-band p' => 'color: {{VALUE}}',
			],
		] );
		$this->add_responsive_control( 'desc_margin', [
			'label'      => __( 'Description Margin', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em' ],
			'default'    => [ 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => true ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-cta-band > div > p:not(.tcs-cta-note)' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
			'name'     => 'note_typography',
			'label'    => __( 'Note Typography', 'textcrafttoolspro' ),
			'selector' => '{{WRAPPER}} .tcs-cta-note',
		] );
		$this->add_control( 'note_color', [
			'label'     => __( 'Note Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#94a3bb',
			'selectors' => [
				'{{WRAPPER}} .tcs-cta-note' => 'color: {{VALUE}}',
			],
		] );
		$this->add_responsive_control( 'note_margin', [
			'label'      => __( 'Note Margin', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em' ],
			'default'    => [ 'top' => '16', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-cta-note' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'style_actions', [
			'label' => __( 'Buttons', 'textcrafttoolspro' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control( 'actions_gap', [
			'label'      => __( 'Buttons Gap', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
			'default'    => [ 'size' => 12, 'unit' => 'px' ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-cta-actions' => 'gap: {{SIZE}}{{UNIT}}',
			],
		] );
		$this->add_control( 'btn_height', [
			'label'      => __( 'Button Height', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 32, 'max' => 80 ] ],
			'default'    => [ 'size' => 48, 'unit' => 'px' ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-cta-actions .tcs-btn' => 'height: {{SIZE}}{{UNIT}}; padding: 0 22px;',
			],
		] );
		$this->add_control( 'btn_radius', [
			'label'      => __( 'Button Radius', 'textcrafttoolspro' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
			'default'    => [ 'size' => 10, 'unit' => 'px' ],
			'selectors'  => [
				'{{WRAPPER}} .tcs-btn' => 'border-radius: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->add_control( 'heading_primary', [
			'label' => __( 'Primary Button', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::HEADING,
			'separator' => 'before',
		] );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
			'name'     => 'primary_typography',
			'label'    => __( 'Primary Typography', 'textcrafttoolspro' ),
			'selector' => '{{WRAPPER}} .tcs-btn-primary',
		] );
		$this->add_control( 'primary_bg', [
			'label'     => __( 'Primary Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [
				'{{WRAPPER}} .tcs-btn-primary' => 'background: {{VALUE}}; border-color: {{VALUE}}',
			],
		] );
		$this->add_control( 'primary_color', [
			'label'     => __( 'Primary Text', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#0b1220',
			'selectors' => [
				'{{WRAPPER}} .tcs-btn-primary' => 'color: {{VALUE}}',
			],
		] );
		$this->add_control( 'primary_hover', [
			'label'     => __( 'Primary Hover Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#eaf0ff',
			'selectors' => [
				'{{WRAPPER}} .tcs-btn-primary:hover' => 'background: {{VALUE}}; border-color: {{VALUE}}',
			],
		] );

		$this->add_control( 'heading_secondary', [
			'label'     => __( 'Secondary Button', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::HEADING,
			'separator' => 'before',
		] );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
			'name'     => 'secondary_typography',
			'label'    => __( 'Secondary Typography', 'textcrafttoolspro' ),
			'selector' => '{{WRAPPER}} .tcs-btn-secondary',
		] );
		$this->add_control( 'secondary_bg', [
			'label'     => __( 'Secondary Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => 'rgba(255,255,255,0)', // transparent
			'selectors' => [
				'{{WRAPPER}} .tcs-btn-secondary' => 'background: {{VALUE}}',
			],
		] );
		$this->add_control( 'secondary_border', [
			'label'     => __( 'Secondary Border', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => 'rgba(255,255,255,0.35)',
			'selectors' => [
				'{{WRAPPER}} .tcs-btn-secondary' => 'border-color: {{VALUE}}',
			],
		] );
		$this->add_control( 'secondary_color', [
			'label'     => __( 'Secondary Text', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [
				'{{WRAPPER}} .tcs-btn-secondary' => 'color: {{VALUE}}',
			],
		] );
		$this->add_control( 'secondary_hover_border', [
			'label'     => __( 'Secondary Hover Border', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [
				'{{WRAPPER}} .tcs-btn-secondary:hover' => 'border-color: {{VALUE}}',
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
