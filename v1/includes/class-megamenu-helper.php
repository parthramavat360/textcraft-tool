<?php
declare( strict_types=1 );

namespace TextCraft_Tools;

defined( 'ABSPATH' ) || exit;

class Mega_Menu_Helper {

	/**
	 * Seed tool data — used ONLY by sync_menu() for initial menu creation.
	 * After sync, the renderer reads from wp_get_nav_menu_items().
	 *
	 * @return array<string, array<int, array{name:string,url:string,icon:string}>>
	 */
	private static function get_grouped_tools(): array {
		$tools = [
			[ 'PDF Tools', 'PDF Compressor',              '/tools/pdf-compressor/',           'PDF' ],
			[ 'PDF Tools', 'PDF to Word Converter',        '/tools/pdf-to-word-converter/',    'PDF' ],
			[ 'PDF Tools', 'Delete PDF Pages',             '/tools/delete-pdf-pages/',         "\xF0\x9F\x97\x91\xEF\xB8\x8F" ],
			[ 'PDF Tools', 'JPG to PDF',                   '/tools/jpg-to-pdf-converter/',     "\xF0\x9F\x93\x84" ],
			[ 'PDF Tools', 'PDF Merger',                   '/tools/pdf-merger/',               "\xF0\x9F\x93\x91" ],
			[ 'PDF Tools', 'PDF Splitter',                 '/tools/pdf-splitter/',             "\xE2\x9C\x82\xEF\xB8\x8F" ],
			[ 'PDF Tools', 'PDF to JPG',                   '/tools/pdf-to-jpg-converter/',     "\xF0\x9F\x96\xBC\xEF\xB8\x8F" ],
			[ 'PDF Tools', 'PDF to PNG',                   '/tools/pdf-to-png-converter/',     "\xF0\x9F\x9F\xA6" ],
			[ 'PDF Tools', 'Rotate PDF',                   '/tools/rotate-pdf/',               "\xF0\x9F\x94\x84" ],

			[ 'Image Compression Tools', 'JPG Compressor', '/tools/jpg-compressor/',            "\xF0\x9F\x96\xBC\xEF\xB8\x8F" ],
			[ 'Image Compression Tools', 'PNG Compressor', '/tools/png-compressor/',            "\xF0\x9F\x9F\xA6" ],
			[ 'Image Compression Tools', 'WebP Compressor','/tools/webp-compressor/',           "\xE2\x9A\xA1" ],
			[ 'Image Compression Tools', 'GIF Compressor', '/tools/gif-compressor/',            "\xF0\x9F\x8E\x9E\xEF\xB8\x8F" ],
			[ 'Image Compression Tools', 'SVG Compressor', '/tools/svg-compressor/',            'SVG' ],

			[ 'Image & Media Conversion Tools', 'PixelScript — ASCII Art Generator',  '/tools/image-to-ascii-art/',           "\xF0\x9F\x8E\xA8" ],
			[ 'Image & Media Conversion Tools', 'TextLens — Image to Text',           '/tools/image-to-text-ocr/',            "\xF0\x9F\x94\x8D" ],
			[ 'Image & Media Conversion Tools', 'Remove Background from Image',       '/tools/remove-background-from-image/', 'BG' ],
			[ 'Image & Media Conversion Tools', 'SnapConvert — JPG to PNG',           '/tools/jpg-to-png-converter/',         "\xF0\x9F\x93\xB8" ],
			[ 'Image & Media Conversion Tools', 'SwiftWebP — JPG to WebP',            '/tools/jpg-to-webp-converter/',        "\xE2\x9A\xA1" ],
			[ 'Image & Media Conversion Tools', 'VectorTrace - JPG to SVG',           '/tools/jpg-to-svg-converter/',         "\xE2\x9C\x8F\xEF\xB8\x8F" ],
			[ 'Image & Media Conversion Tools', 'MotionConvert - JPG to GIF',         '/tools/jpg-to-gif-converter/',         "\xF0\x9F\x8E\x9E\xEF\xB8\x8F" ],
			[ 'Image & Media Conversion Tools', 'AppleFrame - JPG to HEIC',           '/tools/jpg-to-heic-converter/',        "\xF0\x9F\x93\xB1" ],
			[ 'Image & Media Conversion Tools', 'AviForge - JPG to AVIF',             '/tools/jpg-to-avif-converter/',        "\xF0\x9F\x8C\x9F" ],
			[ 'Image & Media Conversion Tools', 'PhotoShift - PNG to JPG',            '/tools/png-to-jpg-converter/',         "\xF0\x9F\x96\xBC\xEF\xB8\x8F" ],
			[ 'Image & Media Conversion Tools', 'WebPForge - PNG to WebP',            '/tools/png-to-webp-converter/',        "\xE2\x9A\xA1" ],
			[ 'Image & Media Conversion Tools', 'VectorLift - PNG to SVG',            '/tools/png-to-svg-converter/',         "\xE2\x9C\x8F\xEF\xB8\x8F" ],
			[ 'Image & Media Conversion Tools', 'AppleSnap - PNG to HEIC',            '/tools/png-to-heic-converter/',        "\xF0\x9F\x93\xB1" ],
			[ 'Image & Media Conversion Tools', 'PhotoConvert - HEIC to JPG',         '/tools/heic-to-jpg-converter/',        "\xF0\x9F\x96\xBC\xEF\xB8\x8F" ],
			[ 'Image & Media Conversion Tools', 'PixelConvert - HEIC to PNG',         '/tools/heic-to-png-converter/',        "\xF0\x9F\x9F\xA6" ],
			[ 'Image & Media Conversion Tools', 'VectorConvert - HEIC to SVG',        '/tools/heic-to-svg-converter/',        "\xE2\x9C\x8F\xEF\xB8\x8F" ],
			[ 'Image & Media Conversion Tools', 'PhotoRestore - WebP to JPG',         '/tools/webp-to-jpg-converter/',        "\xF0\x9F\x96\xBC\xEF\xB8\x8F" ],
			[ 'Image & Media Conversion Tools', 'PixelRestore - WebP to PNG',         '/tools/webp-to-png-converter/',        "\xF0\x9F\x9F\xA6" ],
			[ 'Image & Media Conversion Tools', 'ClipShift — Video Converter',        '/tools/video-converter/',              "\xF0\x9F\x8E\xAC" ],
			[ 'Image & Media Conversion Tools', 'PNG to PDF',                         '/tools/png-to-pdf-converter/',         "\xF0\x9F\x93\x84" ],

			[ 'Case Conversion Tools', 'Case Converter',        '/tools/case-converter/',            "\xF0\x9F\x94\xA4" ],
			[ 'Case Conversion Tools', 'UPPERCASE Converter',   '/tools/case-converter/',            "\xF0\x9F\x94\xA0" ],
			[ 'Case Conversion Tools', 'lowercase converter',   '/tools/case-converter/',            "\xF0\x9F\x94\xA1" ],
			[ 'Case Conversion Tools', 'Sentence Case Converter','/tools/sentence-case-converter/',  "\xF0\x9F\x93\x9D" ],
			[ 'Case Conversion Tools', 'Title Case Converter',  '/tools/title-case-converter/',      "\xF0\x9F\x93\xB0" ],
			[ 'Case Conversion Tools', 'Capitalized Case',      '/tools/case-converter/',            "\xF0\x9F\x85\xB0\xEF\xB8\x8F" ],
			[ 'Case Conversion Tools', 'Alternating Case',      '/tools/case-converter/',            "\xF0\x9F\x94\x80" ],
			[ 'Case Conversion Tools', 'Inverse Case',          '/tools/case-converter/',            "\xF0\x9F\x94\x81" ],

			[ 'Text Cleaning Tools', 'Character Remover',      '/tools/character-remover/',         "\xE2\x9C\x82\xEF\xB8\x8F" ],
			[ 'Text Cleaning Tools', 'Duplicate Line Remover',  '/tools/duplicate-line-remover/',    "\xF0\x9F\x93\x8B" ],
			[ 'Text Cleaning Tools', 'Duplicate Word Finder',   '/tools/duplicate-word-finder/',     "\xF0\x9F\x94\x8D" ],
			[ 'Text Cleaning Tools', 'Em Dash Remover',         '/tools/em-dash-remover/',           "\xE2\x9E\x96" ],
			[ 'Text Cleaning Tools', 'Remove Line Breaks',      '/tools/remove-line-breaks/',        "\xE2\x86\xA9\xEF\xB8\x8F" ],
			[ 'Text Cleaning Tools', 'Remove Text Formatting',  '/tools/remove-text-formatting/',    "\xF0\x9F\x97\x91\xEF\xB8\x8F" ],
			[ 'Text Cleaning Tools', 'Remove Underscores',      '/tools/remove-underscores/',        "\xE3\x80\xB0\xEF\xB8\x8F" ],
			[ 'Text Cleaning Tools', 'Whitespace Remover',      '/tools/whitespace-remover/',        "\xE2\xAC\x9C" ],
			[ 'Text Cleaning Tools', 'Plain Text Converter',    '/tools/plain-text-converter/',      "\xF0\x9F\x93\x84" ],

			[ 'Text Generators & Writing Tools', 'APA Format Generator',     '/tools/apa-format-generator/',    "\xF0\x9F\x93\x9A" ],
			[ 'Text Generators & Writing Tools', 'Invisible Text Generator', '/tools/invisible-text-generator/',"\xF0\x9F\x91\xBB" ],
			[ 'Text Generators & Writing Tools', 'Online Notepad',           '/tools/online-notepad/',          "\xF0\x9F\x93\x93" ],
			[ 'Text Generators & Writing Tools', 'Repeat Text Generator',    '/tools/repeat-text-generator/',   "\xF0\x9F\x94\x82" ],
			[ 'Text Generators & Writing Tools', 'Reverse Text Generator',   '/tools/reverse-text-generator/',  "\xE2\x86\x94\xEF\xB8\x8F" ],
			[ 'Text Generators & Writing Tools', 'Roman Numeral Dates',      '/tools/roman-numeral-dates/',     "\xF0\x9F\x8F\x9B\xEF\xB8\x8F" ],
			[ 'Text Generators & Writing Tools', 'Word Cloud Generator',     '/tools/word-cloud-generator/',    "\xE2\x98\x81\xEF\xB8\x8F" ],

			[ 'Random Generators', 'SpinPick — Choice Picker', '/tools/spinpick-choice-picker/',     "\xF0\x9F\x8E\xAF" ],
			[ 'Random Generators', 'DateForge — Date Generator','/tools/random-date-generator/',     "\xF0\x9F\x93\x85" ],
			[ 'Random Generators', 'IPForge — IP Generator',    '/tools/random-ip-generator/',       "\xF0\x9F\x8C\x90" ],
			[ 'Random Generators', 'LetterDraw — Letter Generator','/tools/random-letter-generator/',"\xF0\x9F\x94\xA0" ],
			[ 'Random Generators', 'MonthSpin — Month Generator','/tools/random-month-generator/',   "\xF0\x9F\x97\x93\xEF\xB8\x8F" ],
			[ 'Random Generators', 'NumForge — Number Generator','/tools/random-number-generator/',  "\xF0\x9F\x94\xA2" ],
			[ 'Random Generators', 'VaultKey — Password Generator','/tools/password-generator/',      "\xF0\x9F\x94\x90" ],
			[ 'Random Generators', 'UniqueForge — UUID Generator','/tools/uuid-generator/',           "\xF0\x9F\xAA\xAA" ],

			[ 'Text Translators & Counters', 'Find and Replace Text',    '/tools/find-and-replace-text/',      "\xF0\x9F\x94\x8E" ],
			[ 'Text Translators & Counters', 'NATO Phonetic Alphabet',   '/tools/nato-phonetic-alphabet/',     "\xF0\x9F\xAA\x96" ],
			[ 'Text Translators & Counters', 'Online Sentence Counter',  '/tools/online-sentence-counter/',    "\xF0\x9F\x94\xA2" ],
			[ 'Text Translators & Counters', 'Phonetic Spelling Tool',   '/tools/phonetic-spelling-tool/',     "\xF0\x9F\x94\x8A" ],
			[ 'Text Translators & Counters', 'Pig Latin Translator',     '/tools/pig-latin-translator/',       "\xF0\x9F\x90\xB7" ],
			[ 'Text Translators & Counters', 'Sort Words Alphabetically', '/tools/sort-words-alphabetically/', "\xF0\x9F\x94\xA4" ],
			[ 'Text Translators & Counters', 'Wingdings Translator',     '/tools/wingdings-translator/',       "\xE2\x9C\xA1\xEF\xB8\x8F" ],
			[ 'Text Translators & Counters', 'Word Frequency Counter',   '/tools/word-frequency-counter/',     "\xF0\x9F\x93\x8A" ],
		];

		$grouped = [];
		foreach ( $tools as $t ) {
			$grouped[ $t[0] ][] = [
				'name' => $t[1],
				'url'  => home_url( $t[2] ),
				'icon' => $t[3],
			];
		}
		return $grouped;
	}

