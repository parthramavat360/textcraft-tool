<?php
/**
 * Plugin Name: TextCraft Tools Pro
 * Plugin URI: https://textcraft.tools
 * Description: Custom Elementor widgets for TextCraft Tools — header, megamenu, individual tool widgets, and more.
 * Version: 2.0.2
 * Author: TextCraft
 * Author URI: https://textcraft.tools
 * Text Domain: textcrafttoolspro
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Elementor tested up to: 3.25
 * License: GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Define plugin constants.
 */
define( 'TCTP_VERSION', '1.0.51' );
define( 'TCTP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'TCTP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'TCTP_DATA_VERSION', 4 );

/**
 * Ensure the tool-views tracking table exists.
 */
function tctp_ensure_tool_views_table() {
	global $wpdb;
	$table = $wpdb->prefix . 'tctp_tool_views';
	if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) !== $table ) {
		$charset = $wpdb->get_charset_collate();
		$wpdb->query(
			"CREATE TABLE $table (
				id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				slug VARCHAR(200) NOT NULL,
				day DATE NOT NULL,
				views BIGINT UNSIGNED NOT NULL DEFAULT 0,
				PRIMARY KEY  (id),
				UNIQUE KEY uq_slug_day (slug, day)
			) $charset"
		);
	}
}

/**
 * Record a view for the current tool page (front-end singular pages under the
 * tools parent), one row per slug per day. Counts real page loads only.
 */
function tctp_record_tool_view() {
	if ( is_admin() || ! is_singular( 'page' ) || is_user_logged_in() ) {
		return;
	}
	$post = get_queried_object();
	if ( ! $post || (int) $post->post_parent !== 168 ) {
		return;
	}
	if ( ! empty( $_GET['elementor-preview'] ) || ! empty( $_GET['elementor_library'] ) ) {
		return;
	}
	tctp_ensure_tool_views_table();
	global $wpdb;
	$table = $wpdb->prefix . 'tctp_tool_views';
	$wpdb->query(
		$wpdb->prepare(
			"INSERT INTO $table (slug, day, views) VALUES (%s, %s, 1)
			 ON DUPLICATE KEY UPDATE views = views + 1",
			$post->post_name,
			current_time( 'Y-m-d' )
		)
	);
}
add_action( 'template_redirect', 'tctp_record_tool_view', 20 );

/**
 * Return the slugs of the most-viewed tools within the last 7 days.
 */
function tctp_get_most_used_tools( $limit = 4 ) {
	tctp_ensure_tool_views_table();
	global $wpdb;
	$table = $wpdb->prefix . 'tctp_tool_views';
	$since = gmdate( 'Y-m-d', strtotime( '-7 days' ) );
	$rows  = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT slug, SUM(views) AS total FROM $table WHERE day >= %s GROUP BY slug ORDER BY total DESC, slug ASC LIMIT %d",
			$since,
			$limit
		)
	);
	$slugs = [];
	foreach ( $rows as $r ) {
		$slugs[] = $r->slug;
	}
	return $slugs;
}

/**
 * Check if Elementor is active before initializing.
 */
function tctp_check_elementor() {
    if ( ! did_action( 'elementor/loaded' ) ) {
        add_action( 'admin_notices', function () {
            echo '<div class="notice notice-error"><p>';
            echo esc_html__( 'TextCraft Tools Pro requires Elementor to be installed and active.', 'textcrafttoolspro' );
            echo '</p></div>';
        } );
        return false;
    }
    return true;
}

/**
 * Register Elementor widgets.
 */
