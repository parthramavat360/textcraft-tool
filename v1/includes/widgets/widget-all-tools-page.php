<?php
/**
 * Widget: All Tools Page
 *
 * Renders a complete "All Tools" browsing page with:
 *  - Hero section (badge, title with gradient, subtitle)
 *  - Live client-side search bar that filters cards and hides empty categories
 *  - Eight category sections, each with a title and tool-count badge
 *  - 74 tool cards covering all registered public tools
 *  - "No results" empty state shown when search returns nothing
 *
 * All 74 tools are stored as a repeater so editors can add, edit,
 * reorder, or remove tools entirely from the Elementor panel without
 * touching code. The live search is pure vanilla JS — no jQuery.
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
 * Elementor widget: All Tools Page
 *
 * A self-contained, searchable directory of all text tools,
 * grouped into category sections with live filtering.
 */
class Widget_All_Tools_Page extends Widget_Base {

	// ── Identity ──────────────────────────────────────────────

	public function get_name(): string  { return 'textcraft_all_tools_page'; }
	public function get_title(): string { return esc_html__( 'All Tools Page', 'textcraft-tools' ); }
	public function get_icon(): string  { return 'eicon-apps'; }

	public function get_categories(): array { return [ 'textcraft-tools' ]; }

	public function get_keywords(): array {
		return [ 'all tools', 'tools page', 'directory', 'search', 'grid', 'textcraft', 'free online tools', 'browser utilities' ];
	}

	// ── Default tool data ──────────────────────────────────────

