# TextCraft Tools

**Version:** 1.0.0  
**Requires:** WordPress 6.0+, Elementor 3.10+, PHP 8.0+  
**License:** GPL-2.0-or-later  
**Text Domain:** textcraft-tools

A comprehensive suite of 70+ free online text, image, and PDF tools built as native Elementor widgets. All processing runs client-side in the browser — no data is ever uploaded to any server.

---

## Features

- **70+ browser-based tools** across 9 categories
- **Privacy-first**: all processing happens locally via JavaScript/WASM — zero server uploads
- **Dark theme** design system with configurable accent colors via Elementor panel
- **SEO-optimised** auto-generated content sections (intro, how-to, features, benefits, FAQ)
- **Fully responsive** — works on mobile, tablet, and desktop
- **Accessible** — ARIA labels, keyboard shortcuts, screen-reader support

---

## Widget Categories & Tools

### Case Converters
| Widget | Description |
|--------|-------------|
| Case Converter | UPPERCASE, lowercase, Sentence, Title, Capitalized, Alternating, Inverse |
| Sentence Case Converter | Capitalises first letter of every sentence |
| Title Case Converter | Capitalises major words for headlines |

### Text Cleaners
| Widget | Description |
|--------|-------------|
| Character Remover | Delete specific characters from text |
| Duplicate Line Remover | Remove repeated lines |
| Duplicate Word Finder | Find and remove repeated words |
| Em Dash Remover | Replace or remove em/en dashes |
| Remove Line Breaks | Strip line breaks and join lines |
| Remove Text Formatting | Strip bold, italic, HTML styling |
| Remove Underscores | Replace or remove underscores |
| Whitespace Remover | Strip extra whitespace |
| Plain Text Converter | Convert HTML/rich text to plain text |

### Text Generators
| Widget | Description |
|--------|-------------|
| APA Format Generator | Generate APA 7th edition citations |
| Invisible Text Generator | Create zero-width and blank Unicode characters |
| Online Notepad | Browser-based notepad with auto-save |
| Repeat Text Generator | Repeat text N times |
| Reverse Text Generator | Reverse characters, words, or lines |
| Roman Numeral Date Converter | Convert dates to Roman numerals |
| Word Cloud Generator | Generate word frequency clouds |

### Random Generators
| Widget | Description |
|--------|-------------|
| Random Choice Picker | Pick randomly from a list |
| Random Date Generator | Generate random dates between ranges |
| Random IP Generator | Generate random IPv4/IPv6 addresses |
| Random Letter Generator | Generate random letters with case/type controls |
| Random Month Generator | Pick random months by season or quarter |
| Random Number Generator | Generate random integers, decimals, or multiples |
| Password Generator | Cryptographically secure passwords with strength meter |
| UUID Generator | Generate UUIDs in multiple formats |

### Translators & Counters
| Widget | Description |
|--------|-------------|
| Find and Replace Text | Search and replace with regex support |
| NATO Phonetic Alphabet | Translate text to Alpha-Bravo-Charlie |
| Sentence Counter | Count sentences, words, characters |
| Phonetic Spelling Tool | Convert words to phonetic spelling |
| Pig Latin Translator | Translate English to Pig Latin |
| Sort Words Alphabetically | Sort lines/words A-Z or Z-A |
| Wingdings Translator | Convert text to/from Wingdings |
| Word Frequency Counter | Count word occurrences with frequency bars |

### Image Compression
| Widget | Description |
|--------|-------------|
| JPG Compressor | Compress JPG with preview and batch download |
| PNG Compressor | Compress PNG with color reduction |
| WebP Compressor | Compress WebP images |
| GIF Compressor | Compress GIF with frame control |
| SVG Compressor | Optimize SVG files |

### Image & Media Conversion
| Widget | Description |
|--------|-------------|
| ASCII Art Generator | Convert images to ASCII art |
| Image to Text (OCR) | Extract text from images via Tesseract.js |
| Remove Background | Remove image backgrounds (browser-based AI) |
| JPG ↔ PNG | Bidirectional JPG/PNG conversion |
| JPG ↔ WebP | Bidirectional JPG/WebP conversion |
| JPG ↔ SVG | JPG/PNG/HEIC to SVG conversion |
| JPG ↔ GIF | JPG to GIF (static and animated) |
| JPG ↔ HEIC | Bidirectional JPG/HEIC conversion |
| JPG → AVIF | JPG/PNG to next-gen AVIF format |
| PNG ↔ WebP | Bidirectional PNG/WebP conversion |
| PNG ↔ HEIC | Bidirectional PNG/HEIC conversion |
| HEIC ↔ JPG/PNG/SVG | HEIC to universal formats |
| WebP ↔ JPG/PNG | WebP to universal formats |
| JPG/PNG → PDF | Images to PDF document |
| Video Converter | Browser-based video conversion (FFmpeg.wasm) |

