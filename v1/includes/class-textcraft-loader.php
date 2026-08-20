<?php
/**
 * TextCraft_Loader — bootstraps all Elementor widgets and shared assets.
 *
 * @package TextCraft_Tools
 */

declare( strict_types = 1 );

namespace TextCraft_Tools;

// ── Direct access guard ───────────────────────────────────────
defined( 'ABSPATH' ) || exit;

/**
 * Singleton loader.  Wires up:
 *  - A custom Elementor widget category ("TextCraft Tools")
 *  - Shared front-end CSS / JS assets
 *  - All individual widget classes
 */
final class TextCraft_Loader {

	// ── Singleton ─────────────────────────────────────────────

	/** @var self|null Sole instance. */
	private static ?self $instance = null;

	/** Return / create the singleton. */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/** Private constructor — use instance(). */
	private function __construct() {
		$this->hook();
	}

	// ── Hooks ─────────────────────────────────────────────────

	/** Register all WordPress / Elementor hooks. */
	private function hook(): void {
		// Fix Hello Elementor theme: get_pages() with hierarchical=1 filters out
		// child pages (post_parent != 0) via get_page_children(0, ...), causing
		// get_elementor_page() to return null on every call and creating infinite
		// "Hello Theme #N" draft pages. See admin-config.php:ensure_elementor_page_exists().
		add_filter(
			'get_pages',
			static function ( array $pages, array $parsed_args ): array {
				if ( ! empty( $parsed_args['meta_key'] ) && '_elementor_edit_mode' === $parsed_args['meta_key'] && empty( $pages ) ) {
					$parsed_args['hierarchical'] = false;
					return get_pages( $parsed_args );
				}
				return $pages;
			},
			10,
			2
		);

		// Register custom widget category before widgets are registered.
		add_action( 'elementor/elements/categories_registered', [ $this, 'register_category' ] );

		// Register widgets after Elementor's widget manager is ready.
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );

