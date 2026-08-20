<?php
/**
 * Plugin Name:       TextCraft Tools
 * Plugin URI:        https://example.com/textcraft-tools
 * Description:       Free online text tools, image converters, and PDF utilities for WordPress. 40+ browser-based tools including a case converter, text cleaner, password generator, image compressor, PDF merger, and more — all processed locally with no data uploaded to any server. Privacy-focused online utilities for US and UK users.
 * Version:           1.0.1
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            TextCraft
 * Author URI:        https://example.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       textcraft-tools
 * Domain Path:       /languages
 *
 * @package TextCraft_Tools
 */

declare( strict_types = 1 );

// ── Guard: direct access forbidden ───────────────────────────
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── Plugin constants ──────────────────────────────────────────
define( 'TEXTCRAFT_VERSION',     '1.0.0.1' );
define( 'TEXTCRAFT_PLUGIN_FILE', __FILE__ );
define( 'TEXTCRAFT_PLUGIN_DIR',  plugin_dir_path( __FILE__ ) );
define( 'TEXTCRAFT_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );
define( 'TEXTCRAFT_MIN_PHP',     '8.0' );
define( 'TEXTCRAFT_MIN_WP',      '6.0' );
define( 'TEXTCRAFT_MIN_EL',      '3.10.0' );

// ── PHP version gate ──────────────────────────────────────────
if ( version_compare( PHP_VERSION, TEXTCRAFT_MIN_PHP, '<' ) ) {
	add_action(
		'admin_notices',
		static function (): void {
			printf(
				'<div class="notice notice-error"><p>%s</p></div>',
				esc_html(
					sprintf(
						/* translators: 1: plugin name, 2: required PHP version, 3: running PHP version */
						__( '%1$s requires PHP %2$s or higher. You are running PHP %3$s.', 'textcraft-tools' ),
						'TextCraft Tools',
						TEXTCRAFT_MIN_PHP,
						PHP_VERSION
					)
				)
			);
		}
	);
	return;
}

// ── Load the plugin only when Elementor is ready ─────────────
add_action( 'elementor/init', static function (): void {
	require_once TEXTCRAFT_PLUGIN_DIR . 'includes/class-textcraft-loader.php';
	TextCraft_Tools\TextCraft_Loader::instance();

	// Load Mega Menu helper (runs elementor/init so Rank Math is already loaded).
	require_once TEXTCRAFT_PLUGIN_DIR . 'includes/class-megamenu-helper.php';

	// Mark "All Tools" for mega-menu rendering.
	add_filter( 'wp_nav_menu_objects', [ TextCraft_Tools\Mega_Menu_Helper::class, 'mark_all_tools_item' ], 10, 2 );

	// Append mega trigger to header nav.
	add_filter( 'wp_nav_menu_items', [ TextCraft_Tools\Mega_Menu_Helper::class, 'add_items_filter' ], 10, 2 );

	// Register shortcode for testing.
	add_shortcode( 'textcraft_tools_megamenu', [ TextCraft_Tools\Mega_Menu_Helper::class, 'shortcode' ] );
} );

// ── Premium Footer Builder ──────────────────────────────────
require_once TEXTCRAFT_PLUGIN_DIR . 'includes/class-footer-builder.php';
TextCraft_Tools\Footer_Builder::init();

// ── Body classes for page-specific CSS scoping ───────────────
add_filter( 'body_class', static function ( array $classes ): array {
	if ( is_page( 'accessibility-statement' ) ) {
		$classes[] = 'tc-accessibility-page';
	}
	if ( is_page( 'about-us' ) ) {
		$classes[] = 'tc-about-page';
	}
	if ( is_page( 'dmca-policy' ) ) {
		$classes[] = 'tc-dmca-page';
	}
	return $classes;
} );

// ── Activation hook: create mega menu on first activation ────
register_activation_hook( TEXTCRAFT_PLUGIN_FILE, static function (): void {
	if ( ! get_option( 'textcraft_menu_synced' ) ) {
		// Defer to elementor/init so wp_get_nav_menu_items works
		add_action( 'elementor/init', static function (): void {
			require_once TEXTCRAFT_PLUGIN_DIR . 'includes/class-megamenu-helper.php';
			TextCraft_Tools\Mega_Menu_Helper::sync_menu();
		}, 20 );
	}
} );

// ── Notify if Elementor is missing ───────────────────────────
add_action( 'admin_notices', static function (): void {
	if ( did_action( 'elementor/init' ) ) {
		return;
	}
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-warning is-dismissible"><p>';
	echo wp_kses(
		sprintf(
			/* translators: %s: Elementor plugin link */
			__( '<strong>TextCraft Tools</strong> requires %s to be installed and active.', 'textcraft-tools' ),
			'<a href="https://wordpress.org/plugins/elementor/" target="_blank" rel="noopener noreferrer">Elementor</a>'
		),
		[ 'strong' => [], 'a' => [ 'href' => [], 'target' => [], 'rel' => [] ] ]
	);
	echo '</p></div>';
} );


// ── Admin menu (runs independently of Elementor) ─────────────
add_action( 'admin_menu', static function (): void {
	add_menu_page(
		'TextCraft Tools',
		'TextCraft Tools',
		'manage_options',
		'textcraft-tools',
		static function (): void {
			$sync_result = '';
			if ( isset( $_GET['tc_sync_menu'] ) && '1' === $_GET['tc_sync_menu'] ) {
				check_admin_referer( 'tc_sync_menu_action' );
				$result      = TextCraft_Tools\Mega_Menu_Helper::sync_menu();
				$sync_result = $result['success']
					? '<div class="notice notice-success is-dismissible"><p>' . esc_html( $result['message'] ) . '</p></div>'
					: '<div class="notice notice-error is-dismissible"><p>' . esc_html( $result['message'] ) . '</p></div>';
			}

			echo '<div class="wrap">';
			echo '<h1 style="color:#d4a24c;">' . esc_html__( 'TextCraft Tools', 'textcraft-tools' ) . '</h1>';
			echo '<p style="color:#a8997d;">';
			esc_html_e( 'Welcome to TextCraft Tools. The plugin provides over 70 browser-based tools for text, image, and PDF processing.', 'textcraft-tools' );
			echo '</p>';

			// Allow HTML in sync result
			echo wp_kses_post( $sync_result );

			$menu_assigned = false;
			$locations     = get_nav_menu_locations();
			if ( ! empty( $locations['menu-1'] ) ) {
				$menu_obj = wp_get_nav_menu_object( $locations['menu-1'] );
				if ( $menu_obj ) {
					$menu_assigned = true;
					echo '<div class="notice notice-info is-dismissible"><p>'
						. esc_html__( 'Header menu assigned:', 'textcraft-tools' ) . ' <strong>' . esc_html( $menu_obj->name ) . '</strong>'
						. ' &mdash; <a href="' . esc_url( admin_url( 'nav-menus.php?action=edit&menu=' . $menu_obj->term_id ) ) . '">'
						. esc_html__( 'Edit in Appearance → Menus', 'textcraft-tools' ) . '</a></p></div>';
				}
			}

			if ( ! $menu_assigned ) {
				echo '<div class="notice notice-warning"><p>'
					. esc_html__( 'No menu is assigned to the Header (menu-1) location. Use the sync button below to create and assign the TextCraft Tools menu.', 'textcraft-tools' )
					. '</p></div>';
			}

			echo '<hr><h2>' . esc_html__( 'Mega Menu Sync', 'textcraft-tools' ) . '</h2>';
			echo '<p>' . esc_html__( 'Click the button below to create or rebuild the "TextCraft Tools" navigation menu with all 74 tools. This will overwrite any existing menu with the same name.', 'textcraft-tools' ) . '</p>';
			echo '<p><a href="' . esc_url( wp_nonce_url( admin_url( 'admin.php?page=textcraft-tools&tc_sync_menu=1' ), 'tc_sync_menu_action' ) ) . '" class="button button-primary">'
				. esc_html__( 'Sync Mega Menu', 'textcraft-tools' ) . '</a></p>';

			echo '</div>';
		},
		'dashicons-text',
		'30'
	);
} );

// ── SEO content injection via the_content filter ─────────────
