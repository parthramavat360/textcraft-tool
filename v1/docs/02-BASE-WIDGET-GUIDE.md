# TextCraft Tools — Building New Widgets

## Architecture

```
\Elementor\Widget_Base
  └── TextCraft_Base_Widget  (abstract)
       ├── Widget_Case_Converter
       ├── Widget_Pdf_Merger
       └── ... (70+ concrete widgets)
```

## Step-by-Step: Adding a New Widget

### 1. Create the Widget File

Create `includes/widgets/widget-{slug}.php`:

```php
<?php
declare( strict_types = 1 );

namespace TextCraft_Tools\Widgets;

defined( 'ABSPATH' ) || exit;

class Widget_{Pascal_Slug} extends TextCraft_Base_Widget {

    public function get_name(): string {
        return 'textcraft_{snake_slug}';
    }

    public function get_title(): string {
        return esc_html__( 'Tool Title', 'textcraft-tools' );
    }

    public function get_icon(): string {
        return 'eicon-text'; // Elementor icon class
    }

    public function get_keywords(): array {
        return [ 'keyword1', 'keyword2', 'free online tool' ];
    }

    protected function render_tool_content( array $settings ): void {
        // Your tool HTML goes here
    }
}
```

### 2. Register in Loader

Add to `$widgets` array in `class-textcraft-loader.php`:

```php
'widget-{slug}' => Widgets\Widget_{Pascal_Slug}::class,
```

### 3. Add SEO Content (Optional)

Add entry to `includes/seo-content-data.php`:

```php
'textcraft_{snake_slug}' => [
    'intro'      => [ 'Paragraph 1...', 'Paragraph 2...' ],
    'how_to'     => [
        ['title' => 'Step 1', 'desc' => 'Description...'],
    ],
    'features'   => [
        ['icon' => '⚡', 'title' => 'Feature', 'desc' => 'Description...'],
    ],
    'benefits'   => [ ['title' => 'Benefit', 'desc' => 'Description...'] ],
    'use_cases'  => [ ['title' => 'Who', 'desc' => 'How they benefit...'] ],
    'why_choose' => [ ['title' => 'Reason', 'desc' => 'Why...'] ],
    'faq'        => [ ['q' => 'Question?', 'a' => 'Answer.'] ],
],
```

---

## Required Methods

### `get_name(): string`
Return a unique snake_case identifier.
```php
return 'textcraft_my_tool';
```

### `get_title(): string`
Return the human-readable widget name.
```php
return esc_html__( 'My Tool', 'textcraft-tools' );
```

### `get_icon(): string`
Return an Elementor icon class.
```php
return 'eicon-text';      // Text tools
return 'eicon-image';     // Image tools
return 'eicon-document-file'; // PDF tools
```

### `render_tool_content(array $settings): void`
Output the tool's interactive HTML. This is called inside the tool card wrapper.

---

## Optional Overrides

### `register_tool_controls(): void`
Add custom Elementor panel controls inside the Content section.

```php
protected function register_tool_controls(): void {
    $this->add_control(
        'my_option',
        [
            'label'   => esc_html__( 'My Option', 'textcraft-tools' ),
            'type'    => Controls_Manager::TEXT,
            'default' => 'default value',
        ]
    );
}
```

### `get_keywords(): array`
Extend Elementor search keywords (merged with base keywords).

---

## Render Helpers (from Base Widget)

### `render_textarea($id, $label, $placeholder, $rows, $readonly)`
Output a labelled textarea with character counter.

```php
$this->render_textarea(
    'tc-my-input',                    // DOM id
    esc_html__( 'Your Text', 'textcraft-tools' ),  // Label
    esc_html__( 'Type here...', 'textcraft-tools' ), // Placeholder
    8,                                // Rows
    false                             // Readonly
);
```

### `render_button_row($buttons)`
Output action buttons with variants.

```php
$this->render_button_row( [
    ['id' => 'tc-my-copy',     'label' => '📋 Copy',     'variant' => 'ghost'],
    ['id' => 'tc-my-download', 'label' => '💾 Download', 'variant' => 'ghost'],
    ['id' => 'tc-my-clear',    'label' => '🗑️ Clear',    'variant' => 'danger'],
] );
```

Variants: `primary`, `ghost`, `danger`, `secondary`

### `render_stat_bar($stats)`
Output live statistics display.

```php
$this->render_stat_bar( [
    ['id' => 'tc-my-chars', 'label' => esc_html__( 'Characters', 'textcraft-tools' )],
    ['id' => 'tc-my-words', 'label' => esc_html__( 'Words', 'textcraft-tools' )],
] );
```

### `render_options_row($options)`
Output checkbox/toggle options.

