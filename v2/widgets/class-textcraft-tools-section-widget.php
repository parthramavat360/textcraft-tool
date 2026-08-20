<?php
/**
 * Tools Section Elementor Widget
 *
 * All 74 tools from textcrafttools.com, 6 categories matching HTML design.
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

    private function get_tools_data() {
        return [
            'pdf' => [
                'title' => 'PDF',
                'tools' => [
                    [ 'name' => 'PDF Compressor', 'desc' => 'Compress PDF files online with preview, compression levels, and instant browser download.', 'url' => '/tools/pdf-compressor/', 'icon' => 'PDF' ],
                    [ 'name' => 'PDF to Word Converter', 'desc' => 'Convert PDF to Word DOCX online - editable documents with a fast server-side converter.', 'url' => '/tools/pdf-to-word-converter/', 'icon' => 'DOC', 'tag' => 'Popular' ],
                    [ 'name' => 'PDF Splitter', 'desc' => 'Split a PDF online into multiple documents by range, pages, or file size.', 'url' => '/tools/pdf-splitter/', 'icon' => '✂' ],
                    [ 'name' => 'PDF Merger', 'desc' => 'Merge multiple PDF files into one document online - free and secure.', 'url' => '/tools/pdf-merger/', 'icon' => '＋' ],
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
                ],
            ],
            'text' => [
                'title' => 'Text',
                'tools' => [
                    [ 'name' => 'Case Converter', 'desc' => 'Convert text between UPPERCASE, lowercase, Title Case, and Sentence case.', 'url' => '/tools/case-converter/', 'icon' => 'Aa' ],
                    [ 'name' => 'Word Counter', 'desc' => 'Count words, characters, sentences, and reading time.', 'url' => '/tools/online-sentence-counter/', 'icon' => '#' ],
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
                    [ 'name' => 'Sort Words Alphabetically', 'desc' => 'Sort words or lines A-Z, Z-A, or by length.', 'url' => '/tools/sort-words-alphabetically/', 'icon' => '↕' ],
                    [ 'name' => 'Wingdings Translator', 'desc' => 'Convert text to Wingdings and back.', 'url' => '/tools/wingdings-translator/', 'icon' => '☻' ],
                    [ 'name' => 'NATO Phonetic Alphabet', 'desc' => 'Translate text to NATO phonetic alphabet (Alpha, Bravo, Charlie).', 'url' => '/tools/nato-phonetic-alphabet/', 'icon' => 'A=' ],
                    [ 'name' => 'Phonetic Spelling Tool', 'desc' => 'Convert words to phonetic spelling.', 'url' => '/tools/phonetic-spelling-tool/', 'icon' => 'f]' ],
                    [ 'name' => 'Pig Latin Translator', 'desc' => 'Translate English to Pig Latin.', 'url' => '/tools/pig-latin-translator/', 'icon' => '🐖' ],
                    [ 'name' => 'Word Frequency Counter', 'desc' => 'Count word frequency in any text with sortable results.', 'url' => '/tools/word-frequency-counter/', 'icon' => '📊' ],
                ],
            ],
            'dev' => [
                'title' => 'Developer',
                'tools' => [
                    [ 'name' => 'JSON Formatter', 'desc' => 'Validate, beautify and minify JSON trees.', 'url' => '/tools/json-formatter/', 'icon' => '{}' ],
                    [ 'name' => 'Base64 Encoder', 'desc' => 'Encode and decode text or files safely.', 'url' => '/tools/base64-encode-decode/', 'icon' => 'B64' ],
                    [ 'name' => 'Hash Generator', 'desc' => 'MD5, SHA-1, SHA-256 in one click.', 'url' => '/tools/hash-generator/', 'icon' => '#' ],
                    [ 'name' => 'URL Encoder', 'desc' => 'Escape and unescape query strings.', 'url' => '/tools/url-encode-decode/', 'icon' => 'URL' ],
                    [ 'name' => 'Regex Tester', 'desc' => 'Live matching with capture-group breakdown.', 'url' => '/tools/regex-tester/', 'icon' => '.*' ],
                    [ 'name' => 'QR Generator', 'desc' => 'Create sharp QR codes as SVG or PNG.', 'url' => '/tools/qr-generator/', 'icon' => 'QR' ],
                    [ 'name' => 'HTML Encode/Decode', 'desc' => 'Encode and decode HTML entities.', 'url' => '/tools/html-encode-decode/', 'icon' => '<>' ],
                    [ 'name' => 'Morse Code Translator', 'desc' => 'Translate text to and from Morse code.', 'url' => '/tools/morse-code-translator/', 'icon' => '··' ],
                    [ 'name' => 'Binary Translator', 'desc' => 'Translate text to and from binary code.', 'url' => '/tools/binary-translator/', 'icon' => '01' ],
                    [ 'name' => 'Unicode Translator', 'desc' => 'Convert text to special Unicode characters.', 'url' => '/tools/unicode-translator/', 'icon' => 'U+' ],
                    [ 'name' => 'Character Frequency', 'desc' => 'Count the frequency of each character in your text.', 'url' => '/tools/character-frequency-counter/', 'icon' => 'f' ],
                    [ 'name' => 'Invisible Text Generator', 'desc' => 'Generate invisible Unicode characters.', 'url' => '/tools/invisible-text-generator/', 'icon' => '👻' ],
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
                    [ 'name' => 'Choice Picker', 'desc' => 'Randomly pick from any list of choices.', 'url' => '/tools/spinpick-choice-picker/', 'icon' => '🎲' ],
                    [ 'name' => 'APA Format Generator', 'desc' => 'Generate APA 7th edition citations and references.', 'url' => '/tools/apa-format-generator/', 'icon' => '📚' ],
                    [ 'name' => 'Online Notepad', 'desc' => 'Free online notepad with auto-save - write notes in your browser.', 'url' => '/tools/online-notepad/', 'icon' => '📝' ],
                    [ 'name' => 'Repeat Text Generator', 'desc' => 'Repeat any text any number of times.', 'url' => '/tools/repeat-text-generator/', 'icon' => '↻' ],
                    [ 'name' => 'Reverse Text Generator', 'desc' => 'Reverse characters, words, or lines of text.', 'url' => '/tools/reverse-text-generator/', 'icon' => '↺' ],
                    [ 'name' => 'Roman Numeral Dates', 'desc' => 'Convert dates and numbers to Roman numerals.', 'url' => '/tools/roman-numeral-dates/', 'icon' => 'XII' ],
                    [ 'name' => 'Word Cloud Generator', 'desc' => 'Create beautiful word clouds from any text.', 'url' => '/tools/word-cloud-generator/', 'icon' => '☁' ],
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
