# TextCraft Tools — Plugin Overview

## Identity

| Field | Value |
|-------|-------|
| Plugin Name | TextCraft Tools |
| Version | 1.0.1 |
| Text Domain | `textcraft-tools` |
| Namespace | `TextCraft_Tools` |
| PHP Required | 8.0 |
| WP Required | 6.0 |
| Elementor Required | 3.10.0 |
| License | GPL-2.0-or-later |

## Entry Point

**File:** `textcraft-tools.php`

### Constants Defined

```php
TEXTCRAFT_VERSION      '1.0.0.1'
TEXTCRAFT_PLUGIN_FILE  __FILE__
TEXTCRAFT_PLUGIN_DIR   plugin_dir_path(__FILE__)
TEXTCRAFT_PLUGIN_URL   plugin_dir_url(__FILE__)
TEXTCRAFT_MIN_PHP      '8.0'
TEXTCRAFT_MIN_WP       '6.0'
TEXTCRAFT_MIN_EL       '3.10.0'
```

## Bootstrap Flow

```
textcraft-tools.php
  ├── PHP version gate (return if < 8.0)
  ├── elementor/init hook
  │   ├── require class-textcraft-loader.php
  │   ├── TextCraft_Loader::instance() (singleton)
  │   ├── require class-megamenu-helper.php
  │   ├── wp_nav_menu_objects → mark_all_tools_item()
  │   ├── wp_nav_menu_items → add_items_filter()
  │   └── shortcode: [textcraft_tools_megamenu]
  ├── require class-footer-builder.php
  ├── Footer_Builder::init()
  ├── body_class filter (tc-accessibility-page, tc-about-page, tc-dmca-page)
  ├── register_activation_hook → Mega_Menu_Helper::sync_menu()
  ├── admin_notices → Elementor missing warning
  └── admin_menu → TextCraft Tools admin page with Mega Menu sync
```

## Key Files

| File | Purpose |
|------|---------|
| `textcraft-tools.php` | Bootstrap, constants, version gates, admin menu |
| `includes/class-textcraft-loader.php` | Singleton loader — widget registration, REST routes, assets, SEO |
| `includes/class-footer-builder.php` | Premium 3-tier footer rendering |
| `includes/class-megamenu-helper.php` | Mega menu — 74 tools, 8 categories, nav sync |
| `includes/seo-content-data.php` | Structured SEO content for ~30 widgets |
| `seo-data.php` | Legacy SEO data (focus keywords, FAQs) for all tool pages |
| `includes/widgets/class-textcraft-base-widget.php` | Abstract base widget — controls, render helpers, SEO |
| `includes/widgets/widget-{slug}.php` | Individual tool widgets (70+ files) |
| `assets/css/textcraft-tools.css` | Main design system (~3400+ lines) |
| `assets/css/textcraft-megamenu.css` | Mega menu styles |
| `assets/js/textcraft-case-converter.js` | Shared case conversion library |
| `assets/js/textcraft-megamenu.js` | Mega menu controller |
| `assets/js/textcraft-faq-accordion.js` | FAQ accordion |
| `AI_CONTEXT.md` | AI development context file |
| `CHANGELOG.md` | Version history |

## Architecture Pattern

- **Singleton Loader** — `TextCraft_Loader::instance()` bootstraps everything
- **Abstract Base Widget** — `TextCraft_Base_Widget` extends `Widget_Base`
- **Client-side Processing** — All tools run in browser (JS/WASM)
- **One Exception** — PDF→Word uses server-side LibreOffice
- **SEO Auto-render** — Base widget auto-appends intro, how-to, features, FAQ, related tools
- **JSON-LD Schema** — SoftwareApplication + BreadcrumbList + FAQPage per tool
- **Dark Theme** — CSS custom properties with `--tc-*` prefix
- **Elementor Only** — No shortcode API, no classic widget support

## Hooks Summary

### Frontend
- `wp_enqueue_scripts` → CSS/JS enqueuing (guarded: `if (is_admin()) return`)
- `elementor/preview/enqueue_styles` → Same assets in editor preview
- `wp_head` (priority 0) → Site-wide JSON-LD schema
- `wp_head` (priority 1) → SEO meta fallbacks (when Rank Math inactive)
- `wp_footer` (priority 1) → Premium footer rendering
- `body_class` → Page-specific CSS classes

### Elementor
- `elementor/init` → Loader bootstrap + mega menu
- `elementor/elements/categories_registered` → Custom category
- `elementor/widgets/register` → Widget registration

### REST API
- `rest_api_init` → `POST /textcraft-tools/v1/pdf-to-word`

### Admin
- `admin_menu` → TextCraft Tools page with Mega Menu sync
- `admin_notices` → Elementor missing warning

### Activation
- `register_activation_hook` → Mega menu sync on first activation

## Filters

| Filter | Purpose |
|--------|---------|
| `textcraft_pdf_to_word_converter` | Custom PDF conversion API |
| `textcraft_pdf_to_word_soffice_paths` | LibreOffice binary paths |
| `textcraft_pdf_to_word_max_size` | Upload size limit (default 50MB) |
| `textcraft_tools_base_url` | Tool page base URL |
| `textcraft_tool_categories` | Related tools categories map |
| `textcraft_soffice_path` | LibreOffice path override |

## Design System Summary

- **Theme:** Dark luxury black & gold
- **Fonts:** Playfair Display (headings), Lexend (body)
- **Accent:** `#d4a24c` (gold)
- **Background:** `#050505` → `#11100d` (cards)
- **CSS Variables:** `--tc-*` prefix throughout
- **Responsive:** 9 breakpoints with `clamp()` fluid typography
- **Animations:** Gold dust particles, card sheen hover, pulse dots