		// Enqueue shared front-end assets (CSS + JS) for widget pages.
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );

		// Enqueue shared assets inside the Elementor editor preview iframe.
		add_action( 'elementor/preview/enqueue_styles', [ $this, 'enqueue_frontend_assets' ] );

		// Register PDF conversion API routes.
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );

		// ── SEO meta fallbacks (only when Rank Math is not active) ─
		add_action( 'wp_head', [ $this, 'output_seo_meta_fallbacks' ], 1 );

		// Site-wide JSON-LD schema (WebSite + Organization).
		add_action( 'wp_head', [ $this, 'output_site_schema' ], 0 );
	}

	// ── Category ──────────────────────────────────────────────

	/**
	 * Add a custom "TextCraft Tools" category to the Elementor panel.
	 *
	 * @param \Elementor\Elements_Manager $manager Elementor elements manager.
	 */
	public function register_category( \Elementor\Elements_Manager $manager ): void {
		$manager->add_category(
			'textcraft-tools',
			[
				'title' => esc_html__( 'TextCraft Tools', 'textcraft-tools' ),
				'icon'  => 'eicon-text',
			]
		);
	}

	// ── Widget registration ───────────────────────────────────

	/**
	 * Load widget files and register each widget with Elementor.
	 *
	 * @param \Elementor\Widgets_Manager $manager Elementor widgets manager.
	 */
	public function register_widgets( \Elementor\Widgets_Manager $manager ): void {

		// Load the abstract base widget first.
		require_once TEXTCRAFT_PLUGIN_DIR . 'includes/widgets/class-textcraft-base-widget.php';

		/** Map of widget filename slug → FQCN */
		$widgets = [
			// ── Case Converters ─────────────────────────────────
			'widget-case-converter'        => Widgets\Widget_Case_Converter::class,
			'widget-sentence-case'         => Widgets\Widget_Sentence_Case::class,
			'widget-title-case'            => Widgets\Widget_Title_Case::class,

			// ── Text Cleaners ───────────────────────────────────
			'widget-character-remover'     => Widgets\Widget_Character_Remover::class,
			'widget-duplicate-line'        => Widgets\Widget_Duplicate_Line::class,
			'widget-duplicate-word'        => Widgets\Widget_Duplicate_Word::class,
			'widget-em-dash-remover'       => Widgets\Widget_Em_Dash_Remover::class,
			'widget-remove-line-breaks'    => Widgets\Widget_Remove_Line_Breaks::class,
			'widget-remove-formatting'     => Widgets\Widget_Remove_Formatting::class,
			'widget-remove-underscores'    => Widgets\Widget_Remove_Underscores::class,
			'widget-whitespace-remover'    => Widgets\Widget_Whitespace_Remover::class,
			'widget-plain-text'            => Widgets\Widget_Plain_Text::class,

			// ── Text Generators ─────────────────────────────────
			'widget-apa-format'            => Widgets\Widget_Apa_Format::class,
			'widget-invisible-text'        => Widgets\Widget_Invisible_Text::class,
			'widget-online-notepad'        => Widgets\Widget_Online_Notepad::class,
			'widget-repeat-text'           => Widgets\Widget_Repeat_Text::class,
			'widget-reverse-text'          => Widgets\Widget_Reverse_Text::class,
			'widget-roman-numeral'         => Widgets\Widget_Roman_Numeral::class,
			'widget-word-cloud'            => Widgets\Widget_Word_Cloud::class,

			// ── Random Generators ───────────────────────────────
			'widget-random-choice'         => Widgets\Widget_Random_Choice::class,
			'widget-random-date'           => Widgets\Widget_Random_Date::class,
			'widget-random-ip'             => Widgets\Widget_Random_Ip::class,
			'widget-random-letter'         => Widgets\Widget_Random_Letter::class,
			'widget-random-month'          => Widgets\Widget_Random_Month::class,
			'widget-random-number'         => Widgets\Widget_Random_Number::class,
			'widget-password-generator'    => Widgets\Widget_Password_Generator::class,
			'widget-uuid-generator'        => Widgets\Widget_Uuid_Generator::class,

			// ── Translators & Counters ──────────────────────────
			'widget-find-replace'          => Widgets\Widget_Find_Replace::class,
			'widget-nato-phonetic'         => Widgets\Widget_Nato_Phonetic::class,
			'widget-sentence-counter'      => Widgets\Widget_Sentence_Counter::class,
			'widget-phonetic-spelling'     => Widgets\Widget_Phonetic_Spelling::class,
			'widget-pig-latin'             => Widgets\Widget_Pig_Latin::class,
			'widget-sort-words'            => Widgets\Widget_Sort_Words::class,
			'widget-wingdings'             => Widgets\Widget_Wingdings::class,
			'widget-word-frequency'        => Widgets\Widget_Word_Frequency::class,

			// Image Compression
			'widget-jpg-compressor'        => Widgets\Widget_Jpg_Compressor::class,
			'widget-png-compressor'        => Widgets\Widget_Png_Compressor::class,
			'widget-webp-compressor'       => Widgets\Widget_Webp_Compressor::class,
			'widget-gif-compressor'        => Widgets\Widget_Gif_Compressor::class,
			'widget-svg-compressor'        => Widgets\Widget_Svg_Compressor::class,

			// ── Layout / Sections ───────────────────────────────
			'widget-all-tools-page'        => Widgets\Widget_All_Tools_Page::class,
			'widget-features-section'      => Widgets\Widget_Features_Section::class,
			'widget-seo-cases-section'     => Widgets\Widget_Seo_Cases_Section::class,
			'widget-tools-grid-section'    => Widgets\Widget_Tools_Grid_Section::class,

			// ── Image & Media ───────────────────────────────────
			'widget-ascii-art'             => Widgets\Widget_Ascii_Art::class,
			'widget-image-to-text'         => Widgets\Widget_Image_To_Text::class,
			'widget-remove-background'     => Widgets\Widget_Remove_Background::class,
			'widget-jpg-to-png'            => Widgets\Widget_Jpg_To_Png::class,
			'widget-jpg-to-webp'           => Widgets\Widget_Jpg_To_Webp::class,
			'widget-jpg-to-svg'            => Widgets\Widget_Jpg_To_Svg::class,
			'widget-jpg-to-gif'            => Widgets\Widget_Jpg_To_Gif::class,
			'widget-jpg-to-heic'           => Widgets\Widget_Jpg_To_Heic::class,
			'widget-jpg-to-avif'           => Widgets\Widget_Jpg_To_Avif::class,
			'widget-png-to-jpg'            => Widgets\Widget_Png_To_Jpg::class,
			'widget-png-to-webp'           => Widgets\Widget_Png_To_Webp::class,
			'widget-png-to-svg'            => Widgets\Widget_Png_To_Svg::class,
			'widget-png-to-heic'           => Widgets\Widget_Png_To_Heic::class,
			'widget-heic-to-jpg'           => Widgets\Widget_Heic_To_Jpg::class,
			'widget-heic-to-png'           => Widgets\Widget_Heic_To_Png::class,
			'widget-heic-to-svg'           => Widgets\Widget_Heic_To_Svg::class,
			'widget-webp-to-jpg'           => Widgets\Widget_Webp_To_Jpg::class,
			'widget-webp-to-png'           => Widgets\Widget_Webp_To_Png::class,
			'widget-jpg-to-pdf'            => Widgets\Widget_Jpg_To_Pdf::class,
			'widget-png-to-pdf'            => Widgets\Widget_Png_To_Pdf::class,
			'widget-video-converter'       => Widgets\Widget_Video_Converter::class,

			// PDF Tools
			'widget-pdf-compressor'        => Widgets\Widget_Pdf_Compressor::class,
			'widget-pdf-merger'            => Widgets\Widget_Pdf_Merger::class,
			'widget-pdf-splitter'          => Widgets\Widget_Pdf_Splitter::class,
			'widget-pdf-to-jpg'            => Widgets\Widget_Pdf_To_Jpg::class,
			'widget-pdf-to-png'            => Widgets\Widget_Pdf_To_Png::class,
			'widget-rotate-pdf'            => Widgets\Widget_Rotate_Pdf::class,
			'widget-delete-pdf-pages'      => Widgets\Widget_Delete_Pdf_Pages::class,
			'widget-pdf-to-word'           => Widgets\Widget_Pdf_To_Word::class,
		];

		foreach ( $widgets as $file_slug => $class ) {
			$file = TEXTCRAFT_PLUGIN_DIR . "includes/widgets/{$file_slug}.php";
			if ( file_exists( $file ) ) {
				require_once $file;
				$manager->register( new $class() );
			}
		}
	}

	// ── Assets ────────────────────────────────────────────────

	/**
	 * Enqueue shared front-end CSS and JS used by all widgets.
	 * Individual widgets may enqueue their own inline scripts.
	 */
	public function enqueue_frontend_assets(): void {
		// Never load frontend assets in wp-admin (defense-in-depth).
		if ( is_admin() ) {
			return;
		}

		// ── Shared design-system CSS (loads after Elementor to prevent theme style bleed) ─
		wp_enqueue_style(
			'textcraft-tools-style',
			TEXTCRAFT_PLUGIN_URL . 'assets/css/textcraft-tools.css',
			[ 'elementor-frontend' ],
			TEXTCRAFT_VERSION
		);

		// ── Mega-menu CSS (only when header is rendered by WP theme) ─
		// The WordPress theme controls the header; we provide just the
		// mega-menu CSS so a theme using wp_nav_menu() can integrate it.
		wp_enqueue_style(
			'textcraft-megamenu',
			TEXTCRAFT_PLUGIN_URL . 'assets/css/textcraft-megamenu.css',
			[],
			TEXTCRAFT_VERSION
		);

		// ── Shared case-conversion JS library ────────────────
		wp_enqueue_script(
			'textcraft-case-converter',
			TEXTCRAFT_PLUGIN_URL . 'assets/js/textcraft-case-converter.js',
			[],
			TEXTCRAFT_VERSION,
			[ 'strategy' => 'defer', 'in_footer' => true ]
		);

		// ── Mega-menu JS ──────────────────────────────────────
		wp_enqueue_script(
			'textcraft-megamenu',
			TEXTCRAFT_PLUGIN_URL . 'assets/js/textcraft-megamenu.js',
			[],
			TEXTCRAFT_VERSION,
			[ 'strategy' => 'defer', 'in_footer' => true ]
		);

		// ── FAQ accordion JS ──────────────────────────────────
		wp_enqueue_script(
			'textcraft-faq-accordion',
			TEXTCRAFT_PLUGIN_URL . 'assets/js/textcraft-faq-accordion.js',
			[],
			TEXTCRAFT_VERSION,
			[ 'strategy' => 'defer', 'in_footer' => true ]
		);
	}

	// ── REST API ───────────────────────────────────────────────

	/** Register front-end tool API routes. */
	public function register_rest_routes(): void {
		register_rest_route(
			'textcraft-tools/v1',
			'/pdf-to-word',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'convert_pdf_to_word' ],
				'permission_callback' => [ $this, 'verify_rest_nonce' ],
			]
		);
	}

	/**
	 * Verify the front-end REST nonce.
	 *
	 * @param \WP_REST_Request $request REST request.
	 */
	public function verify_rest_nonce( \WP_REST_Request $request ): bool {
		$nonce = (string) $request->get_header( 'x_wp_nonce' );
		return (bool) wp_verify_nonce( $nonce, 'wp_rest' );
	}

	/**
	 * Convert an uploaded PDF to DOCX through a server-side converter.
	 *
	 * This method is intentionally filterable so the project can later point to
	 * a private PDF conversion API while keeping the same front-end tool.
	 *
	 * @param \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function convert_pdf_to_word( \WP_REST_Request $request ) {
		$files = $request->get_file_params();
		$file  = $files['pdf'] ?? null;

		if ( empty( $file ) || ! empty( $file['error'] ) || empty( $file['tmp_name'] ) ) {
			return new \WP_Error( 'textcraft_pdf_missing', __( 'Please upload a valid PDF file.', 'textcraft-tools' ), [ 'status' => 400 ] );
		}

		if ( (int) $file['size'] > 25 * 1024 * 1024 ) {
			return new \WP_Error( 'textcraft_pdf_too_large', __( 'PDF file is too large. Please upload a file under 25 MB.', 'textcraft-tools' ), [ 'status' => 413 ] );
		}

		$check = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], [ 'pdf' => 'application/pdf' ] );
		if ( 'pdf' !== ( $check['ext'] ?? '' ) ) {
			return new \WP_Error( 'textcraft_pdf_invalid', __( 'Only PDF files can be converted.', 'textcraft-tools' ), [ 'status' => 400 ] );
		}

		$upload_dir = wp_upload_dir();
		$work_dir   = trailingslashit( $upload_dir['basedir'] ) . 'textcraft-pdf-to-word/' . wp_generate_uuid4();
		if ( ! wp_mkdir_p( $work_dir ) ) {
			return new \WP_Error( 'textcraft_pdf_workdir', __( 'Could not prepare the conversion workspace.', 'textcraft-tools' ), [ 'status' => 500 ] );
		}

		$input_path  = trailingslashit( $work_dir ) . 'source.pdf';
		$output_path = trailingslashit( $work_dir ) . 'source.docx';

		if ( ! move_uploaded_file( $file['tmp_name'], $input_path ) ) {
			$this->cleanup_directory( $work_dir );
			return new \WP_Error( 'textcraft_pdf_upload', __( 'Could not store the uploaded PDF.', 'textcraft-tools' ), [ 'status' => 500 ] );
		}

		/**
		 * Custom converter hook for future private APIs.
		 *
		 * Return a DOCX file path or WP_Error. Return null to use LibreOffice.
		 *
		 * @param string $input_path  Uploaded PDF path.
		 * @param string $output_path Expected DOCX output path.
		 * @param array  $file        Original upload metadata.
		 */
		$custom_result = apply_filters( 'textcraft_pdf_to_word_converter', null, $input_path, $output_path, $file );
		if ( $custom_result instanceof \WP_Error ) {
			$this->cleanup_directory( $work_dir );
			return $custom_result;
		}

		if ( is_string( $custom_result ) && file_exists( $custom_result ) ) {
			$output_path = $custom_result;
		} else {
			$converted = $this->convert_pdf_with_libreoffice( $input_path, $work_dir );
			if ( $converted instanceof \WP_Error ) {
				$this->cleanup_directory( $work_dir );
				return $converted;
			}
			$output_path = $converted;
		}

		if ( ! file_exists( $output_path ) ) {
			$this->cleanup_directory( $work_dir );
			return new \WP_Error( 'textcraft_pdf_no_output', __( 'The converter did not create a Word file.', 'textcraft-tools' ), [ 'status' => 500 ] );
		}

		$docx_data = file_get_contents( $output_path );
		$filename  = sanitize_file_name( preg_replace( '/\.pdf$/i', '.docx', (string) $file['name'] ) );
		$response  = [
			'filename' => $filename ?: 'converted.docx',
			'mime'     => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'bytes'    => filesize( $output_path ),
			'content'  => base64_encode( false === $docx_data ? '' : $docx_data ),
		];

		$this->cleanup_directory( $work_dir );

		return rest_ensure_response( $response );
	}

	/**
	 * Convert PDF to DOCX with LibreOffice/soffice.
	 *
	 * @param string $input_path PDF path.
	 * @param string $output_dir Output directory.
	 * @return string|\WP_Error
	 */
	private function convert_pdf_with_libreoffice( string $input_path, string $output_dir ) {
		$soffice = $this->find_soffice_binary();
		if ( '' === $soffice ) {
			return new \WP_Error(
				'textcraft_pdf_converter_missing',
				__( 'PDF to Word conversion engine is not installed. Install LibreOffice on the server, or connect your custom API using the textcraft_pdf_to_word_converter filter.', 'textcraft-tools' ),
				[ 'status' => 501 ]
			);
		}

		$command = sprintf(
			'%s --headless --convert-to docx --outdir %s %s',
			escapeshellarg( $soffice ),
			escapeshellarg( $output_dir ),
			escapeshellarg( $input_path )
		);

		$result = $this->run_process( $command, 120 );
		if ( 0 !== $result['code'] ) {
			return new \WP_Error( 'textcraft_pdf_converter_failed', __( 'The PDF conversion engine failed to convert this file.', 'textcraft-tools' ), [ 'status' => 500 ] );
		}

		$output = trailingslashit( $output_dir ) . 'source.docx';
		return file_exists( $output ) ? $output : new \WP_Error( 'textcraft_pdf_converter_output', __( 'The PDF conversion engine finished without producing a DOCX file.', 'textcraft-tools' ), [ 'status' => 500 ] );
	}

	/** Find a LibreOffice/soffice executable. */
	private function find_soffice_binary(): string {
		$candidates = apply_filters(
			'textcraft_pdf_to_word_soffice_paths',
			[
				'soffice',
				'libreoffice',
				'C:\Program Files\LibreOffice\program\soffice.exe',
				'C:\Program Files (x86)\LibreOffice\program\soffice.exe',
				'/usr/bin/soffice',
				'/usr/bin/libreoffice',
				'/snap/bin/libreoffice',
			]
		);

		foreach ( $candidates as $candidate ) {
			if ( str_contains( (string) $candidate, DIRECTORY_SEPARATOR ) && is_executable( (string) $candidate ) ) {
				return (string) $candidate;
			}

			if ( ! str_contains( (string) $candidate, DIRECTORY_SEPARATOR ) ) {
				return (string) $candidate;
			}
		}

		return '';
	}

	/**
	 * Run a shell process with timeout.
	 *
	 * @param string $command Command to run.
	 * @param int    $timeout Timeout in seconds.
	 * @return array{code:int,stdout:string,stderr:string}
	 */
	private function run_process( string $command, int $timeout ): array {
		$descriptors = [
			1 => [ 'pipe', 'w' ],
			2 => [ 'pipe', 'w' ],
		];
		$process     = proc_open( $command, $descriptors, $pipes );
		if ( ! is_resource( $process ) ) {
			return [ 'code' => 1, 'stdout' => '', 'stderr' => 'Could not start process.' ];
		}

		$start = time();
		do {
			$status = proc_get_status( $process );
			if ( ! $status['running'] ) {
				break;
			}
			if ( time() - $start > $timeout ) {
				proc_terminate( $process );
				break;
			}
			usleep( 100000 );
		} while ( true );

		$stdout = stream_get_contents( $pipes[1] );
		$stderr = stream_get_contents( $pipes[2] );
		fclose( $pipes[1] );
		fclose( $pipes[2] );

		$code = proc_close( $process );
		return [ 'code' => is_int( $code ) ? $code : 1, 'stdout' => (string) $stdout, 'stderr' => (string) $stderr ];
	}

	// ── SEO Meta Fallbacks ──────────────────────────────────────

	/**
	 * Output OG/Twitter/canonical/robots fallback meta in <head>
	 * only when Rank Math is NOT active, to avoid duplicates.
	 */
	public function output_seo_meta_fallbacks(): void {
		if ( defined( 'RANK_MATH_VERSION' ) || class_exists( '\\RankMath\\Helper' ) ) {
			return;
		}

		if ( ! is_singular() ) {
			return;
		}

		$post = get_queried_object();
		if ( ! $post instanceof \WP_Post ) {
			return;
		}

		$title   = wp_get_document_title();
		$url     = get_permalink( $post );
		$excerpt = get_the_excerpt( $post );
		$image   = get_the_post_thumbnail_url( $post, 'full' ) ?: '';

		// OG
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
		echo '<meta property="og:url" content="' . esc_url( $url ) . '" />' . "\n";
		echo '<meta property="og:type" content="website" />' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( wp_trim_words( $excerpt ?: $title, 30 ) ) . '" />' . "\n";
		echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '" />' . "\n";
		if ( $image ) {
			echo '<meta property="og:image" content="' . esc_url( $image ) . '" />' . "\n";
		}

		// Twitter Card
		echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
		echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '" />' . "\n";
		echo '<meta name="twitter:description" content="' . esc_attr( wp_trim_words( $excerpt ?: $title, 30 ) ) . '" />' . "\n";
		if ( $image ) {
			echo '<meta name="twitter:image" content="' . esc_url( $image ) . '" />' . "\n";
		}

		// Canonical
		echo '<link rel="canonical" href="' . esc_url( $url ) . '" />' . "\n";

		// Robots (index, follow default)
		echo '<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />' . "\n";
	}

	/**
	 * Output site-wide JSON-LD schema (WebSite + Organization).
	 * Hooked early and checks Rank Math.
	 */
	public function output_site_schema(): void {
		if ( defined( 'RANK_MATH_VERSION' ) || class_exists( '\\RankMath\\Helper' ) ) {
			return;
		}

		$site_name   = get_bloginfo( 'name' );
		$site_url    = get_site_url();
		$description = get_bloginfo( 'description' );
		$logo        = '';
		$custom_logo = get_theme_mod( 'custom_logo' );
		if ( $custom_logo ) {
			$logo_data = wp_get_attachment_image_src( $custom_logo, 'full' );
			if ( $logo_data ) {
				$logo = $logo_data[0];
			}
		}

		$schema = [
			'@context' => 'https://schema.org',
			'@graph'   => [
				[
					'@type'        => 'WebSite',
					'@id'          => $site_url . '/#website',
					'url'          => $site_url,
					'name'         => $site_name,
					'description'  => $description,
					'potentialAction' => [
						[
							'@type'       => 'SearchAction',
							'target'      => $site_url . '/?s={search_term_string}',
							'query-input' => 'required name=search_term_string',
						],
					],
				],
				[
					'@type' => 'Organization',
					'@id'   => $site_url . '/#organization',
					'name'  => $site_name,
					'url'   => $site_url,
				],
			],
		];

		if ( $logo ) {
			$schema['@graph'][1]['logo'] = $logo;
		}

		$encoded = wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		if ( false === $encoded ) {
			return;
		}
		echo '<script type="application/ld+json">' . "\n";
		echo $encoded . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON already safe
		echo '</script>' . "\n";
	}

	/** Remove generated temporary files. */
	private function cleanup_directory( string $dir ): void {
		if ( ! is_dir( $dir ) ) {
			return;
		}

		$files = glob( trailingslashit( $dir ) . '*' );
		if ( is_array( $files ) ) {
			foreach ( $files as $file ) {
				if ( is_file( $file ) ) {
					wp_delete_file( $file );
				}
			}
		}

		rmdir( $dir );
	}
}
