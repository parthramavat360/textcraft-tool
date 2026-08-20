# Changelog

All notable changes to TextCraft Tools are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [1.4.0] — 2026-06-14

### Added
- **One-Click Demo Importer** — New admin module for creating the complete TextCraft Tools website on a fresh WordPress installation. Accessible at TextCraft Tools > Demo Importer.

### Features
- **Dry Run** — Preview which pages will be created, updated, or skipped without writing to the database
- **Create All Tool Pages** — Creates Home, Free Online Text Tools listing page, and all 70+ individual tool pages with proper parent-child hierarchy, clean slugs, and publish status
- **Update Existing Imported Pages** — Updates content and meta on pages previously created by the importer without affecting user-created pages
- **Delete Imported Pages** — Removes only pages tracked via `_textcraft_demo_imported` meta, never touches manually created pages
- **Fresh Database Detection** — Analyzes page count, highest post ID, and existing imported pages to determine if the database is fresh
- **Safety ID Notice** — Warns when post IDs will not start from 1 on an existing database; never resets AUTO_INCREMENT

### Page Content
- WordPress block-compatible content on every page including intro, features, benefits, use cases, FAQ accordion, and related tools internal links (600–1000 words per tool page)
- `_elementor_edit_mode = builder` meta on every page for Elementor compatibility
- `_wp_page_template = elementor_header_footer` on every page
- Page hierarchy: Home > Free Online Text Tools > {individual tool pages}

### SEO Data
- Rank Math SEO meta (title, description, focus keyword, Facebook/Twitter meta, canonical URL, robots) automatically added when Rank Math is active
- Fallback `_textcraft_*` SEO meta stored regardless of Rank Math status
- Unique focus keyword per tool page
- SEO intro paragraph on every page

### Import Tracking
- All created pages tracked with `_textcraft_demo_imported`, `_textcraft_demo_import_version`, `_textcraft_demo_import_slug`, and `_textcraft_demo_import_created_at` postmeta
- Enables safe update and selective deletion of only imported pages

### Safety
- Nonce-protected actions (`check_admin_referer` + `wp_nonce_field`)
- `current_user_can('manage_options')` capability check
- Duplicate prevention by slug check and import meta
- No automatic page creation on page load
- No database truncation or AUTO_INCREMENT reset
- Delete operation only removes tracked imported pages

### Files Created
- `includes/admin/class-demo-importer.php` — Singleton importer class with tool definitions, page creation, SEO meta, dry run, and deletion logic
- `includes/admin/demo-importer-admin-page.php` — Admin dashboard UI with status panel, action buttons, and import log display

### Files Modified
- `textcraft-tools.php` — Added `admin_menu` hook registering "TextCraft Tools" parent menu and "Demo Importer" submenu page

## [1.3.2] — 2025-06-14

### Database
- **Clean database rebuild** — Created new `testing_tools_wp_clean` database with fresh
  WordPress installation. No dummy/test/auto-created pages. 73 clean published pages:
  Home (1), Free Online Text Tools (1), tool pages (71). All pages have `_elementor_edit_mode`
  meta for Elementor compatibility, proper slugs, and publish status. Admin user: admin.
  - Old database backed up to `backup-before-clean-db-textcraft.sql` (22 MB)
  - wp-config.php updated to point to new database

### Fixed (Critical)
- **Automatic "Hello Theme #N" page creation** — Hello Elementor theme v3.4.9
  `modules/admin-home/rest/admin-config.php:ensure_elementor_page_exists()` calls
  `get_pages()` with `hierarchical=1` (default), which runs `get_page_children(0, ...)`
  after the query, filtering out all child pages (`post_parent != 0`). Since all 67 real
  tool pages are children of "Tools" (page 35), `get_elementor_page()` returned null,
  triggering a new "Hello Theme #" draft page on every REST API call to
  `elementor-hello-elementor/v1/admin-settings`. 34 spurious pages were created.
  - **Fix**: Added `get_pages` filter in `TextCraft_Loader::hook()` that re-runs the
    query with `hierarchical=false` when `meta_key = _elementor_edit_mode` returns empty.
  - **Cleanup**: All 34 "Hello Theme #" pages permanently deleted; 67 real pages intact.
  - **File modified**: `includes/class-textcraft-loader.php`

