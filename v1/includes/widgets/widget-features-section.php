<?php
/**
 * Widget: Features Section
 *
 * Renders a "Why ConvertCase?" style features grid — a section header
 * (tag line, title, subtitle) above a responsive card grid where each
 * card has an emoji icon, coloured background, title, and description.
 *
 * All content is fully editable from the Elementor panel:
 *  - Section tag / title / subtitle via the Content tab.
 *  - Up to 8 feature cards via a Repeater control.
 *  - Accent colour, card columns, padding via the Style tab.
 *
 * Zero shortcodes. Zero PHP output buffering. Pure Elementor widget.
 *
 * @package TextCraft_Tools\Widgets
 * @version 1.0.0
 */

declare( strict_types = 1 );

namespace TextCraft_Tools\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Elementor widget: Features Section
 *
 * Renders a full "features" section with a centred header and a
 * responsive card grid. Every field is panel-editable.
 */
class Widget_Features_Section extends Widget_Base {

	// ── Identity ──────────────────────────────────────────────

	/** {@inheritDoc} */
	public function get_name(): string {
		return 'textcraft_features_section';
	}

	/** {@inheritDoc} */
	public function get_title(): string {
		return esc_html__( 'Features Section', 'textcraft-tools' );
	}

	/** {@inheritDoc} */
	public function get_icon(): string {
		return 'eicon-icon-box';
	}

	/** {@inheritDoc} */
	public function get_categories(): array {
		return [ 'textcraft-tools' ];
	}

	/** {@inheritDoc} */
	public function get_keywords(): array {
		return [ 'features', 'cards', 'grid', 'icons', 'why', 'benefits', 'section', 'textcraft', 'free online tools', 'privacy-first tool' ];
	}

	// ── Panel controls ────────────────────────────────────────

