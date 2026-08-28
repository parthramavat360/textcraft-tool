<?php
/**
 * Why Choose This Tool Section Elementor Widget
 *
 * "Faster than the alternatives, safer by design" — why-list + why-metrics.
 * Pixel-perfect match to the textcraft-tool-page-full.html design.
 *
 * @package    TextCraftToolsPro
 * @subpackage Widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TextCraft_Why_Section_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'tctp_why_section';
	}

	public function get_title() {
		return __( 'TCTP: Why Choose Section', 'textcrafttoolspro' );
	}

	public function get_icon() {
		return 'eicon-help';
	}

	public function get_categories() {
		return [ 'textcrafttools' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_why', [
			'label' => __( 'Content', 'textcrafttoolspro' ),
		] );

		$this->add_control( 'kicker', [
			'label'   => __( 'Kicker Label', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Why choose this tool',
		] );
		$this->add_control( 'title', [
			'label'   => __( 'Title', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Faster than the alternatives, safer by design',
		] );
		$this->add_control( 'description', [
			'label'   => __( 'Description', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'rows'    => 2,
			'default' => 'Most online tools upload your file to a server, queue it, then hand it back with a watermark. This one does not.',
		] );

		/* Why list */
		$list = new \Elementor\Repeater();
		$list->add_control( 'title', [
			'label' => __( 'Title', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::TEXT,
		] );
		$list->add_control( 'description', [
			'label' => __( 'Description', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::TEXTAREA,
			'rows'  => 2,
		] );

		$this->add_control( 'list', [
			'label'       => __( 'Why Items', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $list->get_controls(),
			'default'     => [
				[ 'title' => 'Local-first processing', 'description' => 'Everything runs in your browser using modern web APIs — your data stays on your device from start to finish.' ],
				[ 'title' => 'No hidden paywall', 'description' => 'Every feature is available on the first visit. There is no pro tier waiting behind the download button.' ],
				[ 'title' => 'Predictable quality', 'description' => 'Clear controls with live estimates, so you decide the trade-off instead of guessing at a single fixed preset.' ],
				[ 'title' => 'Lightweight pages', 'description' => 'Each tool ships as its own small page — no heavy dashboard to load before you can get to work.' ],
			],
			'title_field' => 'title',
		] );

		/* Metrics */
		$metric = new \Elementor\Repeater();
		$metric->add_control( 'value', [
			'label' => __( 'Value (e.g. <1s, 0, 74 tools)', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::TEXT,
		] );
		$metric->add_control( 'label', [
			'label' => __( 'Label', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::TEXTAREA,
			'rows'  => 2,
		] );
		$metric->add_control( 'dark', [
			'label'        => __( 'Dark (full-width) card', 'textcrafttoolspro' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'default'      => '',
			'label_on'     => 'Yes',
			'label_off'    => 'No',
		] );

		$this->add_control( 'metrics', [
			'label'       => __( 'Metric Cards', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $metric->get_controls(),
			'default'     => [
				[ 'value' => '<1s', 'label' => 'Median processing time', 'dark' => '' ],
				[ 'value' => '0', 'label' => 'Files uploaded to servers', 'dark' => '' ],
				[ 'value' => '74 tools', 'label' => 'PDF, image, text, SEO and developer utilities — all free, all in one place.', 'dark' => 'yes' ],
			],
			'title_field' => 'value',
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'style_why', [
			'label' => __( 'Style', 'textcrafttoolspro' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'section_bg', [
			'label'     => __( 'Section Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [
				'{{WRAPPER}} .tcs-section' => 'background: {{VALUE}}',
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
		$this->add_control( 'kicker_bg', [
			'label'     => __( 'Kicker Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#eaf0ff',
			'selectors' => [
				'{{WRAPPER}} .tcs-kicker' => 'background: {{VALUE}}',
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
		$this->add_control( 'list_bg', [
			'label'     => __( 'List Item Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [
				'{{WRAPPER}} .tcs-why-list li' => 'background: {{VALUE}}',
			],
		] );
		$this->add_control( 'list_border', [
			'label'     => __( 'List Item Border', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#e4e9f0',
			'selectors' => [
				'{{WRAPPER}} .tcs-why-list li' => 'border-color: {{VALUE}}',
			],
		] );
		$this->add_control( 'tick_color', [
			'label'     => __( 'Tick Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#16a34a',
			'selectors' => [
				'{{WRAPPER}} .tcs-tick' => 'background: {{VALUE}}',
			],
		] );
		$this->add_control( 'list_title_color', [
			'label'     => __( 'List Title Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#0b1220',
			'selectors' => [
				'{{WRAPPER}} .tcs-why-list h4' => 'color: {{VALUE}}',
			],
		] );
		$this->add_control( 'list_desc_color', [
			'label'     => __( 'List Text Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#4a5568',
			'selectors' => [
				'{{WRAPPER}} .tcs-why-list p' => 'color: {{VALUE}}',
			],
		] );
		$this->add_control( 'metric_bg', [
			'label'     => __( 'Metric Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [
				'{{WRAPPER}} .tcs-metric' => 'background: {{VALUE}}',
			],
		] );
		$this->add_control( 'metric_value_color', [
			'label'     => __( 'Metric Value Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#0b1220',
			'selectors' => [
				'{{WRAPPER}} .tcs-metric b' => 'color: {{VALUE}}',
			],
		] );
		$this->add_control( 'metric_label_color', [
			'label'     => __( 'Metric Label Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#8792a6',
			'selectors' => [
				'{{WRAPPER}} .tcs-metric span' => 'color: {{VALUE}}',
			],
		] );
		$this->add_control( 'metric_dark_bg', [
			'label'     => __( 'Dark Metric Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#0b1220',
			'selectors' => [
				'{{WRAPPER}} .tcs-metric.is-dark' => 'background: {{VALUE}}; border-color: {{VALUE}}',
			],
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrap', 'class', 'tcs-section tcs-why' );
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
				<div class="tcs-why-wrap">
					<ul class="tcs-why-list">
						<?php foreach ( $settings['list'] as $item ) : ?>
							<li>
								<span class="tcs-tick">✓</span>
								<div>
									<h4><?php echo esc_html( $item['title'] ); ?></h4>
									<p><?php echo esc_html( $item['description'] ); ?></p>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
					<div class="tcs-why-metrics">
						<?php foreach ( $settings['metrics'] as $m ) :
							$dark = ! empty( $m['dark'] ) && 'yes' === $m['dark'] ? ' is-dark' : '';
						?>
							<div class="tcs-metric<?php echo esc_attr( $dark ); ?>">
								<b><?php echo esc_html( $m['value'] ); ?></b>
								<span><?php echo esc_html( $m['label'] ); ?></span>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}

	protected function content_template() {}
}