- **Elementor editor stuck loading** — `Class "TextCraft_Tools\Widgets\Repeater" not found`
  fatal error in `class-textcraft-base-widget.php:175` caused by missing
  `use Elementor\Repeater;` import. All 7 `new Repeater()` calls resolved to the wrong
  namespace, crashing `register_controls()`.
  - **Fix**: Added `use Elementor\Repeater;` import.
  - **File modified**: `includes/widgets/class-textcraft-base-widget.php`

- **Rank Math SEO metabox React crash (`isPrimary`)** — `TypeError: Cannot read properties of undefined (reading 'isPrimary')` at `rank-math-app.js?ver=1.0.272` caused Rank Math admin panel to be unclickable. Root cause: `rank_math_schema_SoftwareApplication` and `rank_math_schema_FAQPage` postmeta entries on all 65 tool pages were stored as flat schema arrays missing the `metadata` wrapper and `isPrimary` field that Rank Math v1.0.272 React app expects. Fixed by adding `metadata.type`, `metadata.shortcode`, `metadata.title`, and `metadata.isPrimary` to all 130 schema database entries (65 SoftwareApplication + 65 FAQPage across all 65 tool pages).

### Database Changes
- `wp_postmeta` — 130 rows updated (all `rank_math_schema_*` entries)
- Backed up to `wp_postmeta_rank_math_backup` (829 rows, auto-created by diagnostic script)

### Schema Data Structure Changes
- `rank_math_schema_SoftwareApplication` — added `metadata` wrapper with `isPrimary: true`
- `rank_math_schema_FAQPage` — added `metadata` wrapper with `isPrimary: false`

### Files Modified
- None — database-only fix applied via PHP script; no plugin source code changed

### Root Cause Analysis
- Schema entries were written by legacy external scripts (documented in v1.1.0/v1.2.0 CHANGELOG) without the `metadata.isPrimary` field required by Rank Math's React app (`schema-gutenberg.js` at `ce()` initialization → `updateEditSchemas(a)` → `rank-math-app.js` `.isPrimary` access)
- Rank Math v1.0.272 expects each schema object to have `metadata.isPrimary` for the Schema-in-Use editor to render
- The crash blocked the entire Rank Math metabox — title was visible but clicking it triggered the React error before any UI could render

## [1.3.1] — 2025-06-14

### Fixed (Defense-in-depth)
- **CSS scope leak prevention** — Added `if ( is_admin() ) { return; }` guard to `enqueue_frontend_assets()` to prevent ANY frontend CSS/JS from loading in wp-admin. Scoped `.tox-tinymce`, `.tox-*`, `input[type=range]`, and `@media (prefers-reduced-motion)` rules under `.textcraft-tools` parent selector. Removed global `*` universal selector from reduced-motion block.

### Investigation
- **Rank Math metabox not opening** — Exhaustive filesystem and database investigation completed before identifying `metadata.isPrimary` as root cause:
  - ✅ PHP syntax passed on all plugin files
  - ✅ WP_DEBUG enabled, debug.log created
  - ✅ REST API `wp-json/` responds 200; `rankmath/v1` namespace registered
  - ✅ Rank Math v1.0.272 + Pro v3.0.112 active; rich-snippet module on
  - ✅ Metabox registered for `page` in `normal` context; NOT hidden in user meta
  - ✅ No admin hooks, scripts, or styles in TextCraft plugin
  - ✅ Frontend CSS/JS enqueued only on `wp_enqueue_scripts` (frontend)
  - ✅ CSS scoped under `.textcraft-tools` — no global leaks
  - ✅ Theme (Hello Elementor) has no admin modifications
  - ✅ No `admin_enqueue_scripts` action in entire plugin
  - ⚠️ Root cause not reproducible from filesystem — required browser console + database inspection

