# TextCraft Tools — Homepage Redesign

> Page: `/` (Homepage)
> Design inspiration: ConvertCase.net homepage
> Priority: HIGH — this is the landing page, first impression

---

## Current State

The homepage currently has:
1. Hero with badge + title + subtitle
2. Features section ("Why TextCraft Tools?") — 3 feature cards
3. Tools grid ("Explore More Free Online Tools") — tool cards
4. FAQ accordion
5. CTA section

**Problems:**
- No tool preview on homepage (ConvertCase.net shows the tool immediately)
- Feature cards look AI-generated (glow effects, excessive shadows)
- No social proof or trust indicators
- No visual hierarchy — everything looks the same weight
- Missing categories overview
- No clear conversion path

---

## New Homepage Architecture

```
1. HEADER (global — redesigned separately)
2. HERO SECTION
   - Full-width centered hero
   - Badge: "Free Online Text Tools"
   - H1: "TextCraft Tools"
   - Subtitle: "74 free browser-based tools for text, images, PDFs, and more."
   - Two CTAs: "Explore All Tools" (primary) + "Start Typing" (ghost)
   - Trust badges row: "No Signup" · "100% Browser-Based" · "Free Forever"
3. TOOL PREVIEW SECTION (NEW)
   - Embedded case converter widget (or simplified version)
   - Shows the tool in action immediately
   - White card on light surface background
4. POPULAR TOOLS GRID
   - 8-12 most popular tools in a clean grid
   - Each card: icon + name + one-line description
   - "View All Tools →" link
5. CATEGORIES SECTION
   - 9 category cards in a grid
   - Each: category icon + name + tool count
   - Links to /tools/ or filtered view
6. WHY CHOOSE US (Features)
   - 3-column grid of feature cards
   - Clean white cards with icon + title + description
   - No glow effects, no gradients, no particles
7. HOW IT WORKS
   - 3-step horizontal flow
   - Step numbers + title + description
8. TESTIMONIALS / SOCIAL PROOF (optional)
   - Trust indicators or simple stats
9. CTA BANNER
   - Dark navy background
   - "Ready to get started?" headline
   - Single CTA button
10. FAQ ACCORDION
    - 6-8 common questions
    - Clean accordion with subtle borders
11. FOOTER (global — redesigned separately)
```

---

## Detailed Section Specs

### 1. Hero Section

```
.tc-home-hero
  background: var(--tc-bg)                 /* White */
  padding: 64px 24px 48px
  text-align: center

  .tc-hero-badge
    display: inline-flex
    align-items: center
    gap: 8px
    background: var(--tc-surface-2)
    border: 1px solid var(--tc-border)
    border-radius: 99px                  /* Pill shape */
    padding: 6px 16px
    font-size: 13px
    font-weight: 600
    color: var(--tc-text-secondary)
    margin-bottom: 24px

  .tc-hero__title
    font-family: var(--tc-font-display)
    font-size: clamp(32px, 5vw, 48px)
    font-weight: 700
    color: var(--tc-text)
    letter-spacing: -0.03em
    line-height: 1.1
    margin: 0 0 16px

  .tc-hero__subtitle
    font-size: clamp(16px, 2vw, 20px)
    color: var(--tc-text-secondary)
    line-height: 1.5
    max-width: 600px
    margin: 0 auto 32px

  .tc-hero-ctas
    display: flex
    gap: 12px
    justify-content: center
    margin-bottom: 32px

  .tc-hero-trust
    display: flex
    gap: 24px
    justify-content: center
    flex-wrap: wrap

    .tc-trust-item
      display: flex
      align-items: center
      gap: 6px
      font-size: 13px
      font-weight: 500
      color: var(--tc-text-muted)

      .tc-trust-icon
        color: var(--tc-primary)
        font-size: 16px
```

### 2. Tool Preview Section (NEW)

```
.tc-home-preview
  padding: 0 24px 48px
  max-width: 800px
  margin: 0 auto

  /* Embeds the case converter or a simplified tool widget */
  /* White card on page background, subtle shadow */
```

### 3. Popular Tools Grid

