# TextCraft Tools — Assets Reference

## CSS Files

### `assets/css/textcraft-tools.css` (~3400+ lines)

**Enqueued as:** `textcraft-tools-style`
**Dependencies:** `elementor-frontend`
**Version:** `TEXTCRAFT_VERSION`
**Location:** Frontend only (guarded: `if (is_admin()) return`)

#### Sections
1. Google Fonts import (Playfair Display, Inter, Lexend)
2. CSS custom properties (`:root` tokens)
3. Body background (radial gold glow)
4. Widget wrapper (`.tc-widget-wrap`)
5. Hero section (`.tc-hero`)
6. Tool card (`.tc-tool-card`)
7. Textareas & inputs
8. Case buttons
9. Active indicator
10. Action buttons (primary, ghost, danger, secondary)
11. Stat bar
12. Options row
13. Toast notification
14. Frequency table
15. Responsive breakpoints
16. Focus-visible & reduced motion
17. Home page styles (`.tc-home-*`)
18. Features section (`.tc-features-*`)
19. SEO cards (`.tc-seo-*`)
20. Tool link cards (`.tc-tool-link-card`)
21. All Tools Page (`.tc-atp-*`)
22. APA citation generator
23. Cleaner/remover layout
24. Consolidated widget styles

### `assets/css/textcraft-megamenu.css` (~638 lines)

**Enqueued as:** `textcraft-megamenu`
**Dependencies:** none
**Version:** `TEXTCRAFT_VERSION`
**Location:** Frontend only

#### Sections
1. Mega trigger button
2. Mega panel (dropdown)
3. Mega columns & categories
4. Mega tool links
5. Mega chevron animation
6. Mobile responsive (hides mega on mobile)
7. Dark theme matching main design system

---

## JavaScript Files

### `assets/js/textcraft-case-converter.js`

**Enqueued as:** `textcraft-case-converter`
**Strategy:** `defer`, `in_footer: true`
**Dependencies:** none

**Provides:** `window.TextCraftCaseConverter`

Methods:
```javascript
TextCraftCaseConverter.toUpperCase(text)
TextCraftCaseConverter.toLowerCase(text)
TextCraftCaseConverter.toSentenceCase(text)
TextCraftCaseConverter.toTitleCase(text)
TextCraftCaseConverter.toCapitalizedCase(text)
TextCraftCaseConverter.toAlternatingCase(text)
TextCraftCaseConverter.toInverseCase(text)
TextCraftCaseConverter.getStats(text) // { chars, words, sentences, lines }
```

### `assets/js/textcraft-megamenu.js`

**Enqueued as:** `textcraft-megamenu`
**Strategy:** `defer`, `in_footer: true`
**Dependencies:** none

Handles:
- Toggle mega panel open/close
- Keyboard navigation (Escape to close)
- Click outside to close
- ARIA attributes management

### `assets/js/textcraft-faq-accordion.js`

**Enqueued as:** `textcraft-faq-accordion`
**Strategy:** `defer`, `in_footer: true`
**Dependencies:** none

Handles:
- FAQ item click to toggle
- Keyboard support (Enter/Space)
- ARIA `aria-expanded` management
- Smooth expand/collapse animation

---

## Image Assets

### `assets/images/tools/`

SVG icons for tool pages (auto-loaded by base widget):

```
assets/images/tools/
├── svg-compressor.svg
├── pdf-to-word.svg
└── pdf-compressor.svg
```

**Auto-loading:** The base widget's `render_seo_content()` method looks for `{widget-slug}.svg` in this directory for the media section fallback image.

---

## Enqueue Summary

### Frontend (`wp_enqueue_scripts`)

| Handle | Type | File | Dependencies |
|--------|------|------|-------------|
| `textcraft-tools-style` | CSS | `assets/css/textcraft-tools.css` | `elementor-frontend` |
| `textcraft-megamenu` | CSS | `assets/css/textcraft-megamenu.css` | — |
| `textcraft-case-converter` | JS | `assets/js/textcraft-case-converter.js` | — |
| `textcraft-megamenu` | JS | `assets/js/textcraft-megamenu.js` | — |
| `textcraft-faq-accordion` | JS | `assets/js/textcraft-faq-accordion.js` | — |

### Editor Preview (`elementor/preview/enqueue_styles`)

Same CSS files loaded in Elementor editor preview iframe.

---

## Inline Scripts

Individual widgets render their JavaScript inline via `render_inline_script()`:

```php
$this->render_inline_script( $this->get_script() );
```

This wraps JS in an IIFE:
```html
<script>(function(){
'use strict';
// Widget JavaScript here
})();</script>
```

---

## Version Management

All assets use `TEXTCRAFT_VERSION` as the version parameter for cache busting:
```php
wp_enqueue_style(
    'textcraft-tools-style',
    TEXTCRAFT_PLUGIN_URL . 'assets/css/textcraft-tools.css',
    [ 'elementor-frontend' ],
    TEXTCRAFT_VERSION  // '1.0.0.1'
);
```

---

## Adding New Assets

### Shared JS Library
1. Create file in `assets/js/`
2. Add enqueue in `TextCraft_Loader::enqueue_frontend_assets()`
3. Use `defer` strategy and `in_footer: true`

### Widget-Specific JS
1. Write JS in widget's `get_script()` method
2. Output via `$this->render_inline_script( $this->get_script() )`
3. No separate file needed

### CSS
1. Add styles to `assets/css/textcraft-tools.css`
2. Prefix all classes with `tc-`
3. Use CSS variables from `:root`