function tctp_register_widgets( $widgets_manager ) {
    if ( ! tctp_check_elementor() ) {
        return;
    }

    require_once TCTP_PLUGIN_DIR . 'widgets/class-textcraft-header-widget.php';
    require_once TCTP_PLUGIN_DIR . 'widgets/class-textcraft-footer-widget.php';
    require_once TCTP_PLUGIN_DIR . 'widgets/class-textcraft-hero-widget.php';
    require_once TCTP_PLUGIN_DIR . 'widgets/class-textcraft-tools-section-widget.php';
    require_once TCTP_PLUGIN_DIR . 'widgets/class-textcraft-strip-widget.php';
    require_once TCTP_PLUGIN_DIR . 'widgets/class-textcraft-tool-hero-widget.php';
    require_once TCTP_PLUGIN_DIR . 'widgets/class-textcraft-tool-content-widget.php';
    require_once TCTP_PLUGIN_DIR . 'widgets/class-textcraft-tool-workspace-widget.php';
    require_once TCTP_PLUGIN_DIR . 'widgets/class-textcraft-benefits-section-widget.php';
    require_once TCTP_PLUGIN_DIR . 'widgets/class-textcraft-who-section-widget.php';
    require_once TCTP_PLUGIN_DIR . 'widgets/class-textcraft-why-section-widget.php';
    require_once TCTP_PLUGIN_DIR . 'widgets/class-textcraft-workflow-section-widget.php';
    require_once TCTP_PLUGIN_DIR . 'widgets/class-textcraft-cta-section-widget.php';
    require_once TCTP_PLUGIN_DIR . 'widgets/class-textcraft-tools-below-widget.php';
    require_once TCTP_PLUGIN_DIR . 'widgets/class-textcraft-legal-widget.php';

    $widgets_manager->register( new \TextCraft_Header_Widget() );
    $widgets_manager->register( new \TextCraft_Footer_Widget() );
    $widgets_manager->register( new \TextCraft_Hero_Widget() );
    $widgets_manager->register( new \TextCraft_Tools_Section_Widget() );
    $widgets_manager->register( new \TextCraft_Strip_Widget() );
    $widgets_manager->register( new \TextCraft_Tool_Hero_Widget() );
    $widgets_manager->register( new \TextCraft_Tool_Content_Widget() );
    $widgets_manager->register( new \TextCraft_Tool_Workspace_Widget() );
    $widgets_manager->register( new \TextCraft_Benefits_Section_Widget() );
    $widgets_manager->register( new \TextCraft_Who_Section_Widget() );
    $widgets_manager->register( new \TextCraft_Why_Section_Widget() );
    $widgets_manager->register( new \TextCraft_Workflow_Section_Widget() );
    $widgets_manager->register( new \TextCraft_CTA_Section_Widget() );
    $widgets_manager->register( new \TextCraft_Tools_Below_Widget() );
    $widgets_manager->register( new \TextCraft_Legal_Widget() );

    // New individual tool widgets
    require_once TCTP_PLUGIN_DIR . 'includes/class-textcraft-tool-base.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-apa-format.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-ascii-art.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-case-converter.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-character-remover.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-delete-pdf-pages.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-duplicate-line.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-duplicate-word.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-em-dash-remover.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-find-replace.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-gif-compressor.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-heic-to-jpg.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-heic-to-png.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-heic-to-svg.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-image-to-text.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-invisible-text.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-jpg-compressor.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-jpg-to-avif.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-jpg-to-gif.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-jpg-to-heic.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-jpg-to-pdf.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-jpg-to-png.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-jpg-to-svg.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-jpg-to-webp.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-json-formatter.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-nato-phonetic.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-online-notepad.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-password-generator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-password-strength-checker.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-pdf-compressor.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-pdf-merger.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-pdf-splitter.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-pdf-to-jpg.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-pdf-to-png.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-phonetic-spelling.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-pig-latin.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-plain-text.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-png-compressor.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-png-to-heic.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-png-to-jpg.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-png-to-pdf.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-png-to-svg.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-png-to-webp.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-random-choice.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-random-date.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-random-ip.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-random-letter.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-random-month.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-random-number.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-remove-background.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-remove-formatting.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-remove-line-breaks.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-remove-underscores.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-repeat-text.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-reverse-text.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-roman-numeral.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-rotate-pdf.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-sentence-case.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-sentence-counter.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-sort-words.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-svg-compressor.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-title-case.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-uuid-generator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-webp-compressor.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-webp-to-jpg.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-webp-to-png.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-whitespace-remover.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-wingdings.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-word-cloud.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-word-frequency.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-regex-tester.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-morse-code-translator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-character-frequency-counter.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-unicode-translator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-binary-translator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-html-encode-decode.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-qr-generator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-base64-encode-decode.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-hash-generator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-url-encode-decode.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-resize-image.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-fancy-text.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-video-converter.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-crop-image.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-rotate-image.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-watermark-image.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-flip-image.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-png-to-ico.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-svg-to-png.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-blur-face.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-meme-generator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-html-to-image.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-image-compressor.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-color-picker.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-lorem-ipsum.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-age-calculator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-percentage-calculator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-bmi-calculator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-tip-calculator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-date-calculator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-font-generator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-upscale-image.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-photo-editor.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-small-text.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-text-to-speech.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-readability-checker.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-speech-to-text.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-text-diff.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-number-to-words.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-words-to-number.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-text-summarizer.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-color-converter.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-gradient-generator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-dev-utilities.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-code-formatter.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-json-tools.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-markdown-preview.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-markdown-table-generator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-cipher-tools.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-financial-calculator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-unit-converter.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-timezone-converter.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-instagram-caption.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-hashtag-generator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-social-char-counter.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-youtube-title.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-meta-description.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-random-name.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-team-name.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-dice-roller.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-coin-flipper.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-random-emoji.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-cheat-sheet.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-qr-reader.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-barcode-generator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-keyword-density-checker.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-fake-name-generator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-credit-card-validator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-what-is-my-user-agent.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-text-to-handwriting.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-currency-converter.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-box-shadow-generator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-css-gradient-previewer.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-border-radius-generator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-flexbox-playground.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-css-grid-playground.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-meta-tag-generator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-robots-txt-generator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-page-speed-checker.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-schema-markup-generator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-perplexity-prompt-generator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-claude-prompt-generator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-gemini-prompt-generator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-chatgpt-prompt-generator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-prompt-optimizer.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-color-palette-generator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-text-to-speech.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-speech-to-text.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-text-diff.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-number-to-words.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-readability-checker.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-sql-cheat-sheet.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-git-cheat-sheet.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-linux-cheat-sheet.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-tailwind-css-cheat-sheet.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-words-to-number.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-font-pair-generator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-wifi-qr-generator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-ip-address-lookup.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-xml-sitemap-generator.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-html-preview.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-passport-photo.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-reduce-image-kb.php';
    require_once TCTP_PLUGIN_DIR . 'includes/widgets/widget-office-to-pdf.php';

    // Register new individual tool widgets
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Apa_Format() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Ascii_Art() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Case_Converter() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Character_Remover() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Delete_Pdf_Pages() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Duplicate_Line() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Duplicate_Word() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Em_Dash_Remover() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Find_Replace() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Gif_Compressor() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Heic_To_Jpg() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Heic_To_Png() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Heic_To_Svg() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Image_To_Text() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Invisible_Text() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Jpg_Compressor() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Jpg_To_Avif() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Jpg_To_Gif() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Jpg_To_Heic() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Jpg_To_Pdf() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Jpg_To_Png() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Jpg_To_Svg() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Jpg_To_Webp() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Json_Formatter() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Nato_Phonetic() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Online_Notepad() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Password_Generator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Password_Strength_Checker() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Pdf_Compressor() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Pdf_Merger() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Pdf_Splitter() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Pdf_To_Jpg() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Pdf_To_Png() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Phonetic_Spelling() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Pig_Latin() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Plain_Text() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Png_Compressor() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Png_To_Heic() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Png_To_Jpg() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Png_To_Pdf() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Png_To_Svg() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Png_To_Webp() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Random_Choice() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Random_Date() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Random_Ip() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Random_Letter() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Random_Month() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Random_Number() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Remove_Background() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Remove_Formatting() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Remove_Line_Breaks() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Remove_Underscores() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Repeat_Text() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Reverse_Text() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Roman_Numeral() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Rotate_Pdf() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Sentence_Case() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Sentence_Counter() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Sort_Words() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Svg_Compressor() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Title_Case() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Uuid_Generator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Webp_Compressor() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Webp_To_Jpg() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Webp_To_Png() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Whitespace_Remover() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Wingdings() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Word_Cloud() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Word_Frequency() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Regex_Tester() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Morse_Code_Translator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Character_Frequency_Counter() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Unicode_Translator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Binary_Translator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_HTML_Encode_Decode() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_QR_Generator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Base64_Encode_Decode() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Hash_Generator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_URL_Encode_Decode() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Resize_Image() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Fancy_Text() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Crop_Image() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Rotate_Image() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Watermark_Image() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Flip_Image() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Png_To_Ico() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Svg_To_Png() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Blur_Face() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Meme_Generator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Html_To_Image() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Image_Compressor() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Color_Picker() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Lorem_Ipsum() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Age_Calculator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Percentage_Calculator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Bmi_Calculator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Tip_Calculator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Date_Calculator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Font_Generator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Upscale_Image() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Photo_Editor() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Small_Text() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Text_To_Speech() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Readability_Checker() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Speech_To_Text() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Text_Diff() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Number_To_Words() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Words_To_Number() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Text_Summarizer() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Color_Converter() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Gradient_Generator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Dev_Utilities() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Code_Formatter() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Json_Tools() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Markdown_Preview() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Markdown_Table_Generator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Cipher_Tools() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Financial_Calculator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Unit_Converter() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Timezone_Converter() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Video_Converter() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Instagram_Caption() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Hashtag_Generator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Social_Char_Counter() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Youtube_Title() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Meta_Description() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Random_Name() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Team_Name() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Dice_Roller() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Coin_Flipper() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Random_Emoji() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Cheat_Sheet() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_QR_Reader() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Barcode_Generator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Text_To_Handwriting() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Currency_Converter() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Keyword_Density_Checker() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Fake_Name_Generator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Credit_Card_Validator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_What_Is_My_User_Agent() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Box_Shadow_Generator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_CSS_Gradient_Previewer() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Border_Radius_Generator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Flexbox_Playground() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_CSS_Grid_Playground() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Meta_Tag_Generator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Robots_Txt_Generator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Page_Speed_Checker() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Schema_Markup_Generator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Perplexity_Prompt_Generator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Claude_Prompt_Generator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Gemini_Prompt_Generator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Chatgpt_Prompt_Generator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Prompt_Optimizer() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Color_Palette_Generator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Text_To_Speech() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Speech_To_Text() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Text_Diff() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Number_To_Words() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Readability_Checker() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Sql_Cheat_Sheet() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Git_Cheat_Sheet() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Linux_Cheat_Sheet() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Tailwind_Css_Cheat_Sheet() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Words_To_Number() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Font_Pair_Generator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Wifi_Qr_Generator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Ip_Address_Lookup() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Xml_Sitemap_Generator() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Html_Preview() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Passport_Photo() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Reduce_Image_Kb() );
    $widgets_manager->register( new \TextCraft_Tools_Pro\Widget_Office_To_Pdf() );
}
add_action( 'elementor/widgets/register', 'tctp_register_widgets' );