### PDF Tools
| Widget | Description |
|--------|-------------|
| PDF Compressor | Reduce PDF file size |
| PDF Merger | Combine multiple PDFs into one |
| PDF Splitter | Split PDF by ranges, pages, or size |
| PDF to JPG | Convert PDF pages to JPG images |
| PDF to PNG | Convert PDF pages to PNG images |
| PDF to Word | Server-side PDF to DOCX conversion (LibreOffice) |
| Rotate PDF | Rotate PDF pages |
| Delete PDF Pages | Select and remove specific pages |

### Layout / Sections
| Widget | Description |
|--------|-------------|
| All Tools Page | Full directory with search and category filtering |
| Features Section | Promotional feature cards section |
| SEO Cases Section | SEO-optimised case studies section |
| Tools Grid Section | Grid of tool cards with icons |

---

## Installation

1. Upload the `textcraft-tools` folder to `/wp-content/plugins/`
2. Activate the plugin through the **Plugins** menu in WordPress
3. Ensure **Elementor** (free version is sufficient) is installed and activated
4. Open any page in the Elementor editor
5. Find the **TextCraft Tools** category in the Elementor widget panel
6. Drag any tool widget onto your page and configure via the Elementor panel

## Usage

Each widget provides:
- **Elementor panel controls** — title, subtitle, badge text, accent color, card background
- **Interactive tool UI** — input areas, buttons, file uploads, live stats
- **Auto-generated SEO content** — intro, how-to steps, features, benefits, use-cases, FAQ accordion

### Common Controls
All widgets share these panel options:
- **Content tab**: Title, Subtitle, Badge (toggle + text)
- **Style tab**: Accent Color, Card Background Color

### REST API
```
POST /wp-json/textcraft-tools/v1/pdf-to-word
```
Converts PDF to DOCX via server-side LibreOffice (requires soffice installed).  
Nonce verification via `X-WP-Nonce` header.

---

## Privacy

**All text processing, image conversion, and PDF manipulation happens in the visitor's browser.** No content is uploaded to any server — with two exceptions:

1. **PDF to Word Converter** — uploads PDF to the WordPress server for LibreOffice conversion (can be replaced via the `textcraft_pdf_to_word_converter` filter)
2. **CDN-loaded libraries** — Tesseract.js (OCR) and FFmpeg.wasm (video) load from CDN; no user data is transmitted

---

## Requirements

- WordPress 6.0+
- PHP 8.0+
- Elementor 3.10+ (free version)
- Modern browser with JavaScript enabled

### Optional
- LibreOffice (soffice) for PDF to Word conversion
- Tesseract.js CDN for OCR functionality
- FFmpeg.wasm CDN for video conversion

---

## Development

### Project Structure
```
textcraft-tools/
├── textcraft-tools.php          # Plugin bootstrap
├── readme.txt                   # WordPress.org readme
├── assets/
│   ├── css/
│   │   ├── textcraft-tools.css  # Widget design system (~2613 lines)
│   │   └── textcraft-megamenu.css # Mega menu styles (~638 lines)
│   └── js/
│       ├── textcraft-case-converter.js  # Case conversion library
│       └── textcraft-megamenu.js        # Mega menu controller
├── includes/
│   ├── class-textcraft-loader.php       # Singleton loader (assets, widgets, REST API)
│   ├── seo-content-data.php             # Structured SEO content for ~30 widgets
│   └── widgets/
│       ├── class-textcraft-base-widget.php  # Abstract base widget
│       └── widget-*.php                    # Individual widget classes (70+)
```

### Key Patterns
- **Singleton**: `TextCraft_Loader` — single bootstrapping instance
- **Abstract Base**: `TextCraft_Base_Widget extends \Elementor\Widget_Base` — common controls, render helpers, SEO rendering
- **Inline JS**: Each widget renders its JavaScript as an inline `<script>` closure after the tool HTML
- **CSS Custom Properties**: `--tc-accent` injected per-widget via Elementor `selectors`
- **SEO Content**: Centralized PHP array in `seo-content-data.php`, auto-rendered by the base widget
- **Naming**: Widget files follow `widget-{slug}.php`, classes follow `Widget_{Slug}`

---

## Frequently Asked Questions

**Does this require Elementor Pro?**  
No. The free version of Elementor is sufficient.

**Is data sent to external servers?**  
No. All tools process data locally in the browser. The PDF to Word converter uses a server-side LibreOffice conversion, but this is the only exception.

**Can I use shortcodes?**  
This plugin is designed exclusively as an Elementor widget suite and does not provide shortcodes.

**Can I customize the SEO content?**  
Yes. The SEO content data is in `includes/seo-content-data.php` and can be edited or extended via the `textcraft_pdf_to_word_converter` filter pattern.
