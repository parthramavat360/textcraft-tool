# TextCraft Tools — Admin Features

## Admin Menu

Registered in `textcraft-tools.php` via `admin_menu` hook (runs independently of Elementor):

```php
add_action( 'admin_menu', function() {
    add_menu_page(
        'TextCraft Tools',           // Page title
        'TextCraft Tools',           // Menu title
        'manage_options',            // Capability
        'textcraft-tools',           // Menu slug
        'render_callback',           // Page callback
        'dashicons-text',            // Icon
        '30'                         // Position
    );
} );
```

### Admin Page Features

1. **Welcome message** — Description of plugin capabilities
2. **Header menu status** — Shows if menu-1 is assigned
3. **Mega Menu Sync button** — Creates/rebuilds nav menu
4. **Sync result notices** — Success/error messages

---

## Footer Builder

**File:** `includes/class-footer-builder.php`

### Purpose
Renders a premium 3-tier footer that replaces the Hello Elementor default footer.

### Hooks
```php
add_filter( 'hello_elementor_display_header_footer', [__CLASS__, 'maybe_hide_theme_footer'], 999 );
add_action( 'wp_footer', [__CLASS__, 'render_premium_footer'], 1 );
```

### Footer Structure

```
<footer id="tc-footer" class="tc-footer">
    ├── tc-footer-cta          Top CTA bar ("Ready to Simplify Your Work?")
    ├── tc-footer-grid         5-column grid
    │   ├── tc-footer-brand    Logo + description + trust badges
    │   ├── tc-footer-tools    Popular Tools links (12 items)
    │   ├── Quick Links        Home, About, All Tools, Categories, Blog, Contact, Accessibility
    │   ├── Legal              Privacy, Terms, Disclaimer, DMCA, Cookie, Copyright, Editorial, Advertising
    │   └── Support            Report Bug, Suggest Tool, Request Tool, Feedback, XML Sitemap, HTML Sitemap
    ├── tc-footer-divider      Gold divider line
    └── tc-footer-bottom       Copyright + legal links + social icons
```

### Social Links
- GitHub
- LinkedIn
- Facebook
- X (Twitter)

### Design
- Premium black/gold theme matching widget design system
- CTA section with gold glow background
- 5-column responsive grid
- Bottom bar with copyright, legal links, social SVG icons

---

## Body Classes

Added via `body_class` filter in `textcraft-tools.php`:

```php
// Accessibility Statement page
if ( is_page( 'accessibility-statement' ) ) {
    $classes[] = 'tc-accessibility-page';
}

// About Us page
if ( is_page( 'about-us' ) ) {
    $classes[] = 'tc-about-page';
}

// DMCA Policy page
if ( is_page( 'dmca-policy' ) ) {
    $classes[] = 'tc-dmca-page';
}
```

---

## Elementor Missing Warning

```php
add_action( 'admin_notices', function() {
    if ( did_action( 'elementor/init' ) ) return;
    if ( ! current_user_can( 'activate_plugins' ) ) return;
    echo '<div class="notice notice-warning is-dismissible"><p>';
    echo '<strong>TextCraft Tools</strong> requires Elementor to be installed and active.';
    echo '</p></div>';
} );
```

---

## Demo Importer (v1.4.0)

**Note:** Referenced in CHANGELOG and AI_CONTEXT.md but files not yet present in codebase.

### Planned Files
```
includes/admin/
├── class-demo-importer.php       # Singleton: tool defs, page creation, SEO meta
└── demo-importer-admin-page.php   # Admin dashboard UI
```

### Planned Features
- **Dry Run** — Preview what would be created
- **Create All Tool Pages** — Home + listing page + 70+ tool pages
- **Update Existing** — Update imported pages
- **Delete Imported** — Remove only tracked pages
- **Fresh DB Detection** — Analyze database state
- **SEO Meta** — Rank Math integration
- **Import Tracking** — `_textcraft_demo_imported` postmeta

### Admin Menu Registration
```php
// Planned: TextCraft Tools > Demo Importer submenu
```

---

## Activation Hook

```php
register_activation_hook( TEXTCRAFT_PLUGIN_FILE, function() {
    if ( ! get_option( 'textcraft_menu_synced' ) ) {
        add_action( 'elementor/init', function() {
            Mega_Menu_Helper::sync_menu();
        }, 20 );
    }
} );
```

- Runs on plugin activation
- Syncs mega menu if not already synced
- Deferred to `elementor/init` so nav menu functions work

---

## Security

- All admin actions require `manage_options` capability
- Mega Menu sync uses `wp_nonce_url()` and `check_admin_referer()`
- Direct access blocked via `ABSPATH` checks
- `strict_types = 1` declared in all PHP files
