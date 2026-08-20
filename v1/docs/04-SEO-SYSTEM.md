# TextCraft Tools — SEO System

## Overview

The plugin implements a multi-layered SEO system:

1. **Per-widget SEO content** — Auto-rendered by base widget
2. **JSON-LD Schema** — SoftwareApplication + BreadcrumbList + FAQPage
3. **Site-wide Schema** — WebSite + Organization + SearchAction
4. **Meta tag fallbacks** — OG, Twitter, canonical, robots (when Rank Math inactive)
5. **Legacy SEO data** — `seo-data.php` for manual page content
6. **Internal linking** — Related tools section per widget

---

## 1. Per-Widget SEO Content

### Data Source
**File:** `includes/seo-content-data.php`

Returns a PHP array keyed by widget name:
```php
return [
    'textcraft_case_converter' => [
        'intro'      => [ 'Paragraph...', 'Paragraph...' ],
        'how_to'     => [ ['title' => 'Step', 'desc' => '...'] ],
        'features'   => [ ['icon' => '⚡', 'title' => '...', 'desc' => '...'] ],
        'benefits'   => [ ['title' => '...', 'desc' => '...'] ],
        'use_cases'  => [ ['title' => '...', 'desc' => '...'] ],
        'why_choose' => [ ['title' => '...', 'desc' => '...'] ],
        'faq'        => [ ['q' => '...', 'a' => '...'] ],
    ],
    // ... more widgets
];
```

### Auto-Rendering (Base Widget)
The `render_seo_content()` method in `TextCraft_Base_Widget` renders:

1. **Introduction** — `tc-seo-section` with paragraphs
2. **How To Use** — Ordered list with `tc-steps`
3. **Features** — Grid with `tc-feature-grid` cards
4. **Benefits** — Unordered list
5. **Use Cases** — Unordered list
6. **Why Choose** — Unordered list
7. **Media Section** — Image + description (auto-loads SVG from `assets/images/tools/`)
8. **FAQ Accordion** — Click-to-expand with `tc-faq-accordion`
9. **Related Tools** — Internal links to category siblings

### Override via Elementor
Each widget has an SEO Content section in the Elementor panel:
- Toggle `seo_override` to `yes`
- Edit intro, how-to, features, benefits, use cases, why choose, FAQ, media
- Overrides take precedence over file data

### Caching
Static `$seo_content_cache` property prevents double-loading the data file when both `render_schema()` and `render_seo_content()` are called.

---

## 2. Per-Page JSON-LD Schema

### Output by: `render_schema()` in base widget

Generates three schema types in `@graph`:

### SoftwareApplication
```json
{
    "@type": "SoftwareApplication",
    "name": "Tool Title",
    "applicationCategory": "UtilitiesApplication",
    "operatingSystem": "All (Web-based)",
    "description": "...",
    "url": "https://...",
    "offers": {
        "@type": "Offer",
        "price": "0",
        "priceCurrency": "USD"
    }
}
```

### BreadcrumbList
```json
{
    "@type": "BreadcrumbList",
    "itemListElement": [
        { "@type": "ListItem", "position": 1, "name": "Site Name", "item": "..." },
        { "@type": "ListItem", "position": 2, "name": "Tool Title", "item": "..." }
    ]
}
```

### FAQPage
```json
{
    "@type": "FAQPage",
    "mainEntity": [
        {
            "@type": "Question",
            "name": "Question?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Answer."
            }
        }
    ]
}
```

### Guard
Only outputs when Rank Math is NOT active:
```php
if ( defined( 'RANK_MATH_VERSION' ) || class_exists( '\\RankMath\\Helper' ) ) {
    return;
}
```

---

## 3. Site-Wide JSON-LD Schema

### Output by: `output_site_schema()` in loader (priority 0)

```json
{
    "@graph": [
        {
            "@type": "WebSite",
            "url": "...",
            "name": "...",
            "description": "...",
            "potentialAction": {
                "@type": "SearchAction",
                "target": "/?s={search_term_string}",
                "query-input": "required name=search_term_string"
            }
        },
        {
            "@type": "Organization",
            "name": "...",
            "url": "...",
            "logo": "..."
        }
    ]
}
```