	/**
	 * Return the full default 62-tool list for the tools directory.
	 * Each entry: [ icon, name, desc, href, category ]
	 *
	 * @return array<array<string,string>>
	 */
	private function default_tools(): array {
		return [

			/* PDF Tools */
			[
				'tool_icon' => 'PDF',
				'tool_name' => 'PDF Compressor',
				'tool_desc' => 'Compress PDF files online with preview, compression levels, and instant browser download',
				'tool_url'  => home_url( '/tools/pdf-compressor/' ),
				'tool_cat'  => 'PDF Tools',
			],
			[
				'tool_icon' => 'PDF',
				'tool_name' => 'PDF to Word Converter',
				'tool_desc' => 'Convert PDF to Word DOCX online — editable documents with a fast server-side converter',
				'tool_url'  => home_url( '/tools/pdf-to-word-converter/' ),
				'tool_cat'  => 'PDF Tools',
			],
			[
				'tool_icon' => '🗑️',
				'tool_name' => 'Delete PDF Pages',
				'tool_desc' => 'Delete PDF pages online — select and remove pages locally in your browser',
				'tool_url'  => home_url( '/tools/delete-pdf-pages/' ),
				'tool_cat'  => 'PDF Tools',
			],
			[
				'tool_icon' => '📄',
				'tool_name' => 'JPG to PDF',
				'tool_desc' => 'Convert multiple JPG images into one PDF online — free and private',
				'tool_url'  => home_url( '/tools/jpg-to-pdf-converter/' ),
				'tool_cat'  => 'PDF Tools',
			],
			[
				'tool_icon' => '📑',
				'tool_name' => 'PDF Merger',
				'tool_desc' => 'Merge multiple PDF files into one document online — free and secure',
				'tool_url'  => home_url( '/tools/pdf-merger/' ),
				'tool_cat'  => 'PDF Tools',
			],
			[
				'tool_icon' => '✂️',
				'tool_name' => 'PDF Splitter',
				'tool_desc' => 'Split a PDF online into multiple documents by range, pages, or file size',
				'tool_url'  => home_url( '/tools/pdf-splitter/' ),
				'tool_cat'  => 'PDF Tools',
			],
			[
				'tool_icon' => '🖼️',
				'tool_name' => 'PDF to JPG',
				'tool_desc' => 'Convert every PDF page to a JPG image online — locally in your browser',
				'tool_url'  => home_url( '/tools/pdf-to-jpg-converter/' ),
				'tool_cat'  => 'PDF Tools',
			],
			[
				'tool_icon' => '🟦',
				'tool_name' => 'PDF to PNG',
				'tool_desc' => 'Convert every PDF page to a PNG image online — locally in your browser',
				'tool_url'  => home_url( '/tools/pdf-to-png-converter/' ),
				'tool_cat'  => 'PDF Tools',
			],
			[
				'tool_icon' => '🔄',
				'tool_name' => 'Rotate PDF',
				'tool_desc' => 'Rotate PDF pages online — free browser-based PDF rotation tool',
				'tool_url'  => home_url( '/tools/rotate-pdf/' ),
				'tool_cat'  => 'PDF Tools',
			],

			/* ── Image Compression Tools ─────────────────────────────────────────── */
			[
				'tool_icon' => '🖼️',
				'tool_name' => 'JPG Compressor',
				'tool_desc' => 'Compress JPG images online with previews, tab cache, and ZIP batch download',
				'tool_url'  => home_url( '/tools/jpg-compressor/' ),
				'tool_cat'  => 'Image Compression Tools',
			],
			[
				'tool_icon' => '🟦',
				'tool_name' => 'PNG Compressor',
				'tool_desc' => 'Compress PNG images online with previews, tab cache, and ZIP batch download',
				'tool_url'  => home_url( '/tools/png-compressor/' ),
				'tool_cat'  => 'Image Compression Tools',
			],
			[
				'tool_icon' => '⚡',
				'tool_name' => 'WebP Compressor',
				'tool_desc' => 'Compress WebP images online with quality control, resizing, previews, and ZIP download',
				'tool_url'  => home_url( '/tools/webp-compressor/' ),
				'tool_cat'  => 'Image Compression Tools',
			],
			[
				'tool_icon' => '🎞️',
				'tool_name' => 'GIF Compressor',
				'tool_desc' => 'Compress GIF images online with previews, tab cache, and ZIP batch download',
				'tool_url'  => home_url( '/tools/gif-compressor/' ),
				'tool_cat'  => 'Image Compression Tools',
			],
			[
				'tool_icon' => 'SVG',
				'tool_name' => 'SVG Compressor',
				'tool_desc' => 'Compress SVG images online with previews, tab cache, and ZIP batch download',
				'tool_url'  => home_url( '/tools/svg-compressor/' ),
				'tool_cat'  => 'Image Compression Tools',
			],

			/* ── Image & Media Conversion Tools ────────────────────────────────────────────── */
			[
				'tool_icon' => '🎨',
				'tool_name' => 'PixelScript — ASCII Art Generator',
				'tool_desc' => 'Convert any image to detailed ASCII art online — free browser-based image to text art generator',
				'tool_url'  => home_url( '/tools/image-to-ascii-art/' ),
				'tool_cat'  => 'Image & Media Conversion Tools',
			],
			[
				'tool_icon' => '🔍',
				'tool_name' => 'TextLens — Image to Text',
				'tool_desc' => 'Extract text from images and photos using browser-based OCR — no uploads required',
				'tool_url'  => home_url( '/tools/image-to-text-ocr/' ),
				'tool_cat'  => 'Image & Media Conversion Tools',
			],
			[
				'tool_icon' => 'BG',
				'tool_name' => 'Remove Background from Image',
				'tool_desc' => 'Remove image backgrounds online and export transparent PNG files — free and private',
				'tool_url'  => home_url( '/tools/remove-background-from-image/' ),
				'tool_cat'  => 'Image & Media Conversion Tools',
			],
			[
				'tool_icon' => '📸',
				'tool_name' => 'SnapConvert — JPG to PNG',
				'tool_desc' => 'Convert JPG to PNG online — batch convert images to lossless PNG format',
				'tool_url'  => home_url( '/tools/jpg-to-png-converter/' ),
				'tool_cat'  => 'Image & Media Conversion Tools',
			],
			[
				'tool_icon' => '⚡',
				'tool_name' => 'SwiftWebP — JPG to WebP',
				'tool_desc' => 'Convert JPG to WebP online — create smaller, faster-loading web images instantly',
				'tool_url'  => home_url( '/tools/jpg-to-webp-converter/' ),
				'tool_cat'  => 'Image & Media Conversion Tools',
			],
			[
				'tool_icon' => '✏️',
				'tool_name' => 'VectorTrace - JPG to SVG',
				'tool_desc' => 'Convert JPG to SVG online — transform images into vector-style output',
				'tool_url'  => home_url( '/tools/jpg-to-svg-converter/' ),
				'tool_cat'  => 'Image & Media Conversion Tools',
			],
			[
				'tool_icon' => '🎞️',
				'tool_name' => 'MotionConvert - JPG to GIF',
				'tool_desc' => 'Convert JPG to GIF online — browser-based image format conversion tool',
				'tool_url'  => home_url( '/tools/jpg-to-gif-converter/' ),
				'tool_cat'  => 'Image & Media Conversion Tools',
			],
			[
				'tool_icon' => '📱',
				'tool_name' => 'AppleFrame - JPG to HEIC',
				'tool_desc' => 'Convert JPG to HEIC online — create Apple-compatible HEIC files from your images',
				'tool_url'  => home_url( '/tools/jpg-to-heic-converter/' ),
				'tool_cat'  => 'Image & Media Conversion Tools',
			],
			[
				'tool_icon' => '🌟',
				'tool_name' => 'AviForge - JPG to AVIF',
				'tool_desc' => 'Convert JPG to AVIF online — next-gen image format for better compression',
				'tool_url'  => home_url( '/tools/jpg-to-avif-converter/' ),
				'tool_cat'  => 'Image & Media Conversion Tools',
			],
			[
				'tool_icon' => '🖼️',
				'tool_name' => 'PhotoShift - PNG to JPG',
				'tool_desc' => 'Convert PNG to JPG online — transform images into compact, shareable JPEG files',
				'tool_url'  => home_url( '/tools/png-to-jpg-converter/' ),
				'tool_cat'  => 'Image & Media Conversion Tools',
			],
			[
				'tool_icon' => '⚡',
				'tool_name' => 'WebPForge - PNG to WebP',
				'tool_desc' => 'Convert PNG to WebP online — create efficient, modern web images',
				'tool_url'  => home_url( '/tools/png-to-webp-converter/' ),
				'tool_cat'  => 'Image & Media Conversion Tools',
			],
			[
				'tool_icon' => '✏️',
				'tool_name' => 'VectorLift - PNG to SVG',
				'tool_desc' => 'Convert PNG to SVG online — transform raster images into vector-style output',
				'tool_url'  => home_url( '/tools/png-to-svg-converter/' ),
				'tool_cat'  => 'Image & Media Conversion Tools',
			],
			[
				'tool_icon' => '📱',
				'tool_name' => 'AppleSnap - PNG to HEIC',
				'tool_desc' => 'Convert PNG to HEIC online — Apple-compatible HEIC image conversion tool',
				'tool_url'  => home_url( '/tools/png-to-heic-converter/' ),
				'tool_cat'  => 'Image & Media Conversion Tools',
			],
			[
				'tool_icon' => '🖼️',
				'tool_name' => 'PhotoConvert - HEIC to JPG',
				'tool_desc' => 'Convert HEIC to JPG online — transform iPhone HEIC photos into universal JPEG format',
				'tool_url'  => home_url( '/tools/heic-to-jpg-converter/' ),
				'tool_cat'  => 'Image & Media Conversion Tools',
			],
			[
				'tool_icon' => '🟦',
				'tool_name' => 'PixelConvert - HEIC to PNG',
				'tool_desc' => 'Convert HEIC to PNG online — transform iPhone HEIC photos into lossless PNG format',
				'tool_url'  => home_url( '/tools/heic-to-png-converter/' ),
				'tool_cat'  => 'Image & Media Conversion Tools',
			],
			[
				'tool_icon' => '✏️',
				'tool_name' => 'VectorConvert - HEIC to SVG',
				'tool_desc' => 'Convert HEIC to SVG online — transform iPhone HEIC photos into SVG wrapper files',
				'tool_url'  => home_url( '/tools/heic-to-svg-converter/' ),
				'tool_cat'  => 'Image & Media Conversion Tools',
			],
			[
				'tool_icon' => '🖼️',
				'tool_name' => 'PhotoRestore - WebP to JPG',
				'tool_desc' => 'Convert WebP to JPG online — restore WebP images back to standard JPEG format',
				'tool_url'  => home_url( '/tools/webp-to-jpg-converter/' ),
				'tool_cat'  => 'Image & Media Conversion Tools',
			],
			[
				'tool_icon' => '🟦',
				'tool_name' => 'PixelRestore - WebP to PNG',
				'tool_desc' => 'Convert WebP to PNG online — restore WebP images to lossless PNG format',
				'tool_url'  => home_url( '/tools/webp-to-png-converter/' ),
				'tool_cat'  => 'Image & Media Conversion Tools',
			],
			[
				'tool_icon' => '🎬',
				'tool_name' => 'ClipShift — Video Converter',
				'tool_desc' => 'Convert videos online between MP4, WebM, AVI, MOV and more — free browser-based converter',
				'tool_url'  => home_url( '/' ),
				'tool_cat' => 'Image & Media Conversion Tools',
			],
			[
				'tool_icon' => '📄',
				'tool_name' => 'PNG to PDF',
				'tool_desc' => 'Convert multiple PNG images into one PDF online — free browser-based tool',
				'tool_url'  => home_url( '/tools/png-to-pdf-converter/' ),
				'tool_cat'  => 'Image & Media Conversion Tools',
			],

			/* ── Case Conversion Tools ──────────────────────────────────────────── */
			[
				'tool_icon' => '🔤',
				'tool_name' => 'Case Converter',
				'tool_desc' => 'Convert text between UPPERCASE, lowercase, Title Case, and Sentence case — free online case converter',
				'tool_url'  => home_url( '/tools/case-converter/' ),
				'tool_cat'  => 'Case Conversion Tools',
			],
			[
				'tool_icon' => '🔠',
				'tool_name' => 'UPPERCASE Converter',
				'tool_desc' => 'Convert all text to UPPERCASE instantly — free online uppercase converter',
				'tool_url'  => home_url( '/' ),
				'tool_cat'  => 'Case Conversion Tools',
			],
			[
				'tool_icon' => '🔡',
				'tool_name' => 'lowercase converter',
				'tool_desc' => 'Convert all text to lowercase instantly — free online lowercase converter',
				'tool_url'  => home_url( '/' ),
				'tool_cat'  => 'Case Conversion Tools',
			],
			[
				'tool_icon' => '📝',
				'tool_name' => 'Sentence Case Converter',
				'tool_desc' => 'Capitalize the first letter of each sentence — free online sentence case converter',
				'tool_url'  => home_url( '/tools/sentence-case-converter/' ),
				'tool_cat'  => 'Case Conversion Tools',
			],
			[
				'tool_icon' => '📰',
				'tool_name' => 'Title Case Converter',
				'tool_desc' => 'Apply proper AP, APA, Chicago, and MLA title case rules — free online title case converter',
				'tool_url'  => home_url( '/tools/title-case-converter/' ),
				'tool_cat'  => 'Case Conversion Tools',
			],
			[
				'tool_icon' => '🅰️',
				'tool_name' => 'Capitalized Case',
				'tool_desc' => 'Capitalize the first letter of every word — free online capitalized case converter',
				'tool_url'  => home_url( '/' ),
				'tool_cat'  => 'Case Conversion Tools',
			],
			[
				'tool_icon' => '🔀',
				'tool_name' => 'Alternating Case',
				'tool_desc' => 'Convert text to aLtErNaTiNg CaSe — free online alternating case converter for fun text',
				'tool_url'  => home_url( '/' ),
				'tool_cat'  => 'Case Conversion Tools',
			],
			[
				'tool_icon' => '🔁',
				'tool_name' => 'Inverse Case',
				'tool_desc' => 'Flip the case of every character — free online inverse case converter',
				'tool_url'  => home_url( '/' ),
				'tool_cat'  => 'Case Conversion Tools',
			],

			/* ── Text Cleaning Tools ────────────────────────────────────────────── */
			[
				'tool_icon' => '✂️',
				'tool_name' => 'Character Remover',
				'tool_desc' => 'Remove any specific character from text — free online character remover tool',
				'tool_url'  => home_url( '/tools/character-remover/' ),
				'tool_cat'  => 'Text Cleaning Tools',
			],
			[
				'tool_icon' => '📋',
				'tool_name' => 'Duplicate Line Remover',
				'tool_desc' => 'Remove duplicate lines from a list — free online duplicate line remover',
				'tool_url'  => home_url( '/tools/duplicate-line-remover/' ),
				'tool_cat'  => 'Text Cleaning Tools',
			],
			[
				'tool_icon' => '🔍',
				'tool_name' => 'Duplicate Word Finder',
				'tool_desc' => 'Find and highlight repeated words in your text — free online duplicate word finder',
				'tool_url'  => home_url( '/tools/duplicate-word-finder/' ),
				'tool_cat'  => 'Text Cleaning Tools',
			],
			[
				'tool_icon' => '➖',
				'tool_name' => 'Em Dash Remover',
				'tool_desc' => 'Remove or replace em and en dashes in your text — free online dash remover',
				'tool_url'  => home_url( '/tools/em-dash-remover/' ),
				'tool_cat'  => 'Text Cleaning Tools',
			],
			[
				'tool_icon' => '↩️',
				'tool_name' => 'Remove Line Breaks',
				'tool_desc' => 'Strip line breaks and join lines together — free online line break remover',
				'tool_url'  => home_url( '/tools/remove-line-breaks/' ),
				'tool_cat'  => 'Text Cleaning Tools',
			],
			[
				'tool_icon' => '🗑️',
				'tool_name' => 'Remove Text Formatting',
				'tool_desc' => 'Strip Unicode bold, italic, and cursive styling — free online text formatting remover',
				'tool_url'  => home_url( '/tools/remove-text-formatting/' ),
				'tool_cat'  => 'Text Cleaning Tools',
			],
			[
				'tool_icon' => '〰️',
				'tool_name' => 'Remove Underscores',
				'tool_desc' => 'Remove or replace underscores in your text — free online underscore remover tool',
				'tool_url'  => home_url( '/tools/remove-underscores/' ),
				'tool_cat'  => 'Text Cleaning Tools',
			],
			[
				'tool_icon' => '⬜',
				'tool_name' => 'Whitespace Remover',
				'tool_desc' => 'Remove extra spaces, tabs, and whitespace — free online whitespace remover',
				'tool_url'  => home_url( '/tools/whitespace-remover/' ),
				'tool_cat'  => 'Text Cleaning Tools',
			],
			[
				'tool_icon' => '📄',
				'tool_name' => 'Plain Text Converter',
				'tool_desc' => 'Strip HTML tags and rich text formatting — free online plain text converter',
				'tool_url'  => home_url( '/tools/plain-text-converter/' ),
				'tool_cat'  => 'Text Cleaning Tools',
			],

			/* ── Text Generators & Writing Tools ──────────────────────────────────────────── */
			[
				'tool_icon' => '📚',
				'tool_name' => 'APA Format Generator',
				'tool_desc' => 'Generate APA 7th edition citations and references — free online citation generator',
				'tool_url'  => home_url( '/tools/apa-format-generator/' ),
				'tool_cat'  => 'Text Generators & Writing Tools',
			],
			[
				'tool_icon' => '👻',
				'tool_name' => 'Invisible Text Generator',
				'tool_desc' => 'Generate invisible Unicode characters — free online invisible text generator tool',
				'tool_url'  => home_url( '/tools/invisible-text-generator/' ),
				'tool_cat'  => 'Text Generators & Writing Tools',
			],
			[
				'tool_icon' => '📓',
				'tool_name' => 'Online Notepad',
				'tool_desc' => 'Free online notepad with auto-save — write notes in your browser with rich text support',
				'tool_url'  => home_url( '/tools/online-notepad/' ),
				'tool_cat'  => 'Text Generators & Writing Tools',
			],
			[
				'tool_icon' => '🔂',
				'tool_name' => 'Repeat Text Generator',
				'tool_desc' => 'Repeat any text any number of times — free online text repeater generator',
				'tool_url'  => home_url( '/tools/repeat-text-generator/' ),
				'tool_cat'  => 'Text Generators & Writing Tools',
			],
			[
				'tool_icon' => '↔️',
				'tool_name' => 'Reverse Text Generator',
				'tool_desc' => 'Reverse characters, words, or lines of text — free online reverse text generator',
				'tool_url'  => home_url( '/tools/reverse-text-generator/' ),
				'tool_cat'  => 'Text Generators & Writing Tools',
			],
			[
				'tool_icon' => '🏛️',
				'tool_name' => 'Roman Numeral Dates',
				'tool_desc' => 'Convert dates and numbers to Roman numerals — free online Roman numeral converter',
				'tool_url'  => home_url( '/tools/roman-numeral-dates/' ),
				'tool_cat'  => 'Text Generators & Writing Tools',
			],
			[
				'tool_icon' => '☁️',
				'tool_name' => 'Word Cloud Generator',
				'tool_desc' => 'Create beautiful word clouds from any text — free online word cloud generator',
				'tool_url'  => home_url( '/tools/word-cloud-generator/' ),
				'tool_cat'  => 'Text Generators & Writing Tools',
			],

			/* ── Random Generators ────────────────────────────────────────── */
			[
				'tool_icon' => '🎯',
				'tool_name' => 'SpinPick — Choice Picker',
				'tool_desc' => 'Randomly pick from any list of choices — free online random choice picker tool',
				'tool_url'  => home_url( '/tools/spinpick-choice-picker/' ),
				'tool_cat'  => 'Random Generators',
			],
			[
				'tool_icon' => '📅',
				'tool_name' => 'DateForge — Date Generator',
				'tool_desc' => 'Generate random dates between any two dates — free online random date generator',
				'tool_url'  => home_url( '/tools/random-date-generator/' ),
				'tool_cat'  => 'Random Generators',
			],
			[
				'tool_icon' => '🌐',
				'tool_name' => 'IPForge — IP Generator',
				'tool_desc' => 'Generate random IPv4 and IPv6 addresses in bulk — free online IP generator tool',
				'tool_url'  => home_url( '/tools/random-ip-generator/' ),
				'tool_cat'  => 'Random Generators',
			],
			[
				'tool_icon' => '🔠',
				'tool_name' => 'LetterDraw — Letter Generator',
				'tool_desc' => 'Generate random letters with case, type, and frequency controls — free online letter generator',
				'tool_url'  => home_url( '/tools/random-letter-generator/' ),
				'tool_cat'  => 'Random Generators',
			],
			[
				'tool_icon' => '🗓️',
				'tool_name' => 'MonthSpin — Month Generator',
				'tool_desc' => 'Pick random months filtered by season or quarter — free online month generator tool',
				'tool_url'  => home_url( '/tools/random-month-generator/' ),
				'tool_cat'  => 'Random Generators',
			],
			[
				'tool_icon' => '🔢',
				'tool_name' => 'NumForge — Number Generator',
				'tool_desc' => 'Generate random integers, decimals, or multiples in any range — free online number generator',
				'tool_url'  => home_url( '/tools/random-number-generator/' ),
				'tool_cat'  => 'Random Generators',
			],
			[
				'tool_icon' => '🔐',
				'tool_name' => 'VaultKey — Password Generator',
				'tool_desc' => 'Create cryptographically secure passwords with a live strength meter — free online password generator',
				'tool_url'  => home_url( '/tools/password-generator/' ),
				'tool_cat'  => 'Random Generators',
			],
			[
				'tool_icon' => '🪪',
				'tool_name' => 'UniqueForge — UUID Generator',
				'tool_desc' => 'Generate UUID v1, v4, v5, ULID, and NanoID identifiers in bulk — free online UUID generator',
				'tool_url'  => home_url( '/tools/uuid-generator/' ),
				'tool_cat'  => 'Random Generators',
			],

			/* ── Text Translators & Counters ───────────────────────────────────── */
			[
				'tool_icon' => '🔎',
				'tool_name' => 'Find and Replace Text',
				'tool_desc' => 'Find and replace text with regex support — free online find and replace tool',
				'tool_url'  => home_url( '/tools/find-and-replace-text/' ),
				'tool_cat'  => 'Text Translators & Counters',
			],
			[
				'tool_icon' => '🪖',
				'tool_name' => 'NATO Phonetic Alphabet',
				'tool_desc' => 'Translate text to NATO phonetic alphabet (Alpha, Bravo, Charlie…) — free online translator',
				'tool_url'  => home_url( '/tools/nato-phonetic-alphabet/' ),
				'tool_cat'  => 'Text Translators & Counters',
			],
			[
				'tool_icon' => '🔢',
				'tool_name' => 'Online Sentence Counter',
				'tool_desc' => 'Count sentences, words, characters, and reading time — free online text counter tool',
				'tool_url'  => home_url( '/tools/online-sentence-counter/' ),
				'tool_cat'  => 'Text Translators & Counters',
			],
			[
				'tool_icon' => '🔊',
				'tool_name' => 'Phonetic Spelling Tool',
				'tool_desc' => 'Convert words to phonetic spelling — free online phonetic transcription tool',
				'tool_url'  => home_url( '/tools/phonetic-spelling-tool/' ),
				'tool_cat'  => 'Text Translators & Counters',
			],
			[
				'tool_icon' => '🐷',
				'tool_name' => 'Pig Latin Translator',
				'tool_desc' => 'Translate English to Pig Latin — free online Pig Latin translator tool',
				'tool_url'  => home_url( '/tools/pig-latin-translator/' ),
				'tool_cat'  => 'Text Translators & Counters',
			],
			[
				'tool_icon' => '🔤',
				'tool_name' => 'Sort Words Alphabetically',
				'tool_desc' => 'Sort words or lines A–Z, Z–A, or by length — free online text sorter tool',
				'tool_url'  => home_url( '/tools/sort-words-alphabetically/' ),
				'tool_cat'  => 'Text Translators & Counters',
			],
			[
				'tool_icon' => '✡️',
				'tool_name' => 'Wingdings Translator',
				'tool_desc' => 'Convert text to Wingdings and back — free online Wingdings translator tool',
				'tool_url'  => home_url( '/tools/wingdings-translator/' ),
				'tool_cat'  => 'Text Translators & Counters',
			],
			[
				'tool_icon' => '📊',
				'tool_name' => 'Word Frequency Counter',
				'tool_desc' => 'Count word frequency in any text with sortable results — free online word counter tool',
				'tool_url'  => home_url( '/tools/word-frequency-counter/' ),
				'tool_cat'  => 'Text Translators & Counters',
			],
		]; // end default_tools()
	}