/**
 * Force-disable e_optimized_markup and e_atomic_elements.
 *
 * Elementor 4.x promotes these to stable and they default to active,
 * which breaks server-side rendering of custom widgets.
 * Disabling them forces Elementor to render widget HTML server-side.
 */
function tctp_disable_experiments( $experiments_manager ) {
    $experiments_manager->set_feature_default_state( 'e_optimized_markup', 'inactive' );
    $experiments_manager->set_feature_default_state( 'e_atomic_elements', 'inactive' );
}
add_action( 'elementor/experiments/default-features-registered', 'tctp_disable_experiments' );

/**
 * Force e_optimized_markup and e_atomic_elements to false in frontend JS config.
 */
function tctp_disable_optimized_markup_js( $settings ) {
    if ( isset( $settings['experimentalFeatures']['e_optimized_markup'] ) ) {
        $settings['experimentalFeatures']['e_optimized_markup'] = false;
    }
    if ( isset( $settings['experimentalFeatures']['e_atomic_elements'] ) ) {
        $settings['experimentalFeatures']['e_atomic_elements'] = false;
    }
    return $settings;
}

/**
 * Enqueue Google Fonts site-wide — applies Space Grotesk + DM Sans everywhere.
 */
function tctp_enqueue_global_fonts() {
    wp_enqueue_style(
        'tctp-google-fonts',
        'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,700&display=swap',
        [],
        null
    );

    /* Apply fonts globally to the entire site */
    wp_add_inline_style( 'tctp-google-fonts', '
        /* Global — DM Sans (body) + Space Grotesk (headings) applied site-wide */
        body {
            font-family: "DM Sans", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        h1, h2, h3, h4, h5, h6,
        .elementor-heading-title,
        .elementor-widget-heading .elementor-heading-title {
            font-family: "Space Grotesk", system-ui, sans-serif;
        }
    ' );
}
add_action( 'wp_enqueue_scripts', 'tctp_enqueue_global_fonts' );

/**
 * Import map for onnxruntime-web (used by background-removal AI library).
 */
function tctp_import_map() {
    echo '<script type="importmap">{"imports":{"onnxruntime-web":"https://esm.sh/onnxruntime-web@1.21.0","onnxruntime-web/webgpu":"https://esm.sh/onnxruntime-web@1.21.0/webgpu"}}</script>' . "\n";
}
add_action( 'wp_head', 'tctp_import_map', 1 );

/**
 * COOP/COEP headers for SharedArrayBuffer (needed by ffmpeg.wasm).
 */
function tctp_send_cross_origin_isolation_headers() {
    if ( is_page( 'video-converter' ) ) {
        header( 'Cross-Origin-Opener-Policy: same-origin' );
        header( 'Cross-Origin-Embedder-Policy: credentialless' );
    }
}
add_action( 'send_headers', 'tctp_send_cross_origin_isolation_headers' );

/**
 * Enqueue plugin assets on frontend.
 */
function tctp_enqueue_assets() {
    wp_enqueue_style(
        'tctp-header-css',
        TCTP_PLUGIN_URL . 'assets/css/header.css',
        [ 'tctp-google-fonts' ],
        TCTP_VERSION
    );

    wp_enqueue_style(
        'tctp-footer-css',
        TCTP_PLUGIN_URL . 'assets/css/footer.css',
        [ 'tctp-google-fonts' ],
        TCTP_VERSION
    );

    wp_enqueue_style(
        'tctp-hero-css',
        TCTP_PLUGIN_URL . 'assets/css/hero.css',
        [ 'tctp-google-fonts' ],
        TCTP_VERSION
    );

    wp_enqueue_style(
        'tctp-strip-css',
        TCTP_PLUGIN_URL . 'assets/css/strip.css',
        [ 'tctp-google-fonts' ],
        TCTP_VERSION
    );

    wp_enqueue_style(
        'tctp-tool-page-css',
        TCTP_PLUGIN_URL . 'assets/css/tool-page.css',
        [ 'tctp-google-fonts' ],
        TCTP_VERSION
    );

    wp_enqueue_style(
        'tctp-tools-css',
        TCTP_PLUGIN_URL . 'assets/css/tools.css',
        [ 'tctp-google-fonts' ],
        TCTP_VERSION
    );

    wp_enqueue_style(
        'tctp-global-styles',
        TCTP_PLUGIN_URL . 'assets/css/global-styles.css',
        [],
        TCTP_VERSION
    );

    wp_enqueue_script(
        'tctp-header-js',
        TCTP_PLUGIN_URL . 'assets/js/header.js',
        [],
        TCTP_VERSION,
        true
    );

    wp_enqueue_script(
        'tctp-hero-js',
        TCTP_PLUGIN_URL . 'assets/js/hero.js',
        [],
        TCTP_VERSION,
        true
    );

    wp_enqueue_script(
        'tctp-tools-js',
        TCTP_PLUGIN_URL . 'assets/js/tools.js',
        [],
        TCTP_VERSION,
        true
    );

    wp_enqueue_style(
        'tctp-tool-workspace-css',
        TCTP_PLUGIN_URL . 'assets/css/tool-workspace.css',
        [ 'tctp-google-fonts' ],
        TCTP_VERSION
    );

    wp_enqueue_style(
        'tctp-tool-widgets-css',
        TCTP_PLUGIN_URL . 'assets/css/tool-widgets.css',
        [ 'tctp-google-fonts' ],
        TCTP_VERSION
    );

    wp_enqueue_style(
        'tctp-tools-below-css',
        TCTP_PLUGIN_URL . 'assets/css/tools-below.css',
        [ 'tctp-google-fonts' ],
        TCTP_VERSION
    );

    wp_enqueue_style(
        'tctp-legal-css',
        TCTP_PLUGIN_URL . 'assets/css/legal.css',
        [ 'tctp-google-fonts' ],
        TCTP_VERSION
    );

    wp_enqueue_script(
        'tctp-legal-js',
        TCTP_PLUGIN_URL . 'assets/js/legal.js',
        [],
        TCTP_VERSION,
        true
    );


wp_enqueue_style(
        'tctp-tool-premium-css',
        TCTP_PLUGIN_URL . 'assets/css/tool-premium.css',
        [ 'tctp-tool-widgets-css' ],
        TCTP_VERSION
    );

    wp_enqueue_style(
        'tctp-tools-sections-css',
        TCTP_PLUGIN_URL . 'assets/css/tools-sections.css',
        [ 'tctp-google-fonts' ],
        TCTP_VERSION
    );

    wp_enqueue_script(
        'tctp-tool-workspace-js',
        TCTP_PLUGIN_URL . 'assets/js/tool-workspace.js',
        [],
        TCTP_VERSION,
        true
    );

    // Individual tool JS — shared helper first, then each tool
    wp_enqueue_script(
        'tctp-tool-shared-js',
        TCTP_PLUGIN_URL . 'assets/js/tool-shared.js',
        [],
        TCTP_VERSION,
        true
    );

    $tool_js_files = [
        'tool-apa-format',
        'tool-ascii-art',
        'tool-case-converter',
        'tool-character-remover',
        'tool-delete-pdf-pages',
        'tool-duplicate-line',
        'tool-duplicate-word',
        'tool-em-dash-remover',
        'tool-find-replace',
        'tool-gif-compressor',
        'tool-heic-to-jpg',
        'tool-heic-to-png',
        'tool-heic-to-svg',
        'tool-image-to-text',
        'tool-invisible-text',
        'tool-jpg-compressor',
        'tool-jpg-to-avif',
        'tool-jpg-to-heic',
        'tool-jpg-to-pdf',
        'tool-jpg-to-png',
        'tool-jpg-to-svg',
        'tool-jpg-to-webp',
        'tool-json-formatter',
        'tool-nato-phonetic',
        'tool-online-notepad',
        'tool-password-generator',
        'tool-password-strength-checker',
        'tool-pdf-compressor',
        'tool-pdf-merger',
        'tool-pdf-splitter',
        'tool-pdf-to-jpg',
        'tool-pdf-to-png',
        'tool-phonetic-spelling',
        'tool-pig-latin',
        'tool-plain-text',
        'tool-png-compressor',
        'tool-png-to-heic',
        'tool-png-to-jpg',
        'tool-png-to-pdf',
        'tool-png-to-svg',
        'tool-png-to-webp',
        'tool-random-choice',
        'tool-random-date',
        'tool-random-ip',
        'tool-random-letter',
        'tool-random-month',
        'tool-random-number',
        'tool-remove-background',
        'tool-remove-formatting',
        'tool-remove-line-breaks',
        'tool-remove-underscores',
        'tool-repeat-text',
        'tool-reverse-text',
        'tool-roman-numeral',
        'tool-rotate-pdf',
        'tool-sentence-case',
        'tool-sentence-counter',
        'tool-sort-words',
        'tool-svg-compressor',
        'tool-title-case',
        'tool-uuid-generator',
        'tool-webp-compressor',
        'tool-webp-to-jpg',
        'tool-webp-to-png',
        'tool-whitespace-remover',
        'tool-wingdings',
        'tool-word-cloud',
        'tool-word-frequency',
        'tool-regex-tester',
        'tool-morse-code-translator',
        'tool-character-frequency-counter',
        'tool-unicode-translator',
        'tool-binary-translator',
        'tool-html-encode-decode',
        'tool-qr-generator',
        'tool-base64-encode-decode',
        'tool-hash-generator',
        'tool-url-encode-decode',
        'tool-resize-image',
        'tool-crop-image',
        'tool-rotate-image',
        'tool-watermark-image',
        'tool-flip-image',
        'tool-png-to-ico',
        'tool-svg-to-png',
        'tool-blur-face',
        'tool-meme-generator',
        'tool-html-to-image',
        'tool-image-compressor',
        'tool-color-picker',
        'tool-lorem-ipsum',
        'tool-age-calculator',
        'tool-percentage-calculator',
        'tool-bmi-calculator',
        'tool-tip-calculator',
        'tool-date-calculator',
        'tool-font-generator',
        'tool-upscale-image',
        'tool-photo-editor',
        'tool-small-text',
        'tool-text-to-speech',
        'tool-readability-checker',
        'tool-speech-to-text',
        'tool-text-diff',
        'tool-number-to-words',
        'tool-words-to-number',
        'tool-font-pair-generator',
        'tool-wifi-qr-generator',
        'tool-ip-address-lookup',
        'tool-xml-sitemap-generator',
        'tool-html-preview',
        'tool-text-summarizer',
        'tool-color-converter',
        'tool-gradient-generator',
        'tool-dev-utilities',
        'tool-code-formatter',
        'tool-json-tools',
        'tool-markdown-preview',
        'tool-markdown-table-generator',
        'tool-cipher-tools',
        'tool-financial-calculator',
        'tool-unit-converter',
        'tool-timezone-converter',
        'tool-fancy-text',
        'tool-video-converter',
        'tool-instagram-caption',
        'tool-hashtag-generator',
        'tool-social-char-counter',
        'tool-youtube-title',
        'tool-meta-description',
        'tool-random-name',
        'tool-team-name',
        'tool-dice-roller',
        'tool-coin-flipper',
        'tool-random-emoji',
        'tool-cheat-sheet',
        'tool-qr-reader',
        'tool-barcode-generator',
        'tool-text-to-handwriting',
        'tool-currency-converter',
        'tool-keyword-density-checker',
        'tool-fake-name-generator',
        'tool-credit-card-validator',
        'tool-what-is-my-user-agent',
        'tool-box-shadow-generator',
        'tool-css-gradient-previewer',
        'tool-border-radius-generator',
        'tool-flexbox-playground',
        'tool-css-grid-playground',
        'tool-meta-tag-generator',
        'tool-robots-txt-generator',
        'tool-page-speed-checker',
        'tool-schema-markup-generator',
        'tool-passport-photo',
        'tool-reduce-image-kb',
        'tool-office-to-pdf',
    ];

    foreach ( $tool_js_files as $js_file ) {
        wp_enqueue_script(
            'tctp-' . $js_file,
            TCTP_PLUGIN_URL . 'assets/js/' . $js_file . '.js',
            [ 'tctp-tool-shared-js' ],
            TCTP_VERSION,
            true
        );
    }

    wp_localize_script(
        'tctp-tool-video-converter',
        'tctpWasm',
        [ 'url' => TCTP_PLUGIN_URL . 'assets/wasm' ]
    );

    wp_register_script(
        'tctp-lib-omggif',
        TCTP_PLUGIN_URL . 'assets/js/lib-omggif.js',
        [],
        '1.0.10',
        true
    );
    wp_register_script(
        'tctp-lib-gifjs',
        'https://cdn.jsdelivr.net/npm/gif.js@0.2.0/dist/gif.js',
        [],
        '0.2.0',
        true
    );
    wp_register_script(
        'tctp-tool-gif-compressor',
        TCTP_PLUGIN_URL . 'assets/js/tool-gif-compressor.js',
        [ 'tctp-tool-shared-js', 'tctp-lib-omggif' ],
        TCTP_VERSION,
        true
    );
    if ( is_page( 'gif-compressor' ) ) {
        wp_enqueue_script( 'tctp-lib-omggif' );
        wp_enqueue_script( 'tctp-tool-gif-compressor' );
    }

    // omggif also needed by jpg-to-gif converter
    wp_enqueue_script( 'tctp-lib-omggif' );

    // jpg-to-gif needs omggif as dependency
    wp_register_script(
        'tctp-tool-jpg-to-gif',
        TCTP_PLUGIN_URL . 'assets/js/tool-jpg-to-gif.js',
        [ 'tctp-tool-shared-js', 'tctp-lib-omggif' ],
        TCTP_VERSION,
        true
    );
    wp_enqueue_script( 'tctp-tool-jpg-to-gif' );

    wp_localize_script(
        'tctp-tool-shared-js',
        'tctpGifWorker',
        [ 'url' => TCTP_PLUGIN_URL . 'assets/js/gif.worker.js' ]
    );
}
add_action( 'wp_enqueue_scripts', 'tctp_enqueue_assets' );

/**
 * Enqueue assets in Elementor editor preview so fonts render live.
 */
function tctp_enqueue_editor_assets() {
    wp_enqueue_style(
        'tctp-google-fonts',
        'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,700&display=swap',
        [],
        null
    );

    /* Apply fonts globally inside the Elementor editor preview */
    wp_add_inline_style( 'tctp-google-fonts', '
        /* Global — DM Sans (body) + Space Grotesk (headings) in Elementor editor */
        body {
            font-family: "DM Sans", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        h1, h2, h3, h4, h5, h6,
        .elementor-heading-title,
        .elementor-widget-heading .elementor-heading-title {
            font-family: "Space Grotesk", system-ui, sans-serif;
        }
    ' );

    wp_enqueue_style(
        'tctp-header-css',
        TCTP_PLUGIN_URL . 'assets/css/header.css',
        [ 'tctp-google-fonts' ],
        TCTP_VERSION
    );

    wp_enqueue_style(
        'tctp-footer-css',
        TCTP_PLUGIN_URL . 'assets/css/footer.css',
        [ 'tctp-google-fonts' ],
        TCTP_VERSION
    );

    wp_enqueue_style(
        'tctp-hero-css',
        TCTP_PLUGIN_URL . 'assets/css/hero.css',
        [ 'tctp-google-fonts' ],
        TCTP_VERSION
    );

    wp_enqueue_style(
        'tctp-strip-css',
        TCTP_PLUGIN_URL . 'assets/css/strip.css',
        [ 'tctp-google-fonts' ],
        TCTP_VERSION
    );

    wp_enqueue_style(
        'tctp-tool-page-css',
        TCTP_PLUGIN_URL . 'assets/css/tool-page.css',
        [ 'tctp-google-fonts' ],
        TCTP_VERSION
    );

    wp_enqueue_style(
        'tctp-tools-css',
        TCTP_PLUGIN_URL . 'assets/css/tools.css',
        [ 'tctp-google-fonts' ],
        TCTP_VERSION
    );

    wp_enqueue_style(
        'tctp-tool-workspace-css',
        TCTP_PLUGIN_URL . 'assets/css/tool-workspace.css',
        [ 'tctp-google-fonts' ],
        TCTP_VERSION
    );

    wp_enqueue_style(
        'tctp-tool-widgets-css',
        TCTP_PLUGIN_URL . 'assets/css/tool-widgets.css',
        [ 'tctp-google-fonts' ],
        TCTP_VERSION
    );

    wp_enqueue_style(
        'tctp-tool-premium-css',
        TCTP_PLUGIN_URL . 'assets/css/tool-premium.css',
        [ 'tctp-tool-widgets-css' ],
        TCTP_VERSION
    );

    wp_enqueue_style(
        'tctp-tools-sections-css',
        TCTP_PLUGIN_URL . 'assets/css/tools-sections.css',
        [ 'tctp-google-fonts' ],
        TCTP_VERSION
    );

    wp_enqueue_style(
        'tctp-tools-below-css',
        TCTP_PLUGIN_URL . 'assets/css/tools-below.css',
        [ 'tctp-google-fonts' ],
        TCTP_VERSION
    );

    wp_enqueue_style(
        'tctp-legal-css',
        TCTP_PLUGIN_URL . 'assets/css/legal.css',
        [ 'tctp-google-fonts' ],
        TCTP_VERSION
    );
}
add_action( 'elementor/preview/enqueue_styles', 'tctp_enqueue_editor_assets' );

/**
 * Create default WordPress menus on plugin activation.
 * Populates Appearance → Menus with all mega menu columns.
 */
function tctp_create_default_menus() {
    /* Helper: create a menu, add items, return slug */
    $menus_data = [
        'pdf-tools' => [
            'title' => 'PDF Tools',
            'items' => [
                'PDF Compressor'      => '#pdf',
                'PDF to Word'         => '#pdf',
                'PDF Splitter'        => '#pdf',
                'PDF Merger'          => '#pdf',
                'Delete PDF Pages'    => '#pdf',
                'Rotate PDF'          => '#pdf',
                'JPG to PDF'          => '#pdf',
                'PDF to JPG'          => '#pdf',
                'PDF to PNG'          => '#pdf',
            ],
        ],
        'compression' => [
            'title' => 'Compression',
            'items' => [
                'JPG Compressor'      => '#compress',
                'PNG Compressor'      => '#compress',
                'WebP Compressor'     => '#compress',
                'GIF Compressor'      => '#compress',
                'SVG Compressor'      => '#compress',
            ],
        ],
        'image-media' => [
            'title' => 'Image & Media',
            'items' => [
                'Remove Background'   => '#image',
                'Image to Text (OCR)' => '#image',
                'ASCII Art Generator' => '#image',
                'JPG to PNG'          => '#image',
                'JPG to WebP'         => '#image',
                'Image Resizer'       => '#image',
                'Image Cropper'       => '#image',
                'Favicon Generator'   => '#image',
            ],
        ],
        'text-tools' => [
            'title' => 'Text Tools',
            'items' => [
                'Case Converter'      => '#text',
                'Word Counter'        => '#text',
                'Text Cleaner'        => '#text',
                'Text Diff'           => '#text',
                'Line Sorter'         => '#text',
                'Find & Replace'      => '#text',
                'Reverse Text'        => '#text',
                'Slug Generator'      => '#text',
            ],
        ],
        'developer' => [
            'title' => 'Developer',
            'items' => [
                'JSON Formatter'      => '#dev',
                'Base64 Encoder'      => '#dev',
                'Hash Generator'      => '#dev',
                'URL Encoder'         => '#dev',
                'Regex Tester'        => '#dev',
                'QR Generator'        => '#dev',
                'HTML Minifier'       => '#dev',
                'Color Converter'     => '#dev',
            ],
        ],
        'main-navigation' => [
            'title' => 'Main Navigation',
            'items' => [
                'PDF'       => '#pdf',
                'Image'     => '#image',
                'Text'      => '#text',
                'Developer' => '#dev',
                'Blog'      => '#',
            ],
        ],
    ];

    foreach ( $menus_data as $slug => $data ) {
        /* Skip if menu already exists */
        $existing = wp_get_nav_menu_object( $data['title'] );
        if ( $existing ) {
            continue;
        }

        /* Create the menu */
        $menu_id = wp_create_nav_menu( $data['title'] );
        if ( is_wp_error( $menu_id ) ) {
            continue;
        }

        /* Add items to the menu */
        $menu_item_id = 0;
        foreach ( $data['items'] as $label => $url ) {
            $menu_item_id = wp_update_nav_menu_item( $menu_id, 0, [
                'menu-item-title'     => $label,
                'menu-item-url'       => $url,
                'menu-item-status'    => 'publish',
                'menu-item-type'      => 'custom',
            ] );
        }

        /* Assign to theme location (primary) if not already assigned */
        $locations = get_theme_mod( 'nav_menu_locations', [] );
        if ( empty( $locations['primary'] ) && $slug === 'main-navigation' ) {
            $locations['primary'] = $menu_id;
            set_theme_mod( 'nav_menu_locations', $locations );
        }
    }
}
register_activation_hook( __FILE__, 'tctp_create_default_menus' );

/**
 * Auto-create menus on admin_init if they don't exist yet.
 * Safety net in case activation hook was skipped.
 */
function tctp_maybe_create_menus() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $check = get_option( 'tctp_menus_created' );
    if ( $check ) {
        return;
    }
    tctp_create_default_menus();
    update_option( 'tctp_menus_created', true );
}
add_action( 'admin_init', 'tctp_maybe_create_menus' );

/**
 * Create the Home page with Elementor data (Header, Hero, Footer) on activation.
 */
function tctp_create_home_page() {
    /* Check if page already exists */
    $existing = get_page_by_path( 'home' );
    if ( $existing ) {
        return;
    }

    /* Create the page */
    $page_id = wp_insert_post( [
        'post_title'   => 'Home',
        'post_name'    => 'home',
        'post_content' => '',
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'page_template' => 'elementor_header_footer',
    ] );

    if ( ! $page_id || is_wp_error( $page_id ) ) {
        return;
    }

    /* Enable Elementor builder on this page */
    update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );

    /* Build Elementor data with Header, Hero, and Footer */
    $elementor_data = tctp_build_home_elementor_data();
    update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $elementor_data ) ) );

    /* Set page settings — stored as array, not JSON string */
    update_post_meta( $page_id, '_elementor_page_settings', [
        'content_width' => [
            'unit'  => 'px',
            'size'  => 1200,
        ],
    ] );

    /* Set as static front page */
    update_option( 'show_on_front', 'page' );
    update_option( 'page_on_front', $page_id );
}