---

## 4. Meta Tag Fallbacks

### Output by: `output_seo_meta_fallbacks()` in loader (priority 1)

Only when Rank Math is NOT active and on singular pages:

```html
<!-- Open Graph -->
<meta property="og:title" content="...">
<meta property="og:url" content="...">
<meta property="og:type" content="website">
<meta property="og:description" content="...">
<meta property="og:site_name" content="...">
<meta property="og:image" content="...">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="...">
<meta name="twitter:description" content="...">
<meta name="twitter:image" content="...">

<!-- Canonical -->
<link rel="canonical" href="...">

<!-- Robots -->
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
```

---

## 5. Legacy SEO Data

### File: `seo-data.php`

Contains SEO metadata for manual page content:
```php
$seo_data = [
    'pdf-compressor' => [
        'focus_keyword' => 'reduce PDF file size',
        'seo_title'     => 'Online PDF Compressor — Reduce PDF File Size for Free',
        'meta_desc'     => '...',
        'seo_intro'     => '...',
        'faqs'          => [
            ['q' => '...', 'a' => '...'],
        ],
    ],
    // ... more tools
];
```

Used by Demo Importer (v1.4.0) and manual page creation.

---

## 6. Internal Linking

### Related Tools Section
The `render_related_tools()` method in base widget:

1. Looks up the current widget in `get_tool_categories()`
2. Finds sibling tools in the same category
3. Renders links to `/tools/{widget-slug}/`
4. URL base is filterable via `textcraft_tools_base_url`

### Category Map
```php
'case-converters' => [
    'label' => 'Case Converters',
    'tools' => [
        'widget_case_converter'  => 'Case Converter',
        'widget_sentence_case'   => 'Sentence Case Converter',
        'widget_title_case'      => 'Title Case Converter',
    ],
],
// ... 7 categories total
```

Filterable via: `apply_filters( 'textcraft_tool_categories', $cats )`

---

## Rank Math Integration

### When Active
- All schema output is suppressed (Rank Math handles it)
- Meta tag fallbacks are suppressed
- Site-wide schema is suppressed
- Per-page schema is suppressed

### When Inactive
- Plugin outputs its own OG/Twitter/canonical/robots meta
- Plugin outputs its own JSON-LD schemas
- Fallback SEO data stored in `_textcraft_*` postmeta

### Rank Math Postmeta
Set by Demo Importer:
- `rank_math_title`
- `rank_math_description`
- `rank_math_focus_keyword`
- `rank_math_facebook_title` / `rank_math_facebook_description`
- `rank_math_twitter_title` / `rank_math_twitter_description`
- `rank_math_canonical_url`
- `rank_math_robots` = `index`
- `rank_math_schema_SoftwareApplication`
- `rank_math_schema_FAQPage`

---

## SEO Content Statistics

- **30+ widgets** have structured SEO content in `seo-content-data.php`
- **600-1000 words** per tool page (from Demo Importer)
- **3 FAQ items** per tool (from `seo-data.php`)
- **3 related tools** per tool page (internal linking)
- **JSON-LD** on every tool page (when Rank Math inactive)

---

## Adding SEO Content for New Widgets

1. Add entry to `includes/seo-content-data.php`:
```php
'textcraft_new_tool' => [
    'intro'      => [ '...' ],
    'how_to'     => [ ['title' => '...', 'desc' => '...'] ],
    'features'   => [ ['icon' => '⚡', 'title' => '...', 'desc' => '...'] ],
    'benefits'   => [ ['title' => '...', 'desc' => '...'] ],
    'use_cases'  => [ ['title' => '...', 'desc' => '...'] ],
    'why_choose' => [ ['title' => '...', 'desc' => '...'] ],
    'faq'        => [ ['q' => '...', 'a' => '...'] ],
],
```

2. Add to category map in base widget `get_tool_categories()`:
```php
'your-category' => [
    'label' => __( 'Category Name', 'textcraft-tools' ),
    'tools' => [
        'widget_new_tool' => __( 'Tool Name', 'textcraft-tools' ),
    ],
],
```

3. Optionally add SVG icon to `assets/images/tools/{slug}.svg`
