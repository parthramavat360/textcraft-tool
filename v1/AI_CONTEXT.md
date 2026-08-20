# AI Context — TextCraft Tools v1.4.0

This file provides architectural, structural, and convention information for AI-assisted development of the TextCraft Tools WordPress plugin.

---

## Overview

TextCraft Tools is a WordPress plugin that adds 70+ client-side browser tools as Elementor widgets. Every tool (text conversion, image manipulation, PDF editing) runs in the user's browser — no data is uploaded to servers.

**Entry point:** `textcraft-tools.php`  
**Key file:** `includes/class-textcraft-loader.php`  
**Base class:** `includes/widgets/class-textcraft-base-widget.php`

---

## Project Structure

```
textcraft-tools/
├── textcraft-tools.php              # Plugin bootstrap / constants / version gates
├── readme.txt                        # WordPress.org plugin readme
├── README.md                         # Full project documentation
├── CHANGELOG.md                      # Version history
├── AI_CONTEXT.md                     # This file
├── assets/
│   ├── css/
│   │   ├── textcraft-tools.css       # All widget styles (~2613 lines)
│   │   └── textcraft-megamenu.css    # Mega menu nav styles (~638 lines)
│   └── js/
│       ├── textcraft-case-converter.js  # Case transformation library
│       └── textcraft-megamenu.js        # Mega menu controller
├── includes/
│   ├── class-textcraft-loader.php     # Singleton loader
│   ├── seo-content-data.php           # Structured SEO content for ~30 widgets
│   └── widgets/
│       ├── class-textcraft-base-widget.php  # Abstract base widget
│       ├── _all_user_facing_text.md        # Human-readable text reference doc
│       ├── widget-{slug}.php               # Individual widgets (70+ files)
│       └── ...
```

---

## Architecture

### Bootstrap Flow
1. `textcraft-tools.php` defines constants, checks PHP version, hooks into `elementor/init`
2. `TextCraft_Loader::instance()` (singleton) is called when Elementor is ready
3. The loader registers:
   - Custom Elementor category: `textcraft-tools`
   - All widget classes (loaded from `includes/widgets/widget-{slug}.php`)
   - Front-end CSS (`textcraft-tools.css`, `textcraft-megamenu.css`)
   - Front-end JS (`textcraft-case-converter.js`, `textcraft-megamenu.js`)
   - REST API route: `POST /textcraft-tools/v1/pdf-to-word`

### Widget Class Hierarchy
```
\Elementor\Widget_Base
  └── TextCraft_Base_Widget        (abstract, in class-textcraft-base-widget.php)
       ├── Widget_Case_Converter
       ├── Widget_Pdf_Merger
       ├── Widget_Jpg_Compressor
       └── ... (70+ concrete widgets)
```

### Key Conventions

#### 1. Naming
- **Widget files:** `widget-{slug}.php` (lowercase, hyphenated)
- **Widget classes:** `Widget_{Slug}` in namespace `TextCraft_Tools\Widgets`
- **Elementor name:** `textcraft_{slug}` (snake_case, returned by `get_name()`)
- **Filter hooks:** `textcraft_*` prefix
- **REST API namespace:** `textcraft-tools/v1`
- **CSS classes:** `tc-*` prefix (`tc-hero`, `tc-tool-card`, `tc-btn`, `tc-textarea`)
- **JS IDs:** camelCase or hyphenated within widget context

#### 2. Widget Registration
Widgets are registered in `TextCraft_Loader::register_widgets()` via a flat array mapping file slugs to FQCNs. To add a new widget:
```php
// In the $widgets array:
'widget-my-tool' => Widgets\Widget_My_Tool::class,
```

The loader auto-requires the file and instantiates the class. No other registration is needed.

#### 3. Base Widget Contract
Every widget must implement `TextCraft_Base_Widget` and provide:
- `get_name()` — unique snake_case ID (e.g., `textcraft_my_tool`)
- `get_title()` — human-readable name
- `get_icon()` — Elementor icon class (e.g., `eicon-text`)
- `render_tool_content(array $settings): void` — outputs the tool's interactive HTML

Optionally override:
- `register_tool_controls()` — add custom Elementor panel controls
- `get_keywords()` — extend Elementor search keywords

#### 4. CSS Custom Properties
Each widget gets its own `--tc-accent` CSS variable via Elementor selectors. The design system uses properties like:
- `--tc-accent` — primary accent color (configurable in panel)
- `--tc-bg` — dark card background
- `--tc-text` — text color
- `--tc-border` — border color