/**
 * Build the Elementor data array for the Home page.
 *
 * Structure: Section > Column > Widget
 *
 * @return array Elementor data array.
 */
function tctp_build_home_elementor_data() {
    $elements = [];

    /* ---- Section 1: Header (full-width, no padding) ---- */
    $header_id    = tctp_gen_id();
    $col_id       = tctp_gen_id();
    $widget_id    = tctp_gen_id();

    $elements[] = [
        'id'       => $header_id,
        'elType'   => 'section',
        'settings' => [
            'layout'                => 'full_width',
            'content_width'         => [ 'unit' => 'px', 'size' => '' ],
            'stretch_section'       => 'full_width',
            'space_between'         => [ 'unit' => 'px', 'size' => 0 ],
            'gap'                   => 'no',
            'background_background' => '',
            'padding'               => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => false ],
        ],
        'elements' => [
            [
                'id'       => $col_id,
                'elType'   => 'column',
                'settings' => [
                    '_column_size' => 100,
                    'padding'     => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => false ],
                ],
                'elements' => [
                    [
                        'id'         => $widget_id,
                        'elType'     => 'widget',
                        'widgetType' => 'tctp_header',
                        'settings'   => [],
                        'elements'   => [],
                    ],
                ],
            ],
        ],
    ];

    /* ---- Section 2: Hero (full-width, no padding) ---- */
    $hero_sec_id  = tctp_gen_id();
    $hero_col_id  = tctp_gen_id();
    $hero_wgt_id  = tctp_gen_id();

    $elements[] = [
        'id'       => $hero_sec_id,
        'elType'   => 'section',
        'settings' => [
            'layout'                => 'full_width',
            'content_width'         => [ 'unit' => 'px', 'size' => '' ],
            'stretch_section'       => 'full_width',
            'space_between'         => [ 'unit' => 'px', 'size' => 0 ],
            'gap'                   => 'no',
            'padding'               => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => false ],
        ],
        'elements' => [
            [
                'id'       => $hero_col_id,
                'elType'   => 'column',
                'settings' => [
                    '_column_size' => 100,
                    'padding'     => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => false ],
                ],
                'elements' => [
                    [
                        'id'         => $hero_wgt_id,
                        'elType'     => 'widget',
                        'widgetType' => 'tctp_hero',
                        'settings'   => [],
                        'elements'   => [],
                    ],
                ],
            ],
        ],
    ];

    /* ---- Section 3: Tools Section (full-width, no padding) ---- */
    $tools_sec_id = tctp_gen_id();
    $tools_col_id = tctp_gen_id();
    $tools_wgt_id = tctp_gen_id();

    $elements[] = [
        'id'       => $tools_sec_id,
        'elType'   => 'section',
        'settings' => [
            'layout'                => 'full_width',
            'content_width'         => [ 'unit' => 'px', 'size' => '' ],
            'stretch_section'       => 'full_width',
            'space_between'         => [ 'unit' => 'px', 'size' => 0 ],
            'gap'                   => 'no',
            'padding'               => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => false ],
        ],
        'elements' => [
            [
                'id'       => $tools_col_id,
                'elType'   => 'column',
                'settings' => [
                    '_column_size' => 100,
                    'padding'     => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => false ],
                ],
                'elements' => [
                    [
                        'id'         => $tools_wgt_id,
                        'elType'     => 'widget',
                        'widgetType' => 'tctp_tools_section',
                        'settings'   => [],
                        'elements'   => [],
                    ],
                ],
            ],
        ],
    ];

    /* ---- Section 4: Strip (full-width, no padding) ---- */
    $strip_sec_id = tctp_gen_id();
    $strip_col_id = tctp_gen_id();
    $strip_wgt_id = tctp_gen_id();

    $elements[] = [
        'id'       => $strip_sec_id,
        'elType'   => 'section',
        'settings' => [
            'layout'                => 'full_width',
            'content_width'         => [ 'unit' => 'px', 'size' => '' ],
            'stretch_section'       => 'full_width',
            'space_between'         => [ 'unit' => 'px', 'size' => 0 ],
            'gap'                   => 'no',
            'padding'               => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => false ],
        ],
        'elements' => [
            [
                'id'       => $strip_col_id,
                'elType'   => 'column',
                'settings' => [
                    '_column_size' => 100,
                    'padding'     => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => false ],
                ],
                'elements' => [
                    [
                        'id'         => $strip_wgt_id,
                        'elType'     => 'widget',
                        'widgetType' => 'tctp_strip',
                        'settings'   => [],
                        'elements'   => [],
                    ],
                ],
            ],
        ],
    ];

    /* ---- Section 5: Footer (full-width, no padding) ---- */
    $footer_sec_id = tctp_gen_id();
    $footer_col_id = tctp_gen_id();
    $footer_wgt_id = tctp_gen_id();

    $elements[] = [
        'id'       => $footer_sec_id,
        'elType'   => 'section',
        'settings' => [
            'layout'                => 'full_width',
            'content_width'         => [ 'unit' => 'px', 'size' => '' ],
            'stretch_section'       => 'full_width',
            'space_between'         => [ 'unit' => 'px', 'size' => 0 ],
            'gap'                   => 'no',
            'padding'               => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => false ],
        ],
        'elements' => [
            [
                'id'       => $footer_col_id,
                'elType'   => 'column',
                'settings' => [
                    '_column_size' => 100,
                    'padding'     => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => false ],
                ],
                'elements' => [
                    [
                        'id'         => $footer_wgt_id,
                        'elType'     => 'widget',
                        'widgetType' => 'tctp_footer',
                        'settings'   => [],
                        'elements'   => [],
                    ],
                ],
            ],
        ],
    ];

    return $elements;
}