	/**
	 * Get the nav menu ID assigned to the 'menu-1' location.
	 */
	private static function get_menu_id(): ?int {
		$locations = get_nav_menu_locations();
		$menu_id   = $locations['menu-1'] ?? 0;
		if ( ! $menu_id || ! wp_get_nav_menu_object( $menu_id ) ) {
			return null;
		}
		return $menu_id;
	}

	/**
	 * Build a hierarchical tree from a flat nav menu items array.
	 *
	 * @param int $menu_id Nav menu term ID.
	 * @return array<int, object> Root-level items with ->children.
	 */
	private static function get_menu_tree( int $menu_id ): array {
		$items = wp_get_nav_menu_items( $menu_id );
		if ( ! $items ) {
			return [];
		}

		$index = [];
		foreach ( $items as $item ) {
			$item->children = [];
			$index[ $item->ID ] = $item;
		}

		$tree = [];
		foreach ( $index as $id => $item ) {
			$parent = (int) $item->menu_item_parent;
			if ( $parent && isset( $index[ $parent ] ) ) {
				$index[ $parent ]->children[] = $index[ $id ];
			} else {
				$tree[] = $index[ $id ];
			}
		}

		return $tree;
	}

	/**
	 * Find the "All Tools" root item in the menu tree.
	 */
	private static function find_all_tools_item( array $tree ): ?object {
		foreach ( $tree as $item ) {
			if ( 'All Tools' === trim( $item->title ) ) {
				return $item;
			}
		}
		return null;
	}

