<?php
/**
 * Tools Section Elementor Widget
 *
 * All 207 tools from textcrafttools.com, 16 categories matching HTML design.
 *
 * @package    TextCraftToolsPro
 * @subpackage Widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class TextCraft_Tools_Section_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'tctp_tools_section';
    }

    public function get_title() {
        return __( 'Tools Section', 'textcrafttoolspro' );
    }

    public function get_icon() {
        return 'eicon-grid-3x3';
    }

    public function get_categories() {
        return [ 'textcrafttools' ];
    }

    public function get_tools_data() {
        return [
            'pdf' => [
                'title' => 'PDF',
                'tools' => [
                    [ 'name' => 'PDF Compressor', 'desc' => 'Compress PDF files online with preview, compression levels, and instant browser download.', 'url' => '/tools/pdf-compressor/', 'icon' => 'PDF' ],
                    [ 'name' => 'PDF to Word Converter', 'desc' => 'Convert PDF to Word DOCX online - editable documents with a fast server-side converter.', 'url' => '/tools/pdf-to-word-converter/', 'icon' => 'DOC', 'tag' => 'Popular' ],
                    [ 'name' => 'PDF Splitter', 'desc' => 'Split a PDF online into multiple documents by range, pages, or file size.', 'url' => '/tools/pdf-splitter/', 'icon' => '✂' ],
                    [ 'name' => 'PDF Merger', 'desc' => 'Merge multiple PDF files into one document online - free and secure.', 'url' => '/tools/pdf-merger/', 'icon' => '＋' ],
                    [ 'name' => 'Word / Excel / PowerPoint to PDF', 'desc' => 'Convert Office documents to PDF with a clean, formatted output.', 'url' => '/tools/word-excel-powerpoint-to-pdf/', 'icon' => 'OF' ],
                    [ 'name' => 'Delete PDF Pages', 'desc' => 'Delete PDF pages online - select and remove pages locally in your browser.', 'url' => '/tools/delete-pdf-pages/', 'icon' => '⌫' ],
                    [ 'name' => 'Rotate PDF', 'desc' => 'Rotate PDF pages online - free browser-based PDF rotation tool.', 'url' => '/tools/rotate-pdf/', 'icon' => '↻' ],
                    [ 'name' => 'JPG to PDF', 'desc' => 'Convert multiple JPG images into one PDF online - free and private.', 'url' => '/tools/jpg-to-pdf-converter/', 'icon' => 'JPG' ],
                    [ 'name' => 'PDF to JPG', 'desc' => 'Convert every PDF page to a JPG image online - locally in your browser.', 'url' => '/tools/pdf-to-jpg-converter/', 'icon' => 'IMG' ],
                    [ 'name' => 'PDF to PNG', 'desc' => 'Convert every PDF page to a PNG image online - locally in your browser.', 'url' => '/tools/pdf-to-png-converter/', 'icon' => 'PNG' ],
                ],
            ],
            'compress' => [
                'title' => 'Compression',
                'tools' => [
                    [ 'name' => 'JPG Compressor', 'desc' => 'Compress JPG images online with previews, tab cache, and ZIP batch download.', 'url' => '/tools/jpg-compressor/', 'icon' => 'JPG' ],
                    [ 'name' => 'PNG Compressor', 'desc' => 'Compress PNG images online with previews, tab cache, and ZIP batch download.', 'url' => '/tools/png-compressor/', 'icon' => 'PNG' ],
                    [ 'name' => 'WebP Compressor', 'desc' => 'Compress WebP images online with quality control, resizing, previews, and ZIP download.', 'url' => '/tools/webp-compressor/', 'icon' => 'WEB' ],
                    [ 'name' => 'GIF Compressor', 'desc' => 'Compress GIF images online with previews, tab cache, and ZIP batch download.', 'url' => '/tools/gif-compressor/', 'icon' => 'GIF' ],
                    [ 'name' => 'SVG Compressor', 'desc' => 'Compress SVG images online with previews, tab cache, and ZIP batch download.', 'url' => '/tools/svg-compressor/', 'icon' => 'SVG' ],
                    [ 'name' => 'Image Compressor', 'desc' => 'Compress any image file with a simple, fast browser-based tool.', 'url' => '/tools/image-compressor/', 'icon' => 'IMG' ],
                    [ 'name' => 'Reduce Image Size to KB', 'desc' => 'Shrink an image to a target file size in kilobytes.', 'url' => '/tools/reduce-image-size-to-kb/', 'icon' => 'KB' ],
                ],
            ],
            'image' => [
                'title' => 'Image & Media',
                'tools' => [
                    [ 'name' => 'Remove Background', 'desc' => 'Remove image backgrounds online and export transparent PNG files - free and private.', 'url' => '/tools/remove-background-from-image/', 'icon' => 'BG', 'tag' => 'Popular' ],
                    [ 'name' => 'Image to Text (OCR)', 'desc' => 'Extract text from images and photos using browser-based OCR - no uploads required.', 'url' => '/tools/image-to-text-ocr/', 'icon' => 'OCR' ],
                    [ 'name' => 'ASCII Art Generator', 'desc' => 'Convert any image to detailed ASCII art online - free browser-based image to text art generator.', 'url' => '/tools/image-to-ascii-art/', 'icon' => 'ASC' ],
                    [ 'name' => 'JPG to PNG', 'desc' => 'Convert JPG to PNG online - batch convert images to lossless PNG format.', 'url' => '/tools/jpg-to-png-converter/', 'icon' => '→' ],
                    [ 'name' => 'JPG to WebP', 'desc' => 'Convert JPG to WebP online - create smaller, faster-loading web images instantly.', 'url' => '/tools/jpg-to-webp-converter/', 'icon' => '→' ],
                    [ 'name' => 'JPG to SVG', 'desc' => 'Convert JPG to SVG online - transform images into vector-style output.', 'url' => '/tools/jpg-to-svg-converter/', 'icon' => '→' ],
                    [ 'name' => 'JPG to GIF', 'desc' => 'Convert JPG to GIF online - browser-based image format conversion tool.', 'url' => '/tools/jpg-to-gif-converter/', 'icon' => '→' ],
                    [ 'name' => 'JPG to HEIC', 'desc' => 'Convert JPG to HEIC online - create Apple-compatible HEIC files from your images.', 'url' => '/tools/jpg-to-heic-converter/', 'icon' => '→' ],
                    [ 'name' => 'JPG to AVIF', 'desc' => 'Convert JPG to AVIF online - next-gen image format for better compression.', 'url' => '/tools/jpg-to-avif-converter/', 'icon' => '→' ],
                    [ 'name' => 'PNG to JPG', 'desc' => 'Convert PNG to JPG online - transform images into compact, shareable JPEG files.', 'url' => '/tools/png-to-jpg-converter/', 'icon' => '→' ],
                    [ 'name' => 'PNG to WebP', 'desc' => 'Convert PNG to WebP online - create efficient, modern web images.', 'url' => '/tools/png-to-webp-converter/', 'icon' => '→' ],
                    [ 'name' => 'PNG to SVG', 'desc' => 'Convert PNG to SVG online - transform raster images into vector-style output.', 'url' => '/tools/png-to-svg-converter/', 'icon' => '→' ],
                    [ 'name' => 'PNG to HEIC', 'desc' => 'Convert PNG to HEIC online - Apple-compatible HEIC image conversion tool.', 'url' => '/tools/png-to-heic-converter/', 'icon' => '→' ],
                    [ 'name' => 'HEIC to JPG', 'desc' => 'Convert HEIC to JPG online - transform iPhone HEIC photos into universal JPEG format.', 'url' => '/tools/heic-to-jpg-converter/', 'icon' => '→' ],
                    [ 'name' => 'HEIC to PNG', 'desc' => 'Convert HEIC to PNG online - transform iPhone HEIC photos into lossless PNG format.', 'url' => '/tools/heic-to-png-converter/', 'icon' => '→' ],
                    [ 'name' => 'HEIC to SVG', 'desc' => 'Convert HEIC to SVG online - transform iPhone HEIC photos into SVG wrapper files.', 'url' => '/tools/heic-to-svg-converter/', 'icon' => '→' ],
                    [ 'name' => 'WebP to JPG', 'desc' => 'Convert WebP to JPG online - restore WebP images back to standard JPEG format.', 'url' => '/tools/webp-to-jpg-converter/', 'icon' => '→' ],
                    [ 'name' => 'WebP to PNG', 'desc' => 'Convert WebP to PNG online - restore WebP images to lossless PNG format.', 'url' => '/tools/webp-to-png-converter/', 'icon' => '→' ],
                    [ 'name' => 'PNG to PDF', 'desc' => 'Convert multiple PNG images into one PDF online - free browser-based tool.', 'url' => '/tools/png-to-pdf-converter/', 'icon' => 'PDF' ],
                    [ 'name' => 'Video Converter', 'desc' => 'Convert videos online between MP4, WebM, AVI, MOV and more - free browser-based converter.', 'url' => '/tools/video-converter/', 'icon' => 'VID' ],
                    [ 'name' => 'HTML to Image', 'desc' => 'Turn live HTML into a shareable image snapshot.', 'url' => '/tools/html-to-image/', 'icon' => '▨' ],
                    [ 'name' => 'Meme Generator', 'desc' => 'Create classic memes from your own image with editable captions.', 'url' => '/tools/meme-generator/', 'icon' => '😂' ],
                ],
            ],
            'image_edit' => [
                'title' => 'Image Editing',
                'tools' => [
                    [ 'name' => 'Image Resizer', 'desc' => 'Resize images by pixels, percentage or preset dimensions.', 'url' => '/tools/resize-image/', 'icon' => '⬒' ],
                    [ 'name' => 'Crop Image', 'desc' => 'Crop images to any size or aspect ratio online.', 'url' => '/tools/crop-image/', 'icon' => '✂' ],
                    [ 'name' => 'Rotate Image', 'desc' => 'Rotate and flip images by any angle.', 'url' => '/tools/rotate-image/', 'icon' => '↻' ],
                    [ 'name' => 'Watermark Image', 'desc' => 'Add text or image watermarks to your photos.', 'url' => '/tools/watermark-image/', 'icon' => '©' ],
                    [ 'name' => 'Flip Image', 'desc' => 'Flip images horizontally or vertically instantly.', 'url' => '/tools/flip-image/', 'icon' => '⇄' ],
                    [ 'name' => 'Upscale Image', 'desc' => 'Enlarge images with smart upscaling and sharpening.', 'url' => '/tools/upscale-image/', 'icon' => '⤢' ],
                    [ 'name' => 'Blur Face / Objects', 'desc' => 'Blur faces or objects in a photo for privacy.', 'url' => '/tools/blur-face/', 'icon' => '◒' ],
                    [ 'name' => 'Photo Editor', 'desc' => 'A lightweight browser photo editor for quick fixes.', 'url' => '/tools/photo-editor/', 'icon' => '◑' ],
                    [ 'name' => 'Passport Photo Maker', 'desc' => 'Make ID / passport sized photos with the right dimensions and background.', 'url' => '/tools/passport-photo-maker/', 'icon' => '🪪' ],
                    [ 'name' => 'PNG to ICO', 'desc' => 'Convert PNG images to ICO icons for favicons and apps.', 'url' => '/tools/png-to-ico/', 'icon' => 'ICO' ],
                    [ 'name' => 'SVG to PNG', 'desc' => 'Convert SVG vector files to high-resolution PNG.', 'url' => '/tools/svg-to-png/', 'icon' => 'SVG' ],
                ],
            ],
            'text' => [
                'title' => 'Text',
                'tools' => [
                    [ 'name' => 'Case Converter', 'desc' => 'Convert text between UPPERCASE, lowercase, Title Case, and Sentence case.', 'url' => '/tools/case-converter/', 'icon' => 'Aa' ],
                    [ 'name' => 'Word Counter', 'desc' => 'Count words, characters, sentences, and reading time.', 'url' => '/tools/word-counter/', 'icon' => '#' ],
                    [ 'name' => 'Online Sentence Counter', 'desc' => 'Count words, sentences, and paragraphs in any text.', 'url' => '/tools/online-sentence-counter/', 'icon' => '#2' ],
                    [ 'name' => 'Text Cleaner', 'desc' => 'Strip extra spaces, breaks and hidden characters.', 'url' => '/tools/remove-line-breaks/', 'icon' => '⌦' ],
                    [ 'name' => 'Find and Replace', 'desc' => 'Find and replace text with regex support.', 'url' => '/tools/find-and-replace-text/', 'icon' => '↔' ],
                    [ 'name' => 'Character Remover', 'desc' => 'Remove any specific character from text.', 'url' => '/tools/character-remover/', 'icon' => '✕' ],
                    [ 'name' => 'Duplicate Line Remover', 'desc' => 'Remove duplicate lines from a list.', 'url' => '/tools/duplicate-line-remover/', 'icon' => '≡' ],
                    [ 'name' => 'Duplicate Word Finder', 'desc' => 'Find and highlight repeated words in your text.', 'url' => '/tools/duplicate-word-finder/', 'icon' => '🔍' ],
                    [ 'name' => 'Em Dash Remover', 'desc' => 'Remove or replace em and en dashes in your text.', 'url' => '/tools/em-dash-remover/', 'icon' => '—' ],
                    [ 'name' => 'Remove Text Formatting', 'desc' => 'Strip Unicode bold, italic, and cursive styling.', 'url' => '/tools/remove-text-formatting/', 'icon' => 'Tx' ],
                    [ 'name' => 'Remove Underscores', 'desc' => 'Remove or replace underscores in your text.', 'url' => '/tools/remove-underscores/', 'icon' => '_' ],
                    [ 'name' => 'Whitespace Remover', 'desc' => 'Remove extra spaces, tabs, and whitespace.', 'url' => '/tools/whitespace-remover/', 'icon' => '␣' ],
                    [ 'name' => 'Plain Text Converter', 'desc' => 'Strip HTML tags and rich text formatting.', 'url' => '/tools/plain-text-converter/', 'icon' => '¶' ],
                    [ 'name' => 'Sort Lines / Words', 'desc' => 'Sort lines or words A-Z, Z-A, or by length.', 'url' => '/tools/sort-words-alphabetically/', 'icon' => '↕' ],
                    [ 'name' => 'Text Diff', 'desc' => 'Compare two blocks of text and highlight every change.', 'url' => '/tools/text-diff/', 'icon' => '≠' ],
                    [ 'name' => 'Text Summarizer', 'desc' => 'Summarize long text into concise key points.', 'url' => '/tools/text-summarizer/', 'icon' => 'Σ' ],
                    [ 'name' => 'Text to Speech', 'desc' => 'Listen to any text read aloud in your browser.', 'url' => '/tools/text-to-speech/', 'icon' => '▶' ],
                    [ 'name' => 'Speech to Text', 'desc' => 'Turn your spoken words into written text.', 'url' => '/tools/speech-to-text/', 'icon' => '🎙' ],
                    [ 'name' => 'Readability Checker', 'desc' => 'Measure the reading level and clarity of your content.', 'url' => '/tools/readability-checker/', 'icon' => 'R' ],
                    [ 'name' => 'Number to Words', 'desc' => 'Convert numbers into written English words.', 'url' => '/tools/number-to-words/', 'icon' => 'N' ],
                    [ 'name' => 'Words to Number', 'desc' => 'Convert written number words back into digits.', 'url' => '/tools/words-to-number/', 'icon' => '#' ],
                    [ 'name' => 'Word Frequency Counter', 'desc' => 'Count word frequency in any text with sortable results.', 'url' => '/tools/word-frequency-counter/', 'icon' => '📊' ],
                    [ 'name' => 'Wingdings Translator', 'desc' => 'Convert text to Wingdings and back.', 'url' => '/tools/wingdings-translator/', 'icon' => '☻' ],
                    [ 'name' => 'NATO Phonetic Alphabet', 'desc' => 'Translate text to NATO phonetic alphabet (Alpha, Bravo, Charlie).', 'url' => '/tools/nato-phonetic-alphabet/', 'icon' => 'A=' ],
                    [ 'name' => 'Phonetic Spelling Tool', 'desc' => 'Convert words to phonetic spelling.', 'url' => '/tools/phonetic-spelling-tool/', 'icon' => 'f]' ],
                    [ 'name' => 'Pig Latin Translator', 'desc' => 'Translate English to Pig Latin.', 'url' => '/tools/pig-latin-translator/', 'icon' => '🐖' ],
                    [ 'name' => 'Lorem Ipsum Generator', 'desc' => 'Generate placeholder Lorem Ipsum text in any length.', 'url' => '/tools/lorem-ipsum-generator/', 'icon' => 'LI' ],
                    [ 'name' => 'Repeat Text Generator', 'desc' => 'Repeat any text any number of times.', 'url' => '/tools/repeat-text-generator/', 'icon' => '↻' ],
                    [ 'name' => 'Reverse Text Generator', 'desc' => 'Reverse characters, words, or lines of text.', 'url' => '/tools/reverse-text-generator/', 'icon' => '↺' ],
                    [ 'name' => 'Roman Numeral Dates', 'desc' => 'Convert dates and numbers to Roman numerals.', 'url' => '/tools/roman-numeral-dates/', 'icon' => 'XII' ],
                    [ 'name' => 'Word Cloud Generator', 'desc' => 'Create beautiful word clouds from any text.', 'url' => '/tools/word-cloud-generator/', 'icon' => '☁' ],
                ],
            ],
            'case' => [
                'title' => 'Case Converters',
                'tools' => [
                    [ 'name' => 'Sentence Case', 'desc' => 'Convert text to sentence case with proper capitalization.', 'url' => '/tools/sentence-case/', 'icon' => 'S' ],
                    [ 'name' => 'Title Case', 'desc' => 'Convert text to title case for headings and titles.', 'url' => '/tools/title-case/', 'icon' => 'T' ],
                    [ 'name' => 'Camel Case', 'desc' => 'Convert text to camelCase for variables and identifiers.', 'url' => '/tools/camel-case/', 'icon' => 'c' ],
                    [ 'name' => 'Pascal Case', 'desc' => 'Convert text to PascalCase for classes and names.', 'url' => '/tools/pascal-case/', 'icon' => 'P' ],
                    [ 'name' => 'Snake Case', 'desc' => 'Convert text to snake_case for files and databases.', 'url' => '/tools/snake-case/', 'icon' => '_' ],
                    [ 'name' => 'Kebab Case', 'desc' => 'Convert text to kebab-case for URLs and packages.', 'url' => '/tools/kebab-case/', 'icon' => '-' ],
                    [ 'name' => 'Dot Case', 'desc' => 'Convert text to dot.case notation.', 'url' => '/tools/dot-case/', 'icon' => '.' ],
                    [ 'name' => 'Constant Case', 'desc' => 'Convert text to CONSTANT_CASE for configs and enums.', 'url' => '/tools/constant-case/', 'icon' => 'K' ],
                    [ 'name' => 'Alternating Case', 'desc' => 'Convert text to AlTeRnAtInG cAsE for emphasis.', 'url' => '/tools/alternating-case/', 'icon' => 'aA' ],
                    [ 'name' => 'Character Frequency', 'desc' => 'Count the frequency of each character in your text.', 'url' => '/tools/character-frequency-counter/', 'icon' => 'f' ],
                    [ 'name' => 'Invisible Text Generator', 'desc' => 'Generate invisible Unicode characters.', 'url' => '/tools/invisible-text-generator/', 'icon' => '👻' ],
                    [ 'name' => 'Fancy Text Generator', 'desc' => 'Turn plain text into 25+ Unicode font styles.', 'url' => '/tools/fancy-text-generator/', 'icon' => '𝓕' ],
                    [ 'name' => 'Small Text Generator', 'desc' => 'Generate tiny subscript and superscript text.', 'url' => '/tools/small-text-generator/', 'icon' => 'ₛ' ],
                ],
            ],
            'dev' => [
                'title' => 'Developer',
                'tools' => [
                    [ 'name' => 'JSON Formatter', 'desc' => 'Validate, beautify and minify JSON trees.', 'url' => '/tools/json-formatter/', 'icon' => '{}' ],
                    [ 'name' => 'Base64 Encoder', 'desc' => 'Encode and decode text or files safely.', 'url' => '/tools/base64-encode-decode/', 'icon' => 'B64' ],
                    [ 'name' => 'Hash Generator', 'desc' => 'MD5, SHA-1, SHA-256 in one click.', 'url' => '/tools/hash-generator/', 'icon' => '#' ],
                    [ 'name' => 'URL Encoder', 'desc' => 'Escape and unescape query strings.', 'url' => '/tools/url-encode-decode/', 'icon' => 'URL' ],
                    [ 'name' => 'HTML Encode/Decode', 'desc' => 'Encode and decode HTML entities.', 'url' => '/tools/html-encode-decode/', 'icon' => '<>' ],
                    [ 'name' => 'Regex Tester', 'desc' => 'Live matching with capture-group breakdown.', 'url' => '/tools/regex-tester/', 'icon' => '.*' ],
                    [ 'name' => 'QR Generator', 'desc' => 'Create sharp QR codes as SVG or PNG.', 'url' => '/tools/qr-generator/', 'icon' => 'QR' ],
                    [ 'name' => 'QR Code Reader', 'desc' => 'Scan and read QR codes straight from your camera or an image.', 'url' => '/tools/qr-code-reader/', 'icon' => 'QR' ],
                    [ 'name' => 'Barcode Generator', 'desc' => 'Generate barcodes in common formats.', 'url' => '/tools/barcode-generator/', 'icon' => 'BAR' ],
                    [ 'name' => 'JWT Decoder', 'desc' => 'Decode and inspect JSON Web Tokens.', 'url' => '/tools/jwt-decoder/', 'icon' => 'JWT' ],
                    [ 'name' => 'Slugify URL', 'desc' => 'Turn any text into a clean, URL-friendly slug.', 'url' => '/tools/slugify-url/', 'icon' => 'URL' ],
                    [ 'name' => 'UTM Builder', 'desc' => 'Build trackable UTM campaign links.', 'url' => '/tools/utm-builder/', 'icon' => 'UTM' ],
                    [ 'name' => 'Color Converter', 'desc' => 'Convert between HEX, RGB, HSL, and CMYK.', 'url' => '/tools/color-converter/', 'icon' => '◧' ],
                    [ 'name' => 'Color Picker', 'desc' => 'Pick colors and grab HEX / RGB values from the web.', 'url' => '/tools/color-picker/', 'icon' => '◨' ],
                    [ 'name' => 'Gradient Generator', 'desc' => 'Create beautiful CSS gradients quickly.', 'url' => '/tools/gradient-generator/', 'icon' => '▭' ],
                    [ 'name' => 'IP Address Lookup', 'desc' => 'Look up the details of any IP address.', 'url' => '/tools/ip-address-lookup/', 'icon' => 'IP' ],
                    [ 'name' => 'What is my User Agent', 'desc' => 'Identify your exact browser user agent string.', 'url' => '/tools/what-is-my-user-agent/', 'icon' => 'UA' ],
                ],
            ],
            'dev_convert' => [
                'title' => 'Data & Code Tools',
                'tools' => [
                    [ 'name' => 'Morse Code Translator', 'desc' => 'Translate text to and from Morse code.', 'url' => '/tools/morse-code-translator/', 'icon' => '··' ],
                    [ 'name' => 'Binary Translator', 'desc' => 'Translate text to and from binary code.', 'url' => '/tools/binary-translator/', 'icon' => '01' ],
                    [ 'name' => 'Hex to Text', 'desc' => 'Convert hexadecimal values to readable text.', 'url' => '/tools/hex-to-text/', 'icon' => '0x' ],
                    [ 'name' => 'Text to Hex', 'desc' => 'Convert text to hexadecimal values.', 'url' => '/tools/text-to-hex/', 'icon' => '0x' ],
                    [ 'name' => 'Binary to Decimal', 'desc' => 'Convert binary numbers to decimal instantly.', 'url' => '/tools/binary-to-decimal/', 'icon' => '10' ],
                    [ 'name' => 'Decimal to Binary', 'desc' => 'Convert decimal numbers to binary.', 'url' => '/tools/decimal-to-binary/', 'icon' => '10' ],
                    [ 'name' => 'HTML to Markdown', 'desc' => 'Convert HTML markup to clean Markdown.', 'url' => '/tools/html-to-markdown/', 'icon' => 'MD' ],
                    [ 'name' => 'Markdown Preview', 'desc' => 'Preview and render Markdown in real time.', 'url' => '/tools/markdown-preview/', 'icon' => 'MD' ],
                    [ 'name' => 'Markdown Table Generator', 'desc' => 'Generate clean Markdown tables from pasted data.', 'url' => '/tools/markdown-table-generator/', 'icon' => 'MD' ],
                    [ 'name' => 'HTML Formatter', 'desc' => 'Beautify and tidy minified HTML.', 'url' => '/tools/html-formatter/', 'icon' => 'HTML' ],
                    [ 'name' => 'CSS Formatter', 'desc' => 'Beautify and tidy minified CSS.', 'url' => '/tools/css-formatter/', 'icon' => 'CSS' ],
                    [ 'name' => 'JavaScript Formatter', 'desc' => 'Beautify and tidy minified JavaScript.', 'url' => '/tools/js-formatter/', 'icon' => 'JS' ],
                    [ 'name' => 'JSON to TypeScript', 'desc' => 'Generate TypeScript interfaces from JSON.', 'url' => '/tools/json-to-typescript/', 'icon' => 'TS' ],
                    [ 'name' => 'JSON to CSV', 'desc' => 'Convert JSON arrays to CSV.', 'url' => '/tools/json-to-csv/', 'icon' => 'CSV' ],
                    [ 'name' => 'CSV to JSON', 'desc' => 'Convert CSV data to JSON.', 'url' => '/tools/csv-to-json/', 'icon' => 'CSV' ],
                    [ 'name' => 'JSON to YAML', 'desc' => 'Convert JSON to YAML markup.', 'url' => '/tools/json-to-yaml/', 'icon' => 'YML' ],
                    [ 'name' => 'YAML to JSON', 'desc' => 'Convert YAML markup to JSON.', 'url' => '/tools/yaml-to-json/', 'icon' => 'YML' ],
                    [ 'name' => 'XML Formatter', 'desc' => 'Beautify and validate XML documents.', 'url' => '/tools/xml-formatter/', 'icon' => 'XML' ],
                    [ 'name' => 'SQL Formatter', 'desc' => 'Beautify and tidy SQL queries.', 'url' => '/tools/sql-formatter/', 'icon' => 'SQL' ],
                ],
            ],
            'cipher' => [
                'title' => 'Ciphers & Encoding',
                'tools' => [
                    [ 'name' => 'Caesar Cipher', 'desc' => 'Encode or decode text with a Caesar shift.', 'url' => '/tools/caesar-cipher/', 'icon' => 'C' ],
                    [ 'name' => 'ROT13 Encoder', 'desc' => 'Encode / decode text with a ROT13 rotation.', 'url' => '/tools/rot13/', 'icon' => 'R13' ],
                    [ 'name' => 'A1Z26 Cipher', 'desc' => 'Encode text to numeric A1Z26 positions.', 'url' => '/tools/a1z26-cipher/', 'icon' => 'A1' ],
                    [ 'name' => 'Vigenere Cipher', 'desc' => 'Encode / decode text with a Vigenere key.', 'url' => '/tools/vigenere-cipher/', 'icon' => 'V' ],
                    [ 'name' => 'Atbash Cipher', 'desc' => 'Encode / decode text with the Atbash substitution.', 'url' => '/tools/atbash-cipher/', 'icon' => 'A' ],
                    [ 'name' => 'UTF-8 Encoder', 'desc' => 'Encode / decode UTF-8 byte sequences.', 'url' => '/tools/utf8-encoder/', 'icon' => '8' ],
                    [ 'name' => 'HTML Entity Encoder', 'desc' => 'Encode / decode HTML entities and symbols.', 'url' => '/tools/html-entity-encoder/', 'icon' => '&' ],
                    [ 'name' => 'Unicode Inspector', 'desc' => 'Inspect the codepoints and properties of any character.', 'url' => '/tools/unicode-inspector/', 'icon' => 'U+' ],
                    [ 'name' => 'Unicode Translator', 'desc' => 'Convert text to special Unicode characters.', 'url' => '/tools/unicode-translator/', 'icon' => 'U+' ],
                ],
            ],
            'calc' => [
                'title' => 'Calculators',
                'tools' => [
                    [ 'name' => 'Age Calculator', 'desc' => 'Calculate exact age from any date of birth.', 'url' => '/tools/age-calculator/', 'icon' => '🎂' ],
                    [ 'name' => 'Percentage Calculator', 'desc' => 'Work out percentage values and changes.', 'url' => '/tools/percentage-calculator/', 'icon' => '%' ],
                    [ 'name' => 'Tip Calculator', 'desc' => 'Split bills and calculate tips quickly.', 'url' => '/tools/tip-calculator/', 'icon' => '💵' ],
                    [ 'name' => 'Date Calculator', 'desc' => 'Add, subtract, or compare dates and durations.', 'url' => '/tools/date-calculator/', 'icon' => '📅' ],
                    [ 'name' => 'BMI Calculator', 'desc' => 'Calculate your Body Mass Index.', 'url' => '/tools/bmi-calculator/', 'icon' => '⚖' ],
                    [ 'name' => 'Loan / EMI Calculator', 'desc' => 'Estimate monthly loan EMI payments.', 'url' => '/tools/loan-emi-calculator/', 'icon' => '🏦' ],
                    [ 'name' => 'Compound Interest Calculator', 'desc' => 'Compute compound interest on savings or loans.', 'url' => '/tools/compound-interest/', 'icon' => '📈' ],
                    [ 'name' => 'GST / VAT Calculator', 'desc' => 'Add or remove GST / VAT from any amount.', 'url' => '/tools/gst-calculator/', 'icon' => 'GST' ],
                    [ 'name' => 'Discount Calculator', 'desc' => 'Calculate final prices after discounts.', 'url' => '/tools/discount-calculator/', 'icon' => '%' ],
                    [ 'name' => 'Currency Converter', 'desc' => 'Convert between world currencies.', 'url' => '/tools/currency-converter/', 'icon' => '💱' ],
                    [ 'name' => 'Unit Converter', 'desc' => 'Convert length, weight, volume, and more.', 'url' => '/tools/unit-converter/', 'icon' => 'U' ],
                    [ 'name' => 'Time Zone Converter', 'desc' => 'Convert times across different time zones.', 'url' => '/tools/timezone-converter/', 'icon' => '🕐' ],
                ],
            ],
            'gen' => [
                'title' => 'Generators',
                'tools' => [
                    [ 'name' => 'Random Number Generator', 'desc' => 'Generate random integers, decimals, or multiples in any range.', 'url' => '/tools/random-number-generator/', 'icon' => '#' ],
                    [ 'name' => 'Random Password Generator', 'desc' => 'Create cryptographically secure passwords with a live strength meter.', 'url' => '/tools/password-generator/', 'icon' => '🔒', 'tag' => 'Popular' ],
                    [ 'name' => 'Random UUID Generator', 'desc' => 'Generate UUID v1, v4, v5, ULID, and NanoID identifiers in bulk.', 'url' => '/tools/uuid-generator/', 'icon' => 'UUID' ],
                    [ 'name' => 'Random Date Generator', 'desc' => 'Generate random dates between any two dates.', 'url' => '/tools/random-date-generator/', 'icon' => '📅' ],
                    [ 'name' => 'Random IP Generator', 'desc' => 'Generate random IPv4 and IPv6 addresses in bulk.', 'url' => '/tools/random-ip-generator/', 'icon' => 'IP' ],
                    [ 'name' => 'Random Letter Generator', 'desc' => 'Generate random letters with case, type, and frequency controls.', 'url' => '/tools/random-letter-generator/', 'icon' => 'AZ' ],
                    [ 'name' => 'Random Month Generator', 'desc' => 'Pick random months filtered by season or quarter.', 'url' => '/tools/random-month-generator/', 'icon' => 'Mo' ],
                    [ 'name' => 'Random Name Generator', 'desc' => 'Generate random human names and usernames.', 'url' => '/tools/random-name-generator/', 'icon' => '👤' ],
                    [ 'name' => 'Team Name Generator', 'desc' => 'Generate cool team names for your group.', 'url' => '/tools/team-name-generator/', 'icon' => '🎽' ],
                    [ 'name' => 'Fake Name Generator', 'desc' => 'Generate realistic fake identities and details.', 'url' => '/tools/fake-name-generator/', 'icon' => '🪪' ],
                    [ 'name' => 'Dice Roller', 'desc' => 'Roll die with any number of sides.', 'url' => '/tools/dice-roller/', 'icon' => '🎲' ],
                    [ 'name' => 'Coin Flipper', 'desc' => 'Flip a virtual coin with instant results.', 'url' => '/tools/coin-flipper/', 'icon' => '🪙' ],
                    [ 'name' => 'Random Emoji Picker', 'desc' => 'Pick a random emoji to add some fun.', 'url' => '/tools/random-emoji-picker/', 'icon' => '🎭' ],
                    [ 'name' => 'Choice Picker', 'desc' => 'Randomly pick from any list of choices.', 'url' => '/tools/spinpick-choice-picker/', 'icon' => '🎲' ],
                    [ 'name' => 'APA Format Generator', 'desc' => 'Generate APA 7th edition citations and references.', 'url' => '/tools/apa-format-generator/', 'icon' => '📚' ],
                    [ 'name' => 'Online Notepad', 'desc' => 'Free online notepad with auto-save - write notes in your browser.', 'url' => '/tools/online-notepad/', 'icon' => '📝' ],
                    [ 'name' => 'Password Strength Checker', 'desc' => 'Check how strong a password really is.', 'url' => '/tools/password-strength-checker/', 'icon' => '🔐' ],
                    [ 'name' => 'Credit Card Validator', 'desc' => 'Validate credit card numbers with Luhn checks.', 'url' => '/tools/credit-card-validator/', 'icon' => '💳' ],
                ],
            ],
            'fonts' => [
                'title' => 'Fonts & Text Styles',
                'tools' => [
                    [ 'name' => 'Font Generator', 'desc' => 'Generate stylish Unicode fonts for social media.', 'url' => '/tools/font-generator/', 'icon' => 'Ff' ],
                    [ 'name' => 'Font Pair Generator', 'desc' => 'Discover great Google Font pairings.', 'url' => '/tools/font-pair-generator/', 'icon' => 'Ff' ],
                    [ 'name' => 'Text to Handwriting', 'desc' => 'Turn typed text into realistic handwriting.', 'url' => '/tools/text-to-handwriting/', 'icon' => '✍' ],
                    [ 'name' => 'WhatsApp Font Generator', 'desc' => 'Style your WhatsApp messages with fancy fonts.', 'url' => '/tools/whatsapp-font-generator-702/', 'icon' => 'W' ],
                    [ 'name' => 'Instagram Font Generator', 'desc' => 'Get fancy fonts for Instagram bios and posts.', 'url' => '/tools/instagram-font-generator-702/', 'icon' => 'IG' ],
                    [ 'name' => 'Facebook Font Generator', 'desc' => 'Style your Facebook text with Unicode fonts.', 'url' => '/tools/facebook-font-generator-702/', 'icon' => 'FB' ],
                    [ 'name' => 'Twitter / X Font Generator', 'desc' => 'Make your tweets stand out with fancy fonts.', 'url' => '/tools/twitter-font-generator-702/', 'icon' => 'X' ],
                    [ 'name' => 'TikTok Font Generator', 'desc' => 'Generate stylish fonts for TikTok captions.', 'url' => '/tools/tiktok-font-generator-702/', 'icon' => 'TT' ],
                    [ 'name' => 'Discord Font Generator', 'desc' => 'Style up your Discord username and messages.', 'url' => '/tools/discord-font-generator-702/', 'icon' => 'D' ],
                ],
            ],
            'ai' => [
                'title' => 'AI & Prompts',
                'tools' => [
                    [ 'name' => 'ChatGPT Prompt Generator', 'desc' => 'Generate effective prompts for ChatGPT.', 'url' => '/tools/chatgpt-prompt-generator/', 'icon' => 'AI' ],
                    [ 'name' => 'Claude Prompt Generator', 'desc' => 'Build strong prompts for Claude.', 'url' => '/tools/claude-prompt-generator/', 'icon' => 'AI' ],
                    [ 'name' => 'Gemini Prompt Generator', 'desc' => 'Create powerful prompts for Google Gemini.', 'url' => '/tools/gemini-prompt-generator/', 'icon' => 'AI' ],
                    [ 'name' => 'Perplexity AI Prompt Generator', 'desc' => 'Craft prompts for Perplexity AI.', 'url' => '/tools/perplexity-prompt-generator/', 'icon' => 'AI' ],
                    [ 'name' => 'Prompt Optimizer', 'desc' => 'Improve and refine your AI prompts.', 'url' => '/tools/prompt-optimizer/', 'icon' => 'AI' ],
                ],
            ],
            'seo' => [
                'title' => 'SEO & Web',
                'tools' => [
                    [ 'name' => 'Meta Description Generator', 'desc' => 'Write compelling meta descriptions.', 'url' => '/tools/meta-description-generator/', 'icon' => 'MD' ],
                    [ 'name' => 'Meta Tag Generator', 'desc' => 'Generate meta tags for your web pages.', 'url' => '/tools/meta-tag-generator/', 'icon' => 'MT' ],
                    [ 'name' => 'Hashtag Generator', 'desc' => 'Find the best hashtags for your content.', 'url' => '/tools/hashtag-generator/', 'icon' => '#' ],
                    [ 'name' => 'Keyword Density Checker', 'desc' => 'Check keyword usage and density in text.', 'url' => '/tools/keyword-density-checker/', 'icon' => 'KW' ],
                    [ 'name' => 'Robots.txt Generator', 'desc' => 'Generate a robots.txt for your site.', 'url' => '/tools/robots-txt-generator/', 'icon' => 'RB' ],
                    [ 'name' => 'Schema Markup Generator', 'desc' => 'Generate structured data markup.', 'url' => '/tools/schema-markup-generator/', 'icon' => '{}' ],
                    [ 'name' => 'XML Sitemap Generator', 'desc' => 'Generate an XML sitemap for your website.', 'url' => '/tools/xml-sitemap-generator/', 'icon' => 'MAP' ],
                    [ 'name' => 'Page Speed Checker', 'desc' => 'Analyze and check page load speed.', 'url' => '/tools/page-speed-checker/', 'icon' => '⏱' ],
                    [ 'name' => 'YouTube Title Generator', 'desc' => 'Generate click-worthy YouTube titles.', 'url' => '/tools/youtube-title-generator/', 'icon' => '▶' ],
                    [ 'name' => 'Instagram Caption Generator', 'desc' => 'Generate catchy Instagram captions.', 'url' => '/tools/instagram-caption-generator/', 'icon' => 'IG' ],
                    [ 'name' => 'Social Character Counter', 'desc' => 'Count characters for any social platform limits.', 'url' => '/tools/social-character-counter/', 'icon' => '#' ],
                ],
            ],
            'cheat' => [
                'title' => 'Cheat Sheets',
                'tools' => [
                    [ 'name' => 'Markdown Cheat Sheet', 'desc' => 'Quick reference for Markdown syntax.', 'url' => '/tools/markdown-cheat-sheet/', 'icon' => 'MD' ],
                    [ 'name' => 'JSON Cheat Sheet', 'desc' => 'Quick reference for JSON syntax.', 'url' => '/tools/json-cheat-sheet/', 'icon' => '{}' ],
                    [ 'name' => 'Regex Cheat Sheet', 'desc' => 'Quick reference for regular expressions.', 'url' => '/tools/regex-cheat-sheet/', 'icon' => '.*' ],
                    [ 'name' => 'SQL Cheat Sheet', 'desc' => 'Quick reference for SQL commands.', 'url' => '/tools/sql-cheat-sheet/', 'icon' => 'SQL' ],
                    [ 'name' => 'Git Cheat Sheet', 'desc' => 'Quick reference for everyday Git commands.', 'url' => '/tools/git-cheat-sheet/', 'icon' => 'GIT' ],
                    [ 'name' => 'Linux Commands Cheat Sheet', 'desc' => 'Handy reference for Linux terminal commands.', 'url' => '/tools/linux-commands-cheat-sheet/', 'icon' => '>_' ],
                    [ 'name' => 'Tailwind CSS Cheat Sheet', 'desc' => 'Quick reference for Tailwind CSS utilities.', 'url' => '/tools/tailwind-css-cheat-sheet/', 'icon' => 'TW' ],
                ],
            ],
            'webdev' => [
                'title' => 'Web & CSS Tools',
                'tools' => [
                    [ 'name' => 'CSS Gradient Previewer', 'desc' => 'Preview and copy CSS gradient values.', 'url' => '/tools/css-gradient-previewer/', 'icon' => '▭' ],
                    [ 'name' => 'Border Radius Generator', 'desc' => 'Generate and preview border radius CSS.', 'url' => '/tools/border-radius-generator/', 'icon' => '◍' ],
                    [ 'name' => 'Box Shadow Generator', 'desc' => 'Generate and preview box-shadow CSS.', 'url' => '/tools/box-shadow-generator/', 'icon' => '▣' ],
                    [ 'name' => 'Flexbox Playground', 'desc' => 'Experiment with CSS flexbox visually.', 'url' => '/tools/flexbox-playground/', 'icon' => '⇉' ],
                    [ 'name' => 'CSS Grid Playground', 'desc' => 'Experiment with CSS grid visually.', 'url' => '/tools/css-grid-playground/', 'icon' => '▦' ],
                    [ 'name' => 'HTML Preview', 'desc' => 'Write and preview HTML / CSS in a live editor.', 'url' => '/tools/html-preview/', 'icon' => '<>' ],
                    [ 'name' => 'WiFi QR Code Generator', 'desc' => 'Generate a QR code to share your WiFi password.', 'url' => '/tools/wifi-qr-code-generator/', 'icon' => 'WiFi' ],
                ],
            ],
        ];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_tools_content',
            [ 'label' => __( 'Tools Content', 'textcrafttoolspro' ) ]
        );

        $this->add_control(
            'active_category',
            [
                'label'   => __( 'Default Active Category', 'textcrafttoolspro' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'all',
                'options' => [
                    'all'      => 'All',
                    'pdf'      => 'PDF',
                    'compress' => 'Compression',
                    'image'    => 'Image & Media',
                    'text'     => 'Text',
                    'dev'      => 'Developer',
                    'gen'      => 'Generators',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings   = $this->get_settings_for_display();
        $tools_data = $this->get_tools_data();
        $active     = $settings['active_category'] ?? 'all';

        /* Count total tools */
        $total = 0;
        foreach ( $tools_data as $cat ) {
            $total += count( $cat['tools'] );
        }

        $this->add_render_attribute( 'wrapper', 'class', 'tctp-main' );
        $this->add_render_attribute( 'wrapper', 'id', 'tools' );
        ?>
        <div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
            <aside class="tctp-rail">
                <p>Categories</p>
                <button class="tctp-rail-link<?php echo 'all' === $active ? ' on' : ''; ?>" data-category="all">
                    All
                    <i><?php echo $total; ?></i>
                </button>
                <?php foreach ( $tools_data as $cat_key => $cat ) :
                    $count = count( $cat['tools'] );
                    $on    = $active === $cat_key ? ' on' : '';
                ?>
                    <button class="tctp-rail-link<?php echo $on; ?>" data-category="<?php echo esc_attr( $cat_key ); ?>">
                        <?php echo esc_html( $cat['title'] ); ?>
                        <i><?php echo $count; ?></i>
                    </button>
                <?php endforeach; ?>
            </aside>

            <div class="tctp-sections-wrap">
                <?php foreach ( $tools_data as $cat_key => $cat ) :
                    $count = count( $cat['tools'] );
                ?>
                    <section class="tctp-section" id="tctp-<?php echo esc_attr( $cat_key ); ?>">
                        <div class="tctp-sec-head">
                            <h2><?php echo esc_html( $cat['title'] ); ?></h2>
                            <span><?php echo $count; ?> tools</span>
                            <hr>
                        </div>
                        <div class="tctp-grid">
                            <?php foreach ( $cat['tools'] as $tool ) :
                                $tag_html = '';
                                if ( ! empty( $tool['tag'] ) ) {
                                    $tag_html = '<span class="tctp-tag">' . esc_html( $tool['tag'] ) . '</span>';
                                }
                            ?>
                                <a class="tctp-card" href="<?php echo esc_url( home_url( $tool['url'] ) ); ?>" data-name="<?php echo esc_attr( strtolower( $tool['name'] ) ); ?>">
                                    <div class="tctp-ico"><?php echo esc_html( $tool['icon'] ); ?></div>
                                    <h3><?php echo esc_html( $tool['name'] ); ?><?php echo $tag_html; ?></h3>
                                    <p><?php echo esc_html( $tool['desc'] ); ?></p>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    protected function content_template() {}
}
