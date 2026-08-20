<?php
/**
 * Widget: SEO Cases Section
 *
 * Displays a "What Does Each Case Mean?" reference section — a centred
 * section header followed by a responsive grid of case-description cards.
 * Each card shows an emoji icon, the case name, a monospaced example
 * output badge, and a short description.
 *
 * All cards are managed via an Elementor Repeater control so editors
 * can add, remove, reorder, and customise every card without touching code.
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
 * Elementor widget: SEO Cases Section
 *
 * Renders the "What Does Each Case Mean?" text-guide section with a
 * full Repeater so every card field is panel-editable.
 */
class Widget_Seo_Cases_Section extends Widget_Base {

	// ── Identity ──────────────────────────────────────────────

	public function get_name(): string  { return 'textcraft_seo_cases_section'; }
	public function get_title(): string { return esc_html__( 'SEO Cases Section', 'textcraft-tools' ); }
	public function get_icon(): string  { return 'eicon-info-box'; }

	public function get_categories(): array { return [ 'textcraft-tools' ]; }

	public function get_keywords(): array {
		return [ 'seo', 'case', 'description', 'grid', 'guide', 'reference', 'textcraft', 'text case guide', 'case conversion' ];
	}

	// ── Controls ──────────────────────────────────────────────

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
				'label'   => esc_html__( 'Tag Line', 'textcraft-tools' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Text Case Guide & Reference', 'textcraft-tools' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'section_title',
			[
				'label'   => esc_html__( 'Title', 'textcraft-tools' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'What Does Each Case Mean?', 'textcraft-tools' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'section_subtitle',
			[
				'label'   => esc_html__( 'Subtitle', 'textcraft-tools' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'A quick reference to all seven text case formats — learn when and how to use each one for better writing, coding, and SEO.', 'textcraft-tools' ),
				'rows'    => 2,
			]
		);

		$this->add_control(
			'title_html_tag',
			[
				'label'   => esc_html__( 'Title HTML Tag', 'textcraft-tools' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => [
					'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'div' => 'div',
				],
			]
		);

		$this->end_controls_section();

		/* ── Content › Case Cards (Repeater) ───────────────── */
		$this->start_controls_section(
			'section_cards',
			[
				'label' => esc_html__( 'Case Cards', 'textcraft-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'card_icon',
			[
				'label'   => esc_html__( 'Icon / Emoji', 'textcraft-tools' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '🔠',
			]
		);

		$repeater->add_control(
			'card_name',
			[
				'label'       => esc_html__( 'Case Name', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'UPPERCASE',
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'card_example',
			[
				'label'       => esc_html__( 'Example Output', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'HELLO WORLD',
				'label_block' => true,
				'description' => esc_html__( 'Shown in a monospaced badge.', 'textcraft-tools' ),
			]
		);

		$repeater->add_control(
			'card_desc',
			[
				'label'       => esc_html__( 'Description', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'A short description of this case format.', 'textcraft-tools' ),
				'rows'        => 3,
				'label_block' => true,
			]
		);

		// ── Default 7 cards (exact match to original PHP $cases array) ──
		$this->add_control(
			'case_cards',
			[
				'label'       => esc_html__( 'Cards', 'textcraft-tools' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'card_icon'    => '🔠',
						'card_name'    => 'UPPERCASE',
						'card_example' => 'HELLO WORLD',
						'card_desc'    => esc_html__( 'Converts every letter to its capital form. Ideal for headings, acronyms, emphasis in online writing, or SEO title tags.', 'textcraft-tools' ),
					],
					[
						'card_icon'    => '🔡',
						'card_name'    => 'lowercase',
						'card_example' => 'hello world',
						'card_desc'    => esc_html__( 'Converts every letter to its small form. Commonly used in programming, URLs, meta descriptions, and casual online writing.', 'textcraft-tools' ),
					],
					[
						'card_icon'    => '📝',
						'card_name'    => 'Sentence case',
						'card_example' => 'Hello world. How are you?',
						'card_desc'    => esc_html__( 'Capitalises the first letter of every sentence while keeping the rest lowercase — exactly how standard English prose is written online.', 'textcraft-tools' ),
					],
					[
						'card_icon'    => '🅰️',
						'card_name'    => 'Capitalized Case',
						'card_example' => 'Hello World From Us',
						'card_desc'    => esc_html__( 'Capitalises the first letter of every word regardless of type. A simpler form of title capitalisation for headings and names.', 'textcraft-tools' ),
					],
					[
						'card_icon'    => '📰',
						'card_name'    => 'Title Case',
						'card_example' => 'Hello World from the Top',
						'card_desc'    => esc_html__( 'Follows AP/Chicago style rules — major words are capitalised while articles, short prepositions, and conjunctions stay lowercase. Ideal for SEO.', 'textcraft-tools' ),
					],
					[
						'card_icon'    => '🔀',
						'card_name'    => 'aLtErNaTiNg cAsE',
						'card_example' => 'hElLo wOrLd',
						'card_desc'    => esc_html__( 'Alternates between lowercase and uppercase on every character. Perfect for memes, sarcastic quotes, or adding visual flair to text.', 'textcraft-tools' ),
					],
					[
						'card_icon'    => '🔁',
						'card_name'    => 'InVeRsE CaSe',
						'card_example' => 'hELLO wORLD',
						'card_desc'    => esc_html__( 'Flips the case of every single letter — uppercase becomes lowercase and vice versa. Great for fixing or decoding mixed-case text online.', 'textcraft-tools' ),
					],
				],
				'title_field' => '{{{ card_icon }}} {{{ card_name }}}',
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
				'label'      => esc_html__( 'Padding', 'textcraft-tools' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top' => '64', 'bottom' => '64',
					'left' => '0', 'right' => '0',
					'unit' => 'px', 'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .tc-seo-section' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .tc-seo-section' => 'border-top-color: {{VALUE}};',
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
				'selectors' => [ '{{WRAPPER}} .tc-section-tag' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Title Color', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .tc-section-title' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label'     => esc_html__( 'Subtitle Color', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#a8997d',
				'selectors' => [ '{{WRAPPER}} .tc-section-subtitle' => 'color: {{VALUE}};' ],
			]
		);

		$this->end_controls_section();

		/* ── Style › Cards ──────────────────────────────────── */
		$this->start_controls_section(
			'style_cards',
			[
				'label' => esc_html__( 'Case Cards', 'textcraft-tools' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'card_min_width',
			[
				'label'      => esc_html__( 'Min Card Width', 'textcraft-tools' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 180, 'max' => 480 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 260 ],
				'selectors'  => [
					'{{WRAPPER}} .tc-seo-grid' => 'grid-template-columns: repeat(auto-fill, minmax({{SIZE}}{{UNIT}}, 1fr));',
				],
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
				'selectors'  => [ '{{WRAPPER}} .tc-seo-grid' => 'gap: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'card_bg',
			[
				'label'     => esc_html__( 'Card Background', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b0b0b',
				'selectors' => [ '{{WRAPPER}} .tc-seo-card' => 'background: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'card_border_color',
			[
				'label'     => esc_html__( 'Card Border', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.07)',
				'selectors' => [ '{{WRAPPER}} .tc-seo-card' => 'border-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'card_radius',
			[
				'label'      => esc_html__( 'Card Radius', 'textcraft-tools' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 18 ],
				'selectors'  => [ '{{WRAPPER}} .tc-seo-card' => 'border-radius: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'example_accent',
			[
				'label'       => esc_html__( 'Example Badge Accent', 'textcraft-tools' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '#d4a24c',
				'description' => esc_html__( 'Sets the text colour and tint of the monospaced example badge.', 'textcraft-tools' ),
				'selectors'   => [
					'{{WRAPPER}} .tc-seo-card__example' => 'color: {{VALUE}}; background: color-mix(in srgb, {{VALUE}} 8%, transparent); border-color: color-mix(in srgb, {{VALUE}} 20%, transparent);',
				],
			]
		);

		$this->add_control(
			'name_color',
			[
				'label'     => esc_html__( 'Case Name Color', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .tc-seo-card__name' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label'     => esc_html__( 'Description Color', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#a8997d',
				'selectors' => [ '{{WRAPPER}} .tc-seo-card__desc' => 'color: {{VALUE}};' ],
			]
		);

		$this->end_controls_section();
	}

	// ── Render ────────────────────────────────────────────────

	protected function render(): void {
		$s = $this->get_settings_for_display();

		// Allowed title tags.
		$allowed_tags = [ 'h1', 'h2', 'h3', 'h4', 'div' ];
		$title_tag    = in_array( $s['title_html_tag'] ?? 'h2', $allowed_tags, true )
			? $s['title_html_tag'] : 'h2';

		$border_style = ( 'yes' === ( $s['show_top_border'] ?? 'yes' ) )
			? '' : ' class="tc-border-top-none"';

		// ── Section open ──────────────────────────────────────
		printf(
			'<section class="tc-seo-section textcraft-tools"%s aria-labelledby="tc-sc-%s-heading">',
			$border_style, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			esc_attr( $this->get_id() )
		);

		// ── Section header ────────────────────────────────────
		echo '<div class="tc-section-header">';

		if ( ! empty( $s['section_tag'] ) ) {
			echo '<span class="tc-section-tag">' . esc_html( $s['section_tag'] ) . '</span>';
		}

		if ( ! empty( $s['section_title'] ) ) {
			printf(
				'<%1$s class="tc-section-title" id="tc-sc-%2$s-heading">%3$s</%1$s>',
				$title_tag,                          // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				esc_attr( $this->get_id() ),
				esc_html( $s['section_title'] )
			);
		}

		if ( ! empty( $s['section_subtitle'] ) ) {
			echo '<p class="tc-section-subtitle">' . esc_html( $s['section_subtitle'] ) . '</p>';
		}

		echo '</div>'; // .tc-section-header

		// ── SEO grid ──────────────────────────────────────────
		$cards = $s['case_cards'] ?? [];

		if ( ! empty( $cards ) ) {
			echo '<div class="tc-seo-grid">';

			foreach ( $cards as $card ) {
				$icon    = $card['card_icon']    ?? '';
				$name    = $card['card_name']    ?? '';
				$example = $card['card_example'] ?? '';
				$desc    = $card['card_desc']    ?? '';

                echo '<div class="tc-seo-card">';
                echo '<div class="tc-card-sheen"></div>';

                // Header: icon + name inline.
				echo '<div class="tc-seo-card__header">';
				if ( $icon ) {
					echo '<span class="tc-seo-card__icon" aria-hidden="true">' . esc_html( $icon ) . '</span>';
				}
				if ( $name ) {
					echo '<h3 class="tc-seo-card__name">' . esc_html( $name ) . '</h3>';
				}
				echo '</div>'; // .tc-seo-card__header

				// Example badge.
				if ( $example ) {
					printf(
						'<p class="tc-seo-card__example" aria-label="%s">%s</p>',
						esc_attr__( 'Example output', 'textcraft-tools' ),
						esc_html( $example )
					);
				}

				// Description.
				if ( $desc ) {
					echo '<p class="tc-seo-card__desc">' . esc_html( $desc ) . '</p>';
				}

				echo '</div>'; // .tc-seo-card
			}

			echo '</div>'; // .tc-seo-grid
		}

		echo '</section>'; // .tc-seo-section
	}

	// ── Editor live-preview template ──────────────────────────

	protected function content_template(): void {
		?>
		<#
		var titleTag    = ( ['h1','h2','h3','h4','div'].indexOf( settings.title_html_tag ) !== -1 )
		                  ? settings.title_html_tag : 'h2';
		var borderStyle = ( settings.show_top_border === 'yes' ) ? '' : 'tc-border-top-none';
		#>
		<section class="tc-seo-section {{ borderStyle }}">

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

			<# if ( settings.case_cards && settings.case_cards.length ) { #>
			<div class="tc-seo-grid">
				<# _.each( settings.case_cards, function( card ) { #>
                <div class="tc-seo-card">
                    <div class="tc-card-sheen"></div>
                    <div class="tc-seo-card__header">
						<# if ( card.card_icon ) { #>
							<span class="tc-seo-card__icon" aria-hidden="true">{{{ card.card_icon }}}</span>
						<# } #>
						<# if ( card.card_name ) { #>
							<h3 class="tc-seo-card__name">{{{ card.card_name }}}</h3>
						<# } #>
					</div>
					<# if ( card.card_example ) { #>
						<p class="tc-seo-card__example">{{{ card.card_example }}}</p>
					<# } #>
					<# if ( card.card_desc ) { #>
						<p class="tc-seo-card__desc">{{{ card.card_desc }}}</p>
					<# } #>
				</div>
				<# }); #>
			</div>
			<# } #>

		</section>
		<?php
	}
}