	/**
	 * Maximum child tool links per category column.
	 */
	private const CHILD_LIMIT = 5;

	/**
	 * Generate the "View All" URL for a category title.
	 * Anchors into /free-online-text-tools/#{slug} by convention.
	 */
	private static function get_view_all_url( string $category ): string {
		return home_url( '/' );
	}

	/**
	 * Render the mega panel HTML from the WP nav menu tree.
	 */
	private static function render_panel(): string {
		$menu_id = self::get_menu_id();
		if ( ! $menu_id ) {
			return self::render_panel_fallback();
		}

		$tree      = self::get_menu_tree( $menu_id );
		$root_item = self::find_all_tools_item( $tree );
		if ( ! $root_item || empty( $root_item->children ) ) {
			return self::render_panel_fallback();
		}

		$html  = '<div id="mega-panel" class="tc-mega-panel mega-panel" aria-label="All Tools Menu">';
		$html .= '<div class="tc-mega-inner mega-panel__inner">';

		foreach ( $root_item->children as $col ) {
			$html .= '<div class="tc-mega-col mega-col">';
			$html .= '<div class="tc-mega-category mega-col__heading">' . esc_html( $col->title ) . '</div>';
			$html .= '<ul class="tc-mega-list mega-col__list">';
			if ( ! empty( $col->children ) ) {
				$shown = 0;
				foreach ( $col->children as $child ) {
					if ( $shown >= self::CHILD_LIMIT ) {
						break;
					}
					$shown++;
					$icon  = ! empty( $child->description ) ? $child->description : '';
					$html .= '<li>'
						   . '<a class="tc-mega-link mega-tool-link" href="' . esc_url( $child->url ) . '">'
						   . ( $icon ? '<span class="tc-mega-icon mega-tool-link__icon">' . $icon . '</span>' : '' )
						   . '<span class="tc-mega-label mega-tool-link__label">' . esc_html( $child->title ) . '</span>'
						   . '</a></li>';
				}
				if ( count( $col->children ) > self::CHILD_LIMIT ) {
					$view_url = self::get_view_all_url( $col->title );
					$html    .= '<li class="tc-mega-view-all">'
							. '<a class="tc-mega-view-all-link" href="' . esc_url( $view_url ) . '">'
							. 'View All →'
							. '</a></li>';
				}
			}
			$html .= '</ul></div>';
		}

		$html .= '</div></div>';
		return $html;
	}