/**
 * Generate a unique 7-character hex ID for Elementor elements.
 *
 * @return string
 */
function tctp_gen_id() {
    return substr( md5( uniqid( mt_rand(), true ) ), 0, 7 );
}
register_activation_hook( __FILE__, 'tctp_create_home_page' );

/**
 * Auto-create home page on admin_init if not yet created.
 */
function tctp_maybe_create_home_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    /* Create menus if needed */
    $menu_check = get_option( 'tctp_menus_created' );
    if ( ! $menu_check ) {
        tctp_create_default_menus();
        update_option( 'tctp_menus_created', true );
    }

    /* Create home page if needed */
    $home_check = get_option( 'tctp_home_created' );
    if ( ! $home_check ) {
        tctp_create_home_page();
        update_option( 'tctp_home_created', true );
    }

    $version_check = get_option( 'tctp_data_version', 0 );

    /* V4 migration: Add workspace widget to all tool pages (runs before home rebuild) */
    if ( $version_check < 4 ) {
        tctp_migrate_add_workspace_to_tool_pages();
    }

    /* If home page exists but has no Elementor data, rebuild it */
    $page = get_page_by_path( 'home' );
    if ( $page ) {
        $mode = get_post_meta( $page->ID, '_elementor_edit_mode', true );
        $data = get_post_meta( $page->ID, '_elementor_data', true );
        if ( empty( $mode ) || empty( $data ) || $version_check < TCTP_DATA_VERSION ) {
            update_post_meta( $page->ID, '_elementor_edit_mode', 'builder' );
            update_post_meta( $page->ID, '_elementor_data', wp_slash( wp_json_encode( tctp_build_home_elementor_data() ) ) );
            update_post_meta( $page->ID, 'page_template', 'elementor_header_footer' );
            update_option( 'show_on_front', 'page' );
            update_option( 'page_on_front', $page->ID );
        }
    }

    /* Always update version and clear CSS cache if needed */
    if ( $version_check < TCTP_DATA_VERSION ) {
        update_option( 'tctp_data_version', TCTP_DATA_VERSION );
        /* Clear Elementor CSS cache */
        $css_dir = WP_CONTENT_DIR . '/uploads/elementor/css/';
        if ( is_dir( $css_dir ) ) {
            $files = glob( $css_dir . '*.css' );
            foreach ( $files as $file ) {
                @unlink( $file );
            }
        }
    }
}
add_action( 'admin_init', 'tctp_maybe_create_home_page' );