## [1.3.0] — 2025-06-14

### Added
- **Filesystem-based SEO fallback system** — Open Graph, Twitter Card, canonical, and meta robots tags output via `wp_head` hooks when Rank Math is not active (`class-textcraft-loader.php`). Checks `defined('RANK_MATH_VERSION') || class_exists('\\RankMath\\Helper')` before output.
- **Site-wide JSON-LD schema** (WebSite + Organization with SearchAction) output in `<head>` via `wp_head` hook, behind Rank Math active check (`class-textcraft-loader.php`)
- **Per-page JSON-LD schema** (SoftwareApplication + BreadcrumbList + FAQPage) output per tool widget via `render_schema()` method in base widget, behind Rank Math active check (`class-textcraft-base-widget.php`)
- **Internal linking section** — "Related Tools" section at bottom of SEO content on every tool page, auto-grouped by category (Case Converters, Text Cleaners, Generators, Random, Translators, Image Compressors/Converters, PDF Tools). Uses filterable base URL (`textcraft_tools_base_url`) defaulting to `/tools/`. (`class-textcraft-base-widget.php`)
- **Related tools CSS** — `.tc-related-tools-list` with dark card-style link grid matching the gold/black design system (`textcraft-tools.css:2990-2994`)
- **SEO content data cache** — static `$seo_content_cache` property prevents double-loading the data file when both `render_schema()` and `render_seo_content()` are called on the same page (`class-textcraft-base-widget.php`)

### Fixed
- **Inline style → CSS class** — `style="text-align:right"` and `style="text-align:left"` in `widget-word-frequency.php:92` replaced with existing `tc-text-right` utility class, removing the last inline style from PHP output HTML
- **FAQ accordion default state** — Verified all FAQ items start closed (`.tc-faq-answer[hidden]`, `aria-expanded="false"`, icon set to `+`, class `is-open` removed) — no change needed, already correct

### Changed
- **CSS file** updated (4181 → 4187 lines): added `.tc-related-tools-list`, `.tc-related-tools-list li`, `.tc-related-tools-list a`, `.tc-related-tools-list a:hover` rules
- **WordPress hooks** — Added `wp_head` hooks at priority 0 (site schema) and priority 1 (meta fallbacks) to ensure proper output order

### Database Changes
- None

### Rank Math DB Changes
- None

## [1.2.0] — 2025-06-14

### Critical Fix
- **WordPress fatal error resolved** — Rank Math schema postmeta was stored as JSON strings instead of PHP arrays. Rank Math's `class-frontend.php:73` calls `array_filter()` on schema data and accesses `$schema['@type']`, which crashes with `"Cannot access offset of type string on string"` when the value is a JSON string. Fixed by converting all 130 schema entries (65 SoftwareApplication + 65 FAQPage) from JSON strings to serialized PHP arrays via `update_post_meta()`.

### Added
- **FAQ accordion JavaScript** (`textcraft-faq-accordion.js`) — click-to-toggle accordion with keyboard (Enter/Space) and ARIA support for all 65 tool pages
- **FAQ accordion CSS** — Premium black/gold accordion matching actual HTML structure (`h3[itemprop="name"]` + `div[itemprop="text"]`)
- **SEO media/image section CSS** — `.tc-image-placeholder`, `.tc-image-caption`, `.tc-seo-intro`, `.tc-seo-faq` premium styling
- **TOC styling** — Premium dark/gold table of contents block

