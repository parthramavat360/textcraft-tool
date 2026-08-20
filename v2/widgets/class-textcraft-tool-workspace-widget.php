<?php
/**
 * Tool Workspace Elementor Widget
 *
 * Renders the interactive workspace area for all 78 tool pages.
 * Sits between the Tool Hero and Tool Content widgets.
 *
 * @package TextCraftToolsPro
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class TextCraft_Tool_Workspace_Widget extends \Elementor\Widget_Base {

    public function get_name()      { return 'tctp_tool_workspace'; }
    public function get_title()     { return __( 'Tool Workspace', 'textcrafttoolspro' ); }
    public function get_icon()      { return 'eicon-settings'; }
    public function get_categories() { return [ 'textcrafttools' ]; }

    protected function register_controls() {

        $this->start_controls_section( 'workspace', [
            'label' => __( 'Workspace', 'textcrafttoolspro' ),
        ] );

        $this->add_control( 'tool_slug', [
            'label'       => __( 'Tool Slug', 'textcrafttoolspro' ),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'description' => __( 'e.g. pdf-compressor, case-converter. Auto-detected from URL if empty.', 'textcrafttoolspro' ),
            'default'     => '',
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'style', [
            'label' => __( 'Style', 'textcrafttoolspro' ),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'accent_color', [
            'label'     => __( 'Accent Color', 'textcrafttoolspro' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#2563eb',
            'selectors' => [
                '{{SELECTOR}} .tctp-ws .tctp-btn-accent'   => 'background: {{VALUE}}',
                '{{SELECTOR}} .tctp-ws .tctp-drop:hover'   => 'border-color: {{VALUE}}',
                '{{SELECTOR}} .tctp-ws .tctp-drop.hot'     => 'border-color: {{VALUE}}',
            ],
        ] );

        $this->end_controls_section();
    }

    /* ------------------------------------------------------------------ */
    /*  Tool Map — 78 tools                                               */
    /* ------------------------------------------------------------------ */

    private static function get_tool_map(): array {
        return [

            /* ── TEXT TOOLS ────────────────────────────────────────── */

            'case-converter' => [
                'type'     => 'text',
                'title'    => 'Case Converter',
                'hint'     => 'Convert text between uppercase, lowercase, sentence case, title case, and more.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => 'Aa',
            ],
            'sentence-case' => [
                'type'     => 'text',
                'title'    => 'Sentence Case Converter',
                'hint'     => 'Capitalize only the first letter of each sentence automatically.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => 'Aa',
            ],
            'title-case' => [
                'type'     => 'text',
                'title'    => 'Title Case Converter',
                'hint'     => 'Convert text to proper title case following major style guides.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => 'Aa',
            ],
            'find-replace' => [
                'type'     => 'text',
                'title'    => 'Find and Replace',
                'hint'     => 'Search for text and replace it with something else instantly.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => 'Fr',
            ],
            'character-remover' => [
                'type'     => 'text',
                'title'    => 'Character Remover',
                'hint'     => 'Remove specific characters, letters, or symbols from your text.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '✕',
            ],
            'reverse-text' => [
                'type'     => 'text',
                'title'    => 'Reverse Text Generator',
                'hint'     => 'Reverse the order of characters, words, or lines in your text.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '⟲',
            ],
            'sort-words' => [
                'type'     => 'text',
                'title'    => 'Sort Words Online',
                'hint'     => 'Sort words alphabetically, by length, or reverse order.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => 'Az',
            ],
            'repeat-text' => [
                'type'     => 'text',
                'title'    => 'Repeat Text Generator',
                'hint'     => 'Repeat any text or phrase a specified number of times.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '×',
            ],
            'remove-line-breaks' => [
                'type'     => 'text',
                'title'    => 'Remove Line Breaks',
                'hint'     => 'Strip unwanted line breaks and join paragraphs cleanly.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '↵',
            ],
            'remove-formatting' => [
                'type'     => 'text',
                'title'    => 'Remove Formatting',
                'hint'     => 'Strip HTML tags, bold, italic, and other formatting from text.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => 'T',
            ],
            'remove-underscores' => [
                'type'     => 'text',
                'title'    => 'Remove Underscores',
                'hint'     => 'Replace or remove underscores from file names and text.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '_',
            ],
            'whitespace-remover' => [
                'type'     => 'text',
                'title'    => 'Whitespace Remover',
                'hint'     => 'Remove extra spaces, tabs, and leading/trailing whitespace.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '⬚',
            ],
            'plain-text' => [
                'type'     => 'text',
                'title'    => 'Convert to Plain Text',
                'hint'     => 'Convert rich or formatted text to clean plain text.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => 'T',
            ],
            'duplicate-line' => [
                'type'     => 'text',
                'title'    => 'Duplicate Line Remover',
                'hint'     => 'Find and remove duplicate lines from your text.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '≡',
            ],
            'duplicate-word' => [
                'type'     => 'text',
                'title'    => 'Duplicate Word Remover',
                'hint'     => 'Remove consecutively repeated words from text.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '≡',
            ],
            'em-dash-remover' => [
                'type'     => 'text',
                'title'    => 'Em Dash Remover',
                'hint'     => 'Replace em dashes with commas, spaces, or remove them.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '—',
            ],
            'word-frequency' => [
                'type'     => 'text',
                'title'    => 'Word Frequency Counter',
                'hint'     => 'Count how often each word appears in your text.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '#',
            ],
            'sentence-counter' => [
                'type'     => 'text',
                'title'    => 'Sentence Counter',
                'hint'     => 'Count sentences, words, characters, and paragraphs.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '#',
            ],
            'pig-latin' => [
                'type'     => 'text',
                'title'    => 'Pig Latin Translator',
                'hint'     => 'Convert English text to Pig Latin and back.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '🐷',
            ],
            'nato-phonetic' => [
                'type'     => 'text',
                'title'    => 'NATO Phonetic Converter',
                'hint'     => 'Convert text to the NATO phonetic alphabet.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '📻',
            ],
            'phonetic-spelling' => [
                'type'     => 'text',
                'title'    => 'Phonetic Spelling Converter',
                'hint'     => 'Show phonetic spelling for any English word.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '🔤',
            ],
            'wingdings' => [
                'type'     => 'text',
                'title'    => 'Wingdings Translator',
                'hint'     => 'Translate text to Wingdings symbols and back.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '.fromCharCode',
            ],
            'roman-numeral' => [
                'type'     => 'text',
                'title'    => 'Roman Numeral Converter',
                'hint'     => 'Convert between Roman numerals and standard numbers.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => 'Ⅻ',
            ],
            'word-cloud' => [
                'type'     => 'text',
                'title'    => 'Word Cloud Generator',
                'hint'     => 'Generate a visual word cloud from your text.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '☁',
            ],
            'online-notepad' => [
                'type'     => 'text',
                'title'    => 'Online Notepad',
                'hint'     => 'A quick, distraction-free notepad that saves locally.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '📝',
            ],
            'apa-format' => [
                'type'     => 'text',
                'title'    => 'APA Format Generator',
                'hint'     => 'Generate properly formatted APA references and citations.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => 'APA',
            ],
            'invisible-text' => [
                'type'     => 'text',
                'title'    => 'Invisible Text Generator',
                'hint'     => 'Generate invisible or blank Unicode characters.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => 'ᅠ',
            ],
            'ascii-art' => [
                'type'     => 'text',
                'title'    => 'ASCII Art Generator',
                'hint'     => 'Convert text to ASCII art using various fonts.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '∎',
            ],
            'trim-lines' => [
                'type'     => 'text',
                'title'    => 'Trim Lines',
                'hint'     => 'Remove leading and trailing whitespace from every line.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '⎀',
            ],
            'lorem-ipsum' => [
                'type'     => 'generator',
                'title'    => 'Lorem Ipsum Generator',
                'hint'     => 'Generate placeholder text in paragraphs, words, or sentences.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '¶',
            ],
            'markdown-to-html' => [
                'type'     => 'text',
                'title'    => 'Markdown to HTML',
                'hint'     => 'Convert Markdown syntax to clean HTML.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => 'MD',
            ],
            'json-formatter' => [
                'type'     => 'text',
                'title'    => 'JSON Formatter',
                'hint'     => 'Format, validate, and beautify JSON data.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '{}',
            ],
            'hash-generator' => [
                'type'     => 'text',
                'title'    => 'Hash Generator',
                'hint'     => 'Generate MD5, SHA-1, SHA-256, and SHA-512 hashes.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '#',
            ],
            'qr-code-generator' => [
                'type'     => 'generator',
                'title'    => 'QR Code Generator',
                'hint'     => 'Generate QR codes for URLs, text, emails, and Wi-Fi.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '⊞',
            ],

            /* ── FILE TOOLS — Image OCR & Effects ──────────────────── */

            'image-to-text' => [
                'type'     => 'file',
                'title'    => 'Image to Text Converter',
                'hint'     => 'Extract text from images using OCR.',
                'accept'   => 'image/*',
                'multiple' => false,
                'icon'     => 'OCR',
            ],
            'remove-background' => [
                'type'     => 'file',
                'title'    => 'Remove Background from Image',
                'hint'     => 'Automatically remove the background from any image.',
                'accept'   => 'image/*',
                'multiple' => false,
                'icon'     => 'BG',
            ],
            'image-resizer' => [
                'type'     => 'file',
                'title'    => 'Image Resizer',
                'hint'     => 'Resize images to any dimension with quality control.',
                'accept'   => 'image/*',
                'multiple' => false,
                'icon'     => '⬚',
            ],
            'svg-to-png' => [
                'type'     => 'file',
                'title'    => 'SVG to PNG Converter',
                'hint'     => 'Rasterize SVG vectors to high-resolution PNG images.',
                'accept'   => 'image/svg+xml,.svg',
                'multiple' => true,
                'icon'     => 'SVG',
            ],

            /* ── FILE TOOLS — PDF ──────────────────────────────────── */

            'pdf-compressor' => [
                'type'     => 'file',
                'title'    => 'PDF Compressor',
                'hint'     => 'Shrink PDFs by 30–80% while keeping text sharp and fonts intact.',
                'accept'   => 'application/pdf,.pdf',
                'multiple' => false,
                'icon'     => 'PDF',
            ],
            'pdf-merger' => [
                'type'     => 'file',
                'title'    => 'PDF Merger',
                'hint'     => 'Combine multiple PDFs into one document.',
                'accept'   => 'application/pdf,.pdf',
                'multiple' => true,
                'icon'     => 'PDF',
            ],
            'pdf-splitter' => [
                'type'     => 'file',
                'title'    => 'PDF Splitter',
                'hint'     => 'Split a PDF into separate pages or ranges.',
                'accept'   => 'application/pdf,.pdf',
                'multiple' => false,
                'icon'     => 'PDF',
            ],
            'pdf-to-jpg' => [
                'type'     => 'file',
                'title'    => 'PDF to JPG Converter',
                'hint'     => 'Convert each PDF page to a high-quality JPG image.',
                'accept'   => 'application/pdf,.pdf',
                'multiple' => false,
                'icon'     => 'PDF',
            ],
            'pdf-to-png' => [
                'type'     => 'file',
                'title'    => 'PDF to PNG Converter',
                'hint'     => 'Convert PDF pages to transparent PNG images.',
                'accept'   => 'application/pdf,.pdf',
                'multiple' => false,
                'icon'     => 'PDF',
            ],
            'pdf-to-word' => [
                'type'     => 'file',
                'title'    => 'PDF to Word Converter',
                'hint'     => 'Convert PDF documents to editable Word files.',
                'accept'   => 'application/pdf,.pdf',
                'multiple' => false,
                'icon'     => 'PDF',
            ],
            'rotate-pdf' => [
                'type'     => 'file',
                'title'    => 'Rotate PDF',
                'hint'     => 'Rotate all or selected PDF pages by 90°, 180°, or 270°.',
                'accept'   => 'application/pdf,.pdf',
                'multiple' => false,
                'icon'     => 'PDF',
            ],
            'delete-pdf-pages' => [
                'type'     => 'file',
                'title'    => 'Delete PDF Pages',
                'hint'     => 'Remove specific pages from a PDF document.',
                'accept'   => 'application/pdf,.pdf',
                'multiple' => false,
                'icon'     => 'PDF',
            ],
            'pdf-to-text' => [
                'type'     => 'file',
                'title'    => 'PDF to Text',
                'hint'     => 'Extract plain text content from PDF documents.',
                'accept'   => 'application/pdf,.pdf',
                'multiple' => false,
                'icon'     => 'PDF',
            ],

            /* ── FILE TOOLS — Image Convert ────────────────────────── */

            'jpg-to-png' => [
                'type'     => 'file',
                'title'    => 'JPG to PNG Converter',
                'hint'     => 'Convert JPG images to PNG with transparency support.',
                'accept'   => 'image/jpeg,image/jpg',
                'multiple' => true,
                'icon'     => 'JPG',
            ],
            'jpg-to-webp' => [
                'type'     => 'file',
                'title'    => 'JPG to WebP Converter',
                'hint'     => 'Convert JPG images to modern WebP format.',
                'accept'   => 'image/jpeg,image/jpg',
                'multiple' => true,
                'icon'     => 'JPG',
            ],
            'jpg-to-svg' => [
                'type'     => 'file',
                'title'    => 'JPG to SVG Converter',
                'hint'     => 'Trace JPG images and convert them to scalable SVG.',
                'accept'   => 'image/jpeg,image/jpg',
                'multiple' => false,
                'icon'     => 'JPG',
            ],
            'jpg-to-gif' => [
                'type'     => 'file',
                'title'    => 'JPG to GIF Converter',
                'hint'     => 'Convert JPG images to GIF format.',
                'accept'   => 'image/jpeg,image/jpg',
                'multiple' => false,
                'icon'     => 'JPG',
            ],
            'jpg-to-heic' => [
                'type'     => 'file',
                'title'    => 'JPG to HEIC Converter',
                'hint'     => 'Convert JPG images to Apple HEIC format.',
                'accept'   => 'image/jpeg,image/jpg',
                'multiple' => false,
                'icon'     => 'JPG',
            ],
            'jpg-to-avif' => [
                'type'     => 'file',
                'title'    => 'JPG to AVIF Converter',
                'hint'     => 'Convert JPG images to next-gen AVIF format.',
                'accept'   => 'image/jpeg,image/jpg',
                'multiple' => false,
                'icon'     => 'JPG',
            ],
            'jpg-to-pdf' => [
                'type'     => 'file',
                'title'    => 'JPG to PDF Converter',
                'hint'     => 'Combine JPG images into a single PDF document.',
                'accept'   => 'image/jpeg,image/jpg',
                'multiple' => true,
                'icon'     => 'JPG',
            ],
            'png-to-jpg' => [
                'type'     => 'file',
                'title'    => 'PNG to JPG Converter',
                'hint'     => 'Convert PNG images to smaller JPG files.',
                'accept'   => 'image/png',
                'multiple' => true,
                'icon'     => 'PNG',
            ],
            'png-to-webp' => [
                'type'     => 'file',
                'title'    => 'PNG to WebP Converter',
                'hint'     => 'Convert PNG images to efficient WebP format.',
                'accept'   => 'image/png',
                'multiple' => true,
                'icon'     => 'PNG',
            ],
            'png-to-svg' => [
                'type'     => 'file',
                'title'    => 'PNG to SVG Converter',
                'hint'     => 'Trace PNG images and convert them to scalable SVG.',
                'accept'   => 'image/png',
                'multiple' => false,
                'icon'     => 'PNG',
            ],
            'png-to-heic' => [
                'type'     => 'file',
                'title'    => 'PNG to HEIC Converter',
                'hint'     => 'Convert PNG images to Apple HEIC format.',
                'accept'   => 'image/png',
                'multiple' => false,
                'icon'     => 'PNG',
            ],
            'png-to-pdf' => [
                'type'     => 'file',
                'title'    => 'PNG to PDF Converter',
                'hint'     => 'Combine PNG images into a single PDF document.',
                'accept'   => 'image/png',
                'multiple' => true,
                'icon'     => 'PNG',
            ],
            'heic-to-jpg' => [
                'type'     => 'file',
                'title'    => 'HEIC to JPG Converter',
                'hint'     => 'Convert Apple HEIC images to universal JPG format.',
                'accept'   => 'image/heic,image/heif,.heic,.heif',
                'multiple' => true,
                'icon'     => 'HEIC',
            ],
            'heic-to-png' => [
                'type'     => 'file',
                'title'    => 'HEIC to PNG Converter',
                'hint'     => 'Convert HEIC images to PNG with lossless quality.',
                'accept'   => 'image/heic,image/heif,.heic,.heif',
                'multiple' => true,
                'icon'     => 'HEIC',
            ],
            'heic-to-svg' => [
                'type'     => 'file',
                'title'    => 'HEIC to SVG Converter',
                'hint'     => 'Trace HEIC images and convert to scalable SVG.',
                'accept'   => 'image/heic,image/heif,.heic,.heif',
                'multiple' => false,
                'icon'     => 'HEIC',
            ],
            'webp-to-jpg' => [
                'type'     => 'file',
                'title'    => 'WebP to JPG Converter',
                'hint'     => 'Convert WebP images to widely supported JPG.',
                'accept'   => 'image/webp',
                'multiple' => true,
                'icon'     => 'WEBP',
            ],
            'webp-to-png' => [
                'type'     => 'file',
                'title'    => 'WebP to PNG Converter',
                'hint'     => 'Convert WebP images to PNG with transparency.',
                'accept'   => 'image/webp',
                'multiple' => true,
                'icon'     => 'WEBP',
            ],
            'video-converter' => [
                'type'     => 'file',
                'title'    => 'Video Converter',
                'hint'     => 'Convert video files between formats directly in your browser.',
                'accept'   => 'video/*',
                'multiple' => false,
                'icon'     => 'VID',
            ],

            /* ── FILE TOOLS — Image Compress ───────────────────────── */

            'jpg-compressor' => [
                'type'     => 'file',
                'title'    => 'JPG Compressor',
                'hint'     => 'Reduce JPG file size while maintaining visual quality.',
                'accept'   => 'image/jpeg,image/jpg',
                'multiple' => true,
                'icon'     => 'JPG',
            ],
            'png-compressor' => [
                'type'     => 'file',
                'title'    => 'PNG Compressor',
                'hint'     => 'Optimize PNG files for faster loading and smaller size.',
                'accept'   => 'image/png',
                'multiple' => true,
                'icon'     => 'PNG',
            ],
            'webp-compressor' => [
                'type'     => 'file',
                'title'    => 'WebP Compressor',
                'hint'     => 'Further compress WebP images for optimal delivery.',
                'accept'   => 'image/webp',
                'multiple' => true,
                'icon'     => 'WEBP',
            ],
            'gif-compressor' => [
                'type'     => 'file',
                'title'    => 'GIF Compressor',
                'hint'     => 'Reduce animated and static GIF file sizes.',
                'accept'   => 'image/gif',
                'multiple' => false,
                'icon'     => 'GIF',
            ],
            'svg-compressor' => [
                'type'     => 'file',
                'title'    => 'SVG Compressor',
                'hint'     => 'Optimize SVG files by removing unnecessary data.',
                'accept'   => 'image/svg+xml,.svg',
                'multiple' => false,
                'icon'     => 'SVG',
            ],

            /* ── GENERATOR TOOLS ───────────────────────────────────── */

            'random-number' => [
                'type'     => 'generator',
                'title'    => 'Random Number Generator',
                'hint'     => 'Generate random numbers with integers, decimals, or multiples.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '#',
            ],
            'random-date' => [
                'type'     => 'generator',
                'title'    => 'Random Date Generator',
                'hint'     => 'Generate random dates within any range.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '📅',
            ],
            'random-letter' => [
                'type'     => 'generator',
                'title'    => 'Random Letter Generator',
                'hint'     => 'Generate random letters from A-Z.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '🔤',
            ],
            'random-month' => [
                'type'     => 'generator',
                'title'    => 'Random Month Generator',
                'hint'     => 'Generate random months of the year.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '📆',
            ],
            'random-choice' => [
                'type'     => 'generator',
                'title'    => 'Random Choice Generator',
                'hint'     => 'Pick random items from a list you provide.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '🎲',
            ],
            'random-ip' => [
                'type'     => 'generator',
                'title'    => 'Random IP Generator',
                'hint'     => 'Generate random IPv4 or IPv6 addresses.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '🌐',
            ],
            'uuid-generator' => [
                'type'     => 'generator',
                'title'    => 'UUID Generator',
                'hint'     => 'Generate UUIDs, ULIDs, and NanoIDs in various formats.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '🆔',
            ],
            'password-generator' => [
                'type'     => 'generator',
                'title'    => 'Password Generator',
                'hint'     => 'Create strong, random passwords with customizable character sets.',
                'accept'   => '',
                'multiple' => false,
                'icon'     => '🔐',
            ],
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Slug resolution                                                   */
    /* ------------------------------------------------------------------ */

    private function resolve_slug( array $settings ): string {

        $slug = sanitize_title( $settings['tool_slug'] ?? '' );

        if ( ! empty( $slug ) ) {
            return $slug;
        }

        $path = trim( wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' );

        if ( preg_match( '#^tools/([a-z0-9-]+)$#', $path, $m ) ) {
            return sanitize_title( $m[1] );
        }

        return '';
    }

    /* ------------------------------------------------------------------ */
    /*  Render                                                            */
    /* ------------------------------------------------------------------ */

    protected function render() {

        $s       = $this->get_settings_for_display();
        $slug    = $this->resolve_slug( $s );
        $map     = self::get_tool_map();
        $tool    = $map[ $slug ] ?? null;

        if ( ! $tool ) {
            return;
        }

        $type = $tool['type'];

        echo '<div class="tctp-ws" data-tool-slug="' . esc_attr( $slug ) . '" data-tool-type="' . esc_attr( $type ) . '">';

        if ( 'text' === $type ) {
            $this->render_text_pattern( $slug, $tool );
        } elseif ( 'file' === $type ) {
            $this->render_file_pattern( $slug, $tool );
        } elseif ( 'generator' === $type ) {
            $this->render_generator_pattern( $slug, $tool );
        }

        echo '</div>';
    }

    /* ------------------------------------------------------------------ */
    /*  Pattern A — Text Tools                                            */
    /* ------------------------------------------------------------------ */

    private function render_text_pattern( string $slug, array $tool ): void {

        $hint     = $tool['hint'] ?? '';
        $controls = $this->get_tool_specific_controls( $slug );
        ?>

        <div class="panel">
            <div class="panel-head">
                <h3><?php echo esc_html( '1 · Input' ); ?></h3>
                <span><?php echo esc_html( $hint ); ?></span>
            </div>
            <div class="panel-body">

                <?php if ( ! empty( $controls['before_textarea'] ) ) : ?>
                    <?php echo $controls['before_textarea']; // phpcs:ignore -- escaped per-tool ?>
                <?php endif; ?>

                <textarea
                    id="tctp-<?php echo esc_attr( $slug ); ?>-input"
                    class="tctp-textarea"
                    placeholder="<?php echo esc_attr( $controls['placeholder'] ?? 'Paste or type your text here…' ); ?>"
                    rows="8"
                ></textarea>

                <?php if ( ! empty( $controls['between_textarea_and_actions'] ) ) : ?>
                    <?php echo $controls['between_textarea_and_actions']; // phpcs:ignore -- escaped per-tool ?>
                <?php endif; ?>

                <div class="actions">
                    <button class="btn btn-accent" id="tctp-<?php echo esc_attr( $slug ); ?>-run">
                        <?php echo esc_html( $controls['run_label'] ?? 'Run' ); ?>
                    </button>
                    <button class="btn btn-ghost" id="tctp-<?php echo esc_attr( $slug ); ?>-copy">Copy</button>
                    <button class="btn btn-ghost" id="tctp-<?php echo esc_attr( $slug ); ?>-download">Download</button>
                    <button class="btn btn-ghost" id="tctp-<?php echo esc_attr( $slug ); ?>-clear">Clear</button>
                </div>

            </div>
        </div>

        <div class="ws-right">
            <div class="panel">
                <div class="panel-head">
                    <h3><?php echo esc_html( '2 · Result' ); ?></h3>
                    <span id="tctp-<?php echo esc_attr( $slug ); ?>-status">Ready</span>
                </div>
                <div class="panel-body">
                    <textarea
                        id="tctp-<?php echo esc_attr( $slug ); ?>-output"
                        class="tctp-textarea"
                        readonly
                        rows="12"
                        placeholder="Result will appear here…"
                    ></textarea>
                </div>
            </div>
        </div>

        <?php
    }

    /* ------------------------------------------------------------------ */
    /*  Pattern B — File Tools                                            */
    /* ------------------------------------------------------------------ */

    private function render_file_pattern( string $slug, array $tool ): void {

        $hint     = $tool['hint'] ?? 'Drop your file here';
        $accept   = $tool['accept'] ?? '';
        $multiple = ! empty( $tool['multiple'] );
        $icon     = $tool['icon'] ?? 'FILE';
        $controls = $this->get_tool_specific_controls( $slug );
        ?>

        <div class="panel">
            <div class="panel-head">
                <h3><?php echo esc_html( '1 · Upload' ); ?></h3>
                <span><?php echo esc_html( $hint ); ?></span>
            </div>
            <div class="panel-body">

                <div class="drop" id="tctp-<?php echo esc_attr( $slug ); ?>-drop">
                    <div class="ic"><?php echo esc_html( $icon ); ?></div>
                    <b>Drop your file<?php echo $multiple ? 's' : ''; ?> here</b>
                    <small>or click to browse — nothing is uploaded to a server</small>
                    <input
                        type="file"
                        id="tctp-<?php echo esc_attr( $slug ); ?>-file"
                        accept="<?php echo esc_attr( $accept ); ?>"
                        <?php echo $multiple ? 'multiple' : ''; ?>
                    >
                </div>

                <div class="file-row" id="tctp-<?php echo esc_attr( $slug ); ?>-file-row">
                    <div class="ic"><?php echo esc_html( $icon ); ?></div>
                    <div class="meta">
                        <b id="tctp-<?php echo esc_attr( $slug ); ?>-fname"></b>
                        <span id="tctp-<?php echo esc_attr( $slug ); ?>-fmeta"></span>
                    </div>
                    <button class="x" id="tctp-<?php echo esc_attr( $slug ); ?>-clear-file" aria-label="Remove file">✕</button>
                </div>

                <?php if ( ! empty( $controls['file_options'] ) ) : ?>
                    <?php echo $controls['file_options']; // phpcs:ignore -- escaped per-tool ?>
                <?php endif; ?>

                <div class="bar"><i id="tctp-<?php echo esc_attr( $slug ); ?>-bar-fill"></i></div>
                <div class="bar-label">
                    <span id="tctp-<?php echo esc_attr( $slug ); ?>-bar-text">Ready</span>
                    <span id="tctp-<?php echo esc_attr( $slug ); ?>-bar-pct">0%</span>
                </div>

                <div class="actions">
                    <button class="btn btn-accent" id="tctp-<?php echo esc_attr( $slug ); ?>-run" disabled>
                        <?php echo esc_html( $controls['run_label'] ?? 'Process' ); ?>
                    </button>
                    <button class="btn btn-ghost" id="tctp-<?php echo esc_attr( $slug ); ?>-download" disabled>Download</button>
                    <button class="btn btn-ghost" id="tctp-<?php echo esc_attr( $slug ); ?>-clear">Clear</button>
                </div>

            </div>
        </div>

        <div class="ws-right">
            <div class="panel">
                <div class="panel-head">
                    <h3><?php echo esc_html( '2 · Result' ); ?></h3>
                    <span id="tctp-<?php echo esc_attr( $slug ); ?>-status">Idle</span>
                </div>
                <div class="panel-body">
                    <div class="stats" id="tctp-<?php echo esc_attr( $slug ); ?>-stats"></div>
                    <div class="preview" id="tctp-<?php echo esc_attr( $slug ); ?>-preview">Preview appears after processing</div>
                </div>
            </div>
        </div>

        <?php
    }

    /* ------------------------------------------------------------------ */
    /*  Pattern C — Generator Tools                                       */
    /* ------------------------------------------------------------------ */

    private function render_generator_pattern( string $slug, array $tool ): void {

        $hint     = $tool['hint'] ?? '';
        $controls = $this->get_tool_specific_controls( $slug );
        ?>

        <div class="panel">
            <div class="panel-head">
                <h3><?php echo esc_html( 'Settings' ); ?></h3>
                <span><?php echo esc_html( $hint ); ?></span>
            </div>
            <div class="panel-body">

                <?php echo $controls['settings']; // phpcs:ignore -- escaped per-tool ?>

                <div class="actions">
                    <button class="btn btn-accent" id="tctp-<?php echo esc_attr( $slug ); ?>-run">
                        <?php echo esc_html( $controls['run_label'] ?? 'Generate' ); ?>
                    </button>
                    <button class="btn btn-ghost" id="tctp-<?php echo esc_attr( $slug ); ?>-copy">Copy</button>
                    <button class="btn btn-ghost" id="tctp-<?php echo esc_attr( $slug ); ?>-download">Download</button>
                    <button class="btn btn-ghost" id="tctp-<?php echo esc_attr( $slug ); ?>-clear">Clear</button>
                </div>

            </div>
        </div>

        <div class="ws-right">
            <div class="panel">
                <div class="panel-head">
                    <h3><?php echo esc_html( 'Result' ); ?></h3>
                    <span id="tctp-<?php echo esc_attr( $slug ); ?>-status">Ready</span>
                </div>
                <div class="panel-body">
                    <div class="stats" id="tctp-<?php echo esc_attr( $slug ); ?>-stats"></div>
                    <textarea
                        id="tctp-<?php echo esc_attr( $slug ); ?>-output"
                        class="tctp-textarea"
                        readonly
                        rows="12"
                        placeholder="Result will appear here…"
                    ></textarea>
                </div>
            </div>
        </div>

        <?php
    }

    /* ------------------------------------------------------------------ */
    /*  Tool-Specific Controls                                            */
    /* ------------------------------------------------------------------ */

    private function get_tool_specific_controls( string $slug ): array {

        $controls = [
            'placeholder'                  => 'Paste or type your text here…',
            'run_label'                    => 'Run',
            'before_textarea'              => '',
            'between_textarea_and_actions' => '',
            'file_options'                 => '',
            'settings'                     => '',
        ];

        switch ( $slug ) {

            /* ── Text tools with extra controls ───────────────────── */

            case 'case-converter':
                $controls['before_textarea'] = '<div class="tctp-mode-btns" id="tctp-case-converter-modes">'
                    . '<button class="btn-ghost sel" data-case="lowercase">lowercase</button>'
                    . '<button class="btn-ghost" data-case="uppercase">UPPERCASE</button>'
                    . '<button class="btn-ghost" data-case="title">Title Case</button>'
                    . '<button class="btn-ghost" data-case="sentence">Sentence case</button>'
                    . '<button class="btn-ghost" data-case="inverse">tOGGLE cASE</button>'
                    . '<button class="btn-ghost" data-case="capitalized">Start Case</button>'
                    . '</div>';
                $controls['run_label'] = 'Convert';
                break;

            case 'sentence-case':
                $controls['run_label'] = 'Convert';
                break;

            case 'title-case':
                $controls['run_label'] = 'Convert';
                break;

            case 'find-replace':
                $controls['before_textarea'] = '<div class="tctp-find-row">'
                    . '<div class="tctp-find-field"><label>Find</label><input type="text" id="tctp-find-replace-find" class="tctp-input" placeholder="Search text…"></div>'
                    . '<div class="tctp-find-field"><label>Replace</label><input type="text" id="tctp-find-replace-replace" class="tctp-input" placeholder="Replace with…"></div>'
                    . '<label class="tctp-checkbox-label"><input type="checkbox" id="tctp-find-replace-case"> Match case</label>'
                    . '</div>';
                $controls['run_label'] = 'Replace';
                $controls['placeholder'] = 'Enter text containing occurrences to replace…';
                break;

            case 'character-remover':
                $controls['before_textarea'] = '<div class="tctp-char-opts">'
                    . '<label>Remove characters</label>'
                    . '<input type="text" id="tctp-character-remover-chars" class="tctp-input" placeholder="Enter characters to remove…">'
                    . '<label class="tctp-checkbox-label"><input type="checkbox" id="tctp-character-remover-spaces"> Also remove spaces</label>'
                    . '</div>';
                $controls['run_label'] = 'Remove';
                break;

            case 'reverse-text':
                $controls['before_textarea'] = '<div class="tctp-mode-btns" id="tctp-reverse-text-modes">'
                    . '<button class="btn-ghost sel" data-rev-mode="characters">Reverse Characters</button>'
                    . '<button class="btn-ghost" data-rev-mode="words">Reverse Words</button>'
                    . '<button class="btn-ghost" data-rev-mode="lines">Reverse Lines</button>'
                    . '</div>';
                $controls['run_label'] = 'Reverse';
                break;

            case 'sort-words':
                $controls['before_textarea'] = '<div class="tctp-mode-btns" id="tctp-sort-words-modes">'
                    . '<button class="btn-ghost sel" data-sort-mode="alpha">A → Z</button>'
                    . '<button class="btn-ghost" data-sort-mode="alpha-desc">Z → A</button>'
                    . '<button class="btn-ghost" data-sort-mode="length">By Length</button>'
                    . '<button class="btn-ghost" data-sort-mode="length-desc">By Length ↓</button>'
                    . '</div>';
                $controls['run_label'] = 'Sort';
                break;

            case 'repeat-text':
                $controls['before_textarea'] = '<div class="tctp-repeat-opts">'
                    . '<label>Repeat count</label>'
                    . '<input type="number" id="tctp-repeat-text-count" class="tctp-input tctp-input-sm" value="3" min="1" max="10000">'
                    . '<label class="tctp-checkbox-label"><input type="checkbox" id="tctp-repeat-text-newline"> Add newline between repeats</label>'
                    . '</div>';
                $controls['run_label'] = 'Repeat';
                break;

            case 'remove-line-breaks':
                $controls['before_textarea'] = '<div class="tctp-mode-btns" id="tctp-remove-line-breaks-modes">'
                    . '<button class="btn-ghost sel" data-lb-mode="spaces">Replace with spaces</button>'
                    . '<button class="btn-ghost" data-lb-mode="join">Join all lines</button>'
                    . '<button class="btn-ghost" data-lb-mode="paragraphs">Keep paragraphs</button>'
                    . '</div>';
                $controls['run_label'] = 'Remove';
                break;

            case 'word-frequency':
                $controls['placeholder'] = 'Paste text to analyse word frequency…';
                $controls['run_label'] = 'Analyse';
                $controls['between_textarea_and_actions'] = '<div class="tctp-freq-sort">'
                    . '<label class="tctp-checkbox-label"><input type="checkbox" id="tctp-word-frequency-ignore" checked> Ignore common words</label>'
                    . '</div>';
                break;

            case 'sentence-counter':
                $controls['placeholder'] = 'Paste text to count sentences, words, and characters…';
                $controls['run_label'] = 'Count';
                break;

            case 'pig-latin':
                $controls['placeholder'] = 'Enter English text to translate to Pig Latin…';
                $controls['run_label'] = 'Translate';
                break;

            case 'nato-phonetic':
                $controls['run_label'] = 'Convert';
                break;

            case 'phonetic-spelling':
                $controls['run_label'] = 'Convert';
                break;

            case 'wingdings':
                $controls['before_textarea'] = '<div class="tctp-mode-btns" id="tctp-wingdings-modes">'
                    . '<button class="btn-ghost sel" data-wing-dir="to">Text → Wingdings</button>'
                    . '<button class="btn-ghost" data-wing-dir="from">Wingdings → Text</button>'
                    . '</div>';
                $controls['run_label'] = 'Convert';
                break;

            case 'roman-numeral':
                $controls['before_textarea'] = '<div class="tctp-mode-btns" id="tctp-roman-numeral-modes">'
                    . '<button class="btn-ghost sel" data-roman-mode="to">Number → Roman</button>'
                    . '<button class="btn-ghost" data-roman-mode="from">Roman → Number</button>'
                    . '</div>';
                $controls['placeholder'] = 'Enter a number or Roman numeral…';
                $controls['run_label'] = 'Convert';
                break;

            case 'word-cloud':
                $controls['placeholder'] = 'Paste text to generate a word cloud…';
                $controls['run_label'] = 'Generate';
                break;

            case 'online-notepad':
                $controls['placeholder'] = 'Start typing or paste your notes here…';
                $controls['run_label'] = 'Save';
                $controls['between_textarea_and_actions'] = '<div class="tctp-notepad-info">'
                    . '<span class="tctp-hint" id="tctp-online-notepad-count">0 characters · 0 words</span>'
                    . '</div>';
                break;

            case 'apa-format':
                $controls['placeholder'] = 'Enter source details or paste a reference to format…';
                $controls['run_label'] = 'Format';
                break;

            case 'invisible-text':
                $controls['before_textarea'] = '<div class="tctp-mode-btns" id="tctp-invisible-text-modes">'
                    . '<button class="btn-ghost sel" data-invis-mode="zero-width">Zero-width space</button>'
                    . '<button class="btn-ghost" data-invis-mode="zwsp">ZWSP variant</button>'
                    . '<button class="btn-ghost" data-invis-mode="variation">Variation selectors</button>'
                    . '</div>';
                $controls['run_label'] = 'Generate';
                break;

            case 'ascii-art':
                $controls['before_textarea'] = '<div class="tctp-ascii-font">'
                    . '<label>Font</label>'
                    . '<select id="tctp-ascii-art-font" class="tctp-select">'
                    . '<option value="standard">Standard</option>'
                    . '<option value="shadow">Shadow</option>'
                    . '<option value="slant">Slant</option>'
                    . '<option value="big">Big</option>'
                    . '<option value="block">Block</option>'
                    . '</select>'
                    . '</div>';
                $controls['placeholder'] = 'Enter text to convert to ASCII art…';
                $controls['run_label'] = 'Generate';
                break;

            case 'duplicate-line':
                $controls['run_label'] = 'Remove';
                break;

            case 'duplicate-word':
                $controls['run_label'] = 'Remove';
                break;

            case 'em-dash-remover':
                $controls['before_textarea'] = '<div class="tctp-emdash-opts">'
                    . '<label>Replace with</label>'
                    . '<select id="tctp-em-dash-remover-replace" class="tctp-select">'
                    . '<option value="comma">Comma (,)</option>'
                    . '<option value="space">Space</option>'
                    . '<option value="nothing">Nothing (remove)</option>'
                    . '<option value="hyphen">Hyphen (-)</option>'
                    . '</select>'
                    . '</div>';
                $controls['run_label'] = 'Replace';
                break;

            case 'remove-underscores':
                $controls['before_textarea'] = '<div class="tctp-underscore-opts">'
                    . '<label>Replace with</label>'
                    . '<select id="tctp-remove-underscores-replace" class="tctp-select">'
                    . '<option value="space">Space</option>'
                    . '<option value="hyphen">Hyphen (-)</option>'
                    . '<option value="nothing">Nothing (remove)</option>'
                    . '</select>'
                    . '</div>';
                $controls['run_label'] = 'Remove';
                break;

            case 'whitespace-remover':
                $controls['before_textarea'] = '<div class="tctp-mode-btns" id="tctp-whitespace-remover-modes">'
                    . '<button class="btn-ghost sel" data-ws="all">All whitespace</button>'
                    . '<button class="btn-ghost" data-ws="extra">Extra spaces only</button>'
                    . '<button class="btn-ghost" data-ws="leading">Leading & trailing</button>'
                    . '</div>';
                $controls['run_label'] = 'Remove';
                break;

            case 'remove-formatting':
                $controls['before_textarea'] = '<div class="tctp-format-opts">'
                    . '<label class="tctp-checkbox-label"><input type="checkbox" id="tctp-remove-formatting-html" checked> Remove HTML tags</label>'
                    . '<label class="tctp-checkbox-label"><input type="checkbox" id="tctp-remove-formatting-styles" checked> Remove inline styles</label>'
                    . '<label class="tctp-checkbox-label"><input type="checkbox" id="tctp-remove-formatting-entities"> Decode HTML entities</label>'
                    . '</div>';
                $controls['run_label'] = 'Remove';
                break;

            case 'plain-text':
                $controls['run_label'] = 'Convert';
                break;

            case 'trim-lines':
                $controls['before_textarea'] = '<div class="tctp-mode-btns" id="tctp-trim-lines-modes">'
                    . '<button class="btn-ghost sel" data-trim="both">Both sides</button>'
                    . '<button class="btn-ghost" data-trim="left">Left only</button>'
                    . '<button class="btn-ghost" data-trim="right">Right only</button>'
                    . '</div>';
                $controls['run_label'] = 'Trim';
                break;

            case 'camel-case':
            case 'kebab-case':
            case 'snake-case':
                $controls['before_textarea'] = '<div class="tctp-mode-btns" id="tctp-camel-case-modes">'
                    . '<button class="btn-ghost sel" data-case-mode="camel">camelCase</button>'
                    . '<button class="btn-ghost" data-case-mode="pascal">PascalCase</button>'
                    . '<button class="btn-ghost" data-case-mode="snake">snake_case</button>'
                    . '<button class="btn-ghost" data-case-mode="kebab">kebab-case</button>'
                    . '<button class="btn-ghost" data-case-mode="dot">dot.case</button>'
                    . '<button class="btn-ghost" data-case-mode="path">path/case</button>'
                    . '<button class="btn-ghost" data-case-mode="const">CONSTANT_CASE</button>'
                    . '</div>';
                $controls['run_label'] = 'Convert';
                break;

            case 'text-to-ascii':
                $controls['before_textarea'] = '<div class="tctp-mode-btns" id="tctp-text-to-ascii-modes">'
                    . '<button class="btn-ghost sel" data-ascii-font="standard">Standard</button>'
                    . '<button class="btn-ghost" data-ascii-font="shadow">Shadow</button>'
                    . '<button class="btn-ghost" data-ascii-font="banner">Banner</button>'
                    . '<button class="btn-ghost" data-ascii-font="simple">Simple</button>'
                    . '<button class="btn-ghost" data-ascii-font="stars">Stars</button>'
                    . '<button class="btn-ghost" data-ascii-font="boxed">Boxed</button>'
                    . '</div>';
                $controls['run_label'] = 'Generate';
                break;

            case 'markdown-to-html':
                $controls['placeholder'] = 'Paste Markdown text here…';
                $controls['run_label'] = 'Convert';
                $controls['between_textarea_and_actions'] = '<div class="tctp-md-opts">'
                    . '<label class="tctp-checkbox-label"><input type="checkbox" id="tctp-markdown-to-html-preview"> Show preview</label>'
                    . '</div>';
                break;

            case 'json-formatter':
                $controls['placeholder'] = 'Paste JSON data here…';
                $controls['run_label'] = 'Format';
                $controls['before_textarea'] = '<div class="tctp-json-opts">'
                    . '<label>Indent</label>'
                    . '<select id="tctp-json-formatter-indent" class="tctp-select">'
                    . '<option value="2">2 spaces</option>'
                    . '<option value="4">4 spaces</option>'
                    . '<option value="tab">Tab</option>'
                    . '</select>'
                    . '</div>';
                break;

            case 'hash-generator':
                $controls['placeholder'] = 'Enter text to hash…';
                $controls['before_textarea'] = '<div class="tctp-mode-btns" id="tctp-hash-generator-modes">'
                    . '<button class="btn-ghost" data-algo="SHA-1">SHA-1</button>'
                    . '<button class="btn-ghost sel" data-algo="SHA-256">SHA-256</button>'
                    . '<button class="btn-ghost" data-algo="SHA-384">SHA-384</button>'
                    . '<button class="btn-ghost" data-algo="SHA-512">SHA-512</button>'
                    . '</div>';
                $controls['run_label'] = 'Generate';
                break;

            /* ── File tools with compression levels ───────────────── */

            case 'pdf-compressor':
                $controls['file_options'] = '<h4 class="tctp-options-heading">Compression level</h4>'
                    . '<div class="tctp-levels" id="tctp-pdf-compressor-levels">'
                    . '<div class="tctp-level" data-l="light"><b>Light</b><span>Keeps selectable text · ~25% smaller</span></div>'
                    . '<div class="tctp-level tctp-sel" data-l="balanced"><b>Balanced</b><span>Recommended · ~55% smaller</span></div>'
                    . '<div class="tctp-level" data-l="strong"><b>Strong</b><span>Rebuilds pages · ~80% smaller</span></div>'
                    . '</div>'
                    . '<p class="tctp-hint">Light mode retains selectable text. Stronger compression rebuilds pages visually for smaller files.</p>';
                $controls['run_label'] = 'Compress PDF';
                break;

            case 'jpg-compressor':
            case 'png-compressor':
            case 'webp-compressor':
                $controls['file_options'] = '<div class="tctp-quality-opts">'
                    . '<label>Quality level</label>'
                    . '<input type="range" id="tctp-' . esc_attr( $slug ) . '-quality" min="10" max="100" value="80" class="tctp-range">'
                    . '<span id="tctp-' . esc_attr( $slug ) . '-quality-label" class="tctp-range-label">80%</span>'
                    . '</div>';
                $controls['run_label'] = 'Compress';
                break;

            case 'gif-compressor':
                $controls['file_options'] = '<div class="tctp-quality-opts">'
                    . '<label>Max colours</label>'
                    . '<select id="tctp-gif-compressor-colours" class="tctp-select">'
                    . '<option value="256">256 (default)</option>'
                    . '<option value="128">128</option>'
                    . '<option value="64">64</option>'
                    . '<option value="32">32</option>'
                    . '</select>'
                    . '</div>';
                $controls['run_label'] = 'Compress';
                break;

            case 'svg-compressor':
                $controls['file_options'] = '<div class="tctp-svg-opts">'
                    . '<label class="tctp-checkbox-label"><input type="checkbox" id="tctp-svg-compressor-precision" checked> Round numeric values</label>'
                    . '<label class="tctp-checkbox-label"><input type="checkbox" id="tctp-svg-compressor-comments" checked> Remove comments</label>'
                    . '<label class="tctp-checkbox-label"><input type="checkbox" id="tctp-svg-compressor-meta" checked> Remove metadata</label>'
                    . '</div>';
                $controls['run_label'] = 'Compress';
                break;

            case 'pdf-to-word':
                $controls['run_label'] = 'Convert';
                break;

            case 'pdf-to-jpg':
            case 'pdf-to-png':
                $controls['run_label'] = 'Convert';
                break;

            case 'pdf-merger':
                $controls['run_label'] = 'Merge';
                break;

            case 'pdf-splitter':
                $controls['file_options'] = '<div class="tctp-split-opts">'
                    . '<label>Page range</label>'
                    . '<input type="text" id="tctp-pdf-splitter-range" class="tctp-input" placeholder="e.g. 1-3, 5, 8-10">'
                    . '<p class="tctp-hint">Leave empty to split into individual pages.</p>'
                    . '</div>';
                $controls['run_label'] = 'Split';
                break;

            case 'rotate-pdf':
                $controls['file_options'] = '<div class="tctp-rotate-opts">'
                    . '<label>Rotation</label>'
                    . '<select id="tctp-rotate-pdf-angle" class="tctp-select" data-opt="angle">'
                    . '<option value="90">90° clockwise</option>'
                    . '<option value="180">180°</option>'
                    . '<option value="270">270° (counter-clockwise)</option>'
                    . '</select>'
                    . '</div>';
                $controls['run_label'] = 'Rotate';
                break;

            case 'delete-pdf-pages':
                $controls['file_options'] = '<div class="tctp-delete-opts">'
                    . '<label>Pages to delete</label>'
                    . '<input type="text" id="tctp-delete-pdf-pages-range" class="tctp-input" placeholder="e.g. 1, 3, 5-8">'
                    . '</div>';
                $controls['run_label'] = 'Delete Pages';
                break;

            case 'jpg-to-pdf':
            case 'png-to-pdf':
                $controls['file_options'] = '<div class="tctp-pdf-opts">'
                    . '<label class="tctp-checkbox-label"><input type="checkbox" id="tctp-' . esc_attr( $slug ) . '-fit" checked> Fit to A4 page</label>'
                    . '<label class="tctp-checkbox-label"><input type="checkbox" id="tctp-' . esc_attr( $slug ) . '-landscape"> Landscape orientation</label>'
                    . '</div>';
                $controls['run_label'] = 'Create PDF';
                break;

            case 'jpg-to-png':
            case 'jpg-to-webp':
            case 'jpg-to-gif':
            case 'jpg-to-heic':
            case 'jpg-to-avif':
            case 'png-to-jpg':
            case 'png-to-webp':
            case 'png-to-heic':
            case 'heic-to-jpg':
            case 'heic-to-png':
            case 'webp-to-jpg':
            case 'webp-to-png':
                $controls['run_label'] = 'Convert';
                break;

            case 'jpg-to-svg':
            case 'png-to-svg':
            case 'heic-to-svg':
                $controls['file_options'] = '<div class="tctp-trace-opts">'
                    . '<label>Detail level</label>'
                    . '<input type="range" id="tctp-' . esc_attr( $slug ) . '-detail" min="1" max="10" value="5" class="tctp-range">'
                    . '<span id="tctp-' . esc_attr( $slug ) . '-detail-label" class="tctp-range-label">5</span>'
                    . '</div>';
                $controls['run_label'] = 'Trace & Convert';
                break;

            case 'video-converter':
                $controls['file_options'] = '<div class="tctp-video-opts">'
                    . '<label>Output format</label>'
                    . '<select id="tctp-video-converter-format" class="tctp-select">'
                    . '<option value="webm">WebM</option>'
                    . '<option value="mp4">MP4</option>'
                    . '<option value="gif">GIF (short clips)</option>'
                    . '</select>'
                    . '</div>';
                $controls['run_label'] = 'Convert';
                break;

            case 'image-to-text':
                $controls['run_label'] = 'Extract Text';
                break;

            case 'remove-background':
                $controls['run_label'] = 'Remove Background';
                break;

            case 'image-resizer':
                $controls['file_options'] = '<div class="tctp-resize-opts">'
                    . '<div class="tctp-resize-fields">'
                    . '<div><label>Width</label><input type="number" id="tctp-image-resizer-width" class="tctp-input" placeholder="px"></div>'
                    . '<div><label>Height</label><input type="number" id="tctp-image-resizer-height" class="tctp-input" placeholder="px"></div>'
                    . '</div>'
                    . '<label class="tctp-checkbox-label"><input type="checkbox" id="tctp-image-resizer-lock" checked> Lock aspect ratio</label>'
                    . '<label>Scale</label>'
                    . '<select id="tctp-image-resizer-unit" class="tctp-select">'
                    . '<option value="px">Pixels</option>'
                    . '<option value="pct">Percentage</option>'
                    . '</select>'
                    . '</div>';
                $controls['run_label'] = 'Resize';
                break;

            case 'svg-to-png':
                $controls['file_options'] = '<div class="tctp-raster-opts">'
                    . '<label>Scale</label>'
                    . '<select id="tctp-svg-to-png-scale" class="tctp-select">'
                    . '<option value="1">1×</option>'
                    . '<option value="2" selected>2×</option>'
                    . '<option value="3">3×</option>'
                    . '<option value="4">4×</option>'
                    . '</select>'
                    . '<label>Background</label>'
                    . '<div class="tctp-mode-btns" id="tctp-svg-to-png-bg-modes">'
                    . '<button class="btn-ghost sel" data-bg="transparent">Transparent</button>'
                    . '<button class="btn-ghost" data-bg="white">White</button>'
                    . '</div>'
                    . '</div>';
                $controls['run_label'] = 'Convert';
                break;

            case 'gif-to-png':
                $controls['run_label'] = 'Convert';
                break;

            case 'pdf-to-text':
                $controls['run_label'] = 'Extract Text';
                break;

            /* ── Generator tools ──────────────────────────────────── */

            case 'random-number':
                $controls['settings'] = '<div class="tctp-gen-field">'
                    . '<label>Min value</label>'
                    . '<input type="number" id="tctp-random-number-min" class="tctp-input" value="1">'
                    . '<label>Max value</label>'
                    . '<input type="number" id="tctp-random-number-max" class="tctp-input" value="100">'
                    . '<label>Count</label>'
                    . '<input type="number" id="tctp-random-number-count" class="tctp-input" value="1" min="1" max="1000">'
                    . '<label class="tctp-checkbox-label"><input type="checkbox" id="tctp-random-number-unique"> Unique only</label>'
                    . '</div>';
                $controls['run_label'] = 'Generate';
                break;

            case 'random-date':
                $controls['settings'] = '<div class="tctp-gen-field">'
                    . '<label>From</label>'
                    . '<input type="date" id="tctp-random-date-from" class="tctp-input">'
                    . '<label>To</label>'
                    . '<input type="date" id="tctp-random-date-to" class="tctp-input">'
                    . '<label>Count</label>'
                    . '<input type="number" id="tctp-random-date-count" class="tctp-input" value="1" min="1" max="1000">'
                    . '<label class="tctp-checkbox-label"><input type="checkbox" id="tctp-random-date-time"> Include time</label>'
                    . '</div>';
                $controls['run_label'] = 'Generate';
                break;

            case 'random-letter':
                $controls['settings'] = '<div class="tctp-gen-field">'
                    . '<label>Count</label>'
                    . '<input type="number" id="tctp-random-letter-count" class="tctp-input" value="10" min="1" max="10000">'
                    . '<label class="tctp-checkbox-label"><input type="checkbox" id="tctp-random-letter-upper"> Uppercase</label>'
                    . '<label class="tctp-checkbox-label"><input type="checkbox" id="tctp-random-letter-unique"> Unique only</label>'
                    . '</div>';
                $controls['run_label'] = 'Generate';
                break;

            case 'random-month':
                $controls['settings'] = '<div class="tctp-gen-field">'
                    . '<label>Count</label>'
                    . '<input type="number" id="tctp-random-month-count" class="tctp-input" value="1" min="1" max="100">'
                    . '<label class="tctp-checkbox-label"><input type="checkbox" id="tctp-random-month-unique"> Unique only</label>'
                    . '</div>';
                $controls['run_label'] = 'Generate';
                break;

            case 'random-choice':
                $controls['settings'] = '<div class="tctp-gen-field">'
                    . '<label>Enter items (one per line)</label>'
                    . '<textarea id="tctp-random-choice-input" class="tctp-textarea" rows="6" placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>'
                    . '<label>Pick count</label>'
                    . '<input type="number" id="tctp-random-choice-count" class="tctp-input" value="1" min="1" max="100">'
                    . '<label class="tctp-checkbox-label"><input type="checkbox" id="tctp-random-choice-unique"> Unique picks</label>'
                    . '</div>';
                $controls['run_label'] = 'Pick';
                break;

            case 'random-ip':
                $controls['settings'] = '<div class="tctp-gen-field">'
                    . '<label>Format</label>'
                    . '<div class="tctp-mode-btns" id="tctp-random-ip-modes">'
                    . '<button class="btn-ghost sel" data-ip="ipv4">IPv4</button>'
                    . '<button class="btn-ghost" data-ip="ipv6">IPv6</button>'
                    . '</div>'
                    . '<label>Count</label>'
                    . '<input type="number" id="tctp-random-ip-count" class="tctp-input" value="1" min="1" max="1000">'
                    . '</div>';
                $controls['run_label'] = 'Generate';
                break;

            case 'uuid-generator':
                $controls['settings'] = '<div class="tctp-gen-field">'
                    . '<label>Format</label>'
                    . '<div class="tctp-mode-btns" id="tctp-uuid-generator-modes">'
                    . '<button class="btn-ghost sel" data-uuid="v4">UUID v4</button>'
                    . '<button class="btn-ghost" data-uuid="ulid">ULID</button>'
                    . '<button class="btn-ghost" data-uuid="nano">NanoID</button>'
                    . '</div>'
                    . '<label>Count</label>'
                    . '<input type="number" id="tctp-uuid-generator-count" class="tctp-input" value="1" min="1" max="1000">'
                    . '<label class="tctp-checkbox-label"><input type="checkbox" id="tctp-uuid-generator-upper"> Uppercase</label>'
                    . '</div>';
                $controls['run_label'] = 'Generate';
                break;

            case 'password-generator':
                $controls['settings'] = '<div class="tctp-gen-field">'
                    . '<label>Length</label>'
                    . '<input type="number" id="tctp-password-generator-length" class="tctp-input" value="16" min="4" max="128">'
                    . '<label class="tctp-checkbox-label"><input type="checkbox" id="tctp-password-generator-upper" checked> Uppercase (A-Z)</label>'
                    . '<label class="tctp-checkbox-label"><input type="checkbox" id="tctp-password-generator-lower" checked> Lowercase (a-z)</label>'
                    . '<label class="tctp-checkbox-label"><input type="checkbox" id="tctp-password-generator-digits" checked> Digits (0-9)</label>'
                    . '<label class="tctp-checkbox-label"><input type="checkbox" id="tctp-password-generator-symbols" checked> Symbols (!@#$…)</label>'
                    . '<label>Count</label>'
                    . '<input type="number" id="tctp-password-generator-count" class="tctp-input" value="5" min="1" max="100">'
                    . '</div>';
                $controls['run_label'] = 'Generate';
                break;

            case 'lorem-ipsum':
                $controls['settings'] = '<div class="tctp-gen-field">'
                    . '<label>Type</label>'
                    . '<select id="tctp-lorem-ipsum-type" class="tctp-select" data-opt="type">'
                    . '<option value="paragraphs">Paragraphs</option>'
                    . '<option value="sentences">Sentences</option>'
                    . '<option value="words">Words</option>'
                    . '</select>'
                    . '<label>Count</label>'
                    . '<input type="number" id="tctp-lorem-ipsum-count" class="tctp-input" data-opt="count" value="3" min="1" max="200">'
                    . '</div>';
                $controls['run_label'] = 'Generate';
                break;

            case 'qr-code-generator':
                $controls['settings'] = '<div class="tctp-gen-field">'
                    . '<label>Content type</label>'
                    . '<div class="tctp-mode-btns" id="tctp-qr-code-generator-modes">'
                    . '<button class="btn-ghost sel" data-qr="url">URL</button>'
                    . '<button class="btn-ghost" data-qr="text">Text</button>'
                    . '<button class="btn-ghost" data-qr="email">Email</button>'
                    . '<button class="btn-ghost" data-qr="wifi">Wi-Fi</button>'
                    . '</div>'
                    . '<input type="text" id="tctp-qr-code-generator-input" class="tctp-input" placeholder="https://example.com">'
                    . '<label>Size</label>'
                    . '<select id="tctp-qr-code-generator-size" class="tctp-select">'
                    . '<option value="200">200 × 200</option>'
                    . '<option value="400" selected>400 × 400</option>'
                    . '<option value="600">600 × 600</option>'
                    . '<option value="800">800 × 800</option>'
                    . '</select>'
                    . '<label>Foreground</label>'
                    . '<input type="color" id="tctp-qr-code-generator-fg" class="tctp-color" value="#000000">'
                    . '<label>Background</label>'
                    . '<input type="color" id="tctp-qr-code-generator-bg" class="tctp-color" value="#ffffff">'
                    . '</div>';
                $controls['run_label'] = 'Generate';
                break;
        }

        return $controls;
    }

    protected function content_template() {}
}