```php
$this->render_options_row( [
    ['id' => 'tc-my-opt1', 'label' => 'Option 1', 'checked' => true],
    ['id' => 'tc-my-opt2', 'label' => 'Option 2'],
] );
```

### `render_inline_script($js)`
Wrap JavaScript in an IIFE closure.

```php
$this->render_inline_script( $this->get_script() );
```

---

## Common Patterns

### Text Tool Pattern
```php
protected function render_tool_content( array $settings ): void {
    // Description
    echo '<p class="tc-text-14 tc-text-muted tc-mb-16">Description...</p>';

    // Input textarea
    $this->render_textarea( 'tc-my-input', 'Your Text', 'Type here...', 8 );

    // Options
    $this->render_options_row( [ /* ... */ ] );

    // Stat bar
    $this->render_stat_bar( [ /* ... */ ] );

    // Action buttons
    $this->render_button_row( [ /* ... */ ] );

    // Toast
    echo '<div class="tc-toast" id="tc-my-toast" role="alert" aria-live="assertive">'
       . '<span class="tc-toast__icon">✅</span>'
       . '<span id="tc-my-toast-msg"></span>'
       . '</div>';

    // Inline JS
    $this->render_inline_script( $this->get_script() );
}
```

### File Upload Pattern
```php
protected function render_tool_content( array $settings ): void {
    ?>
    <div class="tc-drop-zone" role="button" tabindex="0"
         aria-label="Click or drag a file">
        <p class="tc-drop-zone__title">Click to upload or drag & drop</p>
        <input type="file" class="tc-file-upload"
               accept="image/*,.pdf" aria-hidden="true">
    </div>
    <?php

    $this->render_button_row( [
        ['id' => 'tc-convert',  'label' => 'Convert',  'variant' => 'primary', 'disabled' => true],
        ['id' => 'tc-download', 'label' => 'Download', 'variant' => 'ghost'],
        ['id' => 'tc-clear',    'label' => 'Clear',    'variant' => 'danger'],
    ] );

    $this->render_stat_bar( [ /* ... */ ] );

    $this->render_inline_script( $this->get_script() );
}
```

---

## JavaScript Pattern

```javascript
(function(){
'use strict';

// DOM refs — scoped to THIS widget's card
var anyBtn = document.querySelector('.tc-my-button');
if(!anyBtn) return;
var card = anyBtn.closest('.tc-tool-card');
if(!card) return;

var inp = card.querySelector('#tc-my-input');

// Toast helper
function showToast(msg, icon){
    var toast = card.querySelector('#tc-my-toast');
    var toastMsg = card.querySelector('#tc-my-toast-msg');
    if(toastMsg) toastMsg.textContent = msg;
    if(toast){
        toast.querySelector('.tc-toast__icon').textContent = icon || '✅';
        toast.classList.add('tc-toast--show');
    }
    setTimeout(function(){
        if(toast) toast.classList.remove('tc-toast--show');
    }, 2800);
}

// Tool logic here...

})();
```

### Key JavaScript Rules
1. Always use IIFE wrapper (via `render_inline_script()`)
2. Scope DOM queries to `.closest('.tc-tool-card')` to prevent conflicts
3. Use `card.querySelector()` not `document.querySelector()`
4. Toast notifications via `.tc-toast--show` class toggle

---

## SEO Content Auto-Rendering

The base widget automatically renders these sections after the tool card:

1. **Introduction** — Paragraphs from `seo_intro`
2. **How To Use** — Numbered steps from `seo_howto`
3. **Features** — Icon + title + desc grid from `seo_features`
4. **Benefits** — List from `seo_benefits`
5. **Use Cases** — List from `seo_usecases`
6. **Why Choose** — List from `seo_whychoose`
7. **Media Section** — Image + description
8. **FAQ Accordion** — Click-to-expand Q&A from `seo_faq`
9. **Related Tools** — Internal links to category siblings

Override via Elementor panel: SEO Content → Override Default Content → Yes

---

## CSS Conventions

- Prefix all classes with `tc-`
- Use CSS variables: `var(--tc-accent)`, `var(--tc-bg-card)`, etc.
- Follow dark theme: dark backgrounds, gold accents
- Responsive: use `clamp()` for fluid typography
- Respect `prefers-reduced-motion`

---

## Registration Checklist

- [ ] Widget file created at `includes/widgets/widget-{slug}.php`
- [ ] Class extends `TextCraft_Base_Widget`
- [ ] `get_name()` returns `textcraft_{snake_slug}`
- [ ] `get_title()` returns translatable string
- [ ] `render_tool_content()` outputs tool HTML
- [ ] Added to `$widgets` array in `class-textcraft-loader.php`
- [ ] SEO content added to `seo-content-data.php` (optional)
- [ ] CSS classes prefixed with `tc-`
- [ ] JavaScript wrapped in IIFE via `render_inline_script()`
