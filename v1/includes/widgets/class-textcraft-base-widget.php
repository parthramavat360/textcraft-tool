<?php
/**
 * TextCraft_Base_Widget — abstract foundation for every TextCraft Elementor widget.
 *
 * Provides:
 *  - Common Elementor panel controls (title, subtitle, badge text).
 *  - Shared render helpers (hero section, tool-card wrapper, stat bars, toasts).
 *  - Automatic namespaced CSS-variable injection so each widget's JS can read
 *    the accent colour set in the Elementor panel.
 *
 * @package TextCraft_Tools\Widgets
 */

declare( strict_types = 1 );

namespace TextCraft_Tools\Widgets;

use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

// ── Direct access guard ───────────────────────────────────────
defined( 'ABSPATH' ) || exit;

/**
 * Abstract base class.  Every tool widget must implement:
 *  - get_name()            — unique snake_case slug
 *  - get_title()           — human-readable widget name
 *  - get_icon()            — Elementor icon class (e.g. "eicon-text")
 *  - render_tool_content() — the inner HTML of the tool card
 */
abstract class TextCraft_Base_Widget extends Widget_Base {

	// ── Elementor identity helpers ────────────────────────────

	/** @inheritDoc */
	public function get_categories(): array {
		return [ 'textcraft-tools' ];
	}

	/** @inheritDoc */
	public function get_keywords(): array {
		return [ 'textcraft', 'text', 'tool', 'converter', 'free', 'online utility', 'browser-based' ];
	}

	// ── Panel controls ────────────────────────────────────────

