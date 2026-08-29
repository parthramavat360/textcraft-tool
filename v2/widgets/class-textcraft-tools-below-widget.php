<?php
/**
 * Tools Below Sections Elementor Widget
 *
 * Six homepage sections shown below the tools listing:
 * How it works, Most used this week, Browse by category,
 * Questions people ask (FAQ), SEO body, and CTA band.
 *
 * White theme design, driven entirely by repeater controls.
 *
 * @package    TextCraftToolsPro
 * @subpackage Widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TextCraft_Tools_Below_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'tctp_tools_below';
	}

	public function get_title() {
		return __( 'TCTP: Home Below Sections', 'textcrafttoolspro' );
	}

	public function get_icon() {
		return 'eicon-section';
	}

	public function get_categories() {
		return [ 'textcrafttools' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_how', [
			'label' => __( '1. How it Works', 'textcrafttoolspro' ),
		] );

		$this->add_control( 'how_show', [
			'label'        => __( 'Show section', 'textcrafttoolspro' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'label_on'     => __( 'Show', 'textcrafttoolspro' ),
			'label_off'    => __( 'Hide', 'textcrafttoolspro' ),
			'return_value' => 'yes',
			'default'      => 'yes',
		] );
		$this->add_control( 'how_kicker', [
			'label'   => __( 'Kicker badge', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'How it works',
		] );
		$this->add_control( 'how_title', [
			'label'   => __( 'Title', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Three steps. No account, no upload queue.',
		] );

		$how = new \Elementor\Repeater();
		$how->add_control( 'num', [
			'label' => __( 'Step number (e.g. 01)', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::TEXT,
			'default' => '01',
		] );
		$how->add_control( 'title', [
			'label' => __( 'Step title', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::TEXT,
		] );
		$how->add_control( 'text', [
			'label' => __( 'Step description', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::TEXTAREA,
			'rows'  => 3,
		] );
		$this->add_control( 'how_steps', [
			'label'       => __( 'Steps', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $how->get_controls(),
			'default'     => [
				[ 'num' => '01', 'title' => 'Pick a tool', 'text' => 'Search or browse the utilities grouped by PDF, image, text, SEO and developer work.' ],
				[ 'num' => '02', 'title' => 'Drop your file or text', 'text' => 'Everything is handled inside the tab — nothing is queued on a server you can’t see.' ],
				[ 'num' => '03', 'title' => 'Download instantly', 'text' => 'Clean output, no watermark, no email gate, no daily limit on how often you run it.' ],
			],
			'title_field' => 'title',
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_most', [
			'label' => __( '2. Most Used This Week', 'textcrafttoolspro' ),
		] );

		$this->add_control( 'most_show', [
			'label'        => __( 'Show section', 'textcrafttoolspro' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'label_on'     => __( 'Show', 'textcrafttoolspro' ),
			'label_off'    => __( 'Hide', 'textcrafttoolspro' ),
			'return_value' => 'yes',
			'default'      => 'yes',
		] );
		$this->add_control( 'most_kicker', [
			'label'   => __( 'Kicker', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Popular tools',
		] );
		$this->add_control( 'most_title', [
			'label'   => __( 'Title', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Most used this week',
		] );
		$this->add_control( 'most_desc', [
			'label'   => __( 'Description', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'rows'    => 2,
			'default' => 'The handful of utilities people keep coming back to. Each one runs on its own lightweight page.',
		] );
		$this->add_control( 'most_link_text', [
			'label'   => __( '"See all" link text', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'See all 207 tools',
		] );
		$this->add_control( 'most_link_url', [
			'label'   => __( '"See all" link URL', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::URL,
			'default' => [ 'url' => '#tools' ],
		] );

		$most = new \Elementor\Repeater();
		$most->add_control( 'tag', [
			'label' => __( 'Category tag (e.g. PDF)', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::TEXT,
		] );
		$most->add_control( 'title', [
			'label' => __( 'Tool name', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::TEXT,
		] );
		$most->add_control( 'text', [
			'label' => __( 'Short description', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::TEXTAREA,
			'rows'  => 2,
		] );
		$most->add_control( 'link', [
			'label' => __( 'Link URL', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::URL,
			'default' => [ 'url' => '#tools' ],
		] );
		$this->add_control( 'most_items', [
			'label'       => __( 'Tool cards', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $most->get_controls(),
			'default'     => [
				[ 'tag' => 'PDF', 'title' => 'Compress PDF', 'text' => 'Shrink big decks and scans without turning text to mush.', 'link' => [ 'url' => '#tools' ] ],
				[ 'tag' => 'IMG', 'title' => 'Image converter', 'text' => 'PNG, JPG, WebP and AVIF in both directions, batch friendly.', 'link' => [ 'url' => '#tools' ] ],
				[ 'tag' => 'TXT', 'title' => 'Word counter', 'text' => 'Live words, characters, sentences and reading time as you type.', 'link' => [ 'url' => '#tools' ] ],
				[ 'tag' => 'DEV', 'title' => 'JSON formatter', 'text' => 'Pretty-print, minify and validate payloads with error line hints.', 'link' => [ 'url' => '#tools' ] ],
			],
			'title_field' => 'title',
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_cat', [
			'label' => __( '3. Browse by Category', 'textcrafttoolspro' ),
		] );

		$this->add_control( 'cat_show', [
			'label'        => __( 'Show section', 'textcrafttoolspro' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'label_on'     => __( 'Show', 'textcrafttoolspro' ),
			'label_off'    => __( 'Hide', 'textcrafttoolspro' ),
			'return_value' => 'yes',
			'default'      => 'yes',
		] );
		$this->add_control( 'cat_kicker', [
			'label'   => __( 'Kicker', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Explore',
		] );
		$this->add_control( 'cat_title', [
			'label'   => __( 'Title', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Browse by category',
		] );
		$this->add_control( 'cat_desc', [
			'label'   => __( 'Description', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'rows'    => 2,
			'default' => 'Every category is self-contained — jump straight to the group you need instead of scrolling the full catalogue.',
		] );

		$cat = new \Elementor\Repeater();
		$cat->add_control( 'icon', [
			'label' => __( 'Short icon (e.g. PDF)', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::TEXT,
		] );
		$cat->add_control( 'name', [
			'label' => __( 'Category name', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::TEXT,
		] );
		$cat->add_control( 'count', [
			'label' => __( 'Tool count', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::TEXT,
		] );
		$cat->add_control( 'link', [
			'label' => __( 'Link URL', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::URL,
			'default' => [ 'url' => '#tools' ],
		] );
		$this->add_control( 'cat_items', [
			'label'       => __( 'Categories', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $cat->get_controls(),
			'default'     => [
				[ 'icon' => 'PDF', 'name' => 'PDF', 'count' => '10 tools', 'link' => [ 'url' => '#tools' ] ],
				[ 'icon' => 'IMG', 'name' => 'Image & Media', 'count' => '22 tools', 'link' => [ 'url' => '#tools' ] ],
				[ 'icon' => 'TXT', 'name' => 'Text', 'count' => '31 tools', 'link' => [ 'url' => '#tools' ] ],
				[ 'icon' => 'DEV', 'name' => 'Developer', 'count' => '17 tools', 'link' => [ 'url' => '#tools' ] ],
				[ 'icon' => 'SEO', 'name' => 'SEO & Web', 'count' => '11 tools', 'link' => [ 'url' => '#tools' ] ],
				[ 'icon' => 'AI', 'name' => 'AI & Prompts', 'count' => '5 tools', 'link' => [ 'url' => '#tools' ] ],
			],
			'title_field' => 'name',
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_faq', [
			'label' => __( '4. Questions People Ask', 'textcrafttoolspro' ),
		] );

		$this->add_control( 'faq_show', [
			'label'        => __( 'Show section', 'textcrafttoolspro' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'label_on'     => __( 'Show', 'textcrafttoolspro' ),
			'label_off'    => __( 'Hide', 'textcrafttoolspro' ),
			'return_value' => 'yes',
			'default'      => 'yes',
		] );
		$this->add_control( 'faq_kicker', [
			'label'   => __( 'Kicker', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Support',
		] );
		$this->add_control( 'faq_title', [
			'label'   => __( 'Title', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Questions people ask',
		] );
		$this->add_control( 'faq_desc', [
			'label'   => __( 'Description', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'rows'    => 2,
			'default' => 'Short answers about privacy, limits and what happens to your files.',
		] );

		$faq = new \Elementor\Repeater();
		$faq->add_control( 'q', [
			'label' => __( 'Question', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::TEXT,
		] );
		$faq->add_control( 'a', [
			'label' => __( 'Answer', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::TEXTAREA,
			'rows'  => 3,
		] );
		$this->add_control( 'faq_items', [
			'label'       => __( 'Questions', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $faq->get_controls(),
			'default'     => [
				[ 'q' => 'Are my files uploaded anywhere?', 'a' => 'Most tools run entirely in your browser, so the file never leaves the tab. The few that need a server delete the file right after processing.' ],
				[ 'q' => 'Do I need an account?', 'a' => 'No. There is no sign-up wall, no daily quota and no watermark on anything you export.' ],
				[ 'q' => 'Is there a file size limit?', 'a' => 'Local tools are limited only by your device memory. Very large PDFs and videos are the practical ceiling.' ],
				[ 'q' => 'Does it work on mobile?', 'a' => 'Yes — search, category tabs and every tool page are built to work on a phone-sized screen.' ],
				[ 'q' => 'How much does it cost?', 'a' => 'Nothing. All tools are free to use as often as you like.' ],
			],
			'title_field' => 'q',
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_seo', [
			'label' => __( '5. SEO Body', 'textcrafttoolspro' ),
		] );

		$this->add_control( 'seo_show', [
			'label'        => __( 'Show section', 'textcrafttoolspro' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'label_on'     => __( 'Show', 'textcrafttoolspro' ),
			'label_off'    => __( 'Hide', 'textcrafttoolspro' ),
			'return_value' => 'yes',
			'default'      => 'yes',
		] );
		$this->add_control( 'seo_kicker', [
			'label'   => __( 'Kicker', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Overview',
		] );
		$this->add_control( 'seo_title', [
			'label'   => __( 'Title', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Free online tools for PDF, image, text and code — all in one place',
		] );
		$this->add_control( 'seo_body', [
			'label'   => __( 'SEO body content', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::WYSIWYG,
			'default' => $this->default_seo_body(),
		] );

		$chip = new \Elementor\Repeater();
		$chip->add_control( 'text', [
			'label' => __( 'Popular search', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::TEXT,
		] );
		$this->add_control( 'seo_chips', [
			'label'       => __( 'Popular searches', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $chip->get_controls(),
			'default'     => [
				[ 'text' => 'Compress PDF' ],
				[ 'text' => 'PNG to WebP' ],
				[ 'text' => 'Word counter' ],
				[ 'text' => 'JSON formatter' ],
				[ 'text' => 'Remove background' ],
			],
			'title_field' => 'text',
		] );

		$fact = new \Elementor\Repeater();
		$fact->add_control( 'dt', [
			'label' => __( 'Label', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::TEXT,
		] );
		$fact->add_control( 'dd', [
			'label' => __( 'Value', 'textcrafttoolspro' ),
			'type'  => \Elementor\Controls_Manager::TEXT,
		] );
		$this->add_control( 'seo_facts', [
			'label'       => __( 'At a glance facts', 'textcrafttoolspro' ),
			'type'        => \Elementor\Controls_Manager::REPEATER,
			'fields'      => $fact->get_controls(),
			'default'     => [
				[ 'dt' => 'Tools available', 'dd' => '207' ],
				[ 'dt' => 'Price', 'dd' => 'Free, forever' ],
				[ 'dt' => 'Sign-up', 'dd' => 'Not required' ],
				[ 'dt' => 'File handling', 'dd' => 'Local-first in your browser' ],
				[ 'dt' => 'Watermarks', 'dd' => 'None on any export' ],
				[ 'dt' => 'Works on', 'dd' => 'Desktop, tablet and mobile' ],
			],
			'title_field' => 'dt',
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_cta', [
			'label' => __( '6. CTA Band', 'textcrafttoolspro' ),
		] );

		$this->add_control( 'cta_show', [
			'label'        => __( 'Show section', 'textcrafttoolspro' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'label_on'     => __( 'Show', 'textcrafttoolspro' ),
			'label_off'    => __( 'Hide', 'textcrafttoolspro' ),
			'return_value' => 'yes',
			'default'      => 'yes',
		] );
		$this->add_control( 'cta_title', [
			'label'   => __( 'Title', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Find the right tool in one search.',
		] );
		$this->add_control( 'cta_desc', [
			'label'   => __( 'Description', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'rows'    => 2,
			'default' => '207 utilities, no installs, no accounts. Press “/” anywhere to start typing.',
		] );
		$this->add_control( 'cta_btn_text', [
			'label'   => __( 'Button text', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => 'Browse all 207 tools',
		] );
		$this->add_control( 'cta_btn_url', [
			'label'   => __( 'Button URL', 'textcrafttoolspro' ),
			'type'    => \Elementor\Controls_Manager::URL,
			'default' => [ 'url' => '#tools' ],
		] );

		$this->end_controls_section();

		/* ============================================================
		   STYLE — General (global) then one panel per section.
		   Each section panel carries style controls for every field it
		   renders. Helpers add_heading_style / add_text_style / add_bg
		   keep the panels compact.
		   ============================================================ */

		$this->start_controls_section( 'style_general', [
			'label' => __( 'General', 'textcrafttoolspro' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'general_wrapper_bg', [
			'label'     => __( 'Wrapper Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-sections' => 'background: {{VALUE}}',
			],
		] );
		$this->add_control( 'general_section_bg', [
			'label'     => __( 'Alternate Section Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#f6f8fb',
			'selectors' => [
				'{{WRAPPER}} .tcb-sec.tcb-alt' => 'background: {{VALUE}}',
			],
		] );
		$this->add_control( 'general_section_border', [
			'label'     => __( 'Section Divider Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-sec' => 'border-color: {{VALUE}}',
			],
		] );
		$this->add_typography( 'general_heading_typo', '{{WRAPPER}} .tcb-h2' );
		$this->add_typography( 'general_desc_typo', '{{WRAPPER}} .tcb-desc' );
		$this->add_control( 'general_kicker_color', [
			'label'     => __( 'Kicker Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-kicker' => 'color: {{VALUE}}; border-color: {{VALUE}}',
			],
		] );
		$this->add_control( 'general_kicker_bg', [
			'label'     => __( 'Kicker Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-kicker' => 'background: {{VALUE}}',
			],
		] );

		$this->end_controls_section();

		/* --- 1. How it Works --- */
		$this->start_controls_section( 'style_how', [
			'label' => __( '1. How it Works', 'textcrafttoolspro' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'how_section_bg', [
			'label'     => __( 'Section Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-how' => 'background: {{VALUE}}',
			],
		] );
		$this->add_heading_styles( 'how', [
			'heading_sel' => '{{WRAPPER}} .tcb-how .tcb-h2',
			'desc_sel'    => '',
		], __( 'Section Title', 'textcrafttoolspro' ) );
		$this->add_control( 'how_kicker_color', [
			'label'     => __( 'Kicker Text / Border', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-how .tcb-kicker' => 'color: {{VALUE}}; border-color: {{VALUE}}',
			],
		] );
		$this->add_control( 'how_kicker_bg', [
			'label'     => __( 'Kicker Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-how .tcb-kicker' => 'background: {{VALUE}}',
			],
		] );
		$this->add_control( 'how_card_bg', [
			'label'     => __( 'Step Card Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-how .tcb-card.step' => 'background: {{VALUE}}',
			],
		] );
		$this->add_control( 'how_card_border', [
			'label'     => __( 'Step Card Border', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-how .tcb-card.step' => 'border-color: {{VALUE}}',
			],
		] );
		$this->add_control( 'how_num_color', [
			'label'     => __( 'Step Number Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-how .tcb-num' => 'color: {{VALUE}}',
			],
		] );
		$this->add_control( 'how_num_bg', [
			'label'     => __( 'Step Number Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-how .tcb-num' => 'background: {{VALUE}}',
			],
		] );
		$this->add_field_text_styles( 'how', '{{WRAPPER}} .tcb-how .tcb-card.step h3', __( 'Step Title' ) );
		$this->add_field_text_styles( 'howbody', '{{WRAPPER}} .tcb-how .tcb-card.step p', __( 'Step Text' ) );

		$this->end_controls_section();

		/* --- 2. Most Used This Week --- */
		$this->start_controls_section( 'style_most', [
			'label' => __( '2. Most Used This Week', 'textcrafttoolspro' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'most_section_bg', [
			'label'     => __( 'Section Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-most' => 'background: {{VALUE}}',
			],
		] );
		$this->add_heading_styles( 'most', [
			'heading_sel' => '{{WRAPPER}} .tcb-most .tcb-h2',
			'desc_sel'    => '{{WRAPPER}} .tcb-most .tcb-desc',
		], __( 'Section Title / Description', 'textcrafttoolspro' ) );
		$this->add_text_control( 'most_gost_color', __( '"See all" Link Color', 'textcrafttoolspro' ), '{{WRAPPER}} .tcb-most .tcb-gost', 'color:{{VALUE}}' );
		$this->add_control( 'most_gost_border', [
			'label'     => __( '"See all" Link Border', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-most .tcb-gost' => 'border-color: {{VALUE}}',
			],
		] );
		$this->add_control( 'most_card_bg', [
			'label'     => __( 'Tool Card Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-most .tcb-card.tool' => 'background: {{VALUE}}',
			],
		] );
		$this->add_control( 'most_card_border', [
			'label'     => __( 'Tool Card Border', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-most .tcb-card.tool' => 'border-color: {{VALUE}}',
			],
		] );
		$this->add_control( 'most_tag_bg', [
			'label'     => __( 'Category Tag Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-most .tcb-tag' => 'background: {{VALUE}}',
			],
		] );
		$this->add_text_control( 'most_tag_color', __( 'Category Tag Text Color', 'textcrafttoolspro' ), '{{WRAPPER}} .tcb-most .tcb-tag', 'color:{{VALUE}}' );
		$this->add_field_text_styles( 'most', '{{WRAPPER}} .tcb-most .tcb-card.tool h3', __( 'Tool Title' ) );
		$this->add_field_text_styles( 'mostbody', '{{WRAPPER}} .tcb-most .tcb-card.tool p', __( 'Tool Description' ) );

		$this->end_controls_section();

		/* --- 3. Browse by Category --- */
		$this->start_controls_section( 'style_cat', [
			'label' => __( '3. Browse by Category', 'textcrafttoolspro' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'cat_section_bg', [
			'label'     => __( 'Section Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-cats' => 'background: {{VALUE}}',
			],
		] );
		$this->add_heading_styles( 'cat', [
			'heading_sel' => '{{WRAPPER}} .tcb-cats .tcb-h2',
			'desc_sel'    => '{{WRAPPER}} .tcb-cats .tcb-desc',
		], __( 'Section Title / Description', 'textcrafttoolspro' ) );
		$this->add_control( 'cat_card_bg', [
			'label'     => __( 'Category Card Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-cats .tcb-card.cat' => 'background: {{VALUE}}',
			],
		] );
		$this->add_control( 'cat_card_border', [
			'label'     => __( 'Category Card Border', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-cats .tcb-card.cat' => 'border-color: {{VALUE}}',
			],
		] );
		$this->add_control( 'cat_icon_color', [
			'label'     => __( 'Icon Color', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-cats .tcb-caticon' => 'color: {{VALUE}}',
			],
		] );
		$this->add_control( 'cat_icon_bg', [
			'label'     => __( 'Icon Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-cats .tcb-caticon' => 'background: {{VALUE}}',
			],
		] );
		$this->add_text_control( 'cat_name_color', __( 'Category Name Color', 'textcrafttoolspro' ), '{{WRAPPER}} .tcb-cats .tcb-catname', 'color:{{VALUE}}' );
		$this->add_text_control( 'cat_count_color', __( 'Tool Count Color', 'textcrafttoolspro' ), '{{WRAPPER}} .tcb-cats .tcb-catcount', 'color:{{VALUE}}' );

		$this->end_controls_section();

		/* --- 4. FAQ --- */
		$this->start_controls_section( 'style_faq', [
			'label' => __( '4. Questions People Ask', 'textcrafttoolspro' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'faq_section_bg', [
			'label'     => __( 'Section Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-faq' => 'background: {{VALUE}}',
			],
		] );
		$this->add_heading_styles( 'faq', [
			'heading_sel' => '{{WRAPPER}} .tcb-faq .tcb-h2',
			'desc_sel'    => '{{WRAPPER}} .tcb-faq .tcb-desc',
		], __( 'Section Title / Description', 'textcrafttoolspro' ) );
		$this->add_control( 'faq_card_bg', [
			'label'     => __( 'FAQ Card Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-faq .tcb-card.faq' => 'background: {{VALUE}}',
			],
		] );
		$this->add_control( 'faq_card_border', [
			'label'     => __( 'FAQ Card Border', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-faq .tcb-card.faq' => 'border-color: {{VALUE}}',
			],
		] );
		$this->add_field_text_styles( 'faqq', '{{WRAPPER}} .tcb-faq .tcb-card.faq summary', __( 'Question Text' ) );
		$this->add_field_text_styles( 'faqa', '{{WRAPPER}} .tcb-faq .tcb-card.faq > p', __( 'Answer Text' ) );
		$this->add_control( 'faq_plus_bg', [
			'label'     => __( 'Question Icon (+) Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-faq .tcb-plus' => 'background: {{VALUE}}',
			],
		] );
		$this->add_text_control( 'faq_plus_color', __( 'Question Icon (+) Color', 'textcrafttoolspro' ), '{{WRAPPER}} .tcb-faq .tcb-plus', 'color:{{VALUE}}' );

		$this->end_controls_section();

		/* --- 5. SEO Body --- */
		$this->start_controls_section( 'style_seo', [
			'label' => __( '5. SEO Body', 'textcrafttoolspro' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'seo_section_bg', [
			'label'     => __( 'Section Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-seo' => 'background: {{VALUE}}',
			],
		] );
		$this->add_heading_styles( 'seo', [
			'heading_sel' => '{{WRAPPER}} .tcb-seo .tcb-h2',
			'desc_sel'    => '',
		], __( 'Section Title', 'textcrafttoolspro' ) );
		$this->add_field_text_styles( 'seobody', '{{WRAPPER}} .tcb-seobody p', __( 'Body Paragraph' ) );
		$this->add_field_text_styles( 'seoh3', '{{WRAPPER}} .tcb-seobody h3', __( 'Body Heading' ) );
		$this->add_control( 'seo_chip_bg', [
			'label'     => __( 'Chip Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-seo .tcb-chip' => 'background: {{VALUE}}',
			],
		] );
		$this->add_control( 'seo_chip_border', [
			'label'     => __( 'Chip Border', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-seo .tcb-chip' => 'border-color: {{VALUE}}',
			],
		] );
		$this->add_text_control( 'seo_chip_color', __( 'Chip Text Color', 'textcrafttoolspro' ), '{{WRAPPER}} .tcb-seo .tcb-chip', 'color:{{VALUE}}' );
		$this->add_text_control( 'seo_fact_dt_color', __( 'At-a-glance Label Color', 'textcrafttoolspro' ), '{{WRAPPER}} .tcb-seo .tcb-facts dt', 'color:{{VALUE}}' );
		$this->add_text_control( 'seo_fact_dd_color', __( 'At-a-glance Value Color', 'textcrafttoolspro' ), '{{WRAPPER}} .tcb-seo .tcb-facts dd', 'color:{{VALUE}}' );

		$this->end_controls_section();

		/* --- 6. CTA Band --- */
		$this->start_controls_section( 'style_cta', [
			'label' => __( '6. CTA Band', 'textcrafttoolspro' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'cta_section_bg', [
			'label'     => __( 'Section Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-cta' => 'background: {{VALUE}}',
			],
		] );
		$this->add_control( 'cta_gradient_from', [
			'label'     => __( 'CTA Card Gradient (start)', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-sections' => '--tcb-cta-from: {{VALUE}}',
			],
		] );
		$this->add_control( 'cta_gradient_to', [
			'label'     => __( 'CTA Card Gradient (end)', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-sections' => '--tcb-cta-to: {{VALUE}}',
			],
		] );
		$this->add_field_text_styles( 'ctatitle', '{{WRAPPER}} .tcb-cta .tcb-ctacard h3', __( 'CTA Title' ) );
		$this->add_field_text_styles( 'ctadesc', '{{WRAPPER}} .tcb-cta .tcb-ctacard p', __( 'CTA Description' ) );
		$this->add_control( 'cta_btn_bg', [
			'label'     => __( 'Button Background', 'textcrafttoolspro' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .tcb-cta .tcb-btn' => 'background: {{VALUE}}',
			],
		] );
		$this->add_text_control( 'cta_btn_color', __( 'Button Text Color', 'textcrafttoolspro' ), '{{WRAPPER}} .tcb-cta .tcb-btn', 'color:{{VALUE}}' );

		$this->end_controls_section();
	}

	/**
	 * A filled color control that resolves a plain color to a CSS
	 * "color: <value>" rule for a selector.
	 */
	private function add_text_control( $key, $label, $selector, $rule ) {
		$this->add_control( $key, [
			'label'     => $label,
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				$selector => $rule,
			],
		] );
	}

	/**
	 * Add color + typography set for a section heading and (optionally) its
	 * description, using per-section scoped selectors.
	 */
	private function add_heading_styles( $key, $selectors, $label ) {
		$this->add_control( $key . '_heading_color', [
			'label'     => sprintf( /* translators: %s: heading label */ __( '%s — color', 'textcrafttoolspro' ), $label ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				$selectors['heading_sel'] => 'color: {{VALUE}}',
			],
		] );
		$this->add_typography( $key . '_heading_typo', $selectors['heading_sel'] );
		if ( ! empty( $selectors['desc_sel'] ) ) {
			$this->add_control( $key . '_desc_color', [
				'label'     => sprintf( /* translators: %s: label */ __( '%s — description color', 'textcrafttoolspro' ), $label ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					$selectors['desc_sel'] => 'color: {{VALUE}}',
				],
			] );
			$this->add_typography( $key . '_desc_typo', $selectors['desc_sel'] );
		}
	}

	/**
	 * Add color + typography for an arbitrary field (e.g. a card title / text).
	 */
	private function add_field_text_styles( $key, $selector, $label ) {
		$this->add_control( $key . '_color', [
			'label'     => $label,
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				$selector => 'color: {{VALUE}}',
			],
		] );
		$this->add_typography( $key . '_typo', $selector );
	}

	/**
	 * Add a full Elementor typography group control (family, size, weight,
	 * line height, transform, etc.) applied to a selector.
	 */
	private function add_typography( $key, $selector ) {
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => $key,
				'label'    => __( 'Typography', 'textcrafttoolspro' ),
				'selector' => $selector,
			]
		);
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		$this->add_render_attribute( 'wrap', 'class', 'tcb-sections' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrap' ); ?>>
			<?php $this->render_how( $s ); ?>
			<?php $this->render_most( $s ); ?>
			<?php $this->render_cat( $s ); ?>
			<?php $this->render_faq( $s ); ?>
			<?php $this->render_seo( $s ); ?>
			<?php $this->render_cta( $s ); ?>
		</div>
		<?php
	}

	private function render_how( $s ) {
		if ( ! $this->show( $s, 'how_show' ) ) {
			return;
		}
		?>
		<section class="tcb-sec tcb-how">
			<div class="tcb-wrap">
				<?php if ( ! empty( $s['how_kicker'] ) ) : ?>
					<span class="tcb-kicker"><?php echo esc_html( $s['how_kicker'] ); ?></span>
				<?php endif; ?>
				<h2 class="tcb-h2"><?php echo esc_html( $s['how_title'] ); ?></h2>
				<?php if ( ! empty( $s['how_steps'] ) ) : ?>
					<div class="tcb-grid3">
						<?php foreach ( $s['how_steps'] as $i ) : ?>
							<div class="tcb-card step">
								<span class="tcb-num"><?php echo esc_html( $i['num'] ); ?></span>
								<h3><?php echo esc_html( $i['title'] ); ?></h3>
								<p><?php echo esc_html( $i['text'] ); ?></p>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}

	private function render_most( $s ) {
		if ( ! $this->show( $s, 'most_show' ) ) {
			return;
		}
		$link  = ! empty( $s['most_link_url']['url'] ) ? $s['most_link_url']['url'] : '#tools';
		$cards = $this->resolve_tool_cards( 4 );
		?>
		<section class="tcb-sec tcb-alt tcb-most">
			<div class="tcb-wrap">
				<div class="tcb-head">
					<div>
						<?php if ( ! empty( $s['most_kicker'] ) ) : ?>
							<span class="tcb-kicker"><?php echo esc_html( $s['most_kicker'] ); ?></span>
						<?php endif; ?>
						<h2 class="tcb-h2"><?php echo esc_html( $s['most_title'] ); ?></h2>
						<p class="tcb-desc"><?php echo esc_html( $s['most_desc'] ); ?></p>
					</div>
					<?php if ( ! empty( $s['most_link_text'] ) ) : ?>
						<a class="tcb-gost" href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $s['most_link_text'] ); ?></a>
					<?php endif; ?>
				</div>
				<?php if ( $cards ) : ?>
					<div class="tcb-grid4">
						<?php foreach ( $cards as $c ) : ?>
							<a class="tcb-card tool" href="<?php echo esc_url( $c['url'] ); ?>">
								<span class="tcb-tag"><?php echo esc_html( $c['tag'] ); ?></span>
								<h3><?php echo esc_html( $c['name'] ); ?></h3>
								<p><?php echo esc_html( $c['desc'] ); ?></p>
							</a>
						<?php endforeach; ?>
					</div>
				<?php elseif ( ! empty( $s['most_items'] ) ) : ?>
					<div class="tcb-grid4">
						<?php foreach ( $s['most_items'] as $i ) : ?>
							<?php $u = ! empty( $i['link']['url'] ) ? $i['link']['url'] : '#tools'; ?>
							<a class="tcb-card tool" href="<?php echo esc_url( $u ); ?>">
								<span class="tcb-tag"><?php echo esc_html( $i['tag'] ); ?></span>
								<h3><?php echo esc_html( $i['title'] ); ?></h3>
								<p><?php echo esc_html( $i['text'] ); ?></p>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}

	private function get_tool_map() {
		static $map = null;
		if ( $map !== null ) {
			return $map;
		}
		$map  = [];
		$data = ( new \TextCraft_Tools_Section_Widget() )->get_tools_data();
		foreach ( $data as $cat_key => $cat ) {
			foreach ( $cat['tools'] as $tool ) {
				$slug = '';
				if ( preg_match( '#/tools/([^/]+)/#', $tool['url'], $m ) ) {
					$slug = $m[1];
				}
				$map[ $slug ] = [
					'name' => $tool['name'],
					'desc' => $tool['desc'],
					'icon' => $tool['icon'],
					'cat'  => $cat_key,
				];
			}
		}
		return $map;
	}

	private function cat_tag( $cat ) {
		$tags = [
			'pdf'         => 'PDF',
			'compress'    => 'IMG',
			'image'       => 'IMG',
			'image_edit'  => 'IMG',
			'text'        => 'TXT',
			'case'        => 'TXT',
			'dev'         => 'DEV',
			'dev_convert' => 'DEV',
			'cipher'      => 'DEV',
			'calc'        => 'CAL',
			'gen'         => 'GEN',
			'fonts'       => 'FNT',
			'ai'          => 'AI',
			'seo'         => 'SEO',
			'cheat'       => 'DEV',
			'webdev'      => 'WEB',
		];
		return $tags[ $cat ] ?? 'TOOL';
	}

	private function resolve_tool_cards( $limit = 4 ) {
		$map    = $this->get_tool_map();
		$byName = [];
		foreach ( $map as $slug => $info ) {
			$byName[ strtolower( $info['name'] ) ] = $info;
		}

		$pages = get_posts( [
			'post_type'   => 'page',
			'post_parent' => 168,
			'post_status' => 'publish',
			'numberposts' => -1,
		] );

		$real      = [];
		$realSlugs = [];
		foreach ( $pages as $p ) {
			$realSlugs[]                = $p->post_name;
			$real[ $p->post_name ]      = [
				'name' => get_the_title( $p ),
				'url'  => get_permalink( $p ),
			];
		}

		$used = [];
		if ( function_exists( 'tctp_get_most_used_tools' ) ) {
			foreach ( tctp_get_most_used_tools( $limit ) as $slug ) {
				if ( isset( $real[ $slug ] ) ) {
					$used[] = $slug;
				}
			}
		}

		if ( count( $used ) < $limit ) {
			$pool = array_diff( $realSlugs, $used );
			shuffle( $pool );
			foreach ( array_slice( $pool, 0, $limit - count( $used ) ) as $slug ) {
				$used[] = $slug;
			}
		}

		$cards = [];
		foreach ( $used as $slug ) {
			$rp   = $real[ $slug ];
			$info = $map[ $slug ] ?? ( $byName[ strtolower( $rp['name'] ) ] ?? null );
			$cards[] = [
				'tag'  => $info ? $this->cat_tag( $info['cat'] ) : 'TOOL',
				'name' => $rp['name'],
				'desc' => $info ? $info['desc'] : 'A free, browser-based TextCraft utility with a single, focused purpose.',
				'url'  => $rp['url'],
			];
		}
		return $cards;
	}

	private function render_cat( $s ) {
		if ( ! $this->show( $s, 'cat_show' ) ) {
			return;
		}
		?>
		<section class="tcb-sec tcb-cats">
			<div class="tcb-wrap">
				<?php if ( ! empty( $s['cat_kicker'] ) ) : ?>
					<span class="tcb-kicker"><?php echo esc_html( $s['cat_kicker'] ); ?></span>
				<?php endif; ?>
				<h2 class="tcb-h2"><?php echo esc_html( $s['cat_title'] ); ?></h2>
				<p class="tcb-desc"><?php echo esc_html( $s['cat_desc'] ); ?></p>
				<?php if ( ! empty( $s['cat_items'] ) ) : ?>
					<div class="tcb-gridcat">
						<?php foreach ( $s['cat_items'] as $i ) : ?>
							<?php $u = ! empty( $i['link']['url'] ) ? $i['link']['url'] : '#tools'; ?>
							<a class="tcb-card cat" href="<?php echo esc_url( $u ); ?>">
								<span class="tcb-caticon"><?php echo esc_html( $i['icon'] ); ?></span>
								<span class="tcb-catname"><?php echo esc_html( $i['name'] ); ?></span>
								<?php if ( ! empty( $i['count'] ) ) : ?>
									<span class="tcb-catcount"><?php echo esc_html( $i['count'] ); ?></span>
								<?php endif; ?>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}

	private function render_faq( $s ) {
		if ( ! $this->show( $s, 'faq_show' ) ) {
			return;
		}
		?>
		<section class="tcb-sec tcb-alt tcb-faq">
			<div class="tcb-wrap tcb-faqwrap">
				<div>
					<?php if ( ! empty( $s['faq_kicker'] ) ) : ?>
						<span class="tcb-kicker"><?php echo esc_html( $s['faq_kicker'] ); ?></span>
					<?php endif; ?>
					<h2 class="tcb-h2"><?php echo esc_html( $s['faq_title'] ); ?></h2>
					<p class="tcb-desc"><?php echo esc_html( $s['faq_desc'] ); ?></p>
				</div>
				<?php if ( ! empty( $s['faq_items'] ) ) : ?>
					<div>
						<?php foreach ( $s['faq_items'] as $i ) : ?>
							<details class="tcb-card faq">
								<summary><?php echo esc_html( $i['q'] ); ?><span class="tcb-plus">+</span></summary>
								<p><?php echo esc_html( $i['a'] ); ?></p>
							</details>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}

	private function render_seo( $s ) {
		if ( ! $this->show( $s, 'seo_show' ) ) {
			return;
		}
		?>
		<section class="tcb-sec tcb-seo">
			<div class="tcb-wrap tcb-seowrap">
				<div>
					<?php if ( ! empty( $s['seo_kicker'] ) ) : ?>
						<span class="tcb-kicker"><?php echo esc_html( $s['seo_kicker'] ); ?></span>
					<?php endif; ?>
					<h2 class="tcb-h2"><?php echo esc_html( $s['seo_title'] ); ?></h2>
					<div class="tcb-seobody"><?php echo wp_kses_post( $s['seo_body'] ); ?></div>
				</div>
				<aside class="tcb-seo-aside">
					<?php if ( ! empty( $s['seo_chips'] ) ) : ?>
						<div class="tcb-card side">
							<h3>Popular searches</h3>
							<div class="tcb-chips">
								<?php foreach ( $s['seo_chips'] as $i ) : ?>
									<span class="tcb-chip"><?php echo esc_html( $i['text'] ); ?></span>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $s['seo_facts'] ) ) : ?>
						<div class="tcb-card side">
							<h3>At a glance</h3>
							<dl class="tcb-facts">
								<?php foreach ( $s['seo_facts'] as $i ) : ?>
									<div><dt><?php echo esc_html( $i['dt'] ); ?></dt><dd><?php echo esc_html( $i['dd'] ); ?></dd></div>
								<?php endforeach; ?>
							</dl>
						</div>
					<?php endif; ?>
				</aside>
			</div>
		</section>
		<?php
	}

	private function render_cta( $s ) {
		if ( ! $this->show( $s, 'cta_show' ) ) {
			return;
		}
		$u = ! empty( $s['cta_btn_url']['url'] ) ? $s['cta_btn_url']['url'] : '#tools';
		?>
		<section class="tcb-sec tcb-cta">
			<div class="tcb-wrap">
				<div class="tcb-ctacard">
					<h3><?php echo esc_html( $s['cta_title'] ); ?></h3>
					<p><?php echo esc_html( $s['cta_desc'] ); ?></p>
					<a class="tcb-btn" href="<?php echo esc_url( $u ); ?>"><?php echo esc_html( $s['cta_btn_text'] ); ?></a>
				</div>
			</div>
		</section>
		<?php
	}

	private function show( $settings, $key ) {
		return ! empty( $settings[ $key ] ) && 'yes' === $settings[ $key ];
	}

	private function default_seo_body() {
		return '<p><strong>TextCraft Tools</strong> is a collection of 207 free online utilities built for everyday file and text work: compressing a PDF before you email it, converting a PNG to WebP for a faster page, counting words in an article, cleaning messy text, formatting JSON, generating a strong password or a UUID. Every tool has a single purpose and a single screen, so you are never more than one click from the result you came for.</p>
<p>Most utilities are <strong>local-first</strong>: the processing runs with JavaScript and WebAssembly inside your own browser tab, so the document, photo or snippet you drop in is never uploaded to a server. That matters for invoices, contracts, ID scans, client work and anything else you would rather not hand to a third party. It is also why the tools feel instant — there is no upload, no queue and no download step waiting on someone else’s bandwidth.</p>
<h3>What you can do with the PDF and image tools</h3>
<p>The PDF group covers the tasks that usually push people toward paid desktop software: compress PDF to reduce file size for email limits, merge several PDFs into one document, split or extract specific pages, rotate a badly scanned file, and convert PDF to and from Word, JPG and PNG. The image group handles resizing, cropping, batch conversion between JPG, PNG, WebP and AVIF, and lossy or lossless compression that keeps photos sharp while cutting page weight — a direct win for Core Web Vitals and mobile load times.</p>
<h3>Text, SEO and developer utilities</h3>
<p>Writers and marketers use the word counter, character counter, case converter, duplicate line remover, text diff checker and slug generator to tidy copy before it ships. SEO utilities help you check meta title and description lengths, preview how a page appears in search results, and build clean, readable URLs. Developers reach for the JSON formatter and validator, Base64 encoder and decoder, URL encoder, hash generator, regex tester, timestamp converter and colour picker — small tools that remove friction from a normal working day.</p>
<h3>No account, no limits, no watermarks</h3>
<p>There is nothing to install and nothing to sign up for. No daily quota caps how many files you convert, no watermark is stamped on your exports, and no email address is required before you can download a result. Every page is lightweight and responsive, so the same search, category tabs and tools work exactly the same on a phone, a tablet or a desktop browser.</p>';
	}

	protected function content_template() {}
}
