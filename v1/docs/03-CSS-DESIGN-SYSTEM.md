# TextCraft Tools — CSS Design System

## File: `assets/css/textcraft-tools.css` (~3400+ lines)

## Design Tokens (`:root`)

### Backgrounds
```css
--tc-bg:            #050505;    /* Page background */
--tc-bg-soft:       #0b0906;    /* Soft background */
--tc-bg-card:       #11100d;    /* Card background */
--tc-bg-panel:      #17130c;    /* Panel background */
```

### Surfaces
```css
--tc-surface:       #090909;
--tc-surface-2:     #0d0b08;
--tc-surface-3:     #111111;
```

### Gold / Accent
```css
--tc-accent:        #d4a24c;    /* Primary accent */
--tc-accent-2:      #b8860b;
--tc-accent-3:      #c9973f;
--tc-gold:          #f59e0b;
--tc-gold-light:    #ffcc66;
--tc-gold-dark:     #b8860b;
--tc-gold-bright:   #f59e0b;
--tc-gold-glow:     rgba(245, 158, 11, 0.20);
```

### Glow
```css
--tc-glow:          rgba(245, 158, 11, 0.20);
--tc-glow-soft:     rgba(212, 162, 76, 0.16);
```

### Borders
```css
--tc-border:        rgba(212, 162, 76, 0.30);
--tc-border-soft:   rgba(245, 158, 11, 0.18);
--tc-border-hover:  rgba(255, 204, 102, 0.55);
```

### Text
```css
--tc-text:          #f5f0e8;
--tc-text-soft:     #e8dcc8;
--tc-text-primary:  #ffffff;
--tc-text-secondary:#d8c8aa;
--tc-text-muted:    #a8997d;
```

### Danger
```css
--tc-danger:        #b45309;
--tc-danger-soft:   rgba(180, 83, 9, 0.16);
```

### Shadows
```css
--tc-shadow:        0 8px 32px rgba(0, 0, 0, 0.70);
--tc-shadow-gold:   0 20px 60px rgba(245, 158, 11, 0.14);
```

### Transition
```css
--tc-transition:    0.3s cubic-bezier(0.4, 0, 0.2, 1);
```

### Typography
```css
--tc-font-display:  'Playfair Display', Georgia, 'Times New Roman', serif;
--tc-font-body:     'Lexend', sans-serif;
--tc-font-mono:     'DM Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
--tc-font-lexend:   'Lexend', sans-serif;
```

### Radii
```css
--tc-radius-sm:     8px;
--tc-radius-md:     14px;
--tc-radius-lg:     20px;
```

---

## Component Classes

### Widget Wrapper
```css
.tc-widget-wrap          /* Main widget container */
 tc-hero                 /* Hero section (title, subtitle, badge) */
 tc-tool-section         /* Tool content wrapper */
 tc-tool-card            /* Main card with sheen effect */
```

### Hero Section
```css
.tc-hero                 /* padding: 56px 0 32px, text-align: center */
.tc-hero__badge          /* Inline badge with dot */
.tc-hero__badge-dot      /* Animated pulsing dot */
.tc-hero__title          /* Playfair Display, clamp(28px, 5vw, 52px) */
.tc-hero__subtitle       /* Lexend, 16px, max-width: 520px */
```

### Textareas & Inputs
```css
.tc-textarea             /* Full-width, min-height: 160px, dark bg */
.tc-textarea--input      /* Input textarea */
.tc-textarea--output     /* Read-only output */
.tc-text-input           /* Single-line input */
.tc-label-row            /* Flex row: label + char count */
.tc-label                /* 12px, uppercase, gold secondary */
.tc-char-count           /* 12px, muted */
```

### Buttons
```css
.tc-btn                  /* Base button */
.tc-btn--primary         /* Gold gradient, dark text */
.tc-btn--ghost           /* Transparent with border */
.tc-btn--danger          /* Amber/red for destructive actions */
.tc-btn--secondary       /* Secondary action */
.tc-btn-row              /* Flex row of buttons */
```

### Stat Bar
```css
.tc-stat-bar             /* Flex row with border */
.tc-stat                 /* Individual stat column */
.tc-stat__label          /* 10px, uppercase, muted */
.tc-stat__value          /* 20px, bold, accent color */
.tc-stat-sep             /* Vertical separator */
```

### Options Row
```css
.tc-options-row          /* Flex row of checkboxes */
.tc-option               /* Individual checkbox label */
```

### Case Buttons
```css
.tc-case-buttons         /* Grid: repeat(auto-fill, minmax(130px, 1fr)) */
.tc-btn-case             /* Individual case button */
.tc-btn-case.active      /* Active state with gold border */
.tc-btn-case__icon       /* 18px emoji icon */
.tc-btn-case__label      /* 13px bold label */
.tc-btn-case__preview    /* 11px muted preview text */
```

### Active Indicator
```css
.tc-active-indicator     /* Inline badge showing active mode */
.tc-active-indicator.hidden { display: none; }
```

### Toast Notification
```css
.tc-toast                /* Fixed bottom-right, opacity: 0 by default */
.tc-toast--show          /* opacity: 1, visible */
.tc-toast__icon          /* 16px icon */
```

---

