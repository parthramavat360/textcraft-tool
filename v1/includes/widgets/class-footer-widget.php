<?php declare(strict_types=1);
namespace TextCraftPro\Widgets;

defined('ABSPATH') || exit;

class Footer_Widget extends \Elementor\Widget_Base {

	public function get_name(): string {
		return 'tc_footer';
	}

	public function get_title(): string {
		return esc_html__( 'Footer', 'textcraft-tools' );
	}

	public function get_icon(): string {
		return 'eicon-footer';
	}

	public function get_categories(): array {
		return [ 'textcraft' ];
	}

	protected function register_controls(): void {

		$this->start_controls_section(
			'brand_section',
			[ 'label' => esc_html__( 'Brand', 'textcraft-tools' ) ]
		);

		$this->add_control(
			'brand_initials',
			[
				'label'   => esc_html__( 'Brand Initials', 'textcraft-tools' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'TC',
			]
		);

		$this->add_control(
			'brand_text',
			[
				'label'   => esc_html__( 'Brand Text', 'textcraft-tools' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'TextCraft',
			]
		);

		$this->add_control(
			'brand_url',
			[
				'label'   => esc_html__( 'Brand URL', 'textcraft-tools' ),
				'type'    => \Elementor\Controls_Manager::URL,
				'default' => [
					'url' => home_url(),
				],
			]
		);

		$this->add_control(
			'brand_tagline',
			[
				'label'   => esc_html__( 'Brand Tagline', 'textcraft-tools' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => 'Seventy-four browser utilities for people who just need the job done.',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'footer_columns_section',
			[ 'label' => esc_html__( 'Footer Columns', 'textcraft-tools' ) ]
		);

		$column_repeater = new \Elementor\Repeater();

		$column_repeater->add_control(
			'column_title',
			[
				'label' => esc_html__( 'Column Title', 'textcraft-tools' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			]
		);

		$link_repeater = new \Elementor\Repeater();

		$link_repeater->add_control(
			'link_label',
			[
				'label' => esc_html__( 'Link Label', 'textcraft-tools' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			]
		);

		$link_repeater->add_control(
			'link_url',
			[
				'label' => esc_html__( 'Link URL', 'textcraft-tools' ),
				'type'  => \Elementor\Controls_Manager::URL,
			]
		);

		$column_repeater->add_control(
			'column_links',
			[
				'label'       => esc_html__( 'Column Links', 'textcraft-tools' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $link_repeater->get_controls(),
				'default'     => [],
				'title_field' => 'link_label',
			]
		);

		$this->add_control(
			'footer_columns',
			[
				'label'       => esc_html__( 'Footer Columns', 'textcraft-tools' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $column_repeater->get_controls(),
				'default'     => [],
				'title_field' => 'column_title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'copyright_section',
			[ 'label' => esc_html__( 'Copyright', 'textcraft-tools' ) ]
		);

		$this->add_control(
			'copyright_left',
			[
				'label'   => esc_html__( 'Copyright Left', 'textcraft-tools' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => gmdate( 'Y' ) . ' ' . esc_html__( 'TextCraft Tools', 'textcraft-tools' ),
			]
		);

		$this->add_control(
			'copyright_right',
			[
				'label'   => esc_html__( 'Copyright Right', 'textcraft-tools' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'Made for fast work.',
			]
		);

		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();

		$brand_initials = $settings['brand_initials'] ?? 'TC';
		$brand_text     = $settings['brand_text'] ?? 'TextCraft';
		$brand_url      = $settings['brand_url']['url'] ?? home_url();
		$brand_tagline  = $settings['brand_tagline'] ?? '';
		$columns        = $settings['footer_columns'] ?? [];
		$copyright_left = $settings['copyright_left'] ?? ( gmdate( 'Y' ) . ' TextCraft Tools' );
		$copyright_right = $settings['copyright_right'] ?? 'Made for fast work.';

		$default_columns = [
			[
				'column_title' => 'Tools',
				'column_links' => [
					[ 'link_label' => 'PDF',     'link_url' => [ 'url' => '#pdf' ] ],
					[ 'link_label' => 'Image',   'link_url' => [ 'url' => '#image' ] ],
					[ 'link_label' => 'Text',    'link_url' => [ 'url' => '#text' ] ],
					[ 'link_label' => 'Developer', 'link_url' => [ 'url' => '#dev' ] ],
				],
			],
			[
				'column_title' => 'Company',
				'column_links' => [
					[ 'link_label' => 'About',   'link_url' => [ 'url' => '#' ] ],
					[ 'link_label' => 'Blog',    'link_url' => [ 'url' => '#' ] ],
					[ 'link_label' => 'Contact', 'link_url' => [ 'url' => '#' ] ],
				],
			],
			[
				'column_title' => 'Legal',
				'column_links' => [
					[ 'link_label' => 'Privacy',    'link_url' => [ 'url' => '#' ] ],
					[ 'link_label' => 'Terms',      'link_url' => [ 'url' => '#' ] ],
					[ 'link_label' => 'Disclaimer', 'link_url' => [ 'url' => '#' ] ],
				],
			],
		];

		if ( empty( $columns ) ) {
			$columns = $default_columns;
		}
		?>
		<footer class="tc-widget tc-footer">
			<div class="tc-wrap">
				<div class="tc-footer-grid">
					<div class="tc-footer-brand">
						<a class="tc-header-brand" href="<?php echo esc_url( $brand_url ); ?>">
							<mark><?php echo esc_html( $brand_initials ); ?></mark>
							<span><?php echo esc_html( $brand_text ); ?></span>
						</a>
						<?php if ( $brand_tagline ) : ?>
							<p><?php echo esc_html( $brand_tagline ); ?></p>
						<?php endif; ?>
					</div>
					<?php foreach ( $columns as $column ) :
						$col_title = $column['column_title'] ?? '';
						$col_links = $column['column_links'] ?? [];
					?>
						<div class="tc-footer-col">
							<?php if ( $col_title ) : ?>
								<h4><?php echo esc_html( $col_title ); ?></h4>
							<?php endif; ?>
							<?php if ( ! empty( $col_links ) ) : ?>
								<ul>
									<?php foreach ( $col_links as $link ) :
										$label = $link['link_label'] ?? '';
										$url   = $link['link_url']['url'] ?? '#';
									?>
										<li>
											<a href="<?php echo esc_url( $url ); ?>">
												<?php echo esc_html( $label ); ?>
											</a>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="tc-footer-copy">
				<div class="tc-wrap">
					<span><?php echo esc_html( $copyright_left ); ?></span>
					<span><?php echo esc_html( $copyright_right ); ?></span>
				</div>
			</div>
		</footer>
		<?php
	}
}