These are defined in `textcraft-tools.css` as defaults and overridden per-widget by Elementor.

#### 5. Inline JavaScript
Each widget's tool logic is rendered as an inline `<script>` closure via `render_inline_script()`:
```php
$this->render_inline_script( $js_code );
```
This wraps JS in an IIFE to avoid global scope pollution. Shared libraries (case converter, etc.) are enqueued separately.

#### 6. SEO Content
Structured content lives in `includes/seo-content-data.php` as a PHP array keyed by widget name. The base widget auto-renders these sections after the tool card:
1. Introduction paragraphs
2. How-to steps (numbered list)
3. Features grid (icon + title + description)
4. Benefits list
5. Use cases list
6. Why choose list
7. FAQ accordion (first item open by default)

#### 7. Render Helpers (provided by base widget)
- `render_textarea($id, $label, $placeholder, $rows, $readonly)` — labelled textarea with char counter
- `render_button_row($buttons)` — action buttons with variants (`primary`, `secondary`)
- `render_stat_bar($stats)` — live statistics display
- `render_options_row($options)` — checkbox/toggle options
- `render_inline_script($js)` — inline JS wrapper

#### 8. File Upload / Download Pattern
All image and PDF tools follow this pattern:
- Hidden `<input type="file">` with `accept` attribute
- Drop zone with click-to-browse fallback
- Client-side FileReader / canvas / PDF.js processing
- `URL.createObjectURL()` for previews
- `download` attribute on anchor elements for export
- JSZip for batch ZIP downloads

#### 9. REST API
Single endpoint at `POST /textcraft-tools/v1/pdf-to-word`:
- Nonce verification via `X-WP-Nonce` header
- Accepts PDF file upload (max 25 MB)
- Uses LibreOffice (soffice) for server-side conversion
- Filterable via `textcraft_pdf_to_word_converter` for custom API integration
- Returns base64-encoded DOCX in JSON response

---

## Critical Rules for AI Development

1. **Never upload user data** — tools must process everything client-side. The PDF to Word converter is the only server-side exception.
2. **Extend the base widget** — never create a standalone `\Elementor\Widget_Base` subclass. Always extend `TextCraft_Base_Widget`.
3. **Use render helpers** — prefer `render_textarea()`, `render_button_row()`, etc. over raw HTML.
4. **Wrap inline JS** — always use `render_inline_script()` for widget-specific JavaScript.
5. **Prefix CSS classes** — use `tc-*` prefix for all custom widget styles.
6. **Register in loader** — new widgets must be added to the `$widgets` array in `class-textcraft-loader.php`.
7. **Snake_case widget names** — `get_name()` must return a snake_case string.
8. **No shortcode API** — this plugin is Elementor-only.
9. **Dark theme default** — the design system is dark (`--tc-bg: #050505`, `--tc-bg-soft: #0b0b0b`, `--tc-bg-card: #11100d`). New widgets should match.
10. **Use existing libraries** — check `assets/js/` and existing widgets before adding new JS libraries.

---

## Existing CSS Structure (`textcraft-tools.css`)

The main stylesheet is ~3391 lines with these sections:
- CSS custom properties (design tokens: `--tc-bg`, `--tc-bg-soft`, `--tc-bg-card`, `--tc-surface`, `--tc-border`, `--tc-gold`, `--tc-danger`, `--tc-text-*`, `--tc-shadow-*`, etc.)
- Hero section (`.tc-hero`, `.tc-hero__title`, `.tc-hero__badge`, etc.)
- Premium wrap (`.tc-premium-wrap`, `.tc-premium-wrap::before` gold glow)
- Tool card (`.tc-tool-card`, `.tc-tool-section`)
- Textareas and inputs (`.tc-textarea`, `.tc-label`, `.tc-char-count`, `.tc-search-input`)
- Buttons (`.tc-btn`, `.tc-btn--primary`, `.tc-btn--ghost`, `.tc-btn--danger`, `.tc-btn--secondary`)
- Stat bars (`.tc-stat-bar`, `.tc-stat`, `.tc-stat__value`)
- Options row (`.tc-options-row`, `.tc-option`)
- Drop zones (`.tc-drop-zone`)
- Result cards and image previews
- Progress bars, converter sections
- SEO content sections (`.tc-seo-wrap`, `.tc-seo-section`, `.tc-feature-grid`)
- FAQ accordion (`.tc-faq`, `.tc-faq-item`, `.tc-faq-question`)
- Toast notifications, step indicators
- All utility classes (`.tc-text-error`, `.tc-glass-card`, `.tc-gold-divider`, spacing, layout, etc.)
- Responsive breakpoints (9 breakpoints with `clamp()` fluid typography)
- Mega-menu styles (in separate file, ~642 lines)

