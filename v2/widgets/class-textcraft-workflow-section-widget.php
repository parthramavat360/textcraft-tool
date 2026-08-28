<?php
/**
 * Workflow / Split Section Elementor Widget
 *
 * Alternating image + copy split rows with mini list.
 * Pixel-perfect match to the textcraft-tool-page-full.html design.
 *
 * @package    TextCraftToolsPro
 * @subpackage Widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TextCraft_Workflow_Section_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'tctp_workflow_section';
	}

	public function get_title() {
		return __( 'TCTP: Workflow / Split Section', 'textcrafttoolspro' );
	}

	public function get_icon() {
		return 'eicon-slider-push';
	}

	public function get_categories() {
		return [ 'textcrafttools' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_split', [
			'label' => __( 'Content', 'textcrafttoolspro' ),
		] );

		/* First split */
		$this->add_control( 'first_kicker', [
			'label'   => __( 'First Split - Kicker', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'A simple workflow',
		] );
		$this->add_control( 'first_title', [
			'label'   => __( 'First Split - Title', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Three steps, zero shortcuts',
		] );
		$this->add_control( 'first_desc', [
			'label'   => __( 'First Split - Description', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'rows'    => 3,
			'default' => 'The workflow stays the same no matter which tool you open. Pick a file, choose your output, and let the browser handle the rest.',
		] );
		$this->add_control( 'first_media', [
			'label'   => __( 'First Split - Media', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::MEDIA,
		] );

		$mini = new \Elementor\Repeater();
		$mini->add_control( 'step', [
			'label' => __( 'Step (e.g. 01)', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::TEXT,
		] );
		$mini->add_control( 'text', [
			'label' => __( 'Step Label', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::TEXT,
		] );
		$this->add_control( 'first_list', [
			'label'       => __( 'First Split Steps', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $mini->get_controls(),
			'default'     => [
				[ 'step' => '01', 'text' => 'Choose your file' ],
				[ 'step' => '02', 'text' => 'Set your options' ],
				[ 'step' => '03', 'text' => 'Download the result' ],
			],
			'title_field' => 'step',
		] );

		/* Second split */
		$this->add_control( 'second_kicker', [
			'label'   => __( 'Second Split - Kicker', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Private by default',
		] );
		$this->add_control( 'second_title', [
			'label'   => __( 'Second Split - Title', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Your files never leave the tab',
		] );
		$this->add_control( 'second_desc', [
			'label'   => __( 'Second Split - Description', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'rows'    => 3,
			'default' => 'Most tools route your document through a cloud server. Every TextCraft Pro tool runs entirely in your browser, so the file you open is the only place your data ever appears.',
		] );
		$this->add_control( 'second_media', [
			'label'   => __( 'Second Split - Media', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::MEDIA,
		] );

		$mini2 = new \Elementor\Repeater();
		$mini2->add_control( 'step', [
			'label' => __( 'Step (e.g. 01)', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::TEXT,
		] );
		$mini2->add_control( 'text', [
			'label' => __( 'Step Label', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::TEXT,
		] );
		$this->add_control( 'second_list', [
			'label'       => __( 'Second Split Points', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $mini2->get_controls(),
			'default'     => [
				[ 'step' => '✓', 'text' => 'Nothing uploaded to a server' ],
				[ 'step' => '✓', 'text' => 'No cookies, no tracking' ],
				[ 'step' => '✓', 'text' => 'Works even offline after load' ],
			],
			'title_field' => 'step',
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'style_split', [
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
				'{{WRAPPER}} .tcs-split h3' => 'color: {{VALUE}}',
			],
		] );
		$this->add_control( 'desc_color', [
			'label'     => __( 'Description Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#4a5568',
			'selectors' => [
				'{{WRAPPER}} .tcs-split p' => 'color: {{VALUE}}',
			],
		] );
		$this->add_control( 'media_bg', [
			'label'     => __( 'Media Box Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#eaf0ff',
			'selectors' => [
				'{{WRAPPER}} .tcs-split-media' => 'background: {{VALUE}}',
			],
		] );
		$this->add_control( 'mini_border', [
			'label'     => __( 'Mini Item Border', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#e4e9f0',
			'selectors' => [
				'{{WRAPPER}} .tcs-mini-list li' => 'border-color: {{VALUE}}',
			],
		] );
		$this->add_control( 'mini_num_color', [
			'label'     => __( 'Mini Number Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#2563eb',
			'selectors' => [
				'{{WRAPPER}} .tcs-mini-list b' => 'color: {{VALUE}}',
			],
		] );
		$this->add_control( 'mini_text_color', [
			'label'     => __( 'Mini Text Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#0b1220',
			'selectors' => [
				'{{WRAPPER}} .tcs-mini-list span' => 'color: {{VALUE}}',
			],
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrap', 'class', 'tcs-section tcs-workflow' );
		?>
		<section <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
			<div class="tcs-wrap">

				<div class="tcs-split">
					<div class="tcs-split-media">
						<?php if ( ! empty( $settings['first_media']['url'] ) ) : ?>
							<img src="<?php echo esc_url( $settings['first_media']['url'] ); ?>" alt="<?php echo esc_attr( $settings['first_title'] ); ?>" loading="lazy" />
						<?php endif; ?>
					</div>
					<div class="tcs-split-copy">
						<?php if ( ! empty( $settings['first_kicker'] ) ) : ?>
							<span class="tcs-kicker"><?php echo esc_html( $settings['first_kicker'] ); ?></span>
						<?php endif; ?>
						<h3><?php echo esc_html( $settings['first_title'] ); ?></h3>
						<p><?php echo esc_html( $settings['first_desc'] ); ?></p>
						<?php if ( ! empty( $settings['first_list'] ) ) : ?>
							<ul class="tcs-mini-list">
								<?php foreach ( $settings['first_list'] as $i ) : ?>
									<li><b><?php echo esc_html( $i['step'] ); ?></b><span><?php echo esc_html( $i['text'] ); ?></span></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
				</div>

				<div class="tcs-split is-reverse">
					<div class="tcs-split-copy">
						<?php if ( ! empty( $settings['second_kicker'] ) ) : ?>
							<span class="tcs-kicker"><?php echo esc_html( $settings['second_kicker'] ); ?></span>
						<?php endif; ?>
						<h3><?php echo esc_html( $settings['second_title'] ); ?></h3>
						<p><?php echo esc_html( $settings['second_desc'] ); ?></p>
						<?php if ( ! empty( $settings['second_list'] ) ) : ?>
							<ul class="tcs-mini-list">
								<?php foreach ( $settings['second_list'] as $i ) : ?>
									<li><b><?php echo esc_html( $i['step'] ); ?></b><span><?php echo esc_html( $i['text'] ); ?></span></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
					<div class="tcs-split-media">
						<?php if ( ! empty( $settings['second_media']['url'] ) ) : ?>
							<img src="<?php echo esc_url( $settings['second_media']['url'] ); ?>" alt="<?php echo esc_attr( $settings['second_title'] ); ?>" loading="lazy" />
						<?php endif; ?>
					</div>
				</div>

			</div>
		</section>
		<?php
	}

	protected function content_template() {}
}