	/**
	 * Fallback panel when no WP menu is set up yet — renders from seed data.
	 */
	private static function render_panel_fallback(): string {
		$grouped = self::get_grouped_tools();
		$html  = '<div id="mega-panel" class="tc-mega-panel mega-panel" aria-label="All Tools Menu">';
		$html .= '<div class="tc-mega-inner mega-panel__inner">';
		foreach ( $grouped as $cat => $tools ) {
			$html .= '<div class="tc-mega-col mega-col">';
			$html .= '<div class="tc-mega-category mega-col__heading">' . esc_html( $cat ) . '</div>';
			$html .= '<ul class="tc-mega-list mega-col__list">';
			$shown = 0;
			foreach ( $tools as $t ) {
				if ( $shown >= self::CHILD_LIMIT ) {
					break;
				}
				$shown++;
				$html .= '<li>'
				   . '<a class="tc-mega-link mega-tool-link" href="' . esc_url( $t['url'] ) . '">'
				   . '<span class="tc-mega-icon mega-tool-link__icon">' . $t['icon'] . '</span>'
				   . '<span class="tc-mega-label mega-tool-link__label">' . esc_html( $t['name'] ) . '</span>'
				   . '</a></li>';
			}
			if ( count( $tools ) > self::CHILD_LIMIT ) {
				$view_url = self::get_view_all_url( $cat );
				$html    .= '<li class="tc-mega-view-all">'
						. '<a class="tc-mega-view-all-link" href="' . esc_url( $view_url ) . '">'
						. 'View All →'
						. '</a></li>';
			}
			$html .= '</ul></div>';
		}
		$html .= '</div></div>';
		return $html;
	}