	/** Register all Elementor panel controls. */
	protected function register_controls(): void {

		/* ── Content › Section Header ──────────────────────── */
		$this->start_controls_section(
			'section_header',
			[
				'label' => esc_html__( 'Section Header', 'textcraft-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'section_tag',
			[
				'label'       => esc_html__( 'Tag Line', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Why TextCraft Tools?', 'textcraft-tools' ),
				'label_block' => true,
				'description' => esc_html__( 'Small uppercase label above the title.', 'textcraft-tools' ),
			]
		);

		$this->add_control(
			'section_title',
			[
				'label'       => esc_html__( 'Title', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Built for Speed, Privacy & Simplicity', 'textcraft-tools' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'section_subtitle',
			[
				'label'   => esc_html__( 'Subtitle', 'textcraft-tools' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'No bloat, no distractions. Just the fastest, most private free online text tools and random generators — all running in your browser.', 'textcraft-tools' ),
				'rows'    => 3,
			]
		);

		$this->add_control(
			'title_html_tag',
			[
				'label'   => esc_html__( 'Title HTML Tag', 'textcraft-tools' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'div'=> 'div',
				],
			]
		);

		$this->end_controls_section();

		/* ── Content › Feature Cards (Repeater) ────────────── */
		$this->start_controls_section(
			'section_cards',
			[
				'label' => esc_html__( 'Feature Cards', 'textcraft-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		// ── Repeater ──────────────────────────────────────────
		$repeater = new Repeater();

		$repeater->add_control(
			'card_icon',
			[
				'label'       => esc_html__( 'Icon / Emoji', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '⚡',
				'label_block' => false,
				'description' => esc_html__( 'Paste any emoji or icon character.', 'textcraft-tools' ),
			]
		);

		$repeater->add_control(
			'card_icon_color',
			[
				'label'   => esc_html__( 'Icon Background', 'textcraft-tools' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'purple',
				'options' => [
					'purple' => esc_html__( 'Purple',  'textcraft-tools' ),
					'pink'   => esc_html__( 'Pink',    'textcraft-tools' ),
					'green'  => esc_html__( 'Green',   'textcraft-tools' ),
					'yellow' => esc_html__( 'Yellow',  'textcraft-tools' ),
					'blue'   => esc_html__( 'Blue',    'textcraft-tools' ),
					'red'    => esc_html__( 'Red',     'textcraft-tools' ),
				],
			]
		);

		$repeater->add_control(
			'card_title',
			[
				'label'       => esc_html__( 'Card Title', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Feature Title', 'textcraft-tools' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'card_desc',
			[
				'label'       => esc_html__( 'Description', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'A short description of this feature.', 'textcraft-tools' ),
				'rows'        => 3,
				'label_block' => true,
			]
		);

		// ── Default 4 cards (matching original HTML) ──────────
		$this->add_control(
			'feature_cards',
			[
				'label'       => esc_html__( 'Cards', 'textcraft-tools' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'card_icon'       => '⚡',
						'card_icon_color' => 'purple',
						'card_title'      => esc_html__( 'Instant Results',     'textcraft-tools' ),
						'card_desc'       => esc_html__( 'Results appear the moment you click. No loading, no waiting — pure browser-side speed for every online tool.', 'textcraft-tools' ),
					],
					[
						'card_icon'       => '🔒',
						'card_icon_color' => 'pink',
						'card_title'      => esc_html__( '100% Private & Secure',           'textcraft-tools' ),
						'card_desc'       => esc_html__( 'Your data never leaves your device. All processing happens locally in your browser — no uploads, no servers, total privacy.', 'textcraft-tools' ),
					],
					[
						'card_icon'       => '♿',
						'card_icon_color' => 'green',
						'card_title'      => esc_html__( 'Accessible by Design',   'textcraft-tools' ),
						'card_desc'       => esc_html__( 'Built with ARIA labels, keyboard shortcuts, and full screen-reader support — every free tool is usable by everyone.', 'textcraft-tools' ),
					],
					[
						'card_icon'       => '📊',
						'card_icon_color' => 'yellow',
						'card_title'      => esc_html__( 'Live Statistics',         'textcraft-tools' ),
						'card_desc'       => esc_html__( 'Character, word, sentence, and line counts update live as you type or convert in any browser-based tool.', 'textcraft-tools' ),
					],
				],
				'title_field' => '{{{ card_icon }}} {{{ card_title }}}',
			]
		);

		$this->end_controls_section();

		/* ── Style › Section ────────────────────────────────── */
		$this->start_controls_section(
			'style_section',
			[
				'label' => esc_html__( 'Section', 'textcraft-tools' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'section_padding',
			[
				'label'      => esc_html__( 'Section Padding', 'textcraft-tools' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'    => '64',
					'bottom' => '64',
					'left'   => '0',
					'right'  => '0',
					'unit'   => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .tc-features-section' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'show_top_border',
			[
				'label'        => esc_html__( 'Top Border', 'textcraft-tools' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'textcraft-tools' ),
				'label_off'    => esc_html__( 'Hide', 'textcraft-tools' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'border_color',
			[
				'label'     => esc_html__( 'Border Color', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.07)',
				'condition' => [ 'show_top_border' => 'yes' ],
				'selectors' => [
					'{{WRAPPER}} .tc-features-section' => 'border-top-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		/* ── Style › Header ─────────────────────────────────── */
		$this->start_controls_section(
			'style_header',
			[
				'label' => esc_html__( 'Header', 'textcraft-tools' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'tag_color',
			[
				'label'     => esc_html__( 'Tag Color', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d4a24c',
				'selectors' => [
					'{{WRAPPER}} .tc-section-tag' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Title Color', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .tc-section-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label'     => esc_html__( 'Subtitle Color', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#a8997d',
				'selectors' => [
					'{{WRAPPER}} .tc-section-subtitle' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'header_gap',
			[
				'label'      => esc_html__( 'Header Bottom Gap', 'textcraft-tools' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 16, 'max' => 96 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 48 ],
				'selectors'  => [
					'{{WRAPPER}} .tc-section-header' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/* ── Style › Cards ──────────────────────────────────── */
		$this->start_controls_section(
			'style_cards',
			[
				'label' => esc_html__( 'Feature Cards', 'textcraft-tools' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'card_columns',
			[
				'label'       => esc_html__( 'Min Card Width (grid)', 'textcraft-tools' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px' ],
				'range'       => [ 'px' => [ 'min' => 160, 'max' => 400 ] ],
				'default'     => [ 'unit' => 'px', 'size' => 240 ],
				'selectors'   => [
					'{{WRAPPER}} .tc-features-grid' => 'grid-template-columns: repeat(auto-fill, minmax({{SIZE}}{{UNIT}}, 1fr));',
				],
				'description' => esc_html__( 'Grid auto-fills columns. Smaller value = more columns.', 'textcraft-tools' ),
			]
		);

		$this->add_control(
			'card_gap',
			[
				'label'      => esc_html__( 'Card Gap', 'textcraft-tools' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 8, 'max' => 48 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 20 ],
				'selectors'  => [
					'{{WRAPPER}} .tc-features-grid' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'card_padding',
			[
				'label'      => esc_html__( 'Card Padding', 'textcraft-tools' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'top' => '28', 'right' => '28', 'bottom' => '28', 'left' => '28',
					'unit' => 'px', 'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .tc-feature-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'card_bg',
			[
				'label'     => esc_html__( 'Card Background', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b0b0b',
				'selectors' => [
					'{{WRAPPER}} .tc-feature-card' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'card_border_color',
			[
				'label'     => esc_html__( 'Card Border Color', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.07)',
				'selectors' => [
					'{{WRAPPER}} .tc-feature-card' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'card_radius',
			[
				'label'      => esc_html__( 'Card Border Radius', 'textcraft-tools' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 18 ],
				'selectors'  => [
					'{{WRAPPER}} .tc-feature-card' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'card_title_color',
			[
				'label'     => esc_html__( 'Card Title Color', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .tc-feature-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'card_desc_color',
			[
				'label'     => esc_html__( 'Card Desc Color', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#a8997d',
				'selectors' => [
					'{{WRAPPER}} .tc-feature-desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_size',
			[
				'label'      => esc_html__( 'Icon Box Size', 'textcraft-tools' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 32, 'max' => 80 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 44 ],
				'selectors'  => [
					'{{WRAPPER}} .tc-feature-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_font_size',
			[
				'label'      => esc_html__( 'Icon Font Size', 'textcraft-tools' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 12, 'max' => 48 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 20 ],
				'selectors'  => [
					'{{WRAPPER}} .tc-feature-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	// ── Render ────────────────────────────────────────────────

	/** Render the widget HTML on the front end and in the Elementor preview. */
	protected function render(): void {
		$s = $this->get_settings_for_display();

		// Resolve whether to show the top border.
		$border_style = ( 'yes' === ( $s['show_top_border'] ?? 'yes' ) )
			? ''
			: ' class="tc-border-top-none"';

		// Resolve title HTML tag (allowlist-sanitised).
		$allowed_tags = [ 'h1', 'h2', 'h3', 'h4', 'div' ];
		$title_tag    = in_array( $s['title_html_tag'] ?? 'h2', $allowed_tags, true )
			? $s['title_html_tag']
			: 'h2';

		// ── Section open ──────────────────────────────────────
		printf(
			'<section class="tc-features-section textcraft-tools"%s aria-labelledby="tc-fs-%s-heading">',
			$border_style,  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			esc_attr( $this->get_id() )
		);

		// ── Section header ────────────────────────────────────
		echo '<div class="tc-section-header">';

		if ( ! empty( $s['section_tag'] ) ) {
			echo '<span class="tc-section-tag">' . esc_html( $s['section_tag'] ) . '</span>';
		}

		if ( ! empty( $s['section_title'] ) ) {
			printf(
				'<%1$s class="tc-section-title" id="tc-fs-%2$s-heading">%3$s</%1$s>',
				$title_tag,                             // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				esc_attr( $this->get_id() ),
				esc_html( $s['section_title'] )
			);
		}

		if ( ! empty( $s['section_subtitle'] ) ) {
			echo '<p class="tc-section-subtitle">' . esc_html( $s['section_subtitle'] ) . '</p>';
		}

		echo '</div>'; // .tc-section-header

		// ── Features grid ─────────────────────────────────────
		$cards = $s['feature_cards'] ?? [];

		if ( ! empty( $cards ) ) {
			echo '<div class="tc-features-grid">';

			foreach ( $cards as $card ) {
				$icon       = $card['card_icon']       ?? '⚡';
				$color      = $card['card_icon_color'] ?? 'purple';
				$card_title = $card['card_title']      ?? '';
				$card_desc  = $card['card_desc']       ?? '';

				// Allowlist colour variant classes.
				$allowed_colors = [ 'purple', 'pink', 'green', 'yellow', 'blue', 'red' ];
				$color_class    = in_array( $color, $allowed_colors, true )
					? 'tc-feature-icon--' . $color
					: 'tc-feature-icon--purple';

                echo '<div class="tc-feature-card">';
                echo '<div class="tc-card-sheen"></div>';

                // Icon box.
				printf(
					'<div class="tc-feature-icon %s" aria-hidden="true">%s</div>',
					esc_attr( $color_class ),
					esc_html( $icon )
				);

				// Card title.
				if ( $card_title ) {
					echo '<h3 class="tc-feature-title">' . esc_html( $card_title ) . '</h3>';
				}

				// Card description.
				if ( $card_desc ) {
					echo '<p class="tc-feature-desc">' . esc_html( $card_desc ) . '</p>';
				}

				echo '</div>'; // .tc-feature-card
			}

			echo '</div>'; // .tc-features-grid
		}

		echo '</section>'; // .tc-features-section
	}

	// ── Editor content template ───────────────────────────────

	/**
	 * Elementor live-preview JS template.
	 * Mirrors the PHP render() output so the editor updates instantly
	 * as the user types in the panel — no page reload needed.
	 */
	protected function content_template(): void {
		?>
		<#
		var borderStyle = ( settings.show_top_border === 'yes' ) ? '' : 'tc-border-top-none';
		var titleTag    = ( ['h1','h2','h3','h4','div'].indexOf( settings.title_html_tag ) !== -1 )
		                  ? settings.title_html_tag : 'h2';
		#>
		<section class="tc-features-section {{ borderStyle }}">

			<div class="tc-section-header">
				<# if ( settings.section_tag ) { #>
					<span class="tc-section-tag">{{{ settings.section_tag }}}</span>
				<# } #>

				<# if ( settings.section_title ) { #>
					<{{{ titleTag }}} class="tc-section-title">{{{ settings.section_title }}}</{{{ titleTag }}}>
				<# } #>

				<# if ( settings.section_subtitle ) { #>
					<p class="tc-section-subtitle">{{{ settings.section_subtitle }}}</p>
				<# } #>
			</div>

			<# if ( settings.feature_cards && settings.feature_cards.length ) { #>
			<div class="tc-features-grid">
				<# _.each( settings.feature_cards, function( card ) {
					var allowedColors = ['purple','pink','green','yellow','blue','red'];
					var colorClass    = ( allowedColors.indexOf( card.card_icon_color ) !== -1 )
					                    ? 'tc-feature-icon--' + card.card_icon_color
					                    : 'tc-feature-icon--purple';
				#>
                    <div class="tc-feature-card">
                        <div class="tc-card-sheen"></div>
                        <div class="tc-feature-icon {{ colorClass }}" aria-hidden="true">
							{{{ card.card_icon }}}
						</div>
						<# if ( card.card_title ) { #>
							<h3 class="tc-feature-title">{{{ card.card_title }}}</h3>
						<# } #>
						<# if ( card.card_desc ) { #>
							<p class="tc-feature-desc">{{{ card.card_desc }}}</p>
						<# } #>
					</div>
				<# }); #>
			</div>
			<# } #>

		</section>
		<?php
	}
}