## Feature Cards
```css
.tc-features-grid        /* Grid: repeat(auto-fill, minmax(240px, 1fr)) */
.tc-feature-card         /* Card with gradient bg, border, hover lift */
.tc-feature-icon         /* 48px icon box */
.tc-feature-title        /* 16px bold */
.tc-feature-desc         /* 14px muted */
```

## SEO Cards
```css
.tc-seo-grid             /* Grid: repeat(auto-fill, minmax(260px, 1fr)) */
.tc-seo-card             /* Card with gradient bg */
.tc-seo-card__header     /* Flex: icon + name */
.tc-seo-card__icon       /* 22px */
.tc-seo-card__name       /* 15px bold */
.tc-seo-card__example    /* Gold badge with monospace text */
.tc-seo-card__desc       /* 13.5px muted */
```

## Tool Link Cards
```css
.tc-tools-grid           /* Grid: repeat(auto-fill, minmax(200px, 1fr)) */
.tc-tool-link-card       /* Card with gradient bg */
.tc-tl-icon              /* 46px icon box */
.tc-tl-name              /* 14px bold */
.tc-tl-desc              /* 12px muted */
```

## All Tools Page
```css
.tc-atp-wrap             /* Page wrapper */
.tc-atp-hero             /* Hero section */
.tc-atp-badge            /* Badge with dot */
.tc-atp-hero-title       /* Playfair Display */
.tc-atp-hero-subtitle    /* Lexend */
.tc-atp-search-wrap      /* Search bar container */
.tc-atp-search           /* Search input with icon */
.tc-atp-category         /* Category section */
.tc-atp-cat-header       /* Category title + count */
.tc-atp-cat-title        /* Category heading */
.tc-atp-cat-count        /* Count badge */
.tc-atp-grid             /* Tool cards grid */
.tc-atp-card             /* Individual tool card */
.tc-atp-card-icon        /* Card icon */
.tc-atp-card-name        /* Card title */
.tc-atp-card-desc        /* Card description */
.tc-atp-no-results       /* Empty state */
```

## FAQ Accordion
```css
.tc-faq-accordion        /* FAQ container */
.tc-faq-item             /* Individual FAQ item */
.tc-faq-question         /* Clickable question button */
.tc-faq-question-text    /* Question text */
.tc-faq-icon             /* +/- icon */
.tc-faq-answer-wrap      /* Answer wrapper */
.tc-faq-answer           /* Answer content */
```

## SEO Content Sections
```css
.tc-seo-wrap             /* SEO content wrapper */
.tc-seo-section          /* Individual section */
.tc-section-has-dust     /* Gold dust particle container */
.tc-dust                 /* Particle animation spans */
.tc-card-sheen           /* Gold gradient sweep on hover */
```

## Related Tools
```css
.tc-related-tools        /* Related tools section */
.tc-related-tools-list   /* Link list */
```

---

## Animations

### Gold Dust Particles
```css
.tc-dust span            /* 10 particles with tcDustFloat animation */
@keyframes tcDustFloat   /* Floating gold particles */
```

### Card Sheen Sweep
```css
.tc-card-sheen           /* Gold gradient sweep on hover */
/* Applied via .tc-card-sheen class on cards */
```

### Pulse Dot
```css
@keyframes tc-pulse      /* Badge dot animation */
```

---

## Body Background
```css
body {
    background:
        radial-gradient(circle at top center, rgba(245, 158, 11, 0.16), transparent 34%),
        radial-gradient(circle at 18% 18%, rgba(212, 162, 76, 0.10), transparent 30%),
        radial-gradient(circle at 82% 12%, rgba(184, 134, 11, 0.08), transparent 28%),
        linear-gradient(180deg, #050505 0%, #0b0906 45%, #050505 100%);
    font-family: var(--tc-font-body);
}
```

---

## Responsive Breakpoints

```css
/* Tablet */
@media (max-width: 768px) {
    .tc-features-grid { grid-template-columns: repeat(2, 1fr); }
    .tc-atp-grid { grid-template-columns: repeat(2, 1fr); }
}

/* Mobile */
@media (max-width: 600px) {
    .tc-tool-card { padding: 18px; }
    .tc-case-buttons { grid-template-columns: 1fr 1fr; }
    .tc-stat-bar { flex-direction: column; }
    .tc-hero { padding: 32px 0 20px; }
}

/* Small Mobile */
@media (max-width: 480px) {
    .tc-features-grid { grid-template-columns: 1fr; }
    .tc-atp-grid { grid-template-columns: 1fr; }
    .tc-atp-hero { padding: 32px 0 20px; }
}

@media (max-width: 360px) {
    .tc-tools-grid { grid-template-columns: 1fr; }
}
```

---

## Accessibility

```css
/* Focus-visible */
.tc-btn:focus-visible,
.tc-btn-case:focus-visible,
.tc-textarea:focus-visible,
.tc-text-input:focus-visible {
    outline: 2px solid var(--tc-accent);
    outline-offset: 2px;
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
    .tc-btn, .tc-btn-case, .tc-toast, .tc-hero__badge-dot {
        transition: none !important;
        animation: none !important;
    }
}
```

---

## Import Statement

```css
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&family=Inter:wght@300;400;500;600;700;800&family=Lexend:wght@100..900&display=swap');
```