	// ── Controls ──────────────────────────────────────────────

	protected function register_controls(): void {

		/* ── Content › Hero ─────────────────────────────────── */
		$this->start_controls_section(
			'section_hero',
			[
				'label' => esc_html__( 'Page Hero', 'textcraft-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'hero_badge',
			[
				'label'   => esc_html__( 'Badge Text', 'textcraft-tools' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( '74 Free Online Text Utilities & Tools', 'textcraft-tools' ),
			]
		);

		$this->add_control(
			'hero_title_plain',
			[
				'label'       => esc_html__( 'Title (plain part)', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'All', 'textcraft-tools' ),
				'description' => esc_html__( 'Rendered as plain text before the gradient word.', 'textcraft-tools' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'hero_title_gradient',
			[
				'label'       => esc_html__( 'Title (gradient word)', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Tools', 'textcraft-tools' ),
				'description' => esc_html__( 'Rendered with the accent gradient after the plain text.', 'textcraft-tools' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'hero_subtitle',
			[
				'label'   => esc_html__( 'Subtitle', 'textcraft-tools' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'A complete collection of free online text utilities, random generators, and browser-based tools. No account needed, no ads, and no data ever leaves your device — 100% private and secure.', 'textcraft-tools' ),
				'rows'    => 3,
			]
		);

		$this->add_control(
			'show_hero',
			[
				'label'        => esc_html__( 'Show Hero', 'textcraft-tools' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'textcraft-tools' ),
				'label_off'    => esc_html__( 'No', 'textcraft-tools' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->end_controls_section();

		/* ── Content › Search ───────────────────────────────── */
		$this->start_controls_section(
			'section_search',
			[
				'label' => esc_html__( 'Search Bar', 'textcraft-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_search',
			[
				'label'        => esc_html__( 'Show Search Bar', 'textcraft-tools' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'search_placeholder',
			[
				'label'     => esc_html__( 'Placeholder Text', 'textcraft-tools' ),
				'type'      => Controls_Manager::TEXT,
'default' => esc_html__( 'Search free online tools…', 'textcraft-tools' ),
				'condition' => [ 'show_search' => 'yes' ],
			]
		);

		$this->add_control(
			'no_results_title',
			[
				'label'   => esc_html__( 'No Results Title', 'textcraft-tools' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'No matching tools found', 'textcraft-tools' ),
			]
		);

		$this->add_control(
			'no_results_hint',
			[
				'label'   => esc_html__( 'No Results Hint', 'textcraft-tools' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Try a different search term or browse the categories below', 'textcraft-tools' ),
			]
		);

		$this->end_controls_section();

		/* ── Content › Tools (Repeater) ─────────────────────── */
		$this->start_controls_section(
			'section_tools',
			[
				'label' => esc_html__( 'Tools List', 'textcraft-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'tools_info',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => '<div class="tc-p-10-12 tc-text-12 tc-text-muted tc-info-box">'
					. '<strong class="tc-text-purple">74 tools pre-loaded.</strong> Edit any card below, or add new tools. '
					. 'The <strong>Category</strong> field groups cards under the matching heading. '
					. 'Leave URL blank to disable the link.'
					. '</div>',
				'content_classes' => 'tc-repeater-info',
			]
		);

		// ── Repeater definition ───────────────────────────────
		$repeater = new Repeater();

		$repeater->add_control(
			'tool_icon',
			[
				'label'   => esc_html__( 'Icon / Emoji', 'textcraft-tools' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '🔠',
			]
		);

		$repeater->add_control(
			'tool_name',
			[
				'label'       => esc_html__( 'Tool Name', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Tool Name', 'textcraft-tools' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'tool_desc',
			[
				'label'       => esc_html__( 'Short Description', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'What this tool does.', 'textcraft-tools' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'tool_url',
			[
				'label'         => esc_html__( 'Tool URL', 'textcraft-tools' ),
				'type'          => Controls_Manager::TEXT,
				'default'       => '',
				'placeholder'   => 'https://example.com/tools/my-tool/',
				'label_block'   => true,
				'description'   => esc_html__( 'Full URL of the tool page. Leave blank to disable the link.', 'textcraft-tools' ),
			]
		);

		$repeater->add_control(
			'tool_cat',
			[
				'label'       => esc_html__( 'Category', 'textcraft-tools' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'Case Conversion Tools',
				'options'     => [
					'PDF Tools'                    => esc_html__( 'PDF Tools',                    'textcraft-tools' ),
					'Image Compression Tools'      => esc_html__( 'Image Compression Tools',      'textcraft-tools' ),
					'Image & Media Conversion Tools'         => esc_html__( 'Image & Media Conversion Tools',         'textcraft-tools' ),
					'Case Conversion Tools'       => esc_html__( 'Case Conversion Tools',       'textcraft-tools' ),
					'Text Cleaning Tools'         => esc_html__( 'Text Cleaning Tools',         'textcraft-tools' ),
					'Text Generators & Writing Tools'       => esc_html__( 'Text Generators & Writing Tools',       'textcraft-tools' ),
					'Random Generators'     => esc_html__( 'Random Generators',     'textcraft-tools' ),
					'Text Translators & Counters'=> esc_html__( 'Text Translators & Counters','textcraft-tools' ),
				],
				'label_block' => true,
			]
		);

		$this->add_control(
			'tools_list',
			[
				'label'       => esc_html__( 'Tools', 'textcraft-tools' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $this->default_tools(),
				'title_field' => '{{{ tool_icon }}} {{{ tool_name }}}',
			]
		);

		$this->end_controls_section();

		/* ── Content › Premium SEO Content ──────────────────── */
		$this->start_controls_section(
			'section_premium_seo',
			[
				'label' => esc_html__( 'Premium SEO Content', 'textcraft-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_premium_section',
			[
				'label'        => esc_html__( 'Show Premium Section', 'textcraft-tools' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'textcraft-tools' ),
				'label_off'    => esc_html__( 'No', 'textcraft-tools' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		/* ── Premium Hero ──────────────────────────────────── */
		$this->add_control(
			'pm_hero_heading',
			[
				'label'     => esc_html__( 'Hero Card', 'textcraft-tools' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [ 'show_premium_section' => 'yes' ],
			]
		);

		$this->add_control(
			'pm_hero_badge',
			[
				'label'     => esc_html__( 'Badge Text', 'textcraft-tools' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Over 65 Free Tools', 'textcraft-tools' ),
				'condition' => [ 'show_premium_section' => 'yes' ],
			]
		);

		$this->add_control(
			'pm_hero_title',
			[
				'label'       => esc_html__( 'Title', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Free Online Tools for Every Task', 'textcraft-tools' ),
				'label_block' => true,
				'condition'   => [ 'show_premium_section' => 'yes' ],
			]
		);

		$this->add_control(
			'pm_hero_intro',
			[
				'label'     => esc_html__( 'Intro Paragraph', 'textcraft-tools' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => esc_html__( 'TextCraft Tools brings you over 65 free online tools that work entirely in your browser. Whether you need to convert text case, compress images, edit PDFs, generate random data, or clean up text formatting, you will find the right tool here. All of our utilities run client-side — your files and text never leave your device, ensuring complete privacy and security.', 'textcraft-tools' ),
				'rows'      => 5,
				'condition' => [ 'show_premium_section' => 'yes' ],
			]
		);

		// ── Stats repeater ───────────────────────────────────
		$pm_stat_repeater = new Repeater();

		$pm_stat_repeater->add_control(
			'stat_number',
			[
				'label'   => esc_html__( 'Stat Number/Text', 'textcraft-tools' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '65+',
			]
		);

		$pm_stat_repeater->add_control(
			'stat_label',
			[
				'label'   => esc_html__( 'Stat Label', 'textcraft-tools' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Free Tools',
			]
		);

		$this->add_control(
			'pm_hero_stats',
			[
				'label'       => esc_html__( 'Stats', 'textcraft-tools' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $pm_stat_repeater->get_controls(),
				'default'     => [
					[ 'stat_number' => '65+',  'stat_label' => 'Free Tools'   ],
					[ 'stat_number' => '100%', 'stat_label' => 'Private'      ],
					[ 'stat_number' => 'No Signup', 'stat_label' => 'Required' ],
				],
				'title_field' => '{{{ stat_number }}} — {{{ stat_label }}}',
				'condition'   => [ 'show_premium_section' => 'yes' ],
			]
		);

		/* ── Feature Grid ──────────────────────────────────── */
		$this->add_control(
			'pm_features_heading',
			[
				'label'     => esc_html__( 'Feature Cards (Category Grid)', 'textcraft-tools' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [ 'show_premium_section' => 'yes' ],
			]
		);

		$pm_feature_repeater = new Repeater();

		$pm_feature_repeater->add_control(
			'icon',
			[
				'label'   => esc_html__( 'Icon / Emoji', 'textcraft-tools' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '📄',
			]
		);

		$pm_feature_repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Feature Title', 'textcraft-tools' ),
				'label_block' => true,
			]
		);

		$pm_feature_repeater->add_control(
			'desc',
			[
				'label'       => esc_html__( 'Description', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Feature description.', 'textcraft-tools' ),
				'rows'        => 4,
				'label_block' => true,
			]
		);

		$this->add_control(
			'pm_feature_cards',
			[
				'label'       => esc_html__( 'Feature Cards', 'textcraft-tools' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $pm_feature_repeater->get_controls(),
				'default'     => [
					[
						'icon'  => '📄',
						'title' => esc_html__( 'PDF Tools for Document Management', 'textcraft-tools' ),
						'desc'  => esc_html__( 'Our PDF tool suite includes everything you need to manage documents online. Compress PDF files to reduce file size for email attachments. Convert PDF to Word format when you need to edit content. Merge multiple PDFs into one document for organized reporting. Split large PDFs into smaller files. Rotate pages and delete unwanted sections. All PDF processing respects your privacy — files stay on your computer throughout the entire operation.', 'textcraft-tools' ),
					],
					[
						'icon'  => '🖼️',
						'title' => esc_html__( 'Image Compression and Conversion', 'textcraft-tools' ),
						'desc'  => esc_html__( 'Optimize your images with our compression tools for JPG, PNG, WebP, GIF, and SVG formats. Reduce file sizes while maintaining visual quality, perfect for website optimization and faster page loads. Our image converters handle all common format transitions including JPG to PNG, WebP to JPG, HEIC to PNG, and many more. Remove backgrounds from images, extract text with OCR, and even convert images to ASCII art — all without uploading to any server.', 'textcraft-tools' ),
					],
					[
						'icon'  => '📝',
						'title' => esc_html__( 'Text Case Conversion and Formatting', 'textcraft-tools' ),
						'desc'  => esc_html__( 'Transform text between different cases with our case conversion tools. While dedicated pages for specific case types are available, the main tools directory provides quick access to text cleaning utilities including character remover, duplicate line finder, whitespace remover, and plain text converter. These tools are essential for writers, editors, and data processors who need clean, consistently formatted text.', 'textcraft-tools' ),
					],
					[
						'icon'  => '✏️',
						'title' => esc_html__( 'Text Generators and Writing Aids', 'textcraft-tools' ),
						'desc'  => esc_html__( 'Our writing tools help you create content more effectively. Generate APA format citations for academic work. Create word clouds to visualize text patterns. Use the online notepad for quick notes with auto-save. Reverse text, repeat strings, convert dates to Roman numerals — each tool serves a specific purpose while maintaining the same commitment to privacy and ease of use.', 'textcraft-tools' ),
					],
					[
						'icon'  => '🎲',
						'title' => esc_html__( 'Random Generators for Data and Decision Making', 'textcraft-tools' ),
						'desc'  => esc_html__( 'Need random data for testing or development? Our random generators produce numbers, dates, IP addresses, letters, months, UUIDs, and secure passwords. The SpinPick choice picker makes group decisions fun with its interactive wheel. Generate strong passwords with customizable character sets. These tools are widely used by developers, testers, educators, and anyone needing reliable random data generation.', 'textcraft-tools' ),
					],
					[
						'icon'  => '🔍',
						'title' => esc_html__( 'Text Analysis and Translation Tools', 'textcraft-tools' ),
						'desc'  => esc_html__( 'Analyze and transform text with our specialized tools. Count word frequency to identify overused terms in your writing. Use the NATO phonetic alphabet translator for clear communication. Sort words alphabetically, translate to Pig Latin, decode Wingdings symbols, and convert text to phonetic spelling. The find and replace tool supports regular expressions for advanced text manipulation.', 'textcraft-tools' ),
					],
				],
				'title_field' => '{{{ icon }}} {{{ title }}}',
				'condition'   => [ 'show_premium_section' => 'yes' ],
			]
		);

		/* ── Media Section ─────────────────────────────────── */
		$this->add_control(
			'pm_media_heading',
			[
				'label'     => esc_html__( 'Media Section (Image + Text)', 'textcraft-tools' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [ 'show_premium_section' => 'yes' ],
			]
		);

		$this->add_control(
			'pm_media_image',
			[
				'label'   => esc_html__( 'Image', 'textcraft-tools' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [ 'url' => '', 'id' => '' ],
				'condition' => [ 'show_premium_section' => 'yes' ],
			]
		);

		$this->add_control(
			'pm_media_emoji',
			[
				'label'       => esc_html__( 'Fallback Emoji (when no image)', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '🛠️',
				'description' => esc_html__( 'Shown when no image is selected.', 'textcraft-tools' ),
				'condition'   => [ 'show_premium_section' => 'yes' ],
			]
		);

		$this->add_control(
			'pm_media_title',
			[
				'label'       => esc_html__( 'Title', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Your All-in-One Free Online Toolkit', 'textcraft-tools' ),
				'label_block' => true,
				'condition'   => [ 'show_premium_section' => 'yes' ],
			]
		);

		$this->add_control(
			'pm_media_desc',
			[
				'label'     => esc_html__( 'Description', 'textcraft-tools' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => esc_html__( 'TextCraft Tools combines over 65 free online utilities in one privacy-focused platform. From PDF management and image conversion to text analysis and random data generation, every tool runs entirely in your browser. No uploads, no accounts, no data leaving your device. Whether you are a student formatting citations, a developer testing inputs, or a content creator optimizing images, this toolkit provides the functionality you need with the privacy you deserve.', 'textcraft-tools' ),
				'rows'      => 5,
				'condition' => [ 'show_premium_section' => 'yes' ],
			]
		);

		/* ── Benefit Cards ─────────────────────────────────── */
		$this->add_control(
			'pm_benefits_heading',
			[
				'label'     => esc_html__( 'Benefit Cards', 'textcraft-tools' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [ 'show_premium_section' => 'yes' ],
			]
		);

		$pm_benefit_repeater = new Repeater();

		$pm_benefit_repeater->add_control(
			'icon',
			[
				'label'   => esc_html__( 'Icon / Emoji', 'textcraft-tools' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '🔒',
			]
		);

		$pm_benefit_repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Benefit Title', 'textcraft-tools' ),
				'label_block' => true,
			]
		);

		$pm_benefit_repeater->add_control(
			'desc',
			[
				'label'       => esc_html__( 'Description', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Benefit description.', 'textcraft-tools' ),
				'rows'        => 3,
				'label_block' => true,
			]
		);

		$this->add_control(
			'pm_benefit_cards',
			[
				'label'       => esc_html__( 'Benefit Cards', 'textcraft-tools' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $pm_benefit_repeater->get_controls(),
				'default'     => [
					[
						'icon'  => '🔒',
						'title' => esc_html__( '100% Private &amp; Secure', 'textcraft-tools' ),
						'desc'  => esc_html__( 'All processing happens locally in your browser. No files, text, or personal information is uploaded to any server. Your data never leaves your device.', 'textcraft-tools' ),
					],
					[
						'icon'  => '⚡',
						'title' => esc_html__( 'Fast &amp; Lightweight', 'textcraft-tools' ),
						'desc'  => esc_html__( 'No downloads, installations, or accounts needed. Simply open your browser and start using any tool instantly. Optimized for speed and low resource usage.', 'textcraft-tools' ),
					],
					[
						'icon'  => '🌐',
						'title' => esc_html__( 'Works Everywhere', 'textcraft-tools' ),
						'desc'  => esc_html__( 'Every tool works on desktop, tablet, and mobile devices. The responsive interface adapts to your screen size for a consistent experience.', 'textcraft-tools' ),
					],
					[
						'icon'  => '🎯',
						'title' => esc_html__( 'No Limits', 'textcraft-tools' ),
						'desc'  => esc_html__( 'Use every tool as much as you need with no restrictions, no usage caps, and no premium tiers. All features are completely free forever.', 'textcraft-tools' ),
					],
				],
				'title_field' => '{{{ icon }}} {{{ title }}}',
				'condition'   => [ 'show_premium_section' => 'yes' ],
			]
		);

		/* ── Highlight / CTA ───────────────────────────────── */
		$this->add_control(
			'pm_highlight_heading',
			[
				'label'     => esc_html__( 'Highlight / CTA Card', 'textcraft-tools' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [ 'show_premium_section' => 'yes' ],
			]
		);

		$this->add_control(
			'pm_highlight_icon',
			[
				'label'   => esc_html__( 'Icon / Emoji', 'textcraft-tools' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '🛡️',
				'condition' => [ 'show_premium_section' => 'yes' ],
			]
		);

		$this->add_control(
			'pm_highlight_title',
			[
				'label'       => esc_html__( 'Title', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Privacy and Security First', 'textcraft-tools' ),
				'label_block' => true,
				'condition'   => [ 'show_premium_section' => 'yes' ],
			]
		);

		$this->add_control(
			'pm_highlight_desc',
			[
				'label'     => esc_html__( 'Description', 'textcraft-tools' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => esc_html__( 'Every tool on TextCraft Tools processes data locally in your browser using JavaScript and WebAssembly. No files, text, or personal information is uploaded to any server. This approach guarantees your privacy and makes our tools suitable for handling confidential documents, personal data, and sensitive information. There are no accounts, no tracking, and no data retention — what you process stays on your device.', 'textcraft-tools' ),
				'rows'      => 5,
				'condition' => [ 'show_premium_section' => 'yes' ],
			]
		);

		/* ── FAQ Accordion ─────────────────────────────────── */
		$this->add_control(
			'pm_faq_heading',
			[
				'label'     => esc_html__( 'FAQ Accordion', 'textcraft-tools' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [ 'show_premium_section' => 'yes' ],
			]
		);

		$this->add_control(
			'pm_faq_title',
			[
				'label'     => esc_html__( 'FAQ Section Title', 'textcraft-tools' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Frequently Asked Questions', 'textcraft-tools' ),
				'condition' => [ 'show_premium_section' => 'yes' ],
			]
		);

		$pm_faq_repeater = new Repeater();

		$pm_faq_repeater->add_control(
			'question',
			[
				'label'       => esc_html__( 'Question', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Are these tools really free?', 'textcraft-tools' ),
				'label_block' => true,
			]
		);

		$pm_faq_repeater->add_control(
			'answer',
			[
				'label'       => esc_html__( 'Answer', 'textcraft-tools' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Yes, every tool is completely free to use.', 'textcraft-tools' ),
				'rows'        => 3,
				'label_block' => true,
			]
		);

		$this->add_control(
			'pm_faq_items',
			[
				'label'       => esc_html__( 'FAQ Items', 'textcraft-tools' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $pm_faq_repeater->get_controls(),
				'default'     => [
					[
						'question' => esc_html__( 'Are these tools really free?', 'textcraft-tools' ),
						'answer'   => esc_html__( 'Yes, every tool on this page is completely free to use with no hidden charges, premium tiers, or usage limits. There are no signup requirements, subscriptions, or paywalls of any kind.', 'textcraft-tools' ),
					],
					[
						'question' => esc_html__( 'Do you store my data or files?', 'textcraft-tools' ),
						'answer'   => esc_html__( 'No. All processing happens locally in your browser. Your text, images, PDFs, and any other data you process never leave your device. We do not store, transmit, or have access to your content.', 'textcraft-tools' ),
					],
					[
						'question' => esc_html__( 'Can I use these tools on my phone or tablet?', 'textcraft-tools' ),
						'answer'   => esc_html__( 'Yes, all tools work on mobile devices, tablets, and desktop computers. The responsive design ensures a consistent experience across screen sizes. Most tools support touch input for file selection and interaction.', 'textcraft-tools' ),
					],
					[
						'question' => esc_html__( 'What file formats are supported?', 'textcraft-tools' ),
						'answer'   => esc_html__( 'Our tools support a wide range of formats including PDF, DOCX, JPG, PNG, WebP, GIF, SVG, HEIC, AVIF, TXT, and more. The specific formats depend on the individual tool you are using.', 'textcraft-tools' ),
					],
					[
						'question' => esc_html__( 'Is there a limit on file size or text length?', 'textcraft-tools' ),
						'answer'   => esc_html__( 'Most tools can handle large files and text blocks without issues. Performance depends on your device specifications and browser capabilities rather than server-side restrictions since everything runs locally.', 'textcraft-tools' ),
					],
				],
				'title_field' => '{{{ question }}}',
				'condition'   => [ 'show_premium_section' => 'yes' ],
			]
		);

		$this->end_controls_section();

		/* ── Style › Hero ────────────────────────────────────── */
		$this->start_controls_section(
			'style_hero',
			[
				'label'     => esc_html__( 'Hero', 'textcraft-tools' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_hero' => 'yes' ],
			]
		);

		$this->add_control(
			'hero_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .tc-atp-hero-title' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'hero_subtitle_color',
			[
				'label'     => esc_html__( 'Subtitle Color', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#a8997d',
				'selectors' => [ '{{WRAPPER}} .tc-atp-hero-subtitle' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'accent_color_start',
			[
				'label'     => esc_html__( 'Gradient Start', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d4a24c',
				'selectors' => [
					'{{WRAPPER}} .tc-accent-gradient' =>
						'background: linear-gradient(120deg, {{VALUE}} 0%, var(--tc-grad-end, #b8860b) 100%);'
						. '-webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;',
				],
			]
		);

		$this->add_control(
			'accent_color_end',
			[
				'label'     => esc_html__( 'Gradient End', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#b8860b',
				'selectors' => [
					'{{WRAPPER}} .tc-accent-gradient' =>
						'background: linear-gradient(120deg, var(--tc-grad-start, #d4a24c) 0%, {{VALUE}} 100%);'
						. '-webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;',
				],
			]
		);

		$this->end_controls_section();

		/* ── Style › Cards ───────────────────────────────────── */
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
				'range'      => [ 'px' => [ 'min' => 140, 'max' => 360 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 200 ],
				'selectors'  => [
					'{{WRAPPER}} .tc-atp-grid' =>
						'grid-template-columns: repeat(auto-fill, minmax({{SIZE}}{{UNIT}}, 1fr));',
				],
			]
		);

		$this->add_control(
			'card_gap',
			[
				'label'      => esc_html__( 'Card Gap', 'textcraft-tools' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 8, 'max' => 40 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 14 ],
				'selectors'  => [ '{{WRAPPER}} .tc-atp-grid' => 'gap: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'card_bg',
			[
				'label'     => esc_html__( 'Card Background', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b0b0b',
				'selectors' => [ '{{WRAPPER}} .tc-atp-card' => 'background: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'card_border_color',
			[
				'label'     => esc_html__( 'Card Border', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.07)',
				'selectors' => [ '{{WRAPPER}} .tc-atp-card' => 'border-color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'card_hover_accent',
			[
				'label'     => esc_html__( 'Hover Border & Glow', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d4a24c',
				'selectors' => [
					'{{WRAPPER}} .tc-atp-card:hover' =>
						'border-color: {{VALUE}}; box-shadow: 0 8px 28px color-mix(in srgb, {{VALUE}} 15%, transparent);',
				],
			]
		);

		$this->add_control(
			'icon_bg',
			[
				'label'     => esc_html__( 'Icon Box Background', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1a1d30',
				'selectors' => [ '{{WRAPPER}} .tc-atp-card-icon' => 'background: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'card_name_color',
			[
				'label'     => esc_html__( 'Tool Name Color', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .tc-atp-card-name' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'card_desc_color',
			[
				'label'     => esc_html__( 'Description Color', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#55597a',
				'selectors' => [ '{{WRAPPER}} .tc-atp-card-desc' => 'color: {{VALUE}};' ],
			]
		);

		$this->end_controls_section();

		/* ── Style › Category Headings ───────────────────────── */
		$this->start_controls_section(
			'style_categories',
			[
				'label' => esc_html__( 'Category Headings', 'textcraft-tools' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'cat_title_color',
			[
				'label'     => esc_html__( 'Heading Color', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ '{{WRAPPER}} .tc-atp-cat-title' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'cat_badge_color',
			[
				'label'     => esc_html__( 'Badge Accent', 'textcraft-tools' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d4a24c',
				'selectors' => [
					'{{WRAPPER}} .tc-atp-cat-count' =>
						'color: {{VALUE}}; background: color-mix(in srgb, {{VALUE}} 15%, transparent);',
				],
			]
		);

		$this->add_control(
			'cat_spacing',
			[
				'label'      => esc_html__( 'Section Bottom Gap', 'textcraft-tools' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 16, 'max' => 80 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 44 ],
				'selectors'  => [ '{{WRAPPER}} .tc-atp-category' => 'padding-bottom: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();
	}

	// ── Render ────────────────────────────────────────────────

	protected function render(): void {
		$s      = $this->get_settings_for_display();
		$wid    = $this->get_id(); // unique widget ID used to scope JS
		$tools  = $s['tools_list'] ?? [];

		// ── Group tools by category (preserve insertion order) ─
		$by_cat = [];
		foreach ( $tools as $tool ) {
			$cat = sanitize_text_field( $tool['tool_cat'] ?? 'Uncategorised' );
			$by_cat[ $cat ][] = $tool;
		}

		echo '<div class="tc-atp-wrap textcraft-tools" id="tc-atp-' . esc_attr( $wid ) . '">';

		// ── Hero ──────────────────────────────────────────────
		if ( 'yes' === ( $s['show_hero'] ?? 'yes' ) ) {
			echo '<div class="tc-atp-hero tc-section-has-dust"><div class="tc-dust"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>';

			if ( ! empty( $s['hero_badge'] ) ) {
				echo '<div class="tc-atp-badge">'
					. '<span class="tc-atp-badge-dot" aria-hidden="true"></span>'
					. esc_html( $s['hero_badge'] )
					. '</div>';
			}

			$plain    = $s['hero_title_plain']    ?? 'All';
			$gradient = $s['hero_title_gradient'] ?? 'Tools';

			if ( $plain || $gradient ) {
				echo '<h1 class="tc-atp-hero-title">';
				if ( $plain ) {
					echo esc_html( $plain ) . ' ';
				}
				if ( $gradient ) {
					echo '<span class="tc-accent-gradient">' . esc_html( $gradient ) . '</span>';
				}
				echo '</h1>';
			}

			if ( ! empty( $s['hero_subtitle'] ) ) {
				echo '<p class="tc-atp-hero-subtitle">' . esc_html( $s['hero_subtitle'] ) . '</p>';
			}

			echo '</div>'; // .tc-atp-hero
		}

		// ── Search bar ────────────────────────────────────────
		if ( 'yes' === ( $s['show_search'] ?? 'yes' ) ) {
			$placeholder = $s['search_placeholder'] ?? esc_html__( 'Search free online tools…', 'textcraft-tools' );
			echo '<div class="tc-atp-search-wrap">';
			echo '<span class="tc-atp-search-icon" aria-hidden="true">🔍</span>';
			echo '<input'
				. ' type="text"'
				. ' id="tc-atp-search-' . esc_attr( $wid ) . '"'
				. ' class="tc-atp-search"'
				. ' placeholder="' . esc_attr( $placeholder ) . '"'
				. ' aria-label="' . esc_attr__( 'Search tools', 'textcraft-tools' ) . '"'
				. ' autocomplete="off"'
				. '>';
			echo '</div>';
		}

		// ── Category sections ─────────────────────────────────
		foreach ( $by_cat as $cat_name => $cat_tools ) {
			$count    = count( $cat_tools );
			$cat_slug = 'tc-cat-' . $wid . '-' . sanitize_html_class( strtolower( str_replace( [ ' ', '&' ], [ '-', '' ], $cat_name ) ) );

			printf(
				'<div class="tc-atp-category" data-cat="%s" id="%s">',
				esc_attr( $cat_name ),
				esc_attr( $cat_slug )
			);

			// Category heading row.
			echo '<div class="tc-atp-cat-header">';
			echo '<h2 class="tc-atp-cat-title">' . esc_html( $cat_name ) . '</h2>';
			echo '<span class="tc-atp-cat-count" aria-label="' . esc_attr( $count . ' tools' ) . '">' . esc_html( (string) $count ) . '</span>';
			echo '</div>';

			// Tool cards grid.
			echo '<div class="tc-atp-grid">';

			foreach ( $cat_tools as $tool ) {
				$icon     = $tool['tool_icon'] ?? '';
				$name     = $tool['tool_name'] ?? '';
				$desc     = $tool['tool_desc'] ?? '';
				$url      = $tool['tool_url']  ?? '';
				$name_lc  = strtolower( $name );
				$desc_lc  = strtolower( $desc );
				$cat_lc   = strtolower( $cat_name );

				// Render as <a> if URL present, otherwise <div>.
				$has_url  = ! empty( trim( $url ) );
				$tag      = $has_url ? 'a' : 'div';
				$link_attr = $has_url
					? ' href="' . esc_url( $url ) . '"'
					: ' role="button" tabindex="0"';

printf(
    '<%s%s class="tc-atp-card" data-name="%s" data-desc="%s" data-cat="%s" aria-label="%s">',
    $tag,
    $link_attr,
    esc_attr( $name_lc ),
    esc_attr( $desc_lc ),
    esc_attr( $cat_lc ),
    esc_attr( $name )
);
echo '<div class="tc-card-sheen"></div>';

if ( $icon ) {
					echo '<div class="tc-atp-card-icon" aria-hidden="true">' . esc_html( $icon ) . '</div>';
				}
				if ( $name ) {
					echo '<div class="tc-atp-card-name">' . esc_html( $name ) . '</div>';
				}
				if ( $desc ) {
					echo '<div class="tc-atp-card-desc">' . esc_html( $desc ) . '</div>';
				}

				echo '</' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			echo '</div>'; // .tc-atp-grid
			echo '</div>'; // .tc-atp-category
		}

		// ── No-results state ──────────────────────────────────
		$no_title = $s['no_results_title'] ?? esc_html__( 'No tools found', 'textcraft-tools' );
		$no_hint  = $s['no_results_hint']  ?? esc_html__( 'Try a different search term', 'textcraft-tools' );

		echo '<div class="tc-atp-no-results" id="tc-atp-nores-' . esc_attr( $wid ) . '" aria-hidden="true" aria-live="polite">';
		echo '<div class="tc-atp-no-results-icon" aria-hidden="true">🔍</div>';
		echo '<p class="tc-atp-no-results-title">' . esc_html( $no_title ) . '</p>';
		echo '<p class="tc-atp-no-results-hint">'  . esc_html( $no_hint )  . '</p>';
		echo '</div>';

		// ── Inline search JavaScript ──────────────────────────
		$this->render_search_script( $wid );

		echo '</div>'; // .tc-atp-wrap

		// ── Premium SEO section (full-width, panel-editable) ─
		if ( 'yes' === ( $s['show_premium_section'] ?? 'yes' ) ) {

		echo '<div class="textcraft-tools tc-premium-wrap"><div class="tc-premium-tools-section"><div class="tc-premium-tools-inner">';

			// ── Hero card ─────────────────────────────────────
			$pm_badge = $s['pm_hero_badge'] ?? '';
			$pm_title = $s['pm_hero_title'] ?? '';
			$pm_intro = $s['pm_hero_intro'] ?? '';
			$pm_stats = $s['pm_hero_stats'] ?? [];

			if ( $pm_badge || $pm_title || $pm_intro || ! empty( $pm_stats ) ) {
				echo '<div class="tc-seo-hero-card">';
				if ( $pm_badge ) {
					echo '<span class="tc-seo-hero-badge">' . esc_html( $pm_badge ) . '</span>';
				}
				if ( $pm_title ) {
					echo '<h2 class="tc-seo-hero-title">' . esc_html( $pm_title ) . '</h2>';
				}
				if ( $pm_intro ) {
					echo '<p class="tc-seo-hero-intro">' . esc_html( $pm_intro ) . '</p>';
				}
				if ( ! empty( $pm_stats ) ) {
					echo '<div class="tc-seo-hero-stats">';
					foreach ( $pm_stats as $stat ) {
						$num   = $stat['stat_number'] ?? '';
						$label = $stat['stat_label'] ?? '';
						if ( $num || $label ) {
							echo '<div class="tc-seo-hero-stat">'
								. '<span class="tc-seo-hero-stat-num">' . esc_html( $num ) . '</span>'
								. '<span class="tc-seo-hero-stat-label">' . esc_html( $label ) . '</span>'
								. '</div>';
						}
					}
					echo '</div>';
				}
				echo '</div>'; // .tc-seo-hero-card
			}

			// ── Feature grid (category cards) ────────────────
			$pm_features = $s['pm_feature_cards'] ?? [];

			if ( ! empty( $pm_features ) ) {
				echo '<div class="tc-seo-feature-grid">';
				foreach ( $pm_features as $card ) {
					$icon = $card['icon'] ?? '';
					$ftitle = $card['title'] ?? '';
					$fdesc = $card['desc'] ?? '';
					echo '<div class="tc-seo-feature-card">';
					echo '<div class="tc-card-sheen"></div>';
					if ( $icon ) {
						echo '<span class="tc-seo-feature-icon" aria-hidden="true">' . esc_html( $icon ) . '</span>';
					}
					if ( $ftitle ) {
						echo '<h3 class="tc-seo-feature-title">' . esc_html( $ftitle ) . '</h3>';
					}
					if ( $fdesc ) {
						echo '<p class="tc-seo-feature-desc">' . esc_html( $fdesc ) . '</p>';
					}
					echo '</div>';
				}
				echo '</div>'; // .tc-seo-feature-grid
			}

			// ── Media section ─────────────────────────────────
			$pm_media_id    = $s['pm_media_image']['id'] ?? '';
			$pm_media_url   = $s['pm_media_image']['url'] ?? '';
			$pm_media_emoji = $s['pm_media_emoji'] ?? '🛠️';
			$pm_media_title = $s['pm_media_title'] ?? '';
			$pm_media_desc  = $s['pm_media_desc'] ?? '';

			if ( $pm_media_title || $pm_media_desc || $pm_media_url || $pm_media_emoji ) {
				echo '<div class="tc-seo-media-section">';
				echo '<div class="tc-seo-media-grid">';
				echo '<div class="tc-seo-media-visual">';
				if ( $pm_media_url ) {
					$alt = $pm_media_title ?: '';
					echo '<div class="tc-seo-media-image">';
					echo '<img src="' . esc_url( $pm_media_url ) . '" alt="' . esc_attr( $alt ) . '" style="max-width:100%;height:auto;border-radius:20px;display:block;">';
					echo '</div>';
				} else {
					echo '<div class="tc-seo-media-image tc-seo-media-icon-fallback">';
					echo '<span class="tc-seo-media-emoji" aria-hidden="true">' . esc_html( $pm_media_emoji ) . '</span>';
					echo '</div>';
				}
				echo '</div>'; // .tc-seo-media-visual
				echo '<div class="tc-seo-media-content">';
				if ( $pm_media_title ) {
					echo '<h3 class="tc-seo-media-title">' . esc_html( $pm_media_title ) . '</h3>';
				}
				if ( $pm_media_desc ) {
					echo '<p class="tc-seo-media-desc">' . esc_html( $pm_media_desc ) . '</p>';
				}
				echo '</div></div></div>'; // .tc-seo-media-grid / .tc-seo-media-section
			}

			// ── Benefit grid ────────────────────────────────
			$pm_benefits = $s['pm_benefit_cards'] ?? [];

			if ( ! empty( $pm_benefits ) ) {
				echo '<div class="tc-seo-benefit-grid">';
				foreach ( $pm_benefits as $card ) {
					$icon = $card['icon'] ?? '';
					$btitle = $card['title'] ?? '';
					$bdesc = $card['desc'] ?? '';
					echo '<div class="tc-seo-benefit-card">';
					echo '<div class="tc-card-sheen"></div>';
					if ( $icon ) {
						echo '<span class="tc-seo-benefit-icon" aria-hidden="true">' . esc_html( $icon ) . '</span>';
					}
					if ( $btitle ) {
						echo '<h4 class="tc-seo-benefit-title">' . esc_html( $btitle ) . '</h4>';
					}
					if ( $bdesc ) {
						echo '<p class="tc-seo-benefit-desc">' . esc_html( $bdesc ) . '</p>';
					}
					echo '</div>';
				}
				echo '</div>'; // .tc-seo-benefit-grid
			}

			// ── Highlight / CTA card ──────────────────────────
			$hl_icon  = $s['pm_highlight_icon'] ?? '';
			$hl_title = $s['pm_highlight_title'] ?? '';
			$hl_desc  = $s['pm_highlight_desc'] ?? '';

			if ( $hl_icon || $hl_title || $hl_desc ) {
				echo '<div class="tc-seo-highlight-card">';
				echo '<div class="tc-card-sheen"></div>';
				echo '<div class="tc-seo-highlight-grid">';
				if ( $hl_icon ) {
					echo '<div class="tc-seo-highlight-visual">';
					echo '<span class="tc-seo-highlight-icon" aria-hidden="true">' . esc_html( $hl_icon ) . '</span>';
					echo '</div>';
				}
				echo '<div class="tc-seo-highlight-content">';
				if ( $hl_title ) {
					echo '<h3 class="tc-seo-highlight-title">' . esc_html( $hl_title ) . '</h3>';
				}
				if ( $hl_desc ) {
					echo '<p class="tc-seo-highlight-desc">' . esc_html( $hl_desc ) . '</p>';
				}
				echo '</div></div></div>'; // .tc-seo-highlight-grid / .tc-seo-highlight-card
			}

			// ── FAQ accordion ────────────────────────────────
			$pm_faq_title = $s['pm_faq_title'] ?? '';
			$pm_faq_items = $s['pm_faq_items'] ?? [];

			if ( ! empty( $pm_faq_items ) ) {
				echo '<div class="tc-faq-accordion" data-tc-faq-accordion>';
				if ( $pm_faq_title ) {
					echo '<h2 class="tc-seo-faq-title">' . esc_html( $pm_faq_title ) . '</h2>';
				}
				foreach ( $pm_faq_items as $faq ) {
					$q = $faq['question'] ?? '';
					$a = $faq['answer'] ?? '';
					if ( ! $q && ! $a ) {
						continue;
					}
					echo '<div class="tc-faq-item">'
						. '<div class="tc-card-sheen"></div>'
						. '<button class="tc-faq-question" type="button" aria-expanded="false">'
						. '<span class="tc-faq-question-text">' . esc_html( $q ) . '</span>'
						. '<span class="tc-faq-icon" aria-hidden="true">+</span>'
						. '</button>'
						. '<div class="tc-faq-answer-wrap"><div class="tc-faq-answer" hidden>'
						. '<p>' . esc_html( $a ) . '</p>'
						. '</div></div>'
						. '</div>';
				}
				echo '</div>'; // .tc-faq-accordion
			}

		echo '</div></div></div>'; // .tc-premium-tools-inner / .tc-premium-tools-section / .textcraft-tools

		} // end if show_premium_section
	}

	// ── Search JS ─────────────────────────────────────────────

	/**
	 * Output the self-contained, scoped live-search script.
	 * All DOM queries are scoped to #tc-atp-{id} so multiple instances
	 * of the widget on the same page never interfere.
	 *
	 * @param string $wid Unique widget ID.
	 */
	private function render_search_script( string $wid ): void {
		$search_id  = 'tc-atp-search-' . $wid;
		$wrap_id    = 'tc-atp-' . $wid;
		$nores_id   = 'tc-atp-nores-' . $wid;

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo "\n<script>\n(function(){\n'use strict';\n";

		echo "var searchEl  = document.getElementById(" . wp_json_encode( $search_id )  . ");\n";
		echo "var wrap      = document.getElementById(" . wp_json_encode( $wrap_id )    . ");\n";
		echo "var noResEl   = document.getElementById(" . wp_json_encode( $nores_id )   . ");\n";

		echo <<<'JS'
if(!searchEl || !wrap) return;

searchEl.addEventListener('input', function(){
    var q       = this.value.toLowerCase().trim();
    var cards   = wrap.querySelectorAll('.tc-atp-card');
    var cats    = wrap.querySelectorAll('.tc-atp-category');
    var visible = 0;

    // Show/hide individual cards based on name + description match.
    cards.forEach(function(card){
        var match = !q
            || card.dataset.name.includes(q)
            || card.dataset.desc.includes(q)
            || card.dataset.cat.includes(q);

        if(match){
            card.removeAttribute('hidden');
            visible++;
        } else {
            card.setAttribute('hidden','');
        }
    });

    // Show/hide category sections based on whether any card is visible.
    cats.forEach(function(section){
        var hasVisible = [...section.querySelectorAll('.tc-atp-card')]
            .some(function(c){ return !c.hasAttribute('hidden'); });

        if(hasVisible){
            section.removeAttribute('hidden');
        } else {
            section.setAttribute('hidden','');
        }
    });

    // Toggle no-results state.
    if(noResEl){
        noResEl.setAttribute('aria-hidden', visible === 0 ? 'false' : 'true');
    }
});
JS;

		echo "\n})();\n</script>\n";
		// phpcs:enable
	}

	// ── Editor live-preview template ──────────────────────────

	protected function content_template(): void {
		?>
		<#
		var showHero   = ( settings.show_hero   === 'yes' );
		var showSearch = ( settings.show_search  === 'yes' );
		var tools      = settings.tools_list || [];

		// Group tools by category, preserving order.
		var byCategory = {};
		var catOrder   = [];
		tools.forEach( function( tool ) {
			var cat = tool.tool_cat || 'Uncategorised';
			if ( ! byCategory[ cat ] ) {
				byCategory[ cat ] = [];
				catOrder.push( cat );
			}
			byCategory[ cat ].push( tool );
		});
		#>

		<div class="tc-atp-wrap">

			<# if ( showHero ) { #>
			<div class="tc-atp-hero tc-section-has-dust"><div class="tc-dust"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
				<# if ( settings.hero_badge ) { #>
				<div class="tc-atp-badge">
					<span class="tc-atp-badge-dot" aria-hidden="true"></span>
					{{{ settings.hero_badge }}}
				</div>
				<# } #>
				<# if ( settings.hero_title_plain || settings.hero_title_gradient ) { #>
				<h1 class="tc-atp-hero-title">
					<# if ( settings.hero_title_plain ) { #>{{{ settings.hero_title_plain }}} <# } #>
					<# if ( settings.hero_title_gradient ) { #>
						<span class="tc-accent-gradient">{{{ settings.hero_title_gradient }}}</span>
					<# } #>
				</h1>
				<# } #>
				<# if ( settings.hero_subtitle ) { #>
				<p class="tc-atp-hero-subtitle">{{{ settings.hero_subtitle }}}</p>
				<# } #>
			</div>
			<# } #>

			<# if ( showSearch ) { #>
			<div class="tc-atp-search-wrap">
				<span class="tc-atp-search-icon" aria-hidden="true">🔍</span>
				<input type="text" class="tc-atp-search"
					   placeholder="{{ settings.search_placeholder || 'Search free online tools…' }}"
					   readonly>
			</div>
			<# } #>

			<# catOrder.forEach( function( catName ) {
				var catTools = byCategory[ catName ];
				var count    = catTools.length;
			#>
			<div class="tc-atp-category">
				<div class="tc-atp-cat-header">
					<h2 class="tc-atp-cat-title">{{{ catName }}}</h2>
					<span class="tc-atp-cat-count">{{ count }}</span>
				</div>
				<div class="tc-atp-grid">
					<# catTools.forEach( function( tool ) { #>
    <div class="tc-atp-card">
        <div class="tc-card-sheen"></div>
        <# if ( tool.tool_icon ) { #>
						<div class="tc-atp-card-icon" aria-hidden="true">{{{ tool.tool_icon }}}</div>
						<# } #>
						<# if ( tool.tool_name ) { #>
						<div class="tc-atp-card-name">{{{ tool.tool_name }}}</div>
						<# } #>
						<# if ( tool.tool_desc ) { #>
						<div class="tc-atp-card-desc">{{{ tool.tool_desc }}}</div>
						<# } #>
					</div>
					<# }); #>
				</div>
			</div>
			<# }); #>

			<div class="tc-atp-no-results">
				<div class="tc-atp-no-results-icon" aria-hidden="true">🔍</div>
				<p class="tc-atp-no-results-title">{{{ settings.no_results_title || 'No tools found' }}}</p>
				<p class="tc-atp-no-results-hint">{{{ settings.no_results_hint || 'Try a different search term' }}}</p>
			</div>

		</div>

		<# if ( settings.show_premium_section === 'yes' ) { #>
		<div class="textcraft-tools tc-premium-wrap"><div class="tc-premium-tools-section"><div class="tc-premium-tools-inner">

			<# if ( settings.pm_hero_badge || settings.pm_hero_title || settings.pm_hero_intro || ( settings.pm_hero_stats && settings.pm_hero_stats.length ) ) { #>
			<div class="tc-seo-hero-card">
				<# if ( settings.pm_hero_badge ) { #>
					<span class="tc-seo-hero-badge">{{{ settings.pm_hero_badge }}}</span>
				<# } #>
				<# if ( settings.pm_hero_title ) { #>
					<h2 class="tc-seo-hero-title">{{{ settings.pm_hero_title }}}</h2>
				<# } #>
				<# if ( settings.pm_hero_intro ) { #>
					<p class="tc-seo-hero-intro">{{{ settings.pm_hero_intro }}}</p>
				<# } #>
				<# if ( settings.pm_hero_stats && settings.pm_hero_stats.length ) { #>
					<div class="tc-seo-hero-stats">
					<# _.each( settings.pm_hero_stats, function( stat ) { #>
						<div class="tc-seo-hero-stat">
							<span class="tc-seo-hero-stat-num">{{{ stat.stat_number }}}</span>
							<span class="tc-seo-hero-stat-label">{{{ stat.stat_label }}}</span>
						</div>
					<# }); #>
					</div>
				<# } #>
			</div>
			<# } #>

			<# if ( settings.pm_feature_cards && settings.pm_feature_cards.length ) { #>
			<div class="tc-seo-feature-grid">
				<# _.each( settings.pm_feature_cards, function( card ) { #>
					<div class="tc-seo-feature-card">
						<div class="tc-card-sheen"></div>
						<# if ( card.icon ) { #>
							<span class="tc-seo-feature-icon" aria-hidden="true">{{{ card.icon }}}</span>
						<# } #>
						<# if ( card.title ) { #>
							<h3 class="tc-seo-feature-title">{{{ card.title }}}</h3>
						<# } #>
						<# if ( card.desc ) { #>
							<p class="tc-seo-feature-desc">{{{ card.desc }}}</p>
						<# } #>
					</div>
				<# }); #>
			</div>
			<# } #>

			<# if ( settings.pm_media_title || settings.pm_media_desc || settings.pm_media_image?.url || settings.pm_media_emoji ) { #>
			<div class="tc-seo-media-section">
				<div class="tc-seo-media-grid">
					<div class="tc-seo-media-visual">
						<# if ( settings.pm_media_image?.url ) { #>
							<div class="tc-seo-media-image">
								<img src="{{ settings.pm_media_image.url }}" alt="{{ settings.pm_media_title || '' }}" style="max-width:100%;height:auto;border-radius:20px;display:block;">
							</div>
						<# } else { #>
							<div class="tc-seo-media-image tc-seo-media-icon-fallback">
								<span class="tc-seo-media-emoji" aria-hidden="true">{{ settings.pm_media_emoji || '🛠️' }}</span>
							</div>
						<# } #>
					</div>
					<div class="tc-seo-media-content">
						<# if ( settings.pm_media_title ) { #>
							<h3 class="tc-seo-media-title">{{{ settings.pm_media_title }}}</h3>
						<# } #>
						<# if ( settings.pm_media_desc ) { #>
							<p class="tc-seo-media-desc">{{{ settings.pm_media_desc }}}</p>
						<# } #>
					</div>
				</div>
			</div>
			<# } #>

			<# if ( settings.pm_benefit_cards && settings.pm_benefit_cards.length ) { #>
			<div class="tc-seo-benefit-grid">
				<# _.each( settings.pm_benefit_cards, function( card ) { #>
					<div class="tc-seo-benefit-card">
						<div class="tc-card-sheen"></div>
						<# if ( card.icon ) { #>
							<span class="tc-seo-benefit-icon" aria-hidden="true">{{{ card.icon }}}</span>
						<# } #>
						<# if ( card.title ) { #>
							<h4 class="tc-seo-benefit-title">{{{ card.title }}}</h4>
						<# } #>
						<# if ( card.desc ) { #>
							<p class="tc-seo-benefit-desc">{{{ card.desc }}}</p>
						<# } #>
					</div>
				<# }); #>
			</div>
			<# } #>

			<# if ( settings.pm_highlight_icon || settings.pm_highlight_title || settings.pm_highlight_desc ) { #>
			<div class="tc-seo-highlight-card">
				<div class="tc-card-sheen"></div>
				<div class="tc-seo-highlight-grid">
					<# if ( settings.pm_highlight_icon ) { #>
						<div class="tc-seo-highlight-visual">
							<span class="tc-seo-highlight-icon" aria-hidden="true">{{{ settings.pm_highlight_icon }}}</span>
						</div>
					<# } #>
					<div class="tc-seo-highlight-content">
						<# if ( settings.pm_highlight_title ) { #>
							<h3 class="tc-seo-highlight-title">{{{ settings.pm_highlight_title }}}</h3>
						<# } #>
						<# if ( settings.pm_highlight_desc ) { #>
							<p class="tc-seo-highlight-desc">{{{ settings.pm_highlight_desc }}}</p>
						<# } #>
					</div>
				</div>
			</div>
			<# } #>

			<# if ( settings.pm_faq_items && settings.pm_faq_items.length ) { #>
			<div class="tc-faq-accordion" data-tc-faq-accordion>
				<# if ( settings.pm_faq_title ) { #>
					<h2 class="tc-seo-faq-title">{{{ settings.pm_faq_title }}}</h2>
				<# } #>
				<# _.each( settings.pm_faq_items, function( faq ) { #>
					<div class="tc-faq-item">
						<div class="tc-card-sheen"></div>
						<button class="tc-faq-question" type="button" aria-expanded="false">
							<span class="tc-faq-question-text">{{{ faq.question }}}</span>
							<span class="tc-faq-icon" aria-hidden="true">+</span>
						</button>
						<div class="tc-faq-answer-wrap"><div class="tc-faq-answer" hidden>
							<p>{{{ faq.answer }}}</p>
						</div></div>
					</div>
				<# }); #>
			</div>
			<# } #>

		</div></div></div>
		<# } #>

		<?php
	}
}
