# TextCraft Tools

WordPress plugin suite for text, image, PDF, compression, generator, and developer utilities.

## Versions

### v1 (Original Plugin)
- **Location:** `v1/`
- **Architecture:** Individual Elementor widgets per tool (72 tools)
- **Base class:** `TextCraft_Base_Widget` with inline JS rendering
- **SEO:** Inline JSON-LD, FAQ, and related tools sections

### v2 (Elementor Widgets Rewrite)
- **Location:** `v2/`
- **Architecture:** 78 individual Elementor widgets extending `TextCraft_Tool_Base`
- **Base class:** Shared workspace layout, SEO infrastructure, helper methods
- **JS:** External JS files (one per tool + shared utilities)
- **CSS:** Centralized with `.tc-` prefix (BEM-style)
- **SEO:** Data-driven from `seo-content-data.php`

## Installation

1. Download the repository
2. Copy the desired version (`v1/` or `v2/`) to `wp-content/plugins/`
3. Activate the plugin in WordPress admin
4. Tool pages are auto-created on activation

## Tools Included

| Category | Tools |
|----------|-------|
| Text Tools | Case Converter, Find & Replace, Reverse Text, Word Sorter, and more |
| Image Tools | JPG/PNG/WebP/HEIC converters, compressors, OCR |
| PDF Tools | Compressor, Merger, Splitter, Rotate, Delete Pages |
| Generators | Password, UUID, Random Number, NATO Phonetic |
| Developer | JSON Formatter, Online Notepad, Video Converter |