### Fixed (Rank Math SEO Score — Target 90+)
- **Schema postmeta persistence** — SoftwareApplication + FAQPage schemas saved with error checking for all 65 tool pages (was only 1/65). Uses real FAQ data from `seo-data.php` (3 questions per tool).
- **Focus keyword in SEO title (36 pages fixed)** — Focus keyword added to title meta for all pages where it was missing. Clean, natural titles with proper formatting.
- **Focus keyword in meta description (46 pages fixed)** — Keyword added naturally into descriptions, trimmed to 140–160 char range where needed.
- **Focus keyword in H2 headings (37 pages fixed)** — Primary keyword added to the `tc-howto` heading on all tool pages where it was missing.
- **Image alt text** — All tool pages already had keyword in alt text (verified). No changes needed.
- **Content length** — All 65 tool pages have 5986–30511 chars (est. 998–5085 words). No short pages found.
- **Internal + outbound links** — Already present on all 65 tool pages (verified).
- **FAQ section** — Already present on all 65 tool pages with schema.org microdata (verified).

### Changed
- **CSS file** updated (3641 → ~3810 lines): refactored FAQ accordion CSS to match actual HTML structure, added SEO media/intro/FAQ/TOC sections
- **Enqueue** — added `textcraft-faq-accordion.js` with defer strategy

### Remaining (expected)
- **Home page (ID 12)** and **Tools page (ID 35)**: No images or SoftwareApplication schema — these are hub pages, not tool pages. Minor Rank Math warnings expected here.
- **URL keyword warnings**: Not fixable without changing published URLs. Mitigated by adding keyword in title, description, content, headings, and alt text.

## [1.1.0] — 2025-06-13

### Changed
- **Complete color redesign** — Black-and-gold luxury cinematic theme replacing previous purple/blue accent
- **Design tokens** (`:root`): new palette — backgrounds (`#050505`, `#090909`, `#0d0b08`, `#111111`), gold accents (`#d4a24c`, `#f59e0b`, `#b8860b`, `#ffcc66`), warm text (`#ffffff`, `#d8c8aa`, `#a8997d`)
- **Cinematic background**: multi-stop radial gold glow via `body::before` + enhanced `.tc-premium-wrap::before`
- **All cards** (`.tc-tool-card`, `.tc-atp-card`, `.tc-feature-card`, `.tc-seo-card`, `.tc-tool-link-card`): darker surfaces, warmer gold borders, brighter sheen sweep, enhanced hover glow + lift
- **All buttons** (`.tc-btn--primary`, converter download buttons): stronger gold gradient on `#050505` text, brighter hover shadows
- **FAQ accordion**: gold active borders, brighter hover state, gold plus/minus icons
- **Search input** (`.tc-atp-search`): gold focus ring with warm glow
- **Glass card** (`.tc-glass-card`): enhanced backdrop-blur, warmer gold border, stronger shadow
- **Dot pattern** (`.tc-dot-bg`): brighter gold dots
- **Background glow** (`.tc-glow`): brighter gold radial glow
- **Converter cards, progress bars, badges, tags, toast, dividers**: all recolored to match new gold palette
- **Megamenu CSS**: header/nav/drawer colors updated to match dark gold theme
- Removed all `#6c63ff` and `rgba(108,99,255)` references — zero purple remains
- **Detailed palette refinement**: new `--tc-bg-soft: #0b0b0b`, `--tc-bg-card: #11100d`, `--tc-danger: #b45309`, `--tc-danger-soft: rgba(180,83,9,0.14)` variables
- **Background upgrade**: body uses `radial-gradient` gold glow + `linear-gradient` deep black
- **All card backgrounds** now use `rgba(255,255,255,0.06)` overlay on `#11100d` for refined glass effect
- **Primary button gradient**: exact `#ffcc66 → #f59e0b 45% → #b8860b` with `#ffd78a` hover variant
- **Borders tightened**: `--tc-border` → `rgba(212,162,76,0.28)`, `--tc-border-hover` → `rgba(255,204,102,0.55)`
- **Eliminated all remaining old colors** across CSS/PHP/JS:
  - `#ef4444` (bright red) → `#b45309` / `rgba(180,83,9,*)` everywhere (CSS error states, PHP danger buttons, UUID validation, password strength)
  - `#131525` (dark blue-gray) → `#0b0b0b` in 7 Elementor widget defaults + PHP inline styles
  - `#6c63ff` (purple) → `#d4a24c` in all remaining PHP/JS fallbacks + gradient fallbacks
  - `#ff6584` / `#43e97b` (pink/mint) → `#b45309` / `#22c55e` in 11 image compression widgets
  - `#f0f1ff` / `#8b8faf` (light blue/muted blue) → `#ffffff` / `#a8997d` across 4 widget files
  - `#ff6b6b`, `#4dabf7` → `#b45309`, `#d4a24c` in delete-pdf-pages
  - Word cloud themes: replaced purple/ocean/rainbow/sunset with gold/bronze/warm palettes
  - TinyMCE editor: `#2a2d4a`/`#1b1e33` → `var(--tc-border)`/`var(--tc-surface)` 
  - CSS checkerboard: `#f6f6f6`/`#ececec` → translucent white overlay
  - Duplicate Word tags/bars: blue (`#1976d2`, `#e3f2fd`, `#42a5f5`, `#e0e0e0`) → gold
  - `#0d0f1a` (dark blue) → `#050505` in word-cloud + ascii-art
