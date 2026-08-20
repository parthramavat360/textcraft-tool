<?php
/**
 * Widget: Tools Grid Section
 *
 * Renders a "More Tools" section — a centred header followed by a
 * responsive grid of clickable tool-link cards. Each card shows an
 * emoji icon box, a tool name, and a short description.
 *
 * Cards support three link modes (managed via the Repeater):
 *  - Live URL  — links directly to a page
 *  - "Coming soon" — renders the card without a real link, shows a badge
 *  - No link   — plain static card
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
 * Elementor widget: Tools Grid Section
 *
 * A repeater-driven grid of tool-link cards with full style control.
 */
class Widget_Tools_Grid_Section extends Widget_Base {

	// ── Identity ──────────────────────────────────────────────

	public function get_name(): string  { return 'textcraft_tools_grid_section'; }
	public function get_title(): string { return esc_html__( 'Tools Grid Section', 'textcraft-tools' ); }
	public function get_icon(): string  { return 'eicon-gallery-grid'; }

	public function get_categories(): array { return [ 'textcraft-tools' ]; }

	public function get_keywords(): array {
		return [ 'tools', 'grid', 'cards', 'links', 'coming soon', 'section', 'textcraft', 'free online tools', 'browser utilities' ];
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
				'label'       => esc_html__( 'Tag Line', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'More Tools', 'textcraft-tools' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'section_title',
			[
				'label'       => esc_html__( 'Title', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
'default' => esc_html__( 'Explore More Free Online Tools', 'textcraft-tools' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'section_subtitle',
			[
				'label'   => esc_html__( 'Subtitle', 'textcraft-tools' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__( "We're building a full suite of free online text utilities and browser-based tools to simplify your workflow. More coming soon.", 'textcraft-tools' ),
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

		/* ── Content › Tool Cards (Repeater) ───────────────── */
		$this->start_controls_section(
			'section_cards',
			[
				'label' => esc_html__( 'Tool Cards', 'textcraft-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		// Icon.
		$repeater->add_control(
			'tool_icon',
			[
				'label'   => esc_html__( 'Icon / Emoji', 'textcraft-tools' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '🔤',
			]
		);

		// Tool name.
		$repeater->add_control(
			'tool_name',
			[
				'label'       => esc_html__( 'Tool Name', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Tool Name', 'textcraft-tools' ),
				'label_block' => true,
			]
		);

		// Short description.
		$repeater->add_control(
			'tool_desc',
			[
				'label'       => esc_html__( 'Short Description', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'What this tool does.', 'textcraft-tools' ),
				'label_block' => true,
			]
		);

		// Link URL.
		$repeater->add_control(
			'tool_url',
			[
				'label'         => esc_html__( 'Link URL', 'textcraft-tools' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => 'https://example.com/tools/my-tool/',
				'show_external' => true,
				'default'       => [ 'url' => '' ],
				'description'   => esc_html__( 'Leave blank to show a "Coming soon" badge.', 'textcraft-tools' ),
			]
		);

		// Open in new tab.
		$repeater->add_control(
			'tool_new_tab',
			[
				'label'        => esc_html__( 'Open in New Tab', 'textcraft-tools' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'textcraft-tools' ),
				'label_off'    => esc_html__( 'No', 'textcraft-tools' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		// ── Default 8 cards (exact match to original PHP $upcoming_tools) ──
		$this->add_control(
			'tool_cards',
			[
				'label'       => esc_html__( 'Cards', 'textcraft-tools' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'tool_icon' => '↔️',
						'tool_name' => esc_html__( 'Reverse Text',      'textcraft-tools' ),
						'tool_desc' => esc_html__( 'Reverse any string instantly — free online text reverser tool', 'textcraft-tools' ),
						'tool_url'  => [ 'url' => '' ],
					],
					[
						'tool_icon' => '📏',
						'tool_name' => esc_html__( 'Word Counter',       'textcraft-tools' ),
						'tool_desc' => esc_html__( 'Count words and characters online — free text analysis tool', 'textcraft-tools' ),
						'tool_url'  => [ 'url' => '' ],
					],
					[
						'tool_icon' => '🔗',
						'tool_name' => esc_html__( 'Slug Generator',     'textcraft-tools' ),
						'tool_desc' => esc_html__( 'Create URL-friendly SEO slugs from any text — free online slug maker', 'textcraft-tools' ),
						'tool_url'  => [ 'url' => '' ],
					],
					[
						'tool_icon' => '✂️',
						'tool_name' => esc_html__( 'Text Truncator',     'textcraft-tools' ),
						'tool_desc' => esc_html__( 'Trim text to a specific length — free online text truncation tool', 'textcraft-tools' ),
						'tool_url'  => [ 'url' => '' ],
					],
					[
						'tool_icon' => '🔤',
						'tool_name' => esc_html__( 'Remove Spaces',      'textcraft-tools' ),
						'tool_desc' => esc_html__( 'Strip extra whitespace and clean up text — free online space remover', 'textcraft-tools' ),
						'tool_url'  => [ 'url' => '' ],
					],
					[
						'tool_icon' => '🔢',
						'tool_name' => esc_html__( 'Sort Lines',         'textcraft-tools' ),
						'tool_desc' => esc_html__( 'Sort lines alphabetically online — free line sorter tool', 'textcraft-tools' ),
						'tool_url'  => [ 'url' => '' ],
					],
					[
						'tool_icon' => '✨',
						'tool_name' => esc_html__( 'Remove Duplicates',  'textcraft-tools' ),
						'tool_desc' => esc_html__( 'Delete duplicate lines from any text — free online duplicate remover', 'textcraft-tools' ),
						'tool_url'  => [ 'url' => '' ],
					],
					[
						'tool_icon' => '🌐',
						'tool_name' => esc_html__( 'HTML Encoder',       'textcraft-tools' ),
						'tool_desc' => esc_html__( 'Encode special HTML characters online — free HTML entity encoder tool', 'textcraft-tools' ),
						'tool_url'  => [ 'url' => '' ],
					],
				],
				'title_field' => '{{{ tool_icon }}} {{{ tool_name }}}',
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
					'{{WRAPPER}} .tc-tools-section' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .tc-tools-section' => 'border-top-color: {{VALUE}};',
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
				'label' => esc_html__( 'Tool Cards', 'textcraft-tools' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'card_min_width',
			[
				'label'      => esc_html__( 'Min Card Width', 'textcraft-tools' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 140, 'max' => 400 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 200 ],
				'selectors'  => [
					'{{WRAPPER}} .tc-tools-grid' => 'grid-template-columns: repeat(auto-fill, minmax({{SIZE}}{{UNIT}}, 1fr));',
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
				'default'    => [ 'unit' => 'px', 'size' => 16 ],
				'selectors'  => [ '{{WRAPPER}} .tc-tools-grid' => 'gap: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'card_bg',
			[
				'label'     => esc_html__( 'Card Background', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b0b0b',
				'selectors' => [ '{{WRAPPER}} .tc-tool-link-card' => 'background: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'card_border_color',
			[
				'label'     => esc_html__( 'Card Border', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.07)',
				'selectors' => [ '{{WRAPPER}} .tc-tool-link-card' => 'border-color: {{VALUE}};' ],
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
				'selectors'  => [ '{{WRAPPER}} .tc-tool-link-card' => 'border-radius: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'hover_accent',
			[
				'label'     => esc_html__( 'Hover Border & Glow', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d4a24c',
				'selectors' => [
					'{{WRAPPER}} .tc-tool-link-card:hover' => 'border-color: {{VALUE}}; box-shadow: 0 8px 28px color-mix(in srgb, {{VALUE}} 15%, transparent);',
				],
			]
		);

		$this->add_control(
			'icon_bg',
			[
				'label'     => esc_html__( 'Icon Box Background', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1a1d30',
				'selectors' => [ '{{WRAPPER}} .tc-tl-icon' => 'background: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'icon_box_size',
			[
				'label'      => esc_html__( 'Icon Box Size', 'textcraft-tools' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 32, 'max' => 80 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 44 ],
				'selectors'  => [
					'{{WRAPPER}} .tc-tl-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'name_color',
			[
				'label'     => esc_html__( 'Tool Name Color', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .tc-tl-name' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label'     => esc_html__( 'Description Color', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#55597a',
				'selectors' => [ '{{WRAPPER}} .tc-tl-desc' => 'color: {{VALUE}};' ],
			]
		);

		$this->end_controls_section();
	}

	// ── Render ────────────────────────────────────────────────

	protected function render(): void {
		$s = $this->get_settings_for_display();

		// Title tag allowlist.
		$allowed_tags = [ 'h1', 'h2', 'h3', 'h4', 'div' ];
		$title_tag    = in_array( $s['title_html_tag'] ?? 'h2', $allowed_tags, true )
			? $s['title_html_tag'] : 'h2';

		$border_style = ( 'yes' === ( $s['show_top_border'] ?? 'yes' ) )
			? '' : ' class="tc-border-top-none"';

		// ── Section open ──────────────────────────────────────
		printf(
			'<section class="tc-tools-section textcraft-tools"%s aria-labelledby="tc-tg-%s-heading">',
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
				'<%1$s class="tc-section-title" id="tc-tg-%2$s-heading">%3$s</%1$s>',
				$title_tag,                          // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				esc_attr( $this->get_id() ),
				esc_html( $s['section_title'] )
			);
		}

		if ( ! empty( $s['section_subtitle'] ) ) {
			echo '<p class="tc-section-subtitle">' . esc_html( $s['section_subtitle'] ) . '</p>';
		}

		echo '</div>'; // .tc-section-header

		// ── Tools grid ────────────────────────────────────────
		$cards = $s['tool_cards'] ?? [];

		if ( ! empty( $cards ) ) {
			echo '<div class="tc-tools-grid">';

			foreach ( $cards as $card ) {
				$icon     = $card['tool_icon'] ?? '';
				$name     = $card['tool_name'] ?? '';
				$desc     = $card['tool_desc'] ?? '';
				$url      = $card['tool_url']['url'] ?? '';
				$new_tab  = ! empty( $card['tool_new_tab'] ) && 'yes' === $card['tool_new_tab'];
				$is_soon  = empty( $url );

				// Build card attributes.
				$card_class = 'tc-tool-link-card' . ( $is_soon ? ' tc-tool-link-card--soon' : '' );

				if ( ! $is_soon ) {
					// Live link — render as <a>.
					printf(
						'<a href="%s" class="%s"%s%s>',
						esc_url( $url ),
						esc_attr( $card_class ),
						$new_tab ? ' target="_blank" rel="noopener noreferrer"' : '', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						' aria-label="' . esc_attr( $name ) . '"'
					);
				} else {
					// Coming soon — render as <div> with role button + aria.
					printf(
						'<div class="%s" role="button" tabindex="0" aria-label="%s">',
						esc_attr( $card_class ),
						esc_attr( $name . ' — ' . esc_html__( 'Coming soon', 'textcraft-tools' ) )
					);
				}
				echo '<div class="tc-card-sheen"></div>';

				// Icon box.
				if ( $icon ) {
					echo '<div class="tc-tl-icon" aria-hidden="true">' . esc_html( $icon ) . '</div>';
				}

				// Tool name.
				if ( $name ) {
					echo '<div class="tc-tl-name">' . esc_html( $name ) . '</div>';
				}

				// Description.
				if ( $desc ) {
					echo '<div class="tc-tl-desc">' . esc_html( $desc ) . '</div>';
				}

				// Close tag.
				echo $is_soon ? '</div>' : '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			echo '</div>'; // .tc-tools-grid
		}

		echo '</section>'; // .tc-tools-section
	}

	// ── Editor live-preview template ──────────────────────────

	protected function content_template(): void {
		?>
		<#
		var titleTag    = ( ['h1','h2','h3','h4','div'].indexOf( settings.title_html_tag ) !== -1 )
		                  ? settings.title_html_tag : 'h2';
		var borderStyle = ( settings.show_top_border === 'yes' ) ? '' : 'tc-border-top-none';
		#>
		<section class="tc-tools-section {{ borderStyle }}">

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

			<# if ( settings.tool_cards && settings.tool_cards.length ) { #>
			<div class="tc-tools-grid">
				<# _.each( settings.tool_cards, function( card ) {
					var isSoon     = ! card.tool_url || ! card.tool_url.url;
					var cardClass  = 'tc-tool-link-card' + ( isSoon ? ' tc-tool-link-card--soon' : '' );
				#>
                <div class="{{ cardClass }}">
                    <div class="tc-card-sheen"></div>
                    <# if ( card.tool_icon ) { #>
						<div class="tc-tl-icon" aria-hidden="true">{{{ card.tool_icon }}}</div>
					<# } #>
					<# if ( card.tool_name ) { #>
						<div class="tc-tl-name">{{{ card.tool_name }}}</div>
					<# } #>
					<# if ( card.tool_desc ) { #>
						<div class="tc-tl-desc">{{{ card.tool_desc }}}</div>
					<# } #>
				</div>
				<# }); #>
			</div>
			<# } #>

		</section>
		<?php
	}
}
