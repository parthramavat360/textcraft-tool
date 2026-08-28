<?php
/**
 * Legal & Policies Elementor Widget
 *
 * A fully dynamic "Legal & Policies" page widget: hero with badges,
 * a stack of policy doc-cards (each built from rich repeater blocks),
 * a sticky sidebar (On this page / At a glance / Report an issue) and
 * an optional 3-column strip. Replicates the reference
 * textcraft-legal-policies.html design.
 *
 * @package    TextCraftToolsPro
 * @subpackage Widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TextCraft_Legal_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'tctp_legal';
	}

	public function get_title() {
		return __( 'TCTP: Legal & Policies', 'textcrafttoolspro' );
	}

	public function get_icon() {
		return 'eicon-document-file';
	}

	public function get_categories() {
		return [ 'textcrafttools' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_hero', [
			'label' => __( '1. Hero', 'textcrafttoolspro' ),
		] );

		$this->add_control( 'hero_show', [
			'label'        => __( 'Show hero', 'textcrafttoolspro' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'label_on'     => __( 'Show', 'textcrafttoolspro' ),
			'label_off'    => __( 'Hide', 'textcrafttoolspro' ),
			'return_value' => 'yes',
			'default'      => 'yes',
		] );
		// Place this notify-first then the standard fields, else default applies.
		$this->add_control( 'hero_badge', [
			'label'   => __( 'Status badge', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Files never leave your browser',
		] );
		$this->add_control( 'hero_title', [
			'label'   => __( 'Title', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Legal, privacy & publishing policies',
		] );
		$this->add_control( 'hero_lede', [
			'label'   => __( 'Intro text', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'rows'    => 3,
			'default' => 'Nine documents, one page. Everything that governs how TextCraft Tools handles your files, your data, our content and the ads that keep the tools free — written to be read, not skimmed past.',
		] );

		$badges_repeater = new \Elementor\Repeater();
		$badges_repeater->add_control( 'label', [
			'label'       => __( 'Pill text', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => 'Plain-language policies',
			'placeholder' => __( 'Pill text', 'textcrafttoolspro' ),
		] );
		$badges_repeater->add_control( 'ok', [
			'label'        => __( 'Highlight (green pill)', 'textcrafttoolspro' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'label_on'     => __( 'Yes', 'textcrafttoolspro' ),
			'label_off'    => __( 'No', 'textcrafttoolspro' ),
			'return_value' => 'yes',
			'default'      => 'yes',
		] );
		$this->add_control( 'hero_badges', [
			'label'       => __( 'Badges', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $badges_repeater->get_controls(),
			'title_field' => '{{{ label }}}',
			'default'     => [
				[ 'label' => 'Files never leave your browser', 'ok' => 'yes' ],
				[ 'label' => 'Plain-language policies', 'ok' => '' ],
				[ 'label' => 'WCAG 2.2 AA target', 'ok' => '' ],
			],
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_docs', [
			'label' => __( '2. Policy Documents', 'textcrafttoolspro' ),
		] );

		$docs_repeater = new \Elementor\Repeater();

		$docs_repeater->add_control( 'doc_id', [
			'label'       => __( 'Anchor ID (slug)', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => 'privacy-policy',
			'description' => __( 'Lowercase, hyphenated. Used for the section id and the "On this page" sidebar link.', 'textcrafttoolspro' ),
		] );
		$docs_repeater->add_control( 'doc_title', [
			'label'   => __( 'Title', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Privacy Policy',
		] );
		$docs_repeater->add_control( 'doc_meta', [
			'label'   => __( 'Meta (date)', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Last updated 28 August 2026',
		] );
		$docs_repeater->add_control( 'doc_kicker', [
			'label'   => __( 'Kicker badge', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Your files stay in your tab',
		] );
		$docs_repeater->add_control( 'doc_lede', [
			'label'   => __( 'Intro line', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'rows'    => 2,
			'default' => 'Almost every TextCraft tool runs entirely in your browser. The documents, images and text you process are not uploaded to our servers, and we keep the data we do collect to a minimum.',
		] );

		// Nested content blocks per document.
		$blocks_repeater = new \Elementor\Repeater();

		$blocks_repeater->add_control( 'block_type', [
			'label'   => __( 'Block type', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'paragraph',
			'options' => [
				'paragraph'     => __( 'Paragraph', 'textcrafttoolspro' ),
				'heading'       => __( 'Heading', 'textcrafttoolspro' ),
				'bullets'       => __( 'Bullets (bold lead + text)', 'textcrafttoolspro' ),
				'mini_list'     => __( 'Arrow list', 'textcrafttoolspro' ),
				'callout'       => __( 'Callout (tick)', 'textcrafttoolspro' ),
				'feature_cards' => __( 'Feature cards (2-col grid)', 'textcrafttoolspro' ),
				'faq'           => __( 'FAQ accordion', 'textcrafttoolspro' ),
			],
		] );
		$blocks_repeater->add_control( 'heading', [
			'label'       => __( 'Heading / intro text', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::TEXTAREA,
			'rows'        => 2,
			'placeholder' => __( 'Used by heading, bullets and arrow-list blocks.', 'textcrafttoolspro' ),
		] );
		$blocks_repeater->add_control( 'paragraph', [
			'label'   => __( 'Paragraph text', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'rows'    => 3,
		] );
		$blocks_repeater->add_control( 'callout', [
			'label'   => __( 'Callout text (after ✓)', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'rows'    => 2,
		] );

		$bullet_repeater = new \Elementor\Repeater();
		$bullet_repeater->add_control( 'lead', [
			'label'       => __( 'Bold lead', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => 'Keyboard operable',
		] );
		$bullet_repeater->add_control( 'text', [
			'label'       => __( 'Text after lead', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::TEXTAREA,
			'rows'        => 2,
		] );
		$blocks_repeater->add_control( 'bullets', [
			'label'       => __( 'Bullet items', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $bullet_repeater->get_controls(),
			'title_field' => '{{{ lead }}}',
		] );

		$item_repeater = new \Elementor\Repeater();
		$item_repeater->add_control( 'text', [
			'label'   => __( 'List item', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'rows'    => 1,
		] );
		$blocks_repeater->add_control( 'items', [
			'label'       => __( 'List items', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $item_repeater->get_controls(),
			'title_field' => '{{{ text }}}',
		] );

		$feature_repeater = new \Elementor\Repeater();
		$feature_repeater->add_control( 'title', [
			'label'   => __( 'Card title', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Data we never see',
		] );
		$feature_repeater->add_control( 'text', [
			'label'   => __( 'Card text', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'rows'    => 2,
		] );
		$blocks_repeater->add_control( 'feature_cards', [
			'label'       => __( 'Feature cards', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $feature_repeater->get_controls(),
			'title_field' => '{{{ title }}}',
		] );

		$faq_repeater = new \Elementor\Repeater();
		$faq_repeater->add_control( 'q', [
			'label'   => __( 'Question', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Question?',
		] );
		$faq_repeater->add_control( 'a', [
			'label'   => __( 'Answer', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'rows'    => 3,
		] );
		$blocks_repeater->add_control( 'faq_items', [
			'label'       => __( 'FAQ items', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $faq_repeater->get_controls(),
			'title_field' => '{{{ q }}}',
		] );

		$docs_repeater->add_control( 'doc_blocks', [
			'label'       => __( 'Content blocks', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $blocks_repeater->get_controls(),
			'title_field' => '{{{ block_type }}}',
			'prevent_empty' => false,
		] );

		$this->add_control( 'documents', [
			'label'       => __( 'Documents', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $docs_repeater->get_controls(),
			'title_field' => '{{{ doc_title }}}',
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_cta', [
			'label' => __( '3. CTA band', 'textcrafttoolspro' ),
		] );
		$this->add_control( 'cta_show', [
			'label'        => __( 'Show CTA band', 'textcrafttoolspro' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		] );
		$this->add_control( 'cta_title', [
			'label'   => __( 'Title', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Still have a question about any of this?',
		] );
		$this->add_control( 'cta_text', [
			'label'   => __( 'Text', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'rows'    => 2,
			'default' => 'One inbox handles every policy request — privacy, takedowns, accessibility barriers and corrections. Real replies, usually within five business days.',
		] );
		$this->add_control( 'cta_btn1_text', [
			'label'   => __( 'Primary button text', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Email the legal team',
		] );
		$this->add_control( 'cta_btn1_url', [
			'label'   => __( 'Primary button URL', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::URL,
			'default' => [ 'url' => 'mailto:legal@textcrafttools.com' ],
		] );
		$this->add_control( 'cta_btn2_text', [
			'label'   => __( 'Secondary button text', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Back to the tools',
		] );
		$this->add_control( 'cta_btn2_url', [
			'label'   => __( 'Secondary button URL', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::URL,
			'default' => [ 'url' => 'http://localhost/wordpress/' ],
		] );
		$this->end_controls_section();

		$this->start_controls_section( 'section_sidebar', [
			'label' => __( '4. Sidebar', 'textcrafttoolspro' ),
		] );
		$this->add_control( 'glance_show', [
			'label'        => __( 'Show "At a glance" box', 'textcrafttoolspro' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		] );
		$glance_repeater = new \Elementor\Repeater();
		$glance_repeater->add_control( 'text', [
			'label'   => __( 'Item', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'No file uploads for client-side tools',
		] );
		$this->add_control( 'glance_items', [
			'label'       => __( 'At a glance items', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $glance_repeater->get_controls(),
			'title_field' => '{{{ text }}}',
			'default'     => [
				[ 'text' => 'No file uploads for client-side tools' ],
				[ 'text' => 'Consent-based analytics and ads only' ],
				[ 'text' => 'Editorial independent of advertisers' ],
			],
		] );
		$this->add_control( 'issue_show', [
			'label'        => __( 'Show "Report an issue" box', 'textcrafttoolspro' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		] );
		$this->add_control( 'issue_title', [
			'label'   => __( 'Report title', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Report an issue',
		] );
		$this->add_control( 'issue_text', [
			'label'   => __( 'Report text', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'rows'    => 3,
			'default' => 'Bad ad, broken screen-reader flow, or a factual error in a guide? Send the URL and we\'ll fix it.',
		] );
		$this->add_control( 'issue_btn_text', [
			'label'   => __( 'Report button text', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Contact support',
		] );
		$this->add_control( 'issue_btn_url', [
			'label'   => __( 'Report button URL', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::URL,
			'default' => [ 'url' => 'mailto:support@textcrafttools.com' ],
		] );
		$this->end_controls_section();

		$this->start_controls_section( 'section_strip', [
			'label' => __( '5. Strip (3 columns)', 'textcrafttoolspro' ),
		] );
		$this->add_control( 'strip_show', [
			'label'        => __( 'Show strip', 'textcrafttoolspro' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		] );
		$strip_repeater = new \Elementor\Repeater();
		$strip_repeater->add_control( 'title', [
			'label'   => __( 'Title', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Local-first by design',
		] );
		$strip_repeater->add_control( 'text', [
			'label'   => __( 'Text', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'rows'    => 2,
			'default' => 'Compression, conversion and text tools run in your tab using WebAssembly, so your documents stay on your device.',
		] );
		$this->add_control( 'strip_items', [
			'label'       => __( 'Columns', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $strip_repeater->get_controls(),
			'title_field' => '{{{ title }}}',
			'default'     => [
				[ 'title' => 'Local-first by design', 'text' => 'Compression, conversion and text tools run in your tab using WebAssembly, so your documents stay on your device.' ],
				[ 'title' => 'Minimal data collection', 'text' => 'Aggregated usage counts and short-lived security logs. No accounts required, no file contents retained.' ],
				[ 'title' => 'Answerable to you', 'text' => 'Named editors, dated corrections, published contact routes for every policy on this page.' ],
			],
		] );
		$this->end_controls_section();
	}

	protected function render_blocks( $blocks ) {
		$out = '';
		foreach ( $blocks as $b ) {
			$type = isset( $b['block_type'] ) ? $b['block_type'] : 'paragraph';
			switch ( $type ) {
				case 'heading':
					if ( ! empty( $b['heading'] ) ) {
						$out .= '<h3>' . esc_html( $b['heading'] ) . '</h3>';
					}
					break;

				case 'paragraph':
					if ( ! empty( $b['paragraph'] ) ) {
						$out .= '<p>' . esc_html( $b['paragraph'] ) . '</p>';
					}
					break;

				case 'bullets':
					$items = '';
					if ( ! empty( $b['bullets'] ) && is_array( $b['bullets'] ) ) {
						foreach ( $b['bullets'] as $bi ) {
							$lead = ! empty( $bi['lead'] ) ? '<strong>' . esc_html( $bi['lead'] ) . '</strong>' : '';
							$txt  = ! empty( $bi['text'] ) ? ' — ' . esc_html( $bi['text'] ) : '';
							$items .= '<li>' . $lead . $txt . '</li>';
						}
					}
					if ( $items ) {
						$out .= '<ul class="tclp-bullets">' . $items . '</ul>';
					}
					break;

				case 'mini_list':
					$items = '';
					if ( ! empty( $b['items'] ) && is_array( $b['items'] ) ) {
						foreach ( $b['items'] as $mi ) {
							if ( ! empty( $mi['text'] ) ) {
								$items .= '<li>' . esc_html( $mi['text'] ) . '</li>';
							}
						}
					}
					if ( $items ) {
						$out .= '<ul class="tclp-mini-list">' . $items . '</ul>';
					}
					break;

				case 'callout':
					if ( ! empty( $b['callout'] ) ) {
						$out .= '<div class="tclp-callout"><span class="tclp-tick">✓</span><span>' . esc_html( $b['callout'] ) . '</span></div>';
					}
					break;

				case 'feature_cards':
					if ( ! empty( $b['feature_cards'] ) && is_array( $b['feature_cards'] ) ) {
						$cards = '';
						foreach ( $b['feature_cards'] as $fc ) {
							$cards .= '<div class="tclp-fcard"><h4>' . esc_html( $fc['title'] ) . '</h4><p>' . esc_html( $fc['text'] ) . '</p></div>';
						}
						if ( $cards ) {
							$out .= '<div class="tclp-grid-2">' . $cards . '</div>';
						}
					}
					break;

				case 'faq':
					if ( ! empty( $b['faq_items'] ) && is_array( $b['faq_items'] ) ) {
						$faq = '';
						foreach ( $b['faq_items'] as $fi ) {
							$faq .= '<details><summary>' . esc_html( $fi['q'] ) . '</summary><p>' . esc_html( $fi['a'] ) . '</p></details>';
						}
						if ( $faq ) {
							$out .= '<div class="tclp-faq">' . $faq . '</div>';
						}
					}
					break;
			}
		}
		return $out;
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		// Build the anchor list from documents for the sidebar.
		$docs   = ! empty( $s['documents'] ) && is_array( $s['documents'] ) ? $s['documents'] : [];
		$anchors = [];
		foreach ( $docs as $d ) {
			$id = ! empty( $d['doc_id'] ) ? sanitize_title( $d['doc_id'] ) : '';
			if ( $id ) {
				$anchors[] = [ 'id' => $id, 'title' => $d['doc_title'] ];
			}
		}

		$cta1_url = ! empty( $s['cta_btn1_url']['url'] ) ? $s['cta_btn1_url']['url'] : '#';
		$cta2_url = ! empty( $s['cta_btn2_url']['url'] ) ? $s['cta_btn2_url']['url'] : '#';
		$issue_url = ! empty( $s['issue_btn_url']['url'] ) ? $s['issue_btn_url']['url'] : '#';
		?>
		<div class="tclp-legal">
			<?php if ( 'yes' === $s['hero_show'] ) : ?>
				<div class="tclp-hero">
					<div class="tclp-wrap">
						<?php if ( ! empty( $s['hero_badges'] ) ) : ?>
							<div class="tclp-badges">
								<?php foreach ( $s['hero_badges'] as $b ) : ?>
									<?php if ( ! empty( $b['label'] ) ) : ?>
										<span class="tclp-pill<?php echo 'yes' === $b['ok'] ? ' tclp-pill-ok' : ''; ?>"><?php if ( 'yes' === $b['ok'] ) : ?><span class="tclp-dot"></span><?php endif; ?><?php echo esc_html( $b['label'] ); ?></span>
									<?php endif; ?>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						<?php if ( ! empty( $s['hero_title'] ) ) : ?>
							<h1 class="tclp-h1"><?php echo esc_html( $s['hero_title'] ); ?></h1>
						<?php endif; ?>
						<?php if ( ! empty( $s['hero_lede'] ) ) : ?>
							<p class="tclp-lede"><?php echo esc_html( $s['hero_lede'] ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>

			<div class="tclp-wrap tclp-content">
				<main class="tclp-doc" id="tclp-main">
					<?php foreach ( $docs as $d ) :
						$id = ! empty( $d['doc_id'] ) ? sanitize_title( $d['doc_id'] ) : '';
						?>
						<section class="tclp-doc-card" id="<?php echo esc_attr( $id ); ?>">
							<div class="tclp-doc-head">
								<h2><?php echo esc_html( $d['doc_title'] ); ?></h2>
								<span class="tclp-meta"><?php echo esc_html( $d['doc_meta'] ); ?></span>
							</div>
							<div class="tclp-doc-body">
								<?php if ( ! empty( $d['doc_kicker'] ) ) : ?>
									<span class="tclp-kicker"><?php echo esc_html( $d['doc_kicker'] ); ?></span>
								<?php endif; ?>
								<?php if ( ! empty( $d['doc_lede'] ) ) : ?>
									<p class="tclp-doc-lede"><?php echo esc_html( $d['doc_lede'] ); ?></p>
								<?php endif; ?>
								<div class="tclp-prose">
									<?php
									if ( ! empty( $d['doc_blocks'] ) && is_array( $d['doc_blocks'] ) ) {
										echo $this->render_blocks( $d['doc_blocks'] ); // phpcs:ignore WordPress.Security.EscapeOutput
									}
									?>
								</div>
							</div>
						</section>
					<?php endforeach; ?>

					<?php if ( 'yes' === $s['cta_show'] ) : ?>
						<div class="tclp-cta-band">
							<div>
								<h2><?php echo esc_html( $s['cta_title'] ); ?></h2>
								<p><?php echo esc_html( $s['cta_text'] ); ?></p>
							</div>
							<div class="tclp-cta-actions">
								<a class="tclp-btn tclp-btn-light" href="<?php echo esc_url( $cta1_url ); ?>"><?php echo esc_html( $s['cta_btn1_text'] ); ?></a>
								<a class="tclp-btn tclp-btn-outline-light" href="<?php echo esc_url( $cta2_url ); ?>"><?php echo esc_html( $s['cta_btn2_text'] ); ?></a>
							</div>
						</div>
					<?php endif; ?>
				</main>

				<aside class="tclp-aside">
					<div class="tclp-box">
						<h4>On this page</h4>
						<?php foreach ( $anchors as $a ) : ?>
							<a class="tclp-rel" href="#<?php echo esc_attr( $a['id'] ); ?>"><?php echo esc_html( $a['title'] ); ?> <i>↓</i></a>
						<?php endforeach; ?>
					</div>
					<?php if ( 'yes' === $s['glance_show'] && ! empty( $s['glance_items'] ) ) : ?>
						<div class="tclp-box">
							<h4>At a glance</h4>
							<ul class="tclp-mini-list tclp-glance">
								<?php foreach ( $s['glance_items'] as $g ) : ?>
									<li><?php echo esc_html( $g['text'] ); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
					<?php if ( 'yes' === $s['issue_show'] ) : ?>
						<div class="tclp-box tclp-box-cta">
							<h4><?php echo esc_html( $s['issue_title'] ); ?></h4>
							<p><?php echo esc_html( $s['issue_text'] ); ?></p>
							<a class="tclp-btn tclp-btn-light" href="<?php echo esc_url( $issue_url ); ?>"><?php echo esc_html( $s['issue_btn_text'] ); ?></a>
						</div>
					<?php endif; ?>
				</aside>
			</div>

			<?php if ( 'yes' === $s['strip_show'] && ! empty( $s['strip_items'] ) ) : ?>
				<div class="tclp-strip">
					<div class="tclp-wrap tclp-strip-grid">
						<?php foreach ( $s['strip_items'] as $col ) : ?>
							<div>
								<h3><?php echo esc_html( $col['title'] ); ?></h3>
								<p><?php echo esc_html( $col['text'] ); ?></p>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	protected function content_template() {}
}