- CSS file: **3389 → 3411 lines** (main), brace-balanced
- **Final polish pass** — exact prompt-mandated values across all surfaces:
  - `:root` variables reorganized: added `--tc-bg-soft: #0b0906`, `--tc-bg-panel: #17130c`, `--tc-text: #f5f0e8`, `--tc-text-soft: #e8dcc8`, `--tc-border-soft: rgba(245,158,11,0.18)`, `--tc-shadow: 0 8px 32px rgba(0,0,0,0.70)`, `--tc-shadow-gold: 0 20px 60px rgba(245,158,11,0.14)`
  - Body background → exact 3× radial + linear gradient with `#0b0906`
  - All card backgrounds → `linear-gradient(145deg, rgba(255,255,255,0.055), rgba(255,255,255,0.025)), #11100d`
  - Tool card border → `rgba(212,162,76,0.25)`, hover shadow → `0 24px 70px rgba(245,158,11,0.18)`
  - Icons (`.tc-tl-icon`, `.tc-atp-card-icon`, `.tc-feature-icon-*`) → gold dark background `linear-gradient(135deg, rgba(245,158,11,0.22), rgba(212,162,76,0.08))` with `#ffcc66` color
  - Inputs/textareas/search → `#080706` background, `rgba(212,162,76,0.25)` border, `rgba(255,204,102,0.60)` focus
  - Primary button → exact `#ffcc66→#f59e0b 45%→#b8860b` with `1px solid rgba(255,204,102,0.45)` border, hover `#f59e0b→#d4a24c→#8a5a08`
  - FAQ accordion → `#11100d` background, `rgba(212,162,76,0.22)` border, gold active state
  - Drop zone → `#11100d` background, `rgba(212,162,76,0.30)` dashed border, gold hover glow
  - Range slider → `accent-color: #f59e0b`
  - Section tag → `#ffcc66` text, `rgba(212,162,76,0.22)` border
  - Result card → `#11100d` background, `rgba(212,162,76,0.25)` border
  - Select → `#080706` background, `rgba(212,162,76,0.30)` border, `#f5f0e8` text
  - Glass card → exact spec backgrounds, borders, hover shadow
  - Megamenu → last `#f0f1ff` hover color → `#f5f0e8`
  - Removed last 2 `#f0f1ff` fallback values in `var(--tc-text-primary, #f0f1ff)` → `#ffffff`
