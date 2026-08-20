# TextCraft Tools — Mega Menu System

## File: `includes/class-megamenu-helper.php`

## Overview

The Mega Menu provides a dropdown panel listing all 74 tools organized by 8 categories. It integrates with WordPress nav menus and renders a premium dropdown panel.

---

## Architecture

```
Mega_Menu_Helper
  ├── get_grouped_tools()        Seed data (74 tools, 8 categories)
  ├── get_menu_id()              Get nav menu ID from menu-1 location
  ├── get_menu_tree()            Build hierarchical tree from nav items
  ├── find_all_tools_item()      Find "All Tools" root item
  ├── render_panel()             Render mega panel from WP menu tree
  ├── render_panel_fallback()    Render from seed data (no menu)
  ├── render_nav_item()          Render trigger button + panel
  ├── mark_all_tools_item()      wp_nav_menu_objects filter
  ├── add_items_filter()         wp_nav_menu_items filter
  ├── shortcode()                [textcraft_tools_megamenu]
  └── sync_menu()                Create/rebuild nav menu from seed data
```

---

## Tool Categories (8)

| Category | Tools |
|----------|-------|
| PDF Tools | 9 tools |
| Image Compression Tools | 5 tools |
| Image & Media Conversion Tools | 21 tools |
| Case Conversion Tools | 8 tools |
| Text Cleaning Tools | 9 tools |
| Text Generators & Writing Tools | 7 tools |
| Random Generators | 8 tools |
| Text Translators & Counters | 8 tools |

---

## Seed Data Structure

```php
$tools = [
    ['Category', 'Tool Name', '/tools/slug/', 'icon'],
    // ...
];
```

Example:
```php
['PDF Tools', 'PDF Compressor', '/tools/pdf-compressor/', 'PDF']
['Case Conversion Tools', 'Case Converter', '/tools/case-converter/', '🔤']
```

---

## WordPress Integration

### Filters Registered (in `textcraft-tools.php`)

```php
// Mark "All Tools" item for mega menu rendering
add_filter( 'wp_nav_menu_objects', [Mega_Menu_Helper::class, 'mark_all_tools_item'], 10, 2 );

// Append mega trigger to header nav
add_filter( 'wp_nav_menu_items', [Mega_Menu_Helper::class, 'add_items_filter'], 10, 2 );

// Shortcode for testing
add_shortcode( 'textcraft_tools_megamenu', [Mega_Menu_Helper::class, 'shortcode'] );
```

### `mark_all_tools_item()`
- Hooks: `wp_nav_menu_objects` (priority 10)
- Adds `tc-has-mega` class to "All Tools" menu item
- Only affects `menu-1` theme location
- CSS hides the native sub-menu on desktop; shows it on mobile

### `add_items_filter()`
- Hooks: `wp_nav_menu_items` (priority 10)
- Appends mega trigger `<li>` to `menu-1` location
- Contains: trigger button + panel HTML

---

## Panel Rendering

### `render_panel()`
1. Gets menu ID from `menu-1` location
2. Builds hierarchical tree from nav menu items
3. Finds "All Tools" root item
4. Iterates children (categories) → renders columns
5. Each column shows up to 5 child tools (`CHILD_LIMIT = 5`)
6. If more than 5 tools, shows "View All →" link

### `render_panel_fallback()`
- Used when no WP menu is assigned
- Renders directly from seed data
- Same visual output as `render_panel()`

### HTML Structure
```html
<div id="mega-panel" class="tc-mega-panel mega-panel">
    <div class="tc-mega-inner mega-panel__inner">
        <div class="tc-mega-col mega-col">
            <div class="tc-mega-category mega-col__heading">Category Name</div>
            <ul class="tc-mega-list mega-col__list">
                <li>
                    <a class="tc-mega-link mega-tool-link" href="/tools/slug/">
                        <span class="tc-mega-icon mega-tool-link__icon">icon</span>
                        <span class="tc-mega-label mega-tool-link__label">Tool Name</span>
                    </a>
                </li>
                <!-- ... more tools -->
                <li class="tc-mega-view-all">
                    <a class="tc-mega-view-all-link" href="/">View All →</a>
                </li>
            </ul>
        </div>
        <!-- ... more categories -->
    </div>
</div>
```

---

## Menu Sync

### `sync_menu()`
Creates or rebuilds the "TextCraft Tools" nav menu from seed data.

**WARNING:** Deletes any existing menu with this name.

Flow:
1. Delete existing menu named "TextCraft Tools"
2. Create new menu
3. Create "All Tools" root item (links to `/tools/`)
4. For each category:
   - Create category item (parent: "All Tools")
   - For each tool:
     - Try to find existing page by slug
     - If found: link to page via `post_type` menu item
     - If not found: link via `custom` menu item
5. Assign menu to `menu-1` location
6. Set `textcraft_menu_synced` option

### Activation Hook
```php
register_activation_hook( TEXTCRAFT_PLUGIN_FILE, function() {
    if ( ! get_option( 'textcraft_menu_synced' ) ) {
        add_action( 'elementor/init', function() {
            Mega_Menu_Helper::sync_menu();
        }, 20 );
    }
} );
```

### Admin Sync Button
The TextCraft Tools admin page includes a "Sync Mega Menu" button:
- URL: `admin.php?page=textcraft-tools&tc_sync_menu=1`
- Nonce-protected
- Shows success/error notice

---

## Shortcode

```php
[textcraft_tools_megamenu]
```

Renders standalone mega menu (for testing or non-header placement):
```html
<nav class="tc-mega-menu" aria-label="All Tools">
    <div id="mega-trigger" class="tc-mega-trigger">
        <button id="mega-btn" class="tc-mega-btn">
            <span class="tc-mega-btn-label">All Tools</span>
            <svg class="tc-mega-chevron">...</svg>
        </button>
        <!-- panel HTML -->
    </div>
</nav>
```

---

## Constants

```php
CHILD_LIMIT = 5  // Max tools shown per category column
```

---

## JS Controller

**File:** `assets/js/textcraft-megamenu.js`

Handles:
- Toggle panel open/close on button click
- Keyboard navigation (Escape to close)
- Click outside to close
- ARIA attributes (`aria-expanded`, `aria-controls`)

---

## CSS

**File:** `assets/css/textcraft-megamenu.css` (~638 lines)

Key classes:
```css
.tc-mega-trigger          /* Menu item wrapper */
.tc-mega-btn              /* Trigger button */
.tc-mega-btn-label        /* "All Tools" text */
.tc-mega-chevron          /* Dropdown arrow */
.tc-mega-panel            /* Dropdown panel */
.tc-mega-inner            /* Panel content wrapper */
.tc-mega-col              /* Category column */
.tc-mega-category         /* Category heading */
.tc-mega-list             /* Tool list */
.tc-mega-link             /* Tool link */
.tc-mega-icon             /* Tool icon */
.tc-mega-label            /* Tool name */
.tc-mega-view-all         /* "View All" link */
```

---

## Mobile Behavior

- On mobile, the native WordPress sub-menu is shown (CSS hides mega panel)
- The `tc-has-mega` class triggers CSS to hide/show appropriate elements
- Mega panel is desktop-only via CSS media queries

---

## Adding New Tools to Mega Menu

1. Add to `get_grouped_tools()` seed data:
```php
['Category', 'Tool Name', '/tools/slug/', 'icon'],
```

2. Run "Sync Mega Menu" from admin page, OR
3. The menu will auto-update on next sync