```
.tc-home-tools-section
  padding: 48px 24px
  max-width: 1200px
  margin: 0 auto

  .tc-section-header
    text-align: center
    margin-bottom: 32px

  .tc-home-tools-grid
    display: grid
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr))
    gap: 16px

  .tc-home-tool-card
    display: flex
    align-items: flex-start
    gap: 14px
    background: var(--tc-bg-card)
    border: 1px solid var(--tc-border)
    border-radius: var(--tc-radius-md)
    padding: 18px 16px
    text-decoration: none
    transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s

    hover:
      border-color: var(--tc-border-hover)
      box-shadow: var(--tc-shadow-card-hover)
      transform: translateY(-2px)

    .tc-home-tool-icon
      font-size: 28px
      line-height: 1
      flex-shrink: 0

    .tc-home-tool-name
      font-size: 15px
      font-weight: 600
      color: var(--tc-text)
      margin: 0 0 4px

    .tc-home-tool-desc
      font-size: 13px
      color: var(--tc-text-muted)
      line-height: 1.5
      margin: 0
```

### 4. Categories Section

```
.tc-home-categories
  padding: 48px 24px
  background: var(--tc-surface)           /* Subtle background */
  max-width: 1200px
  margin: 0 auto

  .tc-home-category-grid
    display: grid
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr))
    gap: 16px

  .tc-home-cat-card
    display: flex
    flex-direction: column
    align-items: center
    gap: 8px
    background: var(--tc-bg-card)
    border: 1px solid var(--tc-border)
    border-radius: var(--tc-radius-md)
    padding: 24px 16px
    text-decoration: none
    text-align: center
    transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s

    .tc-home-cat-icon
      font-size: 32px

    .tc-home-cat-name
      font-size: 15px
      font-weight: 600
      color: var(--tc-text)

    .tc-home-cat-count
      font-size: 12px
      color: var(--tc-text-muted)
```

### 5. Features Section

```
.tc-home-features
  padding: 48px 24px
  max-width: 1200px
  margin: 0 auto

  .tc-home-features-grid
    display: grid
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr))
    gap: 20px

  .tc-home-feature-card
    background: var(--tc-bg-card)
    border: 1px solid var(--tc-border)
    border-radius: var(--tc-radius-md)
    padding: 28px 24px
    text-align: center

    .tc-home-feature-emoji
      font-size: 36px
      margin-bottom: 12px

    .tc-home-feature-title
      font-size: 17px
      font-weight: 600
      color: var(--tc-text)
      margin: 0 0 8px

    .tc-home-feature-desc
      font-size: 14px
      color: var(--tc-text-secondary)
      line-height: 1.7
      margin: 0
```

### 6. CTA Banner

```
.tc-home-cta
  background: var(--tc-primary)           /* #2563eb */
  padding: 48px 24px
  text-align: center

  h2
    color: white
    font-size: clamp(22px, 3vw, 28px)
    font-weight: 700
    margin: 0 0 16px

  p
    color: rgba(255,255,255,0.85)
    font-size: 16px
    margin: 0 0 24px

  .tc-btn
    background: white
    color: var(--tc-primary)
    border: 0
    padding: 12px 32px
    font-weight: 600
    border-radius: var(--tc-radius-sm)

    hover: background: var(--tc-surface-2)
```

### 7. FAQ Section

```
.tc-home-faq
  padding: 48px 24px
  max-width: 800px
  margin: 0 auto

  /* Uses existing .tc-faq-accordion styles */
  /* Clean, minimal accordion */
```

---

## Responsive Behavior

| Breakpoint | Grid Columns | Card Layout |
|---|---|---|
| `< 480px` | 1 column | Stack everything |
| `480px – 768px` | 2 columns | Tool cards 2-col, categories 2-col |
| `768px – 1200px` | 3-4 columns | Full grid layout |
| `> 1200px` | 4-5 columns | Max-width container |

---

## Key Design Decisions

1. **Hero is minimal** — just text + CTAs, no decorative elements
2. **Tool preview is front-and-center** — shows value immediately
3. **Categories are discoverable** — clear navigation to tool groups
4. **Features are understated** — white cards, no glow, no gradients
5. **CTA uses primary blue** — not navy, not orange, clean blue
6. **FAQ is clean** — no box-shadow on items, just borders
7. **Trust indicators** — subtle, not loud, placed under hero CTAs
8. **No particles, no sheen, no gold, no neon** — professional restraint