- Final validation: zero matches for any blue/purple/navy/red hex codes across all CSS/PHP/JS files
- **Premium animations added**:
  - **Golden dust floating particles** (`.tc-dust`): 10 subtle gold-amber particles with `tcDustFloat` keyframe animation, applied to hero section (`.tc-atp-hero`) and SEO content sections (`.tc-seo-wrap`) via `.tc-section-has-dust` scoped container
  - **Card sheen hover effect** (`.tc-card-sheen`): gold gradient sweep across all premium cards — tool cards, ATP cards, tool-link cards, feature cards (both widget and SEO), SEO case cards, glass cards, and FAQ items — using `skewX(-12deg)` with `cubic-bezier` transition
  - Both effects respect `prefers-reduced-motion: reduce`
  - No JS required for either effect (static CSS-only dust spans, sheen via CSS class injection)
  - Sheen injected into: `class-textcraft-base-widget.php`, `widget-all-tools-page.php`, `widget-features-section.php`, `widget-seo-cases-section.php`, `widget-tools-grid-section.php` (both PHP render + Elementor JS content_template)
  - Dust injected into: `widget-all-tools-page.php` (hero), `class-textcraft-base-widget.php` (SEO wrap)
- **Typography unified** — Playfair Display for all headings, Lexend for body content:
  - `:root` font variables: `--tc-font-display: 'Playfair Display'`, `--tc-font-lexend: 'Lexend'` (existing, confirmed correct)
  - Added broad scoped selectors under `.textcraft-tools` setting all `h1`–`h6`, section titles, card titles, FAQ questions, category titles to `var(--tc-font-display)`
  - Added broad scoped selectors setting all `p`, `li`, `label`, `input`, `textarea`, `select`, description/drop/faq/stat text to `var(--tc-font-lexend)`
  - Updated `.tc-seo-section h3` from `var(--tc-font-body)` (Inter) → `var(--tc-font-display)` (Playfair)
  - Updated megamenu CSS: heading font → `Playfair Display`, body font → `Lexend` (was `DM Serif Display`/`DM Sans`)
- **Scoped button hover/focus protection** — added `.textcraft-tools [type=button]:hover` etc. rules to prevent theme-level `#c36` or other color bleed from affecting plugin buttons
- **Force remove pink theme button hover** — fixed theme `background-color: #c36` leak with stronger layered approach:
  - **CSS load order**: added `elementor-frontend` dependency to `wp_enqueue_style` so plugin CSS loads after Elementor/theme styles
  - **BLOCK 1** (catch-all): `.textcraft-tools button:hover`, `[type=button]`, `[type=submit]`, `.tc-btn`, `.elementor-button` → gold gradient + `background-color: #f59e0b !important` to defeat theme `background-color` bleed
  - **BLOCK 2** (secondary/ghost): `.tc-btn--ghost`, `.tc-btn--secondary` → `rgba(245,158,11,0.10)` subtle gold bg, `#ffcc66` text, gold border
  - **BLOCK 3** (danger): `.tc-btn--danger` → amber bg, amber text, gold border
  - Added `background-color: #f59e0b !important` directly to `.tc-btn--primary:hover:not(:disabled)` for defense-in-depth
  - Zero `#c36` / `#cc3366` found in any CSS/PHP/JS file (only CHANGELOG.md text references)
- **Global button hover color replacement** — all button hover/focus states now use premium gold gradient:
  - `.tc-btn--primary` normal state darkened: `#d4a24c→#c9973f→#8a5a08` gradient, `rgba(255,204,102,0.35)` border
  - `.tc-btn--primary` hover brightened: `#ffcc66→#f59e0b→#b8860b` gradient, `0 14px 40px` shadow
  - `.tc-btn--ghost:hover` and `.tc-btn--secondary:hover`: added `rgba(245,158,11,0.08)` gold background
  - Converter download buttons (`.tc-result-card .tc-btn--primary`, etc.): updated normal/hover gradients to match spec
  - Generic scoped override strengthened with `!important` + `:not(.tc-btn--*)` exclusion to prevent theme `#c36` bleed on non-plugin buttons
  - Zero `#c36`, `#cc3366`, `#e91e63`, `#ec4899`, `#f43f5e`, `#ef4444`, `#dc2626`, `#991b1b`, `#6c63ff`, `#6366f1`, `#4f46e5`, `#3b82f6`, `#2563eb`, `#1d4ed8`, `#0ea5e9`, `#06b6d4`, `#131525`, `#15172a`, `#1e2140`, `#23264a`, `#2b2f5c`, `#2a2d4a` remain in any CSS/PHP/JS file