---

---

## Demo Importer (v1.4.0)

The Demo Importer creates a complete TextCraft Tools website on a fresh WordPress installation with one click.

### File Structure
```
includes/admin/
├── class-demo-importer.php       # Singleton: tool defs, page creation, SEO meta, dry run, delete
└── demo-importer-admin-page.php   # Admin dashboard UI under TextCraft Tools > Demo Importer
```

### Architecture
- **Admin menu** is registered in `textcraft-tools.php` via `admin_menu` hook (outside `elementor/init` so it works without Elementor)
- **`Demo_Importer`** singleton class in namespace `TextCraft_Tools\Admin`
- All actions require `current_user_can('manage_options')` + `check_admin_referer()`

### Page Creation Flow
1. Home page (`slug: home`)
2. Free Online Text Tools (`slug: free-online-text-tools`) — parent listing page
3. 70+ individual tool pages as children of Free Online Text Tools

### Tool Definitions
`get_tool_definitions()` returns an array of all tools with:
- `slug`, `title`, `category`, `focus_keyword`, `widget_name`, `intro`

Categories and their group mapping for internal links:
- PDF Tools → pdf
- Image Compression Tools → image
- Image & Media Conversion Tools → image
- Case Conversion Tools → text
- Text Cleaning Tools → text
- Text Generators & Writing Tools → text
- Random Generators → generator
- Text Translators & Counters → text

### Page Content
- WordPress block-compatible HTML (heading, ordered/unordered lists, paragraphs)
- SEO intro, features, benefits, use cases, FAQ accordion
- Related tools links based on category group
- 600–1000 words per tool page

### Elementor Meta (added to every page)
- `_elementor_edit_mode = builder`
- `_elementor_template_type = wp-page`
- `_elementor_version = {ELEMENTOR_VERSION}`
- `_wp_page_template = elementor_header_footer`

### SEO Meta (Rank Math when active)
- `rank_math_title`, `rank_math_description`, `rank_math_focus_keyword`
- `rank_math_facebook_title/description`, `rank_math_twitter_title/description`
- `rank_math_canonical_url`, `rank_math_robots = index`
- Fallback: `_textcraft_seo_title`, `_textcraft_seo_description`, `_textcraft_focus_keyword`

### Import Tracking
Every created page stores:
- `_textcraft_demo_imported = yes`
- `_textcraft_demo_import_version = {plugin version}`
- `_textcraft_demo_import_slug = {page slug}`
- `_textcraft_demo_import_created_at = {datetime}`

### Safety Rules
- Nonce-protected form actions only
- Duplicate prevention: checks by slug + import meta before creating
- Never creates pages automatically on page load
- Never truncates tables or resets AUTO_INCREMENT
- Delete only removes pages with `_textcraft_demo_imported = yes`
- Fresh DB detection via page count, highest ID, imported count

### Actions (POST + nonce)
| Action | Description |
|--------|-------------|
| `dry_run` | Reports what would happen without writing |
| `create` | Creates/updates all pages |
| `delete` | Deletes only imported pages |

Checkboxes: `textcraft_update_existing` (updates imported), `textcraft_force_overwrite` (overwrites manual)

## Key Files Reference

| File | Purpose |
|------|---------|
| `textcraft-tools.php:27-33` | Plugin constants (version, paths, requirements) |
| `textcraft-tools.php:58-61` | Elementor init hook, loader bootstrap |
| `class-textcraft-loader.php:91-182` | Full widget registration map |
| `class-textcraft-loader.php:199-236` | Front-end asset enqueueing |
| `class-textcraft-loader.php:241-251` | REST API route registration |
| `class-textcraft-base-widget.php:48-141` | Common Elementor panel controls |
| `class-textcraft-base-widget.php:154-193` | Standard render flow (hero → tool card → SEO) |
| `class-textcraft-base-widget.php:211-329` | SEO content rendering with FAQ accordion |
| `class-textcraft-base-widget.php:389-485` | Shared HTML render helpers |
| `seo-content-data.php` | All SEO content data (intro, how-to, features, FAQ, etc.) |