	/**
	 * Render the mega trigger <li> for appending to the nav.
	 */
	public static function render_nav_item(): string {
		$html  = '<li id="mega-trigger" class="tc-mega-trigger nav-item--mega menu-item menu-item-type-custom menu-item-object-custom">';
		$html .= '<button id="mega-btn" class="tc-mega-btn nav-link nav-link--mega" aria-haspopup="true" aria-expanded="false" aria-controls="mega-panel">';
		$html .= '<span class="tc-mega-btn-label">All Tools</span>';
		$html .= '<svg class="tc-mega-chevron mega-chevron" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M3 5l3 3 3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
		$html .= '</button>';
		$html .= self::render_panel();
		$html .= '</li>';
		return $html;
	}

	// ── WordPress filters ─────────────────────────────────────

	/**
	 * wp_nav_menu_objects — add tc-has-mega class to "All Tools" item.
	 * CSS hides this on desktop; on mobile it shows the native sub-menu.
	 */
	public static function mark_all_tools_item( array $items, \stdClass $args ): array {
		if ( 'menu-1' !== ( $args->theme_location ?? '' ) ) {
			return $items;
		}
		foreach ( $items as $item ) {
			if ( 'All Tools' === trim( $item->title ) && 0 === (int) $item->menu_item_parent ) {
				$item->classes[] = 'menu-item';
				$item->classes[] = 'tc-has-mega';
				$item->classes   = array_unique( $item->classes );
				break;
			}
		}
		return $items;
	}

	/**
	 * wp_nav_menu_items — append mega trigger <li> to menu-1.
	 */
	public static function add_items_filter( string $items, \stdClass $args ): string {
		if ( 'menu-1' !== $args->theme_location ) {
			return $items;
		}
		return $items . self::render_nav_item();
	}

	// ── Shortcode ─────────────────────────────────────────────