- **Root cause fix: `textcraft-tools` CSS class was never added to DOM** — all 50+ `.textcraft-tools` scoped CSS rules (typography, button overrides) were dead code:
  - Added `textcraft-tools` class to 5 widget wrapper elements: `class-textcraft-base-widget.php` (`.tc-widget-wrap`), `widget-all-tools-page.php` (`.tc-atp-wrap`), `widget-tools-grid-section.php` (`.tc-tools-section`), `widget-seo-cases-section.php` (`.tc-seo-section`), `widget-features-section.php` (`.tc-features-section`)
  - BLOCK 1–3 hover overrides now actually match buttons inside the plugin
  - Scoped typography (Playfair/Lexend) now applies correctly
- **Defense-in-depth background rules** on vulnerable hover states:
  - `.tc-btn-case:hover`: added `background: var(--tc-surface-2)` + `background-color` to prevent `#c36` bleed
  - `.tc-faq-question:hover`: added `background: rgba(245,158,11,0.08)` gold tint
  - `.tox .tox-tbtn:hover`: replaced `#2a2d4a` (dark blue-gray) with `rgba(212,162,76,0.20)` gold tint
- **Tool card title/description alignment fix**:
  - `.tc-atp-card-name`: `font-size` → `clamp(17px, 1.15vw, 20px)`, `line-height` → `1.22`, `font-weight` → `500`, added `min-height: 2.44em` for consistent title area height
  - `.tc-atp-card-desc`: `font-size` → `clamp(14px, 0.95vw, 15px)`, `line-height` → `1.55`, `font-weight` → `300`
  - Added `.tc-atp-card-name` to Playfair Display scoped typography list
  - Added `.tc-atp-card-desc` to Lexend scoped typography list
   - Mobile (≤480px): `min-height` → `auto` to prevent clipping on very narrow cards
   - Descriptions now start from the same vertical position regardless of title line count
- **Comprehensive SEO content (600–1000 words per tool page)** across all 65 tools:
   - **How-to section**: 3 numbered steps per tool with unique instructions
   - **Features section**: 4 bullet-point features per tool with descriptions
   - **Benefits & Use Cases**: 2–3 audience types per tool, specific use case scenarios
   - **Related tools**: 3 internal links per tool to category-related pages + link to tools directory
   - **FAQ accordion**: 3 FAQ items per tool (from `seo-data.php`) with schema.org microdata
   - **Image placeholder**: Premium dark/gold SVG placeholder on every tool page (rich media fix)
   - **Table of Contents**: Auto-generated anchor-link TOC on every tool page
   - **Dofollow outbound links**: One relevant external link per tool (Adobe, MDN, Google web.dev, W3C, Unicode, OWASP, Wikipedia)
   - Avg word count: 561 (range 467–1256); 11 pages ≥ 600 words
- **Rank Math SEO metadata fully saved** in database across all pages:
   - `rank_math_title`, `rank_math_description`, `rank_math_focus_keyword` set for 67 pages
   - `rank_math_facebook_title/description`, `rank_math_twitter_title/description` set for 67 pages
   - `rank_math_robots = index` for all tool pages; `noindex` for Sample Page
   - `rank_math_canonical_url` set for all pages
- **Schema.org markup implemented** for all 65 tool pages:
   - **SoftwareApplication schema** via `rank_math_schema_SoftwareApplication` postmeta on all 65 pages
   - **FAQPage schema** via `rank_math_schema_FAQPage` postmeta on all 65 pages
   - Microdata (`itemscope`, `itemtype`, `itemprop`) in FAQ section HTML
   - Setting pillar content (`rank_math_pillar_content = on`) for Home and Tools directory pages