	/** Register the common + widget-specific controls. */
	protected function register_controls(): void {

		// ── Section: Content ─────────────────────────────────
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'textcraft-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'tool_title',
			[
				'label'       => esc_html__( 'Title', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => $this->get_title(),
				'label_block' => true,
			]
		);

		$this->add_control(
			'tool_subtitle',
			[
				'label'       => esc_html__( 'Subtitle', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => '',
				'rows'        => 2,
				'label_block' => true,
			]
		);

		$this->add_control(
			'show_badge',
			[
				'label'        => esc_html__( 'Show Badge', 'textcraft-tools' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'textcraft-tools' ),
				'label_off'    => esc_html__( 'No', 'textcraft-tools' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'badge_text',
			[
				'label'     => esc_html__( 'Badge Text', 'textcraft-tools' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Free · Instant · No Signup', 'textcraft-tools' ),
				'condition' => [ 'show_badge' => 'yes' ],
			]
		);

		// Allow child widgets to add their own content controls.
		$this->register_tool_controls();

		$this->end_controls_section();

		// ── Section: Style ────────────────────────────────────
		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'textcraft-tools' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'accent_color',
			[
				'label'   => esc_html__( 'Accent Color', 'textcraft-tools' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#d4a24c',
				'selectors' => [
					'{{WRAPPER}}' => '--tc-accent: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'bg_color',
			[
				'label'   => esc_html__( 'Card Background', 'textcraft-tools' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#0b0b0b',
				'selectors' => [
					'{{WRAPPER}} .tc-tool-card' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// ── Section: SEO Content (elementor override) ─────
		$this->start_controls_section(
			'section_seo_content',
			[
				'label' => esc_html__( 'SEO Content', 'textcraft-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'seo_override',
			[
				'label'        => esc_html__( 'Override Default Content', 'textcraft-tools' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'textcraft-tools' ),
				'label_off'    => esc_html__( 'No (use file data)', 'textcraft-tools' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => esc_html__( 'Enable to edit all SEO content from Elementor instead of the default file.', 'textcraft-tools' ),
			]
		);

		/* ── Intro ────────────────────────────────────────── */
		$this->add_control(
			'seo_intro_heading',
			[
				'label'     => esc_html__( 'Introduction', 'textcraft-tools' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [ 'seo_override' => 'yes' ],
			]
		);

		$intro_repeater = new Repeater();
		$intro_repeater->add_control(
			'paragraph',
			[
				'label'       => esc_html__( 'Paragraph', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Introduction paragraph.', 'textcraft-tools' ),
				'rows'        => 3,
				'label_block' => true,
			]
		);

		$this->add_control(
			'seo_intro',
			[
				'label'       => esc_html__( 'Intro Paragraphs', 'textcraft-tools' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $intro_repeater->get_controls(),
				'title_field' => '{{{ paragraph }}}',
				'condition'   => [ 'seo_override' => 'yes' ],
			]
		);

		/* ── How To ───────────────────────────────────────── */
		$this->add_control(
			'seo_howto_heading',
			[
				'label'     => esc_html__( 'How To Use', 'textcraft-tools' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [ 'seo_override' => 'yes' ],
			]
		);

		$howto_repeater = new Repeater();
		$howto_repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Step Title', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Step', 'textcraft-tools' ),
				'label_block' => true,
			]
		);
		$howto_repeater->add_control(
			'desc',
			[
				'label'       => esc_html__( 'Step Description', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Description of this step.', 'textcraft-tools' ),
				'rows'        => 2,
				'label_block' => true,
			]
		);

		$this->add_control(
			'seo_howto',
			[
				'label'       => esc_html__( 'How-To Steps', 'textcraft-tools' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $howto_repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'condition'   => [ 'seo_override' => 'yes' ],
			]
		);

		/* ── Features ─────────────────────────────────────── */
		$this->add_control(
			'seo_features_heading',
			[
				'label'     => esc_html__( 'Key Features', 'textcraft-tools' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [ 'seo_override' => 'yes' ],
			]
		);

		$feat_repeater = new Repeater();
		$feat_repeater->add_control(
			'icon',
			[
				'label'   => esc_html__( 'Icon', 'textcraft-tools' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '⚡',
			]
		);
		$feat_repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Feature Title', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Feature', 'textcraft-tools' ),
				'label_block' => true,
			]
		);
		$feat_repeater->add_control(
			'desc',
			[
				'label'       => esc_html__( 'Description', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Feature description.', 'textcraft-tools' ),
				'rows'        => 2,
				'label_block' => true,
			]
		);

		$this->add_control(
			'seo_features',
			[
				'label'       => esc_html__( 'Feature Cards', 'textcraft-tools' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $feat_repeater->get_controls(),
				'title_field' => '{{{ icon }}} {{{ title }}}',
				'condition'   => [ 'seo_override' => 'yes' ],
			]
		);

		/* ── Benefits ─────────────────────────────────────── */
		$this->add_control(
			'seo_benefits_heading',
			[
				'label'     => esc_html__( 'Benefits', 'textcraft-tools' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [ 'seo_override' => 'yes' ],
			]
		);

		$ben_repeater = new Repeater();
		$ben_repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Benefit Title', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Benefit', 'textcraft-tools' ),
				'label_block' => true,
			]
		);
		$ben_repeater->add_control(
			'desc',
			[
				'label'       => esc_html__( 'Description', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Benefit description.', 'textcraft-tools' ),
				'rows'        => 2,
				'label_block' => true,
			]
		);

		$this->add_control(
			'seo_benefits',
			[
				'label'       => esc_html__( 'Benefit Items', 'textcraft-tools' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $ben_repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'condition'   => [ 'seo_override' => 'yes' ],
			]
		);

		/* ── Use Cases ────────────────────────────────────── */
		$this->add_control(
			'seo_usecases_heading',
			[
				'label'     => esc_html__( 'Who Can Benefit (Use Cases)', 'textcraft-tools' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [ 'seo_override' => 'yes' ],
			]
		);

		$uc_repeater = new Repeater();
		$uc_repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'User Group', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Who', 'textcraft-tools' ),
				'label_block' => true,
			]
		);
		$uc_repeater->add_control(
			'desc',
			[
				'label'       => esc_html__( 'Description', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'How they benefit.', 'textcraft-tools' ),
				'rows'        => 2,
				'label_block' => true,
			]
		);

		$this->add_control(
			'seo_usecases',
			[
				'label'       => esc_html__( 'Use Cases', 'textcraft-tools' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $uc_repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'condition'   => [ 'seo_override' => 'yes' ],
			]
		);

		/* ── Why Choose ───────────────────────────────────── */
		$this->add_control(
			'seo_whychoose_heading',
			[
				'label'     => esc_html__( 'Why Choose', 'textcraft-tools' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [ 'seo_override' => 'yes' ],
			]
		);

		$wc_repeater = new Repeater();
		$wc_repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Reason Title', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Reason', 'textcraft-tools' ),
				'label_block' => true,
			]
		);
		$wc_repeater->add_control(
			'desc',
			[
				'label'       => esc_html__( 'Description', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Why choose this tool.', 'textcraft-tools' ),
				'rows'        => 2,
				'label_block' => true,
			]
		);

		$this->add_control(
			'seo_whychoose',
			[
				'label'       => esc_html__( 'Why Choose Items', 'textcraft-tools' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $wc_repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'condition'   => [ 'seo_override' => 'yes' ],
			]
		);

		/* ── Media Section ────────────────────────────────── */
		$this->add_control(
			'seo_media_heading',
			[
				'label'     => esc_html__( 'Media Section (Image + Text)', 'textcraft-tools' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [ 'seo_override' => 'yes' ],
			]
		);

		$this->add_control(
			'seo_media_image',
			[
				'label'     => esc_html__( 'Image', 'textcraft-tools' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => [ 'url' => '', 'id' => '' ],
				'condition' => [ 'seo_override' => 'yes' ],
			]
		);

		$this->add_control(
			'seo_media_title',
			[
				'label'       => esc_html__( 'Media Title', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'condition'   => [ 'seo_override' => 'yes' ],
			]
		);

		$this->add_control(
			'seo_media_desc',
			[
				'label'     => esc_html__( 'Media Description', 'textcraft-tools' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => '',
				'rows'      => 4,
				'condition' => [ 'seo_override' => 'yes' ],
			]
		);

		/* ── FAQ ──────────────────────────────────────────── */
		$this->add_control(
			'seo_faq_heading',
			[
				'label'     => esc_html__( 'FAQ Accordion', 'textcraft-tools' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [ 'seo_override' => 'yes' ],
			]
		);

		$faq_repeater = new Repeater();
		$faq_repeater->add_control(
			'q',
			[
				'label'       => esc_html__( 'Question', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Question?', 'textcraft-tools' ),
				'label_block' => true,
			]
		);
		$faq_repeater->add_control(
			'a',
			[
				'label'       => esc_html__( 'Answer', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Answer.', 'textcraft-tools' ),
				'rows'        => 3,
				'label_block' => true,
			]
		);

		$this->add_control(
			'seo_faq',
			[
				'label'       => esc_html__( 'FAQ Items', 'textcraft-tools' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $faq_repeater->get_controls(),
				'title_field' => '{{{ q }}}',
				'condition'   => [ 'seo_override' => 'yes' ],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Child widgets override this to add extra panel controls
	 * inside the Content section, before it is closed.
	 */
	protected function register_tool_controls(): void {
		// Default: no extra controls.
	}

	// ── Render ────────────────────────────────────────────────

	/** Render the widget HTML on the front end. */
	protected function render(): void {
		$settings = $this->get_settings_for_display();

		$title    = ! empty( $settings['tool_title'] )    ? $settings['tool_title']    : $this->get_title();
		$subtitle = ! empty( $settings['tool_subtitle'] ) ? $settings['tool_subtitle'] : '';
		$badge    = ( 'yes' === ( $settings['show_badge'] ?? 'yes' ) ) ? ( $settings['badge_text'] ?? '' ) : '';

		$widget_class = 'tc-widget--' . sanitize_html_class( str_replace( '_', '-', $this->get_name() ) );

		echo '<div class="tc-widget-wrap textcraft-tools ' . esc_attr( $widget_class ) . '">';

		// ── Hero ─────────────────────────────────────────────
		if ( $title || $subtitle || $badge ) {
			echo '<div class="tc-hero">';
			if ( $badge ) {
				echo '<div class="tc-hero__badge">'
					. '<span class="tc-hero__badge-dot" aria-hidden="true"></span>'
					. esc_html( $badge )
					. '</div>';
			}
			if ( $title ) {
				echo '<h1 class="tc-hero__title">' . wp_kses_post( $title ) . '</h1>';
			}
			if ( $subtitle ) {
				echo '<p class="tc-hero__subtitle">' . esc_html( $subtitle ) . '</p>';
			}
			echo '</div>'; // .tc-hero
		}

		// ── Tool card ─────────────────────────────────────────
		echo '<div class="tc-tool-section">';
        echo '<article class="tc-tool-card">';
        echo '<div class="tc-card-sheen"></div>';
        $this->render_tool_content( $settings );
		echo '</article>';
		// ── JSON-LD schema (SoftwareApplication + BreadcrumbList) ─
		$this->render_schema();
		// ── SEO content sections (auto-appended after tool UI) ─
		$this->render_seo_content();
		echo '</div>'; // .tc-tool-section

		echo '</div>'; // .tc-widget-wrap
	}

	/**
	 * Child widgets implement this to output the inner tool HTML.
	 *
	 * @param array<string,mixed> $settings Elementor settings array.
	 */
	abstract protected function render_tool_content( array $settings ): void;

	// ── SEO Content Sections + FAQ Accordion ──────────────────

	/**
	 * Render SEO-optimised content sections (intro, how-to, features,
	 * benefits, use-cases, why-choose, FAQ accordion) after the tool UI.
	 *
	 * Content data is loaded from the dedicated data file.
	 * If no content exists for this widget the method renders nothing.
	 */
	protected function render_seo_content(): void {
		$settings = $this->get_settings_for_display();
		$override = 'yes' === ( $settings['seo_override'] ?? '' );

		$data = $this->get_tool_seo_content( $this->get_name() );
		if ( ! $override && empty( $data ) ) {
			return;
		}

		echo '<div class="tc-seo-wrap tc-section-has-dust"><div class="tc-dust"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>';

		// ── 1. Introduction ──────────────────────────────────
		$intro = null;
		if ( $override ) {
			$intro = ! empty( $settings['seo_intro'] ) ? array_column( $settings['seo_intro'], 'paragraph' ) : null;
		}
		if ( null === $intro ) {
			$intro = $data['intro'] ?? null;
		}
		if ( ! empty( $intro ) ) {
			echo '<section class="tc-seo-section">';
			echo '<h2>' . esc_html__( 'About This Tool', 'textcraft-tools' ) . '</h2>';
			foreach ( $intro as $paragraph ) {
				echo '<p>' . esc_html( $paragraph ) . '</p>';
			}
			echo '</section>';
		}

		// ── 2. How To Use ────────────────────────────────────
		$how_to = $override ? ( $settings['seo_howto'] ?? null ) : ( $data['how_to'] ?? null );
		if ( ! empty( $how_to ) ) {
			echo '<section class="tc-seo-section">';
			echo '<h2>' . esc_html__( 'How to Use This Tool', 'textcraft-tools' ) . '</h2>';
			echo '<ol class="tc-steps">';
			foreach ( $how_to as $step ) {
				echo '<li class="tc-step-item">'
					. '<div><strong>' . esc_html( $step['title'] ?? '' ) . '</strong>'
					. '<span>' . esc_html( $step['desc'] ?? '' ) . '</span></div>'
					. '</li>';
			}
			echo '</ol>';
			echo '</section>';
		}

		// ── 3. Features ──────────────────────────────────────
		$features = $override ? ( $settings['seo_features'] ?? null ) : ( $data['features'] ?? null );
		if ( ! empty( $features ) ) {
			echo '<section class="tc-seo-section">';
			echo '<h2>' . esc_html__( 'Key Features', 'textcraft-tools' ) . '</h2>';
			echo '<div class="tc-feature-grid">';
			foreach ( $features as $feature ) {
                echo '<div class="tc-feature-card">'
                    . '<div class="tc-card-sheen"></div>'
                    . '<span class="tc-feature-card__icon" aria-hidden="true">' . esc_html( $feature['icon'] ?? '' ) . '</span>'
					. '<h3 class="tc-feature-card__title">' . esc_html( $feature['title'] ?? '' ) . '</h3>'
					. '<p class="tc-feature-card__desc">' . esc_html( $feature['desc'] ?? '' ) . '</p>'
					. '</div>';
			}
			echo '</div>';
			echo '</section>';
		}

		// ── 4. Benefits ─────────────────────────────────────
		$benefits = $override ? ( $settings['seo_benefits'] ?? null ) : ( $data['benefits'] ?? null );
		if ( ! empty( $benefits ) ) {
			echo '<section class="tc-seo-section">';
			echo '<h2>' . esc_html__( 'Benefits', 'textcraft-tools' ) . '</h2>';
			echo '<ul>';
			foreach ( $benefits as $benefit ) {
				echo '<li><strong>' . esc_html( $benefit['title'] ?? '' ) . ':</strong> '
					. esc_html( $benefit['desc'] ?? '' ) . '</li>';
			}
			echo '</ul>';
			echo '</section>';
		}

		// ── 5. Use Cases ─────────────────────────────────────
		$use_cases = $override ? ( $settings['seo_usecases'] ?? null ) : ( $data['use_cases'] ?? null );
		if ( ! empty( $use_cases ) ) {
			echo '<section class="tc-seo-section">';
			echo '<h2>' . esc_html__( 'Who Can Benefit', 'textcraft-tools' ) . '</h2>';
			echo '<ul>';
			foreach ( $use_cases as $use_case ) {
				echo '<li><strong>' . esc_html( $use_case['title'] ?? '' ) . ':</strong> '
					. esc_html( $use_case['desc'] ?? '' ) . '</li>';
			}
			echo '</ul>';
			echo '</section>';
		}

		// ── 6. Why Choose ────────────────────────────────────
		$why_choose = $override ? ( $settings['seo_whychoose'] ?? null ) : ( $data['why_choose'] ?? null );
		if ( ! empty( $why_choose ) ) {
			echo '<section class="tc-seo-section">';
			echo '<h2>' . esc_html__( 'Why Choose This Tool', 'textcraft-tools' ) . '</h2>';
			echo '<ul>';
			foreach ( $why_choose as $reason ) {
				echo '<li><strong>' . esc_html( $reason['title'] ?? '' ) . ':</strong> '
					. esc_html( $reason['desc'] ?? '' ) . '</li>';
			}
			echo '</ul>';
			echo '</section>';
		}

		// ── 7. Image + Description SEO section ──────────────
		$media_image_id  = $override ? ( $settings['seo_media_image']['id']  ?? '' ) : '';
		$media_image_url = $override ? ( $settings['seo_media_image']['url'] ?? '' ) : '';
		$media_title     = $override ? ( $settings['seo_media_title']        ?? '' ) : ( $data['media_title'] ?? '' );
		$media_desc      = $override ? ( $settings['seo_media_desc']         ?? '' ) : ( $data['media_desc']  ?? '' );

		// Auto-load feature image from plugin assets when no override is set
		if ( ! $override && empty( $media_image_url ) ) {
			$svg_name = $this->get_widget_svg_slug() . '.svg';
			$svg_path = TEXTCRAFT_PLUGIN_DIR . 'assets/images/tools/' . $svg_name;
			if ( file_exists( $svg_path ) ) {
				$media_image_url = plugins_url( 'assets/images/tools/' . $svg_name, TEXTCRAFT_PLUGIN_DIR . 'textcraft-tools.php' );
			}
		}

		if ( $media_title || $media_desc || $media_image_url || ! $override ) {
			echo '<div class="tc-seo-media-section">';
			echo '<div class="tc-seo-media-grid">';
			echo '<div class="tc-seo-media-visual">';
			if ( $media_image_url ) {
				$alt = $media_title ?: '';
				echo '<div class="tc-seo-media-image">';
				echo '<img src="' . esc_url( $media_image_url ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy" decoding="async" style="max-width:100%;height:auto;border-radius:20px;display:block;">';
				echo '</div>';
			} else {
				echo '<div class="tc-seo-media-image tc-seo-media-icon-fallback">';
				echo '<span class="tc-seo-media-emoji" aria-hidden="true">🛠️</span>';
				echo '</div>';
			}
			echo '</div>';
			echo '<div class="tc-seo-media-content">';
			if ( $media_title ) {
				echo '<h3 class="tc-seo-media-title">' . esc_html( $media_title ) . '</h3>';
			}
			if ( $media_desc ) {
				echo '<p class="tc-seo-media-desc">' . esc_html( $media_desc ) . '</p>';
			} elseif ( ! $override && ! empty( $data['media_desc'] ) ) {
				echo '<p class="tc-seo-media-desc">' . esc_html( $data['media_desc'] ) . '</p>';
			} elseif ( ! $override ) {
				echo '<p class="tc-seo-media-desc">' . esc_html__( 'Every TextCraft tool processes your data entirely in your browser — nothing is uploaded to any server. This means your files, text, and personal information stay completely private on your device. Whether you are converting documents, editing images, or generating random data, you can work with confidence knowing your content never leaves your computer.', 'textcraft-tools' ) . '</p>';
			}
			echo '</div></div></div>';
		}

		// ── 8. FAQ Accordion ───────────────────────────────
		$faq = $override ? ( $settings['seo_faq'] ?? null ) : ( $data['faq'] ?? null );
		if ( ! empty( $faq ) ) {
			echo '<section class="tc-seo-section">';
			echo '<h2>' . esc_html__( 'Frequently Asked Questions', 'textcraft-tools' ) . '</h2>';
			echo '<div class="tc-faq-accordion" data-tc-faq-accordion>';
			foreach ( $faq as $faq_item ) {
                echo '<div class="tc-faq-item">'
                    . '<div class="tc-card-sheen"></div>'
                    . '<button class="tc-faq-question" type="button" aria-expanded="false">'
					. '<span class="tc-faq-question-text">' . esc_html( $faq_item['q'] ?? '' ) . '</span>'
					. '<span class="tc-faq-icon" aria-hidden="true">+</span>'
					. '</button>'
					. '<div class="tc-faq-answer-wrap"><div class="tc-faq-answer" hidden>'
					. '<p>' . esc_html( $faq_item['a'] ?? '' ) . '</p>'
					. '</div></div>'
					. '</div>';
			}
			echo '</div>'; // .tc-faq-accordion
			echo '</section>';
		}

		// ── 9. Related Tools (internal linking) ──────────────
		$this->render_related_tools( $this->get_name() );

		echo '</div>'; // .tc-seo-wrap
	}

	/** @var array|null Cached SEO content data. */
	private static ?array $seo_content_cache = null;

	/**
	 * Look up the SEO content array for a given widget name.
	 *
	 * @param string $widget_name Elementor widget name (snake_case).
	 * @return array{intro?:string[],how_to?:array[],features?:array[],benefits?:array[],use_cases?:array[],why_choose?:array[],faq?:array[]}
	 */
	private function get_tool_seo_content( string $widget_name ): array {
		if ( null === self::$seo_content_cache ) {
			$file = TEXTCRAFT_PLUGIN_DIR . 'includes/seo-content-data.php';
			if ( ! file_exists( $file ) ) {
				self::$seo_content_cache = [];
			} else {
				$loaded = require $file;
				self::$seo_content_cache = is_array( $loaded ) ? $loaded : [];
			}
		}
		return self::$seo_content_cache[ $widget_name ] ?? [];
	}

	// ── JSON-LD Schema ────────────────────────────────────────

	/**
	 * Output SoftwareApplication, BreadcrumbList, and FAQPage JSON-LD.
	 */
	protected function render_schema(): void {
		if ( defined( 'RANK_MATH_VERSION' ) || class_exists( '\\RankMath\\Helper' ) ) {
			return;
		}

		$settings = $this->get_settings_for_display();
		$override = 'yes' === ( $settings['seo_override'] ?? '' );

		$data = $this->get_tool_seo_content( $this->get_name() );
		if ( ! $override && empty( $data ) ) {
			return;
		}

		$post  = get_queried_object();
		$url   = ( $post instanceof \WP_Post ) ? get_permalink( $post ) : '';
		$title = ! empty( $settings['tool_title'] )
			? $settings['tool_title']
			: $this->get_title();

		$desc = '';
		if ( $override && ! empty( $settings['seo_intro'] ) ) {
			$first = $settings['seo_intro'][0]['paragraph'] ?? '';
			if ( $first ) {
				$desc = $first;
			}
		}
		if ( ! $desc && ! empty( $data['intro'] ) ) {
			$desc = is_array( $data['intro'] ) ? $data['intro'][0] : (string) $data['intro'];
		}

		$schema = [
			'@context' => 'https://schema.org',
			'@graph'   => [],
		];

		// BreadcrumbList
		if ( $url ) {
			$schema['@graph'][] = [
				'@type'           => 'BreadcrumbList',
				'@id'             => $url . '#breadcrumb',
				'itemListElement' => [
					[
						'@type'    => 'ListItem',
						'position' => 1,
						'name'     => get_bloginfo( 'name' ),
						'item'     => get_site_url(),
					],
					[
						'@type'    => 'ListItem',
						'position' => 2,
						'name'     => $title,
						'item'     => $url,
					],
				],
			];
		}

		// SoftwareApplication
		$software = [
			'@type'              => 'SoftwareApplication',
			'@id'                => $url ? $url . '#softwareapplication' : '',
			'name'               => $title,
			'applicationCategory' => 'UtilitiesApplication',
			'operatingSystem'    => 'All (Web-based)',
			'description'        => $desc ?: $title,
			'url'                => $url,
			'offers'             => [
				'@type'         => 'Offer',
				'price'         => '0',
				'priceCurrency' => 'USD',
			],
		];
		$schema['@graph'][] = $software;

		// FAQPage
		$faq_data = $override ? ( $settings['seo_faq'] ?? null ) : ( $data['faq'] ?? null );
		if ( ! empty( $faq_data ) ) {
			$main_entity = [];
			foreach ( $faq_data as $faq ) {
				$main_entity[] = [
					'@type'          => 'Question',
					'name'           => $faq['q'] ?? '',
					'acceptedAnswer' => [
						'@type' => 'Answer',
						'text'  => $faq['a'] ?? '',
					],
				];
			}
			$schema['@graph'][] = [
				'@type'       => 'FAQPage',
				'@id'         => $url ? $url . '#faq' : '',
				'mainEntity'  => $main_entity,
			];
		}

		$encoded = wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		if ( false === $encoded ) {
			return;
		}
		echo '<script type="application/ld+json">' . "\n";
		echo $encoded . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON already safe
		echo '</script>' . "\n";
	}

	// ── Internal Linking (Related Tools) ──────────────────────

	/**
	 * Build a map of tool categories for internal linking.
	 *
	 * @return array<string,array{label:string,tools:array<string,string>}>
	 */
	private function get_tool_categories(): array {
		$cats = [
			'case-converters' => [
				'label' => __( 'Case Converters', 'textcraft-tools' ),
				'tools' => [
					'widget_case_converter'  => __( 'Case Converter', 'textcraft-tools' ),
					'widget_sentence_case'   => __( 'Sentence Case Converter', 'textcraft-tools' ),
					'widget_title_case'      => __( 'Title Case Converter', 'textcraft-tools' ),
				],
			],
			'text-cleaners' => [
				'label' => __( 'Text Cleaners', 'textcraft-tools' ),
				'tools' => [
					'widget_character_remover'  => __( 'Character Remover', 'textcraft-tools' ),
					'widget_duplicate_line'     => __( 'Duplicate Line Remover', 'textcraft-tools' ),
					'widget_duplicate_word'     => __( 'Duplicate Word Remover', 'textcraft-tools' ),
					'widget_em_dash_remover'    => __( 'Em Dash Remover', 'textcraft-tools' ),
					'widget_remove_line_breaks' => __( 'Remove Line Breaks', 'textcraft-tools' ),
					'widget_remove_formatting'  => __( 'Remove Formatting', 'textcraft-tools' ),
					'widget_remove_underscores' => __( 'Remove Underscores', 'textcraft-tools' ),
					'widget_whitespace_remover' => __( 'Whitespace Remover', 'textcraft-tools' ),
					'widget_plain_text'         => __( 'Convert to Plain Text', 'textcraft-tools' ),
				],
			],
			'text-generators' => [
				'label' => __( 'Text Generators', 'textcraft-tools' ),
				'tools' => [
					'widget_apa_format'      => __( 'APA Format Generator', 'textcraft-tools' ),
					'widget_invisible_text'  => __( 'Invisible Text Generator', 'textcraft-tools' ),
					'widget_online_notepad'  => __( 'Online Notepad', 'textcraft-tools' ),
					'widget_repeat_text'     => __( 'Repeat Text Generator', 'textcraft-tools' ),
					'widget_reverse_text'    => __( 'Reverse Text Generator', 'textcraft-tools' ),
					'widget_roman_numeral'   => __( 'Roman Numeral Converter', 'textcraft-tools' ),
					'widget_word_cloud'      => __( 'Word Cloud Generator', 'textcraft-tools' ),
				],
			],
			'random-generators' => [
				'label' => __( 'Random Generators', 'textcraft-tools' ),
				'tools' => [
					'widget_random_choice'    => __( 'Random Choice Generator', 'textcraft-tools' ),
					'widget_random_date'      => __( 'Random Date Generator', 'textcraft-tools' ),
					'widget_random_ip'        => __( 'Random IP Generator', 'textcraft-tools' ),
					'widget_random_letter'    => __( 'Random Letter Generator', 'textcraft-tools' ),
					'widget_random_month'     => __( 'Random Month Generator', 'textcraft-tools' ),
					'widget_random_number'    => __( 'Random Number Generator', 'textcraft-tools' ),
					'widget_password_generator' => __( 'Password Generator', 'textcraft-tools' ),
					'widget_uuid_generator'   => __( 'UUID Generator', 'textcraft-tools' ),
				],
			],
			'translators-counters' => [
				'label' => __( 'Text Tools', 'textcraft-tools' ),
				'tools' => [
					'widget_find_replace'       => __( 'Find and Replace', 'textcraft-tools' ),
					'widget_nato_phonetic'      => __( 'NATO Phonetic Converter', 'textcraft-tools' ),
					'widget_sentence_counter'   => __( 'Sentence Counter', 'textcraft-tools' ),
					'widget_phonetic_spelling'  => __( 'Phonetic Spelling Converter', 'textcraft-tools' ),
					'widget_pig_latin'          => __( 'Pig Latin Translator', 'textcraft-tools' ),
					'widget_sort_words'         => __( 'Sort Words Online', 'textcraft-tools' ),
					'widget_wingdings'          => __( 'Wingdings Translator', 'textcraft-tools' ),
					'widget_word_frequency'     => __( 'Word Frequency Counter', 'textcraft-tools' ),
				],
			],
			'image-compressors' => [
				'label' => __( 'Image Compressors', 'textcraft-tools' ),
				'tools' => [
					'widget_jpg_compressor'  => __( 'JPG Compressor', 'textcraft-tools' ),
					'widget_png_compressor'  => __( 'PNG Compressor', 'textcraft-tools' ),
					'widget_webp_compressor' => __( 'WebP Compressor', 'textcraft-tools' ),
					'widget_gif_compressor'  => __( 'GIF Compressor', 'textcraft-tools' ),
					'widget_svg_compressor'  => __( 'SVG Compressor', 'textcraft-tools' ),
				],
			],
			'image-converters' => [
				'label' => __( 'Image Converters & Editors', 'textcraft-tools' ),
				'tools' => [
					'widget_jpg_to_png'   => __( 'JPG to PNG Converter', 'textcraft-tools' ),
					'widget_jpg_to_webp'  => __( 'JPG to WebP Converter', 'textcraft-tools' ),
					'widget_jpg_to_svg'   => __( 'JPG to SVG Converter', 'textcraft-tools' ),
					'widget_jpg_to_gif'   => __( 'JPG to GIF Converter', 'textcraft-tools' ),
					'widget_jpg_to_heic'  => __( 'JPG to HEIC Converter', 'textcraft-tools' ),
					'widget_jpg_to_avif'  => __( 'JPG to AVIF Converter', 'textcraft-tools' ),
					'widget_png_to_jpg'   => __( 'PNG to JPG Converter', 'textcraft-tools' ),
					'widget_png_to_webp'  => __( 'PNG to WebP Converter', 'textcraft-tools' ),
					'widget_png_to_svg'   => __( 'PNG to SVG Converter', 'textcraft-tools' ),
					'widget_png_to_heic'  => __( 'PNG to HEIC Converter', 'textcraft-tools' ),
					'widget_heic_to_jpg'  => __( 'HEIC to JPG Converter', 'textcraft-tools' ),
					'widget_heic_to_png'  => __( 'HEIC to PNG Converter', 'textcraft-tools' ),
					'widget_heic_to_svg'  => __( 'HEIC to SVG Converter', 'textcraft-tools' ),
					'widget_webp_to_jpg'  => __( 'WebP to JPG Converter', 'textcraft-tools' ),
					'widget_webp_to_png'  => __( 'WebP to PNG Converter', 'textcraft-tools' ),
					'widget_jpg_to_pdf'   => __( 'JPG to PDF Converter', 'textcraft-tools' ),
					'widget_png_to_pdf'   => __( 'PNG to PDF Converter', 'textcraft-tools' ),
					'widget_video_converter' => __( 'Video Converter', 'textcraft-tools' ),
					'widget_ascii_art'    => __( 'ASCII Art Generator', 'textcraft-tools' ),
					'widget_image_to_text' => __( 'Image to Text Converter', 'textcraft-tools' ),
					'widget_remove_background' => __( 'Remove Background from Image', 'textcraft-tools' ),
				],
			],
			'pdf-tools' => [
				'label' => __( 'PDF Tools', 'textcraft-tools' ),
				'tools' => [
					'widget_pdf_compressor'    => __( 'PDF Compressor', 'textcraft-tools' ),
					'widget_pdf_merger'        => __( 'PDF Merger', 'textcraft-tools' ),
					'widget_pdf_splitter'      => __( 'PDF Splitter', 'textcraft-tools' ),
					'widget_pdf_to_jpg'        => __( 'PDF to JPG Converter', 'textcraft-tools' ),
					'widget_pdf_to_png'        => __( 'PDF to PNG Converter', 'textcraft-tools' ),
					'widget_rotate_pdf'        => __( 'Rotate PDF', 'textcraft-tools' ),
					'widget_delete_pdf_pages'  => __( 'Delete PDF Pages', 'textcraft-tools' ),
				],
			],
		];

		/**
		 * Filter the tool categories map for internal linking.
		 *
		 * @param array $categories Category map.
		 */
		return apply_filters( 'textcraft_tool_categories', $cats );
	}

	/**
	 * Get related tool names for a given widget name.
	 *
	 * @param string $widget_name Elementor widget name.
	 * @return array<string,string> Slug => label pairs.
	 */
	private function get_related_tools_for( string $widget_name ): array {
		$categories = $this->get_tool_categories();
		foreach ( $categories as $cat ) {
			if ( isset( $cat['tools'][ $widget_name ] ) ) {
				$related = $cat['tools'];
				unset( $related[ $widget_name ] );
				return $related;
			}
		}
		return [];
	}

	/**
	 * Render the Related Tools section with internal links.
	 *
	 * @param string $widget_name Current widget name.
	 */
	protected function render_related_tools( string $widget_name ): void {
		$related = $this->get_related_tools_for( $widget_name );
		if ( empty( $related ) ) {
			return;
		}

		/**
		 * Base URL for tool pages. Override via filter to match your permalink structure.
		 * Default: /tools/{widget-slug}/
		 */
		$base_url = untrailingslashit( (string) apply_filters( 'textcraft_tools_base_url', home_url( '/tools' ) ) );

		echo '<section class="tc-seo-section tc-related-tools">';
		echo '<h2>' . esc_html__( 'Related Tools', 'textcraft-tools' ) . '</h2>';
		echo '<ul class="tc-related-tools-list">';
		foreach ( $related as $slug => $label ) {
			$slug_clean = str_replace( 'widget_', '', $slug );
			$slug_hyphen = str_replace( '_', '-', $slug_clean );
			$url = trailingslashit( $base_url . '/' . $slug_hyphen );
			echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
		}
		echo '</ul>';
		echo '</section>';
	}

	// ── Shared HTML helpers ───────────────────────────────────

	/**
	 * Output a labelled textarea with an optional character counter.
	 *
	 * @param string $id           DOM id attribute.
	 * @param string $label        Visible label text.
	 * @param string $placeholder  Textarea placeholder.
	 * @param int    $rows         Visible rows.
	 * @param bool   $readonly     Whether the textarea is read-only.
	 */
	protected function render_textarea(
		string $id,
		string $label,
		string $placeholder = '',
		int    $rows         = 8,
		bool   $readonly     = false
	): void {
		$counter_id = $id . '-char-count';
		$css_class  = $readonly ? 'tc-textarea tc-textarea--output' : 'tc-textarea tc-textarea--input';
		$ro_attr    = $readonly ? ' readonly' : '';

		echo '<div class="tc-label-row">';
		echo '<label for="' . esc_attr( $id ) . '" class="tc-label">' . esc_html( $label ) . '</label>';
		if ( ! $readonly ) {
			echo '<span class="tc-char-count" id="' . esc_attr( $counter_id ) . '" aria-live="polite">0 characters</span>';
		}
		echo '</div>';

		echo '<textarea'
			. ' id="' . esc_attr( $id ) . '"'
			. ' class="' . esc_attr( $css_class ) . '"'
			. ' placeholder="' . esc_attr( $placeholder ) . '"'
			. ' rows="' . esc_attr( (string) $rows ) . '"'
			. ' spellcheck="false"'
			. $ro_attr
			. '></textarea>';
	}

	/**
	 * Output a row of action buttons.
	 *
	 * @param array<array{id: string, label: string, variant?: string, disabled?: bool}> $buttons
	 */
	protected function render_button_row( array $buttons ): void {
		echo '<div class="tc-btn-row">';
		foreach ( $buttons as $btn ) {
			$variant  = $btn['variant'] ?? 'primary';
			$disabled = ! empty( $btn['disabled'] ) ? ' disabled' : '';
			echo '<button'
				. ' type="button"'
				. ' id="' . esc_attr( $btn['id'] ) . '"'
				. ' class="tc-btn tc-btn--' . esc_attr( $variant ) . '"'
				. $disabled
				. '>'
				. esc_html( $btn['label'] )
				. '</button>';
		}
		echo '</div>';
	}

	/**
	 * Output a statistics bar.
	 *
	 * @param array<array{id: string, label: string}> $stats
	 */
	protected function render_stat_bar( array $stats ): void {
		echo '<div class="tc-stat-bar" aria-live="polite">';
		$last = array_key_last( $stats );
		foreach ( $stats as $key => $stat ) {
			echo '<div class="tc-stat">'
				. '<span class="tc-stat__label">' . esc_html( $stat['label'] ) . '</span>'
				. '<span class="tc-stat__value" id="' . esc_attr( $stat['id'] ) . '">0</span>'
				. '</div>';
			if ( $key !== $last ) {
				echo '<div class="tc-stat-sep" aria-hidden="true"></div>';
			}
		}
		echo '</div>';
	}

	/**
	 * Output an options row (checkboxes / toggles).
	 *
	 * @param array<array{id: string, label: string, checked?: bool}> $options
	 */
	protected function render_options_row( array $options ): void {
		echo '<div class="tc-options-row">';
		foreach ( $options as $opt ) {
			$checked = ! empty( $opt['checked'] ) ? ' checked' : '';
			echo '<label class="tc-option">'
				. '<input type="checkbox" id="' . esc_attr( $opt['id'] ) . '"' . $checked . '>'
				. '<span>' . esc_html( $opt['label'] ) . '</span>'
				. '</label>';
		}
		echo '</div>';
	}

	/**
	 * Output an inline `<script>` tag containing widget JavaScript.
	 * The JS is executed in a self-calling closure to avoid global scope pollution.
	 *
	 * @param string $js Raw JavaScript (WITHOUT surrounding <script> tags).
	 */
	protected function render_inline_script( string $js ): void {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- intentional JS output
		echo "\n<script>(function(){\n'use strict';\n" . $js . "\n})();</script>\n";
	}

	/**
	 * Derive an SVG file slug from the widget name.
	 * e.g. textcraft_pdf_compressor → pdf-compressor
	 */
	private function get_widget_svg_slug(): string {
		$name = $this->get_name();
		$name = preg_replace( '/^textcraft_/i', '', $name );
		return str_replace( '_', '-', $name );
	}
}