/**
 * V4 Migration: Insert Tool Workspace widget into all 78 tool pages.
 *
 * Each tool page currently has: Hero → Content → Strip
 * We need: Hero → Workspace → Content → Strip
 */
function tctp_migrate_add_workspace_to_tool_pages() {
    $parent = get_page_by_path( 'tools' );
    if ( ! $parent ) {
        return;
    }

    $pages = get_posts( [
        'post_type'   => 'page',
        'post_parent' => $parent->ID,
        'numberposts' => -1,
        'post_status' => 'publish',
    ] );

    $workspace_widget_id = 'tctp_tool_workspace';
    $updated = 0;
    $skipped = 0;

    foreach ( $pages as $page ) {
        $raw = get_post_meta( $page->ID, '_elementor_data', true );
        if ( empty( $raw ) ) {
            continue;
        }

        $elements = json_decode( $raw, true );
        if ( ! is_array( $elements ) ) {
            continue;
        }

        /* Check if workspace widget already exists */
        $has_workspace = false;
        foreach ( $elements as $section ) {
            if ( ! isset( $section['elements'] ) ) continue;
            foreach ( $section['elements'] as $col ) {
                if ( ! isset( $col['elements'] ) ) continue;
                foreach ( $col['elements'] as $widget ) {
                    if ( isset( $widget['widgetType'] ) && $widget['widgetType'] === $workspace_widget_id ) {
                        $has_workspace = true;
                        break 3;
                    }
                }
            }
        }

        if ( $has_workspace ) {
            $skipped++;
            continue;
        }

        /* Find hero widget section+column index to insert after it */
        $hero_section_index = -1;
        $hero_column_index = -1;

        foreach ( $elements as $sIdx => $section ) {
            if ( ! isset( $section['elements'] ) ) continue;
            foreach ( $section['elements'] as $cIdx => $col ) {
                if ( ! isset( $col['elements'] ) ) continue;
                foreach ( $col['elements'] as $widget ) {
                    if ( isset( $widget['widgetType'] ) && $widget['widgetType'] === 'tctp_tool_hero' ) {
                        $hero_section_index = $sIdx;
                        $hero_column_index = $cIdx;
                        break 3;
                    }
                }
            }
        }

        /* Extract tool slug from page slug */
        $slug = $page->post_name;

        /* Build the workspace widget element */
        $ws_widget_id = tctp_gen_id();
        $workspace_widget = [
            'id'         => $ws_widget_id,
            'elType'     => 'widget',
            'widgetType' => $workspace_widget_id,
            'settings'   => [
                'tool_slug' => $slug,
            ],
            'elements'   => [],
        ];

        if ( $hero_section_index >= 0 && isset( $elements[ $hero_section_index ]['elements'][ $hero_column_index ] ) ) {
            /* Insert workspace widget into the same column as hero, right after it */
            $col = &$elements[ $hero_section_index ]['elements'][ $hero_column_index ];
            $col['elements'][] = $workspace_widget;
        } else {
            /* No hero found — create a new full-width section for workspace */
            $section_id  = tctp_gen_id();
            $column_id   = tctp_gen_id();

            $new_section = [
                'id'         => $section_id,
                'elType'     => 'section',
                'settings'   => [
                    'content_width' => [
                        'unit' => 'px',
                        'size' => 1200,
                    ],
                ],
                'elements'   => [
                    [
                        'id'       => $column_id,
                        'elType'   => 'column',
                        'settings' => [
                            '_column_size' => 100,
                        ],
                        'elements' => [
                            $workspace_widget,
                        ],
                    ],
                ],
            ];

            /* Insert after first section (hero) or at beginning */
            if ( count( $elements ) > 0 ) {
                array_splice( $elements, 1, 0, [ $new_section ] );
            } else {
                $elements[] = $new_section;
            }
        }

        update_post_meta( $page->ID, '_elementor_data', wp_slash( wp_json_encode( $elements ) ) );
        $updated++;
    }

    if ( $updated > 0 || $skipped > 0 ) {
        error_log( "TCTP v4 migration: {$updated} tool pages updated with workspace widget, {$skipped} skipped (already has workspace)." );
    }
}

/**
 * Register Elementor category.
 */
function tctp_register_category( $elements_manager ) {
    $elements_manager->add_category(
        'textcraft-tools',
        [
            'title' => __( 'TextCraft Tools', 'textcrafttoolspro' ),
            'icon'  => 'eicon-globe',
        ]
    );
}
add_action( 'elementor/elements/categories_registered', 'tctp_register_category' );