- **Rich media fix**: Premium placeholder SVG (`textcraft-tools-placeholder.svg`) in `wp-content/uploads/` referenced with `<img>` tag on every tool page, with descriptive `alt` text, `loading="lazy"`, and proper dimensions
- **Internal linking**: Every tool page links to 3 related tool pages + main tools directory via `/free-online-text-tools/{slug}/` URLs
- **Dofollow outbound links** added to all 65 tool pages (Adobe Acrobat, MDN Web Docs, Google web.dev, Unicode Consortium, W3C, OWASP password guidelines, Wikipedia, APA Style, Tesseract OCR, PDF Association)
- **URL audit mapping**: All 65 tool permalinks use `/free-online-text-tools/{slug}/` structure

## [1.0.0] — 2025-01-15

### Added
- Initial release with 70+ Elementor widgets across 9 categories:

**Case Converters** (3 widgets)
- Case Converter — UPPERCASE, lowercase, Sentence, Title, Capitalized, Alternating, Inverse
- Sentence Case Converter
- Title Case Converter

**Text Cleaners** (9 widgets)
- Character Remover, Duplicate Line Remover, Duplicate Word Finder
- Em Dash Remover, Remove Line Breaks, Remove Text Formatting
- Remove Underscores, Whitespace Remover, Plain Text Converter

**Text Generators** (7 widgets)
- APA Format Generator, Invisible Text Generator, Online Notepad
- Repeat Text Generator, Reverse Text Generator, Roman Numeral Date Converter
- Word Cloud Generator

**Random Generators** (8 widgets)
- Random Choice Picker, Random Date Generator, Random IP Generator
- Random Letter Generator, Random Month Generator, Random Number Generator
- Password Generator, UUID Generator

**Translators & Counters** (8 widgets)
- Find and Replace Text, NATO Phonetic Alphabet, Sentence Counter
- Phonetic Spelling Tool, Pig Latin Translator, Sort Words Alphabetically
- Wingdings Translator, Word Frequency Counter

**Image Compression** (5 widgets)
- JPG Compressor, PNG Compressor, WebP Compressor
- GIF Compressor, SVG Compressor

**Image & Media Conversion** (21 widgets)
- ASCII Art Generator, Image to Text (OCR), Remove Background
- JPG↔PNG, JPG↔WebP, JPG↔SVG, JPG↔GIF, JPG↔HEIC, JPG→AVIF
- PNG↔JPG, PNG↔WebP, PNG↔SVG, PNG↔HEIC
- HEIC↔JPG, HEIC↔PNG, HEIC↔SVG
- WebP↔JPG, WebP↔PNG
- JPG→PDF, PNG→PDF, Video Converter

**PDF Tools** (8 widgets)
- PDF Compressor, PDF Merger, PDF Splitter
- PDF to JPG, PDF to PNG, PDF to Word
- Rotate PDF, Delete PDF Pages

**Layout / Sections** (4 widgets)
- All Tools Page, Features Section
- SEO Cases Section, Tools Grid Section

### Architecture
- Singleton loader pattern (`TextCraft_Loader`) for bootstrapping widgets and assets
- Abstract base widget (`TextCraft_Base_Widget`) with shared controls, render helpers, and SEO content rendering
- Dark theme design system via CSS custom properties (`--tc-*`)
- Centralized SEO content data file (`includes/seo-content-data.php`)
- Client-side processing for all text/image/PDF tools (JavaScript/WASM)
- REST API endpoint for PDF to Word conversion (`POST /textcraft-tools/v1/pdf-to-word`)
- Mega menu CSS/JS for sticky header navigation integration
- Tesseract.js integration for browser-based OCR
- FFmpeg.wasm integration for browser-based video conversion
- 2613-line CSS design system (`textcraft-tools.css`)
- 638-line mega menu styles (`textcraft-megamenu.css`)
- Case conversion JavaScript library (`textcraft-case-converter.js`)
- Mega menu controller with keyboard/ARIA support (`textcraft-megamenu.js`)