	public static function shortcode(): string {
		$html  = '<nav class="tc-mega-menu" aria-label="All Tools" style="position:relative;z-index:999;">';
		$html .= '<div id="mega-trigger" class="tc-mega-trigger nav-item--mega" style="display:inline-block;">';
		$html .= '<button id="mega-btn" class="tc-mega-btn nav-link nav-link--mega" aria-haspopup="true" aria-expanded="false" aria-controls="mega-panel">';
		$html .= '<span class="tc-mega-btn-label">All Tools</span>';
		$html .= '<svg class="tc-mega-chevron mega-chevron" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M3 5l3 3 3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
		$html .= '</button>';
		$html .= self::render_panel();
		$html .= '</div></nav>';
		return $html;
	}

	// ── WP Nav Menu Sync ──────────────────────────────────────

	/**
	 * Create or rebuild the "TextCraft Tools" nav menu from seed data.
	 *
	 * WARNING: This deletes any existing menu with this name and
	 * recreates it. Manual edits in Appearance → Menus will be lost.
	 *
	 * @return array{success:bool,menu_id?:int,message:string}
	 */
	public static function sync_menu(): array {
		$menu_name = 'TextCraft Tools';

		// Delete existing menu with this name
		$existing = wp_get_nav_menu_object( $menu_name );
		if ( $existing ) {
			wp_delete_nav_menu( $existing->term_id );
		}

		$menu_id = wp_create_nav_menu( $menu_name );
		if ( is_wp_error( $menu_id ) ) {
			return [ 'success' => false, 'message' => $menu_id->get_error_message() ];
		}

		// Create "All Tools" top-level item
		$root_id = wp_update_nav_menu_item( $menu_id, 0, [
			'menu-item-title'     => 'All Tools',
			'menu-item-url'       => home_url( '/tools/' ),
			'menu-item-type'      => 'custom',
			'menu-item-object'    => 'custom',
			'menu-item-status'    => 'publish',
			'menu-item-classes'   => 'tc-has-mega',
		] );

		$grouped   = self::get_grouped_tools();
		$created   = 1; // root
		$tool_slugs = [];

		foreach ( $grouped as $category => $tools ) {
			$cat_id = wp_update_nav_menu_item( $menu_id, 0, [
				'menu-item-title'     => $category,
				'menu-item-url'       => '#',
				'menu-item-type'      => 'custom',
				'menu-item-object'    => 'custom',
				'menu-item-status'    => 'publish',
				'menu-item-parent-id' => $root_id,
			] );
			$created++;

			foreach ( $tools as $t ) {
				$slug  = trim( parse_url( $t['url'], PHP_URL_PATH ), '/' );
				$icon  = $t['icon'];

				$item_args = [
					'menu-item-title'       => $t['name'],
					'menu-item-url'         => $t['url'],
					'menu-item-status'      => 'publish',
					'menu-item-parent-id'   => $cat_id,
					'menu-item-description' => $icon,
				];

				// Try to link to actual page
				if ( ! isset( $tool_slugs[ $slug ] ) ) {
					$page = get_page_by_path( $slug, OBJECT, 'page' );
					$tool_slugs[ $slug ] = $page ? $page->ID : 0;
				}

				if ( $tool_slugs[ $slug ] ) {
					$item_args['menu-item-type']      = 'post_type';
					$item_args['menu-item-object']    = 'page';
					$item_args['menu-item-object-id'] = $tool_slugs[ $slug ];
					unset( $item_args['menu-item-url'] );
				} else {
					$item_args['menu-item-type']   = 'custom';
					$item_args['menu-item-object'] = 'custom';
				}

				wp_update_nav_menu_item( $menu_id, 0, $item_args );
				$created++;
			}
		}

		// Assign to menu-1 location
		$locations = get_theme_mod( 'nav_menu_locations', [] );
		$locations['menu-1'] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );

		update_option( 'textcraft_menu_synced', true );

		return [
			'success'  => true,
			'menu_id'  => $menu_id,
			'message'  => sprintf(
				/* translators: %d: number of menu items created */
				__( 'TextCraft Tools menu created with %d items and assigned to menu-1.', 'textcraft-tools' ),
				$created
			),
		];
	}
}
